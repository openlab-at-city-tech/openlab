<?php

namespace TheLion\OutoftheBox;

class LinkUsers
{
    /**
     * @var \TheLion\OutoftheBox\Main
     */
    private $_main;

    /**
     * Construct the plugin object.
     */
    public function __construct(Main $main)
    {
        $this->_main = $main;
    }

    public function render()
    {
        wp_enqueue_script('OutoftheBox.PrivateFolders', OUTOFTHEBOX_ROOTPATH.'/includes/js/LinkUsers.min.js', ['OutoftheBox'], OUTOFTHEBOX_VERSION, true);

        include sprintf('%s/templates/admin/private_folders.php', OUTOFTHEBOX_ROOTDIR);
    }

    /**
     * @return \TheLion\OutoftheBox\Main
     */
    public function get_main()
    {
        return $this->_main;
    }
}

// WP_List_Table is not loaded automatically so we need to load it in our application
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH.'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Create a new table class that will extend the WP_List_Table.
 */
class User_List_Table extends \WP_List_Table
{
    /**
     * Prepare the items for the table to process.
     */
    public function prepare_items()
    {
        global $role, $usersearch;

        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $usersearch = isset($_REQUEST['s']) ? wp_unslash(trim($_REQUEST['s'])) : '';
        $role = isset($_REQUEST['role']) ? $_REQUEST['role'] : '';
        $per_page = ($this->is_site_users) ? 'site_users_network_per_page' : 'users_per_page';
        $users_per_page = $this->get_items_per_page($per_page);
        $paged = $this->get_pagenum();
        if ('none' === $role) {
            $args = [
                'number' => $users_per_page,
                'offset' => ($paged - 1) * $users_per_page,
                'include' => wp_get_users_with_no_role($this->site_id),
                'search' => $usersearch,
                'fields' => 'all_with_meta',
            ];
        } else {
            $args = [
                'number' => $users_per_page,
                'offset' => ($paged - 1) * $users_per_page,
                'role' => $role,
                'search' => $usersearch,
                'fields' => 'all_with_meta',
            ];
        }
        if ('' !== $args['search']) {
            $args['search'] = '*'.$args['search'].'*';
        }
        if ($this->is_site_users) {
            $args['blog_id'] = $this->site_id;
        }
        if (isset($_REQUEST['orderby'])) {
            $args['orderby'] = $_REQUEST['orderby'];
        }
        if (isset($_REQUEST['order'])) {
            $args['order'] = $_REQUEST['order'];
        }

        $args = apply_filters('users_list_table_query_args', $args);
        $wp_user_search = new \WP_User_Query($args);

        $data = $this->table_data($wp_user_search->get_results());

        $this->set_pagination_args([
            'total_items' => $wp_user_search->get_total() + 1,
            'per_page' => $users_per_page,
        ]);

        $this->_column_headers = [$columns, $hidden, $sortable];
        $this->items = $data;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table.
     *
     * @return array
     */
    public function get_columns()
    {
        return [
            'id' => 'ID',
            'avatar' => '',
            'username' => esc_html__('Username'),
            'name' => esc_html__('Name'),
            'email' => esc_html__('Email'),
            'role' => esc_html__('Role'),
            'private_folder' => esc_html__('Private Folder', 'wpcloudplugins'),
            'buttons' => '',
        ];
    }

    /**
     * Define which columns are hidden.
     *
     * @return array
     */
    public function get_hidden_columns()
    {
        return ['id'];
    }

    /**
     * Define the sortable columns.
     *
     * @return array
     */
    public function get_sortable_columns()
    {
        return [
            'username' => ['username', false],
            'name' => ['name', false],
            'email' => ['email', false],
            'role' => ['role', false],
            'private_folder' => ['private_folder', false],
        ];
    }

    /**
     * Define what data to show on each column of the table.
     *
     * @param array  $item        Data
     * @param string $column_name - Current column name
     *
     * @return mixed
     */
    public function column_default($item, $column_name)
    {
        global $OutoftheBox;

        switch ($column_name) {
            case 'id':
            case 'avatar':
            case 'email':
            case 'role':
            case 'name':
                return $item[$column_name];

            case 'username':
                if ('GUEST' === $item['id']) {
                    return '<strong>'.$item[$column_name].'</strong>';
                }

                return '<strong><a href="'.get_edit_user_link($item['id']).'" title="'.$item[$column_name].'">'.$item[$column_name].'</a></strong>';

            case 'private_folder':
                $linked_data = $item[$column_name];

                if (isset($linked_data['foldertext'])) {
                    if (!isset($linked_data['accountid'])) {
                        $linked_account = $OutoftheBox->get_accounts()->get_primary_account();
                    } else {
                        $linked_account = $OutoftheBox->get_accounts()->get_account_by_id($linked_data['accountid']);
                    }

                    if (!empty($linked_account)) {
                        return '<code>'.$linked_account->get_email().'</code> <p>'.$linked_data['foldertext'].'</p>';
                    }

                    return '<code>'.sprintf(esc_html__('Account with ID: %s not found', 'wpcloudplugins'), $linked_data['accountid']).'.</code> <p>'.$linked_data['foldertext'].'</p>';
                }

                return '';

            case 'buttons':
                $private_folder = $item['private_folder'];

                $has_link = (!(empty($private_folder) || !is_array($private_folder) || !isset($private_folder['foldertext'])));

                $buttons_html = '<a href="#" title="'.esc_html__('Create link with Private Folder', 'wpcloudplugins').'" class="linkbutton '.(($has_link) ? 'hidden' : '').'" data-user-id="'.$item['id'].'"><i class="eva eva-folder eva-lg" aria-hidden="true"></i> <span class="linkedto">'.esc_html__('Select folder', 'wpcloudplugins').'</span></a>';
                $buttons_html .= '<a href="#" title="'.esc_html__('Break link with Private Folder', 'wpcloudplugins').'" class="unlinkbutton '.(($has_link) ? '' : 'hidden').'" data-user-id="'.$item['id'].'"><i class="eva eva-close-circle eva-lg" aria-hidden="true"></i> <span class="linkedto">'.esc_html__('Unlink', 'wpcloudplugins').'</span></a>';
                $buttons_html .= '<div class="wpcp-spinner"></div>';

                return $buttons_html;

            default:
                return print_r($item, true);
        }
    }

    /**
     * Output 'no users' message.
     */
    public function no_items()
    {
        esc_html_e('No users found.');
    }

    protected function get_views()
    {
        global $role;
        $wp_roles = wp_roles();

        $parts = parse_url(home_url());
        $url = get_admin_url(null, 'admin.php?page=OutoftheBox_settings_linkusers');

        $users_of_blog = count_users();

        $total_users = $users_of_blog['total_users'] + 1;
        $avail_roles = &$users_of_blog['avail_roles'];
        unset($users_of_blog);
        $current_link_attributes = empty($role) ? ' class="current" aria-current="page"' : '';
        $role_links = [];
        $role_links['all'] = "<a href='{$url}'{$current_link_attributes}>".sprintf(_nx('All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_users, 'users'), number_format_i18n($total_users)).'</a>';
        foreach ($wp_roles->get_names() as $this_role => $name) {
            if (!isset($avail_roles[$this_role])) {
                continue;
            }
            $current_link_attributes = '';
            if ($this_role === $role) {
                $current_link_attributes = ' class="current" aria-current="page"';
            }
            $name = translate_user_role($name);
            // translators: User role name with count
            $name = sprintf('%1$s <span class="count">(%2$s)</span>', $name, number_format_i18n($avail_roles[$this_role]));
            $role_links[$this_role] = "<a href='".esc_url(add_query_arg('role', $this_role, $url))."'{$current_link_attributes}>{$name}</a>";
        }
        if (!empty($avail_roles['none'])) {
            $current_link_attributes = '';
            if ('none' === $role) {
                $current_link_attributes = ' class="current" aria-current="page"';
            }
            $name = esc_html__('No role');
            // translators: User role name with count
            $name = sprintf('%1$s <span class="count">(%2$s)</span>', $name, number_format_i18n($avail_roles['none']));
            $role_links['none'] = "<a href='".esc_url(add_query_arg('role', 'none', $url))."'{$current_link_attributes}>{$name}</a>";
        }

        return $role_links;
    }

    protected function get_role_list($user_object)
    {
        $wp_roles = wp_roles();
        $role_list = [];
        foreach ($user_object->roles as $role) {
            if (isset($wp_roles->role_names[$role])) {
                $role_list[$role] = translate_user_role($wp_roles->role_names[$role]);
            }
        }
        if (empty($role_list)) {
            $role_list['none'] = _x('None', 'no user roles');
        }

        return apply_filters('get_role_list', $role_list, $user_object);
    }

    /**
     * Get the table data.
     *
     * @param mixed $users
     *
     * @return array
     */
    private function table_data($users)
    {
        $data = [];

        // Guest Data
        $guestfolder = get_site_option('out_of_the_box_guestlinkedto');

        $data[] = [
            'id' => 'GUEST',
            'avatar' => '<img src="'.OUTOFTHEBOX_ROOTPATH.'/css/images/usericon.png" style="height:32px"/>',
            'username' => esc_html__('Anonymous user', 'wpcloudplugins'),
            'name' => '...'.esc_html__('Default folder for Guests and non-linked Users', 'wpcloudplugins'),
            'email' => '',
            'role' => '',
            'private_folder' => $guestfolder,
            'buttons' => '',
        ];

        

        foreach ($users as $user) {
            // Gravatar
            if (function_exists('get_wp_user_avatar_url')) {
                $display_gravatar = get_wp_user_avatar($user->user_email, 32);
            } else {
                $display_gravatar = get_avatar($user->user_email, 32);
                if (false === $display_gravatar) {
                    //Gravatar is disabled, show default image.
                    $display_gravatar = '<img src="'.OUTOFTHEBOX_ROOTPATH.'/css/images/usericon.png" style="height:32px"/>';
                }
            }

            $curfolder = get_user_option('out_of_the_box_linkedto', $user->ID);
            $data[] = [
                'id' => $user->ID,
                'avatar' => $display_gravatar,
                'username' => $user->user_login,
                'name' => $user->display_name,
                'email' => $user->user_email,
                'role' => implode(', ', $this->get_role_list($user)),
                'private_folder' => $curfolder,
                'buttons' => '',
            ];
        }

        return $data;
    }
}
