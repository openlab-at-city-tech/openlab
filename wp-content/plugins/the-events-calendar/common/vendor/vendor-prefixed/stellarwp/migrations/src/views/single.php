<?php

/**
 * Single Migration Detail Template.
 *
 * @since 0.0.1
 * @version 0.0.1
 *
 * @package \StellarWP\Migrations
 *
 * @var TEC\Common\StellarWP\Migrations\Contracts\Migration        $migration     The migration object.
 * @var list<array<string,mixed>>                       $executions    List of execution records.
 * @var string                                          $rest_base_url REST API base URL.
 * @var string|null                                     $list_url      List URL.
 */
defined('ABSPATH') || exit;
use TEC\Common\StellarWP\Migrations\Config;
use TEC\Common\StellarWP\Migrations\Contracts\Migration;
if (!isset($migration) || !$migration instanceof Migration) {
    return;
}
$migration_id = $migration->get_id();
$migration_label = $migration->get_label();
$description = $migration->get_description();
$rest_base_url ??= '';
$executions ??= [];
$list_url ??= null;
$template = Config::get_template_engine();
?>
<div class="wrap stellarwp-migration-single" data-rest-url="<?php 
echo esc_url($rest_base_url);
?>" data-migration-id="<?php 
echo esc_attr($migration_id);
?>">
	<?php 
if (is_string($list_url)) {
    ?>
		<div class="stellarwp-migration-single__back">
			<a href="<?php 
    echo esc_url($list_url);
    ?>" class="stellarwp-migration-single__back-link" aria-label="<?php 
    esc_attr_e('Back to Migrations', 'stellarwp-migrations');
    ?>">
				<span class="dashicons dashicons-arrow-left-alt2"></span> <?php 
    esc_html_e('Migrations', 'stellarwp-migrations');
    ?>
			</a>
			<hr class="stellarwp-migrations-back-link-divider" />
		</div>
	<?php 
}
?>
	<header class="stellarwp-migration-single__header">
		<h1 class="stellarwp-migration-single__label"><?php 
echo esc_html($migration_label);
?></h1>
		<p class="stellarwp-migration-single__description"><?php 
echo esc_html($description);
?></p>
	</header>

	<section class="stellarwp-migration-single__section" aria-labelledby="stellarwp-migration-status-title">
		<h2 id="stellarwp-migration-status-title" class="stellarwp-migration-single__section-title"><?php 
esc_html_e('Status', 'stellarwp-migrations');
?></h2>
		<?php 
$template->template('components/migration-status-card', ['migration' => $migration, 'executions' => $executions]);
?>
	</section>

	<section class="stellarwp-migration-single__section" aria-labelledby="stellarwp-migration-config-title">
		<h2 id="stellarwp-migration-config-title" class="stellarwp-migration-single__section-title"><?php 
esc_html_e('Configuration', 'stellarwp-migrations');
?></h2>
		<?php 
$template->template('components/configuration-box', ['migration' => $migration]);
?>
	</section>

	<section class="stellarwp-migration-single__section" aria-labelledby="stellarwp-migration-logs-title">
		<h2 id="stellarwp-migration-logs-title" class="stellarwp-migration-single__section-title"><?php 
esc_html_e('Logs', 'stellarwp-migrations');
?></h2>
		<?php 
$template->template('components/execution-logs', ['migration' => $migration, 'executions' => $executions, 'rest_base_url' => $rest_base_url]);
?>
	</section>
</div>
