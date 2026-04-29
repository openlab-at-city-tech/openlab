<div data-notification-name="<?php echo esc_attr( $notice_name ); ?>" class="ngg_admin_notice <?php echo esc_attr( $css_class ); ?>">
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $html contains safe HTML for requirement notice content
	echo $html;
	?>
	<?php if ( $show_dismiss_button ) { ?>
		<p><a class='dismiss' href="#"><?php esc_html_e( 'Dismiss', 'nggallery' ); ?></a></p>
	<?php } ?>
</div>