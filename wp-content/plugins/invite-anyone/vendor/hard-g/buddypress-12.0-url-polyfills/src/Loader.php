<?php

namespace HardG\BuddyPress120URLPolyfills;

class Loader {
	private function __construct() {}

	public static function init() {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new static();
			$instance->setup();
		}
	}

	private function setup() {
		add_action( 'bp_include', [ $this, 'load' ] );
	}

	public function load() {
		if ( ! version_compare( bp_get_version(), '12.0', '<' ) ) {
			return;
		}

		require_once __DIR__ . '/components/core.php';
		require_once __DIR__ . '/components/members.php';

		if ( bp_is_active( 'activity' ) ) {
			require_once __DIR__ . '/components/activity.php';
		}

		if ( bp_is_active( 'groups' ) ) {
			require_once __DIR__ . '/components/groups.php';
		}

		if ( bp_is_active( 'blogs' ) ) {
			require_once __DIR__ . '/components/blogs.php';
		}
	}
}
