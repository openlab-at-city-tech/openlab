<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AI Sync Cron Handler
 * 
 * Handles WP-Cron execution of sync jobs using a sequential approach:
 * - Each cron event processes exactly one post
 * - The next cron event is scheduled only after the current one completes
 * - This prevents concurrent execution and race conditions
 * - No locks needed as execution is naturally sequential
 */
class EPKB_AI_Sync_Cron_Handler {

	const CRON_INTERVAL = 5; // Seconds between processing each post
	
	/**
	 * Initialize cron handler
	 */
	public static function init() {
		// TODO add_action( EPKB_AI_Sync_Job_Manager::CRON_HOOK, array( __CLASS__, 'process_sync_cron' ) );
	}
	
	/**
	 * Process sync via cron - processes one post at a time
	 * 
	 * This method is called by WP-Cron to process sync jobs in the background.
	 * Each invocation processes one post and schedules the next invocation after completion.
	 */
	public static function process_sync_cron() {
		
		// Check if AI is enabled
		if ( ! EPKB_AI_Utilities::is_ai_chat_or_search_enabled() ) {
			EPKB_AI_Log::add_log( 'Cron sync skipped: AI is not enabled' );
			return;
		}
		
		// Get current job
		$job = EPKB_AI_Sync_Job_Manager::get_sync_job();
		if ( $job['status'] === 'idle' || $job['type'] !== 'cron' ) {
			EPKB_AI_Log::add_log( 'Cron sync skipped: No active cron job found' );
			return;
		}
		
		// Check if canceled
		if ( ! empty( $job['cancel_requested'] ) ) {
			EPKB_AI_Sync_Job_Manager::update_sync_job( array( 'status' => 'canceled' ) );
			EPKB_AI_Log::add_log( 'Cron sync canceled by user request' );
			return;
		}
		
		// Update status to running
		if ( $job['status'] === 'scheduled' ) {
			EPKB_AI_Sync_Job_Manager::update_sync_job( array( 'status' => 'running' ) );
		}
		
		// Process one post at a time (same as direct sync)
		$result = EPKB_AI_Sync_Job_Manager::process_next_sync_item();
		
		// Check result and schedule next cron ONLY if still running
		if ( $result['status'] === 'running' ) {
			// Schedule next cron event after a delay
			// This ensures no overlap - next cron only runs after this one completes
			wp_schedule_single_event( time() + self::CRON_INTERVAL, EPKB_AI_Sync_Job_Manager::CRON_HOOK );
			
			EPKB_AI_Log::add_log( 'Cron sync post processed, next cron scheduled', array(
				'processed' => isset( $result['processed'] ) ? $result['processed'] : 0,
				'errors' => isset( $result['errors'] ) ? $result['errors'] : 0
			) );

		} elseif ( $result['status'] === 'completed' ) {
			EPKB_AI_Log::add_log( 'Cron sync completed successfully', array( 'job' => $job ) );

		} elseif ( $result['status'] === 'canceled' ) {
			EPKB_AI_Log::add_log( 'Cron sync was canceled' );

		} else {
			EPKB_AI_Log::add_log( 'Cron sync ended with status: ' . $result['status'] );
		}
	}
}