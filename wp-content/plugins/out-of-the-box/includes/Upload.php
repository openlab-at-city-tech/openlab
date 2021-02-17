<?php

namespace TheLion\OutoftheBox;

class Upload
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
     * @var WPC_UploadHandler
     */
    private $upload_handler;

    public function __construct(Processor $_processor = null)
    {
        $this->_client = $_processor->get_client();
        $this->_processor = $_processor;

        // Upload File to server
        if (!class_exists('WPC_UploadHandler')) {
            require 'jquery-file-upload/server/UploadHandler.php';
        }
    }

    public function upload_pre_process()
    {
        do_action('outofthebox_upload_pre_process', $this->_processor);

        $result = ['result' => 1];

        $result = apply_filters('outofthebox_upload_pre_process_result', $result, $this->_processor);

        echo json_encode($result);
    }

    public function do_upload()
    {
        if ('1' === $this->get_processor()->get_shortcode_option('demo')) {
            // TO DO LOG + FAIL ERROR
            exit(-1);
        }

        $shortcode_max_file_size = $this->get_processor()->get_shortcode_option('maxfilesize');
        $shortcode_min_file_size = $this->get_processor()->get_shortcode_option('minfilesize');
        $accept_file_types = '/.('.$this->get_processor()->get_shortcode_option('upload_ext').')$/i';
        $post_max_size_bytes = min(Helpers::return_bytes(ini_get('post_max_size')), Helpers::return_bytes(ini_get('upload_max_filesize')));
        $max_file_size = ('0' !== $shortcode_max_file_size) ? Helpers::return_bytes($shortcode_max_file_size) : $post_max_size_bytes;
        $min_file_size = (!empty($shortcode_min_file_size)) ? Helpers::return_bytes($shortcode_min_file_size) : -1;

        $options = [
            'access_control_allow_methods' => ['POST', 'PUT'],
            'accept_file_types' => $accept_file_types,
            'inline_file_types' => '/\.____$/i',
            'orient_image' => false,
            'image_versions' => [],
            'max_file_size' => $max_file_size,
            'min_file_size' => $min_file_size,
            'print_response' => false,
        ];

        $error_messages = [
            1 => __('The uploaded file exceeds the upload_max_filesize directive in php.ini', 'wpcloudplugins'),
            2 => __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', 'wpcloudplugins'),
            3 => __('The uploaded file was only partially uploaded', 'wpcloudplugins'),
            4 => __('No file was uploaded', 'wpcloudplugins'),
            6 => __('Missing a temporary folder', 'wpcloudplugins'),
            7 => __('Failed to write file to disk', 'wpcloudplugins'),
            8 => __('A PHP extension stopped the file upload', 'wpcloudplugins'),
            'post_max_size' => __('The uploaded file exceeds the post_max_size directive in php.ini', 'wpcloudplugins'),
            'max_file_size' => __('File is too big', 'wpcloudplugins'),
            'min_file_size' => __('File is too small', 'wpcloudplugins'),
            'accept_file_types' => __('Filetype not allowed', 'wpcloudplugins'),
            'max_number_of_files' => __('Maximum number of files exceeded', 'wpcloudplugins'),
            'max_width' => __('Image exceeds maximum width', 'wpcloudplugins'),
            'min_width' => __('Image requires a minimum width', 'wpcloudplugins'),
            'max_height' => __('Image exceeds maximum height', 'wpcloudplugins'),
            'min_height' => __('Image requires a minimum height', 'wpcloudplugins'),
        ];

        $hash = $_REQUEST['hash'];
        $path = $_REQUEST['file_path'];

        delete_transient('outofthebox_upload_'.substr($hash, 0, 40));

        $this->upload_handler = new \WPC_UploadHandler($options, false, $error_messages);
        $response = $this->upload_handler->post(false);

        // Upload files to Dropbox
        foreach ($response['files'] as &$file) {
            // Set return Object
            $file->listtoken = $this->get_processor()->get_listtoken();
            $file->name = Helpers::filter_filename(stripslashes(rawurldecode($file->name)), false);
            $file->hash = $hash;
            $file->path = $path;

            if (!isset($file->error)) {
                $return = ['file' => $file, 'status' => ['bytes_up_so_far' => 0, 'total_bytes_up_expected' => $file->size, 'percentage' => 0, 'progress' => 'starting']];
                self::set_upload_progress($hash, $return);

                /** Check if the user hasn't reached its usage limit */
                $max_user_folder_size = $this->get_processor()->get_shortcode_option('max_user_folder_size');
                if ('0' !== $this->get_processor()->get_shortcode_option('user_upload_folders') && '-1' !== $max_user_folder_size) {
                    $disk_usage_after_upload = $this->get_client()->get_folder_size() + $file->size;
                    $max_allowed_bytes = Helpers::return_bytes($max_user_folder_size);
                    if ($disk_usage_after_upload > $max_allowed_bytes) {
                        $return['status']['progress'] = 'upload-failed';
                        $file->error = __('You have reached your usage limit of', 'wpcloudplugins').' '.Helpers::bytes_to_size_1024($max_allowed_bytes);
                        self::set_upload_progress($hash, $return);
                        echo json_encode($return);

                        exit();
                    }
                }

                // Check if file already exists
                if (!empty($file->path)) {
                    $file->name = $file->path.$file->name;
                }

                $filename = apply_filters('outofthebox_upload_file_name', $file->name, $this->get_processor());
                $new_file_path = Helpers::clean_folder_path($this->get_processor()->get_requested_complete_path().'/'.$filename);
                $new_file_path = apply_filters('outofthebox_upload_file_path', $new_file_path, $this->get_processor());

                // Add or update file?
                $params = ['mode' => 'add', 'autorename' => true];

                if ('1' === $this->get_processor()->get_shortcode_option('overwrite')) {
//                    $entry_if_exists = $this->get_client()->get_entry($new_file_path);
//
//                    $file_rev = false;
//                    if (!empty($entry_if_exists)) {
//                        $file_rev = $entry_if_exists->get_rev();
//                    }

                    $params = ['mode' => 'overwrite', 'autorename' => false];
                }

                // Modify the uploaded file if needed
                $file = apply_filters('outofthebox_upload_file_set_object', $file, $this->get_processor());

                // Write file
                $temp_file_path = $file->tmp_path;

                try {
                    $entry = $this->do_upload_to_dropbox($temp_file_path, $new_file_path, $params);
                    $file->completepath = $this->get_processor()->get_relative_path($entry->get_path_display());
                    $file->account_id = $this->get_processor()->get_current_account()->get_id();
                    $file->fileid = base64_encode($new_file_path);
                    $file->filesize = \TheLion\OutoftheBox\Helpers::bytes_to_size_1024($entry->get_size());
                    $file->link = false; // Currently no Direct link available
                } catch (\Exception $ex) {
                    error_log($ex->getMessage());
                    $file->error = __('Not uploaded to the cloud', 'wpcloudplugins').$ex->getMessage();
                }

                $return['status']['progress'] = 'upload-finished';
                $return['status']['percentage'] = '100';

                CacheRequest::clear_local_cache_for_shortcode($this->get_processor()->get_listtoken());
            } else {
                error_log($file->error);
                $return['status']['progress'] = 'upload-failed';
                $file->error = __('Uploading failed', 'wpcloudplugins');
            }
        }

        $return['file'] = $file;
        self::set_upload_progress($hash, $return);

        // Create response
        echo json_encode($return);

        exit();
    }

    public function do_upload_to_dropbox($temp_file_path, $new_file_path, $params = [])
    {
        return $this->get_client()->upload_file($temp_file_path, $new_file_path, $params);
    }

    public function do_upload_direct()
    {
        if ((!isset($_REQUEST['filename'])) || (!isset($_REQUEST['file_size'])) || (!isset($_REQUEST['mimetype']))) {
            exit();
        }

        if ('1' === $this->get_processor()->get_shortcode_option('demo')) {
            echo json_encode(['result' => 0]);

            exit();
        }

        $name = Helpers::filter_filename(stripslashes(rawurldecode($_REQUEST['filename'])), false);
        $size = $_REQUEST['file_size'];
        $path = $_REQUEST['file_path'];
        $mimetype = $_REQUEST['mimetype'];
        $description = sanitize_textarea_field(wp_unslash($_REQUEST['file_description']));

        if (!empty($path)) {
            $name = $path.$name;
        }

        /** Check if the user hasn't reached its usage limit */
        $max_user_folder_size = $this->get_processor()->get_shortcode_option('max_user_folder_size');
        if ('0' !== $this->get_processor()->get_shortcode_option('user_upload_folders') && '-1' !== $max_user_folder_size) {
            $disk_usage_after_upload = $this->get_client()->get_folder_size() + $size;
            $max_allowed_bytes = Helpers::return_bytes($max_user_folder_size);
            if ($disk_usage_after_upload > $max_allowed_bytes) {
                error_log('[WP Cloud Plugin message]: '.__('You have reached your usage limit of', 'outofthedrove').' '.Helpers::bytes_to_size_1024($max_allowed_bytes));
                echo json_encode(['result' => 0]);

                exit();
            }
        }

        // Check if file already exists
        $filename = apply_filters('outofthebox_upload_file_name', $name, $this->get_processor());
        $new_file_path = Helpers::clean_folder_path($this->get_processor()->get_requested_complete_path().'/'.$filename);
        $new_file_path = apply_filters('outofthebox_upload_file_path', $new_file_path, $this->get_processor());

        // Add or update file?
        $params = ['mode' => 'add', 'autorename' => true];

        if ('1' === $this->get_processor()->get_shortcode_option('overwrite')) {
//            $entry_if_exists = $this->get_client()->get_entry($new_file_path);
//
//            $file_rev = false;
//            if (!empty($entry_if_exists)) {
//                $file_rev = $entry_if_exists->get_rev();
//            }

            $params = ['mode' => 'overwrite', 'autorename' => false];
        }

        $origin = $_REQUEST['orgin'];

        try {
            $temporarily_link = $this->get_client()->get_library()->getTemporarilyUploadLink($new_file_path, $params, $origin);
            echo json_encode(['result' => 1, 'url' => $temporarily_link->getLink(), 'convert' => false, 'id' => base64_encode($new_file_path)]);
        } catch (\Exception $ex) {
            error_log('[WP Cloud Plugin message]: '.sprintf('Not uploaded to the cloud on line %s: %s', __LINE__, $ex->getMessage()));
            echo json_encode(['result' => 0]);
        }

        exit();
    }

    public static function get_upload_progress($file_hash)
    {
        return get_transient('outofthebox_upload_'.substr($file_hash, 0, 40));
    }

    public static function set_upload_progress($file_hash, $status)
    {
        // Update progress
        return set_transient('outofthebox_upload_'.substr($file_hash, 0, 40), $status, HOUR_IN_SECONDS);
    }

    public function get_upload_status()
    {
        $hash = $_REQUEST['hash'];

        // Try to get the upload status of the file
        for ($_try = 1; $_try < 10; ++$_try) {
            $result = self::get_upload_progress($hash);

            if (false !== $result) {
                if ('upload-failed' === $result['status']['progress'] || 'upload-finished' === $result['status']['progress']) {
                    delete_transient('outofthebox_upload_'.substr($hash, 0, 40));
                }

                break;
            }

            // Wait a moment, perhaps the upload still needs to start
            usleep(1000000 * $_try);
        }

        if (false === $result) {
            $result = ['file' => false, 'status' => ['bytes_up_so_far' => 0, 'total_bytes_up_expected' => 0, 'percentage' => 0, 'progress' => 'no-progress-found']];
        }

        echo json_encode($result);

        exit();
    }

    public function upload_convert()
    {
        // NOT IMPLEMENTED
    }

    public function upload_post_process()
    {
        if ((!isset($_REQUEST['files'])) || 0 === count($_REQUEST['files'])) {
            echo json_encode(['result' => 0]);

            exit();
        }

        $uploaded_files = $_REQUEST['files'];
        $_uploaded_entries = [];

        foreach ($uploaded_files as $file_id) {
            $base64_id = base64_decode($file_id, true);
            $file_id = (false === $base64_id) ? $file_id : $base64_id;

            try {
                $api_entry = $this->get_client()->get_library()->getMetadata($file_id);
                $entry = new Entry($api_entry);
            } catch (\Exception $ex) {
                continue;
            }

            if (false === $entry) {
                continue;
            }

            // Upload Hook
            do_action('outofthebox_upload', $entry);
            $_uploaded_entries[] = $entry;

            do_action('outofthebox_log_event', 'outofthebox_uploaded_entry', $entry);
        }

        // Send email if needed
        if (count($_uploaded_entries) > 0) {
            if ('1' === $this->get_processor()->get_shortcode_option('notificationupload')) {
                $this->get_processor()->send_notification_email('upload', $_uploaded_entries);
            }
        }

        // Return information of the files
        $files = [];
        foreach ($_uploaded_entries as $entry) {
            $relative_path_display = $this->get_processor()->get_relative_path($entry->get_path_display());
            $entry->set_path_display($relative_path_display);

            $link = ($this->get_client()->has_shared_link($entry)) ? $this->get_client()->get_shared_link($entry).'?dl=0' : OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-download&OutoftheBoxpath='.rawurlencode($entry->get_id()).'&lastpath='.rawurlencode($this->get_processor()->get_last_path()).'&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken();

            $file = [];
            $file['name'] = $entry->get_name();
            $file['type'] = $entry->get_mimetype();
            $file['completepath'] = $entry->get_path_display();
            $file['description'] = $entry->get_description();
            $file['account_id'] = $this->get_processor()->get_current_account()->get_id();
            $file['fileid'] = $entry->get_id();
            $file['filesize'] = \TheLion\OutoftheBox\Helpers::bytes_to_size_1024($entry->get_size());
            $file['link'] = $link;
            $file['folderurl'] = urlencode('https://www.dropbox.com/home'.rtrim($entry->get_parent(), '/'));

            $files[$file['fileid']] = apply_filters('outofthebox_upload_entry_information', $file, $entry, $this->_processor);
        }

        do_action('outofthebox_upload_post_process', $_uploaded_entries, $this->_processor);

        // Clear Cached Requests
        CacheRequest::clear_request_cache();

        echo json_encode(['result' => 1, 'files' => $files]);
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
        return $this->_client;
    }

    /**
     * @return \TheLion\OutoftheBox\App
     */
    public function get_app()
    {
        return $this->get_processor()->get_app();
    }
}
