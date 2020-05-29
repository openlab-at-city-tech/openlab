<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="wrap">
                <h1><span><?php esc_html_e('GradeBook', 'openlab-gradebook') ?></span>
                    <?php
                    global $oplb_gradebook_api;
                    if ($oplb_gradebook_api->oplb_is_gb_administrator()) {
                        ?>
                        <a class="btn btn-default" id="add-course">
                            <?php esc_html_e('Add New', 'openlab-gradebook') ?>
                        </a>   
                    <?php } ?>    
                </h1>
            </div>
        </div>	
    </div>   
    <div class="row">
        <div class="col-md-12 course-table-wrapper">    
            <table class="table table-bordered table-striped table-hover">  
                <thead>
                    <tr>		
                        <th></th>
                        <th class="course-column-id"><?php esc_html_e('ID', 'openlab-gradebook') ?></th>
                        <th class="course-column-name"><?php esc_html_e('GradeBook', 'openlab-gradebook') ?></th>
                        <th class="course-column-school"><?php esc_html_e('Section', 'openlab-gradebook') ?></th>
                        <th class="course-column-semester"><?php esc_html_e('Semester', 'openlab-gradebook') ?></th>
                        <th class="course-column-year"><?php esc_html_e('Year', 'openlab-gradebook') ?></th>
                    </tr>
                </thead>
                <tbody class="angb-course-list-tbody">			
                </tbody>
            </table>
        </div>
    </div>
</div>