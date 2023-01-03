<?php

/**
 * Subscribe multisite handler class.
 */
class S2_Multisite {

	/**
	 * Handles subscriptions and unsubscriptions for different blogs on WPMU installs.
	 *
	 * @return void
	 */
	public function wpmu_subscribe() {
		global $mysubscribe2;

		$user_ID  = get_current_user_id();
		$sub_id   = ! empty( $_GET['s2mu_subscribe'] ) ? intval( $_GET['s2mu_subscribe'] ) : '';
		$unsub_id = ! empty( $_GET['s2mu_unsubscribe'] ) ? intval( $_GET['s2mu_unsubscribe'] ) : '';

		if ( ! empty( $sub_id ) ) {
			// Subscribe to new blog.
			if ( $sub_id >= 0 ) {
				switch_to_blog( $sub_id );

				// If user is not a user of the current blog.
				if ( ! is_user_member_of_blog( $user_ID, $sub_id ) ) {
					// Add user to current blog as subscriber.
					add_user_to_blog( $sub_id, $user_ID, 'subscriber' );
					// Add an action hook for external manipulation of blog and user data.
					do_action_ref_array( 'subscribe2_wpmu_subscribe', array( $user_ID, $sub_id ) );
				}

				// Det categories, remove excluded ones if override is off.
				if ( 0 === $mysubscribe2->subscribe2_options['reg_override'] ) {
					$all_cats = $mysubscribe2->all_cats( true, 'ID' );
				} else {
					$all_cats = $mysubscribe2->all_cats( false, 'ID' );
				}

				$cats_string = '';
				foreach ( $all_cats as $cat ) {
					$cats_string .= empty( $cats_string ) ? $cat->term_id : ",$cat->term_id";
					update_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_cat' ) . $cat->term_id, $cat->term_id );
				}

				if ( empty( $cats_string ) ) {
					delete_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_subscribed' ) );
				} else {
					update_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_subscribed' ), $cats_string );
				}
			}
		} elseif ( ! empty( $unsub_id ) ) {
			// Unsubscribe from a blog.
			if ( $unsub_id >= 0 ) {
				switch_to_blog( $unsub_id );

				// Delete subscription to all categories on that blog.
				$cats = get_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_subscribed' ), true );
				$cats = explode( ',', $cats );
				if ( ! is_array( $cats ) ) {
					$cats = array( $cats );
				}

				foreach ( $cats as $id ) {
					delete_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_cat' ) . $id );
				}

				delete_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_subscribed' ) );

				// Add an action hook for external manipulation of blog and user data.
				do_action_ref_array( 'subscribe2_wpmu_unsubscribe', array( $user_ID, $unsub_id ) );

				restore_current_blog();
			}
		}

		if ( ! is_user_member_of_blog( $user_ID ) ) {
			$user_blogs = get_active_blog_for_user( $user_ID );
			if ( is_array( $user_blogs ) ) {
				switch_to_blog( key( $user_blogs ) );
			} else {
				// No longer a member of a blog.
				wp_safe_redirect( get_option( 'siteurl' ) ); // Redirect to front page.
				exit( 0 );
			}
		}

		// Redirect to profile page.
		$url = get_option( 'siteurl' ) . '/wp-admin/admin.php?page=s2';
		wp_safe_redirect( $url );
		exit( 0 );
	}

	/**
	 * Obtain a list of current WordPress multiuser blogs
	 * Note this may affect performance but there is no alternative.
	 *
	 * @return array
	 */
	public function get_mu_blog_list() {
		global $wpdb;

		$blogs = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT blog_id, domain, path FROM $wpdb->blogs WHERE site_id = %d AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY registered DESC",
				$wpdb->siteid
			),
			ARRAY_A
		);

		$blog_list = array();
		foreach ( $blogs as $details ) {
			// Reindex the array so the key is the same as the blog_id.
			$blog_list[ $details['blog_id'] ] = $details;
		}

		if ( empty( $blog_list ) || ! is_array( $blog_list ) ) {
			return array();
		}

		return apply_filters( 's2_mu_blog_list', $blog_list );
	}

	/**
	 * Register user details when new user is added to a multisite blog.
	 *
	 * @param int $user_ID
	 *
	 * @return void
	 */
	public function wpmu_add_user( $user_ID = 0 ) {
		global $mysubscribe2;

		if ( 0 === $user_ID ) {
			return;
		}

		if ( 'yes' === $mysubscribe2->subscribe2_options['autosub'] ) {
			$mysubscribe2->register( $user_ID, true );
		} else {
			$mysubscribe2->register( $user_ID, false );
		}
	}

	/**
	 * Delete user details when a user is removed from a multisite blog.
	 *
	 * @param int $user_ID
	 *
	 * @return void
	 */
	public function wpmu_remove_user( $user_ID ) {
		global $mysubscribe2;

		if ( 0 === $user_ID ) {
			return;
		}

		delete_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_format' ) );
		delete_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_autosub' ) );

		$cats = get_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_subscribed' ), true );
		if ( ! empty( $cats ) ) {
			$cats = explode( ',', $cats );
			foreach ( $cats as $cat ) {
				delete_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_cat' ) . $cat );
			}
		}

		delete_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_subscribed' ) );
	}

	/**
	 * Rename WPMU widgets on upgrade without requiring user to re-enable.
	 *
	 * @return void
	 */
	public function namechange_subscribe2_widget() {
		global $wpdb;

		$blogs = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
		foreach ( $blogs as $blog ) {
			switch_to_blog( $blog );

			$sidebars = get_option( 'sidebars_widgets', array() );
			if ( empty( $sidebars ) || ! is_array( $sidebars ) ) {
				return;
			}

			$changed = false;
			foreach ( $sidebars as $s => $sidebar ) {
				if ( empty( $sidebar ) || ! is_array( $sidebar ) ) {
					break;
				}

				foreach ( $sidebar as $w => $widget ) {
					if ( 'subscribe2widget' === $widget ) {
						$sidebars[ $s ][ $w ] = 'subscribe2';
						$changed              = true;
					}
				}
			}

			if ( $changed ) {
				update_option( 'sidebar_widgets', $sidebars );
			}

			restore_current_blog();
		}
	}
}
