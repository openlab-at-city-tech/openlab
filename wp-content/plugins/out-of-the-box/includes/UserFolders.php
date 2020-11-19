<?php

namespace TheLion\OutoftheBox;

class UserFolders
{
    /**
     * @var \TheLion\OutoftheBox\Client
     */
    private $_client;

    /**
     * @var \TheLion\OutoftheBox\Processor
     */
    private $_processor;

    /**
     * @var string
     */
    private $_user_name_template;
    private $_user_folder_name;

    public function __construct(Processor $_processor = null)
    {
        $this->_client = $_processor->get_client();
        $this->_processor = $_processor;
        $this->_user_name_template = $this->get_processor()->get_setting('userfolder_name');

        $shortcode = $this->get_processor()->get_shortcode();
        if (!empty($shortcode) && !empty($shortcode['user_folder_name_template'])) {
            $this->_user_name_template = $shortcode['user_folder_name_template'];
        }
    }

    public function get_auto_linked_folder_name_for_user()
    {
        $shortcode = $this->get_processor()->get_shortcode();
        if (!isset($shortcode['user_upload_folders']) || 'auto' !== $shortcode['user_upload_folders']) {
            return false;
        }

        if (!empty($this->_user_folder_name)) {
            return $this->_user_folder_name;
        }

        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $userfoldername = $this->get_user_name_template($current_user);
        } else {
            $userfoldername = $this->get_guest_user_name();
        }

        $this->_user_folder_name = $userfoldername;

        return $userfoldername;
    }

    public function get_auto_linked_folder_for_user()
    {
        // Add folder if needed
        $result = $this->create_user_folder($this->get_auto_linked_folder_name_for_user(), $this->get_processor()->get_shortcode(), 5000000);

        if (false === $result) {
            die();
        }

        return $result->get_path();
    }

    public function get_manually_linked_folder_for_user()
    {
        $userfolder = get_user_option('out_of_the_box_linkedto');
        if (is_array($userfolder) && isset($userfolder['foldertext'])) {
            if (false === isset($userfolder['accountid'])) {
                $linked_account = $this->get_processor()->get_accounts()->get_primary_account();
            } else {
                $linked_account = $this->get_processor()->get_accounts()->get_account_by_id($userfolder['accountid']);
            }

            $this->get_processor()->set_current_account($linked_account);

            return $userfolder['folderid'];
        }
        $defaultuserfolder = get_site_option('out_of_the_box_guestlinkedto');
        if (is_array($defaultuserfolder) && isset($defaultuserfolder['folderid'])) {
            if (false === isset($defaultuserfolder['accountid'])) {
                $linked_account = $this->get_processor()->get_accounts()->get_primary_account();
            } else {
                $linked_account = $this->get_processor()->get_accounts()->get_account_by_id($defaultuserfolder['accountid']);
            }

            $this->get_processor()->set_current_account($linked_account);

            return $defaultuserfolder['folderid'];
        }
        die(-1);
    }

    public function manually_link_folder($user_id, $linkedto)
    {
        if ('GUEST' === $user_id) {
            $result = update_site_option('out_of_the_box_guestlinkedto', $linkedto);
        } else {
            $result = update_user_option($user_id, 'out_of_the_box_linkedto', $linkedto, false);
        }

        if (false !== $result) {
            die('1');
        }
    }

    public function manually_unlink_folder($user_id)
    {
        if ('GUEST' === $user_id) {
            $result = delete_site_option('out_of_the_box_guestlinkedto');
        } else {
            $result = delete_user_option($user_id, 'out_of_the_box_linkedto', false);
        }

        if (false !== $result) {
            die('1');
        }
    }

    public function create_user_folder($userfoldername, $shortcode, $mswaitaftercreation = 0)
    {
        if (false !== strpos($shortcode['root'], '%user_folder%')) {
            $userfolder_path = Helpers::clean_folder_path(str_replace('%user_folder%', $userfoldername, $shortcode['root']));
        } else {
            $userfolder_path = Helpers::clean_folder_path($shortcode['root'].'/'.$userfoldername);
        }

        try {
            $api_entry = $this->get_client()->get_library()->getMetadata($userfolder_path);

            return new Entry($api_entry);
        } catch (\Exception $ex) {
            // Folder doesn't exists, so continue
        }

        $user_template_path = $shortcode['user_template_dir'];

        try {
            if (empty($user_template_path)) {
                $api_entry_new = $this->get_client()->get_library()->createFolder($userfolder_path);
            } else {
                $api_entry_new = $this->get_client()->get_library()->copy($user_template_path, $userfolder_path);

                // New Meta data isn't fully available directly after copy command
                usleep($mswaitaftercreation);
            }
        } catch (\Exception $ex) {
            return false;
        }

        $user_folder = new Entry($api_entry_new);
        do_action('outofthebox_log_event', 'outofthebox_created_entry', $user_folder);

        return $user_folder;
    }

    public function create_user_folders_for_shortcodes($user_id)
    {
        $new_user = get_user_by('id', $user_id);
        $new_userfoldersname = $this->get_user_name_template($new_user);

        $outoftheboxlists = $this->get_processor()->get_shortcodes()->get_all_shortcodes();
        $current_account = $this->get_processor()->get_current_account();

        foreach ($outoftheboxlists as $list) {
            if (!isset($list['user_upload_folders']) || 'auto' !== $list['user_upload_folders']) {
                continue;
            }

            if (!isset($list['account']) || $current_account->get_id() !== $list['account']) {
                continue; // Skip shortcodes that don't belong to the account that is being processed
            }

            if (false === Helpers::check_user_role($list['view_role'], $new_user)) {
                continue; // Skip shortcodes that aren't accessible for user
            }

            $this->create_user_folder($new_userfoldersname, $list);
        }
    }

    public function create_user_folders($users = [])
    {
        if (0 === count($users)) {
            return;
        }

        foreach ($users as $user) {
            $userfoldersname = $this->get_user_name_template($user);
            $this->create_user_folder($userfoldersname, $this->get_processor()->get_shortcode());
        }
    }

    public function remove_user_folder($user_id)
    {
        $deleted_user = get_user_by('id', $user_id);
        $userfoldername = $this->get_user_name_template($deleted_user);

        $outoftheboxlists = $this->get_processor()->get_shortcodes()->get_all_shortcodes();
        $current_account = $this->get_processor()->get_current_account();

        foreach ($outoftheboxlists as $list) {
            if (!isset($list['user_upload_folders']) || 'auto' !== $list['user_upload_folders']) {
                continue;
            }

            if (!isset($list['account']) || $current_account->get_id() !== $list['account']) {
                continue; // Skip shortcodes that don't belong to the account that is being processed
            }

            if (false === Helpers::check_user_role($list['view_role'], $deleted_user)) {
                continue; // Skip shortcodes that aren't accessible for user
            }

            if (false !== strpos($list['root'], '%user_folder%')) {
                $userfolder_path = Helpers::clean_folder_path(str_replace('%user_folder%', $userfoldername, $list['root']));
            } else {
                $userfolder_path = Helpers::clean_folder_path($list['root'].'/'.$userfoldername);
            }

            try {
                $api_entry_deleted = $this->get_client()->get_library()->delete($userfolder_path);
            } catch (\Exception $ex) {
                return false;
            }
        }

        return true;
    }

    public function update_user_folder($user_id, $old_user)
    {
        $updated_user = get_user_by('id', $user_id);
        $new_userfoldersname = $this->get_user_name_template($updated_user);

        $old_userfoldersname = $this->get_user_name_template($old_user);

        if ($new_userfoldersname === $old_userfoldersname) {
            return false;
        }

        $outoftheboxlists = $this->get_processor()->get_shortcodes()->get_all_shortcodes();
        $current_account = $this->get_processor()->get_current_account();

        foreach ($outoftheboxlists as $list) {
            if (!isset($list['user_upload_folders']) || 'auto' !== $list['user_upload_folders']) {
                continue;
            }

            if (!isset($list['account']) || $current_account->get_id() !== $list['account']) {
                continue; // Skip shortcodes that don't belong to the account that is being processed
            }

            if (false === Helpers::check_user_role($list['view_role'], $updated_user)) {
                continue; // Skip shortcodes that aren't accessible for user
            }

            if (defined('out_of_the_box_update_user_folder_'.$list['root'].'_'.$new_userfoldersname)) {
                continue;
            }

            define('out_of_the_box_update_user_folder_'.$list['root'].'_'.$new_userfoldersname, true);

            if (false !== strpos($list['root'], '%user_folder%')) {
                $new_userfolder_path = Helpers::clean_folder_path(str_replace('%user_folder%', $new_userfoldersname, $list['root']));
                $old_userfolder_path = Helpers::clean_folder_path(str_replace('%user_folder%', $old_userfoldersname, $list['root']));
            } else {
                $new_userfolder_path = Helpers::clean_folder_path($list['root'].'/'.$new_userfoldersname);
                $old_userfolder_path = Helpers::clean_folder_path($list['root'].'/'.$old_userfoldersname);
            }

            try {
                $api_entry_move = $this->get_client()->get_library()->move($old_userfolder_path, $new_userfolder_path);
            } catch (\Exception $ex) {
                return false;
            }
        }

        return true;
    }

    public function get_user_name_template($user_data)
    {
        $user_folder_name = strtr($this->_user_name_template, [
            '%user_login%' => isset($user_data->user_login) ? $user_data->user_login : '',
            '%user_email%' => isset($user_data->user_email) ? $user_data->user_email : '',
            '%user_firstname%' => isset($user_data->user_firstname) ? $user_data->user_firstname : '',
            '%user_lastname%' => isset($user_data->user_lastname) ? $user_data->user_lastname : '',
            '%display_name%' => isset($user_data->display_name) ? $user_data->display_name : '',
            '%ID%' => isset($user_data->ID) ? $user_data->ID : '',
            '%user_role%' => isset($user_data->roles) ? implode(',', $user_data->roles) : '',
            '%jjjj-mm-dd%' => date('Y-m-d'),
        ]);

        return apply_filters('outofthebox_private_folder_name', $user_folder_name, $this->get_processor());
    }

    public function get_guest_user_name()
    {
        $username = $this->get_guest_id();

        $current_user = new \stdClass();
        $current_user->user_login = md5($username);
        $current_user->display_name = $username;
        $current_user->ID = $username;
        $current_user->user_role = __('Anonymous user', 'wpcloudplugins');

        $user_folder_name = $this->get_user_name_template($current_user);

        return apply_filters('outofthebox_private_folder_name_guests', __('Guests', 'wpcloudplugins').' - '.$user_folder_name, $this->get_processor());
    }

    public function get_guest_id()
    {
        $id = uniqid();
        if (!isset($_COOKIE['OftB-ID'])) {
            $expire = time() + 60 * 60 * 24 * 7;
            Helpers::set_cookie('OftB-ID', $id, $expire, COOKIEPATH, COOKIE_DOMAIN, false, false, 'strict');
        } else {
            $id = $_COOKIE['OftB-ID'];
        }

        return $id;
    }

    /**
     * @return \TheLion\OutoftheBox\Processor
     */
    public function get_processor()
    {
        return $this->_processor;
    }

    /**
     * @return \TheLion\OutoftheBox\Client
     */
    public function get_client()
    {
        return $this->get_processor()->get_client();
    }
}
