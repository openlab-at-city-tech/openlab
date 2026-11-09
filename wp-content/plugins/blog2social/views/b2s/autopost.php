<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 */

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
                                    'id' => array(),
                                    'data-error-reason' => array(),
                                    'data-target' => array(),
                                    'aria-expanded' => array(),
                                    'style' => array(),
                                    'data-area-type' => array(),
                                    'tabindex' => array()
                                ),
                                'h4' => array(
                                    'class' => array()
                                ),
                                'a' => array(
                                    'class' => array(),
                                    'target' => array(),
                                    'href' => array(),
                                    'id' => array(),
                                    'data-toggle' => array(),
                                    'data-target' => array(),
                                    'aria-expanded' => array(),
                                    'style' => array()
                                ),
                                'i' => array(
                                    'class' => array()
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
                                    'data-placeholder' => array(),
                                    'data-target' => array(),
                                    'data-network-id' => array(),
                                    'data-network-count' => array()
                                ),
                                'option' => array(
                                    'value' => array(),
                                    'selected' => array(),
                                    'disabled' => array(),
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
<input type="hidden" id="b2sShowSection" value="<?php echo (isset($_GET['show']) ? esc_attr(sanitize_text_field(wp_unslash($_GET['show']))) : ''); ?>">
<input type="hidden" id="b2s_wp_media_headline" value="<?php esc_attr_e('Select or upload an image from media gallery', 'blog2social') ?>">
<input type="hidden" id="b2s_wp_media_btn" value="<?php esc_attr_e('Use image', 'blog2social') ?>">
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
                <h4 class="modal-title"><?php esc_html_e('Select X profile:', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php esc_html_e('To comply with the X TOS and to avoid duplicate posts, autoposts will be sent to your primary X profile.', 'blog2social') ?> <a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('network_tos_faq_032018')) ?>"><?php esc_html_e('More information', 'blog2social') ?></a>
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
                <?php echo sprintf(wp_kses(
                    // translators: %s is a link
                    __('The time of publishing a post can play a decisive role in achieving more likes, shares and comments as well as a wide reach. Each social media network has it\'s "best times". Blog2Social provides you with predefined best times. When you activate the "best times" for your Auto Poster, your WordPress posts and pages will be shared automatically at the "best times". Get more information about the "best times" in the guide "<a href="%s" target="_blank">The Best Times to Post on Social Media</a>".', 'blog2social'), 
                    array(
                        'a' => array(
                            'href' => array(), 
                            'target' => array())
                        )), 
                        esc_url(B2S_Tools::getSupportLink('besttimes_blogpost'))); ?>
                <br>
                <br>
                <?php echo sprintf(wp_kses(
                    // translators: %s is a link
                    __('Please note: You can also set up your own "best times". You will learn how to set up your own "best times" in this <a href="%s" target="_blank">guide</a>.', 'blog2social'),
                    array('a' => array(
                            'href' => array(), 
                            'target' => array())
                        )),
                        esc_url(B2S_Tools::getSupportLink('besttimes_faq'))); ?>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="b2sAutoPostAInfoModal" tabindex="-1" role="dialog" aria-labelledby="b2sAutoPostAInfoModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sAutoPostAInfoModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Important information about the Auto Poster settings for WordPress content', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php esc_html_e('If you like to share your WordPress content (blogposts, pages, and products) automatically, you can use the following checklists where you get all information on the different setting panels for the Auto Poster for WordPress content:', 'blog2social'); ?>
                <br>
                <br>
                <?php echo sprintf(wp_kses(
                    // translators: %s is a link
                    __('<a href="%s" target="_blank">How to set up the Auto Poster for your own WordPress content</a>', 'blog2social'),
                    array('a' => array(
                        'href' => array(), 
                        'target' => array())
                    )), 
                    esc_url(B2S_Tools::getSupportLink('autopost_checklist_wp'))); ?>
                <br>
                <?php echo sprintf(wp_kses(
                    // translators: %s is a link
                    __('<a href="%s" target="_blank">Sharing with the Auto Poster- Things to check for Troubleshooting</a>', 'blog2social'), 
                    array('a' => array(
                        'href' => array(), 
                        'target' => array())
                    )),
                    esc_url(B2S_Tools::getSupportLink('auto_post_troubleshoot'))); ?>
                <br>
                <br>
                <?php esc_html_e('All settings and social networks for the Auto Poster can be defined for each WordPress user individually.', 'blog2social'); ?>
                <br>
                <?php esc_html_e('Please make sure that each WordPress user or author whose posts should be auto-posted', 'blog2social'); ?>
                <br>
                <?php echo sprintf(wp_kses(
                    // translators: %s is a link
                    __('1. is activated with a valid Blog2Social Premium license (<a href="%s" target="_blank">How do I activate my license key?</a>)', 'blog2social'), 
                    array('a' => array(
                        'href' => array(), 
                        'target' => array())
                    )),
                    esc_url(B2S_Tools::getSupportLink('license_key'))); ?>
                <br>
                <?php esc_html_e('2. has the selected social media networks connected or assigned (Blog2Social -> Networks)', 'blog2social'); ?>
                <br>
                <?php esc_html_e('3. is activated with the correct Auto Poster settings (Auto Poster FAQ)', 'blog2social'); ?>
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
                <h4 class="modal-title"><?php esc_html_e('Important information about the Auto Poster settings for imported posts', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <div class="b2s-autopost-m-info-modal-wrap">
                    <div class="b2s-autopost-m-info-modal-infobox">
                        <span class="b2s-autopost-m-info-modal-infobox-icon">i</span>
                        <p class="b2s-autopost-m-info-modal-infobox-text"><?php esc_html_e('Would you like to share imported posts (e.g., from RSS feeds or created/imported with another plugin) automatically? Use the Auto Poster and check these key points.', 'blog2social'); ?></p>
                    </div>
                    <div class="row b2s-autopost-m-info-modal-steps">
                        <div class="col-md-4">
                            <div class="b2s-autopost-m-info-modal-step-card">
                                <span class="b2s-autopost-m-info-modal-step-badge b2s-autopost-m-info-modal-step-badge-1">1</span>
                                <span class="glyphicon glyphicon-ok-circle b2s-autopost-m-info-modal-step-icon"></span>
                                <h4 class="b2s-autopost-m-info-modal-step-heading"><?php esc_html_e('Activate license', 'blog2social'); ?></h4>
                                <p><?php echo wp_kses(__('Make sure the WordPress user has a <strong>valid</strong> Blog2Social Premium license activated.', 'blog2social'), array('strong' => array())); ?></p>
                                <a href="<?php echo esc_url(B2S_Tools::getSupportLink('license_key')); ?>" target="_blank" class="b2s-autopost-m-info-modal-step-link-1">
                                    <span class="glyphicon glyphicon-ok-circle"></span> <?php esc_html_e('Activate license', 'blog2social'); ?>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="b2s-autopost-m-info-modal-step-card-networks">
                                <span class="b2s-autopost-m-info-modal-step-badge b2s-autopost-m-info-modal-step-badge-2">2</span>
                                <span class="glyphicon glyphicon-link b2s-autopost-m-info-modal-step-icon"></span>
                                <h4 class="b2s-autopost-m-info-modal-step-heading"><?php esc_html_e('Connect networks', 'blog2social'); ?></h4>
                                <p><?php esc_html_e('Make sure the desired social media networks are connected or assigned to the user.', 'blog2social'); ?></p>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=blog2social-network')); ?>" class="b2s-autopost-m-info-modal-step-link-2">
                                    <span class="glyphicon glyphicon-link"></span> <?php esc_html_e('Blog2Social → Networks', 'blog2social'); ?>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="b2s-autopost-m-info-modal-step-card">
                                <span class="b2s-autopost-m-info-modal-step-badge b2s-autopost-m-info-modal-step-badge-3">3</span>
                                <span class="glyphicon glyphicon-cog b2s-autopost-m-info-modal-step-icon"></span>
                                <h4 class="b2s-autopost-m-info-modal-step-heading"><?php esc_html_e('Configure Auto Poster', 'blog2social'); ?></h4>
                                <p><?php esc_html_e('Make sure the Auto Poster settings are correctly activated and configured for the user.', 'blog2social'); ?></p>
                                <a href="<?php echo esc_url(B2S_Tools::getSupportLink('auto_post_import')); ?>" target="_blank" class="b2s-autopost-m-info-modal-step-link-3">
                                    <span class="glyphicon glyphicon-cog"></span> <?php esc_html_e('Auto Poster FAQ', 'blog2social'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="b2s-autopost-m-info-modal-note">
                        <span class="glyphicon glyphicon-user b2s-autopost-m-info-modal-note-icon"></span>
                        <p class="b2s-autopost-m-info-modal-note-text"><?php echo wp_kses(__('<strong>Important:</strong> Each WordPress user/author whose posts should be auto-posted must fulfill all three points above.', 'blog2social'), array('strong' => array())); ?></p>
                    </div>
                </div>
                <div class="further-information">
                    <h3 class="b2s-ap-m-modal-further-info-heading"><?php esc_html_e('Further Information:', 'blog2social'); ?></h3>
                    <div class="info-links">
                        <a href="<?php echo esc_url(B2S_Tools::getSupportLink('auto_post_troubleshoot')); ?>" target="_blank">
                            <span class="glyphicon glyphicon-book"></span> 
                            <?php esc_html_e('Sharing with the Auto Poster- Things to check for Troubleshooting', 'blog2social'); ?>
                        </a>
                        <br>
                        <a href="<?php echo esc_url(B2S_Tools::getSupportLink('autopost_checklist_rss')); ?>" target="_blank">
                            <span class="glyphicon glyphicon-book"></span>
                            <?php esc_html_e('How to auto-post imported RSS feeds and blog posts on Social Media', 'blog2social'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="b2sAutoPostNoEffectWarningModal" tabindex="-1" role="dialog" aria-labelledby="b2sAutoPostNoEffectWarningModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sAutoPostNoEffectWarningModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Auto Poster settings have no effect', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php esc_html_e('Your current settings for new and updated posts for your own WordPress content will have no effect. Please select "All types" or add at least one post type under "Include selected only" for new posts or updated posts to actually activate the Auto Poster for Wordpress Content.', 'blog2social') ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="b2sAutoPostUnsavedModal" tabindex="-1" role="dialog" aria-labelledby="b2sAutoPostUnsavedModal" aria-hidden="true" data-backdrop="false" style="display:none; z-index: 2000;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php esc_html_e('Unsaved Changes', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php esc_html_e('You have unsaved changes. Would you like to save them?', 'blog2social') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default b2s-auto-post-unsaved-skip-btn"><?php esc_html_e('Skip', 'blog2social') ?></button>
                <button type="button" class="btn btn-primary b2s-auto-post-unsaved-save-btn"><?php esc_html_e('Save', 'blog2social') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sAutoPostEchoInfoModal" tabindex="-1" role="dialog" aria-labelledby="b2sAutoPostEchoInfoModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sAutoPostEchoInfoModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Repost', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php esc_html_e('Apply the Re-Poster settings to repost the content within 48 hours. Select the time window for repeated posting, either once on Day 1 or Day 2 after publishing, or once on each of the two days.', 'blog2social'); ?>
               </div>
        </div>
    </div>
</div>