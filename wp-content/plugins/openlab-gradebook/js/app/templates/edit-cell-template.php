<% if(gradebook.get('role') === 'instructor') { %>
    <span class="grade-numeric" contenteditable="true"><%= cell.get('assign_points_earned') %></span>
<% } else { %>
    <span class="grade-numeric" style="cursor: default;"><%= cell.get('assign_points_earned') %></span>
<% } %>