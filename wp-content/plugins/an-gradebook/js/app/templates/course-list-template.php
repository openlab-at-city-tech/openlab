<script id="course-list-template" type="text/template">
<div class="container-fluid">
    <div class="row">
    	<div class="col-md-12 wrap">
    		<h1>Courses
    			<?php 
    				global $an_gradebook_api;    			
    				if($an_gradebook_api->angb_is_gb_administrator()){
    			?>
        		<a class="btn btn-default" id="add-course">
       				Add new
	    		</a>   
	    		<?php } ?>    
    		</h1>
    	</div>	
    </div>   
    <div class="row">
    	<div class="col-md-12">    
    	<table class="table table-bordered table-striped table-hover">  
			<thead>
				<tr>		
					<th></th>
					<th class="course-column-id">ID</th>
					<th class="course-column-name">Course</th>
					<th class="course-column-school">School</th>
					<th class="course-column-semester">Semester</th>
					<th class="course-column-year">Year</th>
				</tr>
			</thead>
			<tbody class="angb-course-list-tbody">			
			</tbody>
		</table>
		</div>
	</div>
</div>
</script>  