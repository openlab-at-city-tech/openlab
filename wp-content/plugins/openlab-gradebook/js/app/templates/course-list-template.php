<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="wrap">
                <h1>Gradebooks
                    <?php
                    global $oplb_gradebook_api;
                    if ($oplb_gradebook_api->oplb_is_gb_administrator()) {
                        ?>
                        <a class="btn btn-default" id="add-course">
                            Add new
                        </a>   
                    <?php } ?>    
                </h1>
            </div>
        </div>	
    </div>   
    <div class="row">
        <div class="col-md-12">    
            <table class="table table-bordered table-striped table-hover">  
                <thead>
                    <tr>		
                        <th></th>
                        <th class="course-column-id">ID</th>
                        <th class="course-column-name">Gradebook</th>
                        <th class="course-column-school">School</th>
                        <th class="course-column-semester">Semester</th>
                        <th class="course-column-year">Year</th>
                    </tr>
                </thead>
                <tbody class="angb-course-list-tbody">			
                </tbody>
            </table>
        </div>
    </div>
</div>