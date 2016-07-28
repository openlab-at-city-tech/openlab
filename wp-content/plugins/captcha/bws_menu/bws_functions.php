<?php
/*
* General functions for BestWebSoft plugins
*/

/* Internationalization, first(!) */
load_plugin_textdomain( 'bestwebsoft', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

/**
* Function add BWS Plugins page - for old plugin version
* @deprecated 1.7.9
*/
if ( ! function_exists ( 'bws_add_general_menu' ) ) {
	function bws_add_general_menu() {
		bws_general_menu();
	}
}

/**
* Function add BWS Plugins page
* @return void
*/
if ( ! function_exists ( 'bws_general_menu' ) ) {
	function bws_general_menu() {
		global $menu, $bws_general_menu_exist;

		if ( ! $bws_general_menu_exist ) {
			/* we check also menu exist in global array as in old plugins $bws_general_menu_exist variable not exist */
			foreach ( $menu as $value_menu ) {
				if ( 'bws_panel' == $value_menu[2] ) {
					$bws_general_menu_exist = true;
					return;
				}
			}

			add_menu_page( 'BWS Panel', 'BWS Panel', 'manage_options', 'bws_panel', 'bws_add_menu_render', plugins_url( 'images/bestwebsoft-logo-white.svg', __FILE__ ), '1001' );

			add_submenu_page( 'bws_panel', __( 'Plugins', 'bestwebsoft' ), __( 'Plugins', 'bestwebsoft' ), 'manage_options', 'bws_panel', 'bws_add_menu_render' );
			add_submenu_page( 'bws_panel', __( 'Themes', 'bestwebsoft' ), __( 'Themes', 'bestwebsoft' ), 'manage_options', 'bws_themes', 'bws_add_menu_render' );
			add_submenu_page( 'bws_panel', __( 'System Status', 'bestwebsoft' ), __( 'System Status', 'bestwebsoft' ), 'manage_options', 'bws_system_status', 'bws_add_menu_render' );

			$bws_general_menu_exist = true;
		}
	}
}

/**
* Function check if plugin is compatible with current WP version - for old plugin version
* @deprecated 1.7.4
*/
if ( ! function_exists ( 'bws_wp_version_check' ) ) {
	function bws_wp_version_check( $plugin_basename, $plugin_info, $require_wp ) {
		bws_wp_min_version_check( $plugin_basename, $plugin_info, '3.8' , $require_wp );
	}
}

/**
* Function check if plugin is compatible with current WP version
* @return void
*/
if ( ! function_exists ( 'bws_wp_min_version_check' ) ) {
	function bws_wp_min_version_check( $plugin_basename, $plugin_info, $require_wp, $min_wp = false ) {
		global $wp_version, $bws_versions_notice_array;
		if ( false == $min_wp )
			$min_wp = $require_wp;
		if ( version_compare( $wp_version, $min_wp, "<" ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if ( is_plugin_active( $plugin_basename ) ) {
				deactivate_plugins( $plugin_basename );
				$admin_url = ( function_exists( 'get_admin_url' ) ) ? get_admin_url( null, 'plugins.php' ) : esc_url( '/wp-admin/plugins.php' );
				wp_die( 
					sprintf(
						"<strong>%s</strong> %s <strong>WordPress %s</strong> %s <br /><br />%s <a href='%s'>%s</a>.",
						$plugin_info['Name'],
						__( 'requires', 'bestwebsoft' ),
						$require_wp,
						__( 'or higher, that is why it has been deactivated! Please upgrade WordPress and try again.', 'bestwebsoft' ),
						__( 'Back to the WordPress', 'bestwebsoft' ),
						$admin_url,
						__( 'Plugins page', 'bestwebsoft' )
					)
				);
			}
		} elseif ( version_compare( $wp_version, $require_wp, "<" ) ) {
			$bws_versions_notice_array[] = array( 'name' => $plugin_info['Name'], 'version' => $require_wp );
		}
	}
}

if ( ! function_exists( 'bws_admin_notices' ) ) {
	function bws_admin_notices() {
		global $bws_versions_notice_array, $bws_plugin_banner_to_settings;

		/*  versions notice */
		if ( ! empty( $bws_versions_notice_array ) ) {
			foreach ( $bws_versions_notice_array as $key => $value ) { ?>
				<div class="update-nag"><?php
					echo sprintf(
							"<strong>%s</strong> %s <strong>WordPress %s</strong> %s",
							$value['name'],
							__( 'requires', 'bestwebsoft' ),
							$value['version'],
							__( 'or higher! We do not guarantee that our plugin will work correctly. Please upgrade to WordPress latest version.', 'bestwebsoft' )
						);
				?></div>
			<?php }
		}

		/*  banner_to_settings notice */
		if ( ! empty( $bws_plugin_banner_to_settings ) ) { 
			if ( 1 == count( $bws_plugin_banner_to_settings ) ) { ?>
				<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
					<div class="bws_banner_on_plugin_page bws_banner_to_settings">
						<div class="icon">
							<img title="" src="<?php echo esc_attr( $bws_plugin_banner_to_settings[0]['banner_url'] ); ?>" alt="" />
						</div>						
						<div class="text">
							<strong><?php _e( 'Thank you for installing', 'bestwebsoft' ); ?> <?php echo $bws_plugin_banner_to_settings[0]['plugin_info']['Name']; ?> plugin!</strong><br />
							<?php _e( "Let's get started", 'bestwebsoft' ); ?>: 
							<a href="<?php echo $bws_plugin_banner_to_settings[0]['settings_url']; ?>"><?php _e( 'Settings', 'bestwebsoft' ); ?></a> 
							<?php if ( false != $bws_plugin_banner_to_settings[0]['post_type_url'] ) { ?>
								<?php _e( 'or', 'bestwebsoft' ); ?> 
								<a href="<?php echo $bws_plugin_banner_to_settings[0]['post_type_url']; ?>"><?php _e( 'Add New', 'bestwebsoft' ); ?></a>
							<?php } ?>
						</div>
						<form action="" method="post">
							<button class="notice-dismiss bws_hide_settings_notice" title="<?php _e( 'Close notice', 'bestwebsoft' ); ?>"></button>
							<input type="hidden" name="bws_hide_settings_notice_<?php echo $bws_plugin_banner_to_settings[0]['plugin_options_name']; ?>" value="hide" />
							<?php wp_nonce_field( plugin_basename( __FILE__ ), 'bws_settings_nonce_name' ); ?>
						</form>
					</div>
				</div>			
			<?php } else { ?>
				<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
					<div class="bws_banner_on_plugin_page bws_banner_to_settings_joint">	
						<form action="" method="post">
							<button class="notice-dismiss bws_hide_settings_notice" title="<?php _e( 'Close notice', 'bestwebsoft' ); ?>"></button>
							<div class="bws-text">
								<div class="icon">
									<span class="dashicons dashicons-admin-plugins"></span>
								</div>															
								<strong><?php _e( 'Thank you for installing plugins by BestWebSoft!', 'bestwebsoft' ); ?></strong>
								<div class="hide-if-no-js bws-more-links">
									<a href="#" class="bws-more"><?php _e( 'More Details', 'bestwebsoft' ); ?></a>		
									<a href="#" class="bws-less hidden"><?php _e( 'Less Details', 'bestwebsoft' ); ?></a>	
								</div>			
								<?php wp_nonce_field( plugin_basename( __FILE__ ), 'bws_settings_nonce_name' ); ?>								
								<div class="clear"></div>
							</div>
							<div class="bws-details hide-if-js">
								<?php foreach ( $bws_plugin_banner_to_settings as $value ) { ?>		
									<div>		
										<strong><?php echo str_replace( ' by BestWebSoft', '', $value['plugin_info']['Name'] ); ?></strong>&ensp;<a href="<?php echo $value['settings_url']; ?>"><?php _e( 'Settings', 'bestwebsoft' ); ?></a> 
										<?php if ( false != $value['post_type_url'] ) { ?>
											&ensp;|&ensp;<a target="_blank" href="<?php echo $value['post_type_url']; ?>"><?php _e( 'Add New', 'bestwebsoft' ); ?></a>
										<?php } ?>
										<input type="hidden" name="bws_hide_settings_notice_<?php echo $value['plugin_options_name']; ?>" value="hide" />
									</div>		
								<?php } ?>		
							</div>
						</div>
					</form>
				</div>
			<?php }
		}
	}
}

if ( ! function_exists( 'bws_plugin_banner' ) ) {
	function bws_plugin_banner( $plugin_info, $this_banner_prefix, $link_slug, $link_key, $link_pn, $banner_url_or_slug ) {
		global $wp_version, $bstwbsftwppdtplgns_cookie_add, $bstwbsftwppdtplgns_banner_array;
		
		if ( empty( $bstwbsftwppdtplgns_banner_array ) ) {
			if ( ! function_exists( 'bws_get_banner_array' ) )
				require_once( dirname( __FILE__ ) . '/bws_menu.php' );
			bws_get_banner_array();
		}

		if ( false == strrpos( $banner_url_or_slug, '/' ) ) {
			$banner_url_or_slug = '//ps.w.org/' . $banner_url_or_slug . '/assets/icon-128x128.png';
		}

		if ( ! function_exists( 'is_plugin_active' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$all_plugins = get_plugins();

		$this_banner = $this_banner_prefix . '_hide_banner_on_plugin_page';
		foreach ( $bstwbsftwppdtplgns_banner_array as $key => $value ) {
			if ( $this_banner == $value[0] ) {
				if ( ! isset( $bstwbsftwppdtplgns_cookie_add ) ) {
					echo '<script type="text/javascript" src="' . plugins_url( 'js/c_o_o_k_i_e.js', __FILE__ ) . '"></script>';
					$bstwbsftwppdtplgns_cookie_add = true;
				} ?>
				<script type="text/javascript">
					(function($) {
						$(document).ready( function() {
							var hide_message = $.cookie( '<?php echo $this_banner_prefix; ?>_hide_banner_on_plugin_page' );
							if ( hide_message == "true" ) {
								$( ".<?php echo $this_banner_prefix; ?>_message" ).css( "display", "none" );
							} else {
								$( ".<?php echo $this_banner_prefix; ?>_message" ).css( "display", "block" );
							};
							$( ".<?php echo $this_banner_prefix; ?>_close_icon" ).click( function() {
								$( ".<?php echo $this_banner_prefix; ?>_message" ).css( "display", "none" );
								$.cookie( "<?php echo $this_banner_prefix; ?>_hide_banner_on_plugin_page", "true", { expires: 32 } );
							});
						});
					})(jQuery);
				</script>
				<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
					<div class="<?php echo $this_banner_prefix; ?>_message bws_banner_on_plugin_page bws_go_pro_banner" style="display: none;">
						<button class="<?php echo $this_banner_prefix; ?>_close_icon close_icon notice-dismiss bws_hide_settings_notice" title="<?php _e( 'Close notice', 'bestwebsoft' ); ?>"></button>
						<div class="icon">
							<img title="" src="<?php echo esc_attr( $banner_url_or_slug ); ?>" alt="" />
						</div>						
						<div class="text"><?php
							_e( 'It’s time to upgrade your', 'bestwebsoft' ); ?> <strong><?php echo $plugin_info['Name']; ?> plugin</strong> <?php _e( 'to', 'bestwebsoft' ); ?> <strong>Pro</strong> <?php _e( 'version!', 'bestwebsoft' ); ?><br />
							<span><?php _e( 'Extend standard plugin functionality with new great options.', 'bestwebsoft' ); ?></span>
						</div>
						<div class="button_div">
							<a class="button" target="_blank" href="http://bestwebsoft.com/products/<?php echo $link_slug; ?>/?k=<?php echo $link_key; ?>&amp;pn=<?php echo $link_pn; ?>&amp;v=<?php echo $plugin_info["Version"]; ?>&amp;wp_v=<?php echo $wp_version; ?>"><?php _e( 'Learn More', 'bestwebsoft' ); ?></a>
						</div>
					</div>
				</div>
				<?php break;
			}
			if ( isset( $all_plugins[ $value[1] ] ) && $all_plugins[ $value[1] ]["Version"] >= $value[2] && is_plugin_active( $value[1] ) && ! isset( $_COOKIE[ $value[0] ] ) ) {
				break;
			}
		}
	}
}

if ( ! function_exists( 'bws_plugin_reviews_block' ) ) {
	function bws_plugin_reviews_block( $plugin_name, $plugin_slug ) { ?>
		<div class="bws-plugin-reviews">
			<div class="bws-plugin-reviews-rate">
				<?php _e( 'If you enjoy our plugin, please give it 5 stars on WordPress', 'bestwebsoft' ); ?>:
				<a href="http://wordpress.org/support/view/plugin-reviews/<?php echo $plugin_slug; ?>?filter=5" target="_blank" title="<?php echo $plugin_name; ?> reviews"><?php _e( 'Rate the plugin', 'bestwebsoft' ); ?></a>
			</div>
			<div class="bws-plugin-reviews-support">
				<?php _e( 'If there is something wrong about it, please contact us', 'bestwebsoft' ); ?>:
				<a href="http://support.bestwebsoft.com">http://support.bestwebsoft.com</a>
			</div>
			<div class="bws-plugin-reviews-donate">
				<?php _e( 'Donations play an important role in supporting great projects', 'bestwebsoft' ); ?>:
				<a href="https://www.2checkout.com/checkout/purchase?sid=1430388&quantity=10&product_id=13">Donate</a>
			</div>
		</div>
	<?php }
}

if ( ! function_exists( 'bws_go_pro_tab_check' ) ) {
	function bws_go_pro_tab_check( $plugin_basename, $plugin_options_name = false, $is_network_option = false ) {
		global $wp_version, $bstwbsftwppdtplgns_options;
		$result = array();

		$bws_license_key = ( isset( $_POST['bws_license_key'] ) ) ? stripslashes( esc_html( trim( $_POST['bws_license_key'] ) ) ) : "";

		if ( ! empty( $plugin_options_name ) && isset( $_POST['bws_hide_premium_options_submit'] ) && check_admin_referer( $plugin_basename, 'bws_license_nonce_name' ) ) {

			$plugin_options = ( $is_network_option ) ? get_site_option( $plugin_options_name ) : get_option( $plugin_options_name );

			if ( !empty( $plugin_options['hide_premium_options'] ) ) {

				$key = array_search( get_current_user_id(), $plugin_options['hide_premium_options'] );
				if ( false !== $key ) {
					unset( $plugin_options['hide_premium_options'][ $key ] );
				}
			
				if ( $is_network_option )
					update_site_option( $plugin_options_name, $plugin_options );
				else
					update_option( $plugin_options_name, $plugin_options );

				$result['message'] = __( 'Check premium options on the plugin settings page!', 'bestwebsoft' );
			}
		}

		if ( isset( $_POST['bws_license_submit'] ) && check_admin_referer( $plugin_basename, 'bws_license_nonce_name' ) ) {
			if ( '' != $bws_license_key ) { 
				if ( strlen( $bws_license_key ) != 18 ) {
					$result['error'] = __( "Wrong license key", 'bestwebsoft' );
				} else {
					$bws_license_plugin = stripslashes( esc_html( $_POST['bws_license_plugin'] ) );
					if ( isset( $bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['count'] ) && $bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['time'] > ( time() - (24 * 60 * 60) ) ) {
						$bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['count'] = $bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['count'] + 1;
					} else {
						$bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['count'] = 1;
						$bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['time'] = time();
					}

					/* download Pro */
					if ( ! function_exists( 'get_plugins' ) )
						require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

					$all_plugins = get_plugins();
					
					if ( ! array_key_exists( $bws_license_plugin, $all_plugins ) ) {
						$current = get_site_transient( 'update_plugins' );
						if ( is_array( $all_plugins ) && !empty( $all_plugins ) && isset( $current ) && is_array( $current->response ) ) {
							$to_send = array();
							$to_send["plugins"][ $bws_license_plugin ] = array();
							$to_send["plugins"][ $bws_license_plugin ]["bws_license_key"] = $bws_license_key;
							$to_send["plugins"][ $bws_license_plugin ]["bws_illegal_client"] = true;
							$options = array(
								'timeout' => ( ( defined('DOING_CRON') && DOING_CRON ) ? 30 : 3 ),
								'body' => array( 'plugins' => serialize( $to_send ) ),
								'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ) );
							$raw_response = wp_remote_post( 'http://bestwebsoft.com/wp-content/plugins/paid-products/plugins/update-check/1.0/', $options );

							if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) ) {
								$result['error'] = __( "Something went wrong. Please try again later. If the error appears again, please contact us", 'bestwebsoft' ) . ' <a href="http://support.bestwebsoft.com">BestWebSoft</a>. ' . __( "We are sorry for inconvenience.", 'bestwebsoft' );
							} else {
								$response = maybe_unserialize( wp_remote_retrieve_body( $raw_response ) );
								if ( is_array( $response ) && !empty( $response ) ) {
									foreach ( $response as $key => $value ) {
										if ( "wrong_license_key" == $value->package ) {
											$result['error'] = __( "Wrong license key", 'bestwebsoft' ); 
										} elseif ( "wrong_domain" == $value->package ) {
											$result['error'] = __( "This license key is bind to another site", 'bestwebsoft' );
										} elseif ( "you_are_banned" == $value->package ) {
											$result['error'] = __( "Unfortunately, you have exceeded the number of available tries per day. Please, upload the plugin manually.", 'bestwebsoft' );
										} elseif ( "time_out" == $value->package ) {
											$result['error'] = __( "Unfortunately, Your license has expired. To continue getting top-priority support and plugin updates you should extend it in your", 'bestwebsoft' ) . ' <a href="http://bestwebsoft.com/wp-admin/admin.php?page=client-area">Client area</a>';
										} elseif ( "duplicate_domen_for_trial" == $value->package ) {
											$result['error'] = __( "Unfortunately, the Pro licence was already installed to this domain. The Pro Trial license can be installed only once.", 'bestwebsoft' );
										}
									}
									if ( empty( $result['error'] ) ) {
										$bstwbsftwppdtplgns_options[ $bws_license_plugin ] = $bws_license_key;

										$url = 'http://bestwebsoft.com/wp-content/plugins/paid-products/plugins/downloads/?bws_first_download=' . $bws_license_plugin . '&bws_license_key=' . $bws_license_key . '&download_from=5';
										$uploadDir = wp_upload_dir();
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
											$result['error'] = __( "Failed to download the zip archive. Please, upload the plugin manually", 'bestwebsoft' );
										} else {
											if ( is_writable( $uploadDir["path"] ) ) {
												$file_put_contents = $uploadDir["path"] . "/" . $zip_name[0] . ".zip";
												if ( file_put_contents( $file_put_contents, $received_content ) ) {
													@chmod( $file_put_contents, octdec( 755 ) );
													if ( class_exists( 'ZipArchive' ) ) {
														$zip = new ZipArchive();
														if ( $zip->open( $file_put_contents ) === TRUE ) {
															$zip->extractTo( WP_PLUGIN_DIR );
															$zip->close();
														} else {
															$result['error'] = __( "Failed to open the zip archive. Please, upload the plugin manually", 'bestwebsoft' );
														}
													} elseif ( class_exists( 'Phar' ) ) {
														$phar = new PharData( $file_put_contents );
														$phar->extractTo( WP_PLUGIN_DIR );
													} else {
														$result['error'] = __( "Your server does not support either ZipArchive or Phar. Please, upload the plugin manually", 'bestwebsoft' );
													}
													@unlink( $file_put_contents );
												} else {
													$result['error'] = __( "Failed to download the zip archive. Please, upload the plugin manually", 'bestwebsoft' );
												}
											} else {
												$result['error'] = __( "UploadDir is not writable. Please, upload the plugin manually", 'bestwebsoft' );
											}
										}

										/* activate Pro */
										if ( file_exists( WP_PLUGIN_DIR . '/' . $zip_name[0] ) ) {
											if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
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
											$result['pro_plugin_is_activated'] = true;
										} elseif ( empty( $result['error'] ) ) {
											$result['error'] = __( "Failed to download the zip archive. Please, upload the plugin manually", 'bestwebsoft' );
										}
									}
								} else {
									$result['error'] = __( "Something went wrong. Try again later or upload the plugin manually. We are sorry for inconvenience.", 'bestwebsoft' ); 
								}
							}
						}
					} else {
						$bstwbsftwppdtplgns_options[ $bws_license_plugin ] = $bws_license_key;
						/* activate Pro */
						if ( ! is_plugin_active( $bws_license_plugin ) ) {
							if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
								/* if multisite and free plugin is network activated */
								$network_wide = true;
							} else {
								/* activate on a single blog */
								$network_wide = false;
							}
							activate_plugin( $bws_license_plugin, NULL, $network_wide );
							$result['pro_plugin_is_activated'] = true;
						}
					}
					if ( is_multisite() )
						update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
					else
						update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
				}
			} else {
				$result['error'] = __( "Please, enter Your license key", 'bestwebsoft' );
			}
		}
		return $result;
	}
}

/**
* Function display GO PRO tab - for old plugin version
* @deprecated 1.7.6
*/
if ( ! function_exists( 'bws_go_pro_tab' ) ) {
	function bws_go_pro_tab( $plugin_info, $plugin_basename, $page, $pro_page, $bws_license_plugin, $link_slug, $link_key, $link_pn, $pro_plugin_is_activated = false, $trial_days_number = false ) {
		bws_go_pro_tab_show( false, $plugin_info, $plugin_basename, $page, $pro_page, $bws_license_plugin, $link_slug, $link_key, $link_pn, $pro_plugin_is_activated, $trial_days_number );
	}
}

/**
* Function display GO PRO tab
*/
if ( ! function_exists( 'bws_go_pro_tab_show' ) ) {
	function bws_go_pro_tab_show( $bws_hide_premium_options_check, $plugin_info, $plugin_basename, $page, $pro_page, $bws_license_plugin, $link_slug, $link_key, $link_pn, $pro_plugin_is_activated = false, $trial_days_number = false ) {
		global $wp_version, $bstwbsftwppdtplgns_options;
		$bws_license_key = ( isset( $_POST['bws_license_key'] ) ) ? stripslashes( esc_html( trim( $_POST['bws_license_key'] ) ) ) : "";
		if ( $pro_plugin_is_activated ) { ?>
			<script type="text/javascript">
				window.setTimeout( function() {
					window.location.href = 'admin.php?page=<?php echo $pro_page; ?>';
				}, 5000 );
			</script>
			<p><?php _e( "Congratulations! Pro version of the plugin is successfully installed and activated.", 'bestwebsoft' ); ?></p>
			<p>
				<?php _e( "Please, go to", 'bestwebsoft' ); ?> <a href="admin.php?page=<?php echo $pro_page; ?>"><?php _e( 'the setting page', 'bestwebsoft' ); ?></a> 
				(<?php _e( "You will be redirected automatically in 5 seconds.", 'bestwebsoft' ); ?>)
			</p>
		<?php } else {
			if ( $bws_hide_premium_options_check ) { ?>
				<form method="post" action="">
					<p>
						<input type="hidden" name="bws_hide_premium_options_submit" value="submit" />
						<input type="submit" class="button" value="<?php _e( 'Show Pro features', 'bestwebsoft' ); ?>" />
						<?php wp_nonce_field( $plugin_basename, 'bws_license_nonce_name' ); ?>
					</p>
				</form>
			<?php } ?>
			<form method="post" action="">
				<p>
					<?php _e( 'Enter your license key to install and activate', 'bestwebsoft' ); ?> 
					<a href="http://bestwebsoft.com/products/<?php echo $link_slug; ?>/?k=<?php echo $link_key; ?>&amp;pn=<?php echo $link_pn; ?>&amp;v=<?php echo $plugin_info["Version"]; ?>&amp;wp_v=<?php echo $wp_version; ?>" target="_blank" title="<?php echo $plugin_info["Name"]; ?> Pro">Pro</a> 
					<?php _e( 'version of the plugin.', 'bestwebsoft' ); ?><br />
					<span class="bws_info">
						<?php _e( 'License key can be found in the', 'bestwebsoft' ); ?> 
						<a href="http://bestwebsoft.com/wp-login.php">Client Area</a> 
						<?php _e( '(your username is the email address specified during the purchase).', 'bestwebsoft' ); ?>
					</span>
				</p>
				<?php if ( $trial_days_number !== false )
					$trial_days_number = __( 'or', 'bestwebsoft' ) . ' <a href="http://bestwebsoft.com/products/' . $link_slug . '/trial/" target="_blank">' . sprintf( __( 'Start Your Free %s-Day Trial Now', 'bestwebsoft' ), $trial_days_number ) . '</a>';
				if ( isset( $bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['count'] ) &&
					'5' < $bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['count'] &&
					$bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['time'] > ( time() - ( 24 * 60 * 60 ) ) ) { ?>
					<p>
						<input disabled="disabled" type="text" name="bws_license_key" value="<?php echo $bws_license_key; ?>" />
						<input disabled="disabled" type="submit" class="button-primary" value="<?php _e( 'Activate', 'bestwebsoft' ); ?>" />
						<?php if ( $trial_days_number !== false ) echo $trial_days_number; ?>
					</p>
					<p><?php _e( "Unfortunately, you have exceeded the number of available tries per day. Please, upload the plugin manually.", 'bestwebsoft' ); ?></p>
				<?php } else { ?>
					<p>
						<input type="text" maxlength="100" name="bws_license_key" value="<?php echo $bws_license_key; ?>" />
						<input type="hidden" name="bws_license_plugin" value="<?php echo $bws_license_plugin; ?>" />
						<input type="hidden" name="bws_license_submit" value="submit" />
						<input type="submit" class="button-primary" value="<?php _e( 'Activate', 'bestwebsoft' ); ?>" />
						<?php if ( $trial_days_number !== false ) echo $trial_days_number;
						wp_nonce_field( $plugin_basename, 'bws_license_nonce_name' ); ?>
					</p>
				<?php } ?>
			</form>
		<?php }
	}
}

if ( ! function_exists( 'bws_go_pro_from_trial_tab' ) ) {
	function bws_go_pro_from_trial_tab( $plugin_info, $plugin_basename, $page, $link_slug, $link_key, $link_pn, $trial_license_is_set = true ) {
		global $wp_version, $bstwbsftwppdtplgns_options;
		$bws_license_key = ( isset( $_POST['bws_license_key'] ) ) ? stripslashes( esc_html( trim( $_POST['bws_license_key'] ) ) ) : "";
		if ( $trial_license_is_set ) { ?>
			<form method="post" action="">
				<p>
					<?php echo sprintf( __( 'In order to continue using the plugin it is necessary to buy a %s license.', 'bestwebsoft' ), '<a href="http://bestwebsoft.com/products/' . $link_slug . '/?k=' . $link_key . '&amp;pn=' . $link_pn . '&amp;v=' . $plugin_info["Version"] . '&amp;wp_v=' . $wp_version .'" target="_blank" title="' . $plugin_info["Name"] . '">Pro</a>' ) . ' ';
					_e( 'After that you can activate it by entering your license key.', 'bestwebsoft' ); ?><br />
					<span class="bws_info">
						<?php _e( 'License key can be found in the', 'bestwebsoft' ); ?> 
						<a href="http://bestwebsoft.com/wp-login.php">Client Area</a> 
						<?php _e( '(your username is the email address specified during the purchase).', 'bestwebsoft' ); ?>
					</span>
				</p>
				<?php if ( isset( $bstwbsftwppdtplgns_options['go_pro'][ $plugin_basename ]['count'] ) &&
					'5' < $bstwbsftwppdtplgns_options['go_pro'][ $plugin_basename ]['count'] &&
					$bstwbsftwppdtplgns_options['go_pro'][ $plugin_basename ]['time'] > ( time() - ( 24 * 60 * 60 ) ) ) { ?>
					<p>
						<input disabled="disabled" type="text" name="bws_license_key" value="" />
						<input disabled="disabled" type="submit" class="button-primary" value="<?php _e( 'Activate', 'bestwebsoft' ); ?>" />
					</p>
					<p><?php _e( "Unfortunately, you have exceeded the number of available tries per day.", 'bestwebsoft' ); ?></p>
				<?php } else { ?>
					<p>
						<input type="text" maxlength="100" name="bws_license_key" value="" />
						<input type="hidden" name="bws_license_plugin" value="<?php echo $plugin_basename; ?>" />
						<input type="hidden" name="bws_license_submit" value="submit" />
						<input type="submit" class="button-primary" value="<?php _e( 'Activate', 'bestwebsoft' ); ?>" />
						<?php wp_nonce_field( $plugin_basename, 'bws_license_nonce_name' ); ?>
					</p>
				<?php } ?>
			</form>
		<?php } else { ?>
			<script type="text/javascript">
				window.setTimeout( function() {
					window.location.href = 'admin.php?page=<?php echo $page; ?>';
				}, 5000 );
			</script>
			<p><?php _e( "Congratulations! The Pro license of the plugin is successfully activated.", 'bestwebsoft' ); ?></p>
			<p>
				<?php _e( "Please, go to", 'bestwebsoft' ); ?> <a href="admin.php?page=<?php echo $page; ?>"><?php _e( 'the setting page', 'bestwebsoft' ); ?></a> 
				(<?php _e( "You will be redirected automatically in 5 seconds.", 'bestwebsoft' ); ?>)
			</p>
		<?php }
	}
}

if ( ! function_exists( 'bws_check_pro_license' ) ) {
	function bws_check_pro_license( $plugin_basename, $trial_plugin = false ) {
		global $wp_version, $bstwbsftwppdtplgns_options;
		$result = array();

		if ( isset( $_POST['bws_license_submit'] ) && check_admin_referer( $plugin_basename, 'bws_license_nonce_name' ) ) {
			$license_key = isset( $_POST['bws_license_key'] ) ? stripslashes( esc_html( trim( $_POST['bws_license_key'] ) ) ) : '';
			
			if ( '' != $license_key ) {
				if ( ! function_exists( 'get_plugins' ) )
					require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				$plugins_all = get_plugins();
				$current = get_site_transient( 'update_plugins' );

				if ( is_array( $plugins_all ) && !empty( $plugins_all ) && isset( $current ) && is_array( $current->response ) ) {
					$to_send = array();
					$to_send["plugins"][ $plugin_basename ] = $plugins_all[ $plugin_basename ];
					$to_send["plugins"][ $plugin_basename ]["bws_license_key"] = $license_key;
					$to_send["plugins"][ $plugin_basename ]["bws_illegal_client"] = true;
					$options = array(
							'timeout' => ( ( defined('DOING_CRON') && DOING_CRON ) ? 30 : 3),
							'body' => array( 'plugins' => serialize( $to_send ) ),
							'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
						);
					$raw_response = wp_remote_post( 'http://bestwebsoft.com/wp-content/plugins/paid-products/plugins/update-check/1.0/', $options );
					if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) ) {
						$result['error'] = __( 'Something went wrong. Please try again later. If the error appears again, please contact us', 'bestwebsoft' ) . ' <a href=http://support.bestwebsoft.com>BestWebSoft</a>. ' . __( 'We are sorry for inconvenience.', 'bestwebsoft' );
					} else {
						$response = maybe_unserialize( wp_remote_retrieve_body( $raw_response ) );
						if ( is_array( $response ) && !empty( $response ) ) {
							foreach ( $response as $key => $value ) {
								if ( "wrong_license_key" == $value->package ) {
									$result['error'] = __( 'Wrong license key.', 'bestwebsoft' ); 
								} else if ( "wrong_domain" == $value->package ) {
									$result['error'] = __( 'This license key is bind to another site.', 'bestwebsoft' );
								} else if ( "time_out" == $value->package ) {
									$result['message'] = __( 'This license key is valid, but Your license has expired. If you want to update our plugin in future, you should extend the license.', 'bestwebsoft' );
								} elseif ( "you_are_banned" == $value->package ) {
									$result['error'] = __( "Unfortunately, you have exceeded the number of available tries.", 'bestwebsoft' );
								} elseif ( "duplicate_domen_for_trial" == $value->package ) {
									$result['error'] = __( "Unfortunately, the Pro Trial licence was already installed to this domain. The Pro Trial license can be installed only once.", 'bestwebsoft' );
								}
								if ( empty( $result['message'] ) && empty( $result['error'] ) ) {
									if ( isset( $value->trial ) )
										$result['message'] = __( 'The Pro Trial license key is valid.', 'bestwebsoft' );
									else
										$result['message'] = __( 'The license key is valid.', 'bestwebsoft' );

									if ( isset( $value->time_out ) && $value->time_out != '' )
										$result['message'] .= ' ' . __( 'Your license will expire on', 'bestwebsoft' ) . ' ' . $value->time_out . '.';

									if ( isset( $value->trial ) && $trial_plugin != false )
										$result['message'] .= ' ' . sprintf( __( 'In order to continue using the plugin it is necessary to buy a %s license.', 'bestwebsoft' ), '<a href="http://bestwebsoft.com/products/' . $trial_plugin['link_slug'] . '/?k=' . $trial_plugin['link_key'] . '&pn=' . $trial_plugin['link_pn'] . '&v=' . $trial_plugin['plugin_info']['Version'] . '&wp_v=' . $wp_version . '" target="_blank" title="' . $trial_plugin['plugin_info']['Name'] . '">Pro</a>' );

									if ( isset( $value->trial ) ) {
										$bstwbsftwppdtplgns_options['trial'][ $plugin_basename ] = 1;
									} else {
										unset( $bstwbsftwppdtplgns_options['trial'][ $plugin_basename ] );
									}
								}
								if ( empty( $result['error'] ) ) {
									if ( $bstwbsftwppdtplgns_options[ $plugin_basename ] != $license_key ) {
										$bstwbsftwppdtplgns_options[ $plugin_basename ] = $license_key;
										$bstwbsftwppdtplgns_options['time_out'][ $plugin_basename ] = $value->time_out;
										if ( is_multisite() )
											update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
										else
											update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
										$file = @fopen( dirname( dirname( __FILE__ ) ) . "/license_key.txt" , "w+" );
										if ( $file ) {
											@fwrite( $file, $license_key );
											@fclose( $file );
										}
									}
								}
							}
						} else {
							$result['error'] = __( 'Something went wrong. Please try again later. If the error appears again, please contact us', 'bestwebsoft' ) . ' <a href=http://support.bestwebsoft.com>BestWebSoft</a>. ' . __( 'We are sorry for inconvenience.', 'bestwebsoft' );
						}
					}
				}
			} else {
				$result['error'] = __( 'Please, enter your license key', 'bestwebsoft' );
			}
		}
		return $result;
	}
}

if ( ! function_exists ( 'bws_check_pro_license_form' ) ) {
	function bws_check_pro_license_form( $plugin_basename ) {
		global $bstwbsftwppdtplgns_options;
		$license_key = ( isset( $bstwbsftwppdtplgns_options[ $plugin_basename ] ) ) ? $bstwbsftwppdtplgns_options[ $plugin_basename ] : ''; ?>
		<div class="clear"></div>
		<form method="post" action="">
			<p><?php echo _e( 'If needed you can check if the license key is correct or reenter it in the field below. You can find your license key on your personal page - Client area - on our website', 'bestwebsoft' ) . ' <a href="http://bestwebsoft.com/wp-login.php">http://bestwebsoft.com/wp-login.php</a> ' . __( '(your username is the email address specified during the purchase). If necessary, please submit "Lost your password?" request.', 'bestwebsoft' ); ?></p>
			<p>
				<input type="text" maxlength="100" name="bws_license_key" value="<?php echo $license_key; ?>" />
				<input type="hidden" name="bws_license_submit" value="submit" />
				<input type="submit" class="button" value="<?php _e( 'Check license key', 'bestwebsoft' ) ?>" />
				<?php wp_nonce_field( $plugin_basename, 'bws_license_nonce_name' ); ?>
			</p>
		</form>
	<?php }
}

if ( ! function_exists ( 'bws_plugin_update_row' ) ) {
	function bws_plugin_update_row( $plugin_key, $link_slug = false, $free_plugin_name = false ) {
		global $bstwbsftwppdtplgns_options;
		$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
		if ( isset( $bstwbsftwppdtplgns_options['wrong_license_key'][ $plugin_key ] ) ) {
			echo '<tr class="plugin-update-tr">
					<td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange">
						<div class="update-message" style="background-color: #FFEBE8; border-color: #CC0000;"><strong>' . __( 'WARNING: Illegal use notification', 'bestwebsoft' ) . '.</strong> ' . __( 'You can use one license of the Pro plugin for one domain only. Please check and edit your license or domain if necessary using you personal Client Area. We strongly recommend you to solve the problem within 24 hours, otherwise the Pro plugin will be deactivated.', 'bestwebsoft' ) . ' <a target="_blank" href="http://support.bestwebsoft.com/hc/en-us/articles/204240089">' . __( 'Learn More', 'bestwebsoft' ) . '</a></div>
					</td>
				</tr>';
		} elseif ( isset( $bstwbsftwppdtplgns_options['time_out'][ $plugin_key ] ) && strtotime( $bstwbsftwppdtplgns_options['time_out'][ $plugin_key ] ) < strtotime( date("m/d/Y") ) ) {
			echo '<tr class="plugin-update-tr">
					<td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange">
						<div class="update-message" style="color: #8C0000;">'; 
						if ( isset( $bstwbsftwppdtplgns_options['trial'][ $plugin_key ] ) && $link_slug != false ) {
							echo __( 'Notice: Your Pro Trial license has expired. To continue using the plugin you should buy a Pro license', 'bestwebsoft' ) . ' - <a href="http://bestwebsoft.com/products/' . $link_slug .'/">http://bestwebsoft.com/products/' . $link_slug . '/</a>';
						} else {
							echo __( 'Your license has expired. To continue getting top-priority support and plugin updates you should extend it.', 'bestwebsoft' ) . ' <a target="_new" href="http://support.bestwebsoft.com/entries/53487136">' . __( "Learn more", 'bestwebsoft' ) . '</a>';
						}
					echo '</div>
					</td>
				</tr>';
		} elseif ( isset( $bstwbsftwppdtplgns_options['trial'][ $plugin_key ] ) ) {
			echo '<tr class="plugin-update-tr">
					<td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange">
						<div class="update-message" style="color: #8C0000;">';
							if ( $free_plugin_name != false ) {
								echo sprintf( __( 'Notice: You are using the Pro Trial license of %s plugin.', 'bestwebsoft' ), $free_plugin_name );
							} else {
								_e( 'Notice: You are using the Pro Trial license of plugin.', 'bestwebsoft' );
							}
							if ( isset( $bstwbsftwppdtplgns_options['time_out'][ $plugin_key ] ) )
								echo ' ' . __( "The Pro Trial license will expire on", 'bestwebsoft' ) . ' ' . $bstwbsftwppdtplgns_options['time_out'][ $plugin_key ] . '.';
					echo '</div>
					</td>
				</tr>';
		}
	}
}

if ( ! function_exists ( 'bws_plugin_banner_timeout' ) ) {
	function bws_plugin_banner_timeout( $plugin_key, $plugin_prefix, $plugin_name, $banner_url = false ) {
		global $bstwbsftwppdtplgns_options, $bstwbsftwppdtplgns_cookie_add;
		if ( isset( $bstwbsftwppdtplgns_options['time_out'][ $plugin_key ] ) && ( strtotime( $bstwbsftwppdtplgns_options['time_out'][ $plugin_key ] ) < strtotime( date("m/d/Y") . '+1 month' ) ) && ( strtotime( $bstwbsftwppdtplgns_options['time_out'][ $plugin_key ] ) > strtotime( date("m/d/Y") ) ) ) {
			if ( ! isset( $bstwbsftwppdtplgns_cookie_add ) ) {
				echo '<script type="text/javascript" src="' . plugins_url( 'js/c_o_o_k_i_e.js', __FILE__ ) . '"></script>';
				$bstwbsftwppdtplgns_cookie_add = true;
			} ?>
			<script type="text/javascript">
				(function($) {
					$(document).ready( function() {
						var hide_message = $.cookie( "<?php echo $plugin_prefix; ?>_timeout_hide_banner_on_plugin_page" );
						if ( hide_message == "true" ) {
							$( ".<?php echo $plugin_prefix; ?>_message" ).css( "display", "none" );
						} else {
							$( ".<?php echo $plugin_prefix; ?>_message" ).css( "display", "block" );
						}
						$( ".<?php echo $plugin_prefix; ?>_close_icon" ).click( function() {
							$( ".<?php echo $plugin_prefix; ?>_message" ).css( "display", "none" );
							$.cookie( "<?php echo $plugin_prefix; ?>_timeout_hide_banner_on_plugin_page", "true", { expires: 30 } );
						});
					});
				})(jQuery);
			</script>
			<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
				<div class="<?php echo $plugin_prefix; ?>_message bws_banner_on_plugin_page bws_banner_timeout" style="display:none;">
					<button class="<?php echo $plugin_prefix; ?>_close_icon close_icon notice-dismiss bws_hide_settings_notice" title="<?php _e( 'Close notice', 'bestwebsoft' ); ?>"></button>
					<div class="icon">
						<img title="" src="<?php echo $banner_url; ?>" alt="" />
					</div>
					<div class="text"><?php _e( "You license for", 'bestwebsoft' ); ?> <strong><?php echo $plugin_name; ?></strong> <?php echo __( "expires on", 'bestwebsoft' ) . ' ' . $bstwbsftwppdtplgns_options['time_out'][ $plugin_key ] . ' ' . __( "and you won't be granted TOP-PRIORITY SUPPORT or UPDATES.", 'bestwebsoft' ); ?> <a target="_new" href="http://support.bestwebsoft.com/entries/53487136"><?php _e( "Learn more", 'bestwebsoft' ); ?></a></div>
				</div>  
			</div>
		<?php }
	}
}

if ( ! function_exists( 'bws_plugin_banner_to_settings' ) ) {
	function bws_plugin_banner_to_settings( $plugin_info, $plugin_options_name, $banner_url_or_slug, $settings_url, $post_type_url = false ) {
		global $wp_version, $bws_plugin_banner_to_settings;

		$is_network_admin = is_network_admin();

		$plugin_options = $is_network_admin ? get_site_option( $plugin_options_name ) : get_option( $plugin_options_name );

		if ( isset( $plugin_options['display_settings_notice'] ) && 0 == $plugin_options['display_settings_notice'] )
			return;
		
		if ( isset( $_POST['bws_hide_settings_notice_' . $plugin_options_name ] ) && check_admin_referer( plugin_basename( __FILE__ ), 'bws_settings_nonce_name' )  ) {
			$plugin_options['display_settings_notice'] = 0;
			if ( $is_network_admin )
				update_site_option( $plugin_options_name, $plugin_options );
			else
				update_option( $plugin_options_name, $plugin_options );
			return;
		}

		if ( false == strrpos( $banner_url_or_slug, '/' ) ) {
			$banner_url_or_slug = '//ps.w.org/' . $banner_url_or_slug . '/assets/icon-128x128.png';
		} 

		$bws_plugin_banner_to_settings[] = array(
			'plugin_info'			=> $plugin_info, 
			'plugin_options_name'	=> $plugin_options_name,
			'banner_url'			=> $banner_url_or_slug,
			'settings_url'			=> $settings_url,
			'post_type_url'			=> $post_type_url
		);
	}
}

if ( ! function_exists( 'bws_plugin_suggest_feature_banner' ) ) {
	function bws_plugin_suggest_feature_banner( $plugin_info, $plugin_options_name, $banner_url_or_slug ) {
		global $wp_version;

		$is_network_admin = is_network_admin();

		$plugin_options = $is_network_admin ? get_site_option( $plugin_options_name ) : get_option( $plugin_options_name );

		if ( isset( $plugin_options['display_suggest_feature_banner'] ) && 0 == $plugin_options['display_suggest_feature_banner'] )
			return;

		if ( ! isset( $plugin_options['first_install'] ) ) {
			$plugin_options['first_install'] = strtotime( "now" );
			$update_option = $return = true;
		} elseif ( strtotime( '-2 week' ) < $plugin_options['first_install'] ) {
			$return = true;
		}

		if ( ! isset( $plugin_options['go_settings_counter'] ) ) {
			$plugin_options['go_settings_counter'] = 1;
			$update_option = $return = true;
		} elseif ( 20 > $plugin_options['go_settings_counter'] ) {
			$plugin_options['go_settings_counter'] = $plugin_options['go_settings_counter'] + 1;
			$update_option = $return = true;
		}

		if ( isset( $update_option ) ) {
			if ( $is_network_admin )
				update_site_option( $plugin_options_name, $plugin_options );
			else
				update_option( $plugin_options_name, $plugin_options );
		}

		if ( isset( $return ) )
			return;
		
		if ( isset( $_POST['bws_hide_suggest_feature_banner_' . $plugin_options_name ] ) && check_admin_referer( $plugin_info['Name'], 'bws_settings_nonce_name' )  ) {
			$plugin_options['display_suggest_feature_banner'] = 0;
			if ( $is_network_admin )
				update_site_option( $plugin_options_name, $plugin_options );
			else
				update_option( $plugin_options_name, $plugin_options );
			return;
		}

		if ( false == strrpos( $banner_url_or_slug, '/' ) ) {
			$banner_url_or_slug = '//ps.w.org/' . $banner_url_or_slug . '/assets/icon-128x128.png';
		} ?>
		<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
			<div class="bws_banner_on_plugin_page bws_suggest_feature_banner">
				<div class="icon">
					<img title="" src="<?php echo esc_attr( $banner_url_or_slug ); ?>" alt="" />
				</div>						
				<div class="text">
					<strong><?php _e( 'Thank you for choosing', 'bestwebsoft' ); ?> <?php echo $plugin_info['Name']; ?> plugin!</strong><br />
					<?php _e( "If you have a feature, suggestion or idea you'd like to see in the plugin, we'd love to hear about it!", 'bestwebsoft' ); ?> 
					<a target="_blank" href="http://support.bestwebsoft.com/hc/en-us/requests/new"><?php _e( 'Suggest a Feature', 'bestwebsoft' ); ?></a> 
				</div>
				<form action="" method="post">
					<button class="notice-dismiss bws_hide_settings_notice" title="<?php _e( 'Close notice', 'bestwebsoft' ); ?>"></button>
					<input type="hidden" name="bws_hide_suggest_feature_banner_<?php echo $plugin_options_name; ?>" value="hide" />
					<?php wp_nonce_field( $plugin_info['Name'], 'bws_settings_nonce_name' ); ?>
				</form>
			</div>
		</div>
	<?php }
}

if ( ! function_exists( 'bws_show_settings_notice' ) ) {
	function bws_show_settings_notice() { ?>
		<div id="bws_save_settings_notice" class="updated fade below-h2" style="display:none;">
			<p>
				<strong><?php _e( 'Notice', 'bestwebsoft' ); ?></strong>: <?php _e( "The plugin's settings have been changed.", 'bestwebsoft' ); ?> 
				<a class="bws_save_anchor" href="#bws-submit-button"><?php _e( 'Save Changes', 'bestwebsoft' ); ?></a>
			</p>
		</div>
	<?php }
}

if ( ! function_exists( 'bws_hide_premium_options' ) ) {
	function bws_hide_premium_options( $options ) {
		if ( ! isset( $options['hide_premium_options'] ) || ! is_array( $options['hide_premium_options'] ) )
			$options['hide_premium_options'] = array();
		
		$options['hide_premium_options'][] = get_current_user_id();

		return array( 
				'message' => __( 'You can always look at premium options by clicking on the "Show Pro features" in the "Go PRO" tab', 'bestwebsoft' ),
				'options' => $options );
	}
}

if ( ! function_exists( 'bws_hide_premium_options_check' ) ) {
	function bws_hide_premium_options_check( $options ) {
		if ( ! empty( $options['hide_premium_options'] ) && in_array( get_current_user_id(), $options['hide_premium_options'] ) )
			return true;
		else
			return false;
	}
}

if ( ! function_exists ( 'bws_plugins_admin_init' ) ) {
	function bws_plugins_admin_init() {

		if ( isset( $_GET['bws_activate_plugin'] ) && check_admin_referer( 'bws_activate_plugin' . $_GET['bws_activate_plugin'] ) ) {

			$plugin = isset( $_GET['bws_activate_plugin'] ) ? $_GET['bws_activate_plugin'] : '';				
			$result = activate_plugin( $plugin, '', is_network_admin() );				
			if ( is_wp_error( $result ) ) {
				if ( 'unexpected_output' == $result->get_error_code() ) {
					$redirect = self_admin_url( 'admin.php?page=bws_panel&error=true&charsout=' . strlen( $result->get_error_data() ) . '&plugin=' . $plugin );
					wp_redirect( add_query_arg( '_error_nonce', wp_create_nonce( 'plugin-activation-error_' . $plugin ), $redirect ) );
					exit();
				} else {
					wp_die( $result );
				}
			}

			if ( ! is_network_admin() ) {
				$recent = (array) get_option( 'recently_activated' );
				unset( $recent[ $plugin ] );
				update_option( 'recently_activated', $recent );
			} else {
				$recent = (array) get_site_option( 'recently_activated' );
				unset( $recent[ $plugin ] );
				update_site_option( 'recently_activated', $recent );
			}
			wp_redirect( self_admin_url( 'admin.php?page=bws_panel&activate=true' ) );
			exit();
		}

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'bws_panel' ) {
			if ( ! session_id() )
				@session_start();
		}

		bws_add_editor_buttons();
	}
}

if ( ! function_exists ( 'bws_admin_enqueue_scripts' ) ) {
	function bws_admin_enqueue_scripts() {
		global $wp_version;
		wp_enqueue_style( 'bws-admin-css', plugins_url( 'css/general_style.css', __FILE__ ) );
		wp_enqueue_script( 'bws-admin-scripts', plugins_url( 'js/general_script.js', __FILE__ ), array( 'jquery' ) );

		if ( isset( $_GET['page'] ) && in_array( $_GET['page'], array( 'bws_panel', 'bws_themes', 'bws_system_status' ) ) ) {
			wp_enqueue_style( 'bws_menu_style', plugins_url( 'css/style.css', __FILE__ ) );
			wp_enqueue_script( 'bws_menu_script', plugins_url( 'js/bws_menu.js' , __FILE__ ) );
			wp_enqueue_script( 'theme-install' );
			add_thickbox();
			wp_enqueue_script( 'plugin-install' );
		}
	}
}

if ( ! function_exists ( 'bws_plugins_admin_head' ) ) {
	function bws_plugins_admin_head() {
		global $bws_shortcode_list, $wp_version, $post_type;
		if ( isset( $_GET['page'] ) && $_GET['page'] == "bws_panel" ) { ?>
			<noscript>
				<style type="text/css">
					.bws_product_button {
						display: inline-block;
					}
				</style>
			</noscript>
		<?php }
		if ( 4.2 > $wp_version ) {
			$plugin_dir_array = explode( '/', plugin_basename( __FILE__ ) );
			$plugin_dir = $plugin_dir_array[0]; ?>
			<style type="text/css">
				.bws_hide_settings_notice,
				.bws_hide_premium_options {
					width: 11px;
					height: 11px;
					border: none;
					background: url("<?php echo plugins_url( $plugin_dir . '/bws_menu/images/close_banner.png' ); ?>") no-repeat center center;
					box-shadow: none;
					float: right;
					margin: 8px;
				}
				.bws_hide_settings_notice:hover,
				.bws_hide_premium_options:hover {
					cursor: pointer;
				}
				.bws_hide_premium_options {
					position: relative;
				}
			</style>
		<?php }
		if ( ! empty( $bws_shortcode_list ) ) { ?>
			<!-- TinyMCE Shortcode Plugin -->
			<script type='text/javascript'>
				var bws_shortcode_button = {
					'label': '<?php esc_attr_e( "Add BWS Shortcode", "bestwebsoft" ); ?>',
					'title': '<?php esc_attr_e( "Add BWS Plugins Shortcode", "bestwebsoft" ); ?>',
					'icon_url': '<?php echo plugins_url( "images/shortcode-icon.png" , __FILE__ ); ?>',
					'function_name': [
						<?php foreach ( $bws_shortcode_list as $key => $value ) {
							if ( isset( $value['js_function'] ) )
								echo "'" . $value['js_function'] . "',";	
						} ?>
					],
					'wp_version' : '<?php echo $wp_version; ?>'
				};
			</script>
			<!-- TinyMCE Shortcode Plugin -->
			<?php if ( isset( $post_type ) && in_array( $post_type, array( 'post', 'page' ) ) ) {
				$tooltip_args = array(
					'tooltip_id'	=> 'bws_shortcode_button_tooltip',
					'css_selector' 	=> '.mce-bws_shortcode_button',
					'actions' 		=> array(
						'click' 	=> false,
						'onload' 	=> true
					), 
					'content' 		=> '<h3>' . __( 'Add shortcode', 'bestwebsoft' ) . '</h3><p>' . __( "Add BestWebSoft plugins' shortcodes using this button.", 'bestwebsoft' ) . '</p>',
					'position' => array( 
						'edge' 		=> 'right'
					),
					'set_timeout' => 2000
				);
				if ( $wp_version < '3.9' )
					$tooltip_args['css_selector'] = '.mce_add_bws_shortcode';
				bws_add_tooltip_in_admin( $tooltip_args );
			}
		} 
    }
}

if ( ! function_exists ( 'bws_plugins_include_codemirror' ) ) {
	function bws_plugins_include_codemirror() {
		wp_enqueue_style( 'codemirror.css', plugins_url( 'css/codemirror.css', __FILE__ ) );
		wp_enqueue_script( 'codemirror.js', plugins_url( 'js/codemirror.js', __FILE__ ), array( 'jquery' ) );
    }
}

/**
 * Tooltip block
 */
if ( ! function_exists( 'bws_add_tooltip_in_admin' ) ) {
	function bws_add_tooltip_in_admin( $tooltip_args = array() ) {
		new BWS_admin_tooltip( $tooltip_args );
	}
}

if ( ! class_exists( 'BWS_admin_tooltip' ) ) {
	class BWS_admin_tooltip {
		private $tooltip_args;

		public function __construct( $tooltip_args ) {
			global $wp_version;
			if ( 3.3 > $wp_version )
				return;
			/* Default arguments */
			$tooltip_args_default = array( 
				'tooltip_id'	=> false,
				'css_selector' 	=> false, 
				'actions' 		=> array(
					'click' 	=> true,
					'onload' 	=> false,
				), 
				'buttons'		=> array(
					'close' 	=> array(
						'type' => 'dismiss',
						'text' => __( 'Close', 'bestwebsoft' ),
					),
				),
				'position' => array(
					'edge'  	=> 'top', 
					'align' 	=> 'center',
					'pos-left'	=> 0, 
					'pos-top'	=> 0, 
					'zindex' 	=> 10000 
				),
				'set_timeout' => 0
			);
			$tooltip_args = array_merge( $tooltip_args_default, $tooltip_args );
			/* Check that our merged array has default values */
			foreach ( $tooltip_args_default as $arg_key => $arg_value ) {
				if ( is_array( $arg_value ) ) {
					foreach ( $arg_value as $key => $value) {
						if ( ! isset( $tooltip_args[ $arg_key ][ $key ] ) ) {
							$tooltip_args[ $arg_key ][ $key ] = $tooltip_args_default[ $arg_key ][ $key ];
						}
					}
				}
			}
			/* Check if tooltip is dismissed */
			if ( true === $tooltip_args['actions']['onload'] ) {
				if ( in_array( $tooltip_args['tooltip_id'], array_filter( explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) ) ) ) ) {
					$tooltip_args['actions']['onload'] = false;
				}
			}
			/* Check entered data */
			if ( false === $tooltip_args['tooltip_id'] || false === $tooltip_args['css_selector'] || ( false === $tooltip_args['actions']['click'] && false === $tooltip_args['actions']['onload'] ) ) {
				/* if not enough data to output a tooltip or both actions (click, onload) are false */
				return;
			} else {
				/* check position */
				if ( ! in_array( $tooltip_args['position']['edge'], array( 'left', 'right', 'top', 'bottom' ) )  ) {
					$tooltip_args['position']['edge'] = 'top';
				}
				if ( ! in_array( $tooltip_args['position']['align'], array( 'top', 'bottom', 'left', 'right', 'center', ) ) ) {
					$tooltip_args['position']['align'] = 'center';
				}
			}
			/* fix position */
			switch ( $tooltip_args['position']['edge'] ) {
				case 'left':
				case 'right':
					switch ( $tooltip_args['position']['align'] ) {
						case 'top':
						case 'bottom':
							$tooltip_args['position']['align'] = 'center';
							break;
					}
					break;
				case 'top':
				case 'bottom':
					if ( $tooltip_args['position']['align'] == 'left' ) {
						$tooltip_args['position']['pos-left'] -= 65;
					}
					break;
			}
			$this->tooltip_args = $tooltip_args;
			/* add styles and scripts */
			wp_enqueue_style( 'wp-pointer' );
			wp_enqueue_script( 'wp-pointer' );
			/* add script that displays our tooltip */
			add_action( 'admin_print_footer_scripts', array( $this, 'add_scripts' ) );
		}

		/**
		 * Display tooltip
		 */
		public function add_scripts() {
			global $bstwbsftwppdtplgns_tooltip_script_add;
			if ( ! isset( $bstwbsftwppdtplgns_tooltip_script_add ) ) {
				echo '<script type="text/javascript" src="' . plugins_url( 'js/bws_tooltip.js', __FILE__ ) . '"></script>';
				$bstwbsftwppdtplgns_tooltip_script_add = true;
			}
			$tooltip_args = $this->tooltip_args; ?>
			<script type="text/javascript">
				(function($) {
					$(document).ready( function() {
						$.bwsTooltip( <?php echo json_encode( $tooltip_args ); ?> );
					})
				})(jQuery);
			</script>
		<?php }
	}
}

if ( ! function_exists ( 'bws_form_restore_default_settings' ) ) {
	function bws_form_restore_default_settings( $plugin_basename, $change_permission_attr = '' ) { ?>
		<form method="post" action="">			
			<p><?php _e( 'Restore all plugin settings to defaults', 'bestwebsoft' ); ?></p>
			<p>					
				<input <?php echo $change_permission_attr; ?> type="submit" class="button" value="<?php _e( 'Restore settings', 'bestwebsoft' ); ?>" />
			</p>
			<input type="hidden" name="bws_restore_default" value="submit" />
			<?php wp_nonce_field( $plugin_basename, 'bws_settings_nonce_name' ); ?>
		</form>
	<?php }
}

if ( ! function_exists ( 'bws_form_restore_default_confirm' ) ) {
	function bws_form_restore_default_confirm( $plugin_basename ) { ?>
		<div>
			<p><?php _e( 'Are you sure you want to restore all settings by default?', 'bestwebsoft' ) ?></p>
			<form method="post" action="">
				<p>
					<button class="button" name="bws_restore_confirm"><?php _e( 'Yes, restore all settings', 'bestwebsoft' ) ?></button>
					<button class="button" name="bws_restore_deny"><?php _e( 'No, go back to the settings page', 'bestwebsoft' ) ?></button>
					<?php wp_nonce_field( $plugin_basename, 'bws_settings_nonce_name' ); ?>
				</p>
			</form>
		</div>
	<?php }
}

/* shortcode */
if ( ! function_exists( 'bws_add_editor_buttons' ) ) {
	function bws_add_editor_buttons() {
		global $bws_shortcode_list, $wp_version;
		if ( $wp_version < '3.3' )
			return;
		if ( ! empty( $bws_shortcode_list ) && current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
			add_filter( 'mce_external_plugins', 'bws_add_buttons' );
			add_filter( 'mce_buttons', 'bws_register_buttons' );
		}
	}
}

if ( ! function_exists( 'bws_add_buttons' ) ){
	function bws_add_buttons( $plugin_array ) {
		$plugin_array['add_bws_shortcode'] = plugins_url( 'js/shortcode-button.js', __FILE__ );
		return $plugin_array;
	}
}

if ( ! function_exists( 'bws_register_buttons' ) ) {
	function bws_register_buttons( $buttons ) {
		array_push( $buttons, 'add_bws_shortcode' ); /* dropcap', 'recentposts */
		return $buttons;
	}
}

/* Generate inline content for the popup window when the "bws shortcode" button is clicked */
if ( ! function_exists( 'bws_shortcode_media_button_popup' ) ) {
	function bws_shortcode_media_button_popup() { 
		global $bws_shortcode_list, $wp_version;
		if ( $wp_version < '3.3' )
			return;

		if ( ! empty( $bws_shortcode_list ) ) { ?>
			<div id="bws_shortcode_popup" style="display:none;">
				<div id="bws_shortcode_popup_block">
					<div id="bws_shortcode_select_plugin">
						<h4><?php _e( 'Plugin', 'bestwebsoft' ); ?></h4>
						<select name="bws_shortcode_select" id="bws_shortcode_select">
							<?php foreach ( $bws_shortcode_list as $key => $value ) { ?>
								<option value="<?php echo $key; ?>"><?php echo $value['name']; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="clear"></div>
					<div id="bws_shortcode_content">
						<h4><?php _e( 'Shortcode settings', 'bestwebsoft' ); ?></h4>
						<?php echo apply_filters( 'bws_shortcode_button_content', '' ); ?>
					</div>
					<div class="clear"></div>
					<div id="bws_shortcode_content_bottom">
						<p><?php _e( 'The shortcode will be inserted', 'bestwebsoft' ); ?></p>
						<div id="bws_shortcode_block"><div id="bws_shortcode_display"></div></div>
					</div>
					<?php if ( $wp_version < '3.9' ) { ?>
						<p>
							<button class="button-primary primary bws_shortcode_insert"><?php _e( 'Insert', 'bestwebsoft' ); ?></button>
						</p>
					<?php } ?>
				</div>
			</div>
		<?php }
		if ( $wp_version < '3.9' ) { ?>
			<script type="text/javascript">
				(function($){
					$( '.bws_shortcode_insert' ).on( 'click',function() { 
						var shortcode = $( '#TB_ajaxContent #bws_shortcode_display' ).text();
						if ( '' != shortcode ) {
							/* insert shortcode to tinymce */
							if ( !tinyMCE.activeEditor || tinyMCE.activeEditor.isHidden() ) {
								$( 'textarea#content' ).val( shortcode );
							} else {
								tinyMCE.execCommand( 'mceInsertContent', false, shortcode );
							}               
						}
						/* close the thickbox after adding shortcode to editor */
						self.parent.tb_remove();
					});
				})(jQuery);
			</script>
		<?php } 
	}
}

/* add help tab  */
if ( ! function_exists( 'bws_help_tab' ) ) {
	function bws_help_tab( $screen, $args ) {
		$url = ( ! empty( $args['section'] ) ) ? 'http://support.bestwebsoft.com/hc/en-us/sections/' . $args['section'] : 'http://support.bestwebsoft.com/';

		$content = '<p><a href="' . $url . '" target="_blank">' . __( 'Visit Help Center', 'bestwebsoft' ) . '</a></p>';

		$screen->add_help_tab( 
			array(
				'id'      => $args['id'] . '_help_tab',
				'title'   => __( 'FAQ', 'bestwebsoft' ),
				'content' => $content
			)
		);

		$screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'bestwebsoft' ) . '</strong></p>' .
			'<p><a href="https://drive.google.com/folderview?id=0B5l8lO-CaKt9VGh0a09vUjNFNjA&usp=sharing#list" target="_blank">' . __( 'Documentation', 'bestwebsoft' ) . '</a></p>' .
			'<p><a href="http://www.youtube.com/user/bestwebsoft/playlists?flow=grid&sort=da&view=1" target="_blank">' . __( 'Video Instructions', 'bestwebsoft' ) . '</a></p>' .
			'<p><a href="http://support.bestwebsoft.com/hc/en-us/requests/new" target="_blank">' . __( 'Submit a Request', 'bestwebsoft' ) . '</a></p>'
		);
	}
}

/**
* Function display 'Custom code' tab
*/
if ( ! function_exists( 'bws_custom_code_tab' ) ) {
	function bws_custom_code_tab() {
		if ( ! current_user_can( 'edit_plugins' ) )
			wp_die( __( 'You do not have sufficient permissions to edit plugins for this site.', 'bestwebsoft' ) );

		global $bstwbsftwppdtplgns_options;

		$message = $content = '';
		$is_css_active = $is_php_active = false;

		$upload_dir = wp_upload_dir();
		$folder = $upload_dir['basedir'] . '/bws-custom-code';
		if ( ! $upload_dir["error"] ) {
			if ( ! is_dir( $folder ) )
				wp_mkdir_p( $folder, 0755 );

			$index_file = $upload_dir['basedir'] . '/bws-custom-code/index.php';
			if ( ! file_exists( $index_file ) ) {
				if ( $f = fopen( $index_file, 'w+' ) )
					fclose( $f );
			}				
		}

		$css_file = 'bws-custom-code.css';
		$real_css_file = $folder . '/' . $css_file;

		$php_file = 'bws-custom-code.php';	
		$real_php_file = $folder . '/' . $php_file;	

		$is_multisite = is_multisite();
		if ( $is_multisite )
			$blog_id = get_current_blog_id();

		if ( isset( $_REQUEST['bws_update_custom_code'] ) && check_admin_referer( 'bws_update_' . $css_file ) ) {

			/* CSS */
			$newcontent_css = wp_unslash( $_POST['bws_newcontent_css'] );	
			if ( ! empty( $newcontent_css ) && isset( $_REQUEST['bws_custom_css_active'] ) ) {
				if ( $is_multisite )
					$bstwbsftwppdtplgns_options['custom_code'][ $blog_id ][ $css_file ] = $upload_dir['baseurl'] . '/bws-custom-code/' . $css_file;
				else
					$bstwbsftwppdtplgns_options['custom_code'][ $css_file ] = $upload_dir['baseurl'] . '/bws-custom-code/' . $css_file;
			} else {
				if ( $is_multisite ) {
					if ( isset( $bstwbsftwppdtplgns_options['custom_code'][ $blog_id ][ $css_file ] ) )
						unset( $bstwbsftwppdtplgns_options['custom_code'][ $blog_id ][ $css_file ] );
				} else {
					if ( isset( $bstwbsftwppdtplgns_options['custom_code'][ $css_file ] ) )
						unset( $bstwbsftwppdtplgns_options['custom_code'][ $css_file ] );
				}
			}
			if ( $f = fopen( $real_css_file, 'w+' ) ) {
				fwrite( $f, $newcontent_css );
				fclose( $f );
				$message .= sprintf( __( 'File %s edited successfully.', 'bestwebsoft' ), '<i>' . $css_file . '</i>' ) . ' ';
			} else {
				$error .= __( 'Not enough permissions to create or update the file', 'bestwebsoft' ) . ' ' . $real_css_file . '. ';
			}

			/* PHP */
			$newcontent_php = wp_unslash( trim( $_POST['bws_newcontent_php'] ) );
			if ( file_exists( $index_file ) ) {
				if ( ! empty( $newcontent_php ) && isset( $_REQUEST['bws_custom_php_active'] ) ) {
					if ( $is_multisite )
						$bstwbsftwppdtplgns_options['custom_code'][ $blog_id ][ $php_file ] = $real_php_file;
					else
						$bstwbsftwppdtplgns_options['custom_code'][ $php_file ] = $real_php_file;
				} else {
					if ( $is_multisite ) {
						if ( isset( $bstwbsftwppdtplgns_options['custom_code'][ $blog_id ][ $php_file ] ) )
							unset( $bstwbsftwppdtplgns_options['custom_code'][ $blog_id ][ $php_file ] );
					} else {
						if ( isset( $bstwbsftwppdtplgns_options['custom_code'][ $php_file ] ) )
							unset( $bstwbsftwppdtplgns_options['custom_code'][ $php_file ] );
					}
				}

				if ( $f = fopen( $real_php_file, 'w+' ) ) {
					$newcontent_php = $newcontent_php;
					fwrite( $f, $newcontent_php );
					fclose( $f );
					$message .= sprintf( __( 'File %s edited successfully.', 'bestwebsoft' ), '<i>' . $php_file . '</i>' );
				} else {
					$error .= __( 'Not enough permissions to create or update the file', 'bestwebsoft' ) . ' ' . $real_php_file . '. ';
				}
			} else {
				$error .= __( 'Not enough permissions to create the file', 'bestwebsoft' ) . ' ' . $index_file . '. ';
			}				

			if ( ! empty( $error ) )
				$error .= ' <a href="https://codex.wordpress.org/Changing_File_Permissions" target="_blank">' . __( 'Learn more', 'bestwebsoft' ) . '</a>';

			if ( $is_multisite )
				update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
			else
				update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
		}

		if ( file_exists( $real_css_file ) ) {
			update_recently_edited( $real_css_file );
			$content_css = esc_textarea( file_get_contents( $real_css_file ) );
			if ( ( $is_multisite && isset( $bstwbsftwppdtplgns_options['custom_code'][ $blog_id ][ $css_file ] ) ) ||
				( ! $is_multisite && isset( $bstwbsftwppdtplgns_options['custom_code'][ $css_file ] ) ) ) {
				$is_css_active = true;
			}
		}
		if ( file_exists( $real_php_file ) ) {
			update_recently_edited( $real_php_file );
			$content_php = esc_textarea( file_get_contents( $real_php_file ) );
			if ( ( $is_multisite && isset( $bstwbsftwppdtplgns_options['custom_code'][ $blog_id ][ $php_file ] ) ) ||
				( ! $is_multisite && isset( $bstwbsftwppdtplgns_options['custom_code'][ $php_file ] ) ) ) {
				$is_php_active = true;
			}
		} else {
			$content_php = "<?php" . "\n" . "if ( ! defined( 'ABSPATH' ) ) exit;" . "\n" . "if ( ! defined( 'BWS_GLOBAL' ) ) exit;" . "\n\n" . "/* Start your code here */" . "\n";
		}

		if ( ! empty( $message ) ) { ?>
			<div id="message" class="below-h2 updated notice is-dismissible"><p><?php echo $message; ?></p></div>
		<?php } ?>		
		<form action="" method="post">
			<?php foreach ( array( 'css', 'php' ) as $extension ) { ?>				
				<p>
					<?php if ( 'css' == $extension )
						_e( 'These styles will be added to the header on all pages of your site.', 'bestwebsoft' );
					else
						printf( __( 'These PHP code will be hooked to the %s action and will be printed on front end only.', 'bestwebsoft' ), '<a href="http://codex.wordpress.org/Plugin_API/Action_Reference/init" target="_blank"><code>init</code></a>' ); ?>
				</p>
				<p><big>
					<?php if ( ! file_exists( ${"real_{$extension}_file"} ) || ( is_writeable( ${"real_{$extension}_file"} ) ) ) {
						echo __( 'Editing', 'bestwebsoft' ) . ' <strong>' . ${"{$extension}_file"} . '</strong>';
					} else {
						echo __( 'Browsing', 'bestwebsoft' ) . ' <strong>' . ${"{$extension}_file"} . '</strong>';
					} ?>
				</big></p>
				<p><label><input type="checkbox" name="bws_custom_<?php echo $extension; ?>_active" value="1" <?php if ( ${"is_{$extension}_active"} ) echo "checked"; ?> />	<?php _e( 'Activate', 'bestwebsoft' ); ?></label></p>
				<textarea cols="70" rows="25" name="bws_newcontent_<?php echo $extension; ?>" id="bws_newcontent_<?php echo $extension; ?>"><?php if ( isset( ${"content_{$extension}"} ) ) echo ${"content_{$extension}"}; ?></textarea>
				<p class="description">
					<a href="<?php echo ( 'css' == $extension ) ? 'https://developer.mozilla.org/en-US/docs/Web/Guide/CSS/Getting_started' : 'http://php.net/' ?>" target="_blank">
						<?php printf( __( 'Learn more about %s', 'bestwebsoft' ), strtoupper( $extension ) ); ?>						
					</a>
				</p>				
			<?php }
			if ( ( ! file_exists( $real_css_file ) || is_writeable( $real_css_file ) ) && ( ! file_exists( $real_php_file ) || is_writeable( $real_php_file ) ) ) { ?>
				<p class="submit">
					<input type="hidden" name="bws_update_custom_code" value="submit" />					
					<?php submit_button( __( 'Save Changes', 'bestwebsoft' ), 'primary', 'submit', false ); 
					wp_nonce_field( 'bws_update_' . $css_file ); ?>
				</p>
			<?php } else { ?>
				<p><em><?php printf( __( 'You need to make this files writable before you can save your changes. See %s the Codex %s for more information.', 'bestwebsoft' ),
				'<a href="https://codex.wordpress.org/Changing_File_Permissions" target="_blank">',
				'</a>' ); ?></em></p>
			<?php }	?>
		</form>
	<?php }	
}

if ( ! function_exists( 'bws_enqueue_custom_code_css' ) ) {
	function bws_enqueue_custom_code_css() {
		global $bstwbsftwppdtplgns_options;

		if ( ! isset( $bstwbsftwppdtplgns_options ) )
			$bstwbsftwppdtplgns_options = ( function_exists( 'is_multisite' ) && is_multisite() ) ? get_site_option( 'bstwbsftwppdtplgns_options' ) : get_option( 'bstwbsftwppdtplgns_options' );

		if ( ! empty( $bstwbsftwppdtplgns_options['custom_code'] ) ) {
			$is_multisite = is_multisite();
			if ( $is_multisite )
				$blog_id = get_current_blog_id();

			if ( ! $is_multisite && ! empty( $bstwbsftwppdtplgns_options['custom_code']['bws-custom-code.css'] ) )
				wp_enqueue_style( 'bws-custom-style', $bstwbsftwppdtplgns_options['custom_code']['bws-custom-code.css'] );
			elseif ( $is_multisite && ! empty( $bstwbsftwppdtplgns_options['custom_code'][ $blog_id ]['bws-custom-code.css'] ) )
				wp_enqueue_style( 'bws-custom-style', $bstwbsftwppdtplgns_options['custom_code'][ $blog_id ]['bws-custom-code.css'] );
		}
	}
}

if ( ! function_exists( 'bws_enqueue_custom_code_php' ) ) {
	function bws_enqueue_custom_code_php() {
		if ( is_admin() )
			return;

		global $bstwbsftwppdtplgns_options;

		if ( ! isset( $bstwbsftwppdtplgns_options ) )
			$bstwbsftwppdtplgns_options = ( function_exists( 'is_multisite' ) && is_multisite() ) ? get_site_option( 'bstwbsftwppdtplgns_options' ) : get_option( 'bstwbsftwppdtplgns_options' );

		if ( ! empty( $bstwbsftwppdtplgns_options['custom_code'] ) ) {

			$is_multisite = is_multisite();
			if ( $is_multisite )
				$blog_id = get_current_blog_id();

			if ( ! $is_multisite && ! empty( $bstwbsftwppdtplgns_options['custom_code']['bws-custom-code.php'] ) ) {
				
				if ( file_exists( $bstwbsftwppdtplgns_options['custom_code']['bws-custom-code.php'] ) ) {
					define( 'BWS_GLOBAL', true );
					require_once( $bstwbsftwppdtplgns_options['custom_code']['bws-custom-code.php'] );
				} else {
					unset( $bstwbsftwppdtplgns_options['custom_code']['bws-custom-code.php'] );
					if ( $is_multisite )
						update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
					else
						update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
				}
			} elseif ( $is_multisite && ! empty( $bstwbsftwppdtplgns_options['custom_code'][ $blog_id ]['bws-custom-code.php'] ) ) {
				if ( file_exists( $bstwbsftwppdtplgns_options['custom_code'][ $blog_id ]['bws-custom-code.php'] ) ) {
					define( 'BWS_GLOBAL', true );
					require_once( $bstwbsftwppdtplgns_options['custom_code'][ $blog_id ]['bws-custom-code.php'] );
				} else {
					unset( $bstwbsftwppdtplgns_options['custom_code'][ $blog_id ]['bws-custom-code.php'] );
					if ( $is_multisite )
						update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
					else
						update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
				}
			}
		}
	}
}

if ( ! function_exists( 'bws_delete_plugin' ) ) {
	function bws_delete_plugin( $basename ) {
		global $bstwbsftwppdtplgns_options;

		$is_multisite = is_multisite();
		if ( $is_multisite )
			$blog_id = get_current_blog_id();

		if ( ! isset( $bstwbsftwppdtplgns_options ) )
			$bstwbsftwppdtplgns_options = ( $is_multisite ) ? get_site_option( 'bstwbsftwppdtplgns_options' ) : get_option( 'bstwbsftwppdtplgns_options' );

		/* remove bws_menu versions */
		unset( $bstwbsftwppdtplgns_options['bws_menu']['version'][ $basename ] );
		/* if empty ['bws_menu']['version'] - there is no other bws plugins - delete all */
		if ( empty( $bstwbsftwppdtplgns_options['bws_menu']['version'] ) ) {
			/* remove options */
			if ( $is_multisite ) 
				delete_site_option( 'bstwbsftwppdtplgns_options' );
			else
				delete_option( 'bstwbsftwppdtplgns_options' );

			/* remove custom_code */
			if ( $is_multisite ) {
				global $wpdb;
				$old_blog = $wpdb->blogid;
				/* Get all blog ids */
				$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
				foreach ( $blogids as $blog_id ) {
					switch_to_blog( $blog_id );
					$upload_dir = wp_upload_dir();
					$folder = $upload_dir['basedir'] . '/bws-custom-code';
					if ( file_exists( $folder ) && is_dir( $folder ) ) {
						array_map( 'unlink', glob( "$folder/*" ) );
						rmdir( $folder );
					}
				}
				switch_to_blog( $old_blog );
			} else {
				$upload_dir = wp_upload_dir();
				$folder = $upload_dir['basedir'] . '/bws-custom-code';
				if ( file_exists( $folder ) && is_dir( $folder ) ) {
					array_map( 'unlink', glob( "$folder/*" ) );
					rmdir( $folder );
				}
			}
		}	
	}
}

add_action( 'admin_init', 'bws_plugins_admin_init' );
add_action( 'admin_enqueue_scripts', 'bws_admin_enqueue_scripts' );
add_action( 'admin_head', 'bws_plugins_admin_head' );
add_action( 'admin_footer','bws_shortcode_media_button_popup' );

add_action( 'admin_notices', 'bws_admin_notices', 30 );

add_action( 'wp_enqueue_scripts', 'bws_enqueue_custom_code_css', 20 );

bws_enqueue_custom_code_php();