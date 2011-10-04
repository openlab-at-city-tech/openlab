<?php
get_header();
genesis_before_content_sidebar_wrap();
gconnect_before_content();
?>
			<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>

			<?php do_action( 'bp_before_group_plugin_template' ) ?>

			<div id="item-header">
				<?php gconnect_locate_template( array( 'groups/single/group-header.php' ), true ) ?>
			</div>

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="sub-nav">
					<ul>
						<?php bp_get_options_nav() ?>

						<?php do_action( 'bp_group_plugin_options_nav' ) ?>
					</ul>
					<div class="clear"></div>
				</div>
			</div>

			<div id="item-body">

				<?php do_action( 'bp_before_group_body' ); do_action( 'bp_template_content' ); do_action( 'bp_after_group_body' ); ?>

			</div><!-- #item-body -->

			<?php endwhile; endif; 

do_action( 'bp_after_group_plugin_template' );

gconnect_after_content();
genesis_after_content_sidebar_wrap();
get_footer();
?>
