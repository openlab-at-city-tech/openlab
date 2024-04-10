<?php

/**
 * Tabs Nav Items
 * 
 * @package Dashboard
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

?>

<div class="sydney-dashboard-row">
    <div class="sydney-dashboard-column">
        <div class="sydney-dashboard-card sydney-dashboard-card-top-spacing sydney-dashboard-card-tabs-divider">
            <div class="sydney-dashboard-card-body">
                
                <?php if ( in_array( $this->get_plugin_status( $this->settings['starter_plugin_path'] ), array( 'inactive', 'not_installed' ) ) ) : ?>

                <div class="sydney-dashboard-row">

                    <div class="sydney-dashboard-starter-sites bt-d-block">
                        <div class="sydney-dashboard-starter-sites-locked">
                            <div class="sydney-dashboard-starter-sites-notice">
                                <div class="sydney-dashboard-starter-sites-notice-text"><?php esc_html_e('In order to be able to import any starter sites for Sydney you need to have the aThemes demo importer plugin active.', 'sydney'); ?></div>
                                <?php if ('not_installed' === $this->get_plugin_status($this->settings['starter_plugin_path'])) : ?>
                                    <a href="<?php echo esc_url(add_query_arg(array('page' => $this->settings['menu_slug'], 'section' => 'starter-sites'), admin_url('themes.php'))); ?>" class="button button-primary sydney-dashboard-plugin-ajax-button sydney-ajax-success-redirect" data-type="install" data-path="<?php echo esc_attr($this->settings['starter_plugin_path']); ?>" data-slug="<?php echo esc_attr($this->settings['starter_plugin_slug']); ?>"><?php esc_html_e('Install and Activate', 'sydney'); ?></a>
                                <?php else : ?>
                                    <a href="<?php echo esc_url(add_query_arg(array('page' => $this->settings['menu_slug'], 'section' => 'starter-sites'), admin_url('themes.php'))); ?>" class="button button-primary sydney-dashboard-plugin-ajax-button sydney-ajax-success-redirect" data-type="activate" data-path="<?php echo esc_attr($this->settings['starter_plugin_path']); ?>" data-slug="<?php echo esc_attr($this->settings['starter_plugin_slug']); ?>"><?php esc_html_e('Activate', 'sydney'); ?></a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <figure>
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/inc/dashboard/assets/images/startersbg.jpg'); ?>" />
                        </figure>
                    </div>
                </div>

                <?php else : ?>

                <div class="sydney-dashboard-row">
                    <?php
                    if (has_action('atss_starter_sites')) {
                        do_action('atss_starter_sites'); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound
                    } else {
                       ?>
                       <a href="<?php echo esc_url(add_query_arg(array('page' => 'starter-sites'), admin_url('themes.php'))); ?>" class="button button-primary"><?php esc_html_e('Go to Starter Sites', 'sydney'); ?></a>
                        <?php
                    }
                    ?>
                </div>

                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
