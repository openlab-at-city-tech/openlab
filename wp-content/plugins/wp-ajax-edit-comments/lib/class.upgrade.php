<?php
class AECUpgrade {
	private $slug = 'wp-ajax-edit-comments';
	private $path = '';
	private $upgrade = false;
			function __construct() {
				global $aecomments;
				$this->path = $aecomments->get_plugin_path();
				$args = array(
					'remote_url' => $aecomments->get_aec_upgrade_url(),
					'version' => $aecomments->get_version(),
					'plugin_slug' => $this->slug,
					'plugin_path' => $this->path,
					'plugin_url' => 'http://www.ajaxeditcomments.com',
					'time' => 43200
				);
				
				$this->upgrade = new PluginUpgrade( $args );
				//Actions
				add_action( "pu_plugin_row_{$this->slug}", array( &$this, 'plugin_row' ) );
				//Filters
				add_filter( "pu_remote_body_{$this->slug}", array( &$this, 'remote_body' ) );
				add_filter( "pu_plugin_info_{$this->slug}", array( &$this, 'wp_plugin_info' ) );
				
			} //end constructor
			public function get_upgrade() {
				return $this->upgrade;
			}
			//Unset the upgrade value if the next version is a beta
			public function wp_plugin_info( $plugin_info ) {
				global $aecomments;
				$plugin_key = $this->path;
				$aec_plugin_info = $plugin_info->response[ $this->path ];
				if ( isset( $aec_plugin_info->remote_response->type ) ) {
					if ( $aec_plugin_info->remote_response->type == 'beta' && $aecomments->get_admin_option( 'beta_version_notifications' ) == 'false' ) {
						unset( $plugin_info->response[ $this->path ] );
						return $plugin_info;
					} 
				}
				return $plugin_info;
			} //end wp_plugin_info
			public function remote_body( $body ) {
				$body[ 'key' ] = isset( $_POST['auth_key'] ) ? $_POST['auth_key'] : $this->get_key();
				return $body;
			} //end remote_body
			
			public function plugin_row($plugin_name){
				global $aecomments;
				$key = $this->get_key();
				$remote_version = $this->upgrade->get_remote_version();
				$new_version = '';
				
				//Check to see if auth key has expired
				if ( $aecomments->get_admin_option( 'auth_key_expired' ) == 'true' ) {
					$expiration_notice = sprintf( __('Your support license has expired.  Please visit %s to re-new your license.', 'ajaxEdit'), sprintf( "<a href='%s'>http://www.ajaxeditcomments.com</a>", esc_url( 'http://www.ajaxeditcomments.com/pricing-plans/' ) ) );
					echo '</tr><tr class="plugin-update-tr"><td colspan="3" class="plugin-update"><div class="update-message">' . $expiration_notice . '</div></td>';
					return;
				}
				if( !empty( $version_info ) ) {
						
					$new_version = version_compare( $aecomments->get_version(), $remote_version, '<') ? __('There is a new version of WP Ajax Edit Comments available.', 'ajaxEdit') .' <a class="thickbox" title="WP Ajax Edit Comments" href="plugin-install.php?tab=plugin-information&plugin=wp-ajax-edit-comments&TB_iframe=true&width=640&height=808">'. sprintf(__('View version %s Details', 'ajaxEdit'), $remote_version)  . '</a>. ' : '';
				}
				$upgrade_url = esc_url( wp_nonce_url(admin_url( 'admin-ajax.php') . "?&action=upgradecheck", "upgradecheck" ) );
				echo '</tr><tr class="plugin-update-tr"><td colspan="3" class="plugin-update"><div class="update-message">' . $new_version . "<a id='aeccheckupdates' href='$upgrade_url'>" . __('Check for upgrades', 'ajaxEdit') . '</a>&nbsp;&nbsp;<span id="aeccheckupdatesmessage"></span></div></td>';
		} //end plugin_row	
		
		//Attempts to remove an auth key from a wordpress site
		public function auth_key_remove($key = '') {
			$result = $this->upgrade->perform_remote_request( 'authkey_remove', array( 'key' => $key ) );
			return $result;
		} //end auth_key_remove
		
		//public static class.upgrade
		public function display_changelog(){
			$slug = isset( $_REQUEST[ 'plugin' ] ) ? $_REQUEST[ 'plugin' ] : false;
			if ( $slug != 'wp-ajax-edit-comments' ) return;
			$result = $this->upgrade->perform_remote_request( 'changelog', array(), array(), 'html' );
			echo stripslashes($result);
			exit;
		}//end display_changelog
		//Returns an API key
		//public static class.upgrade
		public function get_key() {
			global $aecomments;
			return $aecomments->get_admin_option( 'auth_key' );
		}
		
		//Checks for upgrades in the plugin_row 
		public function ajax_upgrade_check() {
			global $aecomments;
			check_ajax_referer('upgradecheck');
			
			//Getting version number
			$plugin_info = $this->upgrade->check_for_updates( true );
			$no_upgrade = false;
			$is_beta = false;
			//Check to see if the version if a beta
			if ( isset( $plugin_info->remote_response->type ) ) {
				if ( $plugin_info->remote_response->type == 'beta' && $aecomments->get_admin_option( 'beta_version_notifications' ) == 'false' ) {
					$no_upgrade = true;
				} elseif ( $plugin_info->remote_response->type == 'beta' ) {
					$is_beta = true;
				}
			}
			if ( version_compare( $aecomments->get_version(), $plugin_info->new_version, '==' ) ) $no_upgrade = true;
			if ( $no_upgrade ) {
				$response = array(
					'error' => __($aecomments->get_error( 'upgrade_check_none' ), 'ajaxEdit')
				);
				die( json_encode( $response ) );
			}			
			
			$upgrade_url =  wp_nonce_url(admin_url("update.php?action=upgrade-plugin&amp;plugin={$this->path}"), 'upgrade-plugin_' . $this->path);
			$response = array(
				'success' => sprintf( __('There is a new %sversion of WP Ajax Edit Comments available.', 'ajaxEdit'), $is_beta ? 'beta ' : '' ) . " -- " . "<a href='$upgrade_url'>" . __('Upgrade automatically', 'ajaxEdit') . '</a>.'
			);
			die( json_encode( $response ) );
		} //end function ajax_upgrade_check
		/*BEGIN UPDATE FUNCTIONS*/
		//public static class.upgrade
		public function affiliate_id_check($id = 0) {
			$result = $this->upgrade->perform_remote_request( 'affiliate_id_check', array( 'affiliate_id' => $id ) );
			return $result;
		}
		//public static class.upgrade
		//Check for a valid authentication key
		public function auth_key_check($key = '') {
			$result = $this->upgrade->perform_remote_request( 'authkey_check', array( 'key' => $key ) );
			return $result;
		} // end auth_key_check
} //end class

?>