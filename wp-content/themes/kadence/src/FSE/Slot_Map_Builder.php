<?php
/**
 * Kadence\FSE\Slot_Map_Builder class
 *
 * @package kadence
 */

namespace Kadence\FSE;

use Kadence\Options\Component as Options;
use function Kadence\kadence;

/**
 * Builds the slot map: the value each palette slot's preset holds in Full Site Editing mode.
 *
 * A Customizer value that holds no `var(` goes into the preset as it is. One that does is
 * a reference to another property, and presets can't hold those without risking a cycle
 * with `--global-paletteN`, so the slot takes its default color instead. The one formula
 * is the accent's complement, which `Accent_Complement` computes. Only that slot can be
 * left on its classic behavior, when the accent isn't a color it can read.
 *
 * @since 1.6.0
 */
final class Slot_Map_Builder {

	/**
	 * Number of palette slots.
	 *
	 * @since 1.6.0
	 */
	private const SLOTS = 15;

	/**
	 * The accent slot the complement is computed from.
	 *
	 * @since 1.6.0
	 */
	private const ACCENT_SLOT = 1;

	/**
	 * The slot that holds the accent's complement.
	 *
	 * @since 1.6.0
	 */
	private const ACCENT_COMPLEMENT_SLOT = 10;

	/**
	 * Computes the accent's complement.
	 *
	 * @since 1.6.0
	 *
	 * @var Accent_Complement
	 */
	private Accent_Complement $accent_complement;

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param Accent_Complement $accent_complement Computes the accent's complement.
	 */
	public function __construct( Accent_Complement $accent_complement ) {
		$this->accent_complement = $accent_complement;
	}

	/**
	 * Reads the Customizer palette and builds the slot map from it.
	 *
	 * @since 1.6.0
	 *
	 * @return Slot_Map
	 */
	public function build(): Slot_Map {
		$map = [];

		for ( $slot = 1; $slot <= self::SLOTS; $slot++ ) {
			$map[ $slot ] = $this->resolve( $slot );
		}

		return new Slot_Map( $map );
	}

	/**
	 * The color a slot's preset holds.
	 *
	 * @since 1.6.0
	 *
	 * @param int $slot Slot number.
	 * @return string|null Null when the slot stays on its classic behavior.
	 */
	private function resolve( int $slot ): ?string {
		$raw = trim( (string) kadence()->customizer_palette_option( 'palette' . $slot ) );

		if ( '' !== $raw && ! str_contains( strtolower( $raw ), 'var(' ) ) {
			return $raw;
		}

		if ( self::ACCENT_COMPLEMENT_SLOT === $slot ) {
			// The sentinel, or another formula: our own formula is the only one served.
			return $this->accent_complement->compute( (string) $this->resolve( self::ACCENT_SLOT ) );
		}

		return $this->default_color( $slot );
	}

	/**
	 * A slot's color in the default palette.
	 *
	 * @since 1.6.0
	 *
	 * @param int $slot Slot number.
	 * @return string|null Null when the default palette holds none for the slot.
	 */
	private function default_color( int $slot ): ?string {
		$palette = json_decode( Options::palette_defaults(), true )['palette'] ?? [];

		$color = $palette[ $slot - 1 ]['color'] ?? '';

		return '' === $color ? null : (string) $color;
	}
}
