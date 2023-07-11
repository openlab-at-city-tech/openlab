<?php

/**
 * Subscribe frontend class.
 */
class S2_Frontend extends S2_Core {

    /**
     * Process unsubscribe.
     *
     * @param $email
     */
	public function unsubscribe( $email ) {
	    global $wpdb;

        if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            return;
        }

        $count = $wpdb->delete( $wpdb->subscribe2, [ 'email' => $email ] );
        if ( ! $count ) {
            wp_safe_redirect( home_url() );
        }
    }

	/**
	 * Display form when deprecated <!--subscribe2--> is used.
	 *
	 * @param $content
	 *
	 * @return array|string|string[]|null
	 */
	public function filter( $content = '' ) {
		if ( empty( $content ) || ! strstr( $content, '<!--subscribe2-->' ) ) {
			return $content;
		}

		return preg_replace( '/(<p>)?(\n)*<!--subscribe2-->(\n)*(<\/p>)?/', do_shortcode( '[subscribe2]' ), $content );
	}

	/**
	 * Overrides the default query when handling a (un)subscription confirmation
	 * This is basically a trick: if the s2 variable is in the query string, just grab the first
	 * static page and override it's contents later with title_filter().
	 *
	 * @return array|int[]|void
	 */
	public function query_filter() {
		// Don't interfere if we've already done our thing.
		if ( 1 === $this->filtered ) {
			return;
		}

		global $wpdb;

		// Brute force Simple Facebook Connect to bypass compatibility issues.
		$priority = has_filter( 'wp_head', 'sfc_base_meta' );
		if ( false !== $priority ) {
			remove_action( 'wp_head', 'sfc_base_meta', $priority );
		}

		if ( 0 !== $this->subscribe2_options['s2page'] ) {
			return array( 'page_id' => $this->subscribe2_options['s2page'] );
		} else {
			$id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status='publish' LIMIT 1" );
			if ( $id ) {
				return array( 'page_id' => $id );
			} else {
				return array( 'showposts' => 1 );
			}
		}
	}

	/**
	 * Overrides the page title.
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	public function title_filter( $title ) {
		if ( in_the_loop() ) {
			$code   = $_GET['s2'];
			$action = intval( substr( $code, 0, 1 ) );

			if ( 1 === $action ) {
				return __( 'Subscription Confirmation', 'subscribe2' );
			} else {
				return __( 'Unsubscription Confirmation', 'subscribe2' );
			}
		} else {
			return $title;
		}
	}

	/**
	 * Confirm request from the link emailed to the user and email the admin.
	 *
	 * @param string $content
	 *
	 * @return mixed|string|void|null
	 */
	public function confirm( $content = '' ) {
		global $wpdb;

		if ( 1 === $this->filtered && '' !== $this->message ) {
			return $this->message;
		} elseif ( 1 === $this->filtered ) {
			return $content;
		}

		$code   = $_GET['s2'];
		$action = substr( $code, 0, 1 );
		$hash   = substr( $code, 1, 32 );
		$id     = intval( substr( $code, 33 ) );

		if ( $id ) {
			$this->email = sanitize_email( $this->get_email( $id ) );
			if ( ! $this->email || wp_hash( $this->email ) !== $hash ) {
				return $this->no_such_email;
			}
		} else {
			return $this->no_such_email;
		}

		// Get current status of email so messages are only sent once per emailed link.
		$current = $this->is_public( $this->email );

		if ( '1' === $action ) {
			// Make this subscription active.
			$this->message = apply_filters( 's2_subscribe_confirmed', $this->added );

			if ( '1' !== $this->is_public( $this->email ) ) {
				$this->ip = esc_html( $this->get_remote_ip() );
				$this->toggle( $this->email );

				if ( 'subs' === $this->subscribe2_options['admin_email'] || 'both' === $this->subscribe2_options['admin_email'] ) {
					$this->admin_email( 'subscribe' );
				}
			}

			$this->filtered = 1;
		} elseif ( '0' === $action ) {
			// Remove this public subscriber.
			$this->message = apply_filters( 's2_unsubscribe_confirmed', $this->deleted );

			if ( '0' !== $this->is_public( $this->email ) ) {
				$this->delete( $this->email );

				if ( 'unsubs' === $this->subscribe2_options['admin_email'] || 'both' === $this->subscribe2_options['admin_email'] ) {
					$this->admin_email( 'unsubscribe' );
				}
			}

			$this->filtered = 1;
		}

		if ( '' !== $this->message ) {
			return $this->message;
		}
	}

	/**
	 * Prepare and send emails to admins on new subscriptions and unsubsriptions.
	 *
	 * @param string $action
	 *
	 * @return false|void
	 */
	public function admin_email( $action ) {
		if ( ! in_array( $action, array( 'subscribe', 'unsubscribe' ), true ) ) {
			return false;
		}

		$blogname = get_option( 'blogname' );
		$subject  = empty( $blogname ) ? '[' . stripslashes( html_entity_decode( $blogname, ENT_QUOTES ) ) . '] ' : '';
		if ( 'subscribe' === $action ) {
			$subject .= __( 'New Subscription', 'subscribe2' );
			$message  = $this->email . ' ' . __( 'subscribed to email notifications!', 'subscribe2' );
		} elseif ( 'unsubscribe' === $action ) {
			$subject .= __( 'New Unsubscription', 'subscribe2' );
			$message  = $this->email . ' ' . __( 'unsubscribed from email notifications!', 'subscribe2' );
		}

		$subject = html_entity_decode( $subject, ENT_QUOTES );
		$role    = array(
			'fields' => array(
				'user_email',
			),
			'role'   => 'administrator',
		);

		$wp_user_query = get_users( $role );
		foreach ( $wp_user_query as $user ) {
			$recipients[] = $user->user_email;
		}

		$recipients = apply_filters( 's2_admin_email', $recipients, $action );
		$headers    = $this->headers();

		// Send individual emails so we don't reveal admin emails to each other.
		foreach ( $recipients as $recipient ) {
			$status = wp_mail( $recipient, $subject, $message, $headers );
		}
	}

	/**
	 * Add hook for Minimeta Widget plugin.
	 *
	 * @return void
	 */
	public function add_minimeta() {
		if ( 0 !== $this->subscribe2_options['s2page'] ) {
			echo '<li><a href="' . esc_url( get_permalink( $this->subscribe2_options['s2page'] ) ) . '">' . esc_html__( '[Un]Subscribe to Posts', 'subscribe2' ) . '</a></li>' . "\r\n";
		}
	}

	/**
	 * Check email is not from a barred domain.
	 *
	 * @param string $email
	 *
	 * @return bool
	 */
	public function is_barred( $email = '' ) {
		if ( empty( $email ) ) {
			return false;
		}

		list( $user, $domain ) = explode( '@', $email, 2 );

		$domain = '@' . $domain;
		foreach ( preg_split( '/[\s,]+/', $this->subscribe2_options['barred'] ) as $barred_domain ) {
			if ( false !== strpos( $barred_domain, '!' ) ) {
				$url   = explode( '.', str_replace( '!', '', $barred_domain ) );
				$count = count( $url );

				// Make sure our exploded domain has at least 2 components e.g. yahoo.*
				if ( $count < 2 ) {
					continue;
				}

				for ( $i = 0; $i < $count; $i++ ) {
					if ( '*' === $url[ $i ] ) {
						unset( $url[ $i ] );
					}
				}

				$new_barred_domain = '@' . strtolower( trim( implode( '.', $url ) ) );
				if ( false !== strpos( $barred_domain, '*' ) ) {
					$new_barred_subdomain = '.' . strtolower( trim( implode( '.', $url ) ) );
					if ( false !== stripos( $domain, $new_barred_domain ) || false !== stripos( $domain, $new_barred_subdomain ) ) {
						return false;
					}
				} else {
					if ( false !== stripos( $domain, $new_barred_domain ) ) {
						return false;
					}
				}
			}

			if ( false === strpos( $barred_domain, '!' ) && false !== strpos( $barred_domain, '*' ) ) {
				// Wildcard and explictly allowed checking.
				$url   = explode( '.', str_replace( '!', '', $barred_domain ) );
				$count = count( $url );

				// Make sure our exploded domain has at least 2 components e.g. yahoo.*
				if ( $count < 2 ) {
					continue;
				}

				for ( $i = 0; $i < $count; $i++ ) {
					if ( '*' === $url[ $i ] ) {
						unset( $url[ $i ] );
					}
				}

				$new_barred_domain    = '@' . strtolower( trim( implode( '.', $url ) ) );
				$new_barred_subdomain = '.' . strtolower( trim( implode( '.', $url ) ) );
				if ( false !== stripos( $domain, $new_barred_domain ) || false !== stripos( $domain, $new_barred_subdomain ) ) {
					return true;
				}
			} else {
				// Direct domain string comparison.
				$barred_domain = '@' . $barred_domain;
				if ( strtolower( $domain ) === strtolower( trim( $barred_domain ) ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Enqueue javascript ip updater code.
	 *
	 * @return void
	 */
	public function js_ip_script() {
		wp_register_script( 's2_ip_updater', S2URL . 'include/s2-ip-updater' . $this->script_debug . '.js', array(), '1.1', true );
		wp_enqueue_script( 's2_ip_updater' );
	}

	/**
	 * Add ip updater library to footer.
	 *
	 * @return void
	 */
	public function js_ip_library_script() {
		$args = array(
			'format'   => 'jsonp',
			'callback' => 'getip',
		);

		wp_enqueue_script( 's2_ip_library', add_query_arg( $args, 'https://api.ipify.org' ), array(), S2VERSION, true );
	}

	/**
	 * Reformat WordPress escaped link to IPify library.
	 *
	 * @param string $tag
	 *
	 * @return string
	 */
	public function tag_replace_ampersand( $tag ) {
		if ( strstr( $tag, 'ipify' ) !== false ) {
			$tag = str_replace( '&#038;', '&', $tag );
		}

		return $tag;
	}


    /**
     * Create and display a dropdown list of pages
     */
    public function pages_dropdown( $s2page, $name = 's2page' ) {
        //
    }
}
