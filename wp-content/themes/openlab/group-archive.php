<?php
/* Template Name: Group Archive */
get_header();

$group_type  = openlab_page_slug_to_grouptype();
$can_create  = is_user_logged_in() && bp_user_can_create_groups();
$create_link = bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create/step/group-details/?type=' . $group_type . '&new=true';

if ( $group_type === 'course' ) {
	$can_create = openlab_user_can_create_courses();
} elseif ( $group_type === 'portfolio' ) {
	$can_create = false;
}
?>

<div id="content" class="hfeed row">
	<?php openlab_bp_sidebar('groups', true); ?>
	<div <?php post_class('col-sm-18 col-xs-24'); ?>>
		<div id="openlab-main-content" class="content-wrapper">
			<h1 class="entry-title"><?php echo ucfirst( $group_type ) . 's'; ?> on the OpenLab

			<?php if ( $can_create ) : ?>
				<span class="directory-title-meta pull-right hidden-xs">
					<i aria-hidden="true" class="fa fa-plus-circle"></i>
					<a href="<?php echo esc_attr( $create_link ); ?>">Create / Clone</a>
				</span>
			<?php endif; ?>

			<button data-target="#sidebar" data-backgroundonly="true" class="mobile-toggle direct-toggle pull-right visible-xs" type="button"><span class="fa fa-binoculars"></span><span class="sr-only">Search</span></button>
			</h1>

			<div class="entry-content">
				<?php bp_get_template_part( 'groups/groups-loop' ); ?>
			</div><!--entry-content-->
		</div><!--hentry-->
	</div>
</div><!-- #content -->

<?php
get_footer();
