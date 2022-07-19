<?php
/**
 * WP_Style_Engine_Gutenberg
 *
 * Generates classnames and block styles.
 *
 * @package Gutenberg
 */

if ( class_exists( 'WP_Style_Engine_Gutenberg' ) ) {
	return;
}

/**
 * Singleton class representing the style engine.
 *
 * Consolidates rendering block styles to reduce duplication and streamline
 * CSS styles generation.
 *
 * This class is for internal core usage and is not supposed to be used by extenders (plugins and/or themes).
 * This is a low-level API that may need to do breaking changes. Please, use gutenberg_style_engine_get_styles instead.
 *
 * @access private
 */
class WP_Style_Engine_Gutenberg {
	/**
	 * Container for the main instance of the class.
	 *
	 * @var WP_Style_Engine_Gutenberg|null
	 */
	private static $instance = null;

	/**
	 * Style definitions that contain the instructions to
	 * parse/output valid Gutenberg styles from a block's attributes.
	 * For every style definition, the follow properties are valid:
	 *  - classnames    => (array) an array of classnames to be returned for block styles. The key is a classname or pattern.
	 *                    A value of `true` means the classname should be applied always. Otherwise, a valid CSS property (string)
	 *                    to match the incoming value, e.g., "color" to match var:preset|color|somePresetSlug.
	 *  - css_vars      => (array) an array of key value pairs used to generate CSS var values. The key is a CSS var pattern, whose `$slug` fragment will be replaced with a preset slug.
	 *                    The value should be a valid CSS property (string) to match the incoming value, e.g., "color" to match var:preset|color|somePresetSlug.
	 *  - property_keys => (array) array of keys whose values represent a valid CSS property, e.g., "margin" or "border".
	 *  - path          => (array) a path that accesses the corresponding style value in the block style object.
	 *  - value_func    => (string) the name of a function to generate a CSS definition array for a particular style object. The output of this function should be `array( "$property" => "$value", ... )`.
	 */
	const BLOCK_STYLE_DEFINITIONS_METADATA = array(
		'color'      => array(
			'text'       => array(
				'property_keys' => array(
					'default' => 'color',
				),
				'path'          => array( 'color', 'text' ),
				'css_vars'      => array(
					'--wp--preset--color--$slug' => 'color',
				),
				'classnames'    => array(
					'has-text-color'  => true,
					'has-$slug-color' => 'color',
				),
			),
			'background' => array(
				'property_keys' => array(
					'default' => 'background-color',
				),
				'path'          => array( 'color', 'background' ),
				'classnames'    => array(
					'has-background'             => true,
					'has-$slug-background-color' => 'color',
				),
			),
			'gradient'   => array(
				'property_keys' => array(
					'default' => 'background',
				),
				'path'          => array( 'color', 'gradient' ),
				'classnames'    => array(
					'has-background'                => true,
					'has-$slug-gradient-background' => 'gradient',
				),
			),
		),
		'border'     => array(
			'color'  => array(
				'property_keys' => array(
					'default'    => 'border-color',
					'individual' => 'border-%s-color',
				),
				'path'          => array( 'border', 'color' ),
				'classnames'    => array(
					'has-border-color'       => true,
					'has-$slug-border-color' => 'color',
				),
			),
			'radius' => array(
				'property_keys' => array(
					'default'    => 'border-radius',
					'individual' => 'border-%s-radius',
				),
				'path'          => array( 'border', 'radius' ),
			),
			'style'  => array(
				'property_keys' => array(
					'default'    => 'border-style',
					'individual' => 'border-%s-style',
				),
				'path'          => array( 'border', 'style' ),
			),
			'width'  => array(
				'property_keys' => array(
					'default'    => 'border-width',
					'individual' => 'border-%s-width',
				),
				'path'          => array( 'border', 'width' ),
			),
			'top'    => array(
				'value_func' => 'static::get_individual_property_css_declarations',
				'path'       => array( 'border', 'top' ),
				'css_vars'   => array(
					'color' => '--wp--preset--color--$slug',
				),
			),
			'right'  => array(
				'value_func' => 'static::get_individual_property_css_declarations',
				'path'       => array( 'border', 'right' ),
				'css_vars'   => array(
					'color' => '--wp--preset--color--$slug',
				),
			),
			'bottom' => array(
				'value_func' => 'static::get_individual_property_css_declarations',
				'path'       => array( 'border', 'bottom' ),
				'css_vars'   => array(
					'color' => '--wp--preset--color--$slug',
				),
			),
			'left'   => array(
				'value_func' => 'static::get_individual_property_css_declarations',
				'path'       => array( 'border', 'left' ),
				'css_vars'   => array(
					'color' => '--wp--preset--color--$slug',
				),
			),
		),
		'spacing'    => array(
			'padding' => array(
				'property_keys' => array(
					'default'    => 'padding',
					'individual' => 'padding-%s',
				),
				'path'          => array( 'spacing', 'padding' ),
			),
			'margin'  => array(
				'property_keys' => array(
					'default'    => 'margin',
					'individual' => 'margin-%s',
				),
				'path'          => array( 'spacing', 'margin' ),
			),
		),
		'typography' => array(
			'fontSize'       => array(
				'property_keys' => array(
					'default' => 'font-size',
				),
				'path'          => array( 'typography', 'fontSize' ),
				'classnames'    => array(
					'has-$slug-font-size' => 'font-size',
				),
			),
			'fontFamily'     => array(
				'property_keys' => array(
					'default' => 'font-family',
				),
				'path'          => array( 'typography', 'fontFamily' ),
				'classnames'    => array(
					'has-$slug-font-family' => 'font-family',
				),
			),
			'fontStyle'      => array(
				'property_keys' => array(
					'default' => 'font-style',
				),
				'path'          => array( 'typography', 'fontStyle' ),
			),
			'fontWeight'     => array(
				'property_keys' => array(
					'default' => 'font-weight',
				),
				'path'          => array( 'typography', 'fontWeight' ),
			),
			'lineHeight'     => array(
				'property_keys' => array(
					'default' => 'line-height',
				),
				'path'          => array( 'typography', 'lineHeight' ),
			),
			'textDecoration' => array(
				'property_keys' => array(
					'default' => 'text-decoration',
				),
				'path'          => array( 'typography', 'textDecoration' ),
			),
			'textTransform'  => array(
				'property_keys' => array(
					'default' => 'text-transform',
				),
				'path'          => array( 'typography', 'textTransform' ),
			),
			'letterSpacing'  => array(
				'property_keys' => array(
					'default' => 'letter-spacing',
				),
				'path'          => array( 'typography', 'letterSpacing' ),
			),
		),
	);

	/**
	 * Utility method to retrieve the main instance of the class.
	 *
	 * The instance will be created if it does not exist yet.
	 *
	 * @return WP_Style_Engine_Gutenberg The main instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Extracts the slug in kebab case from a preset string, e.g., "heavenly-blue" from 'var:preset|color|heavenlyBlue'.
	 *
	 * @param string? $style_value  A single css preset value.
	 * @param string  $property_key The CSS property that is the second element of the preset string. Used for matching.
	 *
	 * @return string|null The slug, or null if not found.
	 */
	protected static function get_slug_from_preset_value( $style_value, $property_key ) {
		if ( is_string( $style_value ) && strpos( $style_value, "var:preset|{$property_key}|" ) !== false ) {
			$index_to_splice = strrpos( $style_value, '|' ) + 1;
			return _wp_to_kebab_case( substr( $style_value, $index_to_splice ) );
		}
		return null;
	}

	/**
	 * Checks whether an incoming block style value is valid.
	 *
	 * @param string? $style_value  A single css preset value.
	 *
	 * @return boolean
	 */
	protected static function is_valid_style_value( $style_value ) {
		if ( '0' === $style_value ) {
			return true;
		}

		if ( empty( $style_value ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns classnames, and generates classname(s) from a CSS preset property pattern, e.g., 'var:preset|color|heavenly-blue'.
	 *
	 * @param array         $style_value      A single raw style value or css preset property from the generate() $block_styles array.
	 * @param array<string> $style_definition A single style definition from BLOCK_STYLE_DEFINITIONS_METADATA.
	 *
	 * @return array        An array of CSS classnames.
	 */
	protected static function get_classnames( $style_value, $style_definition ) {
		$classnames = array();

		if ( empty( $style_value ) ) {
			return $classnames;
		}

		if ( ! empty( $style_definition['classnames'] ) ) {
			foreach ( $style_definition['classnames'] as $classname => $property_key ) {
				if ( true === $property_key ) {
					$classnames[] = $classname;
				}

				$slug = static::get_slug_from_preset_value( $style_value, $property_key );

				if ( $slug ) {
					// Right now we expect a classname pattern to be stored in BLOCK_STYLE_DEFINITIONS_METADATA.
					// One day, if there are no stored schemata, we could allow custom patterns or
					// generate classnames based on other properties
					// such as a path or a value or a prefix passed in options.
					$classnames[] = strtr( $classname, array( '$slug' => $slug ) );
				}
			}
		}

		return $classnames;
	}

	/**
	 * Returns an array of CSS declarations based on valid block style values.
	 *
	 * @param array         $style_value      A single raw style value from the generate() $block_styles array.
	 * @param array<string> $style_definition A single style definition from BLOCK_STYLE_DEFINITIONS_METADATA.
	 * @param boolean       $should_return_css_vars Whether to try to build and return CSS var values.
	 *
	 * @return array        An array of CSS definitions, e.g., array( "$property" => "$value" ).
	 */
	protected static function get_css_declarations( $style_value, $style_definition, $should_return_css_vars ) {
		if (
			isset( $style_definition['value_func'] ) &&
			is_callable( $style_definition['value_func'] )
		) {
			return call_user_func( $style_definition['value_func'], $style_value, $style_definition );
		}

		$css_declarations    = array();
		$style_property_keys = $style_definition['property_keys'];

		// Build CSS var values from var:? values, e.g, `var(--wp--css--rule-slug )`
		// Check if the value is a CSS preset and there's a corresponding css_var pattern in the style definition.
		if ( is_string( $style_value ) && strpos( $style_value, 'var:' ) !== false ) {
			if ( $should_return_css_vars && ! empty( $style_definition['css_vars'] ) ) {
				foreach ( $style_definition['css_vars'] as $css_var_pattern => $property_key ) {
					$slug = static::get_slug_from_preset_value( $style_value, $property_key );
					if ( $slug ) {
						$css_var = strtr(
							$css_var_pattern,
							array( '$slug' => $slug )
						);
						$css_declarations[ $style_property_keys['default'] ] = "var($css_var)";
					}
				}
			}
			return $css_declarations;
		}

		// Default rule builder.
		// If the input contains an array, assume box model-like properties
		// for styles such as margins and padding.
		if ( is_array( $style_value ) ) {
			foreach ( $style_value as $key => $value ) {
				$individual_property                      = sprintf( $style_property_keys['individual'], _wp_to_kebab_case( $key ) );
				$css_declarations[ $individual_property ] = $value;
			}
		} else {
			$css_declarations[ $style_property_keys['default'] ] = $style_value;
		}

		return $css_declarations;
	}

	/**
	 * Returns classname and CSS from a block styles object.
	 * Return values are parsed based on the instructions in BLOCK_STYLE_DEFINITIONS_METADATA.
	 *
	 * @param array $block_styles An array of styles from a block's attributes.
	 * @param array $options array(
	 *     'selector' => (string) When a selector is passed, `generate()` will return a full CSS rule `$selector { ...rules }`, otherwise a concatenated string of properties and values.
	 *     'css_vars' => (boolean) Whether to covert CSS values to var() values. If `true` the style engine will try to parse var:? values and output var( --wp--preset--* ) rules. Default is `false`.
	 * );.
	 *
	 * @return array|null array(
	 *     'css'        => (string) A CSS ruleset formatted to be placed in an HTML `style` attribute or tag.  Default is a string of inline styles.
	 *     'classnames' => (string) Classnames separated by a space.
	 * );
	 */
	public function generate( $block_styles, $options ) {
		if ( empty( $block_styles ) || ! is_array( $block_styles ) ) {
			return null;
		}

		$css_declarations       = array();
		$classnames             = array();
		$should_return_css_vars = isset( $options['css_vars'] ) && true === $options['css_vars'];

		// Collect CSS and classnames.
		foreach ( self::BLOCK_STYLE_DEFINITIONS_METADATA as $definition_group_key => $definition_group_style ) {
			if ( empty( $block_styles[ $definition_group_key ] ) ) {
				continue;
			}
			foreach ( $definition_group_style as $style_definition ) {
				$style_value = _wp_array_get( $block_styles, $style_definition['path'], null );

				if ( ! static::is_valid_style_value( $style_value ) ) {
					continue;
				}

				$classnames       = array_merge( $classnames, static::get_classnames( $style_value, $style_definition ) );
				$css_declarations = array_merge( $css_declarations, static::get_css_declarations( $style_value, $style_definition, $should_return_css_vars ) );
			}
		}

		// Build CSS rules output.
		$css_selector              = isset( $options['selector'] ) ? $options['selector'] : null;
		$filtered_css_declarations = array();

		if ( ! empty( $css_declarations ) ) {
			// Generate inline style declarations.
			foreach ( $css_declarations as $css_property => $css_value ) {
				$filtered_css_declaration = esc_html( safecss_filter_attr( "{$css_property}: {$css_value}" ) );
				if ( ! empty( $filtered_css_declaration ) ) {
					$filtered_css_declarations[] = $filtered_css_declaration . ';';
				}
			}
		}

		// The return object.
		$styles_output = array();

		// Return css, if any.
		if ( ! empty( $filtered_css_declarations ) ) {
			// Return an entire rule if there is a selector.
			if ( $css_selector ) {
				$css_rule             = "$css_selector { ";
				$css_rule            .= implode( ' ', $filtered_css_declarations );
				$css_rule            .= ' }';
				$styles_output['css'] = $css_rule;
			} else {
				$styles_output['css'] = implode( ' ', $filtered_css_declarations );
			}
		}

		// Return classnames, if any.
		if ( ! empty( $classnames ) ) {
			$styles_output['classnames'] = implode( ' ', array_unique( $classnames ) );
		}

		return $styles_output;
	}


	/**
	 * Style value parser that returns a CSS definition array comprising style properties
	 * that have keys representing individual style properties, otherwise known as longhand CSS properties.
	 * e.g., "$style_property-$individual_feature: $value;", which could represent the following:
	 * "border-{top|right|bottom|left}-{color|width|style}: {value};" or,
	 * "border-image-{outset|source|width|repeat|slice}: {value};"
	 *
	 * @param array $style_value                    A single raw Gutenberg style attributes value for a CSS property.
	 * @param array $individual_property_definition A single style definition from BLOCK_STYLE_DEFINITIONS_METADATA.
	 *
	 * @return array An array of CSS definitions, e.g., array( "$property" => "$value" ).
	 */
	protected static function get_individual_property_css_declarations( $style_value, $individual_property_definition ) {
		$css_declarations = array();

		if ( ! is_array( $style_value ) || empty( $style_value ) || empty( $individual_property_definition['path'] ) ) {
			return $css_declarations;
		}

		// The first item in $individual_property_definition['path'] array tells us the style property, e.g., "border".
		// We use this to get a corresponding CSS style definition such as "color" or "width" from the same group.
		// The second item in $individual_property_definition['path'] array refers to the individual property marker, e.g., "top".
		$definition_group_key    = $individual_property_definition['path'][0];
		$individual_property_key = $individual_property_definition['path'][1];

		foreach ( $style_value as $css_property => $value ) {
			if ( empty( $value ) ) {
				continue;
			}

			// Build a path to the individual rules in definitions.
			$style_definition_path = array( $definition_group_key, $css_property );
			$style_definition      = _wp_array_get( self::BLOCK_STYLE_DEFINITIONS_METADATA, $style_definition_path, null );

			if ( $style_definition && isset( $style_definition['property_keys']['individual'] ) ) {
				// Set a CSS var if there is a valid preset value.
				$slug = isset( $individual_property_definition['css_vars'][ $css_property ] ) ? static::get_slug_from_preset_value( $value, $css_property ) : null;
				if ( $slug ) {
					$css_var = strtr(
						$individual_property_definition['css_vars'][ $css_property ],
						array( '$slug' => $slug )
					);
					$value   = "var($css_var)";
				}
				$individual_css_property                      = sprintf( $style_definition['property_keys']['individual'], $individual_property_key );
				$css_declarations[ $individual_css_property ] = $value;
			}
		}
		return $css_declarations;
	}
}

/**
 * Global public interface method to WP_Style_Engine_Gutenberg->generate.
 *
 * Returns an CSS ruleset.
 * Styles are bundled based on the instructions in BLOCK_STYLE_DEFINITIONS_METADATA.
 *
 * Example usage:
 *
 * $styles = gutenberg_style_engine_generate( array( 'color' => array( 'text' => '#cccccc' ) ) );
 * // Returns `'color: #cccccc'`.
 *
 * @access public
 *
 * @param array $block_styles An array of styles from a block's attributes.
 * @param array $options An array of options to determine the output.
 *
 * @return array|null array(
 *     'styles'     => (string) A CSS ruleset formatted to be placed in an HTML `style` attribute or tag.
 *     'classnames' => (string) Classnames separated by a space.
 * );
 */
function gutenberg_style_engine_generate( $block_styles, $options = array() ) {
	if ( class_exists( 'WP_Style_Engine_Gutenberg' ) ) {
		$style_engine = WP_Style_Engine_Gutenberg::get_instance();
		return $style_engine->generate( $block_styles, $options );
	}
	return null;
}
