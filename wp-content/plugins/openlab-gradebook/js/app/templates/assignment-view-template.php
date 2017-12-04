<div class="btn-group">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        <span class="name"><%= assignment.get('assign_name') %><span class="caret"></span></span>
    </button>
    <ul class="dropdown-menu" role="menu">	
        <li class='assign-submenu-stats'><a href='#'><?php esc_html_e('Statistics', 'openlab-gradebook') ?></a></li>
        <li class='assign-submenu-details'><a href='#'><?php esc_html_e('Details', 'openlab-gradebook') ?></a></li>  					
        <?php
        global $current_user;
        $x = $current_user->roles;
        $y = array_keys(get_option('oplb_gradebook_settings'), true);
        $z = array_intersect($x, $y);
        if (count($z)) {
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

            <li class='assign-submenu-sort'><a href='#'><?php esc_html_e('Sort', 'openlab-gradebook') ?></a></li>  					
            <li class='assign-submenu-edit'><a href='#'><?php esc_html_e('Edit', 'openlab-gradebook') ?></a></li>
            <li class='assign-submenu-delete'><a href='#'><span class='text-danger'><?php esc_html_e('Delete', 'openlab-gradebook') ?></span></a></li>	
            <?php } ?>			
    </ul>
</div>    