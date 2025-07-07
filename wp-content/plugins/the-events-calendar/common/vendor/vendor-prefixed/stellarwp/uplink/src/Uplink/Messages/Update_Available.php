<?php

namespace TEC\Common\StellarWP\Uplink\Messages;

use TEC\Common\StellarWP\ContainerContract\ContainerInterface;
use TEC\Common\StellarWP\Uplink\Resources\Resource;

class Update_Available extends Message_Abstract {
	/**
	 * Resource instance.
	 *
	 * @var Resource
	 */
	protected $resource;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Resource $resource Resource instance.
	 * @param ContainerInterface|null $container Container instance.
	 */
	public function __construct( Resource $resource, $container = null ) {
		parent::__construct( $container );

		$this->resource = $resource;
	}

	/**
	 * @inheritDoc
	 */
	public function get(): string {
		$link = sprintf( '<a href="%s">', $this->resource->get_home_url() ?: '' );

		return sprintf(
			esc_html__( 'There is an update for %s. You\'ll need to %scheck your license%s to have access to updates, downloads, and support.', 'tribe-common' ),
			$this->resource->get_name(),
			$link,
			'</a>'
		);
	}
}
