<?php

/**
 * Shepherd's regulator.
 *
 * @since 0.0.1
 *
 * @package \StellarWP\Shepherd
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Shepherd;

use TEC\Common\StellarWP\Shepherd\Abstracts\Provider_Abstract;
use TEC\Common\StellarWP\ContainerContract\ContainerInterface as Container;
use TEC\Common\StellarWP\Shepherd\Contracts\Task;
use TEC\Common\StellarWP\Shepherd\Tables\Tasks as Tasks_Table;
use RuntimeException;
use Throwable;
use TEC\Common\StellarWP\DB\DB;
use TEC\Common\StellarWP\Shepherd\Exceptions\ShepherdTaskException;
use TEC\Common\StellarWP\Shepherd\Exceptions\ShepherdTaskAlreadyExistsException;
use TEC\Common\StellarWP\Shepherd\Exceptions\ShepherdTaskFailWithoutRetryException;
use TEC\Common\StellarWP\Shepherd\Traits\Loggable;
use TEC\Common\StellarWP\Shepherd\Tasks\Herding;
use ActionScheduler_QueueRunner;
use WP_Object_Cache;
/**
 * Shepherd's regulator.
 *
 * @since 0.0.1
 *
 * @package \StellarWP\Shepherd
 */
class Regulator extends Provider_Abstract
{
    use Loggable;
    /**
     * The process task hook.
     *
     * @since 0.0.1
     *
     * @var string
     */
    protected string $process_task_hook = 'shepherd_%s_process_task';
    /**
     * The action ID being processed.
     *
     * @since 0.0.1
     *
     * @var int
     */
    protected int $current_action_id = 0;
    /**
     * The scheduled tasks.
     *
     * @since 0.0.1
     *
     * @var array
     */
    protected array $scheduled_tasks = [];
    /**
     * The tasks that failed to be processed.
     *
     * This is used to track tasks that failed to be processed so that they can be retried.
     *
     * @since 0.0.1
     *
     * @var Task[]
     */
    protected array $failed_tasks = [];
    /**
     * The regulator's constructor.
     *
     * @since 0.0.1
     *
     * @param Container $container The container.
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->process_task_hook = sprintf($this->process_task_hook, Config::get_hook_prefix());
    }
    /**
     * Registers the regulator.
     *
     * @since 0.0.1
     * @since 0.0.7 Updated to use the `wp_loaded` hook instead of the `init` hook to schedule the cleanup task.
     */
    public function register(): void
    {
        add_action($this->process_task_hook, [$this, 'process_task']);
        add_action('action_scheduler_before_execute', [$this, 'track_current_action'], 1, 1);
        add_action('action_scheduler_after_execute', [$this, 'untrack_action'], 1, 0);
        add_action('action_scheduler_execution_ignored', [$this, 'untrack_action'], 1, 0);
        add_action('action_scheduler_failed_execution', [$this, 'untrack_action'], 1, 0);
        add_action('action_scheduler_after_process_queue', [$this, 'handle_reschedule_of_failed_task'], 1, 0);
        add_action('wp_loaded', [$this, 'schedule_cleanup_task'], 20, 0);
    }
    /**
     * Handles the rescheduling of a failed task.
     *
     * @since 0.0.1
     */
    public function handle_reschedule_of_failed_task(): void
    {
        if (empty($this->failed_tasks)) {
            return;
        }
        foreach ($this->failed_tasks as $offset => $task) {
            $this->dispatch($task, $task->get_retry_delay());
            unset($this->failed_tasks[$offset]);
        }
    }
    /**
     * Track specified action.
     *
     * @since 0.0.1
     *
     * @param int $action_id Action ID to track.
     */
    public function track_current_action(int $action_id): void
    {
        $this->current_action_id = $action_id;
    }
    /**
     * Un-track action.
     *
     * @since 0.0.1
     */
    public function untrack_action(): void
    {
        $this->current_action_id = 0;
    }
    /**
     * Dispatches a task to be processed later.
     *
     * @since 0.0.1
     * @since 0.0.7 Updated to check if the Shepherd tables have been registered already.
     * @since 0.0.7 Updated to use the `action_scheduler_init` hook instead of the `init` hook to check if Action Scheduler is initialized.
     * @since 0.0.8 Updated to use the delay to determine if the task should be dispatched synchronously.
     * @since 0.0.9 Added a filter `shepherd_{prefix}_dispatch_handler` to allow for custom dispatch handlers.
     *
     * @param Task $task  The task to dispatch.
     * @param int  $delay The delay in seconds before the task is processed.
     *
     * @return self The regulator instance.
     */
    public function dispatch(Task $task, int $delay = 0): self
    {
        $prefix = Config::get_hook_prefix();
        /**
         * Filters the dispatch handler.
         *
         * @since TBD
         *
         * @param callable|null $handler The dispatch handler.
         * @param Task          $task    The task to dispatch.
         * @param int           $delay   The delay in seconds before the task is processed.
         */
        $handler = apply_filters("shepherd_{$prefix}_dispatch_handler", null, $task, $delay);
        if (null !== $handler && is_callable($handler)) {
            try {
                $handler($task, $delay);
            } catch (Throwable $e) {
                /**
                 * Documented in the dispatch_callback method.
                 */
                do_action('shepherd_' . Config::get_hook_prefix() . '_task_scheduling_failed', $task, new RuntimeException($e->getMessage(), $e->getCode(), $e));
            }
            return $this;
        }
        if (!did_action("shepherd_{$prefix}_tables_registered")) {
            /**
             * Filters whether to dispatch a task synchronously.
             *
             * @since 0.0.7
             * @since 0.0.8 Updated to be true by default only when there should be no delay.
             *
             * @param bool $should_dispatch_sync Whether to dispatch a task synchronously.
             * @param Task $task                 The task that should be dispatched synchronously.
             */
            if (!apply_filters("shepherd_{$prefix}_should_dispatch_sync_on_tables_unavailable", 0 === $delay, $task)) {
                return $this;
            }
            // Process the task immediately if the tables are not registered.
            $task->process();
            /**
             * Fires an action when a task is dispatched synchronously.
             *
             * @since 0.0.7
             *
             * @param Task $task The task that was dispatched synchronously.
             */
            do_action("shepherd_{$prefix}_dispatched_sync", $task);
            return $this;
        }
        if (did_action('action_scheduler_init') || doing_action('action_scheduler_init')) {
            $this->dispatch_callback($task, $delay);
            return $this;
        }
        add_action('action_scheduler_init', function () use ($task, $delay): void {
            $this->dispatch_callback($task, $delay);
        }, 10);
        return $this;
    }
    /**
     * Dispatches a task to be processed later.
     *
     * @since 0.0.1
     * @since 0.0.8 Made strings translatable.
     *
     * @param Task $task  The task to dispatch.
     * @param int  $delay The delay in seconds before the task is processed.
     *
     * @throws RuntimeException                 If the task fails to be scheduled or inserted into the database.
     * @throws ShepherdTaskAlreadyExistsException If the task is already scheduled.
     */
    protected function dispatch_callback(Task $task, int $delay): void
    {
        $group = $task->get_group();
        $args_hash = $task->get_args_hash();
        try {
            DB::beginTransaction();
            if (Action_Scheduler_Methods::has_scheduled_action($this->process_task_hook, [$args_hash], $group)) {
                throw new ShepherdTaskAlreadyExistsException(esc_html_x('The task is already scheduled.', 'This error is thrown when a task is already scheduled.', 'stellarwp-shepherd'));
            }
            $previous_action_id = $task->get_action_id();
            $action_id = Action_Scheduler_Methods::schedule_single_action(time() + $delay, $this->process_task_hook, [$args_hash], $group, false, $task->get_priority());
            if (!$action_id) {
                throw new RuntimeException(esc_html_x('Failed to schedule the task.', 'This error is thrown when a task fails to be scheduled.', 'stellarwp-shepherd'));
            }
            $task->set_action_id($action_id);
            $this->scheduled_tasks[] = $task->save();
            $log_data = ['action_id' => $action_id, 'current_try' => $task->get_current_try()];
            if ($previous_action_id) {
                /**
                 * Fires when a task should be retried.
                 *
                 * @since 0.0.1
                 *
                 * @param Task $task The task that should be retried.
                 */
                do_action('shepherd_' . Config::get_hook_prefix() . '_task_rescheduled', $task);
                $this->log_rescheduled($task->get_id(), array_merge($log_data, ['previous_action_id' => $previous_action_id]));
            } else {
                /**
                 * Fires when a task should be retried.
                 *
                 * @since 0.0.1
                 *
                 * @param Task $task The task that should be retried.
                 */
                do_action('shepherd_' . Config::get_hook_prefix() . '_task_created', $task);
                $this->log_created($task->get_id(), $log_data);
            }
            DB::commit();
        } catch (RuntimeException $e) {
            DB::rollback();
            /**
             * Fires when a task fails to be scheduled or inserted into the database.
             *
             * @since 0.0.1
             *
             * @param Task             $task The task that failed to be scheduled or inserted into the database.
             * @param RuntimeException $e    The exception that was thrown.
             */
            do_action('shepherd_' . Config::get_hook_prefix() . '_task_scheduling_failed', $task, $e);
        } catch (ShepherdTaskAlreadyExistsException $e) {
            DB::rollback();
            /**
             * Fires when a task is already scheduled.
             *
             * @since 0.0.1
             *
             * @param Task $task The task that is already scheduled.
             */
            do_action('shepherd_' . Config::get_hook_prefix() . '_task_already_scheduled', $task);
        }
    }
    /**
     * Run a set of tasks.
     *
     * @since 0.1.0
     *
     * @param Task[] $tasks     The tasks to run.
     * @param array  $callables The callables to run.
     *
     * @phpstan-param array{} | array{
     *  before: callable( Task $task ): void,
     *  after: callable( Task $task ): void,
     *  always: callable( list<Task> $tasks ): void,
     * } $callables
     *
     * @return void
     */
    public function run(array $tasks, array $callables = []): void
    {
        $prefix = Config::get_hook_prefix();
        if (!did_action("shepherd_{$prefix}_tables_registered")) {
            foreach ($tasks as $task) {
                $task->process();
                /**
                 * Fires an action when a task is run synchronously.
                 *
                 * @since 0.1.0
                 *
                 * @param Task $task The task that was dispatched synchronously.
                 */
                do_action("shepherd_{$prefix}_task_run_sync", $task);
            }
            return;
        }
        if (did_action('action_scheduler_init') || doing_action('action_scheduler_init')) {
            $this->run_callback($tasks, $callables);
            return;
        }
        add_action('action_scheduler_init', function () use ($tasks, $callables): void {
            $this->run_callback($tasks, $callables);
        }, 10);
    }
    /**
     * Runs a set of tasks.
     *
     * @since 0.1.0
     *
     * @param Task[] $tasks     The tasks to run.
     * @param array  $callables The callables to run.
     *
     * @phpstan-param array{} | array{
     *  before: callable( Task $task ): void,
     *  after: callable( Task $task ): void,
     *  always: callable( list<Task> $tasks ): void,
     * } $callables
     *
     * @return void
     */
    private function run_callback(array $tasks, array $callables = []): void
    {
        $callables = wp_parse_args($callables, ['before' => static function (Task $task): void {
        }, 'after' => static function (Task $task): void {
        }, 'always' => static function (array $tasks): void {
        }]);
        $context = defined('WP_CLI') && WP_CLI ? ' CLI' : '';
        $context = !$context && defined('REST_REQUEST') && REST_REQUEST ? ' REST' : $context;
        $prefix = Config::get_hook_prefix();
        $runner = ActionScheduler_QueueRunner::instance();
        try {
            /**
             * Filters the number of tasks to clean up after.
             *
             * @since 0.1.0
             *
             * @param int $clean_up_memory_every The number of tasks to clean up the memory after.
             *
             * @return int The number of tasks to clean up the memory after.
             */
            $clean_up_memory_every = (int) apply_filters("shepherd_{$prefix}_clean_up_memory_every", 10);
            foreach (array_values($tasks) as $offset => $task) {
                if (!in_array($task->get_id(), $this->scheduled_tasks, true)) {
                    $this->dispatch_callback($task, 0);
                }
                if (is_callable($callables['before'])) {
                    $callables['before']($task);
                }
                /**
                 * Fires when a task is about to be run.
                 *
                 * @since 0.1.0
                 *
                 * @param Task $task The task that is about to be run.
                 */
                do_action("shepherd_{$prefix}_task_before_run", $task);
                $runner->process_action($task->get_action_id(), "Shepherd{$context}");
                if (is_callable($callables['after'])) {
                    $callables['after']($task);
                }
                /**
                 * Fires when a task is finished running.
                 *
                 * @since 0.1.0
                 *
                 * @param Task $task The task that is finished running.
                 */
                do_action("shepherd_{$prefix}_task_after_run", $task);
                if (0 === ($offset + 1) % $clean_up_memory_every) {
                    $this->free_memory();
                }
            }
            if (is_callable($callables['always'])) {
                $callables['always']($tasks);
            }
            /**
             * Fires when a set of tasks is finished running.
             *
             * @since 0.1.0
             *
             * @param Task[] $tasks The tasks that were run.
             */
            do_action("shepherd_{$prefix}_tasks_finished", $tasks);
        } catch (Throwable $e) {
            /**
             * Fires when a set of tasks fails to be run.
             *
             * @since 0.1.0
             *
             * @param Task[] $tasks The tasks that failed to be run.
             * @param Throwable $e The exception that was thrown.
             */
            do_action("shepherd_{$prefix}_tasks_run_failed", $tasks, $e);
        }
    }
    /**
     * Gets the last scheduled task ID.
     *
     * @since 0.0.1
     *
     * @return ?int The last scheduled task ID.
     */
    public function get_last_scheduled_task_id(): ?int
    {
        return empty($this->scheduled_tasks) ? null : end($this->scheduled_tasks);
    }
    /**
     * Gets the process task hook.
     *
     * @since 0.0.1
     *
     * @return string The process task hook.
     */
    public function get_hook(): string
    {
        return $this->process_task_hook;
    }
    /**
     * Busts the runtime cached tasks.
     *
     * @since 0.0.1
     */
    public function bust_runtime_cached_tasks(): void
    {
        $this->scheduled_tasks = [];
    }
    /**
     * Processes a task.
     *
     * @since 0.0.1
     * @since 0.0.8 Made strings translatable.
     *
     * @param string $args_hash The arguments hash.
     *
     * @throws RuntimeException                      If no action ID is found, no Shepherd task is found with the action ID, or the task arguments hash does not match the expected hash.
     * @throws ShepherdTaskException                 If the task fails to be processed.
     * @throws ShepherdTaskFailWithoutRetryException If the task fails to be processed without retry.
     * @throws Throwable                             If the task fails to be processed.
     */
    public function process_task(string $args_hash): void
    {
        $task = null;
        if (!$this->current_action_id) {
            $task = Tasks_Table::get_by_args_hash($args_hash);
            if (!$task) {
                // translators: %s is the arguments hash.
                throw new RuntimeException(sprintf(esc_html_x('No Shepherd task found with args hash %s.', 'This error is thrown when a task is not found with the arguments hash.', 'stellarwp-shepherd'), $args_hash));
            }
            $task = array_shift($task);
        }
        $task ??= Tasks_Table::get_by_action_id($this->current_action_id);
        if (!$task) {
            // translators: %d is the action ID.
            throw new RuntimeException(sprintf(esc_html_x('No Shepherd task found with action ID %d.', 'This error is thrown when a task is not found with the action ID.', 'stellarwp-shepherd'), $this->current_action_id));
        }
        $log_data = ['action_id' => $this->current_action_id, 'current_try' => $task->get_current_try()];
        /**
         * Fires when a task is being processed.
         *
         * @since 0.0.1
         *
         * @param Task $task          The task that is being processed.
         * @param int  $action_id     The action ID that is being processed.
         */
        do_action('shepherd_' . Config::get_hook_prefix() . '_task_started', $task, $this->current_action_id);
        try {
            try {
                if ($task->get_current_try() > 0) {
                    $this->log_retrying($task->get_id(), $log_data);
                } else {
                    $this->log_starting($task->get_id(), $log_data);
                }
                $task->process();
                $this->log_finished($task->get_id(), $log_data);
            } catch (ShepherdTaskException $e) {
                throw $e;
            }
        } catch (ShepherdTaskFailWithoutRetryException $e) {
            /**
             * Fires when a task fails to be processed without retry.
             *
             * @since 0.0.1
             *
             * @param Task                                $task The task that failed to be processed without retry.
             * @param ShepherdTaskFailWithoutRetryException $e    The exception that was thrown.
             */
            do_action('shepherd_' . Config::get_hook_prefix() . '_task_failed_without_retry', $task, $e);
            /**
             * Fires when a task fails to be processed without retry.
             *
             * @since 0.0.1
             *
             * @param Task                                $task The task that failed to be processed without retry.
             * @param ShepherdTaskFailWithoutRetryException $e    The exception that was thrown.
             */
            do_action('shepherd_' . Config::get_hook_prefix() . '_task_failed_without_retry', $task, $e);
            $this->log_failed($task->get_id(), array_merge($log_data, ['exception' => $e->getMessage()]));
            throw $e;
        } catch (Throwable $e) {
            /**
             * Fires when a task fails to be processed.
             *
             * @since 0.0.1
             *
             * @param Task      $task The task that failed to be processed.
             * @param Throwable $e    The exception that was thrown.
             */
            do_action('shepherd_' . Config::get_hook_prefix() . '_task_failed', $task, $e);
            if ($this->should_retry($task)) {
                throw new ShepherdTaskException(esc_html_x('The task failed, but will be retried.', 'This error is thrown when a task fails to be processed, but will be retried.', 'stellarwp-shepherd'));
            }
            $this->log_failed($task->get_id(), array_merge($log_data, ['exception' => $e->getMessage()]));
            throw $e;
        }
        /**
         * Fires when a task is finished processing.
         *
         * @since 0.0.1
         *
         * @param Task $task          The task that is finished processing.
         * @param int  $action_id     The action ID that is finished processing.
         */
        do_action('shepherd_' . Config::get_hook_prefix() . '_task_finished', $task, $this->current_action_id);
    }
    /**
     * Determines if the task should be retried.
     *
     * @since 0.0.1
     *
     * @param Task $task The task.
     * @return bool Whether the task should be retried.
     */
    protected function should_retry(Task $task): bool
    {
        if (0 === $task->get_max_retries()) {
            return false;
        }
        if ($task->get_current_try() >= $task->get_max_retries()) {
            return false;
        }
        $task->set_current_try($task->get_current_try() + 1);
        $this->failed_tasks[] = $task;
        return true;
    }
    /**
     * Schedules the cleanup task.
     *
     * @since 0.0.1
     * @since 0.0.8 Updated to check if the Shepherd tables have been registered before scheduling the cleanup task.
     */
    public function schedule_cleanup_task(): void
    {
        $prefix = Config::get_hook_prefix();
        if (!did_action("shepherd_{$prefix}_tables_registered")) {
            return;
        }
        /**
         * Filters whether to schedule the cleanup task.
         *
         * @since 0.0.8
         *
         * @param int $schedule_every_x_time The time in seconds to schedule the cleanup task. Default is 12 hours.
         */
        $schedule_every_x_time = (int) apply_filters("shepherd_{$prefix}_schedule_cleanup_task_every", 12 * HOUR_IN_SECONDS);
        if (0 === $schedule_every_x_time) {
            return;
        }
        $this->dispatch(new Herding(), $schedule_every_x_time);
        /**
         * Fires when the cleanup task is scheduled.
         *
         * @since 0.0.8
         */
        do_action('shepherd_' . Config::get_hook_prefix() . '_cleanup_task_scheduled');
    }
    /**
     * Reduce memory footprint by clearing the database query and object caches.
     *
     * @since 0.1.0
     *
     * @return void
     */
    private function free_memory(): void
    {
        /**
         * Globals.
         *
         * @var \wpdb           $wpdb
         * @var WP_Object_Cache $wp_object_cache
         */
        global $wpdb, $wp_object_cache;
        $wpdb->queries = [];
        if (!$wp_object_cache instanceof WP_Object_Cache) {
            return;
        }
        // Not all drop-ins support these props, however, there may be existing installations that rely on these being cleared.
        if (property_exists($wp_object_cache, 'group_ops')) {
            $wp_object_cache->group_ops = [];
        }
        if (property_exists($wp_object_cache, 'stats')) {
            $wp_object_cache->stats = [];
        }
        if (property_exists($wp_object_cache, 'memcache_debug')) {
            $wp_object_cache->memcache_debug = [];
        }
        if (property_exists($wp_object_cache, 'cache')) {
            $wp_object_cache->cache = [];
        }
        if (is_callable([$wp_object_cache, '__remoteset'])) {
            call_user_func([$wp_object_cache, '__remoteset']);
            // important!
        }
    }
}