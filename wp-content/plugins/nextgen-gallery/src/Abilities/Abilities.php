<?php
/**
 * NextGEN Gallery Abilities — the registering instance.
 *
 * Every definition and callback lives on AbstractAbilities. This class adds the
 * `ngg_register_abilities` filter, the seam NextGEN Pro, Plus, Starter and addons use to add or
 * replace definitions. Only this instance registers, so whatever editions are active the
 * abilities register once, under the nextgen-gallery category.
 *
 * Requires WordPress 6.9+. Nothing is hooked when wp_register_ability() is absent.
 *
 * @package NextGEN Gallery
 */

namespace Imagely\NGG\Abilities;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Free-plugin registration surface for the WordPress Abilities API.
 */
class Abilities extends AbstractAbilities {

	/**
	 * Return the ability definitions to register.
	 *
	 * Returning a non-array from the filter is ignored.
	 *
	 * @return array<string, array>
	 */
	public function get_ability_definitions(): array {
		$definitions = $this->get_common_ability_definitions();

		/**
		 * Filters the NextGEN ability definitions before registration.
		 *
		 * Keys are fully namespaced ability names ('nextgen-gallery/list-galleries'); values
		 * are the argument arrays passed to wp_register_ability(). Add to the array to register
		 * more abilities, or replace an entry to change one. Never register NextGEN abilities
		 * directly: a second registration of the same name is rejected by core.
		 *
		 * @param array<string, array> $definitions Ability definitions.
		 * @param Abilities            $handler     The registering handler.
		 */
		$filtered = apply_filters( 'ngg_register_abilities', $definitions, $this );

		return is_array( $filtered ) ? $filtered : $definitions;
	}
}
