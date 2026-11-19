<?php
/**
 * Kadence\FSE\Slot_Map class
 *
 * @package kadence
 */

namespace Kadence\FSE;

/**
 * The color each palette preset holds in Full Site Editing mode, by preset slug.
 *
 * Every piece of the palette shim reads this one map, so a slot is either resolved in all
 * of them or left on its classic behavior in all of them.
 *
 * @since 1.6.0
 */
final class Slot_Map {

	/**
	 * Slug of a palette preset, without its slot number.
	 *
	 * @since 1.6.0
	 */
	private const SLUG_PREFIX = 'theme-palette';

	/**
	 * Name of a theme palette property, without its slot number.
	 *
	 * @since 1.6.0
	 */
	private const PROPERTY_PREFIX = '--global-palette';

	/**
	 * Name of a core preset color property, without its slug.
	 *
	 * @since 1.6.0
	 */
	private const PRESET_PROPERTY_PREFIX = '--wp--preset--color--';

	/**
	 * Preset slug => color, or null when the slot stays on its classic behavior.
	 *
	 * @since 1.6.0
	 *
	 * @var array<string, string|null>
	 */
	private array $colors = [];

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param array<int, string|null> $colors Slot number => color, or null when unresolvable.
	 */
	public function __construct( array $colors ) {
		foreach ( $colors as $slot => $color ) {
			$this->colors[ self::SLUG_PREFIX . $slot ] = $color;
		}
	}

	/**
	 * The color a preset holds.
	 *
	 * @since 1.6.0
	 *
	 * @param string $slug Preset slug.
	 * @return string|null Null when the slug is no palette slot, or its slot is unresolvable.
	 */
	public function color_of( string $slug ): ?string {
		return $this->colors[ $slug ] ?? null;
	}

	/**
	 * Every palette slot's preset slug, resolved or not.
	 *
	 * @since 1.6.0
	 *
	 * @return array<int, string>
	 */
	public function slugs(): array {
		return array_keys( $this->colors );
	}

	/**
	 * The theme property that pairs with a preset.
	 *
	 * @since 1.6.0
	 *
	 * @param string $slug Preset slug, such as `theme-palette1`.
	 * @return string Property, such as `--global-palette1`.
	 */
	public function property_of( string $slug ): string {
		return self::PROPERTY_PREFIX . substr( $slug, strlen( self::SLUG_PREFIX ) );
	}

	/**
	 * Each resolved slot's theme property, pointed at its preset.
	 *
	 * @since 1.6.0
	 *
	 * @return array<string, string> Property, such as `--global-palette1` => `var(--wp--preset--color--theme-palette-1)`.
	 */
	public function preset_properties(): array {
		$properties = [];

		foreach ( array_keys( array_filter( $this->colors, 'is_string' ) ) as $slug ) {
			$properties[ $this->property_of( $slug ) ] = 'var(--wp--preset--color--' . _wp_to_kebab_case( $slug ) . ')';
		}

		return $properties;
	}

	/**
	 * Every slot's undashed preset property, pointed at the dashed one core defines.
	 *
	 * Global styles stores a palette pick as `var:preset|color|theme-palette13`. Depending on
	 * the WordPress version it reads back as `var(--wp--preset--color--theme-palette13)`, but
	 * core defines the property from the kebab-cased slug, `--wp--preset--color--theme-palette-13`.
	 * Without an alias the reference is undefined and whatever uses it computes to nothing.
	 *
	 * Covers unresolved slots too: core still defines their dashed property.
	 *
	 * @since 1.6.0
	 *
	 * @return array<string, string> Property, such as `--wp--preset--color--theme-palette1` => `var(--wp--preset--color--theme-palette-1)`.
	 */
	public function preset_aliases(): array {
		$aliases = [];

		foreach ( $this->slugs() as $slug ) {
			$aliases[ self::PRESET_PROPERTY_PREFIX . $slug ] = 'var(' . self::PRESET_PROPERTY_PREFIX . _wp_to_kebab_case( $slug ) . ')';
		}

		return $aliases;
	}
}
