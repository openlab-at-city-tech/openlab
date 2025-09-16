<?php
/**
 * Welcome class.
 *
 * @since 1.8.1
 *
 * @package SoliloquyWP
 * @author  SoliloquyWP Team
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Welcome Class
 *
 * @since 1.7.0
 *
 * @package SoliloquyWP
 * @author  SoliloquyWP Team <help@soliloquywp.com>
 */
class Soliloquy_Welcome {

	/**
	 * Welcome pages
	 *
	 * @var array
	 */
	public $pages = [
		'soliloquy-lite-get-started',
		'soliloquy-lite-about-us',
		'soliloquy-lite-litevspro',
	];

	/**
	 * Holds the submenu pagehook.
	 *
	 * @since 1.7.0
	 *
	 * @var string`
	 */
	public $hook = '';

	/**
	 * Helps installed plugins array
	 *
	 * @var array
	 */
	public $installed_plugins = [];

	/**
	 * Primary class constructor.
	 *
	 * @since 1.8.1
	 */
	public function __construct() {

		if ( ( defined( 'SOLILOQUY_WELCOME_SCREEN' ) && false === SOLILOQUY_WELCOME_SCREEN ) || apply_filters( 'soliloquy_whitelabel', false ) === true ) {
			return;
		}

		// Add custom addons submenu.
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 15 );

		// Add scripts and styles.
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );

		// Misc.
		add_action( 'admin_print_scripts', [ $this, 'disable_admin_notices' ] );
	}

	/**
	 * Register and enqueue addons page specific JS.
	 *
	 * @since 1.5.0
	 */
	public function enqueue_admin_scripts() {
		if ( isset( $_GET['post_type'] ) && isset( $_GET['page'] ) && 'soliloquy' === wp_unslash( $_GET['post_type'] ) && in_array( wp_unslash( $_GET['page'] ), $this->pages ) ) { // @codingStandardsIgnoreLine

			wp_register_script( SOLILOQUY_SLUG . '-welcome-script', plugins_url( 'assets/js/welcome.js', SOLILOQUY_FILE ), [ 'jquery' ], SOLILOQUY_VERSION, true );
			wp_enqueue_script( SOLILOQUY_SLUG . '-welcome-script' );
			wp_localize_script(
				SOLILOQUY_SLUG . '-welcome-script',
				'soliloquy_welcome',
				[
					'activate_nonce'   => wp_create_nonce( 'soliloquy-activate-partner' ),
					'active'           => __( 'Status: Active', 'soliloquy' ),
					'activate'         => __( 'Activate', 'soliloquy' ),
					'get_addons_nonce' => wp_create_nonce( 'soliloquy-get-addons' ),
					'activating'       => __( 'Activating...', 'soliloquy' ),
					'ajax'             => admin_url( 'admin-ajax.php' ),
					'deactivate'       => __( 'Deactivate', 'soliloquy' ),
					'deactivate_nonce' => wp_create_nonce( 'soliloquy-deactivate-partner' ),
					'deactivating'     => __( 'Deactivating...', 'soliloquy' ),
					'inactive'         => __( 'Status: Inactive', 'soliloquy' ),
					'install'          => __( 'Install', 'soliloquy' ),
					'install_nonce'    => wp_create_nonce( 'soliloquy-install-partner' ),
					'installing'       => __( 'Installing...', 'soliloquy' ),
					'proceed'          => __( 'Proceed', 'soliloquy' ),
				]
			);
		}
	}

	/**
	 * Register and enqueue addons page specific CSS.
	 *
	 * @since 1.8.1
	 */
	public function enqueue_admin_styles() {

		if ( isset( $_GET['post_type'] ) && isset( $_GET['page'] ) && 'soliloquy' === wp_unslash( $_GET['post_type'] ) && in_array( wp_unslash( $_GET['page'] ), $this->pages ) ) { // @codingStandardsIgnoreLine

			wp_register_style( SOLILOQUY_SLUG . '-welcome-style', plugins_url( 'assets/css/welcome.css', SOLILOQUY_FILE ), [], SOLILOQUY_VERSION );
			wp_enqueue_style( SOLILOQUY_SLUG . '-welcome-style' );

		}

		// Run a hook to load in custom styles.
		do_action( 'soliloquy_addons_styles' );
	}

	/**
	 * Making page as clean as possible
	 *
	 * @since 1.8.1
	 */
	public function disable_admin_notices() {

		global $wp_filter;

		if ( isset( $_GET['post_type'] ) && isset( $_GET['page'] ) && 'soliloquy' === wp_unslash( $_GET['post_type'] ) && in_array( wp_unslash( $_GET['page'] ), $this->pages ) ) { // @codingStandardsIgnoreLine

			if ( isset( $wp_filter['user_admin_notices'] ) ) {
				unset( $wp_filter['user_admin_notices'] );
			}
			if ( isset( $wp_filter['admin_notices'] ) ) {
				unset( $wp_filter['admin_notices'] );
			}
			if ( isset( $wp_filter['all_admin_notices'] ) ) {
				unset( $wp_filter['all_admin_notices'] );
			}
		}
	}

	/**
	 * Register the Welcome submenu item for soliloquy.
	 *
	 * @since 1.8.1
	 */
	public function admin_menu() {

		global $submenu;

		$whitelabel = apply_filters( 'soliloquy_whitelabel', false ) ? '' : __( 'Soliloquy ', 'soliloquy' );

		add_submenu_page(
			'edit.php?post_type=soliloquy',
			$whitelabel . __( 'About US', 'soliloquy' ),
			'<span style="color:#FFA500"> ' . __( 'About Us', 'soliloquy' ) . '</span>',
			apply_filters( 'soliloquy_menu_cap', 'manage_options' ),
			SOLILOQUY_SLUG . '-about-us',
			[ $this, 'about_page' ]
		);

		// Register the submenus.
		add_submenu_page(
			'edit.php?post_type=soliloquy',
			$whitelabel . __( 'Get Started', 'soliloquy' ),
			'<span style="color:#FFA500"> ' . __( 'Get Started', 'soliloquy' ) . '</span>',
			apply_filters( 'soliloquy_menu_cap', 'manage_options' ),
			SOLILOQUY_SLUG . '-get-started',
			[ $this, 'help_page' ]
		);

		add_submenu_page(
			'edit.php?post_type=soliloquy',
			$whitelabel . __( 'Lite vs Pro', 'soliloquy' ),
			'<span style="color:#FFA500"> ' . __( 'Lite vs Pro', 'soliloquy' ) . '</span>',
			apply_filters( 'soliloquy_menu_cap', 'manage_options' ),
			SOLILOQUY_SLUG . '-litevspro',
			[ $this, 'lite_vs_pro_page' ]
		);

		unset( $submenu['edit.php?post_type=soliloquy'][14] );
		unset( $submenu['edit.php?post_type=soliloquy'][15] );
	}

	/**
	 * Output tab navigation
	 *
	 * @since 2.2.0
	 *
	 * @param string $tab Tab to highlight as active.
	 */
	public static function tab_navigation( $tab = 'whats_new' ) {
		?>

<ul class="soliloquy-nav-tab-wrapper">
			<li>
			<a class="soliloquy-nav-tab
			<?php
			if ( isset( $_GET['page'] ) && 'soliloquy-lite-about-us' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				?>
				soliloquy-nav-tab-active<?php endif; ?>" href="
				<?php
				echo esc_url(
					admin_url(
						add_query_arg(
							[
								'post_type' => 'soliloquy',
								'page'      => 'soliloquy-lite-about-us',
							],
							'edit.php'
						)
					)
				);
				?>
														">
				<?php esc_html_e( 'About Us', 'soliloquy' ); ?>
			</a>
			</li>
			<li>
			<a class="soliloquy-nav-tab
			<?php
			if ( isset( $_GET['page'] ) && 'soliloquy-lite-get-started' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				?>
				soliloquy-nav-tab-active<?php endif; ?>" href="
				<?php
				echo esc_url(
					admin_url(
						add_query_arg(
							[
								'post_type' => 'soliloquy',
								'page'      => 'soliloquy-lite-get-started',
							],
							'edit.php'
						)
					)
				);
				?>
														">
				<?php esc_html_e( 'Getting Started', 'soliloquy' ); ?>
			</a>
			</li>
			<li>
			<a class="soliloquy-nav-tab
			<?php
			if ( isset( $_GET['page'] ) && 'soliloquy-lite-litevspro' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				?>
				soliloquy-nav-tab-active<?php endif; ?>" href="
				<?php
				echo esc_url(
					admin_url(
						add_query_arg(
							[
								'post_type' => 'soliloquy',
								'page'      => 'soliloquy-lite-litevspro',
							],
							'edit.php'
						)
					)
				);
				?>
														">
				<?php esc_html_e( 'Lite vs Pro', 'soliloquy' ); ?>
			</a>
			</li>

		</ul>

		<?php
	}
	/**
	 * Output the about screen.
	 *
	 * @since 1.8.5
	 */
	public function about_page() {

		self::tab_navigation( __METHOD__ );
		?>
		<div class="soliloquy-welcome-wrap soliloquy-about">
			<div class="soliloquy-panel soliloquy-lite-about-panel">
				<div class="content">
					<h3><?php esc_html_e( 'Hello and welcome to Soliloquy, the most beginner-friendly WordPress Slider Plugin. At Soliloquy, we build software that helps you create beautiful image sliders in minutes.', 'soliloquy' ); ?></h3>
					<p><?php esc_html_e( 'Over the years, we found that most WordPress Slider plugins were bloated, buggy, slow, and very hard to use. So, we started with a simple goal: build a responsive WordPress slider plugin that’s both easy and powerful', 'soliloquy' ); ?></p>
					<p><?php esc_html_e( 'Our goal is to provide the easiest way to create beautiful video and image sliders.', 'soliloquy' ); ?></p>
					<p><?php esc_html_e( 'Soliloquy is brought to you by the same team that’s behind the largest WordPress resource site, WPBeginner, the most popular lead-generation software, OptinMonster, the best WordPress analytics plugin, MonsterInsights, and more!', 'soliloquy' ); ?></p>
					<p><?php esc_html_e( 'Yup, we know a thing or two about building awesome products that customers love.', 'soliloquy' ); ?></p>
				</div>
				<div class="image">
					<img src="<?php echo esc_url( trailingslashit( SOLILOQUY_URL ) . 'assets/images/about/team.jpg' ); ?> ">
				</div>
			</div>

			<div class="soliloquy-am-plugins-wrap">
				<?php
				foreach ( $this->get_am_plugins() as $partner ) :

					$this->get_plugin_card( $partner );

				endforeach;
				?>
			</div>

		</div> <!-- wrap -->

		<?php
	}

	/**
	 * Output the about screen.
	 *
	 * @since 1.8.5
	 */
	public function welcome_page() {
		?>
		<?php self::tab_navigation( __METHOD__ ); ?>
<div class="soliloquy-welcome-wrap soliloquy-welcome">

	<div class="soliloquy-welcome-main">

		<div class="soliloquy-welcome-panel">
			<div class="wraps upgrade-wrap">
				<h2 class="headline-title">
					<?php esc_html_e( 'Welcome to Soliloquy Lite', 'soliloquy' ); ?>
				</h2>

				<h4 class="headline-subtitle">
					<?php esc_html_e( 'The Best Responsive WordPress Slider Plugin… Without the High Costs..', 'soliloquy' ); ?>
					</h2>
			</div>
			<div class="wraps about-wsrap">

				<div class="soliloquy-panel soliloquy-lite-updates-panel">
					<h3 class="title"><?php esc_html_e( 'Recent Updates To Soliloquy Lite:', 'soliloquy' ); ?></h3>

					<div class="soliloquy-recent soliloquy three-column">
						<div class="soliloquy column">
							<h4 class="title"><?php esc_html_e( 'Bug Fixes', 'soliloquy' ); ?> <span
									class="badge updated">UPDATED</span></h4>
							<?php /* translators: %1$s: link */ ?>
							<p><?php printf( esc_html__( 'Bugs improving PHP 8+ support.' ) ); ?>
							</p>
						</div>
						<div class="soliloquy column">
							<h4 class="title"><?php esc_html_e( 'Gutenberg Block', 'soliloquy' ); ?></h4>
							<?php /* translators: %1$s: link */ ?>
							<p><?php printf( esc_html__( 'Improved support for the Soliloquy Lite Gutenberg block. Bugs' ) ); ?>
							</p>
						</div>

						<div class="soliloquy column">
							<h4 class="title"><?php esc_html_e( 'Enhancements', 'soliloquy' ); ?></h4>
							<p><?php printf( esc_html__( 'UI tweaks and Improvements.', 'soliloquy' ) ); ?>
							</p>
						</div>
					</div>

				</div>

			</div>

		</div>

	</div>

</div> <!-- wrap -->

		<?php
	}

	/**
	 * Output the about screen.
	 *
	 * @since 1.8.1
	 */
	public function help_page() {
		?>
		<?php self::tab_navigation( __METHOD__ ); ?>

<div class="soliloquy-welcome-wrap soliloquy-help">

	<div class="soliloquy-get-started-main">

		<div class="soliloquy-get-started-section">

			<div class="soliloquy-admin-upgrade-panel soliloquy-panel">

				<div class="section-text-column text-left">

					<h2>Upgrade to a complete Soliloquy experience</h2>

					<p>Get the most out of Soliloquy by <a target="_blank"
							href="<?php echo esc_url( Soliloquy_Common_Admin_Lite::get_instance()->get_upgrade_link( false, 'gettingstartedtab', 'upgradetounlockallitspowerfulfeatures' ) ); ?>">upgrading
							to unlock all of its powerful features</a>.</p>

					<p>With Soliloquy Pro, you can unlock amazing features like:</p>

					<ul>
						<li>Get your slider set up in minutes with pre-built customizable templates </li>
						<li>Have more people find you on Google by making your sliders SEO friendly </li>
						<li>Native video slide support for YouTube, Vimeo and Wistia. Just add a video slide,
							enter in your URL, set a video thumbnail and off you go. </li>
						<li>A large selection of 10+ killer addons that can extend the base functionality of the
							slider.</li>

						</li>
					</ul>
					<a target="_blank" href="<?php echo esc_url( Soliloquy_Common_Admin_Lite::get_instance()->get_upgrade_link( false, 'gettingstartedtab', 'unlockpro' ) ); ?>" class="button soliloquy-button soliloquy-primary-button">Unlock Pro</a>
				</div>

				<div class="feature-photo-column">
					<img class="feature-photo"
						src="<?php echo esc_url( plugins_url( 'assets/images/soliloquy-admin.png', SOLILOQUY_FILE ) ); ?>" />
				</div>

			</div> <!-- panel -->

			<div class="soliloquy-admin-3-col soliloquy-help-section">
				<div class="soliloquy-cols">
					<svg xmlns="http://www.w3.org/2000/svg" width="50px" viewBox="0 0 512 512" fill="#454346">
						<path
							d="M432 0H48C21.6 0 0 21.6 0 48v416c0 26.4 21.6 48 48 48h384c26.4 0 48-21.6 48-48V48c0-26.4-21.6-48-48-48zm-16 448H64V64h352v384zM128 224h224v32H128zm0 64h224v32H128zm0 64h224v32H128zm0-192h224v32H128z">
						</path>
					</svg>
					<h3>Help and Documention</h3>
					<p>The Soliloquy Slider wiki has helpful documentation, tips, tricks, and code snippets to
						help you get started.</p>
					<a href="<?php echo esc_url( Soliloquy_Common_Admin_Lite::get_instance()->get_upgrade_link( 'https://soliloquywp.com/docs/', 'getstartedtab', 'docs' ) ); ?>"
						class="button soliloquy-button soliloquy-primary-button" target="_blank">Browse the docs</a>
				</div>
				<div class="soliloquy-cols">
					<svg xmlns="http://www.w3.org/2000/svg" width="50px" viewBox="0 0 512 512" fill="#A32323">
						<path
							d="M256 0C114.615 0 0 114.615 0 256s114.615 256 256 256 256-114.615 256-256S397.385 0 256 0zm-96 256c0-53.02 42.98-96 96-96s96 42.98 96 96-42.98 96-96 96-96-42.98-96-96zm302.99 85.738l-88.71-36.745C380.539 289.901 384 273.355 384 256s-3.461-33.901-9.72-48.993l88.71-36.745C473.944 196.673 480 225.627 480 256s-6.057 59.327-17.01 85.738zM341.739 49.01l-36.745 88.71C289.902 131.461 273.356 128 256 128s-33.901 3.461-48.993 9.72l-36.745-88.711C196.673 38.057 225.628 32 256 32c30.373 0 59.327 6.057 85.739 17.01zM49.01 170.262l88.711 36.745C131.462 222.099 128 238.645 128 256s3.461 33.901 9.72 48.993l-88.71 36.745C38.057 315.327 32 286.373 32 256s6.057-59.327 17.01-85.738zM170.262 462.99l36.745-88.71C222.099 380.539 238.645 384 256 384s33.901-3.461 48.993-9.72l36.745 88.71C315.327 473.942 286.373 480 256 480s-59.327-6.057-85.738-17.01z">
						</path>
					</svg>
					<h3>Get Support</h3>
					<p>Submit a support ticket and our world class support will be in touch.</p>
					<a href="<?php echo esc_url( Soliloquy_Common_Admin_Lite::get_instance()->get_upgrade_link( false, 'getstartedtab', 'support' ) ); ?>"
						class="button soliloquy-button soliloquy-primary-button" target="_blank">Unlock Pro</a>
				</div>
				<div class="soliloquy-cols">
					<svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" viewBox="0 0 125.23 125.23"
						width="50px">
						<path fill="#162937" fillRule="evenodd"
							d="M112.51 31.91l-7.67-7.68v15.36H89.49l7.67 7.67a21.72 21.72 0 010 30.71l-3.84 3.84v15.35h15.36l3.84-3.84a43.43 43.43 0 00-.01-61.41z">
						</path>
						<path fill="#162937" fillRule="evenodd"
							d="M85.65 89.48L78 97.16a21.72 21.72 0 01-30.71 0l-3.84-3.84H28.07v15.36l3.84 3.83a43.43 43.43 0 0061.41 0l7.68-7.68H85.65z">
						</path>
						<path fill="#ff3700" fillRule="evenodd"
							d="M35.75 85.65L28.07 78a21.72 21.72 0 010-30.71l3.84-3.83V28.07H16.56l-3.84 3.84a43.42 43.42 0 000 61.41L20.4 101V85.65z">
						</path>
						<path fill="#162937" fillRule="evenodd"
							d="M39.59 35.75l7.67-7.68a21.72 21.72 0 0130.71 0l3.84 3.84h15.35V16.56l-3.84-3.84a43.42 43.42 0 00-61.41 0l-7.67 7.68h15.35z">
						</path>
					</svg>
					<h3>Enjoying Soliloquy?</h3>
					<p>Submit a support ticket and our world class support will be in touch.</p>
					<a href="https://wordpress.org/plugins/soliloquy-lite/#reviews"
						class="button soliloquy-button soliloquy-primary-button" target="_blank">Leave a Review</a>
				</div>
			</div>
		</div>

	</div> <!-- wrap -->


		<?php
	}


	/**
	 * Output the upgrade screen.
	 *
	 * @since 1.8.1
	 */
	public function lite_vs_pro_page() {
		?>
		<?php self::tab_navigation( __METHOD__ ); ?>

	<div class="soliloquy-welcome-wrap soliloquy-help">

		<div class="soliloquy-get-started-main">

			<div class="soliloquy-get-started-panel">

				<div id="soliloquy-admin-litevspro" class="wrap soliloquy-admin-wrap">
				<div class="soliloquy-panel soliloquy-litevspro-panel">
				<div class="soliloquy-admin-litevspro-section soliloquy-admin-litevspro-section-hero">

						<h2 class="headline-title">
							<strong>Lite</strong> vs <strong>Pro</strong>
						</h2>

						<h4 class="headline-subtitle">Get the most out of Soliloquy by upgrading to Pro and
							unlocking all of the powerful features.</h2>

				</div>
					<div class="soliloquy-admin-litevspro-section no-bottom soliloquy-admin-litevspro-section-table">

						<table cellspacing="0" cellpadding="0" border="0">
							<thead>
								<th>Feature</th>
								<th>Lite</th>
								<th>Pro</th>
							</thead>
							<tbody>
								<tr class="soliloquy-admin-columns">
									<td class="soliloquy-admin-litevspro-first-column">
										<p>Slider Themes</p>
									</td>
									<td class="soliloquy-admin-litevspro-lite-column">
										<p class="features-partial">
											<strong>Basic Slider Theme</strong>
										</p>
									</td>
									<td class="soliloquy-admin-litevspro-pro-column">
										<p class="features-full">
											<strong>All Slider Themes</strong>
											Enhance the appearance of your WordPress slider layout with
											beautiful and custom slider themes!
										</p>
									</td>
								</tr>

								<tr class="soliloquy-admin-columns">
									<td class="soliloquy-admin-litevspro-first-column">
										<p>Lightbox Features</p>
									</td>
									<td class="soliloquy-admin-litevspro-lite-column">
										<p class="features-partial">
											<strong>No Lightbox</strong>
										</p>
									</td>
									<td class="soliloquy-admin-litevspro-pro-column">
										<p class="features-full">
											<strong>All Advanced Lightbox Features</strong>
											Multiple themes for your Slider Lightbox display, Titles,
											Transitions, Fullscreen, Counter, Thumbnails
										</p>
									</td>
								</tr>

								<tr class="soliloquy-admin-columns">
									<td class="soliloquy-admin-litevspro-first-column">
										<p>Mobile Features</p>
									</td>
									<td class="soliloquy-admin-litevspro-lite-column">
										<p class="features-partial">
											<strong>Basic Mobile Slider</strong>
										</p>
									</td>
									<td class="soliloquy-admin-litevspro-pro-column">
										<p class="features-full">
											<strong>All Advanced Mobile Settings</strong>Customize all
											aspects of your user's mobile sliders display experience to be
											different than the default desktop
										</p>
									</td>
								</tr>
								<tr class="soliloquy-admin-columns">
									<td class="soliloquy-admin-litevspro-first-column">
										<p>HTML Sliders</p>
									</td>
									<td class="soliloquy-admin-litevspro-lite-column">
										<p class="features-none">
											<strong>No HTML Slider</strong>
										</p>
									</td>
									<td class="soliloquy-admin-litevspro-pro-column">
										<p class="features-full">
											<strong>Custom HTML Slides</strong> Add custom fully customizable
											html slides
										</p>
									</td>
								</tr>
								<tr class="soliloquy-admin-columns">
									<td class="soliloquy-admin-litevspro-first-column">
										<p>Video Sliders</p>
									</td>
									<td class="soliloquy-admin-litevspro-lite-column">
										<p class="features-none">
											<strong> No Videos </strong>
										</p>
									</td>
									<td class="soliloquy-admin-litevspro-pro-column">
										<p class="features-full">
											<strong>All Videos Sliders</strong> Import your own videos or
											from any major video sharing platform
										</p>
									</td>
								</tr>
								<tr class="soliloquy-admin-columns">
									<td class="soliloquy-admin-litevspro-first-column">
										<p>Advanced Slider Features </p>
									</td>
									<td class="soliloquy-admin-litevspro-lite-column">
										<p class="features-none">
											<strong> No Advanced Features </strong>
										</p>
									</td>
									<td class="soliloquy-admin-litevspro-pro-column">
										<p class="features-full">
											<strong>All Advanced Features</strong>Carousel, Ecommerce,
											Dynamic, Thumbnails, and Expanded Slider Configurations
										</p>
									</td>
								</tr>
								<tr class="soliloquy-admin-columns">
									<td class="soliloquy-admin-litevspro-first-column">
										<p>Soliloquy Addons</p>
									</td>
									<td class="soliloquy-admin-litevspro-lite-column">
										<p class="features-none">
											<strong>No Addons Included </strong>
										</p>
									</td>
									<td class="soliloquy-admin-litevspro-pro-column">
										<p class="features-full">
											<strong> All Addons Included</strong>WooCommerce, Carousel,
											Lightbox, Schedule, PDF Slider, Thumbnail Navigation, Defaults
											Addon and more
										</p>
									</td>
								</tr>
								<tr class="soliloquy-admin-columns">
									<td class="soliloquy-admin-litevspro-first-column">
										<p>Customer Support </p>
									</td>
									<td class="soliloquy-admin-litevspro-lite-column">
										<p class="features-none">
											<strong>Limited Customer Support</strong>
										</p>
									</td>
									<td class="soliloquy-admin-litevspro-pro-column">
										<p class="features-full">
											<strong> Priority Customer Support</strong>Dedicated prompt
											service via email from our top tier support team. Your request is
											assigned the highest priority
										</p>
									</td>
								</tr>

							</tbody>
						</table>

					</div>

					<div class="soliloquy-admin-litevspro-section soliloquy-admin-litevspro-section-hero">
						<div class="soliloquy-admin-about-section-hero-main no-border">
							<h3 class="call-to-action">
								<a class="soliloquy-button-text"
									href="<?php echo esc_url( Soliloquy_Common_Admin_Lite::get_instance()->get_upgrade_link( false, 'litevsprotab', 'getsoliloquyprotoday' ) ); ?>"
									target="_blank" rel="noopener noreferrer">Get Soliloquy Pro Today and
									Unlock all the Powerful Features!</a>
							</h3>

							<p>
								<strong>Bonus:</strong> Soliloquy Lite users get <span
									class="soliloquy-deal 20-percent-off">special discount</span>, using the
								code in the link above.
							</p>
						</div>
					</div>

				</div>

			</div>

		</div>

	</div> <!-- wrap -->


		<?php
	}

	/**
	 * Helper method to get plugin card
	 *
	 * @param mixed $plugin False or plugin data array.
	 * @return void
	 */
	public function get_plugin_card( $plugin = false ) {

		if ( ! $plugin ) {
			return;
		}
		$this->installed_plugins = get_plugins();

		if ( ( isset( $plugin['basename'] ) && ! isset( $this->installed_plugins[ $plugin['basename'] ] ) ) || isset( $plugin['act'] ) ) {
			?>
			<div class="soliloquy-am-plugins">
				<div class="soliloquy-am-plugins-main">
					<div>
						<img src="<?php echo esc_attr( $plugin['icon'] ); ?>" width="64px" />
					</div>
					<div>
						<h3><?php echo esc_html( $plugin['name'] ); ?></h3>
						<p class="soliloquy-am-plugins-excerpt"><?php echo esc_html( $plugin['description'] ); ?></p>
					</div>
				</div>
					<div class="soliloquy-am-plugins-footer">
					<div class="soliloquy-am-plugins-status">Status:&nbsp;<span>Not Installed</span></div>
						<div class="soliloquy-am-plugins-install-wrap">
							<span class="spinner soliloquy-am-plugins-spinner"></span>
							<?php if ( isset( $plugin['basename'] ) ) : ?>
								<a href="#" target="_blank" class="button button-primary soliloquy-am-plugins-button soliloquy-am-plugins-install" data-url="<?php echo esc_url( $plugin['url'] ); ?>" data-basename="<?php echo esc_attr( $plugin['basename'] ); ?>">Install Plugin</a>
							<?php else : ?>
								<a href="<?php echo esc_url( $plugin['url'] ); ?>" target="_blank" class="button button-primary soliloquy-am-plugins-button" data-url="<?php echo esc_url( $plugin['url'] ); ?>">Install Plugin</a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php
		} elseif ( isset( $plugin['basename'] ) && is_plugin_active( $plugin['basename'] ) ) {
			?>
							<div class="soliloquy-am-plugins">
							<div class="soliloquy-am-plugins-main">
								<div>
									<img src="<?php echo esc_attr( $plugin['icon'] ); ?>" width="64px" />
								</div>
								<div>
									<h3><?php echo esc_html( $plugin['name'] ); ?></h3>
								<p class="soliloquy-am-plugins-excerpt"><?php echo esc_html( $plugin['description'] ); ?></p>
								</div>
							</div>
								<div class="soliloquy-am-plugins-footer">
							<div class="soliloquy-am-plugins-status">Status:&nbsp;<span>Active</span></div>
								<div class="soliloquy-am-plugins-install-wrap">
									<span class="spinner soliloquy-am-plugins-spinner"></span>

							<?php if ( isset( $plugin['basename'] ) ) : ?>
								<a href="#" target="_blank" class="button button-primary soliloquy-am-plugins-button soliloquy-am-plugins-deactivate" data-url="<?php echo esc_url( $plugin['url'] ); ?>" data-basename="<?php echo esc_attr( $plugin['basename'] ); ?>">Deactivate</a>
							<?php else : ?>
								<a href="<?php echo esc_url( $plugin['url'] ); ?>" target="_blank" class="button button-primary soliloquy-am-plugins-button soliloquy-am-plugins-deactivate" data-url="<?php echo esc_url( $plugin['url'] ); ?>">Activate</a>
							<?php endif; ?>
						</div>
				</div>
						</div>
			<?php } else { ?>
				<div class="soliloquy-am-plugins">
							<div class="soliloquy-am-plugins-main">
								<div>
									<img src="<?php echo esc_attr( $plugin['icon'] ); ?>" width="64px" />
								</div>
								<div>
									<h3><?php echo esc_html( $plugin['name'] ); ?></h3>
								<p class="soliloquy-am-plugins-excerpt"><?php echo esc_html( $plugin['description'] ); ?></p>
								</div>
							</div>
							<div class="soliloquy-am-plugins-footer">
							<div class="soliloquy-am-plugins-status">Status:&nbsp;<span>Inactive</span></div>
							<div class="soliloquy-am-plugins-install-wrap">
								<span class="spinner soliloquy-am-plugins-spinner"></span>
							<?php if ( isset( $plugin['basename'] ) ) : ?>
							<a href="#" target="_blank" class="button button-primary soliloquy-am-plugins-button soliloquy-am-plugins-activate" data-url="<?php echo esc_url( $plugin['url'] ); ?>" data-basename="<?php echo esc_attr( $plugin['basename'] ); ?>">Activate</a>
							<?php else : ?>
								<a href="<?php echo esc_url( $plugin['url'] ); ?>" target="_blank" class="button button-primary soliloquy-am-plugins-button soliloquy-am-plugins-activate" data-url="<?php echo esc_url( $plugin['url'] ); ?>">Activate</a>
							<?php endif; ?>

						</div>
				</div>
						</div>
				<?php

			}
	}

	/**
	 * Return true/false based on whether a query argument is set.
	 *
	 * @return bool
	 */
	public static function is_new_install() {

		if ( get_transient( '_soliloquy_is_new_install' ) ) {
			delete_transient( '_soliloquy_is_new_install' );
			return true;
		}

		if ( isset( $_GET['is_new_install'] ) && 'true' === strtolower( sanitize_text_field( wp_unslash( $_GET['is_new_install'] ) ) ) ) { // @codingStandardsIgnoreLine
			return true;
		} elseif ( isset( $_GET['is_new_install'] ) ) { // @codingStandardsIgnoreLine
			return false;
		}
	}
	/**
	 * Helper Method to get AM Plugins
	 *
	 * @since 1.8.7
	 *
	 * @return array
	 */
	public function get_am_plugins() {

		$images_url = trailingslashit( SOLILOQUY_URL . 'assets/images/about' );
		$plugins    = [
			'optinmonster'                                 => [
				'icon'        => $images_url . 'plugin-om.png',
				'name'        => esc_html__( 'OptinMonster', 'soliloquy' ),
				'description' => esc_html__( 'Instantly get more subscribers, leads, and sales with the #1 conversion optimization toolkit. Create high converting popups, announcement bars, spin a wheel, and more with smart targeting and personalization.', 'soliloquy' ),
				'wporg'       => 'https://wordpress.org/plugins/optinmonster/',
				'url'         => 'https://downloads.wordpress.org/plugin/optinmonster.zip',
				'basename'    => 'optinmonster/optin-monster-wp-api.php',
			],
			'google-analytics-for-wordpress'               => [
				'icon'        => $images_url . 'plugin-mi.png',
				'name'        => esc_html__( 'MonsterInsights', 'soliloquy' ),
				'description' => esc_html__( 'The leading WordPress analytics plugin that shows you how people find and use your website, so you can make data driven decisions to grow your business. Properly set up Google Analytics without writing code.', 'soliloquy' ),
				'wporg'       => 'https://wordpress.org/plugins/google-analytics-for-wordpress/',
				'url'         => 'https://downloads.wordpress.org/plugin/google-analytics-for-wordpress.zip',
				'basename'    => 'google-analytics-for-wordpress/googleanalytics.php',
				'pro'         => [
					'plug'        => 'google-analytics-premium/googleanalytics-premium.php',
					'icon'        => $images_url . 'plugin-mi.png',
					'name'        => esc_html__( 'MonsterInsights Pro', 'soliloquy' ),
					'description' => esc_html__( 'The leading WordPress analytics plugin that shows you how people find and use your website, so you can make data driven decisions to grow your business. Properly set up Google Analytics without writing code.', 'soliloquy' ),
					'url'         => 'https://www.monsterinsights.com/?utm_source=soliloquygallerylite&utm_medium=link&utm_campaign=About%20soliloquy',
					'act'         => 'go-to-url',
				],
			],
			'wp-mail-smtp/wp_mail_smtp.php'                => [
				'icon'        => $images_url . 'plugin-smtp.png',
				'name'        => esc_html__( 'WP Mail SMTP', 'soliloquy' ),
				'description' => esc_html__( "Improve your WordPress email deliverability and make sure that your website emails reach user's inbox with the #1 SMTP plugin for WordPress. Over 3 million websites use it to fix WordPress email issues.", 'soliloquy' ),
				'wporg'       => 'https://wordpress.org/plugins/wp-mail-smtp/',
				'url'         => 'https://downloads.wordpress.org/plugin/wp-mail-smtp.zip',
				'basename'    => 'wp-mail-smtp/wp_mail_smtp.php',
				'pro'         => [
					'plug'        => 'wp-mail-smtp-pro/wp_mail_smtp.php',
					'icon'        => $images_url . 'plugin-smtp.png',
					'name'        => esc_html__( 'WP Mail SMTP Pro', 'soliloquy' ),
					'description' => esc_html__( "Improve your WordPress email deliverability and make sure that your website emails reach user's inbox with the #1 SMTP plugin for WordPress. Over 3 million websites use it to fix WordPress email issues.", 'soliloquy' ),
					'url'         => 'https://wpmailsmtp.com/?utm_source=soliloquygallerylite&utm_medium=link&utm_campaign=About%20soliloquy',
					'act'         => 'go-to-url',
				],
			],
			'all-in-one-seo-pack/all_in_one_seo_pack.php'  => [
				'icon'        => $images_url . 'plugin-aioseo.png',
				'name'        => esc_html__( 'AIOSEO', 'soliloquy' ),
				'description' => esc_html__( "The original WordPress SEO plugin and toolkit that improves your website's search rankings. Comes with all the SEO features like Local SEO, WooCommerce SEO, sitemaps, SEO optimizer, schema, and more.", 'soliloquy' ),
				'wporg'       => 'https://wordpress.org/plugins/all-in-one-seo-pack/',
				'url'         => 'https://downloads.wordpress.org/plugin/all-in-one-seo-pack.zip',
				'basename'    => 'all-in-one-seo-pack/all_in_one_seo_pack.php',
				'pro'         => [
					'plug'        => 'all-in-one-seo-pack-pro/all_in_one_seo_pack.php',
					'icon'        => $images_url . 'plugin-aioseo.png',
					'name'        => esc_html__( 'AIOSEO Pro', 'soliloquy' ),
					'description' => esc_html__( "The original WordPress SEO plugin and toolkit that improves your website's search rankings. Comes with all the SEO features like Local SEO, WooCommerce SEO, sitemaps, SEO optimizer, schema, and more.", 'soliloquy' ),
					'url'         => 'https://aioseo.com/?utm_source=soliloquygallerylite&utm_medium=link&utm_campaign=About%20soliloquy',
					'act'         => 'go-to-url',
				],
			],
			'coming-soon/coming-soon.php'                  => [
				'icon'        => $images_url . 'plugin-seedprod.png',
				'name'        => esc_html__( 'SeedProd', 'soliloquy' ),
				'description' => esc_html__( 'The fastest drag & drop landing page builder for WordPress. Create custom landing pages without writing code, connect them with your CRM, collect subscribers, and grow your audience. Trusted by 1 million sites.', 'soliloquy' ),
				'wporg'       => 'https://wordpress.org/plugins/coming-soon/',
				'url'         => 'https://downloads.wordpress.org/plugin/coming-soon.zip',
				'basename'    => 'coming-soon/coming-soon.php',
				'pro'         => [
					'plug'        => 'seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php',
					'icon'        => $images_url . 'plugin-seedprod.png',
					'name'        => esc_html__( 'SeedProd Pro', 'soliloquy' ),
					'description' => esc_html__( 'The fastest drag & drop landing page builder for WordPress. Create custom landing pages without writing code, connect them with your CRM, collect subscribers, and grow your audience. Trusted by 1 million sites.', 'soliloquy' ),
					'url'         => 'https://www.seedprod.com/?utm_source=soliloquygallerylite&utm_medium=link&utm_campaign=About%20soliloquy',
					'act'         => 'go-to-url',
				],
			],
			'rafflepress/rafflepress.php'                  => [
				'icon'        => $images_url . 'plugin-rp.png',
				'name'        => esc_html__( 'RafflePress', 'soliloquy' ),
				'description' => esc_html__( 'Turn your website visitors into brand ambassadors! Easily grow your email list, website traffic, and social media followers with the most powerful giveaways & contests plugin for WordPress.', 'soliloquy' ),
				'wporg'       => 'https://wordpress.org/plugins/rafflepress/',
				'url'         => 'https://downloads.wordpress.org/plugin/rafflepress.zip',
				'basename'    => 'rafflepress/rafflepress.php',
				'pro'         => [
					'plug'        => 'rafflepress-pro/rafflepress-pro.php',
					'icon'        => $images_url . 'plugin-rp.png',
					'name'        => esc_html__( 'RafflePress Pro', 'soliloquy' ),
					'description' => esc_html__( 'Turn your website visitors into brand ambassadors! Easily grow your email list, website traffic, and social media followers with the most powerful giveaways & contests plugin for WordPress.', 'soliloquy' ),
					'url'         => 'https://rafflepress.com/?utm_source=soliloquygallerylite&utm_medium=link&utm_campaign=About%20soliloquy',
					'act'         => 'go-to-url',
				],
			],
			'pushengage/main.php'                          => [
				'icon'        => $images_url . 'plugin-pushengage.png',
				'name'        => esc_html__( 'PushEngage', 'soliloquy' ),
				'description' => esc_html__( 'Connect with your visitors after they leave your website with the leading web push notification software. Over 10,000+ businesses worldwide use PushEngage to send 15 billion notifications each month.', 'soliloquy' ),
				'wporg'       => 'https://wordpress.org/plugins/pushengage/',
				'url'         => 'https://downloads.wordpress.org/plugin/pushengage.zip',
				'basename'    => 'pushengage/main.php',
			],

			'instagram-feed/instagram-feed.php'            => [
				'icon'        => $images_url . 'plugin-sb-instagram.png',
				'name'        => esc_html__( 'Smash Balloon Instagram Feeds', 'soliloquy' ),
				'description' => esc_html__( 'Easily display Instagram content on your WordPress site without writing any code. Comes with multiple templates, ability to show content from multiple accounts, hashtags, and more. Trusted by 1 million websites.', 'soliloquy' ),
				'wporg'       => 'https://wordpress.org/plugins/instagram-feed/',
				'url'         => 'https://downloads.wordpress.org/plugin/instagram-feed.zip',
				'basename'    => 'instagram-feed/instagram-feed.php',
				'pro'         => [
					'plug'        => 'instagram-feed-pro/instagram-feed.php',
					'icon'        => $images_url . 'plugin-sb-instagram.png',
					'name'        => esc_html__( 'Smash Balloon Instagram Feeds Pro', 'soliloquy' ),
					'description' => esc_html__( 'Easily display Instagram content on your WordPress site without writing any code. Comes with multiple templates, ability to show content from multiple accounts, hashtags, and more. Trusted by 1 million websites.', 'soliloquy' ),
					'url'         => 'https://smashballoon.com/instagram-feed/?utm_source=soliloquygallerylite&utm_medium=link&utm_campaign=About%20soliloquy',
					'act'         => 'go-to-url',
				],
			],
			'custom-facebook-feed/custom-facebook-feed.php' => [
				'icon'        => $images_url . 'plugin-sb-fb.png',
				'name'        => esc_html__( 'Smash Balloon Facebook Feeds', 'soliloquy' ),
				'description' => esc_html__( 'Easily display Facebook content on your WordPress site without writing any code. Comes with multiple templates, ability to embed albums, group content, reviews, live videos, comments, and reactions.', 'soliloquy' ),
				'wporg'       => 'https://wordpress.org/plugins/custom-facebook-feed/',
				'url'         => 'https://downloads.wordpress.org/plugin/custom-facebook-feed.zip',
				'basename'    => 'custom-facebook-feed/custom-facebook-feed.php',
				'pro'         => [
					'plug'        => 'custom-facebook-feed-pro/custom-facebook-feed.php',
					'icon'        => $images_url . 'plugin-sb-fb.png',
					'name'        => esc_html__( 'Smash Balloon Facebook Feeds Pro', 'soliloquy' ),
					'description' => esc_html__( 'Easily display Facebook content on your WordPress site without writing any code. Comes with multiple templates, ability to embed albums, group content, reviews, live videos, comments, and reactions.', 'soliloquy' ),
					'url'         => 'https://smashballoon.com/custom-facebook-feed/?utm_source=soliloquygallerylite&utm_medium=link&utm_campaign=About%20soliloquy',
					'act'         => 'go-to-url',
				],
			],
			'feeds-for-youtube/youtube-feed.php'           => [
				'icon'        => $images_url . 'plugin-sb-youtube.png',
				'name'        => esc_html__( 'Smash Balloon YouTube Feeds', 'soliloquy' ),
				'description' => esc_html__( 'Easily display YouTube videos on your WordPress site without writing any code. Comes with multiple layouts, ability to embed live streams, video filtering, ability to combine multiple channel videos, and more.', 'soliloquy' ),
				'wporg'       => 'https://wordpress.org/plugins/feeds-for-youtube/',
				'url'         => 'https://downloads.wordpress.org/plugin/feeds-for-youtube.zip',
				'basename'    => 'feeds-for-youtube/youtube-feed.php',
				'pro'         => [
					'plug'        => 'youtube-feed-pro/youtube-feed.php',
					'icon'        => $images_url . 'plugin-sb-youtube.png',
					'name'        => esc_html__( 'Smash Balloon YouTube Feeds Pro', 'soliloquy' ),
					'description' => esc_html__( 'Easily display YouTube videos on your WordPress site without writing any code. Comes with multiple layouts, ability to embed live streams, video filtering, ability to combine multiple channel videos, and more.', 'soliloquy' ),
					'url'         => 'https://smashballoon.com/youtube-feed/?utm_source=soliloquygallerylite&utm_medium=link&utm_campaign=About%20soliloquy',
					'act'         => 'go-to-url',
				],
			],
			'custom-twitter-feeds/custom-twitter-feed.php' => [
				'icon'        => $images_url . 'plugin-sb-twitter.png',
				'name'        => esc_html__( 'Smash Balloon Twitter Feeds', 'soliloquy' ),
				'description' => esc_html__( 'Easily display Twitter content in WordPress without writing any code. Comes with multiple layouts, ability to combine multiple Twitter feeds, Twitter card support, tweet moderation, and more.', 'soliloquy' ),
				'wporg'       => 'https://wordpress.org/plugins/custom-twitter-feeds/',
				'url'         => 'https://downloads.wordpress.org/plugin/custom-twitter-feeds.zip',
				'basename'    => 'custom-twitter-feeds/custom-twitter-feed.php',
				'pro'         => [
					'plug'        => 'custom-twitter-feeds-pro/custom-twitter-feed.php',
					'icon'        => $images_url . 'plugin-sb-twitter.png',
					'name'        => esc_html__( 'Smash Balloon Twitter Feeds Pro', 'soliloquy' ),
					'description' => esc_html__( 'Easily display Twitter content in WordPress without writing any code. Comes with multiple layouts, ability to combine multiple Twitter feeds, Twitter card support, tweet moderation, and more.', 'soliloquy' ),
					'url'         => 'https://smashballoon.com/custom-twitter-feeds/?utm_source=soliloquygallerylite&utm_medium=link&utm_campaign=About%20soliloquy',
					'act'         => 'go-to-url',
				],
			],
			'trustpulse-api/trustpulse.php'                => [
				'icon'        => $images_url . 'plugin-trustpulse.png',
				'name'        => esc_html__( 'TrustPulse', 'soliloquy' ),
				'description' => esc_html__( 'Boost your sales and conversions by up to 15% with real-time social proof notifications. TrustPulse helps you show live user activity and purchases to help convince other users to purchase.', 'soliloquy' ),
				'wporg'       => 'https://wordpress.org/plugins/trustpulse-api/',
				'url'         => 'https://downloads.wordpress.org/plugin/trustpulse-api.zip',
				'basename'    => 'trustpulse-api/trustpulse.php',
			],
			'searchwp/index.php'                           => [
				'icon'        => $images_url . 'plugin-searchwp.png',
				'name'        => esc_html__( 'SearchWP', 'soliloquy' ),
				'description' => esc_html__( 'The most advanced WordPress search plugin. Customize your WordPress search algorithm, reorder search results, track search metrics, and everything you need to leverage search to grow your business.', 'soliloquy' ),
				'wporg'       => false,
				'url'         => 'https://searchwp.com/?utm_source=soliloquygallerylite&utm_medium=link&utm_campaign=About%20soliloquy',
				'act'         => 'go-to-url',
			],
			'affiliate-wp/affiliate-wp.php'                => [
				'icon'        => $images_url . 'plugin-affwp.png',
				'name'        => esc_html__( 'AffiliateWP', 'soliloquy' ),
				'description' => esc_html__( 'The #1 affiliate management plugin for WordPress. Easily create an affiliate program for your eCommerce store or membership site within minutes and start growing your sales with the power of referral marketing.', 'soliloquy' ),
				'wporg'       => false,
				'url'         => 'https://affiliatewp.com/?utm_source=soliloquygallerylite&utm_medium=link&utm_campaign=About%20soliloquy',
				'act'         => 'go-to-url',
			],
			'stripe/stripe-checkout.php'                   => [
				'icon'        => $images_url . 'plugin-wp-simple-pay.png',
				'name'        => esc_html__( 'WP Simple Pay', 'soliloquy' ),
				'description' => esc_html__( 'The #1 Stripe payments plugin for WordPress. Start accepting one-time and recurring payments on your WordPress site without setting up a shopping cart. No code required.', 'soliloquy' ),
				'wporg'       => 'https://wordpress.org/plugins/stripe/',
				'url'         => 'https://downloads.wordpress.org/plugin/stripe.zip',
				'basename'    => 'stripe/stripe-checkout.php',
				'pro'         => [
					'plug'        => 'wp-simple-pay-pro-3/simple-pay.php',
					'icon'        => $images_url . 'plugin-wp-simple-pay.png',
					'name'        => esc_html__( 'WP Simple Pay Pro', 'soliloquy' ),
					'description' => esc_html__( 'The #1 Stripe payments plugin for WordPress. Start accepting one-time and recurring payments on your WordPress site without setting up a shopping cart. No code required.', 'soliloquy' ),
					'url'         => 'https://wpsimplepay.com/?utm_source=soliloquygallerylite&utm_medium=link&utm_campaign=About%20soliloquy',
					'act'         => 'go-to-url',
				],
			],

			'easy-digital-downloads/easy-digital-downloads.php' => [
				'icon'        => $images_url . 'plugin-edd.png',
				'name'        => esc_html__( 'Easy Digital Downloads', 'soliloquy' ),
				'description' => esc_html__( 'The best WordPress eCommerce plugin for selling digital downloads. Start selling eBooks, software, music, digital art, and more within minutes. Accept payments, manage subscriptions, advanced access control, and more.', 'soliloquy' ),
				'wporg'       => 'https://wordpress.org/plugins/easy-digital-downloads/',
				'url'         => 'https://downloads.wordpress.org/plugin/easy-digital-downloads.zip',
				'basename'    => 'easy-digital-downloads/easy-digital-downloads.php',
			],

			'sugar-calendar-lite/sugar-calendar-lite.php'  => [
				'icon'        => $images_url . 'plugin-sugarcalendar.png',
				'name'        => esc_html__( 'Sugar Calendar', 'soliloquy' ),
				'description' => esc_html__( 'A simple & powerful event calendar plugin for WordPress that comes with all the event management features including payments, scheduling, timezones, ticketing, recurring events, and more.', 'soliloquy' ),
				'wporg'       => 'https://wordpress.org/plugins/sugar-calendar-lite/',
				'url'         => 'https://downloads.wordpress.org/plugin/sugar-calendar-lite.zip',
				'basename'    => 'sugar-calendar-lite/sugar-calendar-lite.php',
				'pro'         => [
					'plug'        => 'sugar-calendar/sugar-calendar.php',
					'icon'        => $images_url . 'plugin-sugarcalendar.png',
					'name'        => esc_html__( 'Sugar Calendar Pro', 'soliloquy' ),
					'description' => esc_html__( 'A simple & powerful event calendar plugin for WordPress that comes with all the event management features including payments, scheduling, timezones, ticketing, recurring events, and more.', 'soliloquy' ),
					'url'         => 'https://sugarcalendar.com/?utm_source=soliloquygallerylite&utm_medium=link&utm_campaign=About%20soliloquy',
					'act'         => 'go-to-url',
				],
			],
			'charitable/charitable.php'                    => [
				'icon'        => $images_url . 'plugin-charitable.png',
				'name'        => esc_html__( 'WP Charitable', 'soliloquy' ),
				'description' => esc_html__( 'Top-rated WordPress donation and fundraising plugin. Over 10,000+ non-profit organizations and website owners use Charitable to create fundraising campaigns and raise more money online.', 'soliloquy' ),
				'wporg'       => 'https://wordpress.org/plugins/charitable/',
				'url'         => 'https://downloads.wordpress.org/plugin/charitable.zip',
				'basename'    => 'charitable/charitable.php',
			],
			'insert-headers-and-footers/ihaf.php'          => [
				'icon'        => $images_url . 'plugin-wpcode.png',
				'name'        => esc_html__( 'WPCode', 'soliloquy' ),
				'description' => esc_html__( 'Future proof your WordPress customizations with the most popular code snippet management plugin for WordPress. Trusted by over 1,500,000+ websites for easily adding code to WordPress right from the admin area.', 'soliloquy' ),
				'wporg'       => 'https://wordpress.org/plugins/insert-headers-and-footers/',
				'url'         => 'https://downloads.wordpress.org/plugin/insert-headers-and-footers.zip',
				'basename'    => 'insert-headers-and-footers/ihaf.php',
			],
			'duplicator/duplicator.php'                    => [
				'icon'        => $images_url . 'plugin-duplicator.png',
				'name'        => esc_html__( 'Duplicator', 'soliloquy' ),
				'description' => esc_html__( 'Leading WordPress backup & site migration plugin. Over 1,500,000+ smart website owners use Duplicator to make reliable and secure WordPress backups to protect their websites. It also makes website migration really easy.', 'soliloquy' ),
				'wporg'       => 'https://wordpress.org/plugins/duplicator/',
				'url'         => 'https://downloads.wordpress.org/plugin/duplicator.zip',
				'basename'    => 'duplicator/duplicator.php',
			],
			'envira-gallery-lite'                          => [
				'name'        => 'Gallery Plugin for WordPress – Envira Photo Gallery',
				'description' => 'Envira Gallery is the fastest, easiest to use WordPress image gallery plugin. Lightbox with Drag & Drop builder that helps you create beautiful galleries.',
				'icon'        => $images_url . 'envira.png',
				'url'         => 'https://downloads.wordpress.org/plugin/envira-gallery-lite.zip',
				'basename'    => 'envira-gallery-lite/envira-gallery-lite.php',
			],
		];

		return $plugins;
	}
	/**
	 * Return a user-friendly version-number string, for use in translations.
	 *
	 * @since 2.2.0
	 *
	 * @return string
	 */
	public static function display_version() {

		return SOLILOQUY_VERSION;
	}
}


$soliloquy_welcome = new Soliloquy_Welcome();
