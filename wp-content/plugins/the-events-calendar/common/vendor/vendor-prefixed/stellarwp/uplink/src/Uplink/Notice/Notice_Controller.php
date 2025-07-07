<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Notice;

use TEC\Common\StellarWP\Uplink\Components\Controller;
use TEC\Common\StellarWP\Uplink\View\Exceptions\FileNotFoundException;

/**
 * Renders a notice.
 */
final class Notice_Controller extends Controller {

	/**
	 * The view file, without ext, relative to the root views directory.
	 */
	public const VIEW = 'admin/notice';

	/**
	 * Render a notice.
	 *
	 * @see Notice::toArray()
	 * @see src/views/admin/notice.php
	 *
	 * @param  array{type?: string, message?: string, dismissible?: bool, alt?: bool, large?: bool}  $args The notice.
	 *
	 * @throws FileNotFoundException If the view is not found.
	 *
	 * @return void
	 */
	public function render( array $args = [] ): void {
		$classes = [
			'notice',
			sprintf( 'notice-%s', $args['type'] ),
			$args['dismissible'] ? 'is-dismissible' : '',
			$args['alt'] ? 'notice-alt' : '',
			$args['large'] ? 'notice-large' : '',
		];

		echo $this->view->render( self::VIEW, [
			'message'           => $args['message'],
			'classes'           => $this->classes( $classes ),
			'allowed_tags'      => [
				'a'      => [
					'href'   => [],
					'title'  => [],
					'target' => [],
					'rel'    => [],
				],
				'br'     => [],
				'code'   => [],
				'em'     => [],
				'pre'    => [],
				'span'   => [],
				'strong' => [],
			],
			'allowed_protocols' => [
				'http',
				'https',
				'mailto',
			],
		] );
	}

}
