<div class="cell-wrapper">
    <div class="checkbox">
        <label>
            <input class="grade-checkmark" type="checkbox"
                <% if(cell.get('assign_points_earned') >= 60) { %>checked="checked" <% } %>
                <%= role === 'instructor' ? '' : 'disabled="disabled"' %>>
        </label>
    </div>
</div>