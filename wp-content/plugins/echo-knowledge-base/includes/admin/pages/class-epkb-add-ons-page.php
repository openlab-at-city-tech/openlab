<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Add-ons page
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Add_Ons_Page {

	/**
	 * Get menu item title
	 *
	 * @return string
	 */
	public static function get_menu_item_title() {
		return '<span style="color:#5cb85c;">' . esc_html__( 'Add-ons / News', 'echo-knowledge-base' ) . '</span>';
	}

	/**
	 * Display add-ons page
	 */
	public function display_add_ons_page() {

		$admin_page_views = self::get_regular_views_config();

		EPKB_HTML_Admin::admin_page_header();   ?>

		<!-- Admin Page Wrap -->
		<div id="ekb-admin-page-wrap">

			<div class="epkb-add-ons-page-container">   <?php

				/**
				 * ADMIN HEADER (KB logo and list of KBs dropdown)
				 */
				EPKB_HTML_Admin::admin_header( [], [], 'logo' );

				/**
				 * ADMIN TOOLBAR
				 */
				EPKB_HTML_Admin::admin_primary_tabs( $admin_page_views );

				/**
				 * ADMIN SECONDARY TABS
				 */
				EPKB_HTML_Admin::admin_secondary_tabs( $admin_page_views );

				/**
				 * LIST OF SETTINGS IN TABS
				 */
				EPKB_HTML_Admin::admin_primary_tabs_content( $admin_page_views );   ?>

			</div>

		</div>      <?php
	}

	private static function add_on_product( $values = array () ) {    ?>

		<div id="<?php echo esc_attr( $values['id'] ); ?>" class="add_on_product">
			<div class="top_heading">
				<h3><?php esc_html_e($values['title']); ?></h3>
				<p><i><?php esc_html_e($values['special_note']); ?></i></p>
			</div>
			<div class="featured_img">
				<img src="<?php echo esc_url( $values['img'] ); ?>">
			</div>
			<div class="description">
				<p>
					<?php echo wp_kses_post( $values['desc'] ); ?>
				</p>
			</div>
			<div class="button_container">				<?php
				if ( ! empty($values['coming_when']) ) { ?>
					<div class="coming_soon"><?php esc_html_e( $values['coming_when'] ); ?></div>				<?php
				} else {        ?>
					<a class="epkb-primary-btn" href="<?php echo esc_url( $values['learn_more_url'] ); ?>" target="_blank"><?php esc_html_e( 'Learn More', 'echo-knowledge-base' ); ?></a>				<?php
				}       ?>
			</div>

		</div>    <?php
	}

	/**
	 * Show Add-ons box
	 *
	 * @return false|string
	 */
	private static function show_addons_box() {

		ob_start();     ?>

		<div class="add_on_container">      <?php

			self::add_on_product( array(
				'id'                => 'epkb-add-on-bundle',
				'title'             => esc_html__( 'Add-on Bundle', 'echo-knowledge-base' ),
				'special_note'      => esc_html__( 'Save money with bundle discount', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/add-on-bundle-2.jpg',
				'desc'              => esc_html__( 'Save up to 50% when buying multiple add-ons together.', 'echo-knowledge-base' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/bundle-pricing/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=bundle',
			) );

			self::add_on_product( array(
				'id'                => '',
				'title'             => esc_html__( 'Elegant Layouts', 'echo-knowledge-base' ),
				'special_note'      => esc_html__( 'More ways to design your KB', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-EL'.'AY-1.1.jpg',
				'desc'              => sprintf( esc_html__( 'Use %sGrid Layout%s or %sSidebar Layout%s for KB Main page or combine Basic, Tabs, Grid and Sidebar layouts in many cool ways.', 'echo-knowledge-base' ), '<strong>', '</strong>', '<strong>', '</strong>' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/elegant-layouts/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=elegant-layouts',
			) );

			self::add_on_product( array(
				'id'                => '',
				'title'             => esc_html__( 'Unlimited Knowledge Bases', 'echo-knowledge-base' ),
				'special_note'      => esc_html__( 'Expand your documentation', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-MKB-1.jpg',
				'desc'              => sprintf( esc_html__( 'Create a separate Knowledge Base for each %sproduct, service or team%s.', 'echo-knowledge-base' ), '<strong>', '</strong>' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/multiple-knowledge-bases/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=multiple-kbs'
			) );

			self::add_on_product( array(
				'id'                => '',
				'title'             => esc_html__( 'Advanced Search', 'echo-knowledge-base' ),
				'special_note'      => esc_html__( 'Enhance and analyze user searches', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-AS'.'EA-1.jpg',
				'desc'              => esc_html__( "Enhance users' search experience and view search analytics, including popular searches and no results searches.", 'echo-knowledge-base' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/advanced-search/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=advanced-search'
			) );

			/** TODO self::add_on_product( array(
			'id'                => '',
			'title'             => esc_html__( 'Article Features', 'echo-knowledge-base' ),
			'special_note'      => esc_html__( 'Includes article rating and article change notifications.', 'echo-knowledge-base' ),
			'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/EP'.'RF-featured-image.jpg',
			'desc'              => esc_html__( 'Current features: article rating with analytics, and email notifications for new or updated articles.', 'echo-knowledge-base' ),
			'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/article-rating-and-feedback/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=article-rating'
			) ); */

			self::add_on_product( array(
				'id'                => '',
				'title'             => esc_html__( 'Widgets', 'echo-knowledge-base' ),
				'special_note'      => esc_html__( 'Shortcodes, Widgets, Sidebars', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-WI'.'DG-2.jpg',
				'desc'              => sprintf( esc_html__( 'Add KB Search, Most Recent Articles and other %sWidgets and shortcodes%s to your articles, sidebars and pages.',
					'echo-knowledge-base' ), '<strong>', '</strong>' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/widgets/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=widgets'
			) );

			self::add_on_product( array(
				'id'                => '',
				'title'             => esc_html__( 'Custom Links for PDFs and More', 'echo-knowledge-base' ),
				'special_note'      => esc_html__( 'Link to PDFs, posts and pages', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-LINK-2.jpg',
				'desc'              => sprintf( esc_html__( 'Set Articles to links to %sPDFs, pages, posts and websites%s. On KB Main Page, choose icons for your articles.', 'echo-knowledge-base' ), '<strong>', '</strong>' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/links-editor-for-pdfs-and-more/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=links-editor'
			) );

			self::add_on_product( array(
				'id'                => '',
				'title'             => esc_html__( 'Article Rating and Feedback', 'echo-knowledge-base' ),
				'special_note'      => esc_html__( 'Let users rate your articles', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/EP'.'RF-featured-image.jpg',
				'desc'              => sprintf( esc_html__( 'Let your readers rate the quality of your articles and submit insightful feedback. Collect analytics on the most and least rated articles.', 'echo-knowledge-base' ), '<strong>', '</strong>' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/article-rating-and-feedback/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=article-rating'
			) );

			self::add_on_product( array(
				'id'                => '',
				'title'             => esc_html__( 'Access Manager', 'echo-knowledge-base' ),
				'special_note'      => esc_html__( 'Protect your KB content', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-AM'.'GR-1.jpg',
				'desc'              => sprintf( esc_html__( 'Restrict your Articles to certain %sGroups%s using KB Categories. Assign users to specific %sKB Roles%s within Groups.', 'echo-knowledge-base' ), '<strong>', '</strong>', '<strong>', '</strong>' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/access-manager/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=access-manager'
			) );

			self::add_on_product( array(
				'id'                => '',
				'title'             => esc_html__( 'Migrate, Copy, Import and Export', 'echo-knowledge-base' ),
				'special_note'      => esc_html__( 'Import, export and copy Articles, images and more', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/edd/2022/01/KB-Import-Export-Banner-v2.jpg',
				'desc'              => esc_html__( "Powerful import and export plugin to migrate, create and copy articles and images from your Knowledge Base. You can also import articles from CSV and other sources.", 'echo-knowledge-base' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/kb-import-export//?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=kb-import-export/',
			) );

			self::add_on_product( array(
				'id'                => '',
				'title'             => esc_html__( 'Help Dialog Chat', 'echo-knowledge-base' ),
				'special_note'      => esc_html__( 'FAQs, Articles and Contact Form', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/edd/2020/08/KB-Import-Export-Banner.jpg',
				'desc'              => sprintf( esc_html__( '%s Engage %s your website visitors and %s gain new customers %s with page-specific %s FAQs %s and %s knowledge base articles %s. Help users communicate with you ' .
										'%s without leaving the page %s by using a simple % scontact form %s shown with the Help Dialog Chat.', 'echo-knowledge-base' ),
										'<strong>', '</strong>','<strong>', '</strong>','<strong>', '</strong>','<strong>', '</strong>','<strong>', '</strong>','<strong>', '</strong>' ),
				'learn_more_url'    => 'https://wordpress.org/plugins/help-dialog/',
			) );   ?>

		</div>      <?php

		return ob_get_clean();
	}

	/**
	 * Show Elementor plugin box
	 *
	 * @return false|string
	 */
	private static function show_elementor_plugin_box() {

		ob_start();     ?>

		<div class="epkb-features-container">   <?php
			EPKB_Add_Ons_Features::display_crel_features_details();    ?>
		</div>      <?php

		return ob_get_clean();
	}

	/**
	 * Get License boxes
	 *
	 * @param $license_content
	 * @return array[]
	 */
	private static function get_license_boxes( $license_content ) {

		$boxes = [];

		// Box: Manage Licenses
		$boxes[] =  array(
			'title' => esc_html__( 'Manage Licenses', 'echo-knowledge-base' ),
			'class' => 'epkb-admin__boxes-list__box-manage-licenses',
			'html'  => self::get_manage_licenses_box(),
		);

		ob_start();

		if ( ! empty( $license_content ) ) {    ?>
			<div class="add_on_container">
				<section id="ekcb-licenses">
					<ul>  	<!-- Add-on name / License input / status  -->   <?php
						echo wp_kses_post( $license_content );      ?>
					</ul>
				</section>
			</div>      <?php
		}

		$license_content = ob_get_clean();

		// Box: Licenses Info Notification
		if ( isset( $_GET['epkb_after_addons_setup'] ) ) {
			$boxes[] = array(
				'class' => 'epkb-admin__boxes-list__box-notification',
				'html'  => EPKB_HTML_Forms::notification_box_middle( array(
					'type'  => 'success',
					'title' => esc_html__( 'New License', 'echo-knowledge-base' ),
					'desc'  => esc_html__( 'Please enter your new license below to ensure you receive updates.', 'echo-knowledge-base' )
				), true ),
			);
		}

		// Box: Licenses
		$boxes[] =  array(
			'title' => esc_html__( 'Licenses for add-ons', 'echo-knowledge-base' ),
			'html'  => $license_content,
		);

		return $boxes;
	}

	/**
	 * Get HTML for Manage Licenses box
	 *
	 * @return string
	 */
	private static function get_manage_licenses_box() {

		ob_start();     ?>

		<p>     <?php
			echo sprintf( esc_html__( 'Please refer to the %s documentation%s for help with your license account and any other issues.', 'echo-knowledge-base' ),
			'<a href="https://www.echoknowledgebase.com/documentation/license-account/" target="_blank" rel="noopener">', '</a>');  ?>
		</p>
		<div class="epkb-license-links">
			<div class="epkb-kb__btn-wrap">
				<a href="https://www.echoknowledgebase.com/account-dashboard/" target="_blank"><?php esc_html_e( 'Licenses Dashboard', 'echo-knowledge-base' ); ?></a>
				<span class="epkb-kbnh__link__icon epkbfa epkbfa-external-link"></span>
			</div>
			<div class="epkb-kb__btn-wrap">
				<a href="https://www.echoknowledgebase.com/edd-manage-your-licenses/" target="_blank"><?php esc_html_e( 'Change Your License Site URL', 'echo-knowledge-base' ); ?></a>
				<span class="epkb-kbnh__link__icon epkbfa epkbfa-external-link"></span>
			</div>
			<div class="epkb-kb__btn-wrap">
				<a href="https://www.echoknowledgebase.com/edd-download-history/" target="_blank"><?php esc_html_e( 'Download Your Add-ons', 'echo-knowledge-base' ); ?></a>
				<span class="epkb-kbnh__link__icon epkbfa epkbfa-external-link"></span>
			</div>
			<div class="epkb-kb__btn-wrap">
				<a href="https://www.echoknowledgebase.com/your-subscriptions/" target="_blank"><?php esc_html_e( 'Manage Subscriptions', 'echo-knowledge-base' ); ?></a>
				<span class="epkb-kbnh__link__icon epkbfa epkbfa-external-link"></span>
			</div>
		</div><?php

		return ob_get_clean();
	}

	/**
	 * Get Our Free Plugins boxes
	 *
	 * @return array[]
	 */
	private static function get_our_free_plugins_boxes() {

		if ( ! function_exists( 'plugins_api' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		}

		remove_all_filters( 'plugins_api' );

		$our_free_plugins = array();

		$args_list = array(
			array( 'slug' => 'help-dialog' ),
			array( 'slug' => 'creative-addons-for-elementor' ),
			array( 'slug' => 'echo-show-ids' ),
			array( 'slug' => 'scroll-down-arrow' ),
		);

		foreach( $args_list as $args ) {
			$args['fields'] = [
				'short_description' => true,
				'icons'             => true,
				'reviews'           => false,
				'banners'           => true,
			];
			$plugin_data = plugins_api( 'plugin_information', $args );
			if ( $plugin_data && ! is_wp_error( $plugin_data ) ) {
				$our_free_plugins[] = $plugin_data;
			}
		}

		ob_start(); ?>
		<div class="wrap recommended-plugins">
			<div class="wp-list-table widefat plugin-install">
				<div class="the-list">  <?php

					foreach( $our_free_plugins as $plugin ) {
						self::display_our_free_plugin_box_html( $plugin );
					}   ?>

				</div>
			</div>
		</div>  <?php

		$boxes_html = ob_get_clean();

		return array(
			array(
				'html' => $boxes_html,
			) );
	}

	/**
	 * Return HTML for a single box on Our Free Plugins tab
	 *
	 * @param $plugin
	 */
	private static function display_our_free_plugin_box_html( $plugin ) {

		$links_allowed_tags = array(
			'a' => array(
				'id'		=> true,
				'class'		=> true,
				'href'		=> true,
				'title'		=> true,
				'target'	=> true,
				'aria-*'	=> true,
				'data-*'	=> true,
			),
			'button' => array(
				'id'		=> true,
				'class'		=> true,
				'disabled'	=> true,
				'type'		=> true,
			),
		);
		$plugins_allowed_tags = array_merge_recursive( $links_allowed_tags, array(
			'abbr'		=> array( 'title' => true ),
			'acronym'	=> array( 'title' => true ),
			'code'		=> array(),
			'pre'		=> array(),
			'em'		=> array(),
			'strong'	=> array(),
			'ul'		=> array(),
			'ol'		=> array(),
			'li'		=> array(),
			'p'			=> array(),
			'br'		=> array(),
			'cite'		=> array(),
		) );

		if ( is_object( $plugin ) ) {
			$plugin = (array) $plugin;
		}

		$title = wp_kses( $plugin['name'], $plugins_allowed_tags );

		// remove any HTML from the description.
		$version = wp_kses( $plugin['version'], $plugins_allowed_tags );

		$name = empty( $plugin['short_description'] ) ? '' : esc_html( wp_strip_all_tags( $title . ' ' . $version ) );

		$author = wp_kses( $plugin['author'], $plugins_allowed_tags );
		if ( ! empty( $author ) ) {
			/* translators: %s: Plugin author. */
			$author = ' <cite>' . sprintf( esc_html__( 'By %s' ), $author ) . '</cite>';
		}

		$requires_php = isset( $plugin['requires_php'] ) ? $plugin['requires_php'] : null;
		$requires_wp  = isset( $plugin['requires'] ) ? $plugin['requires'] : null;

		$compatible_php = is_php_version_compatible( $requires_php );
		$compatible_wp  = is_wp_version_compatible( $requires_wp );
		$tested_wp = empty( $plugin['tested'] ) || version_compare( get_bloginfo( 'version' ), $plugin['tested'], '<=' );

		$details_link = esc_url( self_admin_url(
			'plugin-install.php?tab=plugin-information&amp;plugin=' . $plugin['slug'] .
			'&amp;TB_iframe=true&amp;width=600&amp;height=550'
		) );

		$action_links = self::get_our_free_plugin_action_links( $plugin, $name, $compatible_php, $compatible_wp );

		$action_links[] = sprintf(
			'<a href="%s" class="thickbox open-plugin-details-modal" aria-label="%s" data-title="%s">%s</a>',
			esc_url( $details_link ),
			/* translators: %s: Plugin name and version. */
			esc_attr( sprintf( esc_html__( 'More information about %s' ), $name ) ),
			esc_attr( $name ),
			__( 'More Details' )
		);

		if ( ! empty( $plugin['icons']['svg'] ) ) {
			$plugin_icon_url = $plugin['icons']['svg'];
		} elseif ( ! empty( $plugin['icons']['2x'] ) ) {
			$plugin_icon_url = $plugin['icons']['2x'];
		} elseif ( ! empty( $plugin['icons']['1x'] ) ) {
			$plugin_icon_url = $plugin['icons']['1x'];
		} else {
			$plugin_icon_url = $plugin['icons']['default'];
		}

		$action_links = apply_filters( 'plugin_install_action_links', $action_links, $plugin );
		$action_links = empty( $action_links ) || ! is_array( $action_links ) ? array() : $action_links;

		$last_updated_timestamp = strtotime( $plugin['last_updated'] ); ?>

		<div class="plugin-card plugin-card-<?php echo sanitize_html_class( $plugin['slug'] ); ?>"> <?php

			self::display_our_free_plugin_incompatible_links( $compatible_php, $compatible_wp );  ?>

			<div class="plugin-card-top">
				<div class="name column-name">
					<h3>
						<a href="<?php echo esc_url( $details_link ); ?>" class="thickbox open-plugin-details-modal">							<?php
							echo esc_html( $title ); ?>
							<img src="<?php echo esc_url( $plugin_icon_url ); ?>" class="plugin-icon" alt="" />
						</a>
					</h3>
				</div>
				<div class="action-links">
					<ul class="plugin-action-buttons">	<?php
						foreach ( $action_links as $one_link ) {	?>
							<li><?php echo wp_kses( $one_link, $links_allowed_tags ); ?></li>	<?php
						}	?>
					</ul>
				</div>
				<div class="desc column-description">
					<p><?php echo ( empty( $plugin['short_description'] ) ? '' : esc_html( wp_strip_all_tags( $plugin['short_description'] ) ) ); ?></p>
					<p class="authors"><?php echo wp_kses( $author, $plugins_allowed_tags ); ?></p>
				</div>
			</div>

			<div class="plugin-card-bottom">
				<div class="vers column-rating">    <?php
					wp_star_rating(
						array(
							'rating' => $plugin['rating'],
							'type'   => 'percent',
							'number' => $plugin['num_ratings'],
						)
					);  ?>
					<span class="num-ratings" aria-hidden="true">(<?php echo esc_html( number_format_i18n( $plugin['num_ratings'] ) ); ?>)</span>
				</div>
				<div class="column-updated">
					<strong><?php esc_html_e( 'Last Updated:' ); ?></strong>    <?php
					/* translators: %s: Human-readable time difference. */
					printf( esc_html__( '%s ago' ), human_time_diff( $last_updated_timestamp ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped   ?>
				</div>
				<div class="column-downloaded"> <?php
					if ( $plugin['active_installs'] >= 1000000 ) {
						$active_installs_millions = floor( $plugin['active_installs'] / 1000000 );
						$active_installs_text     = sprintf(
						/* translators: %s: Number of millions. */
							_nx( '%s+ Million', '%s+ Million', $active_installs_millions, 'Active plugin installations' ),
							number_format_i18n( $active_installs_millions )
						);
					} elseif ( 0 == $plugin['active_installs'] ) {
						$active_installs_text = _x( 'Less Than 10', 'Active plugin installations' );
					} else {
						$active_installs_text = number_format_i18n( $plugin['active_installs'] ) . '+';
					}
					/* translators: %s: Number of installations. */
					printf( esc_html__( '%s Active Installations' ), esc_html( $active_installs_text ) );   ?>
				</div>
				<div class="column-compatibility">  <?php
					if ( ! $tested_wp ) {   ?>
						<span class="compatibility-untested"><?php esc_html_e( 'Untested with your version of WordPress' ); ?></span>   <?php
					} elseif ( ! $compatible_wp ) { ?>
						<span class="compatibility-incompatible"><?php esc_html_e( 'Incompatible with your version of WordPress' ); ?></span>   <?php
					} else {    ?>
						<span class="compatibility-compatible"><?php esc_html_e( 'Compatible with your version of WordPress' ); ?></span>   <?php
					}   ?>
				</div>
			</div>
		</div>  <?php
	}

	/**
	 * Display links in case if suggested plugin is incompatible with current WordPress or PHP version
	 *
	 * @param $compatible_php
	 * @param $compatible_wp
	 */
	private static function display_our_free_plugin_incompatible_links( $compatible_php, $compatible_wp ) {

		if ( $compatible_php && $compatible_wp ) {
			return;
		}   ?>

		<div class="notice inline notice-error notice-alt"><p>  <?php

			if ( ! $compatible_php && ! $compatible_wp ) {
				esc_html_e( 'This plugin doesn&#8217;t work with your versions of WordPress and PHP.' );
				if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
					/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
					printf(
						' ' . esc_html__( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.' ),
						esc_url( self_admin_url( 'update-core.php' ) ),
						esc_url( wp_get_update_php_url() )
					);
					wp_update_php_annotation( '</p><p><em>', '</em>' );
				} elseif ( current_user_can( 'update_core' ) ) {
					printf(
					/* translators: %s: URL to WordPress Updates screen. */
						' ' . esc_html__( '<a href="%s">Please update WordPress</a>.' ),
						esc_url( self_admin_url( 'update-core.php' ) )
					);
				} elseif ( current_user_can( 'update_php' ) ) {
					printf(
					/* translators: %s: URL to Update PHP page. */
						' ' . esc_html__( '<a href="%s">Learn more about updating PHP</a>.' ),
						esc_url( wp_get_update_php_url() )
					);
					wp_update_php_annotation( '</p><p><em>', '</em>' );
				}
			} elseif ( ! $compatible_wp ) {
				esc_html_e( 'This plugin doesn&#8217;t work with your version of WordPress.' );
				if ( current_user_can( 'update_core' ) ) {
					printf(
					/* translators: %s: URL to WordPress Updates screen. */
						' ' . esc_html__( '<a href="%s">Please update WordPress</a>.' ),
						esc_url( self_admin_url( 'update-core.php' ) )
					);
				}
			} elseif ( ! $compatible_php ) {
				__( 'This plugin doesn&#8217;t work with your version of PHP.' );
				if ( current_user_can( 'update_php' ) ) {
					printf(
					/* translators: %s: URL to Update PHP page. */
						' ' . esc_html__( '<a href="%s">Learn more about updating PHP</a>.' ),
						esc_url( wp_get_update_php_url() )
					);
					wp_update_php_annotation( '</p><p><em>', '</em>' );
				}
			}   ?>

		</p></div>  <?php
	}

	/**
	 * Get action links for single plugin in Our Free Plugins list
	 *
	 * @param $plugin
	 * @param $name
	 * @param $compatible_php
	 * @param $compatible_wp
	 * @return array
	 */
	private static function get_our_free_plugin_action_links( $plugin, $name, $compatible_php, $compatible_wp ) {

		$action_links = [];

		if ( ! current_user_can( 'install_plugins' ) && ! current_user_can( 'update_plugins' ) ) {
			return $action_links;
		}

		$status = install_plugin_install_status( $plugin );

		// not installed
		if ( $status['status'] == 'install' && $status['url'] ) {

			$action_links[] = $compatible_php && $compatible_wp
				? sprintf(
					'<a class="install-now button" data-slug="%s" href="%s" aria-label="%s" data-name="%s">%s</a>',
					esc_attr( $plugin['slug'] ),
					esc_url( $status['url'] ),
					/* translators: %s: Plugin name and version. */
					esc_attr( sprintf( _x( 'Install %s now', 'plugin' ), $name ) ),
					esc_attr( $name ),
					__( 'Install Now' ) )
				: sprintf(
					'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
					_x( 'Cannot Install', 'plugin' ) );
		}

		// update is available
		if ( $status['status'] == 'update_available' && $status['url'] ) {

			$action_links[] = $compatible_php && $compatible_wp
				? sprintf(
					'<a class="update-now button aria-button-if-js" data-plugin="%s" data-slug="%s" href="%s" aria-label="%s" data-name="%s">%s</a>',
					esc_attr( $status['file'] ),
					esc_attr( $plugin['slug'] ),
					esc_url( $status['url'] ),
					/* translators: %s: Plugin name and version. */
					esc_attr( sprintf( _x( 'Update %s now', 'plugin' ), $name ) ),
					esc_attr( $name ),
					__( 'Update Now' ) )
				: sprintf(
					'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
					_x( 'Cannot Update', 'plugin' ) );
		}

		// installed
		if ( $status['status'] == 'latest_installed' || $status['status'] == 'newer_installed' ) {

			if ( is_plugin_active( $status['file'] ) ) {
				$action_links[] = sprintf(
					'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
					_x( 'Active', 'plugin' )
				);

			} elseif ( current_user_can( 'activate_plugin', $status['file'] ) ) {
				$button_text = esc_html__( 'Activate' );
				/* translators: %s: Plugin name. */
				$button_label = _x( 'Activate %s', 'plugin' );
				$activate_url = add_query_arg(
					array(
						'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $status['file'] ),
						'action'   => 'activate',
						'plugin'   => $status['file'],
					),
					network_admin_url( 'plugins.php' )
				);

				if ( is_network_admin() ) {
					$button_text = esc_html__( 'Network Activate' );
					/* translators: %s: Plugin name. */
					$button_label = _x( 'Network Activate %s', 'plugin' );
					$activate_url = add_query_arg( array( 'networkwide' => 1 ), $activate_url );
				}

				$action_links[] = sprintf(
					'<a href="%1$s" class="button activate-now" aria-label="%2$s">%3$s</a>',
					esc_url( $activate_url ),
					esc_attr( sprintf( $button_label, $plugin['name'] ) ),
					$button_text
				);

			} else {
				$action_links[] = sprintf(
					'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
					_x( 'Installed', 'plugin' )
				);
			}
		}

		return $action_links;
	}

	/**
	 * Get configuration array for regular views
	 *
	 * @return array
	 */
	private static function get_regular_views_config() {

		$views_config = [];

		/**
		 * View: Add-ons
		 */
		$views_config[] = [

			// Shared
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_addons_news_read'] ),
			'list_key' => 'add-ons',

			// Top Panel Item
			'label_text' => esc_html__( 'Add-ons', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-cubes',

			// Boxes List
			'boxes_list' => array(
				array(
					'title' => esc_html__( 'Go Further With Add-ons', 'echo-knowledge-base' ),
					'html' => self::show_addons_box(),
				)
			),
		];

		/**
		 * View: Our Free Plugins
		 */
		$views_config[] = [

			// Shared
			'list_key'   => 'our-free-plugins',

			// Top Panel Item
			'label_text' => esc_html__( 'Our Free Plugins', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-download',

			// Boxes List
			'boxes_list' => self::get_our_free_plugins_boxes(),
		];

		/**
		 * View: Elementor Plugin
		 */
		if ( ! EPKB_Utilities::is_creative_addons_widgets_enabled() ) {
			$views_config[] = [

				// Shared
				'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_addons_news_read'] ),
				'list_key' => 'elementor-plugin',

				// Top Panel Item
				'label_text' => esc_html__( 'Elementor Plugin', 'echo-knowledge-base' ),
				'icon_class' => 'epkbfa epkbfa-info-circle',

				// Boxes List
				'boxes_list' => array(

					// Box: Create Amazing Articles
					array(
						'title' => esc_html__( 'Create Amazing Articles', 'echo-knowledge-base' ),
						'description' => esc_html__( 'Create amazing documentation using our Elementor Widgets from our new plugin called Creative Add-ons', 'echo-knowledge-base' ),
						'html' => self::show_elementor_plugin_box(),
						'extra_tags' => ['iframe']
					)
				),
			];
		}

		/**
		 * View: New Features
		 */
		/*
		$views_config[] = [

			// Shared
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_addons_news_read'] ),
			'list_key' => 'new-features',

			// Top Panel Item
			'label_text' => esc_html__( 'New Features', 'echo-knowledge-base' ),
			'main_class' => '',
			'label_class' => '',
			'icon_class' => 'epkbfa epkbfa-rocket',

			// Secondary Panel Items
			'secondary_tabs' => array(

				// Secondary View: Year 2024
				array(

					// Shared
					'list_key' => 'year-2024',
					'active' => true,

					// Secondary Panel Item
					'label_text' => esc_html__( 'Year 2024', 'echo-knowledge-base' ),
					'main_class' => '',
					'label_class' => '',

					// Secondary Boxes List
					'list_top_actions_html' => '',
					'list_bottom_actions_html' => '',
					'boxes_list' => array(
						array(
							'html' => EPKB_Add_Ons_Features::get_new_features_box_by_year( 'Year 2024' ),
						)
					),
				),

				// Secondary View: Year 2023
				array(

					// Shared
					'list_key' => 'year-2023',

					// Secondary Panel Item
					'label_text' => esc_html__( 'Year 2023', 'echo-knowledge-base' ),
					'main_class' => '',
					'label_class' => '',

					// Secondary Boxes List
					'list_top_actions_html' => '',
					'list_bottom_actions_html' => '',
					'boxes_list' => array(
						array(
							'html' => EPKB_Add_Ons_Features::get_new_features_box_by_year( 'Year 2023' ),
						)
					),
				),

				// Secondary View: Year 2022
				array(

					// Shared
					'list_key' => 'year-2022',

					// Secondary Panel Item
					'label_text' => esc_html__( 'Year 2022', 'echo-knowledge-base' ),
					'main_class' => '',
					'label_class' => '',

					// Secondary Boxes List
					'list_top_actions_html' => '',
					'list_bottom_actions_html' => '',
					'boxes_list' => array(
						array(
							'html' => EPKB_Add_Ons_Features::get_new_features_box_by_year( 'Year 2022' ),
						)
					),
				),

				// Secondary View: Year 2021
				array(

					// Shared
					'list_key' => 'year-2021',

					// Secondary Panel Item
					'label_text' => esc_html__( 'Year 2021', 'echo-knowledge-base' ),
					'main_class' => '',
					'label_class' => '',

					// Secondary Boxes List
					'list_top_actions_html' => '',
					'list_bottom_actions_html' => '',
					'boxes_list' => array(
						array(
							'html' => EPKB_Add_Ons_Features::get_new_features_box_by_year( 'Year 2021' ),
						)
					),
				),

				// Secondary View: Year 2020
				array(

					// Shared
					'list_key' => 'year-2020',

					// Secondary Panel Item
					'label_text' => esc_html__( 'Year 2020', 'echo-knowledge-base' ),
					'main_class' => '',
					'label_class' => '',

					// Secondary Boxes List
					'list_top_actions_html' => '',
					'list_bottom_actions_html' => '',
					'boxes_list' => array(
						array(
							'html' => EPKB_Add_Ons_Features::get_new_features_box_by_year( 'Year 2020' ),
						)
					),
				),

				// Secondary View: Year 2019
				array(

					// Shared
					'list_key' => 'year-2019',

					// Secondary Panel Item
					'label_text' => esc_html__( 'Year 2019', 'echo-knowledge-base' ),
					'main_class' => '',
					'label_class' => '',

					// Secondary Boxes List
					'list_top_actions_html' => '',
					'list_bottom_actions_html' => '',
					'boxes_list' => array(
						array(
							'html' => EPKB_Add_Ons_Features::get_new_features_box_by_year( 'Year 2019' ),
						)
					),
				),
			),

			// Boxes List
			'list_top_actions_html' => '',
			'list_bottom_actions_html' => '',
		];
		*/
		
		$license_content = '';
		if ( current_user_can( 'manage_options' ) ) {
			$license_content = apply_filters( 'epkb_license_fields', $license_content );
		}

		/**
		 * View: Licenses
		 */
		if ( ! empty( $license_content ) ) {
			$views_config[] = [

				// Shared
				'list_id'    => 'eckb_license_tab',
				'list_key'   => 'licenses',

				// Top Panel Item
				'label_text' => esc_html__( 'Licenses', 'echo-knowledge-base' ),
				'icon_class' => 'epkbfa epkbfa-key',

				// Boxes List
				'boxes_list' => self::get_license_boxes( $license_content ),
			];
		}

		return $views_config;
	}
}
