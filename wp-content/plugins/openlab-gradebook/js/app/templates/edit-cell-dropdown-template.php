<div class="cell-wrapper">
    <select class="grade-selector" <%= role === 'instructor' ? '' : 'disabled="disabled"' %>>
        <% grades.each(function(grade) { %>
        <option value="<%= grade.get('value') %>"
            <% if(cell.get('assign_points_earned') >= grade.get('range_low') && cell.get('assign_points_earned') < grade.get('range_high')) { %>selected<% } %>>
            <%= grade.get('label') %></option>
        <% }); %>
    </select>
</div>