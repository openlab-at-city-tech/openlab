<?php 
/**
 * Opening Border Control Markup
 *
 * Outputs the opening html markup for the
 * border controls.
 * 
 * @package   Easy_Google_Fonts
 * @author    Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/easy-google-fonts/
 * @copyright Copyright (c) 2016, Titanium Themes
 * @version   1.4.4
 * 
 */
?>
<div class="egf-font-toggle">
	<div class="toggle-section-title">
		<span class="customize-control-title inner-control-title"><?php _e( 'Border', 'easy-google-fonts' ); ?></span>
	</div>
	<div class="toggle-section-content">
		<span class="customize-control-title"><?php _e( 'Select Border to Control', 'easy-google-fonts' ); ?></span>
		<select class="egf-switch-border-control">
			<option value="top"><?php _e( 'Top', 'easy-google-fonts' ); ?></option>
			<option value="bottom"><?php _e( 'Bottom', 'easy-google-fonts' ); ?></option>
			<option value="left"><?php _e( 'Left', 'easy-google-fonts' ); ?></option>
			<option value="right"><?php _e( 'Right', 'easy-google-fonts' ); ?></option>
		</select>
