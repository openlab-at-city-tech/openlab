<?php
/**
* Group plugins - includes files
*
*/

/**begin layout**/
get_header(); ?>

	<div id="content" class="hfeed">
    	<?php cuny_group_single(); ?>
    </div><!--content-->

    <div id="sidebar" class="sidebar widget-area">
	<?php cuny_buddypress_group_actions(); ?>
    </div>

<?php get_footer();
/**end layout**/

function cuny_group_single() {
	global $bp; ?>

	<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>

		<?php do_action( 'bp_before_group_plugin_template' ); ?>

		<h1 class="entry-title group-title"><?php echo bp_group_name(); ?> Profile</h1>

		<div id="single-course-body">
			<?php if ( $bp->current_action == 'invite-anyone' || $bp->current_action == 'notifications' ) : ?>
				<?php do_action( 'bp_before_group_members_content' ); ?>

				<div class="item-list-tabs no-ajax" id="subnav">
					<ul>
						<?php openlab_group_membership_tabs(); ?>
					</ul>
				</div><!-- .item-list-tabs -->

			<?php else: ?>
				<div id="item-nav">
					<div class="item-list-tabs no-ajax" id="object-nav">
						<ul>
							<?php do_action( 'bp_group_plugin_options_nav' ); ?>
						</ul>
					</div>
				</div><!-- #item-nav -->
			<?php endif; ?>

			<div id="item-body">
				<?php do_action( 'bp_before_group_body' ); ?>

				<?php do_action( 'bp_template_content' ); ?>

				<?php do_action( 'bp_after_group_body' ); ?>
			</div><!-- #item-body -->
		</div>

	<?php endwhile; endif; ?>

	<?php do_action( 'bp_after_group_plugin_template' );
}
