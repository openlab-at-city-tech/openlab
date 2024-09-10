<?php 

namespace Wpmet\Libs;

use WP_Query;

defined('ABSPATH') || exit; 

if( !class_exists('\Wpmet\Libs\Emailkit') ) {
	class Emailkit {

		private $is_already_installed = '';

		/**
		 * Constructor for initializing the class.
		 * 
		 * @access public
		 * @return void
		 */
		public function __construct() {

			do_action('edit_with_emailkit_loaded');

			if( empty( $_GET[ 'page' ] ) || 'wc-settings' !== $_GET[ 'page' ] || empty( $_GET[ 'tab' ] ) || 'email' !== $_GET[ 'tab' ] ) {
				return;
			}

			if( !function_exists('get_plugins') ) {
				include_once  ABSPATH . 'wp-admin/includes/plugin.php';
			}
			
			$installed_plugins 			= get_plugins();
			$this->is_already_installed = isset($installed_plugins['emailkit/EmailKit.php']) ? 1 : 0;
			$message 					= '<div class="edit-with-emailkit-wrapper"><div class="edit-with-emailkit-banner-left"><div class="edit-with-emailkit-banner-main"><div class="edit-with-emailkit-banner-logo"><img src="'.plugin_dir_url( __FILE__ ).'/assets/logo.svg" /></div><div class="edit-with-emailkit-banner-middle"><div class="edit-with-emailkit-banner-title">Edit your woocommerce emails with EmailKit builder for free.</div><p>Get 22+ WooCommerce elements, 14+ templates, Shortcode support, and more for absolutely free with EmailKit - The Best WordPress Email Customizer.✨</p></div></div></div><div class="edit-with-emailkit-banner-right"><div class="emailkit-install-activate-btn">Get EmailKit</div></div></div>';
			$dismissed_coutner 			= get_option('elementskit-lite-edit_with_emailkit_banner_dismissed_'.get_current_user_id(), 0);			
			$notice_showing_delay_time 	= (3600 * 24 * 15);

			if($dismissed_coutner == 1){
				$notice_showing_delay_time = (3600 * 24 * 30);
			}elseif($dismissed_coutner == 2){
				$notice_showing_delay_time = (3600 * 24 * 180);
			}elseif($dismissed_coutner >= 3){
				$notice_showing_delay_time = (3600 * 24 * 1825);
			}

			\Oxaim\Libs\Notice::instance('elementskit-lite', 'edit_with_emailkit_banner')
            ->set_dismiss('user', $notice_showing_delay_time)
            ->set_message($message)
            ->call();

			add_action('admin_head', [$this, 'emailkit_admin_head']);
		}
	
		/**
		 * Added script and style for Edit with EmailKit in admin head
		 * 
		 * @access public
		 * @return void
		 */
		public function emailkit_admin_head() {
			?>
			<style>
				.notice-elementskit-lite-edit_with_emailkit_banner{
					background-image: url("<?php echo esc_url(plugin_dir_url( __FILE__ ).'/assets/notification.svg') ?>") !important;
					color: #fff;
					background-size: cover !important; 
					background-repeat: no-repeat !important;
					background-position: center center !important;
					border: none !important;
				}
				.notice-elementskit-lite-edit_with_emailkit_banner .edit-with-emailkit-banner-main{
					display: flex;
					gap: 15px;
				}
				.notice-elementskit-lite-edit_with_emailkit_banner button::before{
					color: #ffffff;
				}

				.notice-elementskit-lite-edit_with_emailkit_banner .notice-container-full-width{
					padding: 0;
					margin: 0 !important;
				}
				.notice-elementskit-lite-edit_with_emailkit_banner .notice-container-full-width .edit-with-emailkit-wrapper{
					display: flex;
					justify-content: space-between;
					align-items: center;
				}
				.notice-elementskit-lite-edit_with_emailkit_banner .notice-container-full-width .edit-with-emailkit-wrapper .edit-with-emailkit-banner-right{
					margin-right: 60px;
				}

				.notice-elementskit-lite-edit_with_emailkit_banner .notice-container-full-width .edit-with-emailkit-wrapper .edit-with-emailkit-banner-left .edit-with-emailkit-banner-main .edit-with-emailkit-banner-logo{
					padding: 24px 8px 0px 8px;
					border-left: 6px solid #13d5ff;
					background: #FFFFFF1A;
				}
				.notice-elementskit-lite-edit_with_emailkit_banner .notice-container-full-width .edit-with-emailkit-wrapper .edit-with-emailkit-banner-left .edit-with-emailkit-banner-middle{
					padding: 20px 4px;
				}
				.notice-elementskit-lite-edit_with_emailkit_banner .notice-container-full-width .edit-with-emailkit-wrapper .edit-with-emailkit-banner-right .emailkit-install-activate-btn{
					font-size: 14px;
					font-weight: 500;
					color: #070C14;
					padding: 15px 22px;
					background: #EBFF00;
					cursor: pointer;
					border-radius: 4px;
				}
				.edit-with-emailkit-banner-title{
					font-size: 24px;
					font-weight: 600;
					margin-bottom: 8px;
				}
			</style>
			<script type="text/javascript">

				jQuery( document ).ready( function( $ ) {
					
					const emailKitInstallBtn = document.querySelector('.emailkit-install-activate-btn');
					
					if( !emailKitInstallBtn ) {
						return;
					}

					const isAlreadyInstalled = "<?php echo esc_attr($this->is_already_installed) ?>";
					let installationUrl = "<?php echo esc_url($this->installation_url('emailkit/EmailKit.php')) ?>";
					let activationUrl = "<?php echo esc_url($this->activation_url('emailkit/EmailKit.php')) ?>";
					installationUrl = installationUrl?.replace(/&#038;/g, '&');
					activationUrl = activationUrl?.replace(/&#038;/g, '&');

					function emailkit_install_active_plugin(ajaxurl, success_callback, beforeText) {
						try {
							$.ajax({
								type: "GET",
								url: ajaxurl,
								beforeSend: () => {
									emailKitInstallBtn.innerHTML = beforeText;
								},
								success: (response) => {
									if (success_callback) {
										success_callback();
									}else{
										location.reload();
									}
								},
								error: function (error) {
									console.error(error);
								}
							});
						} catch (error) {
							console.error("An error occurred:", error);
						}
					}

					emailKitInstallBtn.addEventListener('click', function(e) {

						e.preventDefault();

						if(isAlreadyInstalled === '0'){
							emailkit_install_active_plugin.call(this, installationUrl, () => {
								emailkit_install_active_plugin.call(this, activationUrl, null, 'Activating...');
							}, 'Installing...');
						} else if (isAlreadyInstalled === '1') {
							emailkit_install_active_plugin.call(this, activationUrl, null, 'Activating...');
						}
					});
				});
			</script>
			<?php
		}

		/**
		 * Get plugin installation url
		 * 
		 * @access public
		 * @param string
		 * @return string
		 */
		public function installation_url( $pluginName ) {
			$action     = 'install-plugin';
			$pluginSlug = $this->get_plugin_slug( $pluginName );
	
			return wp_nonce_url(
				add_query_arg(
					array(
						'action' => $action,
						'plugin' => $pluginSlug
					),
					admin_url( 'update.php' )
				),
				$action . '_' . $pluginSlug
			);
		}

		/**
		 * Get plugin slug
		 * 
		 * @access public
		 * @param string	
		 * @return string
		 */
		public function get_plugin_slug( $name ) {
			$split = explode( '/', $name );
	
			return isset( $split[0] ) ? $split[0] : null;
		}

		/**
		 * Get plugin activation url
		 * 
		 * @access public
		 * @param string
		 * @return string
		 */
		public function activation_url( $pluginName ) {
	
			return wp_nonce_url( add_query_arg(
				array(
					'action'        => 'activate',
					'plugin'        => $pluginName,
					'plugin_status' => 'all',
					'paged'         => '1&s',
				),
				admin_url( 'plugins.php' )
			), 'activate-plugin_' . $pluginName );
		}
		
	}
}