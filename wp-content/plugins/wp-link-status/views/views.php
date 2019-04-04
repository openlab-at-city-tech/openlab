<?php

/**
 * Views class
 *
 * @package WP Link Status
 * @subpackage Views
 */
abstract class WPLNST_Views {



	/**
	 * Compose select option values
	 */
	public static function options($options, $values, $display = true, $space = false) {

		// Init
		$inner = '';

		// Compose options
		foreach ($options as $key => $name) {
			$inner .= '<option'.(self::selected($key, $values, false)? ' selected' : '').' value="'.esc_attr($key).'">'.esc_html($name).'</option>';
		}

		// Check display
		if ($display) {
			echo $inner;
		}

		// Done
		return $inner;
	}



	/**
	 * Check a checked value
	 */
	public static function checked($current, $values, $display = true) {
		$checked = self::is_value($current, $values);
		if ($checked && $display) {
			echo 'checked';
		}
		return $checked;
	}



	/**
	 * Check a selected value
	 */
	public static function selected($current, $values, $display = true, $space = false) {
		$selected = self::is_value($current, $values);
		if ($selected && $display) {
			echo ($space? ' ' : '').'selected';
		}
		return $selected;
	}



	/**
	 * Check if value match an array of values
	 */
	public static function is_value($current, $values) {
		if (empty($values) && false !== $values) {
			return false;
		}
		return is_array($values)? in_array($current, $values) : ($current == $values);
	}



	/**
	 * Encode and sanitize a json list
	 */
	public static function esc_attr_elist($json) {
		$json_new = array();
		foreach ($json as $json_item) {
			$json_item_new = array();
			foreach ($json_item as $key => $value) {
				$json_item_new[esc_attr($key)] = esc_attr($value);
			}
			$json_new[] = $json_item_new;
		}
		return str_replace('&quot;', '%quot%', @json_encode($json_new));
	}



}