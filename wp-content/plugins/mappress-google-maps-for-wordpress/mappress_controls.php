<?php
/**
* Generic HTML controls
*/
class Mappress_Controls {

	static function parse_atts($name, $args = '') {
		$args = empty($args) ? array() : $args;

		// Include name if set
		$atts = (empty($name)) ? '' : "name='" . esc_attr($name) . "'";

		foreach($args as $key => $value) {
			// Attributes with value
			if (in_array($key, array('class', 'id', 'maxlength', 'multiple', 'onclick', 'rows', 'size', 'style', 'title', 'type')))
				$atts .= " $key='" . esc_attr($value) . "' ";

			// Boolean attributes
			if (in_array($key, array('checked', 'disabled', 'multiple', 'readonly', 'selected')) && $value)
				if (!empty($value))
					$atts .= " $key ";

			// Data (not escaped)
			if (substr($key, 0, 4) == 'data')
				$atts .= " $key='$value' ";
		}

		return $atts;
	}

	static function button($name, $value, $args = '') {
		$args = wp_parse_args($args, array('class' => '', 'type' => 'button'));
		$args['class'] = 'button ' . $args['class'];
		$atts = self::parse_atts($name, $args);;
		$value = esc_attr($value);
		$html = "<input value='$value' $atts />";
		return $html;
	}

	static function textarea($name, $value, $args) {
		$atts = self::parse_atts($name, $args);;
		$value = esc_textarea($value);
		return "<textarea $atts>$value</textarea>";
	}

	// Boolean checkbox ('checkmark')
	static function checkmark($name, $value, $label = '', $args = '') {
		$atts = self::parse_atts($name, $args);
		return "<input type='hidden' name='$name' value='false' /><label><input type='checkbox' value='true' " . checked($value, true, false) . " $atts />$label</label> ";
	}

	// Single checkbox
	static function checkbox($name, $value, $checked, $label = '', $args = '') {
		$args['checked'] = $checked;
		$atts = self::parse_atts($name, $args);
		return "<label><input type='checkbox' value='" . esc_attr($value) . "' $atts />$label</label> ";
	}

	// Checkbox list
	static function checkboxes($name, $labels, $selected = null, $args = '') {
		$selected = ($selected) ? $selected : array();
		$name .= '[]';

		$html = "";
		foreach ($labels as $value => $label)
			$html .= self::checkbox($name, $value, in_array($value, $selected), $label, $args);
		return $html;
	}

	static function input($name, $value, $args = '') {
		$args = wp_parse_args($args, array('type' => 'text'));
		$atts = self::parse_atts($name, $args);
		$value = esc_attr($value);
		return "<input $atts value='$value' />";
	}

	static function radio($name, $value, $label = '', $args = '') {
		$atts = self::parse_atts($name, $args);
		$value = esc_attr($value);
		return "<label><input type='radio' name='$name' value='$value' $atts />$label</label>";
	}

	static function radios($name, $data, $selected = null, $args = '') {
		$atts = self::parse_atts($name, $args);

		// If no selected value, use first key
		if (empty($selected) && !empty($data)) {
			$keys = array_keys($data);
			$selected = $keys[0];
		}

		$html = "";
		foreach ((array)$data as $key => $label) {
			$key = esc_attr($key);
			$html .= "<label><input type='radio' value='$key' " . checked($selected, $key, false) . " $atts />$label</label> ";
		}
		return $html;
	}

	static function select($name, $data, $selected = '', $args = '') {
		$args = (object) wp_parse_args($args, array('none' => false));
		$atts = self::parse_atts($name, $args);

		if (!is_array($data) || empty($data))
			$data = array();

		if ($args->none) {
			if ($args->none === true)
				$args->none = '&nbsp;';
			$data = array('' => $args->none) + $data;
		}

		$html = "<select $atts>\r\n";
		foreach ((array)$data as $key => $label) {
			if (substr($key, 0, 8) == 'optgroup') {
				$html .= "<optgroup label='" . esc_attr($label) . "'>";
				continue;
			}
			$select = (is_array($selected)) ? in_array($key, $selected) : $key == $selected;
			$select = ($select) ? 'selected' : '';

			$value = esc_attr($key);
			$text = esc_attr($label);
			$html .= "<option value='$value' $select>$text</option>\r\n";
		}
		$html .= "</select>\r\n";
		return $html;
	}

	static function grid($headers, $rows, $args = '') {
		$options = ($args) ? json_encode($args) : "";
		$html = "<div data-mapp-grid='$options'>";

		// Add delete and sort columns
		$headers[] = '';
		foreach($rows as $i => $row)
			$rows[$i][] = "<span data-mapp-action='remove' title='" . __('Delete', 'mappress-google-maps-for-wordpress') . "'>X</span>";

		// Last row is the template
		$lastrow = count($rows) - 1;
		$template = $rows[$lastrow];
		unset($rows[$lastrow]);

		// Hide table if empty
		if ($lastrow < 1)
			$args['style'] = 'display:none';

		// Generate table
		$html .= self::table($headers, $rows, $args);

		// Add new row button
		$html .= "<button type='button' class='button' data-mapp-action='add'>" . __('Add', 'mappress-google-maps-for-wordpress') . "</button>";

		// Add template
		$html .= "<script type='text/template'><tr>";
		foreach($template as $col)
			$html .= "<td>$col</td>";
		$html .= "</tr></script>";

		$html .= "</div>";
		return $html;
	}

	static function table($headers, $rows, $args = '') {
		$atts = self::parse_atts(null, $args);

		$html = "<table $atts>";
		if ($headers) {
			$html .= "<thead><tr>";
			foreach ((array)$headers as $i => $header)
				$html .= "<th>$header</th>";
			$html .= "</tr></thead>";
		}

		$html .= "<tbody>";
		foreach($rows as $id => $row) {
			$html .= "<tr>";
			foreach($row as $i => $col)
				$html .= "<td>$col</td>";
			$html .= "</tr>";
		}
		$html .= "</tbody></table>";
		return $html;
	}

	// Dropdown multiselect
	static function multiselect($name, $values_list, $selected = array(), $args = '') {
		$atts = self::parse_atts($name, $args);
		$selected = implode(',', $selected);
		$args['readonly'] = true;
		$html = Mappress_Controls::input($name, $selected, $args);
		$html .= "<div style='display:none'>" . $values_list . "</div>";
		return "<div class='mapp-multiselect'>$html</div>";
	}

	// Pseudo-combobox
	static function combobox($name, $data, $selected = '', $args = '') {
		$html = Mappress_Controls::select($name, $data, $selected, $args);
		$html .= " <a class='mapp-combo-new' href='#'>" . __('New', 'mappress-google-maps-for-wordpress') . "</a>";
		$html .= Mappress_Controls::input($name, '', array('style' => 'display:none', 'disabled' => true));
		$html .= " <a class='mapp-combo-cancel' href='#' style='display:none'>" . __('Cancel', 'mappress-google-maps-for-wordpress') . "</a>";
		return "<div class='mapp-combobox'>$html</div>";
	}

	// Toggle panel
	static function toggle($label, $element) {
		$html = "<div class='mapp-toggle'>"
			. "<div class='mapp-toggle-select'></div>"
			. "<div class='mapp-toggle-label'>$label</div>"
			. "</div>"
			. "<div>$element</div>";
		return $html;
	}

	// Icon picker
	static function icon_picker($name = '', $value = '', $args = '') {
		$atts = self::parse_atts($name, $args);
		$value = esc_attr($value);
		$name = esc_attr($name);
		$html = "<input type='hidden' data-mapp-iconpicker name='$name' value='$value' $atts />";
		return $html;
	}

	static function get_post_types() {
		$results = array();
		$post_types = get_post_types(array('show_ui' => true), 'objects');
		unset($post_types['attachment']);
		foreach($post_types as $type => $obj)
			$results[$type] = $obj->label;
		return $results;
	}

	static function get_taxonomies() {
		$results = array();
		$tax_objs = get_taxonomies(array('public' => true), 'objects');
		unset($tax_objs['post_format']);
		foreach($tax_objs as $tax_obj)
			$results[$tax_obj->name] = $tax_obj->label;
		return $results;
	}

	static function get_terms($taxonomy) {
		$results = array();
		$terms = get_terms($taxonomy, array('hide_empty' => false));
		if (is_array($terms)) {
			$walker = new Mappress_Walker();
			$walk = $walker->walk($terms, 0, array('indent' => true));
			if (is_array($walk))
				$results = $walk;
		}
		return $results;
	}
}

/**
* Walker for taxonomy values.
* Call with array of terms objects: $walker->walk($terms, 0);
* Returns array of (term slug => name)
*/
class Mappress_Walker extends Walker {
	public $db_fields = array ('parent' => 'parent', 'id' => 'term_id');
	function start_el( &$output, $term, $depth = 0, $args = array(), $id = 0 ) {
		if (!is_array($output))
			$output = array();

		// If 'indent' set, use spaces (for hierarchical lists like taxonomies)
		$indent = (isset($args['indent']) && $args['indent']) ? str_repeat('&nbsp;', $depth * 3) : '';
		$output[$term->slug] = $indent . $term->slug;
	}
}
?>