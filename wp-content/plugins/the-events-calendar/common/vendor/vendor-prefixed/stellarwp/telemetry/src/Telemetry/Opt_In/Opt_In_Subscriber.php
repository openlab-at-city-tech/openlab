<?php

/**
 * Handles all actions/filters related to the opt-in.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
namespace TEC\Common\StellarWP\Telemetry\Opt_In;

use TEC\Common\StellarWP\Telemetry\Config;
use TEC\Common\StellarWP\Telemetry\Contracts\Abstract_Subscriber;
use TEC\Common\StellarWP\Telemetry\Telemetry\Telemetry;
/**
 * Class to handle all actions/filters related to the opt-in.
 *
 * @since 1.0.0
 *
 * @package \StellarWP\Telemetry
 */
class Opt_In_Subscriber extends Abstract_Subscriber
{
    /**
     * @inheritDoc
     *
     * @return void
     */
    public function register(): void
    {
        /**
         * Planned deprecation: 3.0.0
         *
         * Use stellarwp/telemetry/optin filter instead.
         */
        add_action('stellarwp/telemetry/' . Config::get_stellar_slug() . '/optin', [$this, 'maybe_render_optin'], 10, 1);
        add_action('stellarwp/telemetry/optin', [$this, 'maybe_render_optin'], 10, 1);
        add_action('admin_init', [$this, 'set_optin_status']);
        add_action('init', [$this, 'initialize_optin_option']);
    }
    /**
     * Sets the opt-in status for the site.
     *
     * @since 1.0.0
     * @since 2.3.4 - Added user capability check.
     *
     * @return void
     */
    public function set_optin_status()
    {
        // We're not attempting an action.
        if (empty($_POST['_wpnonce'])) {
            return;
        }
        $nonce = sanitize_text_field($_POST['_wpnonce']);
        if (!wp_verify_nonce($nonce, 'stellarwp-telemetry')) {
            return;
        }
        // Check sent data before we do any database checks for faster failures.
        // We're not attempting a telemetry action.
        if (isset($_POST['action']) && 'stellarwp-telemetry' !== $_POST['action']) {
            return;
        }
        // The user did not respond to the opt-in modal.
        if (!isset($_POST['optin-agreed'])) {
            return;
        }
        // Sent data validated, check if the user has the necessary permissions.
        if (!current_user_can('manage_options')) {
            return;
        }
        $stellar_slug = Config::get_stellar_slug();
        if (isset($_POST['stellar_slug'])) {
            $stellar_slug = sanitize_text_field($_POST['stellar_slug']);
        }
        $opt_in_text = '';
        if (isset($_POST['opt_in_text'])) {
            $opt_in_text = sanitize_text_field($_POST['opt_in_text']);
        }
        // User agreed to opt-in to Telemetry.
        if ('true' === $_POST['optin-agreed']) {
            $this->opt_in($stellar_slug, $opt_in_text);
        }
        // Don't show the opt-in modal again.
        update_option($this->container->get(Opt_In_Template::class)->get_option_name($stellar_slug), '0');
    }
    /**
     * Renders the opt-in modal if it should be rendered.
     *
     * @since 1.0.0
     * @since 2.0.0 - Update to handle rendering multiple modals.
     *
     * @param string $stellar_slug The stellar slug to use in determining when and how the modal is displayed.
     *
     * @return void
     */
    public function maybe_render_optin(string $stellar_slug = '')
    {
        if ('' === $stellar_slug) {
            $stellar_slug = Config::get_stellar_slug();
        }
        $this->container->get(Opt_In_Template::class)->maybe_render($stellar_slug);
    }
    /**
     * Sets the initial value when the plugin is loaded.
     *
     * If the plugin doesn't already have the opt-in option set, we need to set it
     * so that the opt-in should be shown to the user when the do_action is run.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function initialize_optin_option()
    {
        $opt_in_template = $this->container->get(Opt_In_Template::class);
        $opt_in_status = $this->container->get(Status::class);
        // Loop through all registered stellar slugs and add them to the optin option.
        foreach (Config::get_all_stellar_slugs() as $stellar_slug => $wp_slug) {
            // Check if plugin slug exists within array.
            if (!$opt_in_status->plugin_exists($stellar_slug)) {
                $opt_in_status->add_plugin($stellar_slug, false, $wp_slug);
                update_option($opt_in_template->get_option_name($stellar_slug), '1');
            }
        }
    }
    /**
     * Registers the site/user with the telemetry server and sets the opt-in status.
     *
     * @since 1.0.0
     * @since 2.0.0 - Updated to allow specifying the stellar slug.
     * @since 2.2.0 - Updated to add opt-in text.
     *
     * @param string $stellar_slug The slug to use when opting in.
     * @param string $opt_in_text  The text displayed to the user when they agreed to opt-in.
     *
     * @return void
     */
    public function opt_in(string $stellar_slug, string $opt_in_text = '')
    {
        $this->container->get(Status::class)->set_status(true, $stellar_slug);
        try {
            $this->container->get(Telemetry::class)->register_site();
            $this->container->get(Telemetry::class)->register_user($stellar_slug, $opt_in_text);
        } catch (\Error $e) {
            // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
            // We don't want to throw errors if the server cannot be reached.
        }
    }
}