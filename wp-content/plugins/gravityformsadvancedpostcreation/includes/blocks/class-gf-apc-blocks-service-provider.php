<?php

namespace Gravity_Forms\Gravity_Forms_APC\Blocks;

use Gravity_Forms\Gravity_Forms\Config\GF_Config_Service_Provider;
use Gravity_Forms\Gravity_Forms_APC\Blocks\Config\GF_APC_Blocks_Config;

use Gravity_Forms\Gravity_Forms\GF_Service_Container;
use Gravity_Forms\Gravity_Forms\GF_Service_Provider;


/**
 * Class GF_APC_Blocks_Service_Provider
 *
 * Service provider for the APC Blocks Service.
 *
 * @package Gravity_Forms\Gravity_Forms\Blocks;
 */
class GF_APC_Blocks_Service_Provider extends GF_Service_Provider {

	// Configs
	const BLOCKS_CONFIG = 'apc_blocks_config';

	// Attributes
	const APC_POSTS_BLOCK_ATTRIBUTES = 'apc_posts_block_attributes';

	/**
	 * Array mapping config class names to their container ID.
	 *
	 * @since 1.6.0
	 *
	 * @var string[]
	 */
	protected $configs = array(
		self::BLOCKS_CONFIG => GF_APC_Blocks_Config::class,
	);

	/**
	 * Register services to the container.
	 *
	 * @since 1.6.0
	 *
	 * @param GF_Service_Container $container
	 */
	public function register( GF_Service_Container $container ) {
		require_once plugin_dir_path( __FILE__ ) . '/config/class-gf-apc-blocks-config.php';
		$container->add(
			self::APC_POSTS_BLOCK_ATTRIBUTES,
			function () {
				return array(
					'formId'        => array(
						'type'    => 'string',
						'default' => '',
					),
					'formToShow'    => array(
						'type'    => 'string',
						'default' => '',
					),
					'postsPerPage'  => array(
						'type'    => 'string',
						'default' => 5,
					),
					'clientId'      => array(
						'type'    => 'string',
						'default' => '',
					),
					'gridTitle'     => array(
						'type'    => 'string',
						'default' => '',
					),
					'gridStyle'     => array(
						'type'    => 'string',
						'default' => '',
					),
					'buttonColor'   => array(
						'type'    => 'string',
						'default' => '',
					),
					'transparentBg' => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'hideDate'      => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'hideStatus'    => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'loginMessage'  => array(
						'type'    => 'string',
						'default' => __( 'You must be logged in to view your posts.', 'gravityformsadvancedpostcreation' ),
					),
				);
			}
		);

		$this->add_configs( $container );
	}


	/**
	 * For each config defined in $configs, instantiate and add to container.
	 *
	 * @since 1.6.0
	 *
	 * @param GF_Service_Container $container
	 *
	 * @return void
	 */
	private function add_configs( GF_Service_Container $container ) {
		foreach ( $this->configs as $name => $class ) {
			$container->add(
				$name,
				function () use ( $container, $class ) {
					if ( $class === GF_APC_Blocks_Config::class ) {
						return new $class( $container->get( GF_Config_Service_Provider::DATA_PARSER ), $container->get( self::APC_POSTS_BLOCK_ATTRIBUTES ) );
					}

					return new $class( $container->get( GF_Config_Service_Provider::DATA_PARSER ) );
				}
			);

			$container->get( GF_Config_Service_Provider::CONFIG_COLLECTION )->add_config( $container->get( $name ) );
		}
	}
}
