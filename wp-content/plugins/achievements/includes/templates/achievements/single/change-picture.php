<?php query_posts( dpa_change_picture_get_query() ) ?>
<?php if ( dpa_change_picture_has_pictures() && current_user_can( 'upload_files' ) ) : ?>

	<div class="pagination no-ajax">
		<div class="pag-count" id="achievement-change-picture-dir-count">
			<?php dpa_change_picture_pagination_count() ?>
		</div>

		<div class="pagination-links" id="achievement-change-picture-dir-pag">
			<?php dpa_change_picture_pagination() ?>
		</div>
	</div>

	<form class="achievement-changepicture-form standard-form" method="post" action="<?php dpa_achievement_slug_permalink(); echo DPA_SLUG_ACHIEVEMENT_CHANGE_PICTURE ?>">
	<?php if ( dpa_change_picture_has_manylots() ) : ?>
		<div id="message" class="info">
			<p><?php printf( __( "We've noticed that you've got a lot of pictures to choose from. <a href='%s'>Set a filter</a> to restrict which images are retrieved from your <a href='%s'>WordPress Media Library</a>.", 'dpa' ), admin_url( 'admin.php?page=achievements' ), admin_url( 'upload.php' ) ) ?></p>
		</div>
	<?php endif;  ?>

	<div class="avatar-preview">
	<?php while ( have_posts() ) : the_post(); list( $img_src ) = image_downsize( get_the_ID(), 'thumbnail' ); ?>
		<img class="avatar-preview <?php if ( dpa_get_achievement_picture_id() == get_the_ID() ) { echo 'avatar-preview-selected'; } ?>" id="a<?php the_ID() ?>" src="<?php echo esc_attr( $img_src ) ?>" />
	<?php endwhile; ?>
	</div>
	<div style="clear: left"></div>

	<input type="submit" name="achievement-change-picture" id="achievement-change-picture" value="<?php _e( 'Update Picture', 'dpa' ) ?>">
	<input type="hidden" name="picture_id" id="picture_id" value="<?php echo esc_attr( dpa_get_achievement_picture_id() ) ?>" />

	<?php wp_nonce_field( 'achievement-change-picture-' . dpa_get_achievement_slug() ) ?>
	</form>

	<div id="pag-bottom" class="pagination no-ajax">
		<div class="pag-count" id="achievement-change-picture-dir-count">
			<?php dpa_change_picture_pagination_count() ?>
		</div>

		<div class="pagination-links" id="achievement-change-picture-dir-pag">
			<?php dpa_change_picture_pagination() ?>
		</div>
	</div>

<?php elseif ( current_user_can( 'upload_files' ) ) : ?>

	<p><?php printf( __( "Before you can choose a picture, you need to upload an image to the <a href='%s'>WordPress Media Library</a>.", 'dpa' ), admin_url( 'upload.php' ) ) ?></p>

<?php else : ?>

	<p><?php _e( "Unfortunately, there aren't any pictures to choose from. You'll need to ask an Administrator to upload some to the WordPress Media Library for you.", 'dpa' ) ?></p>

<?php endif; ?>