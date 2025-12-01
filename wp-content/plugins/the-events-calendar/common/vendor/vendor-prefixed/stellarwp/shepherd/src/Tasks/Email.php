<?php

/**
 * Shepherd's email task.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Tasks;
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Shepherd\Tasks;

use TEC\Common\StellarWP\Shepherd\Config;
use TEC\Common\StellarWP\Shepherd\Abstracts\Task_Abstract;
use TEC\Common\StellarWP\Shepherd\Exceptions\ShepherdTaskException;
use InvalidArgumentException;
// phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod.Found
/**
 * Shepherd's email task.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Tasks;
 */
class Email extends Task_Abstract
{
    /**
     * The email task's constructor.
     *
     * @since 0.0.1
     * @since TBD - Allow multiple comma-separated recipients.
     *
     * @param string   $to_email    The email address(es) to send the email to. Can be comma-separated for multiple recipients.
     * @param string   $subject     The email subject.
     * @param string   $body        The email body.
     * @param string[] $headers     Optional. Additional headers.
     * @param string[] $attachments Optional. Paths to files to attach.
     *
     * @throws InvalidArgumentException If the email task's arguments are invalid.
     */
    public function __construct(string $to_email, string $subject, string $body, array $headers = [], array $attachments = [])
    {
        parent::__construct($to_email, $subject, $body, $headers, $attachments);
    }
    /**
     * Processes the email task.
     *
     * @since 0.0.1
     *
     * @throws ShepherdTaskException If the email fails to send.
     */
    public function process(): void
    {
        // phpcs:disable WordPressVIPMinimum.Functions.RestrictedFunctions.wp_mail_wp_mail
        $result = wp_mail(...$this->get_args());
        if (!$result) {
            throw new ShepherdTaskException(__('Failed to send email.', 'stellarwp-shepherd'));
        }
        /**
         * Fires when the email task is processed.
         *
         * @since 0.0.1
         *
         * @param Email $task The email task that was processed.
         */
        do_action('shepherd_' . Config::get_hook_prefix() . '_email_processed', $this);
    }
    /**
     * Validates the email task's arguments.
     *
     * @since 0.0.1
     *
     * @throws InvalidArgumentException If the email task's arguments are invalid.
     */
    protected function validate_args(): void
    {
        $args = $this->get_args();
        if (count($args) < 3) {
            throw new InvalidArgumentException(__('Email task requires at least 3 arguments.', 'stellarwp-shepherd'));
        }
        $recipients = $args[0];
        if (!is_string($recipients) || empty(trim($recipients))) {
            throw new InvalidArgumentException(__('Email recipients must be a non-empty string.', 'stellarwp-shepherd'));
        }
        // Split by comma and validate each email.
        $emails = array_map('trim', explode(',', $recipients));
        $invalid_emails = array_filter($emails, fn($email) => !is_email($email));
        if (!empty($invalid_emails)) {
            throw new InvalidArgumentException(sprintf(
                // translators: %s is a comma-separated list of invalid email addresses.
                __('Invalid email address(es): %s', 'stellarwp-shepherd'),
                implode(', ', $invalid_emails)
            ));
        }
        if (!is_string($args[1])) {
            throw new InvalidArgumentException(__('Email subject must be a string.', 'stellarwp-shepherd'));
        }
        if (!is_string($args[2])) {
            throw new InvalidArgumentException(__('Email body must be a string.', 'stellarwp-shepherd'));
        }
    }
    /**
     * Gets the email task's hook prefix.
     *
     * @since 0.0.1
     *
     * @return string The email task's hook prefix.
     */
    public function get_task_prefix(): string
    {
        return 'shepherd_email_';
    }
    /**
     * Gets the maximum number of retries.
     *
     * @since 0.0.1
     *
     * @return int The maximum number of retries.
     */
    public function get_max_retries(): int
    {
        return 4;
    }
}