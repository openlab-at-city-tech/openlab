<th class="gradebook-student-column-interactive student-tools download-csv adjust-widths visible-xs"
    data-targetwidth="50"></th>
<th class="gradebook-student-column-first_name visible-xs">
    <span class="header-wrapper">
        <span class="tooltip-wrapper mobile" data-toggle="tooltip" data-placement="top" data-target="first_name"
            title='<?php esc_html_e('First Name', 'openlab-gradebook')?>'><?php esc_html_e('First Name', 'openlab-gradebook')?></span>
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
<th class="gradebook-student-column-last_name visible-xs">
    <span class="header-wrapper sort-up">
        <span class="tooltip-wrapper mobile" data-toggle="tooltip" data-placement="top" data-target="last_name"
            title='<?php esc_html_e('Last Name', 'openlab-gradebook')?>'><?php esc_html_e('Last Name', 'openlab-gradebook')?></span>
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
<th class="gradebook-student-column-user_login visible-xs">
    <span class="header-wrapper">
        <span class="tooltip-wrapper mobile" data-toggle="tooltip" data-placement="top" data-target="user_login"
            title='<?php esc_html_e('Username', 'openlab-gradebook')?>'><?php esc_html_e('Username', 'openlab-gradebook')?></span>
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
<th class="gradebook-student-column-average adjust-widths visible-xs" data-targetwidth="65">
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
<th class="gradebook-student-mid-semester-grade student-grades adjust-widths" data-targetwidth="65"><span
        data-toggle="tooltip" data-placement="top"
        title='<?php esc_html_e(' Mid-semester Grade ', 'openlab-gradebook ')?>'>
        <?php esc_html_e('Mid-semester Grade', 'openlab-gradebook')?></span></th>
<th class="gradebook-student-final-grade student-grades adjust-widths" data-targetwidth="65"><span data-toggle="tooltip"
        data-placement="top" title='<?php esc_html_e(' Final Grade ', 'openlab-gradebook ')?>'>
        <?php esc_html_e('Final Grade', 'openlab-gradebook')?></span></th>