<?php
        $school = $_GET["q"]; 
        if ($school == "tech" || $option_value_school == "tech")
	    {
		echo "<option value='dept_all'>All</option>
		<option value='advertising-design-and-graphic-arts'>Advertising Design and Graphic Arts</option>
		<option value='architectural-technology'>Architectural Technology</option>
		<option value='computer-engineering-technology'>Computer Engineering Technology</option>
		<option value='computer-systems-technology'>Computer Systems Technology</option>
		<option value='construction-management-and-civil-engineering-technology'>Construction Management and Civil Engineering Technology</option>
		<option value='electrical-and-telecommunications-engineering-technology'>Electrical and Telecommunications Engineering Technology</option>
		<option value='entertainment-technology'>Entertainment Technology</option>
		<option value='environmental-control-technology'>Environmental Control Technology</option>
		<option value='mechanical-engineering-technology'>Mechanical Engineering Technology</option>";
		} else if($school=="studies" || $option_value_school == "studies"){
		echo "<option value='dept_all'>All</option>
		<option value='business'>Business</option>
        <option value='career-and-technology-teacher-education'>Career and Technology Teacher Education</option>
        <option value='dental-hygiene'>Dental Hygiene</option>
        <option value='health-services-administration'>Health Services Administration</option>
        <option value='hospitality-management'>Hospitality Management</option>
        <option value='human-services'>Human Services</option>
        <option value='law-and-paralegal-studies'>Law and Paralegal Studies</option>
        <option value='nursing'>Nursing</option>
        <option value='radiologic-technology-and-medical-imaging'>Radiologic Technology and Medical Imaging</option>
        <option value='restorative-dentistry'>Restorative Dentistry</option>
        <option value='vision-care-technology'>Vision Care Technology</option>";
		}else if ($school=="arts" || $option_value_school == "arts"){
		echo "<option value='dept_all'>All</option>
		<option value='african-american-studies'>African-American Studies</option>
         <option value='biological-sciences'>Biological Sciences</option>
         <option value='chemistry'>Chemistry</option>
         <option value='english'>English</option>
         <option value='humanities'>Humanities</option>
         <option value='library'>Library</option>
         <option value='mathematics'>Mathematics</option>
         <option value='physics'>Physics</option>
         <option value='social-science'>Social Science</option>";
		}else if ($school=="school_all" || $option_value_school == "school_all")
		{
		echo "<option value=''>Select Department</option>";
		}