<?php

namespace OpenLab\Attributions\Shortcode;

use function OpenLab\Attributions\Settings\get_settings;
use function OpenLab\Attributions\Helpers\get_supported_post_types;

class References {

	/**
	 * Attribution references.
	 *
	 * @var array
	 */
	protected $refs = [];

	/**
	 * Register shortcode and filters.
	 *
	 * @return void
	 */
	public function register() {
		add_shortcode( 'ref', [ $this, 'shortcode' ] );
		add_filter( 'the_content', [ $this, 'content' ], 12 );
	}

	/**
	 * Proccess [ref] shortcode.
	 *
	 * @param array  $atts    User defined attributes for this shortcode instance.
	 * @param string $content Content between the opening and closing shortcode elements
	 * @return string
	 */
	public function shortcode( $atts, $content = null ) {
		$post = get_post();
		$id   = $post ? $post->ID : 0;

		if ( null === $content ) {
			return;
		}

		if ( ! isset( $this->refs[ $id ] ) ) {
			$this->refs[ $id ] = [ 0 => false ];
		}

		$this->refs[ $id ][] = $content;
		$attr = count( $this->refs[ $id ] ) - 1;

		return sprintf(
			'<a id="%1$d-anchor" class="note-anchor" href="#attr-%1$d" aria-labelledby="attributions"><sup>%1$d</sup></a>.',
			$attr
		);
	}

	/**
	 * Display attributions for the post.
	 *
	 * @param string $content
	 * @return string $content
	 */
	public function content( $content ) {
		if ( ! is_singular( get_supported_post_types() ) ) {
			return $content;
		}

		$post = get_post();
		if ( empty( $this->refs[ $post->ID ] ) ) {
				return $content;
		}

		$settings = get_settings();

		$content .= sprintf( '<footer><p id="attributions">%s:</p><ol>', $settings['title'] );
		foreach ( array_filter( $this->refs[ $post->ID ] ) as $num => $note ) {
				$content .= sprintf(
					'<li id="attr-%1$d">%2$s<a href="#%1$d-anchor">&#8617;</a></li>',
					$num,
					$note
				);
		}
		$content .= '</ol></footer>';

		return $content;
	}
}
