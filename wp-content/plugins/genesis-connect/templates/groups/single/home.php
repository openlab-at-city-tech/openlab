<?php
gconnect_get_header();

if ( bp_has_groups() ) : 
	while ( bp_groups() ) : 
		bp_the_group();
		do_action( 'bp_before_group_home_content' ); 
?>
	<div id="item-header">
		<?php gconnect_locate_template( array( 'groups/single/group-header.php' ), true ) ?>
	</div>
	<div id="item-nav">
		<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
			<ul>
				<?php bp_get_options_nav(); do_action( 'bp_group_options_nav' ); ?>
			</ul>
			<div class="clear"></div>
		</div>
	</div>
	<div id="item-body">
<?php 
		do_action( 'bp_before_group_body' );
		if ( !gconnect_group_single_template() && !bp_group_is_visible() ) :
			do_action( 'bp_before_group_status_message' );
?>
		<div id="message" class="info">
			<p><?php bp_group_status_message() ?></p>
		</div>
<?php
			do_action( 'bp_after_group_status_message' );
		endif;
		do_action( 'bp_after_group_body' ); 
?>
	</div>
<?php 
		do_action( 'bp_after_group_home_content' );
	endwhile; 
endif;
gconnect_get_footer();

