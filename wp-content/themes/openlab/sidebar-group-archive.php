<?php
global $bp, $wp_query;
$post_obj = $wp_query->get_queried_object();
$group_type = openlab_page_slug_to_grouptype();
$group_slug = $group_type . 's';

//conditional for people archive sidebar
if ($group_type == 'not-archive' && $post_obj->post_title == "People") {
    $group_type = "people";
    $group_slug = $group_type;
    $sidebar_title = 'Find People';
} else {
    $sidebar_title = 'Find a ' . ucfirst($group_type);
}
?>

<h2 class="sidebar-title"><?php echo $sidebar_title; ?></h2>
<div class="sidebar-block">
    <?php
//determine class type for filtering
    $school_color = "passive";
    $dept_color = "passive";
    $semester_color = "passive";
    $sort_color = "passive";
    $user_color = "passive";

//school filter - easiest to do this with a switch statment
    if (empty($_GET['school'])) {
        $_GET['school'] = "";
    } else if ($_GET['school'] == 'school_all') {
        $_GET['school'] = "school_all";
        $school_color = "active";
    } else {
        $school_color = "active";
    }
    switch ($_GET['school']) {
        case "tech":
            $display_option_school = "Technology & Design";
            $option_value_school = "tech";
            break;
        case "studies":
            $display_option_school = "Professional Studies";
            $option_value_school = "studies";
            break;
        case "arts":
            $display_option_school = "Arts & Sciences";
            $option_value_school = "arts";
            break;
        case "school_all":
            $display_option_school = "All";
            $option_value_school = "school_all";
            break;
        default:
            $display_option_school = "Select School";
            $option_value_school = "";
            break;
    }
//processing the department value - now dynamic instead of a switch statement
    if (empty($_GET['department'])) {
        $display_option_dept = "Select Department";
        $option_value_dept = "";
    } else if ($_GET['department'] == 'dept_all') {
        $display_option_dept = "All";
        $option_value_dept = "dept_all";
    } else {
        $dept_color = "active";
        $display_option_dept = ucwords(str_replace('-', ' ', $_GET['department']));
        $display_option_dept = str_replace('And', '&', $display_option_dept);
        $option_value_dept = $_GET['department'];
    }

    //categories
    if (empty($_GET['cat'])) {
        $display_option_bpcgc = "Select Category";
        $option_value_bpcgc = "";
    } else if ($_GET['cat'] == 'cat_all') {
        $display_option_bpcgc = "All";
        $option_value_bpcgc = "cat_all";
    } else {
        $dept_color = "active";
        $display_option_bpcgc = ucwords(str_replace('-', ' ', $_GET['cat']));
        $display_option_bpcgc = str_replace('And', '&', $display_option_bpcgc);
        $option_value_bpcgc = $_GET['cat'];
    }

//semesters
    if (empty($_GET['semester'])) {
        $_GET['semester'] = "";
    } else {
        $semester_color = "active";
    }
//processing the semester value - now dynamic instead of a switch statement
    if (empty($_GET['semester'])) {
        $display_option_semester = "Select Semester";
        $option_value_semester = "";
    } else if ($_GET['semester'] == 'semester_all') {
        $display_option_semester = "All";
        $option_value_semester = "semester_all";
    } else {
        $dept_color = "active";
        $display_option_semester = ucfirst(str_replace('-', ' ', $_GET['semester']));
        $option_value_semester = $_GET['semester'];
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
        <p>Narrow down your search using the filters or search box below.</p>
        <form id="group_seq_form" name="group_seq_form" action="#" method="get">
            <div id="sidebarCustomSelect" class="custom-select-parent">
                <div class="custom-select" id="schoolSelect">
                    <select name="school" class="last-select <?php echo $school_color; ?>-text" id="school-select" tabindex="0">
                        <option value="" <?php selected('', $option_value_school) ?>>Select School</option>
                        <option value='school_all' <?php selected('school_all', $option_value_school) ?>>All Schools</option>
                        <option value='tech' <?php selected('tech', $option_value_school) ?>>Technology &amp; Design</option>
                        <option value='studies' <?php selected('studies', $option_value_school) ?>>Professional Studies</option>
                        <option value='arts' <?php selected('arts', $option_value_school) ?>>Arts & Sciences</option>
                    </select>
                </div>

                <div class="hidden" id="nonce-value"><?php echo wp_create_nonce("dept_select_nonce"); ?></div>
                <div class="custom-select">
                    <select name="department" class="last-select processing <?php echo $dept_color; ?>-text" id="dept-select" <?php disabled('', $option_value_school) ?>>
                        <?php echo openlab_return_course_list($option_value_school, $option_value_dept); ?>
                    </select>
                </div>

                <?php if (function_exists('bpcgc_get_terms_by_group_type')): ?>
                    <?php if ($group_type === 'project' || $group_type === 'club'): ?>

                        <?php $group_terms = bpcgc_get_terms_by_group_type($group_type); ?>

                        <?php if ($group_terms && !empty($group_terms)): ?>

                            <div class="custom-select">
                                <select name="cat" class="last-select <?php echo $bpcgc_color; ?>-text" id="bp-group-categories-select">
                                    <option value="" <?php selected('', $option_value_bpcgc) ?>>Select Category</option>
                                    <option value='cat_all' <?php selected('cat_all', $option_value_bpcgc) ?>>All</option>
                                    <?php foreach ($group_terms as $term) : ?>
                                        <option value="<?php echo $term->slug ?>" <?php selected($option_value_bpcgc, $term->slug) ?>><?php echo $term->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        <?php endif; ?>
                    <?php endif; ?>

                <?php endif;
                ?>

                <?php // @todo figure out a way to make this dynamic ?>
                <?php if ($group_type == 'course'): ?>
                    <div class="custom-select">
                        <select name="semester" class="last-select <?php echo $semester_color; ?>-text">
                            <option value='' <?php selected('', $option_value_semester) ?>>Select Semester</option>
                            <option value='semester_all' <?php selected('semester_all', $option_value_semester) ?>>All</option>
                            <?php foreach (openlab_get_active_semesters() as $sem) : ?>
                                <option value="<?php echo esc_attr($sem['option_value']) ?>" <?php selected($option_value_semester, $sem['option_value']) ?>><?php echo esc_attr($sem['option_label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <?php if ($group_type == 'portfolio' || $post_obj->post_title == 'People'): ?>
                    <div class="custom-select">
                        <select name="usertype" class="last-select <?php echo $user_color; ?>-text">
                            <option value='' <?php selected('', $option_value_user_type) ?>>Select User Type</option>
                            <option value='student' <?php selected('student', $option_value_user_type) ?>>Student</option>
                            <option value='faculty' <?php selected('faculty', $option_value_user_type) ?>>Faculty</option>
                            <option value='staff' <?php selected('staff', $option_value_user_type) ?>>Staff</option>
                            <option value='alumni' <?php selected('alumni', $option_value_user_type) ?>>Alumni</option>
                            <option value='user_type_all' <?php selected('user_type_all', $option_value_user_type) ?>>All</option>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="custom-select">
                    <select name="group_sequence" class="last-select <?php echo $sort_color; ?>-text">
                        <option <?php selected($option_value, 'alphabetical') ?> value='alphabetical'>Alphabetical</option>
                        <option <?php selected($option_value, 'newest') ?>  value='newest'>Newest</option>
                        <option <?php selected($option_value, 'active') ?> value='active'>Last Active</option>
                    </select>
                </div>

            </div>
            <input class="btn btn-primary" type="submit" onchange="document.forms['group_seq_form'].submit();" value="Submit">
            <input class="btn btn-default" type="button" value="Reset" onClick="window.location.href = '<?php echo $bp->root_domain ?>/<?php echo $group_slug; ?>/'">
        </form>

        <div class="archive-search">
            <h3 class="bold font-size font-14">Search</h3>
            <form method="get" class="form-inline btn-combo" role="form">
                <div class="form-group">
                    <input id="search-terms" class="form-control" type="text" name="search" placeholder="Enter keyword" /><label class="sr-only" for="search-terms">Enter keyword</label><button class="btn btn-primary top-align" id="search-submit" type="submit"><i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">Search</span></button>
                </div>
            </form>
            <div class="clearfloat"></div>
        </div><!--archive search-->
    </div><!--filter-->
</div>
<?php

function slug_maker($full_string) {
    $slug_val = str_replace(" ", "-", $full_string);
    $slug_val = strtolower($slug_val);
    return $slug_val;
}
