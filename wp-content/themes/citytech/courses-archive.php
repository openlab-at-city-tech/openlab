<?php /* Template Name: Courses Archive */

remove_action('genesis_post_title', 'genesis_do_post_title');
add_action('genesis_post_title', 'cuny_do_course_archive_title');
function cuny_do_course_archive_title() {
	echo '<h1 class="entry-title">Courses on the OpenLab</h1>';
}

remove_action('genesis_post_content', 'genesis_do_post_content');
add_action('genesis_post_content', 'cuny_course_archive' );
function cuny_course_archive() {
global $wpdb,$bp,$groups_template;
if ( !empty( $_GET['group_sequence'] ) ) {
	$sequence_type = "type=" . $_GET['group_sequence'] . "&";
}

$search_terms = '';

if ( !empty( $_POST['group_search'] ) ) {
	$search_terms="search_terms=".$_POST['group_search']."&";
}
if ( !empty( $_GET['search'] ) ){
	$search_terms="search_terms=".$_GET['search']."&";
}

if ( !empty( $_GET['school'] ) ) {
	$school=$_GET['school'];
	if($school=="tech"){
		$school="School of Technology & Design";
	}elseif($school=="studies"){
		$school="School of Professional Studies";
	}elseif($school=="arts"){
		$school="School of Arts & Sciences";
	}
}

if ( !empty( $_GET['department'] ) ) {
	$department=str_replace("-"," ",$_GET['department']);
	$department=ucwords($department);
}
if ( !empty( $_GET['semester'] ) ) {
	$semester=str_replace("-"," ",$_GET['semester']);
	$semester=explode(" ",$semester);
	$semester_season=ucwords($semester[0]);
	$semester_year=ucwords($semester[1]);
	$semester=ucwords($semester_season.' '.$semester_year);
}

if( !empty( $_GET['school'] ) && !empty( $_GET['department'] ) && !empty($_GET['semester']) ) {
	echo '<h3 id="bread-crumb">'.$school.'<span class="sep"> | </span>';
	echo $department.'<span class="sep"> | </span>'.$semester.'</h3>';
	$sql="SELECT a.group_id FROM {$bp->groups->table_name_groupmeta} a, {$bp->groups->table_name_groupmeta} b, {$bp->groups->table_name_groupmeta} c, {$bp->groups->table_name_groupmeta} d, {$bp->groups->table_name_groupmeta} e where a.group_id=b.group_id and a.group_id=c.group_id and a.group_id=d.group_id and a.group_id=e.group_id and a.meta_key='wds_group_type' and a.meta_value='course' and b.meta_key='wds_group_school' and b.meta_value like '%".$_GET['school']."%' and c.meta_key='wds_departments' and c.meta_value like '%".$department."%' and d.meta_key='wds_semester' and d.meta_value like '%".$semester_season."%' and e.meta_key='wds_year' and e.meta_value like '%".$semester_year."%'";
}
else if( !empty( $_GET['school'] ) && !empty( $_GET['department'] ) ) {
	echo '<h3 id="bread-crumb">'.$school.'<span class="sep"> | </span>';
	echo $department.'</h3>';
	$sql="SELECT a.group_id FROM {$bp->groups->table_name_groupmeta} a, {$bp->groups->table_name_groupmeta} b, {$bp->groups->table_name_groupmeta} c where a.group_id=b.group_id and a.group_id=c.group_id and a.meta_key='wds_group_type' and a.meta_value='course' and b.meta_key='wds_group_school' and b.meta_value like '%".$_GET['school']."%' and c.meta_key='wds_departments' and c.meta_value like '%".$department."%'";

}else if( !empty( $_GET['school'] ) && !empty( $_GET['semester'] ) ) {
	echo '<h3 id="bread-crumb">'.$school.'<span class="sep"> | </span>';
	echo $semester.'</h3>';
	$sql="SELECT a.group_id FROM {$bp->groups->table_name_groupmeta} a, {$bp->groups->table_name_groupmeta} b, {$bp->groups->table_name_groupmeta} c, {$bp->groups->table_name_groupmeta} d where a.group_id=b.group_id and a.group_id=c.group_id and a.group_id=d.group_id and a.meta_key='wds_group_type' and a.meta_value='course' and b.meta_key='wds_group_school' and c.meta_value like '%".$_GET['school']."%' and c.meta_key='wds_semester' and b.meta_value like '%".$semester_season."%' and d.meta_key='wds_year' and d.meta_value like '%".$semester_year."%'";
} elseif( !empty( $_GET['school'] ) ) {
	echo '<h3 id="bread-crumb">'.$school.'</h3>';
	$sql="SELECT a.group_id FROM {$bp->groups->table_name_groupmeta} a, {$bp->groups->table_name_groupmeta} b where a.group_id=b.group_id and a.meta_key='wds_group_type' and a.meta_value='course' and b.meta_key='wds_group_school' and b.meta_value like '%".$_GET['school']."%'";
} elseif( !empty( $_GET['semester'] ) ) {
	echo '<h3 id="bread-crumb">'.$semester.'</h3>';
	$sql="SELECT a.group_id FROM {$bp->groups->table_name_groupmeta} a, {$bp->groups->table_name_groupmeta} b, {$bp->groups->table_name_groupmeta} c where a.group_id=b.group_id and a.group_id=c.group_id and a.meta_key='wds_group_type' and a.meta_value='course' and b.meta_key='wds_semester' and b.meta_value like '%".$semester_season."%' and c.meta_key='wds_year' and c.meta_value like '%".$semester_year."%'";
}else{
	$sql="SELECT group_id FROM {$bp->groups->table_name_groupmeta} where meta_key='wds_group_type' and meta_value='course'";
}
$ids="9999999";
$rs = $wpdb->get_results( $sql );
foreach ( (array)$rs as $r ) $ids.= ",".$r->group_id;
if ( bp_has_groups( $sequence_type.$search_terms.'include='.$ids.'&per_page=12' ) ) : ?>
	<p class="group-count"><?php cuny_groups_pagination_count("Clubs"); ?></p>
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
					<?php
					$admins = groups_get_group_admins( $group_id );
//					echo "<br />Admins: <pre>";print_r($admins);echo "</pre>";
					$faculty_id = $admins[0]->user_id;
//					$faculty_id = $bp->groups->current_group->admins[0]->user_id;
					$first_name= ucfirst(xprofile_get_field_data( 'First Name', $faculty_id));
					$last_name= ucfirst(xprofile_get_field_data( 'Last Name', $faculty_id));
					$wds_faculty= $first_name . " " . $last_name;;
//					$wds_faculty=groups_get_groupmeta($group_id, 'wds_faculty' );
					$wds_course_code=groups_get_groupmeta($group_id, 'wds_course_code' );
					$wds_semester=groups_get_groupmeta($group_id, 'wds_semester' );
		  			$wds_year=groups_get_groupmeta($group_id, 'wds_year' );
		  			$wds_departments=groups_get_groupmeta($group_id, 'wds_departments' );
					?>
                    <div class="info-line"><?php echo $wds_faculty; ?> | <?php echo $wds_departments;?> | <?php echo $wds_course_code;?><br /> <?php echo $wds_semester;?> <?php echo $wds_year;?></div>
					<?php
					     $len = strlen(bp_get_group_description());
					     if ($len > 135) {
						$this_description = substr(bp_get_group_description(),0,135);
						$this_description = str_replace("</p>","",$this_description);
						echo $this_description.'&hellip; (<a href="'.bp_get_group_permalink().'">View More</a>)</p>';
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
		<?php _e('There are no courses to display.', 'buddypress') ?>
	</div>

<?php endif; ?>
		<?php

}

add_action('genesis_before_sidebar_widget_area', 'cuny_buddypress_courses_actions');
function cuny_buddypress_courses_actions() { ?>

<h2 class="sidebar-title">Find a Course</h2>
    <p>Narrow down your search using the filters or search box below.</p>
<?php    
//school filter
if ( empty( $_GET['school'] ) ) {
	$_GET['school'] = "active";
}
switch ($_GET['school']) {
	case "tech":
		$display_option_school = "School of Technology & Design";
		$option_value_school = "tech";
		break;
	case "studies":
		$display_option_school = "School of Professional Studies";
		$option_value_school = "studies";
		break;
	case "arts":
		$display_option_school = "School of Arts & Sciences";
		$option_value_school = "arts";
		break;
	default: 
		$display_option_school = "Select School";
		$option_value_school = "";
		break;
} 
    //departments
      if ( empty( $_GET['department'] ) ) {
	$_GET['department'] = "active";
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
	case "library":
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
	default: 
		$display_option_dept = "Select Department";
		$option_value_dept = "";
		break;
}
	//semesters
if ( empty( $_GET['semester'] ) ) {
	$_GET['semester'] = "active";
}
switch ($_GET['semester']) {
	case "fall-2011":
		$display_option_semester = "Fall 2011";
		$option_value_semester = "fall-2011";
		break;
	case "winter-2011":
		$display_option_semester = "Winter 2011";
		$option_value_semester = "winter-2011";
		break;
	case "spring-2011":
		$display_option_semester = "Spring 2011";
		$option_value_semester = "spring-2011";
		break;
	case "summer-2011":
		$display_option_semester = "Summer 2011";
		$option_value_semester = "summer-2011";
		break;
	case "fall-2012":
		$display_option_semester = "Fall 2012";
		$option_value_semester = "fall-2012";
		break;
	default: 
		$display_option_semester = "Select Semester";
		$option_value_semester = "";
		break;
} 
	//sequence filter
if ( empty( $_GET['group_sequence'] ) ) {
	$_GET['group_sequence'] = "active";
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
<div class="red-square"></div>
	<select name="school" class="last-select" onchange="showDept(this.value);">
		<option value="<?php echo $option_value_school; ?>"><?php echo $display_option_school; ?></option>
		<option value='tech'>School of Technology & Design</option>
		<option value='studies'>School of Professional Studies</option>
		<option value='arts'>School of Arts & Sciences</option>
	</select>
	<div class="red-square"></div>
	<select name="department" class="last-select" id="dept-select">
		<option value="<?php echo $option_value_dept; ?>"><?php echo $display_option_dept; ?></option>
		<?php include '/wp-content/themes/citytech/includes/department_processing.php'; ?>
	</select>
	<div class="red-square"></div>
	<select name="semester" class="last-select">
		<option value="<?php echo $option_value_semester; ?>"><?php echo $display_option_semester; ?></option>
		<option value='fall-2011'>Fall 2011</option>
		<option value='winter-2011'>Winter 2011</option>
		<option value='spring-2012'>Spring 2012</option>
		<option value='summer-2012'>Summer 2012</option>
		<option value='fall-2012'>Fall 2012</option>
	</select>
	<div class="red-square"></div>
	<select name="group_sequence" class="last-select">
		<option value="<?php echo $option_value; ?>"><?php echo $display_option; ?></option>
		<option value='alphabetical'>Alphabetical</option>
		<option value='newest'>Newest</option>
		<option value='active'>Last Active</option>
	</select>
	<input type="submit" onchange="document.forms['group_seq_form'].submit();" value="Filter">
	<!--<input type="reset" onchange="clear_form" value="Reset">-->
</form>
<div class="clearfloat"></div>
</div><!--filter-->
    <div class="archive-search">
    <div class="gray-square"></div>
    <form method="post">
    <input id="search-terms" type="text" name="group_search" value="Search" />
    <input id="search-submit" type="submit" name="group_search_go" value="Search" />
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
function showDept(str)
{
if (str=="")
  {
  document.getElementById("dept-select").innerHTML="";
  return;
  }
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("dept-select").innerHTML=xmlhttp.responseText;
    }
  }
  var $dom = document.domain;
xmlhttp.open("GET","http://"+$dom+"/wp-content/themes/citytech/includes/department_processing.php?q="+str,true);
xmlhttp.send();
}
function clear_form(){
	document.getElementById('group_seq_form').reset();
}
</script>
<?php }

genesis();