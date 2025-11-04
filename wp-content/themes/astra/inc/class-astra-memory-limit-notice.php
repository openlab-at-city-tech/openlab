<?php
/**
 * PHP Memory Limit Warning Notice
 *
 * @package Astra
 * @since 4.11.12
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'DAY_IN_SECONDS' ) ) {
	define( 'DAY_IN_SECONDS', 24 * 60 * 60 );
}

/**
 * Class Astra_Memory_Limit_Notice
 */
class Astra_Memory_Limit_Notice {
	/**
	 * Memory usage percentage threshold to show warning notice
	 * Change this value to adjust when the warning notice appears
	 * Default: 90 (shows when 90% memory is used, 10% remaining)
	 */
	private $warning_threshold = 90;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'add_memory_notice' ) );
		// Added Site Health integration
		add_filter( 'site_status_tests', array( $this, 'add_site_health_tests' ) );
	}

	/**
	 * Add memory notice using Astra_Notices system
	 */
	public function add_memory_notice() {
		$notice_data = $this->get_memory_notice_data();

		// Return if notice is not required.
		if ( ! isset( $notice_data['show_notice'] ) || ! $notice_data['show_notice'] ) {
			return;
		}

		// Add notice using Astra_Notices system.
		$this->display_memory_notice( $notice_data );
	}

	/**
	 * Get memory notice data including whether to show notice and notice type
	 *
	 * @return array Array with memory data and notice information.
	 */
	private function get_memory_notice_data() {
		// Only show to administrators.
		if ( ! current_user_can( 'manage_options' ) ) {
			return array(
				'show_notice' => false,
				'notice_type' => '',
			);
		}

		$memory_limit      = $this->get_memory_limit_in_bytes();
		$memory_usage      = memory_get_peak_usage( true );
		$memory_percentage = $memory_limit > 0 ? ( $memory_usage / $memory_limit * 100 ) : 0;

		// Prepare base return data to avoid repetition.
		$return_data = array(
			'memory_limit'      => $memory_limit,
			'memory_usage'      => $memory_usage,
			'memory_percentage' => $memory_percentage,
		);

		// Show warning notice at threshold usage.
		if ( $memory_percentage >= $this->warning_threshold ) {
			return array_merge(
				$return_data,
				array(
					'show_notice' => true,
					'notice_type' => 'warning',
				)
			);
		}

		return array(
			'show_notice' => false,
			'notice_type' => '',
		);
	}

	/**
	 * Get recommended memory limit based on current limit
	 *
	 * @param int $current_limit_bytes Current memory limit in bytes.
	 * @return string Recommended memory limit string.
	 */
	private function get_recommended_memory_limit( $current_limit_bytes ) {
		$current_limit_mb = $current_limit_bytes / ( 1024 * 1024 );

		if ( $current_limit_mb < 512 ) {
			return '512M';
		}

		if ( $current_limit_mb < 1024 ) {
			return '1G';
		}

		return '2G';
	}

	/**
	 * Get PHP memory limit in bytes
	 */
	private function get_memory_limit_in_bytes() {
		$memory_limit = ini_get( 'memory_limit' );

		if ( $memory_limit == -1 ) {
			return PHP_INT_MAX; // Unlimited
		}

		// Handle edge cases where ini_get returns false or empty
		if ( false === $memory_limit || empty( $memory_limit ) ) {
			return 134217728;
		}

		$unit  = strtolower( substr( $memory_limit, -1 ) );
		$value = (int) $memory_limit;

		if ( $value <= 0 ) {
			return 134217728;
		}

		switch ( $unit ) {
			case 'g':
				$value *= 1024 * 1024 * 1024;
				break;
			case 'm':
				$value *= 1024 * 1024;
				break;
			case 'k':
				$value *= 1024;
				break;
		}

		return $value;
	}

	/**
	 * Register memory notice with Astra_Notices system
	 *
	 * @param array $notice_data Array containing memory data and notice type.
	 */
	private function display_memory_notice( $notice_data ) {
		$limit             = isset( $notice_data['memory_limit'] ) ? $notice_data['memory_limit'] : 0;
		$recommended_limit = $this->get_recommended_memory_limit( $limit );

		$message  = '<div>';
		$message .= '<p><strong>' . esc_html__( '🔔 PHP Memory Limit Notice', 'astra' ) . '</strong></p>';
		$message .= '<p>' . esc_html__( 'Your site is nearing its PHP memory limit, which may affect stability.', 'astra' ) . '</p>';
		$message .= '<p>' . sprintf(
			esc_html__( 'We recommend increasing it to at least %s for best performance.', 'astra' ),
			'<strong>' . esc_html( $recommended_limit ) . '</strong>'
		) . '</p>';
		$message .= '<p><a href="https://wpastra.com/docs/system-requirement-for-astra-theme/" target="_blank" rel="noopener">';
		$message .= esc_html__( 'Learn how to increase your PHP memory', 'astra' ) . '</a></p>';
		$message .= '</div>';

		$notice_args = array(
			'id'                  => 'astra-memory-limit-warning',
			'type'                => 'warning',
			'message'             => $message,
			'show_if'             => true,
			'repeat-notice-after' => false, // Don't repeat notice after a certain time.
			'is_dismissible'      => true,
			'capability'          => 'manage_options',
			'class'               => 'astra-memory-notice',
		);

		Astra_Notices::add_notice( $notice_args );
	}

	/**
	 * Format bytes to human readable format
	 *
	 * @param int $bytes Number of bytes to format.
	 * @return string Formatted bytes string.
	 */
	private function format_bytes( $bytes ) {
		$units = array( 'B', 'KB', 'MB', 'GB', 'TB' );
		$total = count( $units );

		for ( $i = 0; $bytes > 1024 && $i < $total - 1; $i++ ) {
			$bytes /= 1024;
		}

		return round( $bytes, 2 ) . ' ' . $units[ $i ];
	}

	/**
	 * Added Site Health tests for memory usage
	 *
	 * @param array $tests Existing Site Health tests.
	 * @return array Modified tests array.
	 */
	public function add_site_health_tests( $tests ) {
		$tests['direct']['astra_memory_usage'] = array(
			'label' => __( 'Astra Theme Memory Usage', 'astra' ),
			'test'  => array( $this, 'site_health_memory_test' ),
		);

		return $tests;
	}

	/**
	 * Site Health test for memory usage
	 *
	 * @return array Site Health test result.
	 */
	public function site_health_memory_test() {
		$memory_limit      = $this->get_memory_limit_in_bytes();
		$memory_usage      = memory_get_peak_usage( true );
		$memory_percentage = $memory_limit > 0 ? ( $memory_usage / $memory_limit * 100 ) : 0;

		$memory_limit_formatted     = $this->format_bytes( $memory_limit );
		$memory_usage_formatted     = $this->format_bytes( $memory_usage );
		$memory_remaining_formatted = $this->format_bytes( $memory_limit - $memory_usage );
		$percentage_formatted       = number_format( $memory_percentage, 1 );

		if ( $memory_percentage >= $this->warning_threshold ) {
			$status      = 'recommended';
			$label       = __( 'High memory usage detected', 'astra' );
			$badge_color = 'orange';
		} else {
			$status      = 'good';
			$label       = __( 'Memory usage is within acceptable limits', 'astra' );
			$badge_color = 'green';
		}

		$description = $this->get_site_health_description( $memory_percentage, $memory_limit_formatted, $memory_usage_formatted, $memory_remaining_formatted, $percentage_formatted );

		$actions = $this->get_site_health_actions( $memory_percentage );

		return array(
			'label'       => $label,
			'status'      => $status,
			'badge'       => array(
				'label' => __( 'Astra Theme', 'astra' ),
				'color' => $badge_color,
			),
			'description' => $description,
			'actions'     => $actions,
			'test'        => 'astra_memory_usage',
		);
	}

	/**
	 * Get Site Health description based on memory usage
	 *
	 * @param float  $memory_percentage Memory usage percentage.
	 * @param string $memory_limit_formatted Formatted memory limit.
	 * @param string $memory_usage_formatted Formatted memory usage.
	 * @param string $memory_remaining_formatted Formatted remaining memory.
	 * @param string $percentage_formatted Formatted percentage.
	 * @return string Description HTML.
	 */
	private function get_site_health_description( $memory_percentage, $memory_limit_formatted, $memory_usage_formatted, $memory_remaining_formatted, $percentage_formatted ) {
		$description = '<p>';

		if ( $memory_percentage >= $this->warning_threshold ) {
			$description .= sprintf(
				__( 'Your site is using %1$s of %2$s available PHP memory (%3$s%%). Only %4$s remaining.', 'astra' ),
				'<strong>' . esc_html( $memory_usage_formatted ) . '</strong>',
				'<strong>' . esc_html( $memory_limit_formatted ) . '</strong>',
				'<strong>' . esc_html( $percentage_formatted ) . '</strong>',
				'<strong>' . esc_html( $memory_remaining_formatted ) . '</strong>'
			);
			$description .= '</p><p>';
			$description .= __( 'While your site is currently functioning, this high memory usage puts you at risk of crashes and errors, especially when using memory-intensive features like the customizer, page builders, or plugins.', 'astra' );
		} else {
			$description .= sprintf(
				__( 'Your site is using %1$s of %2$s available PHP memory (%3$s%%). You have %4$s remaining.', 'astra' ),
				'<strong>' . esc_html( $memory_usage_formatted ) . '</strong>',
				'<strong>' . esc_html( $memory_limit_formatted ) . '</strong>',
				'<strong>' . esc_html( $percentage_formatted ) . '</strong>',
				'<strong>' . esc_html( $memory_remaining_formatted ) . '</strong>'
			);
			$description .= '</p><p>';
			$description .= __( 'Your memory usage is within acceptable limits. The Astra theme and your plugins have sufficient memory to operate properly.', 'astra' );
		}

		$description .= '</p>';

		$description .= '<p>';
		$description .= __( '<strong>About PHP Memory:</strong> PHP memory limit determines how much memory your website can use. Themes, plugins, and WordPress core all consume memory. When the limit is reached, your site may display errors or stop working.', 'astra' );
		$description .= '</p>';

		return $description;
	}

	/**
	 * Get Site Health actions based on memory usage
	 *
	 * @param float $memory_percentage Memory usage percentage.
	 * @return string Actions HTML.
	 */
	private function get_site_health_actions( $memory_percentage ) {
		$actions = '';

		if ( $memory_percentage >= $this->warning_threshold ) {
			$current_limit     = $this->get_memory_limit_in_bytes();
			$recommended_limit = $this->get_recommended_memory_limit( $current_limit );

			$actions .= '<h4>' . __( 'How to increase PHP memory limit:', 'astra' ) . '</h4>';
			$actions .= '<ol>';
			$actions .= '<li><strong>' . __( 'Contact your hosting provider', 'astra' ) . '</strong><br>';
			$actions .= sprintf( __( 'The easiest solution is to ask your hosting provider to increase your PHP memory limit to at least %s.', 'astra' ), $recommended_limit ) . '</li>';

			$actions .= '<li><strong>' . __( 'Edit wp-config.php file', 'astra' ) . '</strong><br>';
			$actions .= __( 'Add this line to your wp-config.php file (before the "/* That\'s all, stop editing!" line):', 'astra' ) . '<br>';
			$actions .= '<code style="background: #f1f1f1; padding: 4px 8px; border-radius: 3px; font-family: monospace; color: #d63638;">define(\'WP_MEMORY_LIMIT\', \'' . esc_html( $recommended_limit ) . '\');</code></li>';

			$actions .= '<li><strong>' . __( 'Edit .htaccess file', 'astra' ) . '</strong><br>';
			$actions .= __( 'Add this line to your .htaccess file:', 'astra' ) . '<br>';
			$actions .= '<code style="background: #f1f1f1; padding: 4px 8px; border-radius: 3px; font-family: monospace; color: #d63638;">php_value memory_limit ' . esc_html( $recommended_limit ) . '</code></li>';

			$actions .= '<li><strong>' . __( 'Edit php.ini file', 'astra' ) . '</strong><br>';
			$actions .= __( 'If you have access to php.ini, change this line:', 'astra' ) . '<br>';
			$actions .= '<code style="background: #f1f1f1; padding: 4px 8px; border-radius: 3px; font-family: monospace; color: #d63638;">memory_limit = ' . esc_html( $recommended_limit ) . '</code></li>';
			$actions .= '</ol>';

			$actions .= '<p><strong>' . __( 'Recommended memory limits:', 'astra' ) . '</strong></p>';
			$actions .= '<ul>';
			$actions .= '<li>' . __( 'Basic WordPress site: 256MB', 'astra' ) . '</li>';
			$actions .= '<li>' . __( 'Site with multiple plugins: 512MB', 'astra' ) . '</li>';
			$actions .= '<li>' . __( 'WooCommerce or complex site: 1GB or higher', 'astra' ) . '</li>';
			$actions .= '</ul>';

			$actions .= '<p>';
			$actions .= sprintf(
				__( 'For detailed instructions, visit our <a href="%s" target="_blank" rel="noopener">documentation on increasing PHP memory limit</a>.', 'astra' ),
				'https://wpastra.com/docs/system-requirement-for-astra-theme/'
			);
			$actions .= '</p>';
		} else {
			$actions .= '<p>' . __( 'No action required. Your memory usage is within acceptable limits.', 'astra' ) . '</p>';
			$actions .= '<p>' . __( 'Continue monitoring your memory usage, especially when installing new plugins or themes.', 'astra' ) . '</p>';
		}

		return $actions;
	}
}

// Initialize the memory notice class
new Astra_Memory_Limit_Notice();
