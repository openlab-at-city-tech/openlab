<script id="delete-student-template" type="text/template">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Delete Student</h4>
			</div>
			<div class="modal-body">
				<form id="delete-student-form" class="form-horizontal">      
         			<input type="hidden" name="action" value="delete_student"/>
				    <input type="hidden" name="id" value="<%= student ? student.get('id') : '' %>"/> 
				    <div class="form-group">
				    	<label for="delete_options" class="col-sm-3 control-label">Delete from:</label>
				    	<div class="col-sm-7">
							<select class="form-control" id="delete_options" name="delete_options">
								<option value="gradebook">this gradebook only.</option>
								<option value="all_gradebooks">all gradebooks.</option>
								<option value="database">the wordpress database.</option>
							</select>
 							<input type="hidden" name="gbid" value="<%= course.get('id') %>"/>						
 						</div>	
 					</div>
						<div>
						Deleting a student from the wordpress database will also delete that student from all gradebooks.
				    	Delete <%= student.get('firstname')%> <%= student.get('lastname')%> with user login <%= student.get('user_login')%> from course <%= course.get('id') %>?					
				        </div>				        
    				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" id="delete-student-delete" data-dismiss="modal" class="btn btn-danger">Delete</button>
			</div>
		</div>
	</div>
</script>      