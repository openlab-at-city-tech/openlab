<div class="modal-dialog" id="modalDialogEditStudent">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close<?php esc_html_e('Close', 'openlab-gradebook') ?>"><span aria-hidden="true">&times;</span></button>

            <h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Add a Student from Course Members', 'openlab-gradebook') ?></h4>

        </div>
        <div class="modal-body">
            <form id="edit-student-form" class="form-horizontal">
                <div id="studentAddWrapper" class="student-add-wrapper add-all">
                    <div id="selectStudentRange" class="form-group">
                        <div class="col-sm-5">&nbsp;</div>
                        <div class="col-sm-5">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="student_range_option" id="studentRangeAll" value="studentAll" checked="checked">
                                    <?php esc_html_e('Add all students', 'openlab-gradebook') ?>
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="student_range_option" id="studentRangeSingle" value="studentSingle">
                                    <?php esc_html_e('Add a student', 'openlab-gradebook') ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="addSingleStudent" class="form-group">			    
                        <label for="user_login" class="col-sm-5 control-label"><?php esc_html_e("User's name", 'openlab-gradebook') ?>:</label>
                        <div class="ui-front col-sm-5" id="user_login_wrapper">
                            <select class="form-control" name="id-exists" id="user_login">
                                <option value="0"><?php esc_html_e('Loading', 'openlab-gradebook') ?>...</option>
                            </select>
                        </div> 
                        <input type="hidden" name="gbid" value="<%=course.get('id')%>"/>				        
                    </div>
                </div>
                <div>
                    <p class="text-center"><?php esc_html_e('Students must have a user profile on this course site to be added to OpenLab GradeBook.', 'openlab-gradebook') ?></p>
                </div>			        
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php esc_html_e('Close', 'openlab-gradebook') ?></button>
            
            <button type="button" id="edit-student-save" data-dismiss="modal" class="btn btn-primary" disabled="disabled"><span class="dashicons dashicons-image-rotate dashicons-spinning"></span> <?php esc_html_e('Loading', 'openlab-gradebook') ?></button>
            
        </div>
    </div>		
</div>

<!--if student exists in the wordpress database, use the students user_login to add. Otherwise a new record will be created for this student-->