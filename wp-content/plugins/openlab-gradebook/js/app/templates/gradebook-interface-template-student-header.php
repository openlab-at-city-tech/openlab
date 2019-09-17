<th class="gradebook-student-column-interactive student-tools download-csv adjust-widths visible-xs visible-sm"
    data-targetwidth="50"></th>
<th class="gradebook-student-column-first_name visible-xs visible-sm pointer">
    <span class="header-wrapper">
        <span class="tooltip-wrapper mobile" data-target="first_name"><?php esc_html_e('First Name', 'openlab-gradebook')?></span>
        <% if(role === 'instructor') { %>
        <span class="arrow-placement">
            <span class="arrow-wrapper">
                <span class="glyphicon glyphicon-triangle-top" aria-hidden="true"></span>
                <span class="glyphicon glyphicon-triangle-bottom" aria-hidden="true"></span>
            </span>
        </span>
        <% } %>
    </span>
</th>
<th class="gradebook-student-column-last_name visible-xs visible-sm pointer">
    <span class="header-wrapper sort-up">
        <span class="tooltip-wrapper mobile" data-target="last_name"><?php esc_html_e('Last Name', 'openlab-gradebook')?></span>
        <% if(role === 'instructor') { %>
        <span class="arrow-placement">
            <span class="arrow-wrapper">
                <span class="glyphicon glyphicon-triangle-top" aria-hidden="true"></span>
                <span class="glyphicon glyphicon-triangle-bottom" aria-hidden="true"></span>
            </span>
        </span>
        <% } %>
    </span>
</th>
<th class="gradebook-student-column-user_login visible-xs visible-sm pointer">
    <span class="header-wrapper">
        <span class="tooltip-wrapper mobile" data-target="user_login"><?php esc_html_e('Username', 'openlab-gradebook')?></span>
        <% if(role === 'instructor') { %>
        <span class="arrow-placement">
            <span class="arrow-wrapper">
                <span class="glyphicon glyphicon-triangle-top" aria-hidden="true"></span>
                <span class="glyphicon glyphicon-triangle-bottom" aria-hidden="true"></span>
            </span>
        </span>
        <% } %>
    </span>
</th>
<th class="gradebook-student-column-average adjust-widths visible-xs visible-sm pointer" data-targetwidth="65">
    <span class="header-wrapper">
        <span class="tooltip-wrapper mobile" data-toggle="tooltip" data-placement="top" data-target="average"
            title='<?php esc_html_e('Current Average Grade', 'openlab-gradebook')?>'><?php esc_html_e('Avg.', 'openlab-gradebook')?></span>
        <% if(role === 'instructor') { %>
        <span class="arrow-placement">
            <span class="arrow-wrapper">
                <span class="glyphicon glyphicon-triangle-top" aria-hidden="true"></span>
                <span class="glyphicon glyphicon-triangle-bottom" aria-hidden="true"></span>
            </span>
        </span>
        <% } %>
    </span>
</th>
<th class="gradebook-student-mid-semester-grade student-grades adjust-widths" data-targetwidth="65"><span>
        <?php esc_html_e('Mid-semester Grade', 'openlab-gradebook')?></span></th>
<th class="gradebook-student-final-grade student-grades adjust-widths" data-targetwidth="65"><span>
        <?php esc_html_e('Final Grade', 'openlab-gradebook')?></span></th>