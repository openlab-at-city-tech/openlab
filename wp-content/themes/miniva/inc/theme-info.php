<?php
/**
 * A theme info page in Appearance section.
 *
 * @package Miniva
 */

/**
 * Add Theme Info page to admin menu
 */
function miniva_theme_info_menu() {
	add_theme_page(
		esc_html__( 'Theme Info', 'miniva' ),
		esc_html__( 'Theme Info', 'miniva' ),
		'edit_theme_options',
		'theme-info',
		'miniva_theme_info'
	);
}
add_action( 'admin_menu', 'miniva_theme_info_menu' );

/**
 * Display Theme Info page
 */
function miniva_theme_info() {
	// Get theme details.
	$theme = wp_get_theme( 'miniva' );

	$theme_name    = $theme->display( 'Name' );
	$theme_desc    = $theme->display( 'Description' );
	$theme_version = $theme->display( 'Version' );

	$links['doc'] = array(
		'title' => __( 'Documentation', 'miniva' ),
		'desc'  => __( 'If you need help in installation or configuration of the theme, please read the theme documentation on our website.', 'miniva' ),
		'url'   => __( 'https://tajam.id/miniva/documentation/', 'miniva' ),
		'label' => __( 'Read Documentation', 'miniva' ),
	);

	$links['forum'] = array(
		'title' => __( 'Support Forum', 'miniva' ),
		'desc'  => __( 'Have any issues or questions that are beyond the scope of the documentation? Post your question in the support forum.', 'miniva' ),
		'url'   => __( 'https://wordpress.org/support/theme/miniva/', 'miniva' ),
		'label' => __( 'Go to Support Forum', 'miniva' ),
	);

	$links['pro'] = array(
		'title' => __( 'Miniva Pro', 'miniva' ),
		'desc'  => __( 'Get Miniva Pro Add-on for additional features including WooCommerce support, dark color scheme, featured slider, and many more...', 'miniva' ),
		'url'   => __( 'https://tajam.id/miniva-pro/', 'miniva' ),
		'label' => __( 'Learn more about Miniva Pro', 'miniva' ),
	);

	$links = apply_filters( 'miniva_links', $links );
	?>

	<div class="wrap theme-info">

		<h1>
			<?php
			/* translators: 1: theme name, 2: theme version. */
			printf( esc_html__( 'Welcome to %1$s %2$s', 'miniva' ), esc_html( $theme_name ), esc_html( $theme_version ) );
			?>
		</h1>

		<div class="row">

			<div class="col">
				<div class="theme-description">
					<?php
					echo wp_kses(
						make_clickable( $theme_desc ),
						array(
							'a' => array(
								'href' => array(),
							),
						)
					);
					?>
				</div>

				<div class="section">
					<h2><?php esc_html_e( 'Customize Theme', 'miniva' ); ?></h2>

					<p class="desc">
						<?php
						/* translators: %s: theme name. */
						printf( esc_html__( '%s uses the WordPress Customizer for all theme settings. Click on "Customize" to open the Customizer.', 'miniva' ), esc_html( $theme_name ) );
						?>
					</p>
					<p>
						<a href="<?php echo esc_url( wp_customize_url() ); ?>" class="button button-primary"><?php esc_html_e( 'Customize', 'miniva' ); ?></a>
					</p>
				</div>
			</div>

			<div class="col">
				<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/screenshot.png">
			</div>

		</div>

		<hr>

		<div class="row theme-links">
			<?php foreach ( $links as $link ) : ?>
				<div class="col">
					<h2><?php echo esc_html( $link['title'] ); ?></h2>

					<p class="desc">
						<?php echo esc_html( $link['desc'] ); ?>
					</p>
					<p>
						<a href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" class="button button-secondary">
							<?php echo esc_html( $link['label'] ); ?>
						</a>
					</p>
				</div>
			<?php endforeach; ?>
		</div>

	</div>

	<?php
}

/**
 * Enqueues CSS for Theme Info page
 *
 * @param int $hook Hook suffix for the current admin page.
 */
function miniva_theme_info_css( $hook ) {

	// Load styles and scripts only on theme info page.
	if ( 'appearance_page_theme-info' !== $hook ) {
		return;
	}

	// Embed theme info css style.
	wp_enqueue_style( 'miniva-theme-info-css', get_template_directory_uri() . '/css/theme-info.css', array(), MINIVA_VERSION );

}
add_action( 'admin_enqueue_scripts', 'miniva_theme_info_css' );

/**
 * Add admin notice after theme activation
 */
function miniva_activation_admin_notice() {
	global $pagenow;
	if ( is_admin() && 'themes.php' === $pagenow ) {
		if ( function_exists( 'filter_input' ) && filter_input( INPUT_GET, 'activated' ) ) {
			add_action( 'admin_notices', 'miniva_admin_notice' );
		}
	}
}
add_action( 'load-themes.php', 'miniva_activation_admin_notice' );

/**
 * Display admin notice
 */
function miniva_admin_notice() {
	// Get theme details.
	$theme = wp_get_theme();
	?>
	<div class="updated notice is-dismissible">
		<p>
			<?php
			/* translators: 1: theme name. */
			printf( esc_html__( 'Thanks for choosing %1$s. To get started with %1$s please visit the theme info page.', 'miniva' ), esc_html( $theme->display( 'Name' ) ) );
			?>
		</p>
		<p>
			<a class="button" href="<?php echo esc_url( admin_url( 'themes.php?page=theme-info' ) ); ?>">
				<?php
				/* translators: %s: theme name. */
				printf( esc_html__( 'Get Started with %s', 'miniva' ), esc_html( $theme->display( 'Name' ) ) );
				?>
			</a>
		</p>
	</div>
	<?php
}
