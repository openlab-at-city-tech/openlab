<?php
/*
* General functions for BestWebSoft plugins
* Version: 1.1.2
*/

if ( ! function_exists ( 'bws_add_general_menu' ) ) {
	function bws_add_general_menu() {
		add_menu_page( 'BWS Plugins', 'BWS Plugins', 'manage_options', 'bws_plugins', 'bws_add_menu_render', plugins_url( 'images/px.png', __FILE__ ), 1001 );
	}
}

/**
* Function check if plugin is compatible with current WP version - for old plugin version
* @return void
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
		if ( false != $min_wp && version_compare( $wp_version, $min_wp, "<" ) ) {
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

if ( ! function_exists( 'bws_versions_notice' ) ) {
	function bws_versions_notice() {
		global $bws_versions_notice_array;
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
					<div class="<?php echo $this_banner_prefix; ?>_message bws_banner_on_plugin_page" style="display: none;">
						<img class="<?php echo $this_banner_prefix; ?>_close_icon close_icon" title="" src="<?php echo plugins_url( 'images/close_banner.png', __FILE__ ); ?>" alt=""/>
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
				<a href="http://wordpress.org/support/view/plugin-reviews/<?php echo $plugin_slug; ?>" target="_blank" title="<?php echo $plugin_name; ?> reviews"><?php _e( 'Rate the plugin', 'bestwebsoft' ); ?></a>
			</div>
			<div class="bws-plugin-reviews-support">
				<?php _e( 'If there is something wrong about it, please contact us', 'bestwebsoft' ); ?>:
				<a href="http://support.bestwebsoft.com">http://support.bestwebsoft.com</a>
			</div>
		</div>
	<?php }
}

if ( ! function_exists( 'bws_go_pro_tab_check' ) ) {
	function bws_go_pro_tab_check( $plugin_basename ) {
		global $wp_version, $bstwbsftwppdtplgns_options;
		$result = array();

		$bws_license_key = ( isset( $_POST['bws_license_key'] ) ) ? stripslashes( esc_html( trim( $_POST['bws_license_key'] ) ) ) : "";

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
											$result['error'] = __( "Unfortunately, Your license has expired. To continue getting top-priority support and plugin updates you should extend it in your", 'bestwebsoft' ) . ' <a href="http://bestwebsoft.com/wp-admin/admin.php?page=bws_plugins_client_area">Client area</a>';
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

if ( ! function_exists( 'bws_go_pro_tab' ) ) {
	function bws_go_pro_tab( $plugin_info, $plugin_basename, $page, $pro_page, $bws_license_plugin, $link_slug, $link_key, $link_pn, $pro_plugin_is_activated = false, $trial_days_number = false ) {
		global $wp_version, $bstwbsftwppdtplgns_options;
		$bws_license_key = ( isset( $_POST['bws_license_key'] ) ) ? stripslashes( esc_html( trim( $_POST['bws_license_key'] ) ) ) : "";
		if ( $pro_plugin_is_activated ) { ?>
			<script type="text/javascript">
				window.setTimeout( function() {
					window.location.href = 'admin.php?page=<?php echo $pro_page; ?>';
				}, 5000 );
			</script>
			<p><?php _e( "Congratulations! The Pro version of the plugin is successfully download and activated.", 'bestwebsoft' ); ?></p>
			<p>
				<?php _e( "Please, go to", 'bestwebsoft' ); ?> <a href="admin.php?page=<?php echo $pro_page; ?>"><?php _e( 'the setting page', 'bestwebsoft' ); ?></a> 
				(<?php _e( "You will be redirected automatically in 5 seconds.", 'bestwebsoft' ); ?>)
			</p>
		<?php } else { ?>
			<form method="post" action="admin.php?page=<?php echo $page; ?>&amp;action=go_pro">
				<p>
					<?php _e( 'You can download and activate', 'bestwebsoft' ); ?> 
					<a href="http://bestwebsoft.com/products/<?php echo $link_slug; ?>/?k=<?php echo $link_key; ?>&amp;pn=<?php echo $link_pn; ?>&amp;v=<?php echo $plugin_info["Version"]; ?>&amp;wp_v=<?php echo $wp_version; ?>" target="_blank" title="<?php echo $plugin_info["Name"]; ?> Pro">Pro</a> 
					<?php _e( 'version of this plugin by entering Your license key.', 'bestwebsoft' ); ?><br />
					<span class="bws_info">
						<?php _e( 'You can find your license key on your personal page Client area, by clicking on the link', 'bestwebsoft' ); ?> 
						<a href="http://bestwebsoft.com/wp-login.php">http://bestwebsoft.com/wp-login.php</a> 
						<?php _e( '(your username is the email you specify when purchasing the product).', 'bestwebsoft' ); ?>
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
			<form method="post" action="admin.php?page=<?php echo $page; ?>&amp;action=go_pro">
				<p>
					<?php echo sprintf( __( 'In order to continue using the plugin it is necessary to buy a %s license.', 'bestwebsoft' ), '<a href="http://bestwebsoft.com/products/' . $link_slug . '/?k=' . $link_key . '&amp;pn=' . $link_pn . '&amp;v=' . $plugin_info["Version"] . '&amp;wp_v=' . $wp_version .'" target="_blank" title="' . $plugin_info["Name"] . '">Pro</a>' ) . ' ';
					_e( 'After that you can activate it by entering your license key.', 'bestwebsoft' ); ?><br />
					<span class="bws_info">
						<?php _e( 'You can find your license key on your personal page Client area, by clicking on the link', 'bestwebsoft' ); ?> 
						<a href="http://bestwebsoft.com/wp-login.php">http://bestwebsoft.com/wp-login.php</a> 
						 <?php _e( '(your username is the email you specify when purchasing the product).', 'bestwebsoft' ); ?>
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
	function bws_check_pro_license_form( $plugin_basename, $page ) {
		global $bstwbsftwppdtplgns_options;
		$license_key = ( isset( $bstwbsftwppdtplgns_options[ $plugin_basename ] ) ) ? $bstwbsftwppdtplgns_options[ $plugin_basename ] : ''; ?>
		<div class="clear"></div>
		<form method="post" action="admin.php?page=<?php echo $page; ?>">
			<p><?php echo _e( 'If needed you can check if the license key is correct or reenter it in the field below. You can find your license key on your personal page - Client area - on our website', 'bestwebsoft' ) . ' <a href="http://bestwebsoft.com/wp-login.php">http://bestwebsoft.com/wp-login.php</a> ' . __( '(your username is the email you specify when purchasing the product). If necessary, please submit "Lost your password?" request.', 'bestwebsoft' ); ?></p>
			<p>
				<input type="text" maxlength="100" name="bws_license_key" value="<?php echo $license_key; ?>" />
				<input type="hidden" name="bws_license_submit" value="submit" />
				<input type="submit" class="button-primary" value="<?php _e( 'Check license key', 'bestwebsoft' ) ?>" />
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
				<div class="<?php echo $plugin_prefix; ?>_message bws_banner_on_plugin_page" style="display:none;">
					<img class="<?php echo $plugin_prefix; ?>_close_icon close_icon" title="" src="<?php echo plugins_url( 'images/close_banner.png', __FILE__ ); ?>" alt=""/>
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
	function bws_plugin_banner_to_settings( $plugin_info, $plugin_options_name, $banner_url_or_slug, $settings_url, $post_type_url = false, $post_type_name = false ) {
		global $wp_version;

		$plugin_options = get_option( $plugin_options_name );

		if ( isset( $plugin_options['display_settings_notice'] ) && 0 == $plugin_options['display_settings_notice'] )
			return;
		
		if ( isset( $_POST['bws_hide_settings_notice_' . $plugin_options_name ] ) && check_admin_referer( $plugin_info['Name'], 'bws_settings_nonce_name' )  ) {
			$plugin_options['display_settings_notice'] = 0;
			update_option( $plugin_options_name, $plugin_options );
			return;
		}

		if ( false == strrpos( $banner_url_or_slug, '/' ) ) {
			$banner_url_or_slug = '//ps.w.org/' . $banner_url_or_slug . '/assets/icon-128x128.png';
		}

		if ( 4.2 > $wp_version ) {
			$plugin_dir_array = explode( '/', plugin_basename( __FILE__ ) );
			$plugin_dir = $plugin_dir_array[0]; ?>
			<style type="text/css">
				.bws_hide_settings_notice {
					width: 11px;
					height: 11px;
					border: none;
					background: url("<?php echo plugins_url( $plugin_dir . '/bws_menu/images/close_banner.png' ); ?>") no-repeat center center;
					box-shadow: none;
					float: right;
					margin: 8px;
				}
				.bws_hide_settings_notice:hover {
					cursor: pointer;
				}
			</style>
		<?php } ?>
		<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
			<div class="bws_banner_on_plugin_page">
				<div class="icon">
					<img title="" src="<?php echo esc_attr( $banner_url_or_slug ); ?>" alt="" />
				</div>						
				<div class="text">
					<strong><?php _e( 'Thank you for installing', 'bestwebsoft' ); ?> <?php echo $plugin_info['Name']; ?> plugin!</strong><br />
					<?php _e( "Let's get started", 'bestwebsoft' ); ?>: 
					<a target="_blank" href="<?php echo $settings_url; ?>"><?php _e( 'Configure Settings', 'bestwebsoft' ); ?></a> 
					<?php if ( false != $post_type_url && false != $post_type_name ) { ?>
						<?php _e( 'or', 'bestwebsoft' ); ?> 
						<a target="_blank" href="<?php echo $post_type_url; ?>"><?php _e( 'Add New', 'bestwebsoft' ); ?> <?php echo $post_type_name; ?></a>
					<?php } ?>
				</div>
				<form action="" method="post">
					<button class="notice-dismiss bws_hide_settings_notice" title="<?php _e( 'Close notice', 'bestwebsoft' ); ?>"></button>
					<input type="hidden" name="bws_hide_settings_notice_<?php echo $plugin_options_name; ?>" value="hide" />
					<?php wp_nonce_field( $plugin_info['Name'], 'bws_settings_nonce_name' ); ?>
				</form>
			</div>
		</div>
	<?php }
}

if ( ! function_exists( 'bws_show_settings_notice' ) ) {
	function bws_show_settings_notice() { ?>
		<div id="bws_save_settings_notice" class="updated fade" style="display:none">
			<p>
				<strong><?php _e( 'Notice', 'bestwebsoft' ); ?></strong>: <?php _e( "The plugin's settings have been changed.", 'bestwebsoft' ); ?> 
				<a class="bws_save_anchor" href="#bws-submit-button"><?php _e( 'Save Changes', 'bestwebsoft' ); ?></a>
			</p>
		</div>
	<?php }
}

if ( ! function_exists ( 'bws_plugins_admin_init' ) ) {
	function bws_plugins_admin_init() {
		/* Internationalization, first(!) */
		load_plugin_textdomain( 'bestwebsoft', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		bws_add_editor_buttons();
	}
}

if ( ! function_exists ( 'bws_admin_enqueue_scripts' ) ) {
	function bws_admin_enqueue_scripts() {
		global $wp_version;
		wp_enqueue_style( 'bws-admin-css', plugins_url( 'css/general_style.css', __FILE__ ) );
		wp_enqueue_script( 'bws-admin-scripts', plugins_url( 'js/general_script.js', __FILE__ ), array( 'jquery' ) );

		if ( isset( $_GET['page'] ) && $_GET['page'] == "bws_plugins" ) {
			wp_enqueue_style( 'bws_menu_style', plugins_url( 'css/style.css', __FILE__ ) );
			wp_enqueue_script( 'bws_menu_script', plugins_url( 'js/bws_menu.js' , __FILE__ ) );
			wp_enqueue_script( 'theme-install' );
		}
	}
}

if ( ! function_exists ( 'bws_plugins_admin_head' ) ) {
	function bws_plugins_admin_head() {
		global $bws_shortcode_list, $wp_version;
		if ( isset( $_GET['page'] ) && $_GET['page'] == "bws_plugins" ) { ?>
			<noscript>
				<style type="text/css">
					.bws_product_button {
						display: inline-block;
					}
				</style>
			</noscript>
		<?php } 
		if ( ! empty( $bws_shortcode_list ) ) { ?>
			<!-- TinyMCE Shortcode Plugin -->
			<script type='text/javascript'>
				var bws_shortcode_button = {
					'title': '<?php _e( "Add BWS Plugins Shortcode", "bestwebsoft" ); ?>',
					'function_name': [
						<?php foreach ( $bws_shortcode_list as $key => $value ) {
							if ( isset( $value['js_function'] ) )
								echo "'" . $value['js_function'] . "',";	
						} ?>
					],
					'icon_url': '<?php echo plugins_url( "images/shortcode-icon.png" , __FILE__ ); ?>',
					'wp_version' : '<?php echo $wp_version; ?>'
				};
			</script>
			<!-- TinyMCE Shortcode Plugin -->
		<?php } 
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
	function bws_form_restore_default_settings( $plugin_basename ) { ?>
		<form method="post" action="">			
			<p><?php _e( 'Restore all plugin settings to defaults', 'bestwebsoft' ); ?></p>
			<p>					
				<input type="submit" class="button" value="<?php _e( 'Restore settings', 'bestwebsoft' ); ?>" />
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

add_action( 'admin_init', 'bws_plugins_admin_init' );
add_action( 'admin_enqueue_scripts', 'bws_admin_enqueue_scripts' );
add_action( 'admin_head', 'bws_plugins_admin_head' );
add_action( 'admin_footer','bws_shortcode_media_button_popup' );

add_action( 'admin_notices', 'bws_versions_notice' );