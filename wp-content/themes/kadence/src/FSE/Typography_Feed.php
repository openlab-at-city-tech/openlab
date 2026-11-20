<?php
/**
 * Kadence\FSE\Typography_Feed class
 *
 * @package kadence
 */

namespace Kadence\FSE;

use Kadence\Kadence_CSS;
use WP_Theme_JSON;
use WP_Theme_JSON_Data;
use function Kadence\kadence;

/**
 * Gives the Site Editor the Customizer's Base Font, Heading Font and per-heading fonts in Full
 * Site Editing mode, and lets a change made in Styles → Typography reach Kadence's CSS.
 *
 * The theme origin gets each font's family, size, style, weight, line height and letter spacing,
 * at their desktop values. A clamped size goes as its `clamp()`, as the button styles do. Base
 * Font goes to `styles.typography`, Heading Font to the `heading` element, and each heading's own
 * font to its element, where a family left on Inherit isn't written. A family is a reference to
 * the preset `Font_Families` registers for it.
 *
 * Kadence prints these fonts after core's global styles, on the same selectors, so it wins.
 * Where global styles hold a different value than the theme feeds them, the value goes after
 * Kadence's own rules. A Base Font or Heading Font family redefines the `--global-*` variable
 * Kadence's rules and Kadence Blocks read. Core holds no tablet or mobile values for these, so
 * the Customizer's are printed again after a changed size, line height or letter spacing, and
 * still apply below their breakpoints.
 *
 * In the CSS, only the family of the Headings element is read back. Each heading's size, weight,
 * style, line height and letter spacing come from its own Customizer font, and that wins over the
 * Headings element's in core's CSS too.
 *
 * @since 1.6.0
 */
final class Typography_Feed {

	/**
	 * Per-heading Customizer option => its element.
	 *
	 * @since 1.6.0
	 */
	private const HEADINGS = [
		'h1_font' => 'h1',
		'h2_font' => 'h2',
		'h3_font' => 'h3',
		'h4_font' => 'h4',
		'h5_font' => 'h5',
		'h6_font' => 'h6',
	];

	/**
	 * Customizer option => the element its font goes to. Base Font goes to the root.
	 *
	 * @since 1.6.0
	 */
	private const ELEMENTS = [
		'base_font'    => '',
		'heading_font' => 'heading',
	] + self::HEADINGS;

	/**
	 * Global styles typography key => CSS property.
	 *
	 * @since 1.6.0
	 */
	private const PROPERTIES = [
		'fontFamily'    => 'font-family',
		'fontSize'      => 'font-size',
		'fontStyle'     => 'font-style',
		'fontWeight'    => 'font-weight',
		'lineHeight'    => 'line-height',
		'letterSpacing' => 'letter-spacing',
	];

	/**
	 * CSS property => the `Kadence_CSS` method that renders its tablet or mobile value. A line
	 * height goes through `device_line_height()` instead, for its unit.
	 *
	 * @since 1.6.0
	 */
	private const DEVICE_RENDERERS = [
		'font-size'      => 'render_font_size',
		'line-height'    => 'render_font_height',
		'letter-spacing' => 'render_font_spacing',
	];

	/**
	 * Customizer option => the variable Kadence's CSS reads its family from.
	 *
	 * @since 1.6.0
	 */
	private const VARIABLES = [
		'base_font'    => '--global-body-font-family',
		'heading_font' => '--global-heading-font-family',
	];

	/**
	 * The post title selectors Kadence's editor H1 rule adds, at their specificity.
	 *
	 * @since 1.6.0
	 */
	private const EDITOR_TITLE_SELECTOR = '.editor-styles-wrapper .editor-post-title .editor-post-title__input, .block-editor-page .editor-post-title.wp-block .editor-post-title.wp-block__input, .editor-styles-wrapper .edit-post-visual-editor__post-title-wrapper h1';

	/**
	 * The font families registered for the picker.
	 *
	 * @since 1.6.0
	 *
	 * @var Font_Families
	 */
	private Font_Families $font_families;

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
	 * @param Font_Families $font_families The font families registered for the picker.
	 */
	public function __construct( Font_Families $font_families ) {
		$this->font_families = $font_families;
		$this->css           = new Kadence_CSS();
	}

	/**
	 * Gives the theme origin the Customizer's fonts.
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
		$styles = [];

		foreach ( self::ELEMENTS as $option => $element ) {
			$typography = $this->typography( kadence()->option( $option ) );

			if ( ! $typography ) {
				continue;
			}

			if ( '' === $element ) {
				$styles['typography'] = $typography;
			} else {
				$styles['elements'][ $element ]['typography'] = $typography;
			}
		}

		if ( ! $styles ) {
			return $theme_json;
		}

		return $theme_json->update_with(
			[
				'version' => $theme_json->get_data()['version'] ?? WP_Theme_JSON::LATEST_SCHEMA,
				'styles'  => $styles,
			]
		);
	}

	/**
	 * Appends the fonts changed in global styles to the front end CSS.
	 *
	 * Kadence's body and heading rules read the families from their variables, and a heading's
	 * own family is printed on the heading.
	 *
	 * @since 1.6.0
	 *
	 * @param string $css Dynamic CSS built so far.
	 * @return string
	 */
	public function append_properties( $css ): string {
		$changed   = $this->changed();
		$body      = apply_filters( 'kadence_body_css_selector', 'body' );
		$selectors = [ 'base_font' => $body ] + self::HEADINGS;
		$rules     = $this->variables( $changed, ':root' ) . $this->rule( $body . ', input, select, optgroup, textarea', array_diff_key( $changed['base_font'] ?? [], [ 'font-family' => true ] ) );

		foreach ( self::HEADINGS as $option => $element ) {
			$rules .= $this->rule( $element, $changed[ $option ] ?? [] );
		}

		return $css . $rules . $this->device_rules( $changed, $selectors );
	}

	/**
	 * Appends the fonts changed in global styles to the editor CSS.
	 *
	 * The editor's rules print the families themselves rather than read the variables, so the
	 * families go on its rules too. The variables go on `body` as well, since the editor canvas
	 * prints the classic `:root` values after this.
	 *
	 * A heading whose Customizer font sets a family of its own is left out of the headings rule.
	 * Kadence's rule for that heading prints earlier, so the headings family would win over it here,
	 * while on the front end and in core's CSS the heading keeps its own family.
	 *
	 * @since 1.6.0
	 *
	 * @param string $css Dynamic CSS built so far.
	 * @return string
	 */
	public function append_editor_properties( $css ): string {
		$changed   = $this->changed();
		$selectors = [ 'base_font' => Body_Colors::EDITOR_SELECTOR ];
		$rules     = $this->variables( $changed, ':root,body' ) . $this->rule( $this->editor_headings_without_a_family(), array_intersect_key( $changed['heading_font'] ?? [], [ 'font-family' => true ] ) );

		foreach ( self::HEADINGS as $option => $element ) {
			$selectors[ $option ] = ( 'h1' === $element ? self::EDITOR_TITLE_SELECTOR . ', ' : '' ) . '.editor-styles-wrapper ' . $element;
		}

		foreach ( $selectors as $option => $selector ) {
			$rules .= $this->rule( $selector, $changed[ $option ] ?? [] );
		}

		return $css . $rules . $this->device_rules( $changed, $selectors );
	}

	/**
	 * Adds the weights global styles use with a Customizer Google font to Kadence's Google fonts,
	 * so a weight chosen in Styles is loaded with it.
	 *
	 * The footer's set holds only fonts Kadence Blocks finds in the footer, never the theme's, so
	 * it is left alone. The fonts are untyped, since Kadence passes null when it has none.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed  $fonts    Family => font, as Kadence collects them.
	 * @param string $font_set Which set the fonts are for.
	 * @return mixed
	 */
	public function add_chosen_variants( $fonts, string $font_set = '' ) {
		if ( ! is_array( $fonts ) || 'footer_gfonts' === $font_set ) {
			return $fonts;
		}

		$styles  = $this->styles( [] );
		$body    = $this->node( $styles, '' );
		$heading = $this->node( $styles, 'heading' );
		$chosen  = [ $body ];

		foreach ( self::HEADINGS as $element ) {
			// A heading's own values win, then the Headings element's, then the body's family, never the body's weight.
			$chosen[] = $this->node( $styles, $element ) + $heading + array_intersect_key( $body, [ 'fontFamily' => true ] );
		}

		foreach ( $chosen as $typography ) {
			$fonts = $this->with_variant( $fonts, $typography );
		}

		return $fonts;
	}

	/**
	 * A Customizer font as global styles typography.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $font Customizer typography.
	 * @return array<string, string>
	 */
	private function typography( $font ): array {
		if ( ! is_array( $font ) ) {
			return [];
		}

		$slug         = $this->font_families->preset_slug_of( $font );
		$line_unit    = empty( $font['lineType'] ) || '-' === $font['lineType'] ? '' : $font['lineType'];
		$spacing_unit = empty( $font['spacingType'] ) ? 'em' : $font['spacingType'];
		$typography   = [
			'fontFamily'    => null === $slug ? null : 'var:preset|font-family|' . $slug,
			'fontSize'      => $this->size( $font ),
			'fontStyle'     => $font['style'] ?? null,
			'fontWeight'    => $font['weight'] ?? null,
			'lineHeight'    => $this->measure( $font['lineHeight']['desktop'] ?? null, $line_unit ),
			'letterSpacing' => $this->measure( $font['letterSpacing']['desktop'] ?? null, $spacing_unit ),
		];

		return array_filter(
			$typography,
			static function ( $value ): bool {
				return is_string( $value ) && '' !== $value;
			}
		);
	}

	/**
	 * The desktop size, or its `clamp()`, as `Kadence_CSS::render_font()` prints it.
	 *
	 * @since 1.6.0
	 *
	 * @param array<string, mixed> $font Customizer typography.
	 * @return string|null Null when the Customizer sets no size.
	 */
	private function size( array $font ): ?string {
		$clamp = $this->css->generate_clamp_value( $font );

		if ( '' !== $clamp ) {
			return $clamp;
		}

		if ( empty( $font['size']['desktop'] ) ) {
			return null;
		}

		return $this->measure( $font['size']['desktop'], empty( $font['sizeType'] ) ? 'px' : $font['sizeType'] );
	}

	/**
	 * A desktop measure with its unit, as Kadence prints it.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $value Customizer value.
	 * @param mixed $unit  Customizer unit.
	 * @return string|null Null when the Customizer sets no number.
	 */
	private function measure( $value, $unit ): ?string {
		return is_numeric( $value ) && is_string( $unit ) ? $value . $unit : null;
	}

	/**
	 * The font values global styles hold that differ from what the theme feeds them, as CSS.
	 *
	 * @since 1.6.0
	 *
	 * @return array<string, array<string, string>> Customizer option => CSS property => value.
	 */
	private function changed(): array {
		$styles  = $this->styles( [] );
		$base    = $this->styles( [ 'origin' => 'base' ] );
		$changed = [];

		foreach ( self::ELEMENTS as $option => $element ) {
			$theme = $this->css_values( $this->node( $base, $element ) );

			foreach ( $this->css_values( $this->node( $styles, $element ) ) as $property => $value ) {
				if ( $value !== ( $theme[ $property ] ?? null ) ) {
					$changed[ $option ][ $property ] = $value;
				}
			}
		}

		return $changed;
	}

	/**
	 * The global styles of an origin.
	 *
	 * @since 1.6.0
	 *
	 * @param array{origin?: string} $context Context for `wp_get_global_styles()`.
	 * @return array<string, mixed>
	 */
	private function styles( array $context ): array {
		$styles = wp_get_global_styles( [], $context );

		return is_array( $styles ) ? $styles : [];
	}

	/**
	 * The typography of the root, or of an element.
	 *
	 * Core keeps a number saved there, such as a weight of 700 or a line height of 1.5, and
	 * prints it as saved, so a number comes back as text.
	 *
	 * @since 1.6.0
	 *
	 * @param array<string, mixed> $styles  Global styles.
	 * @param string               $element Element, or empty for the root.
	 * @return array{fontFamily?: mixed, fontSize?: mixed, fontStyle?: mixed, fontWeight?: mixed, lineHeight?: mixed, letterSpacing?: mixed}
	 */
	private function node( array $styles, string $element ): array {
		$typography = '' === $element ? ( $styles['typography'] ?? null ) : ( $styles['elements'][ $element ]['typography'] ?? null );

		if ( ! is_array( $typography ) ) {
			return [];
		}

		foreach ( $typography as $key => $value ) {
			if ( is_int( $value ) || is_float( $value ) ) {
				$typography[ $key ] = $this->measure( $value, '' );
			}
		}

		return $typography;
	}

	/**
	 * The font values of a typography node that are safe to print, as CSS.
	 *
	 * Refuses anything that could end the declaration or the style element, as
	 * `Derived_Tokens::is_color()` does: `;`, `{`, `}`, `<`, `>` and comment openers.
	 *
	 * @since 1.6.0
	 *
	 * @param array{fontFamily?: mixed, fontSize?: mixed, fontStyle?: mixed, fontWeight?: mixed, lineHeight?: mixed, letterSpacing?: mixed} $typography Global styles typography.
	 * @return array<string, string> CSS property => value.
	 */
	private function css_values( array $typography ): array {
		$values = [];

		foreach ( self::PROPERTIES as $key => $property ) {
			$value = $typography[ $key ] ?? null;

			if ( ! is_string( $value ) || '' === trim( $value ) || 1 === preg_match( '/[;{}<>]|\/\*/', $value ) ) {
				continue;
			}

			$values[ $property ] = trim( $value );
		}

		return $values;
	}

	/**
	 * The rule that redefines the family variables.
	 *
	 * @since 1.6.0
	 *
	 * @param array<string, array<string, string>> $changed  Changed values, by Customizer option.
	 * @param string                               $selector Selector to declare them on.
	 * @return string
	 */
	private function variables( array $changed, string $selector ): string {
		$declarations = [];

		foreach ( self::VARIABLES as $option => $variable ) {
			if ( isset( $changed[ $option ]['font-family'] ) ) {
				$declarations[ $variable ] = $changed[ $option ]['font-family'];
			}
		}

		return $this->rule( $selector, $declarations );
	}

	/**
	 * The editor's selectors for the headings whose Customizer font has no family of its own, as
	 * `Kadence_CSS::render_font()` reads it.
	 *
	 * @since 1.6.0
	 *
	 * @return string Empty when every heading has its own family.
	 */
	private function editor_headings_without_a_family(): string {
		$selectors = [];

		foreach ( self::HEADINGS as $option => $element ) {
			$family = kadence()->sub_option( $option, 'family' );

			if ( ! $family || 'inherit' === $family ) {
				$selectors[] = '.editor-styles-wrapper ' . $element;
			}
		}

		return implode( ', ', $selectors );
	}

	/**
	 * The Customizer's tablet and mobile values for what global styles changed, in Kadence's media
	 * queries. They go after the desktop overrides, so they still apply below their breakpoints.
	 *
	 * @since 1.6.0
	 *
	 * @param array<string, array<string, string>> $changed   Changed values, by Customizer option.
	 * @param array<string, string>                $selectors Customizer option => selector.
	 * @return string
	 */
	private function device_rules( array $changed, array $selectors ): string {
		$output  = new Kadence_CSS();
		$devices = [
			'tablet' => apply_filters( 'kadence_tablet_media_query', '(max-width: 1024px)' ),
			'mobile' => apply_filters( 'kadence_mobile_media_query', '(max-width: 767px)' ),
		];

		foreach ( $devices as $device => $media_query ) {
			$output->start_media_query( $media_query );

			foreach ( $selectors as $option => $selector ) {
				$font = kadence()->option( $option );

				$output->set_selector( $selector );

				foreach ( array_intersect_key( self::DEVICE_RENDERERS, $changed[ $option ] ?? [] ) as $property => $renderer ) {
					$output->add_property( $property, 'line-height' === $property ? $this->device_line_height( $font, $device ) : $this->css->$renderer( $font, $device ) );
				}
			}

			$output->stop_media_query();
		}

		return $output->css_output();
	}

	/**
	 * A tablet or mobile line height as Kadence's own body and heading rules print it.
	 *
	 * Those rules leave out the unit when the Customizer's type is `-` or none, where
	 * `Kadence_CSS::render_font_height()` would print `1.2-`, which browsers drop, or `1.2px`.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed  $font   Customizer typography.
	 * @param string $device `tablet` or `mobile`.
	 * @return string|false False when the Customizer sets none.
	 */
	private function device_line_height( $font, string $device ) {
		if ( ! empty( $font['lineType'] ) && '-' !== $font['lineType'] ) {
			return $this->css->render_font_height( $font, $device );
		}

		if ( empty( $font['lineHeight'][ $device ] ) ) {
			return false;
		}

		return $this->measure( $font['lineHeight'][ $device ], '' ) ?? false;
	}

	/**
	 * A CSS rule.
	 *
	 * @since 1.6.0
	 *
	 * @param string                $selector     Selector.
	 * @param array<string, string> $declarations Property => value.
	 * @return string Empty when there is no selector or nothing to declare.
	 */
	private function rule( string $selector, array $declarations ): string {
		$body = '';

		foreach ( $declarations as $property => $value ) {
			$body .= $property . ':' . $value . ';';
		}

		return '' === $body || '' === $selector ? '' : $selector . '{' . $body . '}';
	}

	/**
	 * Adds the variant a typography node uses to its Google family, when a Customizer font
	 * loads that family from Google.
	 *
	 * @since 1.6.0
	 *
	 * @param array<string, array{fontfamily: string, fontvariants: string[], fontsubsets: string[]}>                                       $fonts      Family => font, as Kadence collects them.
	 * @param array{fontFamily?: mixed, fontSize?: mixed, fontStyle?: mixed, fontWeight?: mixed, lineHeight?: mixed, letterSpacing?: mixed} $typography Global styles typography.
	 * @return array<string, array{fontfamily: string, fontvariants: string[], fontsubsets: string[]}>
	 */
	private function with_variant( array $fonts, array $typography ): array {
		$family = $this->google_family( $typography['fontFamily'] ?? null );
		$weight = $typography['fontWeight'] ?? null;

		if ( null === $family || ! is_string( $weight ) || ! ctype_digit( $weight ) ) {
			return $fonts;
		}

		$italic  = 'italic' === ( $typography['fontStyle'] ?? '' ) ? 'italic' : '';
		$variant = '400' === $weight ? ( '' === $italic ? 'regular' : $italic ) : $weight . $italic;

		if ( ! is_array( $fonts[ $family ]['fontvariants'] ?? null ) ) {
			$fonts[ $family ] = [
				'fontfamily'   => $family,
				'fontvariants' => [],
				'fontsubsets'  => [],
			];
		}

		if ( ! in_array( $variant, $fonts[ $family ]['fontvariants'], true ) ) {
			$fonts[ $family ]['fontvariants'][] = $variant;
		}

		return $fonts;
	}

	/**
	 * The Google family a global styles family refers to.
	 *
	 * Core has already resolved a preset reference to its variable here.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $family Global styles font family.
	 * @return string|null Null when it isn't a preset a Customizer Google font registers.
	 */
	private function google_family( $family ): ?string {
		$prefix = 'var(--wp--preset--font-family--';

		if ( ! is_string( $family ) || ! str_starts_with( $family, $prefix ) ) {
			return null;
		}

		return $this->font_families->google_family_of( rtrim( substr( $family, strlen( $prefix ) ), ')' ) );
	}
}
