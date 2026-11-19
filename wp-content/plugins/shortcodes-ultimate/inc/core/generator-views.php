<?php
/**
 * Shortcode Generator
 */
class Su_Generator_Views {

	/**
	 * Constructor
	 */
	function __construct() {}

	public static function text( $id, $field ) {
		$field = wp_parse_args( $field, array(
			'default' => ''
		) );
		$return = '<input type="text" name="' . $id . '" value="' . esc_attr( $field['default'] ) . '" id="su-generator-attr-' . $id . '" class="su-generator-attr" />';
		return $return;
	}

	public static function textarea( $id, $field ) {
		$field = wp_parse_args( $field, array(
			'rows'    => 3,
			'default' => ''
		) );
		$return = '<textarea name="' . $id . '" id="su-generator-attr-' . $id . '" rows="' . $field['rows'] . '" class="su-generator-attr">' . esc_textarea( $field['default'] ) . '</textarea>';
		return $return;
	}

	public static function select( $id, $field ) {

		// Multiple selects
		$multiple = isset( $field['multiple'] ) && $field['multiple'] ? ' multiple' : '';
		$return = '<select name="' . $id . '" id="su-generator-attr-' . $id . '" class="su-generator-attr"' . $multiple . '>';
		// Create options
		foreach ( $field['values'] as $option_value => $option_title ) {
			// Is this option selected
			$selected = ( $field['default'] === $option_value ) ? ' selected="selected"' : '';
			$is_pro = strpos($option_value, '_PRO-') === 0;
			$disabled = $is_pro ? ' disabled="disabled"' : '';
			// Create option
			$return .= '<option value="' . $option_value . '"' . $selected . $disabled . '>' . $option_title . '</option>';
		}
		$return .= '</select>';
		return $return;

	}

	public static function radio( $id, $field ) {

		$field = wp_parse_args(
			$field,
			array(
				'default' => '',
				'layout'  => 'vertical',
				'values'  => array(),
			)
		);

		$options = array();

		foreach ( $field['values'] as $option_value => $option_label ) {
			$option_value = (string) $option_value;

			if ( is_array( $option_label ) ) {
				$option_label = isset( $option_label['label'] )
					? $option_label['label']
					: $option_value;
			}

			$options[ $option_value ] = (string) $option_label;
		}

		if ( empty( $options ) ) {
			return '';
		}

		$default = (string) $field['default'];

		if ( ! isset( $options[ $default ] ) ) {
			$default = (string) key( $options );
		}

		$layout = sanitize_key( $field['layout'] );

		if ( 'horizontal' !== $layout ) {
			$layout = 'vertical';
		}

		$return  = '<div class="su-generator-radio su-generator-radio-layout-' . esc_attr( $layout ) . '" role="radiogroup">';
		$return .= '<input type="hidden" name="' . esc_attr( $id ) . '" value="' . esc_attr( $default ) . '" id="su-generator-attr-' . esc_attr( $id ) . '" class="su-generator-attr su-generator-radio-value" />';

		foreach ( $options as $option_value => $option_label ) {
			$option_id = sanitize_html_class( $id . '-' . $option_value );

			if ( '' === $option_id ) {
				$option_id = md5( $id . '-' . $option_value );
			}

			$is_pro   = strpos( $option_value, '_PRO-' ) === 0;
			$disabled = $is_pro ? ' disabled="disabled"' : '';

			$return .= '<label class="su-generator-radio-option" for="su-generator-radio-' . esc_attr( $option_id ) . '">';
			$return .= '<input type="radio" name="' . esc_attr( $id . '_radio' ) . '" value="' . esc_attr( $option_value ) . '" id="su-generator-radio-' . esc_attr( $option_id ) . '" class="su-generator-radio-input"' . checked( $default, $option_value, false ) . $disabled . ' />';
			$return .= '<span class="su-generator-radio-label">' . esc_html( $option_label ) . '</span>';
			$return .= '</label>';
		}

		$return .= '</div>';

		return $return;

	}

	public static function date_picker( $id, $field ) {
		return self::picker_input( $id, $field, 'date', 'su-generator-date-picker' );
	}

	public static function datepicker( $id, $field ) {
		return self::date_picker( $id, $field );
	}

	public static function time_picker( $id, $field ) {
		return self::picker_input( $id, $field, 'time', 'su-generator-time-picker' );
	}

	public static function timepicker( $id, $field ) {
		return self::time_picker( $id, $field );
	}

	public static function image_radio( $id, $field ) {

		$field = wp_parse_args(
			$field,
			array(
				'default' => '',
				'values'  => array(),
			)
		);

		$options = array();

		foreach ( $field['values'] as $option_value => $option ) {
			$option_value = (string) $option_value;

			if ( is_array( $option ) ) {
				$label = isset( $option['label'] ) ? $option['label'] : $option_value;
				$image = isset( $option['image'] ) ? $option['image'] : '';
				$alt   = isset( $option['alt'] ) ? $option['alt'] : $label;
			} else {
				$label = (string) $option;
				$image = '';
				$alt   = $label;
			}

			$options[ $option_value ] = array(
				'label' => $label,
				'image' => $image,
				'alt'   => $alt,
			);
		}

		if ( empty( $options ) ) {
			return '';
		}

		$default = (string) $field['default'];

		if ( ! isset( $options[ $default ] ) ) {
			$default = (string) key( $options );
		}

		$return  = '<div class="su-generator-image-radio">';
		$return .= '<input type="hidden" name="' . esc_attr( $id ) . '" value="' . esc_attr( $default ) . '" id="su-generator-attr-' . esc_attr( $id ) . '" class="su-generator-attr su-generator-image-radio-value" />';

		foreach ( $options as $option_value => $option ) {
			$option_id = sanitize_html_class( $id . '-' . $option_value );

			if ( '' === $option_id ) {
				$option_id = md5( $id . '-' . $option_value );
			}

			$return .= '<label class="su-generator-image-radio-option" for="su-generator-image-radio-' . esc_attr( $option_id ) . '">';
			$return .= '<input type="radio" name="' . esc_attr( $id . '_image_radio' ) . '" value="' . esc_attr( $option_value ) . '" id="su-generator-image-radio-' . esc_attr( $option_id ) . '" class="su-generator-image-radio-input"' . checked( $default, $option_value, false ) . ' />';
			$return .= '<span class="su-generator-image-radio-card">';

			if ( '' !== $option['image'] ) {
				$return .= '<span class="su-generator-image-radio-image"><img src="' . esc_url( $option['image'] ) . '" alt="' . esc_attr( wp_strip_all_tags( $option['alt'] ) ) . '" /></span>';
			}

			$return .= '<span class="su-generator-image-radio-label">' . esc_html( $option['label'] ) . '</span>';
			$return .= '</span>';
			$return .= '</label>';
		}

		$return .= '</div>';

		return $return;

	}

	public static function searchable_select( $id, $field ) {

		$field = wp_parse_args(
			$field,
			array(
				'default'        => '',
				'values'         => array(),
				'multiple'       => false,
				'placeholder'    => __( 'Search...', 'shortcodes-ultimate' ),
				'empty'          => __( 'No results found', 'shortcodes-ultimate' ),
				'loading'        => __( 'Loading...', 'shortcodes-ultimate' ),
				'too_short'      => '',
				'taxonomy_field' => '',
				'ajax_action'    => '',
				'ajax_min_length' => '',
				'ajax_delay'     => '',
			)
		);

		$default = is_array( $field['default'] )
			? implode( ',', $field['default'] )
			: (string) $field['default'];

		$data = array(
			'data-multiple="' . esc_attr( $field['multiple'] ? 'true' : 'false' ) . '"',
			'data-placeholder="' . esc_attr( $field['placeholder'] ) . '"',
			'data-empty="' . esc_attr( $field['empty'] ) . '"',
			'data-loading="' . esc_attr( $field['loading'] ) . '"',
		);

		if ( $field['taxonomy_field'] ) {
			$data[] = 'data-taxonomy-field="' . esc_attr( $field['taxonomy_field'] ) . '"';
		}

		if ( $field['too_short'] ) {
			$data[] = 'data-too-short="' . esc_attr( $field['too_short'] ) . '"';
		}

		if ( $field['ajax_action'] ) {
			$data[] = 'data-ajax-action="' . esc_attr( $field['ajax_action'] ) . '"';
		}

		if ( '' !== $field['ajax_min_length'] ) {
			$data[] = 'data-ajax-min-length="' . esc_attr( $field['ajax_min_length'] ) . '"';
		}

		if ( '' !== $field['ajax_delay'] ) {
			$data[] = 'data-ajax-delay="' . esc_attr( $field['ajax_delay'] ) . '"';
		}

		$return  = '<div class="su-generator-searchable-select" ' . implode( ' ', $data ) . '>';
		$return .= '<input type="hidden" name="' . esc_attr( $id ) . '" value="' . esc_attr( $default ) . '" id="su-generator-attr-' . esc_attr( $id ) . '" class="su-generator-attr su-generator-searchable-select-value" />';
		$return .= '<div class="su-generator-searchable-select-control">';
		$return .= '<span class="su-generator-searchable-select-tokens"></span>';
		$return .= '<input type="search" class="su-generator-searchable-select-input" autocomplete="off" spellcheck="false" placeholder="' . esc_attr( $field['placeholder'] ) . '" />';
		$return .= '</div>';
		$return .= '<div class="su-generator-searchable-select-dropdown" role="listbox">';

		if ( is_array( $field['values'] ) ) {
			foreach ( $field['values'] as $option_value => $option_title ) {
				$return .= '<button type="button" class="su-generator-searchable-select-option" data-value="' . esc_attr( $option_value ) . '" data-label="' . esc_attr( wp_strip_all_tags( $option_title ) ) . '" role="option">' . esc_html( $option_title ) . '</button>';
			}
		}

		$return .= '<div class="su-generator-searchable-select-empty">' . esc_html( $field['empty'] ) . '</div>';
		$return .= '</div>';
		$return .= '</div>';

		return $return;

	}

	public static function searchable_post_type( $id, $field ) {

		$types = get_post_types( array(), 'objects', 'or' );

		$field['values'] = array(
			'any' => __( 'Any post type', 'shortcodes-ultimate' ),
		);

		foreach( $types as $type ) {
			$field['values'][$type->name] = $type->label;
		}

		if ( ! isset( $field['placeholder'] ) ) {
			$field['placeholder'] = __( 'Search post types', 'shortcodes-ultimate' );
		}

		return self::searchable_select( $id, $field );

	}

	public static function searchable_taxonomy( $id, $field ) {

		$taxonomies = get_taxonomies( array(), 'objects', 'or' );

		$field['values'] = array(
			'any' => __( 'Any taxonomy', 'shortcodes-ultimate' ),
		);

		foreach( $taxonomies as $taxonomy ) {
			$field['values'][$taxonomy->name] = $taxonomy->label;
		}

		if ( ! isset( $field['placeholder'] ) ) {
			$field['placeholder'] = __( 'Search taxonomies', 'shortcodes-ultimate' );
		}

		return self::searchable_select( $id, $field );

	}

	public static function searchable_term( $id, $field ) {

		if ( empty( $field['values'] ) && ! empty( $field['taxonomy'] ) ) {
			$field['values'] = Su_Generator::get_terms( $field['taxonomy'] );
		}

		if ( ! isset( $field['placeholder'] ) ) {
			$field['placeholder'] = __( 'Search terms', 'shortcodes-ultimate' );
		}

		return self::searchable_select( $id, $field );

	}

	public static function searchable_posts( $id, $field ) {

		$field = wp_parse_args(
			$field,
			array(
				'multiple'        => true,
				'ajax_action'     => 'su_generator_search_posts',
				'ajax_min_length' => 2,
				'ajax_delay'      => 250,
				'placeholder'     => __( 'Search content', 'shortcodes-ultimate' ),
				'empty'           => __( 'No content found', 'shortcodes-ultimate' ),
				'too_short'       => __( 'Type at least 2 characters to search content', 'shortcodes-ultimate' ),
			)
		);

		return self::searchable_select( $id, $field );

	}

	public static function searchable_users( $id, $field ) {

		$field = wp_parse_args(
			$field,
			array(
				'multiple'        => true,
				'ajax_action'     => 'su_generator_search_users',
				'ajax_min_length' => 2,
				'ajax_delay'      => 250,
				'placeholder'     => __( 'Search users', 'shortcodes-ultimate' ),
				'empty'           => __( 'No users found', 'shortcodes-ultimate' ),
				'too_short'       => __( 'Type at least 2 characters to search users', 'shortcodes-ultimate' ),
			)
		);

		return self::searchable_select( $id, $field );

	}

	public static function sortable_checkboxes( $id, $field ) {

		$field = wp_parse_args(
			$field,
			array(
				'default' => '',
				'values'  => array(),
			)
		);

		$default = is_array( $field['default'] )
			? implode( ', ', $field['default'] )
			: (string) $field['default'];

		$selected = array_filter(
			array_map(
				'trim',
				explode( ',', $default )
			)
		);

		$selected = array_values(
			array_intersect(
				$selected,
				array_keys( $field['values'] )
			)
		);

		$values = array();

		foreach ( $selected as $value ) {
			$values[ $value ] = $field['values'][ $value ];
		}

		foreach ( $field['values'] as $value => $label ) {
			if ( ! isset( $values[ $value ] ) ) {
				$values[ $value ] = $label;
			}
		}

		$return  = '<div class="su-generator-sortable-checkboxes">';
		$return .= '<input type="hidden" name="' . esc_attr( $id ) . '" value="' . esc_attr( $default ) . '" id="su-generator-attr-' . esc_attr( $id ) . '" class="su-generator-attr su-generator-sortable-checkboxes-value" />';
		$return .= '<ul class="su-generator-sortable-checkboxes-list">';

		foreach ( $values as $option_value => $option_label ) {
			$option_id = sanitize_html_class( $id . '-' . $option_value );
			$checked   = in_array( $option_value, $selected, true ) ? ' checked="checked"' : '';

			$return .= '<li class="su-generator-sortable-checkboxes-item" data-value="' . esc_attr( $option_value ) . '">';
			$return .= '<span class="su-generator-sortable-checkboxes-handle" aria-hidden="true"><i class="sui sui-bars"></i></span>';
			$return .= '<label class="su-generator-sortable-checkboxes-label" for="su-generator-sortable-checkboxes-' . esc_attr( $option_id ) . '">';
			$return .= '<input type="checkbox" id="su-generator-sortable-checkboxes-' . esc_attr( $option_id ) . '" class="su-generator-sortable-checkboxes-input" value="' . esc_attr( $option_value ) . '"' . $checked . ' />';
			$return .= '<span class="su-generator-sortable-checkboxes-text">' . esc_html( $option_label ) . '</span>';
			$return .= '</label>';
			$return .= '</li>';
		}

		$return .= '</ul>';
		$return .= '</div>';

		return $return;

	}

	public static function post_type( $id, $field ) {

		// Get post types
		$types = get_post_types( array(), 'objects', 'or' );

		// Prepare empty array for values
		$field['values'] = array(
			'any' => __( 'Any post type', 'shortcodes-ultimate' ),
		);

		// Fill the array
		foreach( $types as $type ) {
			$field['values'][$type->name] = $type->label;
		}

		// Create select
		return self::select( $id, $field );

	}

	public static function taxonomy( $id, $field ) {

		// Get taxonomies
		$taxonomies = get_taxonomies( array(), 'objects', 'or' );

		// Prepare array for values
		$field['values'] = isset( $field['default'] ) && 'any' === $field['default']
			? array( 'any' => __( 'Any taxonomy', 'shortcodes-ultimate' ) )
			: array();

		// Fill the array
		foreach( $taxonomies as $taxonomy ) {
			$field['values'][$taxonomy->name] = $taxonomy->label;
		}

		// Create select
		return self::select( $id, $field );

	}

	public static function term( $id, $field ) {

		// Get categories
		$field['values'] = Su_Generator::get_terms( 'category' );

		// Create select
		return self::select( $id, $field );

	}

	public static function bool( $id, $field ) {
		$value   = ( 'yes' === $field['default'] ) ? 'yes' : 'no';
		$checked = ( 'yes' === $value ) ? 'true' : 'false';
		$return  = '<button type="button" class="su-generator-switch su-generator-switch-' . esc_attr( $value ) . '" role="switch" aria-checked="' . esc_attr( $checked ) . '"><span class="su-generator-switch-track" aria-hidden="true"><span class="su-generator-switch-thumb"></span></span><span class="su-generator-switch-text su-generator-yes">' . __( 'Yes', 'shortcodes-ultimate' ) . '</span><span class="su-generator-switch-text su-generator-no">' . __( 'No', 'shortcodes-ultimate' ) . '</span></button><input type="hidden" name="' . esc_attr( $id ) . '" value="' . esc_attr( $value ) . '" id="su-generator-attr-' . esc_attr( $id ) . '" class="su-generator-attr su-generator-switch-value" />';
		return $return;
	}

	public static function upload( $id, $field ) {
		$return = '<input type="text" name="' . $id . '" value="' . esc_attr( $field['default'] ) . '" id="su-generator-attr-' . $id . '" class="su-generator-attr su-generator-upload-value" /><div class="su-generator-field-actions"><a href="javascript:;" class="button su-generator-upload-button"><img src="' . admin_url( '/images/media-button.png' ) . '" alt="' . __( 'Open Media Library', 'shortcodes-ultimate' ) . '" />' . __( 'Open Media Library', 'shortcodes-ultimate' ) . '</a></div>';
		return $return;
	}

	public static function icon( $id, $field ) {
		$return = '<input type="text" name="' . $id . '" value="' . esc_attr( $field['default'] ) . '" id="su-generator-attr-' . $id . '" class="su-generator-attr su-generator-icon-picker-value" /><div class="su-generator-field-actions"><a href="javascript:;" class="button su-generator-upload-button su-generator-field-action"><img src="' . admin_url( '/images/media-button.png' ) . '" alt="' . __( 'Open Media Library', 'shortcodes-ultimate' ) . '" />' . __( 'Open Media Library', 'shortcodes-ultimate' ) . '</a> <a href="javascript:;" class="button su-generator-icon-picker-button su-generator-field-action"><img src="' . admin_url( '/images/media-button-other.gif' ) . '" alt="' . __( 'Icon picker', 'shortcodes-ultimate' ) . '" />' . __( 'Icon picker', 'shortcodes-ultimate' ) . '</a></div><div class="su-generator-icon-picker su-generator-clearfix"><input type="text" class="widefat" placeholder="' . __( 'Filter icons', 'shortcodes-ultimate' ) . '" /></div>';
		return $return;
	}

	public static function color( $id, $field ) {

		$field = wp_parse_args( $field, array(
			'default'           => '',
			'allow_transparent' => false,
		) );

		$allow_transparent = ! empty( $field['allow_transparent'] );
		$is_transparent    = 'transparent' === strtolower( trim( (string) $field['default'] ) );
		$classes           = array( 'su-generator-select-color' );

		if ( $allow_transparent ) {
			$classes[] = 'su-generator-select-color-allow-transparent';
		}

		$return  = '<span class="' . esc_attr( implode( ' ', $classes ) ) . '">';
		$return .= '<span class="su-generator-select-color-wheel"></span>';
		$return .= '<span class="su-generator-select-color-control">';
		$return .= '<input type="text" name="' . esc_attr( $id ) . '" value="' . esc_attr( $field['default'] ) . '" id="su-generator-attr-' . esc_attr( $id ) . '" class="su-generator-attr su-generator-select-color-value" />';

		if ( $allow_transparent ) {
			$return .= '<label class="su-generator-select-color-transparent">';
			$return .= '<input type="checkbox" class="su-generator-select-color-transparent-input" aria-controls="su-generator-attr-' . esc_attr( $id ) . '" ' . checked( $is_transparent, true, false ) . ' />';
			$return .= '<span class="su-generator-select-color-transparent-label">' . esc_html__( 'Transparent', 'shortcodes-ultimate' ) . '</span>';
			$return .= '</label>';
		}

		$return .= '</span>';
		$return .= '</span>';

		return $return;
	}

	public static function number( $id, $field ) {
		$return = '<input type="number" name="' . $id . '" value="' . esc_attr( $field['default'] ) . '" id="su-generator-attr-' . $id . '" min="' . $field['min'] . '" max="' . $field['max'] . '" step="' . $field['step'] . '" class="su-generator-attr" />';
		return $return;
	}

	public static function slider( $id, $field ) {
		$return = '<div class="su-generator-range-picker su-generator-clearfix"><input type="number" name="' . $id . '" value="' . esc_attr( $field['default'] ) . '" id="su-generator-attr-' . $id . '" min="' . $field['min'] . '" max="' . $field['max'] . '" step="' . $field['step'] . '" class="su-generator-attr" /></div>';
		return $return;
	}

	public static function shadow( $id, $field ) {
		$defaults = ( $field['default'] === 'none' ) ? array ( '0', '0', '0', '#000000' ) : explode( ' ', str_replace( 'px', '', $field['default'] ) );
		$return = '<div class="su-generator-shadow-picker"><span class="su-generator-shadow-picker-field"><input type="number" min="-1000" max="1000" step="1" value="' . $defaults[0] . '" class="su-generator-sp-hoff" /><small>' . __( 'Horizontal offset', 'shortcodes-ultimate' ) . ' (px)</small></span><span class="su-generator-shadow-picker-field"><input type="number" min="-1000" max="1000" step="1" value="' . $defaults[1] . '" class="su-generator-sp-voff" /><small>' . __( 'Vertical offset', 'shortcodes-ultimate' ) . ' (px)</small></span><span class="su-generator-shadow-picker-field"><input type="number" min="-1000" max="1000" step="1" value="' . $defaults[2] . '" class="su-generator-sp-blur" /><small>' . __( 'Blur', 'shortcodes-ultimate' ) . ' (px)</small></span><span class="su-generator-shadow-picker-field su-generator-shadow-picker-color"><span class="su-generator-shadow-picker-color-wheel"></span><input type="text" value="' . $defaults[3] . '" class="su-generator-shadow-picker-color-value" /><small>' . __( 'Color', 'shortcodes-ultimate' ) . '</small></span><input type="hidden" name="' . $id . '" value="' . esc_attr( $field['default'] ) . '" id="su-generator-attr-' . $id . '" class="su-generator-attr" /></div>';
		return $return;
	}

	public static function border( $id, $field ) {
		$defaults = ( $field['default'] === 'none' ) ? array ( '0', 'solid', '#000000' ) : explode( ' ', str_replace( 'px', '', $field['default'] ) );
		$borders = su_html_dropdown( array(
				'options' => su_get_config( 'borders' ),
				'class' => 'su-generator-bp-style',
				'selected' => $defaults[1]
			) );
		$return = '<div class="su-generator-border-picker"><span class="su-generator-border-picker-field"><input type="number" min="-1000" max="1000" step="1" value="' . $defaults[0] . '" class="su-generator-bp-width" /><small>' . __( 'Border width', 'shortcodes-ultimate' ) . ' (px)</small></span><span class="su-generator-border-picker-field">' . $borders . '<small>' . __( 'Border style', 'shortcodes-ultimate' ) . '</small></span><span class="su-generator-border-picker-field su-generator-border-picker-color"><span class="su-generator-border-picker-color-wheel"></span><input type="text" value="' . $defaults[2] . '" class="su-generator-border-picker-color-value" /><small>' . __( 'Border color', 'shortcodes-ultimate' ) . '</small></span><input type="hidden" name="' . $id . '" value="' . esc_attr( $field['default'] ) . '" id="su-generator-attr-' . $id . '" class="su-generator-attr" /></div>';
		return $return;
	}

	public static function border_new( $id, $field ) {

		$field = wp_parse_args(
			$field,
			array(
				'default' => '1px solid #000000',
			)
		);

		$defaults = self::parse_border_new_value( $field['default'] );
		$styles   = su_get_config(
			'borders',
			array(
				'solid'  => __( 'Solid', 'shortcodes-ultimate' ),
				'dotted' => __( 'Dotted', 'shortcodes-ultimate' ),
				'dashed' => __( 'Dashed', 'shortcodes-ultimate' ),
				'double' => __( 'Double', 'shortcodes-ultimate' ),
				'groove' => __( 'Groove', 'shortcodes-ultimate' ),
				'ridge'  => __( 'Ridge', 'shortcodes-ultimate' ),
			)
		);

		unset( $styles['none'] );

		if ( empty( $styles ) ) {
			$styles = array(
				'solid'  => __( 'Solid', 'shortcodes-ultimate' ),
				'dotted' => __( 'Dotted', 'shortcodes-ultimate' ),
				'dashed' => __( 'Dashed', 'shortcodes-ultimate' ),
				'double' => __( 'Double', 'shortcodes-ultimate' ),
				'groove' => __( 'Groove', 'shortcodes-ultimate' ),
				'ridge'  => __( 'Ridge', 'shortcodes-ultimate' ),
			);
		}

		if ( ! isset( $styles[ $defaults['style'] ] ) ) {
			$defaults['style'] = 'solid';
		}

		$width_options = '';
		$style_options = '';
		$popover_id    = 'su-generator-border-new-popover-' . sanitize_html_class( $id );

		foreach ( range( 0, 15 ) as $width ) {
			$value          = $width . 'px';
			$width_options .= '<option value="' . esc_attr( $value ) . '"' . selected( $defaults['width'], $value, false ) . '>' . esc_html( $value ) . '</option>';
		}

		foreach ( $styles as $style_value => $style_label ) {
			$style_options .= '<option value="' . esc_attr( $style_value ) . '"' . selected( $defaults['style'], $style_value, false ) . '>' . esc_html( $style_label ) . '</option>';
		}

		$return  = '<div class="su-generator-border-new">';
		$return .= '<select class="su-generator-border-new-width" aria-label="' . esc_attr__( 'Border width', 'shortcodes-ultimate' ) . '">' . $width_options . '</select>';
		$return .= '<select class="su-generator-border-new-style" aria-label="' . esc_attr__( 'Border style', 'shortcodes-ultimate' ) . '">' . $style_options . '</select>';
		$return .= '<div class="su-generator-border-new-color">';
		$return .= '<button type="button" class="button su-generator-border-new-color-button" aria-expanded="false" aria-controls="' . esc_attr( $popover_id ) . '">';
		$return .= '<span class="su-generator-border-new-color-swatch"><span class="su-generator-border-new-color-swatch-color" style="background-color:' . esc_attr( $defaults['css_color'] ) . '"></span></span>';
		$return .= '<span class="su-generator-border-new-color-label">' . esc_html( $defaults['label'] ) . '</span>';
		$return .= '</button>';
		$return .= '<div id="' . esc_attr( $popover_id ) . '" class="su-generator-border-new-popover" hidden>';
		$return .= '<div class="su-generator-border-new-color-wheel"></div>';
		$return .= '<label class="su-generator-border-new-color-field"><span>' . esc_html__( 'Color', 'shortcodes-ultimate' ) . '</span><input type="text" class="su-generator-border-new-color-value" value="' . esc_attr( $defaults['hex'] ) . '" /></label>';
		$return .= '<label class="su-generator-border-new-alpha-field"><span>' . esc_html__( 'Opacity', 'shortcodes-ultimate' ) . '</span><input type="range" min="0" max="100" step="1" class="su-generator-border-new-alpha-range" value="' . esc_attr( $defaults['alpha'] ) . '" /><input type="number" min="0" max="100" step="1" class="su-generator-border-new-alpha-value" value="' . esc_attr( $defaults['alpha'] ) . '" /></label>';
		$return .= '</div>';
		$return .= '</div>';
		$return .= '<input type="hidden" name="' . esc_attr( $id ) . '" value="' . esc_attr( $field['default'] ) . '" id="su-generator-attr-' . esc_attr( $id ) . '" class="su-generator-attr su-generator-border-new-value" />';
		$return .= '</div>';

		return $return;

	}

	public static function image_source( $id, $field ) {
		$field = wp_parse_args( $field, array(
				'default' => 'none'
			) );

		if ( ! isset( $field['media_sources'] ) ) {
			$field['media_sources'] = array(
				'media'         => __( 'Media library', 'shortcodes-ultimate' ),
				'posts: recent' => __( 'Recent posts', 'shortcodes-ultimate' ),
				'taxonomy'      => __( 'Taxonomy', 'shortcodes-ultimate' ),
			);
		}

		$sources = su_html_dropdown( array(
				'options'  => $field['media_sources'],
				'selected' => '0',
				'none'     => __( 'Select images source', 'shortcodes-ultimate' ) . '&hellip;',
				'class'    => 'su-generator-isp-sources'
			) );
		$categories = su_html_dropdown( array(
				'options'  => Su_Generator::get_terms( 'category' ),
				'multiple' => true,
				'size'     => 10,
				'class'    => 'su-generator-isp-categories'
			) );
		$taxonomies = su_html_dropdown( array(
				'options'  => Su_Generator::get_taxonomies(),
				'none'     => __( 'Select taxonomy', 'shortcodes-ultimate' ) . '&hellip;',
				'selected' => '0',
				'class'    => 'su-generator-isp-taxonomies'
			) );
		$terms = su_html_dropdown( array(
				'class'    => 'su-generator-isp-terms',
				'multiple' => true,
				'size'     => 10,
				'disabled' => true,
				'style'    => 'display:none'
			) );
		$return = '<div class="su-generator-isp">' . $sources . '<div class="su-generator-isp-source su-generator-isp-source-media"><div class="su-generator-clearfix"><a href="javascript:;" class="button button-primary su-generator-isp-add-media"><i class="sui sui-plus"></i>&nbsp;&nbsp;' . __( 'Add images', 'shortcodes-ultimate' ) . '</a></div><div class="su-generator-isp-images su-generator-clearfix"><em class="description">' . __( 'Click the button above and select images.<br>You can select multimple images with Ctrl (Cmd) key', 'shortcodes-ultimate' ) . '</em></div></div><div class="su-generator-isp-source su-generator-isp-source-category"><em class="description">' . __( 'Select categories to retrieve posts from.<br>You can select multiple categories with Ctrl (Cmd) key', 'shortcodes-ultimate' ) . '</em>' . $categories . '</div><div class="su-generator-isp-source su-generator-isp-source-taxonomy"><em class="description">' . __( 'Select taxonomy and it\'s terms.<br>You can select multiple terms with Ctrl (Cmd) key', 'shortcodes-ultimate' ) . '</em>' . $taxonomies . $terms . '</div><input type="hidden" name="' . $id . '" value="' . $field['default'] . '" id="su-generator-attr-' . $id . '" class="su-generator-attr" /></div>';
		return $return;
	}

	public static function extra_css_class( $id, $field ) {
		$field = wp_parse_args( $field, array(
			'default' => ''
		) );
		$return = '<input type="text" name="' . $id . '" value="' . esc_attr( $field['default'] ) . '" id="su-generator-attr-' . $id . '" class="su-generator-attr" />';
		return $return;
	}

	private static function picker_input( $id, $field, $type, $class ) {

		$field = wp_parse_args(
			$field,
			array(
				'default'     => '',
				'max'         => '',
				'min'         => '',
				'placeholder' => '',
				'step'        => '',
			)
		);

		$attributes = array(
			'type="' . esc_attr( $type ) . '"',
			'name="' . esc_attr( $id ) . '"',
			'value="' . esc_attr( $field['default'] ) . '"',
			'id="su-generator-attr-' . esc_attr( $id ) . '"',
			'class="su-generator-attr ' . esc_attr( $class ) . '"',
		);

		foreach ( array( 'min', 'max', 'step', 'placeholder' ) as $attribute ) {
			if ( '' !== (string) $field[ $attribute ] ) {
				$attributes[] = $attribute . '="' . esc_attr( $field[ $attribute ] ) . '"';
			}
		}

		return '<input ' . implode( ' ', $attributes ) . ' />';

	}

	private static function parse_border_new_value( $value ) {

		$parsed = array(
			'width'     => '1px',
			'style'     => 'solid',
			'hex'       => '#000000',
			'alpha'     => 100,
			'css_color' => '#000000',
			'label'     => '#000000',
		);

		$value = trim( (string) $value );

		if ( 'none' === strtolower( $value ) ) {
			$parsed['width'] = '0px';
			$parsed['alpha'] = 0;
		} elseif ( preg_match( '/^(\d+(?:\.\d+)?)px\s+([a-z-]+)\s+(.+)$/i', $value, $matches ) ) {
			$width = intval( $matches[1] );

			if ( $width >= 0 && $width <= 15 ) {
				$parsed['width'] = $width . 'px';
			}

			$parsed['style'] = sanitize_key( $matches[2] );

			$color = self::parse_border_new_color( $matches[3] );

			$parsed['hex']   = $color['hex'];
			$parsed['alpha'] = $color['alpha'];
		}

		$parsed['css_color'] = self::format_border_new_color( $parsed['hex'], $parsed['alpha'] );
		$parsed['label']     = $parsed['css_color'];

		return $parsed;

	}

	private static function parse_border_new_color( $value ) {

		$value = trim( (string) $value );

		if ( 'transparent' === strtolower( $value ) ) {
			return array(
				'hex'   => '#000000',
				'alpha' => 0,
			);
		}

		if ( preg_match( '/^#?([a-f0-9]{3}|[a-f0-9]{6})$/i', $value, $matches ) ) {
			$hex = strtolower( $matches[1] );

			if ( 3 === strlen( $hex ) ) {
				$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
			}

			return array(
				'hex'   => '#' . $hex,
				'alpha' => 100,
			);
		}

		if ( preg_match( '/^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})(?:\s*,\s*([0-9.]+)\s*)?\)$/i', $value, $matches ) ) {
			$red   = max( 0, min( 255, intval( $matches[1] ) ) );
			$green = max( 0, min( 255, intval( $matches[2] ) ) );
			$blue  = max( 0, min( 255, intval( $matches[3] ) ) );
			$alpha = isset( $matches[4] ) && '' !== $matches[4]
				? max( 0, min( 1, floatval( $matches[4] ) ) )
				: 1;

			return array(
				'hex'   => sprintf( '#%02x%02x%02x', $red, $green, $blue ),
				'alpha' => (int) round( $alpha * 100 ),
			);
		}

		return array(
			'hex'   => '#000000',
			'alpha' => 100,
		);

	}

	private static function format_border_new_color( $hex, $alpha ) {

		$alpha = max( 0, min( 100, intval( $alpha ) ) );

		if ( $alpha >= 100 ) {
			return $hex;
		}

		$hex   = ltrim( $hex, '#' );
		$red   = hexdec( substr( $hex, 0, 2 ) );
		$green = hexdec( substr( $hex, 2, 2 ) );
		$blue  = hexdec( substr( $hex, 4, 2 ) );
		$value = rtrim( rtrim( sprintf( '%.2F', $alpha / 100 ), '0' ), '.' );

		return sprintf( 'rgba(%d,%d,%d,%s)', $red, $green, $blue, $value );

	}

}
