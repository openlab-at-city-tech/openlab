<form action="<?php dpa_achievement_slug_permalink(); echo DPA_SLUG_ACHIEVEMENT_GRANT ?>" method="post" id="achievement-grant-form" class="achievement-grant-form standard-form">

	<div class="left-menu">
		<div id="grant-invite-list">
			<ul>
				<?php dpa_grant_achievement_userlist() ?>
			</ul>
		</div>
	</div><!-- .left-menu -->

	<div class="main-column">
		<div id="message" class="info">
			<p><?php _e( 'Select people to give this Achievement to.', 'dpa' ); ?></p>
		</div>

		<ul id="grant-user-list" class="item-list">
		</ul>
	</div><!-- .main-column -->
	<div class="clear"></div>

	<div class="submit">
		<input type="submit" name="achievement-grant" id="submit" value="<?php _e( 'Give Achievement', 'dpa' ) ?>" />
	</div>

	<?php wp_nonce_field( 'achievement_grant_' . dpa_get_achievement_slug(), '_wpnonce_achievements_grant' ) ?>

</form><!-- #achievement-grant-form -->