<?php

namespace PDFEmbedder\Admin;

use WP_Post;
use WP_User;
use PDFEmbedder\Helpers\Links;

/**
 * Extend default WordPress Media library.
 *
 * @since 4.7.0
 */
class MediaLibrary {

	/**
	 * Assign all hooks to proper places.
	 *
	 * @since 4.7.0
	 */
	public function hooks() {

		add_filter( 'attachment_fields_to_edit', [ $this, 'attachment_fields_to_edit' ], 10, 2 );
		add_action(
			'add_meta_boxes_attachment',
			// Using a closure to avoid adding a new class method just to add the meta box.
			function () {
				add_meta_box( 'attachment_meta_box', __( 'PDF Embedder', 'pdf-embedder' ), [ $this, 'add_meta_boxes_attachment' ], 'attachment', 'side' );
			}
		);

		add_filter( 'upload_mimes', [ $this, 'add_pdf_to_upload_mimes' ], 10, 2 );

		add_filter( 'post_mime_types', [ $this, 'add_pdf_mime_type' ] );
	}

	/**
	 * Add additional fields to the "Attachment details" media popup/screen.
	 *
	 * @since 4.7.0
	 *
	 * @param array   $form_fields List of fields to display.
	 * @param WP_Post $attachment  The WP_Post attachment object.
	 */
	public function attachment_fields_to_edit( array $form_fields, WP_Post $attachment ): array {

		if ( $attachment->post_mime_type !== 'application/pdf' ) {
			return $form_fields;
		}

		$current_screen = get_current_screen();

		// We are in single attachment editing screen.
		if ( $current_screen && $current_screen->id === 'attachment' ) {
			// We have a special metabox for this, so we don't need to show the fields here.
			return $form_fields;
		}

		if ( pdf_embedder()->is_premium() ) {
			return $form_fields;
		}

		$file_url  = wp_get_attachment_url( $attachment->ID );
		$is_secure = ! strpos( $file_url, '/securepdfs/' ) === false;

		$secured_value  = '<span class="dashicons dashicons-unlock pdfemb-admin-attachment-meta-icon"></span>' . esc_html__( 'No', 'pdf-embedder-premium' ) . '.&nbsp;';
		$secured_value .= sprintf(
			wp_kses( /* translators: %s - URL to the settings page. */
				__( '<a href="%s">Learn more</a>', 'pdf-embedder' ),
				[
					'a' => [
						'href' => [],
					],
				]
			),
			esc_url( pdf_embedder()->admin()->get_settings_url( 'secure' ) )
		);

		if ( $is_secure ) {
			$secured_value = '<span class="dashicons dashicons-lock pdfemb-admin-attachment-meta-icon"></span>' . esc_html__( 'Yes', 'pdf-embedder-premium' );
		}

		$form_fields['pdfemb-secured-lite'] = [
			'value' => $secured_value,
			'input' => 'value',
			'label' => __( 'PDF Secured', 'pdf-embedder-premium' ),
		];

		$form_fields['pdfemb-tracking-lite'] = [
			'value' => sprintf(
				wp_kses( /* translators: %s - URL to wp-pdf.com page. */
					__( 'Not tracked. <a href="%s" target="_blank">Learn more</a>', 'pdf-embedder' ),
					[
						'a' => [
							'href'   => [],
							'target' => [],
						],
					]
				),
				esc_url( pdf_embedder()->admin()->get_settings_url() )
			),
			'input' => 'value',
			'label' => __( 'PDF Downloads / Views', 'pdf-embedder' ),
		];

		return $form_fields;
	}

	/**
	 * Add a metabox with extra info to the Attachments editing page.
	 *
	 * @since 4.8.0
	 *
	 * @param WP_Post $attachment The WP_Post attachment object.
	 */
	public function add_meta_boxes_attachment( WP_Post $attachment ) {

		if ( $attachment->post_mime_type !== 'application/pdf' ) {
			return;
		}

		if ( pdf_embedder()->is_premium() ) {
			return;
		}

		$file_url  = wp_get_attachment_url( $attachment->ID );
		$is_secure = ! strpos( $file_url, '/securepdfs/' ) === false;
		?>

		<div class="attachment_field_containers">
			<p>
				<style>
					.pdfemb-admin-attachment-meta-icon {
						color: #8c8f94;
						display: inline-block;
						padding-right: 3px;
					}
				</style>

				<?php
				if ( $is_secure ) {
					echo '<span class="dashicons dashicons-lock pdfemb-admin-attachment-meta-icon"></span>';
					esc_html_e( 'Secured PDF', 'pdf-embedder' );
				} else {
					echo '<span class="dashicons dashicons-unlock pdfemb-admin-attachment-meta-icon"></span>';

					esc_html_e( 'Not Secured PDF.', 'pdf-embedder' );
					echo '&nbsp;';
					printf(
						wp_kses( /* translators: %s - URL to the settings page. */
							__( '<a href="%s">Learn more</a>', 'pdf-embedder' ),
							[
								'a' => [
									'href' => [],
								],
							]
						),
						esc_url( pdf_embedder()->admin()->get_settings_url( 'secure' ) )
					);
				}
				?>
			</p>

			<p>
				<?php
				printf(
					wp_kses( /* translators: %s - URL to wp-pdf.com page. */
						__( 'Track downloads and views with <a href="%s" target="_blank">PDF Embedder Premium</a>.', 'pdf-embedder' ),
						[
							'a' => [
								'href'   => [],
								'target' => [],
							],
						]
					),
					esc_url( Links::get_upgrade_link( 'Media Library', 'Downloads / Views' ) )
				);
				?>
			</p>
		</div>

		<?php
	}

	/**
	 * Add PDF mime type to the list of allowed mime types.
	 *
	 * @since 4.7.0
	 *
	 * @param array            $mimes Mime types keyed by the file extension regex corresponding to those types.
	 * @param int|WP_User|null $user  User ID, User object or null if not provided (indicates current user).
	 */
	public function add_pdf_to_upload_mimes( array $mimes, $user ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

		$mimes['pdf'] = 'application/pdf';

		return $mimes;
	}

	/**
	 * Filter for PDFs in Media Gallery.
	 *
	 * @since 4.7.0
	 *
	 * @param array $post_mime_types Default list of post mime types.
	 */
	public function add_pdf_mime_type( array $post_mime_types ): array {

		$post_mime_types['application/pdf'] = [
			__( 'PDFs', 'pdf-embedder' ),
			__( 'Manage PDFs', 'pdf-embedder' ),
			/* translators: %s - number of PDF files. */
			_n_noop(
				'PDF <span class="count">(%s)</span>',
				'PDFs <span class="count">(%s)</span>',
				'pdf-embedder'
			),
		];

		return $post_mime_types;
	}
}
