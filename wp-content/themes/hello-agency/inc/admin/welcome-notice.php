<?php

/**
 * file for holding dashboard welcome page for theme
 */
if (!function_exists('hello_agency_is_plugin_installed')) {
    function hello_agency_is_plugin_installed($plugin_slug)
    {
        $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_slug;
        return file_exists($plugin_path);
    }
}
if (!function_exists('hello_agency_is_plugin_activated')) {
    function hello_agency_is_plugin_activated($plugin_slug)
    {
        return is_plugin_active($plugin_slug);
    }
}
if (!function_exists('hello_agency_welcome_notice')) :
    function hello_agency_welcome_notice()
    {
        if (get_option('hello_agency_dismissed_custom_notice')) {
            return;
        }
        global $pagenow;
        $current_screen  = get_current_screen();

        if (is_admin()) {
            if ($current_screen->id !== 'dashboard' && $current_screen->id !== 'themes') {
                return;
            }
            if (is_network_admin()) {
                return;
            }
            if (!current_user_can('manage_options')) {
                return;
            }
            $theme = wp_get_theme();

            if (is_child_theme()) {
                $theme = wp_get_theme()->parent();
            }
            $hello_agency_version = $theme->get('Version');


?>
            <div class="hello-agency-admin-notice notice notice-info is-dismissible content-install-plugin theme-info-notice" id="hello-agency-dismiss-notice">
                <div class="info-content">
                    <h5><span class="theme-name"><span><?php echo __('Welcome to Hello Agency', 'hello-agency'); ?></span></h5>
                    <h1><?php echo __('Start building your website with the most advanced WordPress theme ever! 🚀', 'hello-agency'); ?></h1>
                    </h3>
                    <p class="notice-text"><?php echo __('Please install and activate all recommended plugins to use 40+ advanced blocks, 200+ pre-built sections, and 10+ starter demos. Enhance website building and launch your site within minutes with just a few clicks! - Cozy Addons, Cozy Essential Addons, Advanced Import.', 'hello-agency'); ?></p>

                    <a href="#" id="install-activate-button" class="button admin-button info-button"><?php echo __('Getting started with a single click', 'hello-agency'); ?></a>
                    <a href="<?php echo admin_url(); ?>themes.php?page=about-hello-agency" class="button admin-button info-button"><?php echo __('Explore Hello Agency', 'hello-agency'); ?></a>


                </div>
                <div class="theme-hero-screens">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/inc/admin/images/hello_agency_screen.png'); ?>" />
                </div>

            </div>
    <?php
        }
    }
endif;
add_action('admin_notices', 'hello_agency_welcome_notice');

add_action('admin_notices', 'hello_agency_welcome_notice');
function hello_agency_dismissble_notice()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'hello_agency_nonce')) {
        wp_send_json_error(array('message' => 'Nonce verification failed.'));
        return;
    }

    $result = update_option('hello_agency_dismissed_custom_notice', 1);

    if ($result) {
        wp_send_json_success();
    } else {
        wp_send_json_error(array('message' => 'Failed to update option'));
    }
}
add_action('wp_ajax_hello_agency_dismissble_notice', 'hello_agency_dismissble_notice');
// Hook into a custom action when the button is clicked
add_action('wp_ajax_hello_agency_install_and_activate_plugins', 'hello_agency_install_and_activate_plugins');
add_action('wp_ajax_nopriv_hello_agency_install_and_activate_plugins', 'hello_agency_install_and_activate_plugins');
add_action('wp_ajax_hello_agency_rplugin_activation', 'hello_agency_rplugin_activation');
add_action('wp_ajax_nopriv_hello_agency_rplugin_activation', 'hello_agency_rplugin_activation');

function check_plugin_installed_status($pugin_slug, $plugin_file)
{
    return file_exists(ABSPATH . 'wp-content/plugins/' . $pugin_slug . '/' . $plugin_file) ? true : false;
}

/* Check if plugin is activated */


function check_plugin_active_status($pugin_slug, $plugin_file)
{
    return is_plugin_active($pugin_slug . '/' . $plugin_file) ? true : false;
}

require_once(ABSPATH . 'wp-admin/includes/plugin-install.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/misc.php');
require_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
function hello_agency_install_and_activate_plugins()
{
    if (!current_user_can('manage_options')) {
        return;
    }
    check_ajax_referer('hello_agency_nonce', 'nonce');
    // Define the plugins to be installed and activated
    $recommended_plugins = array(
        array(
            'slug' => 'cozy-addons',
            'file' => 'cozy-addons.php',
            'name' => __('Cozy Blocks', 'hello-agency')
        ),
        array(
            'slug' => 'advanced-import',
            'file' => 'advanced-import.php',
            'name' => __('Advanced Imporrt', 'hello-agency')
        ),
        array(
            'slug' => 'cozy-essential-addons',
            'file' => 'cozy-essential-addons.php',
            'name' => __('Cozy Essential Addons', 'hello-agency')
        ),
        // Add more plugins here as needed
    );

    // Include the necessary WordPress functions


    // Set up a transient to store the installation progress
    set_transient('install_and_activate_progress', array(), MINUTE_IN_SECONDS * 10);

    // Loop through each plugin
    foreach ($recommended_plugins as $plugin) {
        $plugin_slug = $plugin['slug'];
        $plugin_file = $plugin['file'];
        $plugin_name = $plugin['name'];

        // Check if the plugin is active
        if (is_plugin_active($plugin_slug . '/' . $plugin_file)) {
            update_install_and_activate_progress($plugin_name, 'Already Active');
            continue; // Skip to the next plugin
        }

        // Check if the plugin is installed but not active
        if (is_hello_agency_plugin_installed($plugin_slug . '/' . $plugin_file)) {
            $activate = activate_plugin($plugin_slug . '/' . $plugin_file);
            if (is_wp_error($activate)) {
                update_install_and_activate_progress($plugin_name, 'Error');
                continue; // Skip to the next plugin
            }
            update_install_and_activate_progress($plugin_name, 'Activated');
            continue; // Skip to the next plugin
        }

        // Plugin is not installed or activated, proceed with installation
        update_install_and_activate_progress($plugin_name, 'Installing');

        // Fetch plugin information
        $api = plugins_api('plugin_information', array(
            'slug' => $plugin_slug,
            'fields' => array('sections' => false),
        ));

        // Check if plugin information is fetched successfully
        if (is_wp_error($api)) {
            update_install_and_activate_progress($plugin_name, 'Error');
            continue; // Skip to the next plugin
        }

        // Set up the plugin upgrader
        $upgrader = new Plugin_Upgrader();
        $install = $upgrader->install($api->download_link);

        // Check if installation is successful
        if ($install) {
            // Activate the plugin
            $activate = activate_plugin($plugin_slug . '/' . $plugin_file);

            // Check if activation is successful
            if (is_wp_error($activate)) {
                update_install_and_activate_progress($plugin_name, 'Error');
                continue; // Skip to the next plugin
            }
            update_install_and_activate_progress($plugin_name, 'Activated');
        } else {
            update_install_and_activate_progress($plugin_name, 'Error');
        }
    }

    // Delete the progress transient
    $redirect_url = admin_url('themes.php?page=advanced-import');

    // Delete the progress transient
    delete_transient('install_and_activate_progress');
    // Return JSON response
    wp_send_json_success(array('redirect_url' => $redirect_url));
}

// Function to check if a plugin is installed but not active
function is_hello_agency_plugin_installed($plugin_slug)
{
    $plugins = get_plugins();
    return isset($plugins[$plugin_slug]);
}

// Function to update the installation and activation progress
function update_install_and_activate_progress($plugin_name, $status)
{
    $progress = get_transient('install_and_activate_progress');
    $progress[] = array(
        'plugin' => $plugin_name,
        'status' => $status,
    );
    set_transient('install_and_activate_progress', $progress, MINUTE_IN_SECONDS * 10);
}
function hello_agency_dashboard_menu()
{
    add_theme_page(esc_html__('Hello Agency', 'hello-agency'), esc_html__('Hello Agency', 'hello-agency'), 'edit_theme_options', 'about-hello-agency', 'hello_agency_theme_info_display');
}
add_action('admin_menu', 'hello_agency_dashboard_menu');
function hello_agency_theme_info_display()
{ ?>
    <div class="dashboard-about-hello-agency">
        <h1> <?php echo __('Welcome to the Hello Agency - FSE WordPress Theme', 'hello-agency') ?></h1>
        <p><?php echo __('Hello Agency is the multipurpose FSE theme which provides more than 20 home sections patterns which is ready to build any type of functional website. With its minimal, clean design and powerful feature set, Hello Agency enables power to build any kinds of website and provides a wide range of valuable patterns, including hero/banner, about us, portfolio/project, call-to-action buttons, and customer testimonials, teams and more. Hello Agency is suitable for any niches whether  for your business, personal brand, or creative project or blogs and comes with more than 40+ beautiful premium patterns. Explore more about Hello Agency at https://cozythemes.com/hello-agency/.', 'hello-agency') ?></p>

        <h3 class="hello-agency-baisc-guideline-header"><?php echo __('Basic Theme Setup', 'hello-agency') ?></h3>
        <div class="hello-agency-baisc-guideline">
            <div class="featured-box">
                <ul>
                    <li><strong><?php echo __('Setup Header Layout:', 'hello-agency') ?></strong>
                        <ul>
                            <li> - <?php echo __('Go to Appearance -> Editor -> Patterns -> Template Parts -> Header:', 'hello-agency') ?></li>
                            <li> - <?php echo __('click on Header > Click on Edit (Icon) -> Add or Remove Requirend block/content as your requirement.:', 'hello-agency') ?></li>
                            <li> - <?php echo __('Click on save to update your layout', 'hello-agency') ?></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="featured-box">
                <ul>
                    <li><strong><?php echo __('Setup Footer Layout:', 'hello-agency') ?></strong>
                        <ul>
                            <li> - <?php echo __('Go to Appearance -> Editor -> Patterns -> Template Parts -> Footer:', 'hello-agency') ?></li>
                            <li> - <?php echo __('click on Footer > Click on Edit (Icon) > Add or Remove Requirend block/content as your requirement.:', 'hello-agency') ?></li>
                            <li> - <?php echo __('Click on save to update your layout', 'hello-agency') ?></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="featured-box">
                <ul>
                    <li><strong><?php echo __('Setup Templates like Homepage/404/Search/Page/Single and more templates Layout:', 'hello-agency') ?></strong>
                        <ul>
                            <li> - <?php echo __('Go to Appearance -> Editor -> Templates:', 'hello-agency') ?></li>
                            <li> - <?php echo __('click on Template(You need to edit/update) > Click on Edit (Icon) > Add or Remove Requirend block/content as your requirement.:', 'hello-agency') ?></li>
                            <li> - <?php echo __('Click on save to update your layout', 'hello-agency') ?></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="featured-box">
                <ul>
                    <li><strong><?php echo __('Restore/Reset Default Content layout of Template(Like: Frontpage/Blog/Archive etc.)', 'hello-agency') ?></strong>
                        <ul>
                            <li> - <?php echo __('Go to Appearance -> Editor -> Templates:', 'hello-agency') ?></li>
                            <li> - <?php echo __('Click on Manage all Templates', 'hello-agency') ?></li>
                            <li> - <?php echo __('Click on 3 Dots icon at right side of respective Template', 'hello-agency') ?></li>
                            <li> - <?php echo __('Click on Clear Customization', 'hello-agency') ?></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="featured-box">
                <ul>
                    <li><strong><?php echo __('Restore/Reset Default Content layout of Template Parts(Header/Footer/Sidebar)', 'hello-agency') ?></strong>
                        <ul>
                            <li> - <?php echo __('Go to Appearance -> Editor -> Patterns:', 'hello-agency') ?></li>
                            <li> - <?php echo __('Click on Manage All Template Parts', 'hello-agency') ?></li>
                            <li> - <?php echo __('Click on 3 Dots icon at right side of respective Template parts', 'hello-agency') ?></li>
                            <li> - <?php echo __('Click on Clear Customization', 'hello-agency') ?></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

        <h3><?php echo __('Required Plugins', 'hello-agency'); ?></h3>
        <p class="notice-text"><?php echo __('Please install and activate all recommended pluign to import the demo with "one click demo import" feature and unlock premium features!(for pro version)', 'hello-agency') ?></p>
        <ul class="hello-agency-required-plugin">
            <li>

                <h4><?php echo __('1. Cozy Addons', 'hello-agency'); ?>
                    <?php
                    if (hello_agency_is_plugin_activated('cozy-addons/cozy-addons.php')) {
                        echo __(': Plugin has been already activated!', 'hello-agency');
                    } elseif (hello_agency_is_plugin_installed('cozy-addons/cozy-addons.php')) {
                        echo __(': Plugin does not activated, Activate the plugin to use one click demo import and unlock premium features.', 'hello-agency');
                    } else {
                        echo ': <a href="' . get_admin_url() . 'plugin-install.php?tab=plugin-information&plugin=cozy-addons&TB_iframe=true&width=600&height=550">' . esc_html__('Install and Activate', 'hello-agency') . '</a>';
                    }
                    ?>
                </h4>
            </li>
            <li>

                <h4><?php echo __('2. Cozy Essential Addons', 'hello-agency'); ?>
                    <?php
                    if (hello_agency_is_plugin_activated('cozy-essential-addons/cozy-essential-addons.php')) {
                        echo __(': Plugin has been already activated!', 'hello-agency');
                    } elseif (hello_agency_is_plugin_installed('cozy-essential-addons/cozy-essential-addons.php')) {
                        echo __(': Plugin does not activated, Activate the plugin to use one click demo import and unlock premium features.', 'hello-agency');
                    } else {
                        echo ': <a href="' . get_admin_url() . 'plugin-install.php?tab=plugin-information&plugin=cozy-essential-addons&TB_iframe=true&width=600&height=550">' . esc_html__('Install and Activate', 'hello-agency') . '</a>';
                    }
                    ?>
                </h4>
            </li>
            <li>
                <h4><?php echo __('3. Advanced Import - Need only to use "one click demo import" features', 'hello-agency'); ?>
                    <?php
                    if (hello_agency_is_plugin_activated('advanced-import/advanced-import.php')) {
                        echo __(': Plugin has been already activated!', 'hello-agency');
                    } elseif (hello_agency_is_plugin_installed('advanced-import/advanced-import.php')) {
                        echo __(': Plugin does not activated, Activate the plugin to use one click demo import feature.', 'hello-agency');
                    } else {
                        echo ': <a href="' . get_admin_url() . 'plugin-install.php?tab=plugin-information&plugin=advanced-import&TB_iframe=true&width=600&height=550">' . esc_html__('Install and Activate', 'hello-agency') . '</a>';
                    }
                    ?>
                </h4>
            </li>
        </ul>
        <a href="#" id="install-activate-button" class="button admin-button installing-all-pluign info-button"><?php echo __('Getting started with a single click', 'hello-agency'); ?></a>
        <div class="featured-list">
            <div class="half-col free-features">
                <h3><?php echo __('Hello Agency Features (Free)', 'hello-agency') ?></h3>
                <ul>
                    <li><strong> - <?php echo __('Offer 20+ ready to use Home Sections Patterns', 'hello-agency') ?></strong>
                        <ul>
                            <li> <?php echo __('Banner/Hero section pattern - 2', 'hello-agency') ?></li>
                            <li> <?php echo __('Who We Are Section Pattern', 'hello-agency') ?></li>
                            <li> <?php echo __('About section pattern', 'hello-agency') ?></li>
                            <li> <?php echo __('Brands Logo section pattern', 'hello-agency') ?></li>
                            <li> <?php echo __('Call to Action section pattern', 'hello-agency') ?></li>
                            <li> <?php echo __('FAQ section pattern', 'hello-agency') ?></li>
                            <li> <?php echo __('How It Works section pattern', 'hello-agency') ?></li>
                            <li> <?php echo __('Latest posts section pattern', 'hello-agency') ?></li>
                            <li> <?php echo __('Mision and solutions content tabs section pattern', 'hello-agency') ?></li>
                            <li> <?php echo __('Newsletter section pattern', 'hello-agency') ?></li>
                            <li> <?php echo __('Our Works section pattern', 'hello-agency') ?></li>
                            <li> <?php echo __('Pricing Table section pattern', 'hello-agency') ?></li>
                            <li> <?php echo __('Service section pattern', 'hello-agency') ?></li>
                            <li> <?php echo __('Stats Counter section pattern', 'hello-agency') ?></li>
                            <li> <?php echo __('Team section pattern - 2', 'hello-agency') ?></li>
                            <li> <?php echo __('Testimonial section pattern', 'hello-agency') ?></li>
                            <li> <?php echo __('Featured Products section pattern', 'hello-agency') ?></li>
                        </ul>
                    </li>

                    <li> <strong>- <?php echo __('15+ FSE Templates Ready', 'hello-agency') ?></strong>
                        <ul>
                            <li> <?php echo __('404 Template', 'hello-agency') ?></li>
                            <li> <?php echo __('Archive Template', 'hello-agency') ?></li>
                            <li> <?php echo __('Blank Template', 'hello-agency') ?></li>
                            <li> <?php echo __('Front Page Template', 'hello-agency') ?></li>
                            <li> <?php echo __('Blog Home Template', 'hello-agency') ?></li>
                            <li> <?php echo __('Index Page Template', 'hello-agency') ?></li>
                            <li> <?php echo __('Search Template', 'hello-agency') ?></li>
                            <li> <?php echo __('Sitemap Template', 'hello-agency') ?></li>
                            <li> <?php echo __('Page Template', 'hello-agency') ?></li>
                            <li> <?php echo __('Left Sidebar Page Template', 'hello-agency') ?></li>
                            <li> <?php echo __('Right sidebar page  Template', 'hello-agency') ?></li>
                            <li> <?php echo __('Single Template', 'hello-agency') ?></li>
                            <li> <?php echo __('Left Sidebar Single Template', 'hello-agency') ?></li>
                            <li> <?php echo __('Right Sidebar Single Template', 'hello-agency') ?></li>
                            <li> <?php echo __('Product Archive Template', 'hello-agency') ?></li>
                            <li> <?php echo __('Single Product Template', 'hello-agency') ?></li>

                        </ul>
                    <li>
                    <li><strong> - <?php echo __('Header Layout', 'hello-agency') ?></strong></li>
                    <li> <strong>- <?php echo __('Footer Layout', 'hello-agency') ?></strong></li>
                    <li><strong> - <?php echo __('12+ Beautiful Fonts Option', 'hello-agency') ?></strong></li>
                    <li> <strong>- <?php echo __('8 Styles Variations', 'hello-agency') ?></strong></li>
                </ul>
            </div>
            <div class="half-col pro-features">
                <h3><?php echo __('Premium Features', 'hello-agency') ?></h3>
                <ul>
                    <li><?php echo __('Including all free features and comes with more 40+ Premium patterns (total 60+ patterns)', 'hello-agency') ?></strong></li>
                    <li><?php echo __('Header Layout - 8', 'hello-agency') ?></li>
                    <li><?php echo __('Footer Layout- 4', 'hello-agency') ?></li>
                    <li><?php echo __('Banner Layout', 'hello-agency') ?></li>
                    <li><?php echo __('Logo Branding Section ', 'hello-agency') ?></li>
                    <li><?php echo __('Featured Product Layout', 'hello-agency') ?></li>
                    <li><?php echo __('About Us Layout - 4 ', 'hello-agency') ?></li>
                    <li><?php echo __('Testimonials Layout - 2', 'hello-agency') ?></li>
                    <li><?php echo __('Teams Layout-2', 'hello-agency') ?></li>
                    <li><?php echo __('FAQ Layout Patterns', 'hello-agency') ?></li>
                    <li><?php echo __('Featured Services Layout- 4', 'hello-agency') ?></li>
                    <li><?php echo __('Price List Section', 'hello-agency') ?></li>
                    <li><?php echo __('Pricing Table Layout', 'hello-agency') ?></li>
                    <li><?php echo __('About Layout Patterns', 'hello-agency') ?></li>
                    <li><?php echo __('Features List Layout', 'hello-agency') ?></li>
                    <li><?php echo __('Call to Action Layout', 'hello-agency') ?></li>
                    <li><?php echo __('How it Works Layout', 'hello-agency') ?></li>
                    <li><?php echo __('Portfolio Layout', 'hello-agency') ?></li>
                    <li><?php echo __('Latest Projects Layout', 'hello-agency') ?></li>
                    <li><?php echo __('Our Story and Mission Section', 'hello-agency') ?></li>
                    <li><?php echo __('Latest Post Section - 2', 'hello-agency') ?></li>
                    <li><?php echo __('Stas Counter with Description Text', 'hello-agency') ?></li>
                    <li><?php echo __('Products Layout Sections - 4', 'hello-agency') ?></li>
                </ul>
                <a href="https://cozythemes.com/pricing-and-plans/" class="upgrade-btn button" target="_blank"><?php echo __('Upgrade to Pro', 'hello-agency') ?></a>
            </div>
        </div>
    </div>
<?php
}
