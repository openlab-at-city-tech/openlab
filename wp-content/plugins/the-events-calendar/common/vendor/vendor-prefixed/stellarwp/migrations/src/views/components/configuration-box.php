<?php

/**
 * Configuration Box Component Template.
 *
 * Displays migration configuration details.
 *
 * @since 0.0.1
 * @version 0.0.1
 *
 * @package \StellarWP\Migrations
 *
 * @var TEC\Common\StellarWP\Migrations\Contracts\Migration $migration Migration object.
 */
defined('ABSPATH') || exit;
use TEC\Common\StellarWP\Migrations\Contracts\Migration;
if (!isset($migration) || !$migration instanceof Migration) {
    return;
}
$execution = $migration->get_latest_execution();
$batch_size = $migration->get_default_batch_size();
$retry_attempts = $migration->get_number_of_retries_per_batch();
$total_items = $execution ? $execution->get_items_total() : $migration->get_total_items();
$total_batches = ceil($total_items / $batch_size);
$is_applicable = $migration->is_applicable();
$can_run = $migration->can_run();
$tags = $migration->get_tags();
?>
<div class="stellarwp-migration-config-box">
	<div class="stellarwp-migration-config-box__grid">
		<div class="stellarwp-migration-config-box__item">
			<span class="stellarwp-migration-config-box__label" role="heading" aria-level="3"><?php 
esc_html_e('Total Items', 'stellarwp-migrations');
?></span>
			<span class="stellarwp-migration-config-box__value"><?php 
echo esc_html(number_format_i18n($total_items));
?></span>
		</div>

		<div class="stellarwp-migration-config-box__item">
			<span class="stellarwp-migration-config-box__label" role="heading" aria-level="3"><?php 
esc_html_e('Batch Size', 'stellarwp-migrations');
?></span>
			<span class="stellarwp-migration-config-box__value"><?php 
echo esc_html(number_format_i18n($batch_size));
?></span>
		</div>

		<div class="stellarwp-migration-config-box__item">
			<span class="stellarwp-migration-config-box__label" role="heading" aria-level="3"><?php 
esc_html_e('Total Batches', 'stellarwp-migrations');
?></span>
			<span class="stellarwp-migration-config-box__value"><?php 
echo esc_html(number_format_i18n($total_batches));
?></span>
		</div>

		<div class="stellarwp-migration-config-box__item">
			<span class="stellarwp-migration-config-box__label" role="heading" aria-level="3"><?php 
esc_html_e('Retry Attempts', 'stellarwp-migrations');
?></span>
			<span class="stellarwp-migration-config-box__value"><?php 
echo esc_html(number_format_i18n($retry_attempts));
?></span>
		</div>

		<div class="stellarwp-migration-config-box__item">
			<span class="stellarwp-migration-config-box__label" role="heading" aria-level="3"><?php 
esc_html_e('Applicable', 'stellarwp-migrations');
?></span>
			<span class="stellarwp-migration-config-box__value stellarwp-migration-config-box__value--<?php 
echo $is_applicable ? 'yes' : 'no';
?>">
				<?php 
echo $is_applicable ? esc_html__('Yes', 'stellarwp-migrations') : esc_html__('No', 'stellarwp-migrations');
?>
			</span>
		</div>

		<div class="stellarwp-migration-config-box__item">
			<span class="stellarwp-migration-config-box__label" role="heading" aria-level="3"><?php 
esc_html_e('Can Run', 'stellarwp-migrations');
?></span>
			<span class="stellarwp-migration-config-box__value stellarwp-migration-config-box__value--<?php 
echo $can_run ? 'yes' : 'no';
?>">
				<?php 
echo $can_run ? esc_html__('Yes', 'stellarwp-migrations') : esc_html__('No', 'stellarwp-migrations');
?>
			</span>
		</div>

		<?php 
if (!empty($tags)) {
    ?>
			<div class="stellarwp-migration-config-box__item stellarwp-migration-config-box__item--full">
				<span class="stellarwp-migration-config-box__label" role="heading" aria-level="3"><?php 
    esc_html_e('Tags', 'stellarwp-migrations');
    ?></span>
				<span class="stellarwp-migration-config-box__value">
					<?php 
    foreach ($tags as $migration_tag) {
        ?>
						<span class="stellarwp-migration-card__tag"><?php 
        echo esc_html($migration_tag);
        ?></span>
					<?php 
    }
    ?>
				</span>
			</div>
		<?php 
}
?>
	</div>
</div>
