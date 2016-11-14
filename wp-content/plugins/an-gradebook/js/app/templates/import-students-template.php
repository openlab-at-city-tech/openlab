<script id="an-gradebook-import-students-template" type="text/template">
<div id = "an-gradebook-import-students" class="wrap">
	<div class="container-fluid">
    	<div class="row">
    		<div class="col-md-12 wrap">
    			<h1>Import Students</h1>
	    	</div>	
    	</div>  
		<div class="row">		
		<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading">
      			<h3 class="panel-title">Import File:</h3>
			</div>
  			<div class="panel-body">
      			<span>Select import file. </span> 
      			<form id="an-gradebook-settings-form"> 	     					    				
      				<div class="row">    				
      				<label class="col-sm-12">   			
      					<input type="checkbox" id="subscriber" name="subscriber" value="<%=gradebook_administrators.get('subscriber')%>"> Subscriber
    				</label>
    				</div>
  				</form>
  			</div>
		</div>
	</div>
	</div>
	</div>
	</div>
</script>      
    
    