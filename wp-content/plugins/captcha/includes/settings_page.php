<?php
/**
 * Displays the content of the "Settings" tab on the pligin settings page
 * @package Captcha Pro by BestWebSoft
 * @since 4.2.3
 */

if ( ! class_exists( 'Cptch_Basic_Settings' ) ) {
	class Cptch_Basic_Settings {
		private $forms;
		private $package_list;
		private $is_multisite;
		private $all_pligins;
		private $plugin_basename;
		private $default_options;
		private $pro_forms;
		private $plugins_info  = array();
		private $hide_pro_tabs = false;

		/**
		 * The class constructor
		 * @access public
		 * @param  string   $plugin_basename
		 * @param  boolean  $is_multisite
		 * @return void
		 */
		public function __construct( $plugin_basename, $is_multisite ) {
			global $cptch_options;
			if ( ! function_exists( 'get_plugins' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			$compatible_plugins = array(
				'bws_contact' => array( 'contact-form-plugin/contact_form.php', 'contact-form-pro/contact_form_pro.php' )
			);

			$this->is_multisite    = $is_multisite;
			$this->all_plugins     = get_plugins();
			$this->plugin_basename = $plugin_basename;
			$this->default_options = cptch_get_default_options();
			$this->hide_pro_tabs   = bws_hide_premium_options_check( $cptch_options );

			foreach ( $compatible_plugins as $plugin_slug => $plugin )
				$this->plugins_info[ $plugin_slug ] = cptch_get_plugin_status( $plugin, $this->all_plugins );

			$this->forms = array(
				'general'                   => array( __( 'General Options', 'captcha' ), '' ),
				'wp_login'                  => array( __( 'WordPress Login form', 'captcha' ), 'login_form.jpg' ),
				'wp_register'               => array( __( 'WordPress Registration form', 'captcha' ), 'register_form.jpg' ),
				'wp_lost_password'          => array( __( 'WordPress Reset Password form', 'captcha' ), 'lost_password_form.jpg' ),
				'wp_comments'               => array( __( 'WordPress Comments form', 'captcha' ), 'comment_form.jpg' ),
				'bws_contact'               => array( 'Contact Form by BestWebSoft', 'contact_form.jpg' )
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
					foreach( $new_forms as $new_form ) {
						if ( empty( $cptch_options['forms'][ $new_form ] ) )
							$cptch_options['forms'][ $new_form ] = $this->default_options['forms'][ $new_form ];
					}
				}
			}

			/*
			 * The list of plugins forms, which are compatible with the Pro plugin version,
			 * are not initialized in the $this->forms variable directly
			 * to display custom forms tabs before Pro ad tabs
			 */
			$pro_forms = array(
				'bws_subscriber'            => array( 'Subscriber by BestWebSoft', 'subscribe_form.jpg' ),
				'cf7_contact'               => array( 'Contact Form 7', 'contact_form.jpg' ),
				'buddypress_register'       => array( __( 'Buddypress Registration form', 'captcha' ), 'bdp_register_form.jpg' ),
				'buddypress_comments'       => array( __( 'Buddypress Comments form', 'captcha' ), 'bdp_comments_form.jpg' ),
				'buddypress_group'          => array( __( 'Buddypress "Create a Group" form', 'captcha' ), 'bdp_group_form.jpg' ),
				'woocommerce_login'         => array( __( 'WooCommerce Login form', 'captcha' ), 'woocommerce.png' ),
				'woocommerce_register'      => array( __( 'WooCommerce Register form', 'captcha' ), 'woocommerce-register.png' ),
				'woocommerce_lost_password' => array( __( 'WooCommerce Lost Password form', 'captcha' ), 'woocommerce-lostpassword.png' ),
				'woocommerce_checkout'      => array( __( 'WooCommerce Checkout Billing form', 'captcha' ), 'woocommerce-checkout.png' )
			);
			$this->forms     = array_merge( $this->forms, $pro_forms );
			$this->pro_forms = array_keys( $pro_forms );
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
			return array( esc_html( trim( $form_data[0] ) ) );
		}

		/**
		 * Displays the content of the "Settings" on the plugin settings page
		 * @access public
		 * @param  void
		 * @return void
		 */
		public function display_content() {
			global $cptch_options;
			$error = $message = $notice = "";
			if (
				isset( $_REQUEST['cptch_form_submit'] ) &&
				check_admin_referer( $this->plugin_basename, 'cptch_nonce_name' )
			) {
				$result = $this->save_options();
				if ( ! empty( $result['error'] ) )
					$error = $result['error'];
				if ( ! empty( $result['message'] ) )
					$message = $result['message'];
				if ( ! empty( $result['notice'] ) )
					$notice = $result['notice'];
			}

			/* Restore default settings */
			if (
				isset( $_REQUEST['bws_restore_confirm'] ) &&
				check_admin_referer( $this->plugin_basename, 'bws_settings_nonce_name' )
			) {
				$this->restore_options();
				$message = __( 'All plugin settings were restored.', 'captcha' );
			} ?>

			<div class="updated fade below-h2" <?php if ( empty( $message ) ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div class="updated fade below-h2" <?php if ( empty( $notice ) ) echo "style=\"display:none\""; ?>><p><strong><?php echo $notice; ?></strong></p></div>
			<div class="error below-h2" <?php if ( empty( $error ) ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>

			<?php if ( isset( $_REQUEST['bws_restore_default'] ) && check_admin_referer( $this->plugin_basename, 'bws_settings_nonce_name' ) ) {
				bws_form_restore_default_confirm( $this->plugin_basename );
			} else {
				bws_show_settings_notice(); ?>
				<div id="cptch_settings_form_block">
					<form class="bws_form" method="post" action="">
						<div id="cptch_settings_slick">
							<?php $this->display_forms_list( 'div' ); ?>
						</div>
						<div id="cptch_settings_tabs_wrapper">
							<div id="cptch_settings_tabs_bg"></div>
							<ul id="cptch_settings_tabs">
								<?php $this->display_forms_list( 'li' ); ?>
							</ul>
							<?php $this->display_tabs(); ?>
						</div>
						<input type="hidden" name="cptch_form_submit" value="submit" />
						<input type="hidden" name="cptch_active_tab" value="<?php echo isset( $_REQUEST['cptch_active_tab'] ) ? absint( $_REQUEST['cptch_active_tab'] ) : 0; ?>" />
						<p class="submit">
							<input id="bws-submit-button" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'captcha' ); ?>" />
						</p>
						<?php wp_nonce_field( $this->plugin_basename, 'cptch_nonce_name' ); ?>
					</form>
					<?php bws_form_restore_default_settings( $this->plugin_basename ); ?>
				</div>
			<?php }
		}

		/**
		 * Save plugin options to the database
		 * @see    self::display_content();
		 * @access private
		 * @param  void
		 * @return array    The action results
		 */
		private function save_options() {
			global $cptch_options, $wpdb;
			$notices = array();

			/*
			 * Prepare general options
			 */
			$general_arrays  = array( 'math_actions', 'operand_format', 'used_packages' );
			$general_bool    = array( 'load_via_ajax', 'display_reload_button', 'enlarge_images', 'enable_time_limit' );
			$general_strings = array( 'title', 'required_symbol', 'no_answer', 'wrong_answer', 'time_limit_off', 'time_limit_off_notice', 'whitelist_message' );

			/**
			 * To make an compatibility with old
			 * Contact Form an Subscriber plugins versions
			 * @deprecated since 4.2.3
			 * @todo remove after 1.03.2017
			 */
			$related_options = array(
				'required_symbol' => array( 'cptch_required_symbol' ),
				'title'           => array( 'cptch_label_form' ),
				'bws_contact'     => array( 'cptch_contact_form' )
			);

			$option_notices = array(
				'math_actions'   => __( 'Arithmetic actions', 'captcha' ),
				'operand_format' => __( 'Complexity', 'captcha' )
			);

			foreach ( $general_arrays as $option ) {
				$query = "cptch_{$option}";
				$value = isset( $_REQUEST[ $query ] ) && is_array( $_REQUEST[ $query ] ) ? array_map( 'esc_html', $_REQUEST[ $query ] ) : array();

				/* "Arithmetic actions" and "Complexity" must not be empty */
				if ( empty( $value ) && isset( $option_notices[ $option ] ) )
					$notices[] = sprintf( __( '"%s" option must not be fully disabled', 'captcha' ), $option_notices[ $option ] ) . '.';
				else
					$cptch_options[ $option ] = $value;
			}

			foreach ( $general_bool as $option )
				$cptch_options[ $option ] = isset( $_REQUEST["cptch_{$option}"] );

			foreach ( $general_strings as $option ) {
				$query = "cptch_{$option}";
				$value = isset( $_REQUEST[ $query ] ) ? trim( stripslashes( esc_html( $_REQUEST[ $query ] ) ) ) : '';

				if ( ! in_array( $option, array( 'title', 'required_symbol' ) ) && empty( $value ) ) {
					/* The index has been added in order to prevent the displaying of this message more than once */
					$notices['a'] = __( 'Text fields in the "Notification messages" option must not be empty', 'captcha' ) . '.';
				} else {
					$cptch_options[ $option ] = $value;

					/**
					 * @deprecated since 4.2.3
					 * @todo remove the next condition block after 1.03.2017
					 */
					if ( array_key_exists( $option, $related_options ) ) {
						foreach ( $related_options[ $option ] as $related )
							$cptch_options[ $related ] = $value;
					}
				}
			}

			$cptch_options['time_limit'] = isset( $_REQUEST['cptch_time_limit'] ) ? absint( $_REQUEST['cptch_time_limit'] ) : 120;

			/*
			 * Prepare forms options
			 */
			$forms     = array_keys( $this->forms );
			$form_bool = array( 'enable', 'hide_from_registered' );
			foreach( $forms as $form_slug ) {

				foreach( $form_bool as $option ) {
					$cptch_options['forms'][ $form_slug ][ $option ] = isset( $_REQUEST['cptch']['forms'][ $form_slug ][ $option ] );

					/**
					 * @deprecated since 4.2.3
					 * @todo remove the next condition block after 1.03.2017
					 */
					if ( 'enable' == $option && array_key_exists( $form_slug, $related_options ) ) {
						foreach ( $related_options[ $form_slug ] as $related_option )
							$cptch_options[ $related_option ] = $cptch_options['forms'][ $form_slug ][ $option ] ? 1 : 0;
					}

				}
			}

			/*
			 * If the user has selected images for the CAPTCHA
			 * it is necessary that at least one of the images packages was selected on the General Options tab
			 */
			if (
				$this->images_enabled() &&
				empty( $cptch_options['used_packages'] )
			) {
				$notices[] = __( 'In order to use images in the CAPTCHA, please select at least one of the items in the option "Use image packages". The "Images" checkbox in "Complexity" option has been disabled', 'captcha' ) . '.';
				$key = array_keys( $cptch_options['operand_format'], 'images' );
				unset( $cptch_options['operand_format'][$key[0]] );
				if ( empty( $cptch_options['operand_format'] ) )
					$cptch_options['operand_format'] = $this->default_options['operand_format'];
			}

			update_option( 'cptch_options', $cptch_options );
			$notice  = implode( '<br />', $notices );
			$message = __( "Settings saved", 'captcha' );

			return compact( 'message', 'notice' );
		}

		/**
		 * Whether the images are enabled for the CAPTCHA
		 * @see    self::display_general_options(), self::display_tab_options(), self::save_options();
		 * @access private
		 * @param  void
		 * @return boolean
		 */
		private function images_enabled() {
			global $cptch_options;
			return in_array( 'images', $cptch_options['operand_format'] );
		}

		/**
		 * Restore plugin options to defaults
		 * @see    self::display_content();
		 * @access private
		 * @param  void
		 * @return void
		 */
		private function restore_options() {
			global $cptch_options;
			if ( ! class_exists( 'Cptch_Package_Loader' ) )
				require_once( dirname( __FILE__ ) . '/package_loader.php' );
			$package_loader = new Cptch_Package_Loader();
			$package_loader->save_packages( dirname( dirname( __FILE__ ) ) . '/images/package', false, 'update' );
			$pack_list = $cptch_options['used_packages'];
			$cptch_options = $this->default_options;
			$cptch_options['used_packages'] = $pack_list;
			update_option( 'cptch_options', $cptch_options );
		}

		/**
		 * Displays the list of forms, which are compatible with the plugin on the plugin settings page
		 * @see    self::display_content();
		 * @access private
		 * @param  string    $tag     The HTML tag of the each list item
		 * @return void
		 */
		private function display_forms_list( $tag ) {
			foreach ( $this->forms as $form_slug => $data ) {
				$is_pro_tab = in_array( $form_slug, $this->pro_forms );
				if ( $is_pro_tab && $this->hide_pro_tabs )
					continue;
				$plugin_slug = cptch_get_plugin( $form_slug );
				$label       = esc_html( $data[0] );
				$class       = empty( $plugin_slug ) ? '' : "cptch_tab_{$this->plugins_info[ $plugin_slug ]['status']}";
				$tag_class   = $is_pro_tab ? ' class="cptch_pro_tab"' : '';
				echo "<{$tag}{$tag_class}><a class=\"{$class}\" href=\"#cptch_{$form_slug}_tab\">{$label}</a></{$tag}>";
			}
		}

		/**
		 * Displays the content of form options
		 * @see    self::display_content();
		 * @access private
		 * @param  void
		 * @return void
		 */
		private function display_tabs() {
			foreach ( $this->forms as $form_slug => $data ) {
				$is_pro_tab = in_array( $form_slug, $this->pro_forms );
				if ( $is_pro_tab && $this->hide_pro_tabs )
					continue; ?>
				<div class="cptch_form_tab" id="cptch_<?php echo $form_slug; ?>_tab">
					<h3 class="cptch_form_tab_label">
						<?php echo $data[0];
						if ( ! empty( $this->forms[ $form_slug ][1] ) ) {
							$src   = plugins_url( "images/{$this->forms[ $form_slug ][1]}", dirname( __FILE__ ) );
							$content = "<img src=\"{$src}\" title=\"{$this->forms[ $form_slug ][0]}\" alt=\"{$this->forms[ $form_slug ][0]}\" />";
							$this->add_tooltip( $content, 'cptch_thumb_block' );
						} ?>
					</h3>
					<?php if ( $is_pro_tab ) {
						cptch_pro_block( 'cptch_option_tab' );
					} else {
						'general' == $form_slug ? $this->display_general_options() : $this->display_tab_options( $form_slug );
					} ?>
				</div>
			<?php }
		}

		/**
		 * Displays the tooltip on the plugin options page
		 * @see    self::display_tabs();
		 * @access private
		 * @param  string  $content   The content of the tooltip
		 * @param  string  $class     The CSS-class of the tooltip
		 * @return void
		 */
		private function add_tooltip( $content, $class = '' ) { ?>
			<span class="bws_help_box dashicons dashicons-editor-help <?php echo $class; ?>">
				<span class="bws_hidden_help_text">
					<?php echo $content; ?>
				</span>
			</span>
		<?php }

		/**
		 * Displays general plugin options
		 * @see    self::display_tabs();
		 * @access private
		 * @param  void
		 * @return void
		 */
		private function display_general_options() {
			global $cptch_options;
			$ajax_load_tooltip = sprintf( __( "With this option the CAPTCHA will be generated via %s into the form after the end of the page loading. In this case, the most of spam bots can't figure out the answer to the CAPTCHA automatically because they just can't get the CAPTCHA content", 'captcha' ) . '.', '<a href="https://developer.mozilla.org/en-US/docs/AJAX" target="_blank">AJAX</a>' );
			$options = array(
				'math_actions'          => array( 'checkbox', __( 'Arithmetic actions', 'captcha' ) ),
				'operand_format'        => array( 'checkbox', __( 'Complexity', 'captcha' ) ),
				'used_packages'         => array( 'pack_list', __( 'Use image packages', 'captcha' ) ),
				'use_several_packages'  => '',
				'enlarge_images'        => array( 'checkbox', __( 'Enlarge images on mouseover', 'captcha' ) ),
				'display_reload_button' => array( 'checkbox', __( 'Show reload button', 'captcha' ) ),
				'title'                 => array( 'text', __( 'CAPTCHA title', 'captcha' ) ),
				'required_symbol'       => array( 'text', __( 'Required symbol', 'captcha' ) ),
				'load_via_ajax'         => array( 'checkbox', __( 'Show CAPTCHA after the end of the page loading', 'captcha' ), $ajax_load_tooltip )
			);
			$dirname = dirname(__FILE__);
			$array_options = array(
				'math_actions' => array(
					'plus' => array(
						__( 'Plus', 'captcha' ) . '&nbsp;(+)',
						__( 'seven', 'captcha' ) . ' &#43; 1 = <img src="' . plugins_url( 'images/input.jpg' , $dirname ) . '" alt="" title="" />'
					),
					'minus' => array(
						__( 'Minus', 'captcha' ) . '&nbsp;(-)',
						__( 'eight', 'captcha' ) . ' &minus; 6 = <img src="' . plugins_url( 'images/input.jpg' , $dirname ) . '" alt="" title="" />'
					),
					'multiplications' => array(
						__( 'Multiplication', 'captcha' ) . '&nbsp;(x)',
						'<img src="' . plugins_url( 'images/input.jpg' , $dirname ) . '" alt="" title="" /> &times; 1 = '. __( 'seven', 'captcha' )
					)
				),
				'operand_format' => array(
					'numbers' => array(
						__( 'Numbers', 'captcha' ),
						'5 &minus; <img src="' . plugins_url( 'images/input.jpg' , $dirname ) . '" alt="" title="" /> = 1'
					),
					'words' => array(
						__( 'Words', 'captcha' ),
						__( 'six', 'captcha' ) . ' &#43; ' . __( 'one', 'captcha' ) . ' = <img src="' . plugins_url( 'images/input.jpg' , $dirname ).'" alt="" title="" />'
					),
					'images' => array(
						__( 'Images', 'captcha' ),
						'<span class="cptch_label">
							<span class="cptch_span"><img src="' . plugins_url( 'images/6.png' , $dirname ) . '" /></span>' .
							'<span class="cptch_span">&nbsp;&#43;&nbsp;</span>' .
							'<span class="cptch_span"><img src="' . plugins_url( 'images/input.jpg' , $dirname ) . '" /></span>
							<span class="cptch_span">&nbsp;=&nbsp;</span>
							<span class="cptch_span"><img src="' . plugins_url( 'images/7.png' , $dirname ).'" /></span>
						</span>'
					)
				)
			); ?>
			<table class="form-table">
				<?php
				/**
				 * @deprecated since 4.2.3
				 * @todo remove it after 1.03.2017
				 */
				cptch_display_deprecated_filter();
				foreach( $options as $key => $data ) {

					$is_multi_option = array_key_exists( $key, $array_options );
					$id = $is_multi_option ? '' : ( isset( $cptch_options[ $key ] ) ? "cptch_{$key}" : "cptch_form_general_{$key}" );
					$class =
							in_array( $key, array( 'used_packages', 'enlarge_images', 'use_several_packages' ) )
						?
							' class="cptch_images_options"'
						:
							'';
					if ( 'use_several_packages' == $key ) {
						if ( ! $this->hide_pro_tabs ) { ?>
							<tr<?php echo $class . ( ! empty( $class ) && ! $this->images_enabled() ? ' style="display: none;"' : '' ); ?>>
								<td colspan="2"><?php cptch_pro_block( 'cptch_use_several_packages' ); ?></td>
							</tr>
						<?php }
						continue;
					} ?>
					<tr<?php echo $class . ( ! empty( $class ) && ! $this->images_enabled() ? ' style="display: none;"' : '' ); ?>>
						<th scope="row"><label for="<?php echo $id; ?>"><?php echo $data[1]; ?></label></th>
						<td>
							<fieldset>
								<?php $func = "add_{$data[0]}_input";
								if ( $is_multi_option ) {
									$name = "cptch_{$key}[]";
									foreach ( $array_options[ $key ] as $slug => $sub_data ) {
										$id      = "cptch_{$key}_{$slug}";
										$checked = in_array( $slug, $cptch_options[ $key ] );
										$value   = $slug; ?>
										<label for="<?php echo $id; ?>">
											<?php $this->$func( compact( 'id', 'name', 'value', 'checked' ) );
											echo $sub_data[0]; ?>
										</label>
										<?php $this->add_tooltip(  "<span class=\"cptch_example_fields_actions\">{$sub_data[1]}</span>", 'cptch_option_tooltip' ); ?>
										<br />
									<?php }
								} else {
									$name    = $id;
									$value   = $cptch_options[ $key ];
									$checked = !! $value;
									if ( 'used_packages' == $key ) {
										$open_tag = $close_tag = "";
									} else {
										$open_tag = "<label for=\"{$id}\">";
										$close_tag = "</label>";
									}
									echo $open_tag;
									$this->$func( compact( 'id', 'name', 'value', 'checked' ) );
									echo $close_tag;
									if( ! empty( $data[2] ) )
										$this->add_tooltip( $data[2], 'cptch_option_tooltip' );
								} ?>
							</fieldset>
						</td>
					</tr>
				<?php }
				$this->display_time_limit_option();
				$this->display_notice_options(); ?>
			</table>
		<?php }

		/**
		 * Displays the content of time limit option for each form
		 * @see    self::display_general_options(), self::display_tab_options();
		 * @access private
		 * @param  void
		 * @return void
		 */
		private function display_time_limit_option() {
			global $cptch_options;
			$options = array(
				array(
					'id'      => "cptch_enable_time_limit",
					'name'    => "cptch_enable_time_limit",
					'checked' => $cptch_options['enable_time_limit']
				),
				array(
					'id'      => "cptch_time_limit",
					'name'    => "cptch_time_limit",
					'value'   => $cptch_options['time_limit']
				)
			); ?>
			<tr>
				<th scope="row"><label for="<?php echo $options[0]['id']; ?>"><?php echo __( 'Enable time limit', 'captcha' ); ?></label></th>
				<td>
					<fieldset>
						<?php $this->add_checkbox_input( $options[0] ); ?>
						<span class="cptch_time_limit"<?php echo $options[0]['checked'] ? '' : ' style="display: none;"'; ?>>
							<?php echo '&nbsp;' . __( 'for', 'captcha' ) .'&nbsp;';
							$this->add_number_input( $options[1] ); echo '&nbsp;' . _n( 'second', 'seconds', $options[1]['value'], 'captcha' ); ?>
						</span>
					</fieldset>
				</td>
			</tr>
		<?php }

		/**
		 * Displays the options with plugin service messages
		 * @see    self::display_general_options();
		 * @access private
		 * @param  void
		 * @return void
		 */
		private function display_notice_options() {
			global $cptch_options;

			$options = array(
				'errors' => array(
					'no_answer'             => __( 'If the CAPTCHA field is empty', 'captcha' ),
					'wrong_answer'          => __( 'If the CAPTCHA is incorrect', 'captcha' ),
					'time_limit_off'        => __( 'If the time limit is exhausted', 'captcha' )
				),
				'notices' => array(
					'time_limit_off_notice' => __( 'If the time limit is exhausted (this message will be displayed above the CAPTCHA)', 'captcha' ),
					'whitelist_message'     => __( 'If the user IP is added to the whitelist (this message will be displayed instead of the CAPTCHA)', 'captcha' )
				)
			);
			$labels = array(
				'errors' => array(
					__( 'Errors', 'captcha' ),
					__( 'These messages will be displayed if the CAPTCHA answer has not passed the verification', 'captcha' ) . '.'
				),
				'notices' => array(
					__( 'Info', 'captcha' ),
					__( 'These messages will be displayed inside of the CAPTCHA', 'captcha' ) . '.'
				),
			); ?>

			<tr>
				<th scope="row"><?php echo __( 'Notification messages', 'captcha' ); ?></th>
				<td>
					<fieldset>
						<?php foreach( $options as $key => $notices ) { ?>
							<p>
								<i><?php echo $labels[ $key ][0]; ?></i>
								<?php $this->add_tooltip( $labels[ $key ][1], 'cptch_notice_tooltip' ); ?>
							</p>
							<?php foreach( $notices as $option => $notice ) {
								$id    = $name = "cptch_{$option}";
								$value = $cptch_options[$option]; ?>
								<p><?php $this->add_text_input( compact( 'id', 'name', 'value' ) ); echo $notice; ?></p>
							<?php }
						} ?>
					</fieldset>
				</td>
			</tr>
		<?php }

		/**
		 * Displays the list of options for the current form
		 * @see    self::display_tabs();
		 * @access private
		 * @param  string    $form_slug      The slug of the form
		 * @return boolean
		 */
		private function display_tab_options( $form_slug ) {

			$plugin = cptch_get_plugin( $form_slug );
			if ( ! empty( $plugin ) ) {
				/* Don't display form options if there is to old plugin version */
				if( 'active' == $this->plugins_info[ $plugin ]['status'] &&
					! $this->plugins_info[ $plugin ]['compatible']
				) {
					$link        = $this->plugins_info[$plugin]['link'];
					$plugin_name = $this->plugins_info[$plugin]['plugin_info']['Name'];
					$recommended = __( 'update', 'captcha' );
					$to_current  = __( 'to the current version', 'captcha' );
				/* Don't display form options for deactivated or not installed plugins */
				} else {
					switch ( $this->plugins_info[ $plugin ]['status'] ) {
						case 'not_installed':
							$link        = $this->plugins_info[$plugin]['link'];
							$plugin_name = cptch_get_plugin_name( $plugin );
							$recommended = __( 'install', 'captcha' );
							break;
						case 'deactivated':
							$link        = admin_url( '/plugins.php' );
							$plugin_name = $this->plugins_info[ $plugin ]['plugin_info']['Name'];
							$recommended = __( 'activate', 'captcha' );
							break;
						default:
							break;
					}
				}

				if ( ! empty( $recommended ) ) { ?>
					<div>
						<?php echo __( 'You should', 'captcha' ) .
							"&nbsp;<a href=\"{$link}\" target=\"_blank\">{$recommended}&nbsp;{$plugin_name}</a>&nbsp;" .
							( empty( $to_current ) ? '' : $to_current . '&nbsp;' ) .
							__( 'to use this functionality', 'captcha' ) . '.'; ?>
					</div>
					<?php return false;
				}
			}

			global $cptch_options;
			$options = array(
				'enable'               => __( 'Enable', 'captcha' ),
				'hide_from_registered' => __( 'Hide from registered users', 'captcha' )
			);
			$break = false; ?>

			<table class="form-table">
				<?php foreach( $options as $key => $label ) {

					if ( 'hide_from_registered' == $key && 'wp_comments' != $form_slug )
						continue;

					$id           = "cptch_form_{$form_slug}_{$key}";
					$name         = "cptch[forms][{$form_slug}][{$key}]";
					$checked      = !! $cptch_options['forms'][ $form_slug ][ $key ];
					$style        =  $info = $readonly = '';

					/* Multisite uses common "register" and "lostpassword" forms all sub-sites */
					if (
						$this->is_multisite &&
						in_array( $form_slug, array( 'wp_register', 'wp_lost_password' ) ) &&
						! in_array( get_current_blog_id(), array( 0, 1 ) )
					) {
						$info     = __( 'This option is available only for network or for main blog', 'captcha' );
						$readonly = ' readonly="readonly" disabled="disabled"';
					} elseif ( 'enable' != $key && ! $cptch_options['forms'][ $form_slug ]['enable'] ) {
						$style = ' style="display: none;"';
					} elseif (
						'enable' == $key &&
						'bws_contact' == $form_slug &&
						(
							is_plugin_active( 'contact-form-multi/contact-form-multi.php' ) ||
							is_plugin_active( 'contact-form-multi-pro/contact-form-multi-pro.php' )
						)
					) {
						$info = __( 'Check off for adding the CAPTCHA to forms on their settings pages', 'captcha' );
					} ?>

					<tr class="cptch_form_option_<?php echo $key; ?>"<?php echo $style; ?>>
						<th scope="row"><label for="<?php echo $id; ?>"><?php echo $label; ?></label></th>
						<td>
							<fieldset>
								<?php $this->add_checkbox_input( compact( 'id', 'name', 'checked', 'readonly' ) );
								if ( ! empty( $info ) ) { ?>
									<span class="bws_info"><?php echo $info; ?></span>
								<?php } ?>
							</fieldset>
						</td>
					</tr>
				<?php } ?>
			</table>
			<?php
			cptch_pro_block( 'cptch_additional_options' );
			return true;
		}

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
				value="<?php echo isset( $args['value'] ) ? $args['value'] : 1; ?>"
				<?php echo $args['checked'] ? ' checked="checked"' : '';
				echo isset( $args['readonly'] ) ? $args['readonly'] : ''; ?> />
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
				min="10"
				max="9999"
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
			if ( empty( $this->package_list ) ) {
				global $wpdb;
				$this->package_list = $wpdb->get_results(
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
			}

			if ( empty( $this->package_list ) ) { ?>
				<span><?php _e( 'The image packages list is empty. Please restore default settings or re-install the plugin to fix this error', 'captcha' ); ?>.</span>
				<?php return false;
			}

			if ( is_multisite() ) {
				switch_to_blog( 1 );
				$upload_dir = wp_upload_dir();
				restore_current_blog();
			} else {
				$upload_dir = wp_upload_dir();
			}
			$packages_url = $upload_dir['baseurl'] . '/bws_captcha_images'; ?>
			<div class="cptch_tabs_package_list">
				<ul class="cptch_tabs_package_list_items">
				<?php foreach ( $this->package_list as $pack ) {
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
	}
} ?>