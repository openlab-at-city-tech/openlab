<?php

namespace LottaFramework;

use LottaFramework\Typography\Fonts;

class Css {

	/**
	 * Css initial value
	 */
	const INITIAL_VALUE = '__INITIAL_VALUE__';

	/**
	 * Breakpoints for media query
	 *
	 * @var array|mixed
	 */
	protected $breakpoints;

	/**
	 * @param array $breakpoints
	 */
	public function __construct( array $breakpoints = [] ) {
		$this->setBreakpoints( $breakpoints );
	}

	/**
	 * Set responsive breakpoints
	 *
	 * @param array $breakpoints
	 */
	public function setBreakpoints( $breakpoints = [] ) {
		$this->breakpoints = wp_parse_args( $breakpoints, [
			'desktop' => '1140px',
			'tablet'  => '1024px',
			'mobile'  => '768px',
		] );
	}

	public function desktop() {
		return $this->breakpoints['desktop'] ?? '';
	}

	public function tablet() {
		return $this->breakpoints['tablet'] ?? '';
	}

	public function mobile() {
		return $this->breakpoints['mobile'] ?? '';
	}

	/**
	 * Parse css output
	 *
	 * @param array $css_output
	 * @param bool $beauty
	 *
	 * @return string Generated CSS.
	 */
	public function parse( $css_output = [], $beauty = false ) {

		$parse_css     = '';
		$tablet_output = [];
		$mobile_output = [];
		$eol           = $beauty ? PHP_EOL : '';

		if ( ! is_array( $css_output ) || count( $css_output ) <= 0 ) {
			return $parse_css;
		}

		foreach ( $css_output as $selector => $properties ) {

			if ( null === $properties ) {
				break;
			}

			if ( ! count( $properties ) ) {
				continue;
			}

			$temp_parse_css     = $selector . '{' . $eol;
			$temp_tablet_output = [];
			$temp_mobile_output = [];
			$properties_added   = 0;

			foreach ( $properties as $property => $value ) {

				// responsive value
				if ( is_array( $value ) ) {
					$temp_tablet_output[ $property ] = $value['tablet'] ?? '';
					$temp_mobile_output[ $property ] = $value['mobile'] ?? '';

					$value = $value['desktop'] ?? '';
				}

				if ( '' === $value || null === $value || self::INITIAL_VALUE === $value ) {
					continue;
				}

				$properties_added ++;

				$temp_parse_css .= $property . ':' . $value . ';' . $eol;
			}

			$temp_parse_css .= '}';

			if ( ! empty( $temp_tablet_output ) ) {
				$tablet_output[ $selector ] = $temp_tablet_output;
			}

			if ( ! empty( $temp_mobile_output ) ) {
				$mobile_output[ $selector ] = $temp_mobile_output;
			}

			if ( $properties_added > 0 ) {
				$parse_css .= $temp_parse_css;
			}
		}

		$tablet_css = $this->parse( $tablet_output, $beauty );
		if ( $tablet_css !== '' && isset( $this->breakpoints['tablet'] ) ) {
			$tablet_css = '@media (max-width: ' . $this->breakpoints['tablet'] . ') {' . $eol . $tablet_css . $eol . '}' . $eol;
		}

		$mobile_css = $this->parse( $mobile_output, $beauty );
		if ( $mobile_css !== '' && isset( $this->breakpoints['desktop'] ) ) {
			$mobile_css = '@media (max-width: ' . $this->breakpoints['mobile'] . ') {' . $eol . $mobile_css . $eol . '}' . $eol;
		}

		return $parse_css . $tablet_css . $mobile_css;
	}

	/**
	 * Generate css font faces
	 *
	 * @param array $font_faces_input
	 * @param false $beauty
	 *
	 * @return string
	 */
	public function fontFaces( $font_faces_input = [], $beauty = false ) {
		$parse_css = '';
		$eol       = $beauty ? PHP_EOL : '';

		foreach ( $font_faces_input as $args ) {
			$parse_css .= '@font-face {' . $eol;

			foreach ( $args as $k => $v ) {
				if ( $k === 'src' ) {
					foreach ( $v as $src ) {
						if ( strstr( $src, '.otf' ) ) {
							$parse_css .= 'src:' . 'url("' . $src . '")' . ' format("opentype");' . $eol;
						} else if ( strstr( $src, '.ttf' ) ) {
							$parse_css .= 'src:' . 'url("' . $src . '")' . ' format("truetype");' . $eol;
						} else if ( strstr( $src, '.woff2' ) ) {
							$parse_css .= 'src:' . 'url("' . $src . '")' . ' format("woff2");' . $eol;
						} else if ( strstr( $src, '.woff' ) ) {
							$parse_css .= 'src:' . 'url("' . $src . '")' . ' format("woff");' . $eol;
						}
					}
				} else {
					$parse_css .= $k . ':' . $v . ';' . $eol;
				}
			}

			$parse_css .= '}' . $eol;
		}

		return $parse_css;
	}

	/**
	 * Generate css keyframes
	 *
	 * @param array $keyframes_output
	 * @param false $beauty
	 *
	 * @return string
	 */
	public function keyframes( $keyframes_output = [], $beauty = false ) {
		$parse_css = '';
		$eol       = $beauty ? PHP_EOL : '';

		foreach ( $keyframes_output as $name => $breakpoints ) {
			$parse_css .= "@keyframes $name {" . $eol;

			foreach ( $breakpoints as $breakpoint => $properties ) {
				$parse_css .= "$breakpoint {" . $eol;

				foreach ( $properties as $property => $value ) {
					$parse_css .= $property . ':' . $value . ';' . $eol;
				}

				$parse_css .= '}' . $eol;
			}

			$parse_css .= '}' . $eol;
		}

		return $parse_css;
	}

	/**
	 * Convert spacing control value to css output
	 *
	 * @param mixed $value
	 * @param string $selector
	 *
	 * @return array
	 */
	public function dimensions( $value, $selector = 'margin' ) {
		if ( $value === self::INITIAL_VALUE || $value === null ) {
			return array();
		}

		if ( ! isset( $value['desktop'] ) ) {
			$value = [ null => $value ];
		}

		$spacingCss = [];

		foreach ( $value as $device => $data ) {
			$top    = $data['top'] ?? '';
			$right  = $data['right'] ?? '';
			$bottom = $data['bottom'] ?? '';
			$left   = $data['left'] ?? '';

			if ( $top === '' || $right === '' || $bottom === '' || $left === '' ) {
				continue;
			}

			$spacingCss[ $selector ] = $this->getResponsiveValue(
				"$top $right $bottom $left", $device, $spacingCss[ $selector ] ?? null
			);
		}

		return $spacingCss;
	}

	/**
	 * Get value for responsive
	 *
	 * @param $value
	 * @param null $device
	 * @param null $previous
	 *
	 * @return array|mixed|null
	 */
	protected function getResponsiveValue( $value, $device = null, $previous = null ) {

		if ( ! $device ) {
			return $value;
		}

		$value = [
			$device => $value
		];

		return is_array( $previous ) ? array_merge( $previous, $value ) : $value;
	}

	/**
	 * Convert background control value to css output
	 *
	 * @param array $background
	 *
	 * @return array
	 */
	public function background( $background ) {
		if ( $background === self::INITIAL_VALUE || $background === null ) {
			return [];
		}

		if ( ! isset( $background['desktop'] ) ) {
			$background = [ null => $background ];
		}

		$backgroundCss = [];

		foreach ( $background as $device => $data ) {
			if ( $data === self::INITIAL_VALUE || $data === null ) {
				continue;
			}

			if ( $data['type'] === 'color' ) {
				if ( ! ( $data['color'] ?? '' ) || ( $data['color'] ?? '' ) === 'inherit' || ( $data['color'] ?? '' ) === self::INITIAL_VALUE ) {
					continue;
				}

				// solid color type
				$backgroundCss['background-color'] = $this->getResponsiveValue(
					$data['color'] ?? '', $device,
					$backgroundCss['background-color'] ?? null
				);
				// override background image
				$backgroundCss['background-image'] = $this->getResponsiveValue(
					'none', $device,
					$backgroundCss['background-image'] ?? null
				);
			} else if ( $data['type'] === 'gradient' ) {
				// gradient type
				$backgroundCss['background-image'] = $this->getResponsiveValue(
					$data['gradient'] ?? '', $device,
					$backgroundCss['background-image'] ?? null
				);
			} else if ( $data['type'] === 'image' ) {
				// background image
				$image = $data['image'] ?? [];

				if ( isset( $image['color'] ) ) {
					$backgroundCss['background-color'] = $this->getResponsiveValue(
						$image['color'], $device, $backgroundCss['background-color'] ?? null
					);
				}
				if ( isset( $image['size'] ) ) {
					$backgroundCss['background-size'] = $this->getResponsiveValue(
						$image['size'], $device, $backgroundCss['background-size'] ?? null
					);
				}
				if ( isset( $image['repeat'] ) ) {
					$backgroundCss['background-repeat'] = $this->getResponsiveValue(
						$image['repeat'], $device, $backgroundCss['background-repeat'] ?? null
					);
				}
				if ( isset( $image['attachment'] ) ) {
					$backgroundCss['background-attachment'] = $this->getResponsiveValue(
						$image['attachment'], $device, $backgroundCss['background-attachment'] ?? null
					);
				}

				if ( isset( $image['source'] ) && isset( $image['source']['url'] ) ) {

					$backgroundCss['background-image'] = $this->getResponsiveValue(
						'url(' . $image['source']['url'] . ')', $device,
						$backgroundCss['background-image'] ?? null
					);

					if ( isset( $image['source']['x'] ) && isset( $image['source']['y'] ) ) {
						$x = $image['source']['x'] * 100;
						$y = $image['source']['y'] * 100;

						$backgroundCss['background-position'] = $this->getResponsiveValue(
							"$x% $y%", $device, $backgroundCss['background-position'] ?? null
						);
					}
				}
			}
		}

		return $backgroundCss;
	}

	/**
	 * Convert border control to css output
	 *
	 * @param $selector
	 * @param array $border
	 *
	 * @return array
	 */
	public function border( $border, $selector = 'border' ) {
		if ( $border === null || $border === self::INITIAL_VALUE ) {
			return array();
		}

		if ( ! isset( $border['desktop'] ) ) {
			$border = [ null => $border ];
		}

		$borderCss = [];

		foreach ( $border as $device => $data ) {
			$value = 'none';
			$style = $data['style'] ?? '';
			$width = ( $data['width'] ?? '0' ) . 'px';
			$color = $data['color'] ?? '';
			$hover = $data['hover'] ?? '';

			if ( ( $data['inherit'] ?? false ) || $style === '' || $style === self::INITIAL_VALUE ) {
				continue;
			}

			if ( $style !== 'none' ) {
				$value = "$width $style var(--lotta-border-$selector-initial-color)";
			}

			$borderCss[ $selector ] = $this->getResponsiveValue(
				$value, $device, $borderCss[ $selector ] ?? null
			);

			if ( $color !== self::INITIAL_VALUE ) {
				$borderCss['--lotta-border-initial-color'] = $this->getResponsiveValue(
					$color, $device, $borderCss['--lotta-border-initial-color'] ?? null
				);

				$borderCss["--lotta-border-$selector-initial-color"] = $this->getResponsiveValue(
					$color, $device, $borderCss["--lotta-border-$selector-initial-color"] ?? null
				);
			}

			if ( $hover !== self::INITIAL_VALUE ) {
				$borderCss['--lotta-border-hover-color'] = $this->getResponsiveValue(
					$hover, $device, $borderCss['--lotta-border-hover-color'] ?? null
				);

				$borderCss["--lotta-border-$selector-hover-color"] = $this->getResponsiveValue(
					$hover, $device, $borderCss["--lotta-border-$selector-hover-color"] ?? null
				);
			}
		}

		return $borderCss;
	}

	/**
	 * Convert shadow control value to css output
	 *
	 * @param mixed $shadow
	 * @param string $selector
	 *
	 * @return array
	 */
	public function shadow( $shadow, $selector = 'box-shadow' ) {

		if ( $shadow === null || $shadow === self::INITIAL_VALUE ) {
			return array();
		}

		if ( ! isset( $shadow['desktop'] ) ) {
			$shadow = [ null => $shadow ];
		}

		$shadowCss = [];

		foreach ( $shadow as $device => $data ) {
			if ( $data === null || $data === self::INITIAL_VALUE ) {
				continue;
			}

			$value  = 'none';
			$enable = ( $data['enable'] ?? '' ) === 'yes';
			$h      = $data['horizontal'] ?? '0';
			$v      = $data['vertical'] ?? '0';
			$blur   = $data['blur'] ?? '0';
			$spread = $data['spread'] ?? '0';
			$color  = $data['color'] ?? '';

			if ( $enable ) {
				$value = "$color $h $v $blur $spread";
			}

			$shadowCss[ $selector ] = $this->getResponsiveValue(
				$value, $device, $shadowCss[ $selector ] ?? null
			);
		}

		return $shadowCss;
	}

	/**
	 * Convert filters control value to css output
	 *
	 * @param mixed $filter
	 *
	 * @return array
	 */
	public function filters( $filter ) {

		if ( $filter === null ) {
			return array();
		}

		if ( ! isset( $filter['desktop'] ) ) {
			$filter = [ null => $filter ];
		}

		$filterCss = [];

		foreach ( $filter as $device => $data ) {
			$value      = null;
			$enable     = ( $data['enable'] ?? '' ) === 'yes';
			$blur       = $data['blur'] ?? 0;
			$contrast   = $data['contrast'] ?? 100;
			$brightness = $data['brightness'] ?? 100;
			$saturate   = $data['saturate'] ?? 100;
			$hue        = $data['hue'] ?? 0;

			if ( $enable ) {
				$value = "brightness( {$brightness}% ) contrast( {$contrast}% ) saturate( {$saturate}% ) blur( {$blur}px ) hue-rotate( {$hue}deg )";
			}

			$filterCss['filter'] = $this->getResponsiveValue(
				$value, $device, $filterCss['filter'] ?? null
			);
		}

		return $filterCss;
	}

	/**
	 * Convert typography control value to css output
	 *
	 * @param array $typography
	 *
	 * @return array
	 */
	public function typography( $typography ) {
		if ( $typography === null || $typography === self::INITIAL_VALUE ) {
			return array();
		}

		$custom = Fonts::custom_fonts();
		$system = Fonts::system_fonts();
		$google = Fonts::google_fonts();

		$family        = $typography['family'] ?? 'inherit';
		$variant       = $typography['variant'] ?? '400';
		$fontSize      = $typography['fontSize'] ?? '';
		$lineHeight    = $typography['lineHeight'] ?? '';
		$letterSpacing = $typography['letterSpacing'] ?? '';

		if ( isset( $system[ $family ] ) ) {
			if ( isset( $system[ $family ]['s'] ) && ! empty( $system[ $family ]['s'] ) ) {
				$family = $system[ $family ]['s'];
			}
		}

		if ( isset( $google[ $family ] ) ) {
			$variants = $google[ $family ]['v'] ?? [];
			$family   = $google[ $family ]['f'] ?? $family;
			$variant  = in_array( $variant, $variants ) ? $variant : ( $variants[0] ?? '400' );
		}

		if ( isset( $custom[ $family ] ) ) {
			$variant = $custom[ $family ]['v'] ?? '400';

			if ( isset( $custom[ $family ]['s'] ) && ! empty( $custom[ $family ]['s'] ) ) {
				$family = $custom[ $family ]['f'] . ',' . $custom[ $family ]['s'];
			} else {
				$family = $custom[ $family ]['f'];
			}
		}

		$variant       = $variant === self::INITIAL_VALUE ? '' : $variant;
		$family        = $family === self::INITIAL_VALUE ? '' : $family;
		$fontSize      = $fontSize === self::INITIAL_VALUE ? '' : $fontSize;
		$lineHeight    = $lineHeight === self::INITIAL_VALUE ? '' : $lineHeight;
		$letterSpacing = $letterSpacing === self::INITIAL_VALUE ? '' : $letterSpacing;

		return [
			'font-family'     => $family,
			'font-weight'     => $variant,
			'font-size'       => $fontSize,
			'line-height'     => $lineHeight,
			'letter-spacing'  => $letterSpacing,
			'text-transform'  => $typography['textTransform'] ?? '',
			'text-decoration' => $typography['textDecoration'] ?? '',
		];
	}

	/**
	 * Convert color control value to css output
	 *
	 * @param $colors
	 * @param $maps
	 * @param array $css
	 *
	 * @return array
	 */
	public function colors( $colors, $maps, $css = [] ) {

		foreach ( $maps as $color => $key ) {
			if ( isset( $colors[ $color ] ) && $colors[ $color ] !== self::INITIAL_VALUE ) {
				if ( ! is_array( $key ) ) {
					$key = [ $key ];
				}

				foreach ( $key as $item ) {
					$css[ $item ] = $colors[ $color ];
				}
			}
		}

		return $css;
	}
}