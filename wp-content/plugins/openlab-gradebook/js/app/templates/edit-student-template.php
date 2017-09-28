<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"><%= student ? 'Edit Student' : 'Add a Student from Course Members' %></h4>
        </div>
        <div class="modal-body">
            <form id="edit-student-form" class="form-horizontal">
                <% if (student) { %>
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
                <% } %>
                <% if (!student) { %>
                <div id="studentAddWrapper" class="student-add-wrapper add-all">
                    <div id="selectStudentRange" class="form-group">
                        <div class="col-sm-5">&nbsp;</div>
                        <div class="col-sm-5">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="student_range_option" id="studentRangeAll" value="studentAll" checked="checked">
                                    Add all students
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="student_range_option" id="studentRangeSingle" value="studentSingle">
                                    Add a student
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="addSingleStudent" class="form-group">			    
                        <label for="user_login" class="col-sm-5 control-label">User's name:</label>
                        <div class="ui-front col-sm-5" id="user_login_wrapper">
                            <select class="form-control" name="id-exists" id="user_login">
                                <option value="0">Loading...</option>
                            </select>
                        </div> 
                        <input type="hidden" name="gbid" value="<%=course.get('id')%>"/>				        
                    </div>
                </div>
                <% } %>
                <div>
                    <% if (student) { %>
                    <p>Update user <%= student.get('user_login') %> from course?</p>
                    <% } else { %>
                    <p class="text-center">Students must have a user profile on this course site to be added to OpenLab Gradebook.</p>
                    <% } %>
                </div>			        
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" id="edit-student-save" data-dismiss="modal" class="btn btn-primary"><%= student ? 'Save' : 'Add' %></button>
        </div>
    </div>		
</div>

<!--if student exists in the wordpress database, use the students user_login to add. Otherwise a new record will be created for this student-->