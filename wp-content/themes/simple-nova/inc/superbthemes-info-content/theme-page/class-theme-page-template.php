<?php

namespace SuperbThemesThemeInformationContent\ThemePage;

defined("ABSPATH") || exit();

class ThemePageTemplate
{
    private $Theme;
    private $ParentName;
    private $ThemeName;
    private $PremiumText;

    private $Features;
    private $ThemeLink;
    private $DemoLink;

    public function __construct($data)
    {
        $this->Theme = wp_get_theme();
        $this->ParentName = is_child_theme() ? wp_get_theme($this->Theme->Template) : '';
        $this->ThemeName = is_child_theme() ? sprintf(__("%s and %s", 'simple-nova'), $this->Theme, $this->ParentName) : $this->Theme;
        $this->PremiumText = is_child_theme() ? sprintf(__("Unlock all features by upgrading to the premium edition of %s and its parent theme %s.", 'simple-nova'), $this->Theme, $this->ParentName) : sprintf(__("Unlock all features by upgrading to the premium edition of %s.", 'simple-nova'), $this->Theme);
        $this->ThemeLink = $data['theme_url'];
        $this->DemoLink = $data['demo_url'];
        $base_features = array(
             array(
                'title' => __("Fully Search Engine Optimized", "simple-nova"), 'base' => true, 'icon' => "img-icon-8.png", 'description' => __("Get free traffic by ranking #1 on Google with the lightning-fast & SEO-optimized premium version.", "simple-nova")
            ),
            array(
                'title' => __("Page Speed Optimized", "simple-nova"), 'base' => true, 'icon' => "img-icon-6.png", 'description' => __("Unlock maximum speed with the premium version. It loads in less than 0.3 seconds. ", "simple-nova")
            ),
            array(
                'title' => __("Customize Everything", "simple-nova"), 'base' => true, 'icon' => "img-icon-7.png", 'description' => __("Customize the design to fit your brand or style with our easy-to-use customization options.", "simple-nova")
            ),
            array(
                'title' => __("E-commerce Compatibility", "simple-nova"), 'base' => true, 'icon' => "img-icon-5.png", 'description' => __("Create your online store easily. The premium version is compatible with all popular e-commerce plugins.", "simple-nova")
            ),
            array(
                'title' => __("Customer Support & Documentation", "simple-nova"), 'base' => true, 'icon' => "img-icon-4.png", 'description' => __("Benefit from our comprehensive documentation and dedicated support team, always ready to help.", "simple-nova")
            ),
            array(
                'title' => __("Works With All Page Builders", "simple-nova"), 'base' => true, 'icon' => "img-icon-3.png", 'description' => __("Brizy, Elementor, Divi Builder, Beaver Builder - you name it. Every page builder plugin is compatible.", "simple-nova")
            ),
            array(
                'title' => __("1-Click Starter Content Import", "simple-nova"), 'base' => true, 'icon' => "img-icon-2.png", 'description' => __("Get started easily with our one-click demo content import feature. Get your website up and running in seconds.", "simple-nova")
            ),
            array(
                'title' => __("Premium Designs, Patterns & Layouts", "simple-nova"), 'base' => true, 'icon' => "img-icon-1.png", 'description' => __("Access all the premium layouts and designs perfect for any niche or industry.", "simple-nova")
            ),
            array(
                'title' => __("Works On All Devices And Browsers", "simple-nova"), 'base' => true, 'icon' => "devices-duotone.svg", 'description' => __("The premium version looks perfect everywhere, from desktop to mobile, and in every browser.", "simple-nova")
            ),
            array(
                'title' => __("AMP Compatible And Mobile Ready", "simple-nova"), 'base' => true, 'icon' => "fse_icon_mobile.svg", 'description' => __("Stay ahead with Accelerated Mobile Pages (AMP) compatibility.", "simple-nova")
            ),
            array(
                'title' => __("GDPR Compliant", "simple-nova"), 'base' => true, 'icon' => "shield-check-duotone.svg", 'description' => __("Our premium version comes fully compliant, giving you peace of mind about user data protection and privacy.", "simple-nova")
            ),
            array(
                'title' => __("Frequent Updates", "simple-nova"), 'base' => true, 'icon' => "arrows-clockwise-duotone.svg", 'description' => __("Our premium version provides frequent enhancements for security, performance, and features.", "simple-nova")
            ),
            array(
                'title' => __("Child Themes", "simple-nova"), 'base' => true, 'icon' => "img-2.png", 'description' => __("Use child themes to make modifications without affecting the parent theme's code, ensuring smooth updates.", "simple-nova")
            ),
            array(
                'title' => __("WordPress blocks", "simple-nova"), 'base' => true, 'icon' => "stack-duotone.png", 'description' => __("Use our many custom WordPress Gutenberg blocks for every purpose!", "simple-nova")
            ),
            array(
                'title' => __("WordPress patterns", "simple-nova"), 'base' => true, 'icon' => "grid-nine-duotone.png", 'description' => __("Take advantage of the 400+ beautiful patterns for every type of website.", "simple-nova")
            ),
            array(
                'title' => __("Elementor sections", "simple-nova"), 'base' => true, 'icon' => "img-1.png", 'description' => __("Access 300+ pre-built Elementor sections and build beautiful sites, fast.", "simple-nova")
            )
        );
        $this->Features = $data['features'] ? array_merge($base_features, $data['features']) : $base_features;

        $this->Render();
    }

    private function Render()
    {
        ?>
        <div class="wrap">
            <div class="spt-theme-settings-wrapper">
                <div class="spt-theme-settings-wrapper-main-content">

                    <div class="spt-theme-settings-wrapper-main-content-section">
                        <div class="spt-theme-settings-wrapper-main-content-section-top">
                            <span class="spt-theme-settings-headline"><?php esc_html_e("Customize Settings", 'simple-nova'); ?></span>
                            <a class="spt-theme-settings-headline-link" href="<?php echo esc_url(admin_url('site-editor.php'))  ?>"><?php esc_html_e("Go To Site Editor", 'simple-nova'); ?></a>
                        </div>

                        <div class="spt-theme-settings-content">

                            <div class="spt-theme-settings-content-getting-started-wrapper">
                                <div class="spt-theme-settings-content-item">
                                    <div class="spt-theme-settings-content-item-header">
                                        <img width="25" height="25" src="<?php echo esc_url(get_template_directory_uri() . '/inc/superbthemes-info-content/icons/list-bullets.svg'); ?>" />
                                        <div class="spt-theme-settings-content-item-headline">
                                            <?php esc_html_e("Add Menus", 'simple-nova'); ?>
                                        </div>
                                        <p><?php esc_html_e("Add a navigation to your website to improve the user experience.", 'simple-nova'); ?></p>
                                    </div>
                                    <div class="spt-theme-settings-content-item-content">
                                        <a class="spt-theme-settings-content-item-button" href="<?php echo esc_url(admin_url('site-editor.php'))  ?>"><?php esc_html_e("Go To Site Editor", 'simple-nova'); ?></a>
                                    </div>
                                </div>

                                <div class="spt-theme-settings-content-item">
                                    <div class="spt-theme-settings-content-item-header">
                                        <img width="25" height="25" src="<?php echo esc_url(get_template_directory_uri() . '/inc/superbthemes-info-content/icons/squares-four.svg'); ?>" />
                                        <div class="spt-theme-settings-content-item-headline">
                                            <?php esc_html_e("Edit Front Page", 'simple-nova'); ?>
                                        </div>
                                        <p><?php esc_html_e("Edit and customize your front page design through the site editor.", 'simple-nova'); ?></p>
                                    </div>
                                    <div class="spt-theme-settings-content-item-content">
                                        <a class="spt-theme-settings-content-item-button" href="<?php echo esc_url(admin_url('site-editor.php'))  ?>"><?php esc_html_e("Go To Site Editor", 'simple-nova'); ?></a>
                                    </div>
                                </div>

                                <div class="spt-theme-settings-content-item">
                                    <div class="spt-theme-settings-content-item-header">
                                        <img width="25" height="25" src="<?php echo esc_url(get_template_directory_uri() . '/inc/superbthemes-info-content/icons/paint-brush.svg'); ?>" />
                                        <div class="spt-theme-settings-content-item-headline">
                                            <?php esc_html_e("Customize Design", 'simple-nova'); ?>
                                        </div>
                                        <p><?php esc_html_e("Customize your website design to fit your personality or brand.", 'simple-nova'); ?></p>
                                    </div>
                                    <div class="spt-theme-settings-content-item-content">
                                        <a class="spt-theme-settings-content-item-button" href="<?php echo esc_url(admin_url('site-editor.php'))  ?>"><?php esc_html_e("Go To Site Editor", 'simple-nova'); ?></a>
                                    </div>
                                </div>

                                <div class="spt-theme-settings-content-item">
                                    <div class="spt-theme-settings-content-item-header">
                                        <img width="25" height="25" src="<?php echo esc_url(get_template_directory_uri() . '/inc/superbthemes-info-content/icons/text-a-underline.svg'); ?>" />
                                        <div class="spt-theme-settings-content-item-headline">
                                            <?php esc_html_e("Change Site Title", 'simple-nova'); ?>
                                        </div>
                                        <p><?php esc_html_e("Add your website name and tagline to improve the design and SEO.", 'simple-nova'); ?></p>
                                    </div>
                                    <div class="spt-theme-settings-content-item-content">
                                        <a class="spt-theme-settings-content-item-button" href="<?php echo esc_url(admin_url('site-editor.php'))  ?>"><?php esc_html_e("Go To Site Editor", 'simple-nova'); ?></a>
                                    </div>
                                </div>

                                <div class="spt-theme-settings-content-item">
                                    <div class="spt-theme-settings-content-item-header">
                                        <img width="25" height="25" src="<?php echo esc_url(get_template_directory_uri() . '/inc/superbthemes-info-content/icons/image.svg'); ?>" />
                                        <div class="spt-theme-settings-content-item-headline">
                                            <?php esc_html_e("Upload Logo", 'simple-nova'); ?>
                                        </div>
                                        <p><?php esc_html_e("Add a custom logo to make your website look more professional.", 'simple-nova'); ?></p>
                                    </div>
                                    <div class="spt-theme-settings-content-item-content">
                                        <a class="spt-theme-settings-content-item-button" href="<?php echo esc_url(admin_url('site-editor.php'))  ?>"><?php esc_html_e("Go To Site Editor", 'simple-nova'); ?></a>
                                    </div>
                                </div>

                                <div class="spt-theme-settings-content-item">
                                    <div class="spt-theme-settings-content-item-header">
                                        <img width="25" height="25" src="<?php echo esc_url(get_template_directory_uri() . '/inc/superbthemes-info-content/icons/file.svg'); ?>" />
                                        <div class="spt-theme-settings-content-item-headline">
                                            <?php esc_html_e("Create New Pages", 'simple-nova'); ?>
                                        </div>
                                        <p><?php esc_html_e("Start creating your website by adding pages to it.", 'simple-nova'); ?></p>
                                    </div>
                                    <div class="spt-theme-settings-content-item-content">
                                        <a class="spt-theme-settings-content-item-button" href="<?php echo esc_url(admin_url('edit.php?post_type=page')) ?>"><?php esc_html_e("Create a new page", 'simple-nova'); ?></a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="spt-theme-settings-wrapper-main-content-section">
                        <div class="spt-theme-settings-wrapper-main-content-section-top">
                            <span class="spt-theme-settings-headline"><?php esc_html_e("Premium Features", 'simple-nova'); ?></span>
                            <a class="spt-theme-settings-headline-link" href="<?php echo esc_url($this->ThemeLink); ?>"><?php esc_html_e("Unlock All Features", 'simple-nova'); ?></a>
                        </div>
                        <p class="spt-theme-settings-wrapper-main-content-section-top-description">
                            <?php esc_html_e("Create a beautiful website easily, without coding.", 'simple-nova'); ?>
                        </p>

                        <div class="spt-theme-settings-content spt-theme-settings-content-us">
                            <?php
                            foreach ($this->Features as $feature) :
                                ?>
                                <a target="_blank" href="<?php echo esc_url($this->ThemeLink); ?>" class="spt-theme-settings-content-item spt-theme-settings-content-item-unavailable">
                                    <span class="spt-theme-settings-content-item-unavailable-premium"><?php echo esc_html__("Premium", 'simple-nova'); ?></span>
                                    <div class="spt-theme-settings-content-item-header">
                                        <div>
                                            <img height="32" width="32" src="<?php echo esc_url(get_template_directory_uri() . (isset($feature['base']) ? '/inc/superbthemes-info-content/icons/' : '/inc/superbthemes-info-assets/') . $feature["icon"]); ?>" />
                                        </div>
                                        <span class="spt-theme-settings-content-us-title"><?php echo esc_html($feature["title"]); ?></span></span>
                                        <?php if (isset($feature['description'])) : ?>
                                            <p><?php echo esc_html($feature['description']); ?></p>
                                        <?php else : ?>
                                            <p><?php echo esc_html(sprintf(__("With %s Premium you'll have full access to this feature as well as all the other features listed.", 'simple-nova'), $this->ThemeName)); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="spt-theme-settings-content-item-content">
                                        <span class="spt-theme-settings-content-us-button-link"><?php esc_html_e("Get Premium Version", 'simple-nova'); ?></span>
                                    </div>
                                </a>
                                <?php
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>

                <div class="spt-theme-settings-wrapper-sidebar">
                    <div class="spt-theme-settings-wrapper-sidebar-item">
                        <div class="spt-theme-settings-wrapper-sidebar-item-content">
                            <img class="spt-theme-settings-wrapper-sidebar-item-content-demo-image" src="<?php echo esc_url(get_template_directory_uri() . '/screenshot.png'); ?>" alt="<?php echo esc_attr($this->ThemeName); ?> Preview" />
                            <div class="spt-theme-settings-wrapper-sidebar-item-header"><?php esc_html_e("View Demo", 'simple-nova'); ?></div>
                            <p><?php echo esc_html__("Need inspiration? Take a moment to view our theme demo!", 'simple-nova') ?></p>
                            <a href="<?php echo esc_url($this->DemoLink); ?>" target="_blank" class="button"><?php esc_html_e("View Demo", 'simple-nova'); ?></a>
                        </div>
                    </div>

                    <div class="spt-theme-settings-wrapper-sidebar-item">
                        <img width="25" height="25" src="<?php echo esc_url(get_template_directory_uri() . '/inc/superbthemes-info-content/icons/color-crown.svg'); ?>" />
                        <div class="spt-theme-settings-wrapper-sidebar-item-header"><?php esc_html_e("Upgrade to premium", 'simple-nova'); ?></div>
                        <div class="spt-theme-settings-wrapper-sidebar-item-content">
                            <p><?php echo esc_html($this->PremiumText); ?></p>
                            <a href="<?php echo esc_url($this->ThemeLink); ?>" target="_blank" class="button button-primary"><?php esc_html_e("View Premium Version", 'simple-nova'); ?></a>
                        </div>
                    </div>

                    <div class="spt-theme-settings-wrapper-sidebar-item">
                        <img width="25" height="25" src="<?php echo esc_url(get_template_directory_uri() . '/inc/superbthemes-info-content/icons/chats.svg'); ?>" />
                        <div class="spt-theme-settings-wrapper-sidebar-item-header"><?php esc_html_e("Contact support", 'simple-nova'); ?></div>
                        <div class="spt-theme-settings-wrapper-sidebar-item-content">
                            <p><?php echo esc_html(sprintf(__("If you have issues with %s, please send us an email through our website!", 'simple-nova'), $this->Theme)); ?></p>
                            <a href="https://superbthemes.com/customer-support/" target="_blank" class="button"><?php esc_html_e("Contact Support", 'simple-nova'); ?></a>
                        </div>
                    </div>

                    <div class="spt-theme-settings-wrapper-sidebar-item">
                        <img width="25" height="25" src="<?php echo esc_url(get_template_directory_uri() . '/inc/superbthemes-info-content/icons/shooting-star.svg'); ?>" />
                        <div class="spt-theme-settings-wrapper-sidebar-item-header"><?php esc_html_e("Give us feedback", 'simple-nova'); ?></div>
                        <div class="spt-theme-settings-wrapper-sidebar-item-content">
                            <p><?php echo esc_html(sprintf(__("Do you enjoy using %s? Support us by reviewing us on WordPress.org!", 'simple-nova'), $this->Theme)); ?></p>
                            <a href="https://wordpress.org/support/theme/<?php echo esc_attr(get_stylesheet()); ?>/reviews/#new-post" target="_blank" class="button"><?php esc_html_e("Leave a Review", 'simple-nova'); ?></a>
                        </div>
                    </div>

                </div>

            </div>
        </div>
        <?php
    }
}
