<table cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 600px; border-top: 7px solid <?php echo esc_attr( $args['highlight_color'] ); ?>" bgcolor="<?php echo esc_attr( $args['header_bg'] ); ?>" class="header_bg">
	<tr>
		<td style="text-align: center; padding: 15px 0; font-family: sans-serif; mso-height-rule: exactly; font-weight: bold; color: <?php echo esc_attr( $args['header_text_color'] ); ?>; font-size: <?php echo esc_attr( $args['header_text_size'] . 'px' ); ?>" class="header_text_color header_text_size">
			<?php
			/**
			 * Fires before the display of the email template header.
			 *
			 * @since 2.5.0
			 */
			do_action( 'bp_before_email_header' );
			?>

			<?php /* Note that image path is hardcoded to production site to avoid HTTP auth issues */ ?>
			<img style="max-width: 400px;" src="<?php echo esc_attr( 'https://openlab.citytech.cuny.edu/wp-content/themes/openlab/images/openlab-logo-full.png' ); ?>" alt="<?php echo esc_attr( bp_get_option( 'blogname' ) ); ?>" width="100%" />

			<?php
			/**
			 * Fires after the display of the email template header.
			 *
			 * @since 2.5.0
			 */
			do_action( 'bp_after_email_header' );
			?>
		</td>
	</tr>
</table>
