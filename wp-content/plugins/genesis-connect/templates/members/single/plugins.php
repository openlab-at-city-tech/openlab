<?php
get_header();
genesis_before_content_sidebar_wrap();
gconnect_before_content();

do_action( 'bp_before_member_plugin_template' );
?>

			<div id="item-header">
				<?php gconnect_locate_template( array( 'members/single/member-header.php' ), true ) ?>
			</div>
			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="sub-nav">
					<ul>
						<?php bp_get_displayed_user_nav() ?>
						<?php do_action( 'bp_member_options_nav' ) ?>
					</ul>
					<div class="clear"></div>
				</div>
			</div>
			<div id="item-body">
				<div class="item-list-tabs no-ajax" id="bpsubnav">
					<ul>
						<?php bp_get_options_nav() ?>
						<?php do_action( 'bp_member_plugin_options_nav' ) ?>
					</ul>
					<div class="clear"></div>
				</div>
				<?php do_action( 'bp_template_title' ); do_action( 'bp_template_content' ); do_action( 'bp_after_member_body' ); ?>
			</div><!-- #item-body -->
<?php 
do_action( 'bp_after_member_plugin_template' );

gconnect_after_content();
genesis_after_content_sidebar_wrap();
get_footer();
?>
