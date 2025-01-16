<?php

namespace LottaFramework\Customizer;

use LottaFramework\Facades\Css;
use LottaFramework\Icons\IconsManager;
use LottaFramework\Utils;

class Sanitizes {

	/**
	 * Return primitive value
	 */
	public static function primitive( $v ) {
		return $v;
	}

	/**
	 * Typography sanitization
	 *
	 * @param $value
	 * @param $args
	 *
	 * @return array
	 */
	public static function typography( $value ) {
		return [
			'family'         => sanitize_text_field( $value['family'] ?? '' ),
			'variant'        => sanitize_text_field( $value['variant'] ?? '' ),
			'fontSize'       => self::responsive_sanitize( [ self::class, 'slider' ], $value['fontSize'] ?? '', [
				'options' => [
					'units' => [
						[ 'unit' => 'px', 'min' => 10, 'max' => 200 ],
						[ 'unit' => 'pt', 'min' => 10, 'max' => 50 ],
						[ 'unit' => 'em', 'min' => 0.5, 'max' => 50 ],
						[ 'unit' => 'rem', 'min' => 0.5, 'max' => 50 ],
					],
				],
			] ),
			'lineHeight'     => self::responsive_sanitize( [ self::class, 'slider' ], $value['lineHeight'] ?? '', [
				'options' => [
					'units' => [
						[ 'unit' => '', 'min' => 1, 'max' => 10 ],
						[ 'unit' => 'px', 'min' => 10, 'max' => 100 ],
						[ 'unit' => 'pt', 'min' => 10, 'max' => 100 ],
						[ 'unit' => 'em', 'min' => 1, 'max' => 100 ],
						[ 'unit' => 'rem', 'min' => 1, 'max' => 100 ],
					],
				],
			] ),
			'letterSpacing'  => self::responsive_sanitize( [ self::class, 'slider' ], $value['letterSpacing'] ?? '', [
				'options' => [
					'units' => [
						[ 'unit' => 'px', 'min' => - 20, 'max' => 20 ],
						[ 'unit' => 'pt', 'min' => - 20, 'max' => 20 ],
						[ 'unit' => 'em', 'min' => - 10, 'max' => 10 ],
						[ 'unit' => 'rem', 'min' => - 10, 'max' => 10 ],
					],
				],
			] ),
			'textTransform'  => in_array( $value['textTransform'] ?? '', [
				'capitalize',
				'uppercase',
				'lowercase'
			] ) ? $value['textTransform'] ?? '' : '',
			'textDecoration' => in_array( $value['textDecoration'] ?? '', [
				'underline',
				'line-through'
			] ) ? $value['textDecoration'] ?? '' : '',
		];
	}

	/**
	 * Sanitize responsive control value
	 *
	 * @param $callback
	 * @param $input
	 * @param $args
	 *
	 * @return array
	 */
	public static function responsive_sanitize( $callback, $input, $args ): array {

		$input = self::get_responsive_value( $input );

		foreach ( $input as $dev => $value ) {
			if ( $value !== Css::INITIAL_VALUE ) {
				$input[ $dev ] = call_user_func( $callback, $value, $args );
			} else {
				$input[ $dev ] = $value;
			}
		}

		return $input;
	}

	/**
	 * Get responsive value
	 *
	 * @param $value
	 *
	 * @return array
	 */
	public static function get_responsive_value( $value ): array {
		if ( is_array( $value ) && isset( $value['desktop'] ) ) {
			return $value;
		}

		return [
			'desktop' => $value,
			'tablet'  => Css::INITIAL_VALUE,
			'mobile'  => Css::INITIAL_VALUE,
		];
	}

	/**
	 * Sanitize callback for box-shadow control
	 *
	 * @param $input
	 * @param $args
	 *
	 * @return array
	 */
	public static function shadow( $input, $args ) {
		return [
			'enable'     => ( $input['enable'] ?? '' ) === 'yes' ? 'yes' : 'no',
			'horizontal' => self::slider( $input['horizontal'] ?? '', [
				'options' => [
					'min'         => - 100,
					'max'         => 100,
					'defaultUnit' => 'px',
				]
			] ),
			'vertical'   => self::slider( $input['vertical'] ?? '', [
				'options' => [
					'min'         => - 100,
					'max'         => 100,
					'defaultUnit' => 'px',
				]
			] ),
			'blur'       => self::slider( $input['blur'] ?? '', [
				'options' => [
					'min'         => 0,
					'max'         => 100,
					'defaultUnit' => 'px',
				]
			] ),
			'spread'     => self::slider( $input['spread'] ?? '', [
				'options' => [
					'min'         => - 100,
					'max'         => 100,
					'defaultUnit' => 'px',
				]
			] ),
			'color'      => self::rgba_color( $input['color'] ?? '' ),
		];
	}

	/**
	 * Slider sanitization
	 *
	 * @param $value
	 * @param $args
	 *
	 * @return int
	 */
	public static function slider( $value, $args ) {

		$options = $args['options'] ?? [];
		$min     = $options['min'] ?? 0;
		$max     = $options['max'] ?? 100;

		if ( ! is_string( $value ) && ! is_numeric( $value ) ) {
			return '';
		}

		$unit = preg_replace( [ '/[0-9]/', '/\-/', '/\./' ], '', $value );
		if ( $unit === '' ) {
			$unit = isset( $options['units'] )
				? $options['units'][0]['unit']
				: (
				isset( $options['defaultUnit'] ) && $options['defaultUnit'] === false
					? ''
					: $options['defaultUnit'] ?? 'px'
				);
		}

		if ( isset( $options['units'] ) ) {
			foreach ( $options['units'] as $config ) {
				if ( $config['unit'] === $unit ) {
					$max = $config['max'] ?? $max;
					$min = $config['min'] ?? $min;
					break;
				}
			}
		}

		$number = floatval( $value );

		if ( isset( $max ) && $number > $max ) {
			$number = $max;
		} elseif ( $number < $min ) {
			$number = $min;
		}

		$number = number_format( (float) $number, 2, '.', '' );
		if ( $number == (int) $number ) {
			$number = (int) $number;
		}

		return ( is_numeric( $number ) ? $number : $min ) . $unit;
	}

	/**
	 * RGBA color sanitization callback example.
	 *
	 * @param $color
	 *
	 * @return string|void
	 */
	public static function rgba_color( $color ) {
		if ( $color === Css::INITIAL_VALUE ) {
			return $color;
		}

		// css var
		if ( false !== strpos( $color, 'var' ) ) {
			return $color;
		}

		if ( empty( $color ) || is_array( $color ) ) {
			return 'rgba(0,0,0,0)';
		}

		// If string does not start with 'rgba', then treat as hex
		// sanitize the hex color and finally convert hex to rgba
		if ( false === strpos( $color, 'rgba' ) ) {
			return sanitize_hex_color( $color );
		}

		// By now we know the string is formatted as an rgba color so we need to further sanitize it.
		$color = str_replace( ' ', '', $color );
		sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );

		return 'rgba(' . $red . ',' . $green . ',' . $blue . ',' . $alpha . ')';
	}

	/**
	 * Sanitize callback for filters control
	 *
	 * @param $input
	 * @param $args
	 *
	 * @return array
	 */
	public static function filters( $input, $args ) {
		return [
			'enable'     => ( $input['enable'] ?? '' ) === 'yes' ? 'yes' : 'no',
			'blur'       => absint( $input['blur'] ?? 0 ),
			'contrast'   => absint( $input['contrast'] ?? 100 ),
			'brightness' => absint( $input['brightness'] ?? 100 ),
			'saturate'   => absint( $input['saturate'] ?? 100 ),
			'hue'        => absint( $input['hue'] ?? 0 ),
		];
	}

	/**
	 * Sanitize background control value
	 *
	 * @param $value
	 *
	 * @return array
	 */
	public static function background( $value ) {
		$type  = $value['type'] ?? 'color';
		$image = $value['image'] ?? [];

		return [
			'type'     => in_array( $type, [ 'color', 'gradient', 'image' ] ) ? $type : 'color',
			'color'    => self::rgba_color( $value['color'] ?? '' ),
			'gradient' => sanitize_text_field( $value['gradient'] ?? '' ),
			'image'    => [
				'source'     => self::image_uploader( $image['source'] ?? [] ),
				'repeat'     => in_array( $image['repeat'] ?? '', [
					'repeat',
					'repeat-x',
					'repeat-y',
					'no-repeat'
				] ) ? $image['repeat'] ?? '' : 'repeat',
				'size'       => in_array( $image['size'] ?? '', [
					'auto',
					'cover',
					'contain'
				] ) ? $image['size'] ?? '' : 'auto',
				'attachment' => in_array( $image['attachment'] ?? '', [
					'scroll',
					'fixed',
					'inherit'
				] ) ? $image['attachment'] ?? '' : 'scroll',
				'color'      => self::rgba_color( $image['color'] ?? '' ),
			]
		];
	}

	/**
	 * Image uploader sanitization
	 *
	 * @param $image
	 *
	 * @return array
	 */
	public static function image_uploader( $image ) {
		return [
			'attachment_id' => absint( $image['attachment_id'] ?? 0 ),
			'url'           => esc_url( $image['url'] ?? '' ),
			'x'             => number_format( (float) ( $image['x'] ?? 0 ), 2, '.', '' ),
			'y'             => number_format( (float) ( $image['y'] ?? 0 ), 2, '.', '' )
		];
	}

	/**
	 * @param $file
	 *
	 * @return array
	 */
	public static function attachment_info( $file ) {
		return [
			'id'       => absint( $file['id'] ?? 0 ),
			'url'      => esc_url( $file['url'] ?? '' ),
			'title'    => sanitize_text_field( $file['title'] ?? '' ),
			'filename' => sanitize_text_field( $file['filename'] ?? '' ),
		];
	}

	/**
	 * Sanitize callback for tags control
	 *
	 * @param $input
	 * @param $args
	 *
	 * @return array
	 */
	public static function tags( $input, $args ) {
		$options          = $args['options'] ?? [];
		$results          = [];
		$enforceWhitelist = $options['enforceWhitelist'] ?? false;
		if ( ! $enforceWhitelist ) {
			foreach ( $input as $value ) {
				$results[] = sanitize_text_field( $value );
			}

			return $results;
		}

		$whitelist = Utils::array_pluck( 'value', $options['whitelist'] ?? [] );
		foreach ( $input as $value ) {
			if ( in_array( $value, $whitelist ) ) {
				$results[] = $value;
			}
		}

		return $results;
	}

	/**
	 * Sanitize callback for layers control
	 *
	 * @param $input
	 * @param $args
	 *
	 * @return array|void
	 */
	public static function layers( $input, $args ) {
		if ( ! is_array( $input ) ) {
			return [];
		}

		$layers  = array_keys( $args['options']['layers'] ?? [] );
		$result  = [];
		$existed = [];

		foreach ( $input as $layer ) {
			if ( ! isset( $layer['id'] ) || in_array( $layer['id'], $existed ) || ! in_array( $layer['id'], $layers ) ) {
				continue;
			}

			$existed[] = $layer['id'];
			$result[]  = [
				'id'      => $layer['id'],
				'visible' => (bool) ( $layer['visible'] ?? false )
			];
		}

		return $result;
	}

	/**
	 * Sanitize callback for select control
	 *
	 * @param $input
	 * @param $args
	 *
	 * @return mixed|string
	 */
	public static function select( $input, $args ) {
		// Get list of choices from the control associated with the setting.
		$choices = $args['choices'];

		// If the input is a valid key, return it; otherwise, return the default.
		return ( array_key_exists( $input, $choices ) ? $input : ( $args['default'] ?? '' ) );
	}

	/**
	 * Sanitize callback for multi-select control
	 *
	 * @param $input
	 * @param $args
	 *
	 * @return array
	 */
	public static function multiSelect( $input, $args ) {
		// Get list of choices from the control associated with the setting.
		$choices = $args['choices'];
		$output  = array();

		foreach ( $choices as $choice => $data ) {
			$output[ $choice ] = ( is_array( $input ) && isset( $input[ $choice ] ) && $input[ $choice ] === 'yes' ) ? 'yes' : 'no';
		}

		return $output;
	}

	/**
	 * Sanitize callback for spacing control
	 *
	 * @param $input
	 * @param $args
	 *
	 * @return array
	 */
	public static function spacing( $input, $args ) {
		$slider_args = [
			'options' => [
				'units' => [
					[ 'unit' => 'px', 'min' => - 9999, 'max' => 9999 ],
					[ 'unit' => '%', 'min' => 0, 'max' => 100 ],
					[ 'unit' => 'em', 'min' => - 100, 'max' => 100 ],
					[ 'unit' => 'rem', 'min' => - 100, 'max' => 100 ],
					[ 'unit' => 'pt', 'min' => - 100, 'max' => 100 ],
				],
			]
		];

		$top    = $input['top'] ?? 0;
		$right  = $input['right'] ?? 0;
		$bottom = $input['bottom'] ?? 0;
		$left   = $input['left'] ?? 0;

		return [
			'top'    => $top == 'auto' ? $top : self::slider( $top ?? 0, $slider_args ),
			'right'  => $right == 'auto' ? $right : self::slider( $right ?? 0, $slider_args ),
			'bottom' => $bottom == 'auto' ? $bottom : self::slider( $bottom ?? 0, $slider_args ),
			'left'   => $left == 'auto' ? $left : self::slider( $left ?? 0, $slider_args ),
			'linked' => (bool) ( $input['linked'] ?? false )
		];
	}

	/**
	 * Sanitize callback for border control
	 *
	 * @param $input
	 * @param $args
	 *
	 * @return array
	 */
	public static function border( $input, $args ) {
		$input['style'] = $input['style'] ?? 'none';

		return [
			'inherit' => $input['inherit'] ?? false,
			'width'   => absint( $input['width'] ?? 1 ),
			'color'   => self::rgba_color( $input['color'] ?? '' ),
			'hover'   => self::rgba_color( $input['hover'] ?? '' ),
			'style'   => in_array( $input['style'], [
				'solid',
				'dashed',
				'dotted',
				'none'
			] ) ? $input['style'] : 'none'
		];
	}

	/**
	 * Checkbox sanitization callback example.
	 *
	 * Sanitization callback for 'checkbox' type controls. This callback sanitizes `$checked`
	 * as a boolean value, either TRUE or FALSE.
	 *
	 * @param mixed $checked Whether the checkbox is checked.
	 *
	 * @return string Whether the checkbox is checked.
	 */
	public static function checkbox( $checked ) {
		// Boolean check.
		return $checked === 'yes' ? $checked : 'no';
	}

	/**
	 * Palettes sanitization callback example.
	 *
	 * @param $input
	 * @param $args
	 *
	 * @return mixed|string
	 */
	public static function palettes( $input, $args ) {
		if ( isset( $args['options'] ) && isset( $args['options']['palettes'] ) ) {
			$palettes = $args['options']['palettes'];

			// If the input is a valid key, return it; otherwise, return the default.
			return ( array_key_exists( $input, $palettes ) ? $input : ( $args['default'] ?? '' ) );
		}

		return $args['default'] ?? '';
	}

	/**
	 * A collect of RGBA color sanitization callback example.
	 *
	 * @param $colors
	 *
	 * @return mixed
	 */
	public static function rgba_color_collect( $colors ) {
		if ( ! is_array( $colors ) ) {
			return [];
		}

		foreach ( $colors as $key => $value ) {
			$colors[ $key ] = self::rgba_color( $value );
		}

		return $colors;
	}

	/**
	 * Icons sanitization callback example.
	 *
	 * @param $input
	 * @param $args
	 *
	 * @return array
	 */
	public static function icons( $input, $args ) {
		$options   = $args['options'] ?? [];
		$libraries = $options['libraries'] ?? array_keys( IconsManager::allLibraries() );

		$library = $input['library'] ?? '';

		if ( ! in_array( $library, $libraries ) ) {
			$library = $libraries[0] ?? '';
		}

		return [
			'library' => $library,
			'value'   => sanitize_text_field( $input['value'] ?? '' ),
		];
	}
}