<?php

/*
 * @wordpress-plugin
 *
 * Plugin Name: Spotlight - Social Media Feeds
 * Description: Easily embed beautiful Instagram feeds on your WordPress site.
 * Version: 1.7.5
 * Author: RebelCode
 * Plugin URI: https://spotlightwp.com
 * Author URI: https://rebelcode.com
 * Requires at least: 5.7
 * Requires PHP: 7.1
 *
   */

// If not running within a WordPress context, or the plugin is already running, stop
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/includes/init.php';

// Listen for deactivation requests from a fatal error
slInstaCheckDeactivate();

//=============================================================================
// CONFLICT DETECTION
//=============================================================================

// Check for conflicts on activation
register_activation_hook(__FILE__, function () {
    slInstaCheckForConflicts();
});

// Check for conflicts when a plugin is activated
add_action('activated_plugin', function () {
    slInstaCheckForConflicts();
});

// Check for conflicts when a plugin is deactivated
add_action('deactivated_plugin', function ($plugin = '') {
    set_transient('sli_deactivated_plugin', $plugin);
});

// Check for the above plugin deactivation transient. If set, check for conflicts
$deactivated = get_transient('sli_deactivated_plugin');
if ($deactivated !== false) {
    slInstaCheckForConflicts([$deactivated]);
    delete_transient('sli_deactivated_plugin');
}

//=============================================================================
// BOOTSTRAPPING
//=============================================================================

// Load Freemius
if (!function_exists('sliFreemius')) {
    require_once __DIR__ . '/freemius.php';
}

// Whether or not this copy is a PRO version
// This controls whether this copy of the plugin takes precedence over other copies during the bootstrapping process.
$thisIsPro = false;
if (sliFreemius()->is_plan_or_trial('pro')) {
    $thisIsPro = true;
}

// The bootstrap function
$bootstrapper = function (SlInstaRuntime $sli) use ($thisIsPro) {
    // Filter whether this plugin can run
    if (apply_filters('spotlight/instagram/can_run', true, $sli) !== true) {
        return;
    }

    // Define plugin constants, if not already defined
    if (!defined('SL_INSTA')) {
        // Used to detect the plugin
        define('SL_INSTA', true);
        // The plugin name
        define('SL_INSTA_NAME', 'Spotlight - Social Media Feeds');
        // The plugin version
        define('SL_INSTA_VERSION', '1.7.5');
        // The path to the plugin's main file
        define('SL_INSTA_FILE', __FILE__);
        // The dir to the plugin's directory
        define('SL_INSTA_DIR', __DIR__);
        // The minimum required PHP version
        define('SL_INSTA_PLUGIN_NAME', 'Spotlight - Social Media Feeds');
        // The minimum required PHP version
        define('SL_INSTA_MIN_PHP_VERSION', '7.1');
        // The minimum required WordPress version
        define('SL_INSTA_MIN_WP_VERSION', '5.7');

        // Dev mode constant that controls whether development tools are enabled
        if (!defined('SL_INSTA_DEV')) {
            define('SL_INSTA_DEV', false);
        }
    }

    // Stop if dependencies aren't satisfied
    if (!slInstaDepsSatisfied()) {
        return;
    }

    // If the conflicts notice needs to be shown, stop here
    if (slInstaShowConflictsNotice()) {
        return;
    }

    // If a PRO version is running and the free version is not, show a notice
    if ($sli->isProActive && !$sli->isFreeActive) {
        add_action('admin_notices', 'slInstaRequireFreeNotice');

        return;
    }

    // Show a notice if the free version is v0.4 or older
    if ($sli->isFreeActive && $sli->isProActive && version_compare($sli->freeVersion ?? '0.0', '0.4', '<')) {
        add_action('admin_notices', 'slInstaFreeVersionNotice');

        return;
    }

    // Load the autoloader - loaders all the way down!
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        require __DIR__ . '/vendor/autoload.php';
    }

    // Init Freemius
    sliFreemius()->set_basename($thisIsPro, __FILE__);

    // Load the PRO script, if it exists
    if (file_exists(__DIR__ . '/includes/pro.php')) {
        require_once __DIR__ . '/includes/pro.php';
    }

    global $sliRuntime;
    $sliRuntime = $sli;

    // Run the plugin's modules
    add_action('plugins_loaded', function () {
        // Trigger the plugin-specific `init` action on the WordPress `init` action
        add_action('init', function () {
            do_action('spotlight/instagram/init');
        }, 11);

        try {
            spotlightInsta()->run();
        } catch (Throwable $ex) {
            if (!is_admin()) {
                return;
            }

            $message = sprintf(
                _x('%s has encountered an error.', '%s is the name of the plugin', 'sl-insta'),
                '<b>' . SL_INSTA_NAME . '</b>'
            );

            $link = sprintf(
                '<a href="%s">%s</a>',
                admin_url('plugins.php?sli_error_deactivate=' . wp_create_nonce('sli_error_deactivate')),
                __('Click here to deactivate the plugin', 'sl-insta')
            );

            $details = '<b>' . __('Error details', 'sl-insta') . '</b>' .
                       "<pre>{$ex->getMessage()}</pre>" .
                       "<pre>In file: {$ex->getFile()}:{$ex->getLine()}</pre>" .
                       "<pre>{$ex->getTraceAsString()}</pre>";

            $style = '<style type="text/css">#error-page {max-width: unset;} pre {overflow-x: auto;}</style>';

            wp_die(
                "$style <p>$message <br /> $link</p> $details",
                SL_INSTA_NAME . ' | Error',
                [
                    'back_link' => true,
                ]
            );
        }
    });
};

// Filter the bootstrap function to allow decoration or alteration of bootstrapping process
$bootstrapper = apply_filters('spotlight/instagram/bootstrapper/0.4', $bootstrapper, $thisIsPro, __FILE__);

// Run the plugin
slInstaRunPlugin(__FILE__, $bootstrapper);
