<div id="gradebookWrapper" class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="wrap">
                <h1><span><?php esc_html_e('GradeBook', 'openlab-gradebook')?>:</span> <%= course.get('name')%></h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="action-buttons-wrapper">
                <?php
global $current_user;
$x = $current_user->roles;
$y = array_keys(get_option('oplb_gradebook_settings'), true);
$z = array_intersect($x, $y);
if (count($z)) {
    ?>
                <div class="btn-arrange">
                    <button type="button" id="add-student"
                        class="btn btn-default"><?php esc_html_e('Add Student', 'openlab-gradebook')?></button>
                    <button type="button" id="add-assignment"
                        class="btn btn-default"><?php esc_html_e('Add Assignment', 'openlab-gradebook')?></button>
                    <button type="button" id="download-csv"
                        class="btn btn-default"><?php esc_html_e('Download CSV', 'openlab-gradebook')?></button>
                    <div id="savingStatus" class="btn-group hidden">saving</div>
                    <div class="cat-filter">
                        <select name="filter_option" id="filter-assignments-select" class="form-control">
                            <option value="default"><?php esc_html_e('Show all', 'openlab-gradebook')?></option>
                            <%
                            if( assign_categories){
                            for (var i in assign_categories){
                            print('<option value="'+assign_categories[i]+'">'+assign_categories[i]+'</option>');
                            }
                            }
                            %>
                        </select>
                        <button type="button" id="filter-assignments"
                            class="btn btn-default"><?php esc_html_e('Filter', 'openlab-gradebook')?></button>
                    </div>
                </div>
                <?php
}?>
            </div>
            <div class="weight-message">
                <p><%= total_weight %></p>
            </div>
            <div
                class="table-wrapper <% if(assign_length === 0) { print('no-assignments') } else { print('assignments') } %>">
                <div class="pinned hidden-xs hidden-sm">
                    <table id="an-gradebook-container-pinned" class="table table-bordered table-striped">
                        <thead id="students-header-pinned" class="students-header">
                            <tr>
                                <th class="gradebook-student-column-interactive student-tools download-csv adjust-widths"
                                    data-targetwidth="50"></th>
                                <th class="gradebook-student-column-first_name pointer">
                                    <span class="header-wrapper">
                                        <span class="tooltip-wrapper" data-toggle="tooltip"
                                            data-placement="top"
                                            data-target="first_name"
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
                                <th class="gradebook-student-column-last_name pointer">
                                    <span class="header-wrapper sort-up">
                                        <span class="tooltip-wrapper" data-toggle="tooltip"
                                        data-placement="top"
                                        data-target="last_name"
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
                                <th class="gradebook-student-column-user_login pointer">
                                    <span class="header-wrapper">    
                                        <span class="tooltip-wrapper" data-toggle="tooltip"
                                        data-placement="top"
                                        data-target="user_login"
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
                                <th class="gradebook-student-column-average adjust-widths pointer" data-targetwidth="65">
                                    <span class="header-wrapper">
                                        <span class="tooltip-wrapper"
                                        data-toggle="tooltip" data-placement="top" data-target="average"
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
                            </tr>
                        </thead>
                        <tbody id="students-pinned" class="students"></tbody>
                    </table>
                </div>
                <div class="scroll-control">
                    <div class="scrollable">
                        <table id="an-gradebook-container" class="table table-bordered table-striped">
                            <thead id="students-header" class="students-header">
                                <tr></tr>
                            </thead>
                            <tbody id="students" class="students"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>