<?php
if (!defined("ABSPATH")) {
    exit();
}
?>

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="isEnableOnHome">
    <div class="wpd-opt-name">
        <label for="isEnableOnHome"><?php echo esc_html($setting["options"]["isEnableOnHome"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["isEnableOnHome"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->general["isEnableOnHome"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[isEnableOnHome]" id="isEnableOnHome">
            <label for="isEnableOnHome"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["isEnableOnHome"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="isNativeAjaxEnabled">
    <div class="wpd-opt-name">
        <label for="isNativeAjaxEnabled"><?php echo esc_html($setting["options"]["isNativeAjaxEnabled"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["isNativeAjaxEnabled"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->general["isNativeAjaxEnabled"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[isNativeAjaxEnabled]" id="isNativeAjaxEnabled">
            <label for="isNativeAjaxEnabled"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["isNativeAjaxEnabled"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->
<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="loadComboVersion">
    <div class="wpd-opt-name">
        <label for="loadComboVersion"><?php echo esc_html($setting["options"]["loadComboVersion"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["loadComboVersion"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->general["loadComboVersion"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[loadComboVersion]" id="loadComboVersion">
            <label for="loadComboVersion"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["loadComboVersion"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->
<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="loadMinVersion">
    <div class="wpd-opt-name">
        <label for="loadMinVersion"><?php echo esc_html($setting["options"]["loadMinVersion"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["loadMinVersion"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->general["loadMinVersion"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[loadMinVersion]" id="loadMinVersion">
            <label for="loadMinVersion"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["loadMinVersion"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->
<?php if (is_ssl()) { ?>
    <!-- Option start -->
    <div class="wpd-opt-row" data-wpd-opt="commentLinkFilter">
        <div class="wpd-opt-name">
            <label><?php echo esc_html($setting["options"]["commentLinkFilter"]["label"]) ?></label>
            <p class="wpd-desc"><?php echo esc_html($setting["options"]["commentLinkFilter"]["description"]) ?></p>
        </div>
        <div class="wpd-opt-input">
            <div class="wpd-radio">
                <input type="radio" value="1" <?php checked(1 == $this->general["commentLinkFilter"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[commentLinkFilter]" id="http-to-link"/>
                <label for="http-to-link" class="wpd-radio-circle"></label>
                <label for="http-to-link"><?php esc_html_e("Replace non-https content to simple link URLs", "wpdiscuz") ?></label>
            </div>
            <div class="wpd-radio">
                <input type="radio" value="2" <?php checked(2 == $this->general["commentLinkFilter"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[commentLinkFilter]" id="http-to-https"/>
                <label for="http-to-https" class="wpd-radio-circle"></label>
                <label for="http-to-https"><?php esc_html_e("Just replace http protocols to https (https may not be supported by content provider)", "wpdiscuz") ?></label>
            </div>
            <div class="wpd-radio">
                <input type="radio" value="3" <?php checked(3 == $this->general["commentLinkFilter"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[commentLinkFilter]" id="ignore-https"/>
                <label for="ignore-https" class="wpd-radio-circle"></label>
                <label for="ignore-https"><?php esc_html_e("Ignore non-https content", "wpdiscuz") ?></label>
            </div>
        </div>
        <div class="wpd-opt-doc">
            <?php $this->printDocLink($setting["options"]["commentLinkFilter"]["docurl"]) ?>
        </div>
    </div>
    <!-- Option end -->
<?php } ?>
<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="redirectPage">
    <div class="wpd-opt-name">
        <label for="redirectPage"><?php echo esc_html($setting["options"]["redirectPage"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["redirectPage"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <?php
        wp_dropdown_pages([
            "name" => WpdiscuzCore::TAB_GENERAL . "[redirectPage]",
            "selected" => $this->general["redirectPage"],
            "show_option_none" => esc_html__("Do not redirect", "wpdiscuz"),
            "option_none_value" => 0
        ]);
        ?>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["redirectPage"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="simpleCommentDate">
    <div class="wpd-opt-name">
        <label for="simpleCommentDate"><?php echo esc_html($setting["options"]["simpleCommentDate"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["simpleCommentDate"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher" style="margin-bottom: 5px;">
            <input type="checkbox" <?php checked($this->general["simpleCommentDate"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[simpleCommentDate]" id="simpleCommentDate">
            <label for="simpleCommentDate"></label>
        </div>
        <span style="font-size:13px; color:#999999; padding-left:0px; margin-left:0px; line-height:15px;">
            <?php echo esc_html(date(get_option("date_format"))); ?> / <?php echo esc_html(date(get_option("time_format"))); ?><br />
            <?php esc_html_e("Current Wordpress date/time format", "wpdiscuz"); ?>
        </span>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["simpleCommentDate"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="dateDiffFormat">
    <div class="wpd-opt-name">
        <label for="dateDiffFormat"><?php echo esc_html($setting["options"]["dateDiffFormat"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["dateDiffFormat"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <input type="text" value="<?php echo esc_attr($this->general["dateDiffFormat"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[dateDiffFormat]" id="dateDiffFormat" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["dateDiffFormat"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="isUsePoMo">
    <div class="wpd-opt-name">
        <label for="isUsePoMo"><?php echo esc_html($setting["options"]["isUsePoMo"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["isUsePoMo"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->general["isUsePoMo"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[isUsePoMo]" id="isUsePoMo">
            <label for="isUsePoMo"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["isUsePoMo"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="showPluginPoweredByLink" style="border-bottom: none;">
    <div class="wpd-opt-name">
        <label for="showPluginPoweredByLink" style="padding-right: 20px;"><?php echo esc_html($setting["options"]["showPluginPoweredByLink"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["showPluginPoweredByLink"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <label for="showPluginPoweredByLink">
            <input type="checkbox" <?php checked($this->general["showPluginPoweredByLink"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[showPluginPoweredByLink]" id="showPluginPoweredByLink" />
            <span id="wpdiscuz_thank_you" style="color:#006600; font-size:13px;"> &nbsp;<?php esc_attr_e("Thank you!", "wpdiscuz"); ?></span>
        </label>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["showPluginPoweredByLink"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<div class="wpd-subtitle">
    <span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e("Comment and User Cache", "wpdiscuz") ?>
</div>

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="isCacheEnabled">
    <div class="wpd-opt-name">
        <label for="isCacheEnabled"><?php echo esc_html($setting["options"]["isCacheEnabled"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["isCacheEnabled"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->general["isCacheEnabled"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[isCacheEnabled]" id="isCacheEnabled">
            <label for="isCacheEnabled"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
		<?php $this->printDocLink($setting["options"]["isCacheEnabled"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="cacheTimeout" style="border-bottom: none;">
    <div class="wpd-opt-name">
        <label for="cacheTimeout"><?php echo esc_html($setting["options"]["cacheTimeout"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["cacheTimeout"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
		<?php $cacheTimeout = isset($this->general["cacheTimeout"]) && ($days = absint($this->general["cacheTimeout"])) ? $days : 10; ?>
        <input type="number" id="cacheTimeout" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[cacheTimeout]" value="<?php echo esc_attr($cacheTimeout); ?>" style="width: 80px;"/>&nbsp; <?php esc_html_e("days", "wpdiscuz") ?>
    </div>
    <div class="wpd-opt-doc">
		<?php $this->printDocLink($setting["options"]["cacheTimeout"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<div class="wpd-subtitle">
    <span class="dashicons dashicons-admin-tools"></span> <?php esc_html_e("Maintenance", "wpdiscuz") ?>
</div>

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="removeVoteData">
    <div class="wpd-opt-name">
        <label for="removeVoteData"><?php echo esc_html($setting["options"]["removeVoteData"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["removeVoteData"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <?php $voteUrl = admin_url("admin-post.php?action=removeVoteData"); ?>
        <a id="wpdiscuz-remove-votes" href="<?php echo esc_url_raw(wp_nonce_url($voteUrl, "removeVoteData")); ?>" class="button button-secondary" style="text-decoration: none;"><?php esc_html_e("Remove vote data", "wpdiscuz"); ?></a>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["removeVoteData"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="purgeAllCaches">
    <div class="wpd-opt-name">
        <label for="purgeAllCaches"><?php echo esc_html($setting["options"]["purgeAllCaches"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo esc_html($setting["options"]["purgeAllCaches"]["description"]) ?></p>
    </div>
    <div class="wpd-opt-input">
        <?php $allCacheUrl = admin_url("admin-post.php?action=purgeAllCaches"); ?>
        <a id="wpdiscuz-purge-cache" href="<?php echo esc_url_raw(wp_nonce_url($allCacheUrl, "purgeAllCaches")); ?>" class="button button-secondary" style="text-decoration: none;"><?php esc_html_e("Purge comments and users caches", "wpdiscuz"); ?></a>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["purgeAllCaches"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->