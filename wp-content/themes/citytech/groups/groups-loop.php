<?php
/**
 * Group loop
 *
 * @todo All the other group templates (my-*.php as well as *-archive.php) should at some point
 *       be refactored to include this file instead. Filter stuff will probably have to be
 *       abstracted
 */
// Set up the group meta filters
global $bp;

$filters = array();
if ( bp_is_user_groups() ) {
	if ( isset( $_GET['type'] ) ) {
		$filters['wds_group_type'] = $_GET['type'];
	}

	// Set up the bp_has_groups() args: per_page, page, search_terms
	$group_args = array(
		'per_page' => 12
	);

} else {
	//geting the grouptype by slug - the archive pages are curently WP pages and don't have a specific grouptype associated with them - this function uses the curent page slug to assign a grouptype
	$filters['wds_group_type'] = openlab_page_slug_to_grouptype();

	$group_args = array(
	'per_page'		=> 12,
	'show_hidden'	=> true,
	'user_id'		=> $bp->loggedin_user->id
	);
}

$meta_filter = new BP_Groups_Meta_Filter( $filters );

// @todo
if ( !empty( $search_terms_raw ) ) {
	$group_args['search_terms'] = $search_terms_raw;
}

if ( !empty( $_GET['group_sequence'] ) ) {
	$group_args['type'] = $_GET['group_sequence'];
}
?>

<?php if ( bp_has_groups( $group_args ) ) : ?>
<?php $group_type = ucfirst($filters['wds_group_type']).'s'; ?>

	<div class="submenu">
	  	<?php
		if (openlab_is_my_profile()) {
			echo openlab_my_groups_submenu($filters['wds_group_type']);
		}
		?>
	<div class="group-count"><?php cuny_groups_pagination_count($group_type); ?></div>
    </div><!--submenu-->
	<div class="clearfloat"></div>
	<ul id="course-list" class="item-list">
		<?php
		$count = 1;
		while ( bp_groups() ) : bp_the_group();
			$group_id=bp_get_group_id();?>
			<li class="course<?php echo cuny_o_e_class($count) ?>">
				<div class="item-avatar alignleft">
					<a href="<?php bp_group_permalink() ?>"><?php echo bp_get_group_avatar(array( 'type' => 'full', 'width' => 100, 'height' => 100 )) ?></a>
				</div>
				<div class="item">
					<h2 class="item-title"><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a></h2>

                    <?php	if ($filters['wds_group_type'] == "course") :
					$wds_faculty=groups_get_groupmeta($group_id, 'wds_faculty' );
					$wds_course_code=groups_get_groupmeta($group_id, 'wds_course_code' );
					$wds_semester=groups_get_groupmeta($group_id, 'wds_semester' );
		  			$wds_year=groups_get_groupmeta($group_id, 'wds_year' );
		  			$wds_departments=groups_get_groupmeta($group_id, 'wds_departments' );
					?>
                    <div class="info-line">
					<?php

					// $wds_faculty is used only as a "faculty exists" check.
					if ( $wds_faculty ) {
						echo bp_core_get_user_displayname( bp_get_group_creator_id() );
					}
					if ($wds_departments){
						echo ' | '.$wds_departments;
					}
					if ( ! empty( $wds_project_code ) ){
						echo $wds_project_code;
					}
					if ($wds_semester || $wds_year)
					{
						echo '<br />';
						if ($wds_semester)
						{
							echo $wds_semester.' ';
						}
						if ($wds_year)
						{
							echo $wds_year;
						}
					} ?>
                    </div>
					<?php else: ?>

                    <div class="info-line"><?php echo bp_core_get_userlink( openlab_get_user_id_from_portfolio_group_id( bp_get_group_id() ) ) ?></div>

                    <?php endif; ?>

					<?php
					     $len = strlen(bp_get_group_description());
					     if ($len > 135) {
						$this_description = substr(bp_get_group_description(),0,135);
						$this_description = str_replace("</p>","",$this_description);
						echo $this_description.'&hellip; <a href="'.bp_get_group_permalink().'">See&nbsp;More</a></p>';
					     } else {
						bp_group_description();
					     }
					?>
				</div>

			</li>
			<?php if ( $count % 2 == 0 ) { echo '<hr style="clear:both;" />'; } ?>
			<?php $count++ ?>
		<?php endwhile; ?>
	</ul>

		<div class="pagination-links" id="group-dir-pag-top">
			<?php bp_groups_pagination_links() ?>
		</div>
<?php else: ?>
	<?php $group_type = $filters['wds_group_type'].'s'; ?>
        <div class="submenu">
	  	<?php
		if (openlab_is_my_profile()) {
			echo openlab_my_groups_submenu($filters['wds_group_type']);
		}
		?>
    </div><!--submenu-->
	<div class="widget-error">
		<?php _e('There are no '.$group_type.' to display.', 'buddypress') ?>
	</div>

<?php endif; ?>

<?php $meta_filter->remove_filters(); ?>
