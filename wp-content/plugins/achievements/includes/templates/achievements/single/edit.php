<form class="achievement-edit-form standard-form" method="post" action="<?php dpa_achievement_slug_permalink(); echo DPA_SLUG_ACHIEVEMENT_EDIT ?>">

	<?php if ( bp_is_active( 'groups' ) || is_multisite() && bp_is_active( 'blogs' ) ) : ?>
		<noscript><p><?php _e( "Some of the Action options below may not be relevant to the type or event of the Achievement.", 'dpa' ) ?></p></noscript>
	<?php endif; ?>

	<?php dpa_load_template( array( 'achievements/_addedit.php' ) ) ?>
	<?php wp_nonce_field( 'achievement-edit-' . dpa_get_achievement_slug() ) ?>

</form>