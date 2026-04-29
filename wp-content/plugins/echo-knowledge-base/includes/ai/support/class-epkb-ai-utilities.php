<?php defined( 'ABSPATH' ) || exit();

class EPKB_AI_Utilities {

	const AI_PRO_NOTES_POST_TYPE = 'aipro_ai_note';
	const DEFAULT_TIMEOUT = 300;
	const DEFAULT_MINIMUM_EXECUTION_TIME_LIMIT = 60; // seconds
	const AI_PRO_PLUGIN_BASENAME = 'ai-features-pro/ai-features-pro.php';
	const AI_PRO_VERSION_MISMATCH_OPTION = 'epkb_ai_pro_version_mismatch';

	// Minimum AI Features Pro version compatible with the current KB version.
	const MIN_AI_PRO_VERSION = '6.2.0';

	/**
	 * Generate uuid v4 output.
	 *
	 * @return string
	 */
	public static function generate_uuid_v4() {
		// Try multiple methods for generating secure random data
		$bytes = false;
		
		// Method 1: random_bytes (most secure, PHP 7+)
		if ( function_exists( 'random_bytes' ) ) {
			try {
				$bytes = random_bytes( 16 );
			} catch ( Exception $e ) {
				$bytes = false;
			}
		}
		
		// Method 2: openssl_random_pseudo_bytes (widely compatible, PHP 5.3+)
		if ( $bytes === false && function_exists( 'openssl_random_pseudo_bytes' ) ) {
			$strong = false;
			$bytes = openssl_random_pseudo_bytes( 16, $strong );
			// Only use if cryptographically strong
			if ( ! $strong ) {
				$bytes = false;
			}
		}
		
		// Method 3: Fallback using multiple entropy sources
		if ( $bytes === false ) {
			// Combine multiple sources of entropy
			$entropy = uniqid( '', true );                    // Microsecond precision
			$entropy .= wp_rand();                            // WordPress random
			$entropy .= microtime( true );                    // Current time with microseconds
			$entropy .= serialize( $_SERVER );                // Server variables
			if ( function_exists( 'wp_salt' ) ) {
				$entropy .= wp_salt( 'auth' );                // WordPress salt if available
			}
			
			// Hash the combined entropy and take first 16 bytes
			$bytes = substr( hash( 'sha256', $entropy, true ), 0, 16 );
		}
		
		// Set UUID v4 version and variant bits
		$bytes[6] = chr( ord( $bytes[6] ) & 0x0f | 0x40 ); // version 4
		$bytes[8] = chr( ord( $bytes[8] ) & 0x3f | 0x80 ); // variant 10
		
		// Format as UUID string
		return vsprintf( '%s%s-%s-%s-%s-%s%s%s', str_split( bin2hex( $bytes ), 4 ) );
	}

	/**
	 * Check if AI Search feature is enabled
	 *
	 * @return bool True if AI Search is enabled (on or preview mode for admins)
	 */
	public static function is_ai_search_enabled() {
		$enabled = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_enabled', 'off' );
		return $enabled != 'off';
	}

	public static function is_ai_search_simple_enabled() {
		return self::is_ai_search_enabled_for_frontend() && EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_mode' ) === 'simple_search';
	}

	public static function is_ai_search_smart_enabled( $skip_preview_check = false ) {
		if ( ! self::is_ai_search_enabled_for_frontend( $skip_preview_check ) ) {
			return false;
		}
		$mode = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_mode' );

		// Handle legacy 'advanced_search' value - try to update it; TODO: remove after v16
		if ( $mode === 'advanced_search' ) {
			EPKB_AI_Config_Specs::update_ai_config_value( 'ai_search_mode', 'smart_search' );
			return true;
		}

		return $mode === 'smart_search';
	}

	// TODO: remove this method after v16 - kept for backward compatibility
	public static function is_ai_search_advanced_enabled( $skip_preview_check = false ) {
		return self::is_ai_search_smart_enabled( $skip_preview_check );
	}

	/**
	 * Check if AI Search feature is enabled for frontend; used by ASEA
	 *
	 * @return bool True if AI Search is enabled for frontend
	 */
	public static function is_ai_search_enabled_for_frontend( $skip_preview_check = false ) {
		if ( ! self::is_ai_search_enabled() || ( EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_enabled' ) == 'preview' && ! $skip_preview_check && ( ! function_exists('wp_get_current_user') || ! current_user_can( 'manage_options' ) ) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if AI Chat feature is enabled
	 *
	 * @return bool True if AI Chat is enabled (on or preview mode for admins)
	 */
	public static function is_ai_chat_enabled() {
		$ai_chat_enabled = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_chat_enabled', 'off' );
		
		// Check if chat is enabled
		return $ai_chat_enabled != 'off';
	}

	/**
	 * Check if any AI features are enabled (including preview mode)
	 *
	 * @return bool True if either AI Search or AI Chat is not 'off'
	 */
	public static function is_ai_chat_or_search_enabled() {
		return self::is_ai_search_enabled() || self::is_ai_chat_enabled();
	}

	/**
	 * Check if AI is configured with API credentials (has API key and terms accepted)
	 * This is the base requirement for any AI feature, regardless of whether AI Chat or AI Search are enabled.
	 *
	 * @return bool True if API key is set and terms are accepted
	 */
	public static function is_ai_configured() {
		$ai_key = EPKB_AI_Config_Specs::get_unmasked_api_key_for_provider( EPKB_AI_Provider::get_active_provider() );
		$disclaimer_accepted = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_disclaimer_accepted', 'off' );

		return ! empty( $ai_key ) && $disclaimer_accepted === 'on';
	}

	/**
	 * Register AI PRO compatibility hooks.
	 */
	public static function register_ai_pro_version_guard() {
		add_action( 'plugins_loaded', array( __CLASS__, 'maybe_disable_incompatible_ai_pro' ), 1 );
		add_action( 'admin_notices', array( __CLASS__, 'display_ai_pro_version_mismatch_admin_notice' ) );
		add_action( 'network_admin_notices', array( __CLASS__, 'display_ai_pro_version_mismatch_admin_notice' ) );
	}

	/**
	 * Check if AI Features Pro is enabled
	 *
	 * @return bool True if AI Features Pro is enabled
	 */
	public static function is_ai_features_pro_enabled() {
		return defined( 'AI_FEATURES_PRO_PLUGIN_NAME' ) && ! self::is_ai_pro_version_outdated();
	}

	/**
	 * Check whether the active AI Features Pro version is older than the minimum required by KB.
	 *
	 * @return bool True if AI PRO is active and its version is below MIN_AI_PRO_VERSION.
	 */
	public static function is_ai_pro_version_outdated() {
		if ( ! defined( 'AI_FEATURES_PRO_PLUGIN_NAME' ) || ! class_exists( 'AI_Features_Pro' ) || empty( AI_Features_Pro::$version ) ) {
			return false;
		}
		return version_compare( AI_Features_Pro::$version, self::MIN_AI_PRO_VERSION, '<' );
	}

	/**
	 * Prevent incompatible AI PRO from bootstrapping and disable it when core can own the notice.
	 */
	public static function maybe_disable_incompatible_ai_pro() {

		if ( ! defined( 'AI_FEATURES_PRO_PLUGIN_NAME' ) ) {
			return;
		}

		if ( ! self::is_ai_pro_version_outdated() ) {
			self::clear_ai_pro_version_mismatch_notice_if_resolved();
			return;
		}

		remove_action( 'plugins_loaded', 'aipro_get_instance' );

		$mismatch = array(
			'kb_version' => Echo_Knowledge_Base::$version,
			'ai_pro_version' => AI_Features_Pro::$version,
			'required_ai_pro_version' => self::MIN_AI_PRO_VERSION,
		);

		update_option( self::AI_PRO_VERSION_MISMATCH_OPTION, $mismatch, false );

		if ( ! is_admin() || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		if ( ! function_exists( 'deactivate_plugins' ) || ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$autoloader_file = WP_PLUGIN_DIR . '/ai-features-pro/includes/system/class-aipro-autoloader.php';
		if ( ! class_exists( 'AIPRO_AI_Email_Notifications_Handler' ) && file_exists( $autoloader_file ) ) {    /* @disregard PREFIX */
			require_once $autoloader_file;
		}

		deactivate_plugins(
			self::AI_PRO_PLUGIN_BASENAME,
			true,
			is_multisite() && is_plugin_active_for_network( self::AI_PRO_PLUGIN_BASENAME )
		);
	}

	/**
	 * Render a persistent, non-dismissible admin notice when core disabled an incompatible AI PRO build.
	 */
	public static function display_ai_pro_version_mismatch_admin_notice() {

		if ( ! current_user_can( EPKB_Utilities::ADMIN_CAPABILITY ) ) {
			return;
		}

		if ( ! EPKB_KB_Handler::is_kb_request() ) {
			return;
		}

		$notice = get_option( self::AI_PRO_VERSION_MISMATCH_OPTION, array() );
		if ( empty( $notice ) || empty( $notice['required_ai_pro_version'] ) ) {
			return;
		}

		$installed_version = self::get_installed_ai_pro_version();
		if ( empty( $installed_version ) || version_compare( $installed_version, self::MIN_AI_PRO_VERSION, '>=' ) ) {
			delete_option( self::AI_PRO_VERSION_MISMATCH_OPTION );
			return;
		}

		$plugins_url = is_network_admin() ? network_admin_url( 'plugins.php' ) : admin_url( 'plugins.php' );
		?>
		<div class="notice notice-error">
			<p>
				<strong><?php esc_html_e( 'AI Features could not be loaded.', 'echo-knowledge-base' ); ?></strong>
				<?php
				if ( defined( 'AMAG_PLUGIN_NAME' ) ) {
					printf(
						/* translators: 1: installed AI Features version, 2: current Access Manager version, 3: required AI Features version */
						esc_html__( 'AI Features %1$s is incompatible with Access Manager %2$s. Access Manager %2$s requires AI Features %3$s or later. Update AI Features, then activate it again.', 'echo-knowledge-base' ),
						esc_html( $installed_version ),
						esc_html( Echo_Knowledge_Base::$amag_version ),
						esc_html( self::MIN_AI_PRO_VERSION )
					);
				} else {
					printf(
						/* translators: 1: installed AI Features version, 2: current Knowledge Base version, 3: required AI Features version */
						esc_html__( 'AI Features %1$s is incompatible with Knowledge Base %2$s. Knowledge Base %2$s requires AI Features %3$s or later. Update AI Features, then activate it again.', 'echo-knowledge-base' ),
						esc_html( $installed_version ),
						esc_html( Echo_Knowledge_Base::$version ),
						esc_html( self::MIN_AI_PRO_VERSION )
					);
				}
				?>
				<a href="<?php echo esc_url( $plugins_url ); ?>"><?php esc_html_e( 'Go to Plugins', 'echo-knowledge-base' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Clear the stored mismatch notice once AI PRO is removed or updated to a compatible version.
	 */
	private static function clear_ai_pro_version_mismatch_notice_if_resolved() {
		$notice = get_option( self::AI_PRO_VERSION_MISMATCH_OPTION, array() );
		if ( empty( $notice ) ) {
			return;
		}

		$installed_version = self::get_installed_ai_pro_version();
		if ( empty( $installed_version ) || version_compare( $installed_version, self::MIN_AI_PRO_VERSION, '>=' ) ) {
			delete_option( self::AI_PRO_VERSION_MISMATCH_OPTION );
		}
	}

	/**
	 * Read the installed AI PRO version directly from the plugin header.
	 *
	 * @return string
	 */
	private static function get_installed_ai_pro_version() {
		$plugin_file = WP_PLUGIN_DIR . '/' . self::AI_PRO_PLUGIN_BASENAME;
		if ( ! file_exists( $plugin_file ) ) {
			return '';
		}

		$file_data = get_file_data( $plugin_file, array( 'Version' => 'Version' ) );
		return empty( $file_data['Version'] ) ? '' : $file_data['Version'];
	}

	/**
	 * Render a large, non-dismissible notice when AI PRO is older than required by KB.
	 * Intended to be called at the top of AI admin pages.
	 */
	public static function display_ai_pro_version_mismatch_notice() {

		if ( ! self::is_ai_pro_version_outdated() ) {
			return;
		}   ?>

		<div class="epkb-ai-version-mismatch-notice" role="alert">
			<h2 class="epkb-ai-version-mismatch-notice__title">
				<span class="epkbfa epkbfa-exclamation-triangle" aria-hidden="true"></span>
				<?php esc_html_e( 'AI Features Plugin Update Required', 'echo-knowledge-base' ); ?>
			</h2>
			<p class="epkb-ai-version-mismatch-notice__message">				<?php
				printf(
					/* translators: 1: Knowledge Base version 2: Required AI Features Pro version 3: Currently installed AI Features Pro version */
					esc_html__( 'Knowledge Base %1$s requires the AI Features plugin %2$s or later. You are currently running AI Features %3$s. Please update the AI Features plugin to ensure compatibility.', 'echo-knowledge-base' ),
					esc_html( Echo_Knowledge_Base::$version ),
					esc_html( self::MIN_AI_PRO_VERSION ),
					esc_html( AI_Features_Pro::$version )
				);				?>
			</p>
			<p class="epkb-ai-version-mismatch-notice__action">
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>"><?php esc_html_e( 'Go to Plugins', 'echo-knowledge-base' ); ?></a>
			</p>
		</div>			<?php
	}

	/**
	 * Send chat error notification emails
	 *
	 * @param string $subject Subject for email.
	 * @param string $message Message text for email.
	 * @return bool|WP_Error True on success, WP_Error on failure
	 */
	public static function send_ai_notification_email( $subject, $message ) {

		$current_date = gmdate( 'Y_m_d' );
		$maximum_notification_count = 10;

		// limit number of emails sent each day
		$error_notification_count   = get_transient( 'epkb_ai_error_notification_count_' . $current_date );
		if ( $error_notification_count === false ) {
			$error_notification_count = 0;
		} elseif ( $error_notification_count === $maximum_notification_count - 1 ) { // Last notification for today.
			$message .= esc_html__( 'No additional emails will be sent today. Check the Chat AI dashboard for more details.', 'echo-knowledge-base' );

		} elseif ( $error_notification_count >= $maximum_notification_count ) {
			return new WP_Error( 'daily_limit_reached', __( 'Daily email notification limit reached', 'echo-knowledge-base' ) );
		}

		// send notification if email defined
		$email_ids = ''; // TODO
		$email_ids = explode( ',', $email_ids );
		if ( empty( $email_ids ) ) {
			return new WP_Error( 'no_recipients', __( 'No email recipients configured', 'echo-knowledge-base' ) );
		}

		// update the transient only if emails were sent
		$result = set_transient( 'epkb_ai_error_notification_count_' . $current_date, ++$error_notification_count, DAY_IN_SECONDS );
		if ( $result === false ) {
			// prevent sending too many notifications if failed to set the transient
			return new WP_Error( 'transient_error', __( 'Failed to update notification count', 'echo-knowledge-base' ) );
		}

		$errors = array();
		foreach ( $email_ids as $email_id ) {
			$email_error = EPKB_Utilities::send_email( self::prepare_email_message_body(  $subject, $message, $email_id ), true, $email_id, '', '', $subject );
			if ( ! empty( $email_error ) ) {
				// translators: %1$s is the email address, %2$s is the error message
				$errors[] = sprintf( __( 'Failed to send email to %1$s: %2$s', 'echo-knowledge-base' ), $email_id, $email_error );
			}
		}

		// Return error if all emails failed
		if ( count( $errors ) == count( $email_ids ) ) {
			return new WP_Error( 'all_emails_failed', implode( '; ', $errors ) );
		}

		// Return true if at least one email was sent
		return true;
	}

	/**
	 * Prepare email message body for sending
	 * @param string $subject
	 * @param string $message
	 * @param string $email
	 * @return string
	 */
	public static function prepare_email_message_body( $subject, $message, $email ) {
		$email_message = '
				<html>
					<body>
						<table cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
							<tbody>
								<tr style="background-color:#EAF2FA;">
									<td colspan="2" style="font-family: sans-serif; font-size:12px;padding:3px;"><strong>' . esc_html__( 'Email', 'echo-knowledge-base' ) . '</strong></td>
			                    </tr>
			                    <tr style="background-color:#FFFFFF;">
									<td width="20" style="padding:3px;">&nbsp;</td>
									<td style="font-family: sans-serif; font-size:12px;padding:3px;"><a href="mailto:' . esc_html( $email ) . '">' . esc_html( $email ) . '</a></td>
			                    </tr>
			                    <tr style="background-color:#EAF2FA;">
									<td colspan="2" style="font-family: sans-serif; font-size:12px;padding:3px;"><strong>' . esc_html__( 'Subject', 'echo-knowledge-base' ) . '</strong></td>
			                    </tr>
			                    <tr style="background-color:#FFFFFF;">
									<td width="20" style="padding:3px;">&nbsp;</td>
									<td style="font-family: sans-serif; font-size:12px;padding:3px;">' . esc_html( $subject ) . '</td>
			                    </tr>
								<tr style="background-color:#EAF2FA">
									<td colspan="2" style="font-family: sans-serif; font-size:12px;padding:3px;"><strong>' . esc_html__( 'Message', 'echo-knowledge-base' ) . '</strong></td>
			                    </tr>
			                    <tr style="background-color:#FFFFFF;">
									<td width="20" style="padding:3px;">&nbsp;</td>
									<td style="font-family: sans-serif; font-size:12px;padding:3px;">' . wp_kses_post( $message ) . '<br /></td>
			                    </tr> 
							</tbody>
						</table>
					</body>
				</html>';

		return $email_message;
	}

	/**
	 * Get available post types for vector store sync
	 *
	 * @return array
	 */
	public static function get_available_post_types_for_ai() {
		$post_types = array();

		// Add KB post types (list all when Multiple KBs is active; otherwise stop after the first)
		$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
		$show_all_kbs = EPKB_Utilities::is_multiple_kbs_enabled();
		foreach ( $all_kb_configs as $kb_config ) {
			// Skip archived KBs
			if ( isset( $kb_config['status'] ) && $kb_config['status'] === EPKB_KB_Config_Specs::ARCHIVED ) {
				continue;
			}

			$kb_id = $kb_config['id'];
			$kb_post_type = EPKB_KB_Handler::get_post_type( $kb_id );
			// translators: %d is the Knowledge Base ID
			$kb_name = isset( $kb_config['kb_name'] ) ? $kb_config['kb_name'] : sprintf( __( 'Knowledge Base %d', 'echo-knowledge-base' ), $kb_id );
			$post_types[ $kb_post_type ] = $kb_name;
			if ( ! $show_all_kbs ) { break; }
		}

		// Always show Posts, Pages, and Notes in the list (require AI Pro to use)
		$post_types['post'] = __( 'Posts', 'echo-knowledge-base' );
		$post_types['page'] = __( 'Pages', 'echo-knowledge-base' );
		$post_types[ self::AI_PRO_NOTES_POST_TYPE ] = __( 'Additional Notes', 'echo-knowledge-base' );

		// Add other public CPTs (require AI Pro to use)
		$public_cpts = get_post_types( array( 'public' => true ), 'objects' );
		foreach ( $public_cpts as $slug => $obj ) {
			// Skip post types we already handle
			if ( $slug === 'post' || $slug === 'page' || $slug === 'attachment' || $slug === self::AI_PRO_NOTES_POST_TYPE ) {
				continue;
			}
			// Skip KB post types (already added above)
			if ( EPKB_KB_Handler::is_kb_post_type( $slug ) ) {
				continue;
			}
			$label = isset( $obj->labels->name ) ? $obj->labels->name : ucfirst( $slug );
			$post_types[ $slug ] = $label;
		}

		return $post_types;
	}

	/**
	 * Prepare a training data row for admin display.
	 *
	 * @param object $item Training data row.
	 * @return object
	 */
	public static function prepare_training_data_item_for_display( $item ) {

		if ( empty( $item ) || ! is_object( $item ) ) {
			return $item;
		}

		$item->type_name = self::get_training_data_type_name( $item->type );
		$item->item_type = $item->type;
		$item->can_delete_missing_source = self::can_delete_missing_source_training_data_item( $item );
		if ( $item->can_delete_missing_source ) {
			$item->error_message = __( 'Original post not found. It may have been deleted.', 'echo-knowledge-base' );
		}

		return $item;
	}

	/**
	 * Get a display label for a training data type.
	 *
	 * @param string $type Training data post type.
	 * @return string
	 */
	public static function get_training_data_type_name( $type ) {

		$post_type_obj = get_post_type_object( $type );
		if ( $post_type_obj && isset( $post_type_obj->labels->name ) ) {
			if ( strpos( $type, 'epkb_post_type' ) === 0 ) {
				$type_name = $post_type_obj->labels->name;
			} else {
				$type_name = $post_type_obj->labels->singular_name;
			}
		} else {
			$type_name = ucfirst( $type );
		}

		if ( strlen( $type_name ) > 20 ) {
			return substr( $type_name, 0, 18 ) . '..';
		}

		return $type_name;
	}

	/**
	 * Determine whether an errored training data row can be safely deleted because its source is gone.
	 *
	 * @param object $item Training data row.
	 * @return bool
	 */
	public static function can_delete_missing_source_training_data_item( $item ) {
		return ! empty( $item ) &&
			is_object( $item ) &&
			! empty( $item->status ) &&
			in_array( $item->status, array( 'error', 'skipped' ), true ) &&
			! empty( $item->error_message ) &&
			$item->error_message === 'post_not_found';
	}

	/**
	 * Check whether a training data type is backed by a WordPress post.
	 *
	 * @param string $type Training data type.
	 * @return bool
	 */
	public static function is_post_backed_training_data_type( $type ) {

		if ( empty( $type ) || $type === 'PDF' ) {
			return false;
		}

		if ( $type === self::AI_PRO_NOTES_POST_TYPE || $type === 'epkb_ai_note' ) {
			return true;
		}

		return (bool) get_post_type_object( $type );
	}

	/**
	 * Check whether auto-sync is enabled.
	 *
	 * @return bool
	 */
	public static function is_auto_sync_enabled() {
		return EPKB_AI_Config_Specs::get_ai_config_value( 'ai_auto_sync_enabled', 'off' ) === 'on';
	}

	/**
	 * Reconcile post-backed training data rows with their source posts.
	 *
	 * Used when the Training Data UI is refreshed or opened.
	 * Missing or no-longer-eligible sources are marked as skipped.
	 * Synced items whose source changed are marked as outdated.
	 *
	 * @param int                      $collection_id Collection ID.
	 * @param EPKB_AI_Training_Data_DB $training_data_db Optional DB instance.
	 * @return void
	 */
	public static function reconcile_collection_source_updates( $collection_id, $training_data_db = null ) {

		$collection_id = absint( $collection_id );
		if ( empty( $collection_id ) ) {
			return;
		}

		if ( ! $training_data_db ) {
			$training_data_db = new EPKB_AI_Training_Data_DB();
		}

		$reconcilable_types = self::get_refresh_reconcilable_training_types();
		if ( empty( $reconcilable_types ) ) {
			return;
		}

		$training_rows = $training_data_db->get_training_data_by_collection( $collection_id, array(
			'status' => self::get_refresh_reconcilable_training_statuses(),
			'type'   => $reconcilable_types,
		) );
		if ( empty( $training_rows ) || is_wp_error( $training_rows ) ) {
			return;
		}

		foreach ( $training_rows as $training_row ) {
			if ( ! self::is_refresh_reconcilable_training_row( $training_row ) ) {
				continue;
			}

			$post = get_post( absint( $training_row->item_id ) );
			$unavailable_source_code = self::get_training_row_unavailable_source_code( $post );
			if ( ! empty( $unavailable_source_code ) ) {
				self::mark_unavailable_training_row_as_skipped( $training_row, $unavailable_source_code, $training_data_db );
				continue;
			}

			if ( ! in_array( $training_row->status, array( 'added', 'updated' ), true ) ) {
				continue;
			}

			if ( ! self::did_training_source_change_since_sync( $training_row, $post ) ) {
				continue;
			}

			$training_data_db->update_training_data( $training_row->id, array( 'status' => 'outdated' ) );
		}
	}

	/**
	 * Determine whether a training row can be reconciled against a WordPress post.
	 *
	 * @param object $training_row Training data row.
	 * @return bool
	 */
	private static function is_refresh_reconcilable_training_row( $training_row ) {

		if ( empty( $training_row ) || ! is_object( $training_row ) || empty( $training_row->id ) ) {
			return false;
		}

		if ( ! in_array( $training_row->status, self::get_refresh_reconcilable_training_statuses(), true ) ) {
			return false;
		}

		if ( empty( $training_row->item_id ) || ! ctype_digit( (string) $training_row->item_id ) ) {
			return false;
		}

		if ( empty( $training_row->type ) || $training_row->type === 'PDF' || $training_row->type === 'HTML' ) {
			return false;
		}

		return self::is_post_backed_training_data_type( $training_row->type );
	}

	/**
	 * Mark an unavailable training row as skipped after cleaning up stale provider files.
	 *
	 * @param object                    $training_row Training data row.
	 * @param string                    $unavailable_source_code Source unavailability code.
	 * @param EPKB_AI_Training_Data_DB  $training_data_db Training data DB instance.
	 * @return bool|WP_Error
	 */
	private static function mark_unavailable_training_row_as_skipped( $training_row, $unavailable_source_code, $training_data_db ) {

		if ( ! empty( $training_row->file_id ) ) {
			$cleanup_result = self::cleanup_training_data_record_from_provider( $training_row, 'refresh_reconcile_unavailable_source' );
			if ( is_wp_error( $cleanup_result ) ) {
				return $cleanup_result;
			}
		}

		return $training_data_db->update_training_data( $training_row->id, array(
			'status'        => 'skipped',
			'error_code'    => $unavailable_source_code === 'post_not_found' ? 404 : 400,
			'error_message' => $unavailable_source_code,
			'file_id'       => '',
			'store_id'      => '',
		) );
	}

	/**
	 * Get the refresh reconciliation error code for an unavailable source post.
	 *
	 * @param WP_Post|null $post Source post.
	 * @return string Empty string when the post is available.
	 */
	private static function get_training_row_unavailable_source_code( $post ) {

		if ( empty( $post ) || ! is_object( $post ) || empty( $post->ID ) ) {
			return 'post_not_found';
		}

		$eligibility_check = EPKB_Admin_UI_Access::is_post_eligible_for_ai_training( $post );
		if ( ! is_wp_error( $eligibility_check ) ) {
			return '';
		}

		if ( $eligibility_check->get_error_code() === 'post_not_published' && isset( $post->post_status ) && $post->post_status === 'trash' ) {
			return 'post_not_found';
		}

		return (string) $eligibility_check->get_error_code();
	}

	/**
	 * Get the training data types that can be reconciled during refresh.
	 *
	 * @return array
	 */
	private static function get_refresh_reconcilable_training_types() {

		$types = get_post_types();
		if ( ! is_array( $types ) ) {
			$types = array();
		}

		$types[] = self::AI_PRO_NOTES_POST_TYPE;
		$types[] = 'epkb_ai_note';

		return array_values( array_unique( array_filter( $types ) ) );
	}

	/**
	 * Get the statuses that refresh reconciliation can safely process.
	 *
	 * @return array
	 */
	private static function get_refresh_reconcilable_training_statuses() {
		return array( 'pending', 'added', 'updated', 'outdated' );
	}

	/**
	 * Delete provider-side file references for a training data record.
	 *
	 * @param object $record Training data record.
	 * @param string $reason Cleanup reason for logs.
	 * @return bool|WP_Error
	 */
	public static function cleanup_training_data_record_from_provider( $record, $reason = 'training_data_delete' ) {

		if ( empty( $record ) || empty( $record->file_id ) ) {
			return true;
		}

		$provider = ! empty( $record->provider ) ? sanitize_key( (string) $record->provider ) : '';
		if ( $provider === '' && ! empty( $record->collection_id ) ) {
			$collection_config = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( (int) $record->collection_id );
			if ( ! is_wp_error( $collection_config ) && ! empty( $collection_config['ai_training_data_provider'] ) ) {
				$provider = sanitize_key( (string) $collection_config['ai_training_data_provider'] );
			}
		}
		if ( $provider === '' ) {
			$provider = EPKB_AI_Provider::get_active_provider();
		}

		$vector_store_handler = EPKB_AI_Provider::get_vector_store_handler( $provider );
		$log_context = array(
			'training_data_id' => isset( $record->id ) ? (int) $record->id : 0,
			'item_id'          => isset( $record->item_id ) ? (string) $record->item_id : '',
			'collection_id'    => isset( $record->collection_id ) ? (int) $record->collection_id : 0,
			'provider'         => $provider,
			'store_id'         => isset( $record->store_id ) ? (string) $record->store_id : '',
			'file_id'          => (string) $record->file_id,
			'reason'           => $reason,
		);
		$cleanup_error = null;

		if ( ! empty( $record->store_id ) ) {
			$remove_result = $vector_store_handler->remove_file_from_vector_store( $record->store_id, $record->file_id );
			if ( is_wp_error( $remove_result ) && $remove_result->get_error_code() !== 'not_found' ) {
				EPKB_AI_Log::add_log( $remove_result, array_merge( $log_context, array(
					'message' => 'Failed to remove training data file from vector store during cleanup',
				) ) );
				$cleanup_error = $remove_result;
			}
		}

		$delete_result = $vector_store_handler->delete_file_from_file_storage( $record->file_id, $record->store_id );
		if ( is_wp_error( $delete_result ) && $delete_result->get_error_code() !== 'not_found' ) {
			EPKB_AI_Log::add_log( $delete_result, array_merge( $log_context, array(
				'message' => 'Failed to delete training data file from AI provider during cleanup',
			) ) );
			if ( ! is_wp_error( $cleanup_error ) ) {
				$cleanup_error = $delete_result;
			}
		}

		return is_wp_error( $cleanup_error ) ? $cleanup_error : true;
	}

	/**
	 * Determine whether the source post changed after the last successful sync.
	 *
	 * @param object  $training_row Training data row.
	 * @param WP_Post $post Source post.
	 * @return bool
	 */
	private static function did_training_source_change_since_sync( $training_row, $post ) {

		if ( empty( $training_row->last_synced ) ) {
			return true;
		}

		$post_modified_gmt = ! empty( $post->post_modified_gmt ) && $post->post_modified_gmt !== '0000-00-00 00:00:00'
			? $post->post_modified_gmt
			: get_gmt_from_date( $post->post_modified );

		if ( ! empty( $post_modified_gmt ) && strtotime( $post_modified_gmt ) > strtotime( $training_row->last_synced ) ) {
			return true;
		}

		return self::normalize_training_data_title_for_comparison( isset( $training_row->title ) ? $training_row->title : '' )
			!== self::normalize_training_data_title_for_comparison( $post->post_title );
	}

	/**
	 * Normalize a training data title for storage-compatible comparisons.
	 *
	 * @param string $title Training data title.
	 * @return string
	 */
	private static function normalize_training_data_title_for_comparison( $title ) {
		return EPKB_AI_Validation::validate_title( $title );
	}

	/**
	 * Get AI Training Data Collection options for dropdowns/selects
	 * Returns an array of collection_id => collection_name pairs
	 *
	 * @param string $format Optional format: 'simple' (default) returns key => value, 'block' returns array with key/name/style
	 * @return array Collection options in the requested format
	 */
	public static function get_collection_options( $format = 'simple' ) {
		// Start with "Select Collection" option for value 0
		$select_collection_label = __( 'Select Collection', 'echo-knowledge-base' );
		if ( $format === 'block' ) {
			$options = array(
				array(
					'key' => 0,
					'name' => $select_collection_label,
					'style' => array(),
				)
			);
		} else {
			$options = array( 0 => $select_collection_label );
		}

		// Get all training data collections
		$collections = EPKB_AI_Training_Data_Config_Specs::get_training_data_collections();
		if ( ! is_wp_error( $collections ) && ! empty( $collections ) ) {
			foreach ( $collections as $collection_id => $collection_config ) {
				$collection_name = isset( $collection_config['ai_training_data_store_name'] )
					? $collection_config['ai_training_data_store_name']
					: EPKB_AI_Training_Data_Config_Specs::get_default_collection_name( $collection_id );

				if ( $format === 'block' ) {
					$options[] = array(
						'key' => (int)$collection_id,
						'name' => $collection_name,
						'style' => array(),
					);
				} else {
					$options[ $collection_id ] = $collection_name;
				}
			}
		}

		return $options;
	}

	/**
	 * Sleep safely but never longer than MAX_SLEEP seconds.
	 *
	 * @param float $seconds  The requested delay.
	 * @param float $max      The absolute cap (default 30 s).
	 */
	public static function safe_sleep( $seconds, $max = 30.0 ) {
		// Return early if seconds is negative or zero
		if ( $seconds <= 0 ) {
			return;
		}
		
		// Cap seconds at max if too large
		if ( $seconds > $max ) {
			$seconds = $max;
		}
		
		if ( $seconds < 1 ) {
			usleep( (int) ( $seconds * 1000000 ) );
		} else {
			sleep( (int) round( $seconds ) );
		}

	}

	/**
	 * Get client IP address (hashed for privacy)
	 *
	 * @return string Hashed IP address or empty string
	 */
	public static function get_hashed_ip() {
		$ip_keys = array(
			'HTTP_X_FORWARDED_FOR',
			'HTTP_CLIENT_IP',
			'HTTP_X_REAL_IP',
			'HTTP_CF_CONNECTING_IP',
			'REMOTE_ADDR'
		);

		$raw_ip = '';

		// First check for public IP addresses (ignore private/reserved ranges)
		foreach ( $ip_keys as $key ) {
			if ( ! empty( $_SERVER[$key] ) ) {
				$ip = sanitize_text_field( $_SERVER[$key] );

				// Handle comma-separated IPs (from proxies)
				if ( strpos( $ip, ',' ) !== false ) {
					$ips = explode( ',', $ip );
					$ip = trim( $ips[0] );
				}

				// Validate IP address
				if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
					$raw_ip = $ip;
					break;
				}
			}
		}

		// If no valid public IP found, check for any valid IP (including private)
		if ( empty( $raw_ip ) ) {
			foreach ( $ip_keys as $key ) {
				if ( ! empty( $_SERVER[$key] ) ) {
					$ip = sanitize_text_field( $_SERVER[$key] );

					// Handle comma-separated IPs (from proxies)
					if ( strpos( $ip, ',' ) !== false ) {
						$ips = explode( ',', $ip );
						$ip = trim( $ips[0] );
					}

					// Validate any IP address
					if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
						$raw_ip = $ip;
						break;
					}
				}
			}
		}

		// Hash the IP address for privacy (GDPR compliance)
		if ( ! empty( $raw_ip ) ) {
			// Use a consistent salt for the same IP to produce the same hash
			// This allows rate limiting while preserving privacy
			return wp_hash( $raw_ip . wp_salt() );
		}

		return '';
	}

	/**
	 * Enqueue AI search scripts when a search component is rendered.
	 * Call this from any component that renders a search box with AI capabilities.
	 * Note: Inline script data is pre-attached during script registration in scripts-registration-public.php
	 */
	public static function enqueue_ai_search_scripts() {
		static $scripts_enqueued = false;

		if ( ! self::is_ai_search_enabled() || $scripts_enqueued ) {
			return;
		}

		wp_enqueue_script( 'epkb-ai-chat-util' );
		wp_enqueue_script( 'epkb-marked' );
		wp_enqueue_script( 'epkb-ai-search' );

		$scripts_enqueued = true;

		// Also enqueue search results if smart search enabled
		if ( self::is_ai_search_smart_enabled() ) {
			self::enqueue_ai_search_results_scripts();
		}
	}

	/**
	 * Enqueue Advanced AI Search Results scripts and styles.
	 * Note: Inline script data is pre-attached during script registration in scripts-registration-public.php
	 */
	public static function enqueue_ai_search_results_scripts() {
		static $results_enqueued = false;

		if ( $results_enqueued ) {
			return;
		}

		wp_enqueue_style( 'epkb-ai-search-results' );
		wp_enqueue_script( 'epkb-ai-search-results' );

		$results_enqueued = true;
	}

	/**
	 * Determine if an AI answer is empty after removing markup and whitespace.
	 *
	 * @param string $answer
	 * @return bool
	 */
	public static function is_empty_ai_answer( $answer ) {
		if ( ! is_string( $answer ) ) {
			return true;
		}

		return trim( wp_strip_all_tags( $answer ) ) === '';
	}

	/**
	 * Determine if an AI answer is the configured refusal response.
	 *
	 * @param string $answer
	 * @return bool
	 */
	public static function is_ai_refusal_answer( $answer ) {
		if ( self::is_empty_ai_answer( $answer ) ) {
			return false;
		}

		$normalized = trim( wp_strip_all_tags( $answer ) );

		return stripos( $normalized, EPKB_AI_Config_Specs::get_ai_refusal_message() ) !== false
			|| stripos( $normalized, EPKB_AI_Config_Specs::get_ai_refusal_prompt() ) !== false;
	}

	/**
	 * Determine if sources should be displayed for the current AI answer.
	 *
	 * @param string $answer
	 * @return bool
	 */
	public static function should_show_sources_for_answer( $answer ) {
		return ! self::is_empty_ai_answer( $answer ) && ! self::is_ai_refusal_answer( $answer );
	}

	/**
	 * Determine if an AI search answer is the configured refusal message.
	 *
	 * @param string $answer
	 * @return bool
	 */
	public static function is_search_refusal_answer( $answer ) {
		return self::is_ai_refusal_answer( $answer );
	}

	/**
	 * Calculate exponential backoff delay
	 *
	 * @param int $retry_count Current retry attempt (0-based)
	 * @param int $base_delay Base delay in seconds
	 * @param int $max_delay Maximum delay in seconds
	 * @param WP_Error|null $error Optional error object that may contain retry-after header
	 * @return int Delay in seconds
	 */
	public static function calculate_backoff_delay( $retry_count, $base_delay = 1, $max_delay = 60, $error = null ) {
		$server_hint_delay = 0;

		// Calculate exponential backoff with jitter
		$exponential_delay = $base_delay * pow( 2, $retry_count );
		// Add jitter (0-25% of delay)
		$jitter = $exponential_delay * ( wp_rand( 0, 25 ) / 100 );
		$calculated_delay = $exponential_delay + $jitter;

		// Check for Retry-After header in error data (highest priority)
		if ( is_wp_error( $error ) ) {
			$error_data = $error->get_error_data();
			if ( ! empty( $error_data['retry_after'] ) ) {
				// Retry-After can be seconds or HTTP date
				$retry_after = $error_data['retry_after'];
				if ( is_numeric( $retry_after ) ) {
					// It's seconds
					$server_hint_delay = intval( $retry_after );
				} else {
					// It's an HTTP date, parse it
					$retry_time = strtotime( $retry_after );
					if ( $retry_time !== false ) {
						$server_hint_delay = max( 0, $retry_time - time() );
					}
				}
			}
		}

		// Check for X-RateLimit-Reset from transient (second priority)
		if ( $server_hint_delay === 0 ) {
			$rate_limit_info = get_transient( 'epkb_openai_rate_limit' );
			if ( ! empty( $rate_limit_info['reset_in'] ) && $rate_limit_info['remaining'] === 0 ) {
				// Use actual reset time from OpenAI headers
				$server_hint_delay = $rate_limit_info['reset_in'] + 1;
			}
		}

		// Use the maximum of calculated backoff and server hint
		$delay = max( $calculated_delay, $server_hint_delay );

		// Cap at max delay
		return min( $delay, $max_delay );
	}

	/**
	 * Check if API error is retryable
	 *
	 * This determines whether the server should retry a failed API request.
	 * Note: This is different from EPKB_AI_Log::is_retryable_error() which determines
	 * if the client should retry a request to our REST API.
	 *
	 * @param WP_Error $error
	 * @return bool
	 */
	public static function is_retryable_error( $error ) {
		if ( ! is_wp_error( $error ) ) {
			return false;
		}

		$error_code = $error->get_error_code();
		$error_data = $error->get_error_data();
		$http_code = null;

		// Extract HTTP status code if available
		if ( isset( $error_data['response']['code'] ) ) {
			$http_code = $error_data['response']['code'];
		}

		// Use centralized logic from EPKB_AI_Log for consistency
		$is_retryable = EPKB_AI_Log::is_retryable_error( $error_code, $http_code );

		// - Don't retry 401/403 auth errors (API key issues)
		if ( $http_code === 401 || $http_code === 403 ) {
			return false;
		}

		if ( $error_code === 'json_encode_error' ) {
			return false;
		}

		// - Don't retry if execution_time_too_low (won't be fixed by retrying)
		if ( $error_code === 'execution_time_too_low' ) {
			return false;
		}

		// - Don't retry insufficient_quota errors (billing issues) or invalid API key
		if ( $error_code === 'insufficient_quota' || $error_code === 'invalid_api_key' ) {
			return false;
		}

		// - Don't retry incomplete responses (won't be fixed by retrying)
		if ( $error_code === 'response_incomplete' ) {
			return false;
		}

		return $is_retryable;
	}

	/**
	 * Convert kb_article file references to markdown links with article titles
	 * This converts the file names we generate when uploading to AI storage back to readable links
	 *
	 * @param string $content Content that may contain kb_article references
	 * @return string Content with references converted to links
	 */
	public static function convert_kb_article_references_to_links( $content ) {

		// Remove the †turn0file2 pattern and similar artifacts only when kb_article_ is found
		// Pattern matches optional † followed by turn, number, file, number
		$content = preg_replace('/†?turn\d+file\d+/u', '', $content);

		// Quick check - if content doesn't contain kb_article_, no need to process
		if ( strpos( $content, 'kb_article_' ) === false ) {
			return $content;
		}

		// Pattern: kb_article_[postId]_[timestamp].txt
		// This matches the format we use in upload_file() method
		$pattern = '/kb_article_(\d+)_\d+\.txt/';

		// Find all matches
		if ( preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER ) ) {
			foreach ( $matches as $match ) {
				$file_reference = $match[0];
				$post_id = $match[1];

				// Get the post to retrieve its title
				$post = get_post( $post_id );
				if ( $post ) {
					// Get the article URL
					$article_url = get_permalink( $post_id );
					// Get the article title
					$article_title = get_the_title( $post );

					// Create HTML link that opens in new tab
					// Using HTML directly since marked.js passes through HTML
					$link = '<a href="' . esc_url( $article_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $article_title ) . '</a>';

					// Replace the file reference with the link
					$content = str_replace( $file_reference, $link, $content );
				}
			}
		}

		return $content;
	}

	/**
	 * Extract source references from ChatGPT Responses API file citations.
	 *
	 * OpenAI file search returns file_citation annotations on output_text items.
	 * This maps those file IDs back to training data records so the frontend can
	 * render the same title/url source list used by Gemini.
	 *
	 * @param array $response Raw API response from ChatGPT/OpenAI
	 * @return array Array of source articles with post_id, title, url
	 */
	public static function extract_sources_from_chatgpt_annotations( $response ) {
		$sources = array();
		$seen_file_ids = array();

		if ( empty( $response['output'] ) || ! is_array( $response['output'] ) ) {
			return $sources;
		}

		$training_data_db = new EPKB_AI_Training_Data_DB();

		foreach ( $response['output'] as $output_item ) {
			if ( empty( $output_item['content'] ) || ! is_array( $output_item['content'] ) ) {
				continue;
			}

			foreach ( $output_item['content'] as $content_item ) {
				$annotations = array();

				if ( ! empty( $content_item['annotations'] ) && is_array( $content_item['annotations'] ) ) {
					$annotations = $content_item['annotations'];
				} elseif ( ! empty( $content_item['text']['annotations'] ) && is_array( $content_item['text']['annotations'] ) ) {
					$annotations = $content_item['text']['annotations'];
				}

				if ( empty( $annotations ) ) {
					continue;
				}

				foreach ( $annotations as $annotation ) {
					if ( empty( $annotation['type'] ) || $annotation['type'] !== 'file_citation' || empty( $annotation['file_id'] ) ) {
						continue;
					}

					$file_id = sanitize_text_field( (string) $annotation['file_id'] );
					if ( isset( $seen_file_ids[ $file_id ] ) ) {
						continue;
					}
					$seen_file_ids[ $file_id ] = true;

					$record = $training_data_db->get_training_data_by_file_id_only( $file_id, EPKB_AI_Provider::PROVIDER_CHATGPT );
					if ( ! $record ) {
						continue;
					}

					$source = self::build_source_reference_from_training_record( $record );
					if ( ! empty( $source ) ) {
						$sources[] = $source;
					}
				}
			}
		}

		return $sources;
	}

	/**
	 * Extract source article references from Gemini grounding metadata
	 *
	 * Gemini File Search returns groundingMetadata with groundingChunks that contain
	 * references to the documents used to generate the response. This extracts those
	 * references and converts them to article links.
	 *
	 * @param array $response Raw API response from Gemini
	 * @return array Array of source articles with post_id, title, url
	 */
	public static function extract_sources_from_grounding_metadata( $response ) {
		$sources = array();
		$seen_ids = array();

		// Check for Gemini grounding metadata
		if ( empty( $response['candidates'][0]['groundingMetadata']['groundingChunks'] ) ) {
			return $sources;
		}

		foreach ( $response['candidates'][0]['groundingMetadata']['groundingChunks'] as $chunk ) {
			// File Search uses 'retrievedContext', Google Search uses 'web'
			$context = isset( $chunk['retrievedContext'] ) ? $chunk['retrievedContext'] : null;
			if ( ! $context || empty( $context['title'] ) ) {
				continue;
			}

			// Parse type and ID from file title: kb_{type}_{id}_{timestamp}
			// Matches: kb_article_{postId}_{ts}, kb_page_{postId}_{ts}, kb_post_{postId}_{ts},
			//          kb_pdf_{uuid}_{ts}, kb_aipro_ai_note_{postId}_{ts}
			if ( ! preg_match( '/kb_([a-z_]+)_([a-z0-9-]+)_\d+/', $context['title'], $matches ) ) {
				continue;
			}

			$type = $matches[1];
			$id   = $matches[2];

			// Skip duplicates
			if ( isset( $seen_ids[ $type . '_' . $id ] ) ) {
				continue;
			}
			$seen_ids[ $type . '_' . $id ] = true;

			// Handle PDFs — look up training data DB by UUID
			if ( $type === 'pdf' ) {
				$training_data_db = new EPKB_AI_Training_Data_DB();
				$record = $training_data_db->get_training_data_by_item_id_only( $id );
				if ( ! $record ) {
					continue;
				}

				$sources[] = array(
					'post_id' => 0,
					'title'   => $record->title,
					'url'     => ! empty( $record->url ) ? $record->url : ''
				);
				continue;
			}

			// Handle articles, pages, posts, and notes — use get_post()
			if ( ! is_numeric( $id ) ) {
				continue;
			}

			$post_id = intval( $id );
			$post = get_post( $post_id );
			if ( ! $post ) {
				continue;
			}

			// Notes use private status; articles/pages must be published
			$is_note = ( $type === 'aipro_ai_note' );
			if ( ! $is_note && $post->post_status !== 'publish' ) {
				continue;
			}

			$sources[] = array(
				'post_id' => $post_id,
				'title'   => self::get_source_title_for_post( $post ),
				'url'     => $is_note ? '' : get_permalink( $post_id )
			);
			}

		return $sources;
	}

	/**
	 * Get the source label for a post without WordPress private/protected prefixes.
	 *
	 * @param WP_Post $post
	 * @return string
	 */
	private static function get_source_title_for_post( $post ) {
		if ( empty( $post ) || empty( $post->ID ) ) {
			return '';
		}

		$title = isset( $post->post_title ) ? (string) $post->post_title : '';

		return apply_filters( 'the_title', $title, $post->ID );
	}

	/**
	 * Convert a training data record into a frontend source reference.
	 *
	 * @param object $record Training data DB record
	 * @return array Source reference with post_id, title, url or empty array if unavailable
	 */
	private static function build_source_reference_from_training_record( $record ) {
		if ( empty( $record ) ) {
			return array();
		}

		if ( ! empty( $record->item_id ) && is_numeric( $record->item_id ) ) {
			$post_id = intval( $record->item_id );
			$post = get_post( $post_id );
			if ( ! $post ) {
				return array();
			}

			$is_note = $post->post_type === self::AI_PRO_NOTES_POST_TYPE;
			if ( ! $is_note && $post->post_status !== 'publish' ) {
				return array();
			}

			return array(
				'post_id' => $post_id,
				'title'   => self::get_source_title_for_post( $post ),
				'url'     => $is_note ? '' : get_permalink( $post_id )
			);
		}

		if ( empty( $record->title ) ) {
			return array();
		}

		return array(
			'post_id' => 0,
			'title'   => $record->title,
			'url'     => ! empty( $record->url ) ? $record->url : ''
		);
	}

	/**
	 * Get timeout for a specific purpose with execution time safety check
	 *
	 * @param string $purpose Purpose of the request (e.g., 'content_analysis', 'chat', 'search', 'general')
	 * @return int Timeout in seconds
	 */
	public static function get_timeout_for_purpose( $purpose ) {

		// Determine ideal timeout based on purpose
		$ideal_timeout = self::DEFAULT_TIMEOUT;
		switch ( $purpose ) {
			case 'content_analysis_gap_analysis':
			case 'content_analysis_tag_suggestions':
			case 'content_analysis':
				$ideal_timeout = 120;
				break;
			case 'file_upload':
			case 'pdf_extraction':
			case 'pdf_import_structure':
				$ideal_timeout = 600;
				break;
			case 'chat':
			case 'search':
			case 'general':
			default:
				break;
		}

		// Ensure execution time is sufficient and get safe timeout
		$safe_timeout = self::ensure_execution_time( $ideal_timeout, array( 'purpose' => $purpose ) );

		return $safe_timeout;
	}

	/**
	 * Ensure sufficient PHP execution time for long-running API calls
	 * Attempts to set execution time to desired limit and validates it meets minimum requirements
	 *
	 * @param int $desired_limit Desired execution time limit in seconds (default: 120)
	 * @param array $context Optional context for logging (e.g., article_id, analysis_type)
	 * @return int Safe timeout in seconds, WP_Error if too low
	 */
	private static function ensure_execution_time( $desired_limit = 120, $context = array() ) {

		$minimum_limit = self::DEFAULT_MINIMUM_EXECUTION_TIME_LIMIT;  // seconds
		$current_limit = ini_get( 'max_execution_time' );
		if ( $current_limit == 0 ) {
			return $desired_limit - 10;
		}

		// Check if current limit is too low
		if ( $current_limit < $minimum_limit ) {

			// Try to increase it
			@set_time_limit( $desired_limit );
			$new_limit = ini_get( 'max_execution_time' );

			// Check if we succeeded
			/* if ( $new_limit < $minimum_limit && $new_limit != 0 ) {
				EPKB_AI_Log::add_log( 'PHP execution time limit is too low for AI operations', array_merge( array(
					'current_limit' => $new_limit,
					'minimum_required' => $minimum_limit,
					'desired_limit' => $desired_limit,
					'set_time_limit_failed' => true
				), $context ) );

				return new WP_Error( 'execution_time_too_low',
					sprintf( __( 'PHP execution time limit is too low (%d seconds). Minimum required: %d seconds. Please increase max_execution_time in php.ini or wp-config.php.', 'echo-knowledge-base' ),
						$new_limit, $minimum_limit
					),
					array(
						'current_limit' => $new_limit,
						'minimum_required' => $minimum_limit,
						'desired_limit' => $desired_limit
					)
				);
			} */

			$final_limit = $new_limit;

		} else if ( $current_limit < $desired_limit ) {

			// Current limit is sufficient - try to increase to desired if possible
			@set_time_limit( $desired_limit );
			$new_limit = ini_get( 'max_execution_time' );
			$final_limit = $new_limit;

		} else {
			// Current limit is already >= desired
			$final_limit = $current_limit;
		}

		// Calculate safe HTTP timeout (10 seconds less than execution limit)
		$safe_timeout = $final_limit == 0 ? ( $desired_limit - 10 ) : max( 10, $final_limit - 10 );

		return $safe_timeout;
	}

}
