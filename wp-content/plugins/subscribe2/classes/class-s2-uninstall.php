<?php
class S2_Uninstall {
	public function uninstall() {
		global $wp_version, $wpmu_version;
		// Is Subscribe2 free active
		if ( is_plugin_active( 'subscribe2/subscribe2.php' ) ) {
			return;
		}

		// Is this WordPressMU or not?
		if ( isset( $wpmu_version ) || strpos( $wp_version, 'wordpress-mu' ) ) {
			$s2_mu = true;
		}
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			$s2_mu = true;
		}

		if ( isset( $s2_mu ) && true === $s2_mu ) {
			global $wpdb;
			$blogs = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
			foreach ( $blogs as $blog ) {
				switch_to_blog( $blog );
				$this->clean_database();
				restore_current_blog();
			}
		} else {
			$this->clean_database();
		}
	}

	private function clean_database() {
		global $wpdb;
		// delete entry from wp_options table
		delete_option( 'subscribe2_options' );
		// delete legacy entry from wp-options table
		delete_option( 's2_future_posts' );
		// remove and scheduled events
		wp_clear_scheduled_hook( 's2_digest_cron' );
		// delete usermeta data for registered users
		// use LIKE and % wildcard as meta_key names are prepended on WPMU
		// and s2_cat is appended with category ID integer
		$wpdb->query( "DELETE from $wpdb->usermeta WHERE meta_key LIKE '%s2_cat%'" );
		$wpdb->query( "DELETE from $wpdb->usermeta WHERE meta_key LIKE '%s2_subscribed'" );
		$wpdb->query( "DELETE from $wpdb->usermeta WHERE meta_key LIKE '%s2_format'" );
		$wpdb->query( "DELETE from $wpdb->usermeta WHERE meta_key LIKE '%s2_autosub'" );
		// delete any postmeta data that supressed notifications
		$wpdb->query( "DELETE from $wpdb->postmeta WHERE meta_key = 's2mail'" );

		// drop the subscribe2 table
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}subscribe2" );
	}
}
