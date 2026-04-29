<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

class TRP_Batch_Processor {

    const TIME_LIMIT       = 15.0; // seconds
    const RESCHEDULE_DELAY = 30;   // seconds between cron runs
    const CRON_HOOK        = 'trp_batch_process';

    /**
     * Register cron handler and admin notices.
     */
    public function __construct() {
        if ( ! apply_filters( 'trp_enable_batch_upgrades', true ) ) {
            return;
        }
        add_action( self::CRON_HOOK, array( $this, 'run_cron' ) );
        add_action( 'admin_init', array( $this, 'maybe_show_completion_notices' ) );
    }

    /**
     * Schedule a single cron event if one is not already scheduled.
     */
    public function schedule() {
        if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
            wp_schedule_single_event( time(), self::CRON_HOOK );
        }
    }

    /**
     * Cron callback. Process one pending task, reschedule if work remains.
     */
    public function run_cron() {
        $registry = TRP_Upgrade_Tasks_Registry::get_tasks();

        foreach ( $registry as $flag_name => $task_config ) {
            $flag_value = get_option( $flag_name, 'is not set' );

            if ( $flag_value !== 'no' ) {
                continue;
            }

            $this->process_task( $flag_name, $task_config );

            // Only process one task per cron run.
            break;
        }

        // Reschedule if any task still needs work.
        if ( $this->has_pending_tasks( $registry ) ) {
            wp_schedule_single_event( time() + self::RESCHEDULE_DELAY, self::CRON_HOOK );
        }
    }

    /**
     * Process a single task: build todo list if needed, then execute items within time budget.
     *
     * @param string $flag_name   The option name acting as the task flag.
     * @param array  $task_config Task configuration from registry.
     */
    protected function process_task( $flag_name, $task_config ) {
        $task = $this->instantiate_task( $task_config );

        if ( ! $task ) {
            // Mark as done to prevent running uselessly on every cron run.
            update_option( $flag_name, 'yes' );
            return;
        }

        $todo_option = 'trp_batch_todo_' . $flag_name;
        $todo_list   = get_option( $todo_option, false );

        // Build todo list on first run.
        if ( $todo_list === false ) {
            $todo_list = $task->build_todo_list();
            if ( empty( $todo_list ) ) {
                update_option( $flag_name, 'yes' );
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

            // Execute batches on this item until it's done or time runs out.
            while ( true ) {
                $result = $task->execute( $item );

                if ( isset( $result['error'] ) && $result['error'] !== '' ) {
                    $item['error'] = $result['error'];
                    $item['status'] = 'completed';
                    $changed = true;
                    break;
                }

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
        }
    }

    /**
     * Show dismissable admin notices on TranslatePress settings pages for completed tasks that found rows.
     */
    public function maybe_show_completion_notices() {
        $notifications = TRP_Plugin_Notifications::get_instance();
        if ( ! $notifications->is_plugin_page() ) {
            return;
        }

        $registry = TRP_Upgrade_Tasks_Registry::get_tasks();

        foreach ( $registry as $flag_name => $task_config ) {
            if ( empty( $task_config['completion_notice'] ) ) {
                continue;
            }

            // Only show if task is done and rows were found.
            if ( get_option( $flag_name ) !== 'yes' || get_option( 'trp_batch_found_rows_' . $flag_name ) !== 'yes' ) {
                continue;
            }

            $notification_id = 'trp_batch_notice_' . $flag_name;

            $message  = '<p style="padding-right:30px;">' . wp_kses( $task_config['completion_notice'], array( 'a' => array( 'href' => array(), 'target' => array() ) ) );
            $message .= '<a style="text-decoration: none;z-index:100;" href="' . esc_url( add_query_arg( array( 'trp_dismiss_admin_notification' => $notification_id ) ) ) . '" type="button" class="notice-dismiss"><span class="screen-reader-text">' . esc_html__( 'Dismiss this notice.', 'translatepress-multilingual' ) . '</span></a>';
            $message .= '</p>';

            new TRP_Add_General_Notices( $notification_id, $message, 'notice notice-info' );
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
     * Instantiate a task class from its config.
     *
     * @param array $task_config Task configuration with 'file' and 'class' keys.
     * @return object|false
     */
    protected function instantiate_task( $task_config ) {
        $file = TRP_PLUGIN_DIR . 'includes/upgrade/' . $task_config['file'];

        if ( ! file_exists( $file ) ) {
            return false;
        }

        require_once $file;

        if ( ! class_exists( $task_config['class'] ) ) {
            return false;
        }

        return new $task_config['class']();
    }
}
