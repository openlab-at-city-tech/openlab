<?php
/**
 * Displays the content on the plugin settings page
 */

require_once( dirname( dirname( __FILE__ ) ) . '/bws_menu/class-bws-settings.php' );

if ( ! class_exists( 'Cptch_Settings_Tabs' ) ) {
	class Cptch_Settings_Tabs extends Bws_Settings_Tabs {
		private $forms, $form_categories, $registered_forms;

		/**
		 * Constructor.
		 *
		 * @access public
		 *
		 * @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		 *
		 * @param string $plugin_basename
		 */
		public function __construct( $plugin_basename ) {
			global $cptch_options, $cptch_plugin_info;

			$tabs = array(
				'settings'      => array( 'label' => __( 'Settings', 'captcha' ) ),
				'messages'      => array( 'label' => __( 'Messages', 'captcha' ) ),
				'misc'          => array( 'label' => __( 'Misc', 'captcha' ) ),
				'custom_code'   => array( 'label' => __( 'Custom Code', 'captcha' ) ),
				/*pls */
				'license'       => array( 'label' => __( 'License Key', 'captcha' ) )
				/* pls*/
			);

			if ( ! function_exists( 'cptch_get_default_options' ) )
				require_once( dirname( __FILE__ ) . '/helpers.php' );

			parent::__construct( array(
				'plugin_basename'    => $plugin_basename,
				'plugins_info'       => $cptch_plugin_info,
				'prefix'             => 'cptch',
				'default_options'    => cptch_get_default_options(),
				'options'            => $cptch_options,
				'tabs'               => $tabs,
				'doc_link'           => 'https://docs.google.com/document/d/11_TUSAjMjG7hLa53lmyTZ1xox03hNlEA4tRmllFep3I/',
				/*pls */
				'wp_slug'             => 'cptch',
				'pro_page'           => 'admin.php?page=captcha_pro.php',
				'bws_license_plugin' => 'captcha-pro/captcha_pro.php',
				'link_key'           => '9701bbd97e61e52baa79c58c3caacf6d',
				'link_pn'            => '75'
				/* pls*/			
			) );

			$this->all_plugins = get_plugins();

			$this->forms = array(
				'wp_login'         			=> array( 'name' => __( 'Login form', 'captcha' ) ),
				'wp_register'      			=> array( 'name' => __( 'Registration form', 'captcha' ) ),
				'wp_lost_password' 			=> array( 'name' => __( 'Forgot password form', 'captcha' ) ),
				'wp_comments'      			=> array( 'name' => __( 'Comments form', 'captcha' ) ),
				'bws_contact'      			=> array( 'name' => 'Contact Form' ),
				/*pls */
				'bws_subscriber'            => array( 'name' => 'Subscriber', 'for_pro' => 1 ),
				'cf7_contact'               => array( 'name' => 'Contact Form 7', 'for_pro' => 1 ),
				'buddypress_register'       => array( 'name' => __( 'Registration form', 'captcha' ), 'for_pro' => 1 ),
				'buddypress_comments'       => array( 'name' => __( 'Comments form', 'captcha' ), 'for_pro' => 1 ),
				'buddypress_group'          => array( 'name' => __( 'Create a Group form', 'captcha' ), 'for_pro' => 1 ),
				'woocommerce_login'         => array( 'name' => __( 'Login form', 'captcha' ), 'for_pro' => 1 ),
				'woocommerce_register'      => array( 'name' => __( 'Registration form', 'captcha' ), 'for_pro' => 1 ),
				'woocommerce_lost_password' => array( 'name' => __( 'Forgot password form', 'captcha' ), 'for_pro' => 1 ),
				'woocommerce_checkout'      => array( 'name' => __( 'Checkout form', 'captcha' ), 'for_pro' => 1 )
				/* pls*/
			);

			/*
			 * Add users forms to the forms lists
			 */
			$user_forms = apply_filters( 'cptch_add_form', array() );
			if ( ! empty( $user_forms ) ) {
				/*
				 * Get default form slugs from defaults
				 * which have been added by hook "cptch_add_default_form" */
				$new_default_forms = array_diff( cptch_get_default_forms(), array_keys( $this->forms ) );
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
				),
				'external' => array(
					'title' => __( 'External plugins', 'captcha' ),
					'forms' => array(
						'bws_contact'
					)
				),
				/*pls */
				'other_for_pro' => array(
					'external' => array(
						'title' => __( 'External plugins', 'captcha' ),
						'forms' => array(
							'bws_subscriber',
							'cf7_contact'
						)
					),
					'buddypress' => array(
						'title' => __( 'BuddyPress', 'captcha' ),
						'forms' => array(
							'buddypress_register',
							'buddypress_comments',
							'buddypress_group'
						)
					),
					'woocommerce' => array(
						'title' => __( 'WooCommerce', 'captcha' ),
						'forms' => array(
							'woocommerce_login',
							'woocommerce_register',
							'woocommerce_lost_password',
							'woocommerce_checkout'
						)
					)
				)
				/* pls*/
			);

			/**
			* create list with default compatible forms
			*/
			$this->registered_forms = array_merge(
				$this->form_categories['wp_default']['forms'],
				$this->form_categories['external']['forms'] /*pls */,				
				$this->form_categories['other_for_pro']['external']['forms'],
				$this->form_categories['other_for_pro']['buddypress']['forms'],
				$this->form_categories['other_for_pro']['woocommerce']['forms']/* pls*/
			);

			$user_forms = array_diff( array_keys( $this->forms ), $this->registered_forms );
			if ( ! empty( $user_forms ) )
				$this->form_categories['external']['forms'] = array_merge( $this->form_categories['external']['forms'], $user_forms );

			/**
			* get ralated plugins info
			*/
			$this->options = $this->get_related_plugins_info( $this->options );

			/**
			* The option restoring have place later then $this->__constuct
			* so related plugins info will be lost without this add_filter
			*/
			add_action( get_parent_class( $this ) . '_additional_misc_options', array( $this, 'additional_misc_options' ) );
			add_filter( get_parent_class( $this ) . '_additional_restore_options', array( $this, 'additional_restore_options' ) );
		}

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
			$general_bool    = array( 'load_via_ajax', 'display_reload_button', 'enlarge_images', 'enable_time_limit', 'use_limit_attempts_whitelist' );
			$general_strings = array( 'type', 'title', 'required_symbol', 'no_answer', 'wrong_answer', 'time_limit_off', 'time_limit_off_notice', 'whitelist_message' );

			foreach ( $general_bool as $option ) {
				$this->options[ $option ] = ! empty( $_REQUEST["cptch_{$option}"] );
			}

			foreach ( $general_strings as $option ) {
				$value = isset( $_REQUEST["cptch_{$option}"] ) ? trim( stripslashes( esc_html( $_REQUEST["cptch_{$option}"] ) ) ) : '';

				if ( ! in_array( $option, array( 'title', 'required_symbol' ) ) && empty( $value ) ) {
					/* The index has been added in order to prevent the displaying of this message more than once */
					$notices['a'] = __( 'Text fields in the "Messages" tab must not be empty.', 'captcha' );
				} else {
					$this->options[ $option ] = $value;
				}
			}

			foreach ( $general_arrays as $option => $option_name ) {
				$value = isset( $_REQUEST["cptch_{$option}"] ) && is_array( $_REQUEST["cptch_{$option}"] ) ? array_map( 'esc_html', $_REQUEST["cptch_{$option}"] ) : array();

				/* "Arithmetic actions" and "Complexity" must not be empty */
				if ( empty( $value ) && 'used_packages' != $option && 'recognition' != $this->options['type'] )
					$notices[] = sprintf( __( '"%s" option must not be fully disabled.', 'captcha' ), $option_name );
				else
					$this->options[ $option ] = $value;
			}

			$this->options['images_count'] = isset( $_REQUEST['cptch_images_count'] ) ? absint( $_REQUEST['cptch_images_count'] ) : 4;
			$this->options['time_limit'] = isset( $_REQUEST['cptch_time_limit'] ) ? absint( $_REQUEST['cptch_time_limit'] ) : 120;

			/*
			 * Prepare forms options
			 */
			$forms     = array_keys( $this->forms );
			$form_bool = array( 'enable', 'hide_from_registered' );
			foreach ( $forms as $form_slug ) {
				foreach ( $form_bool as $option ) {
					$this->options['forms'][ $form_slug ][ $option ] = isset( $_REQUEST['cptch']['forms'][ $form_slug ][ $option ] );
				}
			}

			/*
			 * If the user has selected images for the CAPTCHA
			 * it is necessary that at least one of the images packages was selected on the General Options tab
			 */
			if (
				( $this->images_enabled() || 'recognition' == $this->options['type'] ) &&
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

			$this->options = apply_filters( 'cptch_before_save_options', $this->options );
			update_option( 'cptch_options', $this->options );
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
					'class'         => 'cptch_for_math_actions'
				),
				'operand_format' => array(
					'type'              => 'checkbox',
					'title'             => __( 'Complexity', 'captcha' ),
					'array_options'     => array(
						'numbers' => array( __( 'Numbers (1, 2, 3, etc.)', 'captcha' ) ),
						'words'   => array( __( 'Words (one, two, three, etc.)', 'captcha' ) ),
						'images'  => array( __( 'Images', 'captcha' ) )
					),
					'class'             => 'cptch_for_math_actions'
				),
				'images_count' => array(
					'type'              => 'number',
					'title'             => __( 'Number of Images', 'captcha' ),
					'min'               => 1,
					'max'               => 10,
					'block_description' => __( 'Set a number of images to display simultaneously as a captcha question.', 'captcha' ),
					'class'             => 'cptch_for_recognition'
				),
				'use_limit_attempts_whitelist' => array(
					'type'              => 'radio',
					'title'             => __( 'Whitelist', 'captcha' ),
					'block_description' => __( 'With a whitelist you can hide captcha field for your personal and trusted IP addresses.', 'captcha' ),
					'array_options'     => array(
						'0' => array( __( 'Default', 'captcha' ) ),
						'1' => array( __( 'Limit Attempts', 'captcha' ) . ' ' . $this->get_form_message( 'limit_attempts' ) ),
					)
				),
				'used_packages' => array(
					'type'  => 'pack_list',
					'title' => __( 'Image Packages', 'captcha' ),
					'class' => 'cptch_images_options'
				),
				/*pls */
				'use_several_packages' => array(
					'type'   => 'checkbox',
					'title' => __( 'Use several image packages at the same time', 'captcha' ),
					'class' => 'cptch_images_options cptch_enable_to_use_several_packages.'
				),
				/* pls*/
				'enlarge_images' => array(
					'type'               => 'checkbox',
					'title'              => __( 'Enlarge Images', 'captcha' ),
					'inline_description' => __( 'Enable to enlarge captcha images on mouseover.', 'captcha' ),
					'class'              => 'cptch_images_options'
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
			<h3 class="bws_tab_label"><?php _e( 'Captcha Settings', 'captcha' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<div class="bws_tab_sub_label"><?php _e( 'General', 'captcha' ); ?></div>
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Enable Captcha for', 'captcha' ); ?></th>
					<td>
						<?php foreach ( $this->form_categories as $fieldset_name => $fieldset_data ) {
							/**
							* All missed forms will be displayed later in pro blocks
							*/
							if ( 'other_for_pro' == $fieldset_name )
								continue; ?>
							<p><?php echo $fieldset_data['title']; ?></p>
							<br>
							<fieldset id="<?php echo $fieldset_name; ?>">
								<?php foreach ( $fieldset_data['forms'] as $form_name ) { ?>
									<label class="cptch_related">
										<?php $disabled = 'bws_contact' == $form_name && 'active' != $this->options['related_plugins_info']['bws_contact']['status'];
										$value = $fieldset_name . '_' . $form_name;
										$id = 'cptch_' . $form_name . '_enable';
										$name = 'cptch[forms][' . $form_name . '][enable]';
										$checked = !! $this->options['forms'][ $form_name ]['enable'];
										$this->add_checkbox_input( compact( 'id', 'name', 'checked', 'value', 'class', 'disabled' ) );

										echo $this->forms[ $form_name ]['name'];
										if ( 'bws_contact' == $form_name ) {
											/**
											* display the "install/activate" message
											*/
											echo $this->get_form_message( 'bws_contact' );

											if ( is_plugin_active( 'contact-form-multi/contact-form-multi.php' ) ||
													is_plugin_active( 'contact-form-multi-pro/contact-form-multi-pro.php' ) ) { ?>
												<span class="bws_info"> <?php _e( 'Enable to add the CAPTCHA to forms on their settings pages.', 'captcha' ); ?></span>
											<?php }
										} ?>
									</label>
									<br />
								<?php } ?>
							</fieldset>
							<hr>
						<?php } ?>
					</td>
				</tr>
				<!-- pls -->
			</table>
			<?php if ( ! $this->hide_pro_tabs ) { ?>
				<div class="bws_pro_version_bloc">
					<div class="bws_pro_version_table_bloc">
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'captcha' ); ?>"></button>
						<div class="bws_table_bg"></div>
						<table class="form-table bws_pro_version">
							<tr>
								<th></th>
								<td>
									<?php foreach ( $this->form_categories['other_for_pro'] as $fieldset_name => $fieldset_data ) { ?>
										<p><?php echo $fieldset_data['title']; ?></p>
										<br>
										<fieldset id="<?php echo $fieldset_name; ?>">
											<?php foreach ( $fieldset_data['forms'] as $form_name => $form_data ) { ?>
												<label>
													<input type="checkbox" disabled="disabled">
													<?php echo $this->forms[ $form_data ]['name']; ?>
												</label>
												<br />
											<?php } ?>
										</fieldset>
										<hr>
									<?php } ?>
								</td>
							</tr>
						</table>
					</div>
					<?php $this->bws_pro_block_links(); ?>
				</div>
			<?php } ?>			
			<table class="form-table">
				<!-- end pls -->
				<?php foreach ( $options as $key => $data ) {
					if ( 'use_several_packages' == $key ) {
						if ( ! $this->hide_pro_tabs ) { ?>
							</table>
							<div class="bws_pro_version_bloc">
								<div class="bws_pro_version_table_bloc">
									<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'captcha' ); ?>"></button>
									<div class="bws_table_bg"></div>
									<?php cptch_use_several_packages(); ?>
								</div>
								<?php $this->bws_pro_block_links(); ?>
							</div>
							<table class="form-table">
						<?php }
						continue;
					} ?>
					<tr<?php if ( ! empty( $data['class'] ) ) echo ' class="' . $data['class'] . '"'; ?>>
						<th scope="row"><?php echo ucwords( $data['title'] ); ?></th>
						<td>
							<fieldset>
								<?php $func = "add_{$data['type']}_input";
								if ( isset( $data['array_options'] ) ) {
									$name = 'radio' == $data['type'] ? 'cptch_' . $key : 'cptch_' . $key . '[]';
									foreach ( $data['array_options'] as $slug => $sub_data ) {
										$id = "cptch_{$key}_{$slug}"; ?>
										<label for="<?php echo $id; ?>">
											<?php if (
												'use_limit_attempts_whitelist' == $key &&
												$slug &&
												'active' != $this->options['related_plugins_info']['limit_attempts']['status']
											) { ?>
												<input type="radio" id="<?php echo $id; ?>" name="<?php echo $name; ?>" disabled="disabled" />
											<?php } else {
												$checked = 'radio' == $data['type'] ? ( $slug == $this->options[ $key ] ) : in_array( $slug, $this->options[ $key ] );
												$value   = $slug;
												$this->$func( compact( 'id', 'name', 'value', 'checked' ) );
											}
											echo $sub_data[0]; ?>
										</label>
										<br />
									<?php }
								} else {
									$id = isset( $data['array_options'] ) ? '' : ( isset( $this->options[ $key ] ) ? "cptch_{$key}" : "cptch_form_general_{$key}" );
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
										<span class="bws_info"><?php echo $data['inline_description']; ?></span>
									<?php }
								} ?>
							</fieldset>
							<?php if ( isset( $data['block_description'] ) ) { ?>
								<span class="bws_info"><?php echo $data['block_description']; ?></span>
							<?php } ?>
						</td>
					</tr>
				<?php }

				$options = array(
					array(
						'id'                 => "cptch_enable_time_limit",
						'name'               => "cptch_enable_time_limit",
						'checked'            => $this->options['enable_time_limit'],
						'inline_description' => __( 'Enable to activate a time limit requeired to complete captcha.', 'captcha' )
					),
					array(
						'id'    => "cptch_time_limit",
						'name'  => "cptch_time_limit",
						'value' => $this->options['time_limit'],
						'min'   => 10,
						'max'   => 9999
					)
				); ?>
				<tr>
					<th scope="row"><?php _e( 'Time Limit', 'captcha' ); ?></th>
					<td>
						<?php $this->add_checkbox_input( $options[0] ); ?>
						<span class="bws_info"><?php echo $options[0][ 'inline_description' ]; ?></span>
					</td>
				</tr>
				<tr class="cptch_time_limit" <?php echo $options[0]['checked'] ? '' : ' style="display: none"'; ?>>
					<th scope="row"><?php _e( 'Time Limit Thershold', 'captcha' ); ?></th>
					<td>
						<span class="cptch_time_limit">
							<?php $this->add_number_input( $options[1] ); echo '&nbsp;' . _e( 'sec', 'captcha' ); ?>
						</span>
					</td>
				</tr>
			</table>
			<?php foreach ( $this->forms as $form_slug => $data ) {
				if ( isset( $data['for_pro'] ) || ( 'wp_comments' != $form_slug && $this->hide_pro_tabs ) )
					continue;				

				foreach ( $this->form_categories as $category_name => $category_data ) {
					if ( in_array( $form_slug, $category_data['forms'] ) ) {
						if ( 'wp_default' == $category_name )
							$category_title = 'WordPress - ';
						elseif ( 'external' == $category_name )
							$category_title = '';
						else
							$category_title = $category_data['title'] . ' - ';
						break;
					}
				} ?>
				<div class="bws_tab_sub_label cptch_<?php echo $form_slug; ?>_related_form"><?php echo $category_title . $data['name']; ?></div>
				<?php if ( 'wp_comments' == $form_slug ) {
					$id     	= "cptch_form_{$form_slug}_hide_from_registered";
					$name 		= "cptch[forms][{$form_slug}][hide_from_registered]";
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
					<table class="form-table cptch_<?php echo $form_slug; ?>_related_form cptch_related_form_bloc">
						<tr class="cptch_form_option_hide_from_registered"<?php echo $style; ?>>
							<th scope="row"><?php _e( 'Hide from Registered Users', 'captcha' ); ?></th>
							<td>
								<?php $this->add_checkbox_input( compact( 'id', 'name', 'checked', 'readonly' ) ); ?>
							</td>
						</tr>
					</table><!-- .cptch_$form_slug --><!-- pls -->
					<?php if ( ! $this->hide_pro_tabs ) { ?>
						<div class="bws_pro_version_bloc cptch_<?php echo $form_slug; ?>_related_form cptch_related_form_bloc">
							<div class="bws_pro_version_table_bloc">
								<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'captcha' ); ?>"></button>
								<div class="bws_table_bg"></div>
								<?php cptch_additional_options(); ?>
							</div> <!-- .bws_pro_version_table_bloc -->
							<?php $this->bws_pro_block_links(); ?>
						</div>
					<?php } ?>
					<!-- end pls -->
				<?php } else { ?>
					<!-- pls -->
					<div class="bws_pro_version_bloc cptch_<?php echo $form_slug; ?>_related_form cptch_related_form_bloc">
						<div class="bws_pro_version_table_bloc">
							<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'captcha' ); ?>"></button>
							<div class="bws_table_bg"></div>
							<?php $plugin = cptch_get_plugin( $form_slug );

							if ( ! empty( $plugin ) ) {
								/* Don't display form options if there is to old plugin version */
								if ( 'active' == $this->options['related_plugins_info'][ $plugin ]['status'] &&
									! $this->options['related_plugins_info'][ $plugin ]['compatible']
								) {
									$link        = $this->options['related_plugins_info'][ $plugin ]['link'];
									$plugin_name = $this->options['related_plugins_info'][ $plugin ]['plugin_info']['Name'];
									$recommended = __( 'update', 'captcha' );
									$to_current  = __( 'to the current version', 'captcha' );
								/* Don't display form options for deactivated or not installed plugins */
								} else {
									switch ( $this->options['related_plugins_info'][ $plugin ]['status'] ) {
										case 'not_installed':
											$link        = $this->options['related_plugins_info'][ $plugin ]['link'];
											$plugin_name = cptch_get_plugin_name( $plugin );
											$recommended = __( 'install', 'captcha' );
											break;
										case 'deactivated':
											$link        = admin_url( '/plugins.php' );
											$plugin_name = $this->options['related_plugins_info'][ $plugin ]['plugin_info']['Name'];
											$recommended = __( 'activate', 'captcha' );
											break;
										default:
											break;
									}
								}
							}

							if ( ! empty( $recommended ) ) { ?>
								<table class="form-table bws_pro_version">						
									<tr>
										<td colspan="2">
											<?php echo __( 'You should', 'captcha' ) .
											"&nbsp;<a href=\"{$link}\" target=\"_blank\">{$recommended}&nbsp;{$plugin_name}</a>&nbsp;" .
											( empty( $to_current ) ? '' : $to_current . '&nbsp;' ) .
											__( 'to use this functionality.', 'captcha' ); ?>
										</td>
									</tr>
								</table>
								<?php unset( $recommended );
							} else {
								cptch_additional_options();
							} ?>
						</div><!-- .bws_pro_version_table_bloc -->
						<?php $this->bws_pro_block_links(); ?>
					</div><!-- .bws_pro_version_bloc -->
					<!-- end pls -->
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
			<h3 class="bws_tab_label"><?php _e( 'Messages Settings', 'captcha' ); ?></h3>
			<?php $this->help_phrase(); ?>
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
							<textarea <?php echo 'id="cptch_' . $message_name . '" name="cptch_' . $message_name . '"'; ?>><?php echo trim( $this->options[ $message_name ] ); ?></textarea>
							<?php if ( isset( $data['description'] ) ) { ?>
								<div class="bws_info"><?php echo $data['description']; ?></div>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
			</table>
		<?php }

		/**
		 * Display custom options on the 'misc' tab
		 * @access public
		 */
		public function additional_misc_options() {
			do_action( 'cptch_settings_page_misc_action', $this->options );
		}

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
					`{$wpdb->base_prefix}cptch_packages`.`id`,
					`{$wpdb->base_prefix}cptch_packages`.`name`,
					`{$wpdb->base_prefix}cptch_packages`.`folder`,
					`{$wpdb->base_prefix}cptch_packages`.`settings`,
					`{$wpdb->base_prefix}cptch_images`.`name` AS `image`
				FROM
					`{$wpdb->base_prefix}cptch_packages`
				LEFT JOIN
					`{$wpdb->base_prefix}cptch_images`
				ON
					`{$wpdb->base_prefix}cptch_images`.`package_id`=`{$wpdb->base_prefix}cptch_packages`.`id`
				GROUP BY `{$wpdb->base_prefix}cptch_packages`.`id`
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
			$packages_url = $upload_dir['baseurl'] . '/bws_captcha_images'; ?>
			<div class="cptch_tabs_package_list">
				<ul class="cptch_tabs_package_list_items">
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
		 * Form data from the user call function for the "cptch_add_form_tab" hook
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

		/**
		 * Whether the images are enabled for the CAPTCHA
		 * @access private
		 * @param  void
		 * @return boolean
		 */
		private function images_enabled() {
			return in_array( 'images', $this->options['operand_format'] );
		}

		/**
		 * Custom functions for "Restore plugin options to defaults"
		 * @access public
		 */
		public function additional_restore_options( $default_options ) {
			$default_options = $this->get_related_plugins_info( $default_options );

			/* do not update package selection */
			$default_options['used_packages'] = $this->options['used_packages'];

			return $default_options;
		}

		/**
		 * Using for adding related plugin's info during the restoring or creating this class
		 * @access public
		 * @param  array
		 * @return array
		 */
		public function get_related_plugins_info( $options ) {
			/**
			* default compatible plugins
			*/
			$compatible_plugins = array(
				'bws_contact' => array( 'contact-form-plugin/contact_form.php', 'contact-form-pro/contact_form_pro.php' ),
				'limit_attempts' => array( 'limit-attempts/limit-attempts.php', 'limit-attempts-pro/limit-attempts-pro.php' )
			);

			foreach ( $compatible_plugins as $plugin_slug => $plugin )
				$options['related_plugins_info'][ $plugin_slug ] = cptch_get_plugin_status( $plugin, $this->all_plugins );

			return $options;
		}
	}
}