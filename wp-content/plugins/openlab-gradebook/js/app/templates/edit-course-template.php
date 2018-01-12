<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close<?php esc_html_e('', 'openlab-gradebook') ?>"><span aria-hidden="true">&times;</span></button>
            
            <% if (course) { %>
                <h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Edit Gradebook', 'openlab-gradebook') ?></h4>
            <% } else if (initvals.goInit){ %>
                <h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Create A Gradebook?', 'openlab-gradebook') ?></h4>
            <% } else { %>
                <h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Create A Gradebook', 'openlab-gradebook') ?></h4>
            <% } %>
            
        </div>
        <div class="modal-body">
            <form id="edit-course-form" class="form-horizontal"> 
                <div class="form-group">     
                    <input type="hidden" name="gbid" value="<%= course ? course.get('id') : '' %>"/>        
                    <label for="course_name" class="col-sm-3 control-label"><?php esc_html_e('Gradebook Name', 'openlab-gradebook') ?>:</label>
                    <div class="col-sm-7">
                        <% if(initvals.name){ %>
                            <input type="text" id="course_name" class="form-control" name="name" value="<%= initvals.name %>"/>
                        <% } else { %>
                            <input type="text" id="course_name" class="form-control" name="name" value="<%= course ? course.get('name') : '' %>"/>
                        <% } %>
                    </div>
                </div>
                <div class="form-group">     					
                    <label for="course_school" class="col-sm-3 control-label"><?php esc_html_e('School', 'openlab-gradebook') ?>:</label>
                    <div class="col-sm-7">						
                        <input type="text" id="course_school" class="form-control" name="school" value="<%= course ? course.get('school') : '' %>"/>
                    </div>	
                </div>	
                <div class="form-group">     						
                    <label for="course_semester" class="col-sm-3 control-label"><?php esc_html_e('Semester', 'openlab-gradebook') ?>:</label>
                    <div class="col-sm-7">						
                        <input type="text" id="course_semester" class="form-control" name="semester" value="<%= course ? course.get('semester') : '' %>"/>
                    </div>
                </div>						
                <div class="form-group">     						
                    <label for="course_year" class="col-sm-3 control-label"><?php esc_html_e('Year', 'openlab-gradebook') ?>:</label>
                    <div class="col-sm-7">						
                        <input type="text" id="course_year" class="form-control" name="year" value="<%= course ? course.get('year') : '' %>"/>
                    </div>
                </div>					
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php esc_html_e('Close', 'openlab-gradebook') ?></button>
            <button type="button" id="edit-course-save" data-dismiss="modal" class="btn btn-primary"><?php esc_html_e('Save', 'openlab-gradebook') ?></button>
        </div>
    </div>
</div>