<td class="student-tools fixed-column<%= mobile_styles %>">
    <div class="btn-group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" aria-label="<?php esc_html_e('Close', 'openlab-gradebook') ?>">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li class='student-submenu-stats'><a href='#'><?php esc_html_e('Statistics', 'openlab-gradebook') ?></a></li>	
            <?php
            global $current_user;
            $x = $current_user->roles;
            $y = array_keys(get_option('oplb_gradebook_settings'), true);
            $z = array_intersect($x, $y);
            if (count($z)) {
                ?>						
                <li class='student-submenu-delete'><a href='#'><span class="text-danger"><?php esc_html_e('Delete', 'openlab-gradebook') ?></span></a></li>
            <?php 
        } ?>        									
        </ul>
    </div>
</td>
<td class="student<%= mobile_styles %>">
    <div class="column-frame">
        <span id="student-<%= student.get('last_name') %>" data-toggle="tooltip" data-placement="top" title='<%= student.get("first_name") %>'><%= student.get("first_name") %></span> 				
    </div>
</td>
<td class="student<%= mobile_styles %>"><span data-toggle="tooltip" data-placement="top" title='<%= student.get("last_name") %>'><%= student.get("last_name") %></span></td>
<td class="student<%= mobile_styles %>"><span data-toggle="tooltip" data-placement="top" title='<%= student.get("user_login") %>'><%= student.get("user_login") %></span></td>
<td class="gradebook-student-column-average student<%= mobile_styles %>"><span id="average<%= student.get('id') %>" data-toggle="tooltip" data-placement="top" title='<%= student.get("current_grade_average") %>'><%= student.get("current_grade_average") %></span></td>
<td class="student student-grades mid-semester-grade">
<span>
<% if(role === 'instructor') { %>
    <select class="grade-selector mid" data-type="mid" data-uid="<%= student.get('id') %>">
        <% midGrades.each(function(grade) { %>
            <option <% if(grade.get('type') === 'display_value') { print('class="display-value"') } %> value="<%= grade.get('value') %>" <% if(student.get('mid_semester_grade') === grade.get('value')) { %>selected<% } %> ><%= grade.get('label') %></option>
        <% }); %>
    </select>
<% } else { %>
    <span class="grade-numeric" style="cursor: default;">
        <% midGrades.each(function(grade) { %>
            <% if(student.get('mid_semester_grade') === grade.get('value')) { %><%= grade.get('label') %><% } %>
        <% }); %>
    </span>
<% } %>
</span>
</td>
<td class="student student-grades final-grade">
<span>
<% if(role === 'instructor') { %>
    <select class="grade-selector final" data-type="final" data-uid="<%= student.get('id') %>" <%= role === 'instructor' ? '' : 'disabled="disabled"' %>>
            <% finalGrades.each(function(grade) { %>
                <option <% if(grade.get('type') === 'display_value') { print('class="display-value"') } %> value="<%= grade.get('value') %>" <% if(student.get('final_grade') === grade.get('value')) { %>selected<% } %> ><%= grade.get('label') %></option>
            <% }); %>
    </select>
<% } else { %>
    <span class="grade-numeric" style="cursor: default;">
        <% finalGrades.each(function(grade) { %>
            <% if(student.get('final_grade') === grade.get('value')) { %><%= grade.get('label') %><% } %>
        <% }); %>
    </span>
<% } %>
</span>
</td>