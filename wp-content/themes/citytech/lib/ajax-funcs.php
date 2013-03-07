<?php //ajax based functions

/**
* This function process the department dropdown on the Courses archive page
*
*/
function openlab_ajax_return_course_list()
{
	if ( !wp_verify_nonce( $_GET['nonce'], "dept_select_nonce")) {
      exit("exit");
    }
	
	$school = $_GET['school']; 
	
	if ($school == "tech" || $option_value_school == "tech")
	{
	  $tech_depts = openlab_get_department_list('tech');
	  
	  $tech_list = '<option value="dept_all">All</option>';
	  
		foreach ($tech_depts as $tech_dept)
		{
			$display_option_dept = str_replace('And','&amp;',$tech_dept);
			$option_value_dept =  strtolower(str_replace(' ','-',$tech_dept));
			$tech_list .= '<option value="'.$option_value_dept.'">'.$display_option_dept.'</option>';
		}
		die($tech_list);
	} else if($school=="studies" || $option_value_school == "studies"){
	  
	  $tech_depts = openlab_get_department_list('studies');
	  
	  $tech_list = '<option value="dept_all">All</option>';
	  
		foreach ($tech_depts as $tech_dept)
		{
			$display_option_dept = str_replace('And','&amp;',$tech_dept);
			$option_value_dept =  strtolower(str_replace(' ','-',$tech_dept));
			$tech_list .= '<option value="'.$option_value_dept.'">'.$display_option_dept.'</option>';
		}
		die($tech_list);

	}else if ($school=="arts" || $option_value_school == "arts"){
	   
	  $tech_depts = openlab_get_department_list('arts');
	  
	  $tech_list = '<option value="dept_all">All</option>';
	  
		foreach ($tech_depts as $tech_dept)
		{
			$display_option_dept = str_replace('And','&amp;',$tech_dept);
			$option_value_dept =  strtolower(str_replace(' ','-',$tech_dept));
			$tech_list .= '<option value="'.$option_value_dept.'">'.$display_option_dept.'</option>';
		}
		die($tech_list);
	}
}

add_action( 'wp_ajax_nopriv_openlab_ajax_return_course_list', 'openlab_ajax_return_course_list' );  
add_action( 'wp_ajax_openlab_ajax_return_course_list', 'openlab_ajax_return_course_list' );  