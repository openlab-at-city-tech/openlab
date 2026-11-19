<?php
/**
 * Kadence\FSE\Derived_Tokens class
 *
 * @package kadence
 */

namespace Kadence\FSE;

/**
 * Lets button and link colors set in the Site Editor reach the theme's own chrome.
 *
 * Core models twelve of Kadence's derived tokens as global styles: the Base and Secondary
 * buttons' background and text with their hovers, the Outline button's text and its hover,
 * and the link color with its hover. In FSE mode a token takes the value the user set
 * there, and keeps the Customizer-derived value Kadence emits today when they set nothing.
 *
 * `elements.button` and `elements.link` are the site-wide defaults, so they redefine the
 * tokens on `:root`. So do the Button block's Secondary and Outline styles, since the
 * theme's secondary and outline buttons read their tokens by class, outside the block.
 * `blocks.core/button` is specific to the Button block, so it redefines the four Base
 * tokens only on `.wp-block-button .wp-block-button__link`. Buttons outside that block
 * keep the element values.
 *
 * A path counts as set only when global styles hold a usable color string for it that
 * differs from the theme origin's, which `Style_Feed` fills for links and `Button_Styles`
 * for buttons. Each pass reads the merged and theme origin styles once, since core builds
 * them anew on every `wp_get_global_styles()` call.
 *
 * Depending on the WordPress version, a palette pick reads back as
 * `var(--wp--preset--color--theme-palette13)`, which core does not define. It is kept as
 * is, because `Palette_Shim` aliases it to the dashed name core does define.
 *
 * The generator is left alone. An override for each set token is appended after its
 * `:root` block, so an unset token is untouched by construction and classic output is
 * byte-identical.
 *
 * @since 1.6.0
 */
final class Derived_Tokens {

	/**
	 * Selector the Button block's own values apply to.
	 *
	 * @since 1.6.0
	 */
	private const BLOCK_SELECTOR = '.wp-block-button .wp-block-button__link';

	/**
	 * Token name => path under `styles` in global styles, for the site-wide defaults.
	 *
	 * Styles → Colors → Button writes `elements.button`, Styles → Colors → Link writes
	 * `elements.link`, and Styles → Blocks → Button writes its styles under `variations`.
	 *
	 * @since 1.6.0
	 *
	 * @var array<string, string[]>
	 */
	private const TOKENS = [
		'--global-palette-btn-bg'           => [ 'elements', 'button', 'color', 'background' ],
		'--global-palette-btn-bg-hover'     => [ 'elements', 'button', ':hover', 'color', 'background' ],
		'--global-palette-btn'              => [ 'elements', 'button', 'color', 'text' ],
		'--global-palette-btn-hover'        => [ 'elements', 'button', ':hover', 'color', 'text' ],
		'--global-palette-btn-sec-bg'       => [ 'blocks', 'core/button', 'variations', 'secondary', 'color', 'background' ],
		'--global-palette-btn-sec-bg-hover' => [ 'blocks', 'core/button', 'variations', 'secondary', ':hover', 'color', 'background' ],
		'--global-palette-btn-sec'          => [ 'blocks', 'core/button', 'variations', 'secondary', 'color', 'text' ],
		'--global-palette-btn-sec-hover'    => [ 'blocks', 'core/button', 'variations', 'secondary', ':hover', 'color', 'text' ],
		'--global-palette-btn-out'          => [ 'blocks', 'core/button', 'variations', 'outline', 'color', 'text' ],
		'--global-palette-btn-out-hover'    => [ 'blocks', 'core/button', 'variations', 'outline', ':hover', 'color', 'text' ],
		'--global-palette-highlight'        => [ 'elements', 'link', 'color', 'text' ],
		'--global-palette-highlight-alt'    => [ 'elements', 'link', ':hover', 'color', 'text' ],
	];

	/**
	 * Token name => path under `styles` in global styles, for the Button block.
	 *
	 * Styles → Blocks → Button writes `blocks.core/button`. Links have no block-level
	 * equivalent.
	 *
	 * @since 1.6.0
	 *
	 * @var array<string, string[]>
	 */
	private const BLOCK_TOKENS = [
		'--global-palette-btn-bg'       => [ 'blocks', 'core/button', 'color', 'background' ],
		'--global-palette-btn-bg-hover' => [ 'blocks', 'core/button', ':hover', 'color', 'background' ],
		'--global-palette-btn'          => [ 'blocks', 'core/button', 'color', 'text' ],
		'--global-palette-btn-hover'    => [ 'blocks', 'core/button', ':hover', 'color', 'text' ],
	];

	/**
	 * Returns the site-wide tokens the user has set, by name.
	 *
	 * @since 1.6.0
	 *
	 * @return array<string, string>
	 */
	public function set_tokens(): array {
		return $this->read( self::TOKENS );
	}

	/**
	 * Returns the tokens the user has set for the Button block, by name.
	 *
	 * @since 1.6.0
	 *
	 * @return array<string, string>
	 */
	public function block_tokens(): array {
		return $this->read( self::BLOCK_TOKENS );
	}

	/**
	 * Appends an override for each token the user has set.
	 *
	 * @since 1.6.0
	 *
	 * @param string $css Dynamic CSS built so far.
	 * @return string
	 */
	public function append_properties( $css ): string {
		$rules = '';

		foreach ( [
			':root'              => $this->set_tokens(),
			self::BLOCK_SELECTOR => $this->block_tokens(),
		] as $selector => $tokens ) {
			$declarations = '';

			foreach ( $tokens as $name => $value ) {
				$declarations .= $name . ':' . $value . ';';
			}

			if ( '' !== $declarations ) {
				$rules .= $selector . '{' . $declarations . '}';
			}
		}

		return $css . $rules;
	}

	/**
	 * Reads each path and keeps the ones the user has set.
	 *
	 * A value the same as the theme origin's, which the style feed fills, isn't a user edit.
	 *
	 * @since 1.6.0
	 *
	 * @param array<string, string[]> $paths Token name => path under `styles`.
	 * @return array<string, string>
	 */
	private function read( array $paths ): array {
		$merged = wp_get_global_styles();
		$base   = wp_get_global_styles( [], [ 'origin' => 'base' ] );
		$tokens = [];

		foreach ( $paths as $name => $path ) {
			$value = $this->value_at( $merged, $path );

			if ( self::is_color( $value ) && $value !== $this->value_at( $base, $path ) ) {
				$tokens[ $name ] = trim( $value );
			}
		}

		return $tokens;
	}

	/**
	 * The value at a path in a styles tree, or null when the path is missing.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed    $styles Styles tree.
	 * @param string[] $path   Path under `styles`.
	 * @return mixed
	 */
	private function value_at( $styles, array $path ) {
		foreach ( $path as $key ) {
			if ( ! is_array( $styles ) || ! array_key_exists( $key, $styles ) ) {
				return null;
			}

			$styles = $styles[ $key ];
		}

		return $styles;
	}

	/**
	 * Whether a value is a color safe to write into a declaration.
	 *
	 * Rejects empty values, the styles tree core returns for a missing path, and anything
	 * that could end the declaration or the style element: `;`, `{`, `}`, `<`, `>` and
	 * comment openers. `--global-*` references are refused too, since a token pointing at
	 * itself would be invalid. Any other value is left to the browser to accept or ignore.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $value Value read from global styles.
	 * @return bool
	 */
	public static function is_color( $value ): bool {
		if ( ! is_string( $value ) || '' === trim( $value ) ) {
			return false;
		}

		return ! str_contains( $value, '--global-' ) && 1 !== preg_match( '/[;{}<>]|\/\*/', $value );
	}
}
