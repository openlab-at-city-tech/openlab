<?php

if (!defined("ABSPATH")) {
    exit();
}

class WpdiscuzHelperEmail implements WpDiscuzConstants {

    /**
     * @var WpdiscuzOptions
     */
    private $options;

    /**
     * @var WpdiscuzDBManager
     */
    private $dbManager;

    /**
     * @var WpdiscuzHelper
     */
    private $helper;

    public function __construct($options, $dbManager, $helper) {
        $this->options = $options;
        $this->dbManager = $dbManager;
        $this->helper = $helper;
        add_action("wp_ajax_wpdAddSubscription", [&$this, "addSubscription"]);
        add_action("wp_ajax_nopriv_wpdAddSubscription", [&$this, "addSubscription"]);
        add_action("wp_ajax_wpdCheckNotificationType", [&$this, "checkNotificationType"]);
        add_action("wp_ajax_nopriv_wpdCheckNotificationType", [&$this, "checkNotificationType"]);
        add_action("comment_post", [&$this, "notificationFromDashboard"], 10, 2);
    }

    public function addSubscription() {
        $success = 0;
        $currentUser = WpdiscuzHelper::getCurrentUser();
        $subscribeFormNonce = WpdiscuzHelper::sanitize(INPUT_POST, "wpdiscuz_subscribe_form_nonce", "FILTER_SANITIZE_STRING");
        $subscriptionType = WpdiscuzHelper::sanitize(INPUT_POST, "wpdiscuzSubscriptionType", "FILTER_SANITIZE_STRING");
        $postId = WpdiscuzHelper::sanitize(INPUT_POST, "postId", FILTER_SANITIZE_NUMBER_INT);
        $showSubscriptionBarAgreement = WpdiscuzHelper::sanitize(INPUT_POST, "show_subscription_agreement", FILTER_SANITIZE_NUMBER_INT);
        $form = wpDiscuz()->wpdiscuzForm->getForm($postId);
        if ($currentUser && $currentUser->ID) {
            $email = $currentUser->user_email;
        } else {
            $email = WpdiscuzHelper::sanitize(INPUT_POST, "wpdiscuzSubscriptionEmail", "FILTER_SANITIZE_STRING");
        }
        if (!$currentUser->exists() && $form->isShowSubscriptionBarAgreement() && !$showSubscriptionBarAgreement && ($subscriptionType === WpdiscuzCore::SUBSCRIPTION_POST || $subscriptionType === WpdiscuzCore::SUBSCRIPTION_ALL_COMMENT)) {
            $email = "";
        }
        $addSubscription = apply_filters("wpdiscuz_before_subscription_added", true);
        if ($addSubscription && wp_verify_nonce($subscribeFormNonce, "wpdiscuz_subscribe_form_nonce_action") && $email && filter_var($email, FILTER_VALIDATE_EMAIL) !== false && in_array($subscriptionType, [self::SUBSCRIPTION_POST, self::SUBSCRIPTION_ALL_COMMENT]) && $postId) {
            $noNeedMemberConfirm = ($currentUser->ID && !$this->options->subscription["enableMemberConfirm"]);
            $noNeedGuestsConfirm = (!$currentUser->ID && !$this->options->subscription["enableGuestsConfirm"]);
            if ($noNeedMemberConfirm || $noNeedGuestsConfirm) {
                $confirmData = $this->dbManager->addEmailNotification($postId, $postId, $email, $subscriptionType, 1);
                if ($confirmData) {
                    $success = 1;
                }
            } else {
                $confirmData = $this->dbManager->hasSubscription($postId, $email);
                if ($confirmData && !intval($confirmData["confirm"])) {
                    $success = $this->confirmEmailSender($confirmData["id"], $confirmData["activation_key"], $postId, $email) ? 1 : -1;
                } else {
                    $confirmData = $this->dbManager->addEmailNotification($postId, $postId, $email, $subscriptionType, 0);
                    if ($confirmData) {
                        $success = $this->confirmEmailSender($confirmData["id"], $confirmData["activation_key"], $postId, $email) ? 1 : -1;
                        if ($success < 0) {
                            $this->dbManager->unsubscribe($confirmData["id"], $confirmData["activation_key"]);
                        }
                    }
                }
            }
        }
        if ($success == -1) {
            wp_send_json_error(esc_html($this->options->getPhrase("wc_unable_sent_email")));
        } else if ($success == 0) {
            wp_send_json_error(esc_html($this->options->getPhrase("wc_subscription_fault")));
        } else {
            $noNeedMemberConfirm = ($currentUser->ID && !$this->options->subscription["enableMemberConfirm"]);
            $noNeedGuestsConfirm = (!$currentUser->ID && !$this->options->subscription["enableGuestsConfirm"]);
            if ($noNeedMemberConfirm || $noNeedGuestsConfirm) {
                wp_send_json_success(esc_html($this->options->getPhrase("wc_subscribe_message")));
            } else {
                wp_send_json_success(esc_html($this->options->getPhrase("wc_confirm_email")));
            }
        }
    }

    public function confirmEmailSender($id, $activationKey, $postId, $email) {
        $confirm_url = $this->dbManager->confirmLink($id, $activationKey, $postId);
        $unsubscribe_url = $this->dbManager->unsubscribeLink($postId, $email);
        $siteUrl = get_site_url();
        $blogTitle = get_option("blogname");
        $postTitle = get_the_title($postId);

        $search = ["[SITE_URL]", "[POST_URL]", "[BLOG_TITLE]", "[POST_TITLE]"];
        $replace = [$siteUrl, get_permalink($postId), $blogTitle, $postTitle];

        $subject = str_replace(["[BLOG_TITLE]", "[POST_TITLE]"], [$blogTitle, $postTitle], $this->options->subscription["emailSubjectSubscriptionConfirmation"]);
        $message = str_replace($search, $replace, wpautop($this->options->subscription["emailContentSubscriptionConfirmation"]));

        if (strpos($message, "[CONFIRM_URL]") === false) {
            $message .= "<br/><br/><a href='$confirm_url'>" . $this->options->getPhrase("wc_confirm_email") . "</a>";
        } else {
            $message = str_replace("[CONFIRM_URL]", $confirm_url, $message);
        }

        if (strpos($message, "[CANCEL_URL]") === false) {
            $message .= "<br/><br/><a href='$unsubscribe_url'>" . $this->options->getPhrase("wc_ignore_subscription") . "</a>";
        } else {
            $message = str_replace("[CANCEL_URL]", $unsubscribe_url, $message);
        }

        $headers = [];
        $fromName = html_entity_decode($blogTitle, ENT_QUOTES);
        $parsedUrl = parse_url($siteUrl);
        $domain = isset($parsedUrl["host"]) ? WpdiscuzHelper::fixEmailFrom($parsedUrl["host"]) : "";
        $fromEmail = "no-reply@" . $domain;
        $headers[] = "Content-Type: text/html; charset=UTF-8";
        $headers[] = "From: " . $fromName . " <" . $fromEmail . "> \r\n";
        $subject = html_entity_decode($subject, ENT_QUOTES);
        $message = html_entity_decode($message, ENT_QUOTES);
        return wp_mail($email, $subject, do_shortcode($message), $headers);
    }

    /**
     * send email
     */
    public function emailSender($emailData, $commentId, $subject, $message, $subscriptionType) {
        global $wp_rewrite;
        $comment = get_comment($commentId);
        $post = get_post($comment->comment_post_ID);
        $postAuthor = get_userdata($post->post_author);

        $sendMail = apply_filters("wpdiscuz_email_notification", true, $emailData, $comment);
        if ($emailData["email"] === $postAuthor->user_email && ((get_option("moderation_notify") && $comment->comment_approved !== "1") || (get_option("comments_notify") && $comment->comment_approved === "1"))) {
            return;
        }
        if ($sendMail) {
            $message = apply_filters("wpdiscuz_email_content", $message, $comment, $emailData);
            $unsubscribeUrl = get_permalink($comment->comment_post_ID);
            $unsubscribeUrl = $unsubscribeUrl . (parse_url($unsubscribeUrl, PHP_URL_QUERY) ? "&" : "?");
            $unsubscribeUrl .= "wpdiscuzUrlAnchor&wpdiscuzSubscribeID=" . $emailData["id"] . "&key=" . $emailData["activation_key"] . "&#wc_unsubscribe_message";

            $siteUrl = get_site_url();
            $blogTitle = get_option("blogname");
            $postTitle = get_the_title($comment->comment_post_ID);
            if ($subscriptionType === self::SUBSCRIPTION_COMMENT) {
                $parentComment = get_comment($comment->comment_parent);
                $subscriber = $parentComment && $parentComment->comment_author ? $parentComment->comment_author : $this->options->getPhrase("wc_anonymous");
            } else {
                $user = get_user_by("email", $emailData["email"]);
                $subscriber = $user && $user->display_name ? $user->display_name : "";
            }
            $commentAuthor = $comment->comment_author ? $comment->comment_author : $this->options->getPhrase("wc_anonymous");
            $search = ["[SITE_URL]", "[POST_URL]", "[BLOG_TITLE]", "[POST_TITLE]", "[SUBSCRIBER_NAME]", "[COMMENT_URL]", "[COMMENT_AUTHOR]", "[COMMENT_CONTENT]"];
            $replace = [$siteUrl, get_permalink($comment->comment_post_ID), $blogTitle, $postTitle, $subscriber, get_comment_link($commentId), $commentAuthor, wpautop($comment->comment_content)];
            $message = str_replace($search, $replace, $message);

            $subject = str_replace(["[BLOG_TITLE]", "[POST_TITLE]", "[COMMENT_AUTHOR]"], [$blogTitle, $postTitle, $commentAuthor], $subject);

            if (strpos($message, "[UNSUBSCRIBE_URL]") === false) {
                $message .= "<br/><br/><a href='$unsubscribeUrl'>" . $this->options->getPhrase("wc_unsubscribe") . "</a>";
            } else {
                $message = str_replace("[UNSUBSCRIBE_URL]", $unsubscribeUrl, $message);
            }

            $headers = [];
            $fromName = html_entity_decode($blogTitle, ENT_QUOTES);
            $parsedUrl = parse_url($siteUrl);
            $domain = isset($parsedUrl["host"]) ? WpdiscuzHelper::fixEmailFrom($parsedUrl["host"]) : "";
            $fromEmail = "no-reply@" . $domain;
            $headers[] = "Content-Type: text/html; charset=UTF-8";
            $headers[] = "From: " . $fromName . " <" . $fromEmail . "> \r\n";
            $subject = html_entity_decode($subject, ENT_QUOTES);
            $message = html_entity_decode($message, ENT_QUOTES);
            wp_mail($emailData["email"], $subject, do_shortcode($message), $headers);
        }
    }

    /**
     * Check notification type and send email to post new comments subscribers
     */
    public function checkNotificationType() {
        $postId = WpdiscuzHelper::sanitize(INPUT_POST, "postId", FILTER_SANITIZE_NUMBER_INT, 0);
        ;
        $commentId = WpdiscuzHelper::sanitize(INPUT_POST, "comment_id", FILTER_SANITIZE_NUMBER_INT, 0);
        $email = isset($_POST["email"]) ? sanitize_email(trim($_POST["email"])) : "";
        $isParent = WpdiscuzHelper::sanitize(INPUT_POST, "isParent", "FILTER_SANITIZE_STRING");
        $currentUser = WpdiscuzHelper::getCurrentUser();
        if ($currentUser && $currentUser->user_email) {
            $email = $currentUser->user_email;
        }
        if ($commentId && $email && $postId && ($comment = get_comment($commentId))) {
            if (apply_filters("wpdiscuz_enable_user_mentioning", $this->options->subscription["enableUserMentioning"]) && $this->options->subscription["sendMailToMentionedUsers"] && ($mentionedUsers = $this->helper->getMentionedUsers($comment->comment_content))) {
                $this->sendMailToMentionedUsers($mentionedUsers, $comment);
            }
            do_action("wpdiscuz_before_sending_emails", $commentId, $comment);
            $this->notifyPostSubscribers($postId, $commentId, $email);
            $this->notifyFollowers($postId, $commentId, $email);
            if (!$isParent) {
                $parentCommentId = $comment->comment_parent;
                $parentComment = get_comment($parentCommentId);
                $parentCommentEmail = $parentComment->comment_author_email;
                if ($parentCommentEmail !== $email) {
                    $this->notifyAllCommentSubscribers($postId, $commentId, $email);
                    $this->notifyCommentSubscribers($parentCommentId, $comment->comment_ID, $email);
                }
            }
        }
        wp_die();
    }

    /**
     * Send notifications for new comments on the post (including replies)
     *
     * @param $postId      int
     * @param $commentId   int
     * @param $email       string
     */
    public function notifyPostSubscribers($postId, $commentId, $email) {
        $emailsArray = $this->dbManager->getPostNewCommentNotification($postId, $email);
        $subject = $this->options->subscription["emailSubjectPostComment"];
        $message = wpautop($this->options->subscription["emailContentPostComment"]);
        foreach ($emailsArray as $k => $eRow) {
            $subscriberUserId = $eRow["id"];
            $subscriberEmail = $eRow["email"];
            $this->emailSender($eRow, $commentId, $subject, $message, self::SUBSCRIPTION_POST);
            do_action("wpdiscuz_notify_post_subscribers", $postId, $commentId, $subscriberUserId, $subscriberEmail);
        }
    }

    /**
     * Send notifications for new comments on the post (including replies)
     *
     * @param $postId           int
     * @param $newCommentId     int
     * @param $email            string
     */
    public function notifyAllCommentSubscribers($postId, $newCommentId, $email) {
        $emailsArray = $this->dbManager->getAllNewCommentNotification($postId, $email);
        $subject = $this->options->subscription["emailSubjectAllCommentReply"];
        $message = wpautop($this->options->subscription["emailContentAllCommentReply"]);
        foreach ($emailsArray as $k => $eRow) {
            $subscriberUserId = $eRow["id"];
            $subscriberEmail = $eRow["email"];
            $this->emailSender($eRow, $newCommentId, $subject, $message, self::SUBSCRIPTION_ALL_COMMENT);
            do_action("wpdiscuz_notify_all_comment_subscribers", $postId, $newCommentId, $subscriberUserId, $subscriberEmail);
        }
    }

    /**
     * Send notifications for new replies to an individual comment
     * (includes all replies)
     *
     * @param $parentCommentId    int
     * @param $newCommentId       int
     * @param $email              string  email address to exclude (the comment author email)
     */
    public function notifyCommentSubscribers($parentCommentId, $newCommentId, $email) {
        $emailsArray = $this->dbManager->getNewReplyNotification($parentCommentId, $email);
        $subject = $this->options->subscription["emailSubjectCommentReply"];
        $message = wpautop($this->options->subscription["emailContentCommentReply"]);
        foreach ($emailsArray as $k => $eRow) {
            $subscriberUserId = $eRow["id"];
            $subscriberEmail = $eRow["email"];
            $this->emailSender($eRow, $newCommentId, $subject, $message, self::SUBSCRIPTION_COMMENT);
            do_action("wpdiscuz_notify_comment_subscribers", $parentCommentId, $newCommentId, $subscriberUserId, $subscriberEmail);
        }
    }

    /**
     * When a comment is approved from the admin comments.php or posts.php... notify the subscribers
     *
     * @param $commentId       int
     * @param $approved        bool
     */
    public function notificationFromDashboard($commentId, $approved) {
        $wpdiscuz = wpDiscuz();
        $referer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";
        $comment = get_comment($commentId);
        $commentsPage = strpos($referer, "edit-comments.php") !== false;
        $postCommentsPage = (strpos($referer, "post.php") !== false) && (strpos($referer, "action=edit") !== false);
        $isLoadWpdiscuz = false;
        $post = get_post($comment->comment_post_ID);
        if ($post && is_object($post)) {
            $form = $wpdiscuz->wpdiscuzForm->getForm($post->ID);
            $isLoadWpdiscuz = $form->getFormID() && (comments_open($post) || $post->comment_count) && post_type_supports($post->post_type, "comments");
        }
        if ($approved == 1 && ($commentsPage || $postCommentsPage) && $comment && $isLoadWpdiscuz) {
            $postId = $comment->comment_post_ID;
            $email = $comment->comment_author_email;
            $parentComment = $comment->comment_parent ? get_comment($comment->comment_parent) : 0;
            if (apply_filters("wpdiscuz_enable_user_mentioning", $this->options->subscription["enableUserMentioning"]) && $this->options->subscription["sendMailToMentionedUsers"] && ($mentionedUsers = $this->helper->getMentionedUsers($comment->comment_content))) {
                $this->sendMailToMentionedUsers($mentionedUsers, $comment);
            }
            do_action("wpdiscuz_before_sending_emails", $commentId, $comment);
            $this->notifyPostSubscribers($postId, $commentId, $email);
            if ($parentComment) {
                $parentCommentEmail = $parentComment->comment_author_email;
                if ($parentCommentEmail !== $email) {
                    $this->notifyAllCommentSubscribers($postId, $commentId, $email);
                    $this->notifyCommentSubscribers($parentComment->comment_ID, $commentId, $email);
                }
            }
        }
    }

    /**
     * When a comment is approved (after being held for moderation)... notify the author
     *
     * @param $comment  WP_Comment
     */
    public function notifyOnApproving($comment) {
        if ($comment) {
            $wpdiscuz = wpDiscuz();
            $isLoadWpdiscuz = false;
            $post = get_post($comment->comment_post_ID);
            if ($post && is_object($post)) {
                $form = $wpdiscuz->wpdiscuzForm->getForm($post->ID);
                $isLoadWpdiscuz = $form->getFormID() && (comments_open($post) || $post->comment_count) && post_type_supports($post->post_type, "comments");
            }
            if ($isLoadWpdiscuz) {
                $user = $comment->user_id ? get_userdata($comment->user_id) : null;
                if ($user) {
                    $email = $user->user_email;
                } else {
                    $email = $comment->comment_author_email;
                }
                if (apply_filters("wpdiscuz_send_email_on_approving", true, $email, $comment)) {
                    $siteUrl = get_site_url();
                    $blogTitle = get_option("blogname");
                    $postTitle = get_the_title($comment->comment_post_ID);
                    $search = ["[SITE_URL]", "[POST_URL]", "[BLOG_TITLE]", "[POST_TITLE]", "[COMMENT_URL]", "[COMMENT_AUTHOR]", "[COMMENT_CONTENT]"];
                    $replace = [$siteUrl, get_permalink($comment->comment_post_ID), $blogTitle, $postTitle, get_comment_link($comment->comment_ID), $comment->comment_author, wpautop($comment->comment_content)];
                    $message = str_replace($search, $replace, $this->options->getPhrase("wc_comment_approved_email_message", ["comment" => $comment]));

                    $subject = str_replace(["[BLOG_TITLE]", "[POST_TITLE]", "[COMMENT_AUTHOR]"], [$blogTitle, $postTitle, $comment->comment_author], $this->options->subscription["emailSubjectCommentApproved"]);
                    $message = str_replace($search, $replace, wpautop($this->options->subscription["emailContentCommentApproved"]));

                    $headers = [];
                    $fromName = html_entity_decode($blogTitle, ENT_QUOTES);
                    $parsedUrl = parse_url($siteUrl);
                    $domain = isset($parsedUrl["host"]) ? WpdiscuzHelper::fixEmailFrom($parsedUrl["host"]) : "";
                    $fromEmail = "no-reply@" . $domain;
                    $headers[] = "Content-Type: text/html; charset=UTF-8";
                    $headers[] = "From: " . $fromName . " <" . $fromEmail . "> \r\n";
                    $subject = html_entity_decode($subject, ENT_QUOTES);
                    $message = html_entity_decode($message, ENT_QUOTES);
                    wp_mail($email, $subject, do_shortcode($message), $headers);
                }
            }
        }
    }

    public function followConfirmEmail($postId, $id, $key, $email) {
        $confirmUrl = $this->dbManager->followConfirmLink($postId, $id, $key);
        $cancelUrl = $this->dbManager->followCancelLink($postId, $id, $key);
        $siteUrl = get_site_url();
        $blogTitle = get_option("blogname");
        $postTitle = get_the_title($postId);
        $search = ["[SITE_URL]", "[POST_URL]", "[BLOG_TITLE]", "[POST_TITLE]"];
        $replace = [$siteUrl, get_permalink($postId), $blogTitle, $postTitle];

        $subject = str_replace(["[BLOG_TITLE]", "[POST_TITLE]"], [$blogTitle, $postTitle], $this->options->subscription["emailSubjectFollowConfirmation"]);
        $message = str_replace($search, $replace, wpautop($this->options->subscription["emailContentFollowConfirmation"]));

        if (strpos($message, "[CONFIRM_URL]") === false) {
            $message .= "<br/><br/><a href='$confirmUrl'>" . $this->options->getPhrase("wc_follow_confirm") . "</a>";
        } else {
            $message = str_replace("[CONFIRM_URL]", $confirmUrl, $message);
        }

        if (strpos($message, "[CANCEL_URL]") === false) {
            $message .= "<br/><br/><a href='$cancelUrl'>" . $this->options->getPhrase("wc_follow_cancel") . "</a>";
        } else {
            $message = str_replace("[CANCEL_URL]", $cancelUrl, $message);
        }


        $headers = [];
        $fromName = html_entity_decode($blogTitle, ENT_QUOTES);
        $parsedUrl = parse_url($siteUrl);
        $domain = isset($parsedUrl["host"]) ? WpdiscuzHelper::fixEmailFrom($parsedUrl["host"]) : "";
        $fromEmail = "no-reply@" . $domain;
        $headers[] = "Content-Type: text/html; charset=UTF-8";
        $headers[] = "From: " . $fromName . " <" . $fromEmail . "> \r\n";
        $subject = html_entity_decode($subject, ENT_QUOTES);
        $message = html_entity_decode($message, ENT_QUOTES);
        return wp_mail($email, $subject, do_shortcode($message), $headers);
    }

    public function notifyFollowers($postId, $commentId, $email) {
        $followersData = $this->dbManager->getUserFollowers($email);
        $comment = get_comment($commentId);
        $post = get_post($comment->comment_post_ID);
        $postAuthor = get_userdata($post->post_author);
        $moderationNotify = get_option("moderation_notify");
        $commentsNotify = get_option("comments_notify");

        $siteUrl = get_site_url();
        $blogTitle = get_option("blogname");
        $postTitle = get_the_title($post);
        $postUrl = get_permalink($post);
        $commentUrl = get_comment_link($comment);

        $subject = str_replace(["[BLOG_TITLE]", "[POST_TITLE]"], [$blogTitle, $postTitle], $this->options->subscription["emailSubjectFollowComment"]);

        $search = ["[SITE_URL]", "[POST_URL]", "[BLOG_TITLE]", "[POST_TITLE]", "[COMMENT_URL]", "[COMMENT_CONTENT]"];
        $replace = [$siteUrl, $postUrl, $blogTitle, $postTitle, $commentUrl, wpautop($comment->comment_content)];

        $message = str_replace($search, $replace, wpautop($this->options->subscription["emailContentFollowComment"]));
        global $wp_rewrite;
        $cancelLink = !$wp_rewrite->using_permalinks() ? $postUrl . "&" : $postUrl . "?";
        $fromName = html_entity_decode($blogTitle, ENT_QUOTES);
        $parsedUrl = parse_url($siteUrl);
        $domain = isset($parsedUrl["host"]) ? WpdiscuzHelper::fixEmailFrom($parsedUrl["host"]) : "";
        $fromEmail = "no-reply@" . $domain;
        $data = [
            "site_url" => $siteUrl,
            "blog_title" => $blogTitle,
            "from_name" => $fromName,
            "from_email" => $fromEmail,
            "content_type" => "text/html",
        ];

        foreach ($followersData as $k => $followerData) {
            if (($followerData["follower_email"] === $postAuthor->user_email) && (($moderationNotify && $comment->comment_approved === "0") || ($commentsNotify && $comment->comment_approved === "1"))) {
                return;
            }
            $subject = str_replace(["[COMMENT_AUTHOR]"], [$followerData["user_name"]], $subject);
            $body = str_replace(["[COMMENT_AUTHOR]", "[FOLLOWER_NAME]"], [$followerData["user_name"], $followerData["follower_name"]], $message);
            $this->emailToFollower($followerData, $comment, $subject, $body, $cancelLink, $data);
            do_action("wpdiscuz_notify_followers", $comment, $followerData);
        }
    }

    private function emailToFollower($followerData, $comment, $subject, $message, $cancelLink, $data) {
        $sendMail = apply_filters("wpdiscuz_follow_email_notification", true, $followerData, $comment);
        if ($sendMail) {
            $cancelLink .= "wpdiscuzUrlAnchor&wpdiscuzFollowID={$followerData["id"]}&wpdiscuzFollowKey={$followerData["activation_key"]}&wpDiscuzComfirm=0#wc_follow_message";
            if (strpos($message, "[CANCEL_URL]") === false) {
                $message .= "<br/><br/><a href='$cancelLink'>" . esc_html__("Unfollow", "wpdiscuz") . "</a>";
            } else {
                $message = str_replace("[CANCEL_URL]", $cancelLink, $message);
            }
            $headers = [];
            $mailContentType = $data["content_type"];
            $headers[] = "Content-Type:  $mailContentType; charset=UTF-8";
            $headers[] = "From: " . $data["from_name"] . " <" . $data["from_email"] . "> \r\n";
            $subject = html_entity_decode($subject, ENT_QUOTES);
            $message = html_entity_decode($message, ENT_QUOTES);
            wp_mail($followerData["follower_email"], $subject, do_shortcode($message), $headers);
        }
    }

    public function sendMailToMentionedUsers($users, $comment) {
        $post = get_post($comment->comment_post_ID);

        $parsedUrl = parse_url(get_site_url());
        $domain = isset($parsedUrl["host"]) ? WpdiscuzHelper::fixEmailFrom($parsedUrl["host"]) : "";
        $fromEmail = "no-reply@" . $domain;
        $fromName = html_entity_decode(get_option("blogname"), ENT_QUOTES);
        $headers[] = "Content-Type: text/html; charset=UTF-8";
        $headers[] = "From: " . $fromName . " <" . $fromEmail . "> \r\n";

        $siteUrl = get_site_url();
        $blogTitle = get_option("blogname");
        $postTitle = get_the_title($post);
        $postUrl = get_permalink($post);
        $commentUrl = get_comment_link($comment);
        $commentAuthor = $comment->comment_author;

        $subject = str_replace(["[BLOG_TITLE]", "[POST_TITLE]"], [$blogTitle, $postTitle], $this->options->subscription["emailSubjectUserMentioned"]);
        $message = wpautop($this->options->subscription["emailContentUserMentioned"]);

        $search = ["[SITE_URL]", "[POST_URL]", "[BLOG_TITLE]", "[POST_TITLE]", "[MENTIONED_USER_NAME]", "[COMMENT_URL]", "[COMMENT_AUTHOR]"];
        $replace = [$siteUrl, $postUrl, $blogTitle, $postTitle, "", $commentUrl, $commentAuthor];

        foreach ($users as $k => $user) {
            if ($user["email"] !== $comment->comment_author_email) {
                if (apply_filters("wpducm_mail_to_mentioned_user", true, $user, $comment)) {
                    $replace[4] = $user["name"];
                    $body = str_replace($search, $replace, $message);
                    $subject = apply_filters("wpdiscuz_mentioned_user_mail_subject", $subject, $user, $comment);
                    $body = apply_filters("wpdiscuz_mentioned_user_mail_body", $body, $user, $comment);
                    if ($subject && $body) {
                        wp_mail($user["email"], $subject, $body, $headers);
                    }
                }
            }
        }
    }

}
