<?php

if (!defined("ABSPATH")) {
    exit();
}

class WpdiscuzAddons implements WpDiscuzConstants {

    /**
     * @var WpdiscuzOptions
     */
    private $options;

    private $addons;

    public function __construct($options) {
        
        if (is_admin() || !wp_doing_ajax()) {

            $this->options = $options;

            $this->initAddons();
            $this->initTips();

            add_action("wpdiscuz_submenu_page", [$this, "addonsMenu"]);
            add_action("wpdiscuz_addons_check", [$this, "addonsCheck"]);
            add_action("wp_ajax_dismiss_wpdiscuz_addon_note", [&$this, "dismissAddonNote"]);
            add_action("admin_notices", [&$this, "adminNotices"]);
        }
    }


    public function addonsMenu() {
        add_submenu_page(self::PAGE_WPDISCUZ,
            "&raquo; " . esc_html__("Addons", "wpdiscuz"),
            "&raquo; " . esc_html__("Addons", "wpdiscuz"),
            "manage_options",
            self::PAGE_ADDONS,
            [$this, "addonsPage"]
        );
    }

    public function addonsPage() {
        include_once WPDISCUZ_DIR_PATH . "/options/html-addons.php";
    }

    private function initAddons() {
        $this->addons = [
            "bundle" => [
                "version" => "7.0.0",
                "requires" => "7.0.0",
                "class" => "Bundle",
                "title" => "Addons Bundle",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/bundle/header.png"),
                "desc" => esc_html__("All 16 addons in one bundle. Save 90% and get Unlimited Site License with one year premium support.", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-addons-bundle/",
            ],
            "notifications" => [
                "version" => "1.0.0",
                "requires" => "7.3.7",
                "class" => "WunDBManager",
                "title" => "User Notifications",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/notifications/header.png"),
                "desc" => esc_html__("Ads a real-time user notification system and web push notifications in your website.", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-user-notifications/",
            ],
            "buddypress" => [
                "version" => "1.0.2",
                "requires" => "7.2.0",
                "class" => "wpDiscuzBPIntegration",
                "title" => "BuddyPress Integration",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/buddypress/header.png"),
                "desc" => esc_html__("Integrates wpDiscuz with BuddyPress plugin. Profile Tabs, Notifications, Activities, etc...", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-buddypress-integration/",
            ],
            "voice" => [
                "version" => "1.0.0",
                "requires" => "7.2.0",
                "class" => "wpDiscuzAudioComment",
                "title" => "Voice Commenting",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/voice/header.png"),
                "desc" => esc_html__("Allows to discuss with your voice in the comment section. Adds a microphone button to the comment form.", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-voice-commenting/",
            ],
            "tenor" => [
                "version" => "1.0.4",
                "requires" => "7.2.0",
                "class" => "wpDiscuzTenorIntegration",
                "title" => "Tenor GIFs Integration",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/tenor/header.png"),
                "desc" => esc_html__("Adds Tenor [GIF] button and opens popup where you can search for gifs and insert them in comment content.", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-tenor-integration/",
            ],
            "giphy" => [
                "version" => "1.0.0",
                "requires" => "7.2.0",
                "class" => "wpDiscuzGiphyIntegration",
                "title" => "GIPHY Integration",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/giphy/header.png"),
                "desc" => esc_html__("Adds Giphy [GIF] button and opens popup where you can search for gifs and insert them in comment content.", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-giphy-integration/",
            ],
            "uploader" => [
                "version" => "7.0.0",
                "requires" => "7.0.0",
                "class" => "WpdiscuzMediaUploader",
                "title" => "Media Uploader",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/uploader/header.png"),
                "desc" => esc_html__("Extended comment attachment system. Allows to upload images, videos, audios and other file types.", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-media-uploader/",
            ],
            "embeds" => [
                "version" => "1.0.0",
                "requires" => "7.0.0",
                "class" => "WpdiscuzEmbeds",
                "title" => "Embeds",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/embeds/header.png"),
                "desc" => esc_html__("Allows to embed lots of video, social network, audio and photo content providers URLs in comment content.", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-embeds/",
            ],
            "syntax" => [
                "version" => "1.0.0",
                "requires" => "7.0.0",
                "class" => "wpDiscuzSyntaxHighlighter",
                "title" => "Syntax Highlighter",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/syntax/header.png"),
                "desc" => esc_html__("Syntax highlighting for comments, automatic language detection and multi-language code highlighting.", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-syntax-highlighter/",
            ],
            "frontend-moderation" => [
                "version" => "7.0.0",
                "requires" => "7.0.0",
                "class" => "wpDiscuzFrontEndModeration",
                "title" => "Front-end Moderation",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/frontend-moderation/header.png"),
                "desc" => esc_html__("All in one powerful yet simple admin toolkit to moderate comments on front-end.", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-frontend-moderation/",
            ],
            "emoticons" => [
                "version" => "7.0.0",
                "requires" => "7.0.0",
                "class" => "wpDiscuzSmile",
                "title" => "Emoticons",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/emoticons/header.png"),
                "desc" => esc_html__("Brings an ocean of emotions to your comments. It comes with an awesome smile package.", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-emoticons/",
            ],
            "recaptcha" => [
                "version" => "7.0.0",
                "requires" => "7.0.0",
                "class" => "WpdiscuzRecaptcha",
                "title" => "Invisible reCAPTCHA v3",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/recaptcha/header.png"),
                "desc" => esc_html__("Adds Invisible reCAPTCHA on all comment forms. Stops spam and bot comments with reCAPTCHA version 3", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-recaptcha/",
            ],
            "author-info" => [
                "version" => "7.0.0",
                "requires" => "7.0.0",
                "class" => "WpdiscuzCommentAuthorInfo",
                "title" => "Comment Author Info",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/author-info/header.png"),
                "desc" => esc_html__("Extended information about comment author with Profile, Activity, Votes and Subscriptions Tabs on pop-up window.", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-comment-author-info/",
            ],
            "report-flagging" => [
                "version" => "7.0.0",
                "requires" => "7.0.0",
                "class" => "wpDiscuzFlagComment",
                "title" => "Report and Flagging",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/report/header.png"),
                "desc" => esc_html__("Comment reporting tools. Auto-moderates comments based on number of flags and dislikes.", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-report-flagging/",
            ],
            "online-users" => [
                "version" => "7.0.0",
                "requires" => "7.0.0",
                "class" => "WpdiscuzOnlineUsers",
                "title" => "Online Users",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/online-users/header.png"),
                "desc" => esc_html__("Real-time online user checking, pop-up notification of new online users and online/offline badges.", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-online-users/",
            ],
            "private" => [
                "version" => "7.0.0",
                "requires" => "7.0.0",
                "class" => "wpDiscuzPrivateComment",
                "title" => "Private Comments",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/private/header.png"),
                "desc" => esc_html__("Allows to create private comment threads. Rich management options in dashboard by user roles.", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-private-comments/",
            ],
            "subscriptions" => [
                "version" => "7.0.0",
                "requires" => "7.0.0",
                "class" => "wpdSubscribeManager",
                "title" => "Subscription Manager",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/subscriptions/header.png"),
                "desc" => esc_html__("Total control over comment subscriptions. Full list, monitor, manage, filter, unsubscribe, confirm...", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-subscribe-manager/",
            ],
            "ads-manager" => [
                "version" => "7.0.0",
                "requires" => "7.0.0",
                "class" => "WpdiscuzAdsManager",
                "title" => "Ads Manager",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/ads-manager/header.png"),
                "desc" => esc_html__("A full-fledged tool-kit for advertising in comment section of your website. Separate banner and ad managment.", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-ads-manager/",
            ],
            "user-mention" => [
                "version" => "7.0.0",
                "requires" => "7.0.0",
                "class" => "WpdiscuzUCM",
                "title" => "User &amp; Comment Mentioning",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/user-mention/header.png"),
                "desc" => esc_html__("Allows to mention comments and users in comment text using #comment-id and @username tags.", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-user-comment-mentioning/",
            ],
            "likers" => [
                "version" => "7.0.0",
                "requires" => "7.0.0",
                "class" => "WpdiscuzVoters",
                "title" => "Advanced Likers",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/likers/header.png"),
                "desc" => esc_html__("See comment likers and voters of each comment. Adds user reputation and badges based on received likes.", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-advanced-likers/",
            ],
            "search" => [
                "version" => "7.0.0",
                "requires" => "7.0.0",
                "class" => "wpDiscuzCommentSearch",
                "title" => "Comment Search",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/search/header.png"),
                "desc" => esc_html__("AJAX powered front-end comment search. It starts searching while you type search words. ", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-comment-search/",
            ],
            "widgets" => [
                "version" => "7.0.0",
                "requires" => "7.0.0",
                "class" => "wpDiscuzWidgets",
                "title" => "wpDiscuz Widgets",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/widgets/header.png"),
                "desc" => esc_html__("Most voted comments, Active comment threads, Most commented posts, Active comment authors", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-widgets/",
            ],
            "mycred" => [
                "version" => "7.0.0",
                "requires" => "7.0.0",
                "class" => "myCRED_Hook_wpDiscuz_Vote",
                "title" => "myCRED Integration",
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/mycred/header.png"),
                "desc" => esc_html__("Integrates myCRED Badges and Ranks. Converts wpDiscuz comment votes/likes to myCRED points. ", "wpdiscuz"),
                "url" => "https://gvectors.com/product/wpdiscuz-mycred/",
            ],
        ];
    }

    public function refreshAddonPage() {
        $lastHash = get_option("wpdiscuz-addon-note-dismissed");
        $currentHash = $this->addonHash();
        if ($lastHash !== $currentHash) {
            ?>
            <script language="javascript">jQuery(document).ready(function () {
                    location.reload();
                });</script>
            <?php
        }
    }

    public function addonHash() {
        $viewed = "BuddyPress Integration, Tenor GIFs Integration, Voice Commenting, GIPHY Integration, User Notifications";
//		foreach ($this->addons as $key => $addon) {
//			$viewed .= $addon["title"] . ",";
//		}
        $hash = $viewed;
        return $hash;
    }


    public function dismissAddonNoteOnPage() {
        $hash = $this->addonHash();
        update_option("wpdiscuz-addon-note-dismissed", $hash);
    }

    public function dismissAddonNote() {
        $hash = $this->addonHash();
        update_option("wpdiscuz-addon-note-dismissed", $hash);
        exit();
    }

    public function adminNotices() {
        if (current_user_can("manage_options")) {
            $this->addonNote(); //To-do Menu [count] notification
        }
    }

    private function addonNote() {
        if ((!empty($_GET["page"]) && in_array($_GET["page"], [
                    self::PAGE_WPDISCUZ,
                    self::PAGE_SETTINGS,
                    self::PAGE_PHRASES,
                    self::PAGE_TOOLS,
                    self::PAGE_ADDONS,
                ])) || strpos($_SERVER["REQUEST_URI"], "edit.php?post_type=wpdiscuz_form") !== false) {
            $lastHash = get_option("wpdiscuz-addon-note-dismissed");
            $lastHashArray = explode(",", $lastHash);
            $currentHash = "BuddyPress Integration, Tenor GIFs Integration, Voice Commenting, GIPHY Integration, User Notifications";
            if ($lastHash !== $currentHash && (!in_array("BuddyPress Integration", $lastHashArray) || !in_array("Tenor GIFs Integration", $lastHashArray) || !in_array("Voice Commenting", $lastHashArray) || !in_array("GIPHY Integration", $lastHashArray) || !in_array("User Notifications", $lastHashArray))
            ) {
                ?>
                <div class="updated notice wpdiscuz_addon_note is-dismissible" style="margin-top:10px;">
                    <p style="font-weight:normal; font-size:15px; border-bottom:1px dotted #DCDCDC; padding-bottom:10px; clear: both;">
                        <?php esc_html_e("New wpDiscuz addon!"); ?>
                    </p>
                    <div style="font-size:14px;">
                        <!--                        --><?php //if(!in_array("BuddyPress Integration", $lastHashArray)):                           ?>
                        <!--                            <div style="display:inline-block; min-width:27%; padding-right:10px; margin-bottom:10px;">-->
                        <!--                                <img src="-->
                        <?php //echo plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/buddypress/header.png");                          ?><!--" style="height:50px; width:auto; vertical-align:middle; margin:0px 10px; text-decoration:none; float: left;"/>-->
                        <!--                                <a href="https://gvectors.com/product/wpdiscuz-buddypress-integration/" target="_blank" style="color:#444; text-decoration:none;" title="-->
                        <?php //esc_attr_e("Go to the addon page", "wpdiscuz");                           ?><!--">wpDiscuz - BuddyPress Integration <br><span style="margin: 0; font-size: 12px; line-height: 15px; display: block; padding-top: 5px;">This addon integrates wpDiscuz with BuddyPress plugin. Creates &laquoDiscussion&raquo; tab in the users profile page, intgartes notifications, activities, and more...</span></a>-->
                        <!--                            </div>-->
                        <!--                        --><?php //endif;                           ?>
                        <?php if (!in_array("User Notifications", $lastHashArray)): ?>
                            <div style="display:inline-block; min-width:27%; padding-right:10px; margin-bottom:10px;">
                                <img src="<?php echo plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/notifications/header.png"); ?>"
                                     style="height:50px; width:auto; vertical-align:middle; margin:0px 10px; text-decoration:none; float: left;"/>
                                <a href="https://gvectors.com/product/wpdiscuz-user-notifications/" target="_blank"
                                   style="color:#444; text-decoration:none;"
                                   title="<?php esc_attr_e("Go to the addon page", "wpdiscuz"); ?>">wpDiscuz - User
                                    Notifications <br><span
                                            style="width: 60%; margin: 0; font-size: 12px; line-height: 15px; display: block; padding-top: 5px;">Adds a real-time user notification system to your site, so users can receive updates and notifications directly on your website as they happen (when someone likes your comment, rates your post, mentions you, replies to your comment).</span></a>
                            </div>
                        <?php endif; ?>
                        <!--	                    --><?php //if(!in_array("GIPHY Integration", $lastHashArray)):                          ?>
                        <!--                            <div style="display:inline-block; min-width:27%; padding-right:10px; margin-bottom:10px;">-->
                        <!--                                <img src="-->
                        <?php //echo plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/giphy/header.png");                           ?><!--" style="height:50px; width:auto; vertical-align:middle; margin:0px 10px; text-decoration:none; float: left;"/>-->
                        <!--                                <a href="https://gvectors.com/product/wpdiscuz-giphy-integration/" target="_blank" style="color:#444; text-decoration:none;" title="-->
                        <?php //esc_attr_e("Go to the addon page", "wpdiscuz");                           ?><!--">wpDiscuz - GIPHY Integration <br><span style="margin: 0; font-size: 12px; line-height: 15px; display: block; padding-top: 5px;">This adds GIPHY [GIF] button on the toolbar of comment editor. Clicking this will open a new popup where you can search for your favorite gifs and insert them in your comment content.</span></a>-->
                        <!--                            </div>-->
                        <!--	                    --><?php //endif;                           ?>
                        <!--				        --><?php //if(!in_array("Tenor GIFs Integration", $lastHashArray)):                           ?>
                        <!--                            <div style="display:inline-block; min-width:27%; padding-right:10px; margin-bottom:10px;">-->
                        <!--                                <img src="-->
                        <?php //echo plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/tenor/header.png");                           ?><!--" style="height:50px; width:auto; vertical-align:middle; margin:0px 10px; text-decoration:none; float: left;"/>-->
                        <!--                                <a href="https://gvectors.com/product/wpdiscuz-tenor-integration/" target="_blank" style="color:#444; text-decoration:none;" title="-->
                        <?php //esc_attr_e("Go to the addon page", "wpdiscuz");                           ?><!--">wpDiscuz - Tenor GIFs Integration <br><span style="margin: 0; font-size: 12px; line-height: 15px; display: block; padding-top: 5px;">This adds Tenor [GIF] button on the toolbar of comment editor. Clicking this will open a new popup where you can search for your favorite gifs and insert them in your comment content.</span></a>-->
                        <!--                            </div>-->
                        <!--				        --><?php //endif;                           ?>
                        <div style="clear:both;"></div>
                    </div>
                    <p>&nbsp;&nbsp;&nbsp;<a
                                href="<?php echo esc_url_raw(admin_url("admin.php?page=" . self::PAGE_ADDONS)); ?>"><?php esc_html_e("Go to wpDiscuz Addons subMenu"); ?>
                            &raquo;</a></p>
                </div>
                <?php
            }
        }
    }


    private function initTips() {
        $this->tips = [
            "custom-form" => [
                "title" => esc_html__("Custom Comment Forms", "wpdiscuz"),
                "text" => esc_html__("You can create custom comment forms with wpDiscuz. wpDiscuz 4 comes with custom comment forms and fields. You can create custom comment forms for each post type, each form can beceated with different form fields, for eaxample: text, dropdown, rating, checkboxes, etc...", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/tips/custom-form.png"),
                "url" => admin_url() . "edit.php?post_type=wpdiscuz_form",
            ],
            "emoticons" => [
                "title" => esc_html__("Emoticons", "wpdiscuz"),
                "text" => esc_html__("You can add more emotions to your comments using wpDiscuz Emoticons addon.", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/emoticons/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-emoticons/",
            ],
            "ads-manager" => [
                "title" => esc_html__("Ads Manager", "wpdiscuz"),
                "text" => esc_html__("Increase your income using ad banners. Comment area is the most active sections for advertising. wpDiscuz Ads Manager addon is designed to help you add banners and control ads in this section.", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/ads-manager/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-ads-manager/",
            ],
            "user-mention" => [
                "title" => esc_html__("User and Comment Mentioning", "wpdiscuz"),
                "text" => esc_html__("Using wpDiscuz User &amp; Comment Mentioning addon you can allow commenters mention comments and users in comment text using #comment-id and @username tags.", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/user-mention/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-user-comment-mentioning/",
            ],
            "likers" => [
                "title" => esc_html__("Advanced Likers", "wpdiscuz"),
                "text" => esc_html__("wpDiscuz Advanced Likers addon displays likers and voters of each comment. Adds user reputation and badges based on received likes.", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/likers/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-advanced-likers/",
            ],
            "report-flagging" => [
                "title" => esc_html__("Report and Flagging", "wpdiscuz"),
                "text" => esc_html__("Let your commenters help you to determine and remove spam comments. wpDiscuz Report and Flagging addon comes with comment reporting tools. Automaticaly auto-moderates comments based on number of flags and dislikes.", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/report/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-report-flagging/",
            ],
            "translate" => [
                "title" => esc_html__("Comment Translate", "wpdiscuz"),
                "text" => esc_html__("In most cases the big part of your visitors are not a native speakers of your language. Make your comments comprehensible for all visitors using wpDiscuz Comment Translation addon. It adds smart and intuitive AJAX 'Translate' button with 60 language translation options. Uses free translation API.", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/translate/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-comment-translation/",
            ],
            "search" => [
                "title" => esc_html__("Comment Search", "wpdiscuz"),
                "text" => esc_html__("You can let website visitor search in comments. It's always more attractive to find a comment about something that interest you. Using wpDiscuz Comment Search addon you'll get a nice, AJAX powered front-end comment search form above comment list.", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/search/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-comment-search/",
            ],
            "widgets" => [
                "title" => esc_html__("wpDiscuz Widgets", "wpdiscuz"),
                "text" => esc_html__("More Comment Widgets! Most voted comments, Active comment threads, Most commented posts, Active comment authors widgets are available in wpDiscuz Widgets Addon", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/widgets/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-widgets/",
            ],
            "frontend-moderation" => [
                "title" => esc_html__("Front-end Moderation", "wpdiscuz"),
                "text" => esc_html__("You can moderate comments on front-end using all in one powerful yet simple wpDiscuz Frontend Moderation addon.", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/frontend-moderation/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-frontend-moderation/",
            ],
            "uploader" => [
                "title" => esc_html__("Media Uploader", "wpdiscuz"),
                "text" => esc_html__("You can let website visitors attach images and files to comments and embed video/audio content using wpDiscuz Media Uploader addon.", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/uploader/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-media-uploader/",
            ],
            "recaptcha" => [
                "title" => esc_html__("Google ReCaptcha", "wpdiscuz"),
                "text" => esc_html__("Advanced spam protection with wpDiscuz Google reCAPTCHA addon. This addon adds No-CAPTCHA reCAPTCHA on all comment forms. Stops spam and bot comments.", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/recaptcha/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-recaptcha/",
            ],
        ];
    }

    public function dismissTipNote() {
        $hash = $this->tipHash();
        update_option("wpdiscuz-tip-note-dismissed", $hash);
        exit();
    }

    public function tipHash() {
        $viewed = "BuddyPress Integration, Tenor GIFs Integration, Voice Commenting, GIPHY Integration, wpDiscuz - User Notifications";
//		foreach ($this->tips as $key => $tip) {
//			$viewed .= $tip["title"] . ",";
//		}
        $hash = $viewed;
        return $hash;
    }

    public function tipDisplayed() {
        $tipTtile = substr(strip_tags($_GET["tip"]), 0, 100);
        $lastHash = get_option("wpdiscuz-tip-note-dismissed");
        if ($lastHash) {
            $lastHashArray = explode(",", $lastHash);
        } else {
            $lastHashArray = [];
        }
        $lastHashArray[] = $tipTtile;
        $hash = implode(",", $lastHashArray);
        return $hash;
    }

    /* Check addons licenses */

    public function addonsCheck() {
        $this->check();
    }

    private function check() {
        if (WpdiscuzHelper::getRealIPAddr() === "127.0.0.1") {
            return;
        }
        $patterns = [
            '\.qri$',
            '\.grfg$',
            '\.ybpny$',
            '\.fgntvat$',
            '\.rknzcyr$',
            '\.vainyvq$',
            '\.zlsgchcybnq\.pbz$',
            '\.pybhqjnlfnccf\.pbz$',
            '\.atebx\.vb$',
            '\.fgntvat\.jcratvar\.pbz$',
            '^ybpny\.',
            '^qri\.',
            '^grfg\.',
            '^fgntr\.',
            '^fgntvat\.',
            '^fgntvatA\.',
            '^qri\-[\j|-]+\.cnagurbafvgr\.vb',
            '^grfg\-[\j|-]+\.cnagurbafvgr\.vb',
            '^fgntvat\-[\j|-]+\.xvafgn\.pbz',
        ];
        $url_data = parse_url(get_bloginfo("url"));
        $domain = preg_replace('|^www\.|is', "", $url_data["host"]);
        foreach ($patterns as $pattern) {
            if (preg_match('@' . str_rot13($pattern) . '@is', $domain)) {
                return;
            }
        }
        $plugins = [];
        if (is_plugin_active("wpdiscuz-ads-manager/class-WpdiscuzAdsManager.php")) {
            global $wpdiscuzAM;
            $instance = null;
            if (!empty($wpdiscuzAM->apimanager)) {
                $instance = $wpdiscuzAM->apimanager;
            }
            $plugins["wpdiscuz-ads-manager"] = [
                "file" => "wpdiscuz-ads-manager/class-WpdiscuzAdsManager.php",
                "instance" => $instance,
                "name" => "wpDiscuz - Ads Manager",
            ];
        }
        if (is_plugin_active("wpdiscuz-advanced-likers/class.WpdiscuzVoters.php")) {
            global $wpDiscuzVoters;
            $instance = null;
            if (!empty($wpDiscuzVoters->apimanager)) {
                $instance = $wpDiscuzVoters->apimanager;
            }
            $plugins["wpdiscuz-advanced-likers"] = [
                "file" => "wpdiscuz-advanced-likers/class.WpdiscuzVoters.php",
                "instance" => $instance,
                "name" => "wpDiscuz - Advanced Liking",
            ];
        }
        if (is_plugin_active("wpdiscuz-comment-author-info/wpdiscuz-comment-author-info.php")) {
            global $wpdiscuzCommentAuthorInfo;
            $instance = null;
            if (!empty($wpdiscuzCommentAuthorInfo->apimanager)) {
                $instance = $wpdiscuzCommentAuthorInfo->apimanager;
            }
            $plugins["wpdiscuz-comment-author-info"] = [
                "file" => "wpdiscuz-comment-author-info/wpdiscuz-comment-author-info.php",
                "instance" => $instance,
                "name" => "wpDiscuz - Comment Author Info",
            ];
        }
        if (is_plugin_active("wpdiscuz-comment-search/wpDiscuzCommentSearch.php")) {
            global $wpDiscuzCommentSearch;
            $instance = null;
            if (!empty($wpDiscuzCommentSearch->apimanager)) {
                $instance = $wpDiscuzCommentSearch->apimanager;
            }
            $plugins["wpdiscuz-comment-search"] = [
                "file" => "wpdiscuz-comment-search/wpDiscuzCommentSearch.php",
                "instance" => $instance,
                "name" => "wpDiscuz - Comment Search",
            ];
        }
        if (is_plugin_active("wpdiscuz-comment-translation/wpdiscuz-translate.php")) {
            global $wpDiscuzTranslate;
            $instance = null;
            if (!empty($wpDiscuzTranslate->apimanager)) {
                $instance = $wpDiscuzTranslate->apimanager;
            }
            $plugins["wpdiscuz-comment-translation"] = [
                "file" => "wpdiscuz-comment-translation/wpdiscuz-translate.php",
                "instance" => $instance,
                "name" => "wpDiscuz - Comment Translation",
            ];
        }
        if (is_plugin_active("wpdiscuz-embeds/wpdiscuz-embeds.php")) {
            global $wpdiscuzEmbeds;
            $instance = null;
            if (!empty($wpdiscuzEmbeds->apimanager)) {
                $instance = $wpdiscuzEmbeds->apimanager;
            }
            $plugins["wpdiscuz-embeds"] = [
                "file" => "wpdiscuz-embeds/wpdiscuz-embeds.php",
                "instance" => $instance,
                "name" => "wpDiscuz - Embeds",
            ];
        }
        if (is_plugin_active("wpdiscuz-emoticons/wpDiscuzSmile.php")) {
            global $wpDiscuzSmile;
            $instance = null;
            if (!empty($wpDiscuzSmile->apimanager)) {
                $instance = $wpDiscuzSmile->apimanager;
            }
            $plugins["wpdiscuz-emoticons"] = [
                "file" => "wpdiscuz-emoticons/wpDiscuzSmile.php",
                "instance" => $instance,
                "name" => "wpDiscuz - Emoticons",
            ];
        }
        if (is_plugin_active("wpdiscuz-frontend-moderation/class.wpDiscuzFrontEndModeration.php")) {
            global $wpDiscuzFrontEndModeration;
            $instance = null;
            if (!empty($wpDiscuzFrontEndModeration->apimanager)) {
                $instance = $wpDiscuzFrontEndModeration->apimanager;
            }
            $plugins["wpdiscuz-frontend-moderation"] = [
                "file" => "wpdiscuz-frontend-moderation/class.wpDiscuzFrontEndModeration.php",
                "instance" => $instance,
                "name" => "wpDiscuz - Front-end Moderation",
            ];
        }
        if (is_plugin_active("wpdiscuz-media-uploader/class.WpdiscuzMediaUploader.php")) {
            global $wpdiscuzMU;
            $instance = null;
            if (!empty($wpdiscuzMU->apimanager)) {
                $instance = $wpdiscuzMU->apimanager;
            }
            $plugins["wpdiscuz-media-uploader"] = [
                "file" => "wpdiscuz-media-uploader/class.WpdiscuzMediaUploader.php",
                "instance" => $instance,
                "name" => "wpDiscuz - Media Uploader",
            ];
        }
        if (is_plugin_active("wpdiscuz-mycred/wpdiscuz-mc.php")) {
            global $wpdiscuzMycredIntegrationApi;
            $instance = null;
            if (!empty($wpdiscuzMycredIntegrationApi)) {
                $instance = $wpdiscuzMycredIntegrationApi;
            }
            $plugins["wpdiscuz-mycred"] = [
                "file" => "wpdiscuz-mycred/wpdiscuz-mc.php",
                "instance" => $instance,
                "name" => "wpDiscuz - myCRED Integration",
            ];
        }
        if (is_plugin_active("wpdiscuz-online-users/wpdiscuz-ou.php")) {
            global $wpdiscuzOU;
            $instance = null;
            if (!empty($wpdiscuzOU->apimanager)) {
                $instance = $wpdiscuzOU->apimanager;
            }
            $plugins["wpdiscuz-online-users"] = [
                "file" => "wpdiscuz-online-users/wpdiscuz-ou.php",
                "instance" => $instance,
                "name" => "wpDiscuz - Online Users",
            ];
        }
        if (is_plugin_active("wpdiscuz-private-comments/wpDiscuzPrivateComment.php")) {
            global $wpDiscuzPrivateComment;
            $instance = null;
            if (!empty($wpDiscuzPrivateComment->apimanager)) {
                $instance = $wpDiscuzPrivateComment->apimanager;
            }
            $plugins["wpdiscuz-private-comments"] = [
                "file" => "wpdiscuz-private-comments/wpDiscuzPrivateComment.php",
                "instance" => $instance,
                "name" => "wpDiscuz - Private Comments",
            ];
        }
        if (is_plugin_active("wpdiscuz-recaptcha/wpDiscuzReCaptcha.php")) {
            global $wpDiscuzReCaptcha;
            $instance = null;
            if (!empty($wpDiscuzReCaptcha->apimanager)) {
                $instance = $wpDiscuzReCaptcha->apimanager;
            }
            $plugins["wpdiscuz-recaptcha"] = [
                "file" => "wpdiscuz-recaptcha/wpDiscuzReCaptcha.php",
                "instance" => $instance,
                "name" => "wpDiscuz - Google reCAPTCHA",
            ];
        }
        if (is_plugin_active("wpdiscuz-report-flagging/wpDiscuzFlagComment.php")) {
            global $wpDiscuzFlagComment;
            $instance = null;
            if (!empty($wpDiscuzFlagComment->apimanager)) {
                $instance = $wpDiscuzFlagComment->apimanager;
            }
            $plugins["wpdiscuz-report-flagging"] = [
                "file" => "wpdiscuz-report-flagging/wpDiscuzFlagComment.php",
                "instance" => $instance,
                "name" => "wpDiscuz - Report and Flagging",
            ];
        }
        if (is_plugin_active("wpdiscuz-subscribe-manager/wpdSubscribeManager.php")) {
            global $wpdiscuzSubscribeManager;
            $instance = null;
            if (!empty($wpdiscuzSubscribeManager->apimanager)) {
                $instance = $wpdiscuzSubscribeManager->apimanager;
            }
            $plugins["wpdiscuz-subscribe-manager"] = [
                "file" => "wpdiscuz-subscribe-manager/wpdSubscribeManager.php",
                "instance" => $instance,
                "name" => "wpDiscuz - Subscription Manager",
            ];
        }
        if (is_plugin_active("wpdiscuz-syntax-highlighter/wpDiscuzSyntaxHighlighter.php")) {
            global $wpDiscuzSyntaxHighlighter;
            $instance = null;
            if (!empty($wpDiscuzSyntaxHighlighter->apimanager)) {
                $instance = $wpDiscuzSyntaxHighlighter->apimanager;
            }
            $plugins["wpdiscuz-syntax-highlighter"] = [
                "file" => "wpdiscuz-syntax-highlighter/wpDiscuzSyntaxHighlighter.php",
                "instance" => $instance,
                "name" => "wpDiscuz - Syntax Highlighter",
            ];
        }
        if (is_plugin_active("wpdiscuz-user-comment-mentioning/WpdiscuzUCM.php")) {
            global $wpDiscuzUCM;
            $instance = null;
            if (!empty($wpDiscuzUCM->apimanager)) {
                $instance = $wpDiscuzUCM->apimanager;
            }
            $plugins["wpdiscuz-user-comment-mentioning"] = [
                "file" => "wpdiscuz-user-comment-mentioning/WpdiscuzUCM.php",
                "instance" => $instance,
                "name" => "wpDiscuz - User & Comment Mentioning",
            ];
        }
        if (is_plugin_active("wpdiscuz-widgets/wpDiscuzWidgets.php")) {
            global $wpdiscuzWidgets;
            $instance = null;
            if (!empty($wpdiscuzWidgets->apimanager)) {
                $instance = $wpdiscuzWidgets->apimanager;
            }
            $plugins["wpdiscuz-widgets"] = [
                "file" => "wpdiscuz-widgets/wpDiscuzWidgets.php",
                "instance" => $instance,
                "name" => "wpDiscuz - Widgets",
            ];
        }
        if (is_plugin_active("wpdiscuz-buddypress-integration/wpDiscuzBPIntegration.php")) {
            global $wpDiscuzBPIntegration;
            $instance = null;
            if (!empty($wpDiscuzBPIntegration->apimanager)) {
                $instance = $wpDiscuzBPIntegration->apimanager;
            }
            $plugins["wpdiscuz-buddypress-integration"] = [
                "file" => "wpdiscuz-buddypress-integration/wpDiscuzBPIntegration.php",
                "instance" => $instance,
                "name" => "wpDiscuz - BuddyPress Integration",
            ];
        }
        if (is_plugin_active("wpdiscuz-tenor-integration/wpDiscuzTenorIntegration.php")) {
            global $wpDiscuzTenorIntegration;
            $instance = null;
            if (!empty($wpDiscuzTenorIntegration->apimanager)) {
                $instance = $wpDiscuzTenorIntegration->apimanager;
            }
            $plugins["wpdiscuz-tenor-integration"] = [
                "file" => "wpdiscuz-tenor-integration/wpDiscuzTenorIntegration.php",
                "instance" => $instance,
                "name" => "wpDiscuz - Tenor GIFs Integration",
            ];
        }
        if (is_plugin_active("wpdiscuz-giphy-integration/wpDiscuzGiphyIntegration.php")) {
            global $wpDiscuzGiphyIntegration;
            $instance = null;
            if (!empty($wpDiscuzGiphyIntegration->apimanager)) {
                $instance = $wpDiscuzGiphyIntegration->apimanager;
            }
            $plugins["wpdiscuz-giphy-integration"] = [
                "file" => "wpdiscuz-giphy-integration/wpDiscuzGiphyIntegration.php",
                "instance" => $instance,
                "name" => "wpDiscuz - GIPHY Integration",
            ];
        }
        if (is_plugin_active("wpdiscuz-voice-commenting/wpdAudioComment.php")) {
            global $wpdiscuzAudioComment;
            $instance = null;
            if (!empty($wpdiscuzAudioComment->apimanager)) {
                $instance = $wpdiscuzAudioComment->apimanager;
            }
            $plugins["wpdiscuz-voice-commenting"] = [
                "file" => "wpdiscuz-voice-commenting/wpdAudioComment.php",
                "instance" => $instance,
                "name" => "wpDiscuz - Voice Commenting",
            ];
        }
        if (is_plugin_active("wpdiscuz-user-notifications/wun-index.php")) {
            global $wpdiscuzUserNotifications;
            $instance = null;
            if (!empty($wpdiscuzUserNotifications->apimanager)) {
                $instance = $wpdiscuzUserNotifications->apimanager;
            }
            $plugins["wpdiscuz-user-notifications"] = [
                "file" => "wpdiscuz-user-notifications/wun-index.php",
                "instance" => $instance,
                "name" => "wpDiscuz - User Notifications",
            ];
        }
        $checkedData = get_option("wpd_checked_data", []);
        $deactivatePlugins = [];
        $adminNotices = [];
        foreach ($plugins as $key => $value) {
            $redpoint = (int)get_option("gvt_product_" . $key . "_redpoint", "0");
            if (!$redpoint) {
                $checkedData[$key] = [
                    "last_checked" => $this->getLastCheckedDate(),
                    "checked_count" => 0,
                    "valid" => 1,
                ];
            } else if (isset($checkedData[$key])) {
                $diff = $this->getLastCheckedDiff($checkedData[$key]["last_checked"]);
                if ($checkedData[$key]["checked_count"] > 1) {
                    if ($diff->d >= 1 || (($diff->y || $diff->m) && !$diff->d)) {
                        $deactivatePlugins[] = $value["file"];
                        $checkedData[$key] = [
                            "last_checked" => $this->getLastCheckedDate(),
                            "checked_count" => $checkedData[$key]["checked_count"] + 1,
                            "valid" => 0,
                        ];
                        $adminNotices[$key] = sprintf(__("%s addon was deactivated, because your license isn't valid.", "wpdiscuz"), $value["name"]);
                    }
                } else if ($diff->m >= 1) {
                    $deactivatePlugins[] = $value["file"];
                    $checkedData[$key] = [
                        "last_checked" => $this->getLastCheckedDate(),
                        "checked_count" => $checkedData[$key]["checked_count"] + 1,
                        "valid" => 0,
                    ];
                    $adminNotices[$key . "_redpoint"] = sprintf(__("Something is wrong with %s addon license and files. Please activate it using its license key. If this addon has not been purchased and downloaded from the official gVectors.com website, it's probably hacked and may lead to lots of security issues.", "wpdiscuz"), $value["name"]);
                    $adminNotices[$key] = sprintf(__("%s addon was deactivated, because your license isn't valid.", "wpdiscuz"), $value["name"]);
                }
            } else {
                $checkedData[$key] = [
                    "last_checked" => $this->getLastCheckedDate(),
                    "checked_count" => 1,
                    "valid" => 0,
                ];
            }
        }
        if ($deactivatePlugins) {
            deactivate_plugins($deactivatePlugins);
        }
        if ($adminNotices) {
            $notices = get_option("wpd_admin_notices", []);
            update_option("wpd_admin_notices", array_merge($notices, $adminNotices));
        }
        update_option("wpd_checked_data", $checkedData, "no");
    }

    private function getLastCheckedDiff($date) {
        $now = new DateTime($this->getLastCheckedDate());
        $ago = new DateTime($date);
        return $now->diff($ago);
    }

    private function getLastCheckedDate() {
        return current_time("Y-m-d H:i:s");
    }
}
