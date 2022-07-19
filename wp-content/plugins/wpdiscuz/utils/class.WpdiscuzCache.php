<?php

if (!defined("ABSPATH")) {
    exit();
}

class WpdiscuzCache implements WpDiscuzConstants {

    public $wpUploadsDir;
    private $options;
    private $helper;

    public function __construct($options, $helper) {
        $this->options = $options;
        $this->helper = $helper;
        $this->wpUploadsDir = wp_upload_dir();
        add_action("admin_post_purgeAllCaches", [&$this, "purgeAllCaches"]);
        add_action("admin_post_purgePostCaches", [&$this, "purgePostCaches"]);
        add_action("wpdiscuz_reset_users_cache", [&$this, "resetUsersCache"]);
        add_action("wpdiscuz_reset_comments_cache", [&$this, "resetCommentsCache"]);
        add_action("wpdiscuz_reset_comments_extra_cache", [&$this, "resetExtraCache"]);
        add_action("comment_post", [&$this, "commentPost"], 248, 3);
    }

    public function purgeAllCaches() {
        if (current_user_can("manage_options") && isset($_GET["_wpnonce"]) && wp_verify_nonce(sanitize_text_field($_GET["_wpnonce"]), "purgeAllCaches")) {
            $this->resetCommentsCache();
            $this->resetUsersCache();
        }
        $referer = wp_get_referer();
        if (strpos($referer, "page=" . self::PAGE_SETTINGS) === false) {
            $redirect = $referer;
        } else {
            $redirect = admin_url("admin.php?page=" . self::PAGE_SETTINGS . "&wpd_tab=" . self::TAB_GENERAL);
        }
        wp_redirect($redirect);
        exit();
    }

    public function purgePostCaches() {
        if (current_user_can("manage_options") && isset($_GET["_wpnonce"]) && !empty($_GET["post_id"]) && wp_verify_nonce(sanitize_text_field($_GET["_wpnonce"]), "purgePostCaches")) {
            $this->resetCommentsCache(sanitize_text_field($_GET["post_id"]));
            $this->resetUsersCache();
        }
        wp_redirect(wp_get_referer());
        exit();
    }

    public function deleteGravatarsFolder() {
        if (!class_exists("WP_Filesystem_Direct")) {
            require_once ABSPATH . "wp-admin/includes/class-wp-filesystem-base.php";
            require_once ABSPATH . "wp-admin/includes/class-wp-filesystem-direct.php";
        }
        $fs = new WP_Filesystem_Direct([]);
        $fs->rmdir($this->wpUploadsDir["basedir"] . "/wpdiscuz/cache/gravatars/", true);
    }

    /*
     * User and Comment Cache
     */

    public function getUserCache($userKey) {
        return $this->getCache($this->getUserCacheFileinfo($userKey));
    }

    public function getCommentsCache($commentsArgs) {
        return $this->getCache($this->getCommentsCacheFileinfo($commentsArgs));
    }

    public function getExtraCache($args) {
        return $this->getCache($this->getExtraCacheFileinfo($args));
    }

    public function setUserCache($userKey, $user) {
        unset($user["author_title"], $user["commentWrapRoleClass"]);
        return $this->setCache($this->getUserCacheFileinfo($userKey), $user);
    }

    public function setCommentsCache($commentsArgs, $commentList, $commentData) {
        if (!$this->helper->isUnapprovedInTree($commentList)) {
            $data = ["commentList" => $commentList, "commentData" => $commentData];
            return $this->setCache($this->getCommentsCacheFileinfo($commentsArgs), $data);
        }
        return false;
    }

    public function setExtraCache($commentsArgs, $extraData) {
        return $this->setCache($this->getExtraCacheFileinfo($commentsArgs), $extraData);
    }

    public function resetUsersCache($userKey = "") {
        if ($userKey) {
            $dirs = $this->getUserCacheFileinfo($userKey);
            $path = $dirs["path"];
        } else {
            $dirs = $this->getCacheDirectories();
            $path = $dirs["users"];
        }
        $this->resetCache($path);
    }

    public function resetCommentsCache($postId = 0) {
        $dirs = $this->getCacheDirectories();
        $path = $dirs["comments"] . ($postId ? ($postId . "/") : "");
        $this->resetCache($path);
    }

    public function resetExtraCache($postId) {
        $dirs = $this->getCacheDirectories();
        $path = $dirs["comments"] . $postId . "/" . $dirs["extra"];
        $this->resetCache($path);
    }

    private function getCache($fileInfo) {
        // removing stat caches to avoid unexpected results
        clearstatcache();
        if ($this->options->general["isCacheEnabled"] && file_exists($fileInfo["path"])) {
            /**
             * delete old cached file
             * !!!IMPORTANT
             * do not use current_time here as it returns WP time
             */
            if (is_file($fileInfo["path"]) && time() > @filemtime($fileInfo["path"]) + ($this->options->general["cacheTimeout"] * DAY_IN_SECONDS)) {
                @unlink($fileInfo["path"]);
                return [];
            }
            if (is_readable($fileInfo["path"]) && ($cache = maybe_unserialize(file_get_contents($fileInfo["path"]))) && is_array($cache)) {
                return $cache;
            }
        }
        return [];
    }

    private function setCache($fileInfo, $data) {
        if ($this->options->general["isCacheEnabled"]) {
            // removing stat caches to avoid unexpected results
            clearstatcache();
            if (!is_dir($fileInfo["dir"])) {
                wp_mkdir_p($fileInfo["dir"]);
            }

            $htaccces = ".htaccess";
            if (is_dir($fileInfo["basedir"]) && !file_exists($fileInfo["basedir"] . $htaccces)) {
                file_put_contents($fileInfo["basedir"] . $htaccces, "Deny from all");
            }

            if (is_writable($fileInfo["dir"]) && ($data = serialize($data))) {
                return file_put_contents($fileInfo["path"], $data);
            }
        }
        return false;
    }

    private function resetCache($path) {
        if (!class_exists("WP_Filesystem_Direct")) {
            require_once ABSPATH . "wp-admin/includes/class-wp-filesystem-base.php";
            require_once ABSPATH . "wp-admin/includes/class-wp-filesystem-direct.php";
        }
        $fs = new WP_Filesystem_Direct([]);
        $fs->rmdir($path, true);
    }

    private function getUserCacheFileinfo($userKey) {
        $dirs = $this->getCacheDirectories();
        $fileName = md5($userKey);
        return [
            "basedir" => $dirs["users"],
            "dir" => $dirs["users"],
            "name" => $fileName,
            "path" => $dirs["users"] . $fileName,
        ];
    }

    private function getCommentsCacheFileinfo($commentsArgs) {
        $dirs = $this->getCacheDirectories();
        $fileDir = $dirs["comments"] . $commentsArgs["post_id"] . "/";
        $fileName = md5(implode(",", $commentsArgs["user_roles"]) . $commentsArgs["wpdType"] . $commentsArgs["last_parent_id"] . $commentsArgs["page"] . $commentsArgs["order"] . $commentsArgs["orderby"]) . "_" . $commentsArgs["last_parent_id"];
        return [
            "basedir" => $dirs["comments"],
            "name" => $fileName,
            "path" => $fileDir . $fileName,
            "dir" => $fileDir,
        ];
    }

    private function getExtraCacheFileinfo($commentsArgs) {
        $dirs = $this->getCacheDirectories();
        $fileDir = $dirs["comments"] . $commentsArgs["post_id"] . "/" . $dirs["extra"] . "/";
        $fileName = md5(implode(",", $commentsArgs["user_roles"]) . $commentsArgs["wpdType"] . $commentsArgs["last_parent_id"] . $commentsArgs["page"] . $commentsArgs["order"] . $commentsArgs["orderby"]) . "_" . $commentsArgs["last_parent_id"];
        return [
            "basedir" => $dirs["extra"],
            "name" => $fileName,
            "path" => $fileDir . $fileName,
            "dir" => $fileDir,
        ];
    }

    private function getCacheDirectories() {
        return [
            "comments" => $this->wpUploadsDir["basedir"] . self::COMMENTS_CACHE_DIR,
            "users" => $this->wpUploadsDir["basedir"] . self::USERS_CACHE_DIR,
            "extra" => self::EXTRA_CACHE_DIR,
        ];
    }

    public function commentPost($comment_ID, $approved, $commentdata) {
        if (!empty($commentdata['comment_post_ID'])) {
            $this->resetCommentsCache($commentdata['comment_post_ID']);
        }
    }

}
