<% if(gradebook.get('role') === 'instructor') { %>
    <span class="grade-numeric" contenteditable="true">
    <% if(cell.get('is_null')) { %>
        --
    <% } else { %>
        <%= cell.get('assign_points_earned') %>
    <% } %>
    </span>
<% } else { %>
    <span class="grade-numeric" style="cursor: default;">
    <% if(cell.get('is_null')) { %>
        --
    <% } else { %>
        <%= cell.get('assign_points_earned') %>
    <% } %>
    </span>
<% } %>