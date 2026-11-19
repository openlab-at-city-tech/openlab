<?php
/**
 * Uninstall Table of Contents Plus.
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Load plugin file.
require_once 'toc.php';

// Disable Action Scheduler Queue Runner.
if ( class_exists( 'ActionScheduler_QueueRunner' ) ) {
	ActionScheduler_QueueRunner::instance()->unhook_dispatch_async_request();
}

// Drop our custom tables.
aioseoTableOfContents()->core->uninstall->dropData();