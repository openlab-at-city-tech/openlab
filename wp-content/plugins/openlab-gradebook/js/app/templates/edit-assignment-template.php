<div class="modal-dialog" id="modalDialogEditAssignment">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="<?php esc_html_e('Close', 'openlab-gradebook') ?>"><span aria-hidden="true">&times;</span></button>

            <% if (assignment.get('assign_name')) { %>
            <h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Edit Assignment', 'openlab-gradebook') ?></h4>
            <% } else { %>
            <h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Add Assignment', 'openlab-gradebook') ?></h4>
            <% } %>

        </div>
        <div class="modal-body">
            <form id="edit-assignment-form" class="form-horizontal">     
                <div class="form-group">    				 
                    <input type="hidden" name="id" value="<%= assignment ? assignment.get('id') : '' %>"/>  
                    <label for="assign_name" class="col-sm-4 control-label"><?php esc_html_e('Title', 'openlab-gradebook') ?>:</label>
                    <div class="col-sm-6">				        
                        <input type="text" id="assign_name" class="form-control" name="assign_name" value="<%= assignment ? assignment.get('assign_name') : '' %>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="assign-date-datepicker" class="col-sm-4 control-label"><?php esc_html_e('Date Assigned', 'openlab-gradebook') ?>:</label>
                    <div class="col-sm-6">				        
                        <input type="text" class="form-control" name="assign_date" id="assign-date-datepicker"  />        				    
                    </div>	
                </div>
                <div class="form-group">
                    <label for="assign-due-datepicker" class="col-sm-4 control-label"><?php esc_html_e('Date Due', 'openlab-gradebook') ?>:</label>
                    <div class="col-sm-6">				        
                        <input type="text" class="form-control" name="assign_due" id="assign-due-datepicker" value="<%= assignment ? assignment.get('assign_due') : '' %>"/>
                    </div>    
                </div>
                <div class="form-group">					
                    <label for="assign_category" class="col-sm-4 control-label"><?php esc_html_e('Category', 'openlab-gradebook') ?>:</label>
                    <div class="col-sm-6">				        
                        <input type="text" id="assign_category" class="form-control" name="assign_category" value="<%= assignment ? assignment.get('assign_category') : '' %>"/>		        
                    </div>	
                </div>	
                <div class="form-group">
                    <label for="assign_visibility" class="col-sm-4 control-label"><?php esc_html_e('Visibility', 'openlab-gradebook') ?>:</label>
                    <div class="col-sm-6">
                        <select class="form-control" id="assign_visibility" name="assign_visibility">
                            <option value="Students"><?php esc_html_e('Students', 'openlab-gradebook') ?></option>
                            <option value="Instructor"><?php esc_html_e('Instructor', 'openlab-gradebook') ?></option>
                        </select>					
                    </div>	 						
                </div>
                <div class="form-group hidden">					
                    <label for="assign_category" class="col-sm-4 control-label"><?php esc_html_e('Percentage of Total Grade', 'openlab-gradebook') ?>:</label>
                    <div class="col-sm-6">				        
                        <input type="number" id="assign_weight" class="form-control" name="assign_weight" value="<% if (assignment) {  parseFloat(assignment.get('assign_weight')) > 0 ? print(assignment.get('assign_weight')) : print('')  } else { print('') } %>" placeholder="<% if (assignment) {  parseFloat(assignment.get('assign_weight')) > 0 ? print('') : print('--')  } else { print('--') } %>"/>
                    </div>
                </div>	
                <div class="form-group grade-type">
                    <div class="col-sm-4">&nbsp;</div>
                    <div class="col-sm-6">
                        <h5 class="bold"><?php esc_html_e('Grade Type', 'openlab-gradebook') ?>:</h5>
                        <div class="radio">
                            <label>
                                <input type="radio" name="assign_grade_type" id="gradeTypeNumeric" value="numeric" <%= assignment.get('assign_grade_type') === 'numeric' ? 'checked="checked"' : '' %>>
                                       <?php esc_html_e('Numeric', 'openlab-gradebook') ?>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="assign_grade_type" id="gradeTypeLetter" value="letter" <%= assignment.get('assign_grade_type') === 'letter' ? 'checked="checked"' : '' %>>
                                       <?php esc_html_e('Letter Grades', 'openlab-gradebook') ?>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="assign_grade_type" id="gradeTypeCheckmark" value="checkmark" <%= assignment.get('assign_grade_type') === 'checkmark' ? 'checked="checked"' : '' %>>
                                       <?php esc_html_e('Checkmark', 'openlab-gradebook') ?>
                            </label>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="gbid" value="<%= course.get('id') %>"/>	 											
            </form>    		
            <div>
                <% if (assignment.get('assign_name')) { %>
                    <p class="text-right"><?php esc_html_e('Update assignment', 'openlab-gradebook') ?> <%= assignment.get('assign_name') %> <?php esc_html_e('from gradeboook', 'openlab-gradebook') ?> <%= course.get('name') %>?</p>
                <% } else { %>
                 <p class="text-right"><?php esc_html_e('Add to course', 'openlab-gradebook') ?> <%= course.get('name') %>?</p>
                <% } %>

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php esc_html_e('Close', 'openlab-gradebook') ?></button>
            <button type="button" id="edit-assignment-save" class="btn btn-primary"><?php esc_html_e('Save', 'openlab-gradebook') ?></button>
        </div>
    </div>
</div>