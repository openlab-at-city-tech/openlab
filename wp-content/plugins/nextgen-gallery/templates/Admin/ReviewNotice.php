<p>
	<?php
	$review_1_anchor = 'href="https://wordpress.org/support/plugin/nextgen-gallery/reviews/?rate=5#new-post" target="_blank"';
	$user            = get_userdata( get_current_user_id() );
	echo wp_kses_post(
		sprintf(
			/* translators: %1$s: user display name, %2$d: number of galleries, %3$s: review link anchor attributes, %4$s: feedback link anchor attributes */
			__( "Hey <strong>%1\$s</strong>, you've created %2\$d NextGEN galleries! Awesome! Could I ask you to give us a 5-star rating really quickly on <a %3\$s>WordPress.org</a>? It helps other WordPress users and motivates us to keep improving. You can also just send us feedback <a %4\$s>here</a>. Thanks! ~ Syed Balkhi, CEO of Imagely", 'nggallery' ),
			esc_html( $user->display_name ),
			absint( $number ),
			$review_1_anchor,
			'href="https://www.imagely.com/feedback/" target="_blank"'
		)
	);
	?>
</p>
<p>
	<a   class='dismiss' data-dismiss-code="2" <?php echo wp_kses_post( $review_1_anchor ); ?>><?php esc_html_e( 'Ok, you deserve it', 'nggallery' ); ?></a>
	| <a class='dismiss' data-dismiss-code="2" href="#"><?php esc_html_e( 'Nope, maybe later', 'nggallery' ); ?></a>
	| <a class='dismiss' data-dismiss-code="3" href="#"><?php esc_html_e( 'I already did', 'nggallery' ); ?></a>
</p>
