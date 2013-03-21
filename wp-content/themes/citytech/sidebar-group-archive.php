<?php global $bp, $wp_query;
	  $post_obj = $wp_query->get_queried_object();
	  $group_type = openlab_page_slug_to_grouptype();
	  $group_slug = $group_type.'s';
	  
	  //conditional for people archive sidebar
	  if ($group_type == 'not-archive' && $post_obj->post_title == "People")
	  {
		  $group_type = "people";
		  $group_slug = $group_type;
	  }
	  
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

//school filter - easiest to do this with a switch statment
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
//processing the department value - now dynamic instead of a switch statement
if ( empty( $_GET['department'] ) ) {
  $display_option_dept = "Select Department";
  $option_value_dept = "";
}else if ($_GET['department'] == 'dept_all') {
  $display_option_dept = "All";
  $option_value_dept = "dept_all";
} else{
  $dept_color = "red";
  $display_option_dept = ucwords(str_replace('-',' ',$_GET['department']));
  $display_option_dept = str_replace('And','&amp;',$display_option_dept);
  $option_value_dept = $_GET['department'];
}


//semesters
if ( empty( $_GET['semester'] ) ) {
	$_GET['semester'] = "";
}else {
  $semester_color = "red";
}
//processing the semester value - now dynamic instead of a switch statement
if ( empty( $_GET['semester'] ) ) {
  $display_option_semester = "Select Semester";
  $option_value_semester = "";
}else if ($_GET['semester'] == 'semester_all') {
  $display_option_semester = "All";
  $option_value_semester = "semester_all";
} else{
  $dept_color = "red";
  $display_option_dept = ucfirst(str_replace('-',' ',$_GET['semester']));
  $option_value_dept =  $_GET['semester'];
}

//user types - for people archive page
if ( empty( $_GET['usertype'] ) ) {
	$_GET['usertype'] = "";
}else {
  $user_color = "red";
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
	<select name="school" class="last-select <?php echo $school_color; ?>-text" id="school-select">
		<option value="<?php echo $option_value_school; ?>"><?php echo $display_option_school; ?></option>
		<option value='school_all'>All</option>
		<option value='tech'>Technology &amp; Design</option>
		<option value='studies'>Professional Studies</option>
		<option value='arts'>Arts &amp; Sciences</option>
	</select>
	<div class="<?php echo $school_color; ?>-square"></div>
    <div class="hidden" id="nonce-value"><?php echo wp_create_nonce("dept_select_nonce"); ?></div>
	<select name="department" class="last-select <?php echo $dept_color; ?>-text" id="dept-select">
		<option value="<?php echo $option_value_dept; ?>"><?php echo $display_option_dept; ?></option>
	</select>
    <?php endif; ?>
    
    <?php //@to-do figure out a way to make this dynamic ?>
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
        <option value='winter-2013'>Winter 2013</option>
	</select>
    <?php endif; ?>
    <?php if ($group_type == 'portfolio' || $post_obj->post_title == 'People'): ?>
    <div class="<?php echo $user_color; ?>-square"></div>
	<select name="usertype" class="last-select <?php echo $user_color; ?>-text">
		<option value="<?php echo $option_value_user_type; ?>"><?php echo $display_option_user_type; ?></option>
		<option value='student'>Student</option>
		<option value='faculty'>Faculty</option>
		<option value='staff'>Staff</option>
        <option value='user_type_all'>All</option>
	</select>
    <?php endif; ?>
    
    <?php //for clubs and projects pages, which will process the filter without a submit button
		  $on_change = "";
		  if ($group_type == 'project' || $group_type == 'club')
		  {
			  $on_change = 'onchange="document.forms[\'group_seq_form\'].submit();"';
		  }
	?>
	
    <?php if ($group_type == 'portfolio' || $group_type == 'course'): ?>
      <div class="<?php echo $school_color; ?>-square"></div>
	<select name="group_sequence" class="last-select <?php echo $sort_color; ?>-text">
		<option <?php selected( $option_value, 'alphabetical' ) ?> value='alphabetical'>Alphabetical</option>
		<option <?php selected( $option_value, 'newest' ) ?>  value='newest'>Newest</option>
		<option <?php selected( $option_value, 'active' ) ?> value='active'>Last Active</option>
	</select>
      <input type="button" value="Reset" onClick="window.location.href = '<?php echo $bp->root_domain ?>/<?php echo $group_slug; ?>/'">
      <input type="submit" onchange="document.forms['group_seq_form'].submit();" value="Submit">
    <?php else: ?>
       <form id="group_seq_form" name="group_seq_form" action="#" method="get">
	<div class="<?php echo $square_class; ?>"></div>
	<select name="group_sequence" onchange="document.forms['group_seq_form'].submit();" class="last-select <?php echo $select_class; ?>">
		<option <?php selected( $option_value, 'alphabetical' ) ?> value='alphabetical'>Alphabetical</option>
		<option <?php selected( $option_value, 'newest' ) ?>  value='newest'>Newest</option>
		<option <?php selected( $option_value, 'active' ) ?> value='active'>Last Active</option>
	</select>
        </form>
    <?php endif; ?>
    
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
}