<?php
/*
* Deprecated functions for BestWebSoft plugins
*/

/**
* Function check if plugin is compatible with current WP version - for old plugin version
* @deprecated 1.7.4
* @todo Remove function after 01.01.2018
*/
if ( ! function_exists( 'bws_wp_version_check' ) ) {
	function bws_wp_version_check( $plugin_basename, $plugin_info, $require_wp ) {
		global $bstwbsftwppdtplgns_options;
		if ( ! isset( $bstwbsftwppdtplgns_options ) )
			$bstwbsftwppdtplgns_options = ( function_exists( 'is_multisite' ) && is_multisite() ) ? get_site_option( 'bstwbsftwppdtplgns_options' ) : get_option( 'bstwbsftwppdtplgns_options' );
		if ( ! isset( $bstwbsftwppdtplgns_options['deprecated_function']['bws_wp_version_check'] ) ) {
			$bstwbsftwppdtplgns_options['deprecated_function']['bws_wp_version_check'] = array(
				'product-name' => $plugin_info['Name']
			);
			if ( is_multisite() )
				update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
			else
				update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
		}
	}
}
/**
* Function add BWS Plugins page - for old plugin version
* @deprecated 1.7.9
* @todo Remove function after 01.01.2018
*/
if ( ! function_exists( 'bws_add_general_menu' ) ) {
	function bws_add_general_menu() {
		global $bstwbsftwppdtplgns_options;
		if ( ! isset( $bstwbsftwppdtplgns_options ) )
			$bstwbsftwppdtplgns_options = ( function_exists( 'is_multisite' ) && is_multisite() ) ? get_site_option( 'bstwbsftwppdtplgns_options' ) : get_option( 'bstwbsftwppdtplgns_options' );
		if ( ! isset( $bstwbsftwppdtplgns_options['deprecated_function']['bws_add_general_menu'] ) ) {
			$get_debug_backtrace = debug_backtrace();
			$file = ( ! empty( $get_debug_backtrace[0]['file'] ) ) ? $get_debug_backtrace[0]['file'] : '';
			$bstwbsftwppdtplgns_options['deprecated_function']['bws_add_general_menu'] = array(
				'file' => $file
			);
			if ( is_multisite() )
				update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
			else
				update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
		}
	}
}
/**
* Function display GO PRO tab - for old plugin version
* @deprecated 1.7.6
* @todo Remove function after 01.01.2018
*/
if ( ! function_exists( 'bws_go_pro_tab' ) ) {
	function bws_go_pro_tab( $plugin_info, $plugin_basename, $page, $pro_page, $bws_license_plugin, $link_slug, $link_key, $link_pn, $pro_plugin_is_activated = false, $trial_days_number = false ) {
		global $bstwbsftwppdtplgns_options;
		if ( ! isset( $bstwbsftwppdtplgns_options ) )
			$bstwbsftwppdtplgns_options = ( function_exists( 'is_multisite' ) && is_multisite() ) ? get_site_option( 'bstwbsftwppdtplgns_options' ) : get_option( 'bstwbsftwppdtplgns_options' );
		if ( ! isset( $bstwbsftwppdtplgns_options['deprecated_function']['bws_go_pro_tab'] ) ) {
			$bstwbsftwppdtplgns_options['deprecated_function']['bws_go_pro_tab'] = array(
				'product-name' => $plugin_info['Name']
			);
			if ( is_multisite() )
				update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
			else
				update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
		}
	}
}
/**
* Function add BWS Plugins page
* @deprecated 1.9.8 (15.12.2016)
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

			add_menu_page( 'BWS Panel', 'BWS Panel', 'manage_options', 'bws_panel', 'bws_add_menu_render', 'none', '1001' );

			add_submenu_page( 'bws_panel', __( 'Plugins', 'bestwebsoft' ), __( 'Plugins', 'bestwebsoft' ), 'manage_options', 'bws_panel', 'bws_add_menu_render' );
			add_submenu_page( 'bws_panel', __( 'Themes', 'bestwebsoft' ), __( 'Themes', 'bestwebsoft' ), 'manage_options', 'bws_themes', 'bws_add_menu_render' );
			add_submenu_page( 'bws_panel', __( 'System Status', 'bestwebsoft' ), __( 'System Status', 'bestwebsoft' ), 'manage_options', 'bws_system_status', 'bws_add_menu_render' );

			$bws_general_menu_exist = true;
		}
	}
}
/**
* Function check license key for Pro plugins version
* @deprecated 1.9.8 (15.12.2016)
* @todo add notice and remove functional after 01.01.2018. Remove function after 01.01.2019
*/
if ( ! function_exists( 'bws_check_pro_license' ) ) {
	function bws_check_pro_license( $plugin_basename, $trial_plugin = false ) {
		global $wp_version, $bstwbsftwppdtplgns_options;
		$result = array();

		if ( isset( $_POST['bws_license_submit'] ) && check_admin_referer( $plugin_basename, 'bws_license_nonce_name' ) ) {
			$license_key = isset( $_POST['bws_license_key'] ) ? stripslashes( esc_html( trim( $_POST['bws_license_key'] ) ) ) : '';

			if ( '' != $license_key ) {

				delete_transient( 'bws_plugins_update' );

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
						$result['error'] = __( 'Something went wrong. Please try again later. If the error appears again, please contact us', 'bestwebsoft' ) . ' <a href=https://support.bestwebsoft.com>BestWebSoft</a>. ' . __( 'We are sorry for inconvenience.', 'bestwebsoft' );
					} else {
						$response = maybe_unserialize( wp_remote_retrieve_body( $raw_response ) );
						if ( is_array( $response ) && !empty( $response ) ) {
							foreach ( $response as $key => $value ) {
								if ( "wrong_license_key" == $value->package ) {
									$result['error'] = __( 'Wrong license key.', 'bestwebsoft' );
								} else if ( "wrong_domain" == $value->package ) {
									$result['error'] = __( 'This license key is bound to another site.', 'bestwebsoft' );
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

									if ( ! empty( $value->time_out ) )
										$result['message'] .= ' ' . __( 'Your license will expire on', 'bestwebsoft' ) . ' ' . $value->time_out . '.';

									if ( isset( $value->trial ) && $trial_plugin != false )
										$result['message'] .= ' ' . sprintf( __( 'In order to continue using the plugin it is necessary to buy a %s license.', 'bestwebsoft' ), '<a href="https://bestwebsoft.com/products/wordpress/plugins/' . $trial_plugin['link_slug'] . '/?k=' . $trial_plugin['link_key'] . '&pn=' . $trial_plugin['link_pn'] . '&v=' . $trial_plugin['plugin_info']['Version'] . '&wp_v=' . $wp_version . '" target="_blank" title="' . $trial_plugin['plugin_info']['Name'] . '">Pro</a>' );

									if ( isset( $value->trial ) ) {
										$bstwbsftwppdtplgns_options['trial'][ $plugin_basename ] = 1;
									} else {
										unset( $bstwbsftwppdtplgns_options['trial'][ $plugin_basename ] );
									}
								}
								if ( empty( $result['error'] ) ) {
									if ( isset( $value->nonprofit ) ) {
										$bstwbsftwppdtplgns_options['nonprofit'][ $plugin_basename ] = 1;
									} else {
										unset( $bstwbsftwppdtplgns_options['nonprofit'][ $plugin_basename ] );
									}
									
									if ( $bstwbsftwppdtplgns_options[ $plugin_basename ] != $license_key ) {
										$bstwbsftwppdtplgns_options[ $plugin_basename ] = $license_key;

										$file = @fopen( dirname( dirname( __FILE__ ) ) . "/license_key.txt" , "w+" );
										if ( $file ) {
											@fwrite( $file, $license_key );
											@fclose( $file );
										}
										$update_option = true;
									}

									if ( ! isset( $bstwbsftwppdtplgns_options['time_out'][ $plugin_basename ] ) || $bstwbsftwppdtplgns_options['time_out'][ $plugin_basename ] != $value->time_out ) {
										$bstwbsftwppdtplgns_options['time_out'][ $plugin_basename ] = $value->time_out;
										$update_option = true;
									}

									if ( isset( $update_option ) ) {
										if ( is_multisite() )
											update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
										else
											update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
									}
								}
							}
						} else {
							$result['error'] = __( 'Something went wrong. Please try again later. If the error appears again, please contact us', 'bestwebsoft' ) . ' <a href=https://support.bestwebsoft.com>BestWebSoft</a>. ' . __( 'We are sorry for inconvenience.', 'bestwebsoft' );
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


/**
* Function display block for checking license key for Pro plugins version
* @deprecated 1.9.8 (15.12.2016)
* @todo add notice and remove functional after 01.01.2018. Remove function after 01.01.2019
*/
if ( ! function_exists ( 'bws_check_pro_license_form' ) ) {
	function bws_check_pro_license_form( $plugin_basename ) {
		global $bstwbsftwppdtplgns_options;
		$license_key = ( isset( $bstwbsftwppdtplgns_options[ $plugin_basename ] ) ) ? $bstwbsftwppdtplgns_options[ $plugin_basename ] : ''; ?>
		<div class="clear"></div>
		<form method="post" action="">
			<p><?php echo _e( 'If necessary, you can check if the license key is correct or reenter it in the field below. You can find your license key on your personal page - Client Area - on our website', 'bestwebsoft' ) . ' <a href="https://bestwebsoft.com/client-area">https://bestwebsoft.com/client-area</a> ' . __( '(your username is the email address specified during the purchase). If necessary, please submit "Lost your password?" request.', 'bestwebsoft' ); ?></p>
			<p>
				<input type="text" maxlength="100" name="bws_license_key" value="<?php echo $license_key; ?>" />
				<input type="hidden" name="bws_license_submit" value="submit" />
				<input type="submit" class="button" value="<?php _e( 'Check license key', 'bestwebsoft' ) ?>" />
				<?php wp_nonce_field( $plugin_basename, 'bws_license_nonce_name' ); ?>
			</p>
		</form>
	<?php }
}

/**
* Function process submit on the `Go Pro` tab for TRIAL
* @deprecated 1.9.8 (15.12.2016)
* @todo add notice and remove functional after 01.01.2018. Remove function after 01.01.2019
*/
if ( ! function_exists( 'bws_go_pro_from_trial_tab' ) ) {
	function bws_go_pro_from_trial_tab( $plugin_info, $plugin_basename, $page, $link_slug, $link_key, $link_pn, $trial_license_is_set = true ) {
		global $wp_version, $bstwbsftwppdtplgns_options;
		$bws_license_key = ( isset( $_POST['bws_license_key'] ) ) ? stripslashes( esc_html( trim( $_POST['bws_license_key'] ) ) ) : "";
		if ( $trial_license_is_set ) { ?>
			<form method="post" action="">
				<p>
					<?php echo sprintf( __( 'In order to continue using the plugin it is necessary to buy a %s license.', 'bestwebsoft' ), '<a href="https://bestwebsoft.com/products/wordpress/plugins/' . $link_slug . '/?k=' . $link_key . '&amp;pn=' . $link_pn . '&amp;v=' . $plugin_info["Version"] . '&amp;wp_v=' . $wp_version .'" target="_blank" title="' . $plugin_info["Name"] . '">Pro</a>' ) . ' ';
					_e( 'After that, you can activate it by entering your license key.', 'bestwebsoft' ); ?><br />
					<span class="bws_info">
						<?php _e( 'License key can be found in the', 'bestwebsoft' ); ?>
						<a href="https://bestwebsoft.com/wp-login.php">Client Area</a>
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
			<p><?php _e( "Congratulations! The Pro license of the plugin is activated successfully.", 'bestwebsoft' ); ?></p>
			<p>
				<?php _e( "Please, go to", 'bestwebsoft' ); ?> <a href="admin.php?page=<?php echo $page; ?>"><?php _e( 'the setting page', 'bestwebsoft' ); ?></a>
				(<?php _e( "You will be redirected automatically in 5 seconds.", 'bestwebsoft' ); ?>)
			</p>
		<?php }
	}
}

/**
* Function process submit on the `Go Pro` tab
* @deprecated 1.9.8 (15.12.2016)
* @todo add notice and remove functional after 01.01.2018. Remove function after 01.01.2019
*/
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
								'timeout' => ( ( defined( 'DOING_CRON' ) && DOING_CRON ) ? 30 : 3 ),
								'body' => array( 'plugins' => serialize( $to_send ) ),
								'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ) );
							$raw_response = wp_remote_post( 'http://bestwebsoft.com/wp-content/plugins/paid-products/plugins/update-check/1.0/', $options );

							if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) ) {
								$result['error'] = __( "Something went wrong. Please try again later. If the error appears again, please contact us", 'bestwebsoft' ) . ' <a href="https://support.bestwebsoft.com">BestWebSoft</a>. ' . __( "We are sorry for inconvenience.", 'bestwebsoft' );
							} else {
								$response = maybe_unserialize( wp_remote_retrieve_body( $raw_response ) );
								if ( is_array( $response ) && !empty( $response ) ) {
									foreach ( $response as $key => $value ) {
										if ( "wrong_license_key" == $value->package ) {
											$result['error'] = __( "Wrong license key.", 'bestwebsoft' );
										} elseif ( "wrong_domain" == $value->package ) {
											$result['error'] = __( "This license key is bound to another site.", 'bestwebsoft' );
										} elseif ( "you_are_banned" == $value->package ) {
											$result['error'] = __( "Unfortunately, you have exceeded the number of available tries per day. Please, upload the plugin manually.", 'bestwebsoft' );
										} elseif ( "time_out" == $value->package ) {
											$result['error'] = sprintf( __( "Unfortunately, Your license has expired. To continue getting top-priority support and plugin updates, you should extend it in your %s", 'bestwebsoft' ), ' <a href="https://bestwebsoft.com/client-area">Client Area</a>' );
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
											if ( is_multisite() && is_plugin_active_for_network( $plugin_basename ) ) {
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
							if ( is_multisite() && is_plugin_active_for_network( $plugin_basename ) ) {
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

					if ( ! empty( $result['pro_plugin_is_activated'] ) )
						delete_transient( 'bws_plugins_update' );
				}
			} else {
				$result['error'] = __( "Please, enter Your license key", 'bestwebsoft' );
			}
		}
		return $result;
	}
}

/**
* Function display block for restoring default product settings
* @deprecated 1.9.8 (15.12.2016)
* @todo add notice and remove functional after 01.01.2018. Remove function after 01.01.2019
*/
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

/**
* Function display 'Custom code' tab
*
* @deprecated 1.9.8 (15.12.2016)
* @todo add notice and remove functional after 01.01.2018. Remove function after 01.01.2019
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
						printf( __( 'This PHP code will be hooked to the %s action and will be printed on front end only.', 'bestwebsoft' ), '<a href="http://codex.wordpress.org/Plugin_API/Action_Reference/init" target="_blank"><code>init</code></a>' ); ?>
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

/**
* Function display GO PRO tab
* @deprecated 1.9.8 (15.12.2016)
* @todo add notice and remove functional after 01.01.2018. Remove function after 01.01.2019
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
			<p><?php _e( "Congratulations! Pro version of the plugin is  installed and activated successfully.", 'bestwebsoft' ); ?></p>
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
					<a href="https://bestwebsoft.com/products/wordpress/plugins/<?php echo $link_slug; ?>/?k=<?php echo $link_key; ?>&amp;pn=<?php echo $link_pn; ?>&amp;v=<?php echo $plugin_info["Version"]; ?>&amp;wp_v=<?php echo $wp_version; ?>" target="_blank" title="<?php echo $plugin_info["Name"]; ?> Pro">Pro</a>
					<?php _e( 'version of the plugin.', 'bestwebsoft' ); ?><br />
					<span class="bws_info">
						<?php _e( 'License key can be found in the', 'bestwebsoft' ); ?>
						<a href="https://bestwebsoft.com/wp-login.php">Client Area</a>
						<?php _e( '(your username is the email address specified during the purchase).', 'bestwebsoft' ); ?>
					</span>
				</p>
				<?php if ( $trial_days_number !== false )
					$trial_days_number = __( 'or', 'bestwebsoft' ) . ' <a href="https://bestwebsoft.com/products/wordpress/plugins/' . $link_slug . '/trial/" target="_blank">' . sprintf( __( 'Start Your Free %s-Day Trial Now', 'bestwebsoft' ), $trial_days_number ) . '</a>';
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