<?php


namespace ColibriWP\Theme\Components\InnerHeader;

use ColibriWP\Theme\Components\FrontHeader\Hero as FrontHero;
use ColibriWP\Theme\Core\Hooks;
use ColibriWP\Theme\View;

class Hero extends FrontHero {
	protected static $settings_prefix = 'header_post.hero.';

	protected static function getOptions( $include_content_settings = true ) {
		$options = parent::getOptions( false );

		return $options;
	}

	public function printPostFeaturedImage() {
		$bgImage = '';
		if ( Hooks::prefixed_apply_filters( 'override_with_thumbnail_image', false ) ) {
			global $post;
			if ( $post ) {
				$thumbnail = get_the_post_thumbnail_url( $post->ID, 'mesmerize-full-hd' );

				$thumbnail = Hooks::prefixed_apply_filters( 'overriden_thumbnail_image', $thumbnail );

				if ( $thumbnail ) {
					$bgImage = $thumbnail;
				}
			}
		}

		if ( $bgImage ) {
			echo "background-image:url('$bgImage')";
		}
	}

	public function renderContent( $parameters = array() ) {
		View::partial(
			'inner-header',
			'hero',
			array(
				'component' => $this,
			)
		);
	}
}
