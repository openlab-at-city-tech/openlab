<?php

/**
 * Migration UI Utility.
 *
 * Centralizes UI logic for migration views (labels, icons, visibility).
 *
 * @since 0.0.1
 *
 * @package TEC\Common\StellarWP\Migrations\Utilities
 */
namespace TEC\Common\StellarWP\Migrations\Utilities;

use TEC\Common\StellarWP\Migrations\Contracts\Migration;
use TEC\Common\StellarWP\Migrations\Enums\Operation;
use TEC\Common\StellarWP\Migrations\Enums\Status;
use TEC\Common\StellarWP\Migrations\Models\Execution;
use TEC\Common\StellarWP\Migrations\Tables\Migration_Executions;
/**
 * Utility for migration admin UI (labels, icons, button visibility).
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Utilities
 */
class Migration_UI
{
    /**
     * The migration instance.
     *
     * @since 0.0.1
     *
     * @var Migration
     */
    private Migration $migration;
    /**
     * Constructor.
     *
     * @since 0.0.1
     *
     * @param Migration $migration Migration instance.
     */
    public function __construct(Migration $migration)
    {
        $this->migration = $migration;
    }
    /**
     * Returns the display status, used for display purposes only.
     *
     * @since 0.0.1
     *
     * @return Status The display status for the migration.
     */
    public function get_display_status(): Status
    {
        $status = $this->migration->get_status();
        // If the migration is pending and has no items, return NOT_APPLICABLE.
        if ($status->equals(Status::PENDING()) && $this->migration->get_total_items() === 0) {
            return Status::NOT_APPLICABLE();
        }
        // If the migration is reverted but the parent execution is failed, return FAILED.
        $parent_execution = $this->get_parent_execution();
        if ($status->equals(Status::REVERTED()) && null !== $parent_execution && $parent_execution->get_status()->equals(Status::FAILED())) {
            return Status::FAILED();
        }
        return $status;
    }
    /**
     * Returns the human-readable label for the display status.
     *
     * @since 0.0.1
     *
     * @return string Translated display status label.
     */
    public function get_display_status_label(): string
    {
        $parent_execution = $this->get_parent_execution();
        $default_label = $this->get_display_status()->get_label();
        if (null === $parent_execution) {
            // No parent execution, so we can use the regular label.
            return $default_label;
        }
        $migration_status = $this->migration->get_status();
        $parent_execution_status = $parent_execution->get_status();
        // Case of successful auto-revert.
        if ($migration_status->equals(Status::REVERTED()) && $parent_execution_status->equals(Status::FAILED())) {
            return $default_label . __(' (auto-reverted)', 'stellarwp-migrations');
        }
        // Case of failed auto-revert.
        if ($migration_status->equals(Status::FAILED()) && $parent_execution_status->equals(Status::FAILED())) {
            return $default_label . __(' (auto-revert failed)', 'stellarwp-migrations');
        }
        return $default_label;
    }
    /**
     * Returns the run action label for the migration.
     *
     * @since 0.0.1
     *
     * @return string Translated label.
     */
    public function get_run_action_label(): string
    {
        $status = $this->get_display_status();
        if ($status->equals(Status::COMPLETED()) || $status->equals(Status::REVERTED())) {
            return __('Re-run', 'stellarwp-migrations');
        }
        if ($status->equals(Status::FAILED())) {
            return __('Retry', 'stellarwp-migrations');
        }
        return __('Start', 'stellarwp-migrations');
    }
    /**
     * Whether the run button should be shown for the migration.
     *
     * @since 0.0.1
     *
     * @return bool True if run button should be shown.
     */
    public function show_run(): bool
    {
        $status_value = $this->migration->get_status()->getValue();
        $runnable_statuses = [Status::COMPLETED()->getValue(), Status::PENDING()->getValue(), Status::CANCELED()->getValue(), Status::FAILED()->getValue(), Status::REVERTED()->getValue()];
        return $this->migration->is_applicable() && $this->migration->can_run() && in_array($status_value, $runnable_statuses, true) && $this->migration->get_total_items() > 0;
    }
    /**
     * Whether the rollback button should be shown for the migration.
     *
     * @since 0.0.1
     *
     * @return bool True if rollback button should be shown.
     */
    public function show_rollback(): bool
    {
        $status_value = $this->migration->get_status()->getValue();
        $rollbackable_statuses = [Status::COMPLETED()->getValue(), Status::CANCELED()->getValue(), Status::FAILED()->getValue()];
        return $this->migration->is_applicable() && in_array($status_value, $rollbackable_statuses, true) && $this->migration->get_total_items(Operation::DOWN()) > 0;
    }
    /**
     * Get the parent execution for the migration.
     *
     * @since 0.0.1
     *
     * @return Execution|null The parent execution or null if not found.
     */
    private function get_parent_execution(): ?Execution
    {
        $latest_execution = $this->migration->get_latest_execution();
        if (null === $latest_execution || null === $latest_execution->get_parent_execution_id()) {
            return null;
        }
        return Migration_Executions::get_by_id($latest_execution->get_parent_execution_id());
    }
}