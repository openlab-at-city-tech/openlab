<?php

namespace com\cminds\package\free\v1_0_7;

if ( !class_exists( __NAMESPACE__ . '\CmindsLicensingAPI' ) ) {

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
		private $optionLicenseData					 = null;
		private $optionLicenseStatus				 = null;
		private $optionCountLicenseActivations		 = null;
		private $optionCountLicenseMaxActivations	 = null;
		private $license							 = null;
		private $licenseStatus						 = null;
		private $licenseData						 = null;
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

			$this->url		 = str_replace( array( 'http://', 'https://', 'www.' ), array( '', '', '' ), get_bloginfo( 'wpurl' ) );
			$this->slug		 = basename( $cmindsProPackage->getOption( 'plugin-file' ), '.php' );
			$this->plugin	 = $this->name		 = $cmindsProPackage->getOption( 'plugin-basename' );
			$this->version	 = $cmindsProPackage->getOption( 'plugin-version' );

			$this->pluginMenu			 = $cmindsProPackage->getOption( 'plugin-menu-item' );
			$this->pluginMenuPage		 = $this->getPageUrl( 'licensing' );
			$this->pluginUpdateMenuPage	 = $this->getPageUrl( 'update' );

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
			$this->optionLicenseData				 = 'cminds-' . $this->pluginSlug . '-license-data';
			$this->optionLicenseStatus				 = 'cminds-' . $this->pluginSlug . '-license-status';
			$this->optionCountLicenseActivations	 = 'cminds-' . $this->pluginSlug . '-license-activation-count';
			$this->optionCountLicenseMaxActivations	 = 'cminds-' . $this->pluginSlug . '-license-max-ac';
			$this->optionUpdateLastCheck			 = 'cminds-' . $this->pluginSlug . '-last-update-check';
			$this->optionUpdateInfoArr				 = 'cminds-' . $this->pluginSlug . '-last-update-info';

			$this->license		 = trim( get_option( $this->optionLicenseKey, '' ) );
			$this->licenseStatus = trim( get_option( $this->optionLicenseStatus, '' ) );
			$this->licenseData	 = get_option( $this->optionLicenseData );
			$this->itemName		 = $cmindsProPackage->getOption( 'plugin-license-name' );

			$this->validItemNames	 = array( $this->itemName );
			$licensingAliases		 = $cmindsProPackage->getOption( 'plugin-licensing-aliases' );
			if ( !empty( $licensingAliases ) && is_array( $licensingAliases ) ) {
				$this->validItemNames = array_merge( $this->validItemNames, $licensingAliases );
			}
			/*
			 * Remove empty
			 */
			$this->validItemNames = array_filter( $this->validItemNames );

			$this->baseParams = array(
				'item_name'	 => urlencode( $this->itemName ),
				'url'		 => $this->url,
				'license'	 => $this->license,
				'slug'		 => $this->slug,
			);

			self::$instances[ $this->optionGroup ] = $this;

			add_action( 'admin_init', array( $this, 'register_license_option' ) );
			add_action( 'admin_init', array( $this, 'dismiss_notice' ) );

			if ( $cmindsProPackage->getOption( 'plugin-is-pro' ) ) {
				add_action( 'admin_init', array( $this, 'activate_license' ) );
				add_action( 'admin_init', array( $this, 'deactivate_license' ) );
				add_action( 'admin_init', array( $this, 'check_license' ) );
				add_action( 'admin_notices', array( $this, 'showMessage' ) );
			}

			add_action( 'update_option_' . $this->optionLicenseKey, array( $this, 'after_new_license_key' ), 10, 2 );

			if ( $cmindsProPackage->getOption( 'plugin-is-pro' ) ) {
				add_action( 'upgrader_pre_download', array( $this, 'changeSSLVersion' ) );
				add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
				add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
				remove_action( 'after_plugin_row_' . $this->name, 'wp_plugin_update_row', 10, 2 );
				add_action( 'after_plugin_row_' . $this->name, array( $this, 'show_update_notification' ), 10, 2 );

				add_action( 'in_plugin_update_message-' . $this->name, array( $this, 'changelog' ), null, 2 );
			}
		}

		/*
		 * We need the same "hack" as for the licensing activation for automatic updates
		 */
		public function changeSSLVersion($reply) {
			add_action( 'http_api_curl', array( $this, 'setSSLVersion' ), 10, 3 );
			return $reply;
		}

		public function changelog( $pluginData, $newPluginData ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

			$plugin = plugins_api( 'plugin_information', array( 'slug' => $newPluginData->slug ) );

			if ( !$plugin || is_wp_error( $plugin ) ) {
				return;
			}

			if ( !empty( $plugin->sections[ 'changelog' ] ) ) {
				$changes = $plugin->sections[ 'changelog' ];
				$pos	 = strpos( $changes, '<h4>' . preg_replace( '/[^\d\.]/', '', $pluginData[ 'Version' ] ) );
				if ( $pos !== false ) {
					$changes = trim( substr( $changes, 0, $pos ) );
				}

				$replace = array(
					'<ul>'	 => '<ul style="list-style: disc inside; padding-left: 15px; font-weight: normal;">',
					'<h4>'	 => '<h4 style="margin-bottom:0;">',
				);
			} else {
				$changes = '';
				$replace = array();
			}

			$changelogUrl = $this->changelogUrl;
			if ( !empty( $changelogUrl ) ) {
				$changes .= '<br/><a href="' . $changelogUrl . '" target="_blank">Show full plugin changelog</a>';
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
		 * @param array   $_transient_data Update array build by WordPress.
		 * @return array Modified update array with custom plugin data.
		 */
		function check_update( $_transient_data ) {
			global $pagenow;
			if ( !is_object( $_transient_data ) ) {
				$_transient_data = new \stdClass;
			}
			if ( 'plugins.php' == $pagenow && is_multisite() ) {
				return $_transient_data;
			}

			$pluginInfo		 = get_plugin_data( $this->cmindsProPackage->getOption( 'plugin-file' ) );
			$currentVersion	 = isset( $pluginInfo[ 'Version' ] ) ? $pluginInfo[ 'Version' ] : $this->version;
			$this->version	 = $currentVersion;
			if ( empty( $_transient_data->response ) || empty( $_transient_data->response[ $this->name ] ) ) {
				$version_info = $this->api_call( self::GET_VERSION_ACTION, array( 'slug' => $this->slug ) );
				if ( false !== $version_info && is_object( $version_info ) && isset( $version_info->new_version ) ) {
					$version_info->plugin	 = $this->plugin;
					$this->did_check		 = true;
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
				if ( !empty( $api_response->homepage ) ) {
					$api_response->homepage = $this->cmindsProPackage->getOption( 'plugin-store-url' );
				}
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

			$pluginInfo		 = get_plugin_data( $this->cmindsProPackage->getOption( 'plugin-file' ) );
			$currentVersion	 = isset( $pluginInfo[ 'Version' ] ) ? $pluginInfo[ 'Version' ] : $this->version;
			$this->version	 = $currentVersion;

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
				$update_link	 = esc_url( wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $this->name, 'upgrade-plugin_' . $this->name ) );
				if ( empty( $version_info->download_link ) ) {
					printf(
					__( 'There is a new version of %1$s available. <a target="_blank" class="thickbox" href="%2$s">View version %3$s details</a>.', 'cminds-package' ), esc_html( $version_info->name ), esc_url( $changelog_link ), esc_html( $version_info->new_version )
					);
				} else {
					printf(
					__( 'There is a new version of %1$s available. <a target="_blank" class="thickbox" href="%2$s">View version %3$s details</a> or <a href="%4$s" target="_blank">download now</a>.', 'cminds-package' ), esc_html( $version_info->name ), esc_url( $changelog_link ), esc_html( $version_info->new_version ), $update_link
					);
				}
				echo '</div></td></tr>';
			}
		}

		public function license_page() {
			$content			 = '';
			ob_start();
			?>

			<h3><span>CreativeMinds License Activation</span></h3>
			<div class="inside">

				<p>
					Please activate your license key according to the amount of licenses you purchased. If you want to move your plugin to another site please deactivate first before moving and reactivating.
				</p>
				<p>
					In order to activate your plugin license first paste the code, second - click the  "Save changes" button, last - click the "Activate" button. <br/>
				</p>

				<hr>
				<h4>License activation</h4>

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
						</tbody>
					</table>

					<?php submit_button(); ?>

					<hr>

					<h4>Problems activating your license?</h4>

					<p>
						<?php
						$cmindsSSLVersion	 = $this->getSSLVersion();
						?>
						<strong>SSL Version </strong>&nbsp;&nbsp;&nbsp;
						<select id="cminds_ssl_version" name="cminds_ssl_version" type="text" class="regular-text" value="<?php echo $cmindsSSLVersion ?>">
							<option value="">-Auto-</option>
							<option value="no-ssl" <?php selected( 'no-ssl', $cmindsSSLVersion ); ?> >No-SSL</option>
							<option value="1" <?php selected( '1', $cmindsSSLVersion ); ?> >SSL v1</option>
							<option value="2" <?php selected( '2', $cmindsSSLVersion ); ?> >SSS v2</option>
							<option value="3" <?php selected( '3', $cmindsSSLVersion ); ?> >SSL v3</option>
							<option value="4" <?php selected( '4', $cmindsSSLVersion ); ?> >TLS v1</option>
						</select>
					</p>
				</form>
				<p>
					If activation of the license doesn't work - you've clicked the "Activate License" button, but there's still an error (usually it means the wrong SSL version being the default), please do the following:
				</p>
				<ol>
					<li>Select <strong>"No-SSL"</strong></li>
					<li>Press the "Activate License" button</li>
					<li>If error still remains select <strong>"SSL v1"</strong></li>
					<li>Press the "Activate License" button</li>
					<li>Repeat until activated/run out of options</li>
				</ol>
				<p>
					In most cases one of these will work and the green text "active" will appear next to the license.
					However if you tried all options and none of them works please <a href="https://www.cminds.com/wordpress-plugin-customer-support-ticket/"  target="_blank" class="">Open a Support Ticket</a>.
				</p>
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
			<h3><span>Check Your License Version</span></h3>
			<div class="inside">
				<div class="cminds-updating-items">
					<?php
					/*
					 * Call the action in the add-ons
					 */
					do_action( 'cminds-' . $this->pluginShortSlug . '-update-page' );
					?>
				</div>
			</div>
			<?php
			$content .= ob_get_clean();
			return $content;
		}

		public function license_page_short() {
			$license = get_option( $this->optionLicenseKey );
			$status	 = get_option( $this->optionLicenseStatus );
			$data	 = get_option( $this->optionLicenseData );

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
							<span style="color:green;"><?php _e( 'active', 'cminds-package' ); ?></span>
							<input type="submit" class="buttongreen" name="<?php echo $this->optionLicenseDeactivateKey ?>" value="<?php _e( 'Deactivate License', 'cminds-package' ); ?>"/>
							<?php
						else :
							?>
							<input type="submit" class="button" name="<?php echo $this->optionLicenseActivateKey ?>" value="<?php _e( 'Activate License', 'cminds-package' ); ?>"/>
						<?php endif; ?>
						<?php
						if ( !empty( $data->expires ) ) {
							$siteCount		 = (isset( $data->site_count ) && $data->site_count) ? $data->site_count : FALSE;
							$licenseLimit	 = (isset( $data->license_limit ) && $data->license_limit) ? $data->license_limit : FALSE;
							$activationsText = ((FALSE !== $siteCount && FALSE !== $licenseLimit)) ? sprintf( '(%s/%s licenses active)', $siteCount, $licenseLimit ) : '';
							printf( '%s Your license key expires on %s %s %s', '<p>', date( 'F jS, Y', strtotime( $data->expires ) ), $activationsText, '</p>' );
						}
						?>

					<?php endif; ?>
				</td>
			</tr>
			<?php
		}

		public function update_page_short() {
			$versionInfo = $this->getUpdateInfo();
			?>
			<h4><strong><?php echo $this->pluginName ?></strong></h4>
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

			$licensingSlug = $this->cmindsProPackage->getLicensingSlug();

			if ( $pluginPage === $licensingSlug && $manualUpdate == $this->pluginSlug ) {
				delete_option( $this->optionUpdateLastCheck );
				$url = remove_query_arg( 'check' );
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
					$redirect = remove_query_arg( $noticeKey );
					wp_redirect( $redirect );
					exit();
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
			$transientMessage = get_transient( 'cminds_package_message_' . $this->slug );
			if ( empty( $transientMessage ) ) {
				$this->display_license_message();
			} else {
				delete_transient( 'cminds_package_message_' . $this->slug );
				$this->message		 = $transientMessage;
				$this->messageError	 = true;
			}

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
		private function api_call( $action = '', $transientTime = 60, $invalidateTransient = FALSE ) {
			$apiCallResults = array();
			if ( !is_int( $transientTime ) ) {
				$transientTime = 60;
			}

			$cmindsSSL = $this->getSSLVersion();

			foreach ( $this->validItemNames as $itemName ) {
				$this->baseParams[ 'item_name' ] = urlencode( $itemName );

				if ( in_array( $action, self::get_valid_actions() ) ) {
					$params = array_merge( array( 'edd_action' => $action ), $this->baseParams );
				} else {
					$apiCallResults[] = false;
					continue;
				}

				$transientKey = sha1( json_encode( $params ) );
				if ( $invalidateTransient ) {
					delete_transient( $transientKey );
				}
				$transient = get_transient( $transientKey );

				if ( empty( $transient ) ) {
					add_action( 'http_api_curl', array( $this, 'setSSLVersion' ), 10, 3 );

					$url = esc_url_raw( add_query_arg( $params, esc_url_raw( self::$apiEndpointUrl ) ) );
					if ( 'no-ssl' === $cmindsSSL ) {
						$response = wp_remote_post( str_replace( 'https:', 'http:', $url ), array( 'timeout' => 15, 'sslverify' => false, 'user-agent' => 'CmindsClientApp', 'body' => array() ) );
					} else {
						$response = wp_remote_get( $url, array( 'timeout' => 15, 'sslverify' => false, 'user-agent' => 'CmindsClientApp' ) );
					}

					if ( is_wp_error( $response ) ) {
						$cmindsSSL	 = 'no-ssl';
						$response	 = wp_remote_post( str_replace( 'https:', 'http:', $url ), array( 'timeout' => 15, 'sslverify' => false, 'user-agent' => 'CmindsClientApp', 'body' => array() ) );

						if ( is_wp_error( $response ) ) {
							$apiCallResults[] = false;
						}
					}

					$license_data		 = json_decode( wp_remote_retrieve_body( $response ) );
					$apiCallResults[]	 = $license_data;
					set_transient( $transientKey, $license_data, $transientTime );
				} else {
					$apiCallResults[] = $transient;
				}
			}

			$possibleResult = null;

			foreach ( $apiCallResults as $callResult ) {
				if ( $callResult !== FALSE ) {
					if ( is_object( $callResult ) ) {
						$possibleResult = $callResult;

						if ( !empty( $cmindsSSL ) ) {
							update_option( $this->optionSSLVersion, $cmindsSSL );
						}

						if ( self::GET_VERSION_ACTION === $action && $possibleResult && isset( $possibleResult->sections ) ) {
							$possibleResult->sections = maybe_unserialize( $possibleResult->sections );
						}

						if ( self::CHECK_ACTION === $action ) {
							/*
							 * Return immediately if there's a success
							 */
							if ( (isset( $possibleResult->success ) && $possibleResult->success == true) && $possibleResult->license == 'valid' ) {
								return $possibleResult;
							}
						} else {
							/*
							 * Return immediately if there's a success
							 */
							if ( (isset( $possibleResult->success ) && $possibleResult->success == true && $possibleResult->license == 'valid') || !empty( $possibleResult->new_version ) ) {
								return $possibleResult;
							}
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
				case 'expired':
					$renewLink			 = $this->cmindsProPackage->getOption( 'plugin-store-url' );
					/*
					 * This license activation limit has beeen reached
					 */
					$this->message		 = 'Warning! Your "' . $this->pluginName . '"  license has expired which means you\'re missing out on updates and support!'
					. ' <a href="' . $renewLink . '" target="_blank">Renew your license here</a> and receive a 30% discount. See <a href="http://creativeminds.helpscoutdocs.com/article/402-general-support-how-to-renew-your-license" target="_blank">renewal instructions</a>.';
					$this->messageError	 = TRUE;
					break;
				case 'deactivated':
				case 'failed':
				case 'site_inactive':
				case 'inactive':
					/*
					 * This license is invalid / either it has expired or the key was invalid
					 */
					$this->message		 = 'Your license key provided for "' . $this->pluginName . '" is inactive! <br/>'
					. 'Please, go to <a href="' . $this->pluginMenuPage . '">plugin\'s License page</a> and click "Activate License".';
					$this->messageError	 = TRUE;
					break;
				case 'invalid':
					/*
					 * This license is invalid / either it has expired or the key was invalid
					 */
					$this->message		 = 'Your license key provided for "' . $this->pluginName . '" is invalid! <br/>'
					. 'Please go to <a href="' . $this->pluginMenuPage . '">plugin\'s License page</a> for the licencing instructions.';
					$this->messageError	 = TRUE;
					break;
				case '':
					/*
					 * This license is invalid / either it has expired or the key was invalid
					 */
					$this->message		 = 'To use "' . $this->pluginName . '" you have to provide a valid license key! <br/>'
					. 'Please go to <a href="' . $this->pluginMenuPage . '">plugin\'s License page</a> to enter your license.';
					$this->messageError	 = TRUE;
					break;
				case 'valid':

					$days			 = 999;
					$licenseData	 = $this->licenseData;
					$updateStatus	 = $this->getUpdateInfo();
					$renewLink		 = $this->cmindsProPackage->getOption( 'plugin-store-url' );

					if ( $licenseData ) {
						$dStart	 = new \DateTime( date( 'Y-m-d', strtotime( $licenseData->expires ) ) );
						$dEnd	 = new \DateTime( date( 'Y-m-d', time() ) );
						$dDiff	 = $dStart->diff( $dEnd );
						$days	 = $dDiff->days;
					}

					$expiring	 = $days <= 30;
					$needsUpdate = !empty( $updateStatus[ 'needs-update' ] );

					if ( $expiring ) {
						/*
						 * This license activation limit has beeen reached
						 */
						$this->message		 = 'Warning! Your "' . $this->pluginName . '"  license is expiring soon (' . date( 'F jS, Y', strtotime( $licenseData->expires ) ) . '), after it\'s expired you\'ll be missing out on updates and support!'
						. ' <a href="' . $renewLink . '" target="_blank">Renew your license here</a> and receive a 30% discount. See <a href="http://creativeminds.helpscoutdocs.com/article/402-general-support-how-to-renew-your-license" target="_blank">renewal instructions</a>.';
						$this->messageError	 = TRUE;
					} else {
						if ( $needsUpdate ) {

							global $current_user;
							$user_id	 = $current_user->ID;
							$noticeKey	 = str_replace( array( '-', '_', '.' ), array( '', '', '' ), $this->pluginSlug . '_' . $updateStatus[ 'newest-version' ] . '_dis_not' );
							$dismissed	 = get_user_meta( $user_id, $noticeKey );
							/* Check that the user hasn't already clicked to ignore the message */

							if ( !$dismissed ) {
								/*
								 * This license is invalid / either it has expired or the key was invalid
								 */
								$dismissUrl			 = add_query_arg( array( $noticeKey => '1' ) );
								$dismissMsg			 = ' Or <a href="' . $dismissUrl . '">dismiss</a> this message.';
								$this->message		 = 'There is a new version of "' . $this->pluginName . ' ' . $updateStatus[ 'newest-version' ] . '"  available, please <a href="' . esc_url_raw( self::$apiEndpointUrl ) . 'guest-account/" target="_blank">update.</a> or use automatic WordPress <a href="' . esc_url_raw( admin_url( 'plugins.php' ) ) . '">plugins update</a>.' . $dismissMsg;
								$this->messageError	 = TRUE;
							}
						}
					}

					break;

				default:
					break;
			}
		}

		public function activate_license() {

			if ( defined( 'DOING_AJAX' ) ) {
				return;
			}

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

			$result = self::api_call( self::ACTIVATE_ACTION, 60, TRUE );

			if ( $result === false ) {
				$message = __( 'Error: There is a problem with connection to licensing server.', 'cminds-package' );
				set_transient( 'cminds_package_message_' . $this->slug, $message, 30 );
			} else {
				/*
				 * Special case when the activation limit is reached
				 */
				if ( isset( $result->error ) && $result->error == self::NO_ACTIVATIONS_STATUS ) {
					$newLicenseStatus = self::NO_ACTIVATIONS_STATUS;
				} else {
					$newLicenseStatus = $result->license;
				}

				update_option( $this->optionLicenseData, $result );
				if ( !empty( $result->site_count ) ) {
					update_option( $this->optionCountLicenseActivations, $result->site_count );
				}
				update_option( $this->optionCountLicenseMaxActivations, (int) $result->license_limit );
				/*
				 * $result->license will be either "active" or "inactive"
				 */
				update_option( $this->optionLicenseStatus, $newLicenseStatus );
			}
		}

		public function deactivate_license() {

			if ( defined( 'DOING_AJAX' ) ) {
				return;
			}

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
				$message = __( 'Error: There is a problem with connection to licensing server.', 'cminds-package' );
				set_transient( 'cminds_package_message_' . $this->slug, $message, 30 );
			} else {
				if ( !empty( $result->site_count ) ) {
					update_option( $this->optionCountLicenseActivations, $result->site_count );
				}
				/*
				 *  $license_data->license will be either "deactivated" or "failed"
				 */
				update_option( $this->optionLicenseStatus, $result->license );
			}
		}

		public function after_new_license_key( $a, $b ) {
			if ( $a !== $b ) {
				$this->baseParams[ 'license' ] = trim( get_option( $this->optionLicenseKey, '' ) );
			}
		}

		public function check_license( $invalidateTransient = FALSE ) {

			if ( defined( 'DOING_AJAX' ) ) {
				return;
			}

			/*
			 * Don't check if there's no license
			 */
			if ( get_option( $this->optionLicenseKey ) == FALSE ) {
				return false;
			}

			/*
			 * Don't check if license is not valid
			 */
			if ( 'valid' !== get_option( $this->optionLicenseStatus ) ) {
				return false;
			}
			$result = self::api_call( self::CHECK_ACTION, 60 * 60 * 24, $invalidateTransient );

			if ( $result === false ) {
				$message = __( 'Error: There is a problem with connection to licensing server.', 'cminds-package' );
				set_transient( 'cminds_package_message_' . $this->slug, $message, 30 );
			} else {
				if ( $result->license == 'valid' || $result->license == 'site_inactive' ) {
					/*
					 * This license is valid
					 */
				} else {
					update_option( $this->optionLicenseData, $result );

					if ( $result->activations_left == 0 ) {
						update_option( $this->optionLicenseStatus, self::NO_ACTIVATIONS_STATUS );
					} else {
						update_option( $this->optionLicenseStatus, $result->license );
					}
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
				$message = __( 'Error: There is a problem with connection to licensing server.', 'cminds-package' );
				set_transient( 'cminds_package_message_' . $this->slug, $message, 30 );
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

					/*
					 * Update the license info
					 */
					$this->check_license( TRUE );

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
				var_dump( 'Base Params:', $this->baseParams );
				var_dump( 'License:' . $this->license );
				var_dump( 'License status:' . $this->licenseStatus );
				var_dump( 'License activations:' . $licenseActivationCount );
				var_dump( 'License max activations:' . $licenseMaxActivationCount );
				print_r( get_option( $this->optionLicenseData ) );
			}

			$licenseOk = !empty( $this->license ) && in_array( $this->licenseStatus, array( 'valid', 'expired', 'inactive', self::NO_ACTIVATIONS_STATUS ) ) && $isLicenseActivationCountOk;
			return $licenseOk;
		}

		public function getPageUrl( $pageBase = 'licensing' ) {
			$pluginMenuPageBase	 = $this->pluginMenu;
			$abbrev				 = $this->cmindsProPackage->getOption( 'plugin-is-addon' ) ? $this->cmindsProPackage->getOption( 'plugin-parent-abbrev' ) : $this->cmindsProPackage->getOption( 'plugin-abbrev' );
			if ( empty( $abbrev ) ) {
				/*
				 * Abbrev not set for the plugin try to guess
				 */
				$abbrev = substr( $this->cmindsProPackage->getOption( 'plugin-abbrev' ), 0, -1 );
			}
			$page = $abbrev . '_' . $pageBase;
			if ( FALSE === strpos( $pluginMenuPageBase, '.php' ) ) {
				$pageUrl = esc_attr( add_query_arg( urlencode_deep( array( 'page' => $page ) ), admin_url( 'admin.php' ) ) );
			} else {
				$pageUrl = esc_attr( add_query_arg( urlencode_deep( array( 'page' => $page ) ), admin_url( $pluginMenuPageBase ) ) );
			}
			return $pageUrl;
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

if ( !function_exists( __NAMESPACE__ . '\cminds_show_message' ) ) {

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