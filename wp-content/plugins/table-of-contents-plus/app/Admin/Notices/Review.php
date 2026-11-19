<?php
namespace AIOSEO\TableOfContents\Admin\Notices;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Review plugin notice.
 *
 * @since 1.2.0
 */
class Review {
	/**
	 * Class constructor.
	 *
	 * @since 1.2.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_table-of-contents-plus-dismiss-review-plugin-cta', [ $this, 'dismissNotice' ] );
	}

	/**
	 * Go through all the checks to see if we should show the notice.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function maybeShowNotice() {
		// Don't show to users that cannot interact with the plugin.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		if ( aioseoTableOfContents()->admin->isTableOfContentsScreen() ) {
			return;
		}

		$dismissed = get_user_meta( get_current_user_id(), '_aioseo_table_of_contents_plugin_review_dismissed', true );
		if ( '3' === $dismissed || '4' === $dismissed ) {
			return;
		}

		if ( ! empty( $dismissed ) && $dismissed > time() ) {
			return;
		}

		// Show once plugin has been active for 2 weeks.
		if ( ! aioseoTableOfContents()->internalOptions->internal->firstActivated ) {
			aioseoTableOfContents()->internalOptions->internal->firstActivated = time();
		}

		$activated = aioseoTableOfContents()->internalOptions->internal->firstActivated( time() );
		if ( $activated > strtotime( '-2 weeks' ) ) {
			return;
		}

		$this->showNotice();

		// Print the script to the footer.
		add_action( 'admin_footer', [ $this, 'printScript' ] );
	}

	/**
	 * Actually show the review plugin 2.0.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function showNotice() {
		$string1 = sprintf(
			// Translators: 1 - The plugin name ("Table of Contents Plus").
			__( 'Hey, we noticed you have been using %1$s for some time - that’s awesome! Could you please do us a BIG favor and give it a 5-star rating on WordPress to help us spread the word and boost our motivation?', 'table-of-contents-plus' ), // phpcs:ignore Generic.Files.LineLength.MaxExceeded
			'<strong>' . esc_html( AIOSEO_TABLE_OF_CONTENTS_PLUGIN_NAME ) . '</strong>'
		);

		// Translators: 1 - The plugin name ("Table of Contents Plus").
		$string9  = __( 'Ok, you deserve it', 'table-of-contents-plus' );
		$string10 = __( 'Nope, maybe later', 'table-of-contents-plus' );
		$string11 = __( 'I already did', 'table-of-contents-plus' );

		?>
		<div class="notice notice-info table-of-contents-plus-review-plugin-cta is-dismissible">
			<div class="step-3">
				<p><?php echo $string1; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<p>
					<?php // phpcs:ignore Generic.Files.LineLength.MaxExceeded ?>
					<a href="https://aioseo.com/table-of-contents-plus-rating" class="table-of-contents-plus-dismiss-review-notice" target="_blank" rel="noopener noreferrer">
						<?php echo esc_html( $string9 ); ?>
					</a>&nbsp;&bull;&nbsp;
					<a href="#" class="table-of-contents-plus-dismiss-review-notice-delay" target="_blank" rel="noopener noreferrer">
						<?php echo esc_html( $string10 ); ?>
					</a>&nbsp;&bull;&nbsp;
					<a href="#" class="table-of-contents-plus-dismiss-review-notice" target="_blank" rel="noopener noreferrer">
						<?php echo esc_html( $string11 ); ?>
					</a>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Print the script for dismissing the notice.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function printScript() {
		// Create a nonce.
		$nonce = wp_create_nonce( 'table-of-contents-plus-dismiss-review' );
		?>
		<style>
			@keyframes dismissBtnVisible {
				from { opacity: 0.99; }
				to { opacity: 1; }
			}
			.table-of-contents-plus-review-plugin-cta button.notice-dismiss {
				animation-duration: 0.001s;
				animation-name: dismissBtnVisible;
			}
		</style>
		<script>
			window.addEventListener('load', function () {
				var aioseoTableOfContentsSetupButton,
					dismissBtn,
					interval

				aioseoTableOfContentsSetupButton = function (dismissBtn) {
					var notice      = document.querySelector('.notice.table-of-contents-plus-review-plugin-cta'),
						delay       = false,
						relay       = true,
						stepOne     = notice.querySelector('.step-1'),
						stepTwo     = notice.querySelector('.step-2'),
						stepThree   = notice.querySelector('.step-3')

					// Add an event listener to the dismiss button.
					dismissBtn.addEventListener('click', function (event) {
						var httpRequest = new XMLHttpRequest(),
							postData    = ''

						// Build the data to send in our request.
						postData += '&delay=' + delay
						postData += '&relay=' + relay
						postData += '&action=table-of-contents-plus-dismiss-review-plugin-cta'
						postData += '&nonce=<?php echo esc_html( $nonce ); ?>'

						httpRequest.open('POST', '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>')
						httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
						httpRequest.send(postData)
					})

					notice.addEventListener('click', function (event) {
						if (event.target.matches('.table-of-contents-plus-review-switch-step-3')) {
							event.preventDefault()
							stepOne.style.display   = 'none'
							stepTwo.style.display   = 'none'
							stepThree.style.display = 'block'
						}
						if (event.target.matches('.table-of-contents-plus-review-switch-step-2')) {
							event.preventDefault()
							stepOne.style.display   = 'none'
							stepThree.style.display = 'none'
							stepTwo.style.display   = 'block'
						}
						if (event.target.matches('.table-of-contents-plus-dismiss-review-notice-delay')) {
							event.preventDefault()
							delay = true
							relay = false
							dismissBtn.click()
						}
						if (event.target.matches('.table-of-contents-plus-dismiss-review-notice')) {
							if ('#' === event.target.getAttribute('href')) {
								event.preventDefault()
							}
							relay = false
							dismissBtn.click()
						}
					})
				}

				dismissBtn = document.querySelector('.table-of-contents-plus-review-plugin-cta .notice-dismiss')
				if (!dismissBtn) {
					document.addEventListener('animationstart', function (event) {
						if (event.animationName == 'dismissBtnVisible') {
							dismissBtn = document.querySelector('.table-of-contents-plus-review-plugin-cta .notice-dismiss')
							if (dismissBtn) {
								aioseoTableOfContentsSetupButton(dismissBtn)
							}
						}
					}, false)

				} else {
					aioseoTableOfContentsSetupButton(dismissBtn)
				}
			});
		</script>
		<?php
	}

	/**
	 * Dismiss the review plugin CTA.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function dismissNotice() {
		// Early exit if we're not on a table-of-contents-plus-dismiss-review-plugin-cta action.
		if ( ! isset( $_POST['action'] ) || 'table-of-contents-plus-dismiss-review-plugin-cta' !== $_POST['action'] ) {
			return;
		}

		check_ajax_referer( 'table-of-contents-plus-dismiss-review', 'nonce' );
		$delay = isset( $_POST['delay'] ) ? 'true' === sanitize_text_field( wp_unslash( $_POST['delay'] ) ) : false;
		$relay = isset( $_POST['relay'] ) ? 'true' === sanitize_text_field( wp_unslash( $_POST['relay'] ) ) : false;

		if ( ! $delay ) {
			update_user_meta( get_current_user_id(), '_aioseo_table_of_contents_plugin_review_dismissed', $relay ? '4' : '3' );

			wp_send_json_success();

			return;
		}

		update_user_meta( get_current_user_id(), '_aioseo_table_of_contents_plugin_review_dismissed', strtotime( '+1 week' ) );

		wp_send_json_success();
	}
}