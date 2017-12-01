<?php
/**
 * Displays the content on the plugin settings page
 */

if ( ! class_exists( 'hctpc_Settings_Tabs' ) ) {
	class hctpc_Settings_Tabs {
		private $forms, $form_categories, $registered_forms;

		private $tabs;		
		private $plugin_basename;
				
		public $options;
		public $default_options;
		public $plugins_info  = array();

		public $version;
		public $upload_dir;
		public $all_plugins;
		public $is_multisite;

		/**
		 * Constructor.
		 *
		 * @access public
		 * @param string $plugin_basename
		 */
		public function __construct( $plugin_basename ) {
			global $hctpc_options, $hctpc_plugin_info;

			if ( ! function_exists( 'hctpc_get_default_options' ) )
				require_once( dirname( __FILE__ ) . '/helpers.php' );

			$this->plugin_basename		= $plugin_basename;
			$this->plugins_info			= $hctpc_plugin_info;			
			$this->options				= $hctpc_options;
			$this->default_options  	= hctpc_get_default_options();
			$this->tabs  				= array(
				'settings'      => array( 'label' => __( 'Settings', 'captcha' ) ),
				'messages'      => array( 'label' => __( 'Messages', 'captcha' ) ),
				'misc'          => array( 'label' => __( 'Misc', 'captcha' ) )
			);
			$this->version = '1.0.0';
			$this->is_multisite = is_multisite();			

			$this->all_plugins = get_plugins();

			$this->forms = array(
				'wp_login'         			=> array( 'name' => __( 'Login form', 'captcha' ) ),
				'wp_register'      			=> array( 'name' => __( 'Registration form', 'captcha' ) ),
				'wp_lost_password' 			=> array( 'name' => __( 'Forgot password form', 'captcha' ) ),
				'wp_comments'      			=> array( 'name' => __( 'Comments form', 'captcha' ) ),
			);

			/*
			 * Add users forms to the forms lists
			 */
			$user_forms = apply_filters( 'hctpc_add_form', array() );
			if ( ! empty( $user_forms ) ) {
				/*
				 * Get default form slugs from defaults
				 * which have been added by hook "hctpc_add_default_form" */
				$new_default_forms = array_diff( hctpc_get_default_forms(), array_keys( $this->forms ) );
				/*
				 * Remove forms slugs form from the newly added
				 * which have not been added to defaults previously
				 */
				$new_forms = array_intersect( $new_default_forms, array_keys( $user_forms ) );
				/* Get the sub array with new form labels */
				$new_forms_fields = array_intersect_key( $user_forms, array_flip( $new_forms ) ); 
				$new_forms_fields = array_map( array( $this, 'sanitize_new_form_data' ), $new_forms_fields );
				if ( ! empty( $new_forms_fields ) ) {
					/* Add new forms labels to the registered */
					$this->forms = array_merge( $this->forms, $new_forms_fields );					
					/* Add default settings in case if new forms settings have not been saved yet */
					foreach ( $new_forms as $new_form ) {
						if ( empty( $this->options['forms'][ $new_form ] ) )
							$this->options['forms'][ $new_form ] = $this->default_options['forms'][ $new_form ];
					}
				}
			}

			/**
			* form categories are used when compatible plugins are displayed
			*/
			$this->form_categories = array(
				'wp_default' => array(
					'title' => __( 'WordPress default', 'captcha' ),
					'forms' => array(
						'wp_login',
						'wp_register',
						'wp_lost_password',
						'wp_comments'
					)
				)
			);

			/**
			* create list with default compatible forms
			*/
			$this->registered_forms = $this->form_categories['wp_default']['forms'];

			$user_forms = array_diff( array_keys( $this->forms ), $this->registered_forms );
		}

		/**
		 * Displays the content of the "Settings" on the plugin settings page
		 * @access public
		 * @param  void
		 * @return void
		 */
		public function display_content() {
			$save_results = $this->save_all_tabs_options();

			$this->display_messages( $save_results );
			if ( isset( $_REQUEST['hctpc_restore_default'] ) && check_admin_referer( $this->plugin_basename, 'hctpc_nonce_name' ) ) { ?>
				<div>
					<p><?php _e( 'Are you sure you want to restore default settings?', 'captcha' ) ?></p>
					<form method="post" action="">
						<p>
							<button class="button button-primary" name="hctpc_restore_confirm"><?php _e( 'Yes, restore all settings', 'captcha' ) ?></button>
							<button class="button" name="hctpc_restore_deny"><?php _e( 'No, go back to the settings page', 'captcha' ) ?></button>
							<?php wp_nonce_field( $this->plugin_basename, 'hctpc_settings_nonce_name' ); ?>
						</p>
					</form>
				</div>
			<?php } else { ?>
				<form class="hctpc_form" method="post" action="" enctype="multipart/form-data">
					<div id="poststuff">
						<div id="post-body" class="metabox-holder columns-2">
							<div id="post-body-content" style="position: relative;">
								<?php $this->display_tabs(); ?>					
							</div><!-- /post-body-content -->
							<div id="postbox-container-1" class="postbox-container">
								<div class="meta-box-sortables ui-sortable">
									<div id="submitdiv" class="postbox">
										<h3 class="hndle"><?php _e( 'Information', 'captcha' ); ?></h3>
										<div class="inside">
											<div class="submitbox" id="submitpost">
												<div id="minor-publishing">
													<div id="misc-publishing-actions">														
														<div class="misc-pub-section">
															<strong><?php _e( 'Version', 'captcha' ); ?>:</strong> <?php echo $this->plugins_info['Version']; ?>
														</div><!-- .misc-pub-section -->
													</div>
													<div class="clear"></div>
												</div>
												<div id="major-publishing-actions">
													<div id="publishing-action">
														<input type="hidden" name="hctpc_form_submit" value="submit" />
														<input id="hctpc-submit-button" type="submit" class="button button-primary button-large" value="<?php _e( 'Save Changes', 'captcha' ); ?>" />
														<?php wp_nonce_field( $this->plugin_basename, 'hctpc_nonce_name' ); ?>					
													</div>
													<div class="clear"></div>
												</div>
											</div>
										</div>										
									</div>
									<?php /**
									 * action - Display custom metabox
									 */
									do_action( __CLASS__ . '_display_metabox' ); ?>
								</div>
							</div>
							<div id="postbox-container-2" class="postbox-container">
								<?php /**
								 * action - Display additional content for #postbox-container-2
								 */
								do_action( __CLASS__ . '_display_second_postbox' ); ?>
								<div class="submit">
									<input type="submit" class="button button-primary button-large" value="<?php _e( 'Save Changes', 'captcha' ); ?>" />
								</div>								
							</div>
						</div>
					</form>
				</div>
			<?php }
		}

		/**
		 * Displays the Tabs
		 * @access public
		 * @param  void
		 * @return void
		 */
		public function display_tabs() { 
			global $wp_version; ?>
			<div id="hctpc_settings_tabs_wrapper"<?php if ( version_compare( $wp_version, '4.0', '<' ) ) echo ' class="edit-form-section"'; ?>>
				<ul id="hctpc_settings_tabs">
					<?php $this->display_tabs_list(); ?>
				</ul>
				<?php $this->display_tabs_content(); ?>
				<div class="clear"></div>
				<input type="hidden" name="hctpc_active_tab" value="<?php if ( isset( $_REQUEST['hctpc_active_tab'] ) ) echo esc_attr( $_REQUEST['hctpc_active_tab'] ); ?>" />
			</div>
		<?php }

		/**
		 * Displays the list of tabs
		 * @access private
		 * @return void
		 */
		private function display_tabs_list() {
			foreach ( $this->tabs as $tab_slug => $data ) {
				$tab_class = 'hctpc-tab-' . $tab_slug;
				if ( ! empty( $data['class'] ) )
					$tab_class .= ' ' . $data['class']; ?>
				<li class="<?php echo $tab_class; ?>" data-slug="<?php echo $tab_slug; ?>">
					<a href="#hctpc_<?php echo $tab_slug; ?>_tab">
						<span><?php echo esc_html( $data['label'] ); ?></span>
					</a>
				</li>
			<?php }
		}

		/**
		 * Displays the content of tabs
		 * @access private
		 * @param  string $tab_slug
		 * @return void
		 */
		public function display_tabs_content() {
			foreach ( $this->tabs as $tab_slug => $data ) { ?>
				<div class="hctpc_tab ui-tabs-panel ui-widget-content ui-corner-bottom" id="hctpc_<?php echo $tab_slug; ?>_tab" aria-labelledby="ui-id-2" role="tabpanel" aria-hidden="false" style="display: block;">					
					<?php if ( method_exists( $this, 'tab_' . str_replace( '-', '_', $tab_slug ) ) ) {
						call_user_func( array( $this, 'tab_' . str_replace( '-', '_', $tab_slug ) ) );
					} ?>
				</div>
			<?php }
		}

		/**
		 * Save all options from all tabs and display errors\messages
		 * @access public
		 * @param  void
		 * @return void
		 */
		public function save_all_tabs_options() {
			$message = $notice = $error = '';
			/* Restore default settings */
			if ( isset( $_POST['hctpc_restore_confirm'] ) && check_admin_referer( $this->plugin_basename, 'hctpc_settings_nonce_name' ) ) {
				/* do not update package selection */
				$this->default_options['used_packages'] = $this->options['used_packages'];
				update_option( 'hctpc_options', $this->default_options );

				$message = __( 'All plugin settings were restored.', 'captcha' );
			} elseif ( ! isset( $_REQUEST['hctpc_restore_default'] ) && isset( $_REQUEST['hctpc_form_submit'] ) && check_admin_referer( $this->plugin_basename, 'hctpc_nonce_name' ) ) {
				/* save tabs */				
				$result = $this->save_options();
				if ( ! empty( $result['error'] ) )
					$error = $result['error'];
				if ( ! empty( $result['message'] ) )
					$message = $result['message'];
				if ( ! empty( $result['notice'] ) )
					$notice = $result['notice'];
			}

			return compact( 'message', 'notice', 'error' );
		}

		/**
		 * Display error\message\notice
		 * @access public
		 * @param  $save_results - array with error\message\notice
		 * @return void
		 */
		public function display_messages( $save_results ) {
			/**
			 * action - Display custom error\message\notice
			 */
			do_action( __CLASS__ . '_display_custom_messages', $save_results ); ?>
			<div class="updated fade inline" <?php if ( empty( $save_results['message'] ) ) echo "style=\"display:none\""; ?>><p><strong><?php echo $save_results['message']; ?></strong></p></div>
			<div class="updated hctpc-notice inline" <?php if ( empty( $save_results['notice'] ) ) echo "style=\"display:none\""; ?>><p><strong><?php echo $save_results['notice']; ?></strong></p></div>
			<div class="error inline" <?php if ( empty( $save_results['error'] ) ) echo "style=\"display:none\""; ?>><p><strong><?php echo $save_results['error']; ?></strong></p></div>
		<?php }

		/**
		 * Display 'misc' tab
		 * @access private
		 */
		private function tab_misc() { ?>
			<h3 class="hctpc_tab_label"><?php _e( 'Miscellaneous Settings', 'captcha' ); ?></h3>
			<hr>
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Default Settings', 'captcha' ); ?></th>
					<td>
						<input name="hctpc_restore_default" type="submit" class="button" value="<?php _e( 'Restore Settings', 'captcha' ); ?>" />
						<div class="hctpc_info"><?php _e( 'This will restore plugin settings to defaults.', 'captcha' ); ?></div>
					</td>
				</tr>
			</table>
		<?php }

		/**
		 * Save plugin options to the database
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function save_options() {
			$notices = array();

			/*
			 * Prepare general options
			 */
			$general_arrays  = array( 
				'math_actions'   => __( 'Arithmetic Actions', 'captcha' ),
				'operand_format' => __( 'Complexity', 'captcha' ),
				'used_packages'  => __( 'Image Packages', 'captcha' )
			);
			$general_bool    = array( 'load_via_ajax', 'display_reload_button', 'enlarge_images', 'enable_time_limit' );
			$general_strings = array( 'type', 'title', 'required_symbol', 'no_answer', 'wrong_answer', 'time_limit_off', 'time_limit_off_notice', 'whitelist_message' );

			foreach ( $general_bool as $option ) {
				$this->options[ $option ] = ! empty( $_REQUEST["hctpc_{$option}"] );
			}

			foreach ( $general_strings as $option ) {
				$value = isset( $_REQUEST["hctpc_{$option}"] ) ? trim( stripslashes( esc_html( $_REQUEST["hctpc_{$option}"] ) ) ) : '';

				if ( ! in_array( $option, array( 'title', 'required_symbol' ) ) && empty( $value ) ) {
					/* The index has been added in order to prevent the displaying of this message more than once */
					$notices['a'] = __( 'Text fields in the "Messages" tab must not be empty.', 'captcha' );
				} else {
					$this->options[ $option ] = $value;
				}
			}

			foreach ( $general_arrays as $option => $option_name ) {
				$value = isset( $_REQUEST["hctpc_{$option}"] ) && is_array( $_REQUEST["hctpc_{$option}"] ) ? array_map( 'esc_html', $_REQUEST["hctpc_{$option}"] ) : array();

				/* "Arithmetic actions" and "Complexity" must not be empty */
				if ( empty( $value ) && 'used_packages' != $option && 'recognition' != $this->options['type'] )
					$notices[] = sprintf( __( '"%s" option must not be fully disabled.', 'captcha' ), $option_name );
				else
					$this->options[ $option ] = $value;
			}

			$this->options['images_count'] = isset( $_REQUEST['hctpc_images_count'] ) ? absint( $_REQUEST['hctpc_images_count'] ) : 4;
			$this->options['time_limit'] = isset( $_REQUEST['hctpc_time_limit'] ) ? absint( $_REQUEST['hctpc_time_limit'] ) : 120;

			/*
			 * Prepare forms options
			 */
			$forms     = array_keys( $this->forms );
			$form_bool = array( 'enable', 'hide_from_registered' );
			foreach ( $forms as $form_slug ) {
				foreach ( $form_bool as $option ) {
					$this->options['forms'][ $form_slug ][ $option ] = isset( $_REQUEST['hctpc']['forms'][ $form_slug ][ $option ] );
				}
			}

			/*
			 * If the user has selected images for the CAPTCHA
			 * it is necessary that at least one of the images packages was selected on the General Options tab
			 */
			if (
				( in_array( 'images', $this->options['operand_format'] ) || 'recognition' == $this->options['type'] ) &&
				empty( $this->options['used_packages'] )
			) {
				if ( 'recognition' == $this->options['type'] ) {
					$notices[] = __( 'In order to use "Optical Character Recognition" type, please select at least one of the items in the option "Image Packages".', 'captcha' );
					$this->options['type'] = 'math_actions';
				} else {
					$notices[] = __( 'In order to use images in the CAPTCHA, please select at least one of the items in the option "Image Packages". The "Images" checkbox in "Complexity" option has been disabled.', 'captcha' );
				}
				$key = array_keys( $this->options['operand_format'], 'images' );
				unset( $this->options['operand_format'][$key[0]] );
				if ( empty( $this->options['operand_format'] ) )
					$this->options['operand_format'] = array( 'numbers', 'words' );
			}

			$this->options = apply_filters( 'hctpc_before_save_options', $this->options );
			update_option( 'hctpc_options', $this->options );
			$notice  = implode( '<br />', $notices );
			$message = __( "Settings saved.", 'captcha' );

			return compact( 'message', 'notice' );
		}

		/**
		 * Displays 'settings' menu-tab
		 * @access public
		 * @param void
		 * @return void
		 */
		public function tab_settings() { 
			$options = array(
				'type' => array(
					'type'             => 'radio',
					'title'            => __( 'Captcha Type', 'captcha' ),
					'array_options'    => array(
						'math_actions'   => array( __( 'Arithmetic actions', 'captcha' ) ),
						'recognition'    => array( __( 'Optical Character Recognition (OCR)', 'captcha' ) )
					)
				),
				'math_actions' => array(
					'type'          => 'checkbox',
					'title'         => __( 'Arithmetic Actions', 'captcha' ),
					'array_options' => array(
						'plus'            => array( __( 'Addition', 'captcha' ) . '&nbsp;(+)' ),
						'minus'           => array( __( 'Subtruction', 'captcha' ) . '&nbsp;(-)' ),
						'multiplications' => array( __( 'Multiplication', 'captcha' ) . '&nbsp;(x)' )
					),
					'class'         => 'hctpc_for_math_actions'
				),
				'operand_format' => array(
					'type'              => 'checkbox',
					'title'             => __( 'Complexity', 'captcha' ),
					'array_options'     => array(
						'numbers' => array( __( 'Numbers (1, 2, 3, etc.)', 'captcha' ) ),
						'words'   => array( __( 'Words (one, two, three, etc.)', 'captcha' ) ),
						'images'  => array( __( 'Images', 'captcha' ) )
					),
					'class'             => 'hctpc_for_math_actions'
				),
				'images_count' => array(
					'type'              => 'number',
					'title'             => __( 'Number of Images', 'captcha' ),
					'min'               => 1,
					'max'               => 10,
					'block_description' => __( 'Set a number of images to display simultaneously as a captcha question.', 'captcha' ),
					'class'             => 'hctpc_for_recognition'
				),
				'used_packages' => array(
					'type'  => 'pack_list',
					'title' => __( 'Image Packages', 'captcha' ),
					'class' => 'hctpc_images_options'
				),
				'enlarge_images' => array(
					'type'               => 'checkbox',
					'title'              => __( 'Enlarge Images', 'captcha' ),
					'inline_description' => __( 'Enable to enlarge captcha images on mouseover.', 'captcha' ),
					'class'              => 'hctpc_images_options'
				),
				'display_reload_button' => array(
					'type'               => 'checkbox',
					'title'              => __( 'Reload Button', 'captcha' ),
					'inline_description' => __( 'Enable to display reload button for captcha.', 'captcha' ) ),
				'title' => array(
					'type'  => 'text',
					'title' => __( 'Captcha Title', 'captcha' ) ),
				'required_symbol' => array(
					'type'  => 'text',
					'title' => __( 'Required Symbol', 'captcha' ) ),
				'load_via_ajax' => array(
					'type'               => 'checkbox',
					'title'              => __( 'Advanced Protection', 'captcha' ),
					'inline_description' => __( 'Enable to display captcha when the website page is loaded.', 'captcha' )
				)
			); ?>
			<h3 class="hctpc_tab_label"><?php _e( 'Captcha Settings', 'captcha' ); ?></h3>
			<hr>
			<div class="hctpc_tab_sub_label"><?php _e( 'General', 'captcha' ); ?></div>
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Enable Captcha for', 'captcha' ); ?></th>
					<td>
						<?php foreach ( $this->form_categories as $fieldset_name => $fieldset_data ) { ?>
							<p><?php echo $fieldset_data['title']; ?></p>
							<br>
							<fieldset id="<?php echo $fieldset_name; ?>">
								<?php foreach ( $fieldset_data['forms'] as $form_name ) { ?>
									<label class="hctpc_related">
										<?php $value = $fieldset_name . '_' . $form_name;
										$id = 'hctpc_' . $form_name . '_enable';
										$name = 'hctpc[forms][' . $form_name . '][enable]';
										$checked = !! $this->options['forms'][ $form_name ]['enable'];
										$this->add_checkbox_input( compact( 'id', 'name', 'checked', 'value', 'class', 'disabled' ) );

										echo $this->forms[ $form_name ]['name']; ?>
									</label>
									<br />
								<?php } ?>
							</fieldset>
							<hr>
						<?php } ?>
					</td>
				</tr>
				<?php foreach ( $options as $key => $data ) { ?>
					<tr<?php if ( ! empty( $data['class'] ) ) echo ' class="' . $data['class'] . '"'; ?>>
						<th scope="row"><?php echo ucwords( $data['title'] ); ?></th>
						<td>
							<fieldset>
								<?php $func = "add_{$data['type']}_input";
								if ( isset( $data['array_options'] ) ) {
									$name = 'radio' == $data['type'] ? 'hctpc_' . $key : 'hctpc_' . $key . '[]';
									foreach ( $data['array_options'] as $slug => $sub_data ) {
										$id = "hctpc_{$key}_{$slug}"; ?>
										<label for="<?php echo $id; ?>">
											<?php $checked = 'radio' == $data['type'] ? ( $slug == $this->options[ $key ] ) : in_array( $slug, $this->options[ $key ] );
											$value   = $slug;
											$this->$func( compact( 'id', 'name', 'value', 'checked' ) );
											echo $sub_data[0]; ?>
										</label>
										<br />
									<?php }
								} else {
									$id = isset( $data['array_options'] ) ? '' : ( isset( $this->options[ $key ] ) ? "hctpc_{$key}" : "hctpc_form_general_{$key}" );
									$name    = $id;
									$value   = $this->options[ $key ];
									$checked = !! $value;
									if ( 'used_packages' == $key ) {
										$open_tag = $close_tag = "";
									} else {
										$open_tag = "<label for=\"{$id}\">";
										$close_tag = "</label>";
									}
									if ( isset( $data['min'] ) )
										$min = $data['min'];
									if ( isset( $data['max'] ) )
										$max = $data['max'];
									echo $open_tag;
									$this->$func( compact( 'id', 'name', 'value', 'checked', 'min', 'max' ) );
									echo $close_tag;
									if ( isset( $data['inline_description'] ) ) { ?>
										<span class="hctpc_info"><?php echo $data['inline_description']; ?></span>
									<?php }
								} ?>
							</fieldset>
							<?php if ( isset( $data['block_description'] ) ) { ?>
								<span class="hctpc_info"><?php echo $data['block_description']; ?></span>
							<?php } ?>
						</td>
					</tr>
				<?php }

				$options = array(
					array(
						'id'                 => "hctpc_enable_time_limit",
						'name'               => "hctpc_enable_time_limit",
						'checked'            => $this->options['enable_time_limit'],
						'inline_description' => __( 'Enable to activate a time limit requeired to complete captcha.', 'captcha' )
					),
					array(
						'id'    => "hctpc_time_limit",
						'name'  => "hctpc_time_limit",
						'value' => $this->options['time_limit'],
						'min'   => 10,
						'max'   => 9999
					)
				); ?>
				<tr>
					<th scope="row"><?php _e( 'Time Limit', 'captcha' ); ?></th>
					<td>
						<?php $this->add_checkbox_input( $options[0] ); ?>
						<span class="hctpc_info"><?php echo $options[0][ 'inline_description' ]; ?></span>
					</td>
				</tr>
				<tr class="hctpc_time_limit" <?php echo $options[0]['checked'] ? '' : ' style="display: none"'; ?>>
					<th scope="row"><?php _e( 'Time Limit Thershold', 'captcha' ); ?></th>
					<td>
						<span class="hctpc_time_limit">
							<?php $this->add_number_input( $options[1] ); echo '&nbsp;' . _e( 'sec', 'captcha' ); ?>
						</span>
					</td>
				</tr>
			</table>
			<?php foreach ( $this->forms as $form_slug => $data ) {
				if ( 'wp_comments' != $form_slug )
					continue;				

				foreach ( $this->form_categories as $category_name => $category_data ) {
					if ( in_array( $form_slug, $category_data['forms'] ) ) {
						if ( 'wp_default' == $category_name )
							$category_title = 'WordPress - ';
						else
							$category_title = $category_data['title'] . ' - ';
						break;
					}
				} ?>
				<div class="hctpc_tab_sub_label hctpc_<?php echo $form_slug; ?>_related_form"><?php echo $category_title . $data['name']; ?></div>
				<?php if ( 'wp_comments' == $form_slug ) {
					$id     	= "hctpc_form_{$form_slug}_hide_from_registered";
					$name 		= "hctpc[forms][{$form_slug}][hide_from_registered]";
					$checked	= !! $this->options['forms'][ $form_slug ]['hide_from_registered'];
					$style 		= $info = $readonly = '';

					/* Multisite uses common "register" and "lostpassword" forms all sub-sites */
					if (
						$this->is_multisite &&
						in_array( $form_slug, array( 'wp_register', 'wp_lost_password' ) ) &&
						! in_array( get_current_blog_id(), array( 0, 1 ) )
					) {
						$info     = __( 'This option is available only for network or for main blog', 'captcha' );
						$readonly = ' readonly="readonly" disabled="disabled"';
					} elseif ( ! $this->options['forms'][ $form_slug ]['enable'] ) {
						$style = ' style="display: none;"';
					} ?>
					<table class="form-table hctpc_<?php echo $form_slug; ?>_related_form hctpc_related_form_bloc">
						<tr class="hctpc_form_option_hide_from_registered"<?php echo $style; ?>>
							<th scope="row"><?php _e( 'Hide from Registered Users', 'captcha' ); ?></th>
							<td>
								<?php $this->add_checkbox_input( compact( 'id', 'name', 'checked', 'readonly' ) ); ?>
							</td>
						</tr>
					</table><!-- .hctpc_$form_slug -->
				<?php }
			}
		}

		/**
		 * Displays 'messages' menu-tab
		 * @access public
		 * @param void
		 * @return void
		 */
		public function tab_messages() { ?>
			<h3 class="hctpc_tab_label"><?php _e( 'Messages Settings', 'captcha' ); ?></h3>
			<hr>
			<table class="form-table">
				<?php $messages = array(
					'no_answer'             => array(
						'title'         => __( 'Captcha Field is Empty', 'captcha' ),
						'message'       => __( 'Please complete the captcha.', 'captcha' )
					),
					'wrong_answer'             => array(
						'title'         => __( 'Captcha is Incorrect', 'captcha' ),
						'message'       => __( 'Please enter correct captcha value.', 'captcha' )
					),
					'time_limit_off'             => array(
						'title'         => __( 'Time Limit Exceeded', 'captcha' ),
						'message'       => __( 'Time limit exceeded. Please complete the captcha once again.', 'captcha' ),
					),
					'time_limit_off_notice' 	=> array(
						'title'         => __( 'Time Limit Exceeded', 'captcha' ),
						'message'       => __( 'Time limit exceeded. Please complete the captcha once again.', 'captcha' ),
						'description'   => __( 'This message will be displayed above the captcha field.', 'captcha' )
					),
					'whitelist_message'             => array(
						'title'         => __( 'Whitelisted IP', 'captcha' ),
						'message'       => __( 'Your IP address is Whitelisted.', 'captcha' ),
						'description'   => __( 'This message will be displayed instead of the captcha field.', 'captcha' )
					)
				);

				foreach ( $messages as $message_name => $data ) { ?>
					<tr>
						<th scope="row"><?php echo $data['title']; ?></th>
						<td>
							<textarea <?php echo 'id="hctpc_' . $message_name . '" name="hctpc_' . $message_name . '"'; ?>><?php echo trim( $this->options[ $message_name ] ); ?></textarea>
							<?php if ( isset( $data['description'] ) ) { ?>
								<div class="hctpc_info"><?php echo $data['description']; ?></div>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
			</table>
		<?php }

		/**
		 * Displays the HTML radiobutton with the specified attributes
		 * @access private
		 * @param  array  $args   An array of HTML attributes
		 * @return void
		 */
		private function add_radio_input( $args ) { ?>
			<input
				type="radio"
				id="<?php echo $args['id']; ?>"
				name="<?php echo $args['name']; ?>"
				value="<?php echo $args['value']; ?>"
				<?php echo $args['checked'] ? ' checked="checked"' : ''; ?> />
		<?php }

		/**
		 * Displays the HTML checkbox with the specified attributes
		 * @access private
		 * @param  array  $args   An array of HTML attributes
		 * @return void
		 */
		private function add_checkbox_input( $args ) { ?>
			<input
				type="checkbox"
				id="<?php echo $args['id']; ?>"
				name="<?php echo $args['name']; ?>"
				value="<?php echo ! empty( $args['value'] ) ? $args['value'] : 1; ?>"
				<?php echo ( ! empty( $args['disabled'] ) ) ? ' disabled="disabled"' : '';
				echo $args['checked'] ? ' checked="checked"' : ''; ?> />
		<?php }

		/**
		 * Displays the HTML number field with the specified attributes
		 * @access private
		 * @param  array  $args   An array of HTML attributes
		 * @return void
		 */
		private function add_number_input( $args ) { ?>
			<input
				type="number"
				step="1"
				min="<?php echo $args['min']; ?>"
				max="<?php echo $args['max']; ?>"
				id="<?php echo $args['id']; ?>"
				name="<?php echo $args['name']; ?>"
				value="<?php echo $args['value']; ?>" />
		<?php }

		/**
		 * Displays the HTML text field with the specified attributes
		 * @access private
		 * @param  array  $args   An array of HTML attributes
		 * @return void
		 */
		private function add_text_input( $args ) { ?>
			<input
				type="text"
				id="<?php echo $args['id']; ?>"
				name="<?php echo $args['name']; ?>"
				value="<?php echo $args['value']; ?>" />
		<?php }

		/**
		 * Displays the list of available package list on the form options tabs
		 * @access private
		 * @param  array  $args   An array of HTML attributes
		 * @return boolean
		 */
		private function add_pack_list_input( $args ) {
			global $wpdb;

			$package_list = $wpdb->get_results(
				"SELECT
					`{$wpdb->base_prefix}hctpc_packages`.`id`,
					`{$wpdb->base_prefix}hctpc_packages`.`name`,
					`{$wpdb->base_prefix}hctpc_packages`.`folder`,
					`{$wpdb->base_prefix}hctpc_packages`.`settings`,
					`{$wpdb->base_prefix}hctpc_images`.`name` AS `image`
				FROM
					`{$wpdb->base_prefix}hctpc_packages`
				LEFT JOIN
					`{$wpdb->base_prefix}hctpc_images`
				ON
					`{$wpdb->base_prefix}hctpc_images`.`package_id`=`{$wpdb->base_prefix}hctpc_packages`.`id`
				GROUP BY `{$wpdb->base_prefix}hctpc_packages`.`id`
				ORDER BY `name` ASC;",
				ARRAY_A
			);

			if ( empty( $package_list ) ) { ?>
				<span><?php _e( 'The image packages list is empty. Please restore default settings or re-install the plugin to fix this error.', 'captcha' ); ?></span>
				<?php return false;
			}

			if ( $this->is_multisite ) {
				switch_to_blog( 1 );
				$upload_dir = wp_upload_dir();
				restore_current_blog();
			} else {
				$upload_dir = wp_upload_dir();
			}
			$packages_url = $upload_dir['baseurl'] . '/captcha_images'; ?>
			<div class="hctpc_tabs_package_list">
				<ul class="hctpc_tabs_package_list_items">
				<?php foreach ( $package_list as $pack ) {
					$styles = '';
					if ( ! empty( $pack['settings'] ) ) {
						$settings = unserialize( $pack['settings'] );
						if ( is_array( $settings ) ) {
							$styles = ' style="';
							foreach ( $settings as $propery => $value )
								$styles .= "{$propery}: {$value};";
							$styles .= '"';
						}
					}
					$id       = "{$args['id']}_{$pack['id']}";
					$name     = "{$args['name']}[]";
					$value    = $pack['id'];
					$checked  = in_array( $pack['id'], $args['value'] ); ?>
					<li>
						<span><?php $this->add_checkbox_input( compact( 'id', 'name', 'value', 'checked' ) ); ?></span>
						<span><label for="<?php echo $id; ?>"><img src="<?php echo "{$packages_url}/{$pack['folder']}/{$pack['image']}"; ?>" title="<?php echo $pack['name']; ?>"<?php echo $styles; ?>/></label></span>
						<span><label for="<?php echo $id; ?>"><?php echo $pack['name']; ?></label></span>
					</li>
				<?php } ?>
				</ul>
			</div>
			<?php return true;
		}

		/**
		 * Displays messages 'insall now'/'activate' for not active plugins
		 * @param  string $status
		 * @return string
		 */
		private function get_form_message( $slug ) {
			switch ( $this->options['related_plugins_info'][ $slug ]['status'] ) {
				case 'deactivated':
					return ' <a href="plugins.php">' . __( 'Activate', 'captcha' ) . '</a>';
				case 'not_installed':
					return ' <a href="' . $this->options['related_plugins_info'][ $slug ]['link'] . '" target="_blank">' . __( 'Install Now', 'captcha' ) . '</a>';
				default:
					return '';
			}
		}

		/**
		 * Form data from the user call function for the "hctpc_add_form_tab" hook
		 * @access private
		 * @param  string|array   $form_data   Each new form data
		 * @return array                       Sanitized label
		 */
		private function sanitize_new_form_data( $form_data ) {
			$form_data = (array)$form_data;
			/**
			 * Return an array with the one element only
			 * to prevent the processing of potentially dangerous data
			 * @see self::_construct()
			 */
			return array( 'name' => esc_html( trim( $form_data[0] ) ) );
		}
	}
}