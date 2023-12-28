<?php
/**
 * Attachment:image post content.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$image_full = wp_get_attachment_image_src( get_the_ID(), 'full' );

do_action( 'tha_entry_before' );

?>

<article data-id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php do_action( 'tha_entry_top' ); ?>

	<?php echo wp_get_attachment_image( get_the_ID(), 'medium' ); ?>

	<div class="entry-content">

		<?php do_action( 'tha_entry_content_before' ); ?>

		<table>
			<caption><?php echo esc_html_x( 'Image info', 'Attachment page image info table caption.', 'michelle' ); ?></caption>

			<tbody>

				<tr class="date">
					<th><?php echo esc_html_x( 'Image published on:', 'Attachment page publish time.', 'michelle' ); ?></th>
					<td><?php the_time( get_option( 'date_format' ) ); ?></td>
				</tr>

				<?php

				if (
					isset( $image_full[1] )
					&& isset( $image_full[2] )
				) :

					?>

					<tr class="size">
						<th><?php esc_html_e( 'Image size:', 'michelle' ); ?></th>
						<td><?php echo absint( $image_full[1] ) . ' &times; ' . absint( $image_full[2] ) . ' px'; ?></td>
					</tr>

					<?php

				endif;

				?>

				<tr class="filename">
					<th><?php esc_html_e( 'Image file name:', 'michelle' ); ?></th>
					<td><code><?php echo basename( get_attached_file( get_the_ID() ) ); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?></code></td>
				</tr>

			</tbody>
		</table>

		<?php

		the_excerpt();

		do_action( 'tha_entry_content_after' );

		?>

	</div>

	<?php do_action( 'tha_entry_bottom' ); ?>

</article>

<?php

do_action( 'tha_entry_after' );
