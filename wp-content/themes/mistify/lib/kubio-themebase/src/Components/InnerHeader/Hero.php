<?php


namespace Kubio\Theme\Components\InnerHeader;

use ColibriWP\Theme\Core\Hooks;
use ColibriWP\Theme\View;
use Kubio\Theme\Components\Common\HeroStyle;

class Hero extends \Kubio\Theme\Components\FrontHeader\Hero {
	static $settings_prefix = 'header.hero.';

	public static function getStyleComponent() {
		return HeroStyle::getInstance(
			static::$settings_prefix,
			static::selectiveRefreshSelector()
		);
	}

	protected static function getOptions( $include_content_settings = true ) {
		$options = parent::getOptions( false );

		return $options;
	}

	public function renderContent( $parameters = array() ) {
		View::partial(
			'header',
			'hero',
			array(
				'component' => $this,
			)
		);
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
			$bgImageSanitized = esc_url( $bgImage );
			echo "background-image:url('$bgImageSanitized')";
		}
	}


}
