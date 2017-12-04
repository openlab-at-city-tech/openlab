<div id = "an-gradebook-settings" class="wrap">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="wrap">
                    <h1><?php esc_html_e('Settings', 'openlab-gradebook') ?></h1>
                </div>
            </div>	
        </div>  
        <div class="row">		
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php esc_html_e('GradeBook Administrators', 'openlab-gradebook') ?>:</h3>
                    </div>
                    <div class="panel-body">
                        <span><?php esc_html_e('Select from the list of WordPress roles below to add as oplb_GradeBook administrators. WordPress administrators are set as oplb_GradeBook administrators by default. OpenLab GradeBook administrators will be able to create new grade books.', 'openlab-gradebook') ?></span> 
                        <form id="an-gradebook-settings-form"> 	     					  
                            <div class="row">
                                <label class="col-sm-12">   			
                                    <input type="checkbox" id="editor" name="editor" value="<%= gradebook_administrators.get('editor') %>"> <?php esc_html_e('Editor', 'openlab-gradebook') ?>
                                </label>  		
                            </div>	
                            <div class="row">						
                                <label class="col-sm-12">   			
                                    <input type="checkbox" id="author" name="author" value="<%=gradebook_administrators.get('author')%>"> <?php esc_html_e('Author', 'openlab-gradebook') ?>
                                </label>
                            </div>    				
                            <div class="row">    								
                                <label class="col-sm-12">   			
                                    <input type="checkbox" id="contributor" name="contributor" value="<%=gradebook_administrators.get('contributor')%>"> <?php esc_html_e('Contributor', 'openlab-gradebook') ?>
                                </label>
                            </div>    				
                            <div class="row">    				
                                <label class="col-sm-12">   			
                                    <input type="checkbox" id="subscriber" name="subscriber" value="<%=gradebook_administrators.get('subscriber')%>"> <?php esc_html_e('Subscriber', 'openlab-gradebook') ?>
                                </label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

