<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable Generic.Arrays.DisallowLongArraySyntax.Found
if ( ! function_exists( 'aioseo_table_of_contents_php_notice' ) ) {
	/**
	 * Display the notice after deactivation.
	 *
	 * @since 1.0.0
	 */
	function aioseo_table_of_contents_php_notice() {
		$medium = 'plugin';
		?>
		<div class="notice notice-error">
			<p>
				<?php
				echo wp_kses(
					sprintf(
						// Translators: 1 - Opening HTML bold tag, 2 - Closing HTML bold tag, 3 - Opening HTML link tag, 4 - Closing HTML link tag.
						__( 'Your site is running an %1$sinsecure version%2$s of PHP that is no longer supported. Please contact your web hosting provider to update your PHP version or switch to a %3$srecommended WordPress hosting company%4$s.', 'table-of-contents-plus' ), // phpcs:ignore Generic.Files.LineLength.MaxExceeded
						'<strong>',
						'</strong>',
						'<a href="https://www.wpbeginner.com/wordpress-hosting/" target="_blank" rel="noopener noreferrer">',
						'</a>'
					),
					array(
						'a'      => array(
							'href'   => array(),
							'target' => array(),
							'rel'    => array(),
						),
						'strong' => array(),
					)
				);
				?>
				<br><br>
				<?php
				echo wp_kses(
					sprintf(
						// Translators: 1 - Opening HTML bold tag, 2 - Closing HTML bold tag, 3 - The short plugin name ("AIOSEO"), 4 - Opening HTML link tag, 5 - Closing HTML link tag.
						__( '%1$sNote:%2$s %3$s plugin is disabled on your site until you fix the issue. %4$sRead more for additional information.%5$s', 'table-of-contents-plus' ),
						'<strong>',
						'</strong>',
						'AIOSEO',
						'<a href="https://aioseo.com/docs/supported-php-version/?utm_source=WordPress&utm_medium=' . $medium . '&utm_campaign=outdated-php-notice" target="_blank" rel="noopener noreferrer">', // phpcs:ignore Generic.Files.LineLength.MaxExceeded
						'</a>'
					),
					array(
						'a'      => array(
							'href'   => array(),
							'target' => array(),
							'rel'    => array(),
						),
						'strong' => array(),
					)
				);
				?>
			</p>
		</div>

		<?php
		// In case this is on plugin activation.
		if ( isset( $_GET['activate'] ) ) { // phpcs:ignore HM.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Recommended	
			unset( $_GET['activate'] );
		}
	}
}

if ( ! function_exists( 'aioseo_table_of_contents_php_notice_deprecated' ) ) {
	/**
	 * Display the notice after deactivation.
	 *
	 * @since 1.0.0
	 */
	function aioseo_table_of_contents_php_notice_deprecated() {
		$medium = 'plugin';
		?>
		<div class="notice notice-error">
			<p>
				<?php
				echo wp_kses(
					sprintf(
						// Translators: 1 - Opening HTML bold tag, 2 - Closing HTML bold tag, 3 - The plugin short name ("AIOSEO"), 4 - Opening HTML link tag, 5 - Closing HTML link tag.
						__( 'Your site is running an %1$soutdated version%2$s of PHP that is no longer supported and may cause issues with %3$s. Please contact your web hosting provider to update your PHP version or switch to a %4$srecommended WordPress hosting company%5$s.', 'table-of-contents-plus' ), // phpcs:ignore Generic.Files.LineLength.MaxExceeded
						'<strong>',
						'</strong>',
						'<strong>AIOSEO</strong>',
						'<a href="https://www.wpbeginner.com/wordpress-hosting/" target="_blank" rel="noopener noreferrer">',
						'</a>'
					),
					array(
						'a'      => array(
							'href'   => array(),
							'target' => array(),
							'rel'    => array(),
						),
						'strong' => array(),
					)
				);
				?>
				<br><br>
				<?php
				echo wp_kses(
					sprintf(
						// phpcs:ignore Generic.Files.LineLength.MaxExceeded
						// Translators: 1 - Opening HTML bold tag, 2 - Closing HTML bold tag, 3 - The PHP version, 4 - The current year, 5 - The short plugin name ("AIOSEO"), 6 - Opening HTML link tag, 7 - Closing HTML link tag.
						__( '%1$sNote:%2$s Support for PHP %3$s will be discontinued in %4$s. After this, if no further action is taken, %5$s functionality will be disabled. %6$sRead more for additional information.%7$s', 'table-of-contents-plus' ), // phpcs:ignore Generic.Files.LineLength.MaxExceeded
						'<strong>',
						'</strong>',
						PHP_VERSION,
						gmdate( 'Y' ),
						'AIOSEO',
						'<a href="https://aioseo.com/docs/supported-php-version/?utm_source=WordPress&utm_medium=' . $medium . '&utm_campaign=outdated-php-notice" target="_blank" rel="noopener noreferrer">', // phpcs:ignore Generic.Files.LineLength.MaxExceeded
						'</a>'
					),
					array(
						'a'      => array(
							'href'   => array(),
							'target' => array(),
							'rel'    => array(),
						),
						'strong' => array(),
					)
				);
				?>
			</p>
		</div>

		<?php
		// In case this is on plugin activation.
		if ( isset( $_GET['activate'] ) ) { // phpcs:ignore HM.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Recommended
			unset( $_GET['activate'] );
		}
	}
}

if ( ! function_exists( 'aioseo_table_of_contents_wordpress_notice' ) ) {
	/**
	 * Display the notice after deactivation.
	 *
	 * @since 1.0.0
	 */
	function aioseo_table_of_contents_wordpress_notice() {
		$medium = 'plugin';
		?>
		<div class="notice notice-error">
			<p>
				<?php
				echo wp_kses(
					sprintf(
						// Translators: 1 - Opening HTML bold tag, 2 - Closing HTML bold tag, 3 - The plugin name ("All in One SEO").
						__( 'Your site is running an %1$sinsecure version%2$s of WordPress that is no longer supported. Please update your site to the latest version of WordPress in order to continue using %3$s.', 'table-of-contents-plus' ), // phpcs:ignore Generic.Files.LineLength.MaxExceeded
						'<strong>',
						'</strong>',
						'All in One SEO'
					),
					array(
						'strong' => array(),
					)
				);
				?>
				<br><br>
				<?php
				echo wp_kses(
					sprintf(
						// phpcs:ignore Generic.Files.LineLength.MaxExceeded
						// Translators: 1 - Opening HTML bold tag, 2 - Closing HTML bold tag, 3 - The short plugin name ("AIOSEO"), 4 - The WordPress version, 5 - The current year, 6 - Opening HTML link tag, 7 - Closing HTML link tag.
						__( '%1$sNote:%2$s %3$s will be discontinuing support for WordPress versions older than version %4$s by the end of %5$s. %6$sRead more for additional information.%7$s', 'table-of-contents-plus' ), // phpcs:ignore Generic.Files.LineLength.MaxExceeded
						'<strong>',
						'</strong>',
						'AIOSEO',
						'6.0',
						gmdate( 'Y' ),
						'<a href="https://aioseo.com/docs/update-wordpress/?utm_source=WordPress&utm_medium=' . $medium . '&utm_campaign=outdated-wordpress-notice" target="_blank" rel="noopener noreferrer">', // phpcs:ignore Generic.Files.LineLength.MaxExceeded
						'</a>'
					),
					array(
						'a'      => array(
							'href'   => array(),
							'target' => array(),
							'rel'    => array(),
						),
						'strong' => array(),
					)
				);
				?>
			</p>
		</div>

		<?php
		// In case this is on plugin activation.
		if ( isset( $_GET['activate'] ) ) { // phpcs:ignore HM.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Recommended	
			unset( $_GET['activate'] );
		}
	}
}