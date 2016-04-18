<?php

register_activation_hook( __FILE__, array( 'GFDirectory_Admin', 'activation' ) );
add_action( 'init', array( 'GFDirectory_Admin', 'initialize' ) );

class GFDirectory_Admin {

	static function initialize() {
		new GFDirectory_Admin;
	}

	function __construct() {

		if ( ! is_admin() ) {
			return;
		}

		$settings = GFDirectory::get_settings();

		add_action( 'admin_notices', array( &$this, 'gf_warning' ) );
		add_filter( 'gform_pre_render', array( 'GFDirectory_Admin', 'show_field_ids' ) );

		//creates a new Settings page on Gravity Forms' settings screen
		if ( GFDirectory::has_access( "gravityforms_directory" ) ) {
			RGForms::add_settings_page( "Directory & Addons", array( &$this, "settings_page" ), "" );
		}
		add_filter( "gform_addon_navigation", array( &$this, 'create_menu' ) ); //creates the subnav left menu

		//Adding "embed form" button
		add_action( 'media_buttons', array( &$this, 'add_form_button' ), 30 );

		if ( in_array( RG_CURRENT_PAGE, array( 'post.php', 'page.php', 'page-new.php', 'post-new.php' ) ) ) {
			add_action( 'admin_footer', array( &$this, 'add_mce_popup' ) );
			wp_enqueue_script( "jquery-ui-datepicker" );
		}


		if ( ! empty( $settings['modify_admin'] ) ) {
			add_action( 'admin_head', array( &$this, 'admin_head' ), 1 );
		}

		self::process_bulk_update();
	}

	public static function process_bulk_update() {
		global $process_bulk_update_message;

		if ( RGForms::post( "action" ) === 'bulk' ) {
			check_admin_referer( 'gforms_entry_list', 'gforms_entry_list' );

			$bulk_action = ! empty( $_POST["bulk_action"] ) ? $_POST["bulk_action"] : $_POST["bulk_action2"];
			$leads       = $_POST["lead"];

			$entry_count = count( $leads ) > 1 ? sprintf( __( "%d entries", "gravityforms" ), count( $leads ) ) : __( "1 entry", "gravityforms" );

			$bulk_action = explode( '-', $bulk_action );
			if ( ! isset( $bulk_action[1] ) || empty( $leads ) ) {
				return false;
			}

			switch ( $bulk_action[0] ) {
				case "approve":
					self::directory_update_bulk( $leads, 1, $bulk_action[1] );
					$process_bulk_update_message = sprintf( __( "%s approved.", "gravity-forms-addons" ), $entry_count );
					break;

				case "unapprove":
					self::directory_update_bulk( $leads, 0, $bulk_action[1] );
					$process_bulk_update_message = sprintf( __( "%s disapproved.", "gravity-forms-addons" ), $entry_count );
					break;
			}
		}
	}

	static private function directory_update_bulk( $leads, $approved, $form_id ) {
		global $_gform_directory_approvedcolumn;

		if ( empty( $leads ) || ! is_array( $leads ) ) {
			return false;
		}

		$_gform_directory_approvedcolumn = empty( $_gform_directory_approvedcolumn ) ? self::globals_get_approved_column( $_POST['form_id'] ) : $_gform_directory_approvedcolumn;

		$approved = empty( $approved ) ? 0 : 'Approved';
		foreach ( $leads as $lead_id ) {
			GFDirectory::directory_update_approved( $lead_id, $approved, $form_id );
		}
	}

	// If the classes don't exist, the plugin won't do anything useful.
	function gf_warning() {
		global $pagenow;
		$message = '';

		if ( $pagenow != 'plugins.php' ) {
			return;
		}

		if ( ! GFDirectory::is_gravityforms_installed() ) {
			if ( file_exists( WP_PLUGIN_DIR . '/gravityforms/gravityforms.php' ) ) {
				$message .= sprintf( esc_html__( '%sGravity Forms is installed but not active. %sActivate Gravity Forms%s to use the Gravity Forms Directory & Addons plugin.%s', 'gravity-forms-addons' ), '<p>', '<a href="' . wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=gravityforms/gravityforms.php' ), 'activate-plugin_gravityforms/gravityforms.php' ) . '" style="font-weight:strong;">', '</a>', '</p>' );
			} else {
				$message = sprintf( esc_html__( '%sGravity Forms cannot be found%s

				The %sGravity Forms plugin%s must be installed and activated for the Gravity Forms Addons plugin to work.

				If you haven\'t installed the plugin, you can %3$spurchase the plugin here%4$s. If you have, and you believe this notice is in error, %5$sstart a topic on the plugin support forum%4$s.

				%6$s%7$sBuy Gravity Forms%4$s%8$s
				', 'gravity-forms-addons' ), '<strong>', '</strong>', "<a href='http://katz.si/gravityforms'>", '</a>', '<a href="http://wordpress.org/tags/gravity-forms-addons?forum_id=10#postform">', '<p class="submit">', "<a href='http://katz.si/gravityforms' style='color:white!important' class='button button-primary'>", '</p>' );
			}
		}
		if ( ! empty( $message ) ) {
			echo '<div id="message" class="error">' . wpautop( $message ) . '</div>';
		} else if ( $message = get_transient( 'kws_gf_activation_notice' ) ) {
			echo '<div id="message" class="updated">' . wpautop( $message ) . '</div>';
			delete_transient( 'kws_gf_activation_notice' );
		}
	}

	public function activation() {
		self::add_activation_notice();
	}

	public function add_activation_notice() {
#		if(!get_option("gf_addons_settings")) {
		$message = sprintf( esc_html__( 'Congratulations - the Gravity Forms Directory & Addons plugin has been installed. %sGo to the settings page%s to read usage instructions and configure the plugin default settings. %sGo to settings page%s', 'gravity-forms-addons' ), '<a href="' . admin_url( 'admin.php?page=gf_settings&addon=Directory+%26+Addons&viewinstructions=true' ) . '">', '</a>', '<p class="submit"><a href="' . admin_url( 'admin.php?page=gf_settings&addon=Directory+%26+Addons&viewinstructions=true' ) . '" class="button button-secondary">', '</a></p>' );
		set_transient( 'kws_gf_activation_notice', $message, 60 * 60 );
#		}
	}

	public function admin_head( $settings = array() ) {
		if ( empty( $settings ) ) {
			$settings = GFDirectory::get_settings();
		}

		if ( ! empty( $settings['modify_admin']['expand'] ) ) {
			if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'gf_edit_forms' && isset( $_REQUEST['id'] ) && is_numeric( $_REQUEST['id'] ) ) {
				$style = '<style>
					.gforms_edit_form_expanded ul.menu li.add_field_button_container ul,
					.gforms_edit_form_expanded ul.menu li.add_field_button_container ul ol {
						display:block!important;
					}
					#floatMenu {padding-top:1.4em!important;}
				</style>';
				$style = apply_filters( 'kws_gf_display_all_fields', $style );
				echo $style;
			}
		}

		if ( isset( $_REQUEST['page'] ) && ( $_REQUEST['page'] == 'gf_edit_forms' || $_REQUEST['page'] == 'gf_entries' ) ) {
			echo self::add_edit_js( isset( $_REQUEST['id'] ), $settings );
		}
	}

	static private function add_edit_js( $edit_forms = false, $settings = array() ) {
		?>
		<script>
			// Edit link for Gravity Forms entries
			jQuery( document ).ready( function ( $ ) {
				<?php    if(! empty( $settings['modify_admin']['expand'] ) && $edit_forms) { ?>
				var onScrollScript = window.onscroll;
				$( 'div.gforms_edit_form #add_fields #floatMenu' ).prepend( '<div class="gforms_expend_all_menus_form"><label for="expandAllMenus"><input type="checkbox" id="expandAllMenus" value="1" /> Expand All Menus</label></div>' );

				$( 'input#expandAllMenus' ).live( 'click', function ( e ) {
					if ( $( this ).is( ':checked' ) ) {
						window.onscroll = '';
						$( 'div.gforms_edit_form' ).addClass( 'gforms_edit_form_expanded' );
						//$('ul.menu li .button-title-link').unbind().die(); // .unbind() is for the initial .click()... .die() is for the live() below
					} else {
						window.onscroll = onScrollScript;
						$( 'div.gforms_edit_form' ).removeClass( 'gforms_edit_form_expanded' );
					}
				} );

				<?php
				}
				if(! empty( $settings['modify_admin']['toggle'] ) && $edit_forms) { ?>

				$( 'ul.menu' ).addClass( 'noaccordion' );
				<?php
				}

				if(isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'gf_entries' && ! empty( $settings['modify_admin']['edit'] )) {
				?>
				// Changed from :contains('Delete') to :last-child to work with 1.6
				$( ".row-actions span:last-child" ).each( function () {
					var editLink = $( this ).parents( 'tr' ).find( '.column-title a' ).attr( 'href' );
					editLink = editLink + '&screen_mode=edit';
					//alert();
					$( this ).after( '<span class="edit">| <a title="<?php echo esc_js( __( "Edit this entry", "gravity-forms-addons" ) ); ?>" href="' + editLink + '"><?php echo esc_js( __( "Edit", "gravity-forms-addons" ) ); ?></a></span>' );
				} );
				<?php
				}

				else if(isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'gf_edit_forms' && ! empty( $settings['modify_admin']['ids'] )) {
				?>
				// Changed from :contains('Delete') to :last-child for future-proofing
				$( ".row-actions .trash" ).each( function () {
					var formID = $( this ).parents( 'tr' ).find( '.column-id' ).text();

					var title = '<?php echo esc_js( __( "Fields for Form ID %s", "gravity-forms-addons" ) ); ?>';
					title = title.replace( '%s', formID );

					$( this ).after( '<span class="edit"> | <a title="' + title + '" href="<?php echo plugins_url( "field-ids.php", __FILE__ ); ?>?id=' + formID + '&amp;show_field_ids=true&amp;TB_iframe=true&amp;height=295&amp;width=370" class="thickbox form_ids"><?php echo esc_js( __( "IDs", "gravity-forms-addons" ) ); ?></a></span>' );
				} );
				<?php } ?>
			} );
		</script>
		<?php
	}

	static function show_field_ids( $form = array() ) {
		if ( isset( $_REQUEST['show_field_ids'] ) ) {
			$form = RGFormsModel::get_form_meta( $_GET["id"] );
			$form = RGFormsModel::add_default_properties( $form );

			echo <<<EOD
		<style>

			#input_ids th, #input_ids td { border-bottom:1px solid #999; padding:.25em 15px; }
			#input_ids th { border-bottom-color: #333; font-size:.9em; background-color: #464646; color:white; padding:.5em 15px; font-weight:bold;  }
			#input_ids { background:#ccc; margin:0 auto; font-size:1.2em; line-height:1.4; width:100%; border-collapse:collapse;  }
			#input_ids strong { font-weight:bold; }
			#input_ids caption,
			#preview_hdr { display:none;}
			#input_ids caption { color:white!important;}
		</style>
EOD;

			if ( ! empty( $form ) ) {
				echo '<table id="input_ids"><caption id="input_id_caption">Fields for <strong>Form ID ' . $form['id'] . '</strong></caption><thead><tr><th>Field Name</th><th>Field ID</th></thead><tbody>';
			}
			foreach ( $form['fields'] as $field ) {
				// If there are multiple inputs for a field; ie: address has street, city, zip, country, etc.
				if ( is_array( $field['inputs'] ) ) {
					foreach ( $field['inputs'] as $input ) {
						echo "<tr><td width='50%'><strong>{$input['label']}</strong></td><td>{$input['id']}</td></tr>";
					}
				} // Otherwise, it's just the one input.
				else {
					echo "<tr><td width='50%'><strong>{$field['label']}</strong></td><td>{$field['id']}</td></tr>";
				}
			}
			if ( ! empty( $form ) ) {
				echo '</tbody></table><div style="clear:both;"></div></body></html>';
				exit();
			}
		} else {
			return $form;
		}
	}

	function add_mce_popup() {

		//Action target that displays the popup to insert a form to a post/page
		?>
		<script>
			function addslashes( str ) {
				// Escapes single quote, double quotes and backslash characters in a string with backslashes
				// discuss at: http://phpjs.org/functions/addslashes
				return (str + '').replace( /[\\"']/g, '\\$&' ).replace( /\u0000/g, '\\0' );
			}

			jQuery( 'document' ).ready( function ( $ ) {


				$( '#select_gf_directory_form .datepicker' ).each( function () {
					if ( $.fn.datepicker ) {
						var element = jQuery( this );
						var format = "yy-mm-dd";

						var image = "";
						var showOn = "focus";
						if ( element.hasClass( "datepicker_with_icon" ) ) {
							showOn = "both";
							image = jQuery( '#gforms_calendar_icon_' + this.id ).val();
						}

						element.datepicker( {
							yearRange: '-100:+10',
							showOn: showOn,
							buttonImage: image,
							buttonImageOnly: true,
							dateFormat: format
						} );
					}
				} );


				$( '#select_gf_directory_form' ).bind( 'submit', function ( e ) {
					e.preventDefault();
					var shortcode = InsertGFDirectory();
					//send_to_editor(shortcode);
					return false;
				} );


				$( document ).on( 'click', '#insert_gf_directory', function ( e ) {
					e.preventDefault();
					$( '#select_gf_directory_form' ).trigger( 'submit' );
					return;
				} );

				$( 'a.select_gf_directory' ).click( function ( e ) {
					// This auto-sizes the box
					if ( typeof tb_position == 'function' ) {
						tb_position();
					}
					return;
				} );

				// Toggle advanced settings
				$( 'a.kws_gf_advanced_settings' ).click( function ( e ) {
					e.preventDefault();
					$( '#kws_gf_advanced_settings' ).toggle();
					return false;
				} );

				function InsertGFDirectory() {
					var directory_id = jQuery( "#add_directory_id" ).val();
					if ( directory_id == "" ) {
						alert( "<?php echo esc_js( __( "Please select a form", "gravity-forms-addons" ) ); ?>" );
						jQuery( '#add_directory_id' ).focus();
						return false;
					}

					<?php
					$js = self::make_popup_options( true );

					$ids = $idOutputList = $setvalues = $vars = '';

					foreach ( $js as $j ) {
						$vars .= $j['js'] . "
						";
						$ids .= $j['idcode'] . " ";
						$setvalues .= $j['setvalue'] . "
						";
						$idOutputList .= $j['id'] . 'Output' . ' + ';
					}
					echo $vars;
					echo $setvalues;
					?>

					//var win = window.dialogArguments || opener || parent || top;
					var shortcode = "[directory form=\"" + directory_id + "\"" + <?php echo addslashes( $idOutputList ); ?>"]";
					window.send_to_editor( shortcode );
					return false;
				}
			} );

		</script>
		<div id="select_gf_directory" style="overflow-x:hidden; overflow-y:auto;display:none;">
			<form action="#" method="get" id="select_gf_directory_form">
				<div class="wrap">
					<div>
						<div style="padding:15px 15px 0 15px;">
							<h2><?php esc_html_e( "Insert A Directory", "gravity-forms-addons" ); ?></h2>
						<span>
							<?php esc_html_e( "Select a form below to add it to your post or page.", "gravity-forms-addons" ); ?>
						</span>
						</div>
						<div style="padding:15px 15px 0 15px;">
							<select id="add_directory_id">
								<option
									value="">  <?php esc_html_e( "Select a Form", "gravity-forms-addons" ); ?>  </option>
								<?php
								$forms = RGFormsModel::get_forms( 1, "title" );
								foreach ( $forms as $form ) {
									?>
									<option
										value="<?php echo absint( $form->id ) ?>"><?php echo esc_html( $form->title ) ?></option>
									<?php
								}
								?>
							</select> <br/>
							<div
								style="padding:8px 0 0 0; font-size:11px; font-style:italic; color:#5A5A5A"><?php esc_html_e( "This form will be the basis of your directory.", "gravity-forms-addons" ); ?></div>
						</div>
						<?php

						self::make_popup_options();

						?>
						<div class="submit">
							<input type="submit" class="button-primary" style="margin-right:15px;"
							       value="Insert Directory" id="insert_gf_directory"/>
							<a class="button button-secondary" style="color:#bbb;" href="#"
							   onclick="tb_remove(); return false;"><?php esc_html_e( "Cancel", "gravity-forms-addons" ); ?></a>
						</div>
					</div>
				</div>
			</form>
		</div>
		<?php
	}

	static function make_popup_options( $js = false ) {
		$i = 0;

		$defaults = GFDirectory::directory_defaults();

		$standard = array(
			array(
				'text',
				'page_size',
				20,
				sprintf( esc_html__( "Number of entries to show at once. Use %s0%s to show all entries.", 'gravity-forms-addons' ), '<code>', '</code>' ),
			),
			array(
				'select',
				'directoryview',
				array(
					array( 'value' => 'table', 'label' => esc_html__( "Table", 'gravity-forms-addons' ) ),
					array( 'value' => 'ul', 'label' => esc_html__( "Unordered List", 'gravity-forms-addons' ) ),
					array( 'value' => 'dl', 'label' => esc_html__( "Definition List", 'gravity-forms-addons' ) ),
				),
				esc_html__( "Format for directory listings (directory view)", 'gravity-forms-addons' ),
			),
			array(
				'select',
				'entryview',
				array(
					array( 'value' => 'table', 'label' => esc_html__( "Table", 'gravity-forms-addons' ) ),
					array( 'value' => 'ul', 'label' => esc_html__( "Unordered List", 'gravity-forms-addons' ) ),
					array( 'value' => 'dl', 'label' => esc_html__( "Definition List", 'gravity-forms-addons' ) ),
				),
				esc_html__( "Format for single entries (single entry view)", 'gravity-forms-addons' ),
			),
			array( 'checkbox', 'search', true, esc_html__( "Show the search field", 'gravity-forms-addons' ) ),
			array(
				'checkbox',
				'smartapproval',
				true,
				esc_html__( "Automatically convert directory into Approved-only when an Approved field is detected.", 'gravity-forms-addons' ),
			),
			array(
				'checkbox',
				'approved',
				false,
				sprintf( esc_html__( "(If Smart Approval above is not enabled) Show only entries that have been Approved (have a field in the form that is an Admin-only checkbox with a value of 'Approved'). %sNote:%s This will hide entries that have not been explicitly approved.%s", 'gravity-forms-addons' ), "<span class='description'><strong>", "</strong>", "</span>" ),
			),
		);
		if ( ! $js ) {
			echo '<ul>';
			foreach ( $standard as $o ) {
				self::make_field( $o[0], $o[1], maybe_serialize( $o[2] ), $o[3], $defaults );
			}
			echo '</ul>';
		} else {
			foreach ( $standard as $o ) {
				$out[ $i ] = self::make_popup_js( $o[0], $o[1], $defaults );
				$i ++;
			}
		}

		$content = array(
			array(
				'checkbox',
				'entry',
				true,
				esc_html__( "If there's a displayed Entry ID column, add link to each full entry", 'gravity-forms-addons' ),
			),
			#array('checkbox',  'wpautop'  ,  true, sprintf( esc_html__( "Convert bulk paragraph text to paragraphs (using the WordPress function %s)", 'gravity-forms-addons'), "<code><a href='http://codex.wordpress.org/Function_Reference/wpautop'>wpautop()</a></code>" )),
			array(
				'checkbox',
				'getimagesize',
				false,
				esc_html__( "Calculate image sizes (Warning: this may slow down the directory loading speed!)", 'gravity-forms-addons' ),
			),
			array(
				'radio',
				'postimage',
				array(
					array(
						'label'   => '<img src="' . GFCommon::get_base_url() . '/images/doctypes/icon_image.gif" /> ' . esc_html__( 'Show image icon', 'gravity-forms-addons' ),
						'value'   => 'icon',
						'default' => '1',
					),
					array( 'label' => esc_html__( 'Show full image', 'gravity-forms-addons' ), 'value' => 'image' ),
				),
				esc_html__( "How do you want images to appear in the directory?", 'gravity-forms-addons' ),
			),
			#array('checkbox', 'fulltext' , true, esc_html__("Show full content of a textarea or post content field, rather than an excerpt", 'gravity-forms-addons')),

			array(
				'date',
				'start_date',
				false,
				sprintf( esc_html__( 'Start date (in %sYYYY-MM-DD%s format)', 'gravity-forms-addons' ), '<code>', '</code>' ),
			),
			array(
				'date',
				'end_date',
				false,
				sprintf( esc_html__( 'End date (in %sYYYY-MM-DD%s format)', 'gravity-forms-addons' ), '<code>', '</code>' ),
			),
		);

		$administration = array(
			array(
				'checkbox',
				'showadminonly',
				false,
				sprintf( esc_html__( "Show Admin-Only columns %s(in Gravity Forms, Admin-Only fields are defined by clicking the Advanced tab on a field in the Edit Form view, then editing Visibility > Admin Only)%s", 'gravity-forms-addons' ), "<span class='description'>", "</span>" ),
			),
			array(
				'checkbox',
				'useredit',
				false,
				esc_html__( "Allow logged-in users to edit entries they created. Will add an 'Edit Your Entry' field to the Single Entry View.", 'gravity-forms-addons' ),
			),
			array(
				'checkbox',
				'limituser',
				false,
				esc_html__( "Display entries only the the creator of the entry (users will not see other people's entries).", 'gravity-forms-addons' ),
			),
			array(
				'checkbox',
				'adminedit',
				false,
				sprintf( esc_html__( 'Allow %sadministrators%s to edit all entries. Will add an \'Edit Your Entry\' field to the Single Entry View.', 'gravity-forms-addons' ), '<strong>', '</strong>' ),
			),
		);

		$style_label = esc_html_x( 'Style %d', 'Lightbox style', 'gravity-forms-addons' );

		$lightbox = array(

			array(
				'radio',
				'lightboxstyle',
				array(
					array(
						'label' => sprintf( $style_label, 1 ) . ' <a href="http://www.jacklmoore.com/colorbox/example1/" target="_blank">See example</a>',
						'value' => '1',
					),
					array(
						'label' => sprintf( $style_label, 2 ) . ' <a href="http://www.jacklmoore.com/colorbox/example2/" target="_blank">See example</a>',
						'value' => '2',
					),
					array(
						'label'   => sprintf( $style_label, 3 ) . ' <a href="http://www.jacklmoore.com/colorbox/example3/" target="_blank">See example</a>',
						'value'   => '3',
						'default' => '1',
					),
					array(
						'label' => sprintf( $style_label, 4 ) . ' <a href="http://www.jacklmoore.com/colorbox/example4/" target="_blank">See example</a>',
						'value' => '4',
					),
					array(
						'label' => sprintf( $style_label, 5 ) . ' <a href="http://www.jacklmoore.com/colorbox/example5/" target="_blank">See example</a>',
						'value' => '5',
					),
				),
				"What style should the lightbox use?",
			),
			array(
				'checkboxes',
				'lightboxsettings',
				array(
					array(
						'label'   => esc_html__( 'Images', 'gravity-forms-addons' ),
						'value'   => 'images',
						'default' => '1',
					),
					array(
						'label' => esc_html__( "Entry Links (Open entry details in lightbox)" ),
						'value' => 'entry',
					),
					array(
						'label' => esc_html__( 'Website Links (non-entry)', 'gravity-forms-addons' ),
						'value' => 'urls',
					),
				),
				esc_html__( "Set what type of links should be loaded in the lightbox", 'gravity-forms-addons' ),
			),
		);

		$formatting = array(
			array(
				'checkbox',
				'jstable',
				false,
				esc_html__( 'Use the TableSorter jQuery plugin to sort the table?', 'gravity-forms-addons' ),
			),
			array(
				'checkbox',
				'titleshow',
				true,
				'<strong>' . esc_html__( 'Show a form title?', 'gravity-forms-addons' ) . '</strong> ' . esc_html__( "By default, the title will be the form title.", 'gravity-forms-addons' ),
			),
			array(
				'checkbox',
				'showcount',
				true,
				esc_html__( "Do you want to show 'Displaying 1-19 of 19'?", 'gravity-forms-addons' ),
			),
			array(
				'checkbox',
				'thead',
				true,
				sprintf( esc_html__( "Show the top heading row (%s&lt;thead&gt;%s)", 'gravity-forms-addons' ), '<code>', '</code>' ),
			),
			array(
				'checkbox',
				'tfoot',
				true,
				sprintf( esc_html__( "Show the bottom heading row (%s&lt;tfoot&gt;%s)", 'gravity-forms-addons' ), '<code>', '</code>' ),
			),
			array(
				'checkbox',
				'pagelinksshowall',
				true,
				esc_html__( "Show each page number (eg: 1 2 3 4 5 6 7 8) instead of summary (eg: 1 2 3 ... 8 &raquo;)", 'gravity-forms-addons' ),
			),
			array(
				'checkbox',
				'jssearch',
				true,
				sprintf( esc_html__( "Use JavaScript for sorting (otherwise, %slinks%s will be used for sorting by column)", 'gravity-forms-addons' ), '<em>', '</em>' ),
			),
			array(
				'checkbox',
				'dateformat',
				false,
				esc_html__( "Override the options from Gravity Forms, and use standard PHP date formats", 'gravity-forms-addons' ),
			),
		);

		$links = array(
			array(
				'checkbox',
				'linkemail',
				true,
				esc_html__( "Convert email fields to email links", 'gravity-forms-addons' ),
			),
			array( 'checkbox', 'linkwebsite', true, esc_html__( "Convert URLs to links", 'gravity-forms-addons' ) ),
			array(
				'checkbox',
				'truncatelink',
				false,
				sprintf( esc_html__( "Show more simple links for URLs (strip %shttp://%s, %swww.%s, etc.)", 'gravity-forms-addons' ), '<code>', '</code>', '<code>', '</code>' ),
			),    #'truncatelink' => false,
			array(
				'checkbox',
				'linknewwindow',
				false,
				sprintf( esc_html__( "%sOpen links in new window?%s (uses %s)", 'gravity-forms-addons' ), '<strong>', '</strong>', "<code>target='_blank'</code>" ),
			),
			array(
				'checkbox',
				'nofollowlinks',
				false,
				sprintf( esc_html__( "%sAdd %snofollow%s to all links%s, including emails", 'gravity-forms-addons' ), '<strong>', '<code>', '</code>', '</strong>' ),
			),
		);

		$address = array(
			array(
				'checkbox',
				'appendaddress',
				false,
				esc_html__( "Add the formatted address as a column at the end of the table", 'gravity-forms-addons' ),
			),
			array(
				'checkbox',
				'hideaddresspieces',
				false,
				esc_html__( "Hide the pieces that make up an address (Street, City, State, ZIP, Country, etc.)", 'gravity-forms-addons' ),
			),
		);

		$entry = array(
			array(
				'text',
				'entrytitle',
				esc_html__( 'Entry Detail', 'gravity-forms-addons' ),
				esc_html__( "Title of entry lightbox window", 'gravity-forms-addons' ),
			),
			array(
				'text',
				'entrydetailtitle',
				sprintf( esc_html__( 'Entry Detail Table Caption', 'gravity-forms-addons' ), esc_html__( "The text displayed at the top of the entry details. Use %s%%%%formtitle%%%%%s and %s%%%%leadid%%%%%s as variables that will be replaced.", 'gravity-forms-addons' ), '<code>', '</code>', '<code>', '</code>' ),
			),
			array(
				'text',
				'entrylink',
				esc_html__( 'View entry details', 'gravity-forms-addons' ),
				esc_html__( "Link text to show full entry", 'gravity-forms-addons' ),
			),
			array(
				'text',
				'entryth',
				esc_html__( 'More Info', 'gravity-forms-addons' ),
				esc_html__( "Entry ID column title", 'gravity-forms-addons' ),
			),
			array(
				'text',
				'entryback',
				esc_html__( '&larr; Back to directory', 'gravity-forms-addons' ),
				esc_html__( "The text of the link to return to the directory view from the single entry view.", 'gravity-forms-addons' ),
			),
			array(
				'checkbox',
				'entryonly',
				true,
				esc_html__( "When viewing full entry, show entry only? Otherwise, show entry with directory below", 'gravity-forms-addons' ),
			),
			array(
				'checkbox',
				'entryanchor',
				true,
				esc_html__( "When returning to directory view from single entry view, link to specific anchor row?", 'gravity-forms-addons' ),
			),
		);

		$fieldsets = array(
			esc_html__( 'Content Settings', 'gravity-forms-addons' )          => $content,
			esc_html__( 'Administration of Entries', 'gravity-forms-addons' ) => $administration,
			esc_html__( 'Lightbox Options', 'gravity-forms-addons' )          => $lightbox,
			esc_html__( 'Formatting Options', 'gravity-forms-addons' )        => $formatting,
			esc_html__( 'Link Settings', 'gravity-forms-addons' )             => $links,
			esc_html__( 'Address Options', 'gravity-forms-addons' )           => $address,
		);

		if ( ! $js ) {
			echo '<a href="#kws_gf_advanced_settings" class="kws_gf_advanced_settings">' . esc_html__( 'Show advanced settings', 'gravity-forms-addons' ) . '</a>';
			echo '<div style="display:none;" id="kws_gf_advanced_settings">';
			echo "<h2 style='margin:0; padding:0; font-weight:bold; font-size:1.5em; margin-top:1em;'>Single-Entry View</h2>";
			echo '<span class="howto">These settings control whether users can view each entry as a separate page or lightbox. Single entries will show all data associated with that entry.</span>';
			echo '<ul style="padding:0 15px 0 15px; width:100%;">';
			foreach ( $entry as $o ) {
				if ( isset( $o[3] ) ) {
					$o3 = esc_html( $o[3] );
				} else {
					$o3 = '';
				}
				self::make_field( $o[0], $o[1], maybe_serialize( $o[2] ), $o3, $defaults );
			}
			echo '</ul>';

			echo '<div class="hr-divider label-divider"></div>';

			echo "<h2 style='margin:0; padding:0; font-weight:bold; font-size:1.5em; margin-top:1em;'>" . esc_html__( 'Directory View', 'gravity-forms-addons' ) . "</h2>";
			echo '<span class="howto">' . esc_html__( 'These settings affect how multiple entries are shown at once.', 'gravity-forms-addons' ) . '</span>';

			foreach ( $fieldsets as $title => $fieldset ) {
				echo "<fieldset><legend><h3 style='padding-top:1em; padding-bottom:.5em; margin:0;'>{$title}</h3></legend>";
				echo '<ul style="padding: 0 15px 0 15px; width:100%;">';
				foreach ( $fieldset as $o ) {
					self::make_field( $o[0], $o[1], maybe_serialize( $o[2] ), $o[3], $defaults );
				}
				echo '</ul></fieldset>';
				echo '<div class="hr-divider label-divider"></div>';
			}
			echo "<h2 style='margin:0; padding:0; font-weight:bold; font-size:1.5em; margin-top:1em;'>" . esc_html__( 'Additional Settings', 'gravity-forms-addons' ) . "</h2>";
			echo '<span class="howto">' . esc_html__( 'These settings affect both the directory view and single entry view.', 'gravity-forms-addons' ) . '</span>';
			echo '<ul style="padding: 0 15px 0 15px; width:100%;">';
		} else {
			foreach ( $entry as $o ) {
				$out[ $i ] = self::make_popup_js( $o[0], $o[1], $defaults );
				$i ++;
			}
			foreach ( $fieldsets as $title => $fieldset ) {
				foreach ( $fieldset as $o ) {
					$out[ $i ] = self::make_popup_js( $o[0], $o[1], $defaults );
					$i ++;
				}
			}
		}
		$advanced = array(
			array(
				'text',
				'tableclass',
				'gf_directory widefat',
				esc_html__( "Class for the <table>, <ul>, or <dl>", 'gravity-forms-addons' ),
			),
			array(
				'text',
				'tablestyle',
				'',
				esc_html__( "inline CSS for the <table>, <ul>, or <dl>", 'gravity-forms-addons' ),
			),
			array(
				'text',
				'rowclass',
				'',
				esc_html__( "Class for the <table>, <ul>, or <dl>", 'gravity-forms-addons' ),
			),
			array(
				'text',
				'rowstyle',
				'',
				esc_html__( "Inline CSS for all <tbody><tr>'s, <ul><li>'s, or <dl><dt>'s", 'gravity-forms-addons' ),
			),
			array(
				'text',
				'valign',
				'baseline',
				esc_html__( "Vertical align for table cells", 'gravity-forms-addons' ),
			),
			array(
				'text',
				'sort',
				'date_created',
				esc_html__( "Use the input ID ( example: 1.3 or 7 or ip)", 'gravity-forms-addons' ),
			),
			array(
				'text',
				'dir',
				'DESC',
				sprintf( esc_html__( "Sort in ascending order (%sASC%s or descending (%sDESC%s)", 'gravity-forms-addons' ), '<code>', '</code>', '<code>', '</code>' ),
			),
			array(
				'text',
				'startpage',
				1,
				esc_html__( "If you want to show page 8 instead of 1", 'gravity-forms-addons' ),
			),
			array(
				'text',
				'pagelinkstype',
				'plain',
				sprintf( esc_html__( "Type of pagination links. %splain%s is just a string with the links separated by a newline character. The other possible values are either %sarray%s or %slist%s.", 'gravity-forms-addons' ), '<code>', '</code>', '<code>', '</code>', '<code>', '</code>' ),
			),
			array(
				'text',
				'titleprefix',
				'Entries for ',
				esc_html__( "Default GF behavior is 'Entries : '", 'gravity-forms-addons' ),
			),
			array(
				'text',
				'tablewidth',
				'100%',
				esc_html__( "Set the 'width' attribute for the <table>, <ul>, or <dl>", 'gravity-forms-addons' ),
			),
			array(
				'text',
				'datecreatedformat',
				get_option( 'date_format' ) . ' \a\t ' . get_option( 'time_format' ),
				sprintf( esc_html__( "Use %sstandard PHP date formats%s", 'gravity-forms-addons' ), "<a href='http://php.net/manual/en/function.date.php' target='_blank'>", '</a>' ),
			),
			array(
				'checkbox',
				'credit',
				true,
				esc_html__( "Give credit to the plugin creator (who has spent over 300 hours on this free plugin!) with a link at the bottom of the directory", 'gravity-forms-addons' ),
			),
		);
		if ( ! $js ) {
			foreach ( $advanced as $o ) {
				self::make_field( $o[0], $o[1], maybe_serialize( $o[2] ), $o[3], $defaults );
			}
			echo '</ul></fieldset></div>';
		} else {
			foreach ( $advanced as $o ) {
				$out[ $i ] = self::make_popup_js( $o[0], $o[1], $defaults );
				$i ++;
			}

			return $out;
		}
	}

	static function make_field( $type, $id, $default, $label, $defaults = array() ) {
		$rawid   = $id;
		$idLabel = '';
		if ( GFDirectory::is_gravity_page( 'gf_settings' ) ) {
			$id      = 'gf_addons_directory_defaults[' . $id . ']';
			$idLabel = " <span style='color:#868686'>(<pre style='display:inline'>{$rawid}</pre>)</span>";
		}
		$checked = '';
		$label   = str_replace( '&lt;code&gt;', '<code>', str_replace( '&lt;/code&gt;', '</code>', $label ) );
		$output  = '<li class="setting-container" style="width:90%; clear:left; border-bottom: 1px solid #cfcfcf; padding:.25em .25em .4em; margin-bottom:.25em;">';
		$default = maybe_unserialize( $default );

		$class = '';
		if ( $type == 'date' ) {
			$type  = 'text';
			$class = ' class="gf_addons_datepicker datepicker"';
		}

		if ( $type == "checkbox" ) {
			if ( ! empty( $defaults["{$rawid}"] ) || ( $defaults["{$rawid}"] === '1' || $defaults["{$rawid}"] === 1 ) ) {
				$checked = ' checked="checked"';
			}
			$output .= '<label for="gf_settings_' . $rawid . '"><input type="hidden" value="" name="' . $id . '" /><input type="checkbox" id="gf_settings_' . $rawid . '"' . $checked . ' name="' . $id . '" /> ' . $label . $idLabel . '</label>' . "\n";
		} elseif ( $type == "text" ) {
			$default = $defaults["{$rawid}"];
			$output .= '<label for="gf_settings_' . $rawid . '"><input type="text" id="gf_settings_' . $rawid . '" value="' . htmlspecialchars( stripslashes( $default ) ) . '" style="width:40%;" name="' . $id . '"' . $class . ' /> <span class="howto">' . $label . $idLabel . '</span></label>' . "\n";
		} elseif ( $type == 'radio' || $type == 'checkboxes' ) {
			if ( is_array( $default ) ) {
				$output .= $label . $idLabel . '<ul class="ul-disc">';
				foreach ( $default as $opt ) {
					if ( $type == 'radio' ) {
						$id_opt = $id . '_' . sanitize_title( $opt['value'] );
						if ( ! empty( $defaults["{$rawid}"] ) && $defaults["{$rawid}"] == $opt['value'] ) {
							$checked = ' checked="checked"';
						} else {
							$checked = '';
						}
						$inputtype = 'radio';
						$name      = $id;
						$value     = $opt['value'];
						$output .= '
						<li><label for="gf_settings_' . $id_opt . '">';
					} else {
						$id_opt = $rawid . '_' . sanitize_title( $opt['value'] );
						if ( ! empty( $defaults["{$rawid}"][ sanitize_title( $opt['value'] ) ] ) ) {
							$checked = ' checked="checked"';
						} else {
							$checked = '';
						}
						$inputtype = 'checkbox';
						$name      = $id . '[' . sanitize_title( $opt['value'] ) . ']';
						$value     = 1;
						$output .= '
							<li><label for="gf_settings_' . $id_opt . '">
								<input type="hidden" value="0" name="' . $name . '" />';
					}
					$output .= '
							<input type="' . $inputtype . '"' . $checked . ' value="' . $value . '" id="gf_settings_' . $id_opt . '" name="' . $name . '" /> ' . $opt['label'] . " <span style='color:#868686'>(<pre style='display:inline'>" . sanitize_title( $opt['value'] ) . "</pre>)</span>" . '
						</label>
					</li>' . "\n";
				}
				$output .= "</ul>";
			}
		} elseif ( $type == 'select' ) {
			if ( is_array( $default ) ) {
				$output .= '
				<label for="gf_settings_' . $rawid . '">' . $label . '
				<select name="' . $id . '" id="gf_settings_' . $rawid . '">';
				foreach ( $default as $opt ) {

					if ( ! empty( $defaults["{$rawid}"] ) && $defaults["{$rawid}"] == $opt['value'] ) {
						$checked = ' selected="selected"';
					} else {
						$checked = '';
					}
					$id_opt = $id . '_' . sanitize_title( $opt['value'] );
					$output .= '<option' . $checked . ' value="' . $opt['value'] . '"> ' . $opt['label'] . '</option>' . "\n";
				}
				$output .= '</select>' . $idLabel . '
				</label>
				';
			} else {
				$output = '';
			}
		}
		if ( ! empty( $output ) ) {
			$output .= '</li>' . "\n";
			echo $output;
		}
	}

	static function make_popup_js( $type, $id, $defaults ) {

		foreach ( $defaults as $key => $default ) {
			if ( $default === true || $default === 'on' ) {
				$defaults[ $key ] = 'true';
			} elseif ( $default === false || ( $type == 'checkbox' && empty( $default ) ) ) {
				$defaults[ $key ] = 'false';
			}
		}
		$defaultsArray = array();
		if ( $type == "checkbox" ) {
			$js = 'var ' . $id . ' = jQuery("#gf_settings_' . $id . '").is(":checked") ? "true" : "false";';
		} elseif ( $type == "checkboxes" && is_array( $defaults["{$id}"] ) ) {
			$js = '';
			$i  = 0;
			$js .= "\n\t\t\tvar " . $id . ' = new Array();';
			foreach ( $defaults["{$id}"] as $key => $value ) {
				$defaultsArray[] = $key;
				$js .= "\n\t\t\t" . $id . '[' . $i . '] = jQuery("input#gf_settings_' . $id . '_' . $key . '").is(":checked") ? "' . $key . '" : null;';
				$i ++;
			}
		} elseif ( $type == "text" || $type == "date" ) {
			$js = 'var ' . $id . ' = jQuery("#gf_settings_' . $id . '").val();';
		} elseif ( $type == 'radio' ) {
			$js = '
			if(jQuery("input[name=\'' . $id . '\']:checked").length > 0) {
				var ' . $id . ' = jQuery("input[name=\'' . $id . '\']:checked").val();
			} else {
				var ' . $id . ' = jQuery("input[name=\'' . $id . '\']").eq(0).val();
			}';
		} elseif ( $type == 'select' ) {
			$js = '
			if(jQuery("select[name=\'' . $id . '\']:selected").length > 0) {
				var ' . $id . ' = jQuery("select[name=\'' . $id . '\']:selected").val();
			} else {
				var ' . $id . ' = jQuery("select[name=\'' . $id . '\']").eq(0).val();
			}';
		}
		$set = '';
		if ( ! is_array( $defaults["{$id}"] ) ) {
			$idCode = $id . '=\""+' . $id . '+"\"';
			$set    = 'var ' . $id . 'Output = (jQuery.trim(' . $id . ') == "' . trim( addslashes( stripslashes( $defaults["{$id}"] ) ) ) . '") ? "" : " ' . $idCode . '";';
		} else {

			$idCode2 = $id . '.join()';
			$idCode  = '"' . $idCode2 . '"';
			$set     = '
			' . $id . ' =  jQuery.grep(' . $id . ',function(n){ return(n); });
			var ' . $id . 'Output = (jQuery.trim(' . $idCode2 . ') === "' . implode( ',', $defaultsArray ) . '") ? "" : " ' . $id . '=\""+ ' . $idCode2 . '+"\"";';
		}
		// Debug

		$return = array( 'js' => $js, 'id' => $id, 'idcode' => $idCode, 'setvalue' => $set );

		return $return;
	}

	public function add_form_button() {

		$output = '<a href="#TB_inline?width=640&amp;inlineId=select_gf_directory" class="thickbox button select_gf_directory gform_media_link" id="add_gform" title="' . esc_attr__( "Add a Gravity Forms Directory", 'gravity-forms-addons' ) . '"><span class="dashicons dashicons-welcome-widgets-menus" style="line-height: 26px;"></span> ' . esc_html__( "Add Directory", "gravityforms" ) . '</a>';

		echo $output;
	}

	//Creates directory left nav menu under Forms
	public function create_menu( $menus ) {
		// Adding submenu if user has access
		$permission = GFDirectory::has_access( "gravityforms_directory" );
		if ( ! empty( $permission ) ) {
			$menus[] = array(
				"name"       => "gf_settings&amp;addon=Directory+%26+Addons",
				"label"      => esc_html__( "Directory & Addons", "gravity-forms-addons" ),
				"callback"   => array( &$this, "settings_page" ),
				"permission" => $permission,
			);
		}

		return $menus;
	}

	public function settings_page() {
		$message = $validimage = false;
		global $plugin_page;

		if ( isset( $_POST["gf_addons_submit"] ) ) {
			check_admin_referer( "update", "gf_directory_update" );

			$settings = array(
				"directory"          => isset( $_POST["gf_addons_directory"] ),
				"referrer"           => isset( $_POST["gf_addons_referrer"] ),
				"directory_defaults" => GFDirectory::directory_defaults( $_POST['gf_addons_directory_defaults'], true ),
				"modify_admin"       => isset( $_POST["gf_addons_modify_admin"] ) ? $_POST["gf_addons_modify_admin"] : array(),
				"version"            => GFDirectory::get_version(),
				"saved"              => true,
			);
			$message  = esc_html__( 'Settings saved.', 'gravity-forms-addons' );
			update_option( "gf_addons_settings", $settings );
		} else {
			$settings = GFDirectory::get_settings();
		}

		?>
		<style>
			.ul-square li {
				list-style: square !important;
			}

			.ol-decimal li {
				list-style: decimal !important;
			}

			.form-table label {
				font-size: 1em !important;
				margin: .4em 0;
				display: block;
			}

			li.setting-container {
				border: none !important;
			}
		</style>
		<script>
			jQuery( 'document' ).ready( function ( $ ) {
				$( '#kws_gf_advanced_settings' ).show();
				$( 'a:contains(Directory)', $( 'ul.subsubsub' ) ).css( 'font-weight', 'bold' );
				$( '.wp-submenu li.current, .wp-submenu li.current a' ).removeClass( 'current' );
				$( 'a:contains(Directory)', $( '.wp-submenu' ) ).addClass( 'current' ).parent( 'li' ).addClass( 'current' );

				$( 'a.kws_gf_advanced_settings' ).hide(); //click(function(e) {  e.preventDefault(); jQuery('#kws_gf_advanced_settings').slideToggle(); return false; });

				$( '#kws_gf_advanced_settings' ).change( function () {
					if ( $( "#gf_settings_thead:checked" ).length || $( "#gf_settings_tfoot:checked" ).length ) {
						$( '#gf_settings_jssearch' ).parents( 'li' ).show();
					} else {
						$( '#gf_settings_jssearch' ).parents( 'li' ).hide();
					}
				} ).trigger( 'change' );

				$( document ).on( 'load click', 'label[for=gf_addons_directory]', function () {
					if ( $( '#gf_addons_directory' ).is( ":checked" ) ) {
						$( "tr#directory_settings_row" ).show();
					} else {
						$( "tr#directory_settings_row" ).hide();
					}
				} );

				$( '#kws_gf_instructions_button' ).click( function ( e ) {
					e.preventDefault();

					$( '#kws_gf_instructions' ).slideToggle( function () {
						var $that = $( '#kws_gf_instructions_button' );
						$that.text( function () {
							if ( $( '#kws_gf_instructions' ).is( ":visible" ) ) {
								return '<?php echo esc_js( __( 'Hide Directory Instructions', 'gravity-forms-addons' ) ); ?>';
							} else {
								return '<?php echo esc_js( __( 'View Directory Instructions', 'gravity-forms-addons' ) ); ?>';
							}
						} );
					} );

					return false;
				} );

				$( '#message.fade' ).delay( 1000 ).fadeOut( 'slow' );

			} );
		</script>
		<div class="wrap">
			<?php
			if ( $plugin_page !== 'gf_settings' ) {

				echo '<h2>' . esc_html__( 'Gravity Forms Directory Add-on', "gravity-forms-addons" ) . '</h2>';
			}
			if ( $message ) {
				echo "<div class='fade below-h2 updated' id='message'>" . wpautop( $message ) . "</div>";
			}

			// if you must, you can filter this out...
			if ( apply_filters( 'kws_gf_show_donate_box', true ) ) {
				include( plugin_dir_path( __FILE__ ) . '/gravityview-info.php' );
			} // End donate box

			?>

			<p class="submit"><span style="padding-right:.5em;"
			                        class="description"><?php esc_html_e( 'Need help getting started?', 'gravity-forms-addons' ); ?></span>
				<a href="#" class="button button-secondary" id="kws_gf_instructions_button"><?php
					if ( ! empty( $settings['saved'] ) && ! isset( $_REQUEST['viewinstructions'] ) ) {
						esc_html_e( 'View Directory Instructions', 'gravity-forms-addons' );
					} else {
						esc_html_e( 'Hide Directory Instructions', 'gravity-forms-addons' );
					}
					?></a></p>

			<div
				id="kws_gf_instructions"<?php if ( ! empty( $settings['saved'] ) && ! isset( $_REQUEST['viewinstructions'] ) ) { ?>  class="hide-if-js clear" <?php } ?>>
				<div class="delete-alert alert_gray">
					<div class="alignright" style="margin:1em 1.2em;">
						<iframe width="400" height="255"
						        src="http<?php echo is_ssl() ? 's' : ''; ?>://www.youtube.com/embed/PMI7Jb-RP2I?hd=1"
						        frameborder="0" allowfullscreen></iframe>
					</div>
					<h3 style="padding-top:1em;"><?php esc_html_e( 'To integrate a form with Directory:', 'gravity-forms-addons' ); ?></h3>
					<ol class="ol-decimal">
						<li><?php esc_html_e( 'Go to the post or page where you would like to add the directory.', 'gravity-forms-addons' ); ?></li>
						<li><?php esc_html_e( 'Click the "Add Directory" button above the content area.', 'gravity-forms-addons' ); ?></li>
						<li><?php esc_html_e( 'Choose a form from the drop-down menu and configure settings as you would like them.', 'gravity-forms-addons' ); ?></li>
						<li><?php printf( esc_html__( 'Click "Insert Directory". A "shortcode" should appear in the content editor that looks similar to %s[directory form="#"]%s', 'gravity-forms-addons' ), '<code style="font-size:1em;">', '</code>' ); ?></li>
						<li><?php esc_html_e( 'Save the post or page', 'gravity-forms-addons' ); ?></li>
					</ol>

					<h4><?php esc_html_e( 'Configuring Fields & Columns', "gravity-forms-addons" ); ?></h4>

					<?php echo wpautop( esc_html__( 'When editing a form, click on a field to expand the field. Next, click the "Directory" tab. There, you will find options to:', "gravity-forms-addons" ) ); ?>

					<ul class="ul-square">
						<li><?php esc_html_e( "Choose whether you would like the field to be a link to the Single Entry View;", "gravity-forms-addons" ); ?></li>
						<li><?php esc_html_e( "Hide the field in Directory View; and", "gravity-forms-addons" ); ?></li>
						<li><?php esc_html_e( "Hide the field in Single Entry View", "gravity-forms-addons" ); ?></li>
					</ul>

					<h4><?php esc_html_e( 'Configuring Column Visibility & Order', "gravity-forms-addons" ); ?></h4>

					<?php echo wpautop( esc_html__( 'When editing a form in Gravity Forms, click the link near the top-center of the page named "Directory Columns"', "gravity-forms-addons" ) ); ?>

					<ol class="ol-decimal">
						<li><?php esc_html_e( 'When editing a form in Gravity Forms, click the link near the top-center of the page named "Directory Columns"', "gravity-forms-addons" ); ?></li>
						<li><?php esc_html_e( 'Drag and drop columns from the right ("Hidden Columns") side to the left ("Visible Columns") side.', "gravity-forms-addons" ); ?></li>
						<li><?php esc_html_e( 'Click the "Save" button', "gravity-forms-addons" ); ?></li>
					</ol>

				</div>

				<div class="hr-divider"></div>
			</div>
			<form method="post" action="" class="clear">
				<?php wp_nonce_field( "update", "gf_directory_update" ) ?>
				<table class="form-table">
					<tr>
						<th scope="row"><label
								for="gf_addons_directory"><?php esc_html_e( "Gravity Forms Directory", "gravity-forms-addons" ); ?></label>
						</th>
						<td>
							<label for="gf_addons_directory" class="howto"><input type="checkbox"
							                                                      id="gf_addons_directory"
							                                                      name="gf_addons_directory" <?php checked( $settings["directory"] ); ?> /> <?php esc_html_e( 'Enable Gravity Forms Directory capabilities', 'gravity-forms-addons' ); ?>
							</label>
						</td>
					</tr>
					<tr id="directory_settings_row">
						<th scope="row"></th>
						<td>
							<h2 style="margin-bottom:0; padding-bottom:0;"><?php esc_html_e( "Directory Default Settings", "gravity-forms-addons" ); ?></h2>
							<h3><?php esc_html_e( "These defaults can be over-written when inserting a directory.", "gravity-forms-addons" ); ?></h3>

							<?php
							self::make_popup_options( false );
							?>
							<div class="hr-divider"></div>
						</td>
					</tr>
					<tr>
						<th scope="row"><label
								for="gf_addons_referrer"><?php esc_html_e( "Add Referrer Data to Emails", "gravity-forms-addons" ); ?></label>
						</th>
						<td>
							<label for="gf_addons_referrer"><input type="checkbox" id="gf_addons_referrer"
							                                       name="gf_addons_referrer" <?php checked( $settings["referrer"] ); ?> /> <?php esc_html_e( "Adds referrer data to entries, including the path the user took to get to the form before submitting.", 'gravity-forms-addons' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label
								for="gf_addons_modify_admin"><?php esc_html_e( "Modify Gravity Forms Admin", "gravity-forms-addons" ); ?></label>
						</th>
						<td>
							<ul>
								<li><label for="gf_addons_modify_admin_expand"><input type="checkbox"
								                                                      id="gf_addons_modify_admin_expand"
								                                                      name="gf_addons_modify_admin[expand]" <?php checked( isset( $settings["modify_admin"]['expand'] ) ); ?> /> <?php esc_html_e( "Show option to expand Form Editor Field boxes", "gravity-forms-addons" ); ?>
									</label></li>

								<li><label for="gf_addons_modify_admin_toggle"><input type="checkbox"
								                                                      id="gf_addons_modify_admin_toggle"
								                                                      name="gf_addons_modify_admin[toggle]" <?php checked( isset( $settings["modify_admin"]['toggle'] ) ); ?> /> <?php esc_html_e( 'When clicking Form Editor Field boxes, toggle open and closed instead of "accordion mode" (closing all except the clicked box).', "gravity-forms-addons" ); ?>
									</label></li>

								<li><label for="gf_addons_modify_admin_edit"><input type="checkbox"
								                                                    id="gf_addons_modify_admin_edit"
								                                                    name="gf_addons_modify_admin[edit]" <?php checked( isset( $settings["modify_admin"]['edit'] ) ); ?> /> <?php printf( esc_html__( "Makes possible direct editing of entries from %sEntries list view%s", "gravity-forms-addons" ), '<a href="' . admin_url( 'admin.php?page=gf_entries' ) . '">', '</a>' ); ?>
									</label></li>

								<li><label for="gf_addons_modify_admin_ids"><input type="checkbox"
								                                                   id="gf_addons_modify_admin_ids"
								                                                   name="gf_addons_modify_admin[ids]" <?php checked( isset( $settings["modify_admin"]['ids'] ) ); ?> /> <?php printf( esc_html__( "Adds a link in the Forms list view to view form IDs", "gravity-forms-addons" ), '<a href="' . admin_url( 'admin.php?page=gf_edit_forms' ) . '">', '</a>' ); ?>
									</label></li>
							</ul>
						</td>
					</tr>
					<tr>
						<td colspan="2"><input type="submit" name="gf_addons_submit"
						                       class="button-primary button-large button-mega"
						                       value="<?php esc_attr_e( "Save Settings", "gravity-forms-addons" ) ?>"/>
						</td>
					</tr>
				</table>
			</form>
		</div>
		<?php
	}
}
