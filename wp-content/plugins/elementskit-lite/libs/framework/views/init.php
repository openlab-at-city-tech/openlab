<?php

$settings_sections = array(
	'dashboard' => array(
		'title'     => esc_html__( 'Dashboard', 'elementskit-lite' ),
		'sub-title' => esc_html__( 'General info', 'elementskit-lite' ),
		'icon'      => 'icon icon-home',
		// 'view_path' => 'some path to the view file'
	),
	'widgets' => array(
		'title'     => esc_html__( 'Widgets', 'elementskit-lite' ),
		'sub-title' => esc_html__( 'Enable disable widgets', 'elementskit-lite' ),
		'icon'      => 'icon icon-magic-wand',
	),
	'modules' => array(
		'title'     => esc_html__( 'Modules', 'elementskit-lite' ),
		'sub-title' => esc_html__( 'Enable disable modules', 'elementskit-lite' ),
		'icon'      => 'icon icon-settings-2',
	),
	'usersettings' => array(
		'title'     => esc_html__( 'User Settings', 'elementskit-lite' ),
		'sub-title' => esc_html__( 'Settings for fb, mailchimp etc', 'elementskit-lite' ),
		'icon'      => 'icon icon-settings1',
	),
);

$settings_sections = apply_filters( 'elementskit/admin/settings_sections/list', $settings_sections );


$onboard_steps = array(
	'step-01' => array(
		'title'     => esc_html__( 'Configuration', 'elementskit-lite' ),
		'sub-title' => esc_html__( 'Configuration info', 'elementskit-lite' ),
		'icon'      => 'icon icon-ekit',
		// 'view_path' => 'some path to the view file'
	),
	'step-02' => array(
		'title'     => esc_html__( 'Sign Up', 'elementskit-lite' ),
		'sub-title' => esc_html__( 'Sign Up info', 'elementskit-lite' ),
		'icon'      => 'icon icon-user',
	),
	'step-03' => array(
		'title'     => esc_html__( 'Website Powerup', 'elementskit-lite' ),
		'sub-title' => esc_html__( 'Website Powerup info', 'elementskit-lite' ),
		'icon'      => 'icon icon-cog',
	),
	'step-04' => array(
		'title'     => esc_html__( 'Tutorial', 'elementskit-lite' ),
		'sub-title' => esc_html__( 'Tutorial info', 'elementskit-lite' ),
		'icon'      => 'icon icon-youtube-1',
	),
	'step-05' => array(
		'title'     => esc_html__( 'Surprise', 'elementskit-lite' ),
		'sub-title' => esc_html__( 'Surprise info', 'elementskit-lite' ),
		'icon'      => 'icon icon-gift1',
	),
	'step-06' => array(
		'title'     => esc_html__( 'Finalizing', 'elementskit-lite' ),
		'sub-title' => esc_html__( 'Finalizing info', 'elementskit-lite' ),
		'icon'      => 'icon icon-smile',
	),
);

$installed_date = strtotime( get_option( 'elementskit-lite_install_date' ) );
if ( ( 3600 * 24 ) < ( time() - $installed_date ) ) {
	unset( $onboard_steps['step-01'] );
}

if ( \ElementsKit_Lite::package_type() != 'free' ) {
	unset( $onboard_steps['step-05'] );
}

$onboard_steps = apply_filters( 'elementskit/admin/onboard_steps/list', $onboard_steps );

/**
 * We are checking the dashboard page on the Elementskit main page or onboard page and then we decide which template to show.
 * We are not saving any request data, just using it for checking.
 */


 // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Checking current page type. The page only can access admin. So nonce verification is not required.
 $step_css_class = isset($_GET['ekit-onboard-steps']) && $_GET['ekit-onboard-steps'] == 'loaded' ? 'ekit-onboard-dashboard' : '';
?>
<div class="ekit-wid-con <?php echo esc_attr( $step_css_class ) ?>">
    <div class="ekit_container">
        <form action="" method="POST" id="ekit-admin-settings-form">
            <?php 
                if( !empty( $step_css_class ) ) {
                    include 'layout-onboard.php'; 
                } else {
                    do_action('elementskit/admin/settings_sections/before');
                    include 'layout-settings.php';
                }
            ?>
        </form>
    </div>
</div>
