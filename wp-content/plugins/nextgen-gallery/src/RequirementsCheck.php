<?php
/**
 * Requirements check.
 *
 * Decides whether the rest of NextGEN Gallery may load. Below the minimum PHP version the plugin
 * stays active but loads nothing, and the Plugins screen explains why under the plugin row. Between
 * the minimum and the upcoming minimum the plugin runs normally and shows a dismissible admin notice
 * announcing the coming requirement, so hosts can be upgraded before the release that enforces it.
 *
 * This file is loaded before anything else in the plugin, on any PHP version WordPress itself
 * still runs on, so it must only use syntax that PHP 5.6 can parse: no short closures, no
 * nullable or return types, no null coalescing, no typed properties. Do not "modernise" it.
 *
 * @package Nextgen Gallery
 * @since   4.6.0
 */

namespace Imagely\NGG;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Checks the environment against the plugin's minimum requirements.
 *
 * Modelled on Easy Digital Downloads' `EDD\RequirementsCheck`, plus the pre-announcement notice.
 *
 * @since 4.6.0
 */
class RequirementsCheck {

	/**
	 * Minimum PHP version the plugin loads on. Below it the plugin is held back.
	 *
	 * @since 4.6.0
	 *
	 * @var string
	 */
	const MINIMUM_PHP = '7.4';

	/**
	 * PHP version the plugin will require starting with its next release. Sites between
	 * MINIMUM_PHP and this version run the plugin and see the announcement notice. The next release
	 * moves this value into MINIMUM_PHP.
	 *
	 * @since 4.6.0
	 *
	 * @var string
	 */
	const UPCOMING_PHP = '8.1';

	/**
	 * Main plugin file.
	 *
	 * @since 4.6.0
	 *
	 * @var string
	 */
	private $file = '';

	/**
	 * Plugin basename, e.g. `nextgen-gallery/nggallery.php`.
	 *
	 * @since 4.6.0
	 *
	 * @var string
	 */
	private $base = '';

	/**
	 * Plugin name shown in the notices, e.g. `NextGEN Gallery`.
	 *
	 * @since 4.6.0
	 *
	 * @var string
	 */
	private $name = '';

	/**
	 * Requirements to check. Add `wp` here when a WordPress minimum should be enforced too.
	 *
	 * @since 4.6.0
	 *
	 * @var array
	 */
	private $requirements = array(
		'php' => array(
			'minimum' => self::MINIMUM_PHP,
			'name'    => 'PHP',
			'exists'  => true,
			'current' => false,
			'checked' => false,
			'met'     => false,
		),
	);

	/**
	 * Set up the check for a plugin file.
	 *
	 * @since 4.6.0
	 *
	 * @param string $file Main plugin file (`__FILE__` of the bootstrap).
	 * @param string $name Plugin name as in the plugin header.
	 */
	public function __construct( $file, $name = 'NextGEN Gallery' ) {
		$this->file = $file;
		$this->base = plugin_basename( $file );
		$this->name = $name;
	}

	/**
	 * Stop the plugin from loading and explain why on the Plugins screen.
	 *
	 * Call when {@see met()} returns false. Registers the red row under the plugin, the
	 * "Requirements" row action and the row styles. Nothing else of the plugin runs.
	 *
	 * @since 4.6.0
	 *
	 * @return void
	 */
	public function quit() {
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_filter( 'plugin_action_links_' . $this->base, array( $this, 'plugin_row_links' ) );
		add_action( 'after_plugin_row_' . $this->base, array( $this, 'plugin_row_notice' ) );
	}

	/**
	 * Announce the upcoming PHP requirement when this site does not meet it yet.
	 *
	 * Call when {@see met()} returned true. Below UPCOMING_PHP it registers a dismissible admin notice
	 * for users who can manage plugins; at or above it, nothing happens.
	 *
	 * @since 4.6.0
	 *
	 * @return void
	 */
	public function announce() {
		if ( ! $this->upcoming_unmet() ) {
			return;
		}

		add_action( 'admin_init', array( $this, 'maybe_dismiss_upcoming_notice' ) );
		add_action( 'admin_notices', array( $this, 'upcoming_notice' ) );
		add_action( 'network_admin_notices', array( $this, 'upcoming_notice' ) );
	}

	/**
	 * Have all requirements been met?
	 *
	 * @since 4.6.0
	 *
	 * @return bool
	 */
	public function met() {
		$this->check();

		foreach ( $this->requirements as $properties ) {
			if ( empty( $properties['met'] ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Does this site run PHP below the version the next release will require?
	 *
	 * @since 4.6.0
	 *
	 * @return bool
	 */
	public function upcoming_unmet() {
		$version = $this->current_version( 'php' );

		return ! empty( $version ) && version_compare( $version, self::UPCOMING_PHP, '<' );
	}

	/**
	 * Requirements with their current version and result, after {@see met()} ran.
	 *
	 * @since 4.6.0
	 *
	 * @return array
	 */
	public function get_requirements() {
		return $this->requirements;
	}

	/**
	 * Compare every requirement against what the site runs.
	 *
	 * @since 4.6.0
	 *
	 * @return void
	 */
	private function check() {
		foreach ( $this->requirements as $dependency => $properties ) {
			$version = $this->current_version( $dependency );

			if ( empty( $version ) ) {
				continue;
			}

			$this->requirements[ $dependency ] = array_merge(
				$properties,
				array(
					'current' => $version,
					'checked' => true,
					'met'     => version_compare( $version, $properties['minimum'], '>=' ),
				)
			);
		}
	}

	/**
	 * Version of a dependency on this site. Overridable so tests can simulate an old PHP.
	 *
	 * @since 4.6.0
	 *
	 * @param string $dependency Requirement key, e.g. `php`.
	 * @return string|false Version string, or false when unknown.
	 */
	protected function current_version( $dependency ) {
		switch ( $dependency ) {
			case 'php':
				return phpversion();
			case 'wp':
				return get_bloginfo( 'version' );
			default:
				return false;
		}
	}

	/** Upcoming-requirement notice **********************************************************/

	/**
	 * Query argument and nonce action of the notice's Dismiss link.
	 *
	 * @since 4.6.0
	 *
	 * @return string
	 */
	private function dismiss_arg() {
		return $this->unmet_requirements_name() . '-dismiss';
	}

	/**
	 * User meta key recording that this user dismissed the notice for this upcoming version.
	 *
	 * @since 4.6.0
	 *
	 * @return string
	 */
	private function dismiss_meta_key() {
		return str_replace( '-', '_', $this->unmet_requirements_name() ) . '_php_' . str_replace( '.', '_', self::UPCOMING_PHP ) . '_dismissed';
	}

	/**
	 * Record the dismissal when the Dismiss link was followed with a valid nonce.
	 *
	 * Runs on admin_init, before admin_notices, so the notice disappears on the same request.
	 *
	 * @since 4.6.0
	 *
	 * @return bool True when a dismissal was recorded.
	 */
	public function maybe_dismiss_upcoming_notice() {
		$arg = $this->dismiss_arg();

		if ( empty( $_GET[ $arg ] ) || empty( $_GET['_wpnonce'] ) ) {
			return false;
		}

		if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), $arg ) || ! current_user_can( 'activate_plugins' ) ) {
			return false;
		}

		update_user_meta( get_current_user_id(), $this->dismiss_meta_key(), time() );

		return true;
	}

	/**
	 * Output the upcoming-requirement notice for users who can manage plugins and have not dismissed it.
	 *
	 * @since 4.6.0
	 *
	 * @return void
	 */
	public function upcoming_notice() {
		if ( ! current_user_can( 'activate_plugins' ) || get_user_meta( get_current_user_id(), $this->dismiss_meta_key(), true ) ) {
			return;
		}

		$current     = $this->current_version( 'php' );
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : admin_url();
		$dismiss_url = wp_nonce_url( add_query_arg( $this->dismiss_arg(), '1', $request_uri ), $this->dismiss_arg() );
		?>
		<div class="notice notice-warning <?php echo esc_attr( $this->unmet_requirements_name() ); ?>-upcoming">
			<p>
				<?php
				echo wp_kses(
					sprintf(
						/* translators: 1: plugin name, 2: upcoming minimum PHP version, 3: installed PHP version */
						__( '<strong>%1$s</strong> will require <strong>PHP %2$s</strong> or newer starting with its next release. This site runs <strong>PHP %3$s</strong>: after that update the plugin will stay installed but inactive until PHP is upgraded. Ask your host to upgrade PHP before updating.', 'nggallery' ),
						esc_html( $this->name ),
						esc_html( self::UPCOMING_PHP ),
						esc_html( $current )
					),
					array( 'strong' => array() )
				);
				?>
				<a href="<?php echo esc_url( $this->unmet_requirements_url() ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Requirements', 'nggallery' ); ?></a>
				|
				<a href="<?php echo esc_url( $dismiss_url ); ?>"><?php esc_html_e( 'Dismiss this notice', 'nggallery' ); ?></a>
			</p>
		</div>
		<?php
	}

	/** Plugins screen output ****************************************************************/

	/**
	 * URL of the page that explains the requirements.
	 *
	 * @since 4.6.0
	 *
	 * @return string
	 */
	private function unmet_requirements_url() {
		return 'https://www.imagely.com/docs/';
	}

	/**
	 * CSS hook shared by the row, the notice and their styles.
	 *
	 * @since 4.6.0
	 *
	 * @return string
	 */
	private function unmet_requirements_name() {
		return 'nextgen-gallery-requirements';
	}

	/**
	 * Add the "Requirements" link to the plugin's row actions.
	 *
	 * @since 4.6.0
	 *
	 * @param array $links Existing row action links.
	 * @return array
	 */
	public function plugin_row_links( $links = array() ) {
		/* translators: %s: plugin name */
		$label = sprintf( __( '%s requirements', 'nggallery' ), $this->name );

		$links['requirements'] = '<a href="' . esc_url( $this->unmet_requirements_url() ) . '" aria-label="' . esc_attr( $label ) . '">'
			. esc_html__( 'Requirements', 'nggallery' )
			. '</a>';

		return $links;
	}

	/**
	 * Output the extra row under the plugin that lists every unmet requirement.
	 *
	 * The checkbox cell is a `td` to match the plugins list table, which moved the row header
	 * from the checkbox column to the primary column in WordPress 7.1.
	 *
	 * @since 4.6.0
	 *
	 * @return void
	 */
	public function plugin_row_notice() {
		// wp_is_auto_update_enabled_for_type() exists since WordPress 5.5.
		$colspan = function_exists( 'wp_is_auto_update_enabled_for_type' ) && wp_is_auto_update_enabled_for_type( 'plugin' ) ? 2 : 1;
		?>
		<tr class="active <?php echo esc_attr( $this->unmet_requirements_name() ); ?>-row">
			<td class="check-column">
				<span class="dashicons dashicons-warning"></span>
			</td>
			<td class="column-primary">
				<?php esc_html_e( 'This plugin is not fully active.', 'nggallery' ); ?>
			</td>
			<td class="column-description" colspan="<?php echo esc_attr( $colspan ); ?>">
				<?php
				foreach ( $this->requirements as $requirement ) {
					if ( empty( $requirement['met'] ) ) {
						$this->unmet_requirement_description( $requirement );
					}
				}
				?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Output one unmet requirement.
	 *
	 * @since 4.6.0
	 *
	 * @param array $requirement Requirement properties after the check ran.
	 * @return void
	 */
	private function unmet_requirement_description( $requirement ) {
		if ( ! empty( $requirement['exists'] ) && ! empty( $requirement['current'] ) ) {
			$text = sprintf(
				/* translators: 1: requirement name, 2: minimum version, 3: installed version */
				esc_html__( 'Requires %1$s (%2$s), but (%3$s) is installed.', 'nggallery' ),
				'<strong>' . esc_html( $requirement['name'] ) . '</strong>',
				'<strong>' . esc_html( $requirement['minimum'] ) . '</strong>',
				'<strong>' . esc_html( $requirement['current'] ) . '</strong>'
			);
		} else {
			$text = sprintf(
				/* translators: 1: requirement name, 2: minimum version */
				esc_html__( 'Requires %1$s (%2$s), but it appears to be missing.', 'nggallery' ),
				'<strong>' . esc_html( $requirement['name'] ) . '</strong>',
				'<strong>' . esc_html( $requirement['minimum'] ) . '</strong>'
			);
		}

		echo '<p>' . $text . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- every piece was escaped above.
	}

	/**
	 * Styles for the unmet-requirements row.
	 *
	 * @since 4.6.0
	 *
	 * @return void
	 */
	public function admin_head() {
		$name = $this->unmet_requirements_name();
		$base = $this->base;
		?>
		<style id="<?php echo esc_attr( $name ); ?>">
			.plugins tr[data-plugin="<?php echo esc_attr( $base ); ?>"] th,
			.plugins tr[data-plugin="<?php echo esc_attr( $base ); ?>"] td,
			.plugins .<?php echo esc_attr( $name ); ?>-row th,
			.plugins .<?php echo esc_attr( $name ); ?>-row td {
				background: #fff5f5;
			}
			.plugins tr[data-plugin="<?php echo esc_attr( $base ); ?>"] .check-column {
				box-shadow: none;
			}
			.plugins .<?php echo esc_attr( $name ); ?>-row .check-column span {
				margin-left: 6px;
				color: #dc3232;
			}
			.plugins tr[data-plugin="<?php echo esc_attr( $base ); ?>"] .check-column,
			.plugins .<?php echo esc_attr( $name ); ?>-row .check-column {
				border-left: 4px solid #dc3232 !important;
			}
			.plugins .<?php echo esc_attr( $name ); ?>-row .column-description p {
				margin: 0;
				padding: 0;
			}
			.plugins .<?php echo esc_attr( $name ); ?>-row .column-description p:not(:last-of-type) {
				margin-bottom: 8px;
			}
		</style>
		<?php
	}
}
