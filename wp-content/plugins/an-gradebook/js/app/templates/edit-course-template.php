<script id="edit-course-template" type="text/template">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><%= course ? 'Edit ' : 'Create ' %>Course</h4>
			</div>
			<div class="modal-body">
				<form id="edit-course-form" class="form-horizontal"> 
					<div class="form-group">     
						<input type="hidden" name="id" value="<%= course ? course.get('id') : '' %>"/>        
						<label for="course_name" class="col-sm-3 control-label">Course Name:</label>
						<div class="col-sm-7">
							<input type="text" id="course_name" class="form-control" name="name" value="<%= course ? course.get('name') : '' %>"/>
						</div>
					</div>
					<div class="form-group">     					
						<label for="course_school" class="col-sm-3 control-label">School:</label>
						<div class="col-sm-7">						
							<input type="text" id="course_school" class="form-control" name="school" value="<%= course ? course.get('school') : '' %>"/>
						</div>	
					</div>	
					<div class="form-group">     						
						<label for="course_semester" class="col-sm-3 control-label">Semester:</label>
						<div class="col-sm-7">						
							<input type="text" id="course_semester" class="form-control" name="semester" value="<%= course ? course.get('semester') : '' %>"/>
						</div>
					</div>						
					<div class="form-group">     						
						<label for="course_year" class="col-sm-3 control-label">Year:</label>
						<div class="col-sm-7">						
							<input type="text" id="course_year" class="form-control" name="year" value="<%= course ? course.get('year') : '' %>"/>
						</div>
					</div>					
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" id="edit-course-save" data-dismiss="modal" class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</script> 