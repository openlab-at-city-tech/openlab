<?php
wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
require_once B2S_PLUGIN_DIR . 'includes/B2S/AutoPost/Item.php';
require_once B2S_PLUGIN_DIR . 'includes/Options.php';
$autoPostItem = new B2S_AutoPost_Item();
?>

<div class="b2s-container">
    <div class=" b2s-inbox col-md-12 del-padding-left">
        <div class="col-md-9 del-padding-left del-padding-right">
            <!--Header|Start - Include-->
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.php'); ?>
            <!--Header|End-->
            <div class="clearfix"></div>
            <!--Content|Start-->
            <div class="panel panel-group b2s-upload-image-no-permission" style="display:none;">
                <div class="panel-body">
                    <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('You need a higher user role to upload an image on this blog. Please contact your administrator.', 'blog2social'); ?>
                </div>
            </div>  
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-md-12">                       
                        <div class="b2s-post"></div>
                        <div class="row b2s-loading-area width-100" style="display: none;">
                            <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                            <div class="text-center b2s-loader-text"><?php esc_html_e("save...", "blog2social"); ?></div>
                        </div>
                        <div class="row b2s-autopost-area">
                        <?php echo $autoPostItem->getAutoPostingSettingsHtml(); ?>
                        </div>
                        <input type="hidden" id="b2s_user_version" value="<?php echo B2S_PLUGIN_USER_VERSION; ?>" />
                        <?php
                        $noLegend = 1;
                        require_once (B2S_PLUGIN_DIR . 'views/b2s/html/footer.php');
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.php'); ?>
    </div>
</div>

<input type="hidden" id="b2sLang" value="<?php echo substr(B2S_LANGUAGE, 0, 2); ?>">
<input type="hidden" id="b2sUserLang" value="<?php echo strtolower(substr(get_locale(), 0, 2)); ?>">
<input type="hidden" id="b2sShowSection" value="<?php echo (isset($_GET['show']) ? esc_attr($_GET['show']) : ''); ?>">
<input type="hidden" id="b2s_wp_media_headline" value="<?php esc_html_e('Select or upload an image from media gallery', 'blog2social') ?>">
<input type="hidden" id="b2s_wp_media_btn" value="<?php esc_html_e('Use image', 'blog2social') ?>">
<input type="hidden" id="b2s_user_version" value="<?php echo B2S_PLUGIN_USER_VERSION ?>">
<input type="hidden" id="b2sServerUrl" value="<?php echo B2S_PLUGIN_SERVER_URL; ?>">


<div class="modal fade" id="b2sInfoTimeZoneModal" tabindex="-1" role="dialog" aria-labelledby="b2sInfoTimeZoneModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoTimeZoneModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Personal Time Zone', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php esc_html_e('Blog2Social applies the scheduled time settings based on the time zone defined in the general settings of your WordPress. You can select a user-specific time zone that deviates from the Wordpress system time zone for your social media scheduling.<br><br>Select the desired time zone from the drop-down menu.', 'blog2social') ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sTwitterInfoModal" tabindex="-1" role="dialog" aria-labelledby="b2sTwitterInfoModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sTwitterInfoModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Select Twitter profile:', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php esc_html_e('To comply with the Twitter TOS and to avoid duplicate posts, autoposts will be sent to your primary Twitter profile.', 'blog2social') ?> <a target="_blank" href="<?php echo B2S_Tools::getSupportLink('network_tos_faq_032018') ?>"><?php esc_html_e('More information', 'blog2social') ?></a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sAutoPostBestTimesInfoModal" tabindex="-1" role="dialog" aria-labelledby="b2sAutoPostBestTimesInfoModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sAutoPostBestTimesInfoModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Apply best times', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php echo sprintf(__('The time of publishing a post can play a decisive role in achieving more likes, shares and comments as well as a wide reach. Each social media network has it\'s "best times". Blog2Social provides you with predefined best times. When you activate the "best times" for your Auto-Poster, your WordPress posts and pages will be shared automatically at the "best times". Get more information about the "best times" in the guide "<a href="%s" target="_blank">The Best Times to Post on Social Media</a>".', 'blog2social'), B2S_Tools::getSupportLink('besttimes_blogpost')); ?>
                <br>
                <br>
                <?php echo sprintf(__('Please note: You can also set up your own "best times". You will learn how to set up your own "best times" in this <a href="%s" target="_blank">guide</a>.', 'blog2social'), B2S_Tools::getSupportLink('besttimes_faq')); ?>
            </div>
        </div>
    </div>
</div>
