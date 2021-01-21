<?php

add_action( 'init', array( 'GFDirectory_EditForm', 'initialize' ) );

class GFDirectory_EditForm {

	public static function initialize() {
		new self();
	}

	public function __construct() {

		add_action( 'admin_init', array( &$this, 'process_exterior_pages' ), 9 );

		if ( self::is_gravity_page() ) {

			add_filter( 'admin_head', array( &$this, 'directory_admin_head' ) );

			if ( isset( $_REQUEST['id'] ) || self::is_gravity_page( 'gf_entries' ) ) {
				add_filter( 'gform_tooltips', array( &$this, 'directory_tooltips' ) ); //Filter to add a new tooltip
				add_action( 'gform_editor_js', array( &$this, 'editor_script' ) ); //Action to inject supporting script to the form editor page

				// No need to add via JS any more.
				if ( class_exists( 'GFForms' ) && version_compare( GFForms::$version, '2.0', '>=' ) ) {
					add_filter( 'gform_toolbar_menu', array( $this, 'toolbar_menu_item' ), 10, 2 );
				} else {
					add_action( 'admin_head', array( &$this, 'toolbar_links' ) ); //Action to inject supporting script to the form editor page
				}
				add_action( 'gform_field_advanced_settings', array( &$this, 'use_as_entry_link_settings' ), 10, 2 );
				add_filter( 'gform_add_field_buttons', array( &$this, 'add_field_buttons' ) );
				add_action( 'gform_editor_js_set_default_values', array( &$this, 'directory_add_default_values' ) );
			}
		}
	}

	// From gravityforms.php
	public static function process_exterior_pages() {
		if ( rgempty( 'gf_page', $_GET ) ) {
			return;}

		//ensure users are logged in
		if ( ! is_user_logged_in() ) {
			auth_redirect();}

		switch ( rgget( 'gf_page' ) ) {
			case 'directory_columns':
				require_once( GF_DIRECTORY_PATH . 'includes/class-select-directory-columns.php' );
				break;
		}
		exit();
	}

	public function directory_add_default_values() {
		?>
		case "entrylink" :
				field.label = "<?php echo esc_js( __( 'Go to Entry', 'gravity-forms-addons' ) ); ?>";
				field.adminOnly = true;
				field.choices = null;
				field.inputs = null;
				field.hideInSingle = true;
				field.useAsEntryLink = 'label';
				field.type = 'hidden';
				field.disableMargins = true;

		break;

		case 'usereditlink':
				field.label = "<?php echo esc_js( __( 'Edit', 'gravity-forms-addons' ) ); ?>";

				field.adminOnly = true;
				field.choices = null;
				field.inputs = null;
				field.hideInSingle = false;
				field.useAsEntryLink = false;
				field.type = 'hidden';
				field.disableMargins = 2;

		break;

		case 'directoryapproved':
				field.label = "<?php echo esc_js( __( 'Approved? (Admin-only)', 'gravity-forms-addons' ) ); ?>";

				field.adminLabel = "<?php echo esc_js( __( 'Approved?', 'gravity-forms-addons' ) ); ?>";
				field.adminOnly = true;

				field.choices = null;
				field.inputs = null;
				field.gf_directory_approval = true;

				if(!field.choices)
					field.choices = new Array(new Choice("<?php echo esc_js( __( 'Approved', 'gravity-forms-addons' ) ); ?>"));

				field.inputs = new Array();
				for(var i=1; i<=field.choices.length; i++)
					field.inputs.push(new Input(field.id + (i/10), field.choices[i-1].text));

				field.hideInDirectory = true;
				field.hideInSingle = true;
				field.type = 'checkbox';

		break;
		<?php
	}

	public function directory_admin_head() {
		global $process_bulk_update_message;

		// Entries screen shows first form's entries by default, if not specified
		if ( isset( $_GET['id'] ) ) {
			$formID = intval( $_GET['id'] );
		} else {
			if ( class_exists( 'RGFormsModel' ) ) {
				$forms = RGFormsModel::get_forms( null, 'title' );
				$formID = $forms[0]->id;
			}
		}

		if ( ! ( self::is_gravity_page( 'gf_entries' ) && ! self::is_gravity_page( 'gf_edit_forms' ) ) ) {
			return;
		}

		if ( ! isset( $formID ) ) {
			return;
		}

		// Don't display on single entry view.
		if ( ! empty( $_GET['view'] ) && 'entry' === $_GET['view'] ) {
			return;
		}

		include_once( GF_DIRECTORY_PATH . 'includes/views/html-directory-head-admin.php' );
	}

	public function use_as_entry_link_settings( $position, $form_id ) {
		//create settings on position 50 (right after Admin Label)
		if ( -1 === $position ) {
			include_once( GF_DIRECTORY_PATH . 'includes/views/html-gf-tab-directory.php' );
		}
	}

	/**
	 * Add "Directory Columns" item to GF toolbar in GF 2.0+
	 *
	 * @param array $menu_items Menu items in GF toolbar
	 * @param int $form_id Form ID
	 *
	 * @return array
	 */
	public function toolbar_menu_item( $menu_items = array(), $form_id = 0 ) {

		wp_enqueue_style( 'thickbox' );

		$entries_capabilities = array(
			'gravityforms_view_entries',
			'gravityforms_edit_entries',
			'gravityforms_delete_entries',
		);

		$menu_items['directory_columns'] = array(
			'label'        => __( 'Directory Columns', 'gravity-forms-addons' ),
			'icon'         => '<i class="dashicons dashicons-welcome-widgets-menus" style="line-height:17px"></i>',
			'title'        => __( 'Modify Gravity Forms Directory Columns', 'gravity-forms-addons' ),
			'url'          => sprintf( '?gf_page=directory_columns&id=%d&add=entry&TB_iframe=true&height=600&width=700', $form_id ),
			'menu_class'   => 'gf_form_toolbar_directory',
			'link_class'   => 'thickbox',
			'capabilities' => $entries_capabilities,
			'priority'     => 200,
		);

		return $menu_items;
	}

	/**
	* Add "Directory Columns" link to GF toolbar. No longer used after 2.0
	* @see toolbar_menu_item
	* @return void
	*/
	public function toolbar_links() {

		wp_enqueue_style( 'thickbox' );

		?>
		<script type='text/javascript'>
			jQuery(document).ready(function($) {
				var url = '
				<?php
				echo esc_url_raw(
					add_query_arg(
						array(
							'gf_page' => 'directory_columns',
							'id' => intval( $_GET['id'] ),
							'TB_iframe' => 'true',
							'height' => 600,
							'width' => 700,
						),
						admin_url()
					)
				);
				?>
							';
				$link = $('<li class="gf_form_toolbar_preview gf_form_toolbar_directory" id="gf_form_toolbar_directory"><a href="'+url+'" class="thickbox" title="<?php echo esc_js( __( 'Modify Gravity Forms Directory Columns', 'gravity-forms-addons' ) ); ?>"><i class="dashicons dashicons-welcome-widgets-menus" style="line-height:17px"></i> <?php echo esc_js( __( 'Directory Columns', 'gravity-forms-addons' ) ); ?></a></li>');
				$('#gf_form_toolbar_links').append($link);
			});
		</script>
		<?php
	}

	public function editor_script() {
		include_once( GF_DIRECTORY_PATH . 'includes/views/html-editor-script.php' );
	}

	public function directory_tooltips( $tooltips ) {
		$tooltips['kws_gf_directory_use_as_link_to_single_entry'] = sprintf(
			// Translators: placeholder: H6 tags.
			esc_html__( '%1$sLink to single entry using this field%2$sIf you would like to link to the single entry view using this link, check the box.', 'gravity-forms-addons' ),
			'<h6>',
			'</h6>'
		);
		$tooltips['kws_gf_directory_hide_in_directory_view'] = sprintf(
			// Translators: placeholder: H6 tags.
			esc_html__( '%1$sHide in Directory View%2$sIf checked, this field will not be shown in the directory view, even if it is visible in the %3$sDirectory Columns%4$s. If this field is Admin Only (set in the Advanced tab), it will be hidden in the directory view unless "Show Admin-Only columns" is enabled in the directory. Even if "Show Admin-Only columns" is enabled, checking this box will hide the column in the directory view.', 'gravity-forms-addons' ),
			'<h6>',
			'</h6>',
			sprintf(
				'<a class="thickbox" title="%s" href="' . add_query_arg(
					array(
						'gf_page' => 'directory_columns',
						'id' => @$_GET['id'],
						'TB_iframe' => 'true',
						'height' => 600,
						'width' => 700,
					),
					esc_url( admin_url() )
				) . '">',
				esc_html__( 'Modify Directory Columns', 'gravity-forms-addons' )
			),
			'</a>'
		);
		$tooltips['kws_gf_directory_hide_in_single_entry_view'] = sprintf(
			// Translators: placeholder: H6 tags.
			esc_html__( '%1$sHide in Single Entry View%2$sIf checked, this field will not be shown in the single entry view of the directory.', 'gravity-forms-addons' ),
			'<h6>',
			'</h6>'
		);
		$tooltips['kws_gf_directory_use_field_as_search_filter'] = sprintf(
			// Translators: placeholder: H6 tags.
			esc_html__( '%1$sDirectory Search Field%2$sIf checked, add search fields to the Directory search form. If this field is a text field, a text search input will be added that will search only this field. Otherwise, the field choices will be used to populate a dropdown menu search input. Example: if the field has choices "A", "B", and "C", the search dropdown will have those items as choices in a dropdown search field.', 'gravity-forms-addons' ),
			'<h6>',
			'</h6>'
		);
		return $tooltips;
	}

	//Returns true if the current page is one of Gravity Forms pages. Returns false if not
	private static function is_gravity_page( $page = array() ) {
		return GFDirectory::is_gravity_page( $page );
	}

	public function add_field_buttons( $field_groups ) {
		$directory_fields = array(
			'name' => 'directory_fields',
			'label' => 'Directory Fields',
			'fields' => array(
				array(
					'class' => 'button',
					'value' => esc_attr__( 'Approved', 'gravity-forms-addons' ),
					'onclick' => "StartAddField('directoryapproved');",
				),
				array(
					'class' => 'button',
					'value' => esc_attr__( 'Entry Link', 'gravity-forms-addons' ),
					'onclick' => "StartAddField('entrylink');",
				),
				array(
					'class' => 'button',
					'value' => esc_attr__( 'User Edit Link', 'gravity-forms-addons' ),
					'onclick' => "StartAddField('usereditlink');",
				),
			),
		);

		array_push( $field_groups, $directory_fields );

		return $field_groups;
	}
}
