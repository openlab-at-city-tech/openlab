<?php
/**
 * Sanitization
 *
 * Defines all of the sanitization callbacks
 * available to sanitize field types. Every
 * setting type should have a sanitization
 * callback assigned to it.
 *
 * @package easy-google-fonts
 * @author  Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 */

namespace EGF\Sanitization;

use EGF\Settings as Settings;
use EGF\Utils as Utils;

/**
 * Determine Sanitization Callback
 *
 * Used to determine the correct sanitization
 * callback for individual font controls
 * settings.
 *
 * Note: Currently returns false as sanitization
 * happens on the subsetting level.
 *
 * @param string $setting_key Setting key to sanitize.
 */
function get_setting_sanitization_callback( $setting_key ) {
	$sanitize_callback = false;
	return $sanitize_callback;
}

/**
 * Determine Sanitization Callback
 *
 * Used to determine the correct sanitization
 * callback for nested settings within an
 * individual font setting.
 *
 * @param string $setting_prop_key Setting prop key to sanitize.
 */
function get_setting_prop_sanitization_callback( $setting_prop_key ) {
	$sanitize_callback = false;

	switch ( $setting_prop_key ) {
		case 'font_color':
		case 'background_color':
		case 'border_top_color':
		case 'border_bottom_color':
		case 'border_left_color':
		case 'border_right_color':
			$sanitize_callback = '\EGF\Sanitization\sanitize_color';
			break;

		case 'subset':
		case 'font_id':
		case 'font_name':
		case 'font_weight':
		case 'font_style':
		case 'font_weight_style':
			$sanitize_callback = 'esc_attr';
			break;

		case 'stylesheet_url':
			$sanitize_callback = 'esc_url_raw';
			break;

		case 'text_decoration':
			$sanitize_callback = '\EGF\Sanitization\sanitize_text_decoration';
			break;

		case 'text_transform':
			$sanitize_callback = '\EGF\Sanitization\sanitize_text_transform';
			break;

		case 'line_height':
			$sanitize_callback = '\EGF\Sanitization\sanitize_line_height';
			break;

		case 'display':
			$sanitize_callback = '\EGF\Sanitization\sanitize_display';
			break;

		case 'border_top_style':
		case 'border_bottom_style':
		case 'border_left_style':
		case 'border_right_style':
			$sanitize_callback = '\EGF\Sanitization\sanitize_border_style';
			break;

		case 'font_size':
		case 'letter_spacing':
		case 'margin_top':
		case 'margin_right':
		case 'margin_bottom':
		case 'margin_left':
		case 'padding_top':
		case 'padding_right':
		case 'padding_bottom':
		case 'padding_left':
		case 'border_radius_top_left':
		case 'border_radius_top_right':
		case 'border_radius_bottom_left':
		case 'border_radius_bottom_right':
		case 'border_top_width':
		case 'border_bottom_width':
		case 'border_left_width':
		case 'border_right_width':
			$sanitize_callback = '\EGF\Sanitization\sanitize_unit';
			break;

		default:
			$sanitize_callback = 'esc_attr';
			break;
	}

	return $sanitize_callback;
}

/**
 * Sanitize Text Decoration
 *
 * @param string $input   The setting value to be sanitized.
 * @param object $setting \WP_Customize_Manager instance.
 * @return string Sanitized input.
 */
function sanitize_text_decoration( $input, $setting ) {
	$valid_inputs = [ 'none', 'underline', 'line-through', 'overline' ];
	return \in_array( $input, $valid_inputs, true ) ? esc_attr( $input ) : '';
}

/**
 * Sanitize Text Transform
 *
 * @param string $input   The setting value to be sanitized.
 * @param object $setting \WP_Customize_Manager instance.
 * @return string Sanitized input.
 */
function sanitize_text_transform( $input, $setting ) {
	$valid_inputs = [ 'none', 'uppercase', 'lowercase', 'capitalize' ];
	return \in_array( $input, $valid_inputs, true ) ? esc_attr( $input ) : '';
}

/**
 * Sanitize Line Height
 *
 * @param string $input   The setting value to be sanitized.
 * @param object $setting \WP_Customize_Manager instance.
 * @return string Sanitized input.
 */
function sanitize_line_height( $input, $setting ) {
	return empty( \floatval( $input ) ) ? '' : \floatval( $input );
}

/**
 * Sanitize Display
 *
 * @param string $input   The setting value to be sanitized.
 * @param object $setting \WP_Customize_Manager instance.
 * @return string Sanitized input.
 */
function sanitize_display( $input, $setting ) {
	$valid_inputs = [ 'block', 'inline-block', 'flex' ];
	return \in_array( $input, $valid_inputs, true ) ? esc_attr( $input ) : '';
}

/**
 * Sanitize Border Style
 *
 * @param string $input   The setting value to be sanitized.
 * @param object $setting \WP_Customize_Manager instance.
 * @return string Sanitized input.
 */
function sanitize_border_style( $input, $setting ) {
	$valid_inputs = [ 'none', 'solid', 'dashed', 'dotted', 'double', 'groove' ];
	return \in_array( $input, $valid_inputs, true ) ? esc_attr( $input ) : '';
}

/**
 * Sanitize Unit
 *
 * Sanitize the amount and unit values for
 * the settings input.
 *
 * @param string $input   The setting value to be sanitized.
 * @param object $setting \WP_Customize_Manager instance.
 * @return mixed (boolean|string)
 */
function sanitize_unit( $input, $setting ) {
	$sanitized_input = [
		'amount' => '',
		'unit'   => '',
	];

	$valid_units = [ 'px', 'em', 'rem', '%', 'vw', 'vh' ];

	if ( isset( $input['amount'] ) && isset( $input['unit'] ) ) {
		$amount = $input['amount'];
		$unit   = $input['unit'];

		$sanitized_input['amount'] = is_float( $amount ) ? floatval( $amount ) : intval( $amount );
		$sanitized_input['unit']   = \in_array( $unit, $valid_units, true ) ? esc_attr( $unit ) : '';
	}

	return $sanitized_input;
}

/**
 * Sanitize Color
 *
 * Sanitize various css color values from input.
 *
 * @param string $input   The setting value to be sanitized.
 * @param object $setting \WP_Customize_Manager instance.
 * @return mixed (boolean|string)
 */
function sanitize_color( $input, $setting ) {
	if ( empty( $input ) ) {
		return '';
	}

	$color = trim( $input );

	// HEX.
	if ( preg_match( '/^#([a-f\d]{3}){1,2}$/i', $color ) ) {
		return sanitize_hex_color( $color );
	}

	// RGB.
	if ( preg_match( '/rgb\(\s*(?<r>\d{1,3})\s*,\s*(?<g>\d{1,3})\s*,\s*(?<b>\d{1,3})\s*\)/i', $color, $matches ) ) {
		$matches['r'] = min( 255, max( 0, (int) $matches['r'] ) );
		$matches['g'] = min( 255, max( 0, (int) $matches['g'] ) );
		$matches['b'] = min( 255, max( 0, (int) $matches['b'] ) );
		return "rgb({$matches['r']}, {$matches['g']}, {$matches['b']})";
	}

	// RGBA.
	if ( preg_match( '/rgba\(\s*(?<r>\d{1,3})\s*,\s*(?<g>\d{1,3})\s*,\s*(?<b>\d{1,3})\s*,\s*(?<a>[\d.]+)\s*\)/i', $color, $matches ) ) {
		$matches['r'] = min( 255, max( 0, (int) $matches['r'] ) );
		$matches['g'] = min( 255, max( 0, (int) $matches['g'] ) );
		$matches['b'] = min( 255, max( 0, (int) $matches['b'] ) );
		$matches['a'] = min( 1, max( 0, (float) $matches['a'] ) );
		return "rgba({$matches['r']}, {$matches['g']}, {$matches['b']}, {$matches['a']})";
	}

	// HSL.
	if ( preg_match( '/hsl\(\s*(?<h>[\d.]+)\s*,\s*(?<s>[\d.]+)%\s*,\s*(?<l>[\d.]+)%\s*\)/i', $color, $matches ) ) {
		$matches['h'] = min( 360, max( 0, (float) $matches['h'] ) );
		$matches['s'] = min( 100, max( 0, (float) $matches['s'] ) );
		$matches['l'] = min( 100, max( 0, (float) $matches['l'] ) );
		return "hsl({$matches['h']}, {$matches['s']}%, {$matches['l']}%)";
	}

	// HSLA.
	if ( preg_match( '/hsla\(\s*(?<h>[\d.]+)\s*,\s*(?<s>[\d.]+)%\s*,\s*(?<l>[\d.]+)%\s*,\s*(?<a>[\d.]+)\s*\)/i', $color, $matches ) ) {
		$matches['h'] = min( 360, max( 0, (float) $matches['h'] ) );
		$matches['s'] = min( 100, max( 0, (float) $matches['s'] ) );
		$matches['l'] = min( 100, max( 0, (float) $matches['l'] ) );
		$matches['a'] = min( 1, max( 0, (float) $matches['a'] ) );
		return "hsla({$matches['h']}, {$matches['s']}%, {$matches['l']}%, {$matches['a']})";
	}

	return '';
}
