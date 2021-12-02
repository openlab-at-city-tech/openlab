<?php

class WpdiscuzRest extends WP_REST_Controller {

    private $dbManager;
    private $options;
    private $helper;
    private $wpdiscuzForm;
    private $resource_name;
    private $routes;

    public function __construct($dbManager, $options, $helper, $wpdiscuzForm) {
        $this->dbManager = $dbManager;
        $this->options = $options;
        $this->helper = $helper;
        $this->wpdiscuzForm = $wpdiscuzForm;

        $this->namespace = "wpdiscuz/v1";
        $this->resource_name = "update";
        $this->initRoutes();
    }

    public function initRoutes() {
        $routes = [];
        if ($this->options->live["commentListUpdateType"] || ($this->options->live["enableBubble"] && $this->options->live["bubbleLiveUpdate"])) {
            $routes["wpdiscuz"] = [
                "namespace" => $this->namespace,
                "resource_name" => $this->resource_name,
                "data" => [
                    [
                        "methods" => "GET",
                        "callback" => [&$this, "checkNewComments"],
                        "permission_callback" => [&$this, "checkPermission"],
                        "args" => [
                            "postId" => [
                                "required" => true,
                                "type" => "number",
                            ],
                            "lastId" => [
                                "required" => true,
                                "type" => "number",
                            ],
                            "visibleCommentIds" => [
                                "required" => true,
                                "type" => "string",
                            ],
                        ],
                    ],
                ]
            ];
        }
        $this->routes = apply_filters("wpdiscuz_rest_routes", $routes, $this->namespace);
    }

    public function registerRoutes() {
        if (!empty($this->routes)) {
            foreach ($this->routes as $route) {
                if ($this->isValidRoute($route)) {
                    register_rest_route($route["namespace"], "/" . $route["resource_name"], $route["data"]);
                }
            }
        }
    }

    public function checkPermission() {
        if ($this->options->live["commentListUpdateType"] || ($this->options->live["enableBubble"] && $this->options->live["bubbleLiveUpdate"])) {
            $currentUser = WpdiscuzHelper::getCurrentUser();
            return !empty($currentUser->ID) || (empty($currentUser->ID) && $this->options->live["liveUpdateGuests"]);
        }
        return false;
    }

    public function checkNewComments($data) {
        $params = $data->get_params();
        $response = ["ids" => [], "commentIDsToRemove" => []];
        $status = current_user_can("moderate_comments") ? "all" : "approved";
        $args = ["status" => $status, "post_id" => $params["postId"]];
        global $wpdiscuz;
        $wpdiscuz->isWpdiscuzLoaded = true;
        $commentId = $this->dbManager->getLastCommentId($args);
        if ($params["visibleCommentIds"]) {
            $response["commentIDsToRemove"] = $this->dbManager->commentIDsToRemove($args, $params["visibleCommentIds"]);
        }
        $form = $this->wpdiscuzForm->getForm($params["postId"]);
        if ($commentId > $params["lastId"]) {
            $currentUser = WpdiscuzHelper::getCurrentUser();
            $sentEmail = !empty($_COOKIE["comment_author_email_" . COOKIEHASH]) ? trim($_COOKIE["comment_author_email_" . COOKIEHASH]) : "";
            $email = !empty($currentUser->ID) ? $currentUser->user_email : $sentEmail;
            $newCommentIds = $this->dbManager->getNewCommentIds($args, $params["lastId"], $email, $params["visibleCommentIds"]);
            $newCommentIds = apply_filters("wpdiscuz_bubble_new_comment_ids", $newCommentIds, $params["postId"], $currentUser);
            if (!empty($newCommentIds)) {
                $response["ids"] = $newCommentIds;
                if ($this->options->live["bubbleShowNewCommentMessage"]) {
                    $comment = get_comment($commentId);
                    $comment->comment_content = apply_filters("comment_text", $comment->comment_content, $comment, ["is_wpdiscuz_comment" => true]);
                    $comment->comment_content = strip_tags($comment->comment_content);
                    if (stripos($comment->comment_content, "[/spoiler]") === false) {
                        $user = "";
                        if ($comment->user_id) {
                            $user = get_user_by("id", $comment->user_id);
                        } else if ($this->options->login["isUserByEmail"]) {
                            $user = get_user_by("email", $comment->comment_author_email);
                        }
                        if ($user) {
                            $authorName = $user->display_name ? $user->display_name : $comment->comment_author;
                            $authorAvatarField = $user->ID;
                            $gravatarUserId = $user->ID;
                            $gravatarUserEmail = $user->user_email;
                        } else {
                            $authorName = $comment->comment_author ? $comment->comment_author : esc_html($this->options->getPhrase("wc_anonymous"));
                            $authorAvatarField = $comment->comment_author_email;
                            $gravatarUserId = 0;
                            $gravatarUserEmail = $comment->comment_author_email;
                        }
                        $gravatarArgs = [
                            "wpdiscuz_gravatar_field" => $authorAvatarField,
                            "wpdiscuz_gravatar_size" => apply_filters("wpdiscuz_gravatar_size", 16),
                            "wpdiscuz_gravatar_user_id" => $gravatarUserId,
                            "wpdiscuz_gravatar_user_email" => $gravatarUserEmail,
                            "wpdiscuz_current_user" => $user,
                        ];
                        if (function_exists("mb_substr")) {
                            $response["commentText"] = mb_substr($comment->comment_content, 0, 50);
                        } else {
                            $response["commentText"] = substr($comment->comment_content, 0, 50);
                        }
                        if (strlen($comment->comment_content) > strlen($response["commentText"])) {
                            $response["commentText"] .= "...";
                        }
                        $response["commentDate"] = esc_html($this->helper->dateDiff($comment->comment_date_gmt));
                        $response["commentLink"] = esc_url_raw(get_comment_link($comment));
                        $response["authorName"] = esc_html(apply_filters("wpdiscuz_comment_author", $authorName, $comment));
                        $response["avatar"] = get_avatar($gravatarArgs["wpdiscuz_gravatar_field"], $gravatarArgs["wpdiscuz_gravatar_size"], "", $authorName, $gravatarArgs);
                    }
                }
            }
        }
        $response["all_comments_count"] = get_comments_number($params["postId"]);
        $response["all_comments_count_before_threads_html"] = "<span class='wpdtc' title='" . esc_attr($response["all_comments_count"]) . "'>" . esc_html($this->helper->getNumber($response["all_comments_count"])) . "</span> " . esc_html(apply_filters("wpdiscuz_comment_count_phrase", (1 == $response["all_comments_count"] ? $form->getHeaderTextSingle() : $form->getHeaderTextPlural()), $response["all_comments_count"]));
        $response["all_comments_count_bubble_html"] = "<span id='wpd-bubble-all-comments-count'" . ($response["all_comments_count"] ? "" : " style='display:none;'") . " title='" . esc_attr($response["all_comments_count"]) . "'>" . esc_html($this->helper->getNumber($response["all_comments_count"])) . "</span>";
        return $response;
    }

    private function isValidRoute($route) {
        $isValid = empty($route) || !is_array($route) ? false : true;
        if ($isValid) {
            $isValid = !empty($route["namespace"]) &&
                    !empty($route["resource_name"]) &&
                    !empty($route["data"][0]["methods"]) &&
                    !empty($route["data"][0]["callback"]) &&
                    !empty($route["data"][0]["permission_callback"]);
        }
        return $isValid;
    }

}
