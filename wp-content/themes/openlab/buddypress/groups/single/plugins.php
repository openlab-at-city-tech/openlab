<?php
/**
* Group plugins - includes files
*
*/

                global $bp;
		do_action( 'bp_before_group_plugin_template' ); ?>

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
	<?php do_action( 'bp_after_group_plugin_template' );