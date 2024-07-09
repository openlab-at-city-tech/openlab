<?php

namespace PDFEmbedder\Admin;

/**
 * Class WPorgReview is used to display and handle admin notices for plugin WP.org reviews.
 *
 * @since 4.7.0
 */
class WPorgReview {

	/**
	 * Primary class constructor.
	 *
	 * @since 4.7.0
	 */
	public function hooks() {

		// Let's show to admins only.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( $this->is_review_hidden() ) {
			return;
		}

		add_action( 'admin_notices', [ $this, 'display_review' ] );
		add_action( 'wp_ajax_wppdf_dismiss_review', [ $this, 'dismiss_review' ] );
		add_action( 'wp_ajax_wppdf_defer_review', [ $this, 'defer_review' ] );
	}

	/**
	 * Determine if we can display a review notice or not.
	 *
	 * @since 4.7.0
	 */
	private function is_review_hidden(): bool { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		// Verify that we can do a check for reviews.
		$review          = get_option( 'wppdf_review', [] );
		$activation_time = get_option( 'wppdf_emb_activation', 0 );
		$v470_released   = 1708516800;

		// Bail if activation time isn't set.
		if ( empty( $activation_time ) || $activation_time < $v470_released ) {
			return true;
		}

		if ( empty( $review ) ) {
			$review = [
				'time'            => time(),
				'postponed_until' => 0,
				'dismissed'       => false,
			];
		} elseif ( isset( $review['dismissed'] ) && $review['dismissed'] ) {
			return true;
		}

		if ( ! isset( $review['postponed_until'] ) ) {
			$review['postponed_until'] = 0;
		}

		// Wait till the postponed time is over.
		if ( $review['postponed_until'] > time() ) {
			update_option( 'wppdf_review', $review, 'no' );

			return true;
		}

		// Display only in a week after activation.
		if ( time() < (int) strtotime( '+7 day', (int) $activation_time ) ) {
			update_option( 'wppdf_review', $review, 'no' );

			return true;
		}

		// Update the review option now.
		update_option( 'wppdf_review', $review, 'no' );

		return false;
	}

	/**
	 * Add admin notices as needed for reviews.
	 *
	 * @since 4.7.0
	 */
	public function display_review() {

		?>
		<div class="notice notice-info is-dismissible wppdf-review-notice">
			<p>
				<?php
				echo wp_kses(
					__( 'Hey, it looks like you are using <strong>PDF Embedder</strong> for quite a while, which is great! Can you please give it a 5-star rating on WordPress.org to help us spread the word and stay motivated? Thanks!', 'pdf-embedder' ),
					[
						'strong' => [],
					]
				);
				?>
			</p>
			<p style="display: flex;">
				<a href="https://wordpress.org/support/plugin/pdf-embedder/reviews/?filter=5#new-post" class="wppdf-dismiss-review-notice wppdf-review-out" target="_blank"><?php esc_html_e( 'Ok, you deserve it', 'pdf-embedder' ); ?></a>&nbsp;&bull;&nbsp;
				<a href="#" class="wppdf-defer-review-notice" target="_blank" title="<?php esc_html_e( 'Dismiss for one month', 'pdf-embedder' ); ?>"><?php esc_html_e( 'Nope, maybe later', 'pdf-embedder' ); ?></a>&nbsp;&bull;&nbsp;
				<a href="#" class="wppdf-dismiss-review-notice" target="_blank"><?php esc_html_e( 'I already did', 'pdf-embedder' ); ?></a>
			</p>
		</div>

		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				$( document ).on( 'click', '.wppdf-dismiss-review-notice', function( event ) {
					if ( ! $( this ).hasClass( 'wppdf-review-out' ) ) {
						event.preventDefault();
					}

					$.post( ajaxurl, { action: 'wppdf_dismiss_review' }, function() {
						$( '.wppdf-review-notice' ).remove();
					} );
				} );
				$( document ).on( 'click', '.wppdf-defer-review-notice', function( event ) {
					if ( ! $( this ).hasClass( 'wppdf-review-out' ) ) {
						event.preventDefault();
					}

					$.post( ajaxurl, { action: 'wppdf_defer_review' }, function() {
						$( '.wppdf-review-notice' ).remove();
					} );
				} );
			} );
		</script>
		<?php
	}

	/**
	 * Dismiss the review notice.
	 *
	 * @since 4.7.0
	 */
	public function dismiss_review() {

		$review = get_option( 'wppdf_review', [] );

		if ( empty( $review ) ) {
			$review = [];
		}

		$review['time']            = time();
		$review['postponed_until'] = 0;
		$review['dismissed']       = true;

		update_option( 'wppdf_review', $review, 'no' );

		wp_send_json_success();
	}

	/**
	 * Defer the review notice by one month.
	 *
	 * @since 4.7.0
	 */
	public function defer_review() {

		$review = get_option( 'wppdf_review', [] );

		if ( empty( $review ) ) {
			$review = [];
		}

		$review['time']            = time();
		$review['postponed_until'] = $review['time'] + MONTH_IN_SECONDS;
		$review['dismissed']       = false;

		update_option( 'wppdf_review', $review, 'no' );

		wp_send_json_success();
	}
}
