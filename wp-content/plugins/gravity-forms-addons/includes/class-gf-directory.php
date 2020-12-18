<?php
/**
 * The file that defines the core plugin class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://gravityview.co
 * @since      TODO
 *
 * @package    gravity-forms-addons
 * @subpackage gravity-forms-addons/includes
 */

class GFDirectory {

	/**
	 * Instance of this class.
	 *
	 * @since    4.2
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 *
	 * @since      4.2
	 *
	 * @return     void
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_action( 'plugins_loaded', array( $this, 'register_text_domain' ), 9 );
		add_action( 'init', array( 'GFDirectory', 'init' ) );
	}

	/**
	 * Include required files.
	 *
	 * @since      4.2
	 *
	 * @return     void
	 */
	private function includes() {

		// Include common function file.
		include_once( GF_DIRECTORY_PATH . 'includes/class-gf-directory-edit-form.php' );
		include_once( GF_DIRECTORY_PATH . 'includes/admin/class-gf-directory-admin.php' );
		include_once( GF_DIRECTORY_PATH . 'includes/class-gravity-forms-lead-creator.php' );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     4.2
	 *
	 * @return    Object    A single instance of this class.
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Load plugins text domain.
	 *
	 * @since      4.2
	 *
	 * @return     void
	 */
	public function register_text_domain() {
		load_plugin_textdomain( 'gravity-forms-addons', false, GF_DIRECTORY_PATH . '/languages' );
	}

	/**
	 * Get array of default directory arguments.
	 *
	 * @since     TODO
	 *
	 * @param  array  $args    Array of args.
	 * @return array  $options Parsed array with default keys.
	 */
	public static function directory_defaults( $args = array() ) {
		$defaults = array(
			'form'          => 1,
			// Gravity Forms form ID
			'approved'      => false,
			// Show only entries that have been Approved (have a field in the form that is an Admin-only checkbox with a value of 'Approved'
			'smartapproval' => true,
			// Auto-convert form into Approved-only when an Approved field is detected.
			'directoryview' => 'table',
			// Table, list or DL
			'entryview'     => 'table',
			// Table, list or DL
			'hovertitle'    => true,
			// Show column name as user hovers over cell
			'tableclass'    => 'gf_directory widefat',
			// Class for the <table>
			'tablestyle'    => '',
			// inline CSS for the <table>
			'rowclass'      => '',
			// Class for the <table>
			'rowstyle'      => '',
			// inline CSS for all <tbody><tr>'s
			'valign'        => '',
			'sort'          => 'date_created',
			// Use the input ID ( example: 1.3 or 7 or ip )
			'dir'           => 'DESC',

			'useredit'  => false,
			'limituser' => false,
			'adminedit' => false,

			'status'     => 'active',
			// Added in 2.0
			'start_date' => '',
			// Added in 2.0
			'end_date'   => '',
			// Added in 2.0

			//'wpautop' => true, // Convert bulk paragraph text to...paragraphs. Deprecated 3.6.3
			'page_size'  => 20,
			// Number of entries to show at once
			'startpage'  => 1,
			// If you want to show page 8 instead of 1

			'lightboxstyle'    => 3,
			'lightboxsettings' => array(
				'images' => true,
				'entry' => null,
				'websites' => null,
			),

			'showcount'         => true,
			// Do you want to show "Displaying 1-19 of 19"?
			'pagelinksshowall'  => true,
			// Whether to show each page number, or just 7
			'next_text'         => '&raquo;',
			'prev_text'         => '&laquo;',
			'pagelinkstype'     => 'plain',
			// 'plain' is just a string with the links separated by a newline character. The other possible values are either 'array' or 'list'.
			//'fulltext' => true, // If there's a textarea or post content field, show the full content or a summary? Deprecated 3.6.3
			'linkemail'         => true,
			// Convert email fields to email mailto: links
			'linkwebsite'       => true,
			// Convert URLs to links
			'linknewwindow'     => false,
			// Open links in new window? (uses target="_blank")
			'nofollowlinks'     => false,
			// Add nofollow to all links, including emails
			'titleshow'         => true,
			// Show a form title? By default, the title will be the form title.
			'titleprefix'       => 'Entries for ',
			// Default GF behavior is 'Entries : '
			'tablewidth'        => '100%',
			// 'width' attribute for the table
			'searchtabindex'    => false,
			// adds tabindex="" to the search field
			'search'            => true,
			// show the search field
			'tfoot'             => true,
			// show the <tfoot>
			'thead'             => true,
			// show the <thead>
			'showadminonly'     => false,
			// Admin only columns aren't shown by default, but can be (added 2.0.1)
			'datecreatedformat' => get_option( 'date_format' ) . ' \a\t ' . get_option( 'time_format' ),
			// Use standard PHP date formats (http://php.net/manual/en/function.date.php)
			'credit'            => true,
			// Credit link
			'dateformat'        => false,
			// Override the options from Gravity Forms, and use standard PHP date formats (http://php.net/manual/en/function.date.php)
			'postimage'         => 'icon',
			// Whether to show icon, thumbnail, or large image
			'getimagesize'      => false,
			'entry'             => true,
			// If there's an Entry ID column, link to the full entry
			'entrylink'         => 'View entry details',
			'entryth'           => 'More Info',
			'entryback'         => '&larr; Back to directory',
			'entryonly'         => true,
			'entrytitle'        => 'Entry Detail',
			'entrydetailtitle'  => '%%formtitle%% : Entry # %%leadid%%',
			'entryanchor'       => true,
			'truncatelink'      => false,
			'appendaddress'     => false,
			'hideaddresspieces' => false,
			'jssearch'          => true,
			'jstable'           => false,
			'lightbox'          => null,
			// depreciated - Combining with lightboxsettings
			'entrylightbox'     => null,
			// depreciated - Combining with lightboxsettings
		);

		$settings = get_option( 'gf_addons_settings' );
		if ( isset( $settings['directory_defaults'] ) ) {
			$defaults = wp_parse_args( $settings['directory_defaults'], $defaults );
		}

		$options = wp_parse_args( $args, $defaults );

		// Backward Compatibility
		if ( ! empty( $args['lightbox'] ) ) {
			$options['lightboxsettings']['images'] = 1;
		}
		if ( ! empty( $args['entrylightbox'] ) ) {
			$options['lightboxsettings']['entry'] = 1;
		}
		unset( $options['lightbox'], $options['entrylightbox'] ); // Depreciated for lightboxsettings

		return apply_filters( 'kws_gf_directory_defaults', $options );
	}

	/**
	 * Include files and load functionality on loading.
	 *
	 * @since     TODO
	 *
	 */
	public function plugins_loaded() {

		// Include files.
		$this->includes();

		if ( self::is_gravity_page() ) {
			self::load_functionality();
		}
	}

	/**
	 * Plugin starting point. Will load appropriate files.
	 *
	 * @since     TODO
	 *
	 */
	public static function init() {
		global $current_user;

		self::add_rewrite();

		add_action( 'wp_ajax_rg_update_feed_active', array( 'GFDirectory', 'update_feed_active' ) );
		add_action( 'wp_ajax_gf_select_directory_form', array( 'GFDirectory', 'select_directory_form' ) );
		add_action( 'wp_ajax_rg_update_approved', array( 'GFDirectory', 'directory_update_approved_hook' ) );
		add_action( 'wp_ajax_change_directory_columns', array( 'GFDirectory', 'change_directory_columns' ) );
		add_filter( 'plugin_action_links', array( 'GFDirectory', 'settings_link' ), 10, 2 );

		if ( ! self::is_gravityforms_supported() ) {
			return;
		}

		if ( ! is_admin() ) {

			add_action( 'template_redirect', array( 'GFDirectory', 'enqueue_files' ) );
			if ( apply_filters( 'kws_gf_directory_canonical_add', true ) ) {
				add_filter( 'post_link', array( 'GFDirectory', 'directory_canonical' ), 1, 3 );
				add_filter( 'page_link', array( 'GFDirectory', 'directory_canonical' ), 1, 3 );
			}
			if ( apply_filters( 'kws_gf_directory_shortlink', true ) ) {
				add_filter( 'get_shortlink', array( 'GFDirectory', 'shortlink' ) );
			}
			add_filter( 'kws_gf_directory_anchor_text', array( 'GFDirectory', 'directory_anchor_text' ) );
		}

		//integrating with Members plugin
		if ( function_exists( 'members_get_capabilities' ) ) {
			add_filter( 'members_get_capabilities', array( 'GFDirectory', 'members_get_capabilities' ) );
		}

		// Add this filter so it can be removed or overridden by users
		add_filter( 'kws_gf_directory_td_address', array( 'GFDirectory', 'format_address' ), 1, 2 );

		if ( self::is_directory_page() ) {

			//enqueueing sack for AJAX requests
			wp_enqueue_script( array( 'sack', 'datepicker' ) );
			wp_enqueue_style( 'gravityforms-admin', GFCommon::get_base_url() . '/css/admin.css' );

		} else if ( self::is_gravity_page( 'gf_entries' ) ) {
			wp_enqueue_script( 'thickbox', array( 'jquery' ) );
			add_filter( 'gform_get_field_value', array( 'GFDirectory', 'add_lead_approved_hidden_input' ), 1, 3 );
		}

	}

	/**
	 * Target of Member plugin filter. Provides the plugin with Gravity Forms lists of capabilities.
	 *
	 * @since     TODO
	 *
	 */
	public static function members_get_capabilities( $caps ) {
		return array_merge( $caps, array( 'gravityforms_directory', 'gravityforms_directory_uninstall' ) );
	}

	/**
	 * Check if Gravity Forms plugin is installed.
	 *
	 * @since     TODO
	 *
	 * @return bool True if Gravity Forms is installed, false otherwise.
	 *
	 */
	public static function is_gravityforms_installed() {
		return class_exists( 'RGForms' );
	}

	/**
	 * Load functionality.
	 *
	 * @since     TODO
	 *
	 */
	private static function load_functionality() {

		$settings = self::get_settings();
		extract( $settings );

		if ( $referrer ) {
			// Load Joost's referrer tracker.
			@include_once( GF_DIRECTORY_PATH . '/gravity-forms-referrer.php' );
		}

	}

	public static function shortlink( $link = '' ) {
		global $post;
		if ( empty( $post ) ) {
			return;
		}
		if ( empty( $link ) && isset( $post->guid ) ) {
			$link = $post->guid;

			return $link;
		}

		$url = add_query_arg( array() );
		if ( preg_match( '/' . sanitize_title( apply_filters( 'kws_gf_directory_endpoint', 'entry' ) ) . '\/([0-9]+)(?:\/|-)([0-9]+)\/?/ism', $url, $matches ) ) {
			$link = add_query_arg(
				array(
					'form' => (int) $matches[1],
					'leadid' => (int) $matches[2],
				),
				$link
			);
		} elseif ( isset( $_REQUEST['leadid'] ) && isset( $_REQUEST['form'] ) ) {
			$link = wp_nonce_url(
				add_query_arg(
					array(
						'leadid' => (int) $_REQUEST['leadid'],
						'form'   => (int) $_REQUEST['form'],
					),
					$link
				),
				sprintf( 'view-%d-%d', $_REQUEST['leadid'], $_REQUEST['form'] ),
				'view'
			);
		}

		return esc_url_raw( $link );
	}

	public static function directory_canonical( $permalink, $sentPost = '', $leavename = '' ) {

		// This was messing up the wp menu links
		if ( did_action( 'wp_head' ) ) {
			return $permalink;
		}

		global $post;

		if ( is_object( $post ) ) {
			$post->permalink = $permalink;
		}

		$url = add_query_arg( array() );

		$sentPostID = is_object( $sentPost ) ? $sentPost->ID : $sentPost;
		// $post->ID === $sentPostID is so that add_query_arg match doesn't apply to prev/next posts; just current
		preg_match( '/(' . sanitize_title( apply_filters( 'kws_gf_directory_endpoint', 'entry' ) ) . '\/([0-9]+)(?:\/|-)([0-9]+)\/?)/ism', $url, $matches );
		if ( isset( $post->ID ) && $post->ID === $sentPostID && ! empty( $matches ) ) {
			return trailingslashit( $permalink ) . $matches[0];
		} elseif ( isset( $post->ID ) && $post->ID === $sentPostID && ( isset( $_REQUEST['leadid'] ) && isset( $_REQUEST['form'] ) ) || ! empty( $matches ) ) {
			if ( $matches ) {
				$leadid = $matches[2];
				$form   = $matches[1];
			} else {
				$leadid = $_REQUEST['leadid'];
				$form   = $_REQUEST['form'];
			}

			return esc_url_raw(
				wp_nonce_url(
					add_query_arg(
						array(
							'leadid' => $leadid,
							'form'   => $form,
						),
						trailingslashit( $permalink )
					),
					sprintf( 'view-%d-%d', $leadid, $form ),
					'view'
				)
			);
		}

		return $permalink;
	}

	public static function enqueue_files() {
		global $post, $kws_gf_styles, $kws_gf_scripts, $kws_gf_directory_options;

		$kws_gf_styles  = isset( $kws_gf_styles ) ? $kws_gf_styles : array();
		$kws_gf_scripts = isset( $kws_gf_scripts ) ? $kws_gf_scripts : array();

		if ( ! empty( $post ) &&
			 ! empty( $post->post_content ) &&
			 preg_match( '/(.?)\[(directory)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/', $post->post_content, $matches )
		) {

			$options = self::directory_defaults( shortcode_parse_atts( $matches[3] ) );
			if ( ! is_array( $options['lightboxsettings'] ) ) {
				$options['lightboxsettings'] = explode( ',', $options['lightboxsettings'] );
			}

			$kws_gf_directory_options = $options;
			do_action( 'kws_gf_directory_enqueue', $options, $post );

			extract( $options );

			if ( $jstable ) {
				$theme = apply_filters( 'kws_gf_tablesorter_theme', 'blue', $form );
				wp_enqueue_style( 'tablesorter-' . $theme, GF_DIRECTORY_URL . "bower_components/jquery.tablesorter/css/theme.{$theme}.css", array(), GF_DIRECTORY_VERSION );
				wp_enqueue_script( 'tablesorter-min', GF_DIRECTORY_URL . 'bower_components/jquery.tablesorter/js/jquery.tablesorter.min.js', array( 'jquery' ), GF_DIRECTORY_VERSION );
				$kws_gf_styles[]  = 'tablesorter-' . $theme;
				$kws_gf_scripts[] = 'tablesorter-min';
			}

			if ( ! empty( $lightboxsettings ) ) {
				wp_enqueue_script( 'colorbox', GF_DIRECTORY_URL . 'bower_components/colorbox/jquery.colorbox-min.js', array( 'jquery' ), GF_DIRECTORY_VERSION );
				wp_enqueue_style( 'colorbox', GF_DIRECTORY_URL . "bower_components/colorbox/example{$lightboxstyle}/colorbox.css", array(), GF_DIRECTORY_VERSION );
				$kws_gf_scripts[] = $kws_gf_styles[] = 'colorbox';
				add_action(
					apply_filters( 'kws_gf_directory_colorbox_action', 'wp_footer' ),
					array(
						'GFDirectory',
						'load_colorbox',
					),
					1000
				);
			}

			list( $urlformid, $urlleadid ) = self::get_form_and_lead_ids();
			if ( isset( $_GET['edit'] ) && ! empty( $urlformid ) && isset( $urlleadid ) ) {

				$edit_scripts = array( 'jquery', 'gform_json', 'gform_placeholder', 'sack', 'plupload-all' );
				wp_enqueue_script( 'gform_gravityforms', $edit_scripts );

				$kws_gf_scripts[] = array_merge( $kws_gf_scripts, $edit_scripts );
			}
		}
	}

	public static function format_colorbox_settings( $colorboxSettings = array() ) {
		$settings = array();
		if ( ! empty( $colorboxSettings ) && is_array( $colorboxSettings ) ) {
			foreach ( $colorboxSettings as $key => $value ) {
				if ( null === $value ) {
					continue;
				}
				if ( true === $value ) {
					$value = 'true';
				} elseif ( empty( $value ) && 0 !== $value ) {
					$value = 'false';
				} else {
					$value = '"' . $value . '"';
				}
				$settings[ "{$key}" ] = $key . ':' . $value . '';
			}
		}

		return $settings;
	}

	public static function load_colorbox() {
		global $kws_gf_directory_options;
		extract( $kws_gf_directory_options );

		$lightboxsettings = apply_filters( 'kws_gf_directory_lightbox_settings', $lightboxsettings );
		$colorboxSettings = apply_filters(
			'kws_gf_directory_colorbox_settings',
			array(
				'width'     => apply_filters( 'kws_gf_directory_lightbox_width', '70%' ),
				'height'    => apply_filters( 'kws_gf_directory_lightbox_height', '70%' ),
				'iframe'    => true,
				'maxWidth'  => '95%',
				'maxHeight' => '95%',
				'current'   => '{current} of {total}',
				'rel'       => apply_filters( 'kws_gf_directory_lightbox_settings_rel', null ),
			)
		);

		?>
		<script>
			jQuery( document ).ready( function ( $ ) {
				<?php
				$output = '';
				foreach ( $lightboxsettings as $key => $value ) {
					$settings = $colorboxSettings;
					if ( is_numeric( $key ) ) {
						$key = $value;
					}
					switch ( $key ) {
						case 'images':
							$settings['width'] = $settings['height'] = $settings['iframe'] = null;
							break;
						case 'urls':
							$settings['height'] = '80%';
							break;
					}
					$output .= "\t\t" . '$(".colorbox[rel~=\'directory_' . $key . '\']").colorbox(';
					if ( ! empty( $settings ) ) {
						$output .= "{\n\t\t\t" . implode( ",\n\t\t\t", self::format_colorbox_settings( apply_filters( "kws_gf_directory_lightbox_{$key}_settings", $settings ) ) ) . "\n\t\t}";
					}
					$output .= ");\n\n";
				}
				echo $output;
				do_action( 'kws_gf_directory_jquery', $kws_gf_directory_options );
				?>
			} );
		</script>
		<?php
	}

	public static function add_rewrite() {
		global $wp_rewrite, $wp;

		if ( ! $wp_rewrite->using_permalinks() ) {
			return;
		}
		$endpoint = sanitize_title( apply_filters( 'kws_gf_directory_endpoint', 'entry' ) );

		# @TODO: Make sure this works in MU
		$wp_rewrite->add_permastruct( "{$endpoint}", $endpoint . '/%' . $endpoint . '%/?', true );
		$wp_rewrite->add_endpoint( "{$endpoint}", EP_ALL );
	}

	/**
	 * @param array $page
	 *
	 * @return bool Returns true if the current page is one of Gravity Forms pages. Returns false if not
	 */
	public static function is_gravity_page( $page = array() ) {
		$current_page = trim( strtolower( rgget( 'page' ) ) );
		if ( empty( $page ) ) {
			$gf_pages = array( 'gf_edit_forms', 'gf_new_form', 'gf_entries', 'gf_settings', 'gf_export', 'gf_help' );
		} else {
			$gf_pages = is_array( $page ) ? $page : array( $page );
		}

		return in_array( $current_page, $gf_pages, true );
	}

	/**
	 * Update the approval status for the entry
	 *
	 * @param int $lead_id
	 * @param int $approved
	 * @param int $form_id
	 * @param int $approvedcolumn
	 */
	public static function directory_update_approved( $lead_id = 0, $approved = 0, $form_id = 0, $approvedcolumn = 0 ) {

		$current_user = wp_get_current_user();

		// This will be faster in the 1.6+ future.
		if ( function_exists( 'gform_update_meta' ) ) {
			gform_update_meta( $lead_id, 'is_approved', $approved );
		}

		if ( ! empty( $approvedcolumn ) ) {

			if ( ! method_exists( 'GFAPI', 'update_entry_field' ) ) {
				GFCommon::log_error( 'Cannot update approval; update_entry_field not available in Gravity Forms' );
				return;
			}

			$approved = $approved ? $approved : '';

			GFAPI::update_entry_field( $lead_id, $approvedcolumn, $approved );
		}

		$message = empty( $approved ) ? __( 'Disapproved the lead', 'gravity-forms-addons' ) : __( 'Approved the lead', 'gravity-forms-addons' );

		RGFormsModel::add_note( $lead_id, $current_user->ID, $current_user->display_name, $message );
	}

	public static function edit_lead_detail( $Form, $lead, $options ) {
		global $current_user;
		require_once( GFCommon::get_base_path() . '/form_display.php' );

		$approvedcolumn = self::get_approved_column( $Form );

		// We fetch this again, since it may have had some admin-only columns taken out.
		#$lead = RGFormsModel::get_lead($lead["id"]);

		// If you want to allow users to edit their own approval (?) add a filter and return true.
		if ( apply_filters( 'kws_gf_directory_allow_user_edit_approved', false ) === false ) {
			$Form['fields'] = self::remove_approved_column( 'form', $Form['fields'], $approvedcolumn );
		}

		// If this is not the form that should be edited
		list( $urlformid, $urlleadid ) = self::get_form_and_lead_ids();
		if ( intval( $Form['id'] ) !== intval( $urlformid ) || intval( $lead['id'] ) !== intval( $urlleadid ) ) {
			return;
		}

		// If either of these two things are false (creator of lead, or admin)
		if ( ! (

			// Users can edit their own listings, they are logged in, the current user is the creator of the lead
			( ! empty( $options['useredit'] ) && is_user_logged_in() && intval( $current_user->ID ) === intval( $lead['created_by'] ) ) === true || // OR

			// Administrators can edit every listing, and this person has administrator access
			( ! empty( $options['adminedit'] ) && ( self::has_access( 'gravityforms_directory' ) === true || GFCommon::current_user_can_any( 'gravityforms_edit_entries' ) ) )
		) ) {
			// Kick them out.
			printf( esc_html__( '%1$sYou do not have permission to edit this form.%2$s', 'gravity-forms-addons' ), '<div class="error">', '</div>' );

			return;
		}

		$validation_message = '';

		// If the form is submitted
		if ( rgpost( 'action' ) === 'update' ) {
			check_admin_referer( 'gforms_save_entry', 'gforms_save_entry' );

			$lead = apply_filters( 'kws_gf_directory_lead_being_updated', $lead, $Form );

			// We don't DO passwords.
			foreach ( $Form['fields'] as $key => $field ) {
				if ( 'password' === $field->type ) {
					unset( $Form['fields'][ $key ] );
				}
			}

			$is_valid = GFFormDisplay::validate( $Form, $lead );

			$validation_message = '';
			foreach ( $Form['fields'] as $field ) {
				if ( ! GFCommon::is_product_field( $field->type ) ) {
					$validation_message .= ( rgget( 'failed_validation', $field ) && ! empty( $field->validation_message ) ) ? sprintf( "<li class='gfield_description validation_message'><strong>%s</strong>: %s</li>", $field->label, $field->validation_message ) : '';
					;
				}
			}
			if ( ! empty( $validation_message ) ) {
				$validation_message = '<ul>' . $validation_message . '</ul>';
				echo esc_html( apply_filters( 'kws_gf_directory_lead_error_message', sprintf( __( '%1$sThere were errors with the edit you made.%2$s%3$s', 'gravity-forms-addons' ), "<div class='error' id='message' style='padding:.5em .75em; background-color:#ffffcc; border:1px solid #ccc;'><p>", '</p>', $validation_message . '</div>' ), $lead, $Form ) );
			}

			// So the form submission always throws an error even though there's no problem.
			// Product fields can't be edited, so that doesn't really matter.
			if ( ! empty( $is_valid ) || ( empty( $is_valid ) && empty( $validation_message ) ) ) {

				do_action( 'kws_gf_directory_pre_update_lead', $lead, $Form );

				// since @3.6.1 to enable conditional fields' updates.
				self::save_lead( $Form, $lead );

				$lead = RGFormsModel::get_lead( $lead['id'] );

				do_action( 'kws_gf_directory_post_update_lead', $lead, $Form );

				echo apply_filters( 'kws_gf_directory_lead_updated_message', sprintf( esc_html__( '%1$sThe entry was successfully updated.%2$s', 'gravity-forms-addons' ), "<p class='updated' id='message' style='padding:.5em .75em; background-color:#ffffcc; border:1px solid #ccc;'>", '</p>' ), $lead, $Form );

				return $lead;
			}
		}

		if ( ( isset( $_GET['edit'] ) && wp_verify_nonce( $_GET['edit'], 'edit' . $lead['id'] . $Form['id'] ) ) || ! empty( $validation_message ) ) {

			// The ID of the form needs to be `gform_{form_id}` for the pluploader
			?>
			<form method="post" id="gform_<?php echo esc_attr( $Form['id'] ); ?> action=" <?php echo esc_url( remove_query_arg( array( 'gf_search', 'sort', 'dir', 'pagenum', 'edit' ), add_query_arg( array() ) ) ); ?>">
				<?php
				wp_nonce_field( 'gforms_save_entry', 'gforms_save_entry' );
				?>
				<input type="hidden" name="action" id="action" value="update"/>
				<input type="hidden" name="screen_mode" id="screen_mode" value="edit"/>
				<?php

				$form_without_products = $Form;
				$product_fields        = array();
				foreach ( $Form['fields'] as $key => $field ) {
					if (
						GFCommon::is_product_field( $field->type ) ||
						is_numeric( $lead['post_id'] ) && GFCommon::is_post_field( $field )
					) {
						if ( is_numeric( $lead['post_id'] ) && GFCommon::is_post_field( $field ) && ! $message_shown ) {
							echo apply_filters( 'kws_gf_directory_edit_post_details_text', sprintf( esc_html__( 'You can edit post details from the %1$spost page%2$s.', 'gravity-forms-addons' ), '<a href="' . esc_url( admin_url( 'post.php?action=edit&post=' . $lead['post_id'] ) ) . '">', '</a>' ), $field, $lead, $lead['post_id'] );
							$message_shown = true;
						}

						unset( $form_without_products['fields'][ $key ] );
						$product_fields[] = $field->id;
						if ( ! empty( $field->inputs ) ) {
							foreach ( $field->inputs as $input ) {
								$product_fields[] = $input['id'];
							}
						}
					}
				}

				$lead_without_products = &$lead;
				foreach ( $product_fields as $product_field ) {
					$value = RGFormsModel::get_lead_field_value( $lead, $field );
					unset( $lead_without_products[ $product_field ] );
				}

				require_once( GFCommon::get_base_path() . '/entry_detail.php' );
				GFEntryDetail::lead_detail_edit( apply_filters( 'kws_gf_directory_form_being_edited', $form_without_products, $lead ), apply_filters( 'kws_gf_directory_lead_being_edited', $lead_without_products, $form_without_products ) );
				echo '<input class="button-primary" type="submit" tabindex="4" value="' . esc_attr( apply_filters( 'kws_gf_directory_update_lead_button_text', __( 'Update Entry', 'gravity-forms-addons' ) ) ) . '" name="save" />';
				?>
			</form>
			<?php
			do_action( 'kws_gf_directory_post_after_edit_lead_form', $lead, $Form );

			return false;
		} elseif ( ( isset( $_GET['edit'] ) && ! wp_verify_nonce( $_GET['edit'], 'edit' ) ) ) {
			echo apply_filters( 'kws_gf_directory_edit_access_error_message', sprintf( esc_html__( '%1$sThe link to edit this entry is not valid; it may have expired.%2$s', 'gravity-forms-addons' ), "<p class='error' id='message' style='padding:.5em .75em; background-color:#ffffcc; border:1px solid #ccc;'>", '</p>' ), $lead, $Form );
		}

		return $lead;
	}


	public static function lead_detail( $Form, $lead, $allow_display_empty_fields = false, $inline = true, $options = array() ) {

		if ( ! class_exists( 'GFEntryList' ) ) {
			require_once( GFCommon::get_base_path() . '/entry_list.php' );
		}

		global $current_user;
		wp_get_current_user();

		$display_empty_fields       = '';
		$allow_display_empty_fields = true;
		if ( $allow_display_empty_fields ) {
			$cookie_array = isset( $_COOKIE ) ? $_COOKIE : array();
			$display_empty_fields = rgar( $cookie_array, 'gf_display_empty_fields' );
		}
		if ( empty( $options ) ) {
			$options = self::directory_defaults();
		}

		// There is no edit link
		if ( isset( $_GET['edit'] ) || rgpost( 'action' ) === 'update' ) {
			// Process editing leads
			$lead = self::edit_lead_detail( $Form, $lead, $options );
			if ( rgpost( 'action' ) !== 'update' ) {
				return;
			}
		}

		extract( $options );

		include_once( GF_DIRECTORY_PATH . 'includes/views/html-entry-detail.php' );
	}

	public static function get_admin_only( $form, $adminOnly = array() ) {
		if ( ! is_array( $form ) ) {
			return false;
		}

		foreach ( $form['fields'] as $key => $field ) {
			// Only the Go to Entry button adds disableMargins.

			if ( 'hidden' === $field->type && ! empty( $field->useAsEntryLink ) && ! empty( $field->disableMargins ) ) {
				continue;
			}
			if ( ! empty( $field->adminOnly ) ) {
				$adminOnly[] = $field->id;
			}
			if ( isset( $field->inputs ) && is_array( $field->inputs ) ) {
				foreach ( $field->inputs as $key2 => $input ) {
					if ( ! empty( $field->adminOnly ) ) {
						$adminOnly[] = $input['id'];
					}
				}
			}
		}

		return $adminOnly;
	}

	/**
	 * Get the form and lead IDs from the URL or from $_REQUEST
	 *
	 * @return array|null $formid, $leadid if found. Null if not.
	 */
	private static function get_form_and_lead_ids() {
		global $wp, $wp_rewrite;

		$formid = $leadid = null;

		$url = isset( $wp->request ) ? $wp->request : add_query_arg( array(), home_url() );

		if (
			// If permalinks is turned on
			$wp_rewrite->using_permalinks() &&
			// And
			preg_match( '/\/?' . sanitize_title( apply_filters( 'kws_gf_directory_endpoint', 'entry' ) ) . '\/([0-9]+)(?:\/|-)([0-9]+)/ism', $url, $matches )
		) {
			$formid = $matches[1];
			$leadid = $matches[2];
		} else {
			$formid = isset( $_REQUEST['form'] ) ? (int) $_REQUEST['form'] : $formid;
			$leadid = isset( $_REQUEST['leadid'] ) ? (int) $_REQUEST['leadid'] : $leadid;
		}

		return array( $formid, $leadid );

	}


	/**
	 * get_back_link function.
	 *
	 * @access public
	 *
	 * @param string $entryback (default: '') The text of the back-link anchor
	 *
	 * @return string The HTML link for the backlink
	 */
	public static function get_back_link( $passed_entryback = '' ) {
		global $pagenow, $wp_rewrite;

		$options = self::directory_defaults();

		if ( isset( $_GET['edit'] ) ) {
			return '<p class="entryback"><a href="' . esc_url( add_query_arg( array(), remove_query_arg( array( 'edit' ) ) ) ) . '">' . esc_html( __( apply_filters( 'kws_gf_directory_edit_entry_cancel', '&larr; Cancel Editing' ), 'gravity-forms-addons' ) ) . '</a></p>';
		}

		list( $formid, $leadid ) = self::get_form_and_lead_ids();
		extract( $options );

		// Use passed value, if available. Otherwise, use default
		$entryback = ! empty( $passed_entryback ) ? $passed_entryback : $entryback;

		if ( 'entry-details.php' === $pagenow ) {

			// If possible, link back to the original post.
			if ( isset( $_GET['post'] ) ) {
				$href = get_permalink( (int) $_GET['post'] );
			} else {
				// Otherwise we rely on Javascript below.
				$href = '#';
			}

			$onclick = ' onclick="parent.jQuery.fn.colorbox.close();"';
		} else {
			$onclick = '';
			$href    = remove_query_arg( array( 'row', 'leadid', 'form', 'edit' ) );
			if ( $wp_rewrite->using_permalinks() ) {
				$href = preg_replace( '/(' . sanitize_title( apply_filters( 'kws_gf_directory_endpoint', 'entry' ) ) . '\/(?:[0-9]+)(?:\/|-)(?:[0-9]+)\/?)/ism', '', $href );
			}
		}

		$href = esc_url_raw( $href );

		$url = parse_url( add_query_arg( array(), $href ) );
		if ( ! empty( $url['query'] ) && ! empty( $permalink ) ) {
			$href .= '?' . $url['query'];
		}
		if ( ! empty( $options['entryanchor'] ) ) {
			$href .= '#lead_row_' . $leadid;
		}

		// If there's a back link, format it
		if ( ! empty( $entryback ) && ! empty( $entryonly ) ) {
			$link = apply_filters( 'kws_gf_directory_backlink', '<p class="entryback"><a href="' . $href . '"' . $onclick . '>' . esc_html( $entryback ) . '</a></p>', $href, $entryback );
		} else {
			$link = '';
		}

		return $link;
	}

	public static function process_lead_detail( $inline = true, $entryback = '', $showadminonly = false, $adminonlycolumns = array(), $approvedcolumn = null, $options = array(), $entryonly = true ) {
		$formid = $leadid = false;
		global $wp, $post, $wp_rewrite, $wpdb;

		list( $formid, $leadid ) = self::get_form_and_lead_ids();

		$lead = apply_filters( 'kws_gf_directory_lead_detail', GFAPI::get_entry( $leadid ) );

		if ( $lead && ! is_wp_error( $lead ) && ! is_null( $formid ) ) {

			$form = apply_filters( 'kws_gf_directory_lead_detail_form', RGFormsModel::get_form_meta( (int) $formid ) );

			if ( empty( $approvedcolumn ) ) {
				$approvedcolumn = self::get_approved_column( $form );
			}
			if ( empty( $adminonlycolumns ) && ! $showadminonly ) {
				$adminonlycolumns = self::get_admin_only( $form );
			}

			$approved = self::check_approval( $lead, $approvedcolumn, rgar( $options, 'smartapproval', false ) );

			//since 3.5
			$lead = self::remove_hidden_fields( array( $lead ), $adminonlycolumns, $approvedcolumn, true, true, $showadminonly, $form );
			$lead = isset( $lead[0] ) ? $lead[0] : false;

			ob_start(); // Using ob_start() allows us to filter output

			if ( ! $approved && ! GFCommon::current_user_can_any( array( 'gravityforms_view_entries', 'gravityforms_edit_entries' ) ) ) {
				esc_html_e( 'You are not allowed to view this content.', 'gravity-forms-addons' );
				return false;
			}

			if ( ! $approved && empty( $_GET['edit'] ) ) {
				echo '<div class="error" style="border: 1px solid #ccc; padding: 1em; margin: .5em 0 1em;">';
				esc_html_e( 'This entry is not approved, but you are logged-in and have permission to see it.', 'gravity-forms-addons' );
				echo '</div>';
			}

			self::lead_detail( $form, $lead, false, $inline, $options );

			$content = ob_get_contents(); // Get the output
			ob_end_clean(); // Clear the buffer

			// Get the back link if this is a single entry.
			$link = ! empty( $entryonly ) ? self::get_back_link( $entryback ) : '';

			$content = $link . $content;
			$content = apply_filters( 'kws_gf_directory_detail', apply_filters( 'kws_gf_directory_detail_' . (int) $leadid, $content, (int) $leadid ), (int) $leadid );

			if ( isset( $options['entryview'] ) ) {
				$content = self::html_display_type_filter( $content, $options['entryview'], true );
			}

			return $content;
		} else {
			return false;
		}
	}

	public static function change_directory_columns() {
		check_ajax_referer( 'gforms_directory_columns', 'gforms_directory_columns' );
		$columns = GFCommon::json_decode( stripslashes( $_POST['directory_columns'] ), true );
		self::update_grid_column_meta( (int) $_POST['form_id'], $columns );
	}

	public static function update_grid_column_meta( $form_id, $columns ) {

		$meta = maybe_serialize( stripslashes_deep( $columns ) );

		update_option( 'gf_directory_form_' . $form_id . '_grid', $meta );
	}

	public static function get_grid_column_meta( $form_id ) {

		$grid = get_option( 'gf_directory_form_' . $form_id . '_grid' );

		if ( ! $grid ) {
			$grid = GFFormsModel::get_grid_column_meta( $form_id );
			self::update_grid_column_meta( $form_id, $grid );
		}

		return maybe_unserialize( $grid );
	}

	/**
	 * Get the label for the input field. This is necessary to prevent Admin Labels from being used instead of normal labels.
	 */
	public static function get_label( $field, $input_id = 0, $input_only = false ) {
		$field_label = rgobj( $field, 'label' );
		$input       = GFFormsModel::get_input( $field, $input_id );
		if ( 'checkbox' === rgobj( $field, 'type' ) && null != $input ) {
			return $input['label'];
		} else if ( null != $input ) {
			return $input_only ? $input['label'] : $field_label . ' (' . $input['label'] . ')';
		} else {
			return $field_label;
		}
	}

	/**
	 * Should we use Gravity Forms 2.3+ database structure?
	 *
	 * @since 4.1.2
	 *
	 * @return bool True: Gravity Forms 2.3 is alive; false: What's 2.3?
	 */
	public static function use_gf_23_db() {
		return method_exists( 'GFFormsModel', 'get_database_version' ) && version_compare( GFFormsModel::get_database_version(), '2.3-dev-1', '>=' );
	}

	/**
	 * Render image link HTML
	 *
	 * @since  3.7
	 *
	 * @param  [type] $url         [description]
	 * @param  string $title [description]
	 * @param  string $caption [description]
	 * @param  string $description [description]
	 *
	 * @return [type]              [description]
	 */
	public static function render_image_link( $url, $lead, $options, $title = '', $caption = '', $description = '' ) {

		extract( $options );

		$target = ( $linknewwindow && empty( $lightboxsettings['images'] ) ) ? ' target="_blank"' : '';

		$size = false;
		if ( ! empty( $options['getimagesize'] ) ) {
			$size = @getimagesize( $url );
		}

		//displaying thumbnail (if file is an image) or an icon based on the extension
		$icon = GFEntryList::get_icon_url( $url );
		if ( ! preg_match( '/icon\_image\.gif/ism', $icon ) ) {
			$src = $icon;
			if ( ! empty( $size ) ) {
				$img = "<img src='$src' {$size[3]}/>";
			} else {
				$img = "<img src='$src' />";
			}
		} else { // No thickbox for non-images please
			switch ( strtolower( trim( $options['postimage'] ) ) ) {
				case 'image':
					$src = $url;
					break;
				case 'icon':
				default:
					$src = $icon;
					break;
			}
		}
		$img = array(
			'src'         => $src,
			'size'        => $size,
			'title'       => $title,
			'caption'     => $caption,
			'description' => $description,
			'url'         => esc_url_raw( $url ),
			'code'        => isset( $size[3] ) ? "<img src='$src' {$size[3]} />" : "<img src='$src' />",
		);
		$img = apply_filters( 'kws_gf_directory_lead_image', apply_filters( 'kws_gf_directory_lead_image_' . $options['postimage'], apply_filters( 'kws_gf_directory_lead_image_' . $lead['id'], $img ) ) );

		$lightboxclass = '';

		if ( ! empty( $lightboxsettings['images'] ) && self::is_image_file( $url ) ) {
			if ( wp_script_is( 'colorbox', 'registered' ) ) {
				$lightboxclass = ' class="colorbox lightbox"';
			} else if ( wp_script_is( 'thickbox', 'registered' ) ) {
				$lightboxclass = ' class="thickbox lightbox"';
			}

			if ( in_array( 'images', $lightboxsettings ) || ! empty( $lightboxsettings['images'] ) ) {
				$lightboxclass .= ' rel="directory_all directory_images"';
			}
		}

		$value = "<a href='{$url}'{$target}{$lightboxclass}>{$img['code']}</a>";

		$value = apply_filters( 'kws_gf_directory_render_image_link', $value, $url, $lead, $options, $title, $caption, $description );

		return $value;
	}

	/**
	 * Verify that the src URL matches image patterns.
	 *
	 *
	 * @return boolean     True: matches pattern; False: does not match pattern.
	 */
	public static function is_image_file( $src ) {

		$info = pathinfo( $src );

		$image_exts = apply_filters(
			'kws_gf_directory_image_extensions',
			array(
				'jpg',
				'jpeg',
				'jpe',
				'gif',
				'png',
				'bmp',
				'tif',
				'tiff',
				'ico',
			)
		);

		return isset( $info['extension'] ) && in_array( strtolower( $info['extension'] ), $image_exts );
	}

	/**
	 * render_search_dropdown function.
	 *
	 * @since 3.5
	 * @access public
	 * @static
	 *
	 * @param string $label (default: '') search field label
	 * @param string $name (default: '') input name attribute
	 * @param array $choices
	 *
	 * @return field dropdown html
	 */
	public static function render_search_dropdown( $label = '', $name = '', $choices ) {

		if ( empty( $choices ) || ! is_array( $choices ) || empty( $name ) ) {
			return '';
		}

		$current_value = isset( $_GET[ $name ] ) ? $_GET[ $name ] : '';

		$output = '<div class="search-box">';
		$output .= '<label for=search-box-' . $name . '>' . $label . '</label>';
		$output .= '<select name="' . $name . '" id="search-box-' . $name . '">';
		$output .= '<option value="" ' . selected( '', $current_value, false ) . '>---</option>';
		foreach ( $choices as $choice ) {
			$output .= '<option value="' . $choice['value'] . '" ' . selected( $choice['value'], $current_value, false ) . '>' . $choice['text'] . '</option>';
		}
		$output .= '</select>';
		$output .= '</div>';

		return $output;

	}


	/**
	 * render_search_input function.
	 *
	 * @since 3.5
	 * @access public
	 * @static
	 *
	 * @param string $label (default: '') search field label
	 * @param string $name (default: '') input name attribute
	 *
	 * @return field input html
	 */
	public static function render_search_input( $label = '', $name = '' ) {

		if ( empty( $name ) ) {
			return '';
		}

		$current_value = isset( $_GET[ $name ] ) ? $_GET[ $name ] : '';

		$output = '<div class="search-box">';
		$output .= '<label for=search-box-' . $name . '>' . $label . '</label>';
		$output .= '<input type="text" name="' . $name . '" id="search-box-' . $name . '" value="' . $current_value . '">';
		$output .= '</div>';

		return $output;

	}


	public static function get_credit_link( $columns = 1, $options = array() ) {

		if ( ! did_action( 'wp_head' ) || is_admin() ) {
			return;
		}

		$settings = self::get_settings();

		// Only show credit link if the user has saved settings;
		// this prevents existing directories adding a link without user action.
		if ( isset( $settings['version'] ) ) {

			$plugin_name = esc_html__( 'Gravity Forms Directory', 'gravity-forms-addon' );
			$link = '<a href="https://katz.co/gravity-forms-addons/">' . $plugin_name . '</a>';
			$attribution_text = sprintf( esc_html__( 'Powered by %s', 'gravity-forms-addon' ), $link );
			$attribution_html = '<span class="kws_gf_credit" style="font-weight:normal; text-align:center; display:block; margin:0 auto;">' . $attribution_text . '</span>';

			echo "<tr><th colspan='{$columns}'>" . $attribution_html . '</th></tr>';
		}
	}

	// static public function get_version() {
	//  return self::$version;
	// }

	public static function add_lead_approved_hidden_input( $value, $lead, $field = '' ) {

		if ( ! isset( $processed_meta ) ) {
			static $processed_meta = array();
		}

		if ( ! in_array( $lead['id'], $processed_meta ) ) {
			$processed_meta[] = $lead['id'];

			$forms          = RGFormsModel::get_forms( null, 'title' );
			$approvedcolumn = self::globals_get_approved_column( $forms[0]->id );

			if ( self::check_approval( $lead, $approvedcolumn ) ) {
				echo '<span style="display:none;"><input type="hidden" class="lead_approved" id="lead_approved_' . $lead['id'] . '" value="true" /></span>';
			}
		}

		return $value;
	}


	public static function globals_get_approved_column( $form_id = 0 ) {

		$form_id = empty( $form_id ) ? rgget( 'id' ) : $form_id;

		if ( empty( $form_id ) ) {
			// If there's no 'id' query string, grab the first form available
			if ( self::is_gravity_page( 'gf_entries' ) ) {
				$forms   = RGFormsModel::get_forms( null, 'title' );
				$form_id = $forms[0]->id;
			}
		}

		$active_form = RGFormsModel::get_form_meta( $form_id );

		return self::get_approved_column( $active_form );
	}

	public static function get_approved_column( $form ) {

		if ( ! is_array( $form ) || ! is_array( $form['fields'] ) ) {
			return false;
		}

		$approved_strings = array( __( 'Approved', 'gravity-forms-addons' ), __( 'Approved?', 'gravity-forms-addons' ), __( 'Approved? (Admin-only)', 'gravity-forms-addons' ) );

		/** @var GF_Field_Checkbox $field */
		foreach ( $form['fields'] as $field ) {

			if ( ! is_a( $field, 'GF_Field' ) ) {
				continue;
			}

			if ( 'checkbox' === $field->type && $field->adminOnly ) {

				if ( $field->gf_directory_approval || in_array( $field->label, $approved_strings ) || in_array( $field->adminLabel, $approved_strings ) ) {
					foreach ( $field->inputs as $input ) {
						if ( in_array( $input['label'], $approved_strings ) || in_array( $input['value'], $approved_strings ) ) {
							return $input['id'];
						}
					}
				}

				foreach ( $field->inputs as $input ) {
					if ( in_array( $input['label'], $approved_strings ) || ( isset( $input['value'] ) && in_array( $input['value'], $approved_strings ) ) ) {
						return $input['id'];
					}
				}
			}
		}

		return null;
	}


	public static function directory_update_approved_hook() {
		check_ajax_referer( 'rg_update_approved', 'rg_update_approved' );
		if ( ! empty( $_POST['lead_id'] ) ) {
			$_gform_directory_approvedcolumn = self::globals_get_approved_column( $_POST['form_id'] );
			self::directory_update_approved( (int) $_POST['lead_id'], $_POST['approved'], (int) $_POST['form_id'], $_gform_directory_approvedcolumn );
		}
	}

	public static function settings_link( $links, $file ) {
		static $this_plugin;
		if ( ! $this_plugin ) {
			$this_plugin = plugin_basename( __FILE__ );
		}
		if ( $file == $this_plugin ) {
			$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=gf_settings&subview=Directory' ) ) . '">' . esc_html__( 'Settings', 'gravity-forms-addons' ) . '</a>';
			array_unshift( $links, $settings_link ); // before other links
		}

		return $links;
	}

	//Returns true if the current page is an Feed pages. Returns false if not
	private static function is_directory_page() {
		if ( empty( $_GET['pagenum'] ) ) {
			return false;
		}
		$current_page    = trim( strtolower( $_GET['pagenum'] ) );
		$directory_pages = array( 'gf_directory' );

		return in_array( $current_page, $directory_pages );
	}

	public static function get_settings() {
		return get_option(
			'gf_addons_settings',
			array(
				'directory'          => true,
				'directory_defaults' => array(),
				'referrer'           => false,
				'modify_admin'       => array(
					'expand' => true,
					'toggle' => true,
					'edit'   => true,
					'ids'    => true,
				),
				'saved'              => false,
				'version'            => GF_DIRECTORY_VERSION,
			)
		);
	}

	public static function disable_directory() {
		delete_option( 'gf_directory_oid' );
	}

	private static function is_gravityforms_supported() {
		if ( class_exists( 'GFCommon' ) ) {
			$is_correct_version = version_compare( GFCommon::$version, GF_DIRECTORY_MIN_GF_VERSION, '>=' );

			return $is_correct_version;
		} else {
			return false;
		}
	}

	protected static function get_has_access( $required_permission ) {
		$has_members_plugin = function_exists( 'members_get_capabilities' );
		$has_access         = $has_members_plugin ? current_user_can( $required_permission ) : current_user_can( 'level_7' );
		if ( $has_access ) {
			return $has_members_plugin ? $required_permission : 'level_7';
		} else {
			return false;
		}
	}

	public static function has_access( $required_permission ) {
		return self::get_has_access( $required_permission );
	}

	//Returns the url of the plugin's root folder
	public static function get_base_url() {
		return rtrim( GF_DIRECTORY_URL, '/' );
	}


	/**
	 * get_search_filters function.
	 *
	 * @since 3.5
	 * @access public
	 * @static
	 *
	 * @param mixed $form
	 *
	 * @return array search fields ids
	 */
	public static function get_search_filters( $form ) {
		if ( empty( $form['fields'] ) ) {
			return array();
		}

		$search_fields = array();

		foreach ( $form['fields'] as $field ) {
			if ( ! empty( $field->isSearchFilter ) ) {
				$search_fields[] = $field->id;
			}
		}

		return $search_fields;
	}

	/**
	 * get_leads function.
	 *
	 * @access public
	 * @static
	 *
	 * @param int $form_id
	 * @param int $sort_field_number (default: 0)
	 * @param string $sort_direction (default: 'DESC')
	 * @param string $search (default: '')
	 * @param int $offset (default: 0)
	 * @param int $page_size (default: 30)
	 * @param mixed $star (default: null)
	 * @param mixed $read (default: null)
	 * @param bool $is_numeric_sort (default: false)
	 * @param mixed $start_date (default: null)
	 * @param mixed $end_date (default: null)
	 * @param string $status (default: 'active')
	 * @param mixed $approvedcolumn (default: null)
	 * @param bool $limituser (default: false)
	 * @param array $search_criteria , since 3.5
	 *
	 * @return array Leads results
	 */
	public static function get_leads( $form_id, $sort_field_number = 0, $sort_direction = 'DESC', $search = '', $offset = 0, $page_size = 30, $star = null, $read = null, $is_numeric_sort = false, $start_date = null, $end_date = null, $status = 'active', $approvedcolumn = null, $limituser = false, $search_criteria = array(), &$total_count = null ) {

		$search_criteria['status'] = $status;

		if ( 0 === $sort_field_number ) {
			$sort_field_number = 'date_created';
		}

		$sorting = array(
			'direction' => $sort_direction,
			'key' => $sort_field_number,
			'is_numeric' => $is_numeric_sort,
		);

		$paging = array(
			'page_size' => $page_size,
			'offset' => $offset,
		);

		$search_criteria['field_filters'] = rgar( $search_criteria, 'field_filters', array() );

		if ( '' !== $search && ! is_null( $search ) ) {
			$search_criteria['field_filters'][] = array(
				'key' => null,
				'value' => $search,
				'operator' => 'contains',
			);
		}

		if ( ! is_null( $star ) ) {
			$search_criteria['field_filters'][] = array(
				'key' => 'is_starred',
				'value' => (int) $star,
			);
		}

		if ( ! is_null( $read ) ) {
			$search_criteria['field_filters'][] = array(
				'key' => 'is_read',
				'value' => (int) $read,
			);
		}

		if ( $start_date ) {
			$search_criteria['start_date'] = $start_date;
		}

		if ( $end_date ) {
			$search_criteria['end_date'] = $end_date;
		}

		if ( $limituser ) {
			$current_user = wp_get_current_user();

			$search_criteria['field_filters'][] = array(
				'key' => 'created_by',
				'operator' => 'is',
				'value' => $current_user->ID,
			);
		}

		if ( $approvedcolumn ) {
			$search_criteria['field_filters']['mode'] = 'all';
		}

		$return = GFAPI::get_entries( $form_id, $search_criteria, $sorting, $paging, $total_count );

		// Gravity Forms 2.3 supports smart approval out of the box. Before then, nope!
		if ( ! self::use_gf_23_db() ) {

			$entry_ids = array();
			foreach ( $return as $l ) {
				$entry_ids[] = $l['id'];
			}

			$meta_values = gform_get_meta_values_for_entries( $entry_ids, array( 'is_approved' ) );

			foreach ( $return as $key => $lead ) {

				/**
				 * @var object $meta_value {
				 * @type string $lead_id Entry ID
				 * @type string $is_approved 'Approved' if approved; '0' if not
				 * }
				 */
				foreach ( $meta_values as $meta_value ) {
					if ( rgobj( $meta_value, 'lead_id' ) === (string) $lead['id'] && '0' === rgobj( $meta_value, 'is_approved' ) ) {
						unset( $return[ $key ] );
						$total_count--;
					}
				}
			}
		}

		// To avoid PHP warnings.
		$approved   = $first_item_index = $search_query = '';
		$is_numeric = $is_numeric_sort;

		// Used by at least the show_only_user_entries() method
		$return = apply_filters( 'kws_gf_directory_lead_filter', $return, compact( 'approved', 'sort_field_number', 'sort_direction', 'search_query', 'search_criteria', 'first_item_index', 'page_size', 'star', 'read', 'is_numeric', 'start_date', 'end_date', 'status', 'approvedcolumn', 'limituser' ) );

		return $return;
	}

	/**
	 * Is the entry `created_by` equal to the current WP User ID?
	 *
	 * @param array $lead Entry array
	 *
	 * @return bool true: same user; false: nope!
	 */
	public static function is_current_user( $lead = array() ) {
		$current_user = wp_get_current_user();

		if ( empty( $lead['created_by'] ) ) {
			return false;
		}

		return ( (int) $current_user->ID === (int) $lead['created_by'] );
	}

	/**
	 * @deprecated 4.0
	 */
	public static function show_only_user_entries( $leads = array(), $settings = array() ) {
		_deprecated_function( __METHOD__, '4.0' );
		return $leads;
	}

	/**
	 * Modify how the anchor text is displayed based on filters
	 *
	 * @param string $value Original anchor text (eg: "https://www.example.com?query=string")
	 *
	 * @return string Modified anchor text (eg: "example.com")
	 */
	public static function directory_anchor_text( $value = '' ) {

		if ( apply_filters( 'kws_gf_directory_anchor_text_striphttp', true ) ) {
			$value = str_replace( 'http://', '', $value );
			$value = str_replace( 'https://', '', $value );
		}

		if ( apply_filters( 'kws_gf_directory_anchor_text_stripwww', true ) ) {
			$value = str_replace( 'www.', '', $value );
		}
		if ( apply_filters( 'kws_gf_directory_anchor_text_rootonly', true ) ) {
			$value = preg_replace( '/(.*?)\/(.+)/ism', '$1', $value );
		}
		if ( apply_filters( 'kws_gf_directory_anchor_text_nosubdomain', true ) ) {
			$value = preg_replace( '/((.*?)\.)+(.*?)\.(.*?)/ism', '$3.$4', $value );
		}
		if ( apply_filters( 'kws_gf_directory_anchor_text_noquerystring', true ) ) {
			$ary   = explode( '?', $value );
			$value = $ary[0];
		}

		return $value;
	}

	public static function r( $content, $die = false ) {
		echo '<pre>' . print_r( $content, true ) . '</pre>';
		if ( $die ) {
			die();
		}
	}

	private static function prep_address_field( $field ) {
		return ! empty( $field ) ? GFCommon::trim_all( $field ) : '';
	}

	public static function format_address( $address = array(), $linknewwindow = false ) {
		$address_field_id = @self::prep_address_field( $address['id'] );
		$street_value     = @self::prep_address_field( $address[ $address_field_id . '.1' ] );
		$street2_value    = @self::prep_address_field( $address[ $address_field_id . '.2' ] );
		$city_value       = @self::prep_address_field( $address[ $address_field_id . '.3' ] );
		$state_value      = @self::prep_address_field( $address[ $address_field_id . '.4' ] );
		$zip_value        = @self::prep_address_field( $address[ $address_field_id . '.5' ] );
		$country_value    = @self::prep_address_field( $address[ $address_field_id . '.6' ] );

		$address = $street_value;
		$address .= ! empty( $address ) && ! empty( $street2_value ) ? "<br />$street2_value" : $street2_value;
		$address .= ! empty( $address ) && ( ! empty( $city_value ) || ! empty( $state_value ) ) ? "<br />$city_value" : $city_value;
		$address .= ! empty( $address ) && ! empty( $city_value ) && ! empty( $state_value ) ? ", $state_value" : $state_value;
		$address .= ! empty( $address ) && ! empty( $zip_value ) ? " $zip_value" : $zip_value;
		$address .= ! empty( $address ) && ! empty( $country_value ) ? "<br />$country_value" : $country_value;

		//adding map link
		if ( ! empty( $address ) && apply_filters( 'kws_gf_directory_td_address_map', 1 ) ) {
			$address_qs = str_replace( '<br />', ' ', $address ); //replacing <br/> with spaces
			$address_qs = urlencode( $address_qs );
			$target     = '';
			if ( $linknewwindow ) {
				$target = ' target="_blank"';
			}
			$address .= '<br/>' . apply_filters( 'kws_gf_directory_map_link', "<a href='https://maps.google.com/maps?q=$address_qs'" . $target . " class='map-it-link'>" . esc_html__( 'Map It' ) . '</a>' );
		}

		return $address;
	}

	public static function html_display_type_filter( $content = null, $type = 'table', $single = false ) {
		switch ( $type ) {
			case 'table':
				return $content;
				break;
			case 'ul':
				$content = self::convert_to_ul( $content, $single );
				break;
			case 'dl':
				$content = self::convert_to_dl( $content, $single );
				break;
		}

		return $content;
	}

	public static function convert_to_ul( $content = null, $singleUL = false ) {

		$strongHeader = apply_filters( 'kws_gf_convert_to_ul_strong_header', 1 );

		// Directory View
		if ( ! $singleUL ) {
			$content = preg_replace( '/<table([^>]*)>/ism', '<ul$1>', $content );
			$content = preg_replace( '/<\/table([^>]*)>/ism', '</ul>', $content );
			if ( $strongHeader ) {
				$content = preg_replace( '/<tr([^>]*)>\s+/', "\n\t\t\t\t\t\t\t\t\t\t\t\t<li$1><ul>", $content );
				$content = preg_replace( '/<th([^>]*)\>(.*?)\<\/th\>/', "$2</strong>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<ul>", $content );
			} else {
				$content = preg_replace( '/<tr([^>]*)>\s+/', "\n\t\t\t\t\t\t\t\t\t\t\t\t<li$1>", $content );
				$content = preg_replace( '/<th([^>]*)\>(.*?)\<\/th\>/', "$2\n\t\t\t\t\t\t\t\t\t\t\t\t\t<ul>", $content );
			}
			$content = preg_replace( '/<\/tr[^>]*>/', "\t\t\t\t\t</ul>\n\t\t\t\t\t\t\t\t\t\t\t\t</li>", $content );
			// Single listing view
		} else {
			$content = preg_replace( '/<table([^>]*)>/ism', '<ul$1>', $content );
			$content = preg_replace( '/<\/table([^>]*)>/ism', '</ul>', $content );
			if ( $strongHeader ) {
				$content = preg_replace( '/<tr([^>]*)>\s+/', "\n\t\t\t\t\t\t\t\t\t\t\t\t<li$1><strong>", $content );
				$content = preg_replace( '/<th([^>]*)\>(.*?)\<\/th\>/', "$2</strong>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<ul>", $content );
			} else {
				$content = preg_replace( '/<tr([^>]*)>\s+/', "\n\t\t\t\t\t\t\t\t\t\t\t\t<li$1>", $content );
				$content = preg_replace( '/<th([^>]*)\>(.*?)\<\/th\>/', "$2\n\t\t\t\t\t\t\t\t\t\t\t\t\t<ul>", $content );
			}
			$content = preg_replace( '/<\/tr[^>]*>/', "\t\t\t\t\t</ul>\n\t\t\t\t\t\t\t\t\t\t\t\t</li>", $content );
		}
		$content = preg_replace( '/(?:\s+)?(valign\="(?:.*?)"|width\="(?:.*?)"|cellspacing\="(?:.*?)")(?:\s+)?/ism', ' ', $content );
		$content = preg_replace( '/<\/?tbody[^>]*>/', '', $content );
		$content = preg_replace( '/<thead[^>]*>.*<\/thead>|<tfoot[^>]*>.*<\/tfoot>/is', '', $content );
		$content = preg_replace( '/\<td([^>]*)\>(\&nbsp;|)\<\/td\>/', '', $content );
		$content = preg_replace( '/\<td([^>]*)\>/', "\t\t\t\t\t<li$1>", $content );
		$content = preg_replace( '/<\/td[^>]*>/', '</li>', $content );
		$content = preg_replace( '/\s?colspan\="([^>]*?)"\s?/ism', ' ', $content );

		return $content;
	}

	public static function convert_to_dl( $content, $singleDL = false ) {
		$back = '';
		// Get the back link, if it exists
		preg_match( '/\<p\sclass="entryback"\>(.*?)\<\/p\>/', $content, $matches );
		if ( isset( $matches[0] ) ) {
			$back = $matches[0];
		}
		$content = preg_replace( '/\<p\sclass="entryback"\>(.*?)\<\/p\>/', '', $content );
		$content = preg_replace( '/<\/?table[^>]*>|<\/?tbody[^>]*>/', '', $content );
		$content = preg_replace( '/<thead[^>]*>.*<\/thead>|<tfoot[^>]*>.*<\/tfoot>/is', '', $content );
		if ( ! $singleDL ) {
			$content = preg_replace( '/<tr([^>]*)>/', '<dl$1>', $content );
			$content = preg_replace( '/<\/tr[^>]*>/', '</dl>', $content );
		} else {
			$content = preg_replace( '/<tr([^>]*)>/', '', $content );
			$content = preg_replace( '/<\/tr[^>]*>/', '', $content );
		}
		$content = preg_replace( '/\<td([^>]*)\>(\&nbsp;|)\<\/td\>/', '', $content );
		$content = preg_replace( '/\<th([^>]*)\>(.*?)<\/th\>/ism', '<dt$1>$2</dt>', $content );
		$content = preg_replace( '/<td(.*?)(title="(.*?)")?>(.*?)<\/td[^>]*>/ism', '<dt$1>$3</dt><dd>$4</dd>', $content );
		$output  = $back;
		$output .= "\n\t\t\t\t\t\t\t\t" . '<dl>';
		$output .= $content;
		$output .= "\t\t\t\t\t\t" . '</dl>';

		return $output;
	}

	public static function make_entry_link( $options = array(), $link = false, $lead_id = '', $form_id = '', $field_id = '', $field_label = '', $linkClass = '' ) {
		global $wp_rewrite, $post, $wp;
		extract( $options );
		$entrylink = ( empty( $link ) || '&nbsp;' === $link ) ? $field_label : $link; //$entrylink;

		$entrytitle = apply_filters( 'kws_gf_directory_detail_title', apply_filters( 'kws_gf_directory_detail_title_' . $lead_id, $entrytitle ) );

		if ( ! empty( $lightboxsettings['entry'] ) ) {
			$href = wp_nonce_url( GF_DIRECTORY_URL . "includes/entry-details.php?leadid=$lead_id&amp;form={$form_id}&amp;post={$post->ID}", sprintf( 'view-%d-%d', $lead_id, $form_id ), 'view' );
			if ( wp_script_is( 'colorbox', 'registered' ) ) {
				$linkClass = ' class="colorbox lightbox" rel="directory_all directory_entry"';
			} else if ( wp_script_is( 'thickbox', 'registered' ) ) {
				$linkClass = ' class="thickbox lightbox" rel="directory_all directory_entry"';
			}
		} else {
			$multisite = ( function_exists( 'is_multisite' ) && is_multisite() && 1 == $wpdb->blogid );
			if ( $wp_rewrite->using_permalinks() ) {
				// example.com/example-directory/entry/4/14/
				if ( isset( $post->ID ) ) {
					$url = get_permalink( $post->ID );
				} else {
					$url = parse_url( add_query_arg( array(), home_url() ) );
					$url = $url['path'];
				}
				$href = trailingslashit( $url ) . sanitize_title( apply_filters( 'kws_gf_directory_endpoint', 'entry' ) ) . '/' . $form_id . apply_filters( 'kws_gf_directory_endpoint_separator', '/' ) . $lead_id . '/';

				$href = add_query_arg(
					array(
						'gf_search'  => ! empty( $_REQUEST['gf_search'] ) ? $_REQUEST['gf_search'] : null,
						'sort'       => isset( $_REQUEST['sort'] ) ? $_REQUEST['sort'] : null,
						'dir'        => isset( $_REQUEST['dir'] ) ? $_REQUEST['dir'] : null,
						'pagenum'    => isset( $_REQUEST['pagenum'] ) ? $_REQUEST['pagenum'] : null,
						'start_date' => isset( $_REQUEST['start_date'] ) ? $_REQUEST['start_date'] : null,
						'end_date'   => isset( $_REQUEST['start_date'] ) ? $_REQUEST['end_date'] : null,
					),
					$href
				);
			} else {
				// example.com/?page_id=24&leadid=14&form=4
				$href = wp_nonce_url(
					add_query_arg(
						array(
							'leadid' => $lead_id,
							'form'   => $form_id,
						)
					),
					sprintf( 'view-%d-%d', $lead_id, $form_id ),
					'view'
				);
			}
		}

		// If this is a preview, add preview arguments to the link.
		// @since 3.5
		if ( ! empty( $_GET['preview'] ) && ! empty( $_GET['preview_id'] ) && ! empty( $_GET['preview_nonce'] ) ) {
			if ( current_user_can( 'edit_posts' ) ) {
				$href = add_query_arg(
					array(
						'preview'       => $_GET['preview'],
						'preview_id'    => $_GET['preview_id'],
						'preview_nonce' => $_GET['preview_nonce'],
					),
					$href
				);
			}
		}

		$value = '<a href="' . esc_url( $href ) . '"' . $linkClass . ' title="' . $entrytitle . '">' . $entrylink . '</a>';

		return $value;
	}

	public static function get_lead_count( $form_id, $search, $star = null, $read = null, $column, $approved = false, $leads = array(), $start_date = null, $end_date = null, $limituser = false, $search_criterias ) {
		global $wpdb, $current_user;

		if ( ! is_numeric( $form_id ) ) {
			return '';
		}
	}

	public static function check_meta_approval( $lead_id ) {
		return gform_get_meta( $lead_id, 'is_approved' );
	}

	public static function check_approval( $lead, $column, $smartapproval = false ) {

		$approved = self::check_meta_approval( $lead['id'] ) || ! empty( $lead[ $column ] );

		// Approval isn't set yet
		if ( ! $approved && ! empty( $smartapproval ) ) {
			$approved = false === gform_get_meta( $lead['id'], 'is_approved' );
		}

		return $approved;
	}

	public static function hide_in_directory( $form, $field_id ) {
		return self::check_hide_in( 'hideInDirectory', $form, $field_id );
	}

	public static function hide_in_single( $form, $field_id ) {
		return self::check_hide_in( 'hideInSingle', $form, $field_id );
	}

	public static function check_hide_in( $type, $form, $field_id ) {
		foreach ( $form['fields'] as $field ) {
			if ( floor( $field_id ) === floor( $field->id ) && ! empty( $field->{$type} ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * get field property value, for a specific field_id on a $form
	 *
	 * @since  3.5
	 */
	public static function get_field_property( $property, $form, $field_id = '' ) {
		if ( empty( $property ) || empty( $form ) || '' === $field_id ) {
			return false;
		}
		foreach ( $form['fields'] as $field ) {

			if ( floor( $field_id ) === floor( $field->id ) && ! empty( $field[ $property ] ) ) {
				return $field[ $property ];
			}
		}

		return false;
	}

	/**
	 * get field properties, for a specific field_id on a $form
	 *
	 * @since 3.5
	 *
	 * @param  array $form GF Form array
	 * @param  string $field_id Field ID
	 *
	 * @return boolean|array           If the field matches the searched-for field ID, return the field array. Otherwise, return false.
	 */
	public static function get_field_properties( $form, $field_id = '' ) {
		if ( empty( $form ) || '' === $field_id ) {
			return false;
		}

		foreach ( $form['fields'] as $field ) {
			if ( floor( $field_id ) === floor( $field->id ) ) {
				return $field;
			}
		}

		return false;
	}

	/**
	 * Deprecated.
	 *
	 * @deprecated 3.5
	 */
	public static function remove_admin_only() {
	}

	public static function remove_approved_column( $type = 'form', $fields, $approvedcolumn ) {

		foreach ( $fields as $key => $column ) {
			if ( (int) floor( $column['id'] ) === (int) floor( $approvedcolumn ) ) {
				unset( $fields[ "{$key}" ] );
			}
		}

		return $fields;
	}


	/**
	 * Filter columns and fields when generating directory or single entry view based on Admin Only fields, or "hide from directory" fields or (since 3.5) only visible if user is logged in.
	 *
	 * This method replaces GFDirectory::remove_admin_only() in 3.5
	 *
	 * @since  3.5
	 * @access public
	 * @static
	 *
	 * @param mixed $leads
	 * @param mixed $admin_only
	 * @param mixed $approved
	 * @param mixed $is_leads
	 * @param bool $is_single (default: false)
	 * @param bool $show_admin_only (default: false)
	 * @param mixed $form
	 *
	 * @return array
	 */
	public static function remove_hidden_fields( $leads, $admin_only, $approved, $is_leads, $is_single = false, $show_admin_only = false, $form ) {

		if ( empty( $admin_only ) || ! is_array( $admin_only ) ) {
			$admin_only = array();
		}

		if ( empty( $leads ) || ! is_array( $leads ) ) {
			return $leads;
		}

		if ( $is_leads ) {

			foreach ( $leads as $index => $lead ) {

				if ( ! $lead ) {
					unset( $leads[ $index ] );
					continue;
				}

				// the field_ids are the numeric array keys of a lead
				$field_ids = array_filter( array_keys( $lead ), 'is_int' );

				foreach ( $field_ids as $id ) {
					if ( self::check_hide_field_conditions( $id, $admin_only, $approved, $is_single, $show_admin_only, $form ) ) {
						unset( $leads[ $index ][ $id ] );
					}
				}
			}

			return $leads;

		} else {

			// the KEY = field_id (to be used to check directory columns)
			foreach ( $leads as $key => $column ) {

				if ( self::check_hide_field_conditions( $key, $admin_only, $approved, $is_single, $show_admin_only, $form ) ) {
					unset( $leads[ $key ] );
				}
			}

			return $leads;
		}

	}

	/** returns true if field should be hidden / returns false if not , since 3.5 */
	public static function check_hide_field_conditions( $field_id, $admin_only, $approved, $is_single = false, $show_admin_only = false, $form ) {

		$properties = self::get_field_properties( $form, $field_id );
		if ( empty( $properties ) ) {
			return false;
		}

		//check if set to be hidden in directory or in single entry view
		if ( ( $is_single && ! empty( $properties['hideInSingle'] ) ) || ( ! $is_single && ! empty( $properties['hideInDirectory'] ) ) ) {
			return true;
		}

		// check if is and admin only field and remove if not authorized to be shown
		if ( ! $show_admin_only && @in_array( $field_id, $admin_only ) && $field_id != $approved && $field_id != floor( $approved ) ) {
			return true;
		}

		//check if field is only visible for logged in users, and in that case, check capabilities level
		if ( ! empty( $properties['visibleToLoggedIn'] ) && ! current_user_can( $properties['visibleToLoggedInCap'] ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Adapted from forms_model.php, RGFormsModel::save_lead($Form, $lead)
	 *
	 * @param  array $form Form object.
	 * @param  array $lead Lead object
	 *
	 * @return void
	 */
	public static function save_lead( $form, &$lead ) {
		global $wpdb;

		if ( is_admin() && ! GFCommon::current_user_can_any( 'gravityforms_edit_entries' ) ) {
			die( __( "You don't have adequate permission to edit entries.", 'gravityforms' ) );
		}

		// Create a new entry with just an ID, if null
		if ( null === $lead ) {
			$temp_entry = array(
				'form_id' => $form['id'],
				'user_agent' => RGFormsModel::truncate( $_SERVER['HTTP_USER_AGENT'], 250 ),
			);

			//reading newly created lead id
			$entry_id = GFAPI::add_entry( $temp_entry );

			unset( $temp_entry );

			$lead = GFAPI::get_entry( $entry_id );
		}

		if ( self::use_gf_23_db() ) {
			$entry_meta_table = RGFormsModel::get_entry_meta_table_name();
			$current_fields = $wpdb->get_results( $wpdb->prepare( "SELECT id, meta_key FROM $entry_meta_table WHERE entry_id=%d", $lead['id'] ) );
		} else {
			$lead_detail_table = RGFormsModel::get_lead_details_table_name();
			$current_fields    = $wpdb->get_results( $wpdb->prepare( "SELECT id, field_number FROM $lead_detail_table WHERE lead_id=%d", $lead['id'] ) );
		}

		$original_post_id = rgget( 'post_id', $lead );

		$total_fields       = array();
		$calculation_fields = array();
		$recalculate_total  = false;

		foreach ( $form['fields'] as $field ) {

			//Ignore fields that are marked as display only
			if ( rgget( 'displayOnly', $field ) && 'password' !== $field->type ) {
				continue;
			}

			//ignore pricing fields in the entry detail
			if ( 'entry' === GFForms::get( 'view' ) && GFCommon::is_pricing_field( $field->type ) ) {
				continue;
			}

			//process total field after all fields have been saved
			if ( 'total' == $field->type ) {
				$total_fields[] = $field;
				continue;
			}

			//only save fields that are not hidden (except on entry screen)
			if ( GFForms::get( 'view' ) === 'entry' || ! GFFormsModel::is_field_hidden( $form, $field, array(), $lead ) ) {

				// process calculation fields after all fields have been saved (moved after the is hidden check)
				if ( $field->has_calculation() ) {
					$calculation_fields[] = $field;
					continue;
				}

				if ( 'post_category' == $field->type ) {
					$field = GFCommon::add_categories_as_choices( $field, '' );
				}

				if ( isset( $field->inputs ) && is_array( $field->inputs ) ) {
					foreach ( $field->inputs as $input ) {
						GFFormsModel::save_input( $form, $field, $lead, $current_fields, $input['id'] );
					}
				} else {
					GFFormsModel::save_input( $form, $field, $lead, $current_fields, $field->id );
				}
			}

			if ( method_exists( 'GFFormsModel', 'commit_batch_field_operations' ) ) {
				GFFormsModel::commit_batch_field_operations();
			}

			//Refresh lead to support conditionals (not optimal but...)
			$lead = GFFormsModel::get_lead( $lead['id'] );
		}

		if ( ! empty( $calculation_fields ) ) {

			if ( method_exists( 'GFFormsModel', 'begin_batch_field_operations' ) ) {
				GFFormsModel::begin_batch_field_operations();
			}

			foreach ( $calculation_fields as $calculation_field ) {

				if ( isset( $calculation_field->inputs ) && is_array( $calculation_field->inputs ) ) {
					foreach ( $calculation_field->inputs as $input ) {
						RGFormsModel::save_input( $form, $calculation_field, $lead, $current_fields, $input['id'] );
						RGFormsModel::refresh_lead_field_value( $lead['id'], $input['id'] );
					}
				} else {
					RGFormsModel::save_input( $form, $calculation_field, $lead, $current_fields, $calculation_field->id );
					RGFormsModel::refresh_lead_field_value( $lead['id'], $calculation_field->id );
				}
			}

			if ( method_exists( 'GFFormsModel', 'commit_batch_field_operations' ) ) {
				GFFormsModel::commit_batch_field_operations();
			}

			RGFormsModel::refresh_product_cache( $form, $lead = RGFormsModel::get_lead( $lead['id'] ) );
		}

		//saving total field as the last field of the form.
		if ( ! empty( $total_fields ) ) {

			if ( method_exists( 'GFFormsModel', 'begin_batch_field_operations' ) ) {
				GFFormsModel::begin_batch_field_operations();
			}

			foreach ( $total_fields as $total_field ) {
				GFCommon::log_debug( 'Saving total field.' );

				RGFormsModel::save_input( $form, $total_field, $lead, $current_fields, $total_field->id );
			}

			if ( method_exists( 'GFFormsModel', 'commit_batch_field_operations' ) ) {
				GFFormsModel::commit_batch_field_operations();
			}
		}
	}

	/**
	 * (Deprecated) Render [directory] shortcode content.
	 *
	 * @since 4.2 moved to GFDirectory_Shortcode class.
	 *
	 * @param  array  $atts    Array of attributes passed with the shortcode.
	 * @return string $content HTML content.
	 */
	public static function make_directory( $atts ) {
		_deprecated_function( __METHOD__, '4.2', 'GFDirectory_Shortcode::make_directory' );
		return GFDirectory_Shortcode::make_directory( $atts );
	}

	/**
	 * (Deprecated) Get grid columns.
	 *
	 * @since 4.2 moved to GFDirectory_Shortcode class.
	 *
	 * @param  int   $form_id          Gravity form ID.
	 * @param  bool  $input_label_only Input label only or not.
	 * @return array $columns          Array of grid columns.
	 */
	public static function get_grid_columns( $form_id, $input_label_only = false ) {
		_deprecated_function( __METHOD__, '4.2', 'GFDirectory_Shortcode::get_grid_columns' );
		return GFDirectory_Shortcode::get_grid_columns( $form_id, $input_label_only );

	}

	/**
	 * (Deprecated) Get entrylink columns.
	 *
	 * @since 4.2 moved to GFDirectory_Shortcode class.
	 *
	 * @param  array $form    Gravity form ID.
	 * @param  array $entry   Entry.
	 * @return array $columns Array of columns.
	 */
	public static function get_entrylink_column( $form, $entry = false ) {
		_deprecated_function( __METHOD__, '4.2', 'GFDirectory_Shortcode::get_entrylink_column' );
		return GFDirectory_Shortcode::get_entrylink_column( $form, $entry );
	}
}
