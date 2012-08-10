<form class="achievement-delete-form standard-form" method="post" action="<?php dpa_achievement_slug_permalink(); echo DPA_SLUG_ACHIEVEMENT_DELETE ?>">

	<div id="message" class="info">
		<p><?php _e( 'WARNING: Any members who have unlocked this Achievement will have it removed. There is no way back, please be careful with this option.', 'dpa' ); ?></p>
	</div>

	<input type="checkbox" name="delete-achievement-understand" id="delete-achievement-understand" value="1" onclick="if(this.checked) { document.getElementById('delete-achievement-button').disabled = ''; } else { document.getElementById('delete-achievement-button').disabled = 'disabled'; }" /> <?php _e( 'I understand the consequences of deleting this Achievement.', 'dpa' ); ?>

	<div class="submit">
		<input type="submit" value="<?php _e( 'Delete Achievement', 'dpa' ) ?>" id="delete-achievement-button" name="delete-achievement-button" />
	</div>

	<?php wp_nonce_field( 'achievements-delete-achievement-' . dpa_get_achievement_slug() ) ?>

</form>