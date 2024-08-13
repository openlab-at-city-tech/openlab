<?php 

namespace Wpmet\Libs;

use WP_Query;

defined('ABSPATH') || exit; 

if( !class_exists('\Wpmet\Libs\Emailkit') ) {
	class Emailkit {

		private $installed_plugins = [];
		private $activated_plugins = [];

		/**
		 * Constructor for initializing the class.
		 * 
		 * @access public
		 * @return void
		 */
		public function __construct() {

			do_action('edit_with_emailkit_loaded');

			add_action('wp_ajax_emailkit_get_builder_url', [$this, 'emailkit_get_builder_url']);

			if( !function_exists('is_plugin_active') ){
				
				// Include necessary WordPress files
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			if(is_plugin_active('woocommerce/woocommerce.php') && !is_plugin_active('emailkit/EmailKit.php')) {
				add_filter( 'woocommerce_email_setting_columns', [$this, 'emailkit_email_setting_columns' ] );
				add_action( 'woocommerce_email_setting_column_template', array( $this, 'emailkit_column_template' ) );
				add_action( 'admin_enqueue_scripts',[$this, 'enqueue_script'] );
				add_action('admin_head', [$this, 'emailkit_admin_head']);
				$this->collect_installed_plugins();
				$this->collect_activated_plugins();
			}
		}

		/**
		 * Get builder url
		 *
		 * @access public
		 * @return void
		 */
		public function emailkit_get_builder_url() {

			if ( !isset($_POST['emailkit_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['emailkit_nonce'])), 'wp_rest')) {
				return [
					'status'    => 'fail',
					'message'   => ['Nonce mismatch.']
				];
			}

			$wc_template_type = isset($_POST['emailkit_template_type']) ? sanitize_text_field(wp_unslash($_POST['emailkit_template_type'])) : '';
			$post_id = $this->get_emailkit_post_id($wc_template_type);

			if($post_id) {
				$builder_url = admin_url("post.php?post={$post_id}&action=emailkit-builder");
		
				wp_send_json([
					'builder_url' => $builder_url
				]);
			}
			
			$emailkit_template = $this->find_emailkit_template($wc_template_type);

			if (isset($emailkit_template)) {
				$demo_url = isset($emailkit_template['file']) ? $emailkit_template['file'] : '';
				$emailkit_email_type = isset($emailkit_template['mail_type']) ? $emailkit_template['mail_type'] : '';
				$emailkit_template_title = isset($emailkit_template['title']) ? $emailkit_template['title'] : '';
				$template_type = isset($emailkit_template['template_type']) ? $emailkit_template['template_type'] : '';
			}	

			wp_send_json([
				'emailkit_editor_template' => $demo_url,
				'emailkit_email_type' => $emailkit_email_type,
				'emailkit_template_type' => $template_type,
				'emailkit_template_title' => $emailkit_template_title,
				'emailkit_template_status' => 'active'
			]);
		}

		/**
		 * Add heading on woocommerce email settings column
		 * 
		 * @access public
		 * @param array
		 * @return array
		 */
		public function emailkit_email_setting_columns ($array) {

			if ( isset( $array['actions'] ) ) {
				unset( $array['actions'] );
				return array_merge(
					$array,
					array(
						'template' => 'EmailKit',
						'actions'  => '',
					)
				);
			}
			return $array;
		}

		/**
		 * Add template on woocommerce email settings column
		 * 
		 * @access public
		 * @param array
		 * @return array
		 */
		public function emailkit_column_template($email) {
			
			$wc_template_type = $email->id;
			$plugin_name = 'emailkit/EmailKit.php';
			$installation_url = $this->installation_url($plugin_name);
			$activation_url = $this->activation_url($plugin_name);
			$plugin_data = $this->get_plugin_status($plugin_name);
			$plugin_status = isset( $plugin_data['status'] ) ?  $plugin_data['status']  : '';
			$plugin_status_label = isset( $plugin_data['status'] ) ? ( $plugin_data['status'] == 'activated' ? 'activated' : '' ) : '';
		?> 

			<td class="wc-email-settings-table-template wpmet-emailkit-install-btn-wrapper">
				<button class="wpmet-emailkit-install-activate emailkit-open-new-form-editor-modal wpmet-woocom-editwithemailkit <?php echo esc_attr($plugin_status_label); ?>"
					style="width: 165px"
					target="_blank"
					href="<?php echo esc_url($installation_url); ?>"
					data-activation_url="<?php echo esc_url($activation_url); ?>"
					data-plugin_status="<?php echo esc_attr($plugin_status); ?>"
					data-wc-template-type="<?php echo esc_attr($wc_template_type); ?>">
					Edit With Emailkit
				</button>
				<p class="wpmet-emailkit-install-error-msg" style="color: #b82441; font-weight: 500; font-size: 13px; display: none;">Please try again</p>
		</td>
		<?php
		}

		/**
		 * Collect installed and activated plugins
		 * 
		 * @access public
		 * @return void
		 */
		public function collect_installed_plugins() {

			if( !function_exists('get_plugins') ) {
				include_once  ABSPATH . 'wp-admin/includes/plugin.php';
			}

			foreach ( get_plugins() as $key => $plugin ) {
				array_push( $this->installed_plugins, $key );
			}
		}

		/**
		 * Collect activated plugins
		 * 
		 * @access public
		 * @return void
		 */
		public function collect_activated_plugins() {
			foreach ( apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) as $plugin ) {
				array_push( $this->activated_plugins, $plugin );
			}
		}

		/**
		 * Check if plugin is installed
		 * 
		 * @access public
		 * @param string
		 * @return bool
		 */
		public function check_installed_plugin( $name ) {
			
			return in_array( $name, $this->installed_plugins );
		}

		/**
		 * Check if plugin is activated
		 * 
		 * @access public
		 * @param string
		 * @return bool
		 */
		public function check_activated_plugin( $name ) {
			return in_array( $name, $this->activated_plugins );
		}

		/**
		 * Get plugin status
		 * 
		 * @access public
		 * @param string
		 * @return array
		 */
		public function get_plugin_status( $name ) {
			$data = [
				"url"              => "",
				"activation_url"   => "",
				"installation_url" => "",
				"title"            => "",
				"status"           => "",
			];

			if ( $this->check_installed_plugin( $name ) ) {
				if ( $this->check_activated_plugin( $name ) ) {
					$data['title']  = __( 'Activated', 'elementskit-lite' );
					$data['status'] = "activated";
				} else {
					$data['title']          = __( 'Activate Now', 'elementskit-lite' );
					$data['status']         = 'installed';
					$data['activation_url'] = $this->activation_url( $name );
				}
			} else {
				$data['title']            = __( 'Install Now', 'elementskit-lite' );
				$data['status']           = 'not_installed';
				$data['installation_url'] = $this->installation_url( $name );
				$data['activation_url']   = $this->activation_url( $name );
			}

			return $data;
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
		
		/**
		 * Get emailkit post id
		 * 
		 * @access public
		 * @param string
		 * @return string
		 */
		private function get_emailkit_post_id($wc_template_type) {
			
			$args = array(
				'post_type'      => 'emailkit',
				'posts_per_page' => -1,
				'meta_query'     => array(
					'relation' => 'AND', // Use AND relation for matching both conditions
					array(
						'key'   => 'emailkit_template_type',
						'value' => $wc_template_type,
					),
				),
			);
		
			$query = new WP_Query($args);
			$post_ids = array();
		
			if ($query->have_posts()) {
				while ($query->have_posts()) {
					$query->the_post();
					$post_ids[] = get_the_ID();
				}
				wp_reset_postdata();
			}
			
			return $post_ids[0]?? null;
		}

		/**
		 * Enqueue script
		 * 
		 * @access public
		 * @return void
		 */
		function enqueue_script() {
			?>
			<script>
				var emailkit_woocommerce = {
					ajaxurl: "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>",
					nonce: "<?php echo esc_attr( wp_create_nonce( 'emailkit_nonce' ) ); ?>",
					rest_url: "<?php echo esc_url( get_rest_url( null, 'emailkit/v1/' ) ); ?>",
					rest_nonce: "<?php echo esc_attr( wp_create_nonce( 'wp_rest' ) ); ?>"
				}
			</script>
			<?php
		}
		
		/**
		 * Find emailkit template
		 * 
		 * @access public
		 * @param string
		 * @return array
		 */
		function find_emailkit_template($wc_template_type) {
			
			if ( class_exists('\EmailKit\Admin\TemplateList') && class_exists('\EmailKit\Admin\Emails\EmailLists') && method_exists('\EmailKit\Admin\TemplateList', 'get_templates') && method_exists('\EmailKit\Admin\Emails\EmailLists', 'woocommerce_email') ) {
				
				$templates = \EmailKit\Admin\TemplateList::get_templates();
				$template_title = \Emailkit\Admin\Emails\EmailLists::woocommerce_email($wc_template_type);
						
				foreach ( $templates as $key => $value ) {
					$email_type = $value['mail_type'];
					$template_email_type = $value['title'];
					
					if ($email_type == 'woocommerce' && $wc_template_type ==  $template_email_type) {
		
						return [
							'file' => $value['file'],
							'mail_type' => $email_type,
							'template_type' => $wc_template_type,
							'title' => $template_title,
						];
					}
				}
			}

			return [];
		}

		/**
		 * Enqueue style and script
		 * 
		 * @access public
		 * @return void
		 */
		public function emailkit_admin_head(){
			?>

			<style>
				.wpmet-onboard-dashboard .wpmet-plugin-install-activate {
					cursor: no-drop;
					background-color: #E8E9EF;
					color: #5D5E65;
					border-color: #E8E9EF;
				}
				.wpmet-emailkit-install-btn-wrapper .emailkit-open-new-form-editor-modal{
					background-color: #227BFF;
					border: none;
					font-size: 14px;
					font-weight: 500;
					line-height: 35px;
					color: #fff;
					border-radius: 4px;
					padding: 1px 23px 2px;
					-webkit-box-sizing: border-box;
					box-sizing: border-box;
					cursor: pointer;
					transition: all .3s;
				}
				.wpmet-emailkit-install-btn-wrapper .emailkit-open-new-form-editor-modal:hover{
					background:#1168E9;
				}

				.wpmet-emailkit-install-btn-wrapper .emailkit-open-new-form-editor-modal:disabled{
					background-color: #7aa2de;
					cursor: not-allowed;
				}
				.wpmet-emailkit-install-btn-wrapper .emailkit-open-new-form-editor-modal:disabled:hover{
					background-color: #7aa2de;
				}

				.emailkit-slider-loader{
					pointer-events: none;
					position: relative;
				}

				.emailkit-slider-loader::after {
					content: "";
					display: inline-block;
					width: 8px;
					height: 8px;
					border: 3px solid #f9f9f9f1;
					border-radius: 50%;
					border-top-color: #210d0d00;
					position: absolute;
					left: 6px;
					bottom: 13px;
					z-index: 99;
					animation: spin 1s ease-in-out infinite;
					-webkit-animation: spin 1s ease-in-out infinite;
				}
				@keyframes spin {
					to {
						-webkit-transform: rotate(360deg);
					}
				}
			</style>
			<script type="text/javascript">

				jQuery( document ).ready( function( $ ) {

					function disableBtn(el, exceptEl = '', isTrue = false){

						if(exceptEl !== ""){
							jQuery(el).not(exceptEl).each((index, item) => {
							jQuery(item).prop('disabled', isTrue);
						})
						} else {
							jQuery(el).each((index, item) => {
							jQuery(item).prop('disabled', isTrue);
						})
						}

						
					}


					let _emailkit_self = "";
					$('.wpmet-emailkit-install-activate').on('click', function(e){
						
						e.preventDefault();
						_emailkit_self = this;
						jQuery(_emailkit_self).addClass('emailkit-slider-loader');
						if(jQuery(_emailkit_self).next()){
							jQuery(_emailkit_self).next().css('display', 'none');
						}

						disableBtn('.wpmet-woocom-editwithemailkit', _emailkit_self, true);

						jQuery('.wpmet-woocom-editwithemailkit').not(_emailkit_self).each((index, item) => {
							jQuery(item).prop('disabled', true);
						})
						let installation_url = $(this).attr('href');
						let	activation_url = $(this).attr('data-activation_url');
						let	plugin_status = $(this).data('plugin_status');
						let	templateType = $(this).data('wc-template-type');

						if($(this).hasClass('wpmet-plugin-install-activate') || $(this).hasClass('activated')){
							return false;
						}

						if(plugin_status == 'not_installed'){
							wpmet_install_active_plugin.call(this, installation_url, () => {
								wpmet_install_active_plugin.call(this, activation_url, null, 'Activating...', 'Activated', templateType);
							}, 'Installing...', 'Installed');
						} else if (plugin_status == 'installed') {
							wpmet_install_active_plugin.call(this, activation_url, null, 'Activating...', 'Activated', templateType);
						}
					});

					// installing plugin
					function wpmet_install_active_plugin(ajaxurl, success_callback, beforeText, successText, templateType) {
						try {
							$.ajax({
								type: "GET",
								url: ajaxurl,
								beforeSend: () => {
									$(this).addClass('wpmet-plugin-install-activate');
									if (beforeText) {
										$(this).html(beforeText);
									}
								},
								success: (response) => {
									$(this).removeClass('wpmet-plugin-install-activate');

									if (ajaxurl.indexOf('action=activate') >= 0) {
										$(this).addClass('activated');
										sendToBuilderProcessing(templateType);
									}

									$(this).html('Proceeding...');

									if (success_callback) {
										success_callback();
									}
								},
								error: function (error) {
									jQuery(_emailkit_self).remove('emailkit-slider-loader');
									jQuery(_emailkit_self).next().css('display', 'block');
									disableBtn('.wpmet-woocom-editwithemailkit', false);
									console.error(error);
								}
							});
						} catch (error) {

							console.error("An error occurred:", error);
						}
					}

					function sendToBuilderProcessing( templateType ){
						try {
							$.ajax({
								url: emailkit_woocommerce.ajaxurl,
								method: 'POST',
								data: {
									'emailkit_nonce': emailkit_woocommerce.rest_nonce,
									'action': 'emailkit_get_builder_url',
									'emailkit_template_type': templateType
								},
								success: function (response) {

									let builderUrl = response?.builder_url;

									if (builderUrl) {

										window.location.href = builderUrl;

										return false;
									}
									rdirectToBuilder(response);
								},
								error: function (error) {
									jQuery(_emailkit_self).remove('emailkit-slider-loader');
									jQuery(_emailkit_self).next().css('display', 'block');
									disableBtn('.wpmet-woocom-editwithemailkit', false);
									console.error(error);
								}
							});
						} catch (error) {
							
							console.error("An error occurred:", error);
						}
					}

					function rdirectToBuilder(response){
						try {
							$.ajax({
								url: emailkit_woocommerce.rest_url + 'template-data',
								method: 'POST',
								headers: {
									'X-WP-Nonce': emailkit_woocommerce.rest_nonce,
								},
								data: {
									'emailkit-editor-template': response.emailkit_editor_template,
									'emailkit_email_type': response.emailkit_email_type,
									'emailkit_template_type': response.emailkit_template_type,
									'emailkit_template_status': 'active'
								},
								success: function (response) {
									jQuery(_emailkit_self).remove('emailkit-slider-loader');
									window.location.href = response.data.builder_url;
								},
								error: function (error) {
									jQuery(_emailkit_self).remove('emailkit-slider-loader');
									disableBtn('.wpmet-woocom-editwithemailkit', false);
									console.error(error);
								}
							});
						} catch (error) {
							
							console.error("An error occurred:", error);
						}
					}

					_emailkit_self = "";
				});
			</script>
			<?php
		}
	}
}