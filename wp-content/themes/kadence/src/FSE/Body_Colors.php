<?php
/**
 * Kadence\FSE\Body_Colors class
 *
 * @package kadence
 */

namespace Kadence\FSE;

use Kadence\Kadence_CSS;
use function Kadence\kadence;

/**
 * Lets the Site Editor's body background and text colors reach Kadence's body rules in
 * Full Site Editing mode.
 *
 * Kadence prints the Customizer's body background and text color after core's global
 * styles, on the same selector, so it wins. When global styles hold a different color than
 * the theme feeds them, the override goes after Kadence's own rules.
 *
 * The editor canvas paints the content background over the body background. The Site Editor
 * shows whole templates, so there the body takes global styles' colors, which core updates as
 * they're edited, and the Customizer's where global styles have none, as on the front end.
 * A tablet or mobile site background still wins at that width, until global styles hold a
 * background of their own.
 *
 * @since 1.6.0
 */
final class Body_Colors {

	/**
	 * Selector the editor canvas's body styles apply to.
	 *
	 * @since 1.6.0
	 */
	public const EDITOR_SELECTOR = '.block-editor-page .editor-styles-wrapper, body.editor-styles-wrapper';

	/**
	 * Appends the front end overrides.
	 *
	 * @since 1.6.0
	 *
	 * @param string $css Dynamic CSS built so far.
	 * @return string
	 */
	public function append_properties( $css ): string {
		$colors = $this->changed_colors();
		$rules  = '';

		if ( isset( $colors['background'] ) ) {
			$rules .= 'background-color:' . $colors['background'] . ';';
		}

		if ( isset( $colors['text'] ) ) {
			$rules .= 'color:' . $colors['text'] . ';';
		}

		return $css . ( '' === $rules ? '' : apply_filters( 'kadence_body_css_selector', 'body' ) . '{' . $rules . '}' );
	}

	/**
	 * Appends the editor canvas overrides.
	 *
	 * @since 1.6.0
	 *
	 * @param string $css Dynamic CSS built so far.
	 * @return string
	 */
	public function append_editor_properties( $css ): string {
		if ( $this->in_site_editor() ) {
			return $css . $this->site_editor_rules();
		}

		$colors = $this->changed_colors();
		$rules  = '';

		if ( isset( $colors['background'] ) ) {
			$rules .= '--kad-editor-body-bg:' . $colors['background'] . ';';
		}

		if ( isset( $colors['text'] ) ) {
			$rules .= 'color:' . $colors['text'] . ';';
		}

		return $css . ( '' === $rules ? '' : self::EDITOR_SELECTOR . '{' . $rules . '}' );
	}

	/**
	 * Leaves the Site Editor canvas's body background and text color to global styles.
	 *
	 * @since 1.6.0
	 *
	 * @param bool $body_colors Whether the canvas body takes Kadence's colors.
	 * @return bool
	 */
	public function filter_editor_body_colors( $body_colors ): bool {
		return $this->in_site_editor() ? false : (bool) $body_colors;
	}

	/**
	 * The Site Editor canvas's body rules.
	 *
	 * The Customizer's colors go below core's `body` rule, so global styles win where they hold
	 * a color. A tablet or mobile background goes above it, as on the front end.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	private function site_editor_rules(): string {
		$css = new Kadence_CSS();

		$css->set_selector( ':where(body.editor-styles-wrapper)' );
		$css->add_property( 'background', 'var(--kad-editor-body-bg, #fff)' );
		$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'base_font', 'color' ) ) );

		if ( ! isset( $this->changed_colors()['background'] ) ) {
			$devices = [
				'tablet' => apply_filters( 'kadence_tablet_media_query', '(max-width: 1024px)' ),
				'mobile' => apply_filters( 'kadence_mobile_media_query', '(max-width: 767px)' ),
			];

			foreach ( $devices as $device => $media_query ) {
				if ( $this->sets_background( kadence()->sub_option( 'site_background', $device ) ) ) {
					$css->start_media_query( $media_query );
					$css->set_selector( 'body.editor-styles-wrapper' );
					$css->add_property( 'background', 'var(--kad-editor-body-bg, #fff)' );
					$css->stop_media_query();
				}
			}
		}

		return $css->css_output();
	}

	/**
	 * Whether a Customizer background gives `--kad-editor-body-bg` a value.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $background Customizer background for one device.
	 * @return bool
	 */
	private function sets_background( $background ): bool {
		if ( ! is_array( $background ) ) {
			return false;
		}

		return ! empty( $background['color'] ) || ( 'gradient' === ( $background['type'] ?? '' ) && ! empty( $background['gradient'] ) );
	}

	/**
	 * Whether the Site Editor is being loaded.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	private function in_site_editor(): bool {
		global $pagenow;

		return 'site-editor.php' === $pagenow;
	}

	/**
	 * The body colors global styles hold that differ from what the theme feeds them.
	 *
	 * @since 1.6.0
	 *
	 * @return array<string, string> Background and text colors, by name.
	 */
	private function changed_colors(): array {
		$colors = [];

		foreach ( [ 'background', 'text' ] as $name ) {
			$value = wp_get_global_styles( [ 'color', $name ] );

			if ( Derived_Tokens::is_color( $value ) && $value !== wp_get_global_styles( [ 'color', $name ], [ 'origin' => 'base' ] ) ) {
				$colors[ $name ] = trim( $value );
			}
		}

		return $colors;
	}
}
