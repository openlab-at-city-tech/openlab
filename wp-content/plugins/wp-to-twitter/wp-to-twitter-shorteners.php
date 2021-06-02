<?php
/**
 * URL Shorteners WP to Twitter
 *
 * @category Core
 * @package  WP to Twitter
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/wp-to-twitter/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wpt_shorten_url' ) ) {
	// prep work for future plug-in replacement.
	add_filter( 'wptt_shorten_link', 'wpt_shorten_url', 10, 4 );

	/**
	 * Given a URL, shorten it.
	 *
	 * @param string               $url URL.
	 * @param string               $post_title Post Title.
	 * @param int                  $post_ID Post ID.
	 * @param mixed string/boolean $testmode Testing function.
	 * @param boolean              $store_urls Whether to store URL after creating.
	 *
	 * @return shortened URL.
	 */
	function wpt_shorten_url( $url, $post_title, $post_ID, $testmode = false, $store_urls = true ) {
		wpt_mail( 'Shortener running: initial link', "Url: $url, Title: $post_title, Post ID: $post_ID, Test mode: $testmode", $post_ID ); // DEBUG.
		// filter link before sending to shortener or adding analytics.
		$shortener = (string) get_option( 'jd_shortener' );
		// if the URL already exists & a shortener is enabled, return it without processing.
		if ( '3' === $shortener && wpt_short_url( $post_ID ) && $store_urls ) {
			$shrink = wpt_short_url( $post_ID );

			return $shrink;
		}
		$url = apply_filters( 'wpt_shorten_link', $url, $shortener, $post_ID );
		if ( false === $testmode ) {
			if ( '1' === get_option( 'use-twitter-analytics' ) || '1' === get_option( 'use_dynamic_analytics' ) ) {
				if ( '1' === get_option( 'use_dynamic_analytics' ) ) {
					$campaign_type = get_option( 'jd_dynamic_analytics' );
					if ( 'post_category' === $campaign_type && 'link' !== $testmode ) {
						$category = get_the_category( $post_ID );
						$campaign = sanitize_title( $category[0]->cat_name );
					} elseif ( 'post_ID' === $campaign_type ) {
						$campaign = $post_ID;
					} elseif ( 'post_title' === $campaign_type && 'link' !== $testmode ) {
						$post     = get_post( $post_ID );
						$campaign = sanitize_title( $post->post_title );
					} else {
						if ( 'link' !== $testmode ) {
							$post        = get_post( $post_ID );
							$post_author = $post->post_author;
							$campaign    = urlencode( get_the_author_meta( 'user_login', $post_author ) );
						} else {
							$campaign = '';
						}
					}
				} else {
					$campaign = get_option( 'twitter-analytics-campaign' );
				}
				$medium = urlencode( trim( apply_filters( 'wpt_utm_medium', 'twitter' ) ) );
				$source = urlencode( trim( apply_filters( 'wpt_utm_source', 'twitter' ) ) );
				$url    = add_query_arg(
					array(
						'utm_campaign' => $campaign,
						'utm_medium'   => $medium,
						'utm_source'   => $source,
					),
					$url
				);
			}
			$url     = urldecode( trim( $url ) ); // prevent double-encoding.
			$encoded = urlencode( $url );
		} else {
			$url     = urldecode( trim( $url ) ); // prevent double-encoding.
			$encoded = urlencode( $url );
		}

		// custom word setting.
		$keyword_format = ( '1' === get_option( 'jd_keyword_format' ) ) ? $post_ID : '';
		$keyword_format = ( '2' === get_option( 'jd_keyword_format' ) ) ? get_post_meta( $post_ID, '_yourls_keyword', true ) : $keyword_format;
		// Generate and grab the short url.
		$shrink = apply_filters( 'wpt_do_shortening', false, $shortener, $url, $post_title, $post_ID, $testmode );
		// if an add-on has shortened the link, skip shortening.
		$error = false;
		if ( ! $shrink ) {
			switch ( $shortener ) {
				case 3: // no shortener.
					$shrink = $url;
					break;
				case 2: // updated to v3 3/31/2010.
					// v3 is being sunsetted 3/31/2020. Option to enable removed 7/4/2019.
					$bitlyurl = get_post_meta( $post_ID, '_wbitly_shorturl', true );
					if ( ! empty( $bitlyurl ) ) {
						$shrink = $bitlyurl;
					} else {
						if ( function_exists( 'wbitly_generate_shorten_url' ) ) {
							$shrink = wbitly_generate_shorten_url( $url );
						}
						if ( function_exists( 'wbitly_shorten_url' ) && ! $shrink ) {
							$shrink = wbitly_shorten_url( $url );
						}
					}
					break;
				case 4:
					if ( function_exists( 'wp_get_shortlink' ) ) {
						// wp_get_shortlink doesn't natively support custom post types; but don't return an error in that case.
						$shrink = ( false !== $post_ID ) ? wp_get_shortlink( $post_ID, 'post' ) : $url;
					}
					if ( ! $shrink ) {
						$shrink = $url;
					}
					break;
				case 5:
					// local YOURLS installation.
					define( 'YOURLS_INSTALLING', true ); // Pretend we're installing YOURLS to bypass test for install or upgrade.
					define( 'YOURLS_FLOOD_DELAY_SECONDS', 0 ); // Disable flood check.
					$opath = get_option( 'yourlspath' );
					$ypath = str_replace( 'user', 'includes', $opath );
					if ( file_exists( dirname( $ypath ) . '/load-yourls.php' ) ) { // YOURLS 1.4+.
						require_once( dirname( $ypath ) . '/load-yourls.php' );
						global $ydb;
						if ( function_exists( 'yourls_add_new_link' ) ) {
							$yourls_result = yourls_add_new_link( $url, $keyword_format, $post_title );
						} else {
							$yourls_result = $url;
						}
					}
					if ( $yourls_result ) {
						$shrink = $yourls_result['shorturl'];
					} else {
						$shrink = false;
					}
					break;
				case 6:
					// remote YOURLS installation.
					$yourlstoken = trim( get_option( 'yourlstoken' ) );
					$yourlslogin = trim( get_option( 'yourlslogin' ) );
					$yourlsurl   = stripcslashes( get_option( 'yourlsurl' ) );
					if ( $yourlstoken && $yourlsurl ) {
						$token     = stripcslashes( $yourlstoken );
						$yourlsurl = esc_url( $yourlsurl );
						if ( $token ) {
							$args = array(
								'signature' => $token,
								'url'       => $encoded,
								'action'    => 'shorturl',
								'format'    => 'json',
								'title'     => urlencode( $post_title ),
							);
						} else {
							$args = array(
								'username' => $yourlslogin,
								'password' => $yourlsurl,
								'url'      => $encoded,
								'action'   => 'shorturl',
								'format'   => 'json',
								'title'    => urlencode( $post_title ),
							);
						}
						if ( $keyword_format ) {
							$args['keyword'] = $keyword_format;
						}

						$api_url = add_query_arg( $args, $yourlsurl );
						$json    = wpt_remote_json( $api_url, false );
						if ( is_object( $json ) ) {
							$shrink = $json->shorturl;
						} else {
							$error  = 'Error code: YOURLS response is not an object';
							$shrink = false;
						}
					}
					break;
				case 7:
					// Su.pr. Stumbleupon closed doors in June 2018.
				case 8:
					// Goo.gl. Service disabled March 2019.
				case 9:
					// Twitter Friendly Links. This plugin not updated in 8 years.
					$shrink = $url;
					break;
				case 10:
					// jotURL, added: 2013-04-10.
					$joturlapi   = trim( get_option( 'joturlapi' ) );
					$joturllogin = trim( get_option( 'joturllogin' ) );
					if ( ! empty( $joturlapi ) && ! empty( $joturllogin ) ) {
						$joturl_longurl_params = trim( get_option( 'joturl_longurl_params' ) );
						$domain                = trim( get_option( 'joturl_domain', false ) );
						if ( '' !== $joturl_longurl_params ) {
							if ( false === strpos( $url, '%3F' ) && false === strpos( $url, '?' ) ) {
								$ct = '?';
							} else {
								$ct = '&';
							}
							$url    .= $ct . $joturl_longurl_params;
							$encoded = urlencode( urldecode( trim( $url ) ) ); // prevent double-encoding.
						}
						$domain  = ( $domain ) ? '&domain=' . $domain : '';
						$decoded = wpt_fetch_url( 'https://api.joturl.com/a/v1/shorten?url=' . $encoded . '&login=' . $joturllogin . '&key=' . $joturlapi . '&format=plain' . $domain );
						if ( false !== $decoded ) {
							$shrink                 = $decoded;
							$joturl_shorturl_params = trim( get_option( 'joturl_shorturl_params' ) );
							if ( '' !== $joturl_shorturl_params ) {
								if ( false === strpos( $shrink, '%3F' ) && false === strpos( $shrink, '?' ) ) {
									$ct = '?';
								} else {
									$ct = '&';
								}
								$shrink .= $ct . $joturl_shorturl_params;
							}
						} else {
							$error  = $decoded;
							$shrink = false;
						}
						if ( ! wpt_is_valid_url( $shrink ) ) {
							$shrink = false;
						}
					}
					break;
				case 11:
					// Hum URL shortener.
					if ( class_exists( 'Hum' ) && method_exists( 'Hum', 'get_shortlink' ) ) {
						$hum    = new Hum;
						$shrink = $hum->get_shortlink( $url, $post_ID, 'post', true );
					} else {
						$shrink = $url;
					}
					break;
				default:
					$shrink = $url;
			}
		}
		if ( $error ) {
			update_option( 'wpt_shortener_status', "$shrink : $error" );
		}
		if ( ! $testmode ) {
			if ( false === $shrink || ( false === filter_var( $shrink, FILTER_VALIDATE_URL ) ) ) {
				update_option( 'wp_url_failure', '1' );
				$shrink = urldecode( $url );
			} else {
				update_option( 'wp_url_failure', '0' );
			}
		}
		$store_urls = apply_filters( 'wpt_store_url', $store_urls );
		if ( $store_urls ) {
			wpt_store_url( $post_ID, $shrink );
		}

		return $shrink;
	}

	/**
	 * Store shortened URL for re-use.
	 *
	 * @param int    $post_ID Post ID.
	 * @param string $url Shortened URL.
	 */
	function wpt_store_url( $post_ID, $url ) {
		$store_urls = apply_filters( 'wpt_store_urls', true, $post_ID, $url );
		if ( function_exists( 'wpt_shorten_url' ) && $store_urls ) {
			$shortener = get_option( 'jd_shortener' );
			// Don't store URLs if the not shortening is selected.
			if ( '3' === $shortener ) {
				return;
			}
			if ( wpt_short_url( $post_ID ) !== $url && wpt_is_valid_url( $url ) ) {
				update_post_meta( $post_ID, '_wpt_short_url', $url );
			}
			switch ( $shortener ) {
				case 5:
				case 6:
					$target = wpt_expand_yourl( $url, $shortener );
					break;
				default:
					$target = $url;
			}
		} else {
			$target = $url;
		}
		update_post_meta( $post_ID, '_wp_jd_target', $target );
	}

	/**
	 * Expand a saved YOURL URl.
	 *
	 * @param string $short_url Shortened URL.
	 * @param int    $remote Remote or local install.
	 *
	 * @return long url.
	 */
	function wpt_expand_yourl( $short_url, $remote ) {
		if ( 6 === (int) $remote ) {
			$short_url = urlencode( $short_url );
			$yourl_api = get_option( 'yourlsurl' );
			$user      = get_option( 'yourlslogin' );
			$pass      = stripslashes( get_option( 'yourlsapi' ) );
			$token     = get_option( 'yourlstoken' );
			if ( $token ) {
				$decoded = wpt_remote_json( $yourl_api . "?action=expand&shorturl=$short_url&format=json&signature=$token" );
			} else {
				$decoded = wpt_remote_json( $yourl_api . "?action=expand&shorturl=$short_url&format=json&username=$user&password=$pass" );
			}
			$url = ( isset( $decoded['longurl'] ) ) ? $decoded['longurl'] : $short_url;

			return $url;
		} else {
			define( 'YOURLS_INSTALLING', true ); // Pretend we're installing YOURLS to bypass test for install or upgrade.
			define( 'YOURLS_FLOOD_DELAY_SECONDS', 0 ); // Disable flood check.
			if ( file_exists( dirname( get_option( 'yourlspath' ) ) . '/load-yourls.php' ) ) { // YOURLS 1.4+.
				global $ydb;
				require_once( dirname( get_option( 'yourlspath' ) ) . '/load-yourls.php' );
				$yourls_result = yourls_api_expand( $short_url );
			}
			if ( $yourls_result ) {
				$url = $yourls_result['longurl'];
			} else {
				$url = $short_url;
			}
			return $url;
		}
	}

	add_filter( 'wpt_shortener_controls', 'wpt_shortener_controls' );
	/**
	 * Controls for adding shortener relevant data.
	 */
	function wpt_shortener_controls() {
		$shortener  = get_option( 'jd_shortener' );
		$admin_url  = admin_url( 'admin.php?page=wp-tweets-pro' );
		$form_start = '<div class="panel">
							<form method="post" action="' . add_query_arg( 'tab', 'shortener', $admin_url ) . '">
								<div><input type="hidden" name="wpt_shortener_update" value="true" /></div>
								<div>';
		$nonce      = wp_nonce_field( 'wp-to-twitter-nonce', '_wpnonce', true, false );
		$form_end   = '<div>' . $nonce . '</div>
								<p>
									<input type="submit" name="submit" value="' . __( 'Save URL Shortener Settings', 'wp-to-twitter' ) . '" class="button-primary" />
								</p>
							</div>
						</form>
					</div>';
		// for the moment, this just displays the fields. Eventually, a real filter.
		?>
		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h3>
					<span><?php _e( '<abbr title="Uniform Resource Locator">URL</abbr> Shortener Account Settings', 'wp-to-twitter' ); ?></span>
				</h3>

				<div class="inside">
					<?php
					if ( 7 === (int) $shortener ) {
						echo '<p>' . __( 'The Su.pr URL shortener was shut down when Stumbleupon closed doors in June 2018.', 'wp-to-twitter' ) . '</p>';
					} elseif ( 2 === (int) $shortener ) {
						if ( function_exists( 'wbitly_shorten_url' ) ) {
							echo '<p>' . __( 'WP to Twitter supports Bit.ly shortened links via <a href="https://wordpress.org/plugins/codehaveli-bitly-url-shortener/">Codehaveli Bitly URL Shortener</a>. If you are having issues with Bit.ly URLs, please request support from <a href="https://wordpress.org/support/plugin/codehaveli-bitly-url-shortener/">the plugin support forums</a>.', 'wp-to-twitter' ) . '</p>';
						} else {
							echo '<p>' . __( 'WP to Twitter supports Bit.ly shortened links via <a href="https://wordpress.org/plugins/codehaveli-bitly-url-shortener/">Codehaveli Bitly URL Shortener</a>. Install that plug-in to use Bit.ly', 'wp-to-twitter' ) . '</p>';
						}
					} elseif ( 5 === (int) $shortener || 6 === (int) $shortener ) {
						echo $form_start;
						if ( 5 === (int) $shortener ) {
							?>
						<p>
							<label for="yourlspath"><?php _e( 'Path to your YOURLS config file', 'wp-to-twitter' ); ?></label><br/>
							<input type="text" id="yourlspath" name="yourlspath" size="60" value="<?php echo esc_attr( get_option( 'yourlspath' ) ); ?>"/><br/>
							<small><?php _e( 'Example:', 'wp-to-twitter' ); ?> <code>/home/username/www/www/yourls/user/config.php</code>
							</small>
						</p>
							<?php
						}
						if ( 6 === (int) $shortener ) {
							?>
						<p>
							<label for="yourlsurl"><?php _e( 'URI to the YOURLS API', 'wp-to-twitter' ); ?></label><br/>
							<input type="text" id="yourlsurl" name="yourlsurl" size="60" value="<?php echo esc_attr( get_option( 'yourlsurl' ) ); ?>"/><br/>
							<small><?php _e( 'Example:', 'wp-to-twitter' ); ?> <code>http://domain.com/yourls-api.php</code>
							</small>
						</p>
							<?php
						}
						?>
						<p>
							<label for="yourlstoken"><?php _e( 'YOURLS signature token:', 'wp-to-twitter' ); ?></label>
							<input type="text" name="yourlstoken" id="yourlstoken" size="30" value="<?php echo esc_attr( get_option( 'yourlstoken' ) ); ?>"/>
						</p>
						<?php
						if ( get_option( 'yourlsapi' ) && get_option( 'yourlslogin' ) ) {
							?>
							<p>
								<em><?php _e( 'Your YOURLS username and password are saved. If you add a signature token, that will be used for API calls and your username and password will be deleted from the database.', 'wp-to-twitter' ); ?></em>
							</p>
							<?php
						}
						?>
						<p>
							<input type="radio" name="jd_keyword_format" id="jd_keyword_id" value="1" <?php checked( get_option( 'jd_keyword_format' ), 1 ); ?> />
							<label for="jd_keyword_id"><?php _e( 'Post ID for YOURLS url slug.', 'wp-to-twitter' ); ?></label><br/>
							<input type="radio" name="jd_keyword_format" id="jd_keyword" value="2" <?php checked( get_option( 'jd_keyword_format' ), 2 ); ?> />
							<label for="jd_keyword"><?php _e( 'Custom keyword for YOURLS url slug.', 'wp-to-twitter' ); ?></label><br/>
							<input type="radio" name="jd_keyword_format" id="jd_keyword_default" value="0" <?php checked( get_option( 'jd_keyword_format' ), 0 ); ?> />
							<label for="jd_keyword_default"><?php _e( 'Default: sequential URL numbering.', 'wp-to-twitter' ); ?></label>
						</p>
						<div>
							<input type="hidden" name="submit-type" value="yourlsapi" />
						</div>
						<?php
						echo $form_end;
					} elseif ( 8 === (int) $shortener ) {
						echo '<p>' . __( 'The Goo.gl URL shortener was shut down by Google in March 2019.', 'wp-to-twitter' ) . '</p>';
					} elseif ( 10 === (int) $shortener ) {
						echo $form_start;
						?>
						<p>
							<label for="joturllogin"><?php _e( "Your jotURL public <abbr title='application programming interface'>API</abbr> key:", 'wp-to-twitter' ); ?></label>
							<input type="text" name="joturllogin" id="joturllogin" value="<?php echo esc_attr( get_option( 'joturllogin' ) ); ?>"/>
						</p>
						<p>
							<label for="joturlapi"><?php _e( "Your jotURL private <abbr title='application programming interface'>API</abbr> key:", 'wp-to-twitter' ); ?></label>
							<input type="text" name="joturlapi" id="joturlapi" size="40" value="<?php echo esc_attr( get_option( 'joturlapi' ) ); ?>"/>
						</p>
						<p>
							<label for="joturl_domain"><?php _e( 'Your jotURL custom domain:', 'wp-to-twitter' ); ?></label>
							<input type="text" name="joturl_domain" id="joturl_domain" size="40" value="<?php echo esc_attr( get_option( 'joturl_domain' ) ); ?>"/>
						</p>
						<p>
							<label for="joturl_longurl_params"><?php _e( 'Parameters to add to the long URL (before shortening):', 'wp-to-twitter' ); ?></label>
							<input type="text" name="joturl_longurl_params" id="joturl_longurl_params" size="40" value="<?php echo esc_attr( get_option( 'joturl_longurl_params' ) ); ?>"/>
						</p>

						<p>
							<label for="joturl_shorturl_params"><?php _e( 'Parameters to add to the short URL (after shortening):', 'wp-to-twitter' ); ?></label>
							<input type="text" name="joturl_shorturl_params" id="joturl_shorturl_params" size="40" value="<?php echo esc_attr( get_option( 'joturl_shorturl_params' ) ); ?>"/>
						</p>
						<p>
							<a href="https://www.joturl.com/reserved/api.html"><?php _e( 'View your jotURL public and private API key', 'wp-to-twitter' ); ?></a>
						</p>
						<div><input type="hidden" name="submit-type" value="joturlapi"/></div>
						<?php
						echo $form_end;
					} else {
						$form = apply_filters( 'wpt_shortener_settings', '', $shortener );
						if ( '' !== $form ) {
							echo $form_start . $form . $form_end;
						} else {
							_e( 'Your shortener does not require any account settings.', 'wp-to-twitter' );
						}
					}
					?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Update settings for shorteners.
	 *
	 * @param array $post POST data.
	 */
	function wpt_shortener_update( $post ) {
		$message = '';
		if ( isset( $post['submit-type'] ) && 'yourlsapi' === $post['submit-type'] ) {
			$message = '';
			if ( '' !== $post['yourlstoken'] && isset( $post['submit'] ) ) {
				update_option( 'yourlstoken', trim( $post['yourlstoken'] ) );
				delete_option( 'yourlsapi' );
				delete_option( 'yourlslogin' );
				$message .= __( 'YOURLS signature token updated.', 'wp-to-twitter' );
			}
			update_option( 'yourlsurl', trim( $post['yourlsurl'] ) );
			// yourls path is deprecated.
			if ( isset( $post['yourlspath'] ) && '' !== $post['yourlspath'] ) {
				update_option( 'yourlspath', trim( $post['yourlspath'] ) );
				if ( file_exists( $post['yourlspath'] ) ) {
					$message .= ' ' . __( 'YOURLS local server path added. ', 'wp-to-twitter' );
				} else {
					$message .= ' ' . __( 'The path to your YOURLS installation is not correct. ', 'wp-to-twitter' );
				}
			}
			if ( '' !== $post['jd_keyword_format'] ) {
				update_option( 'jd_keyword_format', $post['jd_keyword_format'] );
				if ( '1' === $post['jd_keyword_format'] ) {
					$message .= ' ' . __( 'YOURLS will use Post ID for short URL slug.', 'wp-to-twitter' );
				} elseif ( '0' === $post['jd_keyword_format'] ) {
					$message .= ' ' . __( 'YOURLS will use default URL structures.', 'wp-to-twitter' );
				} else {
					$message .= ' ' . __( 'YOURLS will use your custom keyword for short URL slug.', 'wp-to-twitter' );
				}
			}
			if ( isset( $post['clear'] ) ) {
				delete_option( 'yourlsapi' );
				delete_option( 'yourlslogin' );
				delete_option( 'yourlstoken' );
				delete_option( 'jd_keyword_format' );
				delete_option( 'yourlspath' );
				delete_option( 'yourlsurl' );
				$message .= __( 'YOURLS data cleared.', 'wp-to-twitter' );
			}
		}

		if ( isset( $post['submit-type'] ) && 'joturlapi' === $post['submit-type'] ) {
			if ( '' !== $post['joturlapi'] && isset( $post['submit'] ) ) {
				update_option( 'joturlapi', trim( $post['joturlapi'] ) );
				$message = __( 'jotURL private API Key Updated.', 'wp-to-twitter' );
			} elseif ( isset( $post['clear'] ) ) {
				update_option( 'joturlapi', '' );
				$message = __( 'jotURL private API Key deleted. You cannot use the jotURL API without a private API key.', 'wp-to-twitter' );
			} else {
				$message = __( "jotURL private API Key not added - <a href='https://www.joturl.com/reserved/api.html'>get one here</a>! A private API key is required to use the jotURL URL shortening service. ", 'wp-to-twitter' );
			}
			if ( '' !== $post['joturllogin'] && isset( $post['submit'] ) ) {
				update_option( 'joturllogin', trim( $post['joturllogin'] ) );
				$message .= __( 'jotURL public API Key Updated.', 'wp-to-twitter' );
			} elseif ( isset( $post['clear'] ) ) {
				update_option( 'joturllogin', '' );
				$message = __( 'jotURL public API Key deleted. You cannot use the jotURL API without providing your public API Key.', 'wp-to-twitter' );
			} else {
				$message = __( "jotURL public API Key not added - <a href='https://www.joturl.com/reserved/settings.html#tools-api'>get one here</a>! ", 'wp-to-twitter' );
			}
			if ( '' !== $post['joturl_longurl_params'] && isset( $post['submit'] ) ) {
				$v = trim( $post['joturl_longurl_params'] );
				if ( substr( $v, 0, 1 ) === '&' || substr( $v, 0, 1 ) === '?' ) {
					$v = substr( $v, 1 );
				}
				update_option( 'joturl_longurl_params', $v );
				$message .= __( 'Long URL parameters added.', 'wp-to-twitter' );
			} elseif ( isset( $post['clear'] ) ) {
				update_option( 'joturl_longurl_params', '' );
				$message = __( 'Long URL parameters deleted.', 'wp-to-twitter' );
			}
			if ( '' !== $post['joturl_domain'] && isset( $post['submit'] ) ) {
				update_option( 'joturl_domain', $post['joturl_domain'] );
				$message .= __( 'Custom jotURL domain saved.', 'wp-to-twitter' );
			} elseif ( isset( $post['clear'] ) ) {
				update_option( 'joturl_domain', '' );
				$message = __( 'Custom jotURL domain deleted.', 'wp-to-twitter' );
			}
			if ( '' !== $post['joturl_shorturl_params'] && isset( $post['submit'] ) ) {
				$v = trim( $post['joturl_shorturl_params'] );
				if ( substr( $v, 0, 1 ) === '&' || substr( $v, 0, 1 ) === '?' ) {
					$v = substr( $v, 1 );
				}
				update_option( 'joturl_shorturl_params', $v );
				$message .= __( 'Short URL parameters added.', 'wp-to-twitter' );
			} elseif ( isset( $post['clear'] ) ) {
				update_option( 'joturl_shorturl_params', '' );
				$message = __( 'Short URL parameters deleted.', 'wp-to-twitter' );
			}
		}
		$message = apply_filters( 'wpt_save_shortener_settings', $message );

		return $message;
	}

	/**
	 * Select a shortener.
	 *
	 * @param array $post POST data.
	 *
	 * @return message.
	 */
	function wpt_select_shortener( $post ) {
		$message = '';
		// don't return a message if unchanged.
		$stored = ( isset( $_POST['wpt_use_stored_urls'] ) ) ? 'false' : 'true';
		update_option( 'wpt_use_stored_urls', $stored );
		if ( get_option( 'jd_shortener' ) === $post['jd_shortener'] ) {
			return;
		}
		update_option( 'jd_shortener', sanitize_key( $post['jd_shortener'] ) );
		$short     = (string) get_option( 'jd_shortener' );
		$admin_url = admin_url( 'admin.php?page=wp-tweets-pro' );
		$admin_url = add_query_arg( 'tab', 'shortener', $admin_url );

		// these are the URL shorteners which require settings.
		if ( '2' === $short || '10' === $short || '6' === $short ) {
			// Translators: Settings URL for shortener configuration.
			$message .= sprintf( __( 'You must <a href="%s">configure your URL shortener settings</a>.', 'wp-to-twitter' ), $admin_url );
		}

		if ( '' !== $message ) {
			$message .= '<br />';
		}

		return $message;
	}

	add_filter( 'wpt_pick_shortener', 'wpt_pick_shortener' );
	/**
	 * Form to select your shortener.
	 */
	function wpt_pick_shortener() {
		$shortener = (string) get_option( 'jd_shortener' );
		if ( '2' === $shortener && ! function_exists( 'wbitly_shorten_url' ) ) {
			$install_bitly = admin_url( 'plugin-install.php?s=codehaveli+bitly+url+shortener&tab=search&type=term' );
			// Translators: search URL to find Bit.ly plug-in.
			echo '<p>' . sprintf( __( 'Bit.ly support is provided via the <a href="https://wordpress.org/plugins/codehaveli-bitly-url-shortener/">Codehaveli Bitly URL Shortener</a> (<a href="%s">Install</a>) plug-in, available from WordPress.org', 'wp-to-twitter' ), $install_bitly ) . '</p>';
		}
		if ( '11' === $shortener && ! class_exists( 'Hum' ) ) {
			$install_hum = admin_url( 'plugin-install.php?s=hum+url+shortener+norris&tab=search&type=term' );
			// Translators: search URL to find Hum plug-in.
			echo '<p>' . sprintf( __( 'Hum is a custom shortener plug-in. Support is provided via the <a href="https://wordpress.org/plugins/hum/">Hum URL Shortener</a> (<a href="%s">Install</a>) plug-in, available from WordPress.org', 'wp-to-twitter' ), $install_hum ) . '</p>';
		}
		?>
		<p>
			<label for="jd_shortener"><?php _e( 'Choose a URL shortener', 'wp-to-twitter' ); ?></label>
			<select name="jd_shortener" id="jd_shortener">
				<option value="3" <?php selected( $shortener, '3' ); ?>><?php _e( "Don't shorten URLs.", 'wp-to-twitter' ); ?></option>
				<option value="4" <?php selected( $shortener, '4' ); ?>>WordPress</option>
				<option value="2" <?php selected( $shortener, '2' ); ?>>Bit.ly</option>
				<?php
				if ( '5' === $shortener ) { // if the user has already selected local server, leave available.
					?>
				<option value="5" <?php selected( $shortener, '5' ); ?>><?php _e( 'YOURLS (this server)', 'wp-to-twitter' ); ?></option>
					<?php
				}
				?>
				<option value="6" <?php selected( $shortener, '6' ); ?>><?php _e( 'YOURLS (remote server)', 'wp-to-twitter' ); ?></option>
				<option value="10" <?php selected( $shortener, '10' ); ?>>jotURL</option>
				<option value="11" <?php selected( $shortener, '11' ); ?>>Hum</option>
				<?php
				// Add a custom shortener.
				echo apply_filters( 'wpt_choose_shortener', '', $shortener );
				?>
			</select>
		<?php
		if ( '3' !== $shortener ) {
			?>
			<input type='checkbox' value='false' name='wpt_use_stored_urls' id='wpt_use_stored_urls' <?php checked( get_option( 'wpt_use_stored_urls' ), 'false' ); ?>> <label for='wpt_use_stored_urls'><?php _e( 'Always request a new short URL for Tweets', 'wp-to-twitter' ); ?></label>
			<?php
		}
		?>
		</p>
		<?php
	}
}
