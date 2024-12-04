<?php


namespace ColibriWP\Theme\Customizer\Controls;

use ColibriWP\Theme\Core\Hooks;
use ColibriWP\Theme\PluginsManager;
use ColibriWP\Theme\Theme;
use ColibriWP\Theme\Translations;

class PluginMessageControl extends VueControl {

	public $type        = 'colibri-plugin-message';
	public static $slug = null;
	protected function printVueContent() {

		$this->addData();

		?>
		<div class="plugin-message card">
			<p>
				<?php echo Translations::get( 'plugin_message', 'Kubio Page Builder' ); ?>
			</p>
			<?php if ( Theme::getInstance()->getPluginsManager()->getPluginState( $this->getBuilderSlug() ) === PluginsManager::NOT_INSTALLED_PLUGIN ) : ?>
				<button data-colibri-plugin-action="install"
						class="el-button el-link h-col el-button--primary el-button--small"
						style="text-decoration: none">
					<?php echo Translations::get( 'install_with_placeholder', 'Kubio Page Builder' ); ?>
				</button>
			<?php endif; ?>

			<?php if ( Theme::getInstance()->getPluginsManager()->getPluginState( $this->getBuilderSlug() ) === PluginsManager::INSTALLED_PLUGIN ) : ?>
				<button data-colibri-plugin-action="activate"
						class="el-button el-link h-col el-button--primary el-button--small"
						style="text-decoration: none">
					<?php echo Translations::get( 'activate_with_placeholder', 'Kubio Page Builder' ); ?>
				</button>
			<?php endif; ?>

			<p class="notice notice-large" data-colibri-plugin-action-message="1" style="display: none"></p>
		</div>
		<?php
	}

	protected function getBuilderSlug() {
		if ( self::$slug ) {
			return self::$slug;
		}
		$builder_plugin    = 'kubio';
		$installed_plugins = get_plugins();
		foreach ( $installed_plugins as $key => $plugin_data ) {
			if ( strpos( $key, 'kubio-pro' ) !== false ) {
				$builder_plugin = 'kubio-pro';
			}
		}
		self::$slug = $builder_plugin;

		return self::$slug;
	}
	public function addData() {

		if ( Hooks::prefixed_apply_filters( 'plugin-customizer-controller-data-added', false ) ) {
			return;
		}

		Hooks::prefixed_add_filter( 'plugin-customizer-controller-data-added', '__return_true' );

		add_action(
			'customize_controls_print_footer_scripts',
			function () {

				$data = array(
					'theme_prefix'          => Theme::prefix( '', false ),
					'slug'                  => $this->getBuilderSlug(),
					'status'                => Theme::getInstance()->getPluginsManager()->getPluginState( 'kubio' ),
					'install_url'           => Theme::getInstance()->getPluginsManager()->getInstallLink( 'kubio' ),
					'activate_url'          => Theme::getInstance()->getPluginsManager()->getActivationLink( 'kubio' ),
					'plugin_activate_nonce' => wp_create_nonce( 'plugin_activate_nonce' ),
					'messages'              => array(
						'installing' => Translations::get(
							'installing',
							'Kubio Page Builder'
						),
						'activating' => Translations::get(
							'activating',
							'Kubio Page Builder'
						),
					),
					'admin_url'             => add_query_arg(
						array(
							'colibri_create_pages'       => '1',
							'colibri_generator_callback' => 'customizer',
						),
						admin_url()
					),
				);
				?>
			<script>
				window.colibriwp_plugin_status = <?php echo json_encode( $data ); ?>;
			</script>
				<?php
			},
			PHP_INT_MAX
		);

	}
}
