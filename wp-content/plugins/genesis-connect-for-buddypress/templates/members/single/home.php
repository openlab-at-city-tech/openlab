<?php
gconnect_get_header();

do_action( 'bp_before_member_home_content' );
?>
	<div id="item-header" role="complementary">
		<?php gconnect_locate_template( array( 'members/single/member-header.php' ), true ); ?>
	</div><!-- #item-header -->
	<div id="item-nav">
		<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
			<ul>
				<?php bp_get_displayed_user_nav(); do_action( 'bp_members_directory_member_types' ); ?>
			</ul>
			<div class="clear"></div>
		</div>
	</div><!-- #item-nav -->
	<div id="item-body">
		<?php gconnect_member_single_template(); ?>
	</div><!-- #item-body -->
<?php
do_action( 'bp_after_member_home_content' );

gconnect_get_footer();
