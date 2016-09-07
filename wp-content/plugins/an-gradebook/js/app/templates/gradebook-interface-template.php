<script id="gradebook-interface-template" type="text/template">
<div class="container-fluid">
    <div class="row">
    	<div class="col-md-12 wrap">
    		<h1>GradeBook:  <%= course.get('name')%></h1>
    	</div>	
    </div>
    <div class="row">
    	<div class="col-md-12">  
		<?php 
			global $current_user;  
			$x = $current_user->roles;
			$y = array_keys(get_option('an_gradebook_settings'),true);
			$z = array_intersect($x,$y);
			if( count($z) ){
		?>    	 
			<div class="btn-group">    		
				<button type="button" id="add-student" class="btn btn-default">Add Student</button>
				<button type="button" id="add-assignment" class="btn btn-default">Add Assignment</button>
			</div>
		<?php } ?>
			<div class="btn-group">
					<select name="filter_option" id="filter-assignments-select" class="form-control">
						<option value="-1">Show all</option>	
        			<% 
        				if( assign_categories){
	 						for (var i in assign_categories){
 								print('<option value='+assign_categories[i]+'>'+assign_categories[i]+'</option>');
 							}
 						}
        			%>  											   			      
					</select>
			</div>
			<div class="btn-group">
					<button type="button" id="filter-assignments" class="btn btn-default">Filter</button>  	    		   	
			</div>    	 		
			<hr/>
			<div>
				<table id="an-gradebook-container" class="table table-bordered table-striped">  
					<thead id="students-header">
						<tr>
							<th></th>
							<th class="gradebook-student-column-first_name">First Name</th>
							<th class="gradebook-student-column-last_name">Last Name</th>
							<th class="gradebook-student-column-user_login">Login</th>
						</tr>
					</thead>		    	
					<tbody id="students"></tbody>
				</table>
			</div>
		</div>
	</div>
</div>	
</script>