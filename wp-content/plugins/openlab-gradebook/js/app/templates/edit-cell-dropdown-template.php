<div class="cell-wrapper">
    <% if (role === 'instructor') { %>
    <select class="grade-selector">
        <% grades.each(function(grade) { %>
        <option value="<%= grade.get('value') %>"
            <% if(cell.get('assign_points_earned') >= grade.get('range_low') && cell.get('assign_points_earned') < grade.get('range_high')) { %>selected<% } %>>
            <%= grade.get('label') %></option>
        <% }); %>
    </select>
    <% } else { %>
    <span class="grade-numeric" style="cursor: default;">
        <% grades.each(function(grade) { %>
        <% if(cell.get('assign_points_earned') >= grade.get('range_low') && cell.get('assign_points_earned') < grade.get('range_high')) { %>
        <%= grade.get('label') %> <% } %>
        <% }); %>
    </span>
    <% } %>
</div>