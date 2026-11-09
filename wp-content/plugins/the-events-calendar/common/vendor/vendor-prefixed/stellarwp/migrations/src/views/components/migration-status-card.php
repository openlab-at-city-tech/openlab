<?php

/**
 * Migration Status Card Component Template.
 *
 * Displays the current migration status with progress and action buttons.
 * Used on the single migration detail page.
 *
 * @since 0.0.1
 * @version 0.0.1
 *
 * @package \StellarWP\Migrations
 *
 * @var TEC\Common\StellarWP\Migrations\Contracts\Migration $migration  Migration object.
 * @var list<array<string,mixed>>                $executions List of execution records.
 */
defined('ABSPATH') || exit;
use TEC\Common\StellarWP\Migrations\Config;
use TEC\Common\StellarWP\Migrations\Contracts\Migration;
use TEC\Common\StellarWP\Migrations\Enums\Status;
use TEC\Common\StellarWP\Migrations\Utilities\Migration_UI;
if (!isset($migration) || !$migration instanceof Migration) {
    return;
}
$migration_ui = new Migration_UI($migration);
$executions ??= [];
$latest_execution = $migration->get_latest_execution();
$migration_id = $migration->get_id();
$migration_status = $migration_ui->get_display_status();
$status_value = $migration_status->getValue();
$status_label = $migration_ui->get_display_status_label();
$started_at = $latest_execution ? $latest_execution->get_start_date() : null;
?>
<div class="stellarwp-migration-status-card" data-migration-id="<?php 
echo esc_attr($migration_id);
?>">
	<div class="stellarwp-migration-status-card__content">
		<div class="stellarwp-migration-status-card__status">
			<span class="stellarwp-migration-card__status-label stellarwp-migration-card__status-label--<?php 
echo esc_attr($status_value);
?>">
				<?php 
echo esc_html($status_label);
?>
			</span>

			<?php 
if (!$migration_status->equals(Status::NOT_APPLICABLE())) {
    ?>
				<?php 
    Config::get_template_engine()->template('components/progress-text', ['migration' => $migration, 'migration_ui' => $migration_ui]);
    ?>
			<?php 
}
?>
		</div>

		<?php 
if (!$migration_status->equals(Status::NOT_APPLICABLE())) {
    ?>
			<div class="stellarwp-migration-status-card__progress">
				<?php 
    Config::get_template_engine()->template('components/progress-bar', ['migration' => $migration, 'migration_ui' => $migration_ui]);
    ?>
			</div>
		<?php 
}
?>

		<?php 
Config::get_template_engine()->template('components/migration-actions', ['migration' => $migration, 'migration_ui' => $migration_ui]);
?>
	</div>

	<hr class="stellarwp-migration-card__separator" />

	<div class="stellarwp-migration-status-card__timing">
		<?php 
if ($started_at) {
    ?>
			<span class="stellarwp-migration-status-card__started">
				<?php 
    printf(
        /* translators: %s: human-readable time difference */
        esc_html__('Started %s ago', 'stellarwp-migrations'),
        esc_html(human_time_diff($started_at->getTimestamp()))
    );
    ?>
			</span>
		<?php 
} else {
    ?>
			<span class="stellarwp-migration-status-card__not-started">
				<?php 
    esc_html_e('Not yet started', 'stellarwp-migrations');
    ?>
			</span>
		<?php 
}
?>
	</div>

	<div class="stellarwp-migration-card__message" style="display: none;" role="alert" aria-live="assertive"></div>
</div>
