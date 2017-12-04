<div id = "an-gradebook-import-students" class="wrap">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="wrap">
                    <h1><?php esc_html_e('Import Students', 'openlab-gradebook') ?></h1>
                </div>
            </div>	
        </div>  
        <div class="row">		
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php esc_html_e('Import File', 'openlab-gradebook') ?>:</h3>
                    </div>
                    <div class="panel-body">
                        <span><?php esc_html_e('Select import file.', 'openlab-gradebook') ?></span> 
                        <form id="an-gradebook-settings-form"> 	     					    				
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

