<?php
/**
 * Elementor Editor Settings Integration.
 *
 * Adds custom React-based settings to the Elementor editor page settings panel.
 *
 * @package     Astra
 * @link        https://wpastra.com/
 * @since       4.11.3
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Elementor Editor Settings integration class.
 *
 * @since 4.11.3
 */
if ( ! class_exists( 'Astra_Elementor_Editor_Settings' ) ) {
	/**
	 * Elementor Editor Settings integration class.
	 *
	 * @since 4.11.3
	 */
	class Astra_Elementor_Editor_Settings {
		/**
		 * Class instance.
		 *
		 * @var self|null
		 * @since 4.11.3
		 */
		private static $instance = null;

		/**
		 * Get instance.
		 *
		 * @since 4.11.3
		 * @return self
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 4.11.3
		 */
		private function __construct() {
			// Bail if Elementor is not active.
			if ( ! did_action( 'elementor/loaded' ) ) {
				return;
			}

			$this->init_hooks();
		}

		/**
		 * Initialize WordPress hooks.
		 *
		 * @since 4.11.3
		 */
		private function init_hooks() {
			// Registering actions for Elementor editor assets, document controls, and custom control styles.
			add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue_editor_assets' ) );
			add_action( 'elementor/documents/register_controls', array( $this, 'register_document_controls' ), 20 );
			add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'add_custom_control_style' ) );

			// Hook into Elementor's document saving process to persist Astra-specific settings.
			add_action( 'elementor/document/after_save', array( $this, 'sync_save_settings' ) );

			// Hook into the template redirect action to handle Elementor preview.
			add_action( 'template_redirect', array( $this, 'handle_preview' ) );

			// Hook into wp_after_insert_post to sync site-post-title with Elementor hide_title.
			add_action( 'wp_after_insert_post', array( $this, 'sync_site_post_title_to_elementor' ) );
		}

		/**
		 * Check if in Elementor editor mode.
		 *
		 * @since 4.11.9
		 * @return bool
		 */
		public static function is_elementor_editor() {
			// Check if in Elementor editor mode.
			/** @psalm-suppress UndefinedClass */
			if ( ! class_exists( '\Elementor\Plugin' ) || ! \Elementor\Plugin::$instance->editor || ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				return false;
			}

			return true;
		}

		/**
		 * Get array of setting keys for Astra Elementor settings.
		 *
		 * @since 4.11.3
		 *
		 * @return array Array of setting keys.
		 */
		public static function get_astra_elementor_setting_keys() {
			$settings = array(
				'site-post-title', // Astra Disable Title.
				'ast-site-content-layout', // Container Layout.
				'site-content-style', // Container Style.
				'site-sidebar-layout', // Sidebar Layout.
				'site-sidebar-style', // Sidebar Style.
				'ast-global-header-display', // Disable Header.
				'footer-sml-layout', // Disable Footer.
				'ast-banner-title-visibility', // Disable Banner Area.
				'ast-breadcrumbs-content', // Disable breadcrumb.
				'ast-hfb-above-header-display', // Disable Above Header.
				'ast-main-header-display', // Disable Primary Header.
				'ast-hfb-below-header-display', // Disable Below Header.
				'ast-hfb-mobile-header-display', // Disable Mobile Header.
				'theme-transparent-header-meta', // Transparent Header.
				'stick-header-meta', // Sticky Header (Pro Option).
				'header-above-stick-meta', // Stick Above Header (Pro Option).
				'header-main-stick-meta', // Stick Primary Header (Pro Option).
				'header-below-stick-meta', // Stick Below Header (Pro Option).
			);

			/**
			 * Filter to modify the Astra Elementor setting keys.
			 *
			 * @since 4.11.3
			 *
			 * @param array $keys Array of Astra Elementor setting keys.
			 */
			return apply_filters( 'astra_elementor_page_setting_keys', $settings );
		}

		/**
		 * Enqueue scripts for Elementor editor settings React app.
		 *
		 * @since 4.11.3
		 */
		public function enqueue_editor_assets() {
			// Check if in Elementor editor mode.
			if ( ! self::is_elementor_editor() ) {
				return;
			}

			$handle = 'astra-elementor-meta-settings';
			$path   = ASTRA_THEME_URI . '/inc/metabox/extend-metabox/build/elementor.js';

			// Get the script dependencies from the asset file.
			$dependencies = array();
			if ( file_exists( ASTRA_THEME_DIR . 'inc/metabox/extend-metabox/build/elementor.asset.php' ) ) {
				$assets = require ASTRA_THEME_DIR . 'inc/metabox/extend-metabox/build/elementor.asset.php';
				if ( isset( $assets['dependencies'] ) ) {
					$dependencies = $assets['dependencies'];
				}
			}

			wp_enqueue_script(
				$handle,
				$path,
				$dependencies,
				ASTRA_THEME_VERSION,
				true
			);

			/**
			 * Filter to modify the localized data for Astra astraElementor editor settings.
			 *
			 * @since 4.11.3
			 * @param array $localize_data Localized data for astraElementor editor settings.
			 */
			$localize_data = apply_filters(
				'astra_elementor_editor_localize_data',
				array(
					'themeName'    => astra_get_theme_name(),
					'themeIconUrl' => esc_url( apply_filters( 'astra_admin_menu_icon', ASTRA_THEME_URI . 'inc/assets/images/astra-logo.svg' ) ),
					'postTitle'    => get_post_meta( intval( get_the_ID() ), 'site-post-title', true ),
				)
			);

			wp_localize_script( $handle, 'astraElementorEditor', $localize_data );
		}

		/**
		 * Get formatted container layout options with 'title' and 'icon'.
		 *
		 * @return array Formatted content layout options.
		 */
		public static function get_formatted_container_layout_options() {
			$options = Astra_Meta_Boxes::get_instance()->get_content_layout_options();

			foreach ( $options as $key => $label ) {
				$options[ $key ] = array(
					'title' => $label,
					'icon'  => 'default' === $key ? 'layout-default' : $key, // alternatively we can use 'image' key to provide custom image URLs.
				);
			}

			return $options;
		}

		/**
		 * Get formatted container style options with only 'title'.
		 *
		 * @return array Formatted content style options.
		 */
		public static function get_formatted_container_style_options() {
			$options = Astra_Meta_Boxes::get_instance()->get_content_style_options();

			return array_map(
				static function( $title ) {
					return array( 'title' => $title );
				},
				$options
			);
		}

		/**
		 * Get formatted sidebar layout options with 'title' and 'icon'.
		 *
		 * @return array Formatted sidebar layout options.
		 */
		public static function get_formatted_sidebar_layout_options() {
			$options = Astra_Meta_Boxes::get_instance()->get_sidebar_options();

			foreach ( $options as $key => $label ) {
				$options[ $key ] = array(
					'title' => $label,
					'icon'  => 'default' === $key ? 'layout-default' : $key,
				);
			}

			return $options;
		}

		/**
		 * Get formatted sidebar style options with only 'title'.
		 *
		 * @return array Formatted sidebar style options.
		 */
		public static function get_formatted_sidebar_style_options() {
			$options = Astra_Meta_Boxes::get_instance()->get_sidebar_style_options();

			return array_map(
				static function( $title ) {
					return array( 'title' => $title );
				},
				$options
			);
		}

		/**
		 * Get formatted header enabled options with only 'title'.
		 * Usage: For Transparent and Sticky Header options.
		 *
		 * @return array Formatted header enabled options.
		 */
		public static function get_formatted_header_enabled_options() {
			$options = Astra_Meta_Boxes::get_instance()->get_header_enabled_options();

			return array_map(
				static function( $title ) {
					return array( 'title' => $title );
				},
				$options
			);
		}

		/**
		 * Register additional document controls.
		 *
		 * Adds a new section and control to the Elementor Page Settings panel,
		 * providing a placeholder div for the React-powered Astra Settings UI.
		 *
		 * @param \Elementor\Core\DocumentTypes\PageBase $document The PageBase document instance.
		 *
		 * @since 4.11.3
		 *
		 * @psalm-suppress DocblockTypeContradiction
		 * @psalm-suppress UndefinedDocblockClass
		 * @psalm-suppress UndefinedClass
		 */
		public function register_document_controls( $document ) {
			if ( ! class_exists( '\Elementor\Controls_Manager' ) || ! class_exists( '\Elementor\Core\DocumentTypes\PageBase' ) ) {
				return;
			}

			// Only proceed if the document is a valid Elementor PageBase type and has elements.
			if ( ! $document instanceof \Elementor\Core\DocumentTypes\PageBase || ! $document::get_property( 'has_elements' ) ) {
				return;
			}

			$id = intval( get_the_ID() );

			// Start a new section in the Elementor Page Settings -> Setting tab.
			$document->start_controls_section(
				'astra_section',
				array(
					'label' => esc_html__( 'Astra Settings', 'astra' ),
					'tab'   => \Elementor\Controls_Manager::TAB_SETTINGS,
				)
			);

			// Container Section Title.
			$document->add_control(
				'ast-container-heading',
				array(
					'label' => __( 'Container', 'astra' ),
					'type'  => \Elementor\Controls_Manager::HEADING,
				)
			);

			// Container Layout using Choose control with images.
			$document->add_control(
				'ast-site-content-layout',
				array(
					'classes' => 'ast-layout-control',
					'label'   => __( 'Container Layout', 'astra' ),
					'type'    => \Elementor\Controls_Manager::CHOOSE,
					'options' => self::get_formatted_container_layout_options(),
					'default' => get_post_meta( $id, 'ast-site-content-layout', true ),
					'toggle'  => false,
				)
			);

			// Container Style using Choose control (button group style).
			$document->add_control(
				'site-content-style',
				array(
					'classes'     => 'ast-selector-control',
					'label'       => __( 'Container Style', 'astra' ),
					'type'        => \Elementor\Controls_Manager::CHOOSE,
					'options'     => self::get_formatted_container_style_options(),
					'default'     => get_post_meta( $id, 'site-content-style', true ),
					'description' => __( 'Container style will apply only when layout is set to either Normal or Narrow.', 'astra' ),
					'toggle'      => false,
				)
			);

			// Sidebar Section Title.
			$document->add_control(
				'ast-sidebar-heading',
				array(
					'label'     => __( 'Sidebar', 'astra' ),
					'type'      => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$document->add_control(
				'site-sidebar-layout',
				array(
					'classes'     => 'ast-layout-control',
					'label'       => __( 'Sidebar Layout', 'astra' ),
					'type'        => \Elementor\Controls_Manager::CHOOSE,
					'options'     => self::get_formatted_sidebar_layout_options(),
					'default'     => get_post_meta( $id, 'site-sidebar-layout', true ),
					'description' => __( 'Sidebar will only apply when container layout is set to normal.', 'astra' ),
					'toggle'      => false,
				)
			);

			$document->add_control(
				'site-sidebar-style',
				array(
					'classes' => 'ast-selector-control',
					'label'   => __( 'Sidebar Style', 'astra' ),
					'type'    => \Elementor\Controls_Manager::CHOOSE,
					'options' => self::get_formatted_sidebar_style_options(),
					'default' => get_post_meta( $id, 'site-sidebar-style', true ),
					'toggle'  => false,
				)
			);

			// Disable Elements Section Title.
			$document->add_control(
				'ast-disable-elements-heading',
				array(
					'label'     => __( 'Disable Elements', 'astra' ),
					'type'      => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$controls = Astra_Meta_Boxes::get_instance()->get_disable_section_fields();
			foreach ( $controls as $details ) {
				$meta_key = $details['key'];
				$document->add_control(
					$meta_key,
					array(
						'label'   => $details['label'],
						'type'    => \Elementor\Controls_Manager::SWITCHER,
						'default' => get_post_meta( $id, $meta_key, true ) === 'disabled' ? 'yes' : '',
					)
				);
			}

			$document->add_control(
				'ast-advanced-settings-heading',
				array(
					'label'     => __( 'Advanced Settings', 'astra' ),
					'type'      => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$document->add_control(
				'ast-header-rows-popover',
				array(
					'classes' => 'ast-popover-control',
					'type'    => \Elementor\Controls_Manager::POPOVER_TOGGLE,
					'label'   => __( 'Header Rows', 'astra' ),
				)
			);

			$document->start_popover();

			$controls = Astra_Meta_Boxes::get_instance()->get_header_disable_meta_fields();
			foreach ( $controls as $details ) {
				$meta_key = $details['key'];
				$document->add_control(
					$meta_key,
					array(
						'label'   => $details['label'],
						'type'    => \Elementor\Controls_Manager::SWITCHER,
						'default' => get_post_meta( $id, $meta_key, true ) === 'disabled' ? 'yes' : '',
					)
				);
			}

			$document->end_popover();

			$document->add_control(
				'theme-transparent-header-meta',
				array(
					'classes' => 'ast-selector-control',
					'label'   => __( 'Transparent Header', 'astra' ),
					'type'    => \Elementor\Controls_Manager::CHOOSE,
					'options' => self::get_formatted_header_enabled_options(),
					'default' => get_post_meta( $id, 'theme-transparent-header-meta', true ),
					'toggle'  => false,
				)
			);

			if ( defined( 'ASTRA_EXT_VER' ) && class_exists( 'Astra_Ext_Extension' ) && Astra_Ext_Extension::is_active( 'sticky-header' ) ) {
				$document->add_control(
					'stick-header-meta',
					array(
						'classes' => 'ast-selector-control',
						'label'   => __( 'Sticky Header', 'astra' ),
						'type'    => \Elementor\Controls_Manager::CHOOSE,
						'options' => self::get_formatted_header_enabled_options(),
						'default' => get_post_meta( $id, 'stick-header-meta', true ),
						'toggle'  => false,
					)
				);

				$document->add_control(
					'ast-sticky-header-rows-popover',
					array(
						'classes'   => 'ast-popover-control',
						'type'      => \Elementor\Controls_Manager::POPOVER_TOGGLE,
						'label'     => __( 'Sticky Header Rows', 'astra' ),
						'condition' => array(
							'stick-header-meta' => 'enabled', // show only when Sticky Header is enabled.
						),
					)
				);

				$document->start_popover();

				$controls = Astra_Meta_Boxes::get_instance()->get_sticky_header_options();
				foreach ( $controls as $details ) {
					$meta_key = $details['key'];
					$document->add_control(
						$meta_key,
						array(
							'label'   => $details['label'],
							'type'    => \Elementor\Controls_Manager::SWITCHER,
							'default' => get_post_meta( $id, $meta_key, true ) === 'disabled' ? 'yes' : '',
						)
					);
				}

				$document->end_popover();
			}

			$document->add_control(
				'ast-save-changes-notice',
				array(
					'type'        => \Elementor\Controls_Manager::NOTICE,
					'notice_type' => 'warning',
					'content'     => __( 'Make sure to update your post for changes to take effect.', 'astra' ),
				)
			);

			// Preview changes button.
			$document->add_control(
				'ast-preview-changes-button',
				array(
					'type'        => \Elementor\Controls_Manager::BUTTON,
					'button_type' => 'success',
					'text'        => esc_html__( 'Preview Changes', 'astra' ),
					'event'       => 'namespace:editor:astraRefresh',
					'disabled'    => true,
				)
			);

			if ( ! defined( 'ASTRA_EXT_VER' ) ) {
				$document->add_control(
					'ast-pro-notice',
					array(
						'type' => \Elementor\Controls_Manager::RAW_HTML,
						'raw'  => sprintf(
							'<div class="ast-pro-upgrade-cta-wrapper">
								<img src="%1$s" alt="Astra Logo">
								<p class="elementor-control-field-description">%2$s</p>
								<a href="%3$s" class="ast-pro-upgrade-link" target="_blank">%4$s</a>
							</div>',
							esc_url( ASTRA_THEME_URI . 'inc/assets/images/astra-logo.svg' ),
							__( 'Unlock your full design potential and build a website to be proud of with Astra Pro.', 'astra' ),
							astra_get_pro_url( '/pricing/', 'free-theme', 'elementor-editor', 'upgrade' ),
							__( 'Upgrade Now', 'astra' )
						),
					)
				);
			}

			$document->end_controls_section();

			// Add a info notice below the Hide Title control.
			$document->start_injection(
				array(
					'of'       => 'post_status',
					'fallback' => array(
						'of' => 'post_title',
					),
				)
			);

			$document->add_control(
				'ast-hide-title-notice',
				array(
					'type'        => \Elementor\Controls_Manager::NOTICE,
					'notice_type' => 'info',
					'content'     => __( "Changes to 'Hide Title' will automatically sync with Astra’s 'Disable Title' option.", 'astra' ),
				)
			);

			$document->end_injection();
		}

		/**
		 * Adds inline styles to enhance Astra controls in Elementor Page Settings.
		 *
		 * Optimizes layout and display of CHOOSE controls (like container and sidebar settings).
		 * by using grid layouts and ensuring button labels are visible.
		 *
		 * @since 4.11.3
		 *
		 * @return void
		 */
		public function add_custom_control_style() {
			$css_rules = '
				<style>
					.ast-layout-control .elementor-control-field,
					.ast-selector-control .elementor-control-field {
						flex-direction: column;
						align-items: flex-start;
						gap: 10px;
					}

					.ast-layout-control .elementor-control-input-wrapper,
					.ast-selector-control .elementor-control-input-wrapper {
						width: 100%;
					}

					.ast-layout-control .elementor-choices {
						display: grid;
						grid-template-columns: repeat(2, 1fr);
						gap: 10px;
						height: auto;
					}

					.ast-layout-control .elementor-choices-label,
					.ast-selector-control .elementor-choices-label {
						position: relative;
						width: 100%;
					}

					.ast-layout-control .elementor-choices-label {
						min-height: 92.5px;
						border-radius: 3px;
						border: none !important;
					}

					.ast-selector-control .elementor-choices-label::before {
						content: attr(data-tooltip);
					}

					.ast-popover-control .elementor-control-popover-toggle-reset-label {
						display: none;
					}

					.elementor-control-ast-preview-changes-button.elementor-control {
						position: sticky;
						bottom: 0;
						padding-top: 15px;
						backdrop-filter: blur(2px);
					}

					.elementor-control-ast-preview-changes-button.elementor-control .elementor-control-input-wrapper,
					.elementor-control-ast-preview-changes-button.elementor-control .elementor-button {
						width: 100%;
					}

					.ast-pro-upgrade-cta-wrapper {
						padding: 20px 0;
						text-align: center;
						display: flex;
						flex-direction: column;
						align-items: center;
						gap: 15px;
					}

					.ast-pro-upgrade-cta-wrapper .elementor-control-field-description {
						margin-top: 0;
					}

					.ast-pro-upgrade-cta-wrapper a.ast-pro-upgrade-link {
						color: #0284C7;
						width: 100%;
						padding: 8px;
						border: 1px solid #0284C7;
						border-radius: 2px;
					}
				';

			// Icons CSS.
			$icon_svgs = array(
				'.layout-default'         => self::get_svg( 'layout-default' ),
				'.normal-width-container' => self::get_svg( 'normal-width-container' ),
				'.narrow-width-container' => self::get_svg( 'narrow-width-container' ),
				'.full-width-container'   => self::get_svg( 'full-width-container' ),
				'.no-sidebar'             => self::get_svg( 'no-sidebar' ),
				'.left-sidebar'           => self::get_svg( 'left-sidebar' ),
				'.right-sidebar'          => self::get_svg( 'right-sidebar' ),
			);

			foreach ( $icon_svgs as $selector => $svg ) {
				$encoded_svg = self::transform_svg( $svg );

				$css_rules .= "{$selector}::before {
					content: '';
					position: absolute;
					width: 100%;
					height: 100%;
					top: 0;
					left: 0;
					background: url(\"data:image/svg+xml;utf8,{$encoded_svg}\") no-repeat center / contain;
				}";
			}

			$css_rules .= '</style>';

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- This is inline CSS and does not require escaping.
			echo class_exists( 'Astra_Minify' ) ? Astra_Minify::trim_css( $css_rules ) : $css_rules;
		}

		/**
		 * Handle Elementor template redirect for previewing Astra settings changes.
		 *
		 * @since 4.11.3
		 */
		public function handle_preview() {
			$post_id       = intval( get_the_ID() );
			$preview_id    = intval( isset( $_GET['preview_id'] ) ? $_GET['preview_id'] : 0 );
			$preview_nonce = isset( $_GET['preview_nonce'] ) && is_scalar( $_GET['preview_nonce'] ) ? sanitize_text_field( $_GET['preview_nonce'] ) : '';

			/** @psalm-suppress UndefinedClass */
			$is_elementor_editor_preview   = class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->preview->is_preview_mode();
			$is_elementor_frontend_preview = is_preview() && $post_id === $preview_id && wp_verify_nonce( $preview_nonce, 'post_preview_' . $preview_id );

			// Bail early if not in Elementor preview.
			if ( ! $is_elementor_editor_preview && ! $is_elementor_frontend_preview ) {
				return;
			}

			$revision_id = $post_id;
			$revisions   = wp_get_post_revisions(
				$post_id,
				array(
					'order'   => 'DESC',
					'orderby' => 'ID',
				)
			);

			foreach ( $revisions as $revision ) {
				// Elementor stores this meta in preview revisions.
				if ( get_post_meta( $revision->ID, '_elementor_edit_mode', true ) ) {
					$revision_id = $revision->ID;
					break;
				}
			}

			$settings = get_post_meta( $revision_id, '_elementor_page_settings', true );
			$keys     = self::get_astra_elementor_setting_keys();
			foreach ( $keys as $meta_key ) {
				$meta_val = isset( $settings[ $meta_key ] ) ? $settings[ $meta_key ] : get_post_meta( $post_id, $meta_key, true );

				add_filter(
					'get_post_metadata',
					static function( $value, $object_id, $key ) use ( $post_id, $meta_key, $meta_val ) {
						if ( $object_id === $post_id && $key === $meta_key ) {
							return array( $meta_val === 'yes' ? 'disabled' : $meta_val );
						}
						return $value;
					},
					10,
					3
				);
			}

			// Ensure the title is enabled for Elementor preview so that Hide Title toggle works correctly.
			add_action( 'astra_the_title_enabled', '__return_true', 999 );
		}

		/**
		 * Sync specific Astra settings from Elementor's Page Settings to post meta.
		 *
		 * This function checks if certain custom Elementor controls are enabled (like disabling the header, footer,
		 * or banner area) and updates the corresponding post meta values accordingly. If the setting is not enabled,
		 * the meta key is removed.
		 *
		 * @psalm-suppress UndefinedDocblockClass
		 * @param \Elementor\Core\Base\Document $document The Elementor document object being saved.
		 *
		 * @since 4.11.3
		 *
		 * @return void
		 */
		public function sync_save_settings( $document ) {
			$post_id = $document->get_id();

			if ( ! $post_id || wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
				return;
			}

			// Get fresh saved settings directly from the meta.
			$page_settings = $document->get_meta( '_elementor_page_settings' );

			// If the settings are not an array, initialize it as an empty array.
			if ( ! is_array( $page_settings ) ) {
				$page_settings = array();
			}

			// Sync astra site-post-title with elementor hide_title.
			$page_settings['site-post-title'] = isset( $page_settings['hide_title'] ) ? $page_settings['hide_title'] : '';

			$keys = array(
				'ast-site-content-layout', // Container Layout.
				'site-content-style', // Container Style.
				'site-sidebar-layout', // Sidebar Layout.
				'site-sidebar-style', // Sidebar Style.
				'theme-transparent-header-meta', // Transparent Header.
				'stick-header-meta', // Sticky Header (Pro Option).
			);

			foreach ( $keys as $meta_key ) {
				if ( isset( $page_settings[ $meta_key ] ) ) {
					$value = $page_settings[ $meta_key ];
					if ( $value ) {
						update_post_meta( $post_id, $meta_key, $value );
					} else {
						delete_post_meta( $post_id, $meta_key );
					}

					// Remove setting from Elementor's saved settings.
					unset( $page_settings[ $meta_key ] );
				}
			}

			$keys = array(
				'site-post-title', // Astra Disable Title.
				'ast-global-header-display', // Disable Header.
				'footer-sml-layout', // Disable Footer.
				'ast-banner-title-visibility', // Disable Banner Area.
				'ast-breadcrumbs-content', // Disable breadcrumb.
				'ast-hfb-above-header-display', // Disable Above Header.
				'ast-main-header-display', // Disable Primary Header.
				'ast-hfb-below-header-display', // Disable Below Header.
				'ast-hfb-mobile-header-display', // Disable Mobile Header.
				'header-above-stick-meta', // Stick Above Header (Pro Option).
				'header-main-stick-meta', // Stick Primary Header (Pro Option).
				'header-below-stick-meta', // Stick Below Header (Pro Option).
			);

			foreach ( $keys as $meta_key ) {
				if ( isset( $page_settings[ $meta_key ] ) ) {
					$value = $page_settings[ $meta_key ];

					if ( $value === 'yes' ) {
						update_post_meta( $post_id, $meta_key, 'disabled' );
					} else {
						delete_post_meta( $post_id, $meta_key );
					}

					// Remove setting from Elementor's saved settings.
					unset( $page_settings[ $meta_key ] );
				}
			}

			// $document->update_meta( '_elementor_page_settings', $page_settings );
			update_post_meta( $post_id, '_elementor_page_settings', $page_settings );
		}

		/**
		 * Retrieves an SVG icon based on the given icon name.
		 *
		 * @param string $icon The name of the icon to retrieve.
		 *
		 * @since 4.11.3
		 *
		 * @return string The SVG code for the icon or an empty string if the icon does not exist.
		 */
		public static function get_svg( $icon = '' ) {
			if ( ! class_exists( 'Astra_Builder_UI_Controller' ) ) {
				return '';
			}

			// Ensure the SVG icons are loaded.
			$svg_icons = Astra_Builder_UI_Controller::$ast_svgs;
			if ( ! Astra_Builder_UI_Controller::$ast_svgs ) {
				Astra_Builder_UI_Controller::fetch_svg_icon();
				$svg_icons = Astra_Builder_UI_Controller::$ast_svgs;
			}

			return isset( $svg_icons[ $icon ] ) ? $svg_icons[ $icon ] : '';
		}

		/**
		 * Transforms SVG markup for use in inline CSS by modifying colors and encoding.
		 *
		 * @param string $svg The raw SVG markup.
		 *
		 * @since 4.11.3
		 *
		 * @return string The transformed and encoded SVG string.
		 */
		private static function transform_svg( $svg ) {
			// Basic validation: check if input is a non-empty string.
			if ( trim( $svg ) === '' ) {
				return rawurlencode( "<svg xmlns='http://www.w3.org/2000/svg' width='1' height='1'></svg>" );
			}

			// Replace only the first occurrence of stroke='#D1D5DB' with a semi-transparent color.
			$svg = preg_replace( "/stroke='#D1D5DB'/", "stroke='#9DA5AE55'", $svg, 1 );

			// Perform bulk replacements for remaining known values.
			$svg = str_replace(
				array( "fill='white'", "stroke='#D8DBDF'", "fill='#E5E7EB'", "fill='#D1D5DB'", "stroke='#D1D5DB'" ),
				array( "fill='#9DA5AE55'", "stroke='#9DA5AE55'", "fill='currentColor'", "fill='currentColor'", "stroke='currentColor'" ),
				$svg
			);

			// URL-encode the SVG for embedding as a background-image.
			return rawurlencode( $svg );
		}

		/**
		 * Sync site-post-title meta to Elementor hide_title when post is saved via WP editor.
		 *
		 * @param int $post_id Post id.
		 *
		 * @since 4.11.9
		 */
		public function sync_site_post_title_to_elementor( $post_id ) {
			// Skip if this is a revision or autosave.
			if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
				return;
			}

			// Bail early if this is an Elementor AJAX request.
			if ( ( isset( $_REQUEST['action'] ) && 'elementor_ajax' === $_REQUEST['action'] ) ) {
				return true;
			}

			// Skip if saving from Elementor to avoid conflicts.
			if ( did_action( 'elementor/document/after_save' ) ) {
				return;
			}

			// Skip if post builder is not Elementor.
			if ( 'builder' !== get_post_meta( $post_id, '_elementor_edit_mode', true ) ) {
				return;
			}

			// Get the site-post-title meta value.
			$site_post_title = get_post_meta( $post_id, 'site-post-title', true );

			// Get current Elementor page settings.
			$elementor_settings = get_post_meta( $post_id, '_elementor_page_settings', true );
			if ( ! is_array( $elementor_settings ) ) {
				$elementor_settings = array();
			}

			// Check if we need to update the hide_title setting.
			$current_hide_title = isset( $elementor_settings['hide_title'] ) ? $elementor_settings['hide_title'] : '';
			$new_hide_title     = 'disabled' === $site_post_title ? 'yes' : '';

			// Only update if the value has changed.
			if ( $current_hide_title !== $new_hide_title ) {
				if ( 'yes' === $new_hide_title ) {
					$elementor_settings['hide_title'] = 'yes';
				} else {
					unset( $elementor_settings['hide_title'] );
				}

				// Update the Elementor page settings meta.
				empty( $elementor_settings )
					? delete_post_meta( $post_id, '_elementor_page_settings' )
					: update_post_meta( $post_id, '_elementor_page_settings', $elementor_settings );
			}
		}

	}
}

/**
 * Initialize the class singleton.
 */
Astra_Elementor_Editor_Settings::get_instance();
