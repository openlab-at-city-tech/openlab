<?php /* Template Name: Portfolios Archive */

remove_action('genesis_post_title', 'genesis_do_post_title');
add_action('genesis_post_title', 'cuny_do_portfolio_archive_title');
function cuny_do_portfolio_archive_title() {
	echo '<h1 class="entry-title">Portfolios on the OpenLab</h1>';
}

remove_action('genesis_post_content', 'genesis_do_post_content');
add_action('genesis_post_content', 'cuny_portfolio_archive' );
function cuny_portfolio_archive() {
global $wpdb,$bp,$groups_template;
$sequence_type = '';
if ( !empty( $_GET['group_sequence'] ) ) {
	$sequence_type = "type=" . $_GET['group_sequence'] . "&";
}

$search_terms = $search_terms_raw = '';

if ( !empty( $_POST['group_search'] ) ) {
	$search_terms_raw = $_POST['group_search'];
	$search_terms     = "search_terms=" . $search_terms_raw . "&";
}
if ( !empty( $_GET['search'] ) ){
	$search_terms_raw = $_GET['search'];
	$search_terms     = "search_terms=" . $search_terms_raw . "&";
}

if ( !empty( $_GET['school'] ) ) {
	$school=$_GET['school'];
	/*if($school=="tech"){
		$school="Technology & Design";
	}elseif($school=="studies"){
		$school="Professional Studies";
	}elseif($school=="arts"){
		$school="Arts & Sciences";
	}*/
}

if ( !empty( $_GET['department'] ) ) {
	$department=str_replace("-"," ",$_GET['department']);
	$department=ucwords($department);
}

if ( !empty( $_GET['user_type'] ) ) {
	$semester = str_replace( "-", " " , $_GET['user_type'] );
}

// Set up filters
$filters = array(
	'wds_group_type' => 'portfolio'
);

if ( !empty( $school ) && 'school_all' != strtolower( $school ) ) {
	$filters['wds_group_school'] = $school;
}

if ( !empty( $department ) && 'dept_all' != strtolower( $department ) ) {
	$filters['wds_departments'] = $department;
}

if ( !empty( $_GET['user_type'] ) && 'user_type_all' != $_GET['user_type'] ) {
	$filters['portfolio_user_type'] = ucwords( $_GET['user_type'] );
}

$meta_filter = new BP_Groups_Meta_Filter( $filters );

$group_args = array(
	'search_terms' => $search_terms_raw,
	'per_page'     => 12,
);

if ( !empty( $_GET['group_sequence'] ) ) {
	$group_args['type'] = $_GET['group_sequence'];
}

?>

<div class="current-group-filters current-portfolio-filters">
	<?php openlab_current_directory_filters() ?>
</div>

<?php

if ( bp_has_groups( $group_args ) ) : ?>

	<div class="group-count"><?php cuny_groups_pagination_count( "Portfolios" ); ?></div>
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

					<div class="info-line"><?php echo bp_core_get_userlink( openlab_get_user_id_from_portfolio_group_id( bp_get_group_id() ) ) ?></div>

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

	<div class="widget-error">
		There are no portfolios to display.
	</div>

<?php endif; ?>
		<?php

	$meta_filter->remove_filters();
}

add_action('genesis_before_sidebar_widget_area', 'cuny_buddypress_courses_actions');
function cuny_buddypress_courses_actions() {
global $bp;?>

<h2 class="sidebar-title">Find a Portfolio</h2>
    <p>Narrow down your search using the filters or search box below.</p>
<?php
//determine class type for filtering
	  $school_color = "gray";
	  $dept_color = "gray";
	  $user_color = "gray";
	  $sort_color = "gray";

//school filter
if ( empty( $_GET['school'] ) ) {
	$_GET['school'] = "";
} else if ($_GET['school']=='school_all'){
  $_GET['school'] = "school_all";
  $school_color = "red";
} else {
  $school_color = "red";
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
    //departments
      if ( empty( $_GET['department'] ) ) {
	$_GET['department'] = "";
	  }else {
  	$dept_color = "red";
	}
switch ($_GET['department']) {
    //School of Technology and Design
	case "advertising-design-and-graphic-arts":
		$display_option_dept = "Advertising Design and Graphic Arts";
		$option_value_dept = "advertising-design-and-graphic-arts";
		break;
	case "architectural-technology":
		$display_option_dept = "Architectural Technology";
		$option_value_dept = "architectural-technology";
		break;
	case "computer-engineering-technology":
		$display_option_dept = "Computer Engineering Technology";
		$option_value_dept = "computer-engineering-technology";
		break;
	case "computer-systems-technology":
		$display_option_dept = "Computer Systems Technology";
		$option_value_dept = "computer-systems-technology";
		break;
	case "construction-management-and-civil-engineering-technology":
		$display_option_dept = "Construction Management and Civil Engineering Technology";
		$option_value_dept = "construction-management-and-civil-engineering-technology";
		break;
	case "electrical-and-telecommunications-engineering-technology":
		$display_option_dept = "Electrical and Telecommunications Engineering Technology";
		$option_value_dept = "electrical-and-telecommunications-engineering-technology";
		break;
	case "entertainment-technology":
		$display_option_dept = "Entertainment Technology";
		$option_value_dept = "entertainment-technology";
		break;
	case "environmental-control-technology":
		$display_option_dept = "Environmental Control Technology";
		$option_value_dept = "environmental-control-technology";
		break;
	case "mechanical-engineering-technology":
		$display_option_dept = "Mechanical Engineering Technology";
		$option_value_dept = "mechanical-engineering-technology";
		break;
    //School of Professional Studies
    case "business":
		$display_option_dept = "Business";
		$option_value_dept = "business";
		break;
	case "career-and-technology-teacher-education":
		$display_option_dept = "Career and Technology Teacher Education";
		$option_value_dept = "career-and-technology-teacher-education";
		break;
	case "dental-hygiene":
		$display_option_dept = "Dental Hygiene";
		$option_value_dept = "dental-hygiene";
		break;
	case "health-services-administration":
		$display_option_dept = "Health Services Administration";
		$option_value_dept = "health-services-administration";
		break;
	case "hospitality-management":
		$display_option_dept = "Hospitality Management";
		$option_value_dept = "hospitality-management";
		break;
	case "human-services":
		$display_option_dept = "Human Services";
		$option_value_dept = "human-services";
		break;
	case "law-and-paralegal-studies":
		$display_option_dept = "Law and Paralegal Studies";
		$option_value_dept = "law-and-paralegal-studies";
		break;
	case "nursing":
		$display_option_dept = "Nursing";
		$option_value_dept = "nursing";
		break;
	case "radiologic-technology-and-medical-imaging":
		$display_option_dept = "Radiologic Technology and Medical Imaging";
		$option_value_dept = "radiologic-technology-and-medical-imaging";
		break;
	case "restorative-dentistry":
		$display_option_dept = "Restorative Dentistry";
		$option_value_dept = "restorative-dentistry";
		break;
	case "vision-care-technology":
		$display_option_dept = "Vision Care Technology";
		$option_value_dept = "vision-care-technology";
		break;
		//School of Arts and Sciences
	case "african-american-studies":
		$display_option_dept = "African-American Studies";
		$option_value_dept = "african-american-studies";
		break;
	case "biological-sciences":
		$display_option_dept = "Biological Sciences";
		$option_value_dept = "biological-sciences";
		break;
	case "chemistry":
		$display_option_dept = "Chemistry";
		$option_value_dept = "chemistry";
		break;
	case "english":
		$display_option_dept = "English";
		$option_value_dept = "english";
		break;
	case "humanities":
		$display_option_dept = "Humanities";
		$option_value_dept = "humanities";
		break;
	case "library":
		$display_option_dept = "Library";
		$option_value_dept = "library";
		break;
	case "mathematics":
		$display_option_dept = "Mathematics";
		$option_value_dept = "mathematics";
		break;
	case "physics":
		$display_option_dept = "Physics";
		$option_value_dept = "physics";
		break;
	case "social-science":
		$display_option_dept = "Social Science";
		$option_value_dept = "social-science";
		break;
    case "dept_all":
		$display_option_dept = "All";
		$option_value_dept = "dept_all";
		break;
	default:
		$display_option_dept = "Select Department";
		$option_value_dept = "";
		break;
}
	//semesters
if ( empty( $_GET['user_type'] ) ) {
	$_GET['user_type'] = "";
}else {
  $user_color = "red";
}
switch ($_GET['user_type']) {

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

	default :
		$display_option_user_type = "Select User Type";
		$option_value_user_type = "";
		break;
}
	//sequence filter
if ( empty( $_GET['group_sequence'] ) ) {
	$_GET['group_sequence'] = "active";
}else {
  $sort_color = "red";
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

	<div id="tester">

	</div>
    <div class="<?php echo $school_color; ?>-square"></div>
	<select name="school" class="last-select <?php echo $school_color; ?>-text" onchange="showDept(this.value);">
		<option value="<?php echo $option_value_school; ?>"><?php echo $display_option_school; ?></option>
		<option value='school_all'>All</option>
		<option value='tech'>Technology &amp; Design</option>
		<option value='studies'>Professional Studies</option>
		<option value='arts'>Arts &amp; Sciences</option>
	</select>
	<div class="<?php echo $dept_color; ?>-square"></div>
	<select name="department" class="last-select <?php echo $dept_color; ?>-text" id="dept-select">
		<option value="<?php echo $option_value_dept; ?>"><?php echo $display_option_dept; ?></option>
        <?php $file_loc = dirname(__FILE__); ?>
		<?php include $file_loc.'/includes/department_processing.php'; ?>
	</select>
	<div class="<?php echo $user_color; ?>-square"></div>
	<select name="user_type" class="last-select <?php echo $user_color; ?>-text">
		<option value="<?php echo $option_value_user_type; ?>"><?php echo $display_option_user_type; ?></option>
		<option value='user_type_all'>All</option>
		<option value='student'>Student</option>
		<option value='faculty'>Faculty</option>
		<option value='staff'>Staff</option>
	</select>
	<div class="<?php echo $sort_color; ?>-square"></div>
	<select name="group_sequence" class="last-select <?php echo $sort_color; ?>-text">
		<option <?php selected( $option_value, 'alphabetical' ) ?> value='alphabetical'>Alphabetical</option>
		<option <?php selected( $option_value, 'newest' ) ?>  value='newest'>Newest</option>
		<option <?php selected( $option_value, 'active' ) ?> value='active'>Last Active</option>
	</select>
	<input type="button" value="Reset" onClick="window.location.href = '<?php echo $bp->root_domain ?>/portfolios/'">
	<input type="submit" onchange="document.forms['group_seq_form'].submit();" value="Submit">
</form>
<div class="clearfloat"></div>
</div><!--filter-->
    <div class="archive-search">
    <div class="gray-square"></div>
    <form method="get">
    <input id="search-terms" type="text" name="search" placeholder="Search" />
    <input id="search-submit" type="submit" value="Search" />
    </form>
    <div class="clearfloat"></div>
    </div><!--archive search-->
<?php

function slug_maker($full_string)
{
 $slug_val = str_replace(" ","-",$full_string);
 $slug_val = strtolower($slug_val);
 return $slug_val;
} ?>
<script type="text/javascript">

function showDept(str) {
	if (str=="") {
	  document.getElementById("dept-select").innerHTML="";
	  return;
	}

	if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else {
		// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			if ( navigator.appName == 'Microsoft Internet Explorer' ) {
				var dropdown = '<select name="department" class="last-select" id="dept-select">' + xmlhttp.responseText + '</select>';
				document.getElementById('dept-select').outerHTML = dropdown;
			} else {
				document.getElementById("dept-select").innerHTML = "" + xmlhttp.responseText + "";
			}
		}
	}

	var dom = document.domain;
	xmlhttp.open("GET","http://" + dom + "/wp-content/themes/citytech/includes/department_processing.php?q="+str,true);
	xmlhttp.send();
}
function clear_form(){
	document.getElementById('group_seq_form').reset();
}
</script>
<?php }

genesis();