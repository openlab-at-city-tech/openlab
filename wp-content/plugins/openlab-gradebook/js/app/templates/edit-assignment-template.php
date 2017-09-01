<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"><%= assignment ? 'Edit ' : 'Create ' %>Assignment</h4>
        </div>
        <div class="modal-body">
            <form id="edit-assignment-form" class="form-horizontal">     
                <div class="form-group">    				 
                    <input type="hidden" name="id" value="<%= assignment ? assignment.get('id') : '' %>"/>  
                    <label for="assign_name" class="col-sm-4 control-label">Title:</label>
                    <div class="col-sm-6">				        
                        <input type="text" id="assign_name" class="form-control" name="assign_name" value="<%= assignment ? assignment.get('assign_name') : '' %>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="assign-date-datepicker" class="col-sm-4 control-label">Date Assigned:</label>
                    <div class="col-sm-6">				        
                        <input type="text" class="form-control" name="assign_date" id="assign-date-datepicker"  />        				    
                    </div>	
                </div>
                <div class="form-group">
                    <label for="assign-due-datepicker" class="col-sm-4 control-label">Date Due:</label>
                    <div class="col-sm-6">				        
                        <input type="text" class="form-control" name="assign_due" id="assign-due-datepicker" value="<%= assignment ? assignment.get('assign_due') : '' %>"/>
                    </div>    
                </div>
                <div class="form-group">					
                    <label for="assign_category" class="col-sm-4 control-label">Category:</label>
                    <div class="col-sm-6">				        
                        <input type="text" id="assign_category" class="form-control" name="assign_category" value="<%= assignment ? assignment.get('assign_category') : '' %>"/>		        
                    </div>	
                </div>	
                <div class="form-group">
                    <label for="assign_visibility_options" class="col-sm-4 control-label">Visibility:</label>
                    <div class="col-sm-6">
                        <select class="form-control" id="assign_visibility_options" name="assign_visibility_options">
                            <option value="Students">Students</option>
                            <option value="Instructor">Instructor</option>
                        </select>					
                    </div>	 						
                </div>
                <div class="form-group">					
                    <label for="assign_category" class="col-sm-4 control-label">Weight (no % necessary):</label>
                    <div class="col-sm-6">				        
                        <input type="number" id="assign_weight" class="form-control" name="assign_weight" value="<%= assignment ? assignment.get('assign_weight') : '' %>"/>
                    </div>
                </div>	
                <div class="form-group grade-type">
                    <div class="col-sm-4">&nbsp;</div>
                    <div class="col-sm-6">
                        <h5 class="bold">Grade Type:</h5>
                        <div class="radio">
                            <label>
                                <input type="radio" name="gradeType" id="gradeTypeNumeric" value="numeric" <%= assignment.get('assign_grade_type') === 'numeric' ? 'checked="checked"' : '' %>>
                                Numeric
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="gradeType" id="gradeTypeLetter" value="letter" <%= assignment.get('assign_grade_type') === 'letter' ? 'checked="checked"' : '' %>>
                                Letter Grades
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="gradeType" id="gradeTypeCheckmark" value="checkmark" <%= assignment.get('assign_grade_type') === 'checkmark' ? 'checked="checked"' : '' %>>
                                Checkmark
                            </label>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="gbid" value="<%= course.get('id') %>"/>	 											
            </form>    		
            <div>
                <p class="text-right"><%= assignment ? 'Update assignment ' + assignment.get('assign_name') + ' from course ' +  course.get('name') : 'Add to course ' +  course.get('name') %>?</p>     			
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" id="edit-assignment-save" class="btn btn-primary">Save</button>
        </div>
    </div>
</div>