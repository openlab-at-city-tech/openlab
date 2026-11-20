<?php
/**
 * Kadence\FSE\Button_Styles class
 *
 * @package kadence
 */

namespace Kadence\FSE;

use Kadence\Kadence_CSS;
use WP_Theme_JSON;
use WP_Theme_JSON_Data;
use function Kadence\kadence;

/**
 * Gives the Site Editor the Customizer's Base, Secondary and Outline buttons in Full Site
 * Editing mode.
 *
 * Base goes to the button element, and Secondary and Outline to styles of the Button block.
 * Core keeps a block style's global styles only while the style is registered, so Secondary
 * is registered here. Outline is core's own.
 *
 * Each gets the values Kadence's button CSS renders today, so the Styles panel opens on the
 * site's look. Base takes the stylesheet's defaults where the Customizer has none. A Secondary
 * or Outline value the Customizer leaves empty isn't written, so it falls back to Base as it
 * does today, except where core's Outline sets its own.
 *
 * Core gives the button element no tablet or mobile styles, so Base feeds its desktop values
 * only. Kadence's tablet styles also cover phones, so a style's mobile values fall back to its
 * tablet ones.
 *
 * @since 1.6.0
 */
final class Button_Styles {

	/**
	 * Base values from `assets/css/src/_buttons.scss`, for the settings the Customizer leaves empty.
	 *
	 * @since 1.6.0
	 */
	private const BASE_DEFAULTS = [
		'border'     => [
			'width'  => '0',
			'radius' => '3px',
		],
		'spacing'    => [
			'padding' => [
				'top'    => '0.4em',
				'right'  => '1em',
				'bottom' => '0.4em',
				'left'   => '1em',
			],
		],
		'typography' => [
			'fontSize'   => '1.125rem',
			'lineHeight' => '1.6',
		],
		'shadow'     => '0px 0px 0px -7px rgba(0, 0, 0, 0)',
		':hover'     => [
			'shadow' => '0px 15px 25px -7px rgba(0, 0, 0, 0.1)',
		],
	];

	/**
	 * Outline's border and hover shadow from `assets/css/src/_buttons.scss`, which replace core's.
	 *
	 * @since 1.6.0
	 */
	private const OUTLINE_DEFAULTS = [
		'border' => [
			'width' => '2px',
			'style' => 'solid',
			'color' => 'currentColor',
		],
		':hover' => [
			'shadow' => '0px 0px 0px -7px rgba(0, 0, 0, 0)',
		],
	];

	/**
	 * The style feed, which turns Customizer colors into global styles values.
	 *
	 * @since 1.6.0
	 *
	 * @var Style_Feed
	 */
	private Style_Feed $style_feed;

	/**
	 * Kadence's CSS helpers.
	 *
	 * @since 1.6.0
	 *
	 * @var Kadence_CSS
	 */
	private Kadence_CSS $css;

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param Style_Feed $style_feed The style feed.
	 */
	public function __construct( Style_Feed $style_feed ) {
		$this->style_feed = $style_feed;
		$this->css        = new Kadence_CSS();
	}

	/**
	 * Registers the Button block's Secondary style.
	 *
	 * Kadence's button rule paints every Button block with Base's colors after core's styles,
	 * so the style paints its buttons with the Secondary variables, as the theme's
	 * `.button-style-secondary` buttons are. Those follow Secondary's global styles.
	 *
	 * @since 1.6.0
	 */
	public function register_block_style(): void {
		$selector = '.wp-block-button.is-style-secondary .wp-block-button__link';

		register_block_style(
			'core/button',
			[
				'name'         => 'secondary',
				'label'        => __( 'Secondary', 'kadence' ),
				'inline_style' => $selector . ',' . $selector . ':visited{background:var(--global-palette-btn-sec-bg);color:var(--global-palette-btn-sec)}'
					. $selector . ':hover,' . $selector . ':focus,' . $selector . ':active{background:var(--global-palette-btn-sec-bg-hover);color:var(--global-palette-btn-sec-hover)}',
			]
		);
	}

	/**
	 * Gives the theme origin the Base, Secondary and Outline buttons.
	 *
	 * Untyped, since the Gutenberg plugin passes its own WP_Theme_JSON_Data_Gutenberg, which
	 * has the same methods but doesn't extend core's class.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_Theme_JSON_Data $theme_json Theme origin data.
	 * @return WP_Theme_JSON_Data
	 */
	public function fill_theme_origin( $theme_json ) {
		$data = $theme_json->get_data();

		return $theme_json->update_with(
			[
				'version' => $data['version'] ?? WP_Theme_JSON::LATEST_SCHEMA,
				'styles'  => [
					'elements' => [
						'button' => array_replace_recursive( self::BASE_DEFAULTS, $this->desktop( 'buttons' ) ),
					],
					'blocks'   => [
						'core/button' => [
							'variations' => [
								'secondary' => $this->desktop( 'buttons_secondary' ) + $this->responsive( 'buttons_secondary' ),
								'outline'   => $this->outline(),
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Outline's values.
	 *
	 * Kadence's Outline has a 2px solid currentColor border and Base's padding, and its text
	 * color is Base's background. On hover the border goes back to the Customizer border, or
	 * to currentColor, unless a hover color is set.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	private function outline(): array {
		$own_border = $this->border( 'buttons_outline', 'desktop' );
		$background = $this->option( 'buttons_background' );
		$defaults   = self::OUTLINE_DEFAULTS;

		$this->set( $defaults, [ 'color', 'text' ], $this->color( $background['color'] ?? null ) );
		$this->set( $defaults, [ ':hover', 'color', 'text' ], $this->color( $background['hover'] ?? null ) );
		$this->set( $defaults, [ ':hover', 'border', 'color' ], $own_border['color'] ?? 'currentColor' );

		$padding = $this->outline_padding();

		$this->set( $defaults, [ 'spacing', 'padding' ], $padding['desktop'] ?? null );
		$this->set( $defaults, [ '@tablet', 'spacing', 'padding' ], $padding['tablet'] ?? null );
		$this->set( $defaults, [ '@mobile', 'spacing', 'padding' ], $padding['mobile'] ?? null );

		return array_replace_recursive( $defaults, $this->desktop( 'buttons_outline' ) + $this->responsive( 'buttons_outline' ) );
	}

	/**
	 * Outline's padding when it has none of its own, by state.
	 *
	 * Kadence gives Outline buttons Base's padding, and then Outline's own. At a smaller width,
	 * Outline's own padding for a larger width still beats Base's.
	 *
	 * @since 1.6.0
	 *
	 * @return array<string, array|null> Device => padding.
	 */
	private function outline_padding(): array {
		$own  = $this->option( 'buttons_outline_padding' );
		$base = $this->option( 'buttons_padding' );

		if ( null !== $this->padding( $own, 'desktop' ) ) {
			return [];
		}

		return [
			'desktop' => $this->padding( $base, 'desktop' ) ?? self::BASE_DEFAULTS['spacing']['padding'],
			'tablet'  => null === $this->padding( $own, 'tablet' ) ? $this->padding( $base, 'tablet' ) : null,
			'mobile'  => null === $this->padding( $own, 'mobile' ) && null === $this->padding( $own, 'tablet' ) ? $this->padding( $base, 'mobile' ) ?? $this->padding( $base, 'tablet' ) : null,
		];
	}

	/**
	 * A style's desktop values.
	 *
	 * @since 1.6.0
	 *
	 * @param string $prefix Setting name prefix: `buttons`, `buttons_secondary` or `buttons_outline`.
	 * @return array
	 */
	private function desktop( string $prefix ): array {
		$style      = [];
		$text       = $this->option( $prefix . '_color' );
		$background = $this->option( $prefix . '_background' );
		$colors     = $this->option( $prefix . '_border_colors' );

		$this->set( $style, [ 'color', 'text' ], $this->color( $text['color'] ?? null ) );
		$this->set( $style, [ ':hover', 'color', 'text' ], $this->color( $text['hover'] ?? null ) );
		$this->set_background( $style, [ 'color' ], $background['color'] ?? null );
		$this->set_background( $style, [ ':hover', 'color' ], $background['hover'] ?? null );
		$this->set( $style, [ 'border' ], $this->border( $prefix, 'desktop' ) );
		$this->set( $style, [ 'border', 'color' ], $this->color( $colors['color'] ?? null ) );
		$this->set( $style, [ ':hover', 'border', 'color' ], $this->color( $colors['hover'] ?? null ) );
		$this->set( $style, [ 'border', 'radius' ], $this->css->render_range( $this->option( $prefix . '_border_radius' ), 'desktop' ) ?: null );
		$this->set( $style, [ 'spacing', 'padding' ], $this->padding( $this->option( $prefix . '_padding' ), 'desktop' ) );
		$this->set( $style, [ 'typography' ], $this->typography( $this->option( $prefix . '_typography' ) ) );
		$this->set( $style, [ 'shadow' ], $this->css->render_shadow( $this->option( $prefix . '_shadow' ) ) ?: null );
		$this->set( $style, [ ':hover', 'shadow' ], $this->css->render_shadow( $this->option( $prefix . '_shadow_hover' ) ) ?: null );

		return $style;
	}

	/**
	 * A style's tablet and mobile values, as `@tablet` and `@mobile` states.
	 *
	 * @since 1.6.0
	 *
	 * @param string $prefix Setting name prefix.
	 * @return array
	 */
	private function responsive( string $prefix ): array {
		$tablet = $this->device( $prefix, 'tablet' );
		$mobile = array_replace_recursive( $tablet, $this->device( $prefix, 'mobile' ) );

		return array_filter(
			[
				'@tablet' => $tablet,
				'@mobile' => $mobile,
			]
		);
	}

	/**
	 * A style's values for a tablet or a phone, read the way Kadence's responsive button CSS reads them.
	 *
	 * @since 1.6.0
	 *
	 * @param string $prefix Setting name prefix.
	 * @param string $device `tablet` or `mobile`.
	 * @return array
	 */
	private function device( string $prefix, string $device ): array {
		$style  = [];
		$border = $this->border( $prefix, $device );
		$font   = $this->option( $prefix . '_typography' );

		if ( [] !== $border ) {
			$this->set( $style, [ 'border' ], $border );
			$this->set( $style, [ 'border', 'color' ], $this->color( $this->option( $prefix . '_border_colors' )['color'] ?? null ) );
		}

		$this->set( $style, [ 'border', 'radius' ], $this->css->render_range( $this->option( $prefix . '_border_radius' ), $device ) ?: null );
		$this->set( $style, [ 'spacing', 'padding' ], $this->padding( $this->option( $prefix . '_padding' ), $device ) );
		$this->set( $style, [ 'typography', 'fontSize' ], $this->font_measure( $font, 'size', $device, 'sizeType', 'px' ) );
		$this->set( $style, [ 'typography', 'lineHeight' ], $this->font_measure( $font, 'lineHeight', $device, 'lineType', '' ) );
		$this->set( $style, [ 'typography', 'letterSpacing' ], $this->font_measure( $font, 'letterSpacing', $device, 'spacingType', 'em' ) );

		return $style;
	}

	/**
	 * A style's border for one device, read the way `Kadence_CSS::render_responsive_border()` reads it.
	 *
	 * A tablet or phone border with no style of its own takes the style of a larger device's.
	 *
	 * @since 1.6.0
	 *
	 * @param string $prefix Setting name prefix.
	 * @param string $device Device.
	 * @return array
	 */
	private function border( string $prefix, string $device ): array {
		$borders = $this->option( $prefix . '_border' );
		$border  = $borders[ $device ] ?? null;
		$style   = is_array( $border ) && ! empty( $border['style'] ) ? $border['style'] : '';
		$larger  = [
			'desktop' => [],
			'tablet'  => [ 'desktop' ],
			'mobile'  => [ 'tablet', 'desktop' ],
		];

		foreach ( '' === $style ? $larger[ $device ] : [] as $fallback ) {
			if ( ! empty( $borders[ $fallback ]['style'] ) && 'none' !== $borders[ $fallback ]['style'] ) {
				$style = $borders[ $fallback ]['style'];
				break;
			}
		}

		if ( ! is_array( $border ) || '' === $style ) {
			return [];
		}

		return array_filter(
			[
				'width' => ( empty( $border['width'] ) ? '0' : $border['width'] ) . ( empty( $border['unit'] ) ? 'px' : $border['unit'] ),
				'style' => $style,
				'color' => $this->color( empty( $border['color'] ) ? 'transparent' : $border['color'] ),
			]
		);
	}

	/**
	 * Padding for one device, read the way `render_responsive_measure()` reads it.
	 *
	 * @since 1.6.0
	 *
	 * @param array  $padding Customizer padding.
	 * @param string $device  Device.
	 * @return array|null
	 */
	private function padding( array $padding, string $device ): ?array {
		$sizes = $padding['size'][ $device ] ?? null;

		if ( ! is_array( $sizes ) ) {
			return null;
		}

		$unit   = empty( $padding['unit'][ $device ] ) ? 'px' : $padding['unit'][ $device ];
		$values = [];
		$set    = false;

		foreach ( [ 'top', 'right', 'bottom', 'left' ] as $index => $side ) {
			$size            = $sizes[ $index ] ?? null;
			$set             = $set || is_numeric( $size );
			$values[ $side ] = ( is_numeric( $size ) ? $size : '0' ) . $unit;
		}

		return $set ? $values : null;
	}

	/**
	 * Desktop typography, read the way `Kadence_CSS::render_font()` reads it.
	 *
	 * @since 1.6.0
	 *
	 * @param array $font Customizer typography.
	 * @return array
	 */
	private function typography( array $font ): array {
		$size       = $this->css->generate_clamp_value( $font );
		$line_type  = empty( $font['lineType'] ) || '-' === $font['lineType'] ? '' : $font['lineType'];
		$typography = [
			'fontFamily'    => $this->font_family( $font ),
			'fontStyle'     => empty( $font['style'] ) ? null : $font['style'],
			'fontWeight'    => empty( $font['weight'] ) ? null : (string) $font['weight'],
			'textTransform' => empty( $font['transform'] ) ? null : $font['transform'],
		];

		if ( '' === $size && ! empty( $font['size']['desktop'] ) ) {
			$size = $font['size']['desktop'] . ( empty( $font['sizeType'] ) ? 'px' : $font['sizeType'] );
		}

		if ( is_numeric( $font['lineHeight']['desktop'] ?? null ) ) {
			$typography['lineHeight'] = $font['lineHeight']['desktop'] . $line_type;
		}

		if ( is_numeric( $font['letterSpacing']['desktop'] ?? null ) ) {
			$typography['letterSpacing'] = $font['letterSpacing']['desktop'] . ( empty( $font['spacingType'] ) ? 'em' : $font['spacingType'] );
		}

		$typography['fontSize'] = '' === $size ? null : $size;

		return array_filter( $typography, 'is_string' );
	}

	/**
	 * A font family, written the way `Kadence_CSS::render_font()` writes it.
	 *
	 * @since 1.6.0
	 *
	 * @param array $font Customizer typography.
	 * @return string|null
	 */
	private function font_family( array $font ): ?string {
		$family = $font['family'] ?? '';

		if ( ! is_string( $family ) || '' === $family || 'inherit' === $family ) {
			return null;
		}

		if ( ! str_contains( $family, '"' ) && ! str_contains( $family, ',' ) && str_contains( $family, ' ' ) ) {
			$family = "'" . $family . "'";
		}

		if ( true === ( $font['google'] ?? false ) && ! str_starts_with( $family, 'var(' ) ) {
			$fallback = $font['fallback'] ?? '';

			if ( 'handwriting' === $fallback ) {
				$family .= ', cursive';
			} elseif ( 'display' === $fallback ) {
				$family .= ', var(--global-display-fallback-font)';
			} elseif ( ! empty( $fallback ) ) {
				$family .= ', ' . $fallback;
			} else {
				$family .= ', var(--global-fallback-font)';
			}
		}

		return apply_filters( 'kadence_theme_font_family_string', $family );
	}

	/**
	 * A tablet or phone font size, line height or letter spacing, read the way Kadence's
	 * responsive button CSS reads it.
	 *
	 * @since 1.6.0
	 *
	 * @param array  $font         Customizer typography.
	 * @param string $key          Typography key.
	 * @param string $device       Device.
	 * @param string $unit_key     Key of the value's unit.
	 * @param string $default_unit Unit when none is set.
	 * @return string|null
	 */
	private function font_measure( array $font, string $key, string $device, string $unit_key, string $default_unit ): ?string {
		$value = $font[ $key ][ $device ] ?? '';
		$unit  = $font[ $unit_key ] ?? '';

		if ( empty( $value ) || ! is_scalar( $value ) ) {
			return null;
		}

		return $value . ( empty( $unit ) || '-' === $unit ? $default_unit : $unit );
	}

	/**
	 * Sets a background color, or a gradient when the Customizer value is one.
	 *
	 * @since 1.6.0
	 *
	 * @param array    $style Styles built so far.
	 * @param string[] $path  Path of the `color` object.
	 * @param mixed    $value Customizer value.
	 */
	private function set_background( array &$style, array $path, $value ): void {
		$path[] = $this->is_gradient( $value ) ? 'gradient' : 'background';

		$this->set( $style, $path, $this->style_feed->color_value( $value ) );
	}

	/**
	 * A Customizer color as global styles holds it, or null for a gradient.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $value Customizer value.
	 * @return string|null
	 */
	private function color( $value ): ?string {
		return $this->is_gradient( $value ) ? null : $this->style_feed->color_value( $value );
	}

	/**
	 * Whether a Customizer color is a gradient, as Kadence's button CSS decides it.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $value Customizer value.
	 * @return bool
	 */
	private function is_gradient( $value ): bool {
		return is_string( $value ) && 1 === preg_match( '/^(linear|radial)/', $value );
	}

	/**
	 * A Customizer setting that holds an array, or an empty one.
	 *
	 * @since 1.6.0
	 *
	 * @param string $name Setting name.
	 * @return array
	 */
	private function option( string $name ): array {
		$value = kadence()->option( $name );

		return is_array( $value ) ? $value : [];
	}

	/**
	 * Sets a value in the styles, unless it is empty.
	 *
	 * @since 1.6.0
	 *
	 * @param array    $style Styles built so far.
	 * @param string[] $path  Path under the style.
	 * @param mixed    $value Value.
	 */
	private function set( array &$style, array $path, $value ): void {
		if ( null === $value || [] === $value ) {
			return;
		}

		foreach ( array_reverse( $path ) as $key ) {
			$value = [ $key => $value ];
		}

		$style = array_replace_recursive( $style, $value );
	}
}
