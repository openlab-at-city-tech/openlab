<?php

defined( 'ABSPATH' ) || die();

use Gravity_Forms\Gravity_Forms_APC\Post_Update_Handler;

GFForms::include_feed_addon_framework();

/**
 * Gravity Forms Advanced Post Creation Add-On.
 *
 * @since     1.0
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2016, Rocketgenius
 */
class GF_Advanced_Post_Creation extends GFFeedAddOn {

	/**
	 * Contains an instance of this class, if available.
	 *
	 * @since  1.0
	 * @access private
	 * @var    object $_instance If available, contains an instance of this class.
	 */
	private static $_instance = null;

	/**
	 * Defines the version of the Advanced Post Creation Add-On.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_version Contains the version, defined from advancedpostcreation.php
	 */
	protected $_version = GF_ADVANCEDPOSTCREATION_VERSION;

	/**
	 * Defines the minimum Gravity Forms version required.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_min_gravityforms_version The minimum version required.
	 */
	protected $_min_gravityforms_version = '2.4.5';

	/**
	 * Defines the plugin slug.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_slug The slug used for this plugin.
	 */
	protected $_slug = 'gravityformsadvancedpostcreation';

	/**
	 * Defines the main plugin file.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_path The path to the main plugin file, relative to the plugins folder.
	 */
	protected $_path = 'gravityformsadvancedpostcreation/advancedpostcreation.php';

	/**
	 * Defines the full path to this class file.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_full_path The full path.
	 */
	protected $_full_path = __FILE__;

	/**
	 * Defines the URL where this Add-On can be found.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string The URL of the Add-On.
	 */
	protected $_url = 'http://www.gravityforms.com';

	/**
	 * Defines the title of this Add-On.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_title The title of the Add-On.
	 */
	protected $_title = 'Gravity Forms Advanced Post Creation Add-On';

	/**
	 * Defines the short title of the Add-On.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_short_title The short title.
	 */
	protected $_short_title = 'Post Creation';

	/**
	 * Defines if Add-On should use Gravity Forms servers for update data.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    bool
	 */
	protected $_enable_rg_autoupgrade = true;

	/**
	 * Defines if Add-On should allow users to configure what order feeds are executed in.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    bool
	 */
	protected $_supports_feed_ordering = true;

	/**
	 * Defines the capability needed to access the Add-On settings page.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_capabilities_settings_page The capability needed to access the Add-On settings page.
	 */
	protected $_capabilities_settings_page = 'gravityforms_advancedpostcreation';

	/**
	 * Defines the capability needed to access the Add-On form settings page.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_capabilities_form_settings The capability needed to access the Add-On form settings page.
	 */
	protected $_capabilities_form_settings = 'gravityforms_advancedpostcreation';

	/**
	 * Defines the capability needed to uninstall the Add-On.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_capabilities_uninstall The capability needed to uninstall the Add-On.
	 */
	protected $_capabilities_uninstall = 'gravityforms_advancedpostcreation_uninstall';

	/**
	 * Defines the capabilities needed for the Advanced Post Creation Add-On
	 *
	 * @since  1.0
	 * @access protected
	 * @var    array $_capabilities The capabilities needed for the Add-On
	 */
	protected $_capabilities = array( 'gravityforms_advancedpostcreation', 'gravityforms_advancedpostcreation_uninstall' );

	/**
	 * An array of files attached to the current post, with the file entry URL as the key to the media ID.
	 *
	 * @since 1.0
	 *
	 * @var array
	 */
	private $_current_media = array();

	/**
	 * The ID of the feed currently being processed.
	 *
	 * @since 1.0
	 *
	 * @var bool|int
	 */
	private $_current_feed = false;

	/**
	 * Null or the ID of the user to be assigned as the post author.
	 *
	 * @since 1.0
	 *
	 * @var null|int
	 */
	private $_post_author = null;

	/**
	 * Enabling background feed processing to prevent performance issues delaying form submission completion.
	 *
	 * @since 1.4
	 *
	 * @var bool
	 */
	protected $_async_feed_processing = true;

	protected $_asset_min;

	/**
	 * Get instance of this class.
	 *
	 * @since  1.0
	 * @access public
	 * @static
	 *
	 * @return GF_Advanced_Post_Creation
	 */
	public static function get_instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		if ( ! isset( self::$_instance->_asset_min ) ) {
			self::$_instance->_asset_min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';
		}

		return self::$_instance;

	}

	/**
	 * Register needed hooks for Add-On.
	 *
	 * @since  1.0
	 * @access public
	 */
	public function pre_init() {

		parent::pre_init();

		add_filter( 'gform_export_form', array( $this, 'export_feeds_with_form' ) );
		add_action( 'gform_forms_post_import', array( $this, 'import_feeds_with_form' ) );

	}

	/**
	 * Register needed hooks for Add-On.
	 *
	 * @since  1.0
	 * @access public
	 */
	public function init() {

		parent::init();

		add_filter( 'gform_disable_post_creation', array( $this, 'disable_core_post_creation' ), 10, 3 );
		add_filter( 'gform_entry_detail_meta_boxes', array( $this, 'register_meta_box' ), 10, 3 );
		add_filter( 'gform_pre_replace_merge_tags', array( $this, 'filter_gform_pre_replace_merge_tags' ), 10, 7 );

		add_action( 'gform_user_registered', array( $this, 'action_gform_user_registered' ), 10, 3 );

		$this->add_delayed_payment_support(
			array(
				'option_label' => esc_html__( 'Create post only when payment is received.', 'gravityformsadvancedpostcreation' )
			)
		);
	}

	/**
	 * Register needed AJAX actions.
	 *
	 * @since  1.0
	 * @access public
	 */
	public function init_ajax() {

		parent::init_ajax();

		add_action( 'wp_ajax_gform_advancedpostcreation_taxonomy_search', array( $this, 'get_taxonomy_map_search_results' ) );
		add_action( 'wp_ajax_gform_advancedpostcreation_author_search', array( $this, 'get_post_author_search_results' ) );

	}

	/**
	 * Register needed styles.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @uses   GFAddOn::get_base_url()
	 * @uses   GFAddOn::get_slug()
	 *
	 * @return array $styles
	 */
	public function styles() {

		$styles = array(
			array(
				'handle'  => 'gform_advancedpostcreation_admin',
				'src'     => $this->get_base_url() . "/assets/css/dist/admin{$this->_asset_min}.css",
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'admin_page' => array( 'form_settings' ),
						'tab'        => $this->get_slug(),
					),
				),
			),
			array(
				'handle'  => 'gform_advancedpostcreation_select2',
				'src'     => $this->get_base_url() . '/assets/css/vendor/select2.min.css',
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'admin_page' => array( 'form_settings' ),
						'tab'        => $this->get_slug(),
					),
				),
			),
		);

		return array_merge( parent::styles(), $styles );

	}

	/**
	 * Register needed styles.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @uses   GFAddOn::get_base_url()
	 *
	 * @return array $scripts
	 */
	public function scripts() {

		$scripts = array(
			array(
				'handle'  => 'gform_advancedpostcreation_select2',
				'deps'    => array( 'jquery' ),
				'src'     => $this->get_base_url() . '/assets/js/legacy/select2.min.js',
				'version' => $this->_version,
			),
			array(
				'handle'  => 'gform_advancedpostcreation_taxonomy_map',
				'deps'    => array( 'gform_advancedpostcreation_select2' ),
				'src'     => $this->is_gravityforms_supported( '2.5-beta-1' )
					? $this->get_base_url() . "/assets/js/legacy/taxonomy_map{$this->_asset_min}.js"
					: $this->get_base_url() . "/assets/js/legacy/taxonomy_map_pre_gf25{$this->_asset_min}.js",
				'version' => $this->_version,
			),
			array(
				'handle'  => 'gform_advancedpostcreation_utils',
				'src'     => $this->get_base_url() . "/assets/js/legacy/utils{$this->_asset_min}.js",
				'deps'    => array( 'jquery' ),
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'admin_page' => array( 'form_settings' ),
						'tab'        => $this->_slug,
					),
				),
				'strings' => array(
					'GFVersion' => $this->is_gravityforms_supported( '2.5' ) ? '2.5' : '2.4',
				),
			),
			array(
				'handle'  => 'gform_advancedpostcreation_form_settings',
				'deps'    => array( 'gform_advancedpostcreation_taxonomy_map', 'gform_advancedpostcreation_utils' ),
				'src'     => $this->get_base_url() . "/assets/js/legacy/form_settings{$this->_asset_min}.js",
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'admin_page' => array( 'form_settings' ),
						'tab'        => $this->_slug,
					),
				),
				'strings' => array(
					'nonce_author' => wp_create_nonce( 'gform_advancedpostcreation_author_search' ),
					'nonce_search' => wp_create_nonce( 'gform_advancedpostcreation_taxonomy_search' ),
					'select_user'  => wp_strip_all_tags( __( 'Select a User', 'gravityformsadvancedpostcreation' ) ),
				),
			),
		);

		return array_merge( parent::scripts(), $scripts );

	}





	// # FEED SETTINGS -------------------------------------------------------------------------------------------------

	/**
	 * Setup fields for feed settings.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @uses   GF_Advanced_Post_Creation::feed_settings_fields_create_post()
	 * @uses   GFFeedAddOn::get_default_feed_name()
	 *
	 * @return array
	 */
	public function feed_settings_fields() {

		// Build base fields array.
		$base_fields = array(
			array(
				'title'  => '',
				'fields' => array(
					array(
						'name'          => 'feedName',
						'label'         => esc_html__( 'Feed Name', 'gravityformsadvancedpostcreation' ),
						'type'          => 'text',
						'required'      => true,
						'default_value' => $this->get_default_feed_name(),
						'tooltip'       => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Name', 'gravityformsadvancedpostcreation' ),
							esc_html__( 'Enter a feed name to uniquely identify this setup.', 'gravityformsadvancedpostcreation' )
						),
					),
					array(
						'name'  => 'action',
						'label' => esc_html__( 'Action', 'gravityformsadvancedpostcreation' ),
						'type'  => 'hidden',
						'value' => 'post-create',
					),
				),
			),
		);

		// Build conditional logic fields.
		$conditional_fields = array(
			array(
				'fields' => array(
					array(
						'name'           => 'feedCondition',
						'type'           => 'feed_condition',
						'label'          => esc_html__( 'Conditional Logic', 'gravityformsadvancedpostcreation' ),
						'checkbox_label' => esc_html__( 'Enable', 'gravityformsadvancedpostcreation' ),
						'instructions'   => esc_html__( 'Create if', 'gravityformsadvancedpostcreation' ),
						'tooltip'        => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Conditional Logic', 'gravityformsadvancedpostcreation' ),
							esc_html__( 'When conditional logic is enabled, form submissions will only be created when the condition is met. When disabled, all form submissions will be created.', 'gravityformsadvancedpostcreation' )
						),
					),
				),
			),
		);

		$create_post_fields = $this->feed_settings_fields_create_post();

		return array_merge( $base_fields, $create_post_fields, $conditional_fields );

	}

	/**
	 * Setup fields for post creation feed settings.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @uses GFAddOn::get_current_settings()
	 * @uses GFAddOn::add_field_after()
	 * @uses GF_Advanced_Post_Creation::get_post_formats_as_choices()
	 * @uses GF_Advanced_Post_Creation::get_post_meta_field_map()
	 * @uses GF_Advanced_Post_Creation::get_post_statuses_as_choices()
	 * @uses GF_Advanced_Post_Creation::get_post_types_as_choices()
	 * @uses GF_Advanced_Post_Creation::get_taxonomies_as_feed_settings_fields()
	 * @uses GF_Advanced_Post_Creation::get_thumbnail_fields_as_choices()
	 *
	 * @return array
	 */
	public function feed_settings_fields_create_post() {

		// Get current feed settings and form object.
		$settings = $this->get_current_settings();
		$form     = $this->get_current_form();

		// Get current post type.
		$post_type     = rgar( $settings, 'postType' ) ? rgar( $settings, 'postType' ) : 'post';
		$post_type_obj = get_post_type_object( $post_type );
		$taxonomies    = $this->get_taxonomies_as_feed_settings_fields( $post_type );

		// Prepare post date setting choices.
		$post_date_choices = array(
			array(
				'label' => esc_html__( 'Entry Date', 'gravityformsadvancedpostcreation' ),
				'value' => 'entry',
			),
			array(
				'label' => esc_html__( 'Date & Time Fields', 'gravityformsadvancedpostcreation' ),
				'value' => 'field',
			),
			array(
				'label' => esc_html__( 'Custom Date & Time', 'gravityformsadvancedpostcreation' ),
				'value' => 'custom',
			),
		);

		// Remove Date & Time Fields choice if no Date or Time fields are found.
		if ( ! GFAPI::get_fields_by_type( $form, array( 'date', 'time' ) ) ) {
			unset( $post_date_choices[1] );
		}

		// Setup fields array.
		$fields = array(
			'settings' => array(
				'id'     => 'postSettings',
				'title'  => esc_html__( 'Post Settings', 'gravityformsadvancedpostcreation' ),
				'fields' => array(
					array(
						'name'          => 'postVisibility',
						'label'         => esc_html__( 'Visibility', 'gravityformsadvancedpostcreation' ),
						'type'          => 'radio',
						'required'      => true,
						'horizontal'    => true,
						'onchange'      => "jQuery(this).parents('form').submit();",
						'default_value' => 'public',
						'choices'       => array(
							array(
								'label' => esc_html__( 'Public', 'gravityformsadvancedpostcreation' ),
								'value' => 'public',
							),
							array(
								'label' => esc_html__( 'Password Protected', 'gravityformsadvancedpostcreation' ),
								'value' => 'password-protected',
							),
						),
						'tooltip'       => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Post Visibility', 'gravityformsadvancedpostcreation' ),
							esc_html__( 'Visibility determines who can read the post.', 'gravityformsadvancedpostcreation' )
						),
					),
					array(
						'name'       => 'postPassword',
						'label'      => esc_html__( 'Password', 'gravityformsadvancedpostcreation' ),
						'type'       => 'text',
						'class'      => 'medium merge-tag-support mt-position-right mt-hide_all_fields',
						'dependency' => array( 'field' => 'postVisibility', 'values' => array( 'password-protected' ) ),
						'tooltip'    => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Post Password', 'gravityformsadvancedpostcreation' ),
							esc_html__( 'Users will be required to enter the chosen post password to view the post content', 'gravityformsadvancedpostcreation' )
						),
					),
					array(
						'name'     => 'postType',
						'label'    => esc_html__( 'Type', 'gravityformsadvancedpostcreation' ),
						'type'     => 'select',
						'required' => true,
						'choices'  => $this->get_post_types_as_choices(),
						'onchange' => "jQuery(this).parents('form').submit();",
						'tooltip'  => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Post Type', 'gravityformsadvancedpostcreation' ),
							esc_html__( 'Select one of the defined WordPress post types for the post.', 'gravityformsadvancedpostcreation' )
						),
					),
					array(
						'name'          => 'postStatus',
						'label'         => esc_html__( 'Status', 'gravityformsadvancedpostcreation' ),
						'type'          => 'select',
						'required'      => true,
						'choices'       => $this->get_post_statuses_as_choices(),
						'default_value' => 'publish',
						'tooltip'       => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Post Status', 'gravityformsadvancedpostcreation' ),
							esc_html__( 'Select the status for the post. If published status is selected and the post date is in the future, it will automatically be changed to scheduled.', 'gravityformsadvancedpostcreation' )
						),
					),
					array(
						'name'                => 'postDate',
						'label'               => esc_html__( 'Date', 'gravityformsadvancedpostcreation' ),
						'type'                => 'post_date',
						'required'            => true,
						'choices'             => $post_date_choices,
						'validation_callback' => array( $this, 'validate_settings_post_date' ),
						'tooltip'       => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Post Date', 'gravityformsadvancedpostcreation' ),
							esc_html__( 'Define the post publish date. Custom date & time can be a specific date and time or a relative time (e.g. "today", "next week").', 'gravityformsadvancedpostcreation' )
						),
					),
					array(
						'name'          => 'postAuthor',
						'label'         => esc_html__( 'Author', 'gravityformsadvancedpostcreation' ),
						'type'          => 'select',
						'required'      => false,
						'default_value' => 'logged-in-user',
						'choices'       => $this->get_post_authors_as_choices(),
						'tooltip'       => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Post Author', 'gravityformsadvancedpostcreation' ),
							esc_html__( 'Select the user to be assigned to the post.', 'gravityformsadvancedpostcreation' )
						),

					),
					array(
						'name'    => 'postDiscussion',
						'label'   => esc_html__( 'Discussion', 'gravityformsadvancedpostcreation' ),
						'type'    => 'checkbox',
						'choices' => array(
							array(
								'label' => esc_html__( 'Allow Comments', 'gravityformsadvancedpostcreation' ),
								'name'  => 'postComments',
							),
							array(
								'label' => esc_html__( 'Allow Trackbacks and Pingbacks', 'gravityformsadvancedpostcreation' ),
								'name'  => 'postPingbacks',
							),
						),
					),
				),
			),
			'content'  => array(
				'title'  => esc_html__( 'Post Content', 'gravityformsadvancedpostcreation' ),
				'fields' => array(
					array(
						'name'          => 'postTitle',
						'label'         => esc_html__( 'Title', 'gravityformsadvancedpostcreation' ),
						'type'          => 'text',
						'required'      => true,
						'class'         => 'medium merge-tag-support mt-position-right mt-hide_all_fields',
						'default_value' => $this->get_default_field_merge_tag( 'post_title' ),
					),
					array(
						'name'          => 'postContent',
						'label'         => esc_html__( 'Content', 'gravityformsadvancedpostcreation' ),
						'type'          => 'textarea',
						'required'      => true,
						'use_editor'    => true,
						'class'         => 'medium merge-tag-support mt-position-right mt-hide_all_fields',
						'default_value' => $this->get_default_field_merge_tag( 'post_content' ),
						'tooltip'       => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Post Content', 'gravityformsadvancedpostcreation' ),
							esc_html__( "Define the post's content. File upload field merge tags used within the post content will automatically have their files uploaded to the media library and associated with the post.", 'gravityformsadvancedpostcreation' )
						),
					),
					array(
						'name'       => 'disableAutoformat',
						'label'      => esc_html__( 'Auto-Formatting', 'gravityforms' ),
						'type'       => 'checkbox',
						'choices'    => array(
							array(
								'name'    => 'disableAutoformat',
								'label'   => esc_html__( 'Disable auto-formatting', 'gravityforms' ),
								'tooltip'       => sprintf(
									'<h6>%s</h6>%s',
									esc_html__( 'Disable Auto-Formatting', 'gravityformsadvancedpostcreation' ),
									esc_html__( 'When enabled, auto-formatting will insert paragraph breaks automatically. Disable auto-formatting when using HTML to create the post content.', 'gravityformsadvancedpostcreation' )
								),
							),
						),
					),
					array(
						'name'        => 'postMetaFields',
						'label'       => esc_html__( 'Custom Fields', 'gravityformsadvancedpostcreation' ),
						'type'        => 'generic_map',
						'merge_tags'  => true,
						'key_field'   => array(
							'choices'     => $this->get_post_meta_field_map(),
							'placeholder' => esc_html__( 'Custom Field Name', 'gravityformsadvancedpostcreation' ),
							'title'       => esc_html__( 'Name', 'gravityformsadvancedpostcreation' ),
						),
						'value_field' => array(
							'choices'      => 'form_fields',
							'custom_value' => true,
							'placeholder'  => esc_html__( 'Custom Field Value', 'gravityformsadvancedpostcreation' ),
						),
						'tooltip'       => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Custom Fields', 'gravityformsadvancedpostcreation' ),
							esc_html__( 'Map form values to post meta using an existing meta key or defining a new one.', 'gravityformsadvancedpostcreation' )
						),
					),
				),
			),
		);

		// Get available post formats.
		$post_formats = $this->get_post_formats_as_choices();

		// Add post format field.
		if ( $post_type_obj && post_type_supports( $post_type_obj->name, 'post-formats' ) && count( $post_formats ) > 1 ) {

			// Prepare field.
			$field = array(
				array(
					'name'    => 'postFormat',
					'label'   => esc_html__( 'Format', 'gravityformsadvancedpostcreation' ),
					'type'    => 'select',
					'choices' => $post_formats,
				),
			);

			// Add field.
			$fields = parent::add_field_after( 'postStatus', $field, $fields );

		}

		/**
		 * Enable post excerpt feed settings field.
		 *
		 * @since 1.0
		 * @param bool $enable_excerpt Whether post excerpt feed settings field should be enabled. Defaults to false.
		 */
		if ( apply_filters( 'gform_advancedpostcreation_excerpt', false ) ) {

			// Prepare field.
			$field = array(
				array(
					'name'          => 'postExcerpt',
					'label'         => esc_html__( 'Excerpt', 'gravityformsadvancedpostcreation' ),
					'type'          => 'textarea',
					'class'         => 'medium merge-tag-support mt-position-right mt-hide_all_fields',
					'default_value' => $this->get_default_field_merge_tag( 'post_excerpt' ),
					'allow_html'    => true,
				),
			);

			// Add field.
			$fields = parent::add_field_after( 'postContent', $field, $fields );

		}

		// Add post media field.
		if ( $post_type_obj && $upload_fields = $this->get_file_fields_as_choices() ) {

			// Get single file upload field choices.
			$single_choices = $this->get_file_fields_as_choices( true );

			// If post type supports thumbnails and form has single file upload fields, add Featured Image option.
			if ( post_type_supports( $post_type_obj->name, 'thumbnail' ) && ! empty( $single_choices ) ) {

				// Add placeholder choice.
				$single_choices = array_merge(
					array(
						array(
							'value' => '',
							'label' => esc_html__( 'Select a File Upload Field', 'gravityformsadvancedpostcreation' ),
						),
					),
					$single_choices
				);

				// Prepare field.
				$thumbnail_field = array(
					array(
						'name'    => 'postThumbnail',
						'label'   => esc_html__( 'Featured Image', 'gravityformsadvancedpostcreation' ),
						'type'    => 'select',
						'class'   => 'medium',
						'choices' => $single_choices,
						'tooltip' => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Featured Image', 'gravityformsadvancedpostcreation' ),
							esc_html__( "Select a file upload field to use for the post's featured image. Only file upload fields limited to a single file are permitted.", 'gravityformsadvancedpostcreation' )
						),
					),
				);

				// Add field.
				$fields = parent::add_field_after( 'disableAutoformat', $thumbnail_field, $fields );

			}

			// Prepare Media Library field.
			$media_field = array(
				array(
					'name'        => 'postMedia[]',
					'label'       => esc_html__( 'Media Library', 'gravityformsadvancedpostcreation' ),
					'type'        => 'select',
					'class'       => 'medium',
					'multiple'    => true,
					'placeholder' => esc_attr__( 'Select File Upload Fields', 'gravityformsadvancedpostcreation' ),
					'choices'     => $upload_fields,
					'tooltip'       => sprintf(
						'<h6>%s</h6>%s',
						esc_html__( 'Media Library', 'gravityformsadvancedpostcreation' ),
						esc_html__( 'Select file upload fields whose files should be uploaded to the media library and assigned to the post.', 'gravityformsadvancedpostcreation' )
					),
				),
			);

			// Determine position to insert field.
			$add_field_after = isset( $thumbnail_field ) ? 'postThumbnail' : 'postContent';

			// Add field.
			$fields = parent::add_field_after( $add_field_after, $media_field, $fields );

		}

		// Add taxonomies section.
		if ( $taxonomies ) {

			$fields['taxonomies'] = array(
				'title'      => esc_html__( 'Taxonomies', 'gravityformsadvancedpostcreation' ),
				'fields'     => $this->get_taxonomies_as_feed_settings_fields( $post_type ),
			);

		}

		return $fields;

	}

	/**
	 * Renders and initializes a post date field based on the $field array
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param array $field Field array containing the configuration options of this field.
	 * @param bool  $echo  True to echo the output to the screen, false to simply return the contents as a string.
	 *
	 * @uses   GFAddOn::get_setting()
	 * @uses   GFAddOn::settings_select()
	 * @uses   GF_Advanced_Post_Creation::settings_post_date_custom()
	 * @uses   GF_Advanced_Post_Creation::settings_post_date_field()
	 *
	 * @return string The HTML for the field
	 */
	public function settings_post_date( $field, $echo = true ) {

		// Get field value.
		$default_value = rgar( $field, 'value' ) ? rgar( $field, 'value' ) : rgar( $field, 'default_value' );
		$value         = $this->get_setting( $field['name'], $default_value );

		// Add base select.
		$html = $this->settings_select( $field, false );

		// Add form field section.
		$html .= $this->settings_post_date_field( $field, $value );

		// Add custom section.
		$html .= $this->settings_post_date_custom( $field, $value );

		if ( $echo ) {
			echo $html;
		}

		return $html;
	}

	/**
	 * Renders and initializes a form field section for a post date field based on the $field array
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param array  $field       Field array containing the configuration options of this field.
	 * @param string $field_value Value of the parent post date field.
	 *
	 * @uses   GFAddOn::settings_field_select()
	 *
	 * @return string The HTML for the field
	 */
	public function settings_post_date_field( $field, $field_value = '' ) {

		// Create the date field.
		$date_field = array(
			'name' => $field['name'] . 'FieldDate',
			'type' => 'field_select',
			'args' => array( 'input_types' => array( 'date' ) ),
		);

		// Create the time field.
		$time_field = array(
			'name' => $field['name'] . 'FieldTime',
			'type' => 'field_select',
			'args' => array( 'input_types' => array( 'time' ) ),
		);

		// Display form field options.
		return sprintf(
			'<div class="gform_advancedpostcreation_date_field"%s>%s%s</div>',
			'field' === $field_value ? ' style="display:block;"' : '',
			$this->settings_field_select( $date_field, false ),
			$this->settings_field_select( $time_field, false )
		);

	}

	/**
	 * Renders and initializes a custom value section for a post date field based on the $field array
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @param array  $field       Field array containing the configuration options of this field.
	 * @param string $field_value Value of the parent post date field.
	 *
	 * @uses GFAddOn::settings_text()
	 *
	 * @return string The HTML for the field
	 */
	public function settings_post_date_custom( $field, $field_value = '' ) {

		// Create the custom field.
		$custom_field = array(
			'name'  => $field['name'] . 'Custom',
			'type'  => 'text',
			'class' => 'medium',
		);

		// Display form field options.
		return sprintf(
			'<div class="gform_advancedpostcreation_date_custom"%s>%s</div>',
			$field_value === 'custom' ? ' style="display:block;"' : '',
			$this->settings_text( $custom_field, false )
		);

	}

	/**
	 * Validates the posted value of a post date field based on the $field array
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @param array  $field       Field array containing the configuration options of this field.
	 * @param string $field_value Submitted value.
	 *
	 * @uses GFAddOn::get_current_settings()
	 * @uses GFAddOn::set_field_error()
	 */
	public function validate_settings_post_date( $field, $field_value = '' ) {

		// Get settings.
		$settings = $this->get_current_settings();

		// If not using a date field, exit.
		if ( 'field' !== $field_value ) {
			return;
		}

		// Prepare field names to validate.
		$date_field = array( 'name' => $field['name'] . 'FieldDate' );
		$time_field = array( 'name' => $field['name'] . 'FieldTime' );

		if ( rgblank( $settings[ $date_field['name'] ] ) ) {
			$this->set_field_error( $date_field, rgar( $date_field, 'error_message' ) );
		}

		if ( rgblank(  $settings[ $time_field['name'] ] ) ) {
			$this->set_field_error( $time_field, rgar( $time_field, 'error_message' ) );
		}

	}

	/**
	 * Renders and initializes a taxonomy map field based on the $field array
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param array|object $field - Field array or object containing the configuration options of this field.
	 * @param bool         $echo  - True to echo the output to the screen, false to simply return the contents as a
	 *                            string.
	 * @uses   GFAddOn::get_setting()
	 * @uses   GFAddOn::get_field_attributes()
	 *
	 * @return string The HTML for the field
	 */
	public function settings_taxonomy_map( $field, $echo = true ) {

		// Initialize return HTML string.
		$html = '';

		// Initialize field objects for child fields.
		if ( is_object( $field ) ) {
			$key_field          = clone $field;
			$value_field        = clone $field;
			$custom_value_field = clone $field;
		} else {
			$value_field = $key_field = $custom_value_field = $field;
		}

		// Define custom placeholder.
		$custom_placeholder = 'gf_custom';

		// Get taxonomy.
		$taxonomy = get_taxonomy( $field['taxonomy'] );

		// Define key field properties.
		$key_field['name']    .= '_key';
		$key_field['class']    = 'key key_{i}';
		$key_field['choices']  = array(
			array(
				'label' => esc_html__( 'Select an Option', 'gravityformsadvancedpostcreation' ),
				'value' => '',
			),
			array(
				'label' => esc_html__( 'Field Value', 'gravityformsadvancedpostcreation' ),
				'value' => 'field',
			),
			array(
				'label' => sprintf( esc_html__( 'Assign %s', 'gravityformsadvancedpostcreation' ), $taxonomy->labels->singular_name ),
				'value' => 'term',
			),
		);

		// Define value field properties.
		$value_field['name']   .= '_custom_value';
		$value_field['class']   = 'value value_{i}';
		$value_field['choices'] = array(
			array(
				'label' => 'Select an Option',
				'value' => '',
			),
			array(
				'label' => sprintf( esc_html__( 'Add New %s', 'gravityformsadvancedpostcreation' ), $taxonomy->labels->singular_name ),
				'value' => 'gf_custom',
			),
		);

		// Define custom value field properties.
		$custom_value_field['name']        .= '_custom_value_{i}';
		$custom_value_field['class']        = 'custom_value custom_value_{i}';
		$custom_value_field['value']        = '{custom_value}';
		$custom_value_field['placeholder']  = rgars( $field, 'value_field/placeholder' ) ? $field['value_field']['placeholder'] : esc_html__( 'Custom Value', 'gravityforms' );

		// Remove unneeded field properties.
		$unneeded_props = array( 'field_map', 'key_choices', 'value_choices', 'placeholders', 'callback', 'taxonomy' );
		foreach ( $unneeded_props as $unneeded_prop ) {
			unset( $key_field[ $unneeded_prop ] );
			unset( $value_field[ $unneeded_prop ] );
			unset( $custom_value_field[ $unneeded_prop ] );
		}

		// If field failed validation, display error icon.
		if ( $this->field_failed_validation( $field ) ) {
			$html .= $this->get_error_icon( $field );
		}

		// Display hidden field containing dynamic field map value.
		$html .= $this->settings_hidden( $field, false );

		// Display map table.
		$html .= '
            <table class="settings-field-map-table" cellspacing="10px" cellpadding="0">
                <tbody class="repeater">
	                <tr>
	                    <th>' . $this->settings_select( $key_field, false ) . '</th>
	                    <th>' . $this->settings_select( $value_field, false ) . '
		                    <div class="custom-value-container">' .
		                    	$this->settings_text( $custom_value_field, false ) . '
		                    </div>
	                    </th>
						<td>
							{buttons}
						</td>
	                </tr>
                </tbody>
            </table>';

		// Get generic map limit.
		$limit = empty( $field['limit'] ) ? 0 : $field['limit'];

		// Initialize generic map via Javascript.
		$html .= "
			<script type=\"text/javascript\">
			jQuery( document ).ready( function() {
				var taxonomyMap" . esc_js( preg_replace( '/[^A-Za-z0-9_]/', '', $field['name'] ) ) . " = new GFTaxonomyMap({
					'baseURL':        '". GFCommon::get_base_url() ."',
					'taxonomy':       '". $field['taxonomy'] . "',
					'fieldId':        '". esc_attr( $field['name'] ) ."',
					'fieldName':      '". $field['name'] ."',
					'keyFieldName':   '". $key_field['name'] ."',
					'valueFieldName': '". $value_field['name'] ."',
					'formFields':     '". $this->get_taxonomy_map_form_fields() ."',
					'preloadedTerms': '". $this->get_taxonomy_map_preload_terms( $field ) ."',
					'limit':          '". $limit ."'
				});
			});
			</script>";

		// If automatic display is enabled, echo field HTML.
		if ( $echo ) {
			echo $html;
		}

		return $html;

	}

	/**
	 * Get the merge tag for first form field found matching field type.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param string $field_type Field type to search for.
	 *
	 * @uses GFAddOn::get_current_form()
	 * @uses GFAPI::get_fields_by_type()
	 *
	 * @return string
	 */
	public function get_default_field_merge_tag( $field_type = '' ) {

		// If no field type was provided, return.
		if ( rgblank( $field_type ) ) {
			return '';
		}

		// Get current form.
		$form = $this->get_current_form();

		// Get form fields for field type.
		$fields = GFAPI::get_fields_by_type( $form, $field_type );

		// If no fields were found, return.
		if ( empty( $fields ) ) {
			return '';
		}

		return '{' . $fields[0]->label . ':' . $fields[0]->id . '}';

	}

	/**
	 * Prepare post types for feed settings field.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_post_types_as_choices() {

		// Initialize choices.
		$choices = array();

		// Get default post types.
		$default_post_types = get_post_types( array( 'public' => true, '_builtin' => true ), 'objects' );

		// Add default post types.
		if ( ! empty( $default_post_types ) ) {

			// Initialize default choices.
			$default_choices = array();

			// Loop through default post types.
			foreach ( $default_post_types as $post_type ) {

				// Ignore attachment post type.
				if ( 'attachment' === $post_type->name ) {
					continue;
				}

				$default_choices[] = array(
					'label' => esc_html( $post_type->labels->singular_name ),
					'value' => esc_attr( $post_type->name ),
				);

			}

			// Add default post types to choices.
			$choices[] = array(
				'label' => esc_html__( 'WordPress Post Types', 'gravityformsadvancedpostcreation' ),
				'choices' => $default_choices,
			);

		}

		$args = array(
			'public'   => true,
			'_builtin' => false,
		);

		/**
		 * Enables the arguments used to get the custom post types to be overridden.
		 *
		 * @since 1.0
		 *
		 * @param array $args The arguments to be used with get_post_types().
		 */
		$args = apply_filters( 'gform_advancedpostcreation_args_pre_get_custom_post_types', $args );

		// Get custom post types.
		$custom_post_types = get_post_types( $args, 'objects' );

		// Add custom post types.
		if ( ! empty( $custom_post_types ) ) {

			// Initialize custom choices.
			$custom_choices = array();

			// Loop through custom post types.
			foreach ( $custom_post_types as $post_type ) {

				$custom_choices[] = array(
					'label' => esc_html( $post_type->labels->singular_name ),
					'value' => esc_attr( $post_type->name ),
				);

			}

			// Add custom post types to choices.
			$choices[] = array(
				'label' => esc_html__( 'Custom Post Types', 'gravityformsadvancedpostcreation' ),
				'choices' => $custom_choices,
			);

		}

		return $choices;

	}

	/**
	 * Prepare post statuses for feed settings field.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_post_statuses_as_choices() {

		$choices       = array();
		$post_statuses = get_post_stati( array( 'show_in_admin_status_list' => true ), 'objects' );

		foreach ( $post_statuses as $post_status ) {
			$choices[] = array(
				'label' => esc_html( $post_status->label ),
				'value' => esc_attr( $post_status->name ),
			);
		}

		return $choices;

	}

	/**
	 * Prepare post formats for feed settings field.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_post_formats_as_choices() {

		// Setup choices.
		$choices = array(
			array(
				'value' => 'standard',
				'label' => esc_html( _x( 'Standard', 'Post format' ) ),
			),
		);

		// Get post formats.
		$post_formats = get_theme_support( 'post-formats' );

		// If post formats are not defined, return choices.
		if ( ! $post_formats ) {
			return $choices;
		}

		// Get post formats array, translated strings.
		$post_formats         = rgar( $post_formats, 0 );
		$post_formats_strings = get_post_format_strings();

		// Prepare post formats to be choices.
		if ( is_array( $post_formats ) && ! empty( $post_formats ) ) {
			foreach ( $post_formats as $post_format ) {

				// Prepare label.
				$format_label = rgar( $post_formats_strings, $post_format ) ? $post_formats_strings[ $post_format ] : ucfirst( $post_format );

				$choices[] = array(
					'value' => $post_format,
					'label' => esc_html( $format_label ),
				);

			}
		}

		return $choices;

	}

	/**
	 * Prepare file upload fields for feed settings field.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param bool $single_file Return single file upload fields.
	 *
	 * @uses   GFAddOn::get_current_form()
	 * @uses   GFAPI::get_fields_by_type()
	 * @uses   GFCommon::get_label()
	 *
	 * @return array
	 */
	public function get_file_fields_as_choices( $single_file = false ) {

		// Get form.
		$form = $this->get_current_form();

		// Get file upload fields for form.
		$fields = GFAPI::get_fields_by_type( $form, array( 'fileupload' ) );

		// Setup initial choice.
		$choices = array();

		if ( ! empty( $fields ) ) {
			// Loop through File Upload fields; add to choices.
			foreach ( $fields as $field ) {

				// If single file upload only and more than one file can be uploaded, skip.
				if ( $single_file && $field->multipleFiles && ( rgblank( $field->maxFiles ) || absint( $field->maxFiles ) > 1 ) ) {
					continue;
				}

				// Add field as choice.
				$choices[] = array(
					'value' => $field->id,
					'label' => GFCommon::get_label( $field ),
				);

			}
		}

		/**
		 * Allows the available choices for the media settings to be overridden on the feed configuration page.
		 *
		 * @since 1.0
		 *
		 * @param array $choices     The file fields as choices.
		 * @param array $form        The current form.
		 * @param bool  $single_file Indicates if only single file upload fields should be returned.
		 */
		$choices = apply_filters( 'gform_advancedpostcreation_file_fields_choices', $choices, $form, $single_file );

		return $choices;

	}

	/**
	 * Prepare fields for comment meta field mapping.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @uses GFFormsModel::get_custom_field_names()
	 *
	 * @return array
	 */
	public function get_post_meta_field_map() {

		// Setup meta fields array.
		$meta_fields = array(
			array(
				'label' => esc_html__( 'Select a Custom Field Name', 'gravityformsadvancedpostcreation' ),
				'value' => '',
			),
		);

		// Get existing post meta keys.
		$meta_keys = GFFormsModel::get_custom_field_names();

		// If no meta keys exist, return an empty array.
		if ( empty( $meta_keys ) ) {
			return array();
		}

		// Add post meta keys to the meta fields array.
		foreach ( $meta_keys as $meta_key ) {
			$meta_fields[] = array(
				'label' => esc_html( $meta_key ),
				'value' => esc_attr( $meta_key ),
			);
		}

		// Add custom option.
		$meta_fields[] = array(
			'label' => esc_html__( 'Add New Custom Field Name', 'gravityformsadvancedpostcreation' ),
			'value' => 'gf_custom',
		);

		return $meta_fields;

	}

	/**
	 * Prepare post authors for feed settings field.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @uses GFAddOn::get_setting()
	 *
	 * @return array
	 */
	public function get_post_authors_as_choices() {

		// Get current value.
		$value = $this->get_setting( 'postAuthor' );

		// Initialize choices.
		$choices = array(
			array(
				'label' => esc_html__( 'Logged In User', 'gravityformsadvancedpostcreation' ),
				'value' => 'logged-in-user',
			),
		);

		// If current value is empty or not numeric, return choices.
		if ( rgblank( $value ) || ! is_numeric( $value ) ) {
			return $choices;
		}

		// Get user.
		$user = get_user_by( 'id', $value );

		// If user exists, add it as a choice.
		if ( $user ) {

			 $choices[] = array(
				 'label' => esc_html( $user->display_name ),
				 'value' => esc_attr( $user->ID ),
			 );

		}

		return $choices;

	}

	/**
	 * Get suggestions for post author field.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @uses WP_User_Query::get_results()
	 */
	public function get_post_author_search_results() {

		// Verify nonce.
		if ( false === wp_verify_nonce( rgget( 'nonce' ), 'gform_advancedpostcreation_author_search' ) ) {
			wp_send_json( array( 'message' => esc_html__( 'Access denied.', 'gravityformsadvancedpostcreation' ), 'results' => array() ) );
		}

		// If user is not authorized, exit.
		if ( ! GFCommon::current_user_can_any( $this->_capabilities_settings_page ) ) {
			wp_send_json( array( 'message' => esc_html__( 'Access denied.', 'gravityformsadvancedpostcreation' ), 'results' => array() ) );
		}

		// Get needed variables.
		$query = sanitize_text_field( rgget( 'query' ) );

		// Initialize results array.
		$results = array(
			array(
				'id'   => 'logged-in-user',
				'text' => esc_html__( 'Logged In User', 'gravityformsadvancedpostcreation' ),
			),
		);

		// Create user query.
		$users = new WP_User_Query(
			array(
				'search'         => $query,
				'search_columns' => array( 'user_login', 'user_email', 'user_nicename' ),
			)
		);

		// Get users.
		$users = $users->get_results();

		// Add matching users.
		if ( ! empty( $users ) ) {

			foreach ( $users as $user ) {

				// Add field to results.
				$results[] = array(
					'id'   => esc_html( $user->ID ),
					'text' => esc_html( $user->display_name ),
				);

			}

		}

		// Return results.
		wp_send_json( array( 'results' => $results ) );

	}

	/**
	 * Prepare feed settings fields for taxonomies registered to post type.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param  string $post_type Post type.
	 *
	 * @return array
	 */
	public function get_taxonomies_as_feed_settings_fields( $post_type ) {

		// Setup fields array.
		$fields = array();

		// Get the taxonomies.
		$taxonomies = get_object_taxonomies( $post_type, 'objects' );

		// Prepare fields.
		foreach ( $taxonomies as $taxonomy ) {

			// Ignore post format taxonomy.
			if ( $taxonomy->name === 'post_format' ) {
				continue;
			}

			// Add field.
			$fields[] = array(
				'name'     => 'postTaxonomy_' . $taxonomy->name,
				'label'    => $taxonomy->label,
				'type'     => 'taxonomy_map',
				'class'    => 'medium',
				'taxonomy' => $taxonomy->name,
				'tooltip'       => sprintf(
					'<h6>%s</h6>%s',
					esc_html( $taxonomy->label ),
					esc_html__( 'Assign terms to the post using a field value, an existing term or by adding a new term.', 'gravityformsadvancedpostcreation' )
				),

			);

		}

		// Return fields.
		return $fields;

	}

	/**
	 * Get form fields for taxonomy map settings field.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @uses GFAddOn::get_current_form()
	 *
	 * @return string
	 */
	public function get_taxonomy_map_form_fields() {

		// Initialize return array.
		$fields = array();

		// Get current form.
		$form = $this->get_current_form();

		// Loop through form fields.
		foreach ( $form['fields'] as $field ) {

			// Add field to return array.
			$fields[] = array(
				'value' => esc_attr( $field->id ),
				'label' => esc_html( $field->label ),
				'type'  => esc_html( $field->type ),
			);

		}

		return json_encode( $fields );

	}

	/**
	 * Get term names for taxonomy map mappings.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param array$field The current settings field.
	 *
	 * @uses GFAddOn::get_current_form()
	 * @uses GFAddOn::get_setting()
	 *
	 * @return string
	 */
	public function get_taxonomy_map_preload_terms( $field ) {

		// Initialize return array.
		$terms = array();

		// Get current mappings.
		$mappings = $this->get_setting( $field['name'] );

		// If taxonomy mappings exist, preload values.
		if ( ! empty( $mappings ) ) {

			// Loop through mappings.
			foreach ( $mappings as $mapping ) {

				// If this is not a term mapping, skip it.
				if ( 'term' !== $mapping['key'] ) {
					continue;
				}

				// Get term.
				$term = get_term_by( 'slug', $mapping['value'], $field['taxonomy'] );

				// If term exists, add it to the preload array.
				if ( $term ) {
					$terms[ $term->slug ] = esc_html( $term->name );
				}

			}

		}

		return json_encode( $terms );

	}

	/**
	 * Get suggestions for taxonomy map existing term field.
	 *
	 * @since  1.0
	 * @access public
	 */
	public function get_taxonomy_map_search_results() {

		// Verify nonce.
		if ( false === wp_verify_nonce( rgget( 'nonce' ), 'gform_advancedpostcreation_taxonomy_search' ) ) {
			wp_send_json( array( 'message' => esc_html__( 'Access denied.', 'gravityformsadvancedpostcreation' ), 'results' => array() ) );
		}

		// If user is not authorized, exit.
		if ( ! GFCommon::current_user_can_any( $this->_capabilities_settings_page ) ) {
			wp_send_json( array( 'message' => esc_html__( 'Access denied.', 'gravityformsadvancedpostcreation' ), 'results' => array() ) );
		}

		// Get needed variables.
		$taxonomy = sanitize_text_field( rgget( 'taxonomy' ) );
		$query    = sanitize_text_field( rgget( 'query' ) );

		// Get taxonomy object.
		$taxonomy_object = get_taxonomy( $taxonomy );

		// Initialize results array.
		$results = array();

		// Get terms for taxonomy.
		$terms = get_terms(
			array(
				'hide_empty' => false,
				'name__like' => $query,
				'taxonomy'   => $taxonomy,
			)
		);

		// Add matching terms.
		if ( ! empty( $terms ) ) {

			foreach ( $terms as $term ) {

				// Add field to results.
				$results[] = array(
					'id'   => esc_html( $term->slug ),
					'text' => wp_strip_all_tags( $term->name ),
				);

			}

		}

		// Add custom option.
		$results[] = array(
			'id'   => 'gf_custom',
			'text' => sprintf( esc_html__( 'Add New %s', 'gravityformsadvancedpostcreation' ), $taxonomy_object->labels->singular_name ),
		);

		// Return results.
		wp_send_json( array( 'results' => $results ) );

	}

	/**
	 * Return the plugin's icon for the plugin/form settings menu.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_menu_icon() {
		return 'gform-icon--advanced-post-creation';
	}


	// # FEED LIST -----------------------------------------------------------------------------------------------------

	/**
	 * Setup columns for feed list table.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return array
	 */
	public function feed_list_columns() {

		return array(
			'feedName'   => esc_html__( 'Name', 'gravityformsadvancedpostcreation' ),
			'postType'   => esc_html__( 'Post Type', 'gravityformsadvancedpostcreation' ),
			'postStatus' => esc_html__( 'Status', 'gravityformsadvancedpostcreation' ),
		);

	}

	/**
	 * Get post type for feed list table.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param array $feed The current Feed object.
	 *
	 * @return string
	 */
	public function get_column_value_postType( $feed = array() ) {

		// If no post type is selected, return.
		if ( ! rgars( $feed, 'meta/postType' ) ) {
			return esc_html__( 'Not Selected', 'gravityformsadvancedpostcreation' );
		}

		// Get post type object.
		$post_type = get_post_type_object( $feed['meta']['postType'] );

		return $post_type ? esc_html( $post_type->labels->singular_name ) : esc_html__( 'Not Available', 'gravityformsadvancedpostcreation' );

	}

	/**
	 * Get post status for feed list table.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param array $feed The current Feed object.
	 *
	 * @return string
	 */
	public function get_column_value_postStatus( $feed = array() ) {

		// If no post status is selected, return.
		if ( ! rgars( $feed, 'meta/postStatus' ) ) {
			return esc_html__( 'Not Selected', 'gravityformsadvancedpostcreation' );
		}

		// Get post status object.
		$post_status = get_post_status_object( $feed['meta']['postStatus'] );

		return $post_status ? esc_html( $post_status->label ) : esc_html__( 'Not Available', 'gravityformsadvancedpostcreation' );

	}

	/**
	 * Enable feed duplication.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param string $id Feed ID requesting duplication.
	 *
	 * @return bool
	 */
	public function can_duplicate_feed( $id ) {

		return true;

	}





	// # FORM SUBMISSION -----------------------------------------------------------------------------------------------

	/**
	 * Disable Gravity Forms post creation if an applicable feed exists for form.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param bool  $is_disabled If post creation should be disabled.
	 * @param array $form        The current Form object.
	 * @param array $entry       The current Entry object.
	 *
	 * @uses   GFFeedAddOn::get_single_submission_feed()
	 *
	 * @return bool
	 */
	public function disable_core_post_creation( $is_disabled, $form, $entry ) {

		return $this->get_single_submission_feed( $entry, $form ) ? true : $is_disabled;

	}

	/**
	 * Process the feed.
	 *
	 * @since  1.0
	 * @since  1.5 Updated return value for consistency with other add-ons, so the framework can save the feed status to the entry meta.
	 *
	 * @access public
	 *
	 * @param array $feed  The Feed object to be processed.
	 * @param array $entry The Entry object currently being processed.
	 * @param array $form  The Form object currently being processed.
	 *
	 * @return array|WP_Error
	 */
	public function process_feed( $feed, $entry, $form ) {

		return $this->create_post( $feed, $entry, $form );

	}





	// # POST CREATION -------------------------------------------------------------------------------------------------

	/**
	 * Create post from post creation feed.
	 *
	 * @since  1.0
	 * @since  1.5 Updated return value for consistency with other add-ons, so the framework can save the feed status to the entry meta.
	 *
	 * @access public
	 *
	 * @param array $feed  The feed object to be processed.
	 * @param array $entry The entry object currently being processed.
	 * @param array $form  The form object currently being processed.
	 *
	 * @return array|WP_Error
	 */
	public function create_post( $feed, $entry, $form ) {

		// Set current feed ID.
		$this->_current_feed = $feed['id'];
		$this->set_post_author( null, $feed, $entry );

		// Initialize uploaded files array.
		$this->set_current_media();

		// Prepare post object.
		$post = array(
			'post_status'    => $feed['meta']['postStatus'],
			'post_type'      => $feed['meta']['postType'],
			'post_title'     => $this->get_post_title( $feed, $entry, $form ),
			'post_date_gmt'  => rgar( $entry, 'date_created' ),
			'comment_status' => rgars( $feed, 'meta/postComments' ) ? 'open' : 'closed',
			'ping_status'    => rgars( $feed, 'meta/postPingbacks' ) ? 'open' : 'closed',
		);

		// Create base post object.
		$post_id = wp_insert_post( $post, true );

		// If post could not be created, exit.
		if ( is_wp_error( $post_id ) ) {

			// Log that post was not created.
			$this->add_feed_error( 'Could not create base post object: ' . $post_id->get_error_message(), $feed, $entry, $form );

			return $post_id;

		} else {

			// Add post ID to post object.
			$post['ID'] = $post_id;

			// Add the form ID so it is available for GFFormsModel::copy_post_image().
			update_post_meta( $post['ID'], '_gform-form-id', $form['id'] );

		}

		// Added uploaded files to Media Library.
		$this->maybe_handle_post_media( $post['ID'], $feed, $entry );

		// Set standard post data.
		$post = $this->set_post_data( $post, $feed, $entry, $form );

		/**
		 * Modify the post object to be created.
		 *
		 * @since 1.0
		 *
		 * @param array $post  The post object to be created.
		 * @param array $feed  The current Feed object.
		 * @param array $entry The current Entry object.
		 * @param array $form  The current Form object.
		 */
		$post = gf_apply_filters( array( 'gform_advancedpostcreation_post', $form['id'] ), $post, $feed, $entry, $form );

		// Save full post object.
		$updated_post = wp_update_post( $post, true );

		// If post could not be created, exit.
		if ( is_wp_error( $updated_post ) ) {

			// Log that post was not created.
			$this->add_feed_error( 'Could not create post object: ' . $updated_post->get_error_message(), $feed, $entry, $form );

			return $updated_post;

		} else {

			// Log that post was created.
			$this->log_debug( __METHOD__ . '(): Post was created with an ID of ' . $post['ID'] . '.' );
			$this->add_note( rgar( $entry, 'id' ), sprintf( esc_html__( 'Post created: %d.', 'gravityformsadvancedpostcreation' ), $post['ID'] ), 'success' );

			// Add entry and feed ID to post meta.
			update_post_meta( $post['ID'], '_' . $this->_slug . '_entry_id', $entry['id'] );
			update_post_meta( $post['ID'], '_' . $this->_slug . '_feed_id', $feed['id'] );

		}

		// Set post format.
		if ( rgars( $feed, 'meta/postFormat' ) ) {
			$this->log_debug( __METHOD__ . "(): Setting post format for post ID {$post['ID']} to: " . rgars( $feed, 'meta/postFormat' ) );
			set_post_format( $post['ID'], rgars( $feed, 'meta/postFormat' ) );
		}

		$this->maybe_set_post_thumbnail( $post['ID'], $feed, $entry, $form );
		$this->maybe_set_post_meta( $post['ID'], $feed, $entry, $form );
		$this->maybe_set_post_taxonomies( $post['ID'], $feed, $entry, $form );

		// Get entry post ID meta.
		$entry_post_ids = gform_get_meta( $entry['id'], $this->_slug . '_post_id' );

		// If entry post ID meta is not an array, set it to an array.
		if ( ! is_array( $entry_post_ids ) ) {
			$entry_post_ids = array();
		}

		// Add post ID to array.
		$entry_post_ids[] = array(
			'post_id' => $post['ID'],
			'feed_id' => $feed['id'],
			'media'   => $this->_current_media,
		);

		// Save entry meta.
		gform_update_meta( $entry['id'], $this->_slug . '_post_id', $entry_post_ids );

		/**
		 * Run action after post has been created.
		 *
		 * @since 1.0
		 *
		 * @param int   $post_id  New post ID.
		 * @param array $feed     The current Feed object.
		 * @param array $entry    The current Entry object.
		 * @param array $form     The current Form object.
		 */
		gf_do_action( array( 'gform_advancedpostcreation_post_after_creation', $form['id'] ), $post['ID'], $feed, $entry, $form );

		return $entry;

	}

	/**
	 * Updates a post.
	 *
	 * @since 1.0
	 * @since 1.5 Updated to return the WP_error from wp_update_post().
	 *
	 * @param integer|string $post_id The ID of the post being updated.
	 * @param array          $feed    The feed being processed.
	 * @param array          $entry   The entry associated with the post being updated.
	 * @param array          $form    The form object.
	 *
	 * @return bool|WP_Error
	 */
	public function update_post( $post_id, $feed, $entry, $form ) {
		require_once 'includes/class-post-update-handler.php';
		$update_handler = new Post_Update_Handler( $this, $post_id, $feed, $entry, $form );
		return $update_handler->update();
	}

	/**
	 * Prepare post content.
	 *   Detect uploaded files used in content, add to media library, replace URLs.
	 *
	 * @param array $feed    The current Feed object.
	 * @param array $entry   The current Entry object.
	 * @param array $form    The current Form object.
	 * @param int   $post_id The WP_Post ID.
	 *
	 * @uses GFAPI::get_fields_by_type()
	 * @uses GFCommon::replace_variables()
	 * @uses GFFormsModel::media_handle_upload()
	 *
	 * @return string
	 */
	public function prepare_post_content( $feed, $entry, $form, $post_id = 0 ) {

		// Get post content.
		$content = $feed['meta']['postContent'];

		// Get all field merge tags.
		preg_match_all( '/{[^{]*?:(\d+(\.\d+)?)(:(.*?))?}/mi', $content, $matches, PREG_SET_ORDER );

		// Loop through found field merge tags, replace file upload references.
		if ( is_array( $matches ) ) {

			foreach ( $matches as $match ) {

				// Determine if this is an {apc_media} merge tag.
				$is_apc_media_tag = strpos( $match[0], '{apc_media' ) === 0;

				// Get input ID from merge tag.
				$input_id = $match[1];

				// Get field.
				$field_object = GFAPI::get_field( $form, $input_id );

				// If field is not a file upload field, skip.
				if ( ! $field_object || 'fileupload' !== $field_object->get_input_type() ) {
					continue;
				}

				// Get value for field.
				$field_value = rgar( $entry, $field_object->id );

				// If no files were uploaded to field, skip.
				if ( ! $field_value ) {
					continue;
				}

				// Handle multiple file uploads separately.
				if ( $field_object->multipleFiles ) {

					// Initialize array for uploaded files.
					$media_urls = array();

					// Decode string.
					$field_value = $this->maybe_decode_json( $field_value );

					// Loop through files, upload media and replace URLs.
					foreach ( $field_value as $file_url ) {

						// Upload file to media library.
						$media_id = $this->media_handle_upload( $file_url, $post_id );

						// If file could not be uploaded to media library, skip.
						if ( is_wp_error( $media_id ) ) {
							continue;
						}

						// Get media URL.
						$media_url = wp_get_attachment_url( $media_id );

						// Add media URL to array.
						$media_urls[] = $media_url;

					}

					// Add media URLs to content.
					if ( ! $is_apc_media_tag ) {
						$content = str_replace( $match[0], GFCommon::implode_non_blank( ', ', $media_urls ), $content );
					}

				} else {

					// Get file URL.
					$file_url = $field_value;

					// Upload file to media library.
					$media_id = $this->media_handle_upload( $file_url, $post_id );

					// If file could not be uploaded to media library, skip.
					if ( is_wp_error( $media_id ) ) {
						continue;
					}

					// Get media URL.
					$media_url = wp_get_attachment_url( $media_id );

					// Replace URL in post content.
					if ( ! $is_apc_media_tag ) {
						$content = str_replace( $match[0], $media_url, $content );
					}

				}

			}

		}

		// Replace remaining variables in post content.
		$nl2br   = rgars( $feed, 'meta/disableAutoformat' ) === '0' ? true : false;
		$content = GFCommon::replace_variables( $content, $form, $entry, false, false, $nl2br );

		return $content;

	}

	/**
	 * Get taxonomies mapped to a feed and their terms.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param  array $feed Feed object.
	 * @param  array $entry Entry object.
	 * @param  array $form Form object.
	 *
	 * @uses GFAddOn::get_field_value()
	 *
	 * @return array
	 */
	public function get_mapped_taxonomies( $feed, $entry, $form ) {

		// Initialize return array.
		$taxonomies = array();

		/**
		 * Filter the separator character used when separating term names.
		 *
		 * @since 1.0
		 *
		 * @param string $separator The separator to be filtered.
		 */
		$separator = apply_filters( 'gform_advancedpostcreation_term_separator', ',' );

		// Loop through feed meta.
		foreach ( $feed['meta'] as $key => $mappings ) {

			// If key does not start with "postTaxonomy_", skip it.
			if ( strpos( $key, 'postTaxonomy_' ) !== 0 ) {
				continue;
			}

			// If there are no mappings, skip it.
			if ( rgblank( $mappings ) ) {
				continue;
			}

			// Get the taxonomy.
			$taxonomy = str_replace( 'postTaxonomy_', null, $key );

			// Initialize terms array.
			$terms = array();

			// Loop through mappings.
			foreach ( $mappings as $mapping ) {
				$term_ids = $this->get_taxonomy_mapping_term_ids( $mapping, $taxonomy, $separator, $form, $entry );

				if ( ! empty( $term_ids ) ) {
					$terms = array_merge( $terms, $term_ids );
				}
			}

			// Add taxonomy to return array.
			if ( ! empty( $terms ) ) {
				$taxonomies[ $taxonomy ] = $terms;
			}

		}

		return $taxonomies;

	}

	/**
	 * Returns an array of term IDs for the given taxonomy mapping.
	 *
	 * @since 1.0
	 *
	 * @param array  $mapping   The properties of the taxonomy mapping to be processed.
	 * @param string $taxonomy  The taxonomy name.
	 * @param string $separator The character used when separating term names in the value.
	 * @param array  $form      The form currently being processed.
	 * @param array  $entry     The entry currently being processed.
	 *
	 * @return array
	 */
	public function get_taxonomy_mapping_term_ids( $mapping, $taxonomy, $separator, $form, $entry ) {
		$term_ids = array();

		if ( empty( $mapping['key'] ) || empty( $mapping['value'] ) ) {
			return $term_ids;
		}

		$mapping_value = $mapping['value'];

		switch ( $mapping['key'] ) {

			case 'field':

				$mapped_field  = GFAPI::get_field( $form, $mapping_value );
				$mapping_value = $this->get_field_value( $form, $entry, $mapping_value );

				if ( $mapped_field && 'post_category' === $mapped_field->type ) {
					if ( empty( $mapping_value ) ) {
						return $term_ids;
					}

					return GFCommon::prepare_post_category_value( $mapping_value, $mapped_field, 'conditional_logic' );

				}

				break;

			case 'term':

				if ( 'gf_custom' === $mapping_value ) {

					$mapping_value = GFCommon::replace_variables( rgar( $mapping, 'custom_value' ), $form, $entry, false, false, false, 'text' );

				} else {

					$term = get_term_by( 'slug', $mapping_value, $taxonomy );

					if ( $term ) {
						$term_ids[] = $term->term_id;
					}

					return $term_ids;

				}

				break;

			default:

				if ( 'gf_custom' === $mapping_value ) {
					$mapping_value = GFCommon::replace_variables( rgar( $mapping, 'custom_value' ), $form, $entry, false, false, false, 'text' );
				}

				break;

		}

		if ( ! empty( $mapping_value ) ) {

			$values = is_array( $mapping_value ) ? $mapping_value : array_map( 'trim', explode( $separator, $mapping_value ) );

			/**
			 * Allows users to modify which field is used when looking up terms via `get_term_by()`. Defaults to `name`, but can be
			 * any of: 'slug', 'name', 'term_id' (or 'id', 'ID'), or 'term_taxonomy_id'
			 *
			 * @since 1.3
			 *
			 * @param string $field
			 * @param string $taxonomy
			 * @param array  $values
			 *
			 * @return string
			 */
			$mapping_field = apply_filters( 'gform_advancedpostcreation_taxonomy_mapping_field', 'name', $taxonomy, $values );
			foreach ( $values as $val ) {
				$existing_term = get_term_by( $mapping_field, $val, $taxonomy );
				if ( $existing_term ) {
					$term_ids[] = $existing_term->term_id;
				} else {
					$new_term = wp_insert_term( $val, $taxonomy );
					if ( ! is_wp_error( $new_term ) ) {
						$term_ids[] = $new_term['term_id'];
					}
				}
			}

		}

		return $term_ids;
	}

	/**
	 * Upload file to media library.
	 *
	 * @since  1.0
	 * @access private
	 *
	 * @param string $file_url URL to file.
	 * @param int    $post_id  Post ID to attach media to.
	 *
	 * @uses   GFFormsModel::media_handle_upload()
	 *
	 * @return int|WP_Error
	 */
	private function media_handle_upload( $file_url, $post_id ) {

		// If file was already uploaded, return it.
		if ( isset( $this->_current_media[ $file_url ] ) ) {
			return $this->_current_media[ $file_url ];
		}

		// Upload file.
		$media_id = GFFormsModel::media_handle_upload( $file_url, $post_id, array( 'post_author' => $this->get_post_author() ) );

		// If file could not be uploaded, return.
		if ( is_wp_error( $media_id ) ) {
			return $media_id;
		}

		// Cache media ID.
		$this->_current_media[ $file_url ] = $media_id;

		return $media_id;

	}

	/**
	 * Sets the _current_media property.
	 *
	 * @since 1.0
	 *
	 * @param array $media An array of files attached to the current post, with the file entry URL as the key to the media ID. Defaults to an empty array.
	 */
	public function set_current_media( $media = array() ) {
		$this->_current_media = $media;
	}

	/**
	 * Returns the value of the _current_media property. An array of files attached to the current post, with the file entry URL as the key to the media ID.
	 *
	 * @since 1.0
	 *
	 * return array
	 */
	public function get_current_media() {
		return $this->_current_media;
	}

	/**
	 * Sets the _post_author property.
	 *
	 * @since 1.0
	 *
	 * @param null|int $id    Null or the ID of the user to be assigned as the post author.
	 * @param array    $feed  The feed being processed.
	 * @param array    $entry The entry being processed.
	 */
	public function set_post_author( $id, $feed = array(), $entry = array() ) {
		if ( ! empty( $id ) ) {
			$this->_post_author = $id;
		} elseif ( 'logged-in-user' === rgars( $feed, 'meta/postAuthor' ) ) {
			$this->_post_author = rgar( $entry, 'created_by' );
		} else {
			$this->_post_author = rgars( $feed, 'meta/postAuthor' );
		}
	}

	/**
	 * Returns the value of the _post_author property. Null or the ID of the user to be assigned as the post author.
	 *
	 * @since 1.0
	 *
	 * return null|int
	 */
	public function get_post_author() {
		return $this->_post_author;
	}

	/**
	 * Returns the post title.
	 *
	 * @since 1.0
	 *
	 * @param array $feed  The feed being processed.
	 * @param array $entry The entry being processed.
	 * @param array $form  The form being processed.
	 *
	 * @return string
	 */
	public function get_post_title( $feed, $entry, $form ) {
		return GFCommon::replace_variables( $feed['meta']['postTitle'], $form, $entry, false, false, false, 'text' );
	}

	/**
	 * Sets the post content, password, excerpt, author, and date.
	 *
	 * @since 1.0
	 *
	 * @param array $post  The current post data.
	 * @param array $feed  The feed being processed.
	 * @param array $entry The entry being processed.
	 * @param array $form  The form being processed.
	 *
	 * @return array
	 */
	public function set_post_data( $post, $feed, $entry, $form ) {
		// Set the post content.
		$post['post_content'] = $this->prepare_post_content( $feed, $entry, $form, $post['ID'] );

		// Set the post password.
		if ( 'password-protected' === $feed['meta']['postVisibility'] ) {
			$post['post_password'] = GFCommon::replace_variables( $feed['meta']['postPassword'], $form, $entry, false, false, false, 'text' );
		}

		// Set the post excerpt.
		if ( rgars( $feed, 'meta/postExcerpt' ) ) {
			$post['post_excerpt'] = GFCommon::replace_variables( $feed['meta']['postExcerpt'], $form, $entry );
		}

		// Set the post author.
		$post['post_author'] = $this->get_post_author();

		// Set the post date.
		switch ( rgars( $feed, 'meta/postDate' ) ) {
			case 'custom':
				$post['post_date']     = $this->get_formatted_date( rgars( $feed, 'meta/postDateCustom' ) );
				$post['post_date_gmt'] = get_gmt_from_date( $post['post_date'] );
				break;
			case 'entry':
				$post['post_date_gmt'] = rgar( $entry, 'date_created' );
				break;
			case 'field':
				$date = $this->get_field_value( $form, $entry, $feed['meta']['postDateFieldDate'] ) . ' ' . $this->get_field_value( $form, $entry, $feed['meta']['postDateFieldTime'] );

				$post['post_date']     = $this->get_formatted_date( $date );
				$post['post_date_gmt'] = get_gmt_from_date( $date );
				break;
		}

		return $post;
	}

	/**
	 * Get the formatted date.
	 *
	 * @since 1.0
	 *
	 * @param string $date Date data.
	 *
	 * @return string|false
	 */
	private function get_formatted_date( $date ) {
		require_once __DIR__ . '/includes/helpers/wp-timezone.php';

		try {
			if ( rgblank( trim( $date ) ) ) {
				$date = current_time( 'mysql' );
			}

			return ( new DateTime( $date, wp_timezone() ) )->format( 'Y-m-d H:i:s' );
		} catch ( Exception $e ) {
			// If we can't parse the date because it's invalid, set the post date to now.
			return ( new DateTime( 'now', wp_timezone() ) )->format( 'Y-m-d H:i:s' );
		}
	}

	/**
	 * Sets the post thumbnail.
	 *
	 * @since 1.0
	 *
	 * @param int   $post_id The ID of the post the thumbnail is to be set for.
	 * @param array $feed    The feed being processed.
	 * @param array $entry   The entry being processed.
	 * @param array $form    The form being processed.
	 */
	public function maybe_set_post_thumbnail( $post_id, $feed, $entry, $form ) {

		$field_id = rgars( $feed, 'meta/postThumbnail' );
		if ( ! $field_id ) {
			return;
		}

		$file_url = $this->get_field_value( $form, $entry, $field_id );
		$tag_args = array( 'gform_advancedpostcreation_file_url_pre_set_post_thumbnail', absint( rgar( $form, 'id' ) ), $field_id );

		if ( gf_has_filters( $tag_args ) ) {
			$this->log_debug( __METHOD__ . '(): Executing functions hooked to gform_advancedpostcreation_file_url_pre_set_post_thumbnail.' );

			/**
			 * Allows the URL of the file to be added to the media library and set as the post thumbnail to be overridden.
			 *
			 * @since 1.3
			 *
			 * @param string     $file_url The URL of the file to be added to the media library and set as the post thumbnail.
			 * @param int|string $field_id The form field ID or entry meta key mapped to the feed postThumbnail setting.
			 * @param array      $feed     The feed being processed.
			 * @param array      $entry    The entry being processed.
			 * @param array      $form     The form being processed.
			 * @param int        $post_id  The ID of the post the thumbnail is to be set for.
			 */
			$file_url = gf_apply_filters( $tag_args, $file_url, $field_id, $feed, $entry, $form, $post_id );
		}

		if ( ! $file_url ) {
			$this->log_debug( __METHOD__ . "(): No file uploaded for field ID {$field_id}." );
			return;
		}

		$image_id = $this->media_handle_upload( $file_url, $post_id );
		if ( is_wp_error( $image_id ) ) {
			$this->log_error( __METHOD__ . "(): Unable to set featured image for post ID {$post_id} " . $image_id->get_error_message() );
			return;
		}

		$image_exts = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'webp' );
		$file_type  = wp_check_filetype( $file_url );

		if ( in_array( strtolower( $file_type['ext'] ), $image_exts ) ) {
			$this->log_debug( __METHOD__ . "(): Setting image ID {$image_id} as featured image for post ID {$post_id}." );
			set_post_thumbnail( $post_id, $image_id );
		} else {
			$this->log_error( __METHOD__ . "(): Due to using an unsupported file extension, unable to set image ID {$image_id} as featured image for post ID {$post_id}." );
		}

	}

	/**
	 * Triggers processing of any additional media for the post.
	 *
	 * @since 1.0
	 *
	 * @param int   $post_id The ID of the post the media is to be attached to.
	 * @param array $feed    The feed being processed.
	 * @param array $entry   The entry being processed.
	 */
	public function maybe_handle_post_media( $post_id, $feed, $entry ) {

		$post_media_fields = rgars( $feed, 'meta/postMedia' );
		if ( empty( $post_media_fields ) ) {
			return;
		}

		// Loop through file upload fields, upload files to Media Library.
		foreach ( $post_media_fields as $upload_field ) {

			// Get uploaded files.
			$uploaded_files = rgar( $entry, $upload_field );
			$uploaded_files = $this->maybe_decode_json( $uploaded_files );

			// If no files were uploaded to field, continue.
			if ( empty( $uploaded_files ) ) {
				$this->log_debug( __METHOD__ . "(): No files uploaded for field ID {$upload_field}." );
				continue;
			}

			// Convert uploaded files to array.
			if ( ! is_array( $uploaded_files ) ) {
				$uploaded_files = array( $uploaded_files );
			}

			// Loop through uploaded files, upload to Media Library.
			foreach ( $uploaded_files as $uploaded_file ) {

				if ( ! $uploaded_file ) {
					continue;
				}

				// Upload file.
				$this->log_debug( __METHOD__ . "(): Adding file to media library: {$uploaded_file}" );
				$this->media_handle_upload( $uploaded_file, $post_id );

			}

		}

	}

	/**
	 * Sets the post meta.
	 *
	 * @since 1.0
	 *
	 * @param int   $post_id The ID of the post the meta is to be set for.
	 * @param array $feed    The feed being processed.
	 * @param array $entry   The entry being processed.
	 * @param array $form    The form being processed.
	 */
	public function maybe_set_post_meta( $post_id, $feed, $entry, $form ) {

		// Get mapped meta fields.
		$meta_fields = $this->get_generic_map_fields( $feed, 'postMetaFields', $form, $entry );
		if ( empty( $meta_fields ) ) {
			return;
		}

		foreach ( $meta_fields as $meta_key => $meta_value ) {

			// If meta value is empty, skip it.
			if ( rgblank( $meta_value ) ) {
				$this->log_debug( __METHOD__ . "(): Empty value for Custom Field {$meta_key}" );
				continue;
			}

			// Process merge tags.
			if ( GFCommon::has_merge_tag( $meta_value ) ) {
				$meta_value = GFCommon::replace_variables( $meta_value, $form, $entry );
			}

			// Add post meta.
			$this->log_debug( __METHOD__ . "(): Adding custom field {$meta_key} with value {$meta_value}" );
			add_post_meta( $post_id, $meta_key, $meta_value );

		}

	}

	/**
	 * Sets the taxonomies for the post.
	 *
	 * @since 1.0
	 *
	 * @param int   $post_id The ID of the post the taxonomies are to be set for.
	 * @param array $feed    The feed being processed.
	 * @param array $entry   The entry being processed.
	 * @param array $form    The form being processed.
	 */
	public function maybe_set_post_taxonomies( $post_id, $feed, $entry, $form ) {

		// Get mapped terms.
		$mapped_terms = $this->get_mapped_taxonomies( $feed, $entry, $form );
		if ( empty( $mapped_terms ) ) {
			return;
		}

		// Loop through taxonomies.
		foreach ( $mapped_terms as $taxonomy => $term_ids ) {

			// Ensure term IDs are integers.
			$term_ids = array_map( 'intval', $term_ids );

			// Set the terms.
			$this->log_debug( __METHOD__ . "(): Adding terms for taxonomy {$taxonomy} in post ID {$post_id}: " . print_r( $term_ids, true ) );
			wp_set_post_terms( $post_id, $term_ids, $taxonomy );

		}

	}

	/**
	 * Update post author when user is registered with User Registration Add-On.
	 *
	 * @since 1.0
	 *
	 * @param int   $user_id The new user ID.
	 * @param array $ur_feed The User Registration Feed object.
	 * @param array $entry   The current Entry object.
	 */
	public function action_gform_user_registered( $user_id, $ur_feed, $entry ) {

		// If User Registration feed does not have Set Post Author enabled, exit.
		if ( ! rgars( $ur_feed, 'meta/setPostAuthor' ) ) {
			return;
		}

		// Get feeds for form.
		$feeds = $this->get_feeds( $entry['form_id'] );

		// If there are no feeds for this form, exit.
		if ( empty( $feeds ) ) {
			return;
		}

		// Get created post meta.
		$created_posts = gform_get_meta( $entry['id'], $this->_slug . '_post_id' );

		// If no posts were created for this form, exit.
		if ( ! $created_posts ) {
			return;
		}

		// Loop through created posts, assign to new user.
		foreach ( $created_posts as $created_post ) {
			// Update post data.
			$this->log_debug( __METHOD__ . "(): Setting user ID {$user_id} as author for post ID {$created_post['post_id']}." );
			$updated = wp_update_post(
				array(
					'ID'          => $created_post['post_id'],
					'post_author' => $user_id,
				)
			);

			// Log error if post could not be updated.
			if ( is_wp_error( $updated ) ) {
				$this->log_error( __METHOD__ . '(): Unable to update author on post #' . $created_posts[ $feed['id'] ] . '; ' . $updated->get_error_message() );
			}

		}

	}





	// # ENTRY DETAILS -------------------------------------------------------------------------------------------------

	/**
	 * Add Advanced Post Creation posts meta box to the entry detail page.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param array $meta_boxes The properties for the meta boxes.
	 * @param array $entry      The Entry currently being viewed/edited.
	 *
	 * @return array
	 */
	public function register_meta_box( $meta_boxes, $entry ) {

		// Get post IDs entry meta.
		$created_posts = gform_get_meta( $entry['id'], $this->_slug . '_post_id' );

		if ( $created_posts ) {
			$meta_boxes[ $this->_slug ] = array(
				'title'    => esc_html__( 'Created Posts', 'gravityformsadvancedpostcreation' ),
				'callback' => array( $this, 'add_posts_meta_box' ),
				'context'  => 'side',
			);
		}

		return $meta_boxes;

	}

	/**
	 * Display created posts in Advanced Post Creation entry meta box.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param array $args An array containing the form and entry objects.
	 */
	public function add_posts_meta_box( $args ) {

		// Get post IDs entry meta.
		$created_posts = gform_get_meta( $args['entry']['id'], $this->get_slug() . '_post_id' );

		// Loop through created posts.
		foreach ( $created_posts as $post_info ) {

			// Get post ID.
			$post_id = absint( rgar( $post_info, 'post_id' ) );

			// Display links to post.
			printf(
				'<p><strong>%s</strong><br /><a href="%s">%s</a> - <a href="%s">%s</a></p>',
				esc_html( get_the_title( $post_id ) ),
				get_permalink( $post_id ),
				esc_html__( 'View Post', 'gravityformsadvancedpostcreation' ),
				get_edit_post_link( $post_id ),
				esc_html__( 'Edit Post', 'gravityformsadvancedpostcreation' )
			);

		}

	}





	// # IMPORT / EXPORT -----------------------------------------------------------------------------------------------

	/**
	 * Export Advanced Post Creation Add-On feeds when exporting form.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param array $form The current Form object being exported.
	 *
	 * @uses   GFAddOn::get_slug()
	 * @uses   GFFeedAddOn::get_feeds()
	 *
	 * @return array
	 */
	public function export_feeds_with_form( $form ) {

		// Get feeds for form.
		$feeds = $this->get_feeds( $form['id'] );

		// If form does not have a feeds property, create it.
		if ( ! isset( $form['feeds'] ) ) {
			$form['feeds'] = array();
		}

		// Add feeds to form object.
		$form['feeds'][ $this->get_slug() ] = $feeds;

		return $form;

	}

	/**
	 * Import Advanced Post Creation Add-On feeds when importing form.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param array $forms Imported Form objects.
	 *
	 * @uses   GFAPI::add_feed()
	 * @uses   GFAPI::get_form()
	 * @uses   GFAPI::update_form()
	 */
	public function import_feeds_with_form( $forms ) {

		// Loop through each form being imported.
		foreach ( $forms as $import_form ) {

			// Ensure the imported form is the latest. Compensates for a bug in Gravity Forms < 2.1.1.13
			$form = GFAPI::get_form( $import_form['id'] );

			// If the form does not have any Advanced Post Creation Add-On feeds, skip.
			if ( ! rgars( $form, 'feeds/' . $this->get_slug() ) ) {
				continue;
			}

			// Loop through feeds.
			foreach ( rgars( $form, 'feeds/' . $this->get_slug() ) as $feed ) {

				// Import feed.
				GFAPI::add_feed( $form['id'], $feed['meta'], $this->get_slug() );

			}

			// Remove feeds from Form object.
			unset( $form['feeds'][ $this->get_slug() ] );

			// Remove feeds property if empty.
			if ( empty( $form['feeds'] ) ) {
				unset( $form['feeds'] );
			}

			// Update form.
			GFAPI::update_form( $form );

		}

	}





	// # HELPER METHODS ------------------------------------------------------------------------------------------------

	/**
	 * Replace {post_id} and {post_edit_url} merge tags.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param string $text       The current text in which merge tags are being replaced.
	 * @param array  $form       The current Form.
	 * @param array  $entry      The current Entry.
	 * @param bool   $url_encode Whether or not to encode any URLs found in the replaced value.
	 * @param bool   $esc_html   Whether or not to encode HTML found in the replaced value.
	 * @param bool   $nl2br      Whether or not to convert newlines to break tags.
	 * @param string $format     Determines how the value should be formatted. Default is html.
	 *
	 * @return string
	 */
	public function filter_gform_pre_replace_merge_tags( $text, $form, $entry, $url_encode = false, $esc_html = true, $nl2br = true, $format = 'html' ) {

		// If Advanced Post Creation Media merge tag was found, replace merge tag.
		if ( preg_match_all( '/\{apc_media:?(\d*):?(ids|urls)?\}/m', $text ) ) {
			$text = $this->replace_media_merge_tag( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format );
		}

		// If neither the post ID or post edit URL merge tags are used, return.
		if ( ! preg_match_all( '/({(post_id|post_edit_url)( feed_id=(\d*))?})/m', $text ) ) {
			return $text;
		}

		// Get post IDs for entry.
		$post_ids = gform_get_meta( $entry['id'], $this->_slug . '_post_id' );

		// If no post IDs were found, return.
		if ( ! $post_ids ) {
			return $text;
		}

		// Find post ID tag matches.
		preg_match_all( '/{post_id( feed_id=(\d*))?}/m', $text, $id_matches, PREG_SET_ORDER, 0 );

		// Replace post ID tag matches.
		if ( ! empty( $id_matches ) ) {

			// Loop through matches, replace with first post found or post for specific feed.
			foreach ( $id_matches as $id_match ) {

				// Set initial post ID.
				$post_id = null;

				// If no feed ID is set, use first post ID.
				if ( ! isset( $id_match[2] ) ) {

					$post_id = $post_ids[0]['post_id'];

				} else {

					// Loop through post IDs, get matching ID.
					foreach ( $post_ids as $id ) {

						if ( (int) $id['feed_id'] !== (int) $id_match[2] ) {
							continue;
						}

						$post_id = $id['post_id'];

					}

				}

				// Replace merge tag.
				$text = str_replace( $id_match[0], $url_encode ? urlencode( $post_id ) : $post_id, $text );

			}

		}

		// Find post ID tag matches.
		preg_match_all( '/{post_edit_url( feed_id=(\d*))?}/m', $text, $url_matches, PREG_SET_ORDER, 0 );

		// Replace post ID tag matches.
		if ( ! empty( $url_matches ) ) {

			// Loop through matches, replace with first post found or post for specific feed.
			foreach ( $url_matches as $url_match ) {

				// Set initial post ID.
				$post_id = null;

				// If no feed ID is set, use first post ID.
				if ( ! isset( $url_match[2] ) ) {

					$post_id = $post_ids[0]['post_id'];

				} else {

					// Loop through post IDs, get matching ID.
					foreach ( $post_ids as $id ) {

						if ( (int) $id['feed_id'] !== (int) $url_match[2] ) {
							continue;
						}

						$post_id = $id['post_id'];

					}

				}

				// Prepare URL.
				$post_edit_url = $post_id ? get_bloginfo( 'wpurl' ) . '/wp-admin/post.php?action=edit&post=' . $post_id : null;

				// Replace merge tag.
				$text = str_replace( $url_match[0], $url_encode ? urlencode( $post_edit_url ) : $post_edit_url, $text );

			}

		}

		return $text;

	}

	/**
	 * Replace {apc_media} merge tag.
	 *    Required parameter: field ID.
	 *    Optional parameter: return type (ids or urls).
	 *
	 * @since 1.0
	 *
	 * @param string $text       The current text in which merge tags are being replaced.
	 * @param array  $form       The current Form.
	 * @param array  $entry      The current Entry.
	 * @param bool   $url_encode Whether or not to encode any URLs found in the replaced value.
	 * @param bool   $esc_html   Whether or not to encode HTML found in the replaced value.
	 * @param bool   $nl2br      Whether or not to convert newlines to break tags.
	 * @param string $format     Determines how the value should be formatted. Default is html.
	 *
	 * @return string
	 */
	public function replace_media_merge_tag( $text, $form, $entry, $url_encode = false, $esc_html = true, $nl2br = true, $format = 'html' ) {

		// Find merge tag matches.
		preg_match_all( '/\{apc_media:?(\d*):?(ids|urls)?\}/m', $text, $matches, PREG_SET_ORDER, 0 );

		// Loop through merge tags, replace.
		foreach ( $matches as $match ) {

			// Get field ID and return type.
			$field_id    = rgar( $match, 1 ) && is_numeric( $match[1] ) ? $match[1] : false;
			$return_type = rgar( $match, 2 ) ? $match[2] : 'ids';

			// If no field ID is defined, replace merge tag with empty string.
			if ( ! $field_id ) {
				$text = str_replace( $match[0], '', $text );
				continue;
			}

			// Set media files to false.
			$media = array();

			// If field value is empty, replace merge tag with empty string.
			if ( ! $files = rgar( $entry, $field_id ) ) {
				$text = str_replace( $match[0], '', $text );
				continue;
			}

			// Decode field value.
			$files = $this->maybe_decode_json( $files );
			$files = ! is_array( $files ) ? array( $files ) : $files;

			// Get current media files.
			$current_media = $this->get_current_media();

			// Loop through uploaded files, get media IDs.
			foreach ( $files as $file ) {
				if ( empty( $current_media[ $file ] ) ) {
					continue;
				}
				$media[] = rgar( $current_media, $file );
			}

			// If no media was found, replace merge tag with empty string.
			if ( empty( $media ) ) {
				$text = str_replace( $match[0], '', $text );
				continue;
			}

			switch ( $return_type ) {

				case 'urls':
					// Get media URLs.
					$urls = array_values( $media );
					$urls = array_map( 'wp_get_attachment_url', $urls );
					$urls = implode( ', ', $urls );

					// Replace merge tag.
					$text = str_replace( $match[0], $urls, $text );

					break;

				default:
					// Get media IDs.
					$ids = array_values( $media );
					$ids = implode( ', ', $ids );

					// Replace merge tag.
					$text = str_replace( $match[0], $ids, $text );

					break;

			}

		}

		return $text;

	}





	// # PAYPAL STANDARD -----------------------------------------------------------------------------------------------

	/**
	 * Add the Post Payments Actions setting to the PayPal feed and removes the setting for the legacy post fields.
	 *
	 * @param array $feed_settings_fields The PayPal feed settings.
	 *
	 * @return array
	 */
	public function add_paypal_post_payment_actions( $feed_settings_fields ) {
		$feed_settings_fields = parent::add_paypal_post_payment_actions( $feed_settings_fields );

		$form_id = absint( rgget( 'id' ) );
		if ( $this->has_feed( $form_id ) ) {
			$feed_settings_fields = $this->remove_field( 'post_checkboxes', $feed_settings_fields );
		}

		return $feed_settings_fields;
	}

}
