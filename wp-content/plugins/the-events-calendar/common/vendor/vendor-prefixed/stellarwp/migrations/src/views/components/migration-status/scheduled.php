<?php

/**
 * Migration Status Template — Scheduled.
 *
 * @since 0.0.1
 * @version 0.0.1
 *
 * @package \StellarWP\Migrations
 *
 * @var TEC\Common\StellarWP\Migrations\Contracts\Migration    $migration    Migration object.
 * @var TEC\Common\StellarWP\Migrations\Utilities\Migration_UI $migration_ui UI helper object.
 */
defined('ABSPATH') || exit;
use TEC\Common\StellarWP\Migrations\Config;
use TEC\Common\StellarWP\Migrations\Contracts\Migration;
use TEC\Common\StellarWP\Migrations\Utilities\Migration_UI;
if (!isset($migration) || !isset($migration_ui) || !$migration instanceof Migration || !$migration_ui instanceof Migration_UI) {
    return;
}
$status_value = $migration_ui->get_display_status()->getValue();
$status_label = $migration_ui->get_display_status_label();
$template = Config::get_template_engine();
?>
<div class="stellarwp-migration-card__status">
	<span class="stellarwp-migration-card__status-label stellarwp-migration-card__status-label--<?php 
echo esc_attr($status_value);
?>">
		<?php 
echo esc_html($status_label);
?>
	</span>

		<?php 
$template->template('components/progress-text', ['migration' => $migration, 'migration_ui' => $migration_ui]);
?>
</div>
