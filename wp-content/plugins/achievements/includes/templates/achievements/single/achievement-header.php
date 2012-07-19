<?php do_action( 'dpa_before_achievement_header' ) ?>

<div id="item-actions">
	<?php do_action( 'dpa_after_achievement_item_actions' ) ?>
</div><!-- #item-actions -->

<div id="item-header-avatar" style="<?php if ( dpa_get_achievement_action_count() <= 1 || dpa_get_achievement_counter() < 1 ) { echo 'margin-bottom: 25px;'; } ?>">
	<a href="<?php dpa_achievement_slug_permalink() ?>">
		<?php dpa_achievement_picture( 'full' ) ?>
	</a>
	<?php if ( dpa_get_achievement_action_count() > 1 && dpa_get_achievement_counter() >= 1 ) : ?>
		<div id="progress-bar" title="<?php dpa_achievement_progress_bar_alt_text() ?>" alt="<?php dpa_achievement_progress_bar_alt_text() ?>" style="width: <?php dpa_achievement_picture_width() ?>px"><div style="width: <?php dpa_achievement_progress_bar_width() ?>px"></div></div>
	<?php endif; ?>
</div><!-- #item-header-avatar -->

<div id="item-header-content">
	<h2><a href="<?php dpa_achievement_slug_permalink() ?>"><?php dpa_achievement_name() ?></a></h2>

	<p>
		<?php if ( dpa_is_achievement_a_badge() ) : ?>
			<span class="highlight"><?php _e( 'Award', 'dpa' ) ?></span>&nbsp;
		<?php endif; ?>

		<span class="highlight"><?php printf( _n( "%s point", "%s points", bp_core_number_format( dpa_get_achievement_points() ), 'dpa' ), bp_core_number_format( dpa_get_achievement_points() ) ) ?></span>&nbsp;

		<?php if ( !dpa_get_achievement_is_active() && dpa_permission_can_user_edit() ) : ?>
			<span class="highlight"><?php _e( 'Inactive Achievement', 'dpa' ) ?></span>&nbsp;
		<?php endif; ?>

		<span class="activity"><?php printf( _n( 'Only %s person has unlocked this Achievement', '%s people have unlocked this Achievement', bp_core_number_format( dpa_get_achievement_unlocked_count() ), 'dpa' ), bp_core_number_format( dpa_get_achievement_unlocked_count() ) ) ?></span>
	</p>

	<?php do_action( 'dpa_before_achievement_header_meta' ) ?>

	<div id="item-meta">
		<?php dpa_achievement_description_excerpt() ?>

		<div id="item-buttons">
			<?php do_action( 'dpa_achievement_header_actions' ); ?>
		</div><!-- #item-buttons -->

		<?php do_action( 'dpa_achievement_header_meta' ) ?>
	</div>
</div><!-- #item-header-content -->

<?php do_action( 'dpa_after_achievement_header' ) ?>

<?php do_action( 'template_notices' ) ?>