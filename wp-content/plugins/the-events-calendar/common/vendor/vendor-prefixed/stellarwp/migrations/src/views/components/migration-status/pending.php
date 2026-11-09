<?php

/**
 * Migration Status Template — Pending.
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
use TEC\Common\StellarWP\Migrations\Contracts\Migration;
use TEC\Common\StellarWP\Migrations\Utilities\Migration_UI;
if (!isset($migration) || !isset($migration_ui) || !$migration instanceof Migration || !$migration_ui instanceof Migration_UI) {
    return;
}
$status_value = $migration_ui->get_display_status()->getValue();
$status_label = $migration_ui->get_display_status_label();
?>
<div class="stellarwp-migration-card__status">
	<span class="stellarwp-migration-card__status-label stellarwp-migration-card__status-label--<?php 
echo esc_attr($status_value);
?>">
		<?php 
echo esc_html($status_label);
?>
	</span>

	<span class="stellarwp-migration-card__dot-separator">&middot;</span>

	<span class="stellarwp-migration-card__total-items">
		<?php 
$total_items = $migration->get_total_items();
printf(
    /* translators: %s: total number of items */
    esc_html(_n('%s total item', '%s total items', $total_items, 'stellarwp-migrations')),
    esc_html(number_format_i18n($total_items))
);
?>
	</span>
</div>
