<?php 
/**
* Plugins - template: for MOL Invitations
*
**/

/**begin layout**/
get_header(); ?>

	<div id="content" class="hfeed">
    	<?php cuny_plugin_loop(); ?>
    </div><!--content-->

	<div id="sidebar" class="sidebar widget-area">
	<?php get_template_part('members/single/sidebar'); ?>
    </div>

<?php
get_footer();

/**end layout**/

function cuny_plugin_loop(){
	
	global $bp;

do_action( 'bp_before_member_plugin_template' );
?>

	<h1 class="entry-title mol-title"><?php bp_displayed_user_fullname() ?>'s Profile</h1>
	
    <div class="submenu"><div class="submenu-text">My Invitations: </div><?php echo openlab_my_invitations_submenu(); ?></div>
    
	<div id="item-body" role="main">
		<?php do_action( 'bp_before_member_body' ); ?>
		<?php do_action( 'bp_template_content' ); 
			  do_action( 'bp_after_member_body' ); ?>
	</div><!-- #item-body -->
<?php
do_action( 'bp_after_member_plugin_template' );
}
