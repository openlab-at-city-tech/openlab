<?php

/**
 * Settings - General
 * 
 * @package Dashboard
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

?>

<div class="sydney-dashboard-card sydney-dashboard-card-no-box-shadow">
    <div class="sydney-dashboard-card-body sydney-dashboard-card-body-content-with-dividers">
       
        <?php if ( defined( 'SYDNEY_PRO_VERSION' ) ) : ?>

            <div class="sydney-dashboard-license-wrapper">
                <h2 class="bt-mb-10px"><?php echo esc_html__( 'Sydney Pro License', 'sydney' ); ?></h2>
                <p class="bt-text-color-grey bt-mb-20px"><?php echo esc_html__( 'Activate your license key for Sydney Pro to get latest theme updates automatically updates right from your WordPress Dashboard.', 'sydney' ); ?> </p>
                <?php do_action( 'sydney_pro_license_form' ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound ?>
                <div class="sydney-dashboard-content-expand bt-mt-20px" data-bt-toggle-expand style="max-width: 700px;">
                    <div class="sydney-dashboard-content-expand-title">
                        <a href="#" class="sydney-dashboard-content-expand-link">
                            <?php echo sydney_dashboard_get_setting_icon( 'info' ); ?>
                            <?php echo esc_html__( 'Instructions', 'sydney' ); ?>
                        </a>
                    </div>
                    <div class="sydney-dashboard-content-expand-content bt-toggle-expand-content">
                        <ul class="sydney-dashboard-content-expand-list">
                            <li>
                                <?php echo sydney_dashboard_get_setting_icon( 'arrow' ); ?>
                                <?php echo sprintf(
                                    esc_html__( 'To get your key, please login to your %1$saThemes account%2$s.', 'sydney' ),
                                    '<a href="https://athemes.com/your-account/" target="_blank">',
                                    '</a>'
                                ); ?>
                            </li>
                            <li>
                                <?php echo sydney_dashboard_get_setting_icon( 'arrow' ); ?>
                                <?php echo esc_html__( 'In the licenses section, click on the 🔑 key icon. A license key will appear. Copy and paste the key here', 'sydney' ); ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        <?php else : ?>

            <div class="sydney-dashboard-module-card">
                <div class="sydney-dashboard-module-card-header">
                    <div class="sydney-dashboard-module-card-header-info">
                        <h2 class="bt-mb-10px"><?php echo esc_html__( 'Get Sydney Pro', 'sydney' ); ?></h2>
                        <p class="bt-text-color-grey"><?php echo esc_html__( 'Take Sydney to a whole other level by upgrading to the premium version.', 'sydney' ); ?></p>
                    </div>
                    <div class="sydney-dashboard-module-card-header-actions">
                        <a href="<?php echo esc_url( $this->settings['upgrade_pro'] ); ?>" class="sydney-dashboard-external-link" target="_blank">
                            <?php echo esc_html__( 'Upgrade Now', 'sydney' ); ?>
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.4375 0H8.25C7.94531 0 7.66406 0.1875 7.54688 0.492188C7.42969 0.773438 7.5 1.10156 7.71094 1.3125L8.67188 2.27344L4.14844 6.79688C3.84375 7.07812 3.84375 7.57031 4.14844 7.85156C4.28906 7.99219 4.47656 8.0625 4.6875 8.0625C4.875 8.0625 5.0625 7.99219 5.20312 7.85156L9.72656 3.32812L10.6875 4.28906C10.8281 4.42969 11.0156 4.5 11.2266 4.5C11.3203 4.5 11.4141 4.5 11.5078 4.45312C11.8125 4.33594 12 4.05469 12 3.75V0.5625C12 0.257812 11.7422 0 11.4375 0ZM9.1875 7.5C8.85938 7.5 8.625 7.75781 8.625 8.0625V10.6875C8.625 10.8047 8.53125 10.875 8.4375 10.875H1.3125C1.19531 10.875 1.125 10.8047 1.125 10.6875V3.5625C1.125 3.46875 1.19531 3.375 1.3125 3.375H3.9375C4.24219 3.375 4.5 3.14062 4.5 2.8125C4.5 2.50781 4.24219 2.25 3.9375 2.25H1.3125C0.585938 2.25 0 2.85938 0 3.5625V10.6875C0 11.4141 0.585938 12 1.3125 12H8.4375C9.14062 12 9.75 11.4141 9.75 10.6875V8.0625C9.75 7.75781 9.49219 7.5 9.1875 7.5Z" fill="#2271b1"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

        <?php endif; ?>

    </div>
</div>