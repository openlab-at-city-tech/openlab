<?php
/**
 * Kadence\FSE\Dashboard class
 *
 * @package kadence
 */

namespace Kadence\FSE;

/**
 * Gives the Kadence dashboard what its Full Site Editing toggle needs.
 *
 * @since 1.6.0
 */
final class Dashboard {

	/**
	 * Full Site Editing mode.
	 *
	 * @since 1.6.0
	 *
	 * @var Mode
	 */
	private Mode $mode;

	/**
	 * REST endpoint for the mode.
	 *
	 * @since 1.6.0
	 *
	 * @var Rest
	 */
	private Rest $rest;

	/**
	 * Repair checklist, whose detection the opt-in dialog shares.
	 *
	 * @since 1.6.0
	 *
	 * @var Repair
	 */
	private Repair $repair;

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param Mode   $mode Full Site Editing mode.
	 * @param Rest   $rest   REST endpoint for the mode.
	 * @param Repair $repair Repair checklist.
	 */
	public function __construct( Mode $mode, Rest $rest, Repair $repair ) {
		$this->mode   = $mode;
		$this->rest   = $rest;
		$this->repair = $repair;
	}

	/**
	 * Prints the toggle data before the dashboard script.
	 *
	 * The toggle only appears when this data is present.
	 *
	 * @since 1.6.0
	 */
	public function add_dashboard_data(): void {
		if ( ! $this->rest->can_manage() ) {
			return;
		}

		$enabled = $this->mode->is_enabled();
		$data    = [
			'enabled'       => $enabled,
			'supported'     => $this->mode->is_supported(),
			'minimumWp'     => $this->mode->minimum_wp_version(),
			'path'          => $this->rest->get_path(),
			'repairPath'    => $this->rest->get_repair_path(),
			'siteEditorUrl' => admin_url( 'site-editor.php' ),
			'updateCoreUrl' => current_user_can( 'update_core' ) ? network_admin_url( 'update-core.php' ) : '',
			// Only the turn-on dialog shows these, so there's nothing to work out once the mode is on.
			'disclosures'   => $enabled ? [] : $this->repair->get_disclosure_messages(),
		];

		wp_add_inline_script( 'kadence-dashboard', 'window.kadenceFseParams = ' . wp_json_encode( $data ) . ';', 'before' );
	}
}
