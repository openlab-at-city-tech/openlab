<?php

/**
 * Migration Card Component Template.
 *
 * @since 0.0.1
 * @version 0.0.1
 *
 * @package \StellarWP\Migrations
 *
 * @var TEC\Common\StellarWP\Migrations\Contracts\Migration $migration Migration object.
 */
defined('ABSPATH') || exit;
use TEC\Common\StellarWP\Migrations\Admin\Provider as Admin_Provider;
use TEC\Common\StellarWP\Migrations\Config;
use TEC\Common\StellarWP\Migrations\Contracts\Migration;
use TEC\Common\StellarWP\Migrations\Utilities\Migration_UI;
if (!isset($migration) || !$migration instanceof Migration) {
    return;
}
$migration_ui = new Migration_UI($migration);
$migration_id = $migration->get_id();
$single_url = Admin_Provider::get_single_url($migration_id);
$migration_label = $migration->get_label();
$description = $migration->get_description();
$migration_tags = $migration->get_tags();
$template = Config::get_template_engine();
?>
<div class="stellarwp-migration-card" data-migration-id="<?php 
echo esc_attr($migration_id);
?>">
	<div class="stellarwp-migration-card__header">
		<h3 class="stellarwp-migration-card__label">
			<a href="<?php 
echo esc_url($single_url);
?>"><?php 
echo esc_html($migration_label);
?></a>
		</h3>
		<?php 
if (!empty($migration_tags)) {
    ?>
			<div class="stellarwp-migration-card__tags">
				<?php 
    foreach ($migration_tags as $migration_tag) {
        ?>
					<span class="stellarwp-migration-card__tag"><?php 
        echo esc_html($migration_tag);
        ?></span>
				<?php 
    }
    ?>
			</div>
		<?php 
}
?>
	</div>

	<p class="stellarwp-migration-card__description">
		<?php 
echo esc_html($description);
?>
	</p>

	<a href="<?php 
echo esc_url($single_url);
?>" class="stellarwp-migration-card__details-link">
		<?php 
esc_html_e('View Details', 'stellarwp-migrations');
?> &rarr;
	</a>

	<hr class="stellarwp-migration-card__separator" />

	<div class="stellarwp-migration-card__footer">
		<?php 
$template->template('components/migration-status/' . $migration_ui->get_display_status()->getValue(), ['migration' => $migration, 'migration_ui' => $migration_ui]);
$template->template('components/migration-actions', ['migration' => $migration, 'migration_ui' => $migration_ui]);
?>
	</div>

	<div class="stellarwp-migration-card__message" style="display: none;"></div>
</div>
