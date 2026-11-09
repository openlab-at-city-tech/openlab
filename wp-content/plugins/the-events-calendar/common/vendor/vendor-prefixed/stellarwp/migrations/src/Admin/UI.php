<?php

/**
 * Admin UI.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Admin
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Migrations\Admin;

use TEC\Common\StellarWP\Migrations\Config;
use TEC\Common\StellarWP\Migrations\Registry;
use TEC\Common\StellarWP\Migrations\Contracts\Migration;
use TEC\Common\StellarWP\Migrations\Enums\Status;
use TEC\Common\StellarWP\Migrations\REST\Provider as REST_Provider;
use TEC\Common\StellarWP\Migrations\Tables\Migration_Executions;
use TEC\Common\StellarWP\Migrations\Utilities\Cast;
use TEC\Common\StellarWP\Migrations\Models\Execution;
/**
 * Admin UI.
 *
 * Provides methods to render the migrations admin interface.
 * Consumers call these methods to display the UI wherever they want.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Admin
 */
class UI
{
    /**
     * Additional query parameters to preserve in filter form.
     *
     * @since 0.0.1
     *
     * @var array<string,string|int|bool>
     */
    protected array $additional_params = [];
    /**
     * Render the migrations list page.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public function render_list(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        $container = Config::get_container();
        $container->get(Assets::class)->enqueue_assets();
        $filters = $this->parse_filters();
        $registry = $container->get(Registry::class);
        $all_migrations = $registry->all();
        $all_tags = array_map(static fn(Migration $migration) => $migration->get_tags(), $all_migrations);
        $all_tags = array_unique(array_merge(...array_values($all_tags)));
        sort($all_tags);
        $filtered_migrations = $this->get_filtered_migrations($filters);
        Config::get_template_engine()->template('list', ['additional_params' => $this->additional_params, 'all_tags' => $all_tags, 'filters' => $filters, 'migrations' => $filtered_migrations, 'rest_base_url' => rest_url(REST_Provider::get_namespace())]);
    }
    /**
     * Set additional query parameters to preserve in the filter form.
     *
     * Only string, int, and bool values are accepted. Any other types will trigger a _doing_it_wrong notice.
     *
     * @since 0.0.1
     *
     * @param array<string,string|int|bool> $params Additional query parameters. Only string, int, and bool values are accepted.
     *
     * @return void
     */
    public function set_additional_params(array $params): void
    {
        foreach ($params as $key => $value) {
            if (!is_string($value) && !is_int($value) && !is_bool($value)) {
                _doing_it_wrong(__METHOD__, sprintf('Additional parameter values must be string, int, or bool. %s given for key "%s".', esc_html(gettype($value)), esc_html($key)), '0.0.1');
                unset($params[$key]);
            }
        }
        $this->additional_params = $params;
    }
    /**
     * Render the single migration detail page.
     *
     * @since 0.0.1
     *
     * @param string $migration_id The migration ID to display.
     *
     * @return void
     */
    public function render_single(string $migration_id): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        $container = Config::get_container();
        $container->get(Assets::class)->enqueue_assets();
        $registry = $container->get(Registry::class);
        $migration = $registry->get($migration_id);
        if (!$migration) {
            Config::get_template_engine()->template('single-not-found', ['migration_id' => $migration_id]);
            return;
        }
        $executions = $this->get_migration_executions($migration_id);
        Config::get_template_engine()->template('single', ['migration' => $migration, 'executions' => $executions, 'rest_base_url' => rest_url(REST_Provider::get_namespace()), 'list_url' => Provider::get_list_url()]);
    }
    /**
     * Get executions for a migration.
     *
     * @since 0.0.1
     *
     * @param string $migration_id The migration ID.
     *
     * @return array<string, Execution> List of execution records.
     */
    private function get_migration_executions(string $migration_id): array
    {
        $prefix = Config::get_hook_prefix();
        /**
         * Filter the order by clause for the executions.
         *
         * @since 0.0.1
         *
         * @param string $order_by The order by clause. Default `created_at DESC`.
         *
         * @return string The order by clause.
         */
        $order_by = Cast::to_string(apply_filters("stellarwp_migrations_{$prefix}_executions_order_by", 'created_at DESC'));
        /**
         * Filter the limit for the executions.
         *
         * @since 0.0.1
         *
         * @param int $limit The limit. Default 100.
         *
         * @return int The limit.
         */
        $limit = Cast::to_int(apply_filters("stellarwp_migrations_{$prefix}_executions_limit", 100));
        return Migration_Executions::get_all_by('migration_id', $migration_id, '=', $limit, $order_by);
    }
    /**
     * Parse filters from request or provided array.
     *
     * @since 0.0.1
     *
     * @return array{tags: string[], show_completed: bool} Parsed filters.
     */
    private function parse_filters(): array
    {
        $tags = $this->parse_tags_filter();
        $prefix = Config::get_hook_prefix();
        /**
         * Filter the filters for the migrations list.
         *
         * @since 0.0.1
         *
         * @param array{tags: string[], show_completed: bool} $filters Filters to apply.
         *
         * @return array{tags: string[], show_completed: bool} Filters to apply.
         */
        return apply_filters("stellarwp_migrations_{$prefix}_filters", ['tags' => $tags, 'show_completed' => filter_input(INPUT_GET, 'show_completed', FILTER_SANITIZE_NUMBER_INT) !== '0']);
    }
    /**
     * Parse the tags filter from the request.
     *
     * Supports both array format (tags[]=foo&tags[]=bar) and comma-separated string (tags=foo,bar).
     *
     * @since 0.0.1
     *
     * @return string[] Array of sanitized tag strings.
     */
    private function parse_tags_filter(): array
    {
        // Try to get tags as an array first.
        $raw_tags = filter_input(INPUT_GET, 'tags', FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
        if (is_array($raw_tags)) {
            $tags = array_map(static fn($tag): string => is_string($tag) ? sanitize_text_field($tag) : '', $raw_tags);
            return array_filter($tags);
        }
        // Try to get tags as a string (may be comma-separated).
        $raw_tags = filter_input(INPUT_GET, 'tags', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!is_string($raw_tags) || '' === $raw_tags) {
            return [];
        }
        // Handle comma-separated string.
        if (strpos($raw_tags, ',') !== false) {
            return array_map('sanitize_text_field', explode(',', $raw_tags));
        }
        // Single tag value.
        return [sanitize_text_field($raw_tags)];
    }
    /**
     * Apply filters to migrations.
     *
     * @since 0.0.1
     *
     * @param array{tags: string[], show_completed: bool} $filters Filters to apply.
     *
     * @return list<Migration> Filtered migrations.
     */
    private function get_filtered_migrations(array $filters): array
    {
        $show_completed = !empty($filters['show_completed']);
        $filter_tags = !empty($filters['tags']) ? (array) $filters['tags'] : [];
        $registry = Config::get_container()->get(Registry::class);
        $migrations = $registry->filter(static function (Migration $migration) use ($show_completed, $filter_tags): bool {
            if ($migration->get_status()->equals(Status::NOT_APPLICABLE())) {
                return false;
            }
            if (!$show_completed && $migration->get_status()->equals(Status::COMPLETED())) {
                return false;
            }
            if (!empty($filter_tags)) {
                if (empty(array_intersect($filter_tags, $migration->get_tags()))) {
                    return false;
                }
            }
            return true;
        })->all();
        $prefix = Config::get_hook_prefix();
        /**
         * Filter the filtered migrations.
         *
         * @since 0.0.1
         *
         * @param list<Migration>                             $migrations Migrations to filter.
         * @param array{tags: string[], show_completed: bool} $filters    Filters to apply.
         *
         * @return list<Migration> Filtered migrations.
         */
        return apply_filters("stellarwp_migrations_{$prefix}_filtered_migrations", $this->sort_migrations($migrations), $filters);
    }
    /**
     * Sort migrations by status priority and then by latest execution date.
     *
     * @since 0.0.1
     *
     * @param array<string,Migration> $migrations Migrations to sort.
     *
     * @return list<Migration> Sorted migrations.
     */
    private function sort_migrations(array $migrations): array
    {
        $status_priority = $this->get_status_priority();
        usort($migrations, static function (Migration $a, Migration $b) use ($status_priority): int {
            $status_a = $a->get_status()->getValue();
            $status_b = $b->get_status()->getValue();
            $priority_a = $status_priority[$status_a] ?? 999;
            $priority_b = $status_priority[$status_b] ?? 999;
            // First sort by status priority.
            if ($priority_a !== $priority_b) {
                return $priority_a <=> $priority_b;
            }
            // Then sort by latest execution date (newest first).
            $execution_a = $a->get_latest_execution();
            $execution_b = $b->get_latest_execution();
            $date_a = $execution_a ? $execution_a->get_created_at() : null;
            $date_b = $execution_b ? $execution_b->get_created_at() : null;
            // Migrations with executions come before those without.
            if ($date_a === null && $date_b === null) {
                return 0;
            }
            if ($date_a === null) {
                return 1;
            }
            if ($date_b === null) {
                return -1;
            }
            // Newest first (descending order).
            return $date_b <=> $date_a;
        });
        $prefix = Config::get_hook_prefix();
        /**
         * Filter the sorted migrations.
         *
         * @since 0.0.1
         *
         * @param list<Migration>   $migrations      Sorted migrations.
         * @param array<string,int> $status_priority Status priority map.
         *
         * @return list<Migration> Sorted migrations.
         */
        return apply_filters("stellarwp_migrations_{$prefix}_sorted_migrations", $migrations, $status_priority);
    }
    /**
     * Get status priority map for sorting.
     *
     * Lower numbers have higher priority (appear first).
     *
     * @since 0.0.1
     *
     * @return array<string,int> Status value to priority map.
     */
    private function get_status_priority(): array
    {
        $prefix = Config::get_hook_prefix();
        /**
         * Filter the status priority map.
         *
         * @since 0.0.1
         *
         * @param array<string,int> $status_priority Status priority map.
         *
         * @return array<string,int> Status priority map.
         */
        return apply_filters("stellarwp_migrations_{$prefix}_status_priority", [Status::RUNNING()->getValue() => 1, Status::FAILED()->getValue() => 2, Status::PAUSED()->getValue() => 3, Status::PENDING()->getValue() => 4, Status::SCHEDULED()->getValue() => 5, Status::CANCELED()->getValue() => 6, Status::REVERTED()->getValue() => 7, Status::COMPLETED()->getValue() => 8]);
    }
}