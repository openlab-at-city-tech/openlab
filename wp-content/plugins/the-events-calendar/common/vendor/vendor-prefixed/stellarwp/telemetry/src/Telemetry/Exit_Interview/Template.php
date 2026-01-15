<?php

/**
 * Handles the methods for rendering the "Exit Interview" modal on plugin deactivation.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
namespace TEC\Common\StellarWP\Telemetry\Exit_Interview;

use TEC\Common\StellarWP\ContainerContract\ContainerInterface;
use TEC\Common\StellarWP\Telemetry\Admin\Resources;
use TEC\Common\StellarWP\Telemetry\Config;
use TEC\Common\StellarWP\Telemetry\Contracts\Template_Interface;
/**
 * The primary class for rendering the "Exit Interview" modal on plugin deactivation.
 *
 * @since 1.0.0
 *
 * @package \StellarWP\Telemetry
 */
class Template implements Template_Interface
{
    /**
     * The plugin container.
     *
     * @since 1.0.0
     *
     * @var \TEC\Common\StellarWP\ContainerContract\ContainerInterface
     */
    protected $container;
    /**
     * The class constructor.
     *
     * @since 1.0.0
     *
     * @param \TEC\Common\StellarWP\ContainerContract\ContainerInterface $container The container.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    /**
     * Gets the arguments for configuring the "Exit Interview" modal.
     *
     * @since 1.0.0
     * @since 2.0.0 - Updated to accept a passed stellar slug.
     *
     * @param string $stellar_slug The stellar slug used when outputting the modal.
     *
     * @return array
     */
    protected function get_args(string $stellar_slug)
    {
        $args = ['plugin_slug' => $stellar_slug, 'plugin_logo' => Resources::get_asset_path() . 'resources/images/stellar-logo.svg', 'plugin_logo_width' => 151, 'plugin_logo_height' => 32, 'plugin_logo_alt' => 'StellarWP Logo', 'heading' => __('We’re sorry to see you go.', 'stellarwp-telemetry'), 'intro' => __('We’d love to know why you’re leaving so we can improve our plugin.', 'stellarwp-telemetry'), 'uninstall_reasons' => [['uninstall_reason_id' => 'confusing', 'uninstall_reason' => __('I couldn’t understand how to make it work.', 'stellarwp-telemetry')], ['uninstall_reason_id' => 'better-plugin', 'uninstall_reason' => __('I found a better plugin.', 'stellarwp-telemetry')], ['uninstall_reason_id' => 'no-feature', 'uninstall_reason' => __('I need a specific feature it doesn’t provide.', 'stellarwp-telemetry')], ['uninstall_reason_id' => 'broken', 'uninstall_reason' => __('The plugin doesn’t work.', 'stellarwp-telemetry')], ['uninstall_reason_id' => 'other', 'uninstall_reason' => __('Other', 'stellarwp-telemetry'), 'show_comment' => true]]];
        /**
         * Filters the "Exit Interview" modal arguments.
         *
         * Planned deprecation: 3.0.0
         * Use stellarwp/telemetry/exit_interview_args filter instead.
         *
         * @since 1.0.0
         * @since 2.0.0 - Added the current stellar slug.
         *
         * @param array  $args         The arguments used to configure the modal.
         * @param string $stellar_slug The current stellar slug for the plugin outputting the modal.
         */
        $args = apply_filters('stellarwp/telemetry/' . $stellar_slug . '/exit_interview_args', $args, $stellar_slug);
        /**
         * Filters the "Exit Interview" modal arguments.
         *
         * @since 2.0.0
         *
         * @param array  $args         The arguments used to configure the modal.
         * @param string $stellar_slug The current stellar slug for the plugin outputting the modal.
         *
         * @return void
         */
        $args = apply_filters('stellarwp/telemetry/exit_interview_args', $args, $stellar_slug);
        return $args;
    }
    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @param string $stellar_slug The stellar slug to be referenced when the modal is rendered.
     *
     * @return void
     */
    public function render(string $stellar_slug)
    {
        load_template(dirname(dirname(__DIR__)) . '/views/exit-interview.php', false, $this->get_args($stellar_slug));
    }
    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @param string $stellar_slug The stellar slug for which the modal should be rendered.
     *
     * @return boolean
     */
    public function should_render(string $stellar_slug)
    {
        /**
         * Filters whether the "Exit Interview" modal should render.
         *
         * @since 1.0.0
         * @since 2.0.0 - Update to include current stellar slug.
         *
         * @param bool   $should_render  Whether the modal should render.
         * @param string $stellar_slug The current stellar slug of the plugin for which the modal is shown.
         */
        return apply_filters('stellarwp/telemetry/' . Config::get_hook_prefix() . 'exit_interview_should_render', true, $stellar_slug);
    }
    /**
     * Renders the template if it should be rendered.
     *
     * @since 1.0.0
     *
     * @param string $stellar_slug The stellar slug that could be rendered.
     *
     * @return void
     */
    public function maybe_render(string $stellar_slug)
    {
        if ($this->should_render($stellar_slug)) {
            $this->render($stellar_slug);
        }
    }
}