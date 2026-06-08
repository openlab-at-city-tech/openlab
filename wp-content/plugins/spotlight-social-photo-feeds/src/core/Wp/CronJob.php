<?php

namespace RebelCode\Spotlight\Instagram\Wp;

/**
 * Represents a WordPress cron job.
 *
 * @since 0.1
 */
class CronJob
{
    /**
     * @since 0.1
     *
     * @var string
     */
    protected $hook;

    /**
     * @since 0.1
     *
     * @var array
     */
    protected $args;

    /**
     * @since 0.1
     *
     * @var string|null
     */
    protected $repeat;

    /**
     * @since 0.1
     *
     * @var callable[]
     */
    protected $handlers;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param string      $hook     The hook to trigger when the cron job is invoked.
     * @param array       $args     Optional arguments to pass to cron job handlers.
     * @param string|null $repeat   Optional repetition schedule. See {@link wp_get_schedules()}. If null, the cron job
     *                              will be scheduled for a one-time invocation only.
     * @param callable[]  $handlers Optional handlers to register with the cron job. See {@link CronJob::register()}.
     */
    public function __construct(string $hook, array $args = [], ?string $repeat = null, array $handlers = [])
    {
        $this->hook = $hook;
        $this->args = $args;
        $this->repeat = $repeat;
        $this->handlers = $handlers;
    }

    /**
     * Schedules a cron job.
     *
     * @since 0.1
     *
     * @param CronJob  $job  The job.
     * @param int|null $time The time at which to schedule the job, or null to run immediately.
     */
    public static function schedule(CronJob $job, ?int $time = null)
    {
        $time = $time ?? time();

        if ($job->repeat === null) {
            wp_schedule_single_event($time, $job->hook, $job->args);
        } else {
            wp_schedule_event($time, $job->repeat, $job->hook, $job->args);
        }
    }

    /**
     * Deschedules a job.
     *
     * Yes, "deschedule" is the correct term, not "unschedule". "De" prefixes mean to undo, while "Un" prefixes mean
     * "is not". In other words, "unscheduled" means it is not, or has not, been scheduled. And "unschedule" would mean
     * to make something that is scheduled never have been scheduled in the first place, which would require time
     * travel. :D
     *
     * @since 0.1
     *
     * @param CronJob $job The job to deschedule.
     */
    public static function deschedule(CronJob $job)
    {
        $scheduled = static::getScheduledEvent($job);

        if ($scheduled === false) {
            return;
        }

        wp_unschedule_event($scheduled->timestamp, $job->hook, $job->args);
    }

    /**
     * Checks if a cron job is scheduled.
     *
     * @since 0.1
     *
     * @param CronJob $job The job to check.
     *
     * @return bool True if the job is scheduled, false if not.
     */
    public static function isScheduled(CronJob $job) : bool
    {
        $next = wp_next_scheduled($job->hook, $job->args);

        return $next !== false;
    }

    /**
     * Retrieves the scheduled event for a cron job, if any.
     *
     * @since 0.1
     *
     * @param CronJob $job The cron job whose scheduled event to retrieve.
     *
     * @return object|false The event object. False if the event does not exist.
     */
    public static function getScheduledEvent(CronJob $job)
    {
        return wp_get_scheduled_event($job->hook, $job->args);
    }

    /**
     * Registers the handlers for a cron job.
     *
     * @param CronJob $job The cron job.
     */
    public static function registerHandlers(CronJob $job): void
    {
        foreach ($job->handlers as $handler) {
            add_action($job->hook, $handler);
        }
    }

    /**
     * Ensures that a cron job and its handlers are scheduled.
     *
     * Cron events will be rescheduled if the existing event's repetition schedule does not match the schedule of the
     * cron job given as argument.
     *
     * @since 0.1
     *
     * @param CronJob $job The cron job to register.
     */
    public static function register(CronJob $job)
    {
        // Cache for the WordPress schedules
        static $schedules = null;

        static::registerHandlers($job);

        // Get the existing event, if it exists
        $event = static::getScheduledEvent($job);
        $isScheduled = is_object($event);

        // Check if doing cron or if Crontrol is rescheduling a job
        $doingCron = !empty(filter_input(INPUT_GET, 'doing_wp_cron'));
        $fromCrontrol = filter_input(INPUT_GET, 'crontrol-single-event') === '1';

        // If an event is already scheduled with the same repetition, stop here
        // We also stop if currently running crons or if the Crontrol plugin is rescheduling a job
        if (($isScheduled && $event->schedule === $job->repeat) || ($doingCron || $fromCrontrol)) {
            return;
        }

        // Deschedule any existing event.
        static::deschedule($job);

        // Get the WordPress schedules if not already cached
        if ($schedules === null) {
            $schedules = wp_get_schedules();
        }

        $time = time();
        $time += ($schedules[$job->repeat]['interval'] ?? 0);

        static::schedule($job, $time);
    }
}
