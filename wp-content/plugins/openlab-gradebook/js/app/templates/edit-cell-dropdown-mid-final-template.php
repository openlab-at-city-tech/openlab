<div class="cell-wrapper">
    <% if(type === 'mid_semester'){ %>

        <% if(role === 'instructor') { %>

            <select class="grade-selector mid" data-type="mid" data-uid="<%= student.get('id') %>">
                <% grades.each(function(grade) { %>
                <option <% if(grade.get('type') === 'display_value') { print('class="display-value"') } %> value="<%= grade.get('value') %>" <% if(student.get('mid_semester_grade') === grade.get('value')) { %>selected<% } %>><%= grade.get('label') %>
                </option>
                <% }); %>
            </select>

        <% } else { %>

            <span class="grade-numeric" style="cursor: default;">
                <% grades.each(function(grade) { %>
                <% if(student.get('mid_semester_grade') === grade.get('value')) { %><%= grade.get('label') %><% } %>
                <% }); %>
            </span>
            
        <% } %>

    <% } else { %>

        <% if(role === 'instructor') { %>

            <select class="grade-selector final" data-type="final" data-uid="<%= student.get('id') %>" <%= role === 'instructor' ? '' : 'disabled="disabled"' %>>
                <% grades.each(function(grade) { %>
                <option <% if(grade.get('type') === 'display_value') { print('class="display-value"') } %> value="<%= grade.get('value') %>" <% if(student.get('final_grade') === grade.get('value')) { %>selected<% } %>>
                    <%= grade.get('label') %></option>
                <% }); %>
            </select>

        <% } else { %>

            <span class="grade-numeric" style="cursor: default;">
                <% grades.each(function(grade) { %>
                <% if(student.get('final_grade') === grade.get('value')) { %><%= grade.get('label') %><% } %>
                <% }); %>
            </span>

        <% } %>

    <% } %>
</div>