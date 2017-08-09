<?php
/*
* General functions for BestWebSoft plugins
*/

require( dirname( __FILE__ ) . '/deprecated.php' );

/**
 * Function to add 'bestwebsoft' slug for BWS_Menu MO file if BWS_Menu loaded from theme.
 *
 * @since 1.9.7
 */
if ( ! function_exists ( 'bws_get_mofile' ) ) {
	function bws_get_mofile( $mofile, $domain ) {
		if ( 'bestwebsoft' == $domain ) {
			$locale = get_locale();
			return str_replace( $locale, "bestwebsoft-{$locale}", $mofile );
		}

		return $mofile;
	}
}

/* Internationalization, first(!) */
if ( isset( $bws_menu_source ) && 'themes' == $bws_menu_source ) {
	add_filter( 'load_textdomain_mofile', 'bws_get_mofile', 10, 2 );
	load_theme_textdomain( 'bestwebsoft', get_stylesheet_directory() . '/inc/bws_menu/languages' );
	remove_filter( 'load_textdomain_mofile', 'bws_get_mofile' );
} else {
	load_plugin_textdomain( 'bestwebsoft', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

/**
 * Function to getting url to current BWS_Menu.
 *
 * @since 1.9.7
 */
if ( ! function_exists ( 'bws_menu_url' ) ) {
	if ( ! isset( $bws_menu_source ) || 'plugins' == $bws_menu_source ) {
		function bws_menu_url( $path = '' ) {
			return plugins_url( $path, __FILE__ );
		}
	} else {
		function bws_menu_url( $path = '' ) {
			$bws_menu_current_dir = str_replace( '\\', '/', dirname( __FILE__ ) );
			$bws_menu_abspath = str_replace( '\\', '/', ABSPATH );
			$bws_menu_current_url = site_url( str_replace( $bws_menu_abspath, '', $bws_menu_current_dir ) );

			return sprintf( '%s/%s', $bws_menu_current_url, $path );
		}
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

if ( ! function_exists( 'bws_plugin_reviews_block' ) ) {
	function bws_plugin_reviews_block( $plugin_name, $plugin_slug ) { ?>
		<div class="bws-plugin-reviews">
			<div class="bws-plugin-reviews-rate">
				<?php _e( 'Like the plugin?', 'bestwebsoft' ); ?>
				<a href="http://wordpress.org/support/view/plugin-reviews/<?php echo $plugin_slug; ?>?filter=5" target="_blank" title="<?php echo $plugin_name; ?> reviews">
					<?php _e( 'Rate it', 'bestwebsoft' ); ?> 
					<span class="dashicons dashicons-star-filled"></span>
					<span class="dashicons dashicons-star-filled"></span>
					<span class="dashicons dashicons-star-filled"></span>
					<span class="dashicons dashicons-star-filled"></span>
					<span class="dashicons dashicons-star-filled"></span>
				</a>
			</div>
			<div class="bws-plugin-reviews-support">
				<?php _e( 'Need help?', 'bestwebsoft' ); ?>
				<a href="https://support.bestwebsoft.com"><?php _e( 'Visit Help Center', 'bestwebsoft' ); ?></a>
			</div>
			<div class="bws-plugin-reviews-donate">
				<?php _e( 'Want to support the plugin?', 'bestwebsoft' ); ?>
				<a href="https://bestwebsoft.com/donate/"><?php _e( 'Donate', 'bestwebsoft' ); ?></a>
			</div>
		</div>
	<?php }
}

if ( ! function_exists ( 'bws_plugin_update_row' ) ) {
	function bws_plugin_update_row( $plugin_key, $link_slug = false, $free_plugin_name = false ) {
		global $bstwbsftwppdtplgns_options, $wp_version;
		$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
		if ( isset( $bstwbsftwppdtplgns_options['wrong_license_key'][ $plugin_key ] ) ) {
			$explode_plugin_key = explode( '/', $plugin_key );
			$class = ( $wp_version >= 4.6 ) ? 'active' : '';
			$style = ( $wp_version < 4.6 ) ? ' style="background-color: #FFEBE8;border-color: #CC0000;"' : '';
			$div_class = ( $wp_version >= 4.6 ) ? ' notice inline notice-warning notice-alt' : '';
			echo '<tr class="bws-plugin-update-tr plugin-update-tr ' . $class . '" id="' . $explode_plugin_key[0] . '-update" data-slug="' . $explode_plugin_key[0] . '" data-plugin="' . $plugin_key . '">
					<td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange">
						<div class="update-message' . $div_class . '"' . $style . '>';
						if ( $wp_version >= 4.6 )
							echo '<p>';
						echo '<strong>' . __( 'WARNING: Illegal use notification', 'bestwebsoft' ) . '.</strong> ' . __( 'You can use one license of the Pro plugin for one domain only. Please check and edit your license or domain if necessary using you personal Client Area. We strongly recommend you to solve the problem within 24 hours, otherwise the Pro plugin will be deactivated.', 'bestwebsoft' ) . ' <a target="_blank" href="https://support.bestwebsoft.com/hc/en-us/articles/204240089">' . __( 'Learn More', 'bestwebsoft' ) . '</a>';
						if ( $wp_version >= 4.6 )
							echo '</p>';
						echo '</div>
					</td>
				</tr>';
		} elseif ( isset( $bstwbsftwppdtplgns_options['time_out'][ $plugin_key ] ) && strtotime( $bstwbsftwppdtplgns_options['time_out'][ $plugin_key ] ) < strtotime( date("m/d/Y") ) ) {
			$explode_plugin_key = explode( '/', $plugin_key );
			$class = ( $wp_version >= 4.6 ) ? 'active' : '';
			$style = ( $wp_version < 4.6 ) ? ' style="color: #8C0000;"' : '';
			$div_class = ( $wp_version >= 4.6 ) ? ' notice inline notice-warning notice-alt' : '';
			echo '<tr class="bws-plugin-update-tr plugin-update-tr ' . $class . '" id="' . $explode_plugin_key[0] . '-update" data-slug="' . $explode_plugin_key[0] . '" data-plugin="' . $plugin_key . '">
					<td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange">
						<div class="update-message' . $div_class . '"' . $style . '>';
						if ( $wp_version >= 4.6 )
							echo '<p>';
						if ( isset( $bstwbsftwppdtplgns_options['trial'][ $plugin_key ] ) && $link_slug != false ) {
							echo __( 'Notice: Your Pro Trial license has expired. To continue using the plugin, you should buy a Pro license', 'bestwebsoft' ) . ' - <a href="https://bestwebsoft.com/products/wordpress/plugins/' . $link_slug .'/">https://bestwebsoft.com/products/wordpress/plugins/' . $link_slug . '/</a>';
						} else {
							echo __( 'Your license has expired. To continue getting top-priority support and plugin updates, you should extend it.', 'bestwebsoft' ) . ' <a target="_new" href="https://support.bestwebsoft.com/entries/53487136">' . __( "Learn more", 'bestwebsoft' ) . '</a>';
						}
						if ( $wp_version >= 4.6 )
							echo '</p>';
					echo '</div>
					</td>
				</tr>';
		} elseif ( isset( $bstwbsftwppdtplgns_options['trial'][ $plugin_key ] ) ) {
			$explode_plugin_key = explode( '/', $plugin_key );
			$class = ( $wp_version >= 4.6 ) ? 'active' : '';
			$style = ( $wp_version < 4.6 ) ? ' style="color: #8C0000;"' : '';
			$div_class = ( $wp_version >= 4.6 ) ? ' notice inline notice-warning notice-alt' : '';
			echo '<tr class="bws-plugin-update-tr plugin-update-tr ' . $class . '" id="' . $explode_plugin_key[0] . '-update" data-slug="' . $explode_plugin_key[0] . '" data-plugin="' . $plugin_key . '">
					<td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange">
						<div class="update-message' . $div_class . '"' . $style . '>';
						if ( $wp_version >= 4.6 )
							echo '<p>';
						if ( $free_plugin_name != false ) {
							echo sprintf( __( 'Notice: You are using the Pro Trial license of %s plugin.', 'bestwebsoft' ), $free_plugin_name );
						} else {
							_e( 'Notice: You are using the Pro Trial license of plugin.', 'bestwebsoft' );
						}
						if ( isset( $bstwbsftwppdtplgns_options['time_out'][ $plugin_key ] ) )
							echo ' ' . __( "The Pro Trial license will expire on", 'bestwebsoft' ) . ' ' . $bstwbsftwppdtplgns_options['time_out'][ $plugin_key ] . '.';
						if ( $wp_version >= 4.6 )
							echo '</p>';
					echo '</div>
					</td>
				</tr>';
		}
	}
}

if ( ! function_exists( 'bws_admin_notices' ) ) {
	function bws_admin_notices() {
		global $bws_versions_notice_array, $bws_plugin_banner_to_settings, $bstwbsftwppdtplgns_options;

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
							<strong><?php printf( __( 'Thank you for installing %s plugin!', 'bestwebsoft' ), $bws_plugin_banner_to_settings[0]['plugin_info']['Name'] ); ?> </strong><br />
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

		/**
		 * show notices about deprecated_function
		 * @since 1.9.8
		*/
		if ( ! empty( $bstwbsftwppdtplgns_options['deprecated_function'] ) ) { ?>
			<div class="update-nag">
				<strong><?php _e( 'Deprecated function(-s) is used on the site here:', 'bestwebsoft' ); ?></strong>  
				<?php $i = 1; 
				foreach ( $bstwbsftwppdtplgns_options['deprecated_function'] as $function_name => $attr ) {
					if ( 1 != $i )
						echo ' ,';
					if ( ! empty( $attr['product-name'] ) ) {
						echo $attr['product-name'];
					} elseif ( ! empty( $attr['file'] ) ) {
						echo $attr['file'];
					}
					unset( $bstwbsftwppdtplgns_options['deprecated_function'][ $function_name ] );
					$i++;
				} ?>.
				<br/>
				<?php _e( 'This function(-s) will be removed over time. Please update the product(-s).', 'bestwebsoft' ); ?>				
			</div>				
			<?php if ( is_multisite() )
				update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
			else
				update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
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
					echo '<script type="text/javascript" src="' . bws_menu_url( 'js/c_o_o_k_i_e.js' ) . '"></script>';
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
							<a class="button" target="_blank" href="https://bestwebsoft.com/products/wordpress/plugins/<?php echo $link_slug; ?>/?k=<?php echo $link_key; ?>&amp;pn=<?php echo $link_pn; ?>&amp;v=<?php echo $plugin_info["Version"]; ?>&amp;wp_v=<?php echo $wp_version; ?>"><?php _e( 'Learn More', 'bestwebsoft' ); ?></a>
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

if ( ! function_exists ( 'bws_plugin_banner_timeout' ) ) {
	function bws_plugin_banner_timeout( $plugin_key, $plugin_prefix, $plugin_name, $banner_url = false ) {
		global $bstwbsftwppdtplgns_options, $bstwbsftwppdtplgns_cookie_add;
		if ( isset( $bstwbsftwppdtplgns_options['time_out'][ $plugin_key ] ) && ( strtotime( $bstwbsftwppdtplgns_options['time_out'][ $plugin_key ] ) < strtotime( date("m/d/Y") . '+1 month' ) ) && ( strtotime( $bstwbsftwppdtplgns_options['time_out'][ $plugin_key ] ) > strtotime( date("m/d/Y") ) ) ) {
			if ( ! isset( $bstwbsftwppdtplgns_cookie_add ) ) {
				echo '<script type="text/javascript" src="' . bws_menu_url( 'js/c_o_o_k_i_e.js' ) . '"></script>';
				$bstwbsftwppdtplgns_cookie_add = true;
			} ?>
			<script type="text/javascript">
				(function($) {
					$(document).ready( function() {
						var hide_message = $.cookie( "<?php echo $plugin_prefix; ?>_timeout_hide_banner_on_plugin_page" );
						if ( hide_message == "true" ) {
							$( ".<?php echo $plugin_prefix; ?>_message_timeout" ).css( "display", "none" );
						} else {
							$( ".<?php echo $plugin_prefix; ?>_message_timeout" ).css( "display", "block" );
						}
						$( ".<?php echo $plugin_prefix; ?>_close_icon" ).click( function() {
							$( ".<?php echo $plugin_prefix; ?>_message_timeout" ).css( "display", "none" );
							$.cookie( "<?php echo $plugin_prefix; ?>_timeout_hide_banner_on_plugin_page", "true", { expires: 30 } );
						});
					});
				})(jQuery);
			</script>
			<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
				<div class="<?php echo $plugin_prefix; ?>_message_timeout bws_banner_on_plugin_page bws_banner_timeout" style="display:none;">
					<button class="<?php echo $plugin_prefix; ?>_close_icon close_icon notice-dismiss bws_hide_settings_notice" title="<?php _e( 'Close notice', 'bestwebsoft' ); ?>"></button>
					<div class="icon">
						<img title="" src="<?php echo $banner_url; ?>" alt="" />
					</div>
					<div class="text"><?php printf( __( "Your license key for %s expires on %s and you won't be granted TOP-PRIORITY SUPPORT or UPDATES.", 'bestwebsoft' ), '<strong>' . $plugin_name . '</strong>', $bstwbsftwppdtplgns_options['time_out'][ $plugin_key ] ); ?> <a target="_new" href="https://support.bestwebsoft.com/entries/53487136"><?php _e( "Learn more", 'bestwebsoft' ); ?></a></div>
				</div>
			</div>
		<?php }
	}
}

if ( ! function_exists( 'bws_plugin_banner_to_settings' ) ) {
	function bws_plugin_banner_to_settings( $plugin_info, $plugin_options_name, $banner_url_or_slug, $settings_url, $post_type_url = false ) {
		global $bws_plugin_banner_to_settings;

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
					<strong><?php printf( __( 'Thank you for choosing %s plugin!', 'bestwebsoft' ), $plugin_info['Name'] ); ?></strong><br />
					<?php _e( "If you have a feature, suggestion or idea you'd like to see in the plugin, we'd love to hear about it!", 'bestwebsoft' ); ?>
					<a target="_blank" href="https://support.bestwebsoft.com/hc/en-us/requests/new"><?php _e( 'Suggest a Feature', 'bestwebsoft' ); ?></a>
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
				'message' => __( 'You can always look at premium options by checking the "Pro Options" in the "Misc" tab.', 'bestwebsoft' ),
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
			/**
			* @deprecated 1.9.8 (15.12.2016)
			*/
			$is_main_page = in_array( $_GET['page'], array( 'bws_panel', 'bws_themes', 'bws_system_status' ) );
			$page = esc_attr( $_GET['page'] );
			$tab = isset( $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : '';

			if ( $is_main_page )
				$current_page = 'admin.php?page=' . $page;
			else
				$current_page = isset( $_GET['tab'] ) ? 'admin.php?page=' . $page . '&tab=' . $tab : 'admin.php?page=' . $page;
			/*end deprecated */

			wp_redirect( self_admin_url( $current_page . '&activate=true' ) );
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
		global $wp_scripts;

		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.12.1';

		wp_enqueue_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.min.css', array(), $jquery_version );
		wp_enqueue_style( 'bws-admin-css', bws_menu_url( 'css/general_style.css' ) );
		wp_enqueue_script( 'bws-admin-scripts', bws_menu_url( 'js/general_script.js' ), array( 'jquery', 'jquery-ui-tooltip' ) );

		if ( isset( $_GET['page'] ) && ( in_array( $_GET['page'], array( 'bws_panel', 'bws_themes', 'bws_system_status' ) ) || strpos( $_GET['page'], '-bws-panel' ) ) ) {
			wp_enqueue_style( 'bws_menu_style', bws_menu_url( 'css/style.css' ) );
			wp_enqueue_script( 'bws_menu_script', bws_menu_url( 'js/bws_menu.js' ) );
			wp_enqueue_script( 'theme-install' );
			add_thickbox();
			wp_enqueue_script( 'plugin-install' );
		}
	}
}

/**
* add styles and scripts for Bws_Settings_Tabs
*
* @since 1.9.8
*/
if ( ! function_exists( 'bws_enqueue_settings_scripts' ) ) {
	function bws_enqueue_settings_scripts() {
		wp_enqueue_script( 'jquery-ui-resizable' );
		wp_enqueue_script( 'jquery-ui-tabs' );
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
		if ( 4.2 > $wp_version ) { ?>
			<style type="text/css">
				.bws_hide_settings_notice,
				.bws_hide_premium_options {
					width: 11px;
					height: 11px;
					border: none;
					background: url("<?php echo bws_menu_url( 'images/close_banner.png' ); ?>") no-repeat center center;
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
		wp_enqueue_style( 'codemirror.css', bws_menu_url( 'css/codemirror.css' ) );
		wp_enqueue_script( 'codemirror.js', bws_menu_url( 'js/codemirror.js' ), array( 'jquery' ) );
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
				echo '<script type="text/javascript" src="' . bws_menu_url( 'js/bws_tooltip.js' ) . '"></script>';
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

if ( ! function_exists ( 'bws_form_restore_default_confirm' ) ) {
	function bws_form_restore_default_confirm( $plugin_basename ) { ?>
		<div>
			<p><?php _e( 'Are you sure you want to restore default settings?', 'bestwebsoft' ) ?></p>
			<form method="post" action="">
				<p>
					<button class="button button-primary" name="bws_restore_confirm"><?php _e( 'Yes, restore all settings', 'bestwebsoft' ) ?></button>
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
		global $bws_shortcode_list;
		if ( ! empty( $bws_shortcode_list ) && current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
			add_filter( 'mce_external_plugins', 'bws_add_buttons' );
			add_filter( 'mce_buttons', 'bws_register_buttons' );
		}
	}
}

if ( ! function_exists( 'bws_add_buttons' ) ){
	function bws_add_buttons( $plugin_array ) {
		$plugin_array['add_bws_shortcode'] = bws_menu_url( 'js/shortcode-button.js' );
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

/** 
 * output shortcode in a special block
 * @since 1.9.8
 */
if ( ! function_exists( 'bws_shortcode_output' ) ) {
	function bws_shortcode_output( $shortcode ) { ?>
		<span class="bws_shortcode_output"><input type="text" onfocus="this.select();" readonly="readonly" value="<?php echo $shortcode; ?>" class="large-text bws_no_bind_notice"></span>
	<?php }
}

/** 
 * output tooltip
 * @since 1.9.8
 */
if ( ! function_exists( 'bws_add_help_box' ) ) {
	function bws_add_help_box( $content, $class = '' ) {
		return '<span class="bws_help_box dashicons dashicons-editor-help ' . $class . ' hide-if-no-js">
			<span class="bws_hidden_help_text">' . $content . '</span>
		</span>';
	}
}

/* add help tab  */
if ( ! function_exists( 'bws_help_tab' ) ) {
	function bws_help_tab( $screen, $args ) {
		$url = ( ! empty( $args['section'] ) ) ? 'https://support.bestwebsoft.com/hc/en-us/sections/' . $args['section'] : 'https://support.bestwebsoft.com/';

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
			'<p><a href="https://support.bestwebsoft.com/hc/en-us/requests/new" target="_blank">' . __( 'Submit a Request', 'bestwebsoft' ) . '</a></p>'
		);
	}
}

if ( ! function_exists( 'bws_enqueue_custom_code_css_js' ) ) {
	function bws_enqueue_custom_code_css_js() {
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

			if ( ! $is_multisite && ! empty( $bstwbsftwppdtplgns_options['custom_code']['bws-custom-code.js'] ) )
				wp_enqueue_script( 'bws-custom-style', $bstwbsftwppdtplgns_options['custom_code']['bws-custom-code.js'] );
			elseif ( $is_multisite && ! empty( $bstwbsftwppdtplgns_options['custom_code'][ $blog_id ]['bws-custom-code.js'] ) )
				wp_enqueue_script( 'bws-custom-style', $bstwbsftwppdtplgns_options['custom_code'][ $blog_id ]['bws-custom-code.js'] );
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
					if ( ! defined( 'BWS_GLOBAL' ) )
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
					if ( ! defined( 'BWS_GLOBAL' ) )
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
		/* remove track usage data */
		if ( isset( $bstwbsftwppdtplgns_options['bws_menu']['track_usage']['products'][ $basename ] ) )
			unset( $bstwbsftwppdtplgns_options['bws_menu']['track_usage']['products'][ $basename ] );
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

add_action( 'wp_enqueue_scripts', 'bws_enqueue_custom_code_css_js', 20 );

bws_enqueue_custom_code_php();