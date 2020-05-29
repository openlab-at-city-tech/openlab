<td>
    <div class="btn-group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" aria-label="<?php esc_html_e('View Menu', 'openlab-gradebook') ?>">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li class='course-submenu-view'><a href='#gradebook/<%=course.get('id')%>'><?php esc_html_e('View', 'openlab-gradebook') ?></a></li>
            <?php
            global $current_user;
            $x = $current_user->roles;
            $y = array_keys(get_option('oplb_gradebook_settings'), true);
            $z = array_intersect($x, $y);
            if (count($z)) {
            ?>
                <li class='course-submenu-edit'><a href='#'><?php esc_html_e('Edit', 'openlab-gradebook') ?></a></li>
                <li class='course-submenu-export2csv'><a href='#'><?php esc_html_e('Download CSV', 'openlab-gradebook') ?></a></li>
                <li class='course-submenu-delete'><a href='#'><span class="text-danger"><?php esc_html_e('Delete', 'openlab-gradebook') ?></span></a></li>
            <?php } ?>
        </ul>
    </div>
</td>
<td><%= course.get("id") %></td>
<td class="course"><a href='#gradebook/<%=course.get('id')%>'><%= course.get("name") %></a></td>
<td class="school"><%= course.get("school") %></td>
<td class="semester"><%= course.get("semester") %></td>
<td><%= course.get("year") %></td>