<div class="cell-wrapper">
    <select class="grade-selector" <%= role === 'instructor' ? '' : 'disabled="disabled"' %>>
        <% grades.each(function(grade) { %>
        <option <% if(cell.get('value').indexOf('_display') !== -1) { print('class="display-item"') } %>
            value="<%= grade.get('value') %>" <% if(cell.get('value') >= grade.get('value')) { %>selected<% } %>>
            <%= grade.get('label') %></option>
        <% }); %>
    </select>
</div>