<?php
if ( !class_exists( 'CmindsLicensingAPI' ) ) {

	class CmindsLicensingAPI {

		const ACTIVATE_ACTION			 = 'activate_license';
		const CHECK_ACTION			 = 'check_license';
		const GET_VERSION_ACTION		 = 'get_version';
		const DEACTIVATE_ACTION		 = 'deactivate_license';
		const NO_ACTIVATIONS_STATUS	 = 'no_activations_left';
		const MAX_ACTIVATION_COUNT	 = 1;

		private static $apiEndpointUrl				 = 'https://www.cminds.com/';
		private static $supportUrl					 = 'https://www.cminds.com/wordpress-plugin-customer-support-ticket/';
		private static $customerAreaLoginUrl		 = 'https://www.cminds.com/guest-login/';
		private static $customerAreaRegisterUrl		 = 'https://www.cminds.com/guest-registration/';
		private $cmindsProPackage					 = null;
		private $url								 = null;
		private $slug								 = null;
		private $name								 = null;
		private $plugin								 = null;
		private $version							 = null;
		private $itemName							 = null;
		private $validItemNames						 = null;
		private $baseParams							 = null;
		private $pluginMenu							 = null;
		private $pluginMenuPage						 = null;
		private $pluginUpdateMenuPage				 = null;
		private $pluginName							 = null;
		private $pluginFile							 = null;
		private $pluginSlug							 = null;
		private $pluginShortSlug					 = null;
		private $optionSSLVersion					 = null;
		private $optionGroup						 = null;
		private $optionLicenseKey					 = null;
		private $optionLicenseActivateKey			 = null;
		private $optionLicenseDeactivateKey			 = null;
		private $optionLicenseStatus				 = null;
		private $optionCountLicenseActivations		 = null;
		private $optionCountLicenseMaxActivations	 = null;
		private $license							 = null;
		private $licenseStatus						 = null;
		private $message							 = '';
		private $messageError						 = FALSE;
		private static $instances					 = array();
		private $changelogUrl						 = null;
		private $updateInfoArr						 = null;
		private $optionUpdateLastCheck				 = null;
		private $optionUpdateInfoArr				 = null;

		/**
		 *
		 * @param CmindsProPackage $cmindsProPackage
		 */
		public function __construct( $cmindsProPackage ) {

			$this->cmindsProPackage = $cmindsProPackage;

			$this->url		 = get_bloginfo( 'wpurl' );
			$this->slug		 = basename( $cmindsProPackage->getOption( 'plugin-file' ), '.php' );
			$this->plugin	 = $this->name		 = $cmindsProPackage->getOption( 'plugin-basename' );
			$this->version	 = $cmindsProPackage->getOption( 'plugin-version' );

			$this->pluginMenu			 = $cmindsProPackage->getOption( 'plugin-menu-item' );
			$this->pluginMenuPage		 = mb_strtolower( $this->pluginMenu ) . '_license';
			$this->pluginUpdateMenuPage	 = mb_strtolower( $this->pluginMenu ) . '_update';

			$this->pluginFile	 = $cmindsProPackage->getOption( 'plugin-file' );
			$this->changelogUrl	 = $cmindsProPackage->getOption( 'plugin-changelog-url' );

			$this->pluginName		 = $cmindsProPackage->getOption( 'plugin-name' );
			$this->pluginSlug		 = $cmindsProPackage->getOption( 'plugin-slug' ) ? $cmindsProPackage->getOption( 'plugin-slug' ) : self::camelCaseToHypenSeparated( $this->pluginName );
			$this->pluginShortSlug	 = $cmindsProPackage->getOption( 'plugin-short-slug' );

			$this->optionGroup						 = $this->pluginMenu; //'cminds-' . $this->pluginSlug . '-license';
			$this->optionSSLVersion					 = 'cminds-' . $this->pluginSlug . '-ssl';
			$this->optionLicenseKey					 = 'cminds-' . $this->pluginSlug . '-license-key';
			$this->optionLicenseActivateKey			 = 'cminds-' . $this->pluginSlug . '-license-activate';
			$this->optionLicenseDeactivateKey		 = 'cminds-' . $this->pluginSlug . '-license-deactivate';
			$this->optionLicenseStatus				 = 'cminds-' . $this->pluginSlug . '-license-status';
			$this->optionCountLicenseActivations	 = 'cminds-' . $this->pluginSlug . '-license-activation-count';
			$this->optionCountLicenseMaxActivations	 = 'cminds-' . $this->pluginSlug . '-license-max-ac';
			$this->optionUpdateLastCheck			 = 'cminds-' . $this->pluginSlug . '-last-update-check';
			$this->optionUpdateInfoArr				 = 'cminds-' . $this->pluginSlug . '-last-update-info';

			$this->license		 = trim( get_option( $this->optionLicenseKey, '' ) );
			$this->licenseStatus = trim( get_option( $this->optionLicenseStatus, '' ) );
			$this->itemName		 = $cmindsProPackage->getOption( 'plugin-license-name' );

			$this->validItemNames	 = array( $this->itemName );
			$licensingAliases		 = $cmindsProPackage->getOption( 'plugin-licensing-aliases' );
			if ( !empty( $licensingAliases ) && is_array( $licensingAliases ) ) {
				$this->validItemNames = array_merge( $this->validItemNames, $licensingAliases );
			}

			$this->baseParams = array(
				'item_name'	 => urlencode( $this->itemName ),
				'url'		 => $this->url,
				'license'	 => $this->license,
				'slug'		 => $this->slug,
			);

			self::$instances[ $this->optionGroup ] = $this;

			add_action( 'admin_init', array( $this, 'register_license_option' ) );
			add_action( 'admin_init', array( $this, 'dismiss_notice' ) );

			add_action( 'admin_init', array( $this, 'activate_license' ) );
			add_action( 'admin_init', array( $this, 'deactivate_license' ) );

			if ( $cmindsProPackage->getOption( 'plugin-is-pro' ) ) {
				add_action( 'admin_notices', array( $this, 'showMessage' ) );
			}

			add_action( 'update_option_' . $this->optionLicenseKey, array( $this, 'after_new_license_key' ), 10, 2 );

			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
			add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
			add_action( 'after_plugin_row_' . $this->name, array( $this, 'show_update_notification' ), 10, 2 );

			add_action( 'in_plugin_update_message-' . $this->name, array( $this, 'changelog' ), null, 2 );
		}

		public function changelog( $pluginData, $newPluginData ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

			$plugin = plugins_api( 'plugin_information', array( 'slug' => $newPluginData->slug ) );

			if ( !$plugin || is_wp_error( $plugin ) || empty( $plugin->sections[ 'changelog' ] ) ) {
				return;
			}

			$changes = $plugin->sections[ 'changelog' ];
			$pos	 = strpos( $changes, '<h4>' . preg_replace( '/[^\d\.]/', '', $pluginData[ 'Version' ] ) );
			if ( $pos !== false ) {
				$changes = trim( substr( $changes, 0, $pos ) );
			}

			$replace = array(
				'<ul>'	 => '<ul style="list-style: disc inside; padding-left: 15px; font-weight: normal;">',
				'<h4>'	 => '<h4 style="margin-bottom:0;">',
			);

			$changelogUrl = $this->changelogUrl;
			if ( !empty( $changelogUrl ) ) {
				$changes .= '<a href="' . $changelogUrl . '" target="_blank">Plugin Complete Changelog</a>';
			}
			echo str_replace( array_keys( $replace ), $replace, $changes );
		}

		/**
		 * Check for Updates at the defined API endpoint and modify the update array.
		 *
		 * This function dives into the update API just when WordPress creates its update array,
		 * then adds a custom API call and injects the custom plugin data retrieved from the API.
		 * It is reassembled from parts of the native WordPress plugin update code.
		 * See wp-includes/update.php line 121 for the original wp_update_plugins() function.
		 *
		 * @uses api_call()
		 *
		 * @param array   $_transient_data Update array build by WordPress.
		 * @return array Modified update array with custom plugin data.
		 */
		function check_update( $_transient_data ) {
			global $pagenow;
			if ( !is_object( $_transient_data ) ) {
				$_transient_data = new stdClass;
			}
			if ( 'plugins.php' == $pagenow && is_multisite() ) {
				return $_transient_data;
			}
			if ( empty( $_transient_data->response ) || empty( $_transient_data->response[ $this->name ] ) ) {
				$version_info			 = $this->api_call( self::GET_VERSION_ACTION, array( 'slug' => $this->slug ) );
				$version_info->plugin	 = $this->plugin;
				if ( false !== $version_info && is_object( $version_info ) && isset( $version_info->new_version ) ) {
					$this->did_check = true;
					if ( version_compare( $this->version, $version_info->new_version, '<' ) ) {
						$_transient_data->response[ $this->name ] = $version_info;
					}
					$_transient_data->last_checked			 = time();
					$_transient_data->checked[ $this->name ] = $this->version;
				}
			}
			return $_transient_data;
		}

		/**
		 * Updates information on the "View version x.x details" page with custom data.
		 *
		 * @uses api_call()
		 *
		 * @param mixed   $_data
		 * @param string  $_action
		 * @param object  $_args
		 * @return object $_data
		 */
		function plugins_api_filter( $_data, $_action = '', $_args = null ) {
			if ( $_action != 'plugin_information' ) {
				return $_data;
			}
			if ( !isset( $_args->slug ) || ( $_args->slug != $this->slug ) ) {
				return $_data;
			}
			$to_send		 = array(
				'slug'	 => $this->slug,
				'is_ssl' => is_ssl(),
				'fields' => array(
					'banners'	 => false, // These will be supported soon hopefully
					'reviews'	 => false,
				)
			);
			$api_response	 = $this->api_call( self::GET_VERSION_ACTION, $to_send );
			if ( false !== $api_response ) {
				$_data = $api_response;
			}
			return $_data;
		}

		/**
		 * show update nofication row -- needed for multisite subsites, because WP won't tell you otherwise!
		 *
		 * @param string  $file
		 * @param array   $plugin
		 */
		public function show_update_notification( $file, $plugin ) {
			if ( !current_user_can( 'update_plugins' ) ) {
				return;
			}
			if ( !is_multisite() ) {
				return;
			}
			if ( $this->name != $file ) {
				return;
			}
			// Remove our filter on the site transient
			remove_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ), 10 );
			$update_cache = get_site_transient( 'update_plugins' );
			if ( !is_object( $update_cache ) || empty( $update_cache->response ) || empty( $update_cache->response[ $this->name ] ) ) {
				$cache_key		 = md5( 'edd_plugin_' . sanitize_key( $this->name ) . '_version_info' );
				$version_info	 = get_transient( $cache_key );
				if ( false === $version_info ) {
					$version_info = $this->api_call( self::GET_VERSION_ACTION, array( 'slug' => $this->slug ) );
					set_transient( $cache_key, $version_info, 3600 );
				}
				if ( !is_object( $version_info ) ) {
					return;
				}
				if ( version_compare( $this->version, $version_info->new_version, '<' ) ) {
					$update_cache->response[ $this->name ] = $version_info;
				}
				$update_cache->last_checked				 = time();
				$update_cache->checked[ $this->name ]	 = $this->version;
				set_site_transient( 'update_plugins', $update_cache );
			} else {
				$version_info = $update_cache->response[ $this->name ];
			}
			// Restore our filter
			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
			if ( !empty( $update_cache->response[ $this->name ] ) && version_compare( $this->version, $version_info->new_version, '<' ) ) {
				// build a plugin list row, with update notification
				$wp_list_table	 = _get_list_table( 'WP_Plugins_List_Table' );
				echo '<tr class="plugin-update-tr"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange"><div class="update-message">';
				$changelog_link	 = self_admin_url( 'index.php?edd_sl_action=view_plugin_changelog&plugin=' . $this->name . '&slug=' . $this->slug . '&TB_iframe=true&width=772&height=911' );
				$update_link	 = esc_url( 'https://www.cminds.com/guest-account/' );
				if ( empty( $version_info->download_link ) ) {
					printf(
					__( 'There is a new version of %1$s available. <a target="_blank" class="thickbox" href="%2$s">View version %3$s details</a>.', 'edd' ), esc_html( $version_info->name ), esc_url( $changelog_link ), esc_html( $version_info->new_version )
					);
				} else {
					printf(
					__( 'There is a new version of %1$s available. <a target="_blank" class="thickbox" href="%2$s">View version %3$s details</a> or <a href="%4$s" target="_blank">download now</a>.', 'edd' ), esc_html( $version_info->name ), esc_url( $changelog_link ), esc_html( $version_info->new_version ), $update_link
					);
				}
				echo '</div></td></tr>';
			}
		}

		public function license_page() {
			$content = '';
			ob_start();
			?>

			<div>
				<h2><?php printf( __( '%s - License Options' ), $this->pluginName ); ?></h2>

				<p><strong>Instructions on how to retrieve your license key</strong> <a href="javascript:void(0)" onclick="jQuery( this ).parent().next().slideToggle()">Show/Hide</a></p>
				<div class="cminds-licensing-instructions" style="display:none;">
					You have two options to get your license key:
					<ol>
						<li>
							<p>
								You can get your license keys by logging in the <a target="_blank" href="<?php echo esc_attr( self::$customerAreaLoginUrl ) ?>">Cminds Customer Area</a>. <br/>
								If you don't have an account yet. You have to first <a target="_blank" href="<?php echo esc_attr( self::$customerAreaRegisterUrl ) ?>">register</a> using the e-mail you've used for the purchase. <br/>
								Your license key will be available as shown in the screenshot below.
							</p>
							<img title="Cminds Customer Area screenshot" alt="Example Cminds Customer Area screenshot" src="<?php echo esc_attr( plugin_dir_url( __FILE__ ) ) ?>cminds_user_area.png" />
						</li>
						<li>
							<p>
								You can get the license key for your product from the receipt we've sent you by email after your purchase. In the e-mail there's a link to the online version of the receipt. <br/>
								The online receipt should look similar to the screenshot below.
							</p>
							<img height="400" title="Example Cminds receipt with license key" alt="Example Cminds receipt" src="<?php echo esc_attr( plugin_dir_url( __FILE__ ) ); ?>cminds_receipt.png" />
						</li>
					</ol>

					<p>
						Your license key should be a string of 32 characters (letters and digits). <br/>
						If there's no license key on the customer page nor online receipt, please <a target="_blank" href="<?php echo esc_attr( self::$supportUrl ) ?>">contact support</a>.
					</p>
				</div>

				<p>
					Please activate your license key according to the amount of licenses you purchased. <br/>
					If you want to move your plugin to another site please deactivate first before moving and reactivating. <br/>
					In order to activate your plugin license first paste the code, second - click the  "Save changes" button, last - click the "Activate" button. <br/>
				</p>

				<form method="post" action="options.php">

					<?php settings_fields( $this->optionGroup ); ?>

					<table class="form-table">
						<tbody>
							<?php
							/*
							 * Call the action in the add-ons
							 */
							do_action( 'cminds-' . $this->pluginShortSlug . '-license-page' );
							?>
							<tr valign="top">
								<th scope="row" valign="top" colspan="2">
									<strong>Problems activating?</strong> <a href="javascript:void(0)" onclick="jQuery( this ).parent().parent().next().slideToggle()">Show/Hide</a>
								</th>
							</tr>
							<tr valign="top" style="display:none">
								<th scope="row" valign="top">
									SSL Version
								</th>
								<td>
									<select id="cminds_ssl_version" name="cminds_ssl_version" type="text" class="regular-text" value="<?php echo $this->getSSLVersion(); ?>">
										<option value="">-Auto-</option>
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
									</select>
									<p>If activation of the license doesn't work - you've clicked the "Activate License" button, but there's still an error (usually it means the wrong SSL version being by default), please do the following:</p>
									<ul>
										<li>Select "1"</li>
										<li>Press the "Activate License" button</li>
										<li>If error still remains select "2"</li>
										<li>Press the "Activate License" button</li>
										<li>Repeat until activated/run out of options</li>
										<p>In most cases one of these will work and the green text "active" will appear next to the license.
											However if you tried all options and none of them works please send us a <a href="" target="_blank">Support Ticket.</a>
										</p>
								</td>
							</tr>
						</tbody>
					</table>
					<?php submit_button(); ?>
				</form>
			</div>
			<?php
			$content .= ob_get_clean();
			return $content;
		}

		public function update_page() {
			$versionInfo = $this->getUpdateInfo();
			$content	 = '';
			ob_start();
			?>
			<div>

				<h2><?php printf( __( '%s - Version Update' ), $this->pluginName ); ?></h2>

				<div class="cminds-updating-instructions">
					<iframe src="https://player.vimeo.com/video/134692135?title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
				</div>

				<?php
				/*
				 * Call the action in the add-ons
				 */
				do_action( 'cminds-' . $this->pluginShortSlug . '-update-page' );
				?>
			</div>
			<?php
			$content .= ob_get_clean();
			return $content;
		}

		public function license_page_short() {
			$license = get_option( $this->optionLicenseKey );
			$status	 = get_option( $this->optionLicenseStatus );

			$outputLicense = $license;

			if ( $license && is_string( $license ) && $status == 'valid' ) {
				$outputLicense	 = str_pad( substr( esc_attr( $license ), 0, 18 ), 32, 'X' );
				$disabled		 = true;
			} else {
				$disabled = false;
			}
			?>
			<tr valign="top">
				<th scope="row" valign="top">
					<?php echo $this->pluginName ?>
				</th>
				<td>
					<input name="<?php echo $this->optionLicenseKey ?>" type="hidden" value="<?php echo esc_attr( $license ); ?>" />
					<input id="cminds_license_key" name="<?php echo $this->optionLicenseKey ?>" <?php echo $disabled ? 'disabled="disabled"' : ''; ?> type="text" class="regular-text" value="<?php echo esc_attr( $outputLicense ); ?>" />
					<?php if ( false !== $license ) : ?>
						<?php
						if ( $status !== false && $status == 'valid' ) :
							?>
							<span style="color:green;"><?php _e( 'active' ); ?></span>
							<input type="submit" class="button-secondary" name="<?php echo $this->optionLicenseDeactivateKey ?>" value="<?php _e( 'Deactivate License' ); ?>"/>
							<?php
						else :
							?>
							<input type="submit" class="button-secondary" name="<?php echo $this->optionLicenseActivateKey ?>" value="<?php _e( 'Activate License' ); ?>"/>
						<?php endif; ?>

					<?php endif; ?>
				</td>
			</tr>
			<?php
		}

		public function update_page_short() {
			$versionInfo = $this->getUpdateInfo();
			?>

			<h3><strong><?php echo $this->pluginName ?></strong></h3>

			<table>
				<tr>
					<th>Your version</th>
					<th>Newest version</th>
					<th>Check result</th>
					<th>Options</th>
				</tr>
				<tr>
					<td><?php echo $versionInfo[ 'current-version' ]; ?></td>
					<td><?php echo $versionInfo[ 'newest-version' ]; ?></td>
					<td><?php echo $versionInfo[ 'needs-update' ] ? '<a href="' . esc_attr( self::$apiEndpointUrl ) . 'guest-account/" target="_blank">Update required</a>' : 'Up-to-date' ?></td>
					<td><a class="button submit-btn submit-button" href="<?php echo esc_attr( add_query_arg( array( 'check' => urlencode( $this->pluginSlug ) ) ) ); ?>">Check for updates</a></td>
				</tr>
			</table>
			<?php
		}

		public function register_license_option() {
			// creates our settings in the options table
			register_setting( $this->optionGroup, $this->optionLicenseKey, array( $this, 'sanitize_license' ) );
		}

		public function dismiss_notice() {
			$manualUpdate	 = filter_input( INPUT_GET, 'check' );
			$pluginPage		 = filter_input( INPUT_GET, 'page' );

			if ( $pluginPage === $this->pluginUpdateMenuPage && $manualUpdate == $this->pluginSlug ) {
				delete_option( $this->optionUpdateLastCheck );
				$url = esc_url( remove_query_arg( 'check' ) );
				wp_redirect( $url );
				exit;
			}

			$updateStatus = $this->getUpdateInfo();

			if ( !empty( $updateStatus[ 'newest-version' ] ) ) {
				global $current_user;
				$user_id	 = $current_user->ID;
				$noticeKey	 = str_replace( array( '-', '_', '.' ), array( '', '', '' ), $this->pluginSlug . '_' . $updateStatus[ 'newest-version' ] . '_dis_not' );

				if ( isset( $_GET[ $noticeKey ] ) && '1' == $_GET[ $noticeKey ] ) {
					add_user_meta( $user_id, $noticeKey, 1 );
				}
			}
		}

		public function sanitize_license( $new ) {
			$old = get_option( $this->optionLicenseKey );
			if ( $old && $old != $new ) {
				delete_option( $this->optionLicenseStatus ); // new license has been entered, so must reactivate
			}
			if ( !$new ) {
				delete_option( $this->optionLicenseKey );
				return false;
			}
			return $new;
		}

		/**
		 * Shows the message
		 */
		public function showMessage() {
			$this->display_license_message();

			/*
			 * Only show to admins
			 */
			if ( current_user_can( 'manage_options' ) && !empty( $this->message ) ) {
				cminds_show_message( $this->message, $this->messageError );
			}
		}

		/**
		 * Returns the list of API actions
		 * @return string
		 */
		private function get_valid_actions() {
			$validActions = array( self::ACTIVATE_ACTION, self::DEACTIVATE_ACTION, self::GET_VERSION_ACTION, self::CHECK_ACTION );
			return $validActions;
		}

		/**
		 * Sets the version of the SSL
		 * @param type $handle
		 * @param type $r
		 * @param type $url
		 */
		public function getSSLVersion() {
			$cmindsSSL = filter_input( INPUT_POST, 'cminds_ssl_version' );
			if ( empty( $cmindsSSL ) ) {
				$cmindsSSL = get_option( $this->optionSSLVersion );
			}
			return $cmindsSSL;
		}

		/**
		 * Sets the version of the SSL
		 * @param type $handle
		 * @param type $r
		 * @param type $url
		 */
		public function setSSLVersion( $handle, $r, $url ) {
			$cmindsSSL = $this->getSSLVersion();
			if ( is_numeric( $cmindsSSL ) ) {
				if ( $cmindsSSL < 1 ) {
					$cmindsSSL = 1;
				}
				$cmindsSSL = intval( $cmindsSSL );
				curl_setopt( $handle, CURLOPT_SSLVERSION, $cmindsSSL );
			}
		}

		/**
		 * API call to the licencing server
		 *
		 * @param type $action
		 * @param type $params
		 * @return boolean
		 */
		private function api_call( $action = '' ) {
			$apiCallResults = array();

			foreach ( $this->validItemNames as $itemName ) {
				$this->baseParams[ 'item_name' ] = urlencode( $itemName );

				if ( in_array( $action, self::get_valid_actions() ) ) {
					$params = array_merge( array( 'edd_action' => $action ), $this->baseParams );
				} else {
					$apiCallResults[] = false;
					continue;
				}

				$transientKey	 = sha1( json_encode( $params ) );
				$transient		 = get_transient( $transientKey );

				if ( empty( $transient ) ) {
					add_action( 'http_api_curl', array( $this, 'setSSLVersion' ), 10, 3 );
					$url		 = esc_url_raw( add_query_arg( $params, esc_url_raw( self::$apiEndpointUrl ) ) );
					$response	 = wp_remote_get( $url, array( 'timeout' => 15, 'sslverify' => false, 'user-agent' => 'CmindsClientApp' ) );

					if ( is_wp_error( $response ) ) {
						$apiCallResults[] = false;
					}

					$license_data		 = json_decode( wp_remote_retrieve_body( $response ) );
					$apiCallResults[]	 = $license_data;
					set_transient( $transientKey, $license_data, 60 );
				} else {
					$apiCallResults[] = $transient;
				}
			}

			foreach ( $apiCallResults as $callResult ) {
				if ( $callResult !== FALSE ) {
					if ( is_object( $callResult ) ) {
						$possibleResult = $callResult;

						$cmindsSSL = filter_input( INPUT_POST, 'cminds_ssl_version' );
						if ( !empty( $cmindsSSL ) ) {
							update_option( $this->optionSSLVersion, intval( $cmindsSSL ) );
						}

						if ( self::GET_VERSION_ACTION === $action && $possibleResult && isset( $possibleResult->sections ) ) {
							$possibleResult->sections = maybe_unserialize( $possibleResult->sections );
							unset( $possibleResult->package );
						}

						/*
						 * Return immediately if there's a success
						 */
						if ( (isset( $possibleResult->success ) && $possibleResult->success == true) || !empty( $possibleResult->new_version ) ) {
							return $possibleResult;
						}
					}
				}
			}

			/*
			 * Return the result with 'error'
			 */
			if ( is_object( $possibleResult ) ) {
				return $possibleResult;
			}

			/*
			 * None of the call results is different than FALSE
			 */
			return FALSE;
		}

		public function display_license_message() {
			$licenseStatus = get_option( $this->optionLicenseStatus );

			switch ( $licenseStatus ) {
				case self::NO_ACTIVATIONS_STATUS:
					/*
					 * This license activation limit has beeen reached
					 */
					$this->message		 = 'Your have reached your activation limit for "' . $this->pluginName . '"! <br/>'
					. 'Please, purchase a new license or contact <a target="_blank" href="' . esc_url_raw( self::$supportUrl ) . '">support</a>.';
					$this->messageError	 = TRUE;
					break;
				case 'deactivated':

				case 'site_inactive':
				case 'inactive':
					/*
					 * This license is invalid / either it has expired or the key was invalid
					 */
					$this->message		 = 'Your license key provided for "' . $this->pluginName . '" is inactive! <br/>'
					. 'Please, go to <a href="' . esc_attr( add_query_arg( urlencode_deep( array( 'page' => $this->pluginMenuPage ) ), admin_url( 'admin.php' ) ) ) . '">plugin\'s License page</a> and click "Activate License".';
					$this->messageError	 = TRUE;
					break;
				case 'invalid':
					/*
					 * This license is invalid / either it has expired or the key was invalid
					 */
					$this->message		 = 'Your license key provided for "' . $this->pluginName . '" is invalid! <br/>'
					. 'Please go to <a href="' . esc_attr( add_query_arg( urlencode_deep( array( 'page' => $this->pluginMenuPage ) ), admin_url( 'admin.php' ) ) ) . '">plugin\'s License page</a> for the licencing instructions.';
					$this->messageError	 = TRUE;
					break;
				case '':
					/*
					 * This license is invalid / either it has expired or the key was invalid
					 */
					$this->message		 = 'To use "' . $this->pluginName . '" you have to provide a valid license key! <br/>'
					. 'Please go to <a href="' . esc_attr( add_query_arg( urlencode_deep( array( 'page' => $this->pluginMenuPage ) ), admin_url( 'admin.php' ) ) ) . '">plugin\'s License page</a> to enter your license.';
					$this->messageError	 = TRUE;
					break;
				case 'valid':

					$updateStatus = $this->getUpdateInfo();

					if ( !empty( $updateStatus[ 'needs-update' ] ) ) {
						global $current_user;
						$user_id	 = $current_user->ID;
						$noticeKey	 = str_replace( array( '-', '_', '.' ), array( '', '', '' ), $this->pluginSlug . '_' . $updateStatus[ 'newest-version' ] . '_dis_not' );
						$dismissed	 = get_user_meta( $user_id, $noticeKey );
						/* Check that the user hasn't already clicked to ignore the message */

						if ( !$dismissed ) {
							/*
							 * This license is invalid / either it has expired or the key was invalid
							 */
							$dismissMsg			 = ' Or <a href="?' . $noticeKey . '=1">dismiss</a> this message.';
							$this->message		 = 'There is a new version of "' . $this->pluginName . ' ' . $updateStatus[ 'newest-version' ] . '"  available, please <a href="' . esc_url_raw( self::$apiEndpointUrl ) . 'guest-account/" target="_blank">update.</a>' . $dismissMsg;
							$this->messageError	 = TRUE;
						}
					}
					break;

				default:
					break;
			}
		}

		public function activate_license() {
			$post		 = filter_input( INPUT_POST, $this->optionLicenseActivateKey );
			$pluginPage	 = filter_input( INPUT_POST, 'option_page' );

			/*
			 *  listen for our activate button to be clicked
			 */
			if ( !$post ) {
				return;
			}

			/*
			 * Switch API instance
			 */
			if ( $pluginPage !== $this->optionGroup ) {
				self::$instances[ $pluginPage ]->activate_license();
				return;
			}

			// run a quick security check
			if ( !check_admin_referer( "$this->optionGroup-options" ) ) {
				// get out if we didn't click the button
				return;
			}

			$result = self::api_call( self::ACTIVATE_ACTION );

			if ( $result === false ) {
				cminds_show_message( 'Error', true );
			} else {
				/*
				 * Special case when the activation limit is reached
				 */
				if ( isset( $result->error ) && $result->error == self::NO_ACTIVATIONS_STATUS ) {
					$newLicenseStatus = self::NO_ACTIVATIONS_STATUS;
				} else {
					$newLicenseStatus = $result->license;
				}

				update_option( $this->optionCountLicenseActivations, $result->site_count );
				update_option( $this->optionCountLicenseMaxActivations, (int) $result->license_limit );
				/*
				 * $result->license will be either "active" or "inactive"
				 */
				update_option( $this->optionLicenseStatus, $newLicenseStatus );
			}
		}

		public function deactivate_license() {
			$post		 = filter_input( INPUT_POST, $this->optionLicenseDeactivateKey );
			$pluginPage	 = filter_input( INPUT_POST, 'option_page' );

			/*
			 *  listen for our activate button to be clicked
			 */
			if ( !$post ) {
				return;
			}

			/*
			 * Switch API instance
			 */
			if ( $pluginPage !== $this->optionGroup ) {
				self::$instances[ $pluginPage ]->deactivate_license();
				return;
			}

			// run a quick security check
			if ( !check_admin_referer( "$this->optionGroup-options" ) ) {
				// get out if we didn't click the button
				return;
			}

			$result = self::api_call( self::DEACTIVATE_ACTION );

			if ( $result === false ) {
				cminds_show_message( 'Error', true );
			} else {
				update_option( $this->optionCountLicenseActivations, $result->site_count );
				/*
				 *  $license_data->license will be either "deactivated" or "failed"
				 */
				update_option( $this->optionLicenseStatus, $result->license );
			}
		}

		public function after_new_license_key( $a, $b ) {
			if ( $a !== $b ) {
				$this->baseParams[ 'license' ] = trim( get_option( $this->optionLicenseKey, '' ) );
				$this->check_license();
			}
		}

		public function check_license() {
			/*
			 * Don't check if there's no license
			 */
			if ( get_option( $this->optionLicenseKey ) == FALSE ) {
				return false;
			}

			$result = self::api_call( self::CHECK_ACTION );

			if ( $result === false ) {
				cminds_show_message( 'Error', true );
			} else {
				if ( $result->license == 'valid' ) {
					/*
					 * This license is valid
					 */
				} else {
					/*
					 * $result->license will be either "active" or "inactive"
					 */
					update_option( $this->optionLicenseStatus, $result->license );
				}
			}
		}

		/**
		 * Get the version information from the server
		 * @return type
		 */
		public function get_version() {
			$result = self::api_call( self::GET_VERSION_ACTION );

			if ( $result === false ) {
				cminds_show_message( 'Error', true );
			} else {
				return $result;
			}
		}

		public function getUpdateInfo() {
			if ( empty( $this->updateInfoArr ) ) {
				$pluginInfo		 = get_plugin_data( $this->pluginFile );
				$currentVersion	 = isset( $pluginInfo[ 'Version' ] ) ? $pluginInfo[ 'Version' ] : 'n/a';

				$updateInfoArr = array(
					'current-version'	 => $currentVersion,
					'needs-update'		 => true,
				);

				$checkForUpdate	 = get_option( $this->optionUpdateLastCheck, false );
				$now			 = time();

				if ( false === $checkForUpdate || intval( $checkForUpdate ) < $now ) {
					$versionResult = $this->get_version();

					if ( $versionResult && is_object( $versionResult ) && !empty( $versionResult->new_version ) ) {
						$versionCompare = version_compare( $versionResult->new_version, $currentVersion, '>' );

						$updateInfoArr[ 'newest-version' ]	 = $versionResult->new_version;
						$updateInfoArr[ 'needs-update' ]	 = $versionCompare;
						$nextCheck							 = strtotime( '+2 WEEKS' );
					} else {
						$updateInfoArr[ 'newest-version' ]	 = 'n/a';
						$nextCheck							 = strtotime( '+1 DAY' );
					}

					$this->updateInfoArr = $updateInfoArr;
					update_option( $this->optionUpdateInfoArr, $this->updateInfoArr );
					update_option( $this->optionUpdateLastCheck, $nextCheck );
				} else {
					$this->updateInfoArr = get_option( $this->optionUpdateInfoArr, array() );
					$versionCompare		 = version_compare( $this->updateInfoArr[ 'newest-version' ], $currentVersion, '>' );

					$this->updateInfoArr[ 'current-version' ]	 = $currentVersion;
					$this->updateInfoArr[ 'needs-update' ]		 = $versionCompare;
				}
			}

			return $this->updateInfoArr;
		}

		public function isLicenseOk() {
			$licenseActivationCount		 = get_option( $this->optionCountLicenseActivations, 0 );
			$licenseMaxActivationCount	 = (int) get_option( $this->optionCountLicenseMaxActivations, 1 );

			if ( $licenseMaxActivationCount > 0 ) {
				$licenseMaxActivationCount += self::MAX_ACTIVATION_COUNT;
				$isLicenseActivationCountOk = $licenseActivationCount <= $licenseMaxActivationCount;
			} elseif ( $licenseMaxActivationCount == 0 ) {
				/*
				 * Unlimited activations
				 */
				$isLicenseActivationCountOk = TRUE;
			}

			if ( isset( $_GET[ 'cminds_debug' ] ) && $_GET[ 'cminds_debug' ] == '2' ) {
				var_dump( 'License:' . $this->license );
				var_dump( 'License status:' . $this->licenseStatus );
				var_dump( 'License activations:' . $licenseActivationCount );
				var_dump( 'License max activations:' . $licenseMaxActivationCount );
			}

			$licenseOk = !empty( $this->license ) && in_array( $this->licenseStatus, array( 'valid', 'expired', 'inactive', self::NO_ACTIVATIONS_STATUS ) ) && $isLicenseActivationCountOk;
			return $licenseOk;
		}

		/**
		 * Change SomethingLikeThis to something-like-this
		 *
		 * @param str $str text to change
		 * @return string
		 */
		public static function camelCaseToHypenSeparated( $str ) {
			if ( function_exists( 'lcfirst' ) === false ) {

				function lcfirst( $str ) {
					$str[ 0 ] = strtolower( $str[ 0 ] );
					return $str;
				}

			}
			return strtolower( preg_replace( '/([A-Z])/', '-$1', str_replace( ' ', '', lcfirst( $str ) ) ) );
		}

	}

}

if ( !function_exists( 'cminds_show_message' ) ) {

	/**
	 * Generic function to show a message to the user using WP's
	 * standard CSS classes to make use of the already-defined
	 * message colour scheme.
	 *
	 * @param $message The message you want to tell the user.
	 * @param $errormsg If true, the message is an error, so use
	 * the red message style. If false, the message is a status
	 * message, so use the yellow information message style.
	 */
	function cminds_show_message( $message, $errormsg = false ) {
		if ( $errormsg ) {
			echo '<div id="message" class="error">';
		} else {
			echo '<div id="message" class="updated fade">';
		}

		echo "<p><strong>$message</strong></p></div>";
	}

}