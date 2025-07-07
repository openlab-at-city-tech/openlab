<?php

namespace TEC\Common\StellarWP\Uplink\Messages;

use TEC\Common\StellarWP\ContainerContract\ContainerInterface;
use TEC\Common\StellarWP\Uplink\API\Client;
use TEC\Common\StellarWP\Uplink\Resources\Resource;

class API extends Message_Abstract {
	/**
	 * API message.
	 *
	 * @var string
	 */
	protected $message;

	/**
	 * Resource instance.
	 *
	 * @var Resource
	 */
	protected $resource;

	/**
	 * Resource version.
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message API message.
	 * @param string $version Resource version.
	 * @param Resource $resource Resource instance.
	 * @param ContainerInterface|null $container Container instance.
	 */
	public function __construct( string $message, string $version, Resource $resource, $container = null ) {
		parent::__construct( $container );

		$this->message  = $message;
		$this->version  = $version;
		$this->resource = $resource;
	}

	/**
	 * @inheritDoc
	 */
	public function get(): string {
		/** @var Client */
		$api = $this->container->get( Client::class );

		$message = $this->message;

		$message = str_replace( '%plugin_name%', $this->resource->get_name(), $message );
		$message = str_replace( '%plugin_slug%', $this->resource->get_slug(), $message );
		$message = str_replace( '%update_url%', trailingslashit( $api->get_api_base_url() ), $message );
		$message = str_replace( '%version%', $this->version, $message );
		$message = str_replace( '%changelog%', '<a class="thickbox" title="' . $this->resource->get_name() . '" href="plugin-install.php?tab=plugin-information&plugin=' . $this->resource->get_slug() . '&TB_iframe=true&width=640&height=808">what\'s new</a>', $message );

		return $message;
	}
}
