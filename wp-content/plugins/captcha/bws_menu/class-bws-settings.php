<?php
/**
 * Displays the content on the plugin settings page
 * @package BestWebSoft
 * @since 1.9.8
 */

if ( ! class_exists( 'Bws_Settings_Tabs' ) ) {
	class Bws_Settings_Tabs {
		private $tabs;		
		private $plugin_basename;
		private $prefix;
		private $pro_plugin_is_activated = false;
		private $custom_code_args = array();
		private $wp_slug;
				
		public $options;
		public $default_options;
		public $is_network_options;	
		public $plugins_info  = array();
		public $hide_pro_tabs = false;
		public $demo_data;

		public $is_pro = false;
		public $pro_page;
		public $bws_license_plugin;
		public $link_key;
		public $link_pn;
		public $is_trial = false;
		public $trial_days;
		public $bws_hide_pro_option_exist = true;	

		public $forbid_view = false;
		public $change_permission_attr = '';

		public $version;
		public $upload_dir;
		public $all_plugins;
		public $is_multisite;

		public $doc_link;
		public $doc_video_link;

		/**
		 * Constructor.
		 *
		 * The child class should call this constructor from its own constructor to override
		 * the default $args.
		 * @access public
		 * 
		 * @param array|string $args
		 */
		public function __construct( $args = array() ) {
			$args = wp_parse_args( $args, array(
				'plugin_basename' 	 => '',
				'prefix' 			 => '',
				'plugins_info'		=> array(),
				'default_options' 	 => array(),
				'options' 			 => array(),
				'is_network_options' => false,
				'tabs' 				 => array(),
				'doc_link'			=> '',
				'doc_video_link'	=> '',
				'wp_slug'			=> '',
				'demo_data' 		=> false,
				/* if this is free version and pro exist */
				'pro_page'			=> '',
				'bws_license_plugin'=> '',
				'link_key'			=> '',
				'link_pn'			=> '',
				'trial_days'		=> false
			) );

			$args['plugins_info']['Name'] = str_replace( ' by BestWebSoft', '', $args['plugins_info']['Name'] );
			
			$this->plugin_basename		= $args['plugin_basename'];
			$this->prefix				= $args['prefix'];
			$this->plugins_info			= $args['plugins_info'];			
			$this->options				= $args['options'];
			$this->default_options  	= $args['default_options'];
			$this->wp_slug  			= $args['wp_slug'];
			$this->demo_data  			= $args['demo_data'];

			$this->tabs  				= $args['tabs'];
			$this->is_network_options  	= $args['is_network_options'];

			$this->doc_link  			= $args['doc_link'];
			$this->doc_video_link  		= $args['doc_video_link'];

			$this->pro_page  			= $args['pro_page'];
			$this->bws_license_plugin  	= $args['bws_license_plugin'];
			$this->link_key  			= $args['link_key'];
			$this->link_pn  			= $args['link_pn'];
			$this->trial_days  			= $args['trial_days'];

			$this->hide_pro_tabs   		= bws_hide_premium_options_check( $this->options );
			$this->version = '1.0.0';
			$this->is_multisite = is_multisite();

			if ( empty( $this->pro_page ) && array_key_exists( 'license', $this->tabs ) )
				$this->is_pro = true;
		}

		/**
		 * Displays the content of the "Settings" on the plugin settings page
		 * @access public
		 * @param  void
		 * @return void
		 */
		public function display_content() {
			global $bstwbsftwppdtplgns_options;
			if ( array_key_exists( 'custom_code', $this->tabs ) ) {
				/* get args for `custom code` tab */
				$this->get_custom_code();
			}

			$save_results = $this->save_all_tabs_options();

			$this->display_messages( $save_results );
			if ( isset( $_REQUEST['bws_restore_default'] ) && check_admin_referer( $this->plugin_basename, 'bws_nonce_name' ) ) {
				bws_form_restore_default_confirm( $this->plugin_basename );
			} elseif ( isset( $_POST['bws_handle_demo'] ) && check_admin_referer( $this->plugin_basename, 'bws_nonce_name' ) ) {
				$this->demo_data->bws_demo_confirm();
			} else {
				bws_show_settings_notice(); ?>
				<form class="bws_form" method="post" action="" enctype="multipart/form-data">
					<div id="poststuff">
						<div id="post-body" class="metabox-holder columns-2">
							<div id="post-body-content" style="position: relative;">
								<?php $this->display_tabs(); ?>					
							</div><!-- /post-body-content -->
							<div id="postbox-container-1" class="postbox-container">
								<div class="meta-box-sortables ui-sortable">
									<div id="submitdiv" class="postbox">
										<h3 class="hndle"><?php _e( 'Information', 'bestwebsoft' ); ?></h3>
										<div class="inside">
											<div class="submitbox" id="submitpost">
												<div id="minor-publishing">
													<div id="misc-publishing-actions">
														<?php if ( $this->is_pro ) {
															if ( ! isset( $bstwbsftwppdtplgns_options['time_out'][ $this->plugin_basename ] ) || isset( $bstwbsftwppdtplgns_options['wrong_license_key'][ $this->plugin_basename ] ) ) {
																$license_type = 'Pro';
																$license_status = __( 'Inactive', 'bestwebsoft' ) . ' <a href="#' . $this->prefix . '_license_tab" class="bws_trigger_tab_click">' . __( 'Learn More', 'bestwebsoft' ) . '</a>';
															} else {
																$finish = strtotime( $bstwbsftwppdtplgns_options['time_out'][ $this->plugin_basename ] );
																$today = strtotime( date( "m/d/Y" ) );
																if ( isset( $bstwbsftwppdtplgns_options['trial'][ $this->plugin_basename ] ) ) { 
																	$license_type = 'Trial Pro';

																	if ( $finish < $today ) {
																		$license_status = __( 'Expired', 'bestwebsoft' );
																	} else {
																		$daysleft = floor( ( $finish - $today ) / ( 60*60*24 ) );
																		$license_status = sprintf( __( '%s day(-s) left', 'bestwebsoft' ), $daysleft );
																	}
																	$license_status .= '. <a target="_blank" href="' . $this->plugins_info['PluginURI'] . '">' . __( 'Upgrade to Pro', 'bestwebsoft' ) . '</a>';							 		
																} else {
																	$license_type = isset( $bstwbsftwppdtplgns_options['nonprofit'][ $this->plugin_basename ] ) ? 'Nonprofit Pro' : 'Pro';
																	if ( $finish < $today ) {
																		$license_status = sprintf( __( 'Expired on %s', 'bestwebsoft' ), $bstwbsftwppdtplgns_options['time_out'][ $this->plugin_basename ] ) . '. <a target="_blank" href="https://support.bestwebsoft.com/entries/53487136">' . __( 'Renew Now', 'bestwebsoft' ) . '</a>';
																	} else {
																		$license_status = __( 'Active', 'bestwebsoft' );
																	}
																}
															} ?>
															<div class="misc-pub-section">
																<strong><?php _e( 'License', 'bestwebsoft' ); ?>:</strong> <?php echo $license_type; ?>
															</div>
															<div class="misc-pub-section">
																<strong><?php _e( 'Status', 'bestwebsoft' ); ?>:</strong> <?php echo $license_status; ?>
															</div><!-- .misc-pub-section -->
														<?php } ?>
														<div class="misc-pub-section">
															<strong><?php _e( 'Version', 'bestwebsoft' ); ?>:</strong> <?php echo $this->plugins_info['Version']; ?>
														</div><!-- .misc-pub-section -->
													</div>
													<div class="clear"></div>
												</div>
												<div id="major-publishing-actions">
													<div id="publishing-action">
														<input type="hidden" name="<?php echo $this->prefix; ?>_form_submit" value="submit" />
														<input id="bws-submit-button" type="submit" class="button button-primary button-large" value="<?php _e( 'Save Changes', 'bestwebsoft' ); ?>" />
														<?php wp_nonce_field( $this->plugin_basename, 'bws_nonce_name' ); ?>					
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
									<input type="submit" class="button button-primary button-large" value="<?php _e( 'Save Changes', 'bestwebsoft' ); ?>" />
								</div>								
								<?php if ( ! empty( $this->wp_slug ) )
									bws_plugin_reviews_block( $this->plugins_info['Name'], $this->wp_slug ); ?>
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
			<div id="bws_settings_tabs_wrapper"<?php if ( version_compare( $wp_version, '4.0', '<' ) ) echo ' class="edit-form-section"'; ?>>
				<ul id="bws_settings_tabs">
					<?php $this->display_tabs_list(); ?>
				</ul>
				<?php $this->display_tabs_content(); ?>
				<div class="clear"></div>
				<input type="hidden" name="bws_active_tab" value="<?php if ( isset( $_REQUEST['bws_active_tab'] ) ) echo esc_attr( $_REQUEST['bws_active_tab'] ); ?>" />
			</div>
		<?php }

		/**
		 * Displays the list of tabs
		 * @access private
		 * @return void
		 */
		private function display_tabs_list() {
			foreach ( $this->tabs as $tab_slug => $data ) {
				if ( ! empty( $data['is_pro'] ) && $this->hide_pro_tabs )
					continue;
				$tab_class = 'bws-tab-' . $tab_slug;
				if ( ! empty( $data['is_pro'] ) )
					$tab_class .= ' bws_pro_tab';
				if ( ! empty( $data['class'] ) )
					$tab_class .= ' ' . $data['class']; ?>
				<li class="<?php echo $tab_class; ?>" data-slug="<?php echo $tab_slug; ?>">
					<a href="#<?php echo $this->prefix; ?>_<?php echo $tab_slug; ?>_tab">
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
			foreach ( $this->tabs as $tab_slug => $data ) { 
				if ( ! empty( $data['is_pro'] ) && $this->hide_pro_tabs )
					continue; ?>
				<div class="bws_tab ui-tabs-panel ui-widget-content ui-corner-bottom" id="<?php echo $this->prefix . '_' . $tab_slug; ?>_tab" aria-labelledby="ui-id-2" role="tabpanel" aria-hidden="false" style="display: block;">					
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
			if ( isset( $_POST['bws_restore_confirm'] ) && check_admin_referer( $this->plugin_basename, 'bws_settings_nonce_name' ) ) {
				$this->restore_options();
				$message = __( 'All plugin settings were restored.', 'bestwebsoft' );
			/* Go Pro - check license key */
			} elseif ( isset( $_POST['bws_license_submit'] ) && check_admin_referer( $this->plugin_basename, 'bws_nonce_name' ) ) {
				$result = $this->save_options_license_key();
				if ( ! empty( $result['error'] ) )
					$error = $result['error'];
				if ( ! empty( $result['message'] ) )
					$message = $result['message'];
				if ( ! empty( $result['notice'] ) )
					$notice = $result['notice'];
			/* check demo data */
			} else {
				$demo_result = ! empty( $this->demo_data ) ? $this->demo_data->bws_handle_demo_data() : false;
				if ( false !== $demo_result ) {
					if ( ! empty( $demo_result ) && is_array( $demo_result ) ) {
						$error   = $demo_result['error'];
						$message = $demo_result['done'];
						if ( ! empty( $demo_result['done'] ) && ! empty( $demo_result['options'] ) )
							$this->options = $demo_result['options'];
					}
				/* Save options */
				} elseif ( ! isset( $_REQUEST['bws_restore_default'] ) && ! isset( $_POST['bws_handle_demo'] ) && isset( $_REQUEST[ $this->prefix . '_form_submit'] ) && check_admin_referer( $this->plugin_basename, 'bws_nonce_name' ) ) {
					/* save tabs */				
					$result = $this->save_options();
					if ( ! empty( $result['error'] ) )
						$error = $result['error'];
					if ( ! empty( $result['message'] ) )
						$message = $result['message'];
					if ( ! empty( $result['notice'] ) )
						$notice = $result['notice'];

					if ( '' == $this->change_permission_attr ) {
						/* save `misc` tab */
						$result = $this->save_options_misc();
						if ( ! empty( $result['notice'] ) )
							$notice .= $result['notice'];
					}

					if ( array_key_exists( 'custom_code', $this->tabs ) ) {
						/* save `custom code` tab */
						$this->save_options_custom_code();
					}
				}
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
			<div class="updated bws-notice inline" <?php if ( empty( $save_results['notice'] ) ) echo "style=\"display:none\""; ?>><p><strong><?php echo $save_results['notice']; ?></strong></p></div>
			<div class="error inline" <?php if ( empty( $save_results['error'] ) ) echo "style=\"display:none\""; ?>><p><strong><?php echo $save_results['error']; ?></strong></p></div>
		<?php }

		/**
		 * Save plugin options to the database
		 * @access public
		 * @param  ab
		 * @return array    The action results
		 * @abstract
		 */
		public function save_options() {
			die( 'function Bws_Settings_Tabs::save_options() must be over-ridden in a sub-class.' );
		}

		/**
		 * Get 'custom_code' status and content
		 * @access private
		 */
		private function get_custom_code() {
			global $bstwbsftwppdtplgns_options;

			$this->custom_code_args = array(
				'is_css_active' => false,
				'content_css'  	=> '',
				'css_writeable'	=> false,
				'is_php_active' => false,
				'content_php' 	=> '',
				'php_writeable'	=> false,
				'is_js_active' 	=> false,
				'content_js' 	=> '',
				'js_writeable'	=> false,
			);

			if ( ! $this->upload_dir )
				$this->upload_dir = wp_upload_dir();

			$folder = $this->upload_dir['basedir'] . '/bws-custom-code';
			if ( ! $this->upload_dir["error"] ) {
				if ( ! is_dir( $folder ) )
					wp_mkdir_p( $folder, 0755 );

				$index_file = $this->upload_dir['basedir'] . '/bws-custom-code/index.php';
				if ( ! file_exists( $index_file ) ) {
					if ( $f = fopen( $index_file, 'w+' ) )
						fclose( $f );
				}
			}
			
			if ( $this->is_multisite )
				$this->custom_code_args['blog_id'] = get_current_blog_id();

			foreach ( array( 'css', 'php', 'js' ) as $extension ) {
				$file = 'bws-custom-code.' . $extension;
				$real_file = $folder . '/' . $file;
						
				if ( file_exists( $real_file ) ) {
					update_recently_edited( $real_file );
					$this->custom_code_args["content_{$extension}"] = esc_textarea( file_get_contents( $real_file ) );
					if ( ( $this->is_multisite && isset( $bstwbsftwppdtplgns_options['custom_code'][ $this->custom_code_args['blog_id'] ][ $file ] ) ) ||
						( ! $this->is_multisite && isset( $bstwbsftwppdtplgns_options['custom_code'][ $file ] ) ) ) {
						$this->custom_code_args["is_{$extension}_active"] = true;
					}
					if ( is_writeable( $real_file ) )
						$this->custom_code_args["{$extension}_writeable"] = true;
				} else {
					$this->custom_code_args["{$extension}_writeable"] = true;
					if ( 'php' == $extension )
						$this->custom_code_args["content_{$extension}"] = "<?php" . "\n" . "if ( ! defined( 'ABSPATH' ) ) exit;" . "\n" . "if ( ! defined( 'BWS_GLOBAL' ) ) exit;" . "\n\n" . "/* Start your code here */" . "\n";
				}
			}
		}

		/**
		 * Display 'custom_code' tab
		 * @access private
		 */
		private function tab_custom_code() { ?>
			<h3 class="bws_tab_label"><?php _e( 'Custom Code', 'bestwebsoft' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<?php if ( ! current_user_can( 'edit_plugins' ) ) {
				echo '<p>' . __( 'You do not have sufficient permissions to edit plugins for this site.', 'bestwebsoft' ) . '</p>';
				return;
			}

			$list = array( 
				'css' => array( 'description' 	=> __( 'These styles will be added to the header on all pages of your site.', 'bestwebsoft' ),
							'learn_more_link'	=> 'https://developer.mozilla.org/en-US/docs/Web/Guide/CSS/Getting_started' 
						), 
				'php' => array( 'description' 	=> sprintf( __( 'This PHP code will be hooked to the %s action and will be printed on front end only.', 'bestwebsoft' ), '<a href="http://codex.wordpress.org/Plugin_API/Action_Reference/init" target="_blank"><code>init</code></a>' ),
							'learn_more_link'	=> 'http://php.net/' 
						),  
				'js' => array( 'description' 	=> __( 'These code will be added to the header on all pages of your site.', 'bestwebsoft' ),
							'learn_more_link'	=> 'https://developer.mozilla.org/en-US/docs/Web/JavaScript' 
						), 
			);

			if ( ! $this->custom_code_args['css_writeable'] ||
				! $this->custom_code_args['php_writeable'] ||
				! $this->custom_code_args['js_writeable'] ) { ?>
				<p><em><?php printf( __( 'You need to make this files writable before you can save your changes. See %s the Codex %s for more information.', 'bestwebsoft' ),
				'<a href="https://codex.wordpress.org/Changing_File_Permissions" target="_blank">',
				'</a>' ); ?></em></p>
			<?php }

			foreach ( $list as $extension => $extension_data ) {
				$name = 'js' == $extension ? 'JavaScript' : strtoupper( $extension ); ?>
				<p><big>
					<strong><?php echo $name; ?></strong>
					<?php if ( ! $this->custom_code_args["{$extension}_writeable"] )
						echo '(' . __( 'Browsing', 'bestwebsoft' ) . ')'; ?>
				</big></p>
				<p class="bws_info">
					<input type="checkbox" name="bws_custom_<?php echo $extension; ?>_active" value="1" <?php if ( $this->custom_code_args["is_{$extension}_active"] ) echo "checked"; ?> /> 
					<?php printf( __( 'Activate custom %s code.', 'bestwebsoft' ), $name ); ?>
				</p>
				<textarea cols="70" rows="25" name="bws_newcontent_<?php echo $extension; ?>" id="bws_newcontent_<?php echo $extension; ?>"><?php if ( isset( $this->custom_code_args["content_{$extension}"] ) ) echo $this->custom_code_args["content_{$extension}"]; ?></textarea>
				<p class="bws_info">
					<?php echo $extension_data['description']; ?>
					<br>
					<a href="<?php echo $extension_data['learn_more_link']; ?>" target="_blank">
						<?php printf( __( 'Learn more about %s', 'bestwebsoft' ), $name ); ?>
					</a>
				</p>
			<?php }
		}

		/**
		 * Save plugin options to the database
		 * @access private
		 * @return array    The action results
		 */
		private function save_options_custom_code() {
			global $bstwbsftwppdtplgns_options;
			$folder = $this->upload_dir['basedir'] . '/bws-custom-code';

			foreach ( array( 'css', 'php', 'js' ) as $extension ) {				
				$file = 'bws-custom-code.' . $extension;
				$real_file = $folder . '/' . $file;

				if ( isset( $_POST["bws_newcontent_{$extension}"] ) &&
					$this->custom_code_args["{$extension}_writeable"] ) {
					$newcontent = trim( wp_unslash( $_POST["bws_newcontent_{$extension}"] ) );

					if ( ! empty( $newcontent ) && isset( $_POST["bws_custom_{$extension}_active"] ) ) {
						$this->custom_code_args["is_{$extension}_active"] = true;
						if ( $this->is_multisite ) {
							$bstwbsftwppdtplgns_options['custom_code'][ $this->custom_code_args['blog_id'] ][ $file ] = ( 'php' == $extension ) ? $real_file : $this->upload_dir['baseurl'] . '/bws-custom-code/' . $file;
						} else {
							$bstwbsftwppdtplgns_options['custom_code'][ $file ] = ( 'php' == $extension ) ? $real_file : $this->upload_dir['baseurl'] . '/bws-custom-code/' . $file;
						}
					} else {
						$this->custom_code_args["is_{$extension}_active"] = false;
						if ( $this->is_multisite ) {
							if ( isset( $bstwbsftwppdtplgns_options['custom_code'][ $this->custom_code_args['blog_id'] ][ $file ] ) )
								unset( $bstwbsftwppdtplgns_options['custom_code'][ $this->custom_code_args['blog_id'] ][ $file ] );
						} else {
							if ( isset( $bstwbsftwppdtplgns_options['custom_code'][ $file ] ) )
								unset( $bstwbsftwppdtplgns_options['custom_code'][ $file ] );
						}
					}
					if ( $f = fopen( $real_file, 'w+' ) ) {
						fwrite( $f, $newcontent );
						fclose( $f );
						$this->custom_code_args["content_{$extension}"] = $newcontent;
					}
				}
			}

			if ( $this->is_multisite )
				update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
			else
				update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
		}

		/**
		 * Display 'misc' tab
		 * @access private
		 */
		private function tab_misc() { 
			global $bstwbsftwppdtplgns_options; ?>
			<h3 class="bws_tab_label"><?php _e( 'Miscellaneous Settings', 'bestwebsoft' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<?php /**
			 * action - Display custom options on the Import / Export' tab
			 */
			do_action( __CLASS__ . '_additional_misc_options' );

			if ( ! $this->forbid_view && ! empty( $this->change_permission_attr ) ) { ?>
				<div class="error inline bws_visible"><p><strong><?php _e( "Notice", 'bestwebsoft' ); ?>:</strong> <strong><?php printf( __( "It is prohibited to change %s settings on this site in the %s network settings.", 'bestwebsoft' ), $this->plugins_info["Name"], $this->plugins_info["Name"] ); ?></strong></p></div>
			<?php }
			if ( $this->forbid_view ) { ?>
				<div class="error inline bws_visible"><p><strong><?php _e( "Notice", 'bestwebsoft' ); ?>:</strong> <strong><?php printf( __( "It is prohibited to view %s settings on this site in the %s network settings.", 'bestwebsoft' ), $this->plugins_info["Name"], $this->plugins_info["Name"] ); ?></strong></p></div>
			<?php } else { ?>
				<table class="form-table">			
					<?php /**
					 * action - Display custom options on the 'misc' tab
					 */
					do_action( __CLASS__ . '_additional_misc_options_affected' );
					if ( ! empty( $this->pro_page ) && $this->bws_hide_pro_option_exist ) { ?>
						<tr>
							<th scope="row"><?php _e( 'Pro Options', 'bestwebsoft' ); ?></th>
							<td>
								<input <?php echo $this->change_permission_attr; ?> name="bws_hide_premium_options_submit" type="checkbox" value="1" <?php if ( ! $this->hide_pro_tabs ) echo 'checked="checked "'; ?> /> 
								<span class="bws_info"><?php _e( 'Enable to display plugin Pro options.', 'bestwebsoft' ); ?></span>
							</td>
						</tr>
					<?php } ?>
					<tr>
						<th scope="row"><?php _e( 'Track Usage', 'bestwebsoft' ); ?></th>
						<td>
							<input <?php echo $this->change_permission_attr; ?> name="bws_track_usage" type="checkbox" value="1" <?php if ( ! empty( $bstwbsftwppdtplgns_options['track_usage']['products'][ $this->plugin_basename ] ) ) echo 'checked="checked "'; ?>/> 
							<span class="bws_info"><?php _e( 'Enable to allow tracking plugin usage anonymously in order to make it better.', 'bestwebsoft' ); ?></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Default Settings', 'bestwebsoft' ); ?></th>
						<td>
							<input<?php echo $this->change_permission_attr; ?> name="bws_restore_default" type="submit" class="button" value="<?php _e( 'Restore Settings', 'bestwebsoft' ); ?>" />
							<div class="bws_info"><?php _e( 'This will restore plugin settings to defaults.', 'bestwebsoft' ); ?></div>
						</td>
					</tr>
				</table>
			<?php }
		}

		/**
		 * Display 'Import / Export' tab
		 * @access private
		 */
		public function tab_import_export() { ?>
			<h3 class="bws_tab_label"><?php _e( 'Import / Export', 'bestwebsoft' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>						
			<?php /**
			 * action - Display custom options on the Import / Export' tab
			 */
			do_action( __CLASS__ . '_additional_import_export_options' );

			if ( ! $this->forbid_view && ! empty( $this->change_permission_attr ) ) { ?>
				<div class="error inline bws_visible"><p><strong><?php _e( "Notice", 'bestwebsoft' ); ?>:</strong> <strong><?php printf( __( "It is prohibited to change %s settings on this site in the %s network settings.", 'bestwebsoft' ), $this->plugins_info["Name"], $this->plugins_info["Name"] ); ?></strong></p></div>
			<?php }
			if ( $this->forbid_view ) { ?>
				<div class="error inline bws_visible"><p><strong><?php _e( "Notice", 'bestwebsoft' ); ?>:</strong> <strong><?php printf( __( "It is prohibited to view %s settings on this site in the %s network settings.", 'bestwebsoft' ), $this->plugins_info["Name"], $this->plugins_info["Name"] ); ?></strong></p></div>
			<?php } else { ?>
				<table class="form-table">                      
					<?php /**
					 * action - Display custom options on the Import / Export' tab
					 */
					do_action( __CLASS__ . '_additional_import_export_options_affected' ); ?>
				</table>
			<?php }
		}

		/**
		 * Save plugin options to the database
		 * @access private
		 */
		private function save_options_misc() {
			global $bstwbsftwppdtplgns_options, $wp_version;
			/* hide premium options */
			if ( ! empty( $this->pro_page ) ) {
				if ( isset( $_POST['bws_hide_premium_options'] ) ) {
					$hide_result = bws_hide_premium_options( $this->options );
					$this->hide_pro_tabs = true;
					$this->options = $hide_result['options'];
					if ( ! empty( $hide_result['message'] ) )
						$notice = $hide_result['message'];
					if ( $this->is_network_options )
						update_site_option( $this->prefix . '_options', $this->options );
					else
						update_option( $this->prefix . '_options', $this->options );
				} else if ( isset( $_POST['bws_hide_premium_options_submit'] ) ) {
					if ( ! empty( $this->options['hide_premium_options'] ) ) {
						$key = array_search( get_current_user_id(), $this->options['hide_premium_options'] );
						if ( false !== $key )
							unset( $this->options['hide_premium_options'][ $key ] );
						if ( $this->is_network_options )
							update_site_option( $this->prefix . '_options', $this->options );
						else
							update_option( $this->prefix . '_options', $this->options );
					}
					$this->hide_pro_tabs = false;				
				} else {
					if ( empty( $this->options['hide_premium_options'] ) ) {
						$this->options['hide_premium_options'][] = get_current_user_id();
						if ( $this->is_network_options )
							update_site_option( $this->prefix . '_options', $this->options );
						else
							update_option( $this->prefix . '_options', $this->options );
					}
					$this->hide_pro_tabs = true;
				}
			}
			/* Save 'Track Usage' option */
			if ( isset( $_POST['bws_track_usage'] ) ) {
				if ( empty( $bstwbsftwppdtplgns_options['track_usage']['products'][ $this->plugin_basename ] ) ) {
					$bstwbsftwppdtplgns_options['track_usage']['products'][ $this->plugin_basename ] = true;
					$track_usage = true;
				}
			} else {
				if ( ! empty( $bstwbsftwppdtplgns_options['track_usage']['products'][ $this->plugin_basename ] ) ) {
					unset( $bstwbsftwppdtplgns_options['track_usage']['products'][ $this->plugin_basename ] ); false;
					$track_usage = false;
				}
			}
			if ( isset( $track_usage ) ) {
				$usage_id = ! empty( $bstwbsftwppdtplgns_options['track_usage']['usage_id'] ) ? $bstwbsftwppdtplgns_options['track_usage']['usage_id'] : false;
				/* send data */
				$options = array(
					'timeout' => 3,
					'body' => array(
						'url' 			=> get_bloginfo( 'url' ),
						'wp_version' 	=> $wp_version,
						'is_active'		=> $track_usage,
						'product'		=> $this->plugin_basename,
						'version'		=> $this->plugins_info['Version'],
						'usage_id'		=> $usage_id,
					),
					'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
				);
				$raw_response = wp_remote_post( 'http://bestwebsoft.com/wp-content/plugins/products-statistics/track-usage/', $options );

				if ( ! is_wp_error( $raw_response ) && 200 == wp_remote_retrieve_response_code( $raw_response ) ) {
					$response = maybe_unserialize( wp_remote_retrieve_body( $raw_response ) );

					if ( is_array( $response ) &&
						! empty( $response['usage_id'] ) &&
						$response['usage_id'] != $usage_id ) {
						$bstwbsftwppdtplgns_options['track_usage']['usage_id'] = $response['usage_id'];
					}
				}

				if ( $this->is_multisite )
					update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
				else
					update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
			}

			return compact( 'notice' );
		}				

		/**
		 *
		 */
		public function tab_license() {
			global $wp_version, $bstwbsftwppdtplgns_options; ?>
			<h3 class="bws_tab_label"><?php _e( 'License Key', 'bestwebsoft' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<?php if ( ! empty( $this->pro_page ) ) {
				$bws_license_key = ( isset( $_POST['bws_license_key'] ) ) ? stripslashes( esc_html( trim( $_POST['bws_license_key'] ) ) ) : "";

				if ( $this->pro_plugin_is_activated ) { 
					deactivate_plugins( $this->plugin_basename ); ?>
					<script type="text/javascript">
						(function($) {
							var i = 7;
							function bws_set_timeout() {
								i--;
								if ( 0 == i ) {
									window.location.href = '<?php echo $this->pro_page; ?>';
								} else {
									$( '#bws_timeout_counter' ).text( i );
									window.setTimeout( bws_set_timeout, 1000 );
								}
							}
							window.setTimeout( bws_set_timeout, 1000 );
						})(jQuery);
					</script>
					<p><strong><?php _e( 'Congratulations! Pro license is activated successfully.', 'bestwebsoft' ); ?></strong></p>
					<p><?php printf( __( 'You will be automatically redirected to the %s in %s seconds.', 'bestwebsoft' ), '<a href="' . $this->pro_page . '">' . __( 'Settings page', 'bestwebsoft' ) . '</a>', '<span id="bws_timeout_counter">7</span>' ); ?></p>
				<?php } else { 			
					$attr = '';
					if ( isset( $bstwbsftwppdtplgns_options['go_pro'][ $this->bws_license_plugin ]['count'] ) &&
						'5' < $bstwbsftwppdtplgns_options['go_pro'][ $this->bws_license_plugin ]['count'] &&
						$bstwbsftwppdtplgns_options['go_pro'][ $this->bws_license_plugin ]['time'] > ( time() - ( 24 * 60 * 60 ) ) )
						$attr = 'disabled="disabled"'; ?>		
					<table class="form-table">			
						<tr>
							<th scope="row"><?php _e( 'License Key', 'bestwebsoft' ); ?></th>
							<td>
								<input <?php echo $attr; ?> type="text" name="bws_license_key" value="<?php echo $bws_license_key; ?>" />
								<input <?php echo $attr; ?> type="hidden" name="bws_license_plugin" value="<?php echo $this->bws_license_plugin; ?>" />
								<input <?php echo $attr; ?> type="submit" class="button button-secondary" name="bws_license_submit" value="<?php _e( 'Activate', 'bestwebsoft' ); ?>" />
								<div class="bws_info">
									<?php printf( __( 'Enter your license key to activate %s and get premium plugin features.', 'bestwebsoft' ), '<a href="' . $this->plugins_info['PluginURI'] . '?k=' . $this->link_key . '&pn=' . $this->link_pn . '&v=' . $this->plugins_info["Version"] . '&wp_v=' . $wp_version . '" target="_blank" title="' . $this->plugins_info["Name"] . ' Pro">' . $this->plugins_info["Name"] . ' Pro</a>' ); ?>
								</div>
								<?php if ( '' != $attr ) { ?>
									<p><?php _e( "Unfortunately, you have exceeded the number of available tries per day. Please, upload the plugin manually.", 'bestwebsoft' ); ?></p>
								<?php }
								if ( $this->trial_days !== false )
									echo '<p>' . __( 'or', 'bestwebsoft' ) . ' <a href="' . $this->plugins_info['PluginURI'] . 'trial/?k=' . $this->link_key . '&pn=' . $this->link_pn . '&v=' . $this->plugins_info["Version"] . '&wp_v=' . $wp_version . '" target="_blank">' . sprintf( __( 'Start Your Free %s-Day Trial Now', 'bestwebsoft' ), $this->trial_days ) . '</a></p>'; ?>
							</td>
						</tr>
					</table>
				<?php }
			} else {
				global $bstwbsftwppdtplgns_options;
				$license_key = ( isset( $bstwbsftwppdtplgns_options[ $this->plugin_basename ] ) ) ? $bstwbsftwppdtplgns_options[ $this->plugin_basename ] : ''; ?>
				<table class="form-table">			
					<tr>
						<th scope="row"><?php _e( 'License Key', 'bestwebsoft' ); ?></th>
						<td>
							<input type="text" maxlength="100" name="bws_license_key" value="<?php echo $license_key; ?>" />
							<input type="submit" class="button button-secondary" name="bws_license_submit" value="<?php _e( 'Check license key', 'bestwebsoft' ); ?>" />
							<div class="bws_info">
								<?php _e( 'If necessary, you can check if the license key is correct or reenter it in the field below.', 'bestwebsoft' ); ?>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Manage License Settings', 'bestwebsoft' ); ?></th>
						<td>
							<a class="button button-secondary" href="https://bestwebsoft.com/client-area" target="_blank"><?php _e( 'Login to Client Area', 'bestwebsoft' ); ?></a>
							<div class="bws_info">
								<?php _e( 'Manage active licenses, download BWS products, and view your payment history using BestWebSoft Client Area.', 'bestwebsoft' ); ?>
							</div>
						</td>
					</tr>
				</table>
			<?php }
		}

		/**
		 * Save plugin options to the database
		 * @access private
		 * @param  ab
		 * @return array    The action results
		 */
		private function save_options_license_key() {
			global $wp_version, $bstwbsftwppdtplgns_options;

			$bws_license_key = ( isset( $_POST['bws_license_key'] ) ) ? stripslashes( esc_html( trim( $_POST['bws_license_key'] ) ) ) : '';
			
			if ( '' != $bws_license_key ) {
				if ( strlen( $bws_license_key ) != 18 ) {
					$error = __( 'Wrong license key', 'bestwebsoft' );
				} else {

					/* CHECK license key */
					if ( $this->is_pro ) {
						delete_transient( 'bws_plugins_update' );
						if ( ! $this->all_plugins ) {
							if ( ! function_exists( 'get_plugins' ) )
								require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
							$this->all_plugins = get_plugins();
						}
						$current = get_site_transient( 'update_plugins' );

						if ( ! empty( $this->all_plugins ) && isset( $current ) && is_array( $current->response ) ) {
							$to_send = array();
							$to_send["plugins"][ $this->plugin_basename ] = $this->all_plugins[ $this->plugin_basename ];
							$to_send["plugins"][ $this->plugin_basename ]["bws_license_key"] = $bws_license_key;
							$to_send["plugins"][ $this->plugin_basename ]["bws_illegal_client"] = true;
							$options = array(
								'timeout' => ( ( defined('DOING_CRON') && DOING_CRON ) ? 30 : 3 ),
								'body' => array( 'plugins' => serialize( $to_send ) ),
								'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
							);
							$raw_response = wp_remote_post( 'http://bestwebsoft.com/wp-content/plugins/paid-products/plugins/update-check/1.0/', $options );
							
							if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) ) {
								$error = __( 'Something went wrong. Please try again later. If the error appears again, please contact us', 'bestwebsoft' ) . ': <a href=https://support.bestwebsoft.com>BestWebSoft</a>. ' . __( 'We are sorry for inconvenience.', 'bestwebsoft' );
							} else {
								$response = maybe_unserialize( wp_remote_retrieve_body( $raw_response ) );
								if ( is_array( $response ) && !empty( $response ) ) {
									foreach ( $response as $single_response ) {
										if ( "wrong_license_key" == $single_response->package ) {
											$error = __( 'Wrong license key.', 'bestwebsoft' );
										} else if ( "wrong_domain" == $single_response->package ) {
											$error = __( 'This license key is bound to another site.', 'bestwebsoft' );
										} else if ( "time_out" == $single_response->package ) {
											$message = __( 'This license key is valid, but Your license has expired. If you want to update our plugin in future, you should extend the license.', 'bestwebsoft' );
										} elseif ( "you_are_banned" == $single_response->package ) {
											$error = __( "Unfortunately, you have exceeded the number of available tries.", 'bestwebsoft' );
										} elseif ( "duplicate_domen_for_trial" == $single_response->package ) {
											$error = __( "Unfortunately, the Pro Trial licence was already installed to this domain. The Pro Trial license can be installed only once.", 'bestwebsoft' );
										}										
										if ( empty( $error ) ) {
											if ( empty( $message ) ) {
												if ( isset( $single_response->trial ) )
													$message = __( 'The Pro Trial license key is valid.', 'bestwebsoft' );
												else
													$message = __( 'The license key is valid.', 'bestwebsoft' );

												if ( ! empty( $single_response->time_out ) )
													$message .= ' ' . __( 'Your license will expire on', 'bestwebsoft' ) . ' ' . $single_response->time_out . '.';

												if ( isset( $single_response->trial ) && $this->is_trial )
													$message .= ' ' . sprintf( __( 'In order to continue using the plugin it is necessary to buy a %s license.', 'bestwebsoft' ), '<a href="' . $this->plugins_info['PluginURI'] . '?k=' . $this->link_key . '&pn=' . $this->link_pn . '&v=' . $this->plugins_info["Version"] . '&wp_v=' . $wp_version . '" target="_blank" title="' . $this->plugins_info["Name"] . '">Pro</a>' );
											}

											if ( isset( $single_response->trial ) ) {
												$bstwbsftwppdtplgns_options['trial'][ $this->plugin_basename ] = 1;
											} else {
												unset( $bstwbsftwppdtplgns_options['trial'][ $this->plugin_basename ] );
											}

											if ( isset( $single_response->nonprofit ) ) {
												$bstwbsftwppdtplgns_options['nonprofit'][ $this->plugin_basename ] = 1;
											} else {
												unset( $bstwbsftwppdtplgns_options['nonprofit'][ $this->plugin_basename ] );
											}
									
											if ( ! isset( $bstwbsftwppdtplgns_options[ $this->plugin_basename ] ) || $bstwbsftwppdtplgns_options[ $this->plugin_basename ] != $bws_license_key ) {
												$bstwbsftwppdtplgns_options[ $this->plugin_basename ] = $bws_license_key;

												$file = @fopen( dirname( dirname( __FILE__ ) ) . "/license_key.txt" , "w+" );
												if ( $file ) {
													@fwrite( $file, $bws_license_key );
													@fclose( $file );
												}
												$update_option = true;
											}

											if ( ! isset( $bstwbsftwppdtplgns_options['time_out'][ $this->plugin_basename ] ) || $bstwbsftwppdtplgns_options['time_out'][ $this->plugin_basename ] != $single_response->time_out ) {
												$bstwbsftwppdtplgns_options['time_out'][ $this->plugin_basename ] = $single_response->time_out;
												$update_option = true;
											}

											if ( isset( $update_option ) ) {
												if ( $this->is_multisite )
													update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
												else
													update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
											}
										}
									}
								} else {
									$error = __( 'Something went wrong. Please try again later. If the error appears again, please contact us', 'bestwebsoft' ) . ' <a href=https://support.bestwebsoft.com>BestWebSoft</a>. ' . __( 'We are sorry for inconvenience.', 'bestwebsoft' );
								}
							}
						}
					/* Go Pro */
					} else {

						$bws_license_plugin = stripslashes( esc_html( $_POST['bws_license_plugin'] ) );
						if ( isset( $bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['count'] ) && $bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['time'] > ( time() - (24 * 60 * 60) ) ) {
							$bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['count'] = $bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['count'] + 1;
						} else {
							$bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['count'] = 1;
							$bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['time'] = time();
						}

						/* download Pro */
						if ( ! $this->all_plugins ) {
							if ( ! function_exists( 'get_plugins' ) )
								require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
							$this->all_plugins = get_plugins();
						}

						if ( ! array_key_exists( $bws_license_plugin, $this->all_plugins ) ) {
							$current = get_site_transient( 'update_plugins' );
							if ( ! empty( $current ) && is_array( $current->response ) ) {
								$to_send = array();
								$to_send["plugins"][ $bws_license_plugin ] = array();
								$to_send["plugins"][ $bws_license_plugin ]["bws_license_key"] = $bws_license_key;
								$to_send["plugins"][ $bws_license_plugin ]["bws_illegal_client"] = true;
								$options = array(
									'timeout' => ( ( defined( 'DOING_CRON' ) && DOING_CRON ) ? 30 : 3 ),
									'body' => array( 'plugins' => serialize( $to_send ) ),
									'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ) );
								$raw_response = wp_remote_post( 'http://bestwebsoft.com/wp-content/plugins/paid-products/plugins/update-check/1.0/', $options );

								if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) ) {
									$error = __( "Something went wrong. Please try again later. If the error appears again, please contact us", 'bestwebsoft' ) . ': <a href="https://support.bestwebsoft.com">BestWebSoft</a>. ' . __( "We are sorry for inconvenience.", 'bestwebsoft' );
								} else {
									$response = maybe_unserialize( wp_remote_retrieve_body( $raw_response ) );
									if ( is_array( $response ) && ! empty( $response ) ) {
										foreach ( $response as $single_response ) {
											if ( "wrong_license_key" == $single_response->package ) {
												$error = __( "Wrong license key.", 'bestwebsoft' );
											} elseif ( "wrong_domain" == $single_response->package ) {
												$error = __( "This license key is bound to another site.", 'bestwebsoft' );
											} elseif ( "you_are_banned" == $single_response->package ) {
												$error = __( "Unfortunately, you have exceeded the number of available tries per day. Please, upload the plugin manually.", 'bestwebsoft' );
											} elseif ( "time_out" == $single_response->package ) {
												$error = sprintf( __( "Unfortunately, Your license has expired. To continue getting top-priority support and plugin updates, you should extend it in your %s", 'bestwebsoft' ), ' <a href="https://bestwebsoft.com/client-area">Client Area</a>' );
											} elseif ( "duplicate_domen_for_trial" == $single_response->package ) {
												$error = __( "Unfortunately, the Pro licence was already installed to this domain. The Pro Trial license can be installed only once.", 'bestwebsoft' );
											}
										}
										if ( empty( $error ) ) {
											$bstwbsftwppdtplgns_options[ $bws_license_plugin ] = $bws_license_key;

											$url = 'http://bestwebsoft.com/wp-content/plugins/paid-products/plugins/downloads/?bws_first_download=' . $bws_license_plugin . '&bws_license_key=' . $bws_license_key . '&download_from=5';
											
											if ( ! $this->upload_dir )
												$this->upload_dir = wp_upload_dir();

											$zip_name = explode( '/', $bws_license_plugin );

											if ( !function_exists( 'curl_init' ) ) {
												$received_content = file_get_contents( $url );
											} else {
												$ch = curl_init();
												curl_setopt( $ch, CURLOPT_URL, $url );
												curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
												$received_content = curl_exec( $ch );
												curl_close( $ch );
											}

											if ( ! $received_content ) {
												$error = __( "Failed to download the zip archive. Please, upload the plugin manually", 'bestwebsoft' );
											} else {
												if ( is_writable( $this->upload_dir["path"] ) ) {
													$file_put_contents = $this->upload_dir["path"] . "/" . $zip_name[0] . ".zip";
													if ( file_put_contents( $file_put_contents, $received_content ) ) {
														@chmod( $file_put_contents, octdec( 755 ) );
														if ( class_exists( 'ZipArchive' ) ) {
															$zip = new ZipArchive();
															if ( $zip->open( $file_put_contents ) === TRUE ) {
																$zip->extractTo( WP_PLUGIN_DIR );
																$zip->close();
															} else {
																$error = __( "Failed to open the zip archive. Please, upload the plugin manually", 'bestwebsoft' );
															}
														} elseif ( class_exists( 'Phar' ) ) {
															$phar = new PharData( $file_put_contents );
															$phar->extractTo( WP_PLUGIN_DIR );
														} else {
															$error = __( "Your server does not support either ZipArchive or Phar. Please, upload the plugin manually", 'bestwebsoft' );
														}
														@unlink( $file_put_contents );
													} else {
														$error = __( "Failed to download the zip archive. Please, upload the plugin manually", 'bestwebsoft' );
													}
												} else {
													$error = __( "UploadDir is not writable. Please, upload the plugin manually", 'bestwebsoft' );
												}
											}

											/* activate Pro */
											if ( file_exists( WP_PLUGIN_DIR . '/' . $zip_name[0] ) ) {
												if ( $this->is_multisite && is_plugin_active_for_network( $this->plugin_basename ) ) {
													/* if multisite and free plugin is network activated */
													$active_plugins = get_site_option( 'active_sitewide_plugins' );
													$active_plugins[ $bws_license_plugin ] = time();
													update_site_option( 'active_sitewide_plugins', $active_plugins );
												} else {
													/* activate on a single blog */
													$active_plugins = get_option( 'active_plugins' );
													array_push( $active_plugins, $bws_license_plugin );
													update_option( 'active_plugins', $active_plugins );
												}
												$this->pro_plugin_is_activated = true;
											} elseif ( empty( $error ) ) {
												$error = __( "Failed to download the zip archive. Please, upload the plugin manually", 'bestwebsoft' );
											}
										}
									} else {
										$error = __( "Something went wrong. Try again later or upload the plugin manually. We are sorry for inconvenience.", 'bestwebsoft' );
									}
								}
							}
						} else {
							$bstwbsftwppdtplgns_options[ $bws_license_plugin ] = $bws_license_key;
							/* activate Pro */
							if ( ! is_plugin_active( $bws_license_plugin ) ) {
								if ( $this->is_multisite && is_plugin_active_for_network( $this->plugin_basename ) ) {
									/* if multisite and free plugin is network activated */
									$network_wide = true;
								} else {
									/* activate on a single blog */
									$network_wide = false;
								}
								activate_plugin( $bws_license_plugin, NULL, $network_wide );
								$this->pro_plugin_is_activated = true;
							}
						}
						/* add 'track_usage' for Pro version */
						if ( ! empty( $bstwbsftwppdtplgns_options['track_usage'][ $this->plugin_basename ] ) &&
							empty( $bstwbsftwppdtplgns_options['track_usage'][ $bws_license_plugin ] ) ) {
							$bstwbsftwppdtplgns_options['track_usage'][ $bws_license_plugin ] = $bstwbsftwppdtplgns_options['track_usage'][ $this->plugin_basename ];
						}

						if ( $this->is_multisite )
							update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
						else
							update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );

						if ( $this->pro_plugin_is_activated )
							delete_transient( 'bws_plugins_update' );
					}
				}
			} else {
				$error = __( "Please, enter Your license key", 'bestwebsoft' );
			}
			return compact( 'error', 'message' );
		}

		/**
		 * Display help phrase
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function help_phrase() {
			echo '<div class="bws_tab_description">' . __( 'Need Help?', 'bestwebsoft' ) . ' ';
			if ( '' != $this->doc_link )
				echo '<a href="' . $this->doc_link . '" target="_blank">' . __( 'Read the Instruction', 'bestwebsoft' );
			else
				echo '<a href="https://support.bestwebsoft.com/hc/en-us/" target="_blank">' . __( 'Visit Help Center', 'bestwebsoft' );
			if ( '' != $this->doc_video_link )
				echo '</a>' . ' ' . __( 'or', 'bestwebsoft' ) . ' ' . '<a href="' . $this->doc_video_link . '" target="_blank">' . __( 'Watch the Video', 'bestwebsoft' );
			echo '</a></div>';
		}

		public function bws_pro_block_links() { 
			global $wp_version; ?>
			<div class="bws_pro_version_tooltip">							
				<a class="bws_button" href="<?php echo $this->plugins_info['PluginURI']; ?>?k=<?php echo $this->link_key; ?>&amp;pn=<?php echo $this->link_pn; ?>&amp;v=<?php echo $this->plugins_info["Version"]; ?>&amp;wp_v=<?php echo $wp_version; ?>" target="_blank" title="<?php echo $this->plugins_info["Name"]; ?>"><?php _e( 'Upgrade to Pro', 'bestwebsoft' ); ?></a>
				<?php if ( $this->trial_days !== false ) { ?>
					<span class="bws_trial_info">
						<?php _e( 'or', 'bestwebsoft' ); ?> 
						<a href="<?php echo $this->plugins_info['PluginURI']; ?>?k=<?php echo $this->link_key; ?>&amp;pn=<?php echo $this->link_pn; ?>&amp;v=<?php echo $this->plugins_info["Version"]; ?>&amp;wp_v=<?php echo $wp_version; ?>" target="_blank" title="<?php echo $this->plugins_info["Name"]; ?>"><?php _e( 'Start Your Free Trial', 'bestwebsoft' ); ?></a>
					</span>
				<?php } ?>
				<div class="clear"></div>
			</div>
		<?php }

		/**
		 * Restore plugin options to defaults
		 * @access public
		 * @param  void
		 * @return void
		 */
		public function restore_options() {
			/**
			 * filter - Change default_options array OR process custom functions
			 */
			$this->options = apply_filters( __CLASS__ . '_additional_restore_options', $this->default_options );
			if ( $this->is_network_options ) {
				$this->options['network_apply'] = 'default';
				$this->options['network_view'] = '1';
				$this->options['network_change'] = '1';
				update_site_option( $this->prefix . '_options', $this->options );
			} else {
				update_option( $this->prefix . '_options', $this->options );
			}
		}
	}
}