<?php

/**
 * Migrations List Template.
 *
 * @since 0.0.1
 * @version 0.0.1
 *
 * @package \StellarWP\Migrations
 *
 * @var list<TEC\Common\StellarWP\Migrations\Contracts\Migration>                             $migrations         List of migrations objects.
 * @var list<string>                                                               $all_tags           All available tags.
 * @var array{tags: list<string>, show_completed: bool} $filters            Current filter values.
 * @var string                                                                     $rest_base_url      REST API base URL.
 * @var array<string,string|int|bool>                                              $additional_params  Additional query parameters to preserve.
 */
defined('ABSPATH') || exit;
use TEC\Common\StellarWP\Migrations\Config;
use TEC\Common\StellarWP\Migrations\Utilities\Cast;
$migrations ??= [];
$all_tags ??= [];
$filters ??= [];
$rest_base_url ??= '';
$additional_params ??= [];
$template = Config::get_template_engine();
$current_page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
?>
<div class="wrap">
	<form method="get" class="stellarwp-migrations-filters">
		<?php 
if (is_string($current_page) && '' !== $current_page) {
    ?>
			<input type="hidden" name="page" value="<?php 
    echo esc_attr(sanitize_text_field($current_page));
    ?>" />
		<?php 
}
?>

		<?php 
foreach ($additional_params as $key => $value) {
    ?>
			<input type="hidden" name="<?php 
    echo esc_attr($key);
    ?>" value="<?php 
    echo esc_attr(Cast::to_string($value));
    ?>" />
		<?php 
}
?>

		<div class="stellarwp-migrations-filters__row">
			<?php 
if (!empty($all_tags)) {
    ?>
				<div class="stellarwp-migrations-filters__field stellarwp-migrations-filters__field--tags">
					<select
						id="stellarwp-migrations-tags"
						name="tags[]"
						multiple="multiple"
						class="stellarwp-migrations-select2"
						data-placeholder="<?php 
    esc_attr_e('Filter by tags...', 'stellarwp-migrations');
    ?>"
					>
						<?php 
    foreach ($all_tags as $filter_tag) {
        ?>
							<option value="<?php 
        echo esc_attr($filter_tag);
        ?>" <?php 
        selected(in_array($filter_tag, $filters['tags'], true));
        ?>>
								<?php 
        echo esc_html($filter_tag);
        ?>
							</option>
						<?php 
    }
    ?>
					</select>
				</div>
			<?php 
}
?>

			<div class="stellarwp-migrations-filters__actions">
				<button type="submit" class="button button-primary">
					<?php 
esc_html_e('Filter', 'stellarwp-migrations');
?>
				</button>
				<a href="<?php 
echo esc_url(remove_query_arg(['tags', 'show_completed']));
?>" class="button button-secondary">
					<?php 
esc_html_e('Reset', 'stellarwp-migrations');
?>
				</a>
			</div>

			<div class="stellarwp-migrations-filters__checkboxes">
				<input type="hidden" name="show_completed" value="0" />
				<label>
					<input
						type="checkbox"
						name="show_completed"
						value="1"
						<?php 
checked($filters['show_completed']);
?>
						onchange="this.form.submit();"
					/>
					<?php 
esc_html_e('Show Completed', 'stellarwp-migrations');
?>
				</label>
			</div>
		</div>
	</form>

	<div class="stellarwp-migrations-list" data-rest-url="<?php 
echo esc_url($rest_base_url);
?>">
		<?php 
if (empty($migrations)) {
    ?>
			<div class="stellarwp-migrations-empty">
				<p><?php 
    esc_html_e('No migrations to display.', 'stellarwp-migrations');
    ?></p>
			</div>
		<?php 
} else {
    ?>
			<?php 
    foreach ($migrations as $migration) {
        ?>
				<?php 
        $template->template('components/migration-card', ['migration' => $migration]);
        ?>
			<?php 
    }
    ?>
		<?php 
}
?>
	</div>
</div>
