<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<!-- Option start -->
<div class="wpd-opt-row">
    <div class="wpd-opt-intro">
        <img class="wpd-opt-img" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/" . $setting["icon"])); ?>" style="height: 64px; padding-top: 5px;"/>
        <?php echo sprintf(__("wpDiscuz allows users to get all kind of news from your website comment system, such as new comments, new replies, double opt-in subscription, user mentioning, user following and new comments by followed users. You can manage all those options here. All those options are based on email notifications. You can manage email templates in wpDiscuz > Phrases > Email Tab. <br>In wpDiscuz > Dashboard page, you can find a quick overview of user subscriptions. For an advanced subscriptions management tool, please checkout %s addon.", "wpdiscuz"), "<a href='https://gvectors.com/product/wpdiscuz-subscribe-manager/'  target='_blank' style='color:#07B290;'>wpDiscuz Subscription Manager</a>"); ?>
    </div>
    <div class="wpd-opt-doc" style="padding-top: 10px;">
        <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/subscription-and-user-following/" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="enableUserMentioning">
    <div class="wpd-opt-name">
        <label for="enableUserMentioning"><?php echo esc_html($setting["options"]["enableUserMentioning"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["enableUserMentioning"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->subscription["enableUserMentioning"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[enableUserMentioning]" id="enableUserMentioning">
            <label for="enableUserMentioning"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableUserMentioning"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="sendMailToMentionedUsers">
    <div class="wpd-opt-name">
        <label for="sendMailToMentionedUsers"><?php echo esc_html($setting["options"]["sendMailToMentionedUsers"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["sendMailToMentionedUsers"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->subscription["sendMailToMentionedUsers"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[sendMailToMentionedUsers]" id="sendMailToMentionedUsers">
            <label for="sendMailToMentionedUsers"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["sendMailToMentionedUsers"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="isNotifyOnCommentApprove">
    <div class="wpd-opt-name">
        <label for="isNotifyOnCommentApprove"><?php echo esc_html($setting["options"]["isNotifyOnCommentApprove"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["isNotifyOnCommentApprove"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->subscription["isNotifyOnCommentApprove"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[isNotifyOnCommentApprove]" id="isNotifyOnCommentApprove">
            <label for="isNotifyOnCommentApprove"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["isNotifyOnCommentApprove"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="enableMemberConfirm">
    <div class="wpd-opt-name">
        <label for="enableMemberConfirm"><?php echo esc_html($setting["options"]["enableMemberConfirm"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["enableMemberConfirm"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->subscription["enableMemberConfirm"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[enableMemberConfirm]" id="enableMemberConfirm">
            <label for="enableMemberConfirm"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableMemberConfirm"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="enableGuestsConfirm">
    <div class="wpd-opt-name">
        <label for="enableGuestsConfirm"><?php echo esc_html($setting["options"]["enableGuestsConfirm"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["enableGuestsConfirm"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->subscription["enableGuestsConfirm"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[enableGuestsConfirm]" id="enableGuestsConfirm">
            <label for="enableGuestsConfirm"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableGuestsConfirm"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="subscriptionType">
    <div class="wpd-opt-name">
        <label for="subscriptionType"><?php echo esc_html($setting["options"]["subscriptionType"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["subscriptionType"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-radio">
            <input type="radio" value="2" <?php checked(2 == $this->subscription["subscriptionType"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[subscriptionType]" id="subscriptionTypePost" />
            <label for="subscriptionTypePost" class="wpd-radio-circle"></label>
            <label for="subscriptionTypePost"><?php esc_html_e("Subscribe to all comments of this post", "wpdiscuz") ?></label>
        </div>
        <div class="wpd-radio">
            <input type="radio" value="3" <?php checked(3 == $this->subscription["subscriptionType"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[subscriptionType]" id="subscriptionTypeAllComments" />
            <label for="subscriptionTypeAllComments" class="wpd-radio-circle"></label>
            <label for="subscriptionTypeAllComments"><?php esc_html_e("Subscribe to all replies to my comments", "wpdiscuz") ?></label>
        </div>
        <div class="wpd-radio">
            <input type="radio" value="1" <?php checked(1 == $this->subscription["subscriptionType"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[subscriptionType]" id="subscriptionTypeBoth" />
            <label for="subscriptionTypeBoth" class="wpd-radio-circle"></label>
            <label for="subscriptionTypeBoth"><?php esc_html_e("Both", "wpdiscuz") ?></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["subscriptionType"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="showReplyCheckbox">
    <div class="wpd-opt-name">
        <label for="showReplyCheckbox"><?php echo esc_html($setting["options"]["showReplyCheckbox"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["showReplyCheckbox"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->subscription["showReplyCheckbox"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[showReplyCheckbox]" id="showReplyCheckbox">
            <label for="showReplyCheckbox"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["showReplyCheckbox"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="isReplyDefaultChecked">
    <div class="wpd-opt-name">
        <label for="isReplyDefaultChecked"><?php echo esc_html($setting["options"]["isReplyDefaultChecked"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["isReplyDefaultChecked"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->subscription["isReplyDefaultChecked"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[isReplyDefaultChecked]" id="isReplyDefaultChecked">
            <label for="isReplyDefaultChecked"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["isReplyDefaultChecked"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<?php if (class_exists("Prompt_Comment_Form_Handling")) { ?>
    <!-- Option start -->
    <div class="wpd-opt-row" data-wpd-opt="usePostmaticForCommentNotification">
        <div class="wpd-opt-name">
            <label for="usePostmaticForCommentNotification"><?php echo esc_html($setting["options"]["usePostmaticForCommentNotification"]["label"]) ?></label>
            <p class="wpd-desc"><?php echo esc_html($setting["options"]["usePostmaticForCommentNotification"]["description"]) ?></p>
        </div>
        <div class="wpd-opt-input">
            <div class="wpd-switcher">
                <input type="checkbox" <?php checked($this->subscription["usePostmaticForCommentNotification"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[usePostmaticForCommentNotification]" id="usePostmaticForCommentNotification" />
                <label for="usePostmaticForCommentNotification"></label>
            </div>
        </div>
        <div class="wpd-opt-doc">
            <?php $this->printDocLink($setting["options"]["usePostmaticForCommentNotification"]["docurl"]) ?>
        </div>
    </div>
    <!-- Option end -->
<?php } ?>

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="isFollowActive">
    <div class="wpd-opt-name">
        <label for="isFollowActive"><?php echo esc_html($setting["options"]["isFollowActive"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["isFollowActive"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->subscription["isFollowActive"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[isFollowActive]" id="isFollowActive">
            <label for="isFollowActive"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["isFollowActive"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="disableFollowConfirmForUsers">
    <div class="wpd-opt-name">
        <label for="disableFollowConfirmForUsers"><?php echo esc_html($setting["options"]["disableFollowConfirmForUsers"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["disableFollowConfirmForUsers"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->subscription["disableFollowConfirmForUsers"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[disableFollowConfirmForUsers]" id="disableFollowConfirmForUsers">
            <label for="disableFollowConfirmForUsers"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["disableFollowConfirmForUsers"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<div class="wpd-accordion">

    <div class="wpd-accordion-item">

        <div class="wpd-subtitle fas wpd-accordion-title" style="margin-top: 20px;" data-wpd-selector="wpd-subscription-templates">
            <p><?php esc_html_e("Subscription email templates", "wpdiscuz") ?></p>
        </div>

        <div class="wpd-accordion-content">

            <div class="wpd-subtitle wpd-subtitle" style="margin-top: 20px;">
                <?php esc_html_e("Subscription Type: Post new comment", "wpdiscuz"); ?>
            </div>

            <!-- Option start -->
            <div class="wpd-opt-row" data-wpd-opt="emailSubjectPostComment">
                <div class="wpd-opt-name">
                    <label for="emailSubjectPostComment"><?php esc_html_e($setting["options"]["emailSubjectPostComment"]["label"]) ?></label>
                    <p class="wpd-desc"><?php esc_html_e($setting["options"]["emailSubjectPostComment"]["description"]) ?></p>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?></p>
                    <p class="wpd-desc">
                        <span class="wc_available_variable" data-wpd-clipboard="[BLOG_TITLE]">[BLOG_TITLE]</span>
                        <span class="wc_available_variable" data-wpd-clipboard="[POST_TITLE]">[POST_TITLE]</span>
                        <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_AUTHOR]">[COMMENT_AUTHOR]</span>
                    </p>
                </div>
                <div class="wpd-opt-input">
                    <input type="text" value="<?php esc_attr_e($this->subscription["emailSubjectPostComment"]); ?>" name="<?php echo WpdiscuzCore::TAB_SUBSCRIPTION; ?>[emailSubjectPostComment]" id="emailSubjectPostComment" />
                </div>
                <div class="wpd-opt-doc">
                    <?php $this->printDocLink($setting["options"]["emailSubjectPostComment"]["docurl"]) ?>
                </div>
            </div>
            <!-- Option end -->

            <!-- Option start -->
            <div class="wpd-opt-row" data-wpd-opt="emailContentPostComment">
                <div class="wpd-opt-name">
                    <label for="emailContentPostComment"><?php esc_html_e($setting["options"]["emailContentPostComment"]["label"]) ?></label>
                    <p class="wpd-desc"><?php esc_html_e($setting["options"]["emailContentPostComment"]["description"]) ?></p>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?></p>
                    <span class="wc_available_variable" data-wpd-clipboard="[SITE_URL]">[SITE_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[POST_URL]">[POST_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[BLOG_TITLE]">[BLOG_TITLE]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[POST_TITLE]">[POST_TITLE]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[SUBSCRIBER_NAME]">[SUBSCRIBER_NAME]</span>
                    <p class="wpd-desc"><?php esc_html_e("Shortcodes above will work for registered users only", "wpdiscuz"); ?></p>
                    <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_URL]">[COMMENT_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_AUTHOR]">[COMMENT_AUTHOR]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_CONTENT]">[COMMENT_CONTENT]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[UNSUBSCRIBE_URL]">[UNSUBSCRIBE_URL]</span>
                </div>
                <div class="wpd-opt-input">
                    <?php
                    wp_editor($this->subscription["emailContentPostComment"], "emailContentPostComment", [
                        "textarea_name" => WpdiscuzCore::TAB_SUBSCRIPTION . "[emailContentPostComment]",
                        "textarea_rows" => 10,
                        "teeny" => true,
                        "media_buttons" => true,
                        "default_editor" => "tmce"
                    ]);
                    ?>                    
                </div>
                <div class="wpd-opt-doc">
                    <?php $this->printDocLink($setting["options"]["emailContentPostComment"]["docurl"]) ?>
                </div>
            </div>
            <!-- Option end -->

            <div class="wpd-subtitle wpd-subtitle" style="margin-top: 20px;">
                <?php esc_html_e("Subscription Type: Subscriber's comments", "wpdiscuz"); ?>
            </div>

            <!-- Option start -->
            <div class="wpd-opt-row" data-wpd-opt="emailSubjectAllCommentReply">
                <div class="wpd-opt-name">
                    <label for="emailSubjectAllCommentReply"><?php esc_html_e($setting["options"]["emailSubjectAllCommentReply"]["label"]) ?></label>
                    <p class="wpd-desc"><?php esc_html_e($setting["options"]["emailSubjectAllCommentReply"]["description"]) ?></p>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?></p>
                    <p class="wpd-desc">
                        <span class="wc_available_variable" data-wpd-clipboard="[BLOG_TITLE]">[BLOG_TITLE]</span>
                        <span class="wc_available_variable" data-wpd-clipboard="[POST_TITLE]">[POST_TITLE]</span>
                        <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_AUTHOR]">[COMMENT_AUTHOR]</span>
                    </p>
                </div>
                <div class="wpd-opt-input">
                    <input type="text" value="<?php esc_attr_e($this->subscription["emailSubjectAllCommentReply"]); ?>" name="<?php echo WpdiscuzCore::TAB_SUBSCRIPTION; ?>[emailSubjectAllCommentReply]" id="emailSubjectAllCommentReply" />
                </div>
                <div class="wpd-opt-doc">
                    <?php $this->printDocLink($setting["options"]["emailSubjectAllCommentReply"]["docurl"]) ?>
                </div>
            </div>
            <!-- Option end -->

            <!-- Option start -->
            <div class="wpd-opt-row" data-wpd-opt="emailContentAllCommentReply">
                <div class="wpd-opt-name">
                    <label for="emailContentAllCommentReply"><?php esc_html_e($setting["options"]["emailContentAllCommentReply"]["label"]) ?></label>
                    <p class="wpd-desc"><?php esc_html_e($setting["options"]["emailContentAllCommentReply"]["description"]) ?></p>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?></p>
                    <span class="wc_available_variable" data-wpd-clipboard="[SITE_URL]">[SITE_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[POST_URL]">[POST_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[BLOG_TITLE]">[BLOG_TITLE]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[POST_TITLE]">[POST_TITLE]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[SUBSCRIBER_NAME]">[SUBSCRIBER_NAME]</span>
                    <p class="wpd-desc"><?php esc_html_e("Shortcodes above will work for registered users only", "wpdiscuz"); ?></p>
                    <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_URL]">[COMMENT_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_AUTHOR]">[COMMENT_AUTHOR]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_CONTENT]">[COMMENT_CONTENT]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[UNSUBSCRIBE_URL]">[UNSUBSCRIBE_URL]</span>
                </div>
                <div class="wpd-opt-input">
                    <?php
                    wp_editor($this->subscription["emailContentAllCommentReply"], "emailContentAllCommentReply", [
                        "textarea_name" => WpdiscuzCore::TAB_SUBSCRIPTION . "[emailContentAllCommentReply]",
                        "textarea_rows" => 10,
                        "teeny" => true,
                        "media_buttons" => true,
                        "default_editor" => "tmce"
                    ]);
                    ?>                    
                </div>
                <div class="wpd-opt-doc">
                    <?php $this->printDocLink($setting["options"]["emailContentAllCommentReply"]["docurl"]) ?>
                </div>
            </div>
            <!-- Option end -->

            <div class="wpd-subtitle wpd-subtitle" style="margin-top: 20px;">
                <?php esc_html_e("Subscription Type: Subscriber's specific comment", "wpdiscuz"); ?>
            </div>

            <!-- Option start -->
            <div class="wpd-opt-row" data-wpd-opt="emailSubjectCommentReply">
                <div class="wpd-opt-name">
                    <label for="emailSubjectCommentReply"><?php esc_html_e($setting["options"]["emailSubjectCommentReply"]["label"]) ?></label>
                    <p class="wpd-desc"><?php esc_html_e($setting["options"]["emailSubjectCommentReply"]["description"]) ?></p>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?></p>
                    <p class="wpd-desc">
                        <span class="wc_available_variable" data-wpd-clipboard="[BLOG_TITLE]">[BLOG_TITLE]</span>
                        <span class="wc_available_variable" data-wpd-clipboard="[POST_TITLE]">[POST_TITLE]</span>
                        <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_AUTHOR]">[COMMENT_AUTHOR]</span>
                    </p>
                </div>
                <div class="wpd-opt-input">
                    <input type="text" value="<?php esc_attr_e($this->subscription["emailSubjectCommentReply"]); ?>" name="<?php echo WpdiscuzCore::TAB_SUBSCRIPTION; ?>[emailSubjectCommentReply]" id="emailSubjectCommentReply" />
                </div>
                <div class="wpd-opt-doc">
                    <?php $this->printDocLink($setting["options"]["emailSubjectCommentReply"]["docurl"]) ?>
                </div>
            </div>
            <!-- Option end -->

            <!-- Option start -->
            <div class="wpd-opt-row" data-wpd-opt="emailContentCommentReply">
                <div class="wpd-opt-name">
                    <label for="emailContentCommentReply"><?php esc_html_e($setting["options"]["emailContentCommentReply"]["label"]) ?></label>
                    <p class="wpd-desc"><?php esc_html_e($setting["options"]["emailContentCommentReply"]["description"]) ?></p>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?></p>
                    <span class="wc_available_variable" data-wpd-clipboard="[SITE_URL]">[SITE_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[POST_URL]">[POST_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[BLOG_TITLE]">[BLOG_TITLE]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[POST_TITLE]">[POST_TITLE]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[SUBSCRIBER_NAME]">[SUBSCRIBER_NAME]</span>
                    <p class="wpd-desc"><?php esc_html_e("Shortcodes above will work for registered users only", "wpdiscuz"); ?></p>
                    <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_URL]">[COMMENT_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_AUTHOR]">[COMMENT_AUTHOR]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_CONTENT]">[COMMENT_CONTENT]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[UNSUBSCRIBE_URL]">[UNSUBSCRIBE_URL]</span>
                </div>
                <div class="wpd-opt-input">
                    <?php
                    wp_editor($this->subscription["emailContentCommentReply"], "emailContentCommentReply", [
                        "textarea_name" => WpdiscuzCore::TAB_SUBSCRIPTION . "[emailContentCommentReply]",
                        "textarea_rows" => 10,
                        "teeny" => true,
                        "media_buttons" => true,
                        "default_editor" => "tmce"
                    ]);
                    ?>                    
                </div>
                <div class="wpd-opt-doc">
                    <?php $this->printDocLink($setting["options"]["emailContentCommentReply"]["docurl"]) ?>
                </div>
            </div>
            <!-- Option end -->

            <div class="wpd-subtitle wpd-subtitle" style="margin-top: 20px;">
                <?php esc_html_e("Subscription confirmation", "wpdiscuz"); ?>
            </div>

            <!-- Option start -->
            <div class="wpd-opt-row" data-wpd-opt="emailSubjectSubscriptionConfirmation">
                <div class="wpd-opt-name">
                    <label for="emailSubjectSubscriptionConfirmation"><?php esc_html_e($setting["options"]["emailSubjectSubscriptionConfirmation"]["label"]) ?></label>
                    <p class="wpd-desc"><?php esc_html_e($setting["options"]["emailSubjectSubscriptionConfirmation"]["description"]) ?></p>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?></p>
                    <p class="wpd-desc">
                        <span class="wc_available_variable" data-wpd-clipboard="[BLOG_TITLE]">[BLOG_TITLE]</span>
                        <span class="wc_available_variable" data-wpd-clipboard="[POST_TITLE]">[POST_TITLE]</span>
                    </p>
                </div>
                <div class="wpd-opt-input">
                    <input type="text" value="<?php esc_attr_e($this->subscription["emailSubjectSubscriptionConfirmation"]); ?>" name="<?php echo WpdiscuzCore::TAB_SUBSCRIPTION; ?>[emailSubjectSubscriptionConfirmation]" id="emailSubjectSubscriptionConfirmation" />
                </div>
                <div class="wpd-opt-doc">
                    <?php $this->printDocLink($setting["options"]["emailSubjectSubscriptionConfirmation"]["docurl"]) ?>
                </div>
            </div>
            <!-- Option end -->

            <!-- Option start -->
            <div class="wpd-opt-row" data-wpd-opt="emailContentSubscriptionConfirmation">
                <div class="wpd-opt-name">
                    <label for="emailContentSubscriptionConfirmation"><?php esc_html_e($setting["options"]["emailContentSubscriptionConfirmation"]["label"]) ?></label>
                    <p class="wpd-desc"><?php esc_html_e($setting["options"]["emailContentSubscriptionConfirmation"]["description"]) ?></p>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?></p>
                    <span class="wc_available_variable" data-wpd-clipboard="[SITE_URL]">[SITE_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[POST_URL]">[POST_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[BLOG_TITLE]">[BLOG_TITLE]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[POST_TITLE]">[POST_TITLE]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[CONFIRM_URL]">[CONFIRM_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[CANCEL_URL]">[CANCEL_URL]</span>
                </div>
                <div class="wpd-opt-input">
                    <?php
                    wp_editor($this->subscription["emailContentSubscriptionConfirmation"], "emailContentSubscriptionConfirmation", [
                        "textarea_name" => WpdiscuzCore::TAB_SUBSCRIPTION . "[emailContentSubscriptionConfirmation]",
                        "textarea_rows" => 10,
                        "teeny" => true,
                        "media_buttons" => true,
                        "default_editor" => "tmce"
                    ]);
                    ?>                    
                </div>
                <div class="wpd-opt-doc">
                    <?php $this->printDocLink($setting["options"]["emailContentSubscriptionConfirmation"]["docurl"]) ?>
                </div>
            </div>
            <!-- Option end -->

        </div>

    </div>

    <div class="wpd-accordion-item">

        <div class="wpd-subtitle fas wpd-accordion-title" style="margin-top: 20px;" data-wpd-selector="wpd-comment-status-templates">
            <p><?php esc_html_e("Comment status email templates", "wpdiscuz") ?></p>
        </div>

        <div class="wpd-accordion-content">

            <div class="wpd-subtitle wpd-subtitle" style="margin-top: 20px;">
                <?php esc_html_e("Approved", "wpdiscuz"); ?>
            </div>

            <!-- Option start -->
            <div class="wpd-opt-row" data-wpd-opt="emailSubjectCommentApproved">
                <div class="wpd-opt-name">
                    <label for="emailSubjectCommentApproved"><?php esc_html_e($setting["options"]["emailSubjectCommentApproved"]["label"]) ?></label>
                    <p class="wpd-desc"><?php esc_html_e($setting["options"]["emailSubjectCommentApproved"]["description"]) ?></p>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?></p>
                    <p class="wpd-desc">
                        <span class="wc_available_variable" data-wpd-clipboard="[BLOG_TITLE]">[BLOG_TITLE]</span>
                        <span class="wc_available_variable" data-wpd-clipboard="[POST_TITLE]">[POST_TITLE]</span>
                    </p>
                </div>
                <div class="wpd-opt-input">
                    <input type="text" value="<?php esc_attr_e($this->subscription["emailSubjectCommentApproved"]); ?>" name="<?php echo WpdiscuzCore::TAB_SUBSCRIPTION; ?>[emailSubjectCommentApproved]" id="emailSubjectCommentApproved" />
                </div>
                <div class="wpd-opt-doc">
                    <?php $this->printDocLink($setting["options"]["emailSubjectCommentApproved"]["docurl"]) ?>
                </div>
            </div>
            <!-- Option end -->

            <!-- Option start -->
            <div class="wpd-opt-row" data-wpd-opt="emailContentCommentApproved">
                <div class="wpd-opt-name">
                    <label for="emailContentCommentApproved"><?php esc_html_e($setting["options"]["emailContentCommentApproved"]["label"]) ?></label>
                    <p class="wpd-desc"><?php esc_html_e($setting["options"]["emailContentCommentApproved"]["description"]) ?></p>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?></p>
                    <span class="wc_available_variable" data-wpd-clipboard="[SITE_URL]">[SITE_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[POST_URL]">[POST_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[BLOG_TITLE]">[BLOG_TITLE]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[POST_TITLE]">[POST_TITLE]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_URL]">[COMMENT_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_AUTHOR]">[COMMENT_AUTHOR]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_CONTENT]">[COMMENT_CONTENT]</span>
                </div>
                <div class="wpd-opt-input">
                    <?php
                    wp_editor($this->subscription["emailContentCommentApproved"], "emailContentCommentApproved", [
                        "textarea_name" => WpdiscuzCore::TAB_SUBSCRIPTION . "[emailContentCommentApproved]",
                        "textarea_rows" => 10,
                        "teeny" => true,
                        "media_buttons" => true,
                        "default_editor" => "tmce"
                    ]);
                    ?>                    
                </div>
                <div class="wpd-opt-doc">
                    <?php $this->printDocLink($setting["options"]["emailContentCommentApproved"]["docurl"]) ?>
                </div>
            </div>
            <!-- Option end -->

        </div>
    </div>

    <div class="wpd-accordion-item">

        <div class="wpd-subtitle fas wpd-accordion-title" style="margin-top: 20px;" data-wpd-selector="wpd-user-mentioned-templates">
            <p><?php esc_html_e("Mentioning email templates", "wpdiscuz") ?></p>
        </div>

        <div class="wpd-accordion-content">

            <div class="wpd-subtitle wpd-subtitle" style="margin-top: 20px;">
                <?php esc_html_e("A user have been mentioned", "wpdiscuz"); ?>
            </div>

            <!-- Option start -->
            <div class="wpd-opt-row" data-wpd-opt="emailSubjectUserMentioned">
                <div class="wpd-opt-name">
                    <label for="emailSubjectUserMentioned"><?php esc_html_e($setting["options"]["emailSubjectUserMentioned"]["label"]) ?></label>
                    <p class="wpd-desc"><?php esc_html_e($setting["options"]["emailSubjectUserMentioned"]["description"]) ?></p>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?></p>
                    <p class="wpd-desc">
                        <span class="wc_available_variable" data-wpd-clipboard="[BLOG_TITLE]">[BLOG_TITLE]</span>
                        <span class="wc_available_variable" data-wpd-clipboard="[POST_TITLE]">[POST_TITLE]</span>
                    </p>
                </div>
                <div class="wpd-opt-input">
                    <input type="text" value="<?php esc_attr_e($this->subscription["emailSubjectUserMentioned"]); ?>" name="<?php echo WpdiscuzCore::TAB_SUBSCRIPTION; ?>[emailSubjectUserMentioned]" id="emailSubjectUserMentioned" />
                </div>
                <div class="wpd-opt-doc">
                    <?php $this->printDocLink($setting["options"]["emailSubjectUserMentioned"]["docurl"]) ?>
                </div>
            </div>
            <!-- Option end -->

            <!-- Option start -->
            <div class="wpd-opt-row" data-wpd-opt="emailContentUserMentioned">
                <div class="wpd-opt-name">
                    <label for="emailContentUserMentioned"><?php esc_html_e($setting["options"]["emailContentUserMentioned"]["label"]) ?></label>
                    <p class="wpd-desc"><?php esc_html_e($setting["options"]["emailContentUserMentioned"]["description"]) ?></p>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?></p>
                    <span class="wc_available_variable" data-wpd-clipboard="[SITE_URL]">[SITE_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[POST_URL]">[POST_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[BLOG_TITLE]">[BLOG_TITLE]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[POST_TITLE]">[POST_TITLE]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_URL]">[COMMENT_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_AUTHOR]">[COMMENT_AUTHOR]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[MENTIONED_USER_NAME]">[MENTIONED_USER_NAME]</span>
                </div>
                <div class="wpd-opt-input">
                    <?php
                    wp_editor($this->subscription["emailContentUserMentioned"], "emailContentUserMentioned", [
                        "textarea_name" => WpdiscuzCore::TAB_SUBSCRIPTION . "[emailContentUserMentioned]",
                        "textarea_rows" => 10,
                        "teeny" => true,
                        "media_buttons" => true,
                        "default_editor" => "tmce"
                    ]);
                    ?>                    
                </div>
                <div class="wpd-opt-doc">
                    <?php $this->printDocLink($setting["options"]["emailContentUserMentioned"]["docurl"]) ?>
                </div>
            </div>
            <!-- Option end -->

        </div>
    </div>

    <div class="wpd-accordion-item">

        <div class="wpd-subtitle fas wpd-accordion-title" style="margin-top: 20px;" data-wpd-selector="wpd-follow-templates">
            <p><?php esc_html_e("Follow email templates", "wpdiscuz") ?></p>
        </div>

        <div class="wpd-accordion-content">

            <div class="wpd-subtitle wpd-subtitle" style="margin-top: 20px;">
                <?php esc_html_e("Follow confirmation", "wpdiscuz"); ?>
            </div>

            <!-- Option start -->
            <div class="wpd-opt-row" data-wpd-opt="emailSubjectFollowConfirmation">
                <div class="wpd-opt-name">
                    <label for="emailSubjectFollowConfirmation"><?php esc_html_e($setting["options"]["emailSubjectFollowConfirmation"]["label"]) ?></label>
                    <p class="wpd-desc"><?php esc_html_e($setting["options"]["emailSubjectFollowConfirmation"]["description"]) ?></p>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?></p>
                    <p class="wpd-desc">
                        <span class="wc_available_variable" data-wpd-clipboard="[BLOG_TITLE]">[BLOG_TITLE]</span>
                        <span class="wc_available_variable" data-wpd-clipboard="[POST_TITLE]">[POST_TITLE]</span>
                    </p>
                </div>
                <div class="wpd-opt-input">
                    <input type="text" value="<?php esc_attr_e($this->subscription["emailSubjectFollowConfirmation"]); ?>" name="<?php echo WpdiscuzCore::TAB_SUBSCRIPTION; ?>[emailSubjectFollowConfirmation]" id="emailSubjectFollowConfirmation" />
                </div>
                <div class="wpd-opt-doc">
                    <?php $this->printDocLink($setting["options"]["emailSubjectFollowConfirmation"]["docurl"]) ?>
                </div>
            </div>
            <!-- Option end -->            

            <!-- Option start -->
            <div class="wpd-opt-row" data-wpd-opt="emailContentFollowConfirmation">
                <div class="wpd-opt-name">
                    <label for="emailContentFollowConfirmation"><?php esc_html_e($setting["options"]["emailContentFollowConfirmation"]["label"]) ?></label>
                    <p class="wpd-desc"><?php esc_html_e($setting["options"]["emailContentFollowConfirmation"]["description"]) ?></p>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?></p>
                    <span class="wc_available_variable" data-wpd-clipboard="[SITE_URL]">[SITE_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[POST_URL]">[POST_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[BLOG_TITLE]">[BLOG_TITLE]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[POST_TITLE]">[POST_TITLE]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[CONFIRM_URL]">[CONFIRM_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[CANCEL_URL]">[CANCEL_URL]</span>
                </div>
                <div class="wpd-opt-input">
                    <?php
                    wp_editor($this->subscription["emailContentFollowConfirmation"], "emailContentFollowConfirmation", [
                        "textarea_name" => WpdiscuzCore::TAB_SUBSCRIPTION . "[emailContentFollowConfirmation]",
                        "textarea_rows" => 10,
                        "teeny" => true,
                        "media_buttons" => true,
                        "default_editor" => "tmce"
                    ]);
                    ?>                    
                </div>
                <div class="wpd-opt-doc">
                    <?php $this->printDocLink($setting["options"]["emailContentFollowConfirmation"]["docurl"]) ?>
                </div>
            </div>
            <!-- Option end -->

            <div class="wpd-subtitle wpd-subtitle" style="margin-top: 20px;">
                <?php esc_html_e("Following comment", "wpdiscuz"); ?>
            </div>

            <!-- Option start -->
            <div class="wpd-opt-row" data-wpd-opt="emailSubjectFollowComment">
                <div class="wpd-opt-name">
                    <label for="emailSubjectFollowComment"><?php esc_html_e($setting["options"]["emailSubjectFollowComment"]["label"]) ?></label>
                    <p class="wpd-desc"><?php esc_html_e($setting["options"]["emailSubjectFollowComment"]["description"]) ?></p>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?></p>
                    <p class="wpd-desc">
                        <span class="wc_available_variable" data-wpd-clipboard="[BLOG_TITLE]">[BLOG_TITLE]</span>
                        <span class="wc_available_variable" data-wpd-clipboard="[POST_TITLE]">[POST_TITLE]</span>
                        <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_AUTHOR]">[COMMENT_AUTHOR]</span>
                    </p>
                </div>
                <div class="wpd-opt-input">
                    <input type="text" value="<?php esc_attr_e($this->subscription["emailSubjectFollowComment"]); ?>" name="<?php echo WpdiscuzCore::TAB_SUBSCRIPTION; ?>[emailSubjectFollowComment]" id="emailSubjectFollowComment" />
                </div>
                <div class="wpd-opt-doc">
                    <?php $this->printDocLink($setting["options"]["emailSubjectFollowComment"]["docurl"]) ?>
                </div>
            </div>
            <!-- Option end -->
            
            <!-- Option start -->
            <div class="wpd-opt-row" data-wpd-opt="emailContentFollowComment">
                <div class="wpd-opt-name">
                    <label for="emailContentFollowComment"><?php esc_html_e($setting["options"]["emailContentFollowComment"]["label"]) ?></label>
                    <p class="wpd-desc"><?php esc_html_e($setting["options"]["emailContentFollowComment"]["description"]) ?></p>
                    <p class="wpd-desc"><?php esc_html_e("Available shortcodes", "wpdiscuz"); ?></p>
                    <span class="wc_available_variable" data-wpd-clipboard="[SITE_URL]">[SITE_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[POST_URL]">[POST_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[BLOG_TITLE]">[BLOG_TITLE]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[POST_TITLE]">[POST_TITLE]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[FOLLOWER_NAME]">[FOLLOWER_NAME]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_URL]">[COMMENT_URL]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_AUTHOR]">[COMMENT_AUTHOR]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[COMMENT_CONTENT]">[COMMENT_CONTENT]</span>
                    <span class="wc_available_variable" data-wpd-clipboard="[CANCEL_URL]">[CANCEL_URL]</span>
                </div>
                <div class="wpd-opt-input">
                    <?php
                    wp_editor($this->subscription["emailContentFollowComment"], "emailContentFollowComment", [
                        "textarea_name" => WpdiscuzCore::TAB_SUBSCRIPTION . "[emailContentFollowComment]",
                        "textarea_rows" => 10,
                        "teeny" => true,
                        "media_buttons" => true,
                        "default_editor" => "tmce"
                    ]);
                    ?>                    
                </div>
                <div class="wpd-opt-doc">
                    <?php $this->printDocLink($setting["options"]["emailContentFollowComment"]["docurl"]) ?>
                </div>
            </div>
            <!-- Option end -->

        </div>

    </div>

</div>


