<?php

/**
 * Settings - Performance
 * 
 * @package Dashboard
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

?>

<div class="sydney-dashboard-card">
    <div class="sydney-dashboard-card-body">
        <div class="sydney-dashboard-module-card">
            <div class="sydney-dashboard-module-card-header bt-align-items-center">
                <div class="sydney-dashboard-module-card-header-info">
                    <h2 class="bt-m-0 bt-mb-10px"><?php echo esc_html__( 'Load Google Fonts Locally', 'sydney' ); ?></h2>
                    <p class="bt-text-color-grey"><?php esc_html_e('Activate this option to load the Google fonts locally.', 'sydney'); ?></p>
                </div>
                <div class="sydney-dashboard-module-card-header-actions bt-pt-0">
                    <div class="sydney-dashboard-box-link">
                        <?php if (Sydney_Modules::is_module_active('local-google-fonts')) : ?>
                            <a href="#" class="sydney-dashboard-link sydney-dashboard-link-danger sydney-dashboard-module-activation" data-module-id="local-google-fonts" data-module-activate="false">
                                <?php echo esc_html__( 'Deactivate', 'sydney' ); ?>
                            </a>
                        <?php else : ?>
                            <a href="#" class="sydney-dashboard-link sydney-dashboard-link-success sydney-dashboard-module-activation" data-module-id="local-google-fonts" data-module-activate="true">
                                <?php echo esc_html__( 'Activate', 'sydney' ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>