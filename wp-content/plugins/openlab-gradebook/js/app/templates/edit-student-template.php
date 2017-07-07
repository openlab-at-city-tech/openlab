<script id="edit-student-template" type="text/template">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><%= student ? 'Edit ' : 'Create ' %>Student</h4>
			</div>
			<div class="modal-body">
    			<form id="edit-student-form" class="form-horizontal">     
					<div class="form-group">     			 
				        <input type="hidden" name="id" value="<%= student ? student.get('id') : '' %>"/>         
				        <label for="firstname" class="col-sm-4 control-label">First Name:</label>
						<div class="col-sm-6">					        
				        	<input type="text" class="form-control" id="first_name" name="first_name" value="<%= student ? student.get('first_name') : '' %>"/>
				        </div>	
				    </div>
					<div class="form-group"> 				        
				        <label for="lastname" class="col-sm-4 control-label">Last Name:</label>
						<div class="col-sm-6">					        
				        	<input type="text" class="form-control" id="last_name" name="last_name" value="<%= student ? student.get('last_name') : '' %>"/>
				        </div>	
				    </div>
				    <% if (!student) { %>
					<div class="form-group"> 				    
				        <label for="user_login" class="col-sm-4 control-label">User Login:</label>
				        <div class="ui-front col-sm-6"><input class="form-control" type="text" name="id-exists" id="user_login"/></div> 
				        <input type="hidden" name="gbid" value="<%=course.get('id')%>"/>				        
				    </div>
				    <% } %>
				        <div>
				        <% if (student) { %>
				        	Update user <%= student.get('user_login') %> from course ?
				        <% } else { %>
				        	if student exists in the wordpress database, use the students user_login to add. Otherwise a new record will be created for this student.
				        	Add to course ? 
				        <% } %>
				        </div>			        
    			</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" id="edit-student-save" data-dismiss="modal" class="btn btn-primary">Save</button>
			</div>
		</div>		
	</div>
</script>   

<!--if student exists in the wordpress database, use the students user_login to add. Otherwise a new record will be created for this student-->