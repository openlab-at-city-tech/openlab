<?php
get_header();
genesis_before_content_sidebar_wrap();
gconnect_before_content();
?>
			<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>

			<?php do_action( 'bp_before_group_home_content' ) ?>

			<div id="item-header">
				<?php gconnect_locate_template( array( 'groups/single/group-header.php' ), true ) ?>
			</div>

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_options_nav() ?>

						<?php do_action( 'bp_group_options_nav' ) ?>
					</ul>
					<div class="clear"></div>
				</div>
			</div>

			<div id="item-body">
				<?php do_action( 'bp_before_group_body' ) ?>

				<?php if ( !gconnect_group_single_template() ) : ?>

					<?php do_action( 'bp_before_group_status_message' ) ?>

					<div id="message" class="info">
						<p><?php bp_group_status_message() ?></p>
					</div>

					<?php do_action( 'bp_after_group_status_message' ) ?>
				<?php endif; ?>

				<?php do_action( 'bp_after_group_body' ) ?>
			</div>

			<?php do_action( 'bp_after_group_home_content' ) ?>

			<?php endwhile; endif; ?>
<?php
gconnect_after_content();
genesis_after_content_sidebar_wrap();
get_footer();
?>
