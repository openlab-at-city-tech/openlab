<?php

namespace ColibriWP\Theme\Core;

use ColibriWP\Theme\PluginsManager;
use ColibriWP\Theme\Theme;
use TGM_Plugin_Activation;

const PRO = '-pro';

class EnableKubioInCustomizerPanel extends \WP_Customize_Panel {

	private $plugin_slug    = 'kubio';
	private $plugin_version = '';

	public function __construct( $manager, $id, $args = array() ) {

		$manager->add_section(
			"{$id}-section",
			array( 'panel' => $id )
		);

		$manager->add_control(
			"{$id}-control",
			array(
				'section'    => "{$id}-section",
				'settings'   => array(),
				'type'       => 'button',
				'capability' => 'manage_options',
			)
		);

		add_action( 'customize_controls_print_footer_scripts', array( $this, 'printScripts' ) );
		parent::__construct( $manager, $id, $args );
	}

	public function getInstallLink() {

		return
			add_query_arg(
				array(
					'plugin'        => urlencode( $this->plugin_slug ),
					'tgmpa-install' => 'install-plugin',
				),
				TGM_Plugin_Activation::get_instance()->get_tgmpa_url()
			);

	}

	public function getActivationLink( $slug ) {
		$tgmpa = TGM_Plugin_Activation::get_instance();

		if ( isset( $tgmpa->plugins[ $slug ] ) ) {
			return
				add_query_arg(
					array(
						'plugin'         => urlencode( $slug ),
						'tgmpa-activate' => 'activate-plugin',
					),
					TGM_Plugin_Activation::get_instance()->get_tgmpa_url()
				);
		}
	}

	public function getPluginState( $slug ) {
		$tgmpa         = TGM_Plugin_Activation::get_instance();
		$installed     = $tgmpa->is_plugin_installed( $slug );
		$installed_pro = $tgmpa->is_plugin_installed( $slug . PRO );
		$result        = PluginsManager::NOT_INSTALLED_PLUGIN;

		$active_plugins = get_option( 'active_plugins' );

		// check if free or pro version of the plugins are active
		if ( in_array( 'kubio/plugin.php', $active_plugins ) || in_array( 'kubio-pro/plugin.php', $active_plugins ) ) {
			return PluginsManager::ACTIVE_PLUGIN;
		}

		if ( $installed_pro ) {
			$this->plugin_version = PRO;
		}

		if ( $installed || $installed_pro ) {
			$result = PluginsManager::INSTALLED_PLUGIN;

			if ( $tgmpa->is_plugin_active( $slug . $this->plugin_version ) ) {
				$result = PluginsManager::ACTIVE_PLUGIN;
			}
		}

		return $result;
	}

	public function printScripts() {
		?>
		<style>
			.kubio-customizer-panel {
				margin: 10px;
				border: none !important;
			}

			.kubio-customizer-panel .accordion-section-title {
				cursor: default;
				border: 1px solid #ddd !important;
				box-shadow: none !important;
			}

			.kubio-customizer-panel .accordion-section-title:after {
				display: none;
			}

			.kubio-customizer-panel p {
				font-weight: normal;
				font-size: 13px;
				margin: 0 0 10px 0;
			}

			.kubio-customizer-panel .button.button-primary svg {
				fill: currentColor;
				width: 1em;
				height: 1em;
				margin-right: 0.5em;
			}

			.kubio-customizer-panel .button.button-primary {
				align-items: center;
				width: 100%;
				display: flex;
				justify-content: center;
				padding: 5px 10px;
			}
		</style>
		<?php
	}

	protected function render() {

		$message = sprintf(
			__( 'The Kubio plugin takes %1$s to a whole new level by adding new and powerful editing and styling options. Wanna have full control over your design with %1$s?', 'mistify' ),
			wp_get_theme( get_stylesheet() )->get( 'Name' )
		);

		?>
		<li class="accordion-section kubio-customizer-panel">
		<?php if ( $this->getPluginState( $this->plugin_slug ) !== PluginsManager::ACTIVE_PLUGIN ) : ?>
			<div class="accordion-section-title">
				<p><?php echo esc_html( $message ); ?></p>
				<?php if ( $this->getPluginState( $this->plugin_slug ) === PluginsManager::NOT_INSTALLED_PLUGIN ) : ?>
				<button data-colibri-plugin-action="install" data-source="customizer-sidebar" class="button button-primary kubio-open-editor-panel-button ">
					<span data-action="install"><?php esc_html_e( 'Install Kubio', 'mistify' ); ?></span>
				</button>
			<?php elseif ( $this->getPluginState( $this->plugin_slug ) === PluginsManager::INSTALLED_PLUGIN ) : ?>
				<button data-colibri-plugin-action="activate" data-source="customizer-sidebar" class="button button-primary kubio-open-editor-panel-button">
					<span data-action="activate"><?php esc_html_e( 'Activate Kubio', 'mistify' ); ?></span>
				</button>
			<?php endif; ?>
			</div>
		<?php endif; ?>
		</li>
		<?php
	}

}
