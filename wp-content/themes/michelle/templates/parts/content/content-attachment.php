<?php
/**
 * Attachment post content.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

do_action( 'tha_entry_before' );

?>

<article data-id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php do_action( 'tha_entry_top' ); ?>

	<div class="entry-content">

		<?php do_action( 'tha_entry_content_before' ); ?>

		<div class="attachment-download">
			<span class="attachment-download-label"><?php esc_html_e( 'Download attachment file:', 'michelle' ); ?></span>
			<?php the_attachment_link(); ?>
		</div>

		<?php

		if ( has_excerpt() ) {
			the_excerpt();
		}

		the_content();

		do_action( 'tha_entry_content_after' );

		?>

	</div>

	<?php do_action( 'tha_entry_bottom' ); ?>

</article>

<?php

do_action( 'tha_entry_after' );
