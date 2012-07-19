<?php get_header( 'buddypress' ) ?>

	<div id="content">
		<div class="padder">

		<form action="" method="post" id="achievements-directory-form" class="dir-form">
			<h3><?php _e( 'Achievements Directory', 'dpa' ) ?><?php if ( dpa_permission_can_user_create() ) : ?> &nbsp;<a class="button" href="<?php dpa_achievements_permalink() ?>/<?php echo DPA_SLUG_CREATE ?>"><?php _e( 'Create an Achievement', 'dpa' ) ?></a><?php endif; ?></h3>

			<?php do_action( 'dpa_before_directory_achievements_content' ) ?>

			<div id="achievements-dir-search" class="dir-search">
				<?php dpa_directory_achievements_search_form() ?>
			</div><!-- #achievements-dir-search -->

			<div class="item-list-tabs">
				<ul>
					<li class="selected" id="achievements-all"><a href="<?php dpa_achievements_permalink() ?>"><?php printf( __( 'All Achievements <span>%s</span>', 'dpa' ), dpa_get_total_achievement_count() ) ?></a></li>

					<?php if ( is_user_logged_in() ) : ?>
						<li id="achievements-personal"><a href="<?php echo bp_loggedin_user_domain() . DPA_SLUG . '/' . DPA_SLUG_MY_ACHIEVEMENTS ?>"><?php printf( __( 'My Achievements <span>%s</span>', 'dpa' ), dpa_get_total_achievement_count_for_user() ) ?></a></li>
					<?php endif; ?>

					<?php do_action( 'dpa_achievements_directory_achievement_types' ) ?>

					<li id="achievements-order-select" class="last filter">

						<?php _e( 'Order By:', 'dpa' ) ?>
						<select>
							<option value="alphabetical"><?php _e( 'Alphabetical', 'dpa' ) ?></option>
							<option value="eventcount"><?php _e( 'Event Count', 'dpa' ) ?></option>
							<option value="newest"><?php _e( 'Newest', 'dpa' ) ?></option>
							<option value="points"><?php _e( 'Points', 'dpa' ) ?></option>

							<?php do_action( 'dpa_achievements_directory_order_options' ) ?>
						</select>
					</li>
				</ul>
			</div><!-- .item-list-tabs -->

			<div id="achievements-dir-list" class="achievements dir-list">
				<?php do_action( 'template_notices' ) ?>
				<?php dpa_load_template( array( 'achievements/achievements-loop.php' ) ) ?>
			</div><!-- #achievements-dir-list -->

			<?php do_action( 'dpa_directory_achievements_content' ) ?>

			<?php wp_nonce_field( 'directory_achievements', '_wpnonce-achievements-filter' ) ?>

		</form><!-- #achievements-directory-form -->

		<?php do_action( 'dpa_after_directory_achievements_content' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php get_sidebar( 'buddypress' ) ?>

<?php get_footer( 'buddypress' ) ?>