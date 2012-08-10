<?php get_header( 'buddypress' ) ?>

	<div id="content">
		<div class="padder">
			<?php if ( dpa_has_achievements( 'skip_detail_page_result=0') ) : while ( dpa_achievements() ) : dpa_the_achievement(); ?>

			<?php do_action( 'dpa_before_achievement_home_content' ) ?>

			<div id="item-header">
				<?php dpa_load_template( array( 'achievements/single/achievement-header.php' ) ) ?>
			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_options_nav() ?>

						<?php do_action( 'dpa_achievement_options_nav' ) ?>
					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body">
				<?php do_action( 'dpa_before_achievement_home_body' ) ?>

				<?php if ( dpa_is_achievement_change_picture_page() ) : ?>
					<?php dpa_load_template( array( 'achievements/single/change-picture.php' ) ) ?>

				<?php elseif ( dpa_is_achievement_activity_page() && bp_is_active( 'activity' ) ) : ?>
					<?php dpa_load_template( array( 'achievements/single/activity.php' ) ) ?>

				<?php elseif ( dpa_is_achievement_unlocked_by_page() ) : ?>
					<?php dpa_load_template( array( 'achievements/single/unlocked-by.php' ) ) ?>

				<?php elseif ( dpa_is_achievement_delete_page() ) : ?>
					<?php dpa_load_template( array( 'achievements/single/delete.php' ) ) ?>

				<?php elseif ( dpa_is_achievement_edit_page() ) : ?>
					<?php dpa_load_template( array( 'achievements/single/edit.php' ) ) ?>

				<?php elseif ( dpa_is_achievement_grant_page() ) : ?>
					<?php dpa_load_template( array( 'achievements/single/grant.php' ) ) ?>
				<?php endif; ?>

				<?php do_action( 'dpa_after_achievement_home_body' ) ?>
			</div><!-- #item-body -->

			<?php do_action( 'dpa_after_achievement_home_content' ) ?>

			<?php endwhile; endif; ?>
		</div><!-- .padder -->
	</div><!-- #content -->

	<?php get_sidebar( 'buddypress' ) ?>

<?php get_footer( 'buddypress' ) ?>