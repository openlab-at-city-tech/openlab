<?php defined( 'ABSPATH' ) || exit();

class EPKB_AI_Utilities {

	const AI_PRO_NOTES_POST_TYPE = 'aipro_ai_note';

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
			$entropy .= mt_rand();                            // Mersenne Twister
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

	public static function is_ai_search_advanced_enabled( $skip_preview_check = false ) {
		return self::is_ai_search_enabled_for_frontend( $skip_preview_check ) && EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_mode' ) === 'advanced_search';
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
		$ai_key = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_key', '' );
		$disclaimer_accepted = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_disclaimer_accepted', 'off' );

		return ! empty( $ai_key ) && $disclaimer_accepted === 'on';
	}

	/**
	 * Check if AI Features Pro is enabled
	 *
	 * @return bool True if AI Features Pro is enabled
	 */
	public static function is_ai_features_pro_enabled() {
		return defined( 'AI_FEATURES_PRO_PLUGIN_NAME' );
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
				$errors[] = sprintf( __( 'Failed to send email to %s: %s', 'echo-knowledge-base' ), $email_id, $email_error );
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
			$kb_name = isset( $kb_config['kb_name'] ) ? $kb_config['kb_name'] : sprintf( __( 'Knowledge Base %d', 'echo-knowledge-base' ), $kb_id );
			$post_types[ $kb_post_type ] = $kb_name;
			if ( ! $show_all_kbs ) { break; }
		}

		// FAQ post type removed from AI training content options
		// if ( class_exists( 'EPKB_FAQs_CPT_Setup' ) && post_type_exists( EPKB_FAQs_CPT_Setup::FAQS_POST_TYPE ) ) {
		// 	$post_types[ EPKB_FAQs_CPT_Setup::FAQS_POST_TYPE ] = __( 'FAQs', 'echo-knowledge-base' );
		// }

		/**
		 * Add the list of available post types for AI training data.
		 */
		$post_types = apply_filters( 'epkb_ai_training_available_post_types', $post_types );
		
		return $post_types;
	}

	/**
	 * Get AI Training Data Collection options for dropdowns/selects
	 * Returns an array of collection_id => collection_name pairs
	 *
	 * @param string $format Optional format: 'simple' (default) returns key => value, 'block' returns array with key/name/style
	 * @return array Collection options in the requested format
	 */
	public static function get_collection_options( $format = 'simple' ) {
		$options = array();

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

		// If no collections exist, add a default option
		if ( empty( $options ) ) {
			$default_name = esc_html__( 'Default Collection', 'echo-knowledge-base' );
			if ( $format === 'block' ) {
				$options[] = array(
					'key' => 1,
					'name' => $default_name,
					'style' => array(),
				);
			} else {
				$options[1] = $default_name;
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
	 * Determine if an AI search answer is the configured refusal message.
	 *
	 * @param string $answer
	 * @return bool
	 */
	public static function is_search_refusal_answer( $answer ) {
		if ( empty( $answer ) ) {
			return false;
		}

		$normalized = wp_strip_all_tags( $answer );
		return stripos( $normalized, EPKB_AI_Config_Specs::get_ai_refusal_message() ) !== false;
	}
}
