<?php

/**
 * Single Migration Not Found Template.
 *
 * @since 0.0.1
 * @version 0.0.1
 *
 * @package \StellarWP\Migrations
 *
 * @var string $migration_id The migration ID that was not found.
 */
defined('ABSPATH') || exit;
$migration_id ??= '';
?>
<div class="wrap stellarwp-migration-single">
	<div class="stellarwp-migration-not-found">
		<h1><?php 
esc_html_e('Migration Not Found', 'stellarwp-migrations');
?></h1>
		<p>
			<?php 
printf(
    /* translators: %s: migration ID */
    esc_html__('The migration with ID "%s" could not be found.', 'stellarwp-migrations'),
    esc_html($migration_id)
);
?>
		</p>
	</div>
</div>
