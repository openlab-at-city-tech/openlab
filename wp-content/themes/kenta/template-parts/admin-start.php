<?php
/**
 * Template part for get start notice
 *
 * @package Kenta
 */

global $current_user;
$user_id = $current_user->ID;

if ( get_user_meta( $user_id, 'kenta_dismissed_start' ) ) {
	return;
}

$dismiss_url = add_query_arg(
	array(
		'kenta_dismiss' => 'start',
		'_wpnonce'      => wp_create_nonce( 'kenta_dismiss' )
	), admin_url()
);

$demo_name       = apply_filters( 'kenta_welcome_demo_name', 'Kenta' );
$demo_screenshot = apply_filters( 'kenta_welcome_demo_screenshot', get_template_directory_uri() . '/screenshot.png' );
$demo_slug       = apply_filters( 'kenta_welcome_demo_slug', 'kenta-agency' );
$demo_preview    = 'https://kentatheme.com/' . $demo_slug;
?>

<div
        data-dismiss-url="<?php echo esc_url( $dismiss_url ) ?>"
        class="kenta-theme-notice is-dismissible"
        id="kenta-welcome-panel"
>

    <div class="panel-header">
        <div class="panel-header-content">
            <img class="kenta-logo" src="<?php echo esc_url( kenta_image_url( 'kenta-logo-light.png' ) ) ?>"
                 alt="Kenta Logo">
            <div>
                <h2><?php esc_html_e( 'Congratulations!', 'kenta' ); ?></h2>
                <p>
					<?php esc_html_e( 'Kenta is now installed and ready to use. We have some links to help you get started.', 'kenta' ); ?>
                </p>
            </div>
        </div>

		<?php if ( $demo_screenshot !== '' ): ?>
            <a class="kenta-demo-screenshot" href="<?php echo esc_url( $demo_preview ) ?>" target="_blank">
                <img src="<?php echo esc_url( $demo_screenshot ) ?>" alt="<?php echo esc_html( $demo_name ) ?>"/>

                <span><?php esc_html_e( 'Demo Preview', 'kenta' ) ?> ➞</span>
            </a>
		<?php endif; ?>
    </div>
    <div class="panel-body">

        <a href="#" class="kenta-notice-dismiss text-sm">
			<?php esc_html_e( "Dismiss", 'kenta' ); ?>
        </a>

        <div class="panel-column">
            <span class="panel-column-icon">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
					<path d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z"/>
				</svg>
            </span>
            <div class="panel-column-content">
                <h3><?php esc_html_e( 'Starter Sites', 'kenta' ); ?></h3>

                <p>
					<?php esc_html_e( 'Kenta comes with many starter sites with various designs to choose from.', 'kenta' ); ?>
					<?php
					if ( $demo_slug !== '' ) {
						echo '<b>';
						esc_html_e( 'Would you like to import pre-designed demo like the screenshot?', 'kenta' );
						echo '</b>';
					}
					?>
                </p>

                <div class="panel-column-actions">
					<?php if ( $demo_slug !== '' ): ?>
                        <button data-redirect="<?php echo esc_url( kenta_install_cmp_redirect_url( '#importing/' . $demo_slug ) ) ?>"
                                type="button" class="kenta-button kenta-button-solid kenta-install-cmp-action"
                        >
							<?php esc_html_e( 'Import Demo Now', 'kenta' ); ?>
                        </button>

                        <button data-redirect="<?php echo esc_url( kenta_install_cmp_redirect_url() ) ?>"
                                type="button"
                                class="kenta-button kenta-button-outline kenta-install-cmp-action">
							<?php esc_html_e( 'Visit More Demos', 'kenta' ); ?>
                        </button>
					<?php else: ?>
                        <button data-redirect="<?php echo esc_url( kenta_install_cmp_redirect_url() ) ?>"
                                type="button"
                                class="kenta-button kenta-button-solid kenta-install-cmp-action">
							<?php esc_html_e( 'Visit Starter Sites', 'kenta' ); ?>
                        </button>
					<?php endif; ?>
                </div>
                <p class="text-sm"><?php esc_html_e( 'Kenta Companion & Kenta Blocks plugin will be installed', 'kenta' ); ?></p>
            </div>
        </div>
        <div class="panel-column">
            <span class="panel-column-icon">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
					<path d="M96 0C43 0 0 43 0 96V416c0 53 43 96 96 96H384h32c17.7 0 32-14.3 32-32s-14.3-32-32-32V384c17.7 0 32-14.3 32-32V32c0-17.7-14.3-32-32-32H384 96zm0 384H352v64H96c-17.7 0-32-14.3-32-32s14.3-32 32-32zm32-240c0-8.8 7.2-16 16-16H336c8.8 0 16 7.2 16 16s-7.2 16-16 16H144c-8.8 0-16-7.2-16-16zm16 48H336c8.8 0 16 7.2 16 16s-7.2 16-16 16H144c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/>
				</svg>
            </span>
            <div class="panel-column-content">
                <h3><?php esc_html_e( 'Documentation', 'kenta' ); ?></h3>

                <p>
					<?php esc_html_e( 'Want more details, take a look at our documentation, which will teach you how to use Kenta.', 'kenta' ); ?>
                </p>

                <div>
                    <a href="https://kentatheme.com/docs/"
                       target="_blank"><?php esc_html_e( 'Read Documentation', 'kenta' ); ?> ➞</a>
                </div>
            </div>
        </div>
        <div class="panel-column">
            <span class="panel-column-icon">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
					<path d="M144 160c-44.2 0-80-35.8-80-80S99.8 0 144 0s80 35.8 80 80s-35.8 80-80 80zm368 0c-44.2 0-80-35.8-80-80s35.8-80 80-80s80 35.8 80 80s-35.8 80-80 80zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM416 224c0 53-43 96-96 96s-96-43-96-96s43-96 96-96s96 43 96 96zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z"/>
				</svg>
            </span>
            <div class="panel-column-content">
                <h3><?php esc_html_e( 'Support Forum', 'kenta' ); ?></h3>

                <p>
					<?php esc_html_e( 'If you have any question about using this theme, feel free to create a new topic in the support forum.', 'kenta' ); ?>
                </p>

                <div>
                    <a href="https://wordpress.org/support/theme/kenta/"
                       target="_blank"><?php esc_html_e( 'Create a Topic', 'kenta' ); ?> ➞</a>
                </div>
            </div>
        </div>
    </div>
</div>
