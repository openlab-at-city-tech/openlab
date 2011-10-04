<?php
get_header();
genesis_before_content_sidebar_wrap();
gconnect_before_content();
?>
			<?php do_action( 'bp_before_member_home_content' ) ?>

			<div id="item-header">
				<?php gconnect_locate_template( array( 'members/single/member-header.php' ), true ) ?>
			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_displayed_user_nav() ?>

						<?php do_action( 'bp_members_directory_member_types' ) ?>
					</ul>
					<div class="clear"></div>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body">
				<?php gconnect_member_single_template(); ?>
			</div><!-- #item-body -->

			<?php do_action( 'bp_after_member_home_content' ) ?>
<?php
gconnect_after_content();
genesis_after_content_sidebar_wrap();
get_footer();
?>
