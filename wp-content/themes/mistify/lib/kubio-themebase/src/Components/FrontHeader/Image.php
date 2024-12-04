<?php


namespace Kubio\Theme\Components\FrontHeader;

use ColibriWP\Theme\Components\FrontHeader\Image as BaseImage;
use ColibriWP\Theme\View;

class Image extends BaseImage {

	protected static $settings_prefix = 'front-header.hero.image.';
	protected static $selector        = '.wp-block-kubio-hero';

}
