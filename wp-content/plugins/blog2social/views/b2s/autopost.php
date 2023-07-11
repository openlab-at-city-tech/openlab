<?php
wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
require_once B2S_PLUGIN_DIR . 'includes/B2S/AutoPost/Item.php';
require_once B2S_PLUGIN_DIR . 'includes/Options.php';
$autoPostItem = new B2S_AutoPost_Item();
?>

<div class="b2s-container">
    <div class=" b2s-inbox col-md-12 del-padding-left">
        <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.php'); ?>
        <div class="col-md-9 del-padding-left del-padding-right">
            <!--Header|Start - Include-->
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.php'); ?>
            <!--Header|End-->
            <div class="clearfix"></div>
            <!--Navbar|Start-->
            <div class="panel panel-default">
                <div class="panel-body">
                    <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/post.navbar.php'); ?>
                </div>
            </div>
            <!--Navbar|End-->
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
                            <?php
                            echo wp_kses($autoPostItem->getAutoPostingSettingsHtml(), array(
                                'span' => array(
                                    'class' => array(),
                                    'data-network-count-trigger' => array(),
                                    'data-network-id' => array()
                                ),
                                'input' => array(
                                    'id' => array(),
                                    'class' => array(),
                                    'type' => array(),
                                    'value' => array(),
                                    'name' => array(),
                                    'data-size' => array(),
                                    'data-toggle' => array(),
                                    'data-width' => array(),
                                    'data-height' => array(),
                                    'data-onstyle' => array(),
                                    'data-on' => array(),
                                    'data-off' => array(),
                                    'data-area-type' => array(),
                                    'maxlength' => array(),
                                    'max' => array(),
                                    'min' => array(),
                                    'placeholder' => array(),
                                    'data-network-id' => array(),
                                    'checked' => array()
                                ),
                                'div' => array(
                                    'class' => array(),
                                    'data-error-reason' => array(),
                                    'style' => array(),
                                    'data-area-type' => array()
                                ),
                                'h4' => array(
                                    'class' => array()
                                ),
                                'a' => array(
                                    'class' => array(),
                                    'target' => array(),
                                    'href' => array(),
                                    'id' => array()
                                ),
                                'p' => array(
                                    'class' => array()
                                ),
                                'label' => array(
                                    'class' => array(),
                                    'for' => array()
                                ),
                                'small' => array(),
                                'b' => array(),
                                'form' => array(
                                    'id' => array(),
                                    'method' => array()
                                ),
                                'button' => array(
                                    'class' => array(),
                                    'data-post-type' => array(),
                                    'data-select-toogle-state' => array(),
                                    'data-select-toogle-name' => array(),
                                    'id' => array(),
                                    'type' => array()
                                ),
                                'br' => array(),
                                'hr' => array(),
                                'select' => array(
                                    'class' => array(),
                                    'name' => array(),
                                    'id' => array(),
                                    'multiple' => array(),
                                    'data-placeholder' => array()
                                ),
                                'option' => array(
                                    'value' => array(),
                                    'selected' => array(),
                                    'data-mandant-id' => array()
                                ),
                                'li' => array(
                                    'class' => array(),
                                    'data-network-auth-id' => array(),
                                    'data-network-id' => array(),
                                    'data-network-type' => array()
                                ),
                                'ul' => array(
                                    'class' => array(),
                                    'data-network-id' => array(),
                                    'data-network-count' => array()
                                ),
                                'img' => array(
                                    'class' => array(),
                                    'alt' => array(),
                                    'src' => array(),
                                )
                            ));
                            ?>
                        </div>
                        <input type="hidden" id="b2s_user_version" value="<?php echo esc_attr(B2S_PLUGIN_USER_VERSION); ?>" />
                        <?php
                        $noLegend = 1;
                        require_once (B2S_PLUGIN_DIR . 'views/b2s/html/footer.php');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="b2sLang" value="<?php echo esc_attr(substr(B2S_LANGUAGE, 0, 2)); ?>">
<input type="hidden" id="b2sUserLang" value="<?php echo esc_attr(strtolower(substr(get_locale(), 0, 2))); ?>">
<input type="hidden" id="b2sShowSection" value="<?php echo (isset($_GET['show']) ? esc_attr(sanitize_text_field($_GET['show'])) : ''); ?>">
<input type="hidden" id="b2s_wp_media_headline" value="<?php esc_html_e('Select or upload an image from media gallery', 'blog2social') ?>">
<input type="hidden" id="b2s_wp_media_btn" value="<?php esc_html_e('Use image', 'blog2social') ?>">
<input type="hidden" id="b2s_user_version" value="<?php echo esc_attr(B2S_PLUGIN_USER_VERSION) ?>">
<input type="hidden" id="b2sServerUrl" value="<?php echo esc_attr(B2S_PLUGIN_SERVER_URL); ?>">


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
                <?php esc_html_e('To comply with the Twitter TOS and to avoid duplicate posts, autoposts will be sent to your primary Twitter profile.', 'blog2social') ?> <a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('network_tos_faq_032018')) ?>"><?php esc_html_e('More information', 'blog2social') ?></a>
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
                <?php echo sprintf(__('The time of publishing a post can play a decisive role in achieving more likes, shares and comments as well as a wide reach. Each social media network has it\'s "best times". Blog2Social provides you with predefined best times. When you activate the "best times" for your Auto-Poster, your WordPress posts and pages will be shared automatically at the "best times". Get more information about the "best times" in the guide "<a href="%s" target="_blank">The Best Times to Post on Social Media</a>".', 'blog2social'), esc_url(B2S_Tools::getSupportLink('besttimes_blogpost'))); ?>
                <br>
                <br>
                <?php echo sprintf(__('Please note: You can also set up your own "best times". You will learn how to set up your own "best times" in this <a href="%s" target="_blank">guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('besttimes_faq'))); ?>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="b2sAutoPostAInfoModal" tabindex="-1" role="dialog" aria-labelledby="b2sAutoPostAInfoModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sAutoPostAInfoModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Important information about the Auto-Poster settings for WordPress content', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php esc_html_e('If you like to share your WordPress content (blogposts, pages, and products) automatically, you can use the following checklists where you get all information on the different setting panels for the Auto-Poster for WordPress content:', 'blog2social'); ?>
                <br>
                <br>
                <?php echo sprintf(__('<a href="%s" target="_blank">How to set up the Auto-Poster for your own WordPress content</a>', 'blog2social'), esc_url(B2S_Tools::getSupportLink('autopost_checklist_wp'))); ?>
                <br>
                <?php echo sprintf(__('<a href="%s" target="_blank">Sharing with the Auto-Poster- Things to check for Troubleshooting</a>', 'blog2social'), esc_url(B2S_Tools::getSupportLink('auto_post_troubleshoot'))); ?>
                <br>
                <br>
                <?php esc_html_e('All settings and social networks for the Auto-Poster can be defined for each WordPress user individually.', 'blog2social'); ?>
                <br>
                <?php esc_html_e('Please make sure that each WordPress user or author whose posts should be auto-posted', 'blog2social'); ?>
                <br>
                <?php echo sprintf(__('1. is activated with a valid Blog2Social Premium license (<a href="%s" target="_blank">How do I activate my license key?</a>)', 'blog2social'), esc_url(B2S_Tools::getSupportLink('license_key'))); ?>
                <br>
                <?php esc_html_e('2. has the selected social media networks connected or assigned (Blog2Social -> Networks)', 'blog2social'); ?>
                <br>
                <?php esc_html_e('3. is activated with the correct Auto-Poster settings (Autoposter FAQ)', 'blog2social'); ?>
                <br>
                <br>
                <?php esc_html_e('Please make sure you activate and define the preferred settings panel for each user.', 'blog2social'); ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="b2sAutoPostMInfoModal" tabindex="-1" role="dialog" aria-labelledby="b2sAutoPostMInfoModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sAutoPostMInfoModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Important information about the Auto-Poster settings for imported posts', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php esc_html_e('If you like to share imported (imported RSS feeds or posts created/ imported with another plugin) posts automatically, you can use the following checklists where you get all information on the different setting panels for the Auto-Poster for imported posts:', 'blog2social'); ?>
                <br>
                <br>
                <?php echo sprintf(__('<a href="%s" target="_blank">How to set up the Auto-Poster for imported content</a>', 'blog2social'), esc_url(B2S_Tools::getSupportLink('autopost_checklist_rss'))); ?>
                <br>
                <?php echo sprintf(__('<a href="%s" target="_blank">Sharing imported posts with the Auto-Poster- Things to check for Troubleshooting</a>', 'blog2social'), esc_url(B2S_Tools::getSupportLink('auto_post_troubleshoot'))); ?>
                <br>
                <br>
                <?php esc_html_e('All settings and social networks for the Auto-Poster can be defined for each WordPress user individually.', 'blog2social'); ?>
                <br>
                <?php esc_html_e('Please make sure that each WordPress user or author whose posts should be auto-posted', 'blog2social'); ?>
                <br>
                <?php echo sprintf(__('1. is activated with a valid Blog2Social Premium license (<a href="%s" target="_blank">How do I activate my license key?</a>)', 'blog2social'), esc_url(B2S_Tools::getSupportLink('license_key'))); ?>
                <br>
                <?php esc_html_e('2. has the selected social media networks connected or assigned (Blog2Social -> Networks)', 'blog2social'); ?>
                <br>
                <?php esc_html_e('3. is activated with the correct Auto-Poster settings (Autoposter FAQ)', 'blog2social'); ?>
                <br>
                <br>
                <?php esc_html_e('Please make sure you activate and define the preferred settings panel for each user.', 'blog2social'); ?>
            </div>
        </div>
    </div>
</div>