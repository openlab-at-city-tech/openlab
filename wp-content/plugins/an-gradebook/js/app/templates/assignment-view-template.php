<script id="assignment-view-template" type="text/template">
	<div class="btn-group">
  		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
    		<%= assignment.get('assign_name') %> <span class="caret"></span>
  		</button>
  		<ul class="dropdown-menu" role="menu">	
			<li class='assign-submenu-stats'><a href='#'>Statistics</a></li>
			<li class='assign-submenu-details'><a href='#'>Details</a></li>  					
    			<?php 
    				global $current_user;  
    				$x = $current_user->roles;
    				$y = array_keys(get_option('an_gradebook_settings'),true);
    				$z = array_intersect($x,$y);
    				if( count($z) ){
    			?>
    			<%
				if (min.get('assign_order') != max.get('assign_order')){
					if( assignment.get('assign_order') === min.get('assign_order') ) { 
						print("<li class='assign-submenu-right'><a href='#'>Shift Right</a></li>");				
					} else if ( assignment.get('assign_order') === max.get('assign_order') ) { 
						print("<li class='assign-submenu-left'><a href='#'>Shift Left</a></li>");		
					} else { 
						print("<li class='assign-submenu-left'><a href='#'>Shift Left</a></li>");		
						print("<li class='assign-submenu-right'><a href='#'>Shift Right</a></li>");	
					}
				}
			%>	
				
				<li class='assign-submenu-sort'><a href='#'>Sort</a></li>  					
				<li class='assign-submenu-edit'><a href='#'>Edit</a></li>
				<li class='assign-submenu-delete'><a href='#'><span class='text-danger'>Delete</span></a></li>	
			<?php } ?>			
  		</ul>
  	</div>
</script>      
    
    