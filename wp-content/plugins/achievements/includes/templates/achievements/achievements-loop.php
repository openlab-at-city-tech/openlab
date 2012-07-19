<?php do_action( 'dpa_before_achievements_loop' ) ?>

<?php if ( dpa_has_achievements( bp_ajax_querystring( 'achievements' ) ) ) : ?>

	<div class="pagination">

		<div class="pag-count" id="achievements-count-top">
			<?php dpa_achievements_pagination_count() ?>
		</div>

		<div class="pagination-links" id="achievements-pag-top">
			<?php dpa_achievements_pagination_links() ?>
		</div>

	</div>

	<ul id="achievements-list" class="item-list">
	<?php while ( dpa_achievements() ) : dpa_the_achievement(); ?>

		<li class="<?php dpa_achievement_directory_class() ?>">
			<div class="item-avatar">
				<a href="<?php dpa_achievement_slug_permalink() ?>"><?php dpa_achievement_picture() ?></a>
				<p style="width: <?php dpa_achievement_picture_width() ?>px" title="<?php printf( __( "This Achievement is worth %s points.", 'dpa' ), bp_core_number_format( dpa_get_achievement_points() ) ) ?>"><?php dpa_achievement_points() ?></p>
			</div>

			<div class="item">
				<div class="item-title"><a href="<?php dpa_achievement_slug_permalink() ?>"><?php dpa_achievement_name() ?></a></div>
				<?php if ( dpa_is_achievement_unlocked() ) : ?>
					<div class="item-meta"><span class="activity"><?php printf( __( "Unlocked %s", 'dpa' ), dpa_get_achievement_unlocked_ago() ) ?></span></div>
				<?php endif; ?>

				<div class="item-desc"><?php dpa_achievement_description_excerpt() ?></div>

				<?php do_action( 'dpa_directory_achievements_item' ) ?>
			</div>

			<div class="action">
				<?php do_action( 'dpa_directory_achievements_actions_top' ) ?>

				<div class="meta">
					<?php if ( !dpa_get_achievement_is_active() ) : ?>
						<?php _e( 'Inactive', 'dpa' ) ?>
					<?php else : ?>
						<?php dpa_achievement_type() ?>
					<?php endif; ?>

					<?php do_action( 'dpa_directory_achievements_actions_meta' ) ?>
				</div>

				<p class="quickadmin"><?php dpa_achievements_quickadmin() ?></p>

				<?php do_action( 'dpa_directory_achievements_actions_bottom' ) ?>
			</div>

			<div class="clear"></div>
		</li>

	<?php endwhile; ?>
	</ul>

	<?php do_action( 'dpa_after_achievements_loop' ) ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="achievements-count-bottom">
			<?php dpa_achievements_pagination_count() ?>
		</div>

		<div class="pagination-links" id="achievements-pag-bottom">
			<?php dpa_achievements_pagination_links() ?>
		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<?php if ( !empty( $_REQUEST['search_terms'] ) ) : ?>
			<p><?php echo sprintf( __( 'There were no Achievements found matching &ldquo;%s.&rdquo;', 'dpa' ), apply_filters( 'dpa_get_achievements_search_query', stripslashes( $_REQUEST['search_terms'] ) ) ) ?></p>
		<?php elseif ( dpa_is_member_my_achievements_page() && bp_is_my_profile() ) : ?>
			<p><?php _e( "You haven't unlocked any Achievements yet", 'dpa' ) ?></p>
		<?php elseif ( dpa_is_member_my_achievements_page() && !bp_is_my_profile() ) : ?>
			<p><?php printf( __( "%s hasn't unlocked any Achievements yet", 'dpa' ), bp_get_displayed_user_fullname() ) ?></p>
		<?php else : ?>
			<p><?php _e( 'Oops, no Achievements were found!', 'dpa' ) ?></p>
		<?php endif; ?>
	</div>

<?php endif; ?>