<?php

namespace TheLion\OutoftheBox;

class Zip
{
    /**
     * Unique ID.
     *
     * @var string
     */
    public $request_id;

    /**
     * Name of the zip file.
     *
     * @var string
     */
    public $zip_name;
    /**
     * Files that need to be added to ZIP.
     *
     * @var \TheLion\OutoftheBox\Entry[]
     */
    public $entries = [];

    /**
     * Number of bytes that are downloaded so far.
     *
     * @var int
     */
    public $bytes_so_far = 0;

    /**
     * Bytes that need to be download in total.
     *
     * @var int
     */
    public $bytes_total = 0;

    /**
     * Current status.
     *
     * @var string
     */
    public $current_action = 'starting';

    /**
     * Message describing the current status.
     *
     * @var string
     */
    public $current_action_str = '';

    /**
     * @var \TheLion\OutoftheBox\Entry[]
     */
    public $entries_downloaded = [];
    /**
     * @var \TheLion\OutoftheBox\Client
     */
    private $_client;

    /**
     * @var \TheLion\OutoftheBox\Processor
     */
    private $_processor;

    /**
     * @var \ZipStream\ZipStream
     */
    private $_zip_handler;

    public function __construct(Processor $_processor = null, $request_id)
    {
        $this->_client = $_processor->get_client();
        $this->_processor = $_processor;
        $this->request_id = $request_id;
    }

    public function do_zip()
    {
        if (false === $this->is_shortcode_filtered()) {
            $this->download_zip_via_url();
        }

        $this->download_zip_via_server();

        exit();
    }

    /**
     * Use Dropbox ZIP function for complete folder if possible.
     */
    public function download_zip_via_url()
    {
        $requested_ids = [$this->get_processor()->get_requested_complete_path()];

        if (isset($_REQUEST['files'])) {
            $requested_ids = $_REQUEST['files'];
        }

        if (1 !== count($requested_ids)) {
            return false;
        }

        $entry = $this->get_client()->get_entry(reset($requested_ids));

        if (false === $entry) {
            return false;
        }

        if ($entry->is_file()) {
            return false;
        }

        try {
            $download_url = $this->get_client()->get_shared_link($entry).'?dl=1';
        } catch (\Exception $ex) {
            return false;
        }

        header('Location: '.$download_url);

        $this->current_action = 'finished';
        $this->current_action_str = esc_html__('Finished', 'wpcloudplugins');
        $this->set_progress();

        exit();
    }

    public function download_zip_via_server()
    {
        $this->initialize();
        $this->current_action = 'indexing';
        $this->current_action_str = esc_html__('Selecting files...', 'wpcloudplugins');

        $this->index();
        $this->create();

        $this->current_action = 'downloading';
        $this->add_entries();

        $this->current_action = 'finalizing';
        $this->current_action_str = esc_html__('Almost ready', 'wpcloudplugins');
        $this->set_progress();
        $this->finalize();

        $this->current_action = 'finished';
        $this->current_action_str = esc_html__('Finished', 'wpcloudplugins');
        $this->set_progress();
    }

    /**
     * Load the ZIP library and make sure that the root folder is loaded.
     */
    public function initialize()
    {
        ignore_user_abort(false);

        require_once OUTOFTHEBOX_ROOTDIR.'/vendors/ZipStream/vendor/autoload.php';

        // Check if file/folder is cached and still valid
        $folder = $cachedfolder = $this->get_client()->get_folder();

        if (false === $cachedfolder) {
            return new \WP_Error('broke', esc_html__("Requested directory isn't allowed", 'wpcloudplugins'));
        }

        // Check if entry is allowed
        if (!$this->get_processor()->_is_entry_authorized($folder)) {
            return new \WP_Error('broke', esc_html__("Requested directory isn't allowed", 'wpcloudplugins'));
        }

        // Set Zip file name
        $last_folder_path = $this->get_processor()->get_last_path();
        $zip_filename = basename($last_folder_path).'_'.time().'.zip';
        $this->zip_name = apply_filters('outofthebox_zip_filename', $zip_filename, $last_folder_path);

        $this->set_progress();

        // Stop WP from buffering
        if (ob_get_level() > 0) {
            ob_end_clean();
        } else {
            flush();
        }
    }

    /**
     * Create the ZIP File.
     */
    public function create()
    {
        $options = new \ZipStream\Option\Archive();
        $options->setSendHttpHeaders(true);
        $options->setFlushOutput(true);
        $options->setContentType('application/octet-stream');
        header('X-Accel-Buffering: no');

        // create a new zipstream object
        $this->_zip_handler = new \ZipStream\ZipStream(\TheLion\OutoftheBox\Helpers::filter_filename($this->zip_name), $options);
    }

    /**
     * Create a list of files and folders that need to be zipped.
     */
    public function index()
    {
        $requested_ids = [$this->get_processor()->get_requested_complete_path()];

        if (isset($_REQUEST['files'])) {
            $requested_ids = $_REQUEST['files'];
        }

        foreach ($requested_ids as $fileid) {
            $entry = $this->get_client()->get_entry($fileid);

            if (false === $entry) {
                continue;
            }

            if ($entry->is_dir()) {
                $folder = $this->get_client()->get_folder($entry->get_path(), true, true, false);

                if (false === $folder->has_children()) {
                    continue;
                }

                $this->entries = array_merge($this->entries, $folder->get_children());

                foreach ($folder->get_children() as $child) {
                    $this->bytes_total += $child->get_size();
                }
            } else {
                $relative_path = $this->get_processor()->get_relative_path($entry->get_path());
                $entry->set_path($relative_path);
                $relative_path_display = $this->get_processor()->get_relative_path($entry->get_path_display());
                $entry->set_path_display($relative_path_display);
                $this->entries[] = $entry;
                $this->bytes_total += $entry->get_size();
            }

            $this->current_action_str = esc_html__('Selecting files...', 'wpcloudplugins').' ('.count($this->entries).')';
            $this->set_progress();
        }
    }

    /**
     * Add all requests files to Zip file.
     */
    public function add_entries()
    {
        if (count($this->entries) > 0) {
            foreach ($this->entries as $key => $entry) {
                $this->add_file_to_zip($entry);

                unset($this->entries[$key]);

                $this->entries_downloaded[] = $entry;

                do_action('outofthebox_log_event', 'outofthebox_downloaded_entry', $entry, ['as_zip' => true]);

                $this->bytes_so_far += $entry->get_size();
                $this->current_action_str = esc_html__('Downloading...', 'wpcloudplugins').'<br/>('.Helpers::bytes_to_size_1024($this->bytes_so_far).' / '.Helpers::bytes_to_size_1024($this->bytes_total).')';
                $this->set_progress();
            }
        }
    }

    /**
     * Download the request file and add it to the ZIP.
     *
     * @param Entry $file
     */
    public function add_file_to_zip(Entry $entry)
    {
        $path = $entry->get_path_display();

        if ($entry->is_dir()) {
        } else {
            // Download the File
            // Update the time_limit as this can take a while
            @set_time_limit(60);

            $download_stream = fopen('php://temp/maxmemory:'.(5 * 1024 * 1024), 'r+');

            $fileOptions = new \ZipStream\Option\File();
            if (!empty($entry->get_last_edited())) {
                $date = new \DateTime();
                $date->setTimestamp(strtotime($entry->get_last_edited()));
                $fileOptions->setTime($date);
            }

            $fileOptions->setComment((string) $entry->get_description());

            try {
                // @var $download_file \TheLion\OutoftheBox\API\Dropbox\Models\File
                $this->get_client()->get_library()->stream($download_stream, $entry->get_id());
                // Add file contents to zip

                $this->_zip_handler->addFileFromStream(trim($path, '/'), $download_stream, $fileOptions);

                fclose($download_stream);
            } catch (\Exception $ex) {
                error_log('[WP Cloud Plugin message]: '.sprintf('API Error on line %s: %s', __LINE__, $ex->getMessage()));
                fclose($download_stream);

                $this->current_action = 'failed';
                $this->set_progress();

                exit();
            }
        }
    }

    /**
     * Finalize the zip file.
     */
    public function finalize()
    {
        $this->set_progress();

        // Close zip
        $result = $this->_zip_handler->finish();

        // Send email if needed
        if ('1' === $this->get_processor()->get_shortcode_option('notificationdownload')) {
            $this->get_processor()->send_notification_email('download', $this->entries_downloaded);
        }

        // Download Zip Hook
        do_action('outofthebox_download_zip', $this->entries_downloaded);
    }

    /**
     * Received progress information for the ZIP process from database.
     *
     * @param string $request_id
     */
    public static function get_progress($request_id)
    {
        return get_transient('outofthebox_zip_'.substr($request_id, 0, 40));
    }

    /**
     * Set current progress information for ZIP process in database.
     */
    public function set_progress()
    {
        $status = [
            'id' => $this->request_id,
            'status' => [
                'bytes_so_far' => $this->bytes_so_far,
                'bytes_total' => $this->bytes_total,
                'percentage' => ($this->bytes_total > 0) ? (round(($this->bytes_so_far / $this->bytes_total) * 100)) : 0,
                'progress' => $this->current_action,
                'progress_str' => $this->current_action_str,
            ],
        ];

        // Update progress
        return set_transient('outofthebox_zip_'.substr($this->request_id, 0, 40), $status, HOUR_IN_SECONDS);
    }

    /**
     * Get progress information for the ZIP process
     * Used to display a progress percentage on Front-End.
     *
     * @param string $request_id
     */
    public static function get_status($request_id)
    {
        // Try to get the upload status of the file
        for ($_try = 1; $_try < 6; ++$_try) {
            $result = self::get_progress($request_id);

            if (false !== $result) {
                if ('failed' === $result['status']['progress'] || 'finished' === $result['status']['progress']) {
                    delete_transient('outofthebox_zip_'.substr($request_id, 0, 40));
                }

                break;
            }

            // Wait a moment, perhaps the upload still needs to start
            usleep(500000 * $_try);
        }

        if (false === $result) {
            $result = ['file' => false, 'status' => ['bytes_down_so_far' => 0, 'total_bytes_down_expected' => 0, 'percentage' => 0, 'progress' => 'failed']];
        }

        echo json_encode($result);

        exit();
    }

    /**
     * Check if the current shortcode is excluding data from view
     * If that isn't the case, the complete folder can be downloaded instead of indiviual files.
     */
    public function is_shortcode_filtered()
    {
        $ext = $this->get_processor()->get_shortcode_option('ext');
        $exclude = $this->get_processor()->get_shortcode_option('exclude');
        $include = $this->get_processor()->get_shortcode_option('include');

        return
        '1' !== $this->get_processor()->get_shortcode_option('show_files')
         || ('1' !== $this->get_processor()->get_shortcode_option('show_folders'))
          || ('*' !== $ext[0])
           || ('*' !== $exclude[0])
            || ('*' !== $include[0]);
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
