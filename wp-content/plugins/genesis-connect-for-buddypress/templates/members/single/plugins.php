<?php
gconnect_get_header();
do_action( 'bp_before_member_plugin_template' );
?>
	<div id="item-header">
		<?php gconnect_locate_template( array( 'members/single/member-header.php' ), true ); ?>
	</div><!-- #item-header -->
	<div id="item-nav">
		<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
			<ul>
				<?php bp_get_displayed_user_nav(); do_action( 'bp_member_options_nav' ); ?>
			</ul>
			<div class="clear"></div>
		</div>
	</div><!-- #item-nav -->
	<div id="item-body" role="main">
		<?php do_action( 'bp_before_member_body' ); ?>

		<div class="item-list-tabs no-ajax" id="bpsubnav">
			<ul>
				<?php bp_get_options_nav(); do_action( 'bp_member_plugin_options_nav' ); ?>
			</ul>
			<div class="clear"></div>
		</div><!-- .item-list-tabs -->

		<h3><?php do_action( 'bp_template_title' ); ?></h3>

		<?php do_action( 'bp_template_content' ); do_action( 'bp_after_member_body' ); ?>
	</div><!-- #item-body -->
<?php 
do_action( 'bp_after_member_plugin_template' );
gconnect_get_footer();

