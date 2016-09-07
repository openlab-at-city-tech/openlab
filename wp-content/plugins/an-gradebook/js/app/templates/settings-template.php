<script id="an-gradebook-settings-template" type="text/template">
<div id = "an-gradebook-settings" class="wrap">
	<div class="container-fluid">
    	<div class="row">
    		<div class="col-md-12 wrap">
    			<h1>Settings</h1>
	    	</div>	
    	</div>  
		<div class="row">		
		<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading">
      			<h3 class="panel-title">GradeBook Administrators:</h3>
			</div>
  			<div class="panel-body">
      			<span>Select from the list of WordPress roles below to add as AN_GradeBook administrators.  
      				WordPress administrators are set as AN_GradeBook administrators by default.
      				AN_GradeBook administrators will be able to create new grade books. </span> 
      			<form id="an-gradebook-settings-form"> 	     					  
      				<div class="row">
      				<label class="col-sm-12">   			
						<input type="checkbox" id="editor" name="editor" value="<%= gradebook_administrators.get('editor') %>"> Editor
					</label>  		
					</div>	
      				<div class="row">						
      				<label class="col-sm-12">   			
      					<input type="checkbox" id="author" name="author" value="<%=gradebook_administrators.get('author')%>"> Author
    				</label>
    				</div>    				
      				<div class="row">    								
      				<label class="col-sm-12">   			
      					<input type="checkbox" id="contributor" name="contributor" value="<%=gradebook_administrators.get('contributor')%>"> Contributor
    				</label>
    				</div>    				
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
    
    