<?php


namespace Kubio\Theme\Components;

use ColibriWP\Theme\Theme;

class CSSOutput extends \ColibriWP\Theme\Components\CSSOutput {
	const GRADIENT_VALUE_PATTERN = 'linear-gradient(#angle#deg,#steps.0.color# #steps.0.position#% ,#steps.1.color# #steps.1.position#%)';

	public function themePrefix() {
		return 'html.' . Theme::slug() . '-theme #kubio ';
	}
}
