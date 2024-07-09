<?php

/**
 * We require PHP version 7.0+ for the whole plugin to work.
 *
 * @since 4.7.0
 */
add_action(
	'admin_notices',
	static function () {
		?>
		<div class="notice notice-error">
			<p>
				<?php
				printf(
					wp_kses( /* translators: %s - WPBeginner URL for recommended WordPress hosting. */
						__( 'Your site is running an <strong>insecure version</strong> of PHP that is no longer supported. Please contact your web hosting provider to update your PHP version or switch to a <a href="%s" target="_blank" rel="noopener noreferrer">recommended WordPress hosting company</a>.', 'pdf-embedder' ),
						[
							'a'      => [
								'href'   => [],
								'target' => [],
								'rel'    => [],
							],
							'strong' => [],
						]
					),
					'https://www.wpbeginner.com/wordpress-hosting/'
				);
				?>
				<br><br>
				<?php
				echo wp_kses(
					__( '<strong>Note:</strong> The PDF Embedder plugin is disabled on your site until you fix the issue.', 'pdf-embedder' ),
					[
						'strong' => [],
					]
				);
				?>
			</p>
		</div>

		<?php
		// In case this is on plugin activation.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}
);

