<div class="item-list-tabs no-ajax" id="subnav">
	<ul>
		<?php if ( bp_is_my_profile() ) : ?>
			<?php bp_get_options_nav() ?>
		<?php endif; ?>

		<li id="achievements-order-select" class="last filter">

			<?php _e( 'Order By:', 'dpa' ) ?>
			<select id="achievements-sort-by">
				<option value="newest"><?php _e( 'Recently Unlocked', 'dpa' ) ?></option>
				<option value="alphabetical"><?php _e( 'Alphabetical', 'dpa' ) ?></option>
				<option value="eventcount"><?php _e( 'Event Count', 'dpa' ) ?></option>
				<option value="points"><?php _e( 'Points', 'dpa' ) ?></option>

				<?php do_action( 'dpa_unlocked_achievements_order_options' ) ?>
			</select>
		</li>
	</ul>
</div><!-- .item-list-tabs -->

<?php do_action( 'dpa_before_unlocked_achievements_content' ) ?>

<div class="achievements myachievements">
	<?php dpa_load_template( array( 'achievements/achievements-loop.php' ) ) ?>
</div>

<?php do_action( 'dpa_after_unlocked_achievements_content' ) ?>