<?php

namespace WeBWorK;

/**
 * Server.
 *
 * @since 1.0.0
 */
class Server {
	protected $post_data;
	protected $remote_problem_url;
	protected $remote_class_url;
	protected $webwork_user;

	public function __construct() {
		if ( ! class_exists( 'WP_REST_Controller' ) ) {
			add_action(
				'admin_notices',
				function() {
					echo '<div class=\"error\"><p>' . esc_html__( 'WeBWorK for WordPress requires the WP-API plugin to function properly. Please install WP-API or deactivate WeBWorK for WordPress.', 'webworkqa' ) . '</p></div>';
				}
			);
		}

		$this->schema = new Server\Schema();
		$this->schema->init();

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_redirector_script' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_redirector_script' ) );

		$app_endpoint = new Server\App\Endpoint();
		add_action( 'rest_api_init', array( $app_endpoint, 'register_routes' ) );

		$problems_endpoint = new Server\Problem\Endpoint();
		add_action( 'rest_api_init', array( $problems_endpoint, 'register_routes' ) );

		$questions_endpoint = new Server\Question\Endpoint();
		add_action( 'rest_api_init', array( $questions_endpoint, 'register_routes' ) );

		$responses_endpoint = new Server\Response\Endpoint();
		add_action( 'rest_api_init', array( $responses_endpoint, 'register_routes' ) );

		$votes_endpoint = new Server\Vote\Endpoint();
		add_action( 'rest_api_init', array( $votes_endpoint, 'register_routes' ) );

		$subscriptions_endpoint = new Server\Subscription\Endpoint();
		add_action( 'rest_api_init', array( $subscriptions_endpoint, 'register_routes' ) );

		add_action( 'template_redirect', array( $this, 'catch_post' ) );

		// Mods to default WP behavior to account for uploads.
		add_filter( 'map_meta_cap', array( __CLASS__, 'map_meta_cap' ), 10, 4 );
		add_filter( 'ajax_query_attachments_args', array( $this, 'filter_uploads_query_args' ) );
	}

	/**
	 * @todo This will only work for individual problems. Will need to differentiate for other uses.
	 * @todo Redirect afterward, break up logic into separate items, etc.
	 */
	public function catch_post() {
		// @todo
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( empty( $_GET['webwork'] ) || '1' !== $_GET['webwork'] ) {
			return;
		}

		/**
		 * Logic:
		 *
		 * 1. Store post data
		 * 2. If user is not logged in, redirect to login with post_data query arg.
		 * 3. If (or once) user is logged in, parse Library ID (problemId) from pg_object.
		 *    a. If question exists with that problemId, redirect to problem view
		 *    b. If question doesn't exist with that problemId, redirect to dummy view
		 *    In either case, keep the post_data query arg.
		 * 4. Be sure to store post_data key when processing question, because that metadata must be saved with the question item.
		 */

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! empty( $_POST ) ) {
			$post_data = $this->sanitize_post_data();
			$this->set_remote_class_url( $post_data['remote_problem_url'] );
			$this->set_post_data( $post_data );
			$this->webwork_user = $post_data['webwork_user'];
		} else {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['remote_class_url'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$this->set_remote_class_url( sanitize_text_field( wp_unslash( $_GET['remote_class_url'] ) ) );
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['webwork_user'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$this->webwork_user = sanitize_text_field( wp_unslash( $_GET['webwork_user'] ) );
			}

			if ( $this->webwork_user ) {
				$key       = $this->get_post_data_option_key();
				$post_data = get_option( $key );

				if ( $post_data ) {
					$this->set_post_data( $post_data );

					// This data should never be reused across redirects.
					delete_option( $key );
				}
			}
		}

		// Store the submitted post data, so it's available after a redirect.
		$this->store_post_data();

		$ww_client_site_base = $this->get_client_site_base();
		$redirect_to         = $ww_client_site_base;

		$problem_slug = $post_data['problem_id'];
		if ( $problem_slug ) {
			$redirect_to = $ww_client_site_base . '#:problemId=' . $problem_slug;
			$redirect_to = add_query_arg( 'post_data_key', $this->post_data_key, $redirect_to );
		}

		// For the time being, all requests must be authenticated.
		// @todo Check permissions against client site - maybe share logic with endpoints.
		if ( ! is_user_logged_in() ) {
			$redirect_to = wp_login_url( $redirect_to );
			$redirect_to = add_query_arg( 'is-webwork-redirect', '1', $redirect_to );
		}

		/*
		 * Redirect must happen via JS. Sending 302 Redirect header strips
		 * URL fragment on iOS.
		 */
		echo '<script type="text/javascript">window.location.replace("' . esc_url( $redirect_to ) . '");</script>';
		die;
	}

	public function sanitize_post_data() {
		$filtered_data = apply_filters( 'webwork_pre_sanitize_post_data', null );

		if ( null !== $filtered_data ) {
			return $filtered_data;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$data = array(
			'webwork_user'    => sanitize_text_field( wp_unslash( $_POST['user'] ) ),
			'problem_set'     => sanitize_text_field( wp_unslash( $_POST['set'] ) ),
			'problem_number'  => sanitize_text_field( wp_unslash( $_POST['problem'] ) ),
			'problem_id'      => '',
			'problem_text'    => '',
			'course'          => isset( $_POST['courseId'] ) ? sanitize_text_field( wp_unslash( $_POST['courseId'] ) ) : '',
			'emailableURL'    => isset( $_POST['emailableURL'] ) ? sanitize_text_field( wp_unslash( $_POST['emailableURL'] ) ) : '',
			'randomSeed'      => isset( $_POST['randomSeed'] ) ? sanitize_text_field( wp_unslash( $_POST['randomSeed'] ) ) : '',
			'notifyAddresses' => isset( $_POST['notifyAddresses'] ) ? wp_unslash( $_POST['notifyAddresses'] ) : '',
			'studentName'     => isset( $_POST['studentName'] ) ? sanitize_text_field( wp_unslash( $_POST['studentName'] ) ) : '',
		);
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		$remote_problem_url = wp_unslash( $_SERVER['HTTP_REFERER'] );

		$url_parts = $this->sanitize_class_url( $remote_problem_url );

		$data['remote_course_url']  = $url_parts['base'];
		$data['remote_problem_url'] = remove_query_arg( array( 'user', 'effectiveUser', 'key' ), $remote_problem_url );

		// notifyAddresses may be MIME-encoded.
		if ( function_exists( 'mb_decode_mimeheader' ) ) {
			$data['notifyAddresses'] = mb_decode_mimeheader( $data['notifyAddresses'] );
		} elseif ( function_exists( 'imap_utf8' ) ) {
			$data['notifyAddresses'] = imap_utf8( $data['notifyAddresses'] );
		} elseif ( function_exists( 'iconv_mime_decode' ) ) {
			$data['notifyAddresses'] = iconv_mime_decode( $data['notifyAddresses'] );
		}

		// 'user' is a string - WeBWoRK user name.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$data['webwork_user'] = sanitize_text_field( wp_unslash( $_POST['user'] ) );

		// Split pg_object into discreet problem data. Sanitized in clean_problem_from_webwork().
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$raw_text = $_POST['pg_object'];

		// Do not unslash. wp_insert_post() expects slashed. A nightmare.
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		$text = base64_decode( $raw_text );

		$pf   = new Server\Util\ProblemFormatter();
		$text = $pf->clean_problem_from_webwork( $text, $data );

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$raw_problem_path = isset( $_POST['problemPath'] ) ? wp_unslash( $_POST['problemPath'] ) : '';
		if ( $raw_problem_path ) {
			$data['problem_id'] = sanitize_text_field( $raw_problem_path );
		} else {
			$data['problem_id'] = $pf->get_library_id_from_text( $text );
		}

		$text = $pf->strip_library_id_from_text( $text );

		$data['problem_text'] = $text;

		return $data;
	}

	public function set_post_data( $data ) {
		$this->post_data = $data;
	}

	/**
	 * Sanitize a remote class URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $raw_url Raw URL from the HTTP_REFERER header.
	 * @return array URL parts.
	 */
	public function sanitize_class_url( $raw_url ) {
		$parts = wp_parse_url( $raw_url );

		// Raw URL may contain a set and problem subpath.
		$subpath = '';
		foreach ( array( 'set', 'problem' ) as $key ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( ! empty( $_POST[ $key ] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				$path_part = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
				$subpath  .= trailingslashit( $path_part );
			}
		}

		$this->remote_referer_url = $parts['scheme'] . '://' . $parts['host'] . $parts['path'];

		if ( $subpath && false !== strpos( $parts['path'], $subpath ) ) {
			$pos  = strpos( $parts['path'], $subpath );
			$base = substr( $parts['path'], 0, $pos );
		} else {
			$base = $parts['path'];
		}

		$base = trailingslashit( $parts['scheme'] . '://' . $parts['host'] . $base );

		$retval = array(
			'base'          => $base,
			'effectiveUser' => '',
			'user'          => '',
			'key'           => '',
		);

		if ( ! empty( $parts['query'] ) ) {
			parse_str( $parts['query'], $query );
			foreach ( (array) $query as $k => $v ) {
				$retval[ $k ] = $v;
			}
		}

		return $retval;
	}

	/**
	 * Set the course URL for the request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $remote_class_url
	 */
	public function set_remote_class_url( $remote_class_url ) {
		$url_parts              = $this->sanitize_class_url( $remote_class_url );
		$this->remote_class_url = $url_parts['base'];
		$this->webwork_user     = $url_parts['user'];
	}

	protected function get_client_from_course_url( $course_url ) {
		// @todo We need a better way to do this.
		$clients = get_option( 'webwork_clients', array() );

		$client = 0;
		if ( isset( $clients[ $course_url ] ) ) {
			$client = $clients[ $course_url ];
		}

		return (int) $client;
	}

	/**
	 * Get the key to be used when storing the POST data in the options table.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args {
	 *     @type string $ip   IP address. Falls back on REMOTE_ADDR.
	 *     @type string $user WW user name. Falls back on $this->webwork_user.
	 * }
	 * @return string
	 */
	protected function get_post_data_option_key( $args = array() ) {
		if ( isset( $args['ip'] ) ) {
			$ip = $args['ip'];
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = wp_unslash( $_SERVER['REMOTE_ADDR'] );
		} else {
			$ip = '';
		}

		if ( isset( $args['user'] ) ) {
			$user = $args['user'];
		} else {
			$user = $this->webwork_user;
		}

		// If neither $ip or $user is available, don't store the data.
		if ( ! $ip && ! $user ) {
			return false;
		}

		return 'webwork_post_data_' . md5( $ip . $user . time() );
	}

	/**
	 * Store POST and other data that will be needed after redirect.
	 *
	 * @since 1.0.0
	 */
	public function store_post_data() {
		$this->post_data_key = $this->get_post_data_option_key();

		// Store the remote class URL for later use.
		$this->post_data['remote_class_url']   = $this->remote_class_url;
		$this->post_data['remote_referer_url'] = $this->remote_referer_url;

		update_option( $this->post_data_key, $this->post_data, false );
	}

	public function get_server_site_base() {
		return set_url_scheme( apply_filters( 'webwork_server_site_base', get_option( 'home' ) ) );
	}

	public function get_client_site_base() {
		return set_url_scheme( apply_filters( 'webwork_client_site_base', get_option( 'home' ) ) );
	}

	public function enqueue_redirector_script() {
		wp_enqueue_script( 'webwork-redirector' );
	}

	/**
	 * Give users the 'edit_post' and 'upload_files' cap, when appropriate
	 *
	 * @param array $caps The mapped caps
	 * @param string $cap The cap being mapped
	 * @param int $user_id The user id in question
	 * @param $args
	 * @return array $caps
	 */
	public static function map_meta_cap( $caps, $cap, $user_id, $args ) {
		if ( 'upload_files' !== $cap && 'edit_post' !== $cap ) {
			return $caps;
		}

		$maybe_user = new \WP_User( $user_id );
		if ( ! is_a( $maybe_user, 'WP_User' ) || empty( $maybe_user->ID ) ) {
			return $caps;
		}

		$can = false;
		switch ( $cap ) {
			case 'upload_files':
				$can = true;
				break;

			case 'edit_post':
				$post = get_post( $args[0] );
				$can  = $post && (int) $user_id === (int) $post->post_author;
				break;
		}

		if ( $can ) {
			$caps = array( 'exist' );
		}

		// @todo Better filtering?
		return $caps;
	}

	public function filter_uploads_query_args( $query ) {
		if ( current_user_can( 'edit_posts' ) ) {
			return $query;
		}

		$query['author'] = get_current_user_id();

		return $query;
	}
}
