<?php global $bp; 
	  $group_type = openlab_page_slug_to_grouptype();
	  ?>

<h2 class="sidebar-title">Find a <?php echo ucfirst($group_type); ?></h2>
    <p>Narrow down your search using the filters or search box below.</p>
<?php
//determine class type for filtering
	  $school_color = "gray";
	  $dept_color = "gray";
	  $semester_color = "gray";
	  $sort_color = "gray";
	  $user_color = "gray";

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
		$display_option_dept = "Advertising Design &amp; Graphic Arts";
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
		$display_option_dept = "Construction Management &amp; Civil Engineering Technology";
		$option_value_dept = "construction-management-and-civil-engineering-technology";
		break;
	case "electrical-and-telecommunications-engineering-technology":
		$display_option_dept = "Electrical &amp; Telecommunications Engineering Technology";
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
		$display_option_dept = "Career &amp; Technology Teacher Education";
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
		$display_option_dept = "Law &amp; Paralegal Studies";
		$option_value_dept = "law-and-paralegal-studies";
		break;
	case "nursing":
		$display_option_dept = "Nursing";
		$option_value_dept = "nursing";
		break;
	case "radiologic-technology-and-medical-imaging":
		$display_option_dept = "Radiologic Technology &amp; Medical Imaging";
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
if ( empty( $_GET['semester'] ) ) {
	$_GET['semester'] = "";
}else {
  $semester_color = "red";
}
switch ($_GET['semester']) {
	case "fall-2011":
		$display_option_semester = "Fall 2011";
		$option_value_semester = "fall-2011";
		break;
	case "winter-2012":
		$display_option_semester = "Winter 2012";
		$option_value_semester = "winter-2012";
		break;
	case "spring-2012":
		$display_option_semester = "Spring 2012";
		$option_value_semester = "spring-2012";
		break;
	case "summer-2012":
		$display_option_semester = "Summer 2012";
		$option_value_semester = "summer-2012";
		break;
	case "fall-2012":
		$display_option_semester = "Fall 2012";
		$option_value_semester = "fall-2012";
		break;
	case "winter-2013":
		$display_option_semester = "Winter 2013";
		$option_value_semester = "winter-2013";
		break;
	case "spring-2013":
		$display_option_semester = "Spring 2013";
		$option_value_semester = "spring-2013";
		break;
	case "summer-2013":
		$display_option_semester = "Summer 2013";
		$option_value_semester = "summer-2013";
		break;
	case "fall-2013":
		$display_option_semester = "Fall 2013";
		$option_value_semester = "fall-2013";
		break;
	case "semester_all":
		$display_option_semester = "All";
		$option_value_semester = "semester_all";
		break;
	default:
		$display_option_semester = "Select Semester";
		$option_value_semester = "";
		break;
}
//user types
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
    <?php if ($group_type == 'course' || $group_type == 'portfolio'): ?>
    <div class="<?php echo $school_color; ?>-square"></div>
	<select name="school" class="last-select <?php echo $school_color; ?>-text" onchange="showDept(this.value);">
		<option value="<?php echo $option_value_school; ?>"><?php echo $display_option_school; ?></option>
		<option value='school_all'>All</option>
		<option value='tech'>Technology &amp; Design</option>
		<option value='studies'>Professional Studies</option>
		<option value='arts'>Arts &amp; Sciences</option>
	</select>
	<div class="<?php echo $school_color; ?>-square"></div>
	<select name="department" class="last-select <?php echo $dept_color; ?>-text" id="dept-select">
		<option value="<?php echo $option_value_dept; ?>"><?php echo $display_option_dept; ?></option>
        <?php $file_loc = dirname(__FILE__); ?>
		<?php include $file_loc.'/includes/department_processing.php'; ?>
	</select>
    <?php endif; ?>
    <?php if ($group_type == 'course'): ?>
	<div class="<?php echo $school_color; ?>-square"></div>
	<select name="semester" class="last-select <?php echo $semester_color; ?>-text">
		<option value="<?php echo $option_value_semester; ?>"><?php echo $display_option_semester; ?></option>
		<option value='semester_all'>All</option>
		<option value='fall-2011'>Fall 2011</option>
		<option value='winter-2012'>Winter 2012</option>
		<option value='spring-2012'>Spring 2012</option>
		<option value='summer-2012'>Summer 2012</option>
		<option value='fall-2012'>Fall 2012</option>
        <option value='winter-2013'>Winter 2013</option>
        <option value='spring-2013'>Spring 2013</option>
        <option value='summer-2013'>Summer 2013</option>
        <option value='fall-2013'>Fall 2013</option>
	</select>
    <?php endif; ?>
    <?php if ($group_type == 'portfolio'): ?>
    <div class="<?php echo $user_color; ?>-square"></div>
	<select name="user_type" class="last-select <?php echo $user_color; ?>-text">
		<option value="<?php echo $option_value_user_type; ?>"><?php echo $display_option_user_type; ?></option>
		<option value='user_type_all'>All</option>
		<option value='student'>Student</option>
		<option value='faculty'>Faculty</option>
		<option value='staff'>Staff</option>
	</select>
    <?php endif; ?>
	<div class="<?php echo $school_color; ?>-square"></div>
	<select name="group_sequence" class="last-select <?php echo $sort_color; ?>-text">
		<option <?php selected( $option_value, 'alphabetical' ) ?> value='alphabetical'>Alphabetical</option>
		<option <?php selected( $option_value, 'newest' ) ?>  value='newest'>Newest</option>
		<option <?php selected( $option_value, 'active' ) ?> value='active'>Last Active</option>
	</select>
	<input type="button" value="Reset" onClick="window.location.href = '<?php echo $bp->root_domain ?>/<?php echo $group_type; ?>s/'">
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