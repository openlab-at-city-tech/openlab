<div class="cell-wrapper">
    <% if(gradebook.get('role') === 'instructor') { %>
    <span class="grade-numeric" contenteditable="true">
        <% if(!cell.get('is_null') || parseInt(cell.get('is_null')) === 0) { %>
        <%= cell.get('assign_points_earned') %>
        <% } else { %>
        --
        <% } %>
    </span>
    <% } else { %>
    <span class="grade-numeric" style="cursor: default;">
        <% if(!cell.get('is_null') || parseInt(cell.get('is_null')) === 0) { %>
        <%= cell.get('assign_points_earned') %>
        <% } else { %>
        --
        <% } %>
    </span>
    <% } %>
</div>