<?php
wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
require_once B2S_PLUGIN_DIR . 'includes/B2S/Settings/Item.php';
require_once B2S_PLUGIN_DIR . 'includes/Options.php';
$settingsItem = new B2S_Settings_Item();
$options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
$optionUserTimeFormat = $options->_getOption('user_time_format');
if ($optionUserTimeFormat == false) {
    $optionUserTimeFormat = (substr(B2S_LANGUAGE, 0, 2) == 'de') ? 0 : 1;
}
?>

<div class="b2s-container">
    <div class=" b2s-inbox col-md-12 del-padding-left">
        <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.php'); ?>
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
                        <div class="row b2s-user-settings-area">
                            <ul class="nav nav-pills">
                                <li class="active">
                                    <a href="#b2s-general" class="b2s-general" data-toggle="tab"><?php esc_html_e('General', 'blog2social') ?></a>
                                </li>
                                <li>
                                    <a href="admin.php?page=blog2social-autopost" class="b2s-auto-post"><?php esc_html_e('Auto-Post', 'blog2social') ?></a>
                                </li>
                                <li>
                                    <a href="#b2s-social-meta-data" class="b2s-social-meta-data" data-toggle="tab"><?php esc_html_e('Social Meta Data', 'blog2social') ?></a>
                                </li>
                                <li>
                                    <a href="admin.php?page=blog2social-repost" class="b2s-re-post"><?php esc_html_e("Re-Share Posts", "blog2social") ?></a> 
                                </li>
                                <li>
                                    <a href="#b2s-network" class="b2s-network" data-toggle="tab"><?php esc_html_e("Social Media Networks", "blog2social") ?></a> 
                                </li>
                                <li>
                                    <a href="#b2s-times" class="b2s-times" data-toggle="tab"><?php esc_html_e("Social Media Time Settings", "blog2social") ?></a> 
                                </li>
                                <li>
                                    <a href="#b2s-templates" class="b2s-templates" data-toggle="tab"><?php esc_html_e("Post Templates", "blog2social") ?></a> 
                                </li>
                            </ul>
                            <hr class="b2s-settings-line">
                            <div class="tab-content clearfix">
                                <div class="tab-pane active" id="b2s-general">
                                    <?php
                                    echo wp_kses($settingsItem->getGeneralSettingsHtml(), array(
                                        'h4' => array(
                                            'style' => array()
                                        ),
                                        'br' => array(),
                                        'p' => array(),
                                        'strong' => array(),
                                        'div' => array(
                                            'class' => array()
                                        ),
                                        'label' => array(
                                            'class' => array(),
                                            'for' => array()
                                        ),
                                        'select' => array(
                                            'class' => array(),
                                            'id' => array(),
                                            'name' => array()
                                        ),
                                        'option' => array(
                                            'value' => array(),
                                            'data-offset' => array(),
                                            'selected' => array()
                                        ),
                                        'a' => array(
                                            'href' => array(),
                                            'class' => array(),
                                            'target' => array(),
                                            'data-provider-id' => array(),
                                            'onclick' => array()
                                        ),
                                        'code' => array(
                                            'id' => array()
                                        ),
                                        'input' => array(
                                            'data-size' => array(),
                                            'data-toggle' => array(),
                                            'data-width' => array(),
                                            'data-height' => array(),
                                            'data-onstyle' => array(),
                                            'data-on' => array(),
                                            'data-off' => array(),
                                            'checked' => array(),
                                            'name' => array(),
                                            'class' => array(),
                                            'id' => array(),
                                            'data-area-type' => array(),
                                            'value' => array(),
                                            'type' => array(),
                                            'data-provider-id' => array(),
                                        ),
                                        'span' => array(
                                            'data-provider-id' => array(),
                                            'class' => array(),
                                            'style' => array(),
                                        ),
                                        'img' => array(
                                            'class' => array(),
                                            'alt' => array(),
                                            'src' => array(),
                                        )
                                    ));
                                    ?>
                                </div>
                                <div class="tab-pane" id="b2s-social-meta-data">
                                    <?php if (current_user_can('administrator')) { ?>
                                        <form class="b2sSaveSocialMetaTagsSettings" method="post" novalidate="novalidate">
                                            <?php
                                            echo wp_kses($settingsItem->getSocialMetaDataHtml(), array(
                                                'strong' => array(),
                                                'br' => array(),
                                                'h4' => array(),
                                                'b' => array(),
                                                'p' => array(),
                                                'div' => array(
                                                    'class' => array()
                                                ),
                                                'span' => array(
                                                    'class' => array()
                                                ),
                                                'a' => array(
                                                    'href' => array(),
                                                    'class' => array(),
                                                    'target' => array(),
                                                    'data-meta-type' => array(),
                                                    'data-meta-origin' => array(),
                                                ),
                                                'input' => array(
                                                    'type' => array(),
                                                    'name' => array(),
                                                    'value' => array(),
                                                    'id' => array(),
                                                    'class' => array(),
                                                    'checked' => array(),
                                                    'readonly' => array(),
                                                ),
                                                'label' => array(
                                                    'for' => array()
                                                ),
                                                'button' => array(
                                                    'class' => array(),
                                                    'type' => array(),
                                                    'disabled' => array(),
                                                    'data-id' => array(),
                                                ),
                                                'select' => array(
                                                    'class' => array(),
                                                    'name' => array(),
                                                ),
                                                'option' => array(
                                                    'value' => array(),
                                                    'selected' => array(),
                                                )
                                            ));
                                            ?>
                                            <button class="btn btn-primary pull-right" type="submit" <?php
                                            if (B2S_PLUGIN_ADMIN) {
                                                echo '';
                                            } else {
                                                echo 'disabled="true"';
                                            }
                                            ?>><?php esc_html_e('save', 'blog2social') ?></button>
                                            <input type="hidden" name="is_admin" value="<?php echo ((B2S_PLUGIN_ADMIN) ? 1 : 0) ?>">
                                            <input type="hidden" name="version" value="<?php echo esc_attr(B2S_PLUGIN_USER_VERSION) ?>">
                                            <input type="hidden" name="action" value="b2s_save_social_meta_tags">
                                        </form>
                                    <?php } else { ?>
                                        <div class="row width-100" id="b2s-settings-no-admin">
                                            <div class="text-center b2s-text-bold"><?php esc_html_e("You need admin rights to use the Social Meta Data. Please contact your administrator.", "blog2social"); ?></div>
                                        </div>
                                    <?php } ?>
                                </div>

                                <div class="tab-pane" id="b2s-network">
                                    <p><strong><?php esc_html_e("Connect Blog2Social with 16 different social media networks you like to share your WordPress blog posts and pages as well as imported posts and social media posts on. The following networks are available:", "blog2social") ?></strong></p>
                                    <br>
                                    <ul class="list-group">
                                        <a href="admin.php?page=blog2social-network"><li class="list-group-item"><img class="b2s-network-image" alt="Facebook" src="<?php echo esc_url(plugins_url('/assets/images/portale/1_flat.png', B2S_PLUGIN_FILE)) ?>"> Facebook</li></a>
                                        <a href="admin.php?page=blog2social-network"><li class="list-group-item"><img class="b2s-network-image" alt="Twitter" src="<?php echo esc_url(plugins_url('/assets/images/portale/2_flat.png', B2S_PLUGIN_FILE)) ?>"> Twitter</li></a>
                                        <a href="admin.php?page=blog2social-network"><li class="list-group-item"><img class="b2s-network-image" alt="Instagram" src="<?php echo esc_url(plugins_url('/assets/images/portale/12_flat.png', B2S_PLUGIN_FILE)) ?>"> Instagram</li></a>
                                        <a href="admin.php?page=blog2social-network"><li class="list-group-item"><img class="b2s-network-image" alt="Google Business Profile" src="<?php echo esc_url(plugins_url('/assets/images/portale/18_flat.png', B2S_PLUGIN_FILE)) ?>"> Google Business Profile</li></a>
                                        <a href="admin.php?page=blog2social-network"><li class="list-group-item"><img class="b2s-network-image" alt="LinkedIn" src="<?php echo esc_url(plugins_url('/assets/images/portale/3_flat.png', B2S_PLUGIN_FILE)) ?>"> LinkedIn</li></a>
                                        <a href="admin.php?page=blog2social-network"><li class="list-group-item"><img class="b2s-network-image" alt="XING" src="<?php echo esc_url(plugins_url('/assets/images/portale/19_flat.png', B2S_PLUGIN_FILE)) ?>"> XING</li></a>
                                        <a href="admin.php?page=blog2social-network"><li class="list-group-item"><img class="b2s-network-image" alt="Pinterest" src="<?php echo esc_url(plugins_url('/assets/images/portale/6_flat.png', B2S_PLUGIN_FILE)) ?>"> Pinterest</li></a>
                                        <a href="admin.php?page=blog2social-network"><li class="list-group-item"><img class="b2s-network-image" alt="Reddit" src="<?php echo esc_url(plugins_url('/assets/images/portale/15_flat.png', B2S_PLUGIN_FILE)) ?>"> Reddit</li></a>
                                        <a href="admin.php?page=blog2social-network"><li class="list-group-item"><img class="b2s-network-image" alt="Torial" src="<?php echo esc_url(plugins_url('/assets/images/portale/14_flat.png', B2S_PLUGIN_FILE)) ?>"> Torial</li></a>
                                        <a href="admin.php?page=blog2social-network"><li class="list-group-item"><img class="b2s-network-image" alt="Medium" src="<?php echo esc_url(plugins_url('/assets/images/portale/11_flat.png', B2S_PLUGIN_FILE)) ?>"> Medium</li></a>
                                        <a href="admin.php?page=blog2social-network"><li class="list-group-item"><img class="b2s-network-image" alt="Tumblr" src="<?php echo esc_url(plugins_url('/assets/images/portale/4_flat.png', B2S_PLUGIN_FILE)) ?>"> Tumblr</li></a>
                                        <a href="admin.php?page=blog2social-network"><li class="list-group-item"><img class="b2s-network-image" alt="Flickr" src="<?php echo esc_url(plugins_url('/assets/images/portale/7_flat.png', B2S_PLUGIN_FILE)) ?>"> Flickr</li></a>
                                        <a href="admin.php?page=blog2social-network"><li class="list-group-item"><img class="b2s-network-image" alt="Diigo" src="<?php echo esc_url(plugins_url('/assets/images/portale/9_flat.png', B2S_PLUGIN_FILE)) ?>"> Diigo</li></a>
                                        <a href="admin.php?page=blog2social-network"><li class="list-group-item"><img class="b2s-network-image" alt="Bloglovin" src="<?php echo esc_url(plugins_url('/assets/images/portale/16_flat.png', B2S_PLUGIN_FILE)) ?>"> Bloglovin</li></a>
                                        <a href="admin.php?page=blog2social-network"><li class="list-group-item"><img class="b2s-network-image" alt="VK" src="<?php echo esc_url(plugins_url('/assets/images/portale/17_flat.png', B2S_PLUGIN_FILE)) ?>"> VK</li></a>
                                        <a href="admin.php?page=blog2social-network"><li class="list-group-item"><img class="b2s-network-image" alt="Telegram" src="<?php echo esc_url(plugins_url('/assets/images/portale/24_flat.png', B2S_PLUGIN_FILE)) ?>"> Telegram</li></a>
                                        <a href="admin.php?page=blog2social-network"><li class="list-group-item"><img class="b2s-network-image" alt="Blogger" src="<?php echo esc_url(plugins_url('/assets/images/portale/25_flat.png', B2S_PLUGIN_FILE)) ?>"> Blogger</li></a>
                                        <a href="admin.php?page=blog2social-network"><li class="list-group-item"><img class="b2s-network-image" alt="Ravelry" src="<?php echo esc_url(plugins_url('/assets/images/portale/26_flat.png', B2S_PLUGIN_FILE)) ?>"> Ravelry</li></a>
                                        <a href="admin.php?page=blog2social-network"><li class="list-group-item"><img class="b2s-network-image" alt="Instapaper" src="<?php echo esc_url(plugins_url('/assets/images/portale/27_flat.png', B2S_PLUGIN_FILE)) ?>"> Instapaper</li></a>
                                    </ul>
                                    <p><?php echo sprintf(__('You will find more information on how to connect your social media networks in the <a href="%s" target="_blank">connecting social media network guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('faq_network'))); ?></p>
                                    <br>
                                    <a href="admin.php?page=blog2social-network" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-user"></i> <?php esc_html_e('Connect your social media networks', 'blog2social') ?></a>
                                </div>
                                <div class="tab-pane" id="b2s-times">
                                    <p><?php esc_html_e('Use the pre-defined best time settings or define your own best time settings for sharing  your posts . Posting at the right time can be essential to make sure your content is most likely be seen.', 'blog2social') ?></p>
                                    <br>
                                    <p><?php echo sprintf(__('You will find more information about the pre-defined best time settings by Blog2Social in this <a href="%s" target="_blank">best time guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('besttimes_blogpost'))); ?></p>
                                    <br>
                                    <p><?php echo sprintf(__('An instruction on how to define your own best times is explained in the guide "<a href="%s" target="_blank">How do I set my own time setting to post on social media?</a>".', 'blog2social'), esc_url(B2S_Tools::getSupportLink('besttimes_faq'))); ?></p>
                                    <br>
                                    <a href="admin.php?page=blog2social-network" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-time"></i> <?php esc_html_e('Check, edit or define your social media time settings', 'blog2social') ?></a>
                                </div>
                                <div class="tab-pane" id="b2s-templates">
                                    <p><strong><?php esc_html_e('Edit the post templates for each social media network to turn your social media posts automatically into tailored posts for each network and community. You can edit the structure of your post with the following variables:', 'blog2social') ?></strong></p>
                                    <br>
                                    <ul class="list-group">
                                        <li class="list-group-item"><?php esc_html_e('Title: The title of your post.', 'blog2social') ?></li>
                                        <li class="list-group-item"><?php esc_html_e('Content: The content of your post.', 'blog2social') ?></li>
                                        <li class="list-group-item"><?php esc_html_e('Excerpt: The summary of your post (you define it in the side menu of your post).', 'blog2social') ?></li>
                                        <li class="list-group-item"><?php esc_html_e('Keywords: The tags you have set in your post.', 'blog2social') ?></li>
                                        <li class="list-group-item"><?php esc_html_e('Author: The author of the post.', 'blog2social') ?></li>
                                        <li class="list-group-item"><?php esc_html_e('Price: The price of your product, if you have installed WooCommerce on your website/ blog.', 'blog2social') ?></li>
                                    </ul>
                                    <p><?php echo sprintf(__('You will find more information on how to use post templates for your social media posts in this <a href="%s" target="_blank">post template guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('template_faq'))); ?></p>
                                    <br>
                                    <a href="admin.php?page=blog2social-network" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-pencil"></i> <?php esc_html_e('Define your post templates for each social media network', 'blog2social') ?></a>
                                </div>
                            </div>
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
<input type="hidden" id="b2sUserTimeFormat" value="<?php echo esc_attr($optionUserTimeFormat); ?>">
<input type="hidden" id="b2sUserLang" value="<?php echo esc_attr(strtolower(substr(get_locale(), 0, 2))); ?>">
<input type="hidden" id="b2sShowSection" value="<?php echo (isset($_GET['show']) ? esc_attr(sanitize_text_field($_GET['show'])) : ''); ?>">
<input type="hidden" id="b2s_wp_media_headline" value="<?php esc_html_e('Select or upload an image from media gallery', 'blog2social') ?>">
<input type="hidden" id="b2s_wp_media_btn" value="<?php esc_html_e('Use image', 'blog2social') ?>">
<input type="hidden" id="b2s_user_version" value="<?php echo esc_attr(B2S_PLUGIN_USER_VERSION) ?>">
<input type="hidden" id="b2sServerUrl" value="<?php echo esc_url(B2S_PLUGIN_SERVER_URL); ?>">


<div class="modal fade" id="b2sInfoAllowShortcodeModal" tabindex="-1" role="dialog" aria-labelledby="b2sInfoAllowShortcodeModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoAllowShortcodeModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Allow shortcodes in my social media posts (e.g. Page Builder)', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php echo sprintf(__('Some WordPress plugins use short codes, e.g. Page Builder plugins. When a shortcode is inserted in a WordPress post or WordPress page, WordPress calls the function that is included in the shortcode and performs the corresponding actions as soon as you publish your post on your Wordpress website. If you like Blog2Social to consider shortcodes when posting to social media and automatically insert the defined content in your social media post, activate this feature. You will find more information about the function of shortcodes and which plugins are supported by Blog2Social in the following <a href="%s" target="_blank">shortcode guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('allow_shortcodes'))); ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sInfoLegacyMode" tabindex="-1" role="dialog" aria-labelledby="b2sInfoLegacyMode" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoLegacyMode" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Activate Legacy mode ', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php esc_html_e('Plugin contents are loaded one at a time to minimize server load.', 'blog2social') ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sInfoNoCache" tabindex="-1" role="dialog" aria-labelledby="b2sInfoNoCache" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoNoCache" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Instant Caching for Facebook Link Posts', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php esc_html_e('Please enable this feature, if you are using varnish caching (HTTP accelerator to relieve your website). Blog2Social will add a "no-cache=1" parameter to the post URL of your Facebook link posts to ensure that Facebook always pulls the current meta data of your blog post.', 'blog2social') ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sInfoTimeZoneModal" tabindex="-1" role="dialog" aria-labelledby="b2sInfoTimeZoneModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoTimeZoneModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Personal Time Zone', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php esc_html_e('Blog2Social applies the scheduled time settings based on the time zone defined in the general settings of your WordPress. You can select a user-specific time zone that deviates from the Wordpress system time zone for your social media scheduling. To do this, select the desired time zone 24h or 12h (am/pm), by simply clicking on the button.', 'blog2social') ?>
            </div>
        </div>
    </div>
</div>
