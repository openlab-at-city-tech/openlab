<?php

class M_Marketing extends C_Base_Module {

	public $object;

	public function define(
		$id = 'pope-module',
		$name = 'Pope Module',
		$description = '',
		$version = '',
		$uri = '',
		$author = '',
		$author_uri = '',
		$context = false
	) {
		parent::define(
			'photocrati-marketing',
			'Marketing',
			'Provides resources for encouraging users to upgrade to NextGEN Plus/Pro',
			'3.3.21',
			'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
			'Imagely',
			'https://www.imagely.com'
		);
	}

	public static $big_hitters_block_two_cache = [];

	protected static $display_setting_blocks = [ 'tile', 'mosaic', 'masonry' ];

	public static function is_plus_or_pro_enabled() {
		return defined( 'NGG_PRO_PLUGIN_BASENAME' )
			|| defined( 'NGG_PLUS_PLUGIN_BASENAME' )
			|| defined( 'NGG_STARTER_PLUGIN_BASENAME' )
			|| is_multisite();
	}

	/**
	 * @return stdClass
	 */
	public static function get_i18n() {
		$i18n                        = new stdClass();
		$i18n->lite_coupon           = __( 'NextGEN Basic users get a discount of 50% off regular price', 'nggallery' );
		$i18n->bonus                 = __( 'Bonus', 'nggallery' );
		$i18n->feature_not_available = __( "We're sorry, but %s is not available in the lite version of NextGEN Gallery. Please upgrade to NextGEN Pro to unlock these awesome features.", 'nggallery' );

		return $i18n;
	}

	/**
	 * @return string
	 */
	public static function get_i18n_fragment( $msg ) {
		$params = func_get_args();
		array_shift( $params );

		$i18n = self::get_i18n();

		switch ( $msg ) {
			case 'lite_coupon':
				$params = [
					'<strong>%s</strong> %s',
					$i18n->bonus,
					$i18n->lite_coupon,
				];
				break;
			case 'feature_not_available':
				array_unshift( $params, $i18n->feature_not_available );
				break;
		}

		return call_user_func_array( 'sprintf', $params );
	}

	public function _register_hooks() {

		// Load admin assets.
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );

		add_action( 'in_admin_header', [ $this, 'admin_header' ] );
		add_filter( 'admin_footer_text', [ $this, 'admin_footer' ], 1, 2 );
		add_action( 'in_admin_footer', [ $this, 'footer_template' ] );
		add_action( 'admin_footer', [ $this, 'notifications_template' ] );
		add_action( 'admin_menu', [ $this, 'add_upgrade_menu_item' ], 1000 );
		add_action( 'admin_head', [ $this, 'admin_inline_styles' ] );
		add_action( 'admin_footer', [ $this, 'admin_sidebar_target' ] );
		if ( self::is_plus_or_pro_enabled() || ! is_admin() ) {
			return;
		}

		add_action(
			'ngg_manage_albums_marketing_block',
			function () {
				self::enqueue_blocks_style();
				print self::get_big_hitters_block_albums();
			}
		);

		add_action(
			'ngg_manage_galleries_marketing_block',
			function () {
				self::enqueue_blocks_style();
				print self::get_big_hitters_block_two( 'managegalleries' );
			}
		);

		add_action(
			'ngg_manage_images_marketing_block',
			function () {
				self::enqueue_blocks_style();
				print self::get_big_hitters_block_two( 'manageimages' );
			}
		);

		add_action(
			'ngg_sort_images_marketing_block',
			function () {
				self::enqueue_blocks_style();
				print self::get_big_hitters_block_two( 'sortgallery' );
			}
		);

		add_action(
			'ngg_manage_galleries_above_table',
			function () {
				$title = __( 'Want to sell your images online?', 'nggallery' );
				$block = new C_Marketing_Block_Single_Line( $title, 'managegalleries', 'wanttosell' );
				print $block->render();
			}
		);

		add_action(
			'admin_init',
			function () {
				$forms = \Imagely\NGG\Admin\FormManager::get_instance();
				foreach ( self::$display_setting_blocks as $block ) {
					$forms->add_form( NGG_DISPLAY_SETTINGS_SLUG, "photocrati-marketing_display_settings_{$block}" );
				}

				$forms->add_form( NGG_OTHER_OPTIONS_SLUG, 'marketing_image_animation' );
				$forms->add_form( NGG_OTHER_OPTIONS_SLUG, 'marketing_image_protection' );

				$forms->move_form_to_follow_other_form(
					NGG_OTHER_OPTIONS_SLUG,
					'marketing_image_animation',
					'lightbox_effects'
				);
			}
		);
	}

	public function _register_adapters() {
		if ( ! self::is_plus_or_pro_enabled() && is_admin() ) {
			$registry = $this->get_registry();

			// Add upsell blocks to NGG pages.
			$registry->add_adapter( 'I_MVC_View', 'A_Marketing_Lightbox_Options_MVC', 'lightbox_effects' );
			$registry->add_adapter( 'I_MVC_View', 'A_Marketing_AddGallery_MVC', 'ngg_addgallery' );
			$registry->add_adapter( 'I_Form', 'A_Marketing_Other_Options_Form', 'marketing_image_protection' );
			$registry->add_adapter( 'I_Form', 'A_Marketing_Animations_Form', 'marketing_image_animation' );

			// If we call find_all() before init/admin_init an exception is thrown due to is_user_logged_in() being
			// called too early. Don't remove this action hook.
			add_action(
				'init',
				function () {
					foreach ( \Imagely\NGG\DataMappers\DisplayType::get_instance()->find_all() as $display_type ) {
						$registry = $this->get_registry();
						$registry->add_adapter( 'I_Form', 'A_Marketing_Display_Type_Settings_Form', $display_type->name );
					}

					wp_register_style(
						'ngg_marketing_blocks_style',
						\Imagely\NGG\Util\Router::get_instance()->get_static_url( 'photocrati-marketing#blocks.css' ),
						[ 'wp-block-library' ],
						NGG_SCRIPT_VERSION
					);

					wp_register_script(
						'jquery-modal',
						C_Router::get_instance()->get_static_url( 'photocrati-marketing#jquery.modal.min.js' ),
						[ 'jquery' ],
						'0.9.1'
					);

					wp_register_style(
						'jquery-modal',
						C_Router::get_instance()->get_static_url( 'photocrati-marketing#jquery.modal.min.css' ),
						[],
						'0.9.1'
					);
				}
			);

			foreach ( self::$display_setting_blocks as $block ) {
				$registry->add_adapter(
					'I_Form',
					'A_Marketing_Display_Settings_Form',
					"photocrati-marketing_display_settings_{$block}"
				);
			}
		}
	}
	/**
	 * Loads scripts for all Envira-based Administration Screens.
	 *
	 * @since 1.3.5
	 *
	 * @return void Return early if not on the proper screen.
	 */
	public function admin_scripts() {

		if ( is_nextgen_admin_page() ) {

			// Load necessary admin scripts.
			wp_register_script( NGG_PLUGIN_SLUG . '-admin-script', plugins_url( 'assets/js/min/admin-min.js', NGG_PLUGIN_FILE ), [ 'jquery', 'clipboard' ], NGG_PLUGIN_VERSION, false );
			wp_enqueue_script( NGG_PLUGIN_SLUG . '-admin-script' );
			wp_localize_script(
				NGG_PLUGIN_SLUG . '-admin-script',
				'nextgen_gallery_admin',
				[
					'ajax'                       => admin_url( 'admin-ajax.php' ),
					'dismiss_notification_nonce' => wp_create_nonce( 'nextgen_dismiss_notification' ),
					'dismiss_notice_nonce'       => wp_create_nonce( 'nextgen-dismiss-notice' ),
					'dismiss_topbar_nonce'       => wp_create_nonce( 'nextgen-dismiss-topbar' ),
					'connect_nonce'              => wp_create_nonce( 'nextgen_gallery_connect' ),
					'oops'                       => esc_html__( 'Oops!', 'nggallery' ),
					'ok'                         => esc_html__( 'OK', 'nggallery' ),
					'almost_done'                => esc_html__( 'Almost Done', 'nggallery' ),
					'server_error'               => esc_html__( 'Unfortunately there was a server connection error.', 'nggallery' ),
					'plugin_activate_btn'        => esc_html__( 'Activate', 'nggallery' ),
					'unlock_url'                 => esc_url( $this->get_utm_link( 'https://enviragallery.com/pricing', 'listgallery', 'unlock' ) ),
					'unlock_title'               => esc_html__( 'Unlock All Features', 'nggallery' ),
					'unlock_text'                => esc_html__( 'Upgrade to Pro to get access to Albums, Protected Images,  Video Galleries, and more!', 'nggallery' ),
					'unlock_btn'                 => esc_html__( 'Unlock Gallery Features ' ),
				]
			);

			// Fire a hook to load in custom admin scripts.
			do_action( 'nextgen_gallery_admin_scripts' );
		}
	}

	/**
	 * Loads styles for all Envira-based Administration Screens.
	 *
	 * @since 1.3.1
	 *
	 * @return void Return early if not on the proper screen.
	 */
	public function admin_styles() {

		if ( is_nextgen_admin_page() ) {

			// Load necessary admin styles.
			wp_register_style( NGG_PLUGIN_SLUG . '-admin-style', plugins_url( 'assets/css/admin.css', NGG_PLUGIN_FILE ), [], NGG_PLUGIN_VERSION );
			wp_enqueue_style( NGG_PLUGIN_SLUG . '-admin-style' );

			// Fire a hook to load in custom admin styles.
			do_action( 'nextgen_gallery_admin_styles' );
		}
	}

	/**
	 * Adds the Admin Header
	 *
	 * @since 3.5.0
	 *
	 * @return void
	 */
	public function admin_header() {

		// The following detects if we are viewing a NextGEN admin page.
		$is_modern_page = is_nextgen_admin_page();

		if ( ! M_NextGen_Admin::is_ngg_legacy_page() && ! $is_modern_page ) {
			return;
		}

		$url = self::get_utm_link( 'https://www.imagely.com/lite', 'topbar', 'getnextgenpro' );

		$message = sprintf(
			__( 'You are using NextGEN Gallery. To unlock more features, consider <a href="%s" target="_blank">upgrading to NextGEN Pro</a>.', 'nggallery' ),
			$url
		);

		// Load view.
		nextgen_load_admin_partial( 'header', [] );
	}

	/**
	 * Add inline styles
	 *
	 * @since 3.5.0
	 *
	 * @return void
	 */
	public function admin_inline_styles() {

		if ( nextgen_is_plus_or_pro_enabled() ) {
			return;
		}

		echo '<style>
			.nextgen-sidebar-upgrade-pro {
				background-color: #37993B;
			}
			.nextgen-sidebar-upgrade-pro a {
				color: #fff !important;
			}
		</style>';
	}

	/**
	 * Sidebar Target Blank
	 *
	 * @since 3.5.0
	 *
	 * @return void
	 */
	public function admin_sidebar_target() {

		if ( nextgen_is_plus_or_pro_enabled() ) {
			return;
		}

		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('li.nextgen-sidebar-upgrade-pro a').attr('target','_blank');
		});
		</script>
		<?php
	}

	/**
	 * Add lite-specific upgrade to pro menu item.
	 *
	 * @return void
	 */
	public function add_upgrade_menu_item() {
		global $submenu;

		if ( nextgen_is_plus_or_pro_enabled() ) {
			return;
		}

		add_submenu_page(
			NGGFOLDER,
			esc_html__( 'Upgrade to Pro', 'nggallery' ),
			esc_html__( 'Upgrade to Pro', 'nggallery' ),
			'manage_options',
			esc_url( $this->get_utm_link( 'http://www.imagely.com/lite/', 'adminsidebar', 'unlockprosidebar' ) )
		);

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$upgrade_link_position = key(
			array_filter(
				$submenu[ NGGFOLDER ],
				static function ( $item ) {
					return str_contains( $item[2], 'http://www.imagely.com/lite/' ) !== false;
				}
			)
		);

		// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
		if ( isset( $submenu[ NGGFOLDER ][ $upgrade_link_position ][4] ) ) {
			$submenu[ NGGFOLDER ][ $upgrade_link_position ][4] .= ' nextgen-sidebar-upgrade-pro';
		} else {
			$submenu[ NGGFOLDER ][ $upgrade_link_position ][] = 'nextgen-sidebar-upgrade-pro';
		}
		// phpcs:enable WordPress.WP.GlobalVariablesOverride.Prohibited
	}

	/**
	 * Load footer template
	 *
	 * @since 3.5.0
	 */
	public function footer_template() {
		if ( is_nextgen_admin_page() ) {
			// If here, we're on an Envira Gallery, so output the footer.
			nextgen_load_admin_partial( 'footer', [] );
		}
	}
	/**
	 * Helper Method to load footer template
	 *
	 * @since 3.5.0
	 */
	public function notifications_template() {
		if ( is_nextgen_admin_page() ) {
			// If here, we're on an Envira Gallery, so output the footer.
			nextgen_load_admin_partial( 'notifications', [] );
		}
	}

	/**
	 * When user is on a Envira related admin page, display footer text
	 * that graciously asks them to rate us.
	 *
	 * @since
	 * @param string $text Footer text.
	 * @return string
	 */
	public function admin_footer( $text ) {
		if ( is_nextgen_admin_page() ) {
			$url = 'https://wordpress.org/plugins/nextgen-gallery/reviews/?filter=5#new-post';
			/* translators: %s: url */
			$text = sprintf( __( 'Please rate <strong>NextGEN Gallery by Imagely</strong> <a href="%1$s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on <a href="%2$s" target="_blank">WordPress.org</a> to help us spread the word. Thank you from the NextGEN Gallery team!', 'nggallery' ), $url, $url );
		}
		return $text;
	}

	/**
	 * @param string $path
	 * @param string $medium
	 * @param string $campaign
	 * @param string $hash
	 * @param string $src
	 * @return string
	 */
	public static function get_big_hitter_link_url( $path, $medium, $campaign, $hash = '', $src = 'ngg' ) {
		if ( ! empty( $hash ) ) {
			$hash = '#' . $hash;
		}

		$url = self::get_utm_link(
			'https://www.imagely.com' . $path,
			$medium,
			$campaign,
			$src
		);

		return $url . $hash;
	}

	/**
	 * The same links are used by both of the two blocks
	 *
	 * @return array
	 */
	public static function get_big_hitters_links( $medium ) {
		return [
			[
				[
					'title' => __( 'Ecommerce', 'nggallery' ),
					'href'  => self::get_big_hitter_link_url( '/wordpress-gallery-plugin/pro-ecommerce-demo/', $medium, 'ecommerce' ),
				],
				[
					'title' => __( 'Automated Print Fulfillment', 'nggallery' ),
					'href'  => self::get_big_hitter_link_url( '/sell-photos-wordpress/', $medium, 'printfulfillment' ),
				],
				[
					'title' => __( 'Automated Tax Calculation', 'nggallery' ),
					'href'  => self::get_big_hitter_link_url( '/sell-photos-wordpress/', $medium, 'autotaxcalculations' ),
				],
				[
					'title' => __( 'Additional Gallery Displays', 'nggallery' ),
					'href'  => self::get_big_hitter_link_url( '/wordpress-gallery-plugin/nextgen-pro/', $medium, 'additionalgallerydisplays', 'features' ),
				],
				[
					'title' => __( 'Additional Album Displays', 'nggallery' ),
					'href'  => self::get_big_hitter_link_url( '/wordpress-gallery-plugin/nextgen-pro/', $medium, 'additionalalbumdisplays', 'features' ),
				],
			],
			[
				[
					'title' => __( 'Image Proofing', 'nggallery' ),
					'href'  => self::get_big_hitter_link_url( '/wordpress-gallery-plugin/pro-proofing-demo/', $medium, 'proofing' ),
				],
				[
					'title' => __( 'Image Protection', 'nggallery' ),
					'href'  => self::get_big_hitter_link_url( '/docs/turn-image-protection/', $medium, 'imageprotection' ),
				],
				[
					'title' => __( 'Pro Lightbox', 'nggallery' ),
					'href'  => self::get_big_hitter_link_url( '/wordpress-gallery-plugin/pro-lightbox-demo', $medium, 'prolightbox' ),
				],
				[
					'title' => __( 'Digital Downloads', 'nggallery' ),
					'href'  => self::get_big_hitter_link_url( '/wordpress-gallery-plugin/digital-download-demo/', $medium, 'digitaldownloads' ),
				],
				__( 'Dedicated customer support and so much more!', 'nggallery' ),
			],
		];
	}

	public static function get_big_hitters_block_base( $medium ) {
		return [
			'title'       => __( 'Want to make your gallery workflow and presentation even better?', 'nggallery' ),
			'description' => __( 'By upgrading to NextGEN Pro, you can get access to numerous other features, including:', 'nggallery' ),
			'links'       => self::get_big_hitters_links( $medium ),
			'footer'      => __( '<strong>Bonus:</strong> NextGEN Gallery users get a discount of 50% off regular price.', 'nggallery' ),
			'campaign'    => 'clickheretoupgrade',
			'medium'      => $medium,
		];
	}

	public static function get_big_hitters_block_albums() {
		$base = self::get_big_hitters_block_base( 'managealbums' );

		$base['title'] = __( 'Want to do even more with your albums?', 'nggallery' );

		$block = new C_Marketing_Block_Two_Columns(
			$base['title'],
			$base['description'],
			$base['links'],
			$base['footer'],
			'managealbums',
			$base['campaign']
		);

		return $block->render();
	}

	/**
	 * @param string $medium
	 * @return string
	 */
	public static function get_big_hitters_block_two( $medium ) {
		if ( ! empty( self::$big_hitters_block_two_cache[ $medium ] ) ) {
			return self::$big_hitters_block_two_cache[ $medium ];
		}

		$base = self::get_big_hitters_block_base( $medium );

		$base['title']       = __( 'Want to do even more with your gallery display?', 'nggallery' );
		$base['description'] = [
			__( 'We know that you will truly love NextGEN Pro. It has 2,600+ five star ratings and is active on over 900,000 websites.', 'nggallery' ),
			__( 'By upgrading to NextGEN Pro, you can get access to numerous other features, including:', 'nggallery' ),
		];

		$block = new C_Marketing_Block_Two_Columns(
			$base['title'],
			$base['description'],
			$base['links'],
			$base['footer'],
			$base['medium'],
			$base['campaign']
		);

		self::$big_hitters_block_two_cache[ $medium ] = $block->render();

		return self::$big_hitters_block_two_cache[ $medium ];
	}

	/**
	 * Get UTM link filtered through the ngg_marketing_parameters filter
	 *
	 * @param string $url
	 * @param string $medium
	 * @param string $campaign
	 * @param string $source
	 * @return string
	 */
	public static function get_utm_link( $url, $medium = 'default', $campaign = 'default', $source = 'ngg' ) {
		$params = apply_filters(
			'ngg_marketing_parameters',
			[
				'url'      => $url,
				'medium'   => $medium,
				'campaign' => $campaign,
				'source'   => $source,
			]
		);

		$url .= '?utm_source=' . $params['source'];
		$url .= '&utm_medium=' . $params['medium'];
		$url .= '&utm_campaign=' . $params['campaign'];

		return $url;
	}

	public static function enqueue_blocks_style() {
		wp_enqueue_style( 'ngg_marketing_blocks_style' );
	}

	/**
	 * @return array
	 */
	public function get_type_list() {
		return [
			'A_Marketing_AddGallery_MVC'             => 'adapter.addgallery_mvc.php',
			'A_Marketing_Animations_Form'            => 'adapter.animations_form.php',
			'A_Marketing_Display_Settings_Form'      => 'adapter.display_settings_form.php',
			'A_Marketing_Display_Type_Settings_Form' => 'adapter.display_type_settings_form.php',
			'A_Marketing_Lightbox_Options_MVC'       => 'adapter.lightbox_options_mvc.php',
			'A_Marketing_Other_Options_Form'         => 'adapter.other_options_form.php',
			'C_Marketing_Block_Base'                 => 'class.block_base.php',
			'C_Marketing_Block_Card'                 => 'class.block_card.php',
			'C_Marketing_Block_Large'                => 'class.block_large.php',
			'C_Marketing_Block_Popup'                => 'class.block_popup.php',
			'C_Marketing_Block_Single_Line'          => 'class.block_single_line.php',
			'C_Marketing_Block_Two_Columns'          => 'class.block_two_columns.php',
		];
	}
}

new M_Marketing();
