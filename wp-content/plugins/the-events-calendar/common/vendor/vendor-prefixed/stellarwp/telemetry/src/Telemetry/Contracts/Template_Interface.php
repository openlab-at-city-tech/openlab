<?php
/**
 * Provides an API for rendering templates.
 *
 * @since 1.0.0
 *
 * @package TEC\Common\StellarWP\Telemetry\Contracts
 *
 * @license GPL-2.0-or-later
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace TEC\Common\StellarWP\Telemetry\Contracts;

/**
 * Interface that provides an API for rendering templates.
 */
interface Template_Interface {
	/**
	 * Renders the template.
	 *
	 * @since 1.0.0
	 * @since 2.0.0 - Update to handle passed in stellar slug.
	 *
	 * @param string $stellar_slug The stellar slug to be referenced when the modal is rendered.
	 *
	 * @return void
	 */
	public function render( string $stellar_slug );

	/**
	 * Determines if the template should be rendered.
	 *
	 * @since 1.0.0
	 * @since 2.0.0 - Update to handle passed in stellar slug.
	 *
	 * @param string $stellar_slug The stellar slug for which the modal should be rendered.
	 *
	 * @return boolean
	 */
	public function should_render( string $stellar_slug );
}
