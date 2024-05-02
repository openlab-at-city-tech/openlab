<?php

/**
 * Tabs Nav Items
 * 
 * @package Dashboard
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if  ( empty( $this->settings['settings'] ) ) {
	return;
}

?>

<div class="sydney-dashboard-row">
    <div class="sydney-dashboard-column">
        <div class="sydney-dashboard-card sydney-dashboard-card-top-spacing sydney-dashboard-card-tabs-divider">
            <div class="sydney-dashboard-card-body">

                <div class="sydney-dashboard-row">
                    <div class="sydney-dashboard-column sydney-dashboard-column-2">

                        <nav class="sydney-dashboard-tabs-nav sydney-dashboard-tabs-nav-vertical sydney-dashboard-tabs-nav-with-icons sydney-dashboard-tabs-nav-no-negative-margin" data-tab-wrapper-id="settings-tab">
                            <ul>
                                <?php foreach ( $this->settings['settings'] as $tab_id => $tab_title ) : 
                                    $current_tab = (isset($_GET['current_tab'])) ? sanitize_text_field(wp_unslash($_GET['current_tab'])) : key(array_slice($this->settings['settings'], 0, 1)); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                                    $tab_active  = ( ($current_tab && $current_tab === $tab_id) || (!$current_tab && $tab_id === 'general' ) ) ? ' active' : '';

                                    ?>

                                    <li class="sydney-dashboard-tabs-nav-item<?php echo esc_attr( $tab_active ); ?>">
                                        <a href="#" class="sydney-dashboard-tabs-nav-link" data-tab-to="settings-tab-<?php echo esc_attr( $tab_id ); ?>">
                                            <?php echo sydney_dashboard_get_setting_icon( $tab_id ); ?>
                                            <?php echo esc_html( $tab_title ); ?>
                                        </a>
                                    </li>

                                <?php endforeach; ?>
                            </ul>
                        </nav>

                    </div>
                    <div class="sydney-dashboard-column sydney-dashboard-column-10">

                        <?php 
						$current_tab = ( isset( $_GET['current_tab'] ) ) ? sanitize_text_field( wp_unslash( $_GET['current_tab'] ) ) : '';

						foreach( $this->settings[ 'settings' ] as $tab_id => $tab_title ) : 
							$tab_active = ( ($current_tab && $current_tab === $tab_id) || (!$current_tab && $tab_id === 'general') ) ? ' active' : '';

							?>	
                            <div class="sydney-dashboard-tab-content-wrapper" data-tab-wrapper-id="settings-tab">					
                                <div class="sydney-dashboard-tab-content<?php echo esc_attr( $tab_active ); ?>" data-tab-content-id="settings-tab-<?php echo esc_attr( $tab_id ); ?>">
                                    <?php require get_template_directory() . '/inc/dashboard/html-settings-'. $tab_id .'.php'; ?>
                                </div>
                            </div>
						<?php endforeach; ?>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
