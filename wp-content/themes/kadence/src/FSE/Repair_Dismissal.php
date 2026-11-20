<?php
/**
 * Kadence\FSE\Repair_Dismissal class
 *
 * @package kadence
 */

namespace Kadence\FSE;

/**
 * Whether the repair checklist was dismissed on this site.
 *
 * @since 1.6.0
 */
final class Repair_Dismissal {

	/**
	 * Option that marks the checklist dismissed.
	 *
	 * @since 1.6.0
	 */
	private const OPTION = 'kadence_fse_repair_dismissed';

	/**
	 * Whether the checklist was dismissed on this site.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	public function is_dismissed(): bool {
		return (bool) get_option( self::OPTION );
	}

	/**
	 * Dismisses the checklist for everyone on this site, or shows it again.
	 *
	 * @since 1.6.0
	 *
	 * @param bool $dismissed Whether to dismiss it.
	 * @return bool True when the choice is stored after the call.
	 */
	public function set( bool $dismissed ): bool {
		// Core reports an unchanged value as a failed write.
		if ( $this->is_dismissed() === $dismissed ) {
			return true;
		}

		if ( $dismissed ) {
			return update_option( self::OPTION, true, false );
		}

		return delete_option( self::OPTION );
	}

	/**
	 * Shows the checklist again, since turning the mode on rebuilds it.
	 *
	 * @since 1.6.0
	 */
	public function reset(): void {
		delete_option( self::OPTION );
	}
}
