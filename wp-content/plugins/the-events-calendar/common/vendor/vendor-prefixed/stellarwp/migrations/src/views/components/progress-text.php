<?php

/**
 * Progress Text Component Template.
 *
 * Displays the progress text showing items processed vs total items.
 *
 * @since 0.0.1
 * @version 0.0.1
 *
 * @package \StellarWP\Migrations
 *
 * @var TEC\Common\StellarWP\Migrations\Contracts\Migration $migration       Migration object.
 * @var TEC\Common\StellarWP\Migrations\Utilities\Migration_UI $migration_ui UI helper object.
 */
defined('ABSPATH') || exit;
use TEC\Common\StellarWP\Migrations\Contracts\Migration;
use TEC\Common\StellarWP\Migrations\Utilities\Migration_UI;
if (!isset($migration) || !isset($migration_ui) || !$migration instanceof Migration || !$migration_ui instanceof Migration_UI) {
    return;
}
$latest_execution = $migration->get_latest_execution();
$total_items = $latest_execution ? $latest_execution->get_items_total() : $migration->get_total_items();
$items_processed = $latest_execution ? $latest_execution->get_items_processed() : 0;
?>
<span class="stellarwp-migration-card__progress-text">
	<?php 
printf(
    /* translators: %1$d: items processed, %2$d: total items */
    esc_html__('%1$d / %2$d', 'stellarwp-migrations'),
    absint($items_processed),
    absint($total_items)
);
?>
</span>
