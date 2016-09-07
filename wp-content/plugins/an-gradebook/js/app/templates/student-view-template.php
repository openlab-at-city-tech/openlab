<script id="student-view-template" type="text/template">
	<th class="student-tools fixed-column">
		<div class="btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li class='student-submenu-stats'><a href='#'>Statistics</a></li>	
			<?php 
				global $current_user;  
				$x = $current_user->roles;
				$y = array_keys(get_option('an_gradebook_settings'),true);
				$z = array_intersect($x,$y);
				if( count($z) ){
			?>						
        		<li class='student-submenu-edit'><a href='#'>Edit</a></li>
				<li class='student-submenu-delete'><a href='#'><span class="text-danger">Delete</span></a></li>
			<?php } ?>        									
			</ul>
		</div>
	</th>
	<td class="student">	
		<div class="column-frame">	
			<%= student.get("first_name") %> 				
		</div>				
	</td>
	<td><%= student.get("last_name") %></td>
	<td><%= student.get("user_login") %></td>
</script>      
    
    