<?php
$group_type = openlab_get_group_directory_group_type();
$group_slug = $group_type . 's';

$is_people = 'people' === get_queried_object()->post_name;

//conditional for people archive sidebar
if ( $is_people ) {
    $group_type = "people";
    $group_slug = $group_type;
    $sidebar_title = 'Find People';
} else {
    $sidebar_title = 'Find a ' . ucfirst( $group_type );
}
?>

<h2 class="sidebar-title"><?php echo $sidebar_title; ?></h2>
<div class="sidebar-block">
    <?php

	if ( ! empty( $_GET['group_badge'] ) ) {
		$badge_color = 'active';
		$badge_value = wp_unslash( $_GET['group_badge'] );
	} else {
		$badge_value = '';
	}

//user types - for people archive page
    if (empty($_GET['usertype'])) {
        $_GET['usertype'] = "";
    } else {
        $user_color = "active";
    }
    switch ($_GET['usertype']) {

        case "student" :
            $display_option_user_type = "Student";
            $option_value_user_type = "student";
            break;
        case "faculty" :
            $display_option_user_type = "Faculty";
            $option_value_user_type = "faculty";
            break;
        case "staff" :
            $display_option_user_type = "Staff";
            $option_value_user_type = "staff";
            break;
        case 'alumni' :
            $display_option_user_type = 'Alumni';
            $option_value_user_type = 'alumni';
        case "user_type_all":
            $display_option_user_type = "All";
            $option_value_user_type = "user_type_all";
            break;
        default:
            $display_option_user_type = "Select User Type";
            $option_value_user_type = "";
            break;
    }
//sequence filter - easy enough to keep this as a switch for now
    if (empty($_GET['group_sequence'])) {
        $_GET['group_sequence'] = "active";
    } else {
        $sort_color = "active";
    }
    switch ($_GET['group_sequence']) {
        case "alphabetical":
            $display_option = "Alphabetical";
            $option_value = "alphabetical";
            break;
        case "newest":
            $display_option = "Newest";
            $option_value = "newest";
            break;
        case "active":
            $display_option = "Last Active";
            $option_value = "active";
            break;
        default:
            $display_option = "Order By";
            $option_value = "";
            break;
    }
    ?>
    <div class="filter">
        <form id="group_seq_form" name="group_seq_form" action="#" method="get">
			<?php get_template_part( 'parts/sidebar/filter-search' ); ?>

            <div id="sidebarCustomSelect" class="custom-select-parent">
				<p>Narrow down your results using some of the filters below.</p>

				<div class="custom-select" id="schoolSelect">
					<?php
					$school_filter_type = 'course' === $group_type ? 'school' : 'school-office';
					set_query_var( 'academic_unit_type', $school_filter_type );
					get_template_part( 'parts/sidebar/filter-academic-unit' );
					?>
				</div>

				<div class="custom-select" id="departmentSelect">
					<?php
					set_query_var( 'academic_unit_type', 'department' );
					get_template_part( 'parts/sidebar/filter-academic-unit' );
					?>
				</div>

				<?php if ( function_exists( 'bpcgc_get_terms_by_group_type' ) ) :  ?>
					<?php get_template_part( 'parts/sidebar/filter-group-categories' ); ?>
				<?php endif; ?>

				<?php if ( $group_type == 'course' ) : ?>
					<?php get_template_part( 'parts/sidebar/filter-term' ); ?>
				<?php endif; ?>

				<?php if ( $group_type === 'portfolio' || $is_people ) : ?>
					<?php get_template_part( 'parts/sidebar/filter-member-type' ); ?>
				<?php endif; ?>

				<?php get_template_part( 'parts/sidebar/filter-sort' ); ?>

				<?php if ( ! $is_people ) : ?>
					<?php get_template_part( 'parts/sidebar/filter-open-cloneable' ); ?>
					<?php get_template_part( 'parts/sidebar/filter-badges' ); ?>
				<?php endif; ?>
            </div>

			<div class="sidebar-buttons">
				<input class="btn btn-primary" type="submit" onchange="document.forms['group_seq_form'].submit();" value="Submit">
				<input class="btn btn-default" type="button" value="Reset" onClick="window.location.href = '<?php echo esc_html( home_url( $group_slug ) )?>'">
			</div>
        </form>
    </div><!--filter-->
</div>
<?php

function slug_maker($full_string) {
    $slug_val = str_replace(" ", "-", $full_string);
    $slug_val = strtolower($slug_val);
    return $slug_val;
}
