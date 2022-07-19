<?php

/** COMMENTS WALKER */
class WpdiscuzWalker extends Walker_Comment implements WpDiscuzConstants {

    private $helper;
    private $helperOptimization;
    private $dbManager;
    private $options;
    private $cache;
    private $users = [];
    private $extra = [];
    private $feedbacks = [];

    public function __construct($helper, $helperOptimization, $dbManager, $options, $cache) {
        $this->helper = $helper;
        $this->helperOptimization = $helperOptimization;
        $this->dbManager = $dbManager;
        $this->options = $options;
        $this->cache = $cache;
    }

    /** START_EL */
    public function start_el(&$output, $comment, $depth = 0, $args = [], $id = 0) {
        $depth++;
        $GLOBALS["comment_depth"] = $depth;
        $GLOBALS["comment"] = $comment;
        // BEGIN

        if (!$this->extra && !empty($args["commentsArgs"])) {
            $this->extra = $this->cache->getExtraCache($args["commentsArgs"]);
        }

        $search = [];
        $replace = [];
        $commentOutput = "";
        $depth = isset($args["addComment"]) ? $args["addComment"] : $depth;
        $uniqueId = $comment->comment_ID . "_" . $comment->comment_parent;
        $commentWrapperClass = get_comment_class("wpd-comment",$comment->comment_ID, $comment->comment_post_ID);
        $commentWrapClass = ["wpd-comment-wrap"];

        if (isset($this->extra[$comment->comment_ID]["metas"])) {
            $commentMetas = $this->extra[$comment->comment_ID]["metas"];
        } else {
            $this->extra[$comment->comment_ID]["metas"] = $commentMetas = get_comment_meta($comment->comment_ID);
        }

        $isClosed = isset($commentMetas[self::META_KEY_CLOSED]) ? intval($commentMetas[self::META_KEY_CLOSED][0]) : 0;
        $isInline = isset($commentMetas[self::META_KEY_FEEDBACK_FORM_ID][0]) ? intval($commentMetas[self::META_KEY_FEEDBACK_FORM_ID][0]) : 0;
        $isApproved = $comment->comment_approved === "1";

        if ($isInline && !isset($this->feedbacks[$isInline])) {
            $this->feedbacks[$isInline] = $this->dbManager->getFeedbackForm($isInline);
        }

        if ($this->options->content["enableImageConversion"]) {
            $comment->comment_content = $this->helper->makeClickable($comment->comment_content);
        }

        if (isset($args["new_loaded_class"])) {
            $commentWrapperClass[] = $args["new_loaded_class"];
            if ($args["isSingle"]) {
                $commentWrapperClass[] = "wpdiscuz_single";
            } else {
                $depth = $this->helperOptimization->getCommentDepth($comment->comment_ID);
            }
        }

        if (isset($this->extra[$comment->comment_ID]["commentLink"])) {
            $commentLink = $this->extra[$comment->comment_ID]["commentLink"];
        } else {
            $this->extra[$comment->comment_ID]["commentLink"] = $commentLink = get_comment_link($comment);
        }

        if (!$this->options->wp["isPaginate"]) {
            if (!empty($args["last_visit"]) && !empty($args["current_user_email"]) && strtotime($comment->comment_date) > $args["last_visit"] && $args["current_user_email"] !== $comment->comment_author_email) {
                $commentWrapperClass[] = "wpd-new-loaded-comment";
            }
        }

        $userKey = $comment->user_id . "_" . $comment->comment_author_email . "_" . $comment->comment_author;
        if (isset($this->users[$userKey])) {
            $user = $this->users[$userKey];
        } else if ($user = $this->cache->getUserCache($userKey)) {
            $this->helper->fillUserRoleData($user, $args);
            $this->users[$userKey] = $user;
        } else {
            $this->users[$userKey] = $user = $this->helper->getCommentAuthor($comment, $args);
            $this->cache->setUserCache($userKey, $user);
        }

        if ($comment->comment_parent && $this->options->wp["threadComments"]) {
            $commentWrapperClass[] = "wpd-reply";
        }

        $showDate = false;
        if ($this->options->thread_layouts["showCommentDate"]) {

            if (isset($this->extra[$comment->comment_ID]["commentDate"])) {
                $commentDate = $this->extra[$comment->comment_ID]["commentDate"];
            } else {
                $this->extra[$comment->comment_ID]["commentDate"] = $commentDate = get_comment_date($this->options->wp["dateFormat"] . " " . $this->options->wp["timeFormat"], $comment->comment_ID);
            }

            $search[] = "{DATE_WRAPPER_CLASSES}";
            $search[] = "{DATE_ICON}";
            $search[] = "{DATE}";
            $search[] = "{POSTED_DATE}";
            $replace[] = "wpd-comment-date";
            $replace[] = "<i class='far fa-clock' aria-hidden='true'></i>";
            $replace[] = ($this->options->general["simpleCommentDate"]) ? esc_html($commentDate) : esc_html($this->helper->dateDiff($comment->comment_date_gmt));
            $replace[] = esc_html($commentDate);
            $showDate = true;
        }

        $statusIcons = "";
        $showReplyTo = false;
        $toolsActions = "";
        if ($comment->comment_parent == 0) {
            if ($comment->comment_type === self::WPDISCUZ_STICKY_COMMENT) {
                $commentWrapperClass[] = "wpd-sticky-comment";
                $statusIcons .= "<div class='wpd-sticky' wpd-tooltip='" . esc_attr($this->options->getPhrase("wc_sticky_comment_icon_title", ["comment" => $comment])) . "'><i class='fas fa-thumbtack' aria-hidden='true'></i></div>";
                $stickText = esc_html($this->options->getPhrase("wc_unstick_comment", ["comment" => $comment]));
            } else {
                $stickText = esc_html($this->options->getPhrase("wc_stick_comment", ["comment" => $comment]));
            }

            if ($isClosed) {
                $commentWrapperClass[] = "wpd-closed-comment";
                $statusIcons .= "<div class='wpd-closed' wpd-tooltip='" . esc_attr($this->options->getPhrase("wc_closed_comment_icon_title", ["comment" => $comment])) . "'><i class='fas fa-lock' aria-hidden='true'></i></div>";
                $closeText = esc_html($this->options->getPhrase("wc_open_comment", ["comment" => $comment]));
            } else {
                $closeText = esc_html($this->options->getPhrase("wc_close_comment", ["comment" => $comment]));
            }
            if ($isApproved) {
                if ($comment->comment_type !== self::WPDISCUZ_PRIVATE_COMMENT) {
                    $toolsActions .= sprintf($args["wpd_stick_btn"], $stickText);
                }
                $toolsActions .= sprintf($args["wpd_close_btn"], $closeText);
            }
        } else {
            if (isset($this->extra[$comment->comment_ID]["commentParent"])) {
                $parentComment = $this->extra[$comment->comment_ID]["commentParent"];
            } else {
                $this->extra[$comment->comment_ID]["commentParent"] = $parentComment = get_comment($comment->comment_parent);
            }

            $parentCommentLink = "#comment-" . $parentComment->comment_ID;
            $userKey = $parentComment->user_id . "_" . $parentComment->comment_author_email . "_" . $parentComment->comment_author;
            $parentCommentUserName = isset($this->users[$userKey]) ? $this->users[$userKey]["authorName"] : $parentComment->comment_author;
            $search[] = "{REPLY_TO_WRAPPER_CLASSES}";
            $search[] = "{REPLY_TO_ICON}";
            $search[] = "{REPLY_TO_TEXT}";
            $search[] = "{REPLY_TO_HREF}";
            $search[] = "{REPLY_TO_USER_NAME}";
            $replace[] = "wpd-reply-to";
            $replace[] = "<i class='far fa-comments'></i>";
            $replace[] = esc_html($this->options->getPhrase("wc_reply_to", ["comment" => $comment])) . "&nbsp;";
            $replace[] = esc_url_raw($parentCommentLink);
            $replace[] = apply_filters("wpdiscuz_comment_author", $parentCommentUserName, $parentComment);
            $showReplyTo = true;
        }

        $comment->comment_content = apply_filters("comment_text", $comment->comment_content, $comment, $args);
        $commentReadMoreLimit = $this->options->content["commentReadMoreLimit"];
        if (stripos($comment->comment_content, "[/spoiler]") !== false) {
            $commentReadMoreLimit = 0;
            $comment->comment_content = $this->helper->spoiler($comment->comment_content);
        }
        if ($commentReadMoreLimit && WpdiscuzHelper::strWordCount(wp_strip_all_tags($comment->comment_content)) > $commentReadMoreLimit) {
            $comment->comment_content = WpdiscuzHelper::getCommentExcerpt($comment->comment_content, $uniqueId, $this->options);
        }
        $comment->comment_content = apply_filters("wpdiscuz_after_read_more", $comment->comment_content, $comment, $args);

        $showShare = false;
        if ($isApproved) {
            if ($args["is_share_enabled"]) {
                $shareButtons = $this->options->social["enableTwitterShare"] ? "<a class='wc_tw' rel='noreferrer' target='_blank' href='https://twitter.com/intent/tweet?text=" . $this->helper->getTwitterShareContent($comment->comment_content, $commentLink) . "&url=" . urlencode($commentLink) . "' title='" . esc_attr($this->options->getPhrase("wc_share_twitter")) . "'><i class='fab fa-twitter wpf-cta' aria-hidden='true'></i></a>" : "";
                $shareButtons .= $this->options->social["enableWhatsappShare"] ? "<a class='wc_whatsapp' rel='noreferrer' href='{$args["whatsapp_url"]}/send?text=" . $this->helper->getWhatsappShareContent($comment->comment_content, $commentLink) . "' target='_blank' title='" . esc_attr($this->options->getPhrase("wc_share_whatsapp")) . "'><i class='fab fa-whatsapp wpf-cta' aria-hidden='true'></i></a>" : "";
                $shareButtons .= $args["share_buttons"];
                $showShare = true;
                $search[] = "{SHARE_WRAPPER_CLASSES}";
                $search[] = "{SHARE_ICON}";
                $search[] = "{SHARE_TOOLTIP_CLASSES}";
                $search[] = "{SHARE_ICONS}";
                $replace[] = "wpd-comment-share wpd-hidden wpd-tooltip wpd-top";
                $replace[] = "<i class='fas fa-share-alt' aria-hidden='true'></i>";
                $replace[] = "wpd-tooltip-content";
                $replace[] = $shareButtons;
            }
        } else {
            $commentWrapperClass[] = "wpd-unapproved-comment";
            $statusIcons .= "<div class='wpd-unapproved'><i class='fas fa-exclamation-circle'></i>" . esc_html($this->options->getPhrase("wc_awaiting_for_approval", ["comment" => $comment])) . "</div>";
        }

        $trackOrPingback = $comment->comment_type === "pingback" || $comment->comment_type === "trackback";

        if ($isInline) {
            $commentWrapperClass[] = "wpd-inline-comment";
        }
        $commentWrapperClass[] = "wpd_comment_level-" . $depth;
        $commentWrapperClass = apply_filters("wpdiscuz_comment_wrap_classes", $commentWrapperClass, $comment);
        $wrapperClass = implode(" ", $commentWrapperClass);

        // begin printing comment template
        $commentOutput .= "<div id='wpd-comm-" . esc_attr($uniqueId) . "' class='" . esc_attr($wrapperClass) . "'>";

        $showAvatar = false;
        if ($this->options->thread_layouts["showAvatars"] && $this->options->wp["showAvatars"]) {
            $authorAvatar = $trackOrPingback ? "<img class='avatar avatar-" . esc_attr($user["gravatarArgs"]["wpdiscuz_gravatar_size"]) . " photo' width='" . esc_attr($user["gravatarArgs"]["wpdiscuz_gravatar_size"]) . "' height='" . esc_attr($user["gravatarArgs"]["wpdiscuz_gravatar_size"]) . "' src='" . esc_url($args["avatar_trackback"]) . "' alt='trackback'>" : $user["avatar"];
            if (isset($user["authorAvatarSprintf"])) {
                $authorAvatar = sprintf($user["authorAvatarSprintf"], $authorAvatar);
            }
            if (isset($user["socIcon"])) {
                $authorAvatar .= $user["socIcon"];
            }
            $showAvatar = true;
            $search[] = "{AVATAR_WRAPPER_CLASSES}";
            $search[] = "{AVATAR}";
            $replace[] = "wpd-avatar " . apply_filters("wpdiscuz_avatar_classes", "");
            $replace[] = $authorAvatar;
        }

        $showLabel = false;
        if (!$trackOrPingback) {
            $user["author_title"] = apply_filters("wpdiscuz_author_title", $user["author_title"], $comment);
            if ($user["author_title"]) {
                $showLabel = true;
                $search[] = "{LABEL_WRAPPER_CLASSES}";
                $search[] = "{LABEL_TOOLTIP_POSITION}";
                $search[] = "{LABEL}";
                $replace[] = "wpd-comment-label";
                $replace[] = $args["layout"] == 1 ? "right" : "top";
                $replace[] = esc_html($user["author_title"]);
            }
        }

        $showFollow = false;
        if ($args["can_user_follow"] && $args["current_user_email"] !== $comment->comment_author_email) {
            if (is_array($args["user_follows"]) && in_array($comment->comment_author_email, $args["user_follows"])) {
                $followClass = "wpd-unfollow wpd-follow-active";
                $followTip = esc_html($this->options->getPhrase("wc_unfollow_user", ["comment" => $comment]));
            } else {
                $followClass = "wpd-follow";
                $followTip = esc_html($this->options->getPhrase("wc_follow_user", ["comment" => $comment]));
            }
            $showFollow = true;
            $search[] = "{FOLLOW_WRAPPER_CLASSES}";
            $search[] = "{FOLLOW_TOOLTIP_TEXT}";
            $search[] = "{FOLLOW_TOOLTIP_POSITION}";
            $search[] = "{FOLLOW_ICON}";
            $replace[] = "wpd-follow-link wpd_not_clicked " . $followClass;
            $replace[] = esc_attr($followTip);
            $replace[] = $args["follow_tooltip_position"];
            $replace[] = "<i class='fas fa-rss' aria-hidden='true'></i>";
        }

        $commentLeftClass = apply_filters("wpdiscuz_comment_left_class", "");

        $uNameClasses = apply_filters("wpdiscuz_username_classes", "");
        $user["authorNameHtml"] .= apply_filters("wpdiscuz_after_comment_author", "", $comment, $user["user"]);

        $search[] = "{AUTHOR_WRAPPER_CLASSES}";
        $search[] = "{AUTHOR}";
        $replace[] = "wpd-comment-author " . esc_attr($uNameClasses);
        $replace[] = $user["authorNameHtml"];

        $showStatus = false;
        $statusIcons .= apply_filters("wpdiscuz_comment_type_icon", "", $comment, $user["user"], $args["current_user"]);
        if ($statusIcons) {
            $search[] = "{STATUS_WRAPPER_CLASSES}";
            $search[] = "{STATUS_ICONS}";
            $replace[] = "wpd-comment-status";
            $replace[] = $statusIcons;
            $showStatus = true;
        }

        $showLink = false;
        $beforeCommentLink = apply_filters("wpdiscuz_before_comment_link", "", $comment, $user["user"], $args["current_user"]);
        $afterCommentLink = apply_filters("wpdiscuz_after_comment_link", "", $comment, $user["user"], $args["current_user"]);
        if ($this->options->thread_layouts["showCommentLink"] || $beforeCommentLink || $afterCommentLink) {
            $commentLinkIcon = "";
            if ($beforeCommentLink) {
                $commentLinkIcon = $beforeCommentLink;
            }
            if ($this->options->thread_layouts["showCommentLink"]) {
                $commentLinkIcon .= apply_filters("wpdiscuz_comment_link_img", "<span wpd-tooltip='" . esc_attr($this->options->getPhrase("wc_comment_link", ["comment" => $comment])) . "' wpd-tooltip-position='left'><i class='fas fa-link' aria-hidden='true' data-wpd-clipboard='" . esc_url_raw($commentLink) . "'></i></span>", $comment);
            }
            if ($afterCommentLink) {
                $commentLinkIcon .= $afterCommentLink;
            }
            $showLink = true;
            $search[] = "{LINK_WRAPPER_CLASSES}";
            $search[] = "{LINK_ICON}";
            $replace[] = "wpd-comment-link wpd-hidden";
            $replace[] = $commentLinkIcon;
        }

        $showVote = false;
        if ($this->options->thread_layouts["showVotingButtons"] && $isApproved) {
            if ($this->options->thread_layouts["votingButtonsStyle"]) {
                $voteCount = isset($commentMetas[self::META_KEY_VOTES_SEPARATE]) ? maybe_unserialize($commentMetas[self::META_KEY_VOTES_SEPARATE][0]) : ["like" => 0, "dislike" => 0];
                $like = !empty($voteCount["like"]) ? intval($voteCount["like"]) : 0;
                $voteResult = "<div class='wpd-vote-result wpd-vote-result-like" . ($like ? " wpd-up" : "") . "' title='" . esc_attr($like) . "'>" . esc_html($this->helper->getNumber($like)) . "</div>";
                if ($this->options->thread_layouts["enableDislikeButton"]) {
                    $dislike = !empty($voteCount["dislike"]) ? intval($voteCount["dislike"]) : 0;
                    $voteResult .= "<div class='wpd-vote-result-sep'></div>";
                    $voteResult .= "<div class='wpd-vote-result wpd-vote-result-dislike" . ($dislike ? " wpd-down" : "") . "' title='" . esc_attr(-$dislike) . "'>" . esc_html($this->helper->getNumber(-$dislike)) . "</div>";
                }
            } else {
                $votes = isset($commentMetas[self::META_KEY_VOTES]) ? intval($commentMetas[self::META_KEY_VOTES][0]) : 0;
                $voteResult = "<div class='wpd-vote-result" . ($votes > 0 ? " wpd-up" : ($votes < 0 ? " wpd-down" : "")) . "' title='" . esc_attr($votes) . "'>" . esc_html($this->helper->getNumber($votes)) . "</div>";
            }
            $wpdUpClass = "";
            $wpdDownClass = "";
            if (isset($args["user_votes"][$comment->comment_ID])) {
                if ($args["user_votes"][$comment->comment_ID] > 0) {
                    $wpdUpClass = " wpd-up";
                } else if ($args["user_votes"][$comment->comment_ID] < 0) {
                    $wpdDownClass = " wpd-down";
                }
            }
            $search[] = "{VOTE_WRAPPER_CLASSES}";
            $search[] = "{VOTE_UP_CLASSES}";
            $search[] = "{VOTE_DOWN_CLASSES}";
            $search[] = "{VOTE_UP_ICON}";
            $search[] = "{VOTE_RESULT}";
            $search[] = "{VOTE_DOWN_ICON}";
            $replace[] = "wpd-vote";
            $replace[] = "wpd-vote-up wpd_not_clicked" . $wpdUpClass;
            $replace[] = "wpd-vote-down wpd_not_clicked" . ($this->options->thread_layouts["enableDislikeButton"] ? "" : " wpd-dislike-hidden") . $wpdDownClass;
            $replace[] = $args["voting_icons"][0];
            $replace[] = $voteResult;
            $replace[] = $args["voting_icons"][1];
            $showVote = true;
        }

        $showReply = false;
        if (!$isClosed) {
            if ($args["high_level_user"] || ($this->helper->isCommentEditable($comment) && $this->helper->canUserEditComment($comment, $args["current_user"], $args))) {
                $toolsActions = "<span class='wpd_editable_comment wpd-cta-button'>" . esc_html($this->options->getPhrase("wc_edit_text", ["comment" => $comment])) . "</span>" . $toolsActions;
            }

            if ($args["can_user_reply"] && $isApproved) {
                $showReply = true;
                $search[] = "{REPLY_WRAPPER_CLASSES}";
                $search[] = "{REPLY_ICON}";
                $search[] = "{REPLY_TEXT}";
                $replace[] = "wpd-reply-button";
                $replace[] = "<svg xmlns='https://www.w3.org/2000/svg' viewBox='0 0 24 24'><path d='M10 9V5l-7 7 7 7v-4.1c5 0 8.5 1.6 11 5.1-1-5-4-10-11-11z'/><path d='M0 0h24v24H0z' fill='none'/></svg>";
                $replace[] = esc_html($this->options->getPhrase("wc_reply_text", ["comment" => $comment]));
                $search[] = "{PANEL}";
                if ($args["layout"] == 3 && !$comment->comment_parent) {
                    $replace[] = "<div class='wpd-wpanel'></div>";
                } else {
                    $replace[] = "";
                }
            }
        }
        $afterReplyButton = apply_filters("wpdiscuz_after_reply_button", "", $comment, $user["user"], $args["current_user"]);

        $showToggle = false;
        if ($this->options->wp["threadComments"] && $depth < $this->options->wp["threadCommentsDepth"]) {
            if (isset($args["wpdiscuz_child_count_" . $comment->comment_ID])) {
                if ($countChildren = $args["wpdiscuz_child_count_" . $comment->comment_ID]) {
                    $commentWrapClass[] = "wpd-hidden-replies";
                    $search[] = "{TOGGLE_WRAPPER_CLASSES}";
                    $search[] = "{TOGGLE_TOOLTIP_TEXT}";
                    $search[] = "{TOGGLE_ICON}";
                    $replace[] = "wpd-toggle wpd-hidden wpd_not_clicked";
                    $replace[] = esc_html($this->options->getPhrase("wc_show_replies_text", ["comment" => $comment]));
                    $replace[] = "<span class='wpd-view-replies'><span class='wpd-view-replies-text'>" . esc_html($this->options->getPhrase("wc_show_replies_text", ["comment" => $comment])) . "</span> ($countChildren)</span><i class='fas fa-chevron-down'></i>";
                    $showToggle = true;
                }
            } else if ($comment->get_children(["post_id" => $args["post_id"]])) {
                $search[] = "{TOGGLE_WRAPPER_CLASSES}";
                $search[] = "{TOGGLE_TOOLTIP_TEXT}";
                $search[] = "{TOGGLE_ICON}";
                $replace[] = "wpd-toggle wpd-hidden wpd_not_clicked";
                $replace[] = esc_html($this->options->getPhrase("wc_hide_replies_text", ["comment" => $comment]));
                $replace[] = "<i class='fas fa-chevron-up'></i>";
                $showToggle = true;
            }
        }

        $toolsActions .= apply_filters("wpdiscuz_comment_buttons", "", $comment, $user["user"], $args["current_user"]);
        $showTools = false;
        if ($toolsActions) {
            $search[] = "{TOOLS_WRAPPER_CLASSES}";
            $search[] = "{TOOLS_TOOLTIP_TEXT}";
            $search[] = "{TOOLS_ICON}";
            $search[] = "{TOOLS_ACTIONS}";
            $replace[] = "wpd-tools wpd-hidden";
            $replace[] = esc_attr($this->options->getPhrase("wc_manage_comment", ["comment" => $comment]));
            $replace[] = "<i class='fas fa-cog'></i>";
            $replace[] = "<div class='wpd-tools-actions'>" . $toolsActions . "</div>";
            $search[] = "{SEPARATOR}";
            $replace[] = $showToggle ? "<div class='wpd-sep wpd-hidden'></div>" : "";
            $showTools = true;
        }

        $lastEdited = "";
        if ($this->options->moderation["displayEditingInfo"] && isset($commentMetas[self::META_KEY_LAST_EDITED_AT]) && isset($commentMetas[self::META_KEY_LAST_EDITED_BY])) {
            $lastEditUser = get_user_by(is_numeric($commentMetas[self::META_KEY_LAST_EDITED_BY][0]) ? "id" : "email", $commentMetas[self::META_KEY_LAST_EDITED_BY][0]);
            $username = $lastEditUser ? $lastEditUser->display_name : $comment->comment_author;
            $lastEdited = "<div class='wpd-comment-last-edited'><i class='far fa-edit'></i>" . esc_html(sprintf($this->options->getPhrase("wc_last_edited", ["comment" => $comment]), $this->helper->dateDiff($commentMetas[self::META_KEY_LAST_EDITED_AT][0]), $username)) . "</div>";
        }

        $commentWrapClass = array_merge($commentWrapClass, $user["commentWrapClass"], $user["commentWrapRoleClass"]);
        if ($args["layout"] == 1) {
            $search[] = "{WRAPPER_CLASSES}";
            $search[] = "{HEADER_WRAPPER_CLASSES}";
            $search[] = "{FOOTER_WRAPPER_CLASSES}";
            $search[] = "{RIGHT_WRAPPER_ID}";
            $search[] = "{RIGHT_WRAPPER_CLASSES}";
            $search[] = "{TEXT_WRAPPER_CLASSES}";
            $search[] = "{TEXT}";
            $search[] = "{LEFT_WRAPPER_CLASSES}";
            $replace[] = esc_attr(implode(' ', $commentWrapClass));
            $replace[] = "wpd-comment-header";
            $replace[] = "wpd-comment-footer";
            $replace[] = esc_attr("comment-" . $comment->comment_ID);
            $replace[] = "wpd-comment-right";
            $replace[] = "wpd-comment-text";
            $content = "";
            if ($isInline) {
                $content = "<div class='wpd-inline-feedback-wrapper'><span class='wpd-inline-feedback-info'>" . esc_html($this->options->getPhrase("wc_feedback_content_text")) . "</span> <i class='fas fa-quote-left'></i>" . wp_trim_words($this->feedbacks[$isInline]->content, $args["feedback_content_words_count"]) . "&quot;  <a class='wpd-feedback-content-link' data-feedback-content-id='{$this->feedbacks[$isInline]->id}' href='#wpd-inline-{$this->feedbacks[$isInline]->id}'>" . esc_html($this->options->getPhrase("wc_read_more")) . "</a></div>";
            }
            $replace[] = $content . $comment->comment_content;
            $replace[] = "wpd-comment-left " . esc_attr($commentLeftClass);
            $leftComponent = $showAvatar || $showLabel || $showFollow ? str_replace(["{AVATAR}", "{LABEL}", "{FOLLOW}"], [$showAvatar ? $args["components"]["avatar.html"] : "", ($showLabel ? $args["components"]["label.html"] : "") . apply_filters("wpdiscuz_after_label", "", $comment), $showFollow ? $args["components"]["follow.html"] : ""], $args["components"]["left.html"]) : "";
            $headerComponent = str_replace(["{AUTHOR}", "{DATE}", "{STATUS}", "{SHARE}", "{LINK}"], [$args["components"]["author.html"], $showDate ? $args["components"]["date.html"] : "", $showStatus ? $args["components"]["status.html"] : "", $showShare ? $args["components"]["share.html"] : "", $showLink ? $args["components"]["link.html"] : ""], $args["components"]["header.html"]);
            $footerComponent = $showVote || $showReply || $afterReplyButton || $showTools || $showToggle ? str_replace(["{VOTE}", "{REPLY}", "{TOOLS}", "{TOGGLE}"], [$showVote ? $args["components"]["vote.html"] : "", ($showReply ? $args["components"]["reply.html"] : "") . $afterReplyButton, $showTools ? $args["components"]["tools.html"] : "", $showToggle ? $args["components"]["toggle.html"] : ""], $args["components"]["footer.html"]) : "";
            $rightComponent = str_replace(["{HEADER}", "{REPLY_TO}", "{TEXT}", "{FOOTER}"], [$headerComponent, $showReplyTo ? $args["components"]["reply_to.html"] : "", $args["components"]["text.html"] . $lastEdited, $footerComponent], $args["components"]["right.html"]);
            $wrapperComponent = str_replace(["{LEFT}", "{RIGHT}"], [$leftComponent, $rightComponent], $args["components"]["wrapper.html"]);
            $commentOutput .= str_replace($search, $replace, $wrapperComponent);
        } else if ($args["layout"] == 2) {
            $search[] = "{WRAPPER_CLASSES}";
            $search[] = "{HEADER_WRAPPER_CLASSES}";
            $search[] = "{FOOTER_WRAPPER_CLASSES}";
            $search[] = "{RIGHT_WRAPPER_ID}";
            $search[] = "{RIGHT_WRAPPER_CLASSES}";
            $search[] = "{TEXT_WRAPPER_CLASSES}";
            $search[] = "{TEXT}";
            $search[] = "{USER_INFO_WRAPPER_CLASSES}";
            $search[] = "{USER_INFO_TOP_WRAPPER_CLASSES}";
            $search[] = "{USER_INFO_BOTTOM_WRAPPER_CLASSES}";
            $replace[] = esc_attr(implode(' ', $commentWrapClass));
            $replace[] = "wpd-comment-header";
            $replace[] = "wpd-comment-footer";
            $replace[] = esc_attr("comment-" . $comment->comment_ID);
            $replace[] = "wpd-comment-right";
            $replace[] = "wpd-comment-text";
            $content = "";
            if ($isInline) {
                $content = "<div class='wpd-inline-feedback-wrapper'><span class='wpd-inline-feedback-info'>" . esc_html($this->options->getPhrase("wc_feedback_content_text")) . "</span> <i class='fas fa-quote-left'></i>" . wp_trim_words($this->feedbacks[$isInline]->content, $args["feedback_content_words_count"]) . "&quot;  <a class='wpd-feedback-content-link' data-feedback-content-id='{$this->feedbacks[$isInline]->id}' href='#wpd-inline-{$this->feedbacks[$isInline]->id}'>" . esc_html($this->options->getPhrase("wc_read_more")) . "</a></div>";
            }
            $replace[] = $content . $comment->comment_content;
            $replace[] = "wpd-user-info";
            $replace[] = "wpd-uinfo-top";
            $replace[] = "wpd-uinfo-bottom";
            $userInfoTopComponent = str_replace(["{AUTHOR}", "{LABEL}", "{STATUS}", "{SHARE}"], [$args["components"]["author.html"], ($showLabel ? $args["components"]["label.html"] : "") . apply_filters("wpdiscuz_after_label", "", $comment), $showStatus ? $args["components"]["status.html"] : "", $showShare ? $args["components"]["share.html"] : ""], $args["components"]["user_info_top.html"]);
            $userInfoBottomComponent = $showFollow || $showReplyTo || $showDate ? str_replace(["{FOLLOW}", "{REPLY_TO}", "{DATE}"], [$showFollow ? $args["components"]["follow.html"] : "", $showReplyTo ? $args["components"]["reply_to.html"] : "", $showDate ? $args["components"]["date.html"] : ""], $args["components"]["user_info_bottom.html"]) : "";
            $userInfoComponent = str_replace(["{TOP}", "{BOTTOM}"], [$userInfoTopComponent, $userInfoBottomComponent], $args["components"]["user_info.html"]);
            $headerComponent = str_replace(["{AVATAR}", "{USER_INFO}", "{LINK}"], [$showAvatar ? $args["components"]["avatar.html"] : "", $userInfoComponent, $showLink ? $args["components"]["link.html"] : ""], $args["components"]["header.html"]);
            $footerComponent = $showVote || $showReply || $afterReplyButton || $showTools || $showToggle ? str_replace(["{VOTE}", "{REPLY}", "{TOOLS}", "{TOGGLE}"], [$showVote ? $args["components"]["vote.html"] : "", ($showReply ? $args["components"]["reply.html"] : "") . $afterReplyButton, $showTools ? $args["components"]["tools.html"] : "", $showToggle ? $args["components"]["toggle.html"] : ""], $args["components"]["footer.html"]) : "";
            $rightComponent = str_replace(["{HEADER}", "{TEXT}", "{FOOTER}"], [$headerComponent, $args["components"]["text.html"] . $lastEdited, $footerComponent], $args["components"]["right.html"]);
            $wrapperComponent = str_replace(["{RIGHT}"], [$rightComponent], $args["components"]["wrapper.html"]);
            $commentOutput .= str_replace($search, $replace, $wrapperComponent);
        } else if ($args["layout"] == 3) {
            $search[] = "{WRAPPER_CLASSES}";
            $search[] = "{HEADER_WRAPPER_CLASSES}";
            $search[] = "{FOOTER_WRAPPER_CLASSES}";
            $search[] = "{RIGHT_WRAPPER_ID}";
            $search[] = "{RIGHT_WRAPPER_CLASSES}";
            $search[] = "{TEXT_WRAPPER_CLASSES}";
            $search[] = "{TEXT}";
            $search[] = "{LEFT_WRAPPER_CLASSES}";
            $search[] = "{SUBHEADER_WRAPPER_CLASSES}";
            $search[] = "{TOOLS_WRAP_WRAPPER_CLASSES}";
            $replace[] = esc_attr(implode(' ', $commentWrapClass));
            $replace[] = "wpd-comment-header";
            $replace[] = "wpd-comment-footer";
            $replace[] = esc_attr("comment-" . $comment->comment_ID);
            $replace[] = "wpd-comment-right";
            $replace[] = "wpd-comment-text";
            $content = "";
            if ($isInline) {
                $content = "<div class='wpd-inline-feedback-wrapper'><span class='wpd-inline-feedback-info'>" . esc_html($this->options->getPhrase("wc_feedback_content_text")) . "</span> <i class=\"fas fa-quote-left\"></i>" . wp_trim_words($this->feedbacks[$isInline]->content, $args["feedback_content_words_count"]) . "&quot;  <a class='wpd-feedback-content-link' data-feedback-content-id='{$this->feedbacks[$isInline]->id}' href='#wpd-inline-{$this->feedbacks[$isInline]->id}'>" . esc_html($this->options->getPhrase("wc_read_more")) . "</a></div>";
            }
            $replace[] = $content . $comment->comment_content;
            $replace[] = "wpd-comment-left " . esc_attr($commentLeftClass);
            $replace[] = "wpd-comment-subheader";
            $replace[] = "wpd-tool-wrap";
            $subheaderComponent = !$comment->comment_parent && ($showLabel || $showDate || $showStatus) ? str_replace(["{LABEL}", "{DATE}", "{STATUS}"], [($showLabel ? $args["components"]["label.html"] : "") . apply_filters("wpdiscuz_after_label", "", $comment), $showDate ? $args["components"]["date.html"] : "", $showStatus ? $args["components"]["status.html"] : ""], $args["components"]["subheader.html"]) : "";
            $leftComponent = $showAvatar ? str_replace(["{AVATAR}"], [$args["components"]["avatar.html"]], $args["components"]["left.html"]) : "";
            $headerComponent = str_replace(["{AUTHOR}", "{LABEL}", "{FOLLOW}", "{SHARE}", "{STATUS}", "{LINK}"], [$args["components"]["author.html"], $comment->comment_parent ? ($showLabel ? $args["components"]["label.html"] : "") . apply_filters("wpdiscuz_after_label", "", $comment) : "", $showFollow ? $args["components"]["follow.html"] : "", $showShare ? $args["components"]["share.html"] : "", $comment->comment_parent && $showStatus ? $args["components"]["status.html"] : "", $showLink ? $args["components"]["link.html"] : ""], $args["components"]["header.html"]);
            $toolsWrapComponent = $showTools || $showToggle ? str_replace(["{TOOLS}", "{TOGGLE}"], [$showTools ? $args["components"]["tools.html"] : "", $showToggle ? $args["components"]["toggle.html"] : ""], $args["components"]["tools_wrap.html"]) : "";
            $replyToComponent = str_replace(["{DATE}"], [$showDate ? $args["components"]["date.html"] : ""], $args["components"]["reply_to.html"]);
            $footerComponent = $showVote || $showReply || $afterReplyButton || $toolsWrapComponent ? str_replace(["{VOTE}", "{REPLY}", "{TOOLS_WRAP}"], [$showVote ? $args["components"]["vote.html"] : "", ($showReply ? $args["components"]["reply.html"] : "") . $afterReplyButton, $toolsWrapComponent], $args["components"]["footer.html"]) : "";
            $rightComponent = str_replace(["{HEADER}", "{SUBHEADER}", "{REPLY_TO}", "{TEXT}", "{FOOTER}"], [$headerComponent, $subheaderComponent, $showReplyTo ? $replyToComponent : "", $args["components"]["text.html"] . $lastEdited, $footerComponent], $args["components"]["right.html"]);
            $wrapperComponent = str_replace(["{LEFT}", "{RIGHT}"], [$leftComponent, $rightComponent], $args["components"]["wrapper.html"]);
            $commentOutput .= str_replace($search, $replace, $wrapperComponent);
        }
        $commentOutput .= "<div id='wpdiscuz_form_anchor-" . esc_attr($uniqueId) . "'></div>";
        $output .= apply_filters("wpdiscuz_comment_end", $commentOutput, $comment, $depth, $args);
    }

    public function end_el(&$output, $comment, $depth = 0, $args = []) {
        $output = apply_filters("wpdiscuz_thread_end", $output, $comment, $depth, $args);
        $output .= "</div>";

        if (!empty($args["lastCommentIdInList"]) && !empty($args["commentsArgs"]) && $args["lastCommentIdInList"] === $comment->comment_ID && $this->extra) {
            $this->cache->setExtraCache($args["commentsArgs"], $this->extra);
        }

        return $output;
    }

}
