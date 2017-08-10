<td class="student-tools fixed-column<%= mobile_styles %>">
    <div class="btn-group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li class='student-submenu-stats'><a href='#'>Statistics</a></li>	
            <?php
            global $current_user;
            $x = $current_user->roles;
            $y = array_keys(get_option('oplb_gradebook_settings'), true);
            $z = array_intersect($x, $y);
            if (count($z)) {
                ?>						
                <li class='student-submenu-edit'><a href='#'>Edit</a></li>
                <li class='student-submenu-delete'><a href='#'><span class="text-danger">Delete</span></a></li>
            <?php } ?>        									
        </ul>
    </div>
</td>
<td class="student<%= mobile_styles %>">	
    <div class="column-frame">	
        <span data-toggle="tooltip" data-placement="top" title='<%= student.get("first_name") %>'><%= student.get("first_name") %></span> 				
    </div>				
</td>
<td class="student<%= mobile_styles %>"><span data-toggle="tooltip" data-placement="top" title='<%= student.get("last_name") %>'><%= student.get("last_name") %></span></td>
<td class="student<%= mobile_styles %>"><span data-toggle="tooltip" data-placement="top" title='<%= student.get("user_login") %>'><%= student.get("user_login") %></span></td>

