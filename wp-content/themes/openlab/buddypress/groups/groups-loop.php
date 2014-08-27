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
<?php $group_type = $filters['wds_group_type']; ?>

	  	<?php
		if (openlab_is_my_profile()) {
			echo openlab_submenu_markup('groups', $filters['wds_group_type']);
		}
		?>
  
	<div class="row group-archive-header-row">
            <div class="group-count col-sm-5 pull-right"><?php cuny_groups_pagination_count($group_type); ?></div>
        </div>
	<div id="group-list" class="item-list row">
		<?php
		$count = 1;
		while ( bp_groups() ) : bp_the_group();
			$group_id = bp_get_group_id(); ?>
			<div class="group-item col-md-12">
                    <div class="group-item-wrapper">
                        <div class="row">
				<div class="item-avatar alignleft col-sm-8">
                                <a href="<?php bp_group_permalink() ?>"><img class="img-responsive" src ="<?php echo bp_core_fetch_avatar(array('item_id' => $group_id, 'object' => 'group', 'type' => 'full', 'html' => false)) ?>" alt="<?php echo $group->name; ?>"/></a>
                            </div>
				<div class="item col-sm-16">
					<h2 class="item-title"><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a></h2>
                                <?php
                                //course group type
                                echo $group_type;
                                if ($group_type == 'course'):
                                    ?>

                                    <?php
                                    $admins = groups_get_group_admins($group_id);
                                    $faculty_id = $admins[0]->user_id;
                                    $first_name = ucfirst(xprofile_get_field_data('First Name', $faculty_id));
                                    $last_name = ucfirst(xprofile_get_field_data('Last Name', $faculty_id));
                                    $wds_faculty = $first_name . " " . $last_name;
                                    $wds_course_code = groups_get_groupmeta($group_id, 'wds_course_code');
                                    $wds_semester = groups_get_groupmeta($group_id, 'wds_semester');
                                    $wds_year = groups_get_groupmeta($group_id, 'wds_year');
                                    $wds_departments = groups_get_groupmeta($group_id, 'wds_departments');
                                    ?>
                                    <div class="info-line uppercase"><?php echo $wds_faculty; ?> | <?php echo openlab_shortened_text($wds_departments, 20); ?> | <?php echo $wds_course_code; ?> | <span class="bold"><?php echo $wds_semester; ?> <?php echo $wds_year; ?></span></div>
                                <?php elseif ($group_type == 'portfolio'): ?>

                                    <div class="info-line"><?php echo bp_core_get_userlink(openlab_get_user_id_from_portfolio_group_id(bp_get_group_id())); ?></div>

                                <?php endif; ?>

                                <?php
                                $len = strlen(bp_get_group_description());
                                if ($len > 135) {
                                    $this_description = substr(bp_get_group_description(), 0, 135);
                                    $this_description = str_replace("</p>", "", $this_description);
                                    echo $this_description . '&hellip; <a href="' . bp_get_group_permalink() . '">See&nbsp;More</a></p>';
                                } else {
                                    bp_group_description();
                                }
                                ?>
				</div>

                        </div>
                    </div>
                        </div>
            <?php $count++ ?>
		<?php endwhile; ?>
        </div>

		<div class="pagination-links" id="group-dir-pag-top">
			<?php echo openlab_groups_pagination_links() ?>
		</div>
<?php else: ?>
	<?php $group_type = $filters['wds_group_type'].'s'; ?>
	  	<?php
		if (openlab_is_my_profile()) {
			echo openlab_submenu_markup($filters['wds_group_type']);
		}
		?>
	<div class="widget-error">
		<?php _e('There are no '.$group_type.' to display.', 'buddypress') ?>
	</div>

<?php endif; ?>

<?php $meta_filter->remove_filters(); ?>
