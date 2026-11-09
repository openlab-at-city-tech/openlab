<?php

namespace Imagely\NGG\Lightroom;

// phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound
use Imagely\NGG\DataMappers\Album as AlbumMapper;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\REST\DataMappers\AlbumREST;

use Imagely\NGG\Util\{Filesystem, MassAssignment, Security};

/**
 * Controller for Lightroom integration.
 */
class Controller {

	/** Throttle key for the anonymous execute endpoint's refusal log. */
	const REFUSAL_LOG_THROTTLE = 'ngg_lr_refusal_logged';

	/**
	 * NextGEN API instance.
	 *
	 * @var object|null
	 */
	protected $nextgen_api = null;

	/**
	 * Whether NextGEN API is locked.
	 *
	 * @var bool
	 */
	protected $nextgen_api_locked = false;

	/**
	 * Whether shutdown is registered.
	 *
	 * @var bool
	 */
	protected $shutdown_registered = false;

	// Nonce verification not possible: the Lightroom client never requests or sends back a nonce. All actions are
	// authenticated.
	//
	// phpcs:disable WordPress.Security.NonceVerification.Missing
	// phpcs:disable WordPress.Security.NonceVerification.Recommended

	public static function run() {
		define( 'DOING_AJAX', true );
		ob_start();

		$self   = new Controller();
		$action = $self->param( 'action' ) . '_action';

		$response = [];

		// The following could be dynamic but is written this way to prevent warnings that the methods aren't in use.
		if ( 'enqueue_nextgen_api_task_list_action' === $action ) {
			$response = $self->enqueue_nextgen_api_task_list_action();
		} elseif ( 'execute_nextgen_api_task_list_action' === $action ) {
			$response = $self->execute_nextgen_api_task_list_action();
		} elseif ( 'get_nextgen_api_path_list_action' === $action ) {
			$response = $self->get_nextgen_api_path_list_action();
		} elseif ( 'get_nextgen_api_token_action' === $action ) {
			$response = $self->get_nextgen_api_token_action();
		}

		// Flush the buffer.
		$buffer_limit = 0;
		$zlib         = ini_get( 'zlib.output_compression' );
		if ( ! is_numeric( $zlib ) && $zlib == 'On' ) {
			$buffer_limit = 1;
		} elseif ( is_numeric( $zlib ) && $zlib > 0 ) {
			$buffer_limit = 1;
		}

		while ( ob_get_level() != $buffer_limit ) {
			ob_end_clean();
		}

		wp_send_json( $response );
	}

	/**
	 * Gets a request parameter value.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function param( $key ) {
		if ( isset( $_REQUEST[ $key ] ) ) {
			return $this->recursive_stripslashes( sanitize_text_field( wp_unslash( $_REQUEST[ $key ] ) ) );
		}
	}

	/**
	 * Gets a JSON parameter value, undoes WordPress magic-quoting, and decodes it.
	 *
	 * Unlike param(), this method calls wp_unslash() exactly once (to undo WordPress's
	 * wp_magic_quotes() which runs addslashes() on $_REQUEST at startup), but does NOT
	 * call recursive_stripslashes() a second time. That second call in param() corrupts
	 * JSON-encoded Windows paths: C:\\Users\\ becomes C:\Users\, leaving invalid JSON
	 * escape sequences (\U, \A, etc.) that cause json_decode() to return null.
	 *
	 * @param string $key
	 * @return mixed Decoded value, or null if the parameter is absent or not valid JSON.
	 */
	public function param_json( $key ) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- raw JSON, unslashed exactly once below before json_decode().
		$raw = isset( $_REQUEST[ $key ] ) ? $_REQUEST[ $key ] : null;
		if ( is_string( $raw ) ) {
			// WordPress's wp_magic_quotes() applies addslashes() to $_REQUEST at startup,
			// escaping all " to \" and \ to \\. We must call wp_unslash() once to undo
			// that and restore valid JSON. We must NOT call recursive_stripslashes() a
			// second time (as param() does), because that strips the path backslashes again
			// (C:\\Users\\ -> C:\Users\), leaving invalid escape sequences (\U, \A, etc.)
			// that cause json_decode() to return null on Windows paths.
			return json_decode( wp_unslash( $raw ), true );
		}
		return $raw;
	}

	/**
	 * Recursively calls stripslashes() on strings, arrays, and objects
	 *
	 * Copied here from RoutingApp to maintain compatibility with Lightroom
	 *
	 * @TODO Move this to a better place or find a better solution
	 * @param string|array|\stdClass $value Value to be processed
	 * @return string|array|\stdClass Resulting value
	 */
	public function recursive_stripslashes( $value ) {
		if ( is_string( $value ) ) {
			$value = stripslashes( $value );
		} elseif ( is_array( $value ) ) {
			foreach ( $value as &$tmp ) {
				$tmp = $this->recursive_stripslashes( $tmp );
			}
		} elseif ( is_object( $value ) ) {
			foreach ( get_object_vars( $value ) as $key => $data ) {
				$value->{$key} = $this->recursive_stripslashes( $data );
			}
		}

		return $value;
	}

	/**
	 * Enqueues a NextGEN API task list action.
	 *
	 * @return array
	 */
	public function enqueue_nextgen_api_task_list_action() {
		$api      = $this->get_nextgen_api();
		$user_obj = $this->authenticate_user();
		$response = [];

		if ( $user_obj != null && ! is_a( $user_obj, 'WP_Error' ) ) {
			wp_set_current_user( $user_obj->ID );
			// Use param_json() instead of param() for JSON fields: param() applies wp_unslash()/
			// stripslashes() which converts \\ to \ in JSON strings, leaving invalid escape
			// sequences (e.g. \U, \J) that cause json_decode() to return null on Windows paths.
			$app_config = $this->param_json( 'app_config' );
			$task_list  = $this->param_json( 'task_list' );
			$extra_data = $this->param_json( 'extra_data' ) ?? [];

			if ( ! is_array( $extra_data ) ) {
				$extra_data = [];
			}

			$upload_keys = $this->merge_uploaded_files( $extra_data );

			if ( $task_list != null ) {
				$task_count  = count( $task_list );
				$auth_count  = 0;
				$denied_caps = [];

				foreach ( $task_list as &$task_item ) {
					$task_name  = isset( $task_item['name'] ) ? $task_item['name'] : null;
					$task_type  = isset( $task_item['type'] ) ? $task_item['type'] : null;
					$task_query = isset( $task_item['query'] ) ? $task_item['query'] : null;

					$task_auth = false;

					// The capability each refused task was actually missing, so the response can
					// say which one rather than naming a single capability for every refusal.
					$task_denied_cap = null;

					switch ( $task_type ) {
						case 'gallery_add':
							$task_auth       = Security::is_allowed( 'nextgen_edit_gallery' );
							$task_denied_cap = 'NextGEN Manage gallery';
							break;
						case 'gallery_list_get':
							// Reads every gallery's title/description/preview via find_all() in
							// handle_job(). It had no case here at all, so it was stamped 'forbid'
							// and skipped - which the old $task_count == $auth_count gate at least
							// surfaced as an outright error. Under the relaxed gate it would have
							// become a silent no-result on a successful job.
							$task_auth       = Security::is_allowed( 'nextgen_edit_gallery' );
							$task_denied_cap = 'NextGEN Manage gallery';
							break;
						case 'gallery_remove':
						case 'gallery_edit':
							$query_id = $api->get_query_id( $task_query['id'], $task_list );
							$gallery  = null;

							// The old NextGEN XMLRPC API had this logic so replicating it here for safety.
							if ( $query_id ) {
								$gallery_mapper = GalleryMapper::get_instance();
								$gallery        = $gallery_mapper->find( $query_id );
							}

							/*
							 * The "NextGEN Manage gallery" capability is required in every case
							 * (#932), and ownership is never authorization on its own (#933): the
							 * stored author id outlives the capability being revoked, and a
							 * Subscriber who happens to own a gallery must not be able to edit it
							 * through this endpoint. Past that gate, ownership decides whether the
							 * additional "NextGEN Manage others gallery" capability is also needed.
							 *
							 * The author column is compared as an integer: the mapper returns it as
							 * a numeric string, so a strict comparison against the user id is false
							 * for every user.
							 */
							$task_auth       = Security::is_allowed( 'nextgen_edit_gallery' );
							$task_denied_cap = 'NextGEN Manage gallery';

							if ( $gallery != null ) {
								$is_owner = ( (int) \get_current_user_id() === (int) $gallery->author );

								$task_auth = Security::can_manage_owned( $gallery->author, 'nextgen_edit_gallery' );

								// Which capability to name in the refusal (#933): an owner who is
								// refused is missing the base capability, anyone else is missing the
								// others-gallery one. Only reported when the base gate passed, since
								// without it the base capability is what is missing.
								if ( ! $is_owner && Security::is_allowed( 'nextgen_edit_gallery' ) ) {
									$task_denied_cap = 'NextGEN Manage others gallery';
								}
							}

							break;
						case 'album_add':
							$task_auth       = Security::is_allowed( 'nextgen_edit_album' );
							$task_denied_cap = 'NextGEN Edit album';
							break;
						case 'album_remove':
						case 'album_edit':
							$task_auth       = Security::is_allowed( 'nextgen_edit_album' );
							$task_denied_cap = 'NextGEN Edit album';

							// Editing or deleting an existing album additionally requires
							// object-level ownership, mirroring the REST controller and the
							// gallery cases above (#1096). Without it any delegated
							// "NextGEN Edit album" holder could rename, re-point or permanently
							// delete every album on the site through Lightroom. album_add is a
							// create with no id, so it is gated on the base capability alone.
							if ( $task_auth ) {
								$album_id = $api->get_query_id(
									isset( $task_query['id'] ) ? $task_query['id'] : null,
									$task_list
								);

								if ( $album_id && ! AlbumREST::current_user_can_manage_album( $album_id ) ) {
									$task_auth       = false;
									$task_denied_cap = 'NextGEN Manage others gallery';
								}
							}

							break;
						case 'image_list_move':
							// A no-op in handle_job(), so nothing is lost by leaving it unauthorized.
							break;
					}

					if ( $task_auth ) {
						++$auth_count;
					} elseif ( null !== $task_denied_cap ) {
						$denied_caps[ $task_denied_cap ] = true;
					}

					$task_item['auth'] = $task_auth ? 'allow' : 'forbid';
				}

				// Queue the job when at least one task is authorized, rather than requiring every
				// task to be. Each task already carries its own 'auth' flag, and handle_job()
				// re-reads it and skips anything that is not 'allow', so an unauthorized task
				// drops only itself. Requiring $task_count == $auth_count meant one unauthorized
				// task voided the whole publish - a Lightroom collection is submitted as a single
				// task list, so a photographer lost the entire batch, images included, and the
				// only thing the desktop client showed was the generic "Authorization Failed."
				if ( $auth_count > 0 ) {
					$job_id = $api->add_job(
						[
							'user'     => $user_obj->ID,
							'clientid' => $this->param( 'clientid' ),
						],
						$app_config,
						$task_list
					);

					if ( $job_id != null ) {
						$post_back = $api->get_job_post_back( $job_id );

						// A handler URL with no token in it is refused by the routing gate and by
						// the execute action, so the publish would sit in the queue until
						// MAX_JOB_AGE expired it while the client was told 'ok'.
						if ( empty( $post_back['token'] ) ) {
							$api->remove_job( $job_id );
							$api->log_failure( 'job_token_missing', 'NextGEN Gallery: Lightroom discarded job ' . wp_strip_all_tags( (string) $job_id, true ) . ' because it carried no post_back token.' );

							return [
								'result' => 'error',
								'error'  => [
									'code'    => API::ERR_JOB_NOT_ADDED,
									'message' => __( 'Job execution failed.', 'nggallery' ),
								],
							];
						}

						$handler_delay    = defined( 'NGG_API_JOB_HANDLER_DELAY' ) ? intval( NGG_API_JOB_HANDLER_DELAY ) : 0;
						$handler_delay    = $handler_delay > 0 ? $handler_delay : 30; /* in seconds */
						$handler_maxsize  = defined( 'NGG_API_JOB_HANDLER_MAXSIZE' ) ? intval( NGG_API_JOB_HANDLER_MAXSIZE ) : 0;
						$handler_maxsize  = $handler_maxsize > 0 ? $handler_maxsize : $this->get_max_upload_size(); /* in bytes */
						$handler_maxfiles = $this->get_max_upload_files();

						$response['result']        = 'ok';
						$response['result_object'] = [
							'job_id'               => $job_id,
							'job_post_back'        => $post_back,
							// The token travels in the handler URL because the client stores that URL
							// verbatim and posts back to it, so shipped clients send the token with no
							// client-side change. That does put a bearer credential in a query string,
							// where access and proxy logs keep it (CWE-598) - so a site whose client
							// posts job_post_back in the body instead can define
							// NGG_API_TOKEN_IN_HANDLER_URL false and keep it out of the URL. The token
							// is returned in job_post_back either way, and param() reads $_REQUEST, so
							// the body form needs no server change.
							'job_handler_url'      => home_url( $this->get_job_handler_query( $post_back ) ),
							'job_handler_delay'    => $handler_delay,
							'job_handler_maxsize'  => $handler_maxsize,
							'job_handler_maxfiles' => $handler_maxfiles,
						];

						// Name the refused tasks rather than letting them disappear. They are
						// skipped individually at execution time, so without this the client is
						// told the job was accepted and simply never hears about them again.
						if ( $auth_count < $task_count ) {
							$skipped = $task_count - $auth_count;

							$response['result_object']['unauthorized_task_count'] = $skipped;

							$response['warning'] = [
								'code'    => API::ERR_NOT_AUTHORIZED,
								'message' => sprintf(
									/* translators: 1: number of skipped tasks, 2: total number of tasks, 3: comma-separated capability names. */
									__( '%1$s of %2$s tasks were skipped for lack of permission. Ask an administrator to grant your role %3$s under NextGEN Gallery > Settings > Roles.', 'nggallery' ),
									number_format_i18n( $skipped ),
									number_format_i18n( $task_count ),
									self::describe_denied_caps( $denied_caps )
								),
							];
						}

						if ( ! defined( 'NGG_API_SUPPRESS_QUICK_EXECUTE' ) || NGG_API_SUPPRESS_QUICK_EXECUTE == false ) {
							if ( ! $api->is_execution_locked() ) {
								$this->start_locked_execute();

								try {
									$result = $api->handle_job( $job_id, $api->get_job_data( $job_id ), $app_config, $api->get_job_task_list( $job_id ), $extra_data, $upload_keys );

									$response['result_object']['job_result'] = $api->get_job_task_list( $job_id );

									if ( $result ) {
										// everything was finished, remove job.
										$api->remove_job( $job_id );
									}
									// \Throwable rather than \Exception, for the same reason as the
									// executor: a PHP 8 TypeError out of handle_job() is an \Error.
								} catch ( \Throwable $e ) {
									// handle_job() records the upload failures it recognizes against
									// the individual task. Anything escaping to here does not, so log
									// it instead of discarding it - this is the quick-execute leg of
									// the same job the executor runs, and it is where a failure of the
									// capability re-check inside handle_job() would surface.
									// Logged ungated: the record is an exception string, not
									// caller-chosen keys, and a production install does not
									// define WP_DEBUG, so a gate here means the failure is
									// recorded nowhere at all.
									// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
									error_log( 'NextGEN Gallery: Lightroom quick-execute failed: ' . wp_strip_all_tags( (string) $e->getMessage(), true ) );

									// Revise the 'ok' set above: the job is still enqueued
									// because remove_job() was skipped, so the caller has to
									// learn the run failed and retry through job_handler_url
									// rather than treat the publish as finished. Same code and
									// message the executor leg reports for an escaped throwable.
									$response['result'] = 'error';
									$response['error']  = [
										'code'    => API::ERR_JOB_NOT_ADDED,
										'message' => __( 'Job execution failed.', 'nggallery' ),
									];
								}

								$this->stop_locked_execute();
							}
						}
					} else {
						$response['result'] = 'error';
						$response['error']  = [
							'code'    => API::ERR_JOB_NOT_ADDED,
							'message' => __( 'Job could not be added.', 'nggallery' ),
						];
					}
				} else {
					// Name the missing capability rather than returning a bare
					// "Authorization Failed.". Lightroom surfaces one generic line per photo, so a
					// permissions refusal is indistinguishable from a host or network problem - and
					// it is the one cause the photographer can actually act on. Which capability is
					// missing depends on the task, so it is collected per task above rather than
					// hard-coded here.
					$response['result'] = 'error';
					$response['error']  = [
						'code'    => API::ERR_NOT_AUTHORIZED,
						'message' => sprintf(
							/* translators: %s: comma-separated capability names. */
							__( 'Authorization failed: this account lacks the required NextGEN permission. Ask an administrator to grant your role %s under NextGEN Gallery > Settings > Roles.', 'nggallery' ),
							self::describe_denied_caps( $denied_caps )
						),
					];
				}
			} else {
				$response['result'] = 'error';
				$response['error']  = [
					'code'    => API::ERR_NO_TASK_LIST,
					'message' => __( 'No task list was specified.', 'nggallery' ),
				];
			}
		} else {
			$response['result'] = 'error';
			$response['error']  = [
				'code'    => API::ERR_NOT_AUTHENTICATED,
				'message' => __( 'Authentication Failed.', 'nggallery' ),
			];
		}

		return $response;
	}

	/**
	 * Executes a NextGEN API task list action.
	 *
	 * @return array
	 */
	public function execute_nextgen_api_task_list_action() {
		$api      = $this->get_nextgen_api();
		$job_list = $api->get_job_list();
		$response = [];

		// Authentication, first statement, as the three sibling actions do. A caller with
		// real credentials is trusted for every job.
		$user_obj      = $this->authenticate_user();
		$authenticated = ( null !== $user_obj && ! is_a( $user_obj, 'WP_Error' ) );

		if ( $authenticated ) {
			wp_set_current_user( $user_obj->ID );
		}

		// Anonymous by necessity: the desktop client posts to the job_handler_url it was
		// given at enqueue time and sends no credentials of its own, so the gate is the
		// job's post_back token rather than a user.
		//
		// The request cannot name a file to read: uploaded bytes come only from a verified
		// $_FILES entry, and the FTP branch's staged path comes from the stored job and is
		// contained - see is_staged_image_path().
		//
		// Pre-4.5.1 jobs are refused, not grandfathered: a pending pre-update job is
		// exactly #1029's exploit precondition. Such a publish has to be re-run.
		if ( is_array( $job_list ) ) {
			$job_token      = (string) ( $this->param( 'job_post_back' ) ?? '' );
			$executable     = [];
			$refused        = false;
			$expired        = false;
			$not_authorized = false;
			$refused_count  = 0;
			$unclaimed      = 0;
			$cutoff         = time() - API::MAX_JOB_AGE;

			foreach ( $job_list as $key => $job ) {
				$created = isset( $job['created'] ) ? (int) $job['created'] : 0;

				// Age before token: a pre-4.5.1 job has no stamp and its token is the weak
				// md5( uniqid() ). Enforced here and not only in prune_stale_jobs(), which
				// needs an authenticated enqueue, so a leaked job_handler_url expires.
				if ( $created <= 0 || $created <= $cutoff ) {
					$expired = true;
					++$refused_count;

					continue;
				}

				$expected = isset( $job['post_back']['token'] ) ? (string) $job['post_back']['token'] : '';

				// Credentials authorise only the caller's OWN jobs. authenticate_user() checks no
				// capability, so trusting any authenticated account for every job let a
				// Subscriber force another user's pending publish to run - reading and deleting
				// that user's staged originals - because handle_job() then impersonates the job
				// owner. The token rung still covers the credential-less post-back, so shipped
				// clients are unaffected.
				// Ownership is never authorization on its own (#933): the stored author id
				// outlives the capability being revoked. So the credential rung requires the
				// base NextGEN capability, and a non-owner additionally needs the
				// others-gallery capability - the same model this file already uses at
				// enqueue time.
				$job_owner      = isset( $job['data']['user'] ) ? (int) $job['data']['user'] : 0;
				$owns_job       = false;
				$job_lacks_caps = false;

				if ( $authenticated ) {
					$is_owner = $job_owner > 0 && $job_owner === (int) $user_obj->ID;

					$owns_job = Security::is_allowed( 'nextgen_edit_gallery' )
						&& ( $is_owner || Security::is_allowed( 'nextgen_edit_gallery_unowned' ) );

					if ( ! $owns_job ) {
						$not_authorized = true;
						$job_lacks_caps = true;
					}
				}

				if ( $owns_job
					|| ( '' !== $job_token && '' !== $expected && hash_equals( $expected, $job_token ) ) ) {
					$executable[ $key ] = $job;
				} else {
					$refused = true;
					++$refused_count;

					// Turned away only because this request's token is not this job's.
					if ( ! $job_lacks_caps ) {
						++$unclaimed;
					}
				}
			}

			// A post-back request carries one job's token, so once a job ran, the request has
			// proven it belongs to that job and the rest of the queue is simply not its own:
			// a token that matched job A is no guess at job B. Logging those as refusals put a
			// warning in the log on every multi-job publish (nextgen-gallery-pro#880). A token that matched
			// nothing still counts, since that is the token-probing signal.
			if ( $refused && ! empty( $executable ) ) {
				$refused        = false;
				$refused_count -= $unclaimed;
			}

			// Expiry and capability refusals are logged even when another job ran, since
			// otherwise they are invisible. Rate limited because this endpoint is anonymous
			// and a stale job makes $expired true on every request: without the bound, any
			// caller could grow the error log at will.
			if ( ( $expired || $refused || $not_authorized ) && ! get_transient( self::REFUSAL_LOG_THROTTLE ) ) {
				set_transient( self::REFUSAL_LOG_THROTTLE, 1, 5 * MINUTE_IN_SECONDS );

				// Every cause that applied, not just the first: a single stale job makes
				// $expired true on every request to this endpoint, and a ternary chain let that
				// permanently mask the token-mismatch signal this log exists to capture.
				$causes = [];

				if ( $expired ) {
					$causes[] = 'queued before 4.5.1 or past MAX_JOB_AGE';
				}

				if ( $not_authorized ) {
					$causes[] = 'the caller lacks the capability to run them';
				}

				if ( $refused ) {
					$causes[] = '' === $job_token ? 'no post_back token was sent' : 'post_back token did not match';
				}

				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log(
					'NextGEN Gallery: Lightroom refused ' . $refused_count . ' job(s): '
					. implode( '; ', $causes )
					. '. Further refusals are not logged for 5 minutes.'
				);
			}

			if ( empty( $executable ) && ( $refused || $expired || $not_authorized ) ) {
				// Order matters: a request that failed the token check, or lacked the
				// capability, is told that - expiry only speaks when it is the only cause, so a
				// stale job in the list cannot turn an authentication failure into
				// "publish it again".
				if ( $not_authorized ) {
					return [
						'result' => 'error',
						'error'  => [
							'code'    => API::ERR_NOT_AUTHORIZED,
							'message' => __( 'You do not have permission to run this publish.', 'nggallery' ),
						],
					];
				}

				if ( $refused && '' !== $job_token ) {
					return [
						'result' => 'error',
						'error'  => [
							'code'    => API::ERR_NOT_AUTHENTICATED,
							'message' => __( 'Authentication Failed.', 'nggallery' ),
						],
					];
				}

				if ( $expired ) {
					return [
						'result' => 'error',
						'error'  => [
							'code'    => API::ERR_JOB_TOKEN_REQUIRED,
							'message' => __( 'This publish can no longer be resumed - it was queued before the site was updated, or it has expired. Please publish it again.', 'nggallery' ),
						],
					];
				}

				return [
					'result' => 'error',
					'error'  => [
						'code'    => API::ERR_NOT_AUTHENTICATED,
						'message' => __( 'Authentication Failed.', 'nggallery' ),
					],
				];
			}

			$job_list = empty( $executable ) ? null : $executable;
		}

		if ( ! $authenticated && '' === (string) ( $this->param( 'job_post_back' ) ?? '' ) ) {
			return [
				'result' => 'error',
				'error'  => [
					'code'    => API::ERR_NOT_AUTHENTICATED,
					'message' => __( 'Authentication Failed.', 'nggallery' ),
				],
			];
		}

		if ( $api->is_execution_locked() ) {
			$response['result'] = 'ok';
			$response['info']   = [
				'code'    => API::INFO_EXECUTION_LOCKED,
				'message' => __( 'Job execution is locked.', 'nggallery' ),
			];
		} elseif ( $job_list != null ) {
			$this->start_locked_execute();

			// Declared outside the try: when these lived inside it, a throw before they
			// were assigned left both undefined, so the `$done_count == $job_count`
			// comparison below evaluated null == null and the endpoint reported
			// "Job list is finished." for a run in which nothing executed.
			$job_count          = count( $job_list );
			$done_count         = 0;
			$client_result      = [];
			$unpersisted_result = [];
			$job_error          = null;

			try {
				$extra_data = $this->param_json( 'extra_data' ) ?? [];

				if ( ! is_array( $extra_data ) ) {
					$extra_data = [];
				}

				$upload_keys = $this->merge_uploaded_files( $extra_data );

				foreach ( $job_list as $job ) {
					$job_id   = $job['id'];
					$job_data = $job['data'];
					$result   = $api->handle_job( $job_id, $job_data, $job['app_config'], $job['task_list'], $extra_data, $upload_keys );

					// The clientid match is the normal channel, but a job whose results could not
					// be persisted is returned regardless: remove_job() below deletes it, so this
					// response is the only place those per-image errors still exist.
					if ( $api->has_unpersisted_task_list( $job_id ) ) {
						$unpersisted_result[ $job_id ] = $api->get_job_task_list( $job_id );
					}

					if ( $api->has_unpersisted_task_list( $job_id )
						|| ( isset( $job_data['clientid'] ) && $job_data['clientid'] == $this->param( 'clientid' ) ) ) {
						$client_result[ $job_id ] = $api->get_job_task_list( $job_id );
					}

					if ( $result ) {
						++$done_count;

						// everything was finished, remove job.
						$api->remove_job( $job_id );
					}

					if ( $api->should_stop_execution() ) {
						break;
					}
				}
				// \Throwable, not \Exception: handle_job() calls count() and property
				// assignments on values decoded from the request, so a PHP 8 TypeError -
				// an \Error, which \Exception does not catch - is a realistic outcome and
				// used to escape as an uncaught fatal.
			} catch ( \Throwable $e ) {
				// handle_job() catches the upload exceptions it knows about and records
				// them per task. Anything reaching here is outside that set, so it is a
				// genuine failure of the run and must not be discarded: task progress is
				// persisted only after the task loop completes, so an escape here also
				// loses every per-task status accumulated in this call.
				$job_error = $e->getMessage();

				// Recorded on production - this is the leg publishes actually run through - but
				// bounded, because the endpoint is reachable without credentials and a
				// repeatedly failing publish would otherwise grow the log without limit.
				// log_once() still emits every occurrence under WP_DEBUG.
				$api->log_failure(
					// Keyed on where the failure came from, not on its message: the message can
					// embed caller-supplied strings, so hashing it would let a caller with a
					// valid token vary the key at will and defeat the bound this is here for -
					// growing error_log and wp_options together. Class plus file:line still
					// separates genuinely different failures, which is what the key is for.
					'job_execution_' . md5( get_class( $e ) . ':' . basename( (string) $e->getFile() ) . ':' . $e->getLine() ),
					'NextGEN Gallery: Lightroom job execution failed: ' . wp_strip_all_tags( (string) $job_error, true )
				);
			}

			$this->stop_locked_execute();

			if ( null !== $job_error ) {
				$response['result'] = 'error';
				$response['error']  = [
					'code'    => API::ERR_JOB_NOT_ADDED,
					'message' => __( 'Job execution failed.', 'nggallery' ),
				];
			} elseif ( $done_count == $job_count ) {
				$response['result'] = 'ok';
				$response['info']   = [
					'code'    => API::INFO_JOB_LIST_FINISHED,
					'message' => __( 'Job list is finished.', 'nggallery' ),
				];
			} else {
				$response['result'] = 'ok';
				$response['info']   = [
					'code'    => API::INFO_JOB_LIST_UNFINISHED,
					'message' => __( 'Job list is unfinished.', 'nggallery' ),
				];
			}

			// The suppression switch is a payload-size choice, but remove_job() has already
			// deleted the persisted copy, so an unpersisted result set exists nowhere else -
			// those are returned regardless.
			if ( ! defined( 'NGG_API_SUPPRESS_QUICK_SUMMARY' ) || NGG_API_SUPPRESS_QUICK_SUMMARY == false ) {
				$response['result_object'] = $client_result;
			} elseif ( ! empty( $unpersisted_result ) ) {
				$response['result_object'] = $unpersisted_result;
			}
		} else {
			$response['result'] = 'ok';
			$response['info']   = [
				'code'    => API::INFO_NO_JOB_LIST,
				'message' => __( 'Job list is empty.', 'nggallery' ),
			];
		}

		return $response;
	}

	/**
	 * Gets the NextGEN API path list action.
	 *
	 * @return array
	 */
	public function get_nextgen_api_path_list_action() {
		$api = $this->get_nextgen_api();
		// Client sends app_config as a JSON string; param() would leave it a string.
		$app_config = $this->param_json( 'app_config' );
		$user_obj   = $this->authenticate_user();
		$response   = [];

		if ( ! is_array( $app_config ) ) {
			$app_config = [];
		}

		if ( $user_obj != null && ! is_a( $user_obj, 'WP_Error' ) ) {
			wp_set_current_user( $user_obj->ID );

			// Authentication alone isn't authorization: without this, any authenticated
			// account could make the server open an outbound FTP/SSH connection to a
			// caller-named host/port and hand it a caller-named username/password. The
			// sibling enqueue action gates every task on the same capability.
			if ( ! Security::is_allowed( 'nextgen_edit_gallery' ) ) {
				return [
					'result' => 'error',
					'error'  => [
						'code'    => API::ERR_NOT_AUTHORIZED,
						'message' => __( 'You do not have permission to determine the FTP path.', 'nggallery' ),
					],
				];
			}

			// Coerced, not just presence-checked: a non-scalar value (array/object) here
			// reaches ftp_connect()/ssh2_connect() and throws a TypeError that @ can't
			// suppress, so a malformed shape must be normalized rather than passed through.
			$ftp_method = isset( $app_config['ftp_method'] ) && is_scalar( $app_config['ftp_method'] ) ? (string) $app_config['ftp_method'] : 'ftp';
			$creds      = [
				'connection_type' => $ftp_method == 'sftp' ? 'ssh' : 'ftp',
				'hostname'        => isset( $app_config['ftp_host'] ) && is_scalar( $app_config['ftp_host'] ) ? (string) $app_config['ftp_host'] : null,
				'port'            => isset( $app_config['ftp_port'] ) ? absint( $app_config['ftp_port'] ) : null,
				'username'        => isset( $app_config['ftp_user'] ) && is_scalar( $app_config['ftp_user'] ) ? (string) $app_config['ftp_user'] : null,
				'password'        => isset( $app_config['ftp_pass'] ) && is_scalar( $app_config['ftp_pass'] ) ? (string) $app_config['ftp_pass'] : null,
			];

			require_once ABSPATH . 'wp-admin/includes/file.php';

			$wp_filesystem = $api->create_filesystem_access( $creds );
			$root_path     = null;
			$base_path     = null;
			$plugin_path   = null;

			if ( $wp_filesystem ) {
				$root_path   = $wp_filesystem->wp_content_dir();
				$base_path   = $wp_filesystem->abspath();
				$plugin_path = $wp_filesystem->wp_plugins_dir();
			} else {
				// fallbacks when unable to connect, try to see if we know the path already.
				$root_path = get_option( 'ngg_ftp_root_path' );

				if ( defined( 'FTP_BASE' ) ) {
					$base_path = FTP_BASE;
				}

				if ( $root_path == null && defined( 'FTP_CONTENT_DIR' ) ) {
					$root_path = FTP_CONTENT_DIR;
				}

				if ( defined( 'FTP_PLUGIN_DIR' ) ) {
					$plugin_path = FTP_PLUGIN_DIR;
				}

				if ( $base_path == null && $root_path != null ) {
					$base_path = dirname( $root_path );
				}

				if ( $root_path == null && $base_path != null ) {
					$root_path = rtrim( $base_path, '/\\' ) . '/wp-content/';
				}

				if ( $plugin_path == null && $base_path != null ) {
					$plugin_path = rtrim( $base_path, '/\\' ) . '/wp-content/plugins/';
				}
			}

			if ( $root_path != null ) {
				$response['result']        = 'ok';
				$response['result_object'] = [
					'root_path'       => $root_path,
					'wp_content_path' => $root_path,
					'wp_base_path'    => $base_path,
					'wp_plugin_path'  => $plugin_path,
				];
			} elseif ( $wp_filesystem != null ) {

					$response['result'] = 'error';
					$response['error']  = [
						'code'    => API::ERR_FTP_NO_PATH,
						'message' => __( 'Could not determine FTP path.', 'nggallery' ),
					];
			} else {
				$response['result'] = 'error';
				$response['error']  = [
					'code'    => API::ERR_FTP_NOT_CONNECTED,
					'message' => __( 'Could not connect to FTP to determine path.', 'nggallery' ),
				];
			}
		} else {
			$response['result'] = 'error';
			$response['error']  = [
				'code'    => API::ERR_NOT_AUTHENTICATED,
				'message' => __( 'Authentication Failed.', 'nggallery' ),
			];
		}

		return $response;
	}

	/**
	 * Gets the NextGEN API token action.
	 *
	 * @return array
	 */
	public function get_nextgen_api_token_action() {
		$regen    = $this->param( 'regenerate_token' ) ? true : false;
		$user_obj = $this->authenticate_user( $regen );
		$response = [];

		if ( $user_obj != null ) {
			$response['result']        = 'ok';
			$response['result_object'] = [
				'token' => get_user_meta( $user_obj->ID, 'nextgen_api_token', true ),
			];
		} else {
			$response['result'] = 'error';
			$response['error']  = [
				'code'    => API::ERR_NOT_AUTHENTICATED,
				'message' => __( 'Authentication Failed.', 'nggallery' ),
			];
		}

		return $response;
	}

	/**
	 * Builds the job handler query string.
	 *
	 * @param array $post_back
	 * @return string
	 */
	protected function get_job_handler_query( $post_back ): string {
		$query = '?photocrati_ajax=1&action=execute_nextgen_api_task_list';

		if ( defined( 'NGG_API_TOKEN_IN_HANDLER_URL' ) && ! NGG_API_TOKEN_IN_HANDLER_URL ) {
			return $query;
		}

		return $query . '&job_post_back=' . rawurlencode( isset( $post_back['token'] ) ? $post_back['token'] : '' );
	}

	/**
	 * Merges this request's uploads into $extra_data and returns their keys.
	 *
	 * The keys are what tells handle_job() which $extra_data entries carry a real
	 * upload: everything else in that array is caller-supplied JSON and its
	 * 'tmp_name' must never be read from disk.
	 *
	 * @param array $extra_data Passed by reference; uploads are merged into it.
	 * @return string[]
	 */
	protected function merge_uploaded_files( array &$extra_data ): array {
		$upload_keys = [];

		foreach ( $_FILES as $key => $file ) {
			if ( substr( $key, 0, strlen( 'file_data_' ) ) !== 'file_data_' ) {
				continue;
			}

			$data_key = substr( $key, strlen( 'file_data_' ) );

			if ( '' === $data_key ) {
				continue;
			}

			$extra_data[ $data_key ] = $file;
			$upload_keys[]           = (string) $data_key;
		}

		return $upload_keys;
	}

	/**
	 * Gets the NextGEN API instance.
	 *
	 * @return API
	 */
	protected function get_nextgen_api() {
		if ( is_null( $this->nextgen_api ) ) {
			$this->nextgen_api = API::get_instance();
		}

		return $this->nextgen_api;
	}

	/**
	 * Authenticates a user for the API.
	 *
	 * @param bool $regenerate_token
	 * @return object|null
	 */
	protected function authenticate_user( $regenerate_token = false ) {
		$api      = $this->get_nextgen_api();
		$username = $this->param( 'q' );
		$password = $this->param( 'z' );
		$token    = $this->param( 'tok' );

		return $api->authenticate_user( $username, $password, $token, $regenerate_token );
	}

	/**
	 * Gets the maximum upload size.
	 *
	 * @return int
	 */
	protected function get_max_upload_size() {
		static $max_size = -1;

		if ( $max_size < 0 ) {
			$post_max_size = $this->parse_size( ini_get( 'post_max_size' ) );
			if ( $post_max_size > 0 ) {
				$max_size = $post_max_size;
			}

			$upload_max = $this->parse_size( ini_get( 'upload_max_filesize' ) );
			if ( $upload_max > 0 && $upload_max < $max_size ) {
				$max_size = $upload_max;
			}
		}
		return $max_size;
	}

	/**
	 * Parses a size string to bytes.
	 *
	 * @param string $size
	 * @return int
	 */
	protected function parse_size( $size ) {
		$unit = preg_replace( '/[^bkmgtpezy]/i', '', $size );
		$size = preg_replace( '/[^0-9\.]/', '', $size );
		if ( $unit ) {
			return round( $size * pow( 1024, stripos( 'bkmgtpezy', $unit[0] ) ) );
		} else {
			return round( $size );
		}
	}

	/**
	 * Gets the maximum number of upload files.
	 *
	 * @return int
	 */
	protected function get_max_upload_files() {
		return intval( ini_get( 'max_file_uploads' ) );
	}

	/**
	 * Handles shutdown callback.
	 */
	public function do_shutdown() {
		if ( $this->nextgen_api_locked ) {
			$this->get_nextgen_api()->set_execution_locked( false );
		}
	}

	/**
	 * Starts locked execution mode.
	 */
	protected function start_locked_execute() {
		if ( ! $this->shutdown_registered ) {
			\register_shutdown_function( [ $this, 'do_shutdown' ] );
			$this->shutdown_registered = true;
		}

		$this->get_nextgen_api()->set_execution_locked( true );
		$this->nextgen_api_locked = true;
	}

	/**
	 * Stops locked execution mode.
	 */
	protected function stop_locked_execute() {
		$this->get_nextgen_api()->set_execution_locked( false );
		$this->nextgen_api_locked = false;
	}

	/**
	 * Renders the capabilities that refused tasks were missing, for a client-facing message.
	 *
	 * Refusals in one task list can come from different capabilities - a gallery edit needs
	 * "NextGEN Manage gallery" (or "NextGEN Manage others gallery" for someone else's gallery),
	 * an album task needs "NextGEN Edit album" - so naming a single one would misdiagnose the
	 * others. Falls back to a generic phrase when nothing was recorded, which happens when the
	 * only refused tasks are types that need no capability.
	 *
	 * Must live in this class: both call sites are in enqueue_nextgen_api_task_list_action() and
	 * reach it through `self::`, which resolves to the defining class. It was first added below,
	 * inside `class API`, where `self::describe_denied_caps()` from here raised
	 * "Call to undefined method ...\Controller::describe_denied_caps()" on both refusal paths -
	 * a runtime resolution failure that `php -l` cannot see and no CI on this PR would have caught.
	 *
	 * @param array $denied_caps Capability name => true.
	 * @return string
	 */
	private static function describe_denied_caps( array $denied_caps ) {
		$names = array_keys( $denied_caps );

		if ( ! $names ) {
			return __( 'the required NextGEN permissions', 'nggallery' );
		}

		sort( $names );

		return '"' . implode( '", "', $names ) . '"';
	}
}

// phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound
/**
 * Lightroom API class.
 */
class API {

	// NOTE: these constants' numeric values MUST remain the same, do NOT change the values.
	const ERR_NO_TASK_LIST       = 1001;
	const ERR_NOT_AUTHENTICATED  = 1002;
	const ERR_NOT_AUTHORIZED     = 1003;
	const ERR_JOB_NOT_ADDED      = 1004;
	const ERR_JOB_TOKEN_REQUIRED = 1005;

	/**
	 * How long a queued job stays executable.
	 *
	 * Enforced in execute_nextgen_api_task_list_action() as well as in
	 * prune_stale_jobs(), so it bounds the job's post_back token - and therefore a
	 * leaked job_handler_url - and not merely the size of the option.
	 */
	const MAX_JOB_AGE = WEEK_IN_SECONDS;

	const ERR_FTP_NOT_AUTHENTICATED = 1101;
	const ERR_FTP_NOT_CONNECTED     = 1102;
	const ERR_FTP_NO_PATH           = 1103;

	const INFO_NO_JOB_LIST         = 6001;
	const INFO_JOB_LIST_FINISHED   = 6002;
	const INFO_JOB_LIST_UNFINISHED = 6003;
	const INFO_EXECUTION_LOCKED    = 6004;

	/**
	 * Instances cache.
	 *
	 * @var array
	 */
	public static $_instances = [];

	/**
	 * Start time for execution tracking.
	 *
	 * @var int
	 */
	public $_start_time;

	/**
	 * Gets an API instance.
	 *
	 * @param bool|string $context
	 * @return API
	 */
	public static function get_instance( $context = false ) {
		if ( ! isset( self::$_instances[ $context ] ) ) {
			self::$_instances[ $context ] = new API( $context );
		}
		return self::$_instances[ $context ];
	}

	/**
	 * Constructs the API instance.
	 *
	 * @param string|bool $context
	 */
	public function __construct( $context ) {
		$this->_start_time = time();
	}

	/**
	 * Determines if execution should stop.
	 *
	 * @return bool
	 */
	public function should_stop_execution() {
		$timeout = defined( 'NGG_API_JOB_HANDLER_TIMEOUT' ) ? intval( NGG_API_JOB_HANDLER_TIMEOUT ) : ( intval( ini_get( 'max_execution_time' ) ) - 3 );
		$timeout = $timeout > 0 ? $timeout : 27; /* most hosts have a limit of 30 seconds execution time, so 27 should be a safe default */

		return ( time() - $this->_start_time >= $timeout );
	}

	/**
	 * Checks if execution is locked.
	 *
	 * @return bool
	 */
	public function is_execution_locked() {
		$lock_time = get_option( 'ngg_api_execution_lock', 0 );

		if ( $lock_time == 0 ) {
			return false;
		}

		$lock_max = defined( 'NGG_API_EXECUTION_LOCK_MAX' ) ? intval( NGG_API_EXECUTION_LOCK_MAX ) : 0;
		$lock_max = $lock_max > 0 ? $lock_max : 60 * 5; /* if the lock is 5 minutes old assume something went wrong and the lock couldn't be unset */

		$time_diff = time() - $lock_time;

		if ( $time_diff > $lock_max ) {
			return false;
		}

		return true;
	}

	/**
	 * Sets the execution locked state.
	 *
	 * @param bool $locked
	 */
	public function set_execution_locked( $locked ) {
		if ( $locked ) {
			update_option( 'ngg_api_execution_lock', time(), false );
		} else {
			update_option( 'ngg_api_execution_lock', 0, false );
		}
	}

	/**
	 * Gets the job list.
	 *
	 * @return array|null
	 */
	public function get_job_list() {
		return get_option( 'ngg_api_job_list' );
	}

	/**
	 * Generates a job's post_back token.
	 *
	 * PHP's random_bytes() throws rather than returning false when no randomness source
	 * reachable, and add_job() has no try/catch around it - unguarded it would kill the
	 * enqueue mid-output and hand the client a truncated body. Mirrors the ladder
	 * authenticate_user() already keeps.
	 *
	 * @return string 64 hex characters.
	 */
	protected static function generate_token() {
		if ( function_exists( 'random_bytes' ) ) {
			try {
				return bin2hex( random_bytes( 32 ) );
			} catch ( \Throwable $e ) {
				// Recorded: all rungs return 64 hex chars, so a host on the weakest source
				// would look healthy - and this token is the only credential here.
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'NextGEN Gallery: Lightroom could not use random_bytes() for a job token and fell back to a weaker source: ' . wp_strip_all_tags( (string) $e->getMessage(), true ) );
			}
		}

		if ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
			// Inside try/catch because from PHP 8.0 this throws rather than returning false.
			try {
				$strong = false;
				$bytes  = openssl_random_pseudo_bytes( 32, $strong );

				if ( false !== $bytes && $strong ) {
					return bin2hex( $bytes );
				}
			} catch ( \Throwable $e ) {
				$openssl_error = $e->getMessage();
			}
		}

		// No CSPRNG: refuse rather than mint a guessable credential. wp_rand() cannot reach
		// random_int() on such a host either, so it degrades to a seeded pool - and this token
		// is the only credential on an endpoint that accepts no password. add_job() turns the
		// empty string into a refused enqueue, which the client can report, instead of issuing
		// a weak token nobody would know to distrust.
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log(
			'NextGEN Gallery: Lightroom could not generate a job token; no CSPRNG was available, so the publish was refused.'
			. ( isset( $openssl_error ) ? ' openssl: ' . wp_strip_all_tags( (string) $openssl_error, true ) : '' )
		);

		return '';
	}

	/**
	 * Adds a job to the job list.
	 *
	 * @param array $job_data
	 * @param array $app_config
	 * @param array $task_list
	 * @return string|null
	 */
	public function add_job( $job_data, $app_config, $task_list ) {
		$job_token = self::generate_token();

		if ( '' === $job_token ) {
			// generate_token() has already recorded why.
			return null;
		}

		// Before the list is read, so the new job is not built on a pre-prune snapshot.
		$this->prune_stale_jobs();

		// get_job_list() returns false before the option exists, and "false[$id] = …" is
		// deprecated in PHP 8.1 and an error in 9 - so the first publish on a site hit it.
		$job_list = $this->get_job_list();
		$job_list = is_array( $job_list ) ? $job_list : [];
		$job_id   = uniqid();

		while ( isset( $job_list[ $job_id ] ) ) {
			$job_id = uniqid();
		}

		$job = [
			'id'         => $job_id,
			'post_back'  => [
				// Not md5( $job_id ): $job_id is uniqid(), which is a hex encoding of the
				// current microtime with no entropy source, so deriving the token from it
				// gives the token the identifier's ~20 bits rather than the hash's. This
				// value is the only credential on the execute endpoint, so it comes from
				// the CSPRNG. (Same defect class as envira-gallery-plugin#407.)
				'token' => $job_token,
			],
			// Kept so a job's age is knowable. remove_job() runs only when a job
			// completes, so without a timestamp there is nothing to reclaim an abandoned
			// job by, and prune_stale_jobs() below would have nothing to work from.
			'created'    => time(),
			'data'       => $job_data,
			'app_config' => $app_config,
			'task_list'  => $task_list,
		];

		$job_list[ $job_id ] = $job;

		// Checked: the caller's contract is null on failure (ERR_JOB_NOT_ADDED), and returning
		// an id for a job that was never stored told the photographer the publish was queued
		// when nothing would ever run it.
		if ( ! update_option( 'ngg_api_job_list', $job_list, false ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'NextGEN Gallery: Lightroom could not store job ' . wp_strip_all_tags( (string) $job_id, true ) . '; the publish was not queued.' );

			return null;
		}

		return $job_id;
	}

	/**
	 * Records a job-level warning on every task without an error, so a failure after the
	 * task loop still reaches the client. 'warning', not 'fatal': the images did arrive.
	 *
	 * @param array  $task_list
	 * @param string $message
	 * @return array
	 */
	protected function record_job_warning( $task_list, $message ) {
		if ( ! is_array( $task_list ) ) {
			return $task_list;
		}

		foreach ( $task_list as &$task_item ) {
			if ( ! is_array( $task_item ) || ! empty( $task_item['error'] ) ) {
				continue;
			}

			$task_item['error'] = [
				'level'   => 'warning',
				'message' => $message,
			];
		}

		unset( $task_item );

		return $task_list;
	}

	/**
	 * Drops jobs that can no longer be executed.
	 *
	 * Nothing else drains ngg_api_job_list - remove_job() runs only on completion - so
	 * pre-4.5.1 jobs (no 'created' stamp, refused at the execute endpoint) and jobs past
	 * MAX_JOB_AGE would otherwise stay forever.
	 *
	 * Called from add_job(), which is authenticated, so an anonymous caller can never
	 * trigger the cleanup.
	 *
	 * @return void
	 */
	protected function prune_stale_jobs() {
		$job_list = $this->get_job_list();

		if ( ! is_array( $job_list ) || empty( $job_list ) ) {
			return;
		}

		$cutoff = time() - self::MAX_JOB_AGE;
		$kept   = [];

		foreach ( $job_list as $key => $job ) {
			$created = isset( $job['created'] ) ? (int) $job['created'] : 0;

			if ( $created > $cutoff ) {
				$kept[ $key ] = $job;
			}
		}

		if ( count( $kept ) !== count( $job_list ) ) {
			// Logged, like every other refusal this class makes. Dropping a job silently
			// is the defect shape several earlier fixes on this file removed: the next
			// request finds no job, falls through to "Job list is empty." and the caller
			// is told success for a publish that will never complete.
			$dropped = array_diff( array_keys( $job_list ), array_keys( $kept ) );

			// After the write and only if it took: the value always differs here, so false
			// is a real failure, and add_job() would write those jobs straight back.
			if ( update_option( 'ngg_api_job_list', $kept, false ) ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'NextGEN Gallery: Lightroom dropped ' . count( $dropped ) . ' job(s) that can no longer be executed (queued before 4.5.1, or older than ' . (int) self::MAX_JOB_AGE . ' seconds): ' . wp_strip_all_tags( implode( ', ', $dropped ), true ) );
			} else {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'NextGEN Gallery: Lightroom could not drop ' . count( $dropped ) . ' unexecutable job(s); they remain in the job list.' );
			}
		}
	}

	/**
	 * Updates a job in the job list.
	 *
	 * @param string $job_id
	 * @param array  $job
	 * @return bool True when the job exists and the write took (or was a no-op).
	 */
	public function _update_job( $job_id, $job ) {
		$job_list = $this->get_job_list();

		if ( ! isset( $job_list[ $job_id ] ) ) {
			return false;
		}

		if ( $job_list[ $job_id ] === $job ) {
			// Nothing to write. update_option() reports false for an unchanged value, so
			// without this a no-op update would be reported as a write failure.
			return true;
		}

		$job_list[ $job_id ] = $job;

		// Returned, not discarded: a caller persisting something the client must see needs
		// to know whether the write landed.
		return (bool) update_option( 'ngg_api_job_list', $job_list, false );
	}

	/**
	 * Removes a job from the job list.
	 *
	 * @param string $job_id
	 */
	public function remove_job( $job_id ) {
		$job_list = $this->get_job_list();

		if ( isset( $job_list[ $job_id ] ) ) {
			unset( $job_list[ $job_id ] );

			update_option( 'ngg_api_job_list', $job_list, false );
		}
	}

	/**
	 * Task lists whose write did not land, keyed by job id, so the statuses still reach the
	 * caller. Bounded by the request: at most one entry per job executed in it.
	 *
	 * @var array
	 */
	protected $unpersisted_task_lists = [];

	/**
	 * Whether this request holds task results for a job that could not be persisted.
	 *
	 * @param string $job_id
	 * @return bool
	 */
	public function has_unpersisted_task_list( $job_id ) {
		return isset( $this->unpersisted_task_lists[ $job_id ] );
	}

	/**
	 * Gets a job by ID.
	 *
	 * @param string $job_id
	 * @return array|null
	 */
	public function get_job( $job_id ) {
		$job_list = $this->get_job_list();

		if ( isset( $job_list[ $job_id ] ) ) {
			return $job_list[ $job_id ];
		}

		return null;
	}

	/**
	 * Gets job data by job ID.
	 *
	 * @param string $job_id
	 * @return array|null
	 */
	public function get_job_data( $job_id ) {
		$job = $this->get_job( $job_id );

		if ( $job != null ) {
			return $job['data'];
		}

		return null;
	}

	/**
	 * Gets the task list for a job.
	 *
	 * @param string $job_id
	 * @return array|null
	 */
	public function get_job_task_list( $job_id ) {
		// Prefer the in-memory list when a persist failed: both callers re-read this after
		// handle_job() returns, so otherwise a refused write served them the stale pre-run
		// list - every task 'pending', no per-image errors - while the response still
		// reported the job finished.
		if ( isset( $this->unpersisted_task_lists[ $job_id ] ) ) {
			return $this->unpersisted_task_lists[ $job_id ];
		}

		$job = $this->get_job( $job_id );

		if ( $job != null ) {
			return $job['task_list'];
		}

		return null;
	}

	/**
	 * Sets the task list for a job.
	 *
	 * @param string $job_id
	 * @param array  $task_list
	 * @return bool
	 */
	public function set_job_task_list( $job_id, $task_list ) {
		$job = $this->get_job( $job_id );

		if ( $job != null ) {
			$job['task_list'] = $task_list;

			return $this->_update_job( $job_id, $job );
		}

		return false;
	}

	/**
	 * Gets the post-back data for a job.
	 *
	 * @param string $job_id
	 * @return array|null
	 */
	public function get_job_post_back( $job_id ) {
		$job = $this->get_job( $job_id );

		if ( $job != null ) {
			return $job['post_back'];
		}

		return null;
	}

	/**
	 * Authenticates a user for the API.
	 *
	 * @param string      $username
	 * @param string      $password
	 * @param string|null $token
	 * @param bool        $regenerate_token
	 * @return object|null
	 */
	public function authenticate_user( $username, $password, $token, $regenerate_token = false ) {
		$user_obj = null;

		if ( $token != null ) {
			$users = get_users(
				[
					'meta_key'   => 'nextgen_api_token',
					'meta_value' => $token,
				]
			);

			if ( $users != null && count( $users ) > 0 ) {
				$user_obj = $users[0];
			}
		}

		if ( $user_obj == null ) {
			if ( $username != null && $password != null ) {
				$user_obj = wp_authenticate( $username, $password );
				$token    = get_user_meta( $user_obj->ID, 'nextgen_api_token', true );

				if ( $token == null ) {
					$regenerate_token = true;
				}
			}
		}

		if ( is_a( $user_obj, 'WP_Error' ) ) {
			$user_obj = null;
		}

		if ( $regenerate_token ) {
			if ( $user_obj != null ) {
				$token = '';

				if ( function_exists( 'random_bytes' ) ) {
					$token = bin2hex( random_bytes( 16 ) );
				} elseif ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
					$token = bin2hex( openssl_random_pseudo_bytes( 16 ) );
				} else {
					for ( $i = 0; $i < 16; $i++ ) {
						$token .= bin2hex( wp_rand( 0, 15 ) );
					}
				}

				update_user_meta( $user_obj->ID, 'nextgen_api_token', $token );
			}
		}

		return $user_obj;
	}

	/**
	 * Creates filesystem access for FTP/SSH operations.
	 *
	 * @param array       $args
	 * @param string|null $method
	 * @return object|false
	 */
	public function create_filesystem_access( $args, $method = null ) {
		// taken from wp-admin/includes/file.php but with modifications.
		if ( ! $method && isset( $args['connection_type'] ) && 'ssh' == $args['connection_type'] && extension_loaded( 'ssh2' ) && function_exists( 'stream_get_contents' ) ) {
			$method = 'ssh2';
		}
		if ( ! $method && extension_loaded( 'ftp' ) ) {
			$method = 'ftpext';
		}
		if ( ! $method && ( extension_loaded( 'sockets' ) || function_exists( 'fsockopen' ) ) ) {
			$method = 'ftpsockets'; // Sockets: Socket extension; PHP Mode: FSockopen / fwrite / fread.
		}

		if ( ! $method ) {
			return false;
		}

		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';

		if ( ! class_exists( "WP_Filesystem_$method" ) ) {

			/**
			 * Filter the path for a specific filesystem method class file.
			 *
			 * @since 2.6.0
			 *
			 * @see get_filesystem_method()
			 *
			 * @param string $path   Path to the specific filesystem method class file.
			 * @param string $method The filesystem method to use.
			 */
			$abstraction_file = apply_filters( 'filesystem_method_file', ABSPATH . 'wp-admin/includes/class-wp-filesystem-' . $method . '.php', $method );

			if ( ! file_exists( $abstraction_file ) ) {
				return false;
			}

			require_once $abstraction_file;
		}

		$method_class = "WP_Filesystem_$method";

		$wp_filesystem = new $method_class( $args );

		// Define the timeouts for the connections. Only available after the construct is called to allow for per-transport overriding of the default.
		if ( ! defined( 'FS_CONNECT_TIMEOUT' ) ) {
			define( 'FS_CONNECT_TIMEOUT', 30 );
		}
		if ( ! defined( 'FS_TIMEOUT' ) ) {
			define( 'FS_TIMEOUT', 30 );
		}

		if ( is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
			return false;
		}

		if ( ! $wp_filesystem->connect() ) {
			if ( $method == 'ftpext' ) { // attempt connecting with alternative method.
				return $this->create_filesystem_access( $args, 'ftpsockets' );
			}

			return false; // There was an error connecting to the server.
		}

		// Set the permission constants if not already set.
		if ( ! defined( 'FS_CHMOD_DIR' ) ) {
			define( 'FS_CHMOD_DIR', ( fileperms( ABSPATH ) & 0777 | 0755 ) );
		}
		if ( ! defined( 'FS_CHMOD_FILE' ) ) {
			define( 'FS_CHMOD_FILE', ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 ) );
		}

		return $wp_filesystem;
	}

	/**
	 * Returns an actual scalar ID based on parametric ID (e.g. a parametric ID could represent the query ID from another task).
	 *
	 * @param mixed $id
	 * @param array $task_list
	 * @return mixed
	 */
	public function get_query_id( $id, &$task_list ) {
		$task_id = $id;

		if ( is_object( $task_id ) || is_array( $task_id ) ) {
			$id = null;

			// it was specified that the query ID is referencing the query ID from another task.
			if ( isset( $task_id['target'] ) && $task_id['target'] == 'task' ) {
				if ( isset( $task_id['id'] ) && isset( $task_list[ $task_id['id'] ] ) ) {
					$target_task = $task_list[ $task_id['id'] ];

					if ( isset( $target_task['query']['id'] ) ) {
						$id = $target_task['query']['id'];
					}
				}
			}
		}

		return $id;
	}

	/**
	 * Returns an actual scalar ID based on parametric ID (e.g. a parametric ID could represent the resulting object ID from another task).
	 *
	 * @param mixed $id
	 * @param array $result_list
	 * @return mixed
	 */
	public function get_object_id( $id, &$result_list ) {
		$task_id = $id;

		if ( is_object( $task_id ) || is_array( $task_id ) ) {
			$id = null;

			// it was specified that the query ID is referencing the result from another task.
			if ( isset( $task_id['target'] ) && $task_id['target'] == 'task' ) {
				if ( isset( $task_id['id'] ) && isset( $result_list[ $task_id['id'] ] ) ) {
					$target_result = $result_list[ $task_id['id'] ];

					if ( isset( $target_result['object_id'] ) ) {
						$id = $target_result['object_id'];
					}
				}
			}
		}

		return $id;
	}

	/**
	 * Re-verifies at execution time that the job's user may act on a task.
	 *
	 * The `auth` flag each task carries was decided by
	 * enqueue_nextgen_api_task_list_action() and then persisted with the job in the
	 * `ngg_api_job_list` option. execute_nextgen_api_task_list_action() drains that
	 * option without authenticating the requester, and handle_job() impersonates the
	 * stored user, so a capability revoked between enqueue and execute would otherwise
	 * go unnoticed and the queued write would still run. Every task type that destroys
	 * a record, writes a file, or assigns entity properties calls this.
	 *
	 * @param string      $capability     Capability required in all cases.
	 * @param object|null $entity         Entity being acted on, or null when there is none yet.
	 * @param string|null $owner_field    Entity field holding the owner's user ID, when the
	 *                                    entity type has one. Albums have no owner column.
	 * @param string|null $unowned_capability Capability that permits acting on someone
	 *                                    else's record.
	 * @return bool
	 */
	protected function task_is_authorized( $capability, $entity = null, $owner_field = null, $unowned_capability = null ) {
		// No entity, or an entity type with no owner column, leaves nothing to narrow on.
		if ( null === $entity || null === $owner_field || null === $unowned_capability
			|| ! isset( $entity->{$owner_field} ) ) {
			return Security::is_allowed( $capability );
		}

		return Security::can_manage_owned( $entity->{$owner_field}, $capability, $unowned_capability );
	}

	/**
	 * Finds an array entry by key and value.
	 *
	 * @param array  $array_target
	 * @param string $entry_key
	 * @param mixed  $entry_value
	 * @return int|string|null
	 */
	public function _array_find_by_entry( array $array_target, $entry_key, $entry_value ) {
		foreach ( $array_target as $key => $value ) {
			$item = $value;

			if ( isset( $item[ $entry_key ] ) && $item[ $entry_key ] == $entry_value ) {
				return $key;
			}
		}

		return null;
	}

	/**
	 * Filters an array by entry key.
	 *
	 * @param array  $array_target
	 * @param array  $array_source
	 * @param string $entry_key
	 * @return array
	 */
	public function _array_filter_by_entry( array $array_target, array $array_source, $entry_key ) {
		foreach ( $array_source as $key => $value ) {
			$item = $value;

			if ( isset( $item[ $entry_key ] ) ) {
				$find_key = $this->_array_find_by_entry( $array_target, $entry_key, $item[ $entry_key ] );

				if ( $find_key !== null ) {
					unset( $array_target[ $find_key ] );
				}
			}
		}

		return $array_target;
	}

	/**
	 * Resolves an upload temp path that PHP reported relative.
	 *
	 * PHP's is_uploaded_file() compares against its own per-request registry by
	 * string, so it returns true for the relative name PHP recorded - but
	 * file_get_contents() would then resolve that name against the process working
	 * directory and miss. #460 is that failure, confirmed on a customer install where
	 * tmp_name was "tmp/phprXw5Dc".
	 *
	 * Several bases are tried because the failing configurations disagree: #460's
	 * confirmed host has an upload_tmp_dir outside the open_basedir jail, while its
	 * configuration B has ini_get() reporting a directory PHP did not write to. First
	 * readable candidate wins, and which one is logged.
	 *
	 * Only call this after is_uploaded_file() has accepted the original string. The
	 * input is checked with is_safe_path_string() before any candidate is built, and
	 * each candidate must be contained in the base it was built from - a realpath()
	 * pass, so a symlink planted in a world-writable temp directory cannot point the
	 * read outside it.
	 *
	 * @param string $tmp_name Path as PHP recorded it.
	 * @return string Absolute path where one was found, otherwise the input unchanged.
	 */
	protected function resolve_upload_tmp_path( string $tmp_name ): string {
		$normalized = str_replace( '\\', '/', $tmp_name );

		// Already absolute, POSIX or drive-letter.
		if ( 0 === strpos( $normalized, '/' ) || 1 === preg_match( '#^[A-Za-z]:/#', $normalized ) ) {
			return $tmp_name;
		}

		// The resolved path reaches a read, so it gets the same string rejections as
		// every other path this class handles.
		if ( ! $this->is_safe_path_string( $normalized ) ) {
			return $tmp_name;
		}

		$upload_tmp_dir = ini_get( 'upload_tmp_dir' );
		$relative       = ltrim( $normalized, '/' );
		$leaf           = basename( $relative );
		$candidates     = [];

		// Both join shapes: #460's host reports tmp_name as "tmp/phprXw5Dc" with an
		// upload_tmp_dir of ".../tmp/", so joining the whole relative path gives
		// ".../tmp/tmp/phprXw5Dc" and misses, while the basename against it is the file.
		foreach ( [ $upload_tmp_dir, sys_get_temp_dir() ] as $base ) {
			$candidates[] = [ $base, $relative ];
			$candidates[] = [ $base, $leaf ];
		}

		// Last resort for jailed layouts, where the recorded name is relative to the web
		// root rather than to a temp directory. There is deliberately no '/' candidate:
		// Security::path_is_within() refuses any base that reduces to the filesystem
		// root, so contain_path( $x, '/' ) is always null and such a candidate could
		// never be reached.
		$candidates[] = [ ABSPATH, $relative ];

		foreach ( $candidates as list( $base, $suffix ) ) {
			if ( ! is_string( $base ) || '' === trim( $base ) || '' === $suffix ) {
				continue;
			}

			$candidate = rtrim( str_replace( '\\', '/', $base ), '/' ) . '/' . $suffix;

			// Real containment. An earlier revision compared basenames, which is a
			// tautology here - the candidate is the base plus that suffix - so it rejected
			// nothing. contain_path()'s realpath() pass catches a planted symlink.
			if ( null === Security::contain_path( $candidate, $base ) ) {
				continue;
			}

			if ( is_readable( $candidate ) && is_file( $candidate ) ) {
				// Gated: fires once per image, so a few hundred photos would be a line each.
				// The failure below stays ungated - that is the record #460 needs.
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
					error_log( 'NextGEN Gallery: Lightroom resolved the relative upload path "' . wp_strip_all_tags( $normalized, true ) . '" to "' . wp_strip_all_tags( $candidate, true ) . '".' );
				}

				return $candidate;
			}
		}

		// Nothing resolved. Logged, because otherwise this is indistinguishable from
		// "the path was already absolute" and the read failure that follows reports the
		// same generic message #460 was filed about.
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( 'NextGEN Gallery: Lightroom could not resolve the relative upload path "' . wp_strip_all_tags( $normalized, true ) . '" against any known temporary directory.' );

		return $tmp_name;
	}

	/**
	 * Names a PHP upload error code, so a rejected upload is distinguishable from a
	 * file that could not be read.
	 *
	 * #460 item 2: there was no UPLOAD_ERR_* reference anywhere in this integration,
	 * so UPLOAD_ERR_NO_TMP_DIR and UPLOAD_ERR_CANT_WRITE - both host-configuration
	 * faults with a clear remedy - surfaced as the same "Could not obtain data for
	 * image" string as every other cause.
	 *
	 * @param int $code One of the UPLOAD_ERR_* constants.
	 * @return string
	 */
	protected static function describe_upload_error( int $code ): string {
		switch ( $code ) {
			case UPLOAD_ERR_INI_SIZE:
				return 'UPLOAD_ERR_INI_SIZE';
			case UPLOAD_ERR_FORM_SIZE:
				return 'UPLOAD_ERR_FORM_SIZE';
			case UPLOAD_ERR_PARTIAL:
				return 'UPLOAD_ERR_PARTIAL';
			case UPLOAD_ERR_NO_FILE:
				return 'UPLOAD_ERR_NO_FILE';
			case UPLOAD_ERR_NO_TMP_DIR:
				return 'UPLOAD_ERR_NO_TMP_DIR';
			case UPLOAD_ERR_CANT_WRITE:
				return 'UPLOAD_ERR_CANT_WRITE';
			case UPLOAD_ERR_EXTENSION:
				return 'UPLOAD_ERR_EXTENSION';
			default:
				return 'UPLOAD_ERR_' . $code;
		}
	}

	/**
	 * Checks whether a byte string is a real image.
	 *
	 * PHP's getimagesizefromstring() reads the header only, so this does not depend on
	 * file existing locally - the FTP method hands us bytes, not a path.
	 *
	 * @param string $data
	 * @return bool
	 */
	protected function is_image_data( string $data, string $filename = '' ): bool {
		$named = '' === $filename ? '(unnamed)' : wp_strip_all_tags( $filename, true );

		if ( ! function_exists( 'getimagesizefromstring' ) ) {
			// A host decision, not a bad file: every staged image is refused here, so it is
			// recorded as such. Fails closed rather than ingesting and deleting an unknown file.
			$this->log_once(
				'no_getimagesize',
				'NextGEN Gallery: Lightroom cannot verify image headers on this host (getimagesizefromstring is unavailable), so staged files are refused.'
			);

			return false;
		}

		$info = @getimagesizefromstring( $data ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

		if ( ! is_array( $info ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'NextGEN Gallery: Lightroom could not read an image header from the staged bytes for ' . $named . ', so the file was left in place.' );

			return false;
		}

		if ( empty( $info[0] ) || empty( $info[1] ) || empty( $info['mime'] )
			|| 0 !== strpos( (string) $info['mime'], 'image/' ) ) {
			// The header parsed but is not an image. Naming the detected type is the difference
			// between "your file is broken" and "that is a PDF".
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'NextGEN Gallery: Lightroom refused a staged file that is not an image for ' . $named . '; detected type: ' . wp_strip_all_tags( (string) ( $info['mime'] ?? 'unknown' ), true ) . '.' );

			return false;
		}

		return true;
	}

	/**
	 * Public wrapper for log_once(), for the Controller's execution legs.
	 *
	 * @param string $key
	 * @param string $message
	 * @return void
	 */
	public function log_failure( $key, $message ) {
		$this->log_once( $key, $message );
	}

	/**
	 * Records a message at most once per interval, and always under WP_DEBUG.
	 *
	 * Ungated is not unbounded: this endpoint is reachable without credentials, so a
	 * repeatedly failing publish could otherwise grow the error log without limit. Debug
	 * installs still get every occurrence.
	 *
	 * @param string $key     Short identifier for the kind of message.
	 * @param string $message
	 * @return void
	 */
	protected function log_once( $key, $message ) {
		$debug = defined( 'WP_DEBUG' ) && WP_DEBUG;

		if ( ! $debug ) {
			$transient = 'ngg_lr_log_' . md5( (string) $key );

			if ( get_transient( $transient ) ) {
				return;
			}

			set_transient( $transient, 1, 5 * MINUTE_IN_SECONDS );
		}

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( $message );
	}

	/**
	 * Rejects path strings that are never legitimate here, whatever filesystem they
	 * address: phar payloads, stream wrappers, traversal and NUL bytes.
	 *
	 * Separate from the containment checks below because it is the only part that
	 * applies to a remote (FTP/SFTP) path as well as a local one.
	 *
	 * @param string $path
	 * @return bool
	 */
	public function is_safe_path_string( string $path ): bool {
		$path = str_replace( '\\', '/', $path );

		if ( '' === trim( $path ) ) {
			return false;
		}

		// Reject NUL bytes: they truncate the path inside the underlying C calls.
		if ( false !== strpos( $path, "\0" ) ) {
			return false;
		}

		// Do not allow phar:// streams, and block ".phar" filenames as well.
		if ( false !== strpos( $path, '.phar' ) || false !== strpos( $path, 'phar://' ) ) {
			return false;
		}

		// Also block all streams for good measure.
		if ( false !== strpos( $path, '://' ) ) {
			return false;
		}

		// And prevent all "../".
		if ( false !== strpos( $path, '../' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks whether a local path is one this integration may read or write.
	 *
	 * Fails closed: the path must sit in a directory the caller does not choose. The old
	 * implementation assigned strstr( $filename, '/tmp' ) back to $filename, so on a host
	 * whose temp dir is /tmp any path without "/tmp" in it became false, the containment
	 * test was skipped, and every other absolute path passed (#1029).
	 *
	 * The gallery document root alone would not be enough: NGG_GALLERY_ROOT_TYPE defaults
	 * to 'site', so that root is ABSPATH, which holds wp-config.php.
	 *
	 * Containment uses Util\Security::contain_path() for its realpath() pass, so a symlink
	 * planted in an allowed directory cannot point out of it.
	 *
	 * Local paths only - is_staged_image_path() handles the remote (FTP) case.
	 *
	 * @param string $filename
	 * @return bool
	 */
	public function is_valid_filename( string $filename ): bool {
		if ( ! $this->is_safe_path_string( $filename ) ) {
			return false;
		}

		$filename = str_replace( '\\', '/', $filename );

		$filename = $this->normalize_bitnami_path( $filename );

		// Denied before allowed: the code and translation directories sit inside wp-content,
		// so the allow-list admits them - and a plugin's or theme's .png is a real image,
		// which means it would otherwise pass the image-bytes check and be republished into a
		// public gallery and then deleted. Credit to #1032 for this one.
		if ( $this->is_code_directory_path( $filename ) ) {
			return false;
		}

		$directories = array_map( [ $this, 'normalize_bitnami_path' ], $this->get_allowed_directories() );

		foreach ( $directories as $directory ) {
			if ( null !== Security::contain_path( $filename, $directory ) ) {
				return true;
			}
		}

		// Windows only. contain_path() compares case-sensitively, but Windows paths are
		// not, so a staging path whose casing differs from WP_CONTENT_DIR - the client
		// supplies full_path - would be refused for a difference the OS does not make.
		// This costs no ground on POSIX, where the loop above is the whole answer.
		if ( '\\' === DIRECTORY_SEPARATOR ) {
			foreach ( $directories as $directory ) {
				if ( $this->is_path_within( $filename, $directory ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Applies the Bitnami path rewrite.
	 *
	 * Bitnami stores files under /opt/bitnami but PHP can report the pre-symlink /bitnami
	 * form, which used to make containment reject legitimate files (87543794). It has to be
	 * applied to both sides of the comparison: contain_path()'s first pass is lexical, so a
	 * miss returns before the realpath() pass that would reconcile the symlink, and
	 * rewriting only the subject would refuse a pair that should match.
	 *
	 * @param string $path
	 * @return string
	 */
	protected function normalize_bitnami_path( $path ): string {
		if ( ! is_string( $path ) ) {
			return '';
		}

		return 0 === strpos( $path, '/bitnami' ) ? '/opt' . $path : $path;
	}

	/**
	 * Gets the local directories this integration may read from and write to.
	 *
	 * PHP's upload temp directory is deliberately absent: the only place upload
	 * bytes are read is the $_FILES branch of handle_job(), which asks
	 * is_uploaded_file() instead - PHP's own authoritative answer - so whitelisting
	 * the temp directory here would only widen the FTP branch for no gain.
	 *
	 * @return string[]
	 */
	protected function get_allowed_directories(): array {
		return $this->collect_allowed_directories();
	}

	/**
	 * Whether a path is inside - or is - a directory holding code or translations.
	 *
	 * Nothing Lightroom does has any business reading or deleting there. Each base is
	 * compared in its reported and resolved spellings, for the same reason
	 * collect_allowed_directories() does.
	 *
	 * @param string $path
	 * @return bool
	 */
	protected function is_code_directory_path( string $path ): bool {
		// The same sources DataStorage\Manager builds its protected_trees from, because a
		// thinner third copy of that policy is how a gap gets in: WP_LANG_DIR rather than a
		// hardcoded wp-content/languages (core always defines it, and a site can relocate it),
		// and the template and stylesheet directories as well as the theme root, so a theme
		// tree under a root added with register_theme_directory() is covered too.
		$fs     = Filesystem::get_instance();
		$denied = [
			$fs->get_document_root( 'plugins' ),
			$fs->get_document_root( 'plugins_mu' ),
			$fs->get_document_root( 'templates' ),
			$fs->get_document_root( 'stylesheets' ),
			defined( 'WP_LANG_DIR' ) ? WP_LANG_DIR : WP_CONTENT_DIR . '/languages',
		];

		if ( function_exists( 'get_theme_root' ) ) {
			$denied[] = get_theme_root();
		}

		if ( ! empty( $GLOBALS['wp_theme_directories'] ) && is_array( $GLOBALS['wp_theme_directories'] ) ) {
			foreach ( $GLOBALS['wp_theme_directories'] as $theme_dir ) {
				$denied[] = $theme_dir;
			}
		}

		// Both sides resolved, not just the bases. The allow-list below resolves the subject
		// through Security::contain_path(), so testing the subject only lexically here left a
		// symlink under wp-content pointing into a plugin or theme directory missed by this
		// gate and then admitted by that one - the reported-versus-resolved asymmetry this
		// containment code has already been corrected for twice.
		$subjects = $this->resolve_path_forms( $path );

		foreach ( $denied as $base ) {
			if ( ! is_string( $base ) || '' === trim( $base ) ) {
				continue;
			}

			foreach ( $this->resolve_path_forms( $base ) as $form ) {
				$form_prefix = rtrim( $form, '/' ) . '/';

				foreach ( $subjects as $subject ) {
					// Equality as well as containment, so a staging folder that *is* one of
					// these directories is refused, not just a file beneath it.
					//
					// Compared case-insensitively, unconditionally. On a case-insensitive mount
					// - macOS, a CIFS/NTFS bind mount, Docker Desktop over APFS - the OS opens
					// wp-content/PLUGINS/... as the real plugins directory while realpath()
					// hands the requested spelling straight back, so resolving the path is not
					// enough on its own. This is the deny side, so comparing too eagerly can
					// only refuse a staging folder that differs from a code directory by case
					// alone, which is not a layout anything legitimate uses.
					if ( $this->is_path_within( $subject, $form )
						|| rtrim( $subject, '/' ) === rtrim( $form, '/' )
						|| 0 === strncasecmp( $subject, $form_prefix, strlen( $form_prefix ) )
						|| 0 === strcasecmp( rtrim( $subject, '/' ), rtrim( $form, '/' ) ) ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * A path in every spelling it could be compared under: normalised, Bitnami-rewritten,
	 * and resolved.
	 *
	 * When the path itself does not exist - a staged file already consumed, a folder about to
	 * be written - the deepest existing ancestor is resolved and the remainder re-attached,
	 * so a symlinked parent is still reconciled.
	 *
	 * @param string $path
	 * @return string[]
	 */
	protected function resolve_path_forms( string $path ): array {
		$normalized = $this->normalize_path_for_compare( $this->normalize_bitnami_path( wp_normalize_path( $path ) ) );
		$forms      = [ $normalized ];

		$real = @realpath( $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

		if ( is_string( $real ) && '' !== $real ) {
			$forms[] = $this->normalize_path_for_compare( $this->normalize_bitnami_path( wp_normalize_path( $real ) ) );

			return array_values( array_unique( $forms ) );
		}

		// Walk up to the deepest ancestor that does exist.
		$segments  = explode( '/', $normalized );
		$remaining = count( $segments );
		$tail      = [];

		while ( $remaining > 1 ) {
			$tail[] = array_pop( $segments );
			--$remaining;
			$probe = implode( '/', $segments );

			if ( '' === $probe || '/' === $probe ) {
				break;
			}

			$resolved = @realpath( $probe ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

			if ( is_string( $resolved ) && '' !== $resolved ) {
				$forms[] = $this->normalize_path_for_compare(
					$this->normalize_bitnami_path( wp_normalize_path( $resolved ) ) . '/' . implode( '/', array_reverse( $tail ) )
				);

				break;
			}
		}

		return array_values( array_unique( $forms ) );
	}

	/**
	 * Builds the allowed-directory list.
	 *
	 * @return string[]
	 */
	protected function collect_allowed_directories(): array {
		$directories = [
			// The FTP upload method stages files under wp-content.
			WP_CONTENT_DIR,
		];

		// The gallery storage directory is normally inside wp-content, but the
		// gallerypath setting can place it elsewhere.
		//
		// Not wrapped in try/catch: get_upload_abspath() has no throw site, so a catch
		// here would be dead code whose only effect is to make a narrowed allow-list look
		// handled. The array_filter() below drops an empty return.
		$directories[] = StorageManager::get_instance()->get_upload_abspath();

		// Each base is also offered in its realpath() form, because contain_path() rejects on
		// its lexical pass before it ever calls realpath() - so a host whose reported paths
		// differ from their resolved ones (a symlinked wp-content or WordPress root, Plesk,
		// cPanel, a Docker bind mount, any Bitnami layout other than the hardcoded /bitnami
		// spelling) was refused for a difference that is not a containment failure.
		//
		// Expanded BEFORE the ancestor check below, never after: an earlier revision appended
		// these afterwards, and on a symlinked root a gallerypath of '../' resolved to a
		// directory above the real root that was lexically unrelated to ABSPATH - so it passed
		// the filter and re-admitted wp-config.php by the resolved spelling.
		foreach ( $directories as $directory ) {
			if ( ! is_string( $directory ) || '' === trim( $directory ) ) {
				continue;
			}

			$real = @realpath( $directory ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

			if ( is_string( $real ) && '' !== $real ) {
				$directories[] = wp_normalize_path( $real );

				continue;
			}

			// realpath() returns false exactly when the target crosses an open_basedir
			// boundary - the same condition that makes reported and resolved paths differ, so
			// this repair failing is worth a line on the hosts it was written for.
			$this->log_once(
				'unresolved_base_' . md5( (string) $directory ),
				'NextGEN Gallery: Lightroom could not resolve an allowed directory, so containment uses its reported form only: ' . wp_strip_all_tags( (string) $directory, true )
			);
		}

		$directories = array_unique( $directories );

		// The WordPress root in every spelling a base could be compared against: reported,
		// resolved, and Bitnami-rewritten.
		$wp_roots  = [ $this->normalize_bitnami_path( wp_normalize_path( ABSPATH ) ) ];
		$real_root = @realpath( ABSPATH ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

		if ( is_string( $real_root ) && '' !== $real_root ) {
			$wp_roots[] = $this->normalize_bitnami_path( wp_normalize_path( $real_root ) );
		}

		return array_values(
			array_filter(
				$directories,
				function ( $directory ) use ( $wp_roots ) {
					if ( ! is_string( $directory ) || '' === trim( $directory ) ) {
						return false;
					}

					$directory = $this->normalize_bitnami_path( $directory );

					// Never allow a directory that contains the WordPress root, in either
					// spelling. gallerypath is admin-settable, and '../' or '/' resolves the
					// gallery root to an ancestor of ABSPATH, which would re-admit
					// wp-config.php.
					//
					// Tested with the lexical comparison, not contain_path(): that returns null
					// the moment its lexical pass misses, and null here would mean "keep" - so a
					// resolved base that is an ancestor of the reported root passed the check it
					// was supposed to fail.
					foreach ( $wp_roots as $root ) {
						if ( $this->is_path_within( $root, $directory ) || rtrim( $root, '/' ) === rtrim( $directory, '/' ) ) {
							return false;
						}
					}

					return true;
				}
			)
		);
	}

	/**
	 * Checks whether a staged image path may be read and deleted.
	 *
	 * Two boundaries, because the filesystem methods differ on where the file is:
	 *
	 * - 'direct': local, so it must sit in a directory the caller did not choose
	 *   (is_valid_filename()). Containment against $images_folder alone would be
	 *   self-referential - that folder is built from request JSON.
	 * - FTP/SFTP: the path is on the remote account, where no local root and no realpath()
	 *   apply, so the bound is the job's staging folder plus the string rejections.
	 *
	 * What this does NOT close: on 'direct' an enqueuer can still steer the read-and-delete
	 * at any file under wp-content, and on FTP/SFTP the staging folder is caller-configured,
	 * so the bound is on the suffix, not the location (same for the status write). Both need
	 * a server-side staging root - #1003.
	 *
	 * @param string|null $image_path
	 * @param string      $images_folder
	 * @param object      $wp_fs         WP_Filesystem instance.
	 * @return bool
	 */
	protected function is_staged_image_path( $image_path, $images_folder, $wp_fs ): bool {
		if ( ! is_string( $image_path ) || ! $this->is_safe_path_string( $image_path ) ) {
			return false;
		}

		// The file has to sit in this job's own staging folder. Necessary but not
		// sufficient on a local filesystem - see the note above.
		if ( ! $this->is_path_within( $image_path, $images_folder ) ) {
			return false;
		}

		if ( ! is_object( $wp_fs ) || ! isset( $wp_fs->method ) || 'direct' !== $wp_fs->method ) {
			return true;
		}

		return $this->is_valid_filename( $image_path );
	}

	/**
	 * Checks whether a path may be written to or removed.
	 *
	 * Used for the staging directory that rmdir() removes and for the job status file.
	 * Neither had a containment check of any kind: rmdir()'s only preconditions were
	 * that $storage_path and $path_prefix were non-empty and not a bare separator, and
	 * both are request-supplied.
	 *
	 * Same direct/remote split as is_staged_image_path(), and the same limitation: on
	 * a remote filesystem this is only the string filter, so a caller who can enqueue
	 * still chooses the remote directory written to, bounded by the FTP account's own
	 * scope. See the note on is_staged_image_path() - the write side of #1003 is open
	 * for the same reason as the read side, and is not closed by this method.
	 *
	 * @param string $path  Directory or file path.
	 * @param object $wp_fs WP_Filesystem instance.
	 * @return bool
	 */
	protected function is_permitted_fs_path( $path, $wp_fs ): bool {
		if ( ! is_string( $path ) || ! $this->is_safe_path_string( $path ) ) {
			return false;
		}

		if ( ! is_object( $wp_fs ) || ! isset( $wp_fs->method ) || 'direct' !== $wp_fs->method ) {
			return true;
		}

		return $this->is_valid_filename( $path );
	}

	/**
	 * Checks whether a path points to a location inside a directory.
	 *
	 * Lexical, so it is meaningful for a remote (FTP/SFTP) path as well as a local
	 * one. Local paths get Security::contain_path() on top, which additionally
	 * resolves symlinks; see is_staged_image_path().
	 *
	 * @param string $path
	 * @param string $directory
	 * @return bool
	 */
	public function is_path_within( string $path, string $directory ): bool {
		$path      = $this->normalize_path_for_compare( $path );
		$directory = $this->normalize_path_for_compare( $directory );

		if ( '' === $path || '' === $directory || '/' === $directory ) {
			return false;
		}

		$directory = rtrim( $directory, '/' ) . '/';

		// Windows paths are case-insensitive; POSIX paths are not.
		if ( '\\' === DIRECTORY_SEPARATOR ) {
			return 0 === strncasecmp( $path, $directory, strlen( $directory ) );
		}

		return 0 === strncmp( $path, $directory, strlen( $directory ) );
	}

	/**
	 * Normalises a path for prefix comparison: forward slashes, no "." or ".."
	 * segments, no repeated separators, no trailing separator.
	 *
	 * @param string $path
	 * @return string
	 */
	protected function normalize_path_for_compare( string $path ): string {
		$path = str_replace( '\\', '/', trim( $path ) );

		if ( '' === $path ) {
			return '';
		}

		$is_absolute = 0 === strpos( $path, '/' );
		$segments    = [];

		foreach ( explode( '/', $path ) as $segment ) {
			if ( '' === $segment || '.' === $segment ) {
				continue;
			}

			if ( '..' === $segment ) {
				array_pop( $segments );

				continue;
			}

			$segments[] = $segment;
		}

		$normalized = implode( '/', $segments );

		return $is_absolute ? '/' . $normalized : $normalized;
	}

	/**
	 * Handles a job execution.
	 *
	 * Note: handle_job only worries about processing the job, it does NOT remove finished jobs anymore, the responsibility is on the caller to remove the job when handle_job returns true, this is to allow calling get_job_*() methods after handle_job has been called.
	 *
	 * @param string     $job_id
	 * @param array      $job_data
	 * @param array      $app_config
	 * @param array      $task_list
	 * @param array|null $extra_data
	 * @param string[]   $upload_keys $extra_data keys whose value came from $_FILES in this
	 *                                request. Only those may be read from disk by path.
	 * @return bool
	 */
	public function handle_job( $job_id, $job_data, $app_config, $task_list, $extra_data = null, $upload_keys = [] ) {
		$upload_keys      = is_array( $upload_keys ) ? array_map( 'strval', $upload_keys ) : [];
		$job_user         = $job_data['user'];
		$task_count       = count( $task_list );
		$done_count       = 0;
		$skip_count       = 0;
		$task_list_result = [];

		wp_set_current_user( $job_user );

		// Prevent PHP warnings about accessing undefined array keys.
		$app_config['ftp_path']  = isset( $app_config['ftp_path'] ) ? $app_config['ftp_path'] : '';
		$app_config['full_path'] = isset( $app_config['full_path'] ) ? $app_config['full_path'] : '';

		/*
		This block does all of the filesystem magic:
		 * - determines web paths based on FTP paths
		 * - initializes the WP_Filesystem mechanism in case this host doesn't support direct file access
		 *   (this might not be 100% reliable right now due to NG core not making use of WP_Filesystem)
		 */
		// $ftp_path is assumed to be WP_CONTENT_DIR as accessed through the FTP mount point.
		$ftp_path  = rtrim( $app_config['ftp_path'], '/\\' );
		$full_path = rtrim( $app_config['full_path'], '/\\' );
		$root_path = rtrim( WP_CONTENT_DIR, '/\\' );

		$creds  = true; // WP_Filesystem(true) requests direct filesystem access.
		$fs_sep = DIRECTORY_SEPARATOR;
		$wp_fs  = null;

		require_once ABSPATH . 'wp-admin/includes/file.php';

		if ( get_filesystem_method() !== 'direct' ) {
			$fs_sep     = '/';
			$ftp_method = isset( $app_config['ftp_method'] ) ? $app_config['ftp_method'] : 'ftp';

			$creds = [
				'connection_type' => $ftp_method == 'sftp' ? 'ssh' : 'ftp',
				'hostname'        => $app_config['ftp_host'],
				'port'            => $app_config['ftp_port'],
				'username'        => $app_config['ftp_user'],
				'password'        => $app_config['ftp_pass'],
			];
		}

		if ( WP_Filesystem( $creds ) ) {
			$wp_fs = $GLOBALS['wp_filesystem'];

			$path_prefix = $full_path;

			if ( $wp_fs->method === 'direct' ) {
				if ( trim( $ftp_path, " \t\n\r\x0B\\" ) == '' ) {
					// Note: if ftp_path is empty, we assume the FTP account home dir is on wp-content.
					$path_prefix = $root_path . $full_path;
				} else {
					$path_prefix = str_replace( $ftp_path, $root_path, $full_path );
				}
			}
		} else {
			include_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
			if ( ! $wp_fs ) {
				$wp_fs = new \WP_Filesystem_Direct( $creds );
			}
		}

		foreach ( $task_list as &$task_item ) {
			$task_id     = isset( $task_item['id'] ) ? $task_item['id'] : null;
			$task_name   = isset( $task_item['name'] ) ? $task_item['name'] : null;
			$task_type   = isset( $task_item['type'] ) ? $task_item['type'] : null;
			$task_auth   = isset( $task_item['auth'] ) ? $task_item['auth'] : null;
			$task_query  = isset( $task_item['query'] ) ? $task_item['query'] : null;
			$task_object = isset( $task_item['object'] ) ? $task_item['object'] : null;
			$task_status = isset( $task_item['status'] ) ? $task_item['status'] : null;
			$task_result = isset( $task_item['result'] ) ? $task_item['result'] : null;

			// Reset per-task, not just where it is used. PHP locals persist across foreach
			// iterations, and the gallery_edit branch assigns this only on the path where a
			// gallery was resolved. Any branch that skips that assignment - a gallery that
			// was not found, or an authorization refusal - would otherwise read the value
			// left behind by an earlier task and, if that task had a chunked image list,
			// rewrite this task's terminal 'error' status to 'unfinished', so the job was
			// never removed and the refused task was re-executed indefinitely.
			$image_list_unfinished = false;

			// make sure we don't repeat execution of already finished tasks.
			if ( $task_status == 'done' ) {
				++$done_count;

				// for previously finished tasks, store the result as it may be needed by future tasks.
				if ( $task_id != null && $task_result != null ) {
					$task_list_result[ $task_id ] = $task_result;
				}

				continue;
			}

			// make sure only valid and authorized tasks are executed.
			if ( $task_status == 'error' || $task_auth != 'allow' ) {
				++$skip_count;

				continue;
			}

			// the task query ID can be a simple (integer) ID or more complex ID that gets converted to a simple ID, for instance to point to an object that is the result of a previously finished task.
			if ( isset( $task_query['id'] ) ) {
				$task_query['id'] = $this->get_object_id( $task_query['id'], $task_list_result );
			}

			$task_error = null;

			switch ( $task_type ) {
				case 'gallery_add':
					if ( ! $this->task_is_authorized( 'nextgen_edit_gallery' ) ) {
						$task_status = 'error';
						$task_error  = [
							'level'   => 'fatal',
							'message' => __( 'Not authorized to create a gallery.', 'nggallery' ),
						];

						break;
					}

					$mapper     = GalleryMapper::get_instance();
					$gallery    = null;
					$gal_errors = '';

					if ( isset( $task_query['id'] ) ) {
						$gallery = $mapper->find( $task_query['id'], true );
					}

					if ( $gallery == null ) {
						$title   = isset( $task_object['title'] ) ? $task_object['title'] : '';
						$gallery = $mapper->create( [ 'title' => $title ] );

						if ( ! $gallery || ! $gallery->save() ) {
							if ( $gallery != null ) {
								$gal_errors = $gallery->validation();

								if ( is_array( $gal_errors ) ) {
									$gal_errors = ' [' . wp_json_encode( $gal_errors ) . ']';
								}
							}

							$gallery = null;
						}
					}

					if ( $gallery != null ) {
						$task_status              = 'done';
						$task_result['object_id'] = $gallery->id();
					} else {
						$task_status = 'error';
						$task_error  = [
							'level'   => 'fatal',
							/* translators: 1: gallery title, 2: error details */
							'message' => sprintf( __( 'Gallery creation failed for "%1$s"%2$s.', 'nggallery' ), $title, $gal_errors ),
						];
					}

					break;
				case 'gallery_remove':
				case 'gallery_edit':
					if ( isset( $task_query['id'] ) ) {
						$mapper  = GalleryMapper::get_instance();
						$gallery = $mapper->find( $task_query['id'], true );
						$error   = null;
						$warning = null;

						if ( ! $this->task_is_authorized( 'nextgen_edit_gallery', $gallery, 'author', 'nextgen_edit_gallery_unowned' ) ) {
							/* translators: %1$s: gallery ID */
							$error   = __( 'Not authorized to edit gallery (%1$s).', 'nggallery' );
							$gallery = null;
						}

						if ( $gallery != null ) {
							if ( $task_type == 'gallery_remove' ) {
								/**
								 * Gallery mapper instance.
								 *
								 * @var GalleryMapper $mapper.
								 */
								if ( ! $mapper->destroy( $gallery, true ) ) {
									/* translators: %1$s: gallery ID */
									$error = __( 'Failed to remove gallery (%1$s).', 'nggallery' );
								}
							} elseif ( $task_type == 'gallery_edit' ) {
								if ( isset( $task_object['name'] ) ) {
									$gallery->name = $task_object['name'];
								}

								if ( isset( $task_object['title'] ) ) {
									$gallery->title = $task_object['title'];
								}

								if ( isset( $task_object['description'] ) ) {
									$gallery->galdesc = $task_object['description'];
								}

								if ( isset( $task_object['preview_image'] ) ) {
									$gallery->previewpic = $task_object['preview_image'];
								}

								if ( isset( $task_object['property_list'] ) ) {
									$properties = $task_object['property_list'];

									// Only descriptive schema fields may be assigned: 'path' is the
									// directory every image path in the gallery is built from and
									// 'author' decides who may manage it.
									$assignable = MassAssignment::filter( $properties, 'gallery', $refused );

									foreach ( $assignable as $key => $value ) {
										$gallery->$key = $value;
									}

									if ( $refused ) {
										// Reported as a warning on a 'done' task, not through $error:
										// $error puts the task in the 'error' status with level 'fatal',
										// which the Lightroom client turns into a failed publish for the
										// whole collection, and it would also suppress the save-failure
										// report further down. A refused key is not a failed publish.
										// The image list is deliberately still processed - an
										// unknown property key is no reason to discard uploads.
										// Concatenated, not sprintf()'d, because $warning is itself a
										// format string that gets the id interpolated further down:
										// an inner sprintf() would undo describe_refused()'s percent
										// doubling and the outer call would then eat a literal "%"
										// coming from a request-supplied key.
										/* translators: %1$s: gallery ID. The refused property names are appended. */
										$warning = __( 'Gallery (%1$s) was saved, but these properties may not be set from Lightroom and were ignored: ', 'nggallery' )
											. MassAssignment::describe_refused( $refused );
									}
								}

								// Used to determine whether the task is complete. Reset once per task
								// in the loop preamble above, so every branch reads a defined value.
								if ( isset( $task_object['image_list'] ) && $wp_fs != null ) {
									$storage_path = isset( $task_object['storage_path'] ) ? $task_object['storage_path'] : null;
									$storage_path = trim( $storage_path, '/\\' );

									$storage      = StorageManager::get_instance();
									$image_mapper = ImageMapper::get_instance();
									$creds        = true;

									$images_folder = $path_prefix . $fs_sep . $storage_path . $fs_sep;
									$images_folder = str_replace( [ '\\', '/' ], $fs_sep, $images_folder );

									$images             = $task_object['image_list'];
									$result_images      = isset( $task_result['image_list'] ) ? $task_result['image_list'] : [];
									$images_todo        = array_values( $this->_array_filter_by_entry( $images, $result_images, 'localId' ) );
									$image_count        = count( $images );
									$result_image_count = count( $result_images );

									foreach ( $images_todo as $image_index => $image ) {
										$image_id       = isset( $image['id'] ) ? $image['id'] : null;
										$image_filename = isset( $image['filename'] ) ? $image['filename'] : null;
										$image_path     = isset( $image['path'] ) ? $image['path'] : null;
										$image_data_key = isset( $image['data_key'] ) ? $image['data_key'] : null;
										$image_action   = isset( $image['action'] ) ? $image['action'] : null;
										$image_status   = isset( $image['status'] ) ? $image['status'] : 'skip';

										if ( $image_filename == null ) {
											$image_filename = basename( $image_path );
										}

										$ngg_image = $image_mapper->find( $image_id, true );
										// ensure that we don't transpose the image from one gallery to another in case a remoteId is passed in for the image but the gallery associated to the collection cannot be found.
										if ( $ngg_image && $ngg_image->galleryid != $gallery->id() ) {
											$ngg_image = null;
											$image_id  = null;
										}

										$image_error = null;

										if ( $image_action == 'delete' ) {
											// image was deleted.
											if ( $ngg_image != null ) {
												$settings    = \Imagely\NGG\Settings\Settings::get_instance();
												$delete_fine = true;

												if ( $settings->get( 'deleteImg' ) ) {
													if ( ! $storage->delete_image( $ngg_image ) ) {
														/* translators: %1$s: image filename */
														$image_error = __( 'Could not delete image file(s) from disk (%1$s).', 'nggallery' );
													}
												} elseif ( ! $image_mapper->destroy( $ngg_image ) ) {
													/* translators: %1$s: image filename */
													$image_error = __( 'Could not remove image from gallery (%1$s).', 'nggallery' );
												}

												if ( $image_error == null ) {
													do_action( 'ngg_delete_picture', $ngg_image->{$ngg_image->id_field}, $ngg_image );

													$image_status = 'done';
												}
											} else {
												/* translators: %1$s: image filename */
												$image_error = __( 'Could not remove image because image was not found (%1$s).', 'nggallery' );
											}
										} else {
											// image was added or edited and needs updating.
											$image_data = null;

											if ( $image_data_key != null ) {
												if ( ! isset( $extra_data['__queuedImages'][ $image_data_key ] ) ) {
													if ( isset( $extra_data[ $image_data_key ] ) ) {
														$image_data_arr = $extra_data[ $image_data_key ];
														// is_string(), not a (string) cast: a multi-file "file_data_x[]" upload
														// makes tmp_name an array, and casting that emits an
														// "Array to string conversion" warning before failing anyway.
														$image_tmp_name = is_array( $image_data_arr ) && isset( $image_data_arr['tmp_name'] ) && is_string( $image_data_arr['tmp_name'] )
															? $image_data_arr['tmp_name']
															: '';

														// Only bytes PHP itself received as an upload in *this* request may be
														// read here. Every other value in $extra_data is caller-supplied JSON,
														// and taking a path out of it turned the execute endpoint into an
														// arbitrary file read.
														//
														// is_uploaded_file() is the whole check: it is PHP's own authoritative
														// answer, so a path allowlist ANDed after it can only add false
														// negatives - which is #460, a publish failing on every image. Only
														// #460 items 1-4 are handled at HEAD (allowlist dropped, UPLOAD_ERR
														// surfaced, relative tmp_name resolved, strict result compare). Item 5
														// says to KEEP the prefix allowlist on the *other* call site - do not
														// re-add it here, which is what produced the reported failure.
														$upload_error = is_array( $image_data_arr ) && isset( $image_data_arr['error'] )
															? (int) $image_data_arr['error']
															: UPLOAD_ERR_OK;

														if ( UPLOAD_ERR_OK !== $upload_error ) {
															$image_error = str_replace(
																'%2$s',
																self::describe_upload_error( $upload_error ),
																/* translators: %1$s: image filename, %2$s: PHP upload error constant name */
																__( 'The web server rejected the upload of image (%1$s): %2$s.', 'nggallery' )
															);
														} elseif ( '' !== $image_tmp_name
															&& in_array( (string) $image_data_key, $upload_keys, true )
															&& is_uploaded_file( $image_tmp_name ) ) {
															$image_data = file_get_contents( $this->resolve_upload_tmp_path( $image_tmp_name ) );
														}
													}

													// Strict, and only when nothing more specific was recorded. The loose
													// `$image_data == null` that used to stand here ran unconditionally, so it
													// overwrote the upload-error message set just above with the generic
													// string - which made that message dead code in every configuration it
													// was written for. It also folded four distinct causes into one sentence:
													// path rejected, read failed (false), zero-byte read (''), and upload
													// failed. #460 item 4.
													if ( null === $image_error && ! is_string( $image_data ) ) {
														/* translators: %1$s: image filename */
														$image_error = __( 'Could not obtain data for image (%1$s).', 'nggallery' );
													} elseif ( null === $image_error && '' === $image_data ) {
														$image_data = null;

														/* translators: %1$s: image filename */
														$image_error = __( 'The image file was read but is empty (%1$s).', 'nggallery' );
													}
												} else {
													$image_status = 'queued';
												}
											} else {
												$image_path = $images_folder . $image_path;

												if ( $this->is_staged_image_path( $image_path, $images_folder, $wp_fs )
													&& $wp_fs->exists( $image_path ) ) {
													$image_data = $wp_fs->get_contents( $image_path );

													// The staged path is assembled from the stored job's app_config and
													// task_list, so containment alone cannot say the target is a photo
													// rather than debug.log or a plugin's source. Requiring real image
													// bytes is the bound the caller does not control, and a legitimate
													// staged file is always an image, so this costs nothing.
													//
													// It closes the non-image case only. An enqueuer can still point
													// $images_folder at, say, wp-content/uploads and have a media-library
													// JPEG there republished and then deleted - that is #1003's remaining
													// half, needs an authenticated capability-gated enqueue, and is not
													// closed here. See the note on is_staged_image_path(); #1003 must stay
													// open.
													// Bytes AND the source extension. import_image_file() checks only the
													// destination name, so a file with a valid image header and any other
													// extension - "debug.php" carrying a PNG header - was read out,
													// republished under the destination name, and then deleted, because the
													// delete is skipped only for non-image bytes. Credit to #1032 for this
													// one; reproduced before fixing.
													$staged_not_image = is_string( $image_data ) && '' !== $image_data
														&& ( ! $this->is_image_data( $image_data, (string) $image_filename )
															|| ! $storage->is_allowed_image_extension( $image_path ) );

													if ( $staged_not_image ) {
														$image_data = null;

														/* translators: %1$s: image filename */
														$image_error = __( 'The staged file is not an image, so it was left in place (%1$s).', 'nggallery' );
													}

													// get_contents() returns false, not null, on a failed read, so the
													// loose test that used to stand below set no error, skipped the
													// upload, and still counted the image finished - it vanished while
													// the task reported success.
													if ( null === $image_error && ! is_string( $image_data ) ) {
														/* translators: %1$s: image filename */
														$image_error = __( 'The image file could not be read from the staging folder (%1$s).', 'nggallery' );
													} elseif ( null === $image_error && '' === $image_data ) {
														$image_data = null;

														/* translators: %1$s: image filename */
														$image_error = __( 'The image file was read but is empty (%1$s).', 'nggallery' );
													}

													// Not deleted when the file turned out not to be an image: that is
													// someone else's file, and removing it would be the destructive half
													// of #1003.
													//
													// Otherwise deleted on both outcomes, which keeps the invariant the rmdir()
													// below documents. Deferring it to preserve a "retryable" copy was
													// wrong: _array_filter_by_entry() makes a re-run skip this image, so
													// the file was orphaned in a web-served directory that rmdir() could
													// then not remove. Reporting the failure above is the actual fix.
													// delete temporary image.
													if ( ! $staged_not_image && ! $wp_fs->delete( $image_path ) ) {
														// Checked for the same reason as the rmdir() below - a failed delete
														// leaves the full-resolution original in a web-served directory with
														// the task marked done - and logged here because the file remaining
														// is also what makes that rmdir() fail, so its log names the symptom
														// rather than the cause.
														$this->log_once(
															'staged_delete_failed_' . md5( (string) $image_path ),
															'NextGEN Gallery: Lightroom could not remove a staged original, so it is still on disk: ' . wp_strip_all_tags( (string) $image_path, true )
														);
													}
												} elseif ( ! $this->is_staged_image_path( $image_path, $images_folder, $wp_fs ) ) {
													// Distinguished from a missing file, and recorded: a host whose staging
													// folder falls outside the allow-list published fine before this release
													// and now fails every image, so "could not find" sends the photographer
													// looking for the wrong problem.
													//
													// The two refusals are also told apart. A plugin/theme/translation
													// directory IS inside the allow-list, so telling that photographer the
													// path is "outside the folders this site allows" is advice they cannot
													// act on - and for the site owner, someone steering a read-and-delete at
													// plugin files should not look like an open_basedir false positive.
													if ( $this->is_code_directory_path( $image_path ) ) {
														$this->log_once(
															'staged_path_code_dir_' . md5( (string) $image_path ),
															'NextGEN Gallery: Lightroom refused a staged path inside a plugin, theme or translation directory: ' . wp_strip_all_tags( (string) $image_path, true )
														);

														/* translators: %1$s: image filename */
														$image_error = __( 'NextGEN does not read images from plugin, theme or translation folders (%1$s).', 'nggallery' );
													} else {
														$this->log_once(
															'staged_path_refused_' . md5( (string) $image_path ),
															'NextGEN Gallery: Lightroom refused a staged path outside the permitted locations: ' . wp_strip_all_tags( (string) $image_path, true )
														);

														/* translators: %1$s: image filename */
														$image_error = __( 'The staged file is outside the folders this site allows NextGEN to read (%1$s).', 'nggallery' );
													}
												} elseif ( is_multisite() ) {
														/* translators: %1$s: image filename */
														$image_error = __( 'Could not find image file for image (%1$s). Using FTP Upload Method in Multisite is not recommended.', 'nggallery' );
												} else {
													/* translators: %1$s: image filename */
													$image_error = __( 'Could not find image file for image (%1$s).', 'nggallery' );
												}
											}

											if ( $image_data != null ) {
												try {
													$ngg_image = $storage->upload_base64_image( $gallery, $image_data, $image_filename, $image_id, true );
													$image_mapper->reimport_metadata( $ngg_image );

													if ( $ngg_image != null ) {
														$image_status = 'done';
														$image_id     = is_int( $ngg_image ) ? $ngg_image : $ngg_image->{$ngg_image->id_field};
													}
												} catch ( \E_NoSpaceAvailableException $e ) {
													/* translators: %1$s: image filename */
													$image_error = __( 'No space available for image (%1$s).', 'nggallery' );
												} catch ( \E_UploadException $e ) {
													$upload_error_message = str_replace( '%', '%%', $e->getMessage() );
													/* translators: %1$s: image filename */
													$image_error = $upload_error_message . __( ' (%1$s).', 'nggallery' );
												} catch ( \E_No_Image_Library_Exception $e ) {
													/* translators: %1$s: image filename */
													$image_error = __( 'No image library present, image uploads will fail (%1$s).', 'nggallery' );

													// no point in continuing if the image library is not present but we don't break here to ensure that all images are processed (otherwise they'd be processed in further fruitless handle_job calls).
												} catch ( \E_InsufficientWriteAccessException $e ) {
													/* translators: %1$s: image filename */
													$image_error = __( 'Inadequate system permissions to write image (%1$s).', 'nggallery' );
												} catch ( \E_InvalidEntityException $e ) {
													/* translators: 1: image filename, 2: image ID */
													$image_error = __( 'Requested image with id (%2$s) doesn\'t exist (%1$s).', 'nggallery' );
												} catch ( \E_EntityNotFoundException $e ) {
													// Gallery doesn't exist - already checked above so this should never happen.
													unset( $e );
												}
											}
										}                                       if ( $image_error != null ) {
											$image_status = 'error';

											$image['error'] = [
												'level'   => 'fatal',
												'message' => sprintf( $image_error, $image_filename, $image_id ),
											];
										}

										if ( $image_id ) {
											$image['id'] = $image_id;
										}

										if ( $image_status ) {
											$image['status'] = $image_status;
										}

										if ( $image_status != 'queued' ) {
											// append processed image to result image_list array.
											$result_images[] = $image;
										}

										if ( $this->should_stop_execution() ) {
											break;
										}
									}

									$task_result['image_list'] = $result_images;
									$image_list_unfinished     = count( $result_images ) < $image_count;

									// if images have finished processing, remove the folder used to store the temporary images (the folder should be empty due to delete() calls above).
									if ( ! $image_list_unfinished && $storage_path != null && $storage_path != $fs_sep && $path_prefix != null && $path_prefix != $fs_sep ) {
										if ( $this->is_permitted_fs_path( $images_folder, $wp_fs ) ) {
											// Not recursive, and returns false on a non-empty directory.
											// Discarding that made the only non-removal that happens in
											// practice the silent one.
											if ( ! $wp_fs->rmdir( $images_folder ) ) {
												// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
												error_log( 'NextGEN Gallery: Lightroom could not remove the staging directory, so it is still on disk: ' . wp_strip_all_tags( (string) $images_folder, true ) );
											}
										} else {
											// Without this the refusal is invisible: the task still reports
											// finished, and the staging directory just stays on disk, one per
											// publish, with no symptom an admin can trace back to here.
											// Logged ungated for the same reason as the quick-execute catch
											// above - a production install does not define WP_DEBUG.
											// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
											error_log( 'NextGEN Gallery: Lightroom left a staging directory in place because it is outside the permitted locations: ' . wp_strip_all_tags( (string) $images_folder, true ) );
										}
									}
								} elseif ( $wp_fs == null ) {
									/* translators: %1$s: gallery ID */
									$error = __( 'Could not access file system for gallery (%1$s).', 'nggallery' );
								}

								// save() is now truthful about a no-op update - TableDriver::save_entity()
								// tests `false !== $this->_update()` rather than truthiness - so a
								// falsy return here means the write really was refused. The previous
								// guard inferred that from $wpdb->last_error, which missed the
								// wpdb::update() paths that return false before issuing any query
								// and so reported a rejected save as success.
								if ( ! $gallery->save() ) {
									if ( $error == null ) {
										$gal_errors = '[' . wp_json_encode( $gallery->validation() ) . ']';
										/* translators: %1$s: gallery ID */
										$error = __( 'Failed to save modified gallery (%1$s). ', 'nggallery' ) . $gal_errors;
									}
								}
							}
						} elseif ( $error == null ) {
							/* translators: %1$s: gallery ID */
							$error = __( 'Could not find gallery (%1$s).', 'nggallery' );
						}
						if ( isset( $task_result['image_list'] ) && $gallery != null ) {
							$task_result['object_id'] = $gallery->id();
						}

						if ( $error == null ) {
							$task_status              = 'done';
							$task_result['object_id'] = $gallery->id();

							if ( $warning != null ) {
								// Non-fatal: the task stays 'done' so the client does not fail the
								// publish, but the refused property names still reach the user.
								$task_error = [
									'level'   => 'warning',
									'message' => sprintf( $warning, (string) $task_query['id'] ),
								];
							}
						} else {
							$task_status = 'error';
							$task_error  = [
								'level'   => 'fatal',
								'message' => sprintf( $error, (string) $task_query['id'] ),
							];
						}

						if ( $image_list_unfinished ) {
							// we override the status of the task when the image list has not finished processing.
							$task_status = 'unfinished';
						}
					} else {
						$task_status = 'error';
						$task_error  = [
							'level'   => 'fatal',
							'message' => __( 'No gallery was specified to edit.', 'nggallery' ),
						];
					}

					break;
				case 'album_add':
					if ( ! $this->task_is_authorized( 'nextgen_edit_album' ) ) {
						$task_status = 'error';
						$task_error  = [
							'level'   => 'fatal',
							'message' => __( 'Not authorized to create an album.', 'nggallery' ),
						];

						break;
					}

					$mapper = AlbumMapper::get_instance();

					$name       = isset( $task_object['name'] ) ? $task_object['name'] : '';
					$desc       = isset( $task_object['description'] ) ? $task_object['description'] : '';
					$previewpic = isset( $task_object['preview_image'] ) ? $task_object['preview_image'] : 0;
					$sortorder  = isset( $task_object['sort_order'] ) ? $task_object['sort_order'] : '';
					$page_id    = isset( $task_object['page_id'] ) ? $task_object['page_id'] : 0;

					$album = null;

					if ( isset( $task_query['id'] ) ) {
						$album = $mapper->find( $task_query['id'], true );
					}

					if ( $album == null ) {
						$album = $mapper->create(
							[
								'name'       => $name,
								'previewpic' => $previewpic,
								'albumdesc'  => $desc,
								'sortorder'  => $sortorder,
								'pageid'     => $page_id,
							]
						);

						if ( ! $album || ! $album->save() ) {
							$album = null;
						}
					}

					if ( $album != null ) {
						$task_status              = 'done';
						$task_result['object_id'] = $album->id();
					} else {
						$task_status = 'error';
						$task_error  = [
							'level'   => 'fatal',
							'message' => __( 'Album creation failed.', 'nggallery' ),
						];
					}

					break;
				case 'album_remove':
				case 'album_edit':
					if ( isset( $task_query['id'] ) ) {
						$mapper  = AlbumMapper::get_instance();
						$album   = $mapper->find( $task_query['id'], true );
						$error   = null;
						$warning = null;

						// The base capability must still hold at execution time, for the same
						// reason it does for galleries (a capability revoked between enqueue and
						// execute). Albums have no owner column, so ownership is derived from the
						// album's extras post (post_author) via AlbumREST - re-checked here as
						// well as at the enqueue gate, matching the gallery twin's execute-time
						// ownership re-check. #1096.
						if ( ! $this->task_is_authorized( 'nextgen_edit_album' )
							|| ! AlbumREST::current_user_can_manage_album( $task_query['id'] ) ) {
							/* translators: %1$s: album ID */
							$error = __( 'Not authorized to edit album (%1$s).', 'nggallery' );
							$album = null;
						}

						if ( $album ) {
							if ( $task_type == 'album_remove' ) {
								if ( ! $mapper->destroy( $album ) ) {
									/* translators: %1$s: album ID */
									$error = __( 'Failed to remove album (%1$s).', 'nggallery' );
								}
							} elseif ( $task_type == 'album_edit' ) {
								if ( isset( $task_object['name'] ) ) {
									$album->name = $task_object['name'];
								}

								if ( isset( $task_object['description'] ) ) {
									$album->albumdesc = $task_object['description'];
								}

								if ( isset( $task_object['preview_image'] ) ) {
									$album->previewpic = $task_object['preview_image'];
								}

								if ( isset( $task_object['property_list'] ) ) {
									$properties = $task_object['property_list'];

									// Only descriptive schema fields may be assigned - see the
									// gallery_edit case above.
									$assignable = MassAssignment::filter( $properties, 'album', $refused );

									foreach ( $assignable as $key => $value ) {
										$album->$key = $value;
									}

									if ( $refused ) {
										// See the gallery_edit case above.
										// Concatenated for the same reason as the gallery case above.
										/* translators: %1$s: album ID. The refused property names are appended. */
										$warning = __( 'Album (%1$s) was saved, but these properties may not be set from Lightroom and were ignored: ', 'nggallery' )
											. MassAssignment::describe_refused( $refused );
									}
								}

								if ( isset( $task_object['item_list'] ) ) {
									$item_list   = $task_object['item_list'];
									$sortorder   = $album->sortorder;
									$count       = count( $sortorder );
									$album_items = [];

									for ( $index = 0; $index < $count; $index++ ) {
										$album_items[ $sortorder[ $index ] ] = $index;
									}

									foreach ( $item_list as $item_info ) {
										$item_id    = isset( $item_info['id'] ) ? $item_info['id'] : null;
										$item_type  = isset( $item_info['type'] ) ? $item_info['type'] : null;
										$item_index = isset( $item_info['index'] ) ? $item_info['index'] : null;
										// translate ID in case this gallery has been created as part of this job.
										$item_id = $this->get_object_id( $item_id, $task_list_result );

										if ( $item_id != null ) {
											if ( $item_type == 'album' ) {
												$item_id = 'a' . $item_id;
											}

											$album_items[ $item_id ] = $count + $item_index;
										}
									}

									asort( $album_items );

									$album->sortorder = array_keys( $album_items );
								}

								// Same as the gallery twin above: the no-op-update problem is fixed in
								// TableDriver::save_entity(), so a falsy save() here is a real refusal
								// and needs no $wpdb->last_error inference. Fixing it in the mapper is
								// what closes both branches at once - the previous approach had to be
								// applied per call site, which is how the album half stayed broken
								// after the gallery half was fixed in April.
								if ( ! $mapper->save( $album ) ) {
									if ( $error == null ) {
										$alb_errors = '[' . wp_json_encode( $album->validation() ) . ']';
										/* translators: %1$s: album ID */
										$error = __( 'Failed to save modified album (%1$s). ', 'nggallery' ) . $alb_errors;
									}
								}
							}
						} elseif ( $error == null ) {
							/* translators: %1$s: album ID */
							$error = __( 'Could not find album (%1$s).', 'nggallery' );
						}

						if ( $error == null ) {
							$task_status              = 'done';
							$task_result['object_id'] = $album->id();

							if ( $warning != null ) {
								// See the gallery_edit case above.
								$task_error = [
									'level'   => 'warning',
									'message' => sprintf( $warning, (string) $task_query['id'] ),
								];
							}
						} else {
							$task_status = 'error';
							$task_error  = [
								'level'   => 'fatal',
								'message' => sprintf( $error, (string) $task_query['id'] ),
							];
						}
					} else {
						$task_status = 'error';
						$task_error  = [
							'level'   => 'fatal',
							'message' => __( 'No album was specified to edit.', 'nggallery' ),
						];
					}

					break;
				case 'gallery_list_get':
					$mapper       = GalleryMapper::get_instance();
					$gallery_list = $mapper->find_all();
					$result_list  = [];

					foreach ( $gallery_list as $gallery ) {
						$gallery_result = [
							'id'            => $gallery->id(),
							'name'          => $gallery->name,
							'title'         => $gallery->title,
							'description'   => $gallery->galdesc,
							'preview_image' => $gallery->previewpic,
						];

						$result_list[] = $gallery_result;
					}

					$task_status                 = 'done';
					$task_result['gallery_list'] = $result_list;

					break;
				case 'image_list_move':
					break;
			}

			$task_item['result'] = $task_result;
			$task_item['status'] = $task_status;
			$task_item['error']  = $task_error;

			// for previously finished tasks, store the result as it may be needed by future tasks.
			if ( $task_id != null && $task_result != null ) {
				$task_list_result[ $task_id ] = $task_result;
			}

			// if the task has finished, either successfully or unsuccessfully, increase count for done tasks.
			if ( 'unfinished' != $task_status ) {
				++$done_count;
			}

			if ( $this->should_stop_execution() ) {
				break;
			}
		}

		if ( ! $this->set_job_task_list( $job_id, $task_list ) ) {
			// Hand the callers this list rather than letting them re-read a stale one -
			// get_job_task_list() prefers it - so the statuses reach the client at least
			// once. Not returned as unfinished: a retry would re-read the stale list with
			// every image 'pending' and re-upload the delivered ones as duplicates.
			$this->unpersisted_task_lists[ $job_id ] = $task_list;

			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'NextGEN Gallery: Lightroom could not persist task results for job ' . wp_strip_all_tags( (string) $job_id, true ) . '; serving the in-memory list to the caller.' );
		} else {
			// Cleared on success, so a copy kept by an earlier failure cannot shadow what was
			// just written - get_job_task_list() prefers the in-memory entry.
			unset( $this->unpersisted_task_lists[ $job_id ] );
		}

		if ( $task_count > $done_count + $skip_count ) {
			// unfinished tasks, return false.
			return false;
		} else {
			$upload_method = isset( $app_config['upload_method'] ) ? $app_config['upload_method'] : 'ftp';

			if ( 'ftp' == $upload_method ) {
				// everything was finished, write status file.
				$status_file    = '_ngg_job_status_' . strval( $job_id ) . '.txt';
				$status_content = wp_json_encode( $task_list );

				// Contained like every other filesystem operation here: $path_prefix and
				// $full_path are request JSON, so "full_path": "/../.." wrote attacker-shaped
				// JSON outside wp-content, and the fallback branch used no root prefix at all.
				$status_written = false;

				if ( null != $wp_fs ) {
					$status_path = $path_prefix . $fs_sep . $status_file;
					$status_path = str_replace( [ '\\', '/' ], $fs_sep, $status_path );

					if ( $this->is_permitted_fs_path( $status_path, $wp_fs ) ) {
						$status_written = (bool) $wp_fs->put_contents( $status_path, $status_content );
					} else {
						// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
						error_log( 'NextGEN Gallery: Lightroom did not write a job status file outside the permitted locations: ' . wp_strip_all_tags( (string) $status_path, true ) );
					}
				} else {
					// Currently unreachable: a failed WP_Filesystem() above assigns
					// WP_Filesystem_Direct, so $wp_fs is never null here. Kept, and guarded on
					// the assumption that if it ever runs the write is local.
					$status_path = str_replace( $ftp_path, $root_path, $full_path ) . DIRECTORY_SEPARATOR . $status_file;
					$status_path = str_replace( [ '\\', '/' ], DIRECTORY_SEPARATOR, $status_path );

					if ( $this->is_valid_filename( $status_path ) ) {
						// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
						$status_written = false !== file_put_contents( $status_path, $status_content );
					} else {
						// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
						error_log( 'NextGEN Gallery: Lightroom did not write a job status file outside the permitted locations: ' . wp_strip_all_tags( (string) $status_path, true ) );
					}
				}

				if ( ! $status_written ) {
					// The status file is how the client learns an FTP publish finished, and a
					// bare true here removes the job - leaving the client polling for a file
					// that will never appear. Recorded on the task instead, which both legs
					// read back before remove_job().
					$task_list = $this->record_job_warning(
						$task_list,
						__( 'The publish finished, but its status file could not be written to the upload folder. Lightroom may keep waiting for it.', 'nggallery' )
					);

					// Refreshed either way: the caller reads this list back, and an entry left by
					// the earlier persist failure would otherwise shadow the warning even when
					// this write succeeded.
					$this->unpersisted_task_lists[ $job_id ] = $task_list;

					if ( ! $this->set_job_task_list( $job_id, $task_list ) ) {
						// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
						error_log( 'NextGEN Gallery: Lightroom could not record that the job status file was not written, for job ' . wp_strip_all_tags( (string) $job_id, true ) . '.' );
					}
				}
			}

			return true;
		}
	}
}
