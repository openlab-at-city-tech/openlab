<select class="grade-selector" <%= role === 'instructor' ? '' : 'disabled="disabled"' %>>
        <% grades.each(function(grade) { %>
            <option value="<%= grade.get('value') %>" <% if(cell.get('value') >= grade.get('value')) { %>selected<% } %> ><%= grade.get('label') %></option>
        <% }); %>
</select>