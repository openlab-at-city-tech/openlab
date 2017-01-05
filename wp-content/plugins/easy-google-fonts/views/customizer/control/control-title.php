<?php 
/**
 * Font Control Title and Reset Button
 *
 * Font control title and reset button for
 * the customizer control.
 * 
 * @package   Easy_Google_Fonts
 * @author    Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/easy-google-fonts/
 * @copyright Copyright (c) 2016, Titanium Themes
 * @version   1.4.2
 * 
 */
?>
<div class="egf-font-control-title">
	<span class="customize-control-title">{{ data.label }}</span>
	<a class="egf-reset-font" href="#"><?php _e( 'Reset', 'easy-google-fonts' ); ?></a>
	<div class="egf-clear"></div>
</div>

<# if ( data.description ) { #>
	<span class="description customize-control-description egf-control-description">{{ data.description }}</span>
<# } #>
