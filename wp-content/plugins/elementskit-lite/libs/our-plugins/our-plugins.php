<?php

namespace Wpmet\Libs;

defined( 'ABSPATH' ) || exit;

/**
 * Description: Wpmet Apps class. This class is used to display the wpmet other plugins
 * 
 * @package Wpmet\UtilityPackage
 * @subpackage Wpmet\UtilityPackage\Plugins
 * @author Wpmet
 * 
 * @since 1.0.0
 */
if ( ! class_exists( '\Wpmet\Libs\Our_Plugins' ) ) :

	class Our_Plugins {

		private static $instance;
		private $text_domain;
		private $parent_menu_slug;
		private $menu_slug = '_wpmet_plugins';
		private $submenu_name = 'Our Plugins';
		private $plugins = [];
		public $items_per_row = 4;
		private $section_title = 'Take your website to the next level';
		private $section_description = 'We have some plugins you can install to get most from Wordpress. These are absolute FREE to use.';
		private $installed_plugins = [];
		private $activated_plugins = [];

		/**
		 * Creates and returns an instance of the class.
		 *
		 * @return self
		 * 
		 * @since 1.0.0
		 */
		public static function instance() {
			
			if ( !self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
		
		/**
		 * Initializes the function.
		 *
		 * @param string $text_domain The text domain.
		 * @return $this
		 * 
		 * @since 1.0.0
		 */
		public function init( $text_domain ) {
			
			$this->set_text_domain( $text_domain );
			$this->collect_installed_plugins();
			$this->collect_activated_plugins();
			
			add_action('admin_head', [$this, 'enqueue_scripts']);

			return $this;
		}

		/**
		 * Set the section title.
		 *
		 * @param string $title The title of the section.
		 * @return $this The current object instance.
		 * 
		 * @since 1.0.0
		 */
		public function set_section_title( $title ){

			$this->section_title = $title;

			return $this;
		}

		/**
		 * Sets the description for the section.
		 *
		 * @param mixed $description The description for the section.
		 * @return $this
		 * 
		 * @since 1.0.0
		 */
		public function set_section_description( $description ){

			$this->section_description = $description;
			
			return $this;
		}

		/**
		 * Sets the number of items per row.
		 *
		 * @param int $items The number of items per row.
		 * @return $this The current object instance.
		 * 
		 * @since 1.0.0
		 */
		public function set_items_per_row( $items ){

			$this->items_per_row = $items;
			
			return $this;
		}

		/**
		 * Set the text domain for the object.
		 *
		 * @param mixed $val The value to set as the text domain.
		 * @return $this The current object instance.
		 * 
		 * @since 1.0.0
		 */
		protected function set_text_domain( $val ) {

			$this->text_domain = $val;

			return $this;
		}

		/**
		 * Sets the submenu name.
		 *
		 * @param string $submenu_name The name of the submenu.
		 * @return $this The current instance of the class.
		 * 
		 * @since 1.0.0
		 */
		public function set_submenu_name( $submenu_name ){

			$this->submenu_name = $submenu_name;
			
			return $this;
		}

		/**
		 * Set the parent menu slug.
		 *
		 * @param string $slug The slug of the parent menu.
		 * @return $this The current object.
		 */
		public function set_parent_menu_slug( $slug ) {

			$this->parent_menu_slug = $slug;

			return $this;
		}

		/**
		 * Sets the menu slug for the object.
		 *
		 * @param string $slug The slug to set for the menu.
		 * @return $this Returns the current object.
		 * 
		 * @since 1.0.0
		 */
		public function set_menu_slug( $slug ) {

			$this->menu_slug = $slug;

			return $this;
		}

		/**
		 * Set the plugins for the object.
		 *
		 * @param array $plugins An array of plugins.
		 * @return $this The current instance.
		 * 
		 * @since 1.0.0
		 */
		public function set_plugins( $plugins = [] ) {
			
			$this->plugins = $plugins;

			return $this;
		}

		/**
		 * Registers a menu in the WordPress admin dashboard.
		 *
		 * @return void
		 * 
		 * @since 1.0.0
		 */
		protected function register_menu() {
			add_submenu_page( 
				$this->parent_menu_slug,
				$this->submenu_name,
				'<span style="color: #2fbf17; font-weight: bold">' . $this->submenu_name . '</span>',
				'manage_options',
				$this->text_domain . $this->menu_slug,
				[$this, 'wpmet_apps_renderer'],
				5
			);
		}

		/**
		 * Generates the menus.
		 *
		 * @return void
		 * 
		 * @since 1.0.0
		 */
		public function generate_menus() {
			
			if( !empty($this->parent_menu_slug) ) {

				$this->register_menu();
			}
		}

		/**
		 * Admin menu registration hook.
		 *
		 * @return void
		 * 
		 * @since 1.0.0
		 */
		public function call() {
			add_action('admin_menu', [$this, 'generate_menus'], 99999);
		}

		/**
		 * Activation URL
		 * 
		 * @since 1.0.0
		 * @param string $pluginName The name of the plugin.
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
		 * Installation URL
		 * 
		 * @since 1.0.0
		 * @param string $pluginName The name of the plugin.
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
		 * @since 1.0.0
		 * @param string $name The name of the plugin.
		 * @return string
		 */
		public function get_plugin_slug( $name ) {
			
			$split = explode( '/', $name );

			return isset( $split[0] ) ? $split[0] : null;
		}

		/**
		 * Activated URL
		 * 
		 * @since 1.0.0
		 * @param string $pluginName The name of the plugin.
		 * @return string
		 */
		public function activated_url( $pluginName ) {
			return add_query_arg(
				array(
					'page' => $this->get_plugin_slug( $pluginName ),
				),
				admin_url( 'admin.php' ) );
		}

		/**
		 * Collect installed plugins
		 * 
		 * @since 1.0.0
		 * @return void
		 */
		private function collect_installed_plugins() {

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
		 * @since 1.0.0
		 * @return void
		 */
		private function collect_activated_plugins() {
			foreach ( apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) as $plugin ) {
				array_push( $this->activated_plugins, $plugin );
			}
		}

		/**
		 * Check installed plugin
		 * 
		 * @since 1.0.0
		 * @param string $name The name of the plugin.
		 * @return bool
		 */
		public function check_installed_plugin( $name ) {
			return in_array( $name, $this->installed_plugins );
		}

		/**
		 * Check activated plugin
		 * 
		 * @since 1.0.0
		 * @param string $name The name of the plugin.
		 * @return bool
		 */
		public function check_activated_plugin( $name ) {
			return in_array( $name, $this->activated_plugins );
		}

		/**
		 * Get plugin status
		 * 
		 * @since 1.0.0
		 * @param string $name The name of the plugin.
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
		 * Display the Wpmet apps section.
		 * 
		 * @return void
		 *
		 * @since 1.0.0
		 */
		public function wpmet_apps_renderer() {
			?>
			<div class="wpmet-onboard-dashboard">
				<div class="wpmet-onboard-main-header">
					<h1 class="wpmet-onboard-main-header--title"><strong><?php echo esc_html($this->section_title); ?></strong></h1>
					<p class="wpmet-onboard-main-header--description"><?php echo esc_html($this->section_description); ?></p>
				</div>

				<div class="wpmet-onboard-plugin-list">
					<div class="attr-row">
						<?php
						foreach( $this->plugins as $key => $plugin ):
							$img_url = isset($plugin['icon']) ? $plugin['icon'] : '#';
							$plugin_name = isset($plugin['name']) ? $plugin['name'] : '';
							$plugin_desc = isset($plugin['desc']) ? $plugin['desc'] : '';
							$plugin_docs = isset($plugin['docs']) ? $plugin['docs'] : '';
						?>
						<div class="attr-col-lg-4">
							<div class="wpmet-onboard-single-plugin">
								<label>
									<img class="wpmet-onboard-single-plugin--logo" src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($plugin_name);?>">
									<h4 class="wpmet-single-plugin--name"><?php echo esc_html($plugin_name); ?></h4>
									<p class="wpmet-onboard-single-plugin--description"><?php echo esc_html($plugin_desc); ?></p>
									<?php 
									$plugin_data = $this->get_plugin_status( $key );
									$plugin_status = isset( $plugin_data['status'] ) ?  $plugin_data['status']  : '';
									$plugin_activation_url = isset( $plugin_data['activation_url'] ) ? $plugin_data['activation_url'] : '';
									$plugin_installation_url = isset( $plugin_data['installation_url'] ) ? $plugin_data['installation_url'] : '';
									$plugin_status_label = isset( $plugin_data['status'] ) ? ( $plugin_data['status'] == 'activated' ? 'activated' : '' ) : '';
									$plugin_status_title = isset( $plugin_data['title'] ) ? $plugin_data['title'] : esc_html__('Activate', 'elementskit-lite');
									?>
									<div class="wpmet-apps-footer">
										<?php
										echo sprintf(
											'<a data-plugin_status="%1$s" data-activation_url="%2$s" href="%3$s" class="wpmet-pro-btn wpmet-onboard-single-plugin--install_plugin %4$s">%5$s</a>',
											esc_attr($plugin_status),
											esc_url($plugin_activation_url),
											esc_url($plugin_installation_url),
											esc_attr($plugin_status_label),
											esc_html($plugin_status_title)
										);

										if( !empty($plugin_docs) ) :
											echo sprintf(
												'<a target="_blank" href="%1$s" class="wpmet-onboard-tut-term--help">%2$s</a>',
												esc_url($plugin_docs),
												esc_html__('Read Docs', 'elementskit-lite')
											);
										endif;
										?>
									</div>
								</label>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php
		}

		/**
		 * Enqueues scripts for the plugin.
		 *
		 * This function is responsible for enqueueing the necessary JavaScript scripts for the plugin.
		 *
		 * @return void
		 * 
		 * @since 1.0.0
		 */
		public function enqueue_scripts(){
			?>
			<style>
				.wpmet-onboard-dashboard .wpmet-onboard-plugin-list .attr-row {
					margin-left: -11px;
					margin-right: -11px;
					display: -webkit-box;
					display: grid;
					gap: 5px;
					grid-template-columns: repeat(<?php echo esc_attr($this->items_per_row); ?>, 1fr);
				}
				.wpmet-onboard-dashboard .wpmet-onboard-plugin-list .attr-row > div {
					padding: 11px;
				}
				.wpmet-onboard-main-header{
					margin-bottom: 30px;
				}
				.wpmet-onboard-dashboard .wpmet-onboard-main-header--title{
					color: #021343;
					font-size: 40px;
					line-height: 54px;
					font-weight: normal;
					margin: 0 0 3px 0;
					padding: 0;
				}
				.wpmet-onboard-dashboard .wpmet-onboard-main-header--title strong{
					font-weight: 700;
				}
				.wpmet-onboard-dashboard .wpmet-onboard-main-header--description{
					color: #5d5e65;
					font-size: 16px;
					line-height: 26px;
					margin: 0;
				}
				[class^="attr"] {
					-webkit-box-sizing: border-box;
							box-sizing: border-box;
				}
				.wpmet-onboard-dashboard {
					background-color: #F5F6F9;
					margin-left: -20px;
					padding: 30px;
					position: absolute;
					top: 0;
					left: 0;
					z-index: 1;
					width: calc(100% + 20px);
					-webkit-box-sizing: border-box;
							box-sizing: border-box;
					min-height: calc(100vh - 32px);
					padding-top: 30px;
					padding-bottom: 100px;
				}
				.wpmet-onboard-dashboard .wpmet-pro-btn {
					color: #3E77FC;
					font-size: 15px;
					line-height: 18px;
					background-color: transparent;
					font-weight: 500;
					border: 1.5px solid #3E77FC;
					border-radius: 6px;
					padding: 11px 32px;
					-webkit-transition: all .4s;
					transition: all .4s;
					text-decoration: none;
					display: inline-block;
				}
				.wpmet-onboard-dashboard .wpmet-pro-btn:hover {
					background-color: #3E77FC;
					color: #fff;
				}
				.wpmet-onboard-dashboard .wpmet-pro-btn:focus {
					border-color: #3E77FC;
					-webkit-box-shadow: none;
							box-shadow: none;
				}
				.wpmet-onboard-dashboard .wpmet-pro-btn .icon {
					position: relative;
					top: 1px;
				}
				.wpmet-onboard-dashboard .wpmet-onboard-single-plugin {
					background-color: #fff;
					border-radius: 6px;
					-webkit-box-shadow: 0 30px 50px rgba(0, 10, 36, 0.1);
							box-shadow: 0 30px 50px rgba(0, 10, 36, 0.1);
					position: relative;
					min-height: 320px;
				}
				.wpmet-onboard-dashboard .wpmet-onboard-single-plugin label {
					display: block;
					padding: 30px 40px 36px 30px;
					cursor: default;
				}
				.wpmet-onboard-dashboard .wpmet-onboard-single-plugin .badge--featured {
					position: absolute;
					right: -20px;
					top: -30px;
					height: 100px;
				}
				.wpmet-onboard-dashboard .wpmet-onboard-single-plugin--install {
					color: #021343;
					font-size: 15px;
					font-weight: 500;
					display: block;
					border: 2px solid #E4E6EE;
					border-radius: 6px;
					min-height: 175px;
					line-height: 175px;
					position: relative;
					text-decoration: none;
				}
				.wpmet-onboard-dashboard .wpmet-onboard-single-plugin--install i {
					padding-left: 9px;
					font-weight: bold;
				}
				.wpmet-onboard-dashboard .wpmet-onboard-single-plugin--description {
					color: #5D5E65;
					font-size: 15px;
					line-height: 22px;
					font-weight: 400;
					margin: 0;
				}
				.wpmet-onboard-dashboard .wpmet-single-plugin--name {
					display: block;
					font-size: 1.6rem;
					line-height: normal;
					text-decoration: none;
					margin: 0px;
					font-weight: 600;
					color: #021343;
					margin-top: 5px;
					margin-bottom: 15px;
				}
				.wpmet-onboard-dashboard .wpmet-onboard-single-plugin--description span {
					background: #d7a1f973;
					color: #021343;
					font-weight: 500;
				}
				.wpmet-onboard-dashboard .wpmet-onboard-single-plugin--logo {
					max-width: 60px;
				}
				.wpmet-onboard-dashboard .wpmet-onboard-single-plugin--install_plugin {
					padding: 5px 20px 7px 20px;
				}
				.wpmet-onboard-dashboard .wpmet-apps-footer{
					position: absolute;
					bottom: 30px;
					width: 80%;
					display: flex;
					justify-content: space-between;
					align-items: baseline;
					background: #fff;
					padding-top: 5px;
				}
				.wpmet-onboard-dashboard .wpmet-onboard-single-plugin--install_plugin.wpmet-plugin-install-activate {
					cursor: no-drop;
					background-color: #E8E9EF;
					color: #5D5E65;
					border-color: #E8E9EF;
				}
				.wpmet-onboard-dashboard .wpmet-onboard-single-plugin--install_plugin.activated {
					cursor: default;
					border: 1px solid #2AAE1433;
					background: rgba(42, 174, 20, 0.1);
					color: #2AAE14;
				}
				.wpmet-onboard-dashboard .wpmet-onboard-tut-term--help {
					margin: 0;
					color: #021343;
					font-size: 14px;
					font-weight: 500;
					line-height: 26px;
					margin-top: 10px;
					cursor: pointer;
				}
				.wpmet-onboard-dashboard .wpmet-onboard-tut-term--help:hover {
					color: #3E77FC;
				}
				.wpmet-onboard-dashboard .wpmet-onboard-tut-term--help.active {
					color: #3E77FC;
				}
				@media (max-width: 2000px) {
					.wpmet-onboard-dashboard .wpmet-onboard-plugin-list .attr-row {
						grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
					}
				}
				@media (max-width: 500px) {
					.wpmet-onboard-dashboard .wpmet-onboard-plugin-list .attr-row {
						grid-template-columns: repeat(auto-fit, minmax(100%, 1fr));
					}
				}
				@media (max-width: 991px) {
					body .wpmet-onboard-dashboard img {
						max-width: 100%;
					}
				}
				@media (max-width: 480px) {
					body .wpmet-onboard-dashboard .wpmet-onboard-single-plugin label {
						-webkit-box-align: center;
							-ms-flex-align: center;
								align-items: center;
					}
				}
			</style>

			<script type="text/javascript">
				jQuery( document ).ready( function( $ ) {
						// installing plugin
					function wpmet_install_active_plugin(ajaxurl, success_callback, beforeText, successText){
						$.ajax({
							type : "GET",
							url : ajaxurl,
							beforeSend: () => {
								$(this).addClass('wpmet-plugin-install-activate');
								if(beforeText){
									$(this).html(beforeText);
								}
							},
							success: (response) => {
								$(this).removeClass('wpmet-plugin-install-activate');
								
								if(ajaxurl.indexOf('action=activate') >= 0){
									$(this).addClass('activated');
								}

								$(this).html(successText);

								if(success_callback){success_callback();}
							}
					});
					}

					$('.wpmet-onboard-single-plugin--install_plugin').on('click', function(e){
						e.preventDefault();
						var installation_url = $(this).attr('href'),
							activation_url = $(this).attr('data-activation_url'),
							plugin_status = $(this).data('plugin_status');

						if($(this).hasClass('wpmet-plugin-install-activate') || $(this).hasClass('activated')){
							return false;
						}

						if(plugin_status == 'not_installed'){
							wpmet_install_active_plugin.call(this, installation_url, () => {
								wpmet_install_active_plugin.call(this, activation_url, null, 'Activating...', 'Activated');
							}, 'Installing...', 'Installed');
						} else if (plugin_status == 'installed') {
							wpmet_install_active_plugin.call(this, activation_url, null, 'Activating...', 'Activated');
						}
					});

					jQuery('.wpmet-onboard-tut-term--help').on('click', function(){
						$(this).toggleClass('active').prev().toggleClass('active');
					});

				});
			</script>
			<?php
		}
	}

endif;