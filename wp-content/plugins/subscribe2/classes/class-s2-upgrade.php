<?php
class S2_Upgrade {
	/* ===== Install and reset ===== */
	/**
	 * Install our table
	 */
	public function install() {
		global $wpdb, $mysubscribe2;
		// load our translations and strings
		$mysubscribe2->load_translations();

		// include upgrade functions
		if ( ! function_exists( 'maybe_create_table' ) ) {
			require_once ABSPATH . 'wp-admin/install-helper.php';
		}
		$charset_collate = '';
		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		}

		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE {$wpdb->collate}";
		}

		$date = gmdate( 'Y-m-d' );
		$sql  = "CREATE TABLE $wpdb->subscribe2 (
			id int(11) NOT NULL auto_increment,
			email varchar(64) NOT NULL default '',
			active tinyint(1) default 0,
			date DATE default '$date' NOT NULL,
			time TIME DEFAULT '00:00:00' NOT NULL,
			ip char(64) NOT NULL default 'admin',
			conf_date DATE,
			conf_time TIME,
			conf_ip char(64),
			PRIMARY KEY (id) ) $charset_collate";

		// create the table, as needed
		maybe_create_table( $wpdb->subscribe2, $sql );

		// safety check if options exist and if not create them
		if ( ! is_array( $mysubscribe2->subscribe2_options ) ) {
			$this->reset();
		}

		// create table entries for registered users
		$users = $mysubscribe2->get_all_registered( 'ID' );
		if ( ! empty( $users ) ) {
			foreach ( $users as $user_ID ) {
				$check_format = get_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_format' ), true );
				if ( empty( $check_format ) ) {
					// no prior settings so create them
					$mysubscribe2->register( $user_ID );
				}
			}
		}
	}

	/**
	 * Reset our options
	 */
	public function reset() {
		// load our translations and strings
		global $mysubscribe2;
		$mysubscribe2->load_translations();

		delete_option( 'subscribe2_options' );
		wp_clear_scheduled_hook( 's2_digest_cron' );
		unset( $mysubscribe2->subscribe2_options );
		require S2PATH . 'include/options.php';
		$mysubscribe2->subscribe2_options['version'] = S2VERSION;
		update_option( 'subscribe2_options', $mysubscribe2->subscribe2_options );
	}

	/**
	 * Core upgrade function for the database and settings
	 */
	public function upgrade() {
		global $mysubscribe2;
		// load our translations and strings
		$mysubscribe2->load_translations();

		// ensure that the options are in the database
		require S2PATH . 'include/options.php';
		// catch older versions that didn't use serialised options
		if ( ! isset( $mysubscribe2->subscribe2_options['version'] ) ) {
			$mysubscribe2->subscribe2_options['version'] = '2.0';
		}

		// let's take the time to ensure that database entries exist for all registered users
		$this->upgrade_core();
		if ( version_compare( $mysubscribe2->subscribe2_options['version'], '2.3', '<' ) ) {
			$this->upgrade2_3();
			$mysubscribe2->subscribe2_options['version'] = '2.3';
			update_option( 'subscribe2_options', $mysubscribe2->subscribe2_options );
		}
		if ( version_compare( $mysubscribe2->subscribe2_options['version'], '5.1', '<' ) ) {
			$this->upgrade5_1();
			$mysubscribe2->subscribe2_options['version'] = '5.1';
			update_option( 'subscribe2_options', $mysubscribe2->subscribe2_options );
		}
		if ( version_compare( $mysubscribe2->subscribe2_options['version'], '5.6', '<' ) ) {
			$this->upgrade5_6();
			$mysubscribe2->subscribe2_options['version'] = '5.6';
			update_option( 'subscribe2_options', $mysubscribe2->subscribe2_options );
		}
		if ( version_compare( $mysubscribe2->subscribe2_options['version'], '5.9', '<' ) ) {
			$this->upgrade5_9();
			$mysubscribe2->subscribe2_options['version'] = '5.9';
			update_option( 'subscribe2_options', $mysubscribe2->subscribe2_options );
		}
		if ( version_compare( $mysubscribe2->subscribe2_options['version'], '6.4', '<' ) ) {
			$this->upgrade6_4();
			$mysubscribe2->subscribe2_options['version'] = '6.4';
			update_option( 'subscribe2_options', $mysubscribe2->subscribe2_options );
		}
		if ( version_compare( $mysubscribe2->subscribe2_options['version'], '7.0', '<' ) ) {
			$this->upgrade7_0();
			$mysubscribe2->subscribe2_options['version'] = '7.0';
			update_option( 'subscribe2_options', $mysubscribe2->subscribe2_options );
		}
		if ( version_compare( $mysubscribe2->subscribe2_options['version'], '8.5', '<' ) ) {
			$this->upgrade8_5();
			$mysubscribe2->subscribe2_options['version'] = '8.5';
			update_option( 'subscribe2_options', $mysubscribe2->subscribe2_options );
		}
		if ( version_compare( $mysubscribe2->subscribe2_options['version'], '8.6', '<' ) ) {
			$this->upgrade8_6();
			$mysubscribe2->subscribe2_options['version'] = '8.6';
			update_option( 'subscribe2_options', $mysubscribe2->subscribe2_options );
		}
		if ( version_compare( $mysubscribe2->subscribe2_options['version'], '8.8', '<' ) ) {
			$this->upgrade8_8();
			$mysubscribe2->subscribe2_options['version'] = '8.8';
			update_option( 'subscribe2_options', $mysubscribe2->subscribe2_options );
		}
		if ( version_compare( $mysubscribe2->subscribe2_options['version'], '9.5', '<' ) ) {
			$this->upgrade9_5();
			$mysubscribe2->subscribe2_options['version'] = '9.5';
			update_option( 'subscribe2_options', $mysubscribe2->subscribe2_options );
		}
		if ( version_compare( $mysubscribe2->subscribe2_options['version'], '10.14', '<' ) ) {
			$this->upgrade10_14();
			$mysubscribe2->subscribe2_options['version'] = '10.14';
			update_option( 'subscribe2_options', $mysubscribe2->subscribe2_options );
		}
		if ( version_compare( $mysubscribe2->subscribe2_options['version'], '10.23', '<' ) ) {
			$this->upgrade10_23();
			$mysubscribe2->subscribe2_options['version'] = '10.23';
			update_option( 'subscribe2_options', $mysubscribe2->subscribe2_options );
		}

		$mysubscribe2->subscribe2_options['version'] = S2VERSION;
		update_option( 'subscribe2_options', $mysubscribe2->subscribe2_options );
	}

	private function upgrade_core() {
		// let's take the time to double check data for registered users
		global $mysubscribe2;
		if ( version_compare( $mysubscribe2->wp_release, '3.5', '<' ) ) {
			global $wpdb;
			$users = $wpdb->get_col( $wpdb->prepare( "SELECT ID from $wpdb->users WHERE ID NOT IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key=%s)", $mysubscribe2->get_usermeta_keyname( 's2_format' ) ) );
			if ( ! empty( $users ) ) {
				foreach ( $users as $user_ID ) {
					$mysubscribe2->register( $user_ID );
				}
			}
		} else {
			$args = array(
				'meta_query' => array(
					array(
						'key'     => $mysubscribe2->get_usermeta_keyname( 's2_format' ),
						'compare' => 'NOT EXISTS',
					),
				),
			);

			$user_query = new WP_User_Query( $args );
			$users      = $user_query->get_results();
			if ( ! empty( $users ) ) {
				foreach ( $users as $user ) {
					$mysubscribe2->register( $user->ID );
				}
			}
		}
		// let's make sure that the 's2_authors' key exists on every site for all Registered Users too
		$this->upgrade7_0();
	}

	private function upgrade2_3() {
		global $mysubscribe2, $wpdb;

		// include upgrade functions
		if ( ! function_exists( 'maybe_add_column' ) ) {
			require_once ABSPATH . 'wp-admin/install-helper.php';
		}
		$date = gmdate( 'Y-m-d' );
		maybe_add_column( $wpdb->subscribe2, 'date', "ALTER TABLE $wpdb->subscribe2 ADD date DATE DEFAULT '$date' NOT NULL AFTER active" );

		// update the options table to serialized format
		$old_options = $wpdb->get_col( "SELECT option_name from $wpdb->options where option_name LIKE 's2%' AND option_name <> 's2_future_posts'" );

		if ( ! empty( $old_options ) ) {
			foreach ( $old_options as $option ) {
				$value        = get_option( $option );
				$option_array = substr( $option, 3 );
				$mysubscribe2->subscribe2_options[ $option_array ] = $value;
				delete_option( $option );
			}
		}
	}

	private function upgrade5_1() {
		global $mysubscribe2, $wpdb;

		// include upgrade functions
		if ( ! function_exists( 'maybe_add_column' ) ) {
			require_once ABSPATH . 'wp-admin/install-helper.php';
		}
		maybe_add_column( $wpdb->subscribe2, 'ip', "ALTER TABLE $wpdb->subscribe2 ADD ip char(64) DEFAULT 'admin' NOT NULL AFTER date" );
	}

	private function upgrade5_6() {
		global $mysubscribe2;
		// correct autoformat to upgrade from pre 5.6
		if ( 'text' === $mysubscribe2->subscribe2_options['autoformat'] ) {
			$mysubscribe2->subscribe2_options['autoformat'] = 'excerpt';
		}
		if ( 'full' === $mysubscribe2->subscribe2_options['autoformat'] ) {
			$mysubscribe2->subscribe2_options['autoformat'] = 'post';
		}
	}

	private function upgrade5_9() {
		global $mysubscribe2, $wpdb;
		// ensure existing public subscriber emails are all sanitized
		$confirmed          = $mysubscribe2->get_public();
		$unconfirmed        = $mysubscribe2->get_public( 0 );
		$public_subscribers = array_merge( (array) $confirmed, (array) $unconfirmed );

		foreach ( $public_subscribers as $email ) {
			$new_email = $mysubscribe2->sanitize_email( $email );
			if ( $email !== $new_email ) {
				$wpdb->get_results( $wpdb->prepare( "UPDATE $wpdb->subscribe2 SET email=%s WHERE CAST(email as binary)=%s", $new_email, $email ) );
			}
		}
	}

	private function upgrade6_4() {
		global $mysubscribe2;
		// change old CAPITALISED keywords to those in {PARENTHESES}; since version 6.4
		$keywords = array( 'BLOGNAME', 'BLOGLINK', 'TITLE', 'POST', 'POSTTIME', 'TABLE', 'TABLELINKS', 'PERMALINK', 'TINYLINK', 'DATE', 'TIME', 'MYNAME', 'EMAIL', 'AUTHORNAME', 'LINK', 'CATS', 'TAGS', 'COUNT', 'ACTION' );
		$keyword  = implode( '|', $keywords );
		$regex    = '/(?<!\{)\b(' . $keyword . ')\b(?!\{)/xm';
		$replace  = '{\1}';

		$mysubscribe2->subscribe2_options['mailtext']             = preg_replace( $regex, $replace, $mysubscribe2->subscribe2_options['mailtext'] );
		$mysubscribe2->subscribe2_options['notification_subject'] = preg_replace( $regex, $replace, $mysubscribe2->subscribe2_options['notification_subject'] );
		$mysubscribe2->subscribe2_options['confirm_email']        = preg_replace( $regex, $replace, $mysubscribe2->subscribe2_options['confirm_email'] );
		$mysubscribe2->subscribe2_options['confirm_subject']      = preg_replace( $regex, $replace, $mysubscribe2->subscribe2_options['confirm_subject'] );
		$mysubscribe2->subscribe2_options['remind_email']         = preg_replace( $regex, $replace, $mysubscribe2->subscribe2_options['remind_email'] );
		$mysubscribe2->subscribe2_options['remind_subject']       = preg_replace( $regex, $replace, $mysubscribe2->subscribe2_options['remind_subject'] );

		if ( version_compare( $mysubscribe2->wp_release, '3.5', '<' ) ) {
			$users = $mysubscribe2->get_all_registered( 'ID' );
			foreach ( $users as $user_ID ) {
				$check_format = get_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_format' ), true );
				// if user is already registered update format remove 's2_excerpt' field and update 's2_format'
				if ( 'html' === $check_format ) {
					delete_user_meta( $user_ID, 's2_excerpt' );
				} elseif ( 'text' === $check_format ) {
					update_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_format' ), get_user_meta( $user_ID, 's2_excerpt' ) );
					delete_user_meta( $user_ID, 's2_excerpt' );
				}
				$subscribed = get_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_subscribed' ), true );
				if ( strstr( $subscribed, '-1' ) ) {
					// make sure we remove '-1' from any settings
					$old_cats = explode( ',', $subscribed );
					$pos      = array_search( '-1', $old_cats, true );
					unset( $old_cats[ $pos ] );
					$cats = implode( ',', $old_cats );
					update_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_subscribed' ), $cats );
				}
			}
		} else {
			$args = array(
				'relation'   => 'AND',
				'meta_query' => array(
					array(
						'key'   => $mysubscribe2->get_usermeta_keyname( 's2_format' ),
						'value' => 'html',
					),
				),
				'meta_query' => array(
					array(
						'key'     => 's2_excerpt',
						'compare' => 'EXISTS',
					),
				),
			);

			$user_query = new WP_User_Query( $args );
			$users      = $user_query->get_results();
			if ( ! empty( $users ) ) {
				foreach ( $users as $user ) {
					delete_user_meta( $user->ID, 's2_excerpt' );
				}
			}

			$args = array(
				'relation'   => 'AND',
				'meta_query' => array(
					array(
						'key'   => $mysubscribe2->get_usermeta_keyname( 's2_format' ),
						'value' => 'text',
					),
				),
				'meta_query' => array(
					array(
						'key'     => 's2_excerpt',
						'compare' => 'EXISTS',
					),
				),
			);

			$user_query = new WP_User_Query( $args );
			$users      = $user_query->get_results();
			if ( ! empty( $users ) ) {
				foreach ( $users as $user ) {
					update_user_meta( $user->ID, $mysubscribe2->get_usermeta_keyname( 's2_format' ), get_user_meta( $user->ID, 's2_excerpt' ) );
					delete_user_meta( $user->ID, 's2_excerpt' );
				}
			}

			$args = array(
				'meta_query' => array(
					array(
						'key'     => $mysubscribe2->get_usermeta_keyname( 's2_subscribed' ),
						'value'   => '-1',
						'compare' => 'LIKE',
					),
				),
			);

			$user_query = new WP_User_Query( $args );
			$users      = $user_query->get_results();
			if ( ! empty( $users ) ) {
				foreach ( $users as $user ) {
					$subscribed = get_user_meta( $user->ID, $mysubscribe2->get_usermeta_keyname( 's2_subscribed' ), true );
					$old_cats   = explode( ',', $subscribed );
					$pos        = array_search( '-1', $old_cats, true );
					unset( $old_cats[ $pos ] );
					$cats = implode( ',', $old_cats );
					update_user_meta( $user->ID, $mysubscribe2->get_usermeta_keyname( 's2_subscribed' ), $cats );
				}
			}
		}

		// upgrade old wpmu user meta data to new
		if ( true === $mysubscribe2->s2_mu ) {
			global $s2class_multisite, $wpdb;
			$s2class_multisite->namechange_subscribe2_widget();
			// loop through all users
			foreach ( $users as $user_ID ) {
				// get categories which the user is subscribed to (old ones)
				$categories = get_user_meta( $user_ID, 's2_subscribed', true );
				$categories = explode( ',', $categories );
				$format     = get_user_meta( $user_ID, 's2_format', true );
				$autosub    = get_user_meta( $user_ID, 's2_autosub', true );

				// load blogs of user (only if we need them)
				$blogs = array();
				if ( count( $categories ) > 0 && ! in_array( '-1', $categories, true ) ) {
					$blogs = get_blogs_of_user( $user_ID, true );
				}

				foreach ( $blogs as $blog ) {
					switch_to_blog( $blog->userblog_id );

					$blog_categories       = (array) $wpdb->get_col( "SELECT term_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'category'" );
					$subscribed_categories = array_intersect( $categories, $blog_categories );
					if ( ! empty( $subscribed_categories ) ) {
						foreach ( $subscribed_categories as $subscribed_category ) {
							update_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_cat' ) . $subscribed_category, $subscribed_category );
						}
						update_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_subscribed' ), implode( ',', $subscribed_categories ) );
					}
					if ( ! empty( $format ) ) {
						update_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_format' ), $format );
					}
					if ( ! empty( $autosub ) ) {
						update_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_autosub' ), $autosub );
					}
					restore_current_blog();
				}

				// delete old user meta keys
				delete_user_meta( $user_ID, 's2_subscribed' );
				delete_user_meta( $user_ID, 's2_format' );
				delete_user_meta( $user_ID, 's2_autosub' );
				foreach ( $categories as $cat ) {
					delete_user_meta( $user_ID, 's2_cat' . $cat );
				}
			}
		}
	}

	private function upgrade7_0() {
		global $mysubscribe2, $wpdb;
		if ( version_compare( $mysubscribe2->wp_release, '3.5', '<' ) ) {
			$users = $wpdb->get_col( $wpdb->prepare( "SELECT ID from $wpdb->users WHERE ID NOT IN (SELECT user_id from $wpdb->usermeta WHERE meta_key=%s", $mysubscribe2->get_usermeta_keyname( 's2_authors' ) ) );
			foreach ( $users as $user_ID ) {
				$check_authors = get_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_authors' ), true );
				if ( empty( $check_authors ) ) {
					update_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_authors' ), '' );
				}
			}
		} else {
			$args = array(
				'meta_query' => array(
					array(
						'key'     => $mysubscribe2->get_usermeta_keyname( 's2_authors' ),
						'compare' => 'NOT EXISTS',
					),
				),
			);

			$user_query = new WP_User_Query( $args );
			$users      = $user_query->get_results();
			if ( ! empty( $users ) ) {
				foreach ( $users as $user ) {
					update_user_meta( $user->ID, $mysubscribe2->get_usermeta_keyname( 's2_authors' ), '' );
				}
			}
		}
	}

	private function upgrade8_5() {
		global $mysubscribe2, $wpdb;

		// include upgrade functions
		if ( ! function_exists( 'maybe_add_column' ) ) {
			require_once ABSPATH . 'wp-admin/install-helper.php';
		}
		maybe_add_column( $wpdb->subscribe2, 'time', "ALTER TABLE $wpdb->subscribe2 ADD time TIME DEFAULT '00:00:00' NOT NULL AFTER date" );

		// update postmeta field to a protected name, from version 8.5
		$wpdb->query( "UPDATE $wpdb->postmeta SET meta_key = '_s2mail' WHERE meta_key = 's2mail'" );
	}

	private function upgrade8_6() {
		global $mysubscribe2, $wpdb;

		// include upgrade functions
		if ( ! function_exists( 'maybe_add_column' ) ) {
			require_once ABSPATH . 'wp-admin/install-helper.php';
		}
		maybe_add_column( $wpdb->subscribe2, 'conf_date', "ALTER TABLE $wpdb->subscribe2 ADD conf_date DATE AFTER ip" );
		maybe_add_column( $wpdb->subscribe2, 'conf_time', "ALTER TABLE $wpdb->subscribe2 ADD conf_time TIME AFTER conf_date" );
		maybe_add_column( $wpdb->subscribe2, 'conf_ip', "ALTER TABLE $wpdb->subscribe2 ADD conf_ip char(64) AFTER conf_time" );

		// remove unnecessary table data
		$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key = 's2_cat'" );

		$users = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID FROM $wpdb->users INNER JOIN $wpdb->usermeta ON ( $wpdb->users.ID = $wpdb->usermeta.user_id) WHERE ( $wpdb->usermeta.meta_key = %s AND $wpdb->usermeta.meta_value LIKE %s )",
				$mysubscribe2->get_usermeta_keyname( 's2_subscribed' ),
				$wpdb->esc_like( ',' ) . '%'
			)
		);
		foreach ( $users as $user ) {
			// make sure we remove leading ',' from this setting
			$subscribed = get_user_meta( $user->ID, $mysubscribe2->get_usermeta_keyname( 's2_subscribed' ), true );
			$old_cats   = explode( ',', $subscribed );
			unset( $old_cats[0] );
			$cats = implode( ',', $old_cats );
			update_user_meta( $user->ID, $mysubscribe2->get_usermeta_keyname( 's2_subscribed' ), $cats );
		}
	}

	private function upgrade8_8() {
		// to ensure compulsory category collects all users we need there to be s2_subscribed meta-keys for all users
		global $mysubscribe2, $wpdb;

		if ( version_compare( $mysubscribe2->wp_release, '3.5', '<' ) ) {
			$all_registered = $mysubscribe2->get_all_registered( 'ID' );
			if ( ! empty( $all_registered ) ) {
				foreach ( $all_registered as $user_ID ) {
					$check_subscribed = get_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_subscribed' ), true );
					if ( empty( $check_subscribed ) ) {
						update_user_meta( $user_ID, $mysubscribe2->get_usermeta_keyname( 's2_subscribed' ), '' );
					}
				}
			}
		} else {
			$args = array(
				'meta_query' => array(
					array(
						'key'     => $mysubscribe2->get_usermeta_keyname( 's2_subscribed' ),
						'compare' => 'NOT EXISTS',
					),
				),
			);

			$user_query = new WP_User_Query( $args );
			$users      = $user_query->get_results();
			if ( ! empty( $users ) ) {
				foreach ( $users as $user ) {
					update_user_meta( $user->ID, $mysubscribe2->get_usermeta_keyname( 's2_subscribed' ), '' );
				}
			}
		}

		// check the time column again as the upgrade8_6() function contained a bug
		// include upgrade-functions for maybe_add_column;
		if ( ! function_exists( 'maybe_add_column' ) ) {
			require_once ABSPATH . 'wp-admin/install-helper.php';
		}
		maybe_add_column( $wpdb->subscribe2, 'time', "ALTER TABLE $wpdb->subscribe2 ADD time TIME DEFAULT '00:00:00' NOT NULL AFTER date" );
	}

	private function upgrade9_5() {
		global $mysubscribe2;
		if ( 'never' !== $mysubscribe2->subscribe2_options['email_freq'] ) {
			$mysubscribe2->subscribe2_options['last_s2cron'] = '';
			unset( $mysubscribe2->subscribe2_options['previous_s2cron'] );
		}
	}

	private function upgrade10_14() {
		global $mysubscribe2;
		if ( ! isset( $mysubscribe2->subscribe2_options['frontend_form'] ) ) {
			$mysubscribe2->subscribe2_options['frontend_form'] = '0';
		}
		if ( ! isset( $mysubscribe2->subscribe2_options['dismiss_sender_warning'] ) ) {
			$mysubscribe2->subscribe2_options['dismiss_sender_warning'] = '0';
		}
	}

	private function upgrade10_15() {
		global $mysubscribe2;
		if ( ! isset( $mysubscribe2->subscribe2_options['js_ip_updater'] ) ) {
			$mysubscribe2->subscribe2_options['js_ip_updater'] = '0';
		}
	}

	private function upgrade10_23() {
		global $mysubscribe2;
		if ( isset( $mysubscribe2->subscribe2_options['entries'] ) ) {
			unset( $mysubscribe2->subscribe2_options['entries'] );
		}
	}
}
