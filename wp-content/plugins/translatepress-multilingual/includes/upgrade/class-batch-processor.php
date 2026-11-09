<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

class TRP_Batch_Processor {

    const TIME_LIMIT             = 15.0; // seconds
    const RESCHEDULE_DELAY       = 30;   // seconds between cron fallback runs
    const CRON_HOOK              = 'trp_batch_process';
    const HEALTHCHECK_HOOK       = 'trp_batch_process_healthcheck';
    const ASYNC_ACTION           = 'trp_run_batch_processor';
    const STATUS_ACTION          = 'trp_batch_status';
    const LOCK_OPTION            = 'trp_batch_process_lock';
    const ASYNC_TOKEN_OPTION     = 'trp_batch_process_async_token';
    const STATE_OPTION_PREFIX    = 'trp_batch_state_';
    const LOCK_TTL               = HOUR_IN_SECONDS;
    const DISPATCH_STALE_SECONDS = 60;

    protected $lock = array();
    protected $task_load_error = '';

    /**
     * Register cron handler and admin notices.
     */
    public function __construct() {
        if ( ! apply_filters( 'trp_enable_batch_upgrades', true ) ) {
            return;
        }
        add_filter( 'cron_schedules', array( $this, 'add_cron_schedules' ) );
        add_filter( 'cron_request', array( $this, 'add_loopback_auth_to_cron_request' ) );
        add_action( self::CRON_HOOK, array( $this, 'run_cron' ) );
        add_action( self::HEALTHCHECK_HOOK, array( $this, 'run_healthcheck' ) );
        add_action( 'wp_ajax_' . self::ASYNC_ACTION, array( $this, 'run_async_request' ) );
        add_action( 'wp_ajax_nopriv_' . self::ASYNC_ACTION, array( $this, 'run_async_request' ) );
        add_action( 'wp_ajax_' . self::STATUS_ACTION, array( $this, 'ajax_get_batch_status' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_status_polling_script' ) );
        add_action( 'admin_init', array( $this, 'maybe_handle_batch_task_action' ) );
        add_action( 'admin_init', array( $this, 'maybe_dispatch_pending_tasks' ) );
        add_action( 'admin_init', array( $this, 'maybe_show_completion_notices' ) );
    }

    /**
     * Register the recurring healthcheck interval.
     *
     * @param array $schedules Existing cron schedules.
     * @return array
     */
    public function add_cron_schedules( $schedules ) {
        if ( empty( $schedules['trp_every_minute'] ) ) {
            $schedules['trp_every_minute'] = array(
                'interval' => MINUTE_IN_SECONDS,
                'display'  => __( 'Every minute', 'translatepress-multilingual' ),
            );
        }

        return $schedules;
    }

    /**
     * Schedule a single cron event if one is not already scheduled.
     */
    public function schedule( $delay = 0 ) {
        if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
            wp_schedule_single_event( time() + absint( $delay ), self::CRON_HOOK );
        }
    }

    /**
     * Schedule the recurring healthcheck used as a fallback for stalled async processing.
     */
    public function schedule_healthcheck() {
        if ( ! wp_next_scheduled( self::HEALTHCHECK_HOOK ) ) {
            wp_schedule_event( time() + MINUTE_IN_SECONDS, 'trp_every_minute', self::HEALTHCHECK_HOOK );
        }
    }

    /**
     * Unschedule the recurring healthcheck when no task is pending.
     */
    public function unschedule_healthcheck() {
        $timestamp = wp_next_scheduled( self::HEALTHCHECK_HOOK );

        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, self::HEALTHCHECK_HOOK );
        }
    }

    /**
     * Start a registered batch task.
     *
     * @param string $flag_name Task flag option name.
     * @param bool   $reset     Whether to reset previous todo/progress state.
     *
     * @return bool
     */
    public function start_task( $flag_name, $reset = false ) {
        $registry = TRP_Upgrade_Tasks_Registry::get_tasks();

        if ( ! isset( $registry[ $flag_name ] ) ) {
            return false;
        }

        $task = $this->instantiate_task( $registry[ $flag_name ] );
        if ( ! $task ) {
            $error = $this->task_load_error !== '' ? $this->task_load_error : __( 'Could not load the background task.', 'translatepress-multilingual' );
            $this->fail_task_before_start( $flag_name, $error );
            return false;
        }

        if ( method_exists( $task, 'validate_before_start' ) ) {
            try {
                $error = $task->validate_before_start();
            } catch ( Exception $exception ) {
                $error = $exception->getMessage();
            }

            if ( is_string( $error ) && $error !== '' ) {
                $this->fail_task_before_start( $flag_name, $error );
                return false;
            }
        }

        if ( $reset ) {
            delete_option( 'trp_batch_todo_' . $flag_name );
            delete_option( 'trp_batch_found_rows_' . $flag_name );
            delete_option( 'trp_batch_error_' . $flag_name );
            delete_option( 'trp_batch_progress_' . $flag_name );
            delete_option( $this->get_state_option_name( $flag_name ) );
        } else {
            $todo_option = 'trp_batch_todo_' . $flag_name;
            $todo_list   = get_option( $todo_option, false );

            if ( is_array( $todo_list ) ) {
                foreach ( $todo_list as &$item ) {
                    if ( isset( $item['status'] ) && $item['status'] === 'failed' ) {
                        $item['status'] = 'not_completed';
                        $item['error']  = null;
                    }
                }
                unset( $item );
                update_option( $todo_option, $todo_list, false );
            }

            delete_option( 'trp_batch_error_' . $flag_name );
        }

        update_option( $flag_name, 'no', false );
        $this->update_task_state(
            $flag_name,
            array(
                'status'     => 'waiting',
                'message'    => __( 'Waiting to start background processing...', 'translatepress-multilingual' ),
                'error'      => null,
                'started_at' => time(),
            )
        );
        $this->schedule();
        $this->schedule_healthcheck();
        $this->dispatch_async_runner( true );

        return true;
    }

    /**
     * Record a task preflight failure without scheduling a runner.
     *
     * @param string $flag_name Task flag option name.
     * @param string $error Error returned by the task preflight.
     */
    protected function fail_task_before_start( $flag_name, $error ) {
        $result = array(
            'status' => 'failed',
            'error'  => $error,
        );

        update_option( $flag_name, 'failed', false );
        update_option( 'trp_batch_error_' . $flag_name, $result, false );
        $this->update_task_state(
            $flag_name,
            array(
                'status'  => 'failed',
                'message' => __( 'Background processing could not start.', 'translatepress-multilingual' ),
                'error'   => $error,
            )
        );
    }

    /**
     * Cron callback. Process one pending task, reschedule if work remains.
     */
    public function run_cron() {
        $this->run_queue( 'cron' );
    }

    /**
     * AJAX callback used by the async queue runner.
     */
    public function run_async_request() {
        if ( ! $this->validate_async_request() ) {
            wp_die( 'Invalid request', '', array( 'response' => 403 ) );
        }

        if ( function_exists( 'ignore_user_abort' ) ) {
            ignore_user_abort( true );
        }

        $this->run_queue( 'async' );
        wp_die();
    }

    /**
     * Recurring fallback that restarts pending work if async chaining stalls.
     */
    public function run_healthcheck() {
        $registry = TRP_Upgrade_Tasks_Registry::get_tasks();

        if ( ! $this->has_pending_tasks( $registry ) ) {
            $this->unschedule_healthcheck();
            return;
        }

        if ( $this->has_fresh_lock() ) {
            return;
        }

        $this->clear_stale_lock();

        if ( $this->pending_dispatch_is_stale( $registry ) ) {
            $this->dispatch_async_runner();
        }
    }

    /**
     * Process one queue slice.
     *
     * @param string $context Execution context.
     */
    protected function run_queue( $context = 'cron' ) {
        $this->clear_stale_lock();

        if ( ! $this->claim_lock() ) {
            return;
        }

        $registry      = TRP_Upgrade_Tasks_Registry::get_tasks();
        $pending_after = false;

        try {
            foreach ( $registry as $flag_name => $task_config ) {
                $flag_value = get_option( $flag_name, 'is not set' );

                if ( $flag_value !== 'no' ) {
                    continue;
                }

                $this->process_task( $flag_name, $task_config, $context );

                // Only process one task per cron run.
                break;
            }

            $pending_after = $this->has_pending_tasks( $registry );
        } finally {
            $this->release_lock();
        }

        if ( $pending_after ) {
            $this->schedule( self::RESCHEDULE_DELAY );
            $this->schedule_healthcheck();
            $this->dispatch_async_runner( true );
        } else {
            $this->unschedule_healthcheck();
        }
    }

    /**
     * Process a single task: build todo list if needed, then execute items within time budget.
     *
     * @param string $flag_name   The option name acting as the task flag.
     * @param array  $task_config Task configuration from registry.
     */
    protected function process_task( $flag_name, $task_config, $context = 'cron' ) {
        $task = $this->instantiate_task( $task_config );

        if ( ! $task ) {
            $error = $this->task_load_error !== '' ? $this->task_load_error : __( 'Could not load the background task.', 'translatepress-multilingual' );

            update_option( $flag_name, 'failed', false );
            update_option(
                'trp_batch_error_' . $flag_name,
                array(
                    'status' => 'failed',
                    'error'  => $error,
                ),
                false
            );
            $this->update_task_state(
                $flag_name,
                array(
                    'status'  => 'failed',
                    'message' => __( 'Background processing could not start.', 'translatepress-multilingual' ),
                    'error'   => $error,
                )
            );
            return;
        }

        $this->update_task_state(
            $flag_name,
            array(
                'status'  => 'running',
                'context' => $context,
            )
        );

        $todo_option = 'trp_batch_todo_' . $flag_name;
        $todo_list   = $this->read_option_from_database( $todo_option, false );

        // Build todo list on first run.
        if ( $todo_list === false ) {
            $todo_list = $task->build_todo_list();
            if ( empty( $todo_list ) ) {
                update_option( $flag_name, 'yes' );
                $this->update_task_state(
                    $flag_name,
                    array(
                        'status'  => 'complete',
                        'message' => __( 'Background processing completed.', 'translatepress-multilingual' ),
                    )
                );
                return;
            }
            update_option( $todo_option, $todo_list, false );
        }

        $start_time = microtime( true );
        $changed    = false;

        foreach ( $todo_list as $index => &$item ) {
            if ( $item['status'] === 'completed' ) {
                continue;
            }

            if ( $item['status'] === 'failed' ) {
                update_option( $flag_name, 'failed', false );
                $this->update_task_state(
                    $flag_name,
                    array(
                        'status' => 'failed',
                        'error'  => ! empty( $item['error'] ) ? $item['error'] : __( 'A previous batch item failed.', 'translatepress-multilingual' ),
                    )
                );
                return;
            }

            // Execute batches on this item until it's done or time runs out.
            while ( true ) {
                if ( ! $this->refresh_lock() ) {
                    return;
                }

                $result = $task->execute( $item );

                // A long database operation may have outlived the lease. Do not
                // persist stale todo state if another runner replaced this lock.
                if ( ! $this->refresh_lock() ) {
                    return;
                }

                if ( isset( $result['error'] ) && $result['error'] !== '' ) {
                    $item['error'] = $result['error'];
                    $item['status'] = 'failed';
                    $changed        = true;
                    update_option( 'trp_batch_error_' . $flag_name, $result, false );
                    $progress = $this->get_item_progress( $item, $result );
                    update_option( 'trp_batch_progress_' . $flag_name, $progress, false );
                    update_option( $todo_option, $todo_list, false );
                    update_option( $flag_name, 'failed', false );
                    $this->update_task_state(
                        $flag_name,
                        array(
                            'status'  => 'failed',
                            'message' => ! empty( $progress['message'] ) ? $progress['message'] : '',
                            'error'   => $result['error'],
                        )
                    );
                    return;
                }

                if ( isset( $result['item'] ) && is_array( $result['item'] ) ) {
                    $item = array_merge( $item, $result['item'] );
                }

                $progress = $this->get_item_progress( $item, $result );
                update_option( 'trp_batch_progress_' . $flag_name, $progress, false );
                update_option( $todo_option, $todo_list, false );
                $this->update_task_state(
                    $flag_name,
                    array(
                        'status'  => 'running',
                        'message' => ! empty( $progress['message'] ) ? $progress['message'] : '',
                        'context' => $context,
                    )
                );

                if ( $result['status'] === 'completed' ) {
                    $item['status'] = 'completed';
                    $changed = true;
                    break;
                }

                // Rows were deleted — track that this task found something.
                if ( ! empty( $result['affected_rows'] ) && get_option( 'trp_batch_found_rows_' . $flag_name ) !== 'yes' ) {
                    update_option( 'trp_batch_found_rows_' . $flag_name, 'yes', false );
                }

                $changed = true;

                if ( ( microtime( true ) - $start_time ) >= self::TIME_LIMIT ) {
                    break 2;
                }
            }

            if ( ( microtime( true ) - $start_time ) >= self::TIME_LIMIT ) {
                break;
            }
        }
        unset( $item );

        if ( $changed ) {
            update_option( $todo_option, $todo_list, false );
        }

        // Check if all items are completed.
        $all_completed = true;
        foreach ( $todo_list as $item ) {
            if ( $item['status'] !== 'completed' ) {
                $all_completed = false;
                break;
            }
        }

        if ( $all_completed ) {
            update_option( $flag_name, 'yes' );
            // Keep the todo list in the DB for investigation if errors occurred.
            update_option( $todo_option, $todo_list, false );
            $this->update_task_state(
                $flag_name,
                array(
                    'status'  => 'complete',
                    'message' => __( 'Background processing completed.', 'translatepress-multilingual' ),
                )
            );
        } else {
            $this->update_task_state(
                $flag_name,
                array(
                    'status' => 'waiting',
                )
            );
        }
    }

    /**
     * Show admin notices for running, failed, and completed batch tasks.
     */
    public function maybe_show_completion_notices() {
        $notifications = TRP_Plugin_Notifications::get_instance();
        $is_plugin_page = $notifications->is_plugin_page();

        $registry = TRP_Upgrade_Tasks_Registry::get_tasks();

        foreach ( $registry as $flag_name => $task_config ) {
            $flag_value = get_option( $flag_name, 'is not set' );

            if ( $flag_value === 'no' && ! empty( $task_config['running_notice'] ) ) {
                $progress = get_option( 'trp_batch_progress_' . $flag_name, array() );
                $notice   = $task_config['running_notice'];

                if ( ! empty( $progress['message'] ) ) {
                    $notice .= ' ' . $progress['message'];
                }

                $notice = '<span class="trp-batch-live-notice trp-batch-task-' . sanitize_html_class( $flag_name ) . '" style="display:inline-flex;align-items:center;gap:8px;"><span class="spinner is-active" style="float:none;margin:0;visibility:visible;"></span><span class="trp-batch-message">' . $notice . '</span></span>';

                $this->add_notice( 'trp_batch_running_' . $flag_name, $notice, 'trp-notice notice notice-info', false );
                continue;
            }

            if ( $flag_value === 'failed' ) {
                $error  = get_option( 'trp_batch_error_' . $flag_name, array() );
                $notice = ! empty( $task_config['failure_notice'] ) ? $task_config['failure_notice'] : __( 'TranslatePress background processing did not complete successfully.', 'translatepress-multilingual' );

                if ( ! empty( $error['error'] ) ) {
                    $notice .= ' ' . sprintf( __( 'Error: %s', 'translatepress-multilingual' ), $error['error'] );
                }

                $retry_url = wp_nonce_url(
                    add_query_arg(
                        array(
                            'trp_batch_action' => 'retry',
                            'trp_batch_task'   => $flag_name,
                        )
                    ),
                    'trp_batch_task_action_' . $flag_name
                );
                $notice .= ' <a href="' . esc_url( $retry_url ) . '">' . esc_html__( 'Retry', 'translatepress-multilingual' ) . '</a>';

                $this->add_notice( 'trp_batch_failed_' . $flag_name, $notice, 'trp-notice notice notice-error', false );
                continue;
            }

            if ( empty( $task_config['completion_notice'] ) ) {
                continue;
            }

            if ( ! $is_plugin_page && empty( $task_config['always_show_completion_notice'] ) ) {
                continue;
            }

            // Only show if task is done and rows were found, unless this task always needs completion feedback.
            if ( $flag_value !== 'yes' || ( empty( $task_config['always_show_completion_notice'] ) && get_option( 'trp_batch_found_rows_' . $flag_name ) !== 'yes' ) ) {
                continue;
            }

            $notice_id = 'trp_batch_notice_' . $flag_name;
            $state     = $this->get_task_state( $flag_name );
            if ( ! empty( $state['started_at'] ) ) {
                $notice_id .= '_' . absint( $state['started_at'] );
            }

            $this->add_notice( $notice_id, $task_config['completion_notice'], 'trp-notice notice notice-success is-dismissible', true );
        }
    }

    /**
     * Check if any registered task still has a pending flag.
     *
     * @param array $registry Task registry.
     * @return bool
     */
    protected function has_pending_tasks( $registry ) {
        foreach ( $registry as $flag_name => $task_config ) {
            if ( get_option( $flag_name, 'is not set' ) === 'no' ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return the first pending task flag, if any.
     *
     * @param array $registry Task registry.
     * @return string
     */
    protected function get_first_pending_task_flag( $registry ) {
        foreach ( $registry as $flag_name => $task_config ) {
            if ( get_option( $flag_name, 'is not set' ) === 'no' ) {
                return $flag_name;
            }
        }

        return '';
    }

    /**
     * Enqueue the admin script that refreshes running batch notices without a page reload.
     */
    public function enqueue_status_polling_script() {
        $registry = TRP_Upgrade_Tasks_Registry::get_tasks();
        $tasks    = array();

        foreach ( $registry as $flag_name => $task_config ) {
            if ( get_option( $flag_name, 'is not set' ) !== 'no' ) {
                continue;
            }

            $tasks[] = array(
                'flag'     => $flag_name,
                'selector' => '.trp-batch-task-' . sanitize_html_class( $flag_name ),
            );
        }

        if ( empty( $tasks ) ) {
            return;
        }

        wp_enqueue_script(
            'trp-batch-status',
            TRP_PLUGIN_URL . 'assets/js/trp-batch-status.js',
            array( 'jquery' ),
            TRP_PLUGIN_VERSION,
            true
        );

        wp_localize_script(
            'trp-batch-status',
            'trpBatchStatus',
            array(
                'ajax_url'      => admin_url( 'admin-ajax.php' ),
                'nonce'         => wp_create_nonce( self::STATUS_ACTION ),
                'action'        => self::STATUS_ACTION,
                'poll_interval' => (int) apply_filters( 'trp_batch_status_poll_interval', 5000 ),
                'tasks'         => $tasks,
                'retry_text'    => __( 'Retry', 'translatepress-multilingual' ),
                'dismiss_text'  => __( 'Dismiss this notice.', 'translatepress-multilingual' ),
            )
        );
    }

    /**
     * Return the current status for one batch task.
     */
    public function ajax_get_batch_status() {
        if ( ! current_user_can( apply_filters( 'trp_update_database_capability', 'manage_options' ) ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to view this task status.', 'translatepress-multilingual' ) ), 403 );
        }

        check_ajax_referer( self::STATUS_ACTION, 'nonce' );

        $flag_name = isset( $_POST['task'] ) ? sanitize_text_field( wp_unslash( $_POST['task'] ) ) : '';
        $registry  = TRP_Upgrade_Tasks_Registry::get_tasks();

        if ( empty( $flag_name ) || ! isset( $registry[ $flag_name ] ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid batch task.', 'translatepress-multilingual' ) ), 400 );
        }

        $this->maybe_restart_stalled_task_from_status_poll( $flag_name, $registry );

        $task_config = $registry[ $flag_name ];
        $flag_value  = get_option( $flag_name, 'is not set' );
        $progress    = get_option( 'trp_batch_progress_' . $flag_name, array() );
        $state       = $this->get_task_state( $flag_name );
        $error       = get_option( 'trp_batch_error_' . $flag_name, array() );

        $message = '';
        if ( $flag_value === 'no' ) {
            $message = ! empty( $task_config['running_notice'] ) ? $task_config['running_notice'] : __( 'TranslatePress is processing a background task.', 'translatepress-multilingual' );

            if ( ! empty( $progress['message'] ) ) {
                $message .= ' ' . $progress['message'];
            } elseif ( ! empty( $state['message'] ) ) {
                $message .= ' ' . $state['message'];
            }
        } elseif ( $flag_value === 'yes' ) {
            $message = ! empty( $task_config['completion_notice'] ) ? $task_config['completion_notice'] : __( 'TranslatePress background processing completed successfully.', 'translatepress-multilingual' );
        } elseif ( $flag_value === 'failed' ) {
            $message = ! empty( $task_config['failure_notice'] ) ? $task_config['failure_notice'] : __( 'TranslatePress background processing did not complete successfully.', 'translatepress-multilingual' );

            if ( ! empty( $error['error'] ) ) {
                $message .= ' ' . sprintf( __( 'Error: %s', 'translatepress-multilingual' ), $error['error'] );
            } elseif ( ! empty( $state['error'] ) ) {
                $message .= ' ' . sprintf( __( 'Error: %s', 'translatepress-multilingual' ), $state['error'] );
            }
        }

        $retry_url = '';
        if ( $flag_value === 'failed' ) {
            $retry_url = wp_nonce_url(
                add_query_arg(
                    array(
                        'trp_batch_action' => 'retry',
                        'trp_batch_task'   => $flag_name,
                    )
                ),
                'trp_batch_task_action_' . $flag_name
            );
        }

        wp_send_json_success(
            array(
                'task'      => $flag_name,
                'status'    => $flag_value,
                'message'   => wp_kses_post( $message ),
                'retry_url' => $retry_url,
            )
        );
    }

    /**
     * Dispatch the async queue runner.
     *
     * @param bool $force Whether to bypass dispatch throttling.
     * @return bool
     */
    protected function dispatch_async_runner( $force = false ) {
        $registry  = TRP_Upgrade_Tasks_Registry::get_tasks();
        $flag_name = $this->get_first_pending_task_flag( $registry );

        if ( empty( $flag_name ) ) {
            return false;
        }

        $state = $this->get_task_state( $flag_name );
        if ( ! $force && ! empty( $state['last_dispatch_at'] ) && ( time() - (int) $state['last_dispatch_at'] ) < apply_filters( 'trp_batch_async_dispatch_throttle', 5 ) ) {
            return false;
        }

        $this->update_task_state(
            $flag_name,
            array(
                'status'           => 'waiting',
                'last_dispatch_at' => time(),
            )
        );

        $request_args = array(
            'timeout'   => 0.01,
            'blocking'  => false,
            'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
            'headers'   => $this->get_loopback_request_headers(),
            'body'      => array(
                'action' => self::ASYNC_ACTION,
                'token'  => $this->get_async_token(),
            ),
        );

        $response = wp_remote_post(
            admin_url( 'admin-ajax.php' ),
            apply_filters( 'trp_batch_async_request_args', $request_args, $flag_name )
        );

        return ! is_wp_error( $response );
    }

    /**
     * Forward HTTP authorization to same-site loopbacks.
     *
     * This is required on password-protected staging sites, where otherwise
     * both admin-ajax.php and wp-cron.php reject the background request.
     *
     * @return array
     */
    protected function get_loopback_request_headers() {
        $authorization = '';

        if ( ! empty( $_SERVER['HTTP_AUTHORIZATION'] ) ) {
            $authorization = wp_unslash( $_SERVER['HTTP_AUTHORIZATION'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Forwarded unchanged to a same-site loopback after stripping CR/LF.
        } elseif ( ! empty( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) ) {
            $authorization = wp_unslash( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Forwarded unchanged to a same-site loopback after stripping CR/LF.
        } elseif ( isset( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] ) ) {
            $authorization = 'Basic ' . base64_encode( wp_unslash( $_SERVER['PHP_AUTH_USER'] ) . ':' . wp_unslash( $_SERVER['PHP_AUTH_PW'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Credentials must remain byte-for-byte intact for same-site Basic Auth.
        }

        $authorization = str_replace( array( "\r", "\n" ), '', $authorization );
        $headers       = array();

        if ( $authorization !== '' ) {
            $headers['Authorization'] = $authorization;
        }

        return apply_filters( 'trp_batch_loopback_request_headers', $headers );
    }

    /**
     * Forward HTTP authorization to the WP-Cron fallback request.
     *
     * @param array $cron_request Cron request URL, key, and arguments.
     * @return array
     */
    public function add_loopback_auth_to_cron_request( $cron_request ) {
        $headers = $this->get_loopback_request_headers();

        if ( empty( $headers ) || empty( $cron_request['args'] ) || ! is_array( $cron_request['args'] ) ) {
            return $cron_request;
        }

        $existing_headers                = ! empty( $cron_request['args']['headers'] ) && is_array( $cron_request['args']['headers'] ) ? $cron_request['args']['headers'] : array();
        $cron_request['args']['headers'] = array_merge( $existing_headers, $headers );

        return $cron_request;
    }

    /**
     * Re-dispatch stale work while an authenticated admin is polling its status.
     *
     * @param string $flag_name Requested task flag.
     * @param array  $registry  Task registry.
     */
    protected function maybe_restart_stalled_task_from_status_poll( $flag_name, $registry ) {
        if ( $flag_name !== $this->get_first_pending_task_flag( $registry ) || $this->has_fresh_lock() ) {
            return;
        }

        $this->clear_stale_lock();

        if ( $this->pending_dispatch_is_stale( $registry ) ) {
            $this->schedule();
            $this->schedule_healthcheck();
            $this->dispatch_async_runner();
        }
    }

    /**
     * Kick pending background tasks from admin requests if the queue is idle.
     */
    public function maybe_dispatch_pending_tasks() {
        if ( wp_doing_ajax() ) {
            return;
        }

        $registry = TRP_Upgrade_Tasks_Registry::get_tasks();

        if ( ! $this->has_pending_tasks( $registry ) || $this->has_fresh_lock() ) {
            return;
        }

        $this->clear_stale_lock();

        if ( $this->pending_dispatch_is_stale( $registry ) ) {
            $this->schedule();
            $this->schedule_healthcheck();
            $this->dispatch_async_runner();
        }
    }

    /**
     * Check whether pending work needs another dispatch attempt.
     *
     * @param array $registry Task registry.
     * @return bool
     */
    protected function pending_dispatch_is_stale( $registry ) {
        $flag_name = $this->get_first_pending_task_flag( $registry );

        if ( empty( $flag_name ) ) {
            return false;
        }

        $state            = $this->get_task_state( $flag_name );
        $last_dispatch_at = ! empty( $state['last_dispatch_at'] ) ? (int) $state['last_dispatch_at'] : 0;
        $last_heartbeat   = ! empty( $state['last_heartbeat'] ) ? (int) $state['last_heartbeat'] : 0;
        $last_activity    = max( $last_dispatch_at, $last_heartbeat );

        return $last_activity === 0 || ( time() - $last_activity ) >= self::DISPATCH_STALE_SECONDS;
    }

    /**
     * Validate the async runner token.
     *
     * @return bool
     */
    protected function validate_async_request() {
        $token = isset( $_POST['token'] ) ? sanitize_text_field( wp_unslash( $_POST['token'] ) ) : '';

        return ! empty( $token ) && hash_equals( $this->get_async_token(), $token );
    }

    /**
     * Return the stored async token, creating one when needed.
     *
     * @return string
     */
    protected function get_async_token() {
        $token = get_option( self::ASYNC_TOKEN_OPTION, '' );

        if ( empty( $token ) ) {
            $token = wp_generate_password( 32, false, false );
            update_option( self::ASYNC_TOKEN_OPTION, $token, false );
        }

        return $token;
    }

    /**
     * Build state option name.
     *
     * @param string $flag_name Task flag.
     * @return string
     */
    protected function get_state_option_name( $flag_name ) {
        return self::STATE_OPTION_PREFIX . $flag_name;
    }

    /**
     * Return stored task state.
     *
     * @param string $flag_name Task flag.
     * @return array
     */
    protected function get_task_state( $flag_name ) {
        $state = get_option( $this->get_state_option_name( $flag_name ), array() );

        return is_array( $state ) ? $state : array();
    }

    /**
     * Merge and store task state, refreshing heartbeat metadata.
     *
     * @param string $flag_name Task flag.
     * @param array  $state     State values to merge.
     */
    protected function update_task_state( $flag_name, $state ) {
        $current = $this->get_task_state( $flag_name );
        $now     = time();

        $state['updated_at'] = $now;

        if ( empty( $current['started_at'] ) && empty( $state['started_at'] ) ) {
            $state['started_at'] = $now;
        }

        if ( empty( $state['status'] ) || in_array( $state['status'], array( 'running', 'complete', 'failed' ), true ) ) {
            $state['last_heartbeat'] = $now;
        }

        if ( ! empty( $state['status'] ) && in_array( $state['status'], array( 'running', 'complete' ), true ) && ! isset( $state['error'] ) ) {
            $state['error'] = null;
        }

        update_option( $this->get_state_option_name( $flag_name ), array_merge( $current, $state ), false );
    }

    /**
     * Instantiate a task class from its config.
     *
     * @param array $task_config Task configuration with 'file' and 'class' keys.
     * @return object|false
     */
    protected function instantiate_task( $task_config ) {
        $this->task_load_error = '';
        $file = TRP_PLUGIN_DIR . 'includes/upgrade/' . $task_config['file'];

        if ( ! file_exists( $file ) ) {
            $this->task_load_error = sprintf( 'Background task file is missing: %s', $task_config['file'] );
            return false;
        }

        require_once $file;

        if ( ! class_exists( $task_config['class'] ) ) {
            $this->task_load_error = sprintf( 'Background task class could not be loaded: %s', $task_config['class'] );
            return false;
        }

        return new $task_config['class']();
    }

    /**
     * Handle retry links for failed batch tasks.
     */
    public function maybe_handle_batch_task_action() {
        if ( empty( $_GET['trp_batch_action'] ) || empty( $_GET['trp_batch_task'] ) ) {
            return;
        }

        if ( ! current_user_can( apply_filters( 'trp_update_database_capability', 'manage_options' ) ) ) {
            return;
        }

        $flag_name = sanitize_text_field( wp_unslash( $_GET['trp_batch_task'] ) );
        $nonce     = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'trp_batch_task_action_' . $flag_name ) ) {
            return;
        }

        if ( sanitize_text_field( wp_unslash( $_GET['trp_batch_action'] ) ) === 'retry' ) {
            $this->start_task( $flag_name, false );
            wp_safe_redirect( remove_query_arg( array( 'trp_batch_action', 'trp_batch_task', '_wpnonce' ) ) );
            exit;
        }
    }

    /**
     * Claim the queue lock using an atomic insert.
     *
     * @return bool
     */
    protected function claim_lock() {
        $this->lock['token'] = wp_generate_uuid4();
        $this->lock['value'] = maybe_serialize(
            array(
                'token'   => $this->lock['token'],
                'expires' => time() + self::LOCK_TTL,
            )
        );

        if ( $this->insert_lock() ) {
            return true;
        }

        $existing = $this->get_lock_record();

        if ( ! empty( $existing['expires'] ) && $existing['expires'] > time() ) {
            $this->lock = array();
            return false;
        }

        if ( ! empty( $existing['raw'] ) ) {
            $this->delete_lock_value( $existing['raw'] );
        }

        if ( $this->insert_lock() ) {
            return true;
        }

        $this->lock = array();

        return false;
    }

    /**
     * Release only the lock owned by this runner.
     */
    protected function release_lock() {
        if ( empty( $this->lock['token'] ) || empty( $this->lock['value'] ) ) {
            return;
        }

        $existing = $this->get_lock_record();

        if ( ! empty( $existing['token'] ) && hash_equals( $existing['token'], $this->lock['token'] ) ) {
            $this->delete_lock_value( $existing['raw'] );
        }

        $this->lock = array();
    }

    /**
     * Check whether a non-expired queue lock exists.
     *
     * @return bool
     */
    protected function has_fresh_lock() {
        $lock = $this->get_lock_record();

        return ! empty( $lock['expires'] ) && $lock['expires'] > time();
    }

    /**
     * Clear an expired queue lock.
     */
    protected function clear_stale_lock() {
        $lock = $this->get_lock_record();

        if ( ! empty( $lock['raw'] ) && ! empty( $lock['expires'] ) && $lock['expires'] <= time() ) {
            $this->delete_lock_value( $lock['raw'] );
        }
    }

    /**
     * Insert the prepared lock value atomically.
     *
     * @return bool
     */
    protected function insert_lock() {
        global $wpdb;

        $wpdb->query(
            $wpdb->prepare(
                "INSERT IGNORE INTO `$wpdb->options` (option_name, option_value, autoload) VALUES (%s, %s, %s)",
                self::LOCK_OPTION,
                $this->lock['value'],
                'no'
            )
        );

        $inserted = (int) $wpdb->rows_affected === 1;
        $this->clear_option_cache( self::LOCK_OPTION );

        return $inserted;
    }

    /**
     * Extend the current runner's lock lease without overwriting another owner.
     *
     * @return bool
     */
    protected function refresh_lock() {
        global $wpdb;

        if ( empty( $this->lock['token'] ) || empty( $this->lock['value'] ) ) {
            return false;
        }

        $existing = $this->get_lock_record();

        if ( empty( $existing['token'] ) || ! hash_equals( $existing['token'], $this->lock['token'] ) ) {
            return false;
        }

        $new_value = maybe_serialize(
            array(
                'token'   => $this->lock['token'],
                'expires' => max( time() + self::LOCK_TTL, (int) $existing['expires'] + 1 ),
            )
        );

        $wpdb->query(
            $wpdb->prepare(
                "UPDATE `$wpdb->options` SET option_value = %s WHERE option_name = %s AND option_value = %s",
                $new_value,
                self::LOCK_OPTION,
                $existing['raw']
            )
        );

        if ( (int) $wpdb->rows_affected !== 1 ) {
            return false;
        }

        $this->lock['value'] = $new_value;
        $this->clear_option_cache( self::LOCK_OPTION );

        return true;
    }

    /**
     * Read the current lock directly from the options table.
     *
     * @return array
     */
    protected function get_lock_record() {
        global $wpdb;

        $raw = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT option_value FROM `$wpdb->options` WHERE option_name = %s LIMIT 1",
                self::LOCK_OPTION
            )
        );

        if ( $raw === null ) {
            return array(
                'raw'     => '',
                'token'   => '',
                'expires' => 0,
            );
        }

        $value = maybe_unserialize( $raw );

        if ( is_array( $value ) ) {
            return array(
                'raw'     => $raw,
                'token'   => isset( $value['token'] ) ? (string) $value['token'] : '',
                'expires' => isset( $value['expires'] ) ? (int) $value['expires'] : 0,
            );
        }

        // Backward compatibility for a lock written by the previous integer format.
        return array(
            'raw'     => $raw,
            'token'   => '',
            'expires' => (int) $value,
        );
    }

    /**
     * Delete a lock only if its complete stored value still matches.
     *
     * @param string $lock_value Serialized lock value.
     *
     * @return bool
     */
    protected function delete_lock_value( $lock_value ) {
        global $wpdb;

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM `$wpdb->options` WHERE option_name = %s AND option_value = %s",
                self::LOCK_OPTION,
                $lock_value
            )
        );

        $deleted = (int) $wpdb->rows_affected === 1;
        $this->clear_option_cache( self::LOCK_OPTION );

        return $deleted;
    }

    /**
     * Read an option directly so the todo state is refreshed after claiming the lock.
     *
     * @param string $option_name Option name.
     * @param mixed  $default     Default value.
     *
     * @return mixed
     */
    protected function read_option_from_database( $option_name, $default = false ) {
        global $wpdb;

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT option_value FROM `$wpdb->options` WHERE option_name = %s LIMIT 1",
                $option_name
            ),
            ARRAY_A
        );

        $this->clear_option_cache( $option_name );

        return is_array( $row ) && array_key_exists( 'option_value', $row ) ? maybe_unserialize( $row['option_value'] ) : $default;
    }

    /**
     * Clear WordPress's option caches after direct SQL writes.
     *
     * @param string $option_name Option name.
     */
    protected function clear_option_cache( $option_name ) {
        wp_cache_delete( $option_name, 'options' );
        wp_cache_delete( 'alloptions', 'options' );
        wp_cache_delete( 'notoptions', 'options' );
    }

    /**
     * Build a stored progress payload for notices/debugging.
     *
     * @param array $item   Todo item.
     * @param array $result Execution result.
     *
     * @return array
     */
    protected function get_item_progress( $item, $result ) {
        if ( ! empty( $result['progress'] ) && is_array( $result['progress'] ) ) {
            return $result['progress'];
        }

        $progress = array();

        if ( ! empty( $item['table'] ) ) {
            $progress['message'] = sprintf( __( 'Processing %s...', 'translatepress-multilingual' ), esc_html( $item['table'] ) );
        }

        return $progress;
    }

    /**
     * Register an admin notice.
     *
     * @param string $notification_id Notice id.
     * @param string $notice          Notice HTML/text.
     * @param string $class           Notice class.
     * @param bool   $dismissible     Whether to include a dismiss control.
     */
    protected function add_notice( $notification_id, $notice, $class, $dismissible = true ) {
        add_filter( 'safe_style_css', array( $this, 'allow_notice_flex_styles' ) );
        $message  = '<p style="padding-right:30px;">' . wp_kses(
            $notice,
            array(
                'a'      => array(
                    'href'   => array(),
                    'target' => array(),
                ),
                'strong' => array(),
                'span'   => array(
                    'class' => array(),
                    'style' => array(),
                ),
            )
        );
        remove_filter( 'safe_style_css', array( $this, 'allow_notice_flex_styles' ) );
        if ( $dismissible ) {
            $message .= '<a style="text-decoration: none;z-index:100;" href="' . esc_url( add_query_arg( array( 'trp_dismiss_admin_notification' => $notification_id ) ) ) . '" type="button" class="notice-dismiss"><span class="screen-reader-text">' . esc_html__( 'Dismiss this notice.', 'translatepress-multilingual' ) . '</span></a>';
        }
        $message .= '</p>';

        new TRP_Add_General_Notices( $notification_id, $message, $class, '', '', ! $dismissible );
    }

    /**
     * Allow the minimal inline styles needed to align the notice spinner.
     *
     * @param array $styles Allowed style properties.
     * @return array
     */
    public function allow_notice_flex_styles( $styles ) {
        $styles[] = 'display';
        $styles[] = 'align-items';
        $styles[] = 'gap';
        $styles[] = 'float';
        $styles[] = 'margin';
        $styles[] = 'visibility';

        return $styles;
    }
}
