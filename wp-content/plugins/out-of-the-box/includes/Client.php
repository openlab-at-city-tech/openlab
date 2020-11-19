<?php

namespace TheLion\OutoftheBox;

class Client
{
    /**
     * @var \TheLion\OutoftheBox\App
     */
    private $_app;

    /**
     * @var \Kunnu\Dropbox\Dropbox
     */
    private $_client;

    /**
     * @var \TheLion\OutoftheBox\Processor
     */
    private $_processor;

    public function __construct(App $_app, Processor $_processor = null)
    {
        $this->_app = $_app;
        $this->_client = $_app->get_client();
        $this->_processor = $_processor;
    }

    public function get_account_info()
    {
        return $this->_client->getCurrentAccount();
    }

    public function get_account_space_info()
    {
        return $this->_client->getSpaceUsage();
    }

    public function get_entry($requested_path = null, $check_if_allowed = true)
    {
        if (null === $requested_path) {
            $requested_path = $this->get_processor()->get_requested_complete_path();
        }

        // Clean path if needed
        if (false !== strpos($requested_path, '/')) {
            $requested_path = Helpers::clean_folder_path($requested_path);
        }

        // Get entry meta data (no meta data for root folder_
        if ('/' === $requested_path || '' === $requested_path) {
            $entry = new Entry();
            $entry->set_id('Root');
            $entry->set_name('Root');
            $entry->set_path('');
            $entry->set_is_dir(true);
        } else {
            try {
                $api_entry = $this->_client->getMetadata($requested_path, ['include_media_info' => true]);
                $entry = new Entry($api_entry);

                if ($entry->is_file() && $entry->has_own_thumbnail()) {
                    $media_info = $api_entry->getMediaInfo();
                    $cached_entry = $this->get_cache()->add_to_cache($entry);

                    if ($media_info instanceof \Kunnu\Dropbox\Models\MediaInfo) {
                        $cached_entry->add_media_info($media_info);
                        $this->_processor->get_cache()->set_updated();
                        $this->_processor->get_cache()->update_cache();
                    }
                }
            } catch (\Exception $ex) {
                error_log('[WP Cloud Plugin message]: '.sprintf('API Error on line %s: %s', __LINE__, $ex->getMessage()));

                return false;
            }
        }

        if ($check_if_allowed && !$this->get_processor()->_is_entry_authorized($entry)) {
            die('-1');
        }

        return $entry;
    }

    public function get_multiple_entries($entries = [])
    {
        $dropbox_entries = [];
        foreach ($entries as $entry) {
            $dropbox_entry = $this->get_entry($entry, false);
            if (!empty($dropbox_entry)) {
                $dropbox_entries[] = $dropbox_entry;
            }
        }

        return $dropbox_entries;
    }

    /**
     * @param string $requested_path
     * @param bool   $check_if_allowed
     * @param mixed  $recursive
     * @param mixed  $hierarchical
     *
     * @return bool|\TheLion\OutoftheBox\Entry
     */
    public function get_folder($requested_path = null, $check_if_allowed = true, $recursive = false, $hierarchical = true)
    {
        if (null === $requested_path) {
            $requested_path = $this->get_processor()->get_requested_complete_path();
        }

        // Clean path if needed
        if (false !== strpos($requested_path, '/')) {
            $requested_path = Helpers::clean_folder_path($requested_path);
        }

        $folder = null;
        $children = [];

        // Get folder children
        try {
            $api_folders_contents = $this->_client->listFolder($requested_path, ['recursive' => $recursive]);
            $api_entries = $api_folders_contents->getItems()->toArray();

            while ($api_folders_contents->hasMoreItems()) {
                $cursor = $api_folders_contents->getCursor();
                $api_folders_contents = $this->_client->listFolderContinue($cursor);
                $api_entries = array_merge($api_entries, $api_folders_contents->getItems()->toArray());
            }
        } catch (\Exception $ex) {
            error_log('[WP Cloud Plugin message]: '.sprintf('API Error on line %s: %s', __LINE__, $ex->getMessage()));
            die('-1');
        }

        if (count($api_entries) > 0) {
            foreach ($api_entries as $api_entry) {
                $entry = new Entry($api_entry);

                if ($check_if_allowed && false === $this->get_processor()->_is_entry_authorized($entry)) {
                    continue;
                }

                $relative_path = $this->get_processor()->get_relative_path($entry->get_path());
                $entry->set_path($relative_path);
                $relative_path_display = $this->get_processor()->get_relative_path($entry->get_path_display());
                $entry->set_path_display($relative_path_display);
                $children[$entry->get_id()] = $entry;
            }
        }

        // Sort contents
        if (count($children) > 0) {
            $children = $this->get_processor()->sort_filelist($children);
        }

        // Make a hierarchical structure if a recursive reponse is requested
        if ($recursive && $hierarchical) {
            foreach ($children as $id => $child) {
                $relative_path = $this->get_processor()->get_relative_path($child->get_parent());
                $parent_id = Helpers::find_item_in_array_with_value($children, 'path', $relative_path);

                if (false === $parent_id || $parent_id === $child->get_id()) {
                    $child->flag = false;

                    continue;
                }

                $parent = $children[$parent_id];
                $parent_childs = $parent->get_children();
                $parent_childs[$child->get_id()] = $child;
                $parent->set_children($parent_childs);

                $child->flag = true;
            }

            foreach ($children as $id => $child) {
                if ($child->flag) {
                    unset($children[$id]);
                }
            }
        }

        // Get folder meta data (no meta data for root folder_
        if ('' === $requested_path) {
            $folder_entry = new Entry();
            $folder_entry->set_path($requested_path);
            $folder_entry->set_is_dir(true);
            $folder_entry->set_children($children);
        } elseif (!$recursive || !$hierarchical) {
            $api_entry = $this->_client->getMetadata($requested_path);
            $folder_entry = new Entry($api_entry);
            $folder_entry->set_children($children);
        } else {
            $folder_entry = reset($children);
        }

        return $folder_entry;
    }

    public function search($search_query)
    {
        $found_entries = [];

        // Get requested path
        $requested_path = $this->get_processor()->get_requested_complete_path();

        // Set Search settings
        $folder_to_search_in = ('parent' === $this->get_processor()->get_shortcode_option('searchfrom')) ? $requested_path : $this->get_processor()->get_root_folder();
        $search_for = ('1' === $this->get_processor()->get_shortcode_option('search_contents')) ? 'filename_and_content' : 'filename';

        do_action('outofthebox_log_event', 'outofthebox_searched', $folder_to_search_in, ['query' => $search_query]);

        // Get Results
        try {
            $api_search_result = $this->_client->search($folder_to_search_in, $search_query, ['mode' => $search_for, 'max_results' => 1000]);
            $api_entries = $api_search_result->getItems()->toArray();

            while ($api_search_result->hasMoreItems()) {
                $cursor = $api_search_result->getCursor();
                $api_search_result = $this->_client->search($folder_to_search_in, $search_query, ['mode' => $search_for, 'start' => $cursor, 'max_results' => 1000]);
                $api_entries = array_merge($api_entries, $api_search_result->getItems()->toArray());
            }
        } catch (\Exception $ex) {
            error_log('[WP Cloud Plugin message]: '.sprintf('API Error on line %s: %s', __LINE__, $ex->getMessage()));
            die('-1');
        }

        // Sort contents
        if (count($api_entries) > 0) {
            foreach ($api_entries as $search_result) {
                $entry = new Entry($search_result->getMetadata());

                if ($this->get_processor()->_is_entry_authorized($entry)) {
                    $relative_path = $this->get_processor()->get_relative_path($entry->get_path());
                    $entry->set_path($relative_path);
                    $relative_path_display = $this->get_processor()->get_relative_path($entry->get_path_display());
                    $entry->set_path_display($relative_path_display);
                    $found_entries[$entry->get_id()] = $entry;
                }
            }
        }

        $folder = new Entry();
        $folder->set_name(basename($folder_to_search_in));
        $folder->set_path($this->get_processor()->get_relative_path($folder_to_search_in));
        $folder->set_is_dir(true);
        $folder->set_children($found_entries);

        return $folder;
    }

    public function get_folder_size($requested_path = null)
    {
        if (null === $requested_path) {
            $requested_path = $this->get_processor()->get_requested_complete_path();
        }

        // Clean path if needed
        if (false !== strpos($requested_path, '/')) {
            $requested_path = Helpers::clean_folder_path($requested_path);
        }

        $folder = null;
        $children = [];

        // Get folder children
        try {
            $api_folders_contents = $this->_client->listFolder($requested_path, ['recursive' => true]);
            $api_entries = $api_folders_contents->getItems()->toArray();

            while ($api_folders_contents->hasMoreItems()) {
                $cursor = $api_folders_contents->getCursor();
                $api_folders_contents = $this->_client->listFolderContinue($cursor);
                $api_entries = array_merge($api_entries, $api_folders_contents->getItems()->toArray());
            }

            unset($api_folders_contents);
        } catch (\Exception $ex) {
            error_log('[WP Cloud Plugin message]: '.sprintf('API Error on line %s: %s', __LINE__, $ex->getMessage()));

            return null;
        }

        $total_size = 0;

        foreach ($api_entries as $api_entry) {
            $total_size += ($api_entry instanceof \Kunnu\Dropbox\Models\FolderMetadata) ? 0 : $api_entry->size;
        }

        unset($api_entries);

        return $total_size;
    }

    public function preview_entry()
    {
        // Get file meta data
        $entry = $this->get_entry();

        if (false === $entry) {
            die('-1');
        }

        if (false === $entry->get_can_preview_by_cloud()) {
            die('-1');
        }

        if (false === $this->get_processor()->get_user()->can_preview()) {
            die('-1');
        }

        do_action('outofthebox_log_event', 'outofthebox_previewed_entry', $entry);

        // Preview for Media files in HTML5 Player
        if (in_array($entry->get_extension(), ['mp4', 'm4v', 'ogg', 'ogv', 'webmv', 'mp3', 'm4a', 'ogg', 'oga', 'wav'])) {
            if ($this->has_shared_link($entry)) {
                $temporarily_link = $this->get_shared_link($entry).'?raw=1';
            } else {
                $temporarily_link = $this->get_temporarily_link($entry);
            }
            header('Location: '.$temporarily_link);
            die();
        }

        // Preview for Image files
        if (in_array($entry->get_extension(), ['txt', 'jpg', 'jpeg', 'gif', 'png'])) {
            $shared_link = $this->get_shared_link($entry);
            header('Location: '.$shared_link.'?raw=1');
            die();
        }

        // Preview for PDF files, read only via Google Viewer when needed
        if ('pdf' === $entry->get_extension()) {
            $shared_link = $this->get_shared_link($entry).'?raw=1';
            if (false === $this->get_processor()->get_user()->can_download() && $entry->get_size() < 25000000) {
                $shared_link = 'https://docs.google.com/viewer?embedded=true&url='.$shared_link;
            }
            header('Location: '.$shared_link);
            die();
        }

        // Preview for PDF files
        // Preview for Excel files
        if (in_array($entry->get_extension(), ['xls', 'xlsx', 'xlsm', 'gsheet', 'csv'])) {
            header('Content-Type: text/html');
        } else {
            header('Content-Disposition: inline; filename="'.$entry->get_basename().'.pdf"');
            header('Content-Description: "'.$entry->get_basename().'"');
            header('Content-Type: application/pdf');
        }

        try {
            $preview_file = $this->_client->preview($entry->get_path());
            echo $preview_file->getContents();
        } catch (\Exception $ex) {
            error_log('[WP Cloud Plugin message]: '.sprintf('API Error on line %s: %s', __LINE__, $ex->getMessage()));
            die('-1');
        }

        die();
    }

    public function download_entry($entry = null)
    {
        if (null === $entry) {
            // Get file meta data
            $entry = $this->get_entry();
        }

        if (false === $entry) {
            die(-1);
        }

        // TO DO Download notifications
        if ('1' === $this->get_processor()->get_shortcode_option('notificationdownload')) {
            $this->get_processor()->send_notification_email('download', [$entry]);
        }

        // If there is a temporarily download url present for this file, just redirect the user
        $stream = (isset($_REQUEST['action']) && 'outofthebox-stream' === $_REQUEST['action'] && !isset($_REQUEST['caption']));
        $stored_url = ($stream) ? get_transient('outofthebox_stream_'.$entry->get_id().'_'.$entry->get_extension()) : get_transient('outofthebox_download_'.$entry->get_id().'_'.$entry->get_extension());
        if (false !== $stored_url && filter_var($stored_url, FILTER_VALIDATE_URL)) {
            do_action('outofthebox_download', $entry, $stored_url);
            header('Location: '.$stored_url);
            die();
        }

        // Render file via browser
        //if (in_array($entry->get_extension(), array('csv', 'html'))) {
        //    $download_file = $this->_client->download($entry->get_id());
        //    echo $download_file->getContents();
        //    die();
        //}

        if (0 !== count($entry->save_as)) {
            $this->export_entry($entry);

            do_action('outofthebox_download', $entry, null);
            do_action('outofthebox_log_event', 'outofthebox_downloaded_entry', $entry);
            die();
        }
        if ('url' === $entry->get_extension()) {
            $download_file = $this->_client->download($entry->get_id());
            preg_match_all('/URL=(.*)/', $download_file->getContents(), $location, PREG_SET_ORDER);

            if (2 === count($location[0])) {
                $temporarily_link = $location[0][1];
            }
        } else {
            $temporarily_link = $this->get_temporarily_link($entry);
        }

        // Download Hook
        do_action('outofthebox_download', $entry, $temporarily_link);

        $event_type = (isset($_REQUEST['action']) && 'outofthebox-stream' === $_REQUEST['action']) ? 'outofthebox_streamed_entry' : 'outofthebox_downloaded_entry';
        do_action('outofthebox_log_event', $event_type, $entry);

        if ('redirect' === $this->get_processor()->get_setting('download_method') && !isset($_REQUEST['proxy'])) {
            header('Location: '.$temporarily_link);
            set_transient('outofthebox_'.(($stream) ? 'stream' : 'download').'_'.$entry->get_id().'_'.$entry->get_extension(), $temporarily_link, MINUTE_IN_SECONDS * 10);
        } else {
            $this->download_via_proxy($entry, $temporarily_link);
        }

        die();
    }

    public function export_entry(Entry $entry, $export_as = 'default')
    {
        if ('default' === $export_as) {
            $export_as = $entry->get_save_as();
        }

        $filename = ('default' === $export_as) ? $entry->get_name() : $entry->get_basename().'.'.$export_as;

        @set_time_limit(60);

        // Get file
        $stream = fopen('php://temp', 'r+');

        if (ob_get_level() > 0) {
            ob_end_clean(); // Stop WP from buffering
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; '.sprintf('filename="%s"; ', rawurlencode($filename)).sprintf("filename*=utf-8''%s", rawurlencode($filename)));

        try {
            flush();

            $export_file = $this->_client->download($entry->get_id(), $export_as);

            fwrite($stream, $export_file->getContents());
            rewind($stream);

            unset($export_file);

            while (!@feof($stream)) {
                echo @fread($stream, 1024 * 1024);
                ob_flush();
                flush();
            }
        } catch (\Exception $ex) {
            error_log('[WP Cloud Plugin message]: '.sprintf('API Error on line %s: %s', __LINE__, $ex->getMessage()));
        }

        fclose($stream);

        die();
    }

    public function download_via_proxy(Entry $entry, $url, $inline = false)
    {
        if (ob_get_level() > 0) {
            ob_end_clean(); // Stop WP from buffering
        }

        set_time_limit(500);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: '.($inline ? 'inline' : 'attachment').'; filename="'.basename($entry->get_name()).'"');
        header("Content-length: {$entry->get_size()}");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 500);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($curl, $data) {
            echo $data;

            return strlen($data);
        });
        curl_exec($ch);
        curl_close($ch);

        die();
    }

    public function stream_entry()
    {
        // Get file meta data
        $entry = $this->get_entry();

        if (false === $entry) {
            die(-1);
        }

        $extension = $entry->get_extension();
        $allowedextensions = ['mp4', 'm4v', 'ogg', 'ogv', 'webmv', 'mp3', 'm4a', 'oga', 'wav', 'webm', 'vtt'];

        if (empty($extension) || !in_array($extension, $allowedextensions)) {
            die();
        }

        // Download Captions directly
        if ('vtt' === $extension) {
            $temporarily_link = $this->get_temporarily_link($entry);
            $this->download_via_proxy($entry, $temporarily_link);
            die();
        }

        $this->download_entry();
    }

    public function get_thumbnail(Entry $entry, $aslink = false, $width = null, $height = null, $crop = false)
    {
        if (false === $entry->has_own_thumbnail()) {
            $thumbnail_url = $entry->get_icon_large();
        } else {
            $thumbnail = new \TheLion\OutoftheBox\Thumbnail($this->get_processor(), $entry, $width, $height, $crop);
            $thumbnail_url = $thumbnail->get_url();
        }

        if ($aslink) {
            return $thumbnail_url;
        }
        header('Location: '.$thumbnail_url);

        die();
    }

    public function build_thumbnail()
    {
        $src = $_REQUEST['src'];
        preg_match_all('/(.+)_w(\d+)h(\d+)_c(\d)_([a-z]+)/', $src, $attr, PREG_SET_ORDER);

        if (1 !== count($attr) || 6 !== count($attr[0])) {
            die();
        }

        $entry_id = $attr[0][1];
        $width = $attr[0][2];
        $height = $attr[0][3];
        $crop = $attr[0][4];
        $format = $attr[0][5];

        $entry = $this->get_entry($entry_id, false);

        if (false === $entry) {
            die(-1);
        }

        if (false === $entry->has_own_thumbnail()) {
            header('Location: '.$entry->get_icon_large());
            die();
        }

        $thumbnail = new Thumbnail($this->get_processor(), $entry, $width, $height, $crop, $format);

        if (false === $thumbnail->does_thumbnail_exist()) {
            $thumbnail_created = $thumbnail->build_thumbnail();

            if (false === $thumbnail_created) {
                header('Location: '.$entry->get_icon_large());
                die();
            }
        }

        header('Location: '.$thumbnail->get_url());

        die();
    }

    public function has_temporarily_link(Entry $entry)
    {
        $cached_entry = $this->get_cache()->is_cached($entry->get_id());

        if (false !== $cached_entry) {
            if ($temporarily_link = $cached_entry->get_temporarily_link()) {
                return true;
            }
        }

        return false;
    }

    public function get_temporarily_link(Entry $entry)
    {
        $cached_entry = $this->get_cache()->is_cached($entry->get_id());

        if (false !== $cached_entry) {
            if ($temporarily_link = $cached_entry->get_temporarily_link()) {
                return $temporarily_link;
            }
        }

        try {
            $temporarily_link = $this->_client->getTemporaryLink($entry->get_path());
            $cached_entry = $this->get_cache()->add_to_cache($entry);

            $max_cache_request = ((int) $this->get_processor()->get_setting('request_cache_max_age')) * 60;
            $expires = time() + (4 * 60 * 60) - $max_cache_request;

            $cached_entry->add_temporarily_link($temporarily_link->getLink(), $expires);
        } catch (\Exception $ex) {
            error_log('[WP Cloud Plugin message]: '.sprintf('API Error on line %s: %s', __LINE__, $ex->getMessage()));

            return false;
        }

        $this->get_cache()->set_updated();

        return $cached_entry->get_temporarily_link();
    }

    public function has_shared_link(Entry $entry, $visibility = 'public')
    {
        $cached_entry = $this->get_cache()->is_cached($entry->get_id());

        if (false !== $cached_entry) {
            if ($shared_link = $cached_entry->get_shared_link($visibility)) {
                return true;
            }
        }

        return false;
    }

    public function get_shared_link(Entry $entry, $visibility = 'public')
    {
        $cached_entry = $this->get_cache()->is_cached($entry->get_id());

        if (false !== $cached_entry) {
            if ($shared_link = $cached_entry->get_shared_link($visibility)) {
                return $shared_link;
            }
        }

        $shared_link = $this->create_shared_link($entry, $visibility);

        do_action('outofthebox_log_event', 'outofthebox_created_link_to_entry', $entry, ['url' => $shared_link]);

        return $shared_link;
    }

    public function create_shared_link(Entry $entry, $visibility)
    {
        $cached_entry = $this->get_cache()->add_to_cache($entry);
        $shared_link = false;

        try {
            $shared_link_info = $this->_client->createSharedLinkWithSettings($entry->get_path(), ['requested_visibility' => $visibility]);
            $this->get_cache()->set_updated();
            $shared_link = $cached_entry->add_shared_link($shared_link_info);

            do_action('outofthebox_log_event', 'outofthebox_updated_metadata', $entry, ['metadata_field' => 'Sharing Permissions']);
        } catch (\Kunnu\Dropbox\Exceptions\DropboxClientException $ex) {
            if ('shared_link_already_exists' === $ex->getError() || (false !== strpos($ex->getErrorSummary(), 'shared_link_already_exists'))) {
                // Get existing shared link
                $shared_links = $this->_client->listSharedLinks($entry->get_path());
                $shared_links->getItems()->each(function ($shared_link_info, $key) use ($cached_entry) {
                    $cached_entry->add_shared_link($shared_link_info);
                });

                $this->get_cache()->set_updated();
                $shared_link = $cached_entry->get_shared_link($visibility);

                if (empty($shared_link)) {
                    die(sprintf(__('The sharing permissions on this file is preventing you from accessing a %s shared link. Please contact the administrator to change the sharing settings for this document in the cloud.'), $visibility));
                }

                do_action('outofthebox_log_event', 'outofthebox_updated_metadata', $entry, ['metadata_field' => 'Sharing Permissions']);
            } else {
                error_log('[WP Cloud Plugin message]: '.sprintf('API Error on line %s: %s', __LINE__, $ex->getErrorSummary()));
                die($ex->getErrorSummary());

                return false;
            }
        }

        return $shared_link;
    }

    public function get_embedded_link(Entry $entry)
    {
        $shared_link = $this->get_shared_link($entry).'?raw=1';

        if (
                !in_array($entry->get_extension(), ['pdf', 'jpg', 'jpeg', 'png', 'gif'])
        ) {
            // Embed via Google
            // Update URL so that it directly points to the content
            $shared_link = str_replace('www.dropbox.com', 'dl.dropboxusercontent.com', $shared_link);
            $shared_link = 'https://docs.google.com/viewer?embedded=true&url='.rawurlencode($shared_link);
        }

        return $shared_link;
    }

    public function get_shared_link_for_output($entry_path = null)
    {
        $entry = $this->get_entry($entry_path);

        if (false === $entry) {
            die(-1);
        }

        $shared_link = $this->get_shared_link($entry).'?dl=1';
        $embed_link = $this->get_embedded_link($entry);

        return [
            'name' => $entry->get_name(),
            'extension' => $entry->get_extension(),
            'link' => $this->shorten_url($entry, $shared_link),
            'embeddedlink' => $embed_link,
            'size' => Helpers::bytes_to_size_1024($entry->get_size()),
            'error' => false,
        ];
    }

    public function shorten_url($entry, $url)
    {
        try {
            switch ($this->get_processor()->get_setting('shortlinks')) {
                case 'Bit.ly':

                    $requestBody = json_encode(['long_url' => $url]);
                    $headers = ['Authorization ' => 'Bearer '.$this->get_processor()->get_setting('bitly_apikey'), 'Content-Type' => 'application/json'];
                    $rawResponse = $this->get_library()->getClient()->getHttpClient()->send('https://api-ssl.bitly.com/v4/shorten', 'POST', $requestBody, $headers);

                    $body = $rawResponse->getBody();
                    $data = json_decode($body, true);

                    return $data['link'];
                case 'Shorte.st':
                    $rawResponse = $this->get_library()->getClient()->getHttpClient()->send('https://api.shorte'.'.st/s/'.$this->get_processor()->get_setting('shortest_apikey').'/'.$url, 'GET', '');
                    $body = $rawResponse->getBody();
                    $data = json_decode($body, true);

                    return $data['shortenedUrl'];
                case 'Rebrandly':
                    $requestBody = json_encode(['title' => $entry->get_name(), 'destination' => $url, 'domain' => $this->get_processor()->get_setting('rebrandly_domain')]);
                    $headers = ['apikey' => $this->get_processor()->get_setting('rebrandly_apikey'), 'Content-Type' => 'application/json'];
                    $rawResponse = $this->get_library()->getClient()->getHttpClient()->send('https://api.rebrandly.com/v1/links', 'POST', $requestBody, $headers);
                    $body = $rawResponse->getBody();
                    $data = json_decode($body, true);

                    return '//'.$data['shortUrl'];
                case 'None':
                default:
                    break;
            }
        } catch (\Exception $ex) {
            error_log('[WP Cloud Plugin message]: '.sprintf('API Error on line %s: %s', __LINE__, $ex->getMessage()));

            return $url;
        }

        return $url;
    }

    public function add_folder($name_of_folder_to_create, $target_folder_path = null)
    {
        if ('1' === $this->get_processor()->get_shortcode_option('demo')) {
            // TO DO LOG + FAIL ERROR
            die(-1);
        }

        if (null === $target_folder_path) {
            $target_folder_path = $this->get_processor()->get_requested_complete_path();
        }

        $target_entry = $this->get_entry($target_folder_path);

        // Set new entry path
        $new_folder_path = \TheLion\OutoftheBox\Helpers::clean_folder_path($target_entry->get_path().'/'.$name_of_folder_to_create);

        try {
            $api_entry_new = $this->_client->createFolder($new_folder_path);
            CacheRequest::clear_local_cache_for_shortcode($this->get_processor()->get_listtoken());

            $new_entry = new Entry($api_entry_new);

            do_action('outofthebox_log_event', 'outofthebox_created_entry', $new_entry);

            return $new_entry;
        } catch (\Exception $ex) {
            error_log('[WP Cloud Plugin message]: '.sprintf('API Error on line %s: %s', __LINE__, $ex->getMessage()));

            return new \WP_Error('broke', __('Failed to add folder', 'wpcloudplugins'));
        }

        return false;
    }

    public function rename_entry($new_name, $target_entry_path = null)
    {
        if (null === $target_entry_path) {
            $target_entry_path = $this->get_processor()->get_requested_complete_path();
        }

        $target_entry = $this->get_entry($target_entry_path);

        if (
                $target_entry->is_file() && false === $this->get_processor()->get_user()->can_rename_files()) {
            // TO DO LOG + FAIL ERROR
            die(-1);
        }

        if (
                $target_entry->is_dir() && false === $this->get_processor()->get_user()->can_rename_folders()) {
            // TO DO LOG + FAIL ERROR
            die(-1);
        }

        if ('1' === $this->get_processor()->get_shortcode_option('demo')) {
            // TO DO LOG + FAIL ERROR
            die(-1);
        }

        // Set new entry path
        $new_entry_path = \TheLion\OutoftheBox\Helpers::clean_folder_path($target_entry->get_parent().'/'.$new_name);

        try {
            $api_entry = $this->_client->move($target_entry->get_path(), $new_entry_path);

            $cached_request = new CacheRequest($this->get_processor());
            $cached_request->clear_local_cache_for_shortcode($this->get_processor()->get_listtoken());

            $new_entry = new Entry($api_entry);
            do_action('outofthebox_log_event', 'outofthebox_renamed_entry', $new_entry, ['old_name' => $target_entry->get_name()]);

            return $new_entry;
        } catch (\Exception $ex) {
            error_log('[WP Cloud Plugin message]: '.sprintf('API Error on line %s: %s', __LINE__, $ex->getMessage()));

            return new \WP_Error('broke', __('Failed to rename entry', 'wpcloudplugins'));
        }
    }

    // Copy entry

    public function copy_entry($target_entry = null, $target_parent = null, $new_name = null)
    {
        if (null === $target_entry) {
            $target_entry = $this->get_entry($this->get_processor()->get_requested_complete_path());
        }

        if (false === $target_entry) {
            $message = '[WP Cloud Plugin message]: '.sprintf('Failed to copy the file %s.', $target_entry->get_path());

            error_log($message);

            return new \WP_Error('broke', $message);
        }

        if (($target_entry->is_dir()) && (false === $this->get_processor()->get_user()->can_copy_folders())) {
            $message = '[WP Cloud Plugin message]: '.sprintf('Failed to move %s as user is not allowed to move folders.', $target_entry->get_path());

            error_log($message);

            return new \WP_Error('broke', $message);
        }

        if (($target_entry->is_file()) && (false === $this->get_processor()->get_user()->can_copy_files())) {
            $message = '[WP Cloud Plugin message]: '.sprintf('Failed to copy %s as user is not allowed to copy files.', $target_entry->get_path());

            error_log($message);

            return new \WP_Error('broke', $message);
        }

        if ('1' === $this->get_processor()->get_shortcode_option('demo')) {
            $message = '[WP Cloud Plugin message]: '.sprintf('Failed to copy the file %s.', $target_entry->get_path());

            error_log($message);

            return new \WP_Error('broke', $message);
        }

        $target_path = empty($target_parent) ? $target_entry->get_parent() : $target_parent;
        $new_entry_path = \TheLion\OutoftheBox\Helpers::clean_folder_path($target_path.'/'.$new_name);
        $params = ['autorename' => true];

        try {
            $api_entry = $this->_client->copy($target_entry->get_path(), $new_entry_path, $params);

            $cached_request = new CacheRequest($this->get_processor());
            $cached_request->clear_local_cache_for_shortcode($this->get_processor()->get_listtoken());

            $new_entry = new Entry($api_entry);
            do_action('outofthebox_log_event', 'outofthebox__copied_entry', $new_entry, ['original' => $target_entry->get_name()]);
        } catch (\Exception $ex) {
            error_log('[WP Cloud Plugin message]: '.sprintf('API Error on line %s: %s', __LINE__, $ex->getMessage()));

            if ('1' === $this->get_processor()->get_shortcode_option('debug')) {
                return new \WP_Error('broke', $ex->getMessage());
            }

            return new \WP_Error('broke', __('Failed to copy entry', 'wpcloudplugins'));
        }

        // Clear Cached Requests
        CacheRequest::clear_local_cache_for_shortcode($this->get_processor()->get_listtoken());

        return true;
    }

    public function move_entries($entries = [], $target_entry_path, $copy = false)
    {
        $entries_to_move = [];
        $batch_request = [];

        $target = $this->get_entry($target_entry_path);

        if (false === $target) {
            error_log('[WP Cloud Plugin message]: '.sprintf('Failed to move as target folder %s is not found.', $target_entry_path));

            return $entries_to_move;
        }

        foreach ($entries as $entry_path) {
            $entry = $this->get_entry($entry_path);

            if (false === $entry) {
                continue;
            }

            if (($entry->is_dir()) && (false === $this->get_processor()->get_user()->can_move_folders())) {
                error_log('[WP Cloud Plugin message]: '.sprintf('Failed to move %s as user is not allowed to move folders.', $target->get_path()));
                $entries_to_move[$entry->get_id()] = false;

                continue;
            }

            if (($entry->is_file()) && (false === $this->get_processor()->get_user()->can_move_files())) {
                error_log('[WP Cloud Plugin message]: '.sprintf('Failed to move %s as user is not allowed to remove files.', $target->get_path()));
                $entries_to_move[$entry->get_id()] = false;

                continue;
            }

            if ('1' === $this->get_processor()->get_shortcode_option('demo')) {
                $entries_to_move[$entry->get_id()] = false;

                continue;
            }

            // Check user permission
            if (!$entry->get_permission('canmove')) {
                error_log('[WP Cloud Plugin message]: '.sprintf('Failed to move %s as the sharing permissions on it prevent this.', $target->get_path()));
                $entries_to_move[$entry->get_id()] = false;

                continue;
            }

            $new_entry_path = \TheLion\OutoftheBox\Helpers::clean_folder_path($target->get_path().'/'.$entry->get_name());

            $batch_request[] = [
                'from_path' => $entry->get_path(),
                'to_path' => $new_entry_path,
            ];

            $entries_to_move[$entry->get_id()] = false; // update if batch request was succesfull
        }

        try {
            if ($copy) {
                $request = $this->_client->copyBatch($batch_request);
            } else {
                $request = $this->_client->moveBatch($batch_request);
            }

            $api_entries = $request->getItems()->toArray();

            foreach ($api_entries as $api_entry) {
                $new_entry = new Entry($api_entry);
                do_action('outofthebox_log_event', 'outofthebox_moved_entry', $new_entry);

                $entries_to_move[$entry->get_id()] = $entry;
            }
        } catch (\Exception $ex) {
            error_log('[WP Cloud Plugin message]: '.sprintf('API Error on line %s: %s', __LINE__, $ex->getMessage()));

            return $entries_to_move;
        }

        //CacheRequest::clear_local_cache_for_shortcode($this->get_processor()->get_listtoken());

        return $entries_to_move;
    }

    public function delete_entries($entries_to_delete = [])
    {
        $deleted_entries = [];
        $batch_request = [];

        foreach ($entries_to_delete as $target_entry_id) {
            $target_entry = $this->get_entry($target_entry_id);

            if (false === $target_entry) {
                continue;
            }

            if ($target_entry->is_file() && false === $this->get_processor()->get_user()->can_delete_files()) {
                // TO DO LOG + FAIL ERROR
                continue;
            }

            if ($target_entry->is_dir() && false === $this->get_processor()->get_user()->can_delete_folders()) {
                // TO DO LOG + FAIL ERROR
                continue;
            }

            if ('1' === $this->get_processor()->get_shortcode_option('demo')) {
                continue;
            }

            $deleted_entries[$target_entry->get_id()] = $target_entry;

            $batch_request[] = [
                'path' => $target_entry->get_id(),
            ];
        }

        try {
            $request = $this->_client->deleteBatch($batch_request);

            $api_entries = $request->getItems()->toArray();

            foreach ($api_entries as $api_entry) {
                $deleted_entry = new Entry($api_entry);
                do_action('outofthebox_log_event', 'outofthebox_deleted_entry', $deleted_entry, []);
            }
        } catch (\Exception $ex) {
            error_log('[WP Cloud Plugin message]: '.sprintf('API Error on line %s: %s', __LINE__, $ex->getMessage()));

            return new \WP_Error('broke', __('Failed to delete entry', 'wpcloudplugins'));
        }

        if ('1' === $this->get_processor()->get_shortcode_option('notificationdeletion')) {
            // TO DO NOTIFICATION
            $this->get_processor()->send_notification_email('deletion', $deleted_entries);
        }

        CacheRequest::clear_request_cache();

        return $deleted_entries;
    }

    public function upload_file($temp_file_path, $new_file_path, $params)
    {
        $api_entry = $this->_client->upload($temp_file_path, $new_file_path, $params);

        return new Entry($api_entry);
    }

    /**
     * @return \TheLion\OutoftheBox\Processor
     */
    public function get_processor()
    {
        return $this->_processor;
    }

    /**
     * @return \TheLion\OutoftheBox\Tree
     */
    public function get_cache()
    {
        return $this->get_processor()->get_cache();
    }

    /**
     * @return \Kunnu\Dropbox\Dropbox
     */
    public function get_library()
    {
        return $this->_client;
    }
}
