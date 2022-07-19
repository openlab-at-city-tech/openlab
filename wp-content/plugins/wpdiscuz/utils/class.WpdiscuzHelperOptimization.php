<?php

if (!defined("ABSPATH")) {
    exit();
}

class WpdiscuzHelperOptimization implements WpDiscuzConstants {

    private $options;
    private $dbManager;
    private $helperEmail;
    private $helper;

    public function __construct($options, $dbManager, $helperEmail, $helper) {
        $this->options = $options;
        $this->dbManager = $dbManager;
        $this->helperEmail = $helperEmail;
        $this->helper = $helper;
        add_action("deleted_comment", [&$this, "cleanCommentRelatedRows"], 10, 2);
        add_action("delete_user", [&$this, "deleteUserRelatedData"], 11);
        add_action("profile_update", [&$this, "onProfileUpdate"], 10, 2);
        add_action("admin_post_removeVoteData", [&$this, "removeVoteData"]);
        add_action("admin_post_resetPhrases", [&$this, "resetPhrases"]);
        add_action("transition_comment_status", [&$this, "statusEventHandler"], 10, 3);
        add_action("edit_comment", [&$this, "commentEdited"], 10, 2);
        add_action("post_updated", [&$this, "actionsOnUpdatedPost"]);
        add_action("deleted_post", [&$this, "actionsOnDeletedPost"]);
        add_action("updated_option", [&$this, "optionUpdated"]);
        add_action("bp_members_avatar_uploaded", [&$this, "bpAvatarUploaded"]);
        add_action("wpforo_update_profile_after", [&$this, "wpfProfileUpdate"]);
        add_action("deactivate_plugin", [&$this, "pluginDeactivated"]);
        add_action("wpdiscuz_clean_post_cache", [&$this, "cleanPostCache"]);
        add_action("wpdiscuz_clean_all_caches", [&$this, "cleanAllCaches"]);
        if ($this->isApplicableToRequest()) {
            add_filter('comments_pre_query', [&$this, "addCustomVariables"], PHP_INT_MAX, 2);
            add_filter('the_comments', [&$this, "deleteCustomVariable"], PHP_INT_MIN, 2);
        }
    }

    /**
     * recursively get new comments tree
     * return array of comments' ids
     */
    public function getTreeByParentId($commentId, &$tree) {
        $children = $this->dbManager->getCommentsByParentId($commentId);
        if ($children && is_array($children)) {
            foreach ($children as $child) {
                if (!in_array($child, $tree)) {
                    $tree[] = $child;
                    $this->getTreeByParentId($child, $tree);
                }
            }
        }
    }

    /**
     * add new comment id in comment meta if status is approved
     *
     * @param type $newStatus the comment new status
     * @param type $oldStatus the comment old status
     * @param type $comment current comment object
     */
    public function statusEventHandler($newStatus, $oldStatus, $comment) {
        if ($newStatus === "approved") {
            $currentUser = wp_get_current_user();
            if ((!empty($currentUser->user_email) && $currentUser->user_email !== $comment->comment_author_email) && $this->options->subscription["isNotifyOnCommentApprove"]) {
                $this->helperEmail->notifyOnApproving($comment);
            }
            $this->notifyOnApprove($comment);
        }
        do_action("wpdiscuz_reset_comments_cache", $comment->comment_post_ID);
    }

    public function commentEdited($comment_ID, $data) {
        do_action("wpdiscuz_reset_comments_cache", $data["comment_post_ID"]);
    }

    public function actionsOnUpdatedPost($post_ID) {
        do_action("wpdiscuz_reset_comments_cache", $post_ID);
    }

    public function actionsOnDeletedPost($post_id) {
        $this->dbManager->removeRatings($post_id);
        $this->dbManager->deleteFeedbackFormsForPost($post_id);
        do_action("wpdiscuz_reset_comments_cache", $post_id);
    }

    public function optionUpdated($option) {
        if (in_array($option, ["page_comments", "comments_per_page", "thread_comments", "thread_comments_depth", "default_comments_page", "comment_order"])) {
            do_action("wpdiscuz_reset_comments_cache");
        }
    }

    public function bpAvatarUploaded($userId) {
        $user = get_user_by("id", $userId);
        do_action("wpdiscuz_reset_users_cache", $userId . "_" . $user->user_email . "_" . $user->display_name);
    }

    public function wpfProfileUpdate() {
        do_action("wpdiscuz_reset_users_cache", WPF()->current_object["user"]["userid"] . "_" . WPF()->current_object["user"]["user_email"] . "_" . WPF()->current_object["user"]["display_name"]);
        do_action("wpdiscuz_reset_comments_cache");
    }

    public function pluginDeactivated($plugin) {
        if (in_array($plugin, ["wpdiscuz/class.WpdiscuzCore.php", "wpforo/wpforo.php", "buddypress/bp-loader.php", "ultimate-member/ultimate-member.php"], true)) {
            do_action("wpdiscuz_reset_users_cache");
            do_action("wpdiscuz_reset_comments_cache");
        }
    }

    /**
     * get the current comment root comment
     *
     * @param int $commentId the current comment id
     *
     * @return WP_Comment comment
     */
    public function getCommentRoot($commentId, $commentStatusIn, $includeUnapproved = null) {
        $comment = get_comment($commentId);
        $condition = false;
        if (!is_null($includeUnapproved)) {
            if (is_numeric($includeUnapproved)) {
                if ($comment->user_id == $includeUnapproved) {
                    $condition = true;
                }
            } else if ($comment->comment_author_email === $includeUnapproved) {
                $condition = true;
            }
        }
        if (in_array($comment->comment_approved, $commentStatusIn) || ($comment->comment_approved === "0" && $condition)) {
            if ($comment && $comment->comment_parent) {
                return $this->getCommentRoot($comment->comment_parent, $commentStatusIn, $includeUnapproved);
            } else {
                return $comment;
            }
        }
        return null;
    }

    public function getCommentDepth($commentId, &$depth = 1) {
        $comment = get_comment($commentId);
        if ($comment->comment_parent && ($depth < $this->options->wp["threadCommentsDepth"])) {
            $depth++;
            return $this->getCommentDepth($comment->comment_parent, $depth);
        } else {
            return $depth;
        }
    }

    private function notifyOnApprove($comment) {
        $postId = $comment->comment_post_ID;
        $commentId = $comment->comment_ID;
        $email = $comment->comment_author_email;
        $parentComment = get_comment($comment->comment_parent);
        if (apply_filters("wpdiscuz_enable_user_mentioning", $this->options->subscription["enableUserMentioning"]) && $this->options->subscription["sendMailToMentionedUsers"] && ($mentionedUsers = $this->helper->getMentionedUsers($comment->comment_content))) {
            $this->helperEmail->sendMailToMentionedUsers($mentionedUsers, $comment);
        }
        do_action("wpdiscuz_before_sending_emails", $commentId, $comment);
        $this->helperEmail->notifyPostSubscribers($postId, $commentId, $email);
        $this->helperEmail->notifyFollowers($postId, $commentId, $email);
        if ($parentComment) {
            $parentCommentEmail = $parentComment->comment_author_email;
            if ($parentCommentEmail !== $email) {
                $this->helperEmail->notifyAllCommentSubscribers($postId, $commentId, $email);
                $this->helperEmail->notifyCommentSubscribers($parentComment->comment_ID, $commentId, $email);
            }
        }
    }

    public function removeVoteData() {
        if (isset($_GET["_wpnonce"]) && wp_verify_nonce($_GET["_wpnonce"], "removeVoteData") && current_user_can("manage_options")) {
            $this->dbManager->removeVotes();
            do_action("wpdiscuz_remove_vote_data");
            wp_redirect(admin_url("admin.php?page=" . self::PAGE_SETTINGS . "&wpd_tab=" . self::TAB_GENERAL));
        }
    }

    public function resetPhrases() {
        if (isset($_GET["_wpnonce"]) && wp_verify_nonce($_GET["_wpnonce"], "reset_phrases_nonce") && current_user_can("manage_options")) {
            $this->dbManager->deletePhrases();
            wp_redirect(admin_url("admin.php?page=" . self::PAGE_PHRASES));
        }
    }

    public function cleanCommentRelatedRows($commentId, $comment = null) {
        $this->dbManager->deleteSubscriptions($commentId);
        $this->dbManager->deleteVotes($commentId);
        if ($comment && !empty($comment->comment_post_ID)) {
            $this->cleanPostCache($comment->comment_post_ID);
        }
    }

    public function onProfileUpdate($userId, $oldUser) {
        $user = get_user_by("id", $userId);
        if ($user && $oldUser) {
            if (($user->user_email !== $oldUser->user_email || $user->display_name !== $oldUser->display_name || $user->user_url !== $oldUser->user_url) && $this->dbManager->userHasComments($userId)) {
                $this->dbManager->updateCommenterData($user->user_email, $user->display_name, $user->user_url, $userId);
            }
            $this->dbManager->updateUserInfo($user, $oldUser);
        }
        do_action("wpdiscuz_reset_comments_cache");
        do_action("wpdiscuz_reset_users_cache", $userId . "_" . $oldUser->user_email . "_" . $oldUser->display_name);
    }

    public function deleteUserRelatedData($id) {
        $user = get_user_by("id", $id);
        if ($user && $user->user_email) {
            $this->dbManager->deleteSubscriptionsByEmail($user->user_email);
            $this->dbManager->deleteFollowersByEmail($user->user_email);
            $this->dbManager->deleteFollowsByEmail($user->user_email);
        }
        $this->dbManager->deleteUserVotes($id);
    }

    public function cleanPostCache($postId) {
        if (apply_filters("wpdiscuz_manage_post_cache_clearing", true)) {
            clean_post_cache($postId);
            if (defined("LSCWP_V")) {
                do_action("litespeed_purge_post", $postId);
            }
            if (function_exists("rocket_clean_post")) {
                rocket_clean_post($postId);
            }
            if (function_exists("wpfc_clear_post_cache_by_id")) {
                wpfc_clear_post_cache_by_id($postId);
            }
            if (function_exists("fvm_purge_all")) {
                fvm_purge_all();
            }
            if (function_exists("fvm_purge_others")) {
                fvm_purge_others();
            }
            if (function_exists("w3tc_flush_post")) {
                w3tc_flush_post($postId);
            }
            if (is_callable(["WPO_Page_Cache", "delete_single_post_cache"])) {
                WPO_Page_Cache::delete_single_post_cache($postId);
            }
            if (class_exists("\SiteGround_Optimizer\Supercacher\Supercacher")) {
                \SiteGround_Optimizer\Supercacher\Supercacher::purge_cache();
                \SiteGround_Optimizer\Supercacher\Supercacher::flush_memcache();
                \SiteGround_Optimizer\Supercacher\Supercacher::delete_assets();
            }
        }

        update_post_meta($postId, self::POSTMETA_STATISTICS, []);
    }

    public function cleanAllCaches() {
        if (apply_filters("wpdiscuz_manage_all_cache_clearing", true)) {
            wp_cache_flush();
            if (class_exists("\LiteSpeed\Purge")) {
                \LiteSpeed\Purge::purge_all();
            }
            if (function_exists("rocket_clean_domain")) {
                rocket_clean_domain();
            }
            if (function_exists("wpfc_clear_all_cache")) {
                wpfc_clear_all_cache(true);
            }
            $fvm = get_option("fastvelocity_min_ignore");
            if (is_string($fvm) && strpos($fvm, "/wp-content/plugins/wpdiscuz/*") === false) {
                if ($fvm) {
                    $fvm .= "\n";
                }
                $fvm .= "/wp-content/plugins/wpdiscuz/*";
                update_option("fastvelocity_min_ignore", $fvm);
            }
            if (function_exists("fvm_purge_all")) {
                fvm_purge_all();
            }
            if (function_exists("fvm_purge_others")) {
                fvm_purge_others();
            }
            if (function_exists("w3tc_flush_all")) {
                w3tc_flush_all();
            }
            if (class_exists("autoptimizeCache")) {
                autoptimizeCache::clearall();
            }
            if (class_exists("\SiteGround_Optimizer\Supercacher\Supercacher")) {
                \SiteGround_Optimizer\Supercacher\Supercacher::purge_cache();
                \SiteGround_Optimizer\Supercacher\Supercacher::flush_memcache();
                \SiteGround_Optimizer\Supercacher\Supercacher::delete_assets();
            }
        }
    }

    //Integration with Redis or Memcached
    
    private function isApplicableToRequest(){
        if(!wp_doing_ajax()) {
            return false;
        }
        if(!isset($_REQUEST["action"]) || sanitize_text_field($_REQUEST["action"]) !== "wpdLoadMoreComments") {
            return false;
        }
        return true;
    }
    
    public function addCustomVariables($comment_data, $query) {
        $query->query_var_defaults["wpdiscuz"] = "temporary_from_" . __CLASS__ . "::" . __METHOD__;
        $this->addWpDiscuzParams($query);
        return $comment_data;
    }

    public function deleteCustomVariable($_comments, $query) {
        unset($query->query_var_defaults["wpdiscuz"]);
        return $_comments;
    }

    
    private function addWpDiscuzParams($query){
        $query->query_vars["wpdiscuz"] = wp_array_slice_assoc($_REQUEST , $this->getWpDiscuzSpecificArgs());
    }

    private function getWpDiscuzSpecificArgs(){
        return ["lastParentId", "isFirstLoad", "offset", "sorting"];
    }
}
