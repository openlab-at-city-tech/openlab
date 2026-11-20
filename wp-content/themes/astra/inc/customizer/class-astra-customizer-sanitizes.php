<?php
/**
 * Astra Theme Customizer Sanitize.
 *
 * @package     Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.0.0
 */

// No direct access, please.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customizer Sanitizes
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'Astra_Customizer_Sanitizes' ) ) {

	/**
	 * Customizer Sanitizes Initial setup
	 */
	class Astra_Customizer_Sanitizes {
		/**
		 * Instance
		 *
		 * @var object
		 */
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
		}

		/**
		 * Sanitize Logo SVG Icon.
		 *
		 * @param array $input Logo SVG Icon value.
		 * @return array Sanitized Logo SVG Icon value.
		 * @since 4.7.0
		 */
		public static function sanitize_logo_svg_icon( $input ) {
			if ( empty( $input['type'] ) ) {
				return array(
					'type'  => '',
					'value' => '',
				);
			}

			if ( 'icon-library' === $input['type'] ) {

				$svg_icons = function_exists( 'astra_get_logo_svg_icons_array' ) ? astra_get_logo_svg_icons_array() : array();

				return array(
					'type'  => 'icon-library',
					'value' => isset( $input['value'] ) && isset( $svg_icons[ $input['value'] ] ) ? $input['value'] : '',
				);
			}
			/* Strip code starts */
			return array(
				'type'  => 'custom',
				'value' => isset( $input['value'] ) ? self::sanitize_svg_code( $input['value'] ) : '',
			);
			/* Strip code ends */
		}

		/* Strip code starts */
		/**
		 * Sanitizes SVG Code string.
		 *
		 * @param string $original_content SVG code to sanitize.
		 * @return string
		 * @since 4.7.0
		 */
		public static function sanitize_svg_code( $original_content ) {

			if ( ! $original_content ) {
				return '';
			}

			// Define allowed tags and attributes.
			$allowed_tags = apply_filters( 'astra_custom_svg_allowed_tags', array( 'a', 'circle', 'clippath', 'defs', 'style', 'desc', 'ellipse', 'fegaussianblur', 'filter', 'foreignobject', 'g', 'image', 'line', 'lineargradient', 'marker', 'mask', 'metadata', 'path', 'pattern', 'polygon', 'polyline', 'radialgradient', 'rect', 'stop', 'svg', 'switch', 'symbol', 'text', 'textpath', 'title', 'tspan', 'use' ) );

			$allowed_attributes = apply_filters( 'astra_custom_svg_allowed_attributes', array( 'width', 'height', 'fill', 'class', 'clip-path', 'clip-rule', 'fill-opacity', 'fill-rule', 'filter', 'id', 'mask', 'opacity', 'stroke', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke-width', 'style', 'systemlanguage', 'transform', 'href', 'xlink:href', 'xlink:title', 'cx', 'cy', 'r', 'requiredfeatures', 'clippathunits', 'type', 'rx', 'ry', 'color-interpolation-filters', 'stddeviation', 'filterres', 'filterunits', 'primitiveunits', 'x', 'y', 'font-size', 'display', 'font-family', 'font-style', 'font-weight', 'text-anchor', 'marker-end', 'marker-mid', 'marker-start', 'x1', 'x2', 'y1', 'y2', 'gradienttransform', 'gradientunits', 'spreadmethod', 'markerheight', 'markerunits', 'markerwidth', 'orient', 'preserveaspectratio', 'refx', 'refy', 'maskcontentunits', 'maskunits', 'd', 'patterncontentunits', 'patterntransform', 'patternunits', 'points', 'fx', 'fy', 'offset', 'stop-color', 'stop-opacity', 'xmlns', 'xmlns:se', 'xmlns:xlink', 'xml:space', 'method', 'spacing', 'startoffset', 'dx', 'dy', 'rotate', 'textlength', 'viewbox' ) );

			$is_encoded = false;

			$needle = "\x1f\x8b\x08";
			if ( function_exists( 'mb_strpos' ) ) {
				$is_encoded = 0 === mb_strpos( $original_content, $needle );
			} else {
				$is_encoded = 0 === strpos( $original_content, $needle );
			}

			if ( $is_encoded ) {
				$original_content = gzdecode( $original_content );
				if ( $original_content === false ) {
					return '';
				}
			}

			// Strip php tags.
			 // phpcs:ignore Generic.PHP.ForbiddenFunctions.FoundWithAlternative -- Safe usage: no /e modifier, sanitizes SVG by removing PHP tags
			$content = preg_replace( '/<\?(=|php)(.+?)\?>/i', '', $original_content );
			 // phpcs:ignore Generic.PHP.ForbiddenFunctions.FoundWithAlternative -- Safe usage: no /e modifier, sanitizes SVG by removing PHP tags
			$content = preg_replace( '/<\?(.*)\?>/Us', '', $content );
			 // phpcs:ignore Generic.PHP.ForbiddenFunctions.FoundWithAlternative -- Safe usage: no /e modifier, sanitizes SVG by removing PHP tags
			$content = preg_replace( '/<\%(.*)\%>/Us', '', $content );

			if ( false !== strpos( $content, '<?php' ) || ( false !== strpos( $content, '<%' ) ) ) {
				return '';
			}

			// Strip comments.
			 // phpcs:ignore Generic.PHP.ForbiddenFunctions.FoundWithAlternative -- Safe usage: no /e modifier, sanitizes SVG by removing PHP tags
			$content = preg_replace( '/<!--(.*)-->/Us', '', $content );
			 // phpcs:ignore Generic.PHP.ForbiddenFunctions.FoundWithAlternative -- Safe usage: no /e modifier, sanitizes SVG by removing PHP tags
			$content = preg_replace( '/\/\*(.*)\*\//Us', '', $content );

			if ( false !== strpos( $content, '<!--' ) || ( false !== strpos( $content, '/*' ) ) ) {
				return '';
			}

			// Strip line breaks.
			// phpcs:ignore Generic.PHP.ForbiddenFunctions.FoundWithAlternative -- Safe usage: no /e modifier, sanitizes SVG by removing PHP tags
			$content = preg_replace( '/\r|\n/', '', $content );

			// Find the start and end tags so we can cut out miscellaneous garbage.
			$start = strpos( $content, '<svg' );
			$end   = strrpos( $content, '</svg>' );
			if ( false === $start || false === $end ) {
				return '';
			}

			$content = substr( $content, $start, ( $end - $start + 6 ) );

			// Suppress the errors.
			libxml_use_internal_errors( true );

			// Load the SVG content using SimpleXML.
			$xml = simplexml_load_string( $content, 'SimpleXMLElement', LIBXML_NOENT );
			if ( $xml === false ) {
				return '';
			}

			// Sanitize elements.
			/** @psalm-suppress PossiblyNullPropertyFetch */
			$dom = dom_import_simplexml( $xml )->ownerDocument;
			/** @psalm-suppress PossiblyNullPropertyAssignment */
			$dom->formatOutput = false;
			/** @psalm-suppress PossiblyNullPropertyAssignment */
			$dom->preserveWhiteSpace = false;
			/** @psalm-suppress PossiblyNullPropertyAssignment */
			$dom->strictErrorChecking = false;

			/** @psalm-suppress PossiblyNullReference */
			$elements = $dom->getElementsByTagName( '*' );
			for ( $index = $elements->length - 1; $index >= 0; $index-- ) {
				$current_element = $elements->item( $index );
				if ( ! in_array( strtolower( $current_element->tagName ), $allowed_tags ) ) {
					$current_element->parentNode->removeChild( $current_element );
					continue;
				}

				// Validate allowed attributes.
				for ( $i = $current_element->attributes->length - 1; $i >= 0; $i-- ) {
					$attr_name           = $current_element->attributes->item( $i )->name;
					$attr_name_lowercase = strtolower( $attr_name );
					if ( ! in_array( $attr_name_lowercase, $allowed_attributes ) &&
						! preg_match( '/^aria-/', $attr_name_lowercase ) &&
						! preg_match( '/^data-/', $attr_name_lowercase ) ) {
						$current_element->removeAttribute( $attr_name );
						continue;
					}

					$attr_value = $current_element->attributes->item( $i )->value;
					if ( ! empty( $attr_value ) &&
						( preg_match( '/^((https?|ftp|file):)?\/\//i', $attr_value ) ||
						preg_match( '/base64|data|(?:java)?script|alert\(|window\.|document/i', $attr_value ) ) ) {
						$current_element->removeAttribute( $attr_name );
						continue;
					}
				}

				// Strip xlink:href.
				$xlink_href = $current_element->getAttributeNS( 'http://www.w3.org/1999/xlink', 'href' );
				if ( $xlink_href && strpos( $xlink_href, '#' ) !== 0 ) {
					$current_element->removeAttributeNS( 'http://www.w3.org/1999/xlink', 'href' );
				}

				// Strip use tag with external references.
				if ( strtolower( $current_element->tagName ) === 'use' ) {
					$xlink_href = $current_element->getAttributeNS( 'http://www.w3.org/1999/xlink', 'href' );
					if ( $current_element->parentNode && $xlink_href && strpos( $xlink_href, '#' ) !== 0 ) {
						$current_element->parentNode->removeChild( $current_element );
					}
				}
			}

			// Export sanitized SVG to string.
			$sanitized = $dom->saveXML( $dom->documentElement, LIBXML_NOEMPTYTAG );

			// Restore defaults.
			libxml_use_internal_errors( false );

			return $sanitized;
		}
		/* Strip code ends */

		/**
		 * Sanitize Social Icons using existing logo SVG sanitization.
		 * This function processes the social icons array and uses sanitize_logo_svg_icon() for each item.
		 *
		 * @param array $input Social icons array.
		 * @return array Sanitized social icons array.
		 * @since 4.11.8
		 */
		public static function sanitize_social_icons( $input ) {
			if ( ! isset( $input['items'] ) || ! is_array( $input['items'] ) ) {
				return $input;
			}

			// Process each social icon item
			foreach ( $input['items'] as $key => $item ) {
				if ( ! is_array( $item ) ) {
					continue;
				}

				// If this item has icon data (either icon-library or custom), sanitize it
				if ( isset( $item['icon_type'] ) && ( isset( $item['icon'] ) || isset( $item['custom_svg'] ) ) ) {
					if ( 'custom' === $item['icon_type'] ) {
						$input['items'][ $key ]['custom_svg'] = isset( $item['custom_svg'] ) ? self::sanitize_svg_code( $item['custom_svg'] ) : '';
						if ( ! isset( $input['items'][ $key ]['icon'] ) && isset( $item['id'] ) ) {
							$input['items'][ $key ]['icon'] = $item['id'];
						}
					} else {
						$input['items'][ $key ]['icon'] = isset( $item['icon'] ) && is_string( $item['icon'] ) ? sanitize_text_field( $item['icon'] ) : ( isset( $item['id'] ) ? sanitize_text_field( $item['id'] ) : '' );
						// Remove custom_svg if switching to icon-library
						if ( isset( $input['items'][ $key ]['custom_svg'] ) ) {
							unset( $input['items'][ $key ]['custom_svg'] );
						}
					}
					$input['items'][ $key ]['icon_type'] = in_array( $item['icon_type'], array( 'icon-library', 'custom' ), true ) ? $item['icon_type'] : 'icon-library';
				} elseif ( isset( $item['id'] ) && ! isset( $item['icon'] ) && ! isset( $item['custom_svg'] ) ) {
					// Fallback for items without icon_type - assume icon-library
					$input['items'][ $key ]['icon']      = sanitize_text_field( $item['id'] );
					$input['items'][ $key ]['icon_type'] = 'icon-library';
				}
			}

			return $input;
		}

		/**
		 * Sanitize Integer
		 *
		 * @param  mixed $input Customizer setting input number.
		 * @return int          Absolute number.
		 */
		public static function sanitize_integer( $input ) {
			return absint( $input );
		}

		/**
		 * Sanitize Integer
		 *
		 * @param  number $val      Customizer setting input number.
		 * @param  object $setting  Setting object.
		 * @return number           Return number.
		 */
		public static function sanitize_number( $val, $setting ) {

			$input_attrs = array();

			if ( isset( $setting->manager->get_control( $setting->id )->input_attrs ) ) {
				$input_attrs = $setting->manager->get_control( $setting->id )->input_attrs;
			}

			if ( isset( $input_attrs ) ) {

				$input_attrs['min']  = isset( $input_attrs['min'] ) ? $input_attrs['min'] : 0;
				$input_attrs['step'] = isset( $input_attrs['step'] ) ? $input_attrs['step'] : 1;

				if ( isset( $input_attrs['max'] ) && $val > $input_attrs['max'] ) {
					$val = $input_attrs['max'];
				} elseif ( $val < $input_attrs['min'] ) {
					$val = $input_attrs['min'];
				}

				/** @psalm-suppress InvalidCast */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				$dv = (float) $val / $input_attrs['step'];
				/** @psalm-suppress InvalidCast */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

				$dv = round( $dv );

				$val = $dv * $input_attrs['step'];

				$val = number_format( (float) $val, 2, '.', '' );
				if ( $val == (int) $val ) {
					$val = (int) $val;
				}
			}

			return is_numeric( $val ) ? $val : 0;
		}

		/**
		 * Sanitize Integer
		 *
		 * @param  number $val Customizer setting input number.
		 * @return number        Return number.
		 */
		public static function sanitize_number_n_blank( $val ) {
			return is_numeric( $val ) ? $val : '';
		}

		/**
		 * Sanitize Spacing
		 *
		 * @param  number $val Customizer setting input number.
		 * @return number        Return number.
		 * @since  1.0.6
		 */
		public static function sanitize_spacing( $val ) {

			foreach ( $val as $key => $value ) {
				$val[ $key ] = is_numeric( $val[ $key ] ) && $val[ $key ] >= 0 ? $val[ $key ] : '';
			}

			return $val;
		}

		/**
		 * Sanitize link
		 *
		 * @param  array $val Customizer setting link.
		 * @return array        Return array.
		 * @since  2.3.0
		 */
		public static function sanitize_link( $val ) {

			$link = array();

			$link['url']      = esc_url_raw( $val['url'] );
			$link['new_tab']  = esc_attr( $val['new_tab'] );
			$link['link_rel'] = esc_attr( $val['link_rel'] );

			return $link;
		}

		/**
		 * Sanitize responsive  Spacing
		 *
		 * @param  number $val Customizer setting input number.
		 * @return number        Return number.
		 * @since  1.2.1
		 */
		public static function sanitize_responsive_spacing( $val ) {

			$spacing = array(
				'desktop'      => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
				'tablet'       => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
				'mobile'       => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
				'desktop-unit' => 'px',
				'tablet-unit'  => 'px',
				'mobile-unit'  => 'px',
			);

			if ( isset( $val['desktop'] ) ) {
				$spacing['desktop'] = array_map( array( self::class, 'check_numberic_values' ), $val['desktop'] );

				$spacing['tablet'] = array_map( array( self::class, 'check_numberic_values' ), $val['tablet'] );

				$spacing['mobile'] = array_map( array( self::class, 'check_numberic_values' ), $val['mobile'] );

				$allowed_units = array( '', 'px', 'em', 'rem', '%' );

				if ( isset( $val['desktop-unit'] ) ) {
					$desktop_unit            = sanitize_text_field( $val['desktop-unit'] );
					$spacing['desktop-unit'] = in_array( $desktop_unit, $allowed_units, true ) ? $desktop_unit : 'px';
				}

				if ( isset( $val['tablet-unit'] ) ) {
					$tablet_unit            = sanitize_text_field( $val['tablet-unit'] );
					$spacing['tablet-unit'] = in_array( $tablet_unit, $allowed_units, true ) ? $tablet_unit : 'px';
				}

				if ( isset( $val['mobile-unit'] ) ) {
					$mobile_unit            = sanitize_text_field( $val['mobile-unit'] );
					$spacing['mobile-unit'] = in_array( $mobile_unit, $allowed_units, true ) ? $mobile_unit : 'px';
				}

				return $spacing;

			}

			/** @psalm-suppress DocblockTypeContradiction */
			if ( is_array( $val ) ) {
				foreach ( $val as $key => $value ) {
					$val[ $key ] = is_numeric( $val[ $key ] ) ? $val[ $key ] : '';
				}
			}
			/** @psalm-suppress InvalidReturnStatement */
			return $val;
		}

		/**
		 * Check numeric values.
		 *
		 * @param  int|string $value Value of variable.
		 * @return string|int Return empty if $value is not integer.
		 *
		 * @since 2.5.4
		 */
		public static function check_numberic_values( $value ) {
			return is_numeric( $value ) ? $value : '';
		}

		/**
		 * Sanitize Responsive Slider
		 *
		 * @param  array|number $val Customizer setting input number.
		 * @param  object       $setting Setting Onject.
		 * @return array        Return number.
		 */
		public static function sanitize_responsive_slider( $val, $setting ) {

			$input_attrs = array();
			if ( isset( $setting->manager->get_control( $setting->id )->input_attrs ) ) {
				$input_attrs = $setting->manager->get_control( $setting->id )->input_attrs;
			}

			$responsive = array(
				'desktop' => '',
				'tablet'  => '',
				'mobile'  => '',
			);
			if ( is_array( $val ) ) {
				$responsive['desktop'] = is_numeric( $val['desktop'] ) ? $val['desktop'] : '';
				$responsive['tablet']  = is_numeric( $val['tablet'] ) ? $val['tablet'] : '';
				$responsive['mobile']  = is_numeric( $val['mobile'] ) ? $val['mobile'] : '';
			} else {
				$responsive['desktop'] = is_numeric( $val ) ? $val : '';
			}

			foreach ( $responsive as $key => $value ) {
					$value              = isset( $input_attrs['min'] ) && ( ! empty( $value ) ) && ( $input_attrs['min'] > $value ) ? $input_attrs['min'] : $value;
					$value              = isset( $input_attrs['max'] ) && ( ! empty( $value ) ) && ( $input_attrs['max'] < $value ) ? $input_attrs['max'] : $value;
					$responsive[ $key ] = $value;
			}

			return $responsive;
		}

		/**
		 * Sanitize Responsive Typography
		 *
		 * @param  array|number $val Customizer setting input number.
		 * @return array        Return number.
		 */
		public static function sanitize_responsive_typo( $val ) {

			$responsive = array(
				'desktop'      => '',
				'tablet'       => '',
				'mobile'       => '',
				'desktop-unit' => '',
				'tablet-unit'  => '',
				'mobile-unit'  => '',
			);
			if ( is_array( $val ) ) {
				$responsive['desktop']      = isset( $val['desktop'] ) && is_numeric( $val['desktop'] ) ? $val['desktop'] : '';
				$responsive['tablet']       = isset( $val['tablet'] ) && is_numeric( $val['tablet'] ) ? $val['tablet'] : '';
				$responsive['mobile']       = isset( $val['mobile'] ) && is_numeric( $val['mobile'] ) ? $val['mobile'] : '';
				$responsive['desktop-unit'] = isset( $val['desktop-unit'] ) && in_array( $val['desktop-unit'], array( '', 'px', 'em', 'rem', '%' ) ) ? $val['desktop-unit'] : 'px';
				$responsive['tablet-unit']  = isset( $val['tablet-unit'] ) && in_array( $val['tablet-unit'], array( '', 'px', 'em', 'rem', '%' ) ) ? $val['tablet-unit'] : 'px';
				$responsive['mobile-unit']  = isset( $val['mobile-unit'] ) && in_array( $val['mobile-unit'], array( '', 'px', 'em', 'rem', '%' ) ) ? $val['mobile-unit'] : 'px';
			} else {
				$responsive['desktop'] = is_numeric( $val ) ? $val : '';
			}
			return $responsive;
		}

		/**
		 * Validate Email
		 *
		 * @param  object $validity setting input validity.
		 * @param  string $value    setting input value.
		 * @return object           Return the validity object.
		 */
		public static function validate_email( $validity, $value ) {
			if ( ! is_email( $value ) ) {
				$validity->add( 'required', __( 'Enter valid email address!', 'astra' ) );
			}
			return $validity;
		}

		/**
		 * Validate Sidebar Content Width
		 *
		 * @param  number $value Sidebar content width.
		 * @return number        Sidebar content width value.
		 */
		public static function validate_sidebar_content_width( $value ) {
			$value = intval( $value );
			if ( $value > 50 ) {
				$value = 50;
			} elseif ( $value < 15 ) {
				$value = 15;
			}
			return $value;
		}

		/**
		 * Validate Site width
		 *
		 * @param  number $value Site width.
		 * @return number        Site width value.
		 */
		public static function validate_site_width( $value ) {
			$value = intval( $value );
			if ( 1920 < $value ) {
				$value = 1920;
			} elseif ( 768 > $value ) {
				$value = 768;
			}
			return $value;
		}

		/**
		 * Validate Site padding
		 *
		 * @param  number $value Site padding.
		 * @return number        Site padding value.
		 */
		public static function validate_site_padding( $value ) {
			$value = intval( $value );
			if ( 200 < $value ) {
				$value = 200;
			} elseif ( 1 > $value ) {
				$value = 1;
			}
			return $value;
		}

		/**
		 * Validate Site margin
		 *
		 * @param  number $value Site margin.
		 * @return number        Site margin value.
		 */
		public static function validate_site_margin( $value ) {
			$value = intval( $value );
			if ( 600 < $value ) {
				$value = 600;
			} elseif ( 0 > $value ) {
				$value = 0;
			}
			return $value;
		}

		/**
		 * Sanitize checkbox
		 *
		 * @param  mixed $input setting input.
		 * @return number        setting input value.
		 */
		public static function sanitize_checkbox( $input ) {
			if ( $input ) {
				$output = '1';
			} else {
				$output = false;
			}
			return $output;
		}

		/**
		 * Sanitize HEX color
		 *
		 * @since 4.14.2 Also keeps CSS-wide keywords and 4/8-digit hex.
		 * @param  mixed $color setting input.
		 * @return string       setting input value.
		 */
		public static function sanitize_hex_color( $color ) {

			if ( ! is_string( $color ) || '' === $color ) {
				return '';
			}

			$color = trim( $color );

			// Keywords such as transparent/inherit must round-trip.
			if ( self::is_css_color_keyword( $color ) ) {
				return $color;
			}

			// 3, 4, 6 or 8 hex digits.
			if ( preg_match( '/^#(?:[0-9a-fA-F]{3,4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/D', $color ) ) {
				return $color;
			}

			return '';
		}

		/**
		 * Sanitize Alpha color
		 *
		 * @since 4.14.2 Also keeps CSS-wide keywords, 4/8-digit hex, spaced var() with balanced parentheses, and anchored rgb()/rgba()/hsl()/hsla().
		 * @param  mixed $color setting input.
		 * @return string       setting input value.
		 */
		public static function sanitize_alpha_color( $color ) {

			if ( ! is_string( $color ) || '' === $color ) {
				return '';
			}

			$color = trim( $color );

			// Keywords such as transparent/inherit must round-trip.
			if ( self::is_css_color_keyword( $color ) ) {
				return $color;
			}

			// CSS variable value sanitize, including the spaced "var( --x )" form.
			if ( 0 === stripos( $color, 'var(' ) ) {
				// phpcs:ignore Generic.PHP.ForbiddenFunctions.FoundWithAlternative -- Safe usage: no /e modifier, strips anything outside the CSS var() grammar.
				$color = preg_replace( '/[^A-Za-z0-9_)(#\-,. ]/', '', $color );

				// An unclosed "(" would swallow every rule after it.
				return preg_match( '/^var\((?:[^()]|\((?:[^()]|\([^()]*\))*\))*\)$/iD', $color ) ? $color : '';
			}

			// Accept a single well-formed rgb()/rgba()/hsl()/hsla() value as-is.
			if ( preg_match( '/^(?:rgba?|hsla?)\( *[0-9.\-][0-9a-z,. %\/\-]*\)$/iD', $color ) ) {
				return $color;
			}

			if ( false === strpos( $color, 'rgba' ) ) {
				/* Hex sanitize */
				return self::sanitize_hex_color( $color );
			}

			/* rgba sanitize */
			$color = str_replace( ' ', '', $color );
			if ( 4 !== sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha ) || ! is_finite( (float) $alpha ) ) {
				return '';
			}
			return 'rgba(' . $red . ',' . $green . ',' . $blue . ',' . $alpha . ')';
		}

		/**
		 * Whether a value is a CSS-wide or color keyword that must be preserved.
		 *
		 * @since 4.14.2
		 * @param string $value Color value.
		 * @return bool
		 */
		private static function is_css_color_keyword( $value ) {
			$keywords = array( 'transparent', 'inherit', 'initial', 'unset', 'revert', 'currentcolor' );

			return in_array( strtolower( $value ), $keywords, true );
		}

		/**
		 * Sanitize html
		 *
		 * @param  string $input    setting input.
		 * @return mixed            setting input value.
		 */
		public static function sanitize_html( $input ) {
			return wp_kses_post( $input );
		}

		/**
		 * Sanitize Header/Footer Builder HTML widget content.
		 *
		 * Authors holding unfiltered_html keep raw markup; everyone else loses <iframe>.
		 *
		 * @param  mixed $input setting input.
		 * @return string       sanitized setting input value.
		 * @since 4.13.11
		 */
		public static function sanitize_html_widget( $input ) {
			if ( ! is_string( $input ) ) {
				return '';
			}

			// The gate core applies to post content: unfiltered_html or kses, with no fallback.
			if ( current_user_can( 'unfiltered_html' ) ) {
				return $input;
			}

			return wp_kses( $input, astra_get_html_widget_allowed_tags( false, $input ) );
		}

		/**
		 * Sanitize the post meta Divider Type.
		 *
		 * Value is echoed into the post meta markup, so hold it to the control's own choices.
		 *
		 * @param  mixed $input setting input.
		 * @return string       a Divider Type choice, or the default separator.
		 * @since 4.13.11
		 */
		public static function sanitize_meta_separator( $input ) {
			return in_array( $input, array( '/', '-', '|', '•', 'none' ), true ) ? $input : '/';
		}

		/**
		 * Sanitize Select choices
		 *
		 * @param  string $input    setting input.
		 * @param  object $setting  setting object.
		 * @return mixed            setting input value.
		 */
		public static function sanitize_multi_choices( $input, $setting ) {

			// Get list of choices from the control
			// associated with the setting.
			$choices = $setting->manager->get_control( $setting->id )->choices;

			$input_keys = $input;

			foreach ( $input_keys as $key => $value ) {
				if ( ! array_key_exists( $value, $choices ) ) {
					unset( $input[ $key ] );
				}
			}

			// If the input is a valid key, return it;
			// otherwise, return the default.
			return is_array( $input ) ? $input : $setting->default;
		}

		/**
		 * Sanitize Select choices
		 *
		 * @param  string $input    setting input.
		 * @param  object $setting  setting object.
		 * @return mixed            setting input value.
		 */
		public static function sanitize_choices( $input, $setting ) {

			// Ensure input is a slug.
			$input = sanitize_key( $input );

			// Get list of choices from the control
			// associated with the setting.
			$choices = $setting->manager->get_control( $setting->id )->choices;

			// If the input is a valid key, return it;
			// otherwise, return the default.
			return array_key_exists( $input, $choices ) ? $input : $setting->default;
		}

		/**
		 * Sanitize Font weight
		 *
		 * @param  mixed $input setting input.
		 * @return mixed        setting input value.
		 */
		public static function sanitize_font_weight( $input ) {

			$valid = array(
				'inherit',
				'normal',
				'bold',
				'100',
				'200',
				'300',
				'400',
				'500',
				'600',
				'700',
				'800',
				'900',
			);

			if ( in_array( $input, $valid ) ) {
				return $input;
			}

			return 'normal';
		}

		/**
		 * Sanitize Font variant
		 *
		 * @param  mixed $input setting input.
		 * @return mixed        setting input value.
		 */
		public static function sanitize_font_variant( $input ) {

			if ( is_array( $input ) ) {
				$input = implode( ',', $input );
			}
			return sanitize_text_field( $input );
		}

		/**
		 * Sanitize a CSS property value for safe inline style output.
		 *
		 * @since 4.12.5
		 * @see astra_sanitize_css_value()
		 * @param  string $value Raw CSS property value.
		 * @return string        Sanitized value safe for inline CSS context.
		 */
		public static function sanitize_css_value( $value ) {
			return astra_sanitize_css_value( sanitize_text_field( $value ) );
		}

		/**
		 * Sanitize Background Obj
		 *
		 * @param  mixed $bg_obj setting input.
		 * @return array        setting input value.
		 */
		public static function sanitize_background_obj( $bg_obj ) {

			$out_bg_obj = array(
				'background-color'      => '',
				'background-image'      => '',
				'background-repeat'     => 'repeat',
				'background-position'   => 'center center',
				'background-size'       => 'auto',
				'background-attachment' => 'scroll',
				'overlay-type'          => '',
				'overlay-color'         => '',
				'overlay-opacity'       => '',
				'overlay-gradient'      => '',
				'background-media'      => '',
				'background-type'       => '',
			);

			if ( is_array( $bg_obj ) ) {

				foreach ( $out_bg_obj as $key => $value ) {

					if ( isset( $bg_obj[ $key ] ) ) {

						if ( 'background-image' === $key ) {
							$out_bg_obj[ $key ] = esc_url_raw( $bg_obj[ $key ] );
						} elseif ( 'overlay-gradient' === $key ) {
							$out_bg_obj[ $key ] = self::sanitize_css_value( $bg_obj[ $key ] );
						} else {
							/** @psalm-suppress PossiblyUndefinedStringArrayOffset -- Key existence confirmed by isset check above. */
							$out_bg_obj[ $key ] = esc_attr( $bg_obj[ $key ] );
						}
					}
				}
			}

			return $out_bg_obj;
		}

		/**
		 * Sanitize Border Typography
		 *
		 * @since 1.4.0
		 * @param  array|number $val Customizer setting input number.
		 * @return array        Return number.
		 */
		public static function sanitize_border( $val ) {

			$border = array(
				'top'    => '',
				'right'  => '',
				'bottom' => '',
				'left'   => '',
			);
			/** @psalm-suppress DocblockTypeContradiction */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( is_array( $val ) ) {
				$border['top']    = is_numeric( $val['top'] ) ? $val['top'] : '';
				$border['right']  = is_numeric( $val['right'] ) ? $val['right'] : '';
				$border['bottom'] = is_numeric( $val['bottom'] ) ? $val['bottom'] : '';
				$border['left']   = is_numeric( $val['left'] ) ? $val['left'] : '';
			}
			return $border;
		}

		/**
		 * Sanitize Customizer Link param.
		 *
		 * @param mixed $val array(
		 *      linked : Linked Customizer Section,
		 *      link_text : Link Text.
		 * ).
		 *
		 * @since 1.6.0
		 *
		 * @return Array
		 */
		public static function sanitize_customizer_links( $val ) {
			if ( ! is_array( $val ) ) {
				return array();
			}

			$val['linked']         = sanitize_text_field( isset( $val['linked'] ) ? $val['linked'] : '' );
			$val['link_text']      = esc_html( isset( $val['link_text'] ) ? $val['link_text'] : '' );
			$val['link_type']      = esc_html( isset( $val['link_type'] ) ? $val['link_type'] : '' );
			$val['is_button_link'] = esc_html( isset( $val['is_button_link'] ) ? $val['is_button_link'] : '#' );
			return $val;
		}

		/**
		 * Sanitize Responsive Background Image
		 *
		 * @param  array $bg_obj Background object.
		 * @return array         Background object.
		 */
		public static function sanitize_responsive_background( $bg_obj ) {

			// Default Responsive Background Image.
			$defaults = array(
				'desktop' => array(
					'background-color'      => '',
					'background-image'      => '',
					'background-repeat'     => 'repeat',
					'background-position'   => 'center center',
					'background-size'       => 'auto',
					'background-attachment' => 'scroll',
					'overlay-type'          => '',
					'overlay-color'         => '',
					'overlay-opacity'       => '',
					'overlay-gradient'      => '',
					'background-media'      => '',
					'background-type'       => '',
				),
				'tablet'  => array(
					'background-color'      => '',
					'background-image'      => '',
					'background-repeat'     => 'repeat',
					'background-position'   => 'center center',
					'background-size'       => 'auto',
					'background-attachment' => 'scroll',
					'overlay-type'          => '',
					'overlay-color'         => '',
					'overlay-opacity'       => '',
					'overlay-gradient'      => '',
					'background-media'      => '',
					'background-type'       => '',
				),
				'mobile'  => array(
					'background-color'      => '',
					'background-image'      => '',
					'background-repeat'     => 'repeat',
					'background-position'   => 'center center',
					'background-size'       => 'auto',
					'background-attachment' => 'scroll',
					'overlay-type'          => '',
					'overlay-color'         => '',
					'overlay-opacity'       => '',
					'overlay-gradient'      => '',
					'background-media'      => '',
					'background-type'       => '',
				),
			);

			// Merge responsive background object and default object into $out_bg_obj array.
			$out_bg_obj = wp_parse_args( $bg_obj, $defaults );

			foreach ( $out_bg_obj as $device => $bg ) {
				foreach ( $bg as $key => $value ) {
					if ( 'background-image' === $key ) {
						$out_bg_obj[ $device ] [ $key ] = esc_url_raw( $value );
					} elseif ( 'overlay-gradient' === $key ) {
						$out_bg_obj[ $device ] [ $key ] = self::sanitize_css_value( $value );
					} elseif ( 'background-media' === $key ) {
						$out_bg_obj[ $device ] [ $key ] = floatval( $value );
					} else {
						$out_bg_obj[ $device ] [ $key ] = esc_attr( $value );
					}
				}
			}
			return $out_bg_obj;
		}

		/**
		 * Sanitize Toggle Control param.
		 *
		 * @param bool $val for True|False.
		 *
		 * @since 3.1.0
		 *
		 * @return bool True|False
		 */
		public static function sanitize_toggle_control( $val ) {
			// returns true if checkbox is checked.
			return isset( $val ) && is_bool( $val ) ? $val : '';
		}

		/**
		 * Sanitize Font Extras composite value.
		 *
		 * Whitelists unit fields and sanitizes numeric values for line-height
		 * and letter-spacing. Preserves '' (unitless ratio) as a valid unit for
		 * line-height. Non-array input is discarded and an empty array is returned.
		 *
		 * @since 4.13.2
		 * @param  mixed $input Raw value from the customizer setting.
		 * @return array        Sanitized font-extras array.
		 */
		public static function sanitize_font_extras( $input ) {
			if ( ! is_array( $input ) ) {
				return array();
			}

			$valid_lh_units    = array( '', 'px', 'em', 'rem' );
			$valid_ls_units    = array( 'px', 'em', 'rem' );
			$valid_decorations = array( 'initial', 'underline', 'line-through', '' );
			$valid_transforms  = array( 'lowercase', 'capitalize', 'uppercase', '' );

			$out = array();

			if ( isset( $input['line-height'] ) ) {
				if ( '' === $input['line-height'] ) {
					$out['line-height'] = '';
				} else {
					$lh                 = floatval( $input['line-height'] );
					$out['line-height'] = is_numeric( $input['line-height'] ) && is_finite( $lh ) ? (string) $lh : '';
				}
			}
			if ( isset( $input['line-height-unit'] ) ) {
				$out['line-height-unit'] = in_array( $input['line-height-unit'], $valid_lh_units, true ) ? $input['line-height-unit'] : '';
			}
			if ( isset( $input['letter-spacing'] ) ) {
				if ( '' === $input['letter-spacing'] ) {
					$out['letter-spacing'] = '';
				} else {
					$ls                    = floatval( $input['letter-spacing'] );
					$out['letter-spacing'] = is_numeric( $input['letter-spacing'] ) && is_finite( $ls ) ? (string) $ls : '';
				}
			}
			if ( isset( $input['letter-spacing-unit'] ) ) {
				$out['letter-spacing-unit'] = in_array( $input['letter-spacing-unit'], $valid_ls_units, true ) ? $input['letter-spacing-unit'] : '';
			}
			if ( isset( $input['text-decoration'] ) ) {
				$out['text-decoration'] = in_array( $input['text-decoration'], $valid_decorations, true ) ? $input['text-decoration'] : 'initial';
			}
			if ( isset( $input['text-transform'] ) ) {
				$out['text-transform'] = in_array( $input['text-transform'], $valid_transforms, true ) ? $input['text-transform'] : '';
			}

			return $out;
		}

		/**
		 * Sanitize a single palette colors array.
		 *
		 * Caps the number of slots at the 9 theme slots plus the custom colors limit and
		 * runs every color through the alpha color sanitizer.
		 *
		 * @param  mixed $palette Palette colors input.
		 * @return array Sanitized palette colors.
		 * @since 4.14.0
		 */
		private static function sanitize_palette_colors( $palette ) {
			if ( ! is_array( $palette ) ) {
				return array();
			}

			$max_slots = 9 + Astra_Global_Palette::get_custom_colors_limit();
			$colors    = array();

			foreach ( array_values( $palette ) as $index => $color ) {
				if ( $index >= $max_slots ) {
					break;
				}
				$colors[] = is_string( $color ) && strlen( $color ) <= 100 ? self::sanitize_palette_color( $color ) : '';
			}

			return $colors;
		}

		/**
		 * Sanitize a single global palette color value.
		 *
		 * Stored palettes predate this callback, so every CSS color form must round-trip:
		 * hex in any length, color functions in comma or space syntax, keywords, and var()
		 * references including fallbacks.
		 *
		 * @since 4.14.0
		 * @param string $color Color value to sanitize.
		 * @return string Sanitized color, or an empty string when the value is not a safe CSS color.
		 */
		public static function sanitize_palette_color( $color ) {
			$color = trim( $color );

			if ( '' === $color ) {
				return '';
			}

			if ( 0 === strpos( $color, 'var(--' ) ) {
				// phpcs:ignore Generic.PHP.ForbiddenFunctions.FoundWithAlternative -- Safe usage: strips everything outside the var() token charset.
				return preg_replace( '/[^A-Za-z0-9_)(#\-,.]/', '', $color );
			}

			if ( preg_match( '/^#(?:[0-9a-fA-F]{3,4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $color ) ) {
				return $color;
			}

			// The character set cannot close the declaration or open another CSS context.
			if ( preg_match( '/^(?:rgb|rgba|hsl|hsla|hwb|lab|lch|oklab|oklch|color|color-mix)\(\s*[a-zA-Z0-9,.\s%\/\-]*\)$/i', $color ) ) {
				return $color;
			}

			// Keywords - transparent, currentColor, named colors.
			if ( preg_match( '/^[a-zA-Z]{1,25}$/', $color ) ) {
				return $color;
			}

			return '';
		}

		/**
		 * Sanitize the global color palette setting ( astra-settings[global-color-palette] ).
		 *
		 * @param  mixed $input Setting input.
		 * @return array Sanitized setting value.
		 * @since 4.14.0
		 */
		public static function sanitize_global_color_palette( $input ) {
			if ( ! is_array( $input ) ) {
				return array();
			}

			$out = array(
				'palette' => isset( $input['palette'] ) ? self::sanitize_palette_colors( $input['palette'] ) : array(),
			);

			// Preserved as-is - used by the customizer JS to force-refresh the setting.
			if ( isset( $input['flag'] ) ) {
				$out['flag'] = (bool) $input['flag'];
			}

			return $out;
		}

		/**
		 * Sanitize the color palettes setting ( astra-color-palettes ).
		 *
		 * @param  mixed $input Setting input.
		 * @return array Sanitized setting value.
		 * @since 4.14.0
		 */
		public static function sanitize_color_palettes( $input ) {
			if ( ! is_array( $input ) ) {
				return array();
			}

			$out = array();

			$out['currentPalette'] = isset( $input['currentPalette'] ) && is_string( $input['currentPalette'] ) && preg_match( '/^palette_\d{1,3}\z/', $input['currentPalette'] ) ? $input['currentPalette'] : 'palette_1';

			$out['palettes'] = array();
			if ( isset( $input['palettes'] ) && is_array( $input['palettes'] ) ) {
				foreach ( $input['palettes'] as $palette_key => $palette ) {
					// Palette keys follow the palette_N shape; the entry cap keeps a crafted
					// payload from persisting an oversized autoloaded option.
					if ( ! is_string( $palette_key ) || ! preg_match( '/^palette_\d{1,3}\z/', $palette_key ) || count( $out['palettes'] ) >= 10 ) {
						continue;
					}
					$out['palettes'][ $palette_key ] = self::sanitize_palette_colors( $palette );
				}
			}

			if ( isset( $input['presetNames'] ) && is_array( $input['presetNames'] ) ) {
				$out['presetNames'] = array();
				foreach ( $input['presetNames'] as $palette_key => $preset_name ) {
					if ( ! is_string( $palette_key ) || ! preg_match( '/^palette_\d{1,3}\z/', $palette_key ) || count( $out['presetNames'] ) >= 10 ) {
						continue;
					}
					$out['presetNames'][ $palette_key ] = is_string( $preset_name ) ? substr( sanitize_text_field( $preset_name ), 0, 100 ) : '';
				}
			}

			if ( isset( $input['presets'] ) && is_array( $input['presets'] ) ) {
				$out['presets'] = array();
				foreach ( $input['presets'] as $preset_key => $preset ) {
					if ( count( $out['presets'] ) >= 20 ) {
						break;
					}
					$out['presets'][ substr( sanitize_text_field( (string) $preset_key ), 0, 100 ) ] = self::sanitize_palette_colors( $preset );
				}
			}

			if ( isset( $input['customColors'] ) && is_array( $input['customColors'] ) ) {
				$out['customColors'] = array();
				$custom_colors_limit = Astra_Global_Palette::get_custom_colors_limit();

				foreach ( array_values( $input['customColors'] ) as $index => $custom_color ) {
					if ( $index >= $custom_colors_limit ) {
						break;
					}
					$custom_color_name = isset( $custom_color['name'] ) && is_string( $custom_color['name'] ) ? substr( sanitize_text_field( $custom_color['name'] ), 0, 100 ) : '';

					// A name saved empty would render blank tooltips and labels - keep the slot's default name.
					if ( '' === trim( $custom_color_name ) ) {
						/* translators: %d: custom color number. */
						$custom_color_name = sprintf( __( 'Custom %d', 'astra' ), $index + 1 );
					}

					$out['customColors'][] = array(
						'name'    => $custom_color_name,
						'retired' => ! empty( $custom_color['retired'] ),
					);
				}
			}

			// Preserved as-is - used by the customizer JS to force-refresh the setting.
			if ( isset( $input['flag'] ) ) {
				$out['flag'] = (bool) $input['flag'];
			}

			return $out;
		}
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Astra_Customizer_Sanitizes::get_instance();
