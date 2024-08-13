<?php

use PDFEmbedder\Tasks\Tasks;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Load plugin file.
require_once 'pdf_embedder.php';

// Disable Action Schedule Queue Runner.
if ( class_exists( 'ActionScheduler_QueueRunner' ) ) {
	ActionScheduler_QueueRunner::instance()->unhook_dispatch_async_request();
}

// Un-schedule all plugin ActionScheduler actions.
// Don't use pdf_embedder() because 'tasks' in core are registered on `init` hook,
// which is not executed on uninstallation.
( new Tasks() )->cancel_all();
