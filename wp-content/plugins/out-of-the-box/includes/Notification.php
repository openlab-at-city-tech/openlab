<?php

namespace TheLion\OutoftheBox;

class Notification
{
    /**
     * Kind of notification.
     *
     * @var string
     */
    public $type;

    /**
     * Addresses of recipients.
     *
     * @var array
     */
    public $recipients = [];

    /**
     * Subject of notification.
     *
     * @var string
     */
    public $subject;

    /**
     * Message of notification.
     *
     * @var string
     */
    public $message;

    /**
     * HTML list of entries for in message.
     *
     * @var string
     */
    public $entry_list;

    /**
     * Array of entries.
     *
     * @var array
     */
    public $entries;

    /**
     * List containing the placeholders and its values.
     *
     * @var array
     */
    public $placeholders;

    /**
     * The Root folder used when the notification is triggered (e.g. the upload folder).
     *
     * @var \TheLion\OutoftheBox\Entry
     */
    public $folder;

    /**
     * True if the user who triggers the notification doesn't need to receive it, while listed as recipient.
     *
     * @var bool
     */
    public $skip_notification_for_current_user = false;

    public function __construct(Processor $_processor, $notification_type, $entries)
    {
        $this->_processor = $_processor;
        $this->type = $notification_type;
        $this->entries = $entries;

        // Load current folder
        $this->folder = $this->get_client()->get_folder($this->get_processor()->get_root_folder(), false);
        if (count($entries) > 0) {
            $first_entry = reset($entries);
            $this->folder = $first_entry->get_parent();
        }

        if ('1' === $this->get_processor()->get_shortcode_option('notification_skip_email_currentuser') && is_user_logged_in()) {
            $this->skip_notification_for_current_user = true;
        }

        $this->_process_subject();
        $this->_process_message();
        $this->_process_entry_list();
        $this->_process_recipients();
    }

    /**
     * Send the actual notification.
     */
    public function send_notification()
    {
        // Create and set placeholders
        $this->_create_placeholders();
        $this->_fill_placeholders();

        // Skip notification of current user if needed
        if ($this->skip_notification_for_current_user) {
            $current_user = wp_get_current_user();
            $this->recipients = array_diff($this->recipients, [$current_user->user_email]);
        }

        do_action('outofthebox_notification_before_send', $this);

        $colors = $this->get_processor()->get_setting('colors');
        $template = apply_filters('outofthebox_notification_set_template', OUTOFTHEBOX_ROOTDIR.'/templates/notifications/default_notification.php', $this);

        $subject = $this->get_subject();
        $message = $this->get_message();

        ob_start();
        include_once $template;
        $htmlmessage = Helpers::compress_html(ob_get_clean());

        // Send mail
        try {
            $headers = ['Content-Type: text/html; charset=UTF-8'];
            $recipients = array_unique($this->get_recipients());

            foreach ($recipients as $recipient) {
                $result = wp_mail($recipient, $subject, $htmlmessage, $headers);
            }
        } catch (\Exception $ex) {
            error_log('[Out-of-the-Box message]: '.sprintf('Could not send notification email on line %s: %s', __LINE__, $ex->getMessage()));
        }

        do_action('outofthebox_notification_sent', $this);
    }

    public function get_type()
    {
        return $this->type;
    }

    public function get_recipients()
    {
        return $this->recipients;
    }

    public function get_subject()
    {
        return $this->subject;
    }

    public function get_message()
    {
        return $this->message;
    }

    public function get_entries()
    {
        return $this->entries;
    }

    public function set_type($type)
    {
        $this->type = $type;
    }

    public function add_recipient($recipient, $id = null)
    {
        if (null !== $id) {
            $this->recipients[$id] = $recipient;
        } else {
            $this->recipients[] = $recipient;
        }
    }

    public function set_recipients($recipients)
    {
        $this->recipients = $recipients;
    }

    public function set_subject($subject)
    {
        $this->subject = $subject;
    }

    public function set_message($message)
    {
        $this->message = $message;
    }

    public function set_entries($entries)
    {
        $this->entries = $entries;
    }

    public function get_entry_list()
    {
        return $this->entry_list;
    }

    public function get_placeholders()
    {
        return $this->placeholders;
    }

    public function get_folder()
    {
        return $this->folder;
    }

    public function get_skip_notification_for_current_user()
    {
        return $this->skip_notification_for_current_user;
    }

    public function set_entry_list($entry_list)
    {
        $this->entry_list = $entry_list;
    }

    public function set_placeholders($placeholders)
    {
        $this->placeholders = $placeholders;
    }

    public function set_folder($folder)
    {
        $this->folder = $folder;
    }

    public function set_skip_notification_for_current_user($skip_notification_for_current_user)
    {
        $this->skip_notification_for_current_user = $skip_notification_for_current_user;
    }

    /**
     * @return \TheLion\OutoftheBox\Processor
     */
    public function get_processor()
    {
        return $this->_processor;
    }

    /**
     * @return \TheLion\OutoftheBox\App
     */
    public function get_app()
    {
        return $this->get_processor()->get_app();
    }

    /**
     * @return \TheLion\OutoftheBox\Client
     */
    public function get_client()
    {
        return $this->get_processor()->get_client();
    }

    /**
     * Set subject of notification using the Global Template setting.
     */
    private function _process_subject()
    {
        switch ($this->type) {
            case 'download':
                if (1 === count($this->entries)) {
                    $template_key = 'download_template_subject';
                } else {
                    $template_key = 'download_template_subject_zip';
                }

                break;
            case 'upload':
                $template_key = 'upload_template_subject';

                break;
            case 'deletion':
            case 'deletion_multiple':
                $template_key = 'delete_template_subject';

                break;
            default:
                $template_key = '';
        }

        $subject_template = $this->get_processor()->get_setting($template_key);
        $subject = apply_filters('outofthebox_notification_set_subject', $subject_template, $this);

        $this->set_subject(trim($subject));
    }

    /**
     * Set message of notification using the Global Template setting.
     */
    private function _process_message()
    {
        switch ($this->type) {
            case 'download':
                $message_key = 'download_template';

                break;
            case 'upload':
                $message_key = 'upload_template';

                break;
            case 'deletion':
            case 'deletion_multiple':
                $message_key = 'delete_template';

                break;
            default:
                $message_key = '';
        }

        $message_template = $this->get_processor()->get_setting($message_key);
        $message = apply_filters('outofthebox_notification_set_message', $message_template, $this);

        $this->set_message(trim($message));
    }

    /**
     * Set file list of notification using the Global Template setting
     * This list is used inside the message.
     */
    private function _process_entry_list()
    {
        $entry_list_template = $this->get_processor()->get_setting('filelist_template');
        $entry_list = apply_filters('outofthebox_notification_set_entry_list', $entry_list_template, $this);

        $this->set_entry_list(trim($entry_list));
    }

    /**
     * Set recipients of notification using the shortcode setting.
     */
    private function _process_recipients()
    {
        $recipients_template_str = $this->get_processor()->get_shortcode_option('notificationemail');
        $recipients_template_arr = array_map('trim', explode(',', $recipients_template_str));

        /* Add addresses of linked users if needed
        * Can't send notifications to linked users when folder is deleted */
        $linked_users_key = array_search('%linked_user_email%', $recipients_template_arr);
        if (false !== $linked_users_key && !in_array($this->type, ['deletion', 'deletion_multiple'])) {
            unset($recipients_template_arr[$linked_users_key]);

            $linked_users = \TheLion\OutoftheBox\Helpers::get_linked_users($this->folder);

            foreach ($linked_users as $userdata) {
                $recipients_template_arr[] = $userdata->user_email;
            }
        }

        $recipients_template_arr = array_unique($recipients_template_arr);
        $recipients = apply_filters('outofthebox_notification_set_recipients', $recipients_template_arr, $this);

        $this->set_recipients($recipients);
    }

    /**
     * Create the placeholder which can be used in the different notification templates.
     */
    private function _create_placeholders()
    {
        $this->placeholders = [
            '%admin_email%' => get_option('admin_email'),
            '%site_name%' => get_bloginfo(),
            '%number_of_files%' => count($this->entries),
            '%ip%' => Helpers::get_user_ip(),
            '%folder_name%' => basename($this->get_folder()),
            '%folder_relative_path%' => $this->get_processor()->get_relative_path($this->get_folder(), $this->get_processor()->get_root_folder()),
            '%folder_absolute_path%' => $this->get_folder(),
            '%folder_url' => 'https://www.dropbox.com/home/'.utf8_encode($this->get_folder()),
        ];

        // Current user data
        $this->placeholders['%user_name%'] = (is_user_logged_in()) ? wp_get_current_user()->display_name : __('An anonymous user', 'outofthebox');
        $this->placeholders['%user_email%'] = (is_user_logged_in()) ? wp_get_current_user()->user_email : '';

        // Location data
        $location_data_required = $this->_is_placeholder_needed('%location%');
        if ($location_data_required) {
            $this->placeholders['%location%'] = Helpers::get_user_location();
        }

        // File list
        $filelist = '';
        foreach ($this->entries as $entry) {
            $url = ($this->get_client()->has_shared_link($entry)) ? $this->get_client()->get_shared_link($entry).'?dl=0' : OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-download&OutoftheBoxpath='.rawurlencode($entry->get_id()).'&lastpath='.rawurlencode($this->get_processor()->get_last_path()).'&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken();

            $fileline = strtr($this->_update_depricated_placeholders($this->entry_list), [
                '%file_name%' => $entry->get_name(),
                '%file_size%' => Helpers::bytes_to_size_1024($entry->get_size()),
                '%file_url%' => $url,
                '%file_relative_path%' => $this->get_processor()->get_relative_path($entry->get_path_display(), $this->get_processor()->get_root_folder()),
                '%file_absolute_path%' => $entry->get_path_display(),
                '%folder_relative_path%' => $this->get_processor()->get_relative_path($this->get_folder(), $this->get_processor()->get_root_folder()),
                '%folder_absolute_path%' => $this->get_folder(),
                '%folder_url' => 'https://www.dropbox.com/home/'.utf8_encode($this->get_folder()),
                '%file_icon%' => $entry->get_default_icon(),
            ]);
            $filelist .= $fileline;
        }
        $this->placeholders['%file_list%'] = $filelist;

        // Set entry placeholders for notifications with a single entry
        $entry = reset($this->entries);
        $this->placeholders['%file_name%'] = $entry->get_name();
        $this->placeholders['%file_size%'] = Helpers::bytes_to_size_1024($entry->get_size());
        $this->placeholders['%file_relative_path%'] = $this->get_processor()->get_relative_path($entry->get_path_display(), $this->get_processor()->get_root_folder());
        $this->placeholders['%file_absolute_path%'] = $entry->get_path_display();
        $this->placeholders['%file_icon%'] = $entry->get_default_icon();
        $url = ($this->get_client()->has_shared_link($entry)) ? $this->get_client()->get_shared_link($entry).'?dl=0' : OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-download&OutoftheBoxpath='.rawurlencode($entry->get_id()).'&lastpath='.rawurlencode($this->get_processor()->get_last_path()).'&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken();
        $this->placeholders['%file_url%'] = $url;

        // Set page url
        $current_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $this->placeholders['%current_url%'] = $current_url;
        $this->placeholders['%page_name%'] = get_bloginfo();

        $post_id = url_to_postid($current_url);

        if ($post_id > 0) {
            $this->placeholders['%page_name%'] = get_the_title($post_id);
        }

        // Add filter for custom placeholders
        $this->placeholders = apply_filters('outofthebox_notification_create_placeholders', $this->placeholders, $this);
    }

    /**
     * Update depricated placeholders in template to their new values.
     *
     * @param string $template
     *
     * @return string
     */
    private function _update_depricated_placeholders($template)
    {
        $template = str_replace('%sitename%', '%site_name%', $template);
        $template = str_replace('%user%', '%user_name%', $template);
        $template = str_replace('%visitor%', '%user_name%', $template);
        $template = str_replace('%filename%', '%file_name%', $template);
        $template = str_replace('%filesize%', '%file_size%', $template);
        $template = str_replace('%filepath%', '%file_path%', $template);
        $template = str_replace('%file_path%', '%file_relative_path%', $template);
        $template = str_replace('%filesafepath%', '%file_safe_path%', $template);
        $template = str_replace('%file_safe_path%', '%file_relative_path%', $template);
        $template = str_replace('%fileicon%', '%file_icon%', $template);
        $template = str_replace('%fileurl%', '%file_url%', $template);
        $template = str_replace('%filelist%', '%file_list%', $template);
        $template = str_replace('%folder%', '%folder_name%', $template);
        $template = str_replace('%folderpath%', '%folder_path%', $template);
        $template = str_replace('%folder_path%', '%folder_relative_path%', $template);

        return str_replace('%currenturl%', '%current_url%', $template);
    }

    /**
     * Check if a placeholder needs to be created for the notification
     * Prevent using too many resources when it's needed. (e.g. receiving user location).
     *
     * @param mixed $placeholder
     */
    private function _is_placeholder_needed($placeholder)
    {
        if (false !== strpos($this->subject, $placeholder)) {
            return true;
        }

        if (false !== strpos($this->message, $placeholder)) {
            return true;
        }

        if (false !== strpos($this->entry_list, $placeholder)) {
            return true;
        }

        foreach ($this->recipients as $recipient) {
            if (false !== strpos($recipient, $placeholder)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Fill the placeholders before sending the notification.
     */
    private function _fill_placeholders()
    {
        $this->subject = strtr($this->_update_depricated_placeholders($this->subject), $this->placeholders);
        $this->message = strtr($this->_update_depricated_placeholders($this->message), $this->placeholders);

        $recipients = [];
        foreach ($this->recipients as $key => $recipient) {
            $recipients[$key] = strtr($this->_update_depricated_placeholders($recipient), $this->placeholders);
        }
        $this->recipients = $recipients;
    }
}
