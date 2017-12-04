<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="wrap">
                <h1><?php esc_html_e('GradeBook', 'openlab-gradebook') ?>: <%= course.get('name')%></h1>
            </div>
        </div>	
    </div>
    <div class="row">
        <div class="col-md-12">  
            <?php
            global $current_user;
            $x = $current_user->roles;
            $y = array_keys(get_option('oplb_gradebook_settings'), true);
            $z = array_intersect($x, $y);
            if (count($z)) {
                ?>    	 
                <div class="btn-group">    		
                    <button type="button" id="add-student" class="btn btn-default"><?php esc_html_e('Add Student', 'openlab-gradebook') ?></button>
                    <button type="button" id="download-csv" class="btn btn-default"><?php esc_html_e('Download CSV', 'openlab-gradebook') ?></button>
                    <button type="button" id="add-assignment" class="btn btn-default"><?php esc_html_e('Add Assignment', 'openlab-gradebook') ?></button>
                </div>
            <?php } ?>
            <div class="btn-group">
                <select name="filter_option" id="filter-assignments-select" class="form-control">
                    <option value="-1"><?php esc_html_e('Show all', 'openlab-gradebook') ?></option>	
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
                <button type="button" id="filter-assignments" class="btn btn-default"><?php esc_html_e('Filter', 'openlab-gradebook') ?></button>  	    		   	
            </div>
            <div class="btn-group weight-message">
                <p><%= total_weight %></p>
            </div>
            <hr/>
            <div class="table-wrapper">
                <div class="pinned hidden-xs">
                    <table id="an-gradebook-container-pinned" class="table table-bordered table-striped">  
                        <thead id="students-header-pinned" class="students-header">
                            <tr>
                                <th class="gradebook-student-column-interactive student-tools adjust-widths" data-targetwidth="50"></th>
                                <th class="gradebook-student-column-first_name"><span data-toggle="tooltip" data-placement="top" title='<?php esc_html_e('First Name', 'openlab-gradebook') ?>'><?php esc_html_e('First Name', 'openlab-gradebook') ?></span></th>
                                <th class="gradebook-student-column-last_name"><span data-toggle="tooltip" data-placement="top" title='<?php esc_html_e('Last Name', 'openlab-gradebook') ?>'><?php esc_html_e('Last Name', 'openlab-gradebook') ?></span></th>
                                <th class="gradebook-student-column-user_login"><span data-toggle="tooltip" data-placement="top" title='<?php esc_html_e('Login', 'openlab-gradebook') ?>'><?php esc_html_e('Login', 'openlab-gradebook') ?></span></th>
                                <th class="gradebook-student-column-average adjust-widths" data-targetwidth="65"><span data-toggle="tooltip" data-placement="top" title='<?php esc_html_e('Current Average Grade', 'openlab-gradebook') ?>'><?php esc_html_e('Avg.', 'openlab-gradebook') ?></span></th>
                            </tr>
                        </thead>		    	
                        <tbody id="students-pinned" class="students"></tbody>
                    </table>
                </div>
                <div class="scroll-control">
                    <div class="scrollable">
                        <table id="an-gradebook-container" class="table table-bordered table-striped">  
                            <thead id="students-header" class="students-header">
                                <tr>
                                    <th class="gradebook-student-column-interactive student-tools adjust-widths visible-xs" data-targetwidth="50"></th>
                                    <th class="gradebook-student-column-first_name visible-xs"><span data-toggle="tooltip" data-placement="top" title='<?php esc_html_e('First Name', 'openlab-gradebook') ?>'><?php esc_html_e('First Name', 'openlab-gradebook') ?></span></th>
                                    <th class="gradebook-student-column-last_name visible-xs"><span data-toggle="tooltip" data-placement="top" title='<?php esc_html_e('Last Name', 'openlab-gradebook') ?>'><?php esc_html_e('Last Name', 'openlab-gradebook') ?></span></th>
                                    <th class="gradebook-student-column-user_login visible-xs"><span data-toggle="tooltip" data-placement="top" title='<?php esc_html_e('Login', 'openlab-gradebook') ?>'><?php esc_html_e('Login', 'openlab-gradebook') ?></span></th>
                                    <th class="gradebook-student-column-average visible-xs adjust-widths" data-targetwidth="65"><span data-toggle="tooltip" data-placement="top" title='<?php esc_html_e('Current Average Grade', 'openlab-gradebook') ?>'><?php esc_html_e('Avg.', 'openlab-gradebook') ?></span></th>
                                </tr>
                            </thead>		    	
                            <tbody id="students" class="students"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>