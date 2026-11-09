<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Notice;

use TEC\Common\LiquidWeb\Harbor\Components\Controller;
use TEC\Common\LiquidWeb\Harbor\View\Exceptions\FileNotFoundException;

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
	 * @param array{type?: string, message?: string, dismissible?: bool, alt?: bool, large?: bool, id?: string} $args The notice.
	 *
	 * @throws FileNotFoundException If the view is not found.
	 *
	 * @return void
	 */
	public function render( array $args = [] ): void {
		$type        = $args['type'] ?? 'info';
		$dismissible = $args['dismissible'] ?? false;
		$alt         = $args['alt'] ?? false;
		$large       = $args['large'] ?? false;
		$message     = $args['message'] ?? '';
		$id          = $args['id'] ?? '';

		$classes = [
			'notice',
			sprintf( 'notice-%s', $type ),
			$dismissible ? 'is-dismissible' : '',
			$alt ? 'notice-alt' : '',
			$large ? 'notice-large' : '',
		];

		$this->view->display(
			self::VIEW,
			[
				'message'           => $message,
				'id'                => esc_attr( $id ),
				'classes'           => esc_attr( $this->classes( $classes ) ),
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
			]
		);
	}
}
