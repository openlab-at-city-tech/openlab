<?php
/**
 * The client page password gate.
 *
 * @package Imagely\NGG\PrivateGalleries
 */

namespace Imagely\NGG\PrivateGalleries;

use Imagely\NGG\DataMappers\Gallery as GalleryMapper;

/**
 * Styles the core password form on client pages, checks and rate limits submissions, keeps client
 * pages out of indexes and caches, and counts unlocks.
 */
class Gate {

	/**
	 * Query argument carrying the form error after a refused submission.
	 *
	 * @var string
	 */
	const ERROR_ARG = 'ngg_pw';

	/**
	 * Hidden form field naming the client page.
	 *
	 * @var string
	 */
	const PAGE_FIELD = 'ngg_client_page';

	/**
	 * Prefix of the server-keyed cookie that client pages require next to core's wp-postpass cookie.
	 *
	 * @var string
	 */
	const UNLOCK_COOKIE = 'ngg_pg_unlock_';

	/**
	 * Registers hooks.
	 *
	 * @return void
	 */
	public static function register_hooks() {
		add_filter( 'the_password_form', [ self::class, 'password_form' ], 10, 2 );
		add_action( 'login_form_postpass', [ self::class, 'handle_postpass' ] );
		add_filter( 'post_password_required', [ self::class, 'require_unlock_cookie' ], 10, 2 );
		add_action( 'template_redirect', [ self::class, 'send_client_page_headers' ], 1 );

		if ( function_exists( 'wp_robots' ) ) {
			add_filter( 'wp_robots', [ self::class, 'robots' ] );
		} else {
			add_action( 'wp_head', [ self::class, 'robots_meta_fallback' ], 1 );
		}
	}

	/**
	 * The client page being viewed, if any.
	 *
	 * @return int
	 */
	public static function current_client_page() {
		if ( ! did_action( 'wp' ) || ! is_singular() ) {
			return 0;
		}

		$page_id = (int) get_queried_object_id();

		return Registry::is_client_page( $page_id ) ? $page_id : 0;
	}

	/**
	 * Replaces the core password form on client pages.
	 *
	 * @param string        $output Core form.
	 * @param \WP_Post|null $post   Post (WordPress 5.8+ passes it).
	 * @return string
	 */
	public static function password_form( $output, $post = null ) {
		$post = get_post( $post );

		if ( ! $post instanceof \WP_Post || ! Registry::is_client_page( $post->ID ) ) {
			return $output;
		}

		return self::render_form( $post );
	}

	/**
	 * The styled client page password form.
	 *
	 * @param \WP_Post $post Client page.
	 * @return string
	 */
	public static function render_form( $post ) {
		$field_id = 'ngg-pwbox-' . (int) $post->ID;
		$error_id = 'ngg-pwbox-error-' . (int) $post->ID;
		$locked   = RateLimiter::is_locked();
		$error    = '';

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display only; the value is matched against two literals.
		$flag = isset( $_GET[ self::ERROR_ARG ] ) ? sanitize_key( wp_unslash( $_GET[ self::ERROR_ARG ] ) ) : '';

		if ( $locked || 'locked' === $flag ) {
			$locked = true;
			$error  = __( 'Too many wrong passwords. Please try again in 10 minutes.', 'nggallery' );
		} elseif ( 'invalid' === $flag ) {
			$error = __( 'That password is not correct. Please try again.', 'nggallery' );
		}

		$describedby = $error ? ' aria-describedby="' . esc_attr( $error_id ) . '" aria-invalid="true"' : '';

		ob_start();
		?>
<form action="<?php echo esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ); ?>" class="post-password-form ngg-client-gallery-form" method="post">
	<style>
		.ngg-client-gallery-form{max-width:420px;margin:2em auto;padding:2em;border:1px solid #c3c4c7;border-radius:8px;background:#fff;color:#1d2327;text-align:left}
		.ngg-client-gallery-form h2{margin:0 0 .5em;font-size:1.5em;line-height:1.3;color:#1d2327}
		.ngg-client-gallery-form p{margin:0 0 1em;color:#3c434a}
		.ngg-client-gallery-form label{display:block;margin:0 0 .4em;font-weight:600;color:#1d2327}
		.ngg-client-gallery-form input[type=password]{box-sizing:border-box;width:100%;padding:.6em .75em;border:1px solid #8c8f94;border-radius:4px;font-size:1em;color:#1d2327;background:#fff}
		.ngg-client-gallery-form input[type=password]:focus,.ngg-client-gallery-form button:focus{outline:2px solid #2271b1;outline-offset:2px}
		.ngg-client-gallery-form .ngg-client-gallery-error{margin:.5em 0 0;color:#b32d2e;font-weight:600}
		.ngg-client-gallery-form button{margin-top:1em;padding:.7em 1.4em;border:0;border-radius:4px;background:#1d2327;color:#fff;font-size:1em;cursor:pointer}
		.ngg-client-gallery-form button[disabled]{opacity:.6;cursor:not-allowed}
	</style>
	<h2><?php echo esc_html( self::get_form_title( $post ) ); ?></h2>
	<p><?php esc_html_e( 'This gallery is private. Enter the password to view it.', 'nggallery' ); ?></p>
	<label for="<?php echo esc_attr( $field_id ); ?>"><?php esc_html_e( 'Password', 'nggallery' ); ?></label>
	<input name="post_password" id="<?php echo esc_attr( $field_id ); ?>" type="password" spellcheck="false" autocomplete="current-password" required<?php echo $describedby; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from esc_attr() above. ?><?php disabled( $locked ); ?> />
		<?php if ( $error ) : ?>
	<p class="ngg-client-gallery-error" id="<?php echo esc_attr( $error_id ); ?>" role="alert"><?php echo esc_html( $error ); ?></p>
	<?php endif; ?>
	<input type="hidden" name="<?php echo esc_attr( self::PAGE_FIELD ); ?>" value="<?php echo esc_attr( (string) (int) $post->ID ); ?>" />
	<input type="hidden" name="redirect_to" value="<?php echo esc_attr( get_permalink( $post->ID ) ); ?>" />
	<button type="submit"<?php disabled( $locked ); ?>><?php esc_html_e( 'View gallery', 'nggallery' ); ?></button>
</form>
		<?php
		/**
		 * Filters the client page password form (Pro shows "This gallery is private" in link-only mode).
		 *
		 * @param string   $html Form HTML.
		 * @param \WP_Post $post Client page.
		 */
		return (string) apply_filters( 'ngg_private_gallery_password_form', (string) ob_get_clean(), $post );
	}

	/**
	 * Checks a password form submission before core sets the cookie.
	 *
	 * @return void
	 */
	public static function handle_postpass() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Core's password form carries no nonce.
		if ( ! isset( $_POST['post_password'] ) || ! is_string( $_POST['post_password'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- A password is compared, never stored or printed.
		$submitted = wp_unslash( $_POST['post_password'] );
		$page_id   = isset( $_POST[ self::PAGE_FIELD ] ) ? absint( $_POST[ self::PAGE_FIELD ] ) : 0;
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		// The limit guards client pages only; other protected posts keep core's behaviour.
		if ( empty( Registry::page_ids() ) ) {
			return;
		}

		$client_page = ( $page_id && Registry::is_client_page( $page_id ) ) ? get_post( $page_id ) : null;

		// One check at a time per address; a busy one answers like the lock does.
		if ( ! RateLimiter::acquire() ) {
			if ( $client_page ) {
				self::refuse( $client_page, 'locked' );
			}
			return;
		}

		try {
			$outcome = self::check_submission( $submitted, $client_page );
		} finally {
			RateLimiter::release();
		}

		if ( isset( $outcome['refuse'] ) ) {
			self::refuse( $client_page, $outcome['refuse'] );
		}

		if ( ! empty( $outcome['unlock'] ) ) {
			self::set_unlock_cookie( $outcome['password'] );

			foreach ( $outcome['unlock'] as $unlocked_id ) {
				self::record_access( $unlocked_id );
			}
		}
	}

	/**
	 * Checks a submitted password and counts a failure, under the address's check lock.
	 *
	 * @param string        $submitted   Submitted password.
	 * @param \WP_Post|null $client_page Client page the form was on, if any.
	 * @return array{refuse?:string,unlock?:int[],password?:string}
	 */
	private static function check_submission( $submitted, $client_page ) {
		if ( $client_page ) {
			if ( RateLimiter::is_locked() ) {
				return [ 'refuse' => 'locked' ];
			}

			if ( '' === (string) $client_page->post_password || ! hash_equals( (string) $client_page->post_password, $submitted ) ) {
				return [ 'refuse' => RateLimiter::record_failure() ? 'locked' : 'invalid' ];
			}

			return [
				'unlock'   => [ (int) $client_page->ID ],
				'password' => (string) $client_page->post_password,
			];
		}

		// Posted without our form, so the target is unknown: a guess that opens nothing is a failure.
		$opened = self::pages_opened_by( $submitted );

		if ( empty( $opened ) ) {
			if ( ! RateLimiter::is_locked() ) {
				RateLimiter::record_failure();
			}
			return [];
		}

		$opened_clients = array_values( array_filter( $opened, [ Registry::class, 'is_client_page' ] ) );

		// Locked: answer like a wrong guess, so a direct post cannot tell the right password apart.
		if ( empty( $opened_clients ) || RateLimiter::is_locked() ) {
			return [];
		}

		return [
			'unlock'   => $opened_clients,
			'password' => $submitted,
		];
	}

	/**
	 * Keeps a client page locked without the keyed unlock cookie: anyone can compute core's
	 * wp-postpass cookie, so alone it would let guesses skip the rate limit.
	 *
	 * @param bool          $required Whether core requires the password.
	 * @param \WP_Post|null $post     Post.
	 * @return bool
	 */
	public static function require_unlock_cookie( $required, $post ) {
		if ( $required || ! $post instanceof \WP_Post || '' === (string) $post->post_password || ! Registry::is_client_page( $post->ID ) ) {
			return $required;
		}

		$name = self::UNLOCK_COOKIE . COOKIEHASH;

		if ( ! isset( $_COOKIE[ $name ] ) || ! is_string( $_COOKIE[ $name ] ) ) {
			return true;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Compared in constant time, never printed.
		return ! hash_equals( self::unlock_value( $post->post_password ), wp_unslash( $_COOKIE[ $name ] ) );
	}

	/**
	 * Sets the unlock cookie for a client page password, next to core's wp-postpass cookie.
	 *
	 * @param string $password Page password.
	 * @param int    $expire   Expiry time; defaults to core's postpass expiry.
	 * @return void
	 */
	public static function set_unlock_cookie( $password, $expire = null ) {
		if ( null === $expire ) {
			/** This filter is documented in wp-login.php */
			$expire = apply_filters( 'post_password_expires', time() + 10 * DAY_IN_SECONDS );
		}

		$name  = self::UNLOCK_COOKIE . COOKIEHASH;
		$value = self::unlock_value( (string) $password );

		if ( ! headers_sent() ) {
			setcookie(
				$name,
				$value,
				[
					'expires'  => (int) $expire,
					'path'     => COOKIEPATH,
					'domain'   => COOKIE_DOMAIN,
					'secure'   => is_ssl(),
					'httponly' => true,
				]
			);
		}

		$_COOKIE[ $name ] = $value;
	}

	/**
	 * The unlock cookie value: keyed by the site's salt, so it cannot be computed from a guess.
	 *
	 * @param string $password Page password.
	 * @return string
	 */
	public static function unlock_value( $password ) {
		return hash_hmac( 'sha256', 'ngg-client-page|' . $password, wp_salt( 'auth' ) );
	}

	/**
	 * Counts an unlock by a visitor who is not logged in, for every gallery on the page.
	 *
	 * @param int $page_id Client page.
	 * @return void
	 */
	public static function record_access( $page_id ) {
		if ( is_user_logged_in() ) {
			return;
		}

		$gallery_ids = Registry::galleries_for_page( $page_id );

		if ( empty( $gallery_ids ) ) {
			return;
		}

		global $wpdb;

		$placeholders = implode( ',', array_fill( 0, count( $gallery_ids ), '%d' ) );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- An atomic counter; $placeholders is a list of %d.
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->nggallery} SET access_count = COALESCE( access_count, 0 ) + 1, last_access_at = %s WHERE gid IN ( {$placeholders} )",
				array_merge( [ current_time( 'mysql', true ) ], $gallery_ids )
			)
		);
		// phpcs:enable

		GalleryMapper::get_instance()->flush_query_cache();

		/**
		 * Fires after a visitor unlocked a client page.
		 *
		 * @param int[] $gallery_ids Galleries on the page.
		 * @param int   $page_id     Client page.
		 */
		do_action( 'ngg_private_gallery_accessed', $gallery_ids, (int) $page_id );
	}

	/**
	 * No-cache and noindex headers on client pages, locked or not.
	 *
	 * @return void
	 */
	public static function send_client_page_headers() {
		if ( ! self::current_client_page() ) {
			return;
		}

		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}

		if ( ! headers_sent() ) {
			nocache_headers();
			header( 'X-Robots-Tag: noindex, nofollow', true );
		}
	}

	/**
	 * Adds noindex, nofollow to client pages (WordPress 5.7+).
	 *
	 * @param array $robots Robots directives.
	 * @return array
	 */
	public static function robots( $robots ) {
		if ( self::current_client_page() ) {
			$robots['noindex']  = true;
			$robots['nofollow'] = true;
			unset( $robots['index'], $robots['follow'] );
		}

		return $robots;
	}

	/**
	 * Robots meta tag for WordPress versions without wp_robots().
	 *
	 * @return void
	 */
	public static function robots_meta_fallback() {
		if ( self::current_client_page() ) {
			echo "<meta name='robots' content='noindex, nofollow' />\n";
		}
	}

	/**
	 * Sends the visitor back to the client page without setting the cookie.
	 *
	 * @param \WP_Post $client_page Client page the form was on.
	 * @param string   $reason      'invalid' or 'locked'.
	 * @return void
	 */
	private static function refuse( $client_page, $reason ) {
		$permalink = get_permalink( $client_page->ID );

		wp_safe_redirect( $permalink ? add_query_arg( self::ERROR_ARG, $reason, $permalink ) : home_url( '/' ) );
		exit;
	}

	/**
	 * Posts a submitted password opens.
	 *
	 * @param string $password Submitted password.
	 * @return int[]
	 */
	private static function pages_opened_by( $password ) {
		if ( '' === $password ) {
			return [];
		}

		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Runs only on a password form submission.
		$rows = $wpdb->get_results(
			$wpdb->prepare( "SELECT ID, post_password FROM {$wpdb->posts} WHERE post_password = %s LIMIT 50", $password )
		);

		$opened = [];

		// The column collation is case-insensitive; the cookie check is not.
		foreach ( (array) $rows as $row ) {
			if ( hash_equals( (string) $row->post_password, $password ) ) {
				$opened[] = (int) $row->ID;
			}
		}

		return $opened;
	}

	/**
	 * The heading of the password form: the gallery title.
	 *
	 * @param \WP_Post $post Client page.
	 * @return string
	 */
	private static function get_form_title( $post ) {
		foreach ( Registry::galleries_for_page( $post->ID ) as $gallery_id ) {
			$gallery = GalleryMapper::get_instance()->find( $gallery_id );

			if ( $gallery && '' !== trim( (string) $gallery->title ) ) {
				return (string) $gallery->title;
			}
		}

		return (string) $post->post_title;
	}
}
