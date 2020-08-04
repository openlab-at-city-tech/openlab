<?php

namespace TheLion\OutoftheBox;

class Processor
{
    public $options = [];
    public $mobile = false;
    protected $listtoken = '';
    protected $_requestedFile;
    protected $_requestedDir;
    protected $_requestedPath;
    protected $_requestedCompletePath;
    protected $_lastPath = '/';
    protected $_rootFolder = '';
    protected $_loadscripts = ['general' => false, 'files' => false, 'upload' => false, 'mediaplayer' => false, 'qtip' => false];

    /**
     * @var \TheLion\OutoftheBox\Main
     */
    private $_main;

    /**
     * @var \TheLion\OutoftheBox\App
     */
    private $_app;

    /**
     * @var \TheLion\OutoftheBox\Client
     */
    private $_client;

    /**
     * @var \TheLion\OutoftheBox\User
     */
    private $_user;

    /**
     * @var \TheLion\OutoftheBox\UserFolders
     */
    private $_userfolders;

    /**
     * @var \TheLion\OutoftheBox\Cache
     */
    private $_cache;

    /**
     * @var \TheLion\OutoftheBox\Shortcodes
     */
    private $_shortcodes;

    /**
     * @var \TheLion\OutoftheBox\Account
     */
    private $_current_account;

    /**
     * Construct the plugin object.
     */
    public function __construct(Main $_main)
    {
        $this->_main = $_main;
        register_shutdown_function([&$this, 'do_shutdown']);

        $this->settings = get_option('out_of_the_box_settings');
        if ($this->is_network_authorized()) {
            $this->settings = array_merge($this->settings, get_site_option('outofthebox_network_settings', []));
        }

        if (isset($_REQUEST['mobile']) && ('true' === $_REQUEST['mobile'])) {
            $this->mobile = true;
        }

        // If the user wants a hard refresh, set this globally
        if (isset($_REQUEST['hardrefresh']) && 'true' === $_REQUEST['hardrefresh'] && (!defined('FORCE_REFRESH'))) {
            define('FORCE_REFRESH', true);
        }

        add_filter('outofthebox-set-root-namespace-id', [&$this, 'get_root_namespace_id'], 10, 1);
    }

    public function start_process()
    {
        if (!isset($_REQUEST['action'])) {
            error_log('[Out-of-the-Box message]: '." Function start_process() requires an 'action' request");
            die();
        }

        if (isset($_REQUEST['account_id'])) {
            $requested_account = $this->get_accounts()->get_account_by_id($_REQUEST['account_id']);
            if (null !== $requested_account) {
                $this->set_current_account($requested_account);
            } else {
                error_log(sprintf('[Out-of-the-Box message]: '." Function start_process() cannot use the requested account (ID: %s) as it isn't linked with the plugin", $_REQUEST['account_id']));
                die();
            }
        }

        do_action('outofthebox_before_start_process', $_REQUEST['action'], $this);

        $authorized = $this->_is_action_authorized();

        if ((true === $authorized) && ('outofthebox-revoke' === $_REQUEST['action'])) {
            if (Helpers::check_user_role($this->settings['permissions_edit_settings'])) {
                if (null === $this->get_current_account()) {
                    die(-1);
                }

                if ('true' === $_REQUEST['force']) {
                    $this->get_accounts()->remove_account($this->get_current_account()->get_id());
                } else {
                    $this->get_app()->revoke_token($this->get_current_account());
                }
            }

            die(1);
        }

        if ('outofthebox-reset-cache' === $_REQUEST['action']) {
            if (Helpers::check_user_role($this->settings['permissions_edit_settings'])) {
                $this->reset_complete_cache();
            }
            die(1);
        }

        if ('outofthebox-reset-statistics' === $_REQUEST['action']) {
            if (Helpers::check_user_role($this->settings['permissions_edit_settings'])) {
                Events::truncate_database();
            }
            die(1);
        }

        if (is_wp_error($authorized)) {
            error_log('[Out-of-the-Box message]: '." Function start_process() isn't authorized");

            if ('1' === $this->options['debug']) {
                die($authorized->get_error_message());
            }
            die();
        }

        if ((!isset($_REQUEST['listtoken']))) {
            error_log('[Out-of-the-Box message]: '." Function start_process() requires a 'listtoken' request");
            die(1);
        }

        $this->listtoken = $_REQUEST['listtoken'];
        $this->options = $this->get_shortcodes()->get_shortcode_by_id($this->listtoken);

        if (false === $this->options) {
            $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            error_log('[Out-of-the-Box message]: '.' Function start_process('.$_REQUEST['action'].") hasn't received a valid listtoken (".$this->listtoken.") on: {$url} \n");
            die();
        }

        if (null === $this->get_current_account() || false === $this->get_current_account()->get_authorization()->has_access_token()) {
            error_log('[Out-of-the-Box message]: '." Function _is_action_authorized() discovered that the plugin doesn't have an access token");

            return new \WP_Error('broke', '<strong>'.__('Out-of-the-Box needs your help!', 'outofthebox').'</strong> '.__('Authorize the plugin.', 'outofthebox'));
        }

        $this->get_client();

        // Set rootFolder
        if ('manual' === $this->options['user_upload_folders']) {
            $this->_rootFolder = $this->get_user_folders()->get_manually_linked_folder_for_user();
        } elseif (('auto' === $this->options['user_upload_folders']) && !\TheLion\OutoftheBox\Helpers::check_user_role($this->options['view_user_folders_role'])) {
            $this->_rootFolder = $this->get_user_folders()->get_auto_linked_folder_for_user();
        } elseif (('auto' === $this->options['user_upload_folders'])) {
            $this->_rootFolder = str_replace('/%user_folder%', '', $this->options['root']);
        } else {
            $this->_rootFolder = $this->options['root'];
        }

        $this->_rootFolder = html_entity_decode($this->_rootFolder);
        $this->_rootFolder = str_replace('//', '/', $this->_rootFolder);

        if (false === $this->get_user()->can_view()) {
            error_log('[Out-of-the-Box message]: '." Function start_process() discovered that an user didn't have the permission to view the plugin");

            die();
        }

        if (isset($_REQUEST['lastpath'])) {
            $this->_lastPath = stripslashes(rawurldecode($_REQUEST['lastpath']));
        }

        if (isset($_REQUEST['OutoftheBoxpath']) && '' != $_REQUEST['OutoftheBoxpath']) {
            $path = stripslashes(rawurldecode($_REQUEST['OutoftheBoxpath']));
            $this->_set_requested_path($path);
        } else {
            $this->_set_requested_path();
        }

        $this->set_last_path($this->get_requested_path());

        // Check if the request is cached
        if (defined('FORCE_REFRESH')) {
            CacheRequest::clear_local_cache_for_shortcode($this->get_listtoken());
        }

        if (in_array($_REQUEST['action'], ['outofthebox-get-filelist', 'outofthebox-get-gallery', 'outofthebox-get-playlist'])) {
            // And Set GZIP compression if possible
            $this->_set_gzip_compression();

            if (!defined('FORCE_REFRESH')) {
                $cached_request = new CacheRequest($this);
                if ($cached_request->is_cached()) {
                    echo $cached_request->get_cached_response();
                    die();
                }
            }
        }

        do_action('outofthebox_start_process', $_REQUEST['action'], $this);

        switch ($_REQUEST['action']) {
            case 'outofthebox-get-filelist':
                $filebrowser = new Filebrowser($this);

                if (isset($_REQUEST['query']) && !empty($_REQUEST['query']) && '1' === $this->options['search']) { // Search files
                    $filelist = $filebrowser->search_files();
                } else {
                    $filelist = $filebrowser->get_files_list(); // Read folder
                }

                break;
            case 'outofthebox-preview':
                $preview = $this->get_client()->preview_entry();

                break;
            case 'outofthebox-download':
                if (false === $this->get_user()->can_download()) {
                    die();
                }

                $file = $this->get_client()->download_entry();
                die();

                break;
            case 'outofthebox-create-zip':
                if (false === $this->get_user()->can_download()) {
                    die();
                }

                $file = $this->get_client()->download_entries_as_zip();
                die();

                break;
            case 'outofthebox-create-link':
            case 'outofthebox-embedded':
                $link = [];

                if (isset($_REQUEST['entries'])) {
                    $links = ['links'];
                    foreach ($_REQUEST['entries'] as $entry_id) {
                        $entry = $this->get_requested_complete_path().'/'.stripslashes(rawurldecode($entry_id));
                        $link['links'][] = $this->get_client()->get_shared_link_for_output($entry);
                    }
                } else {
                    $link = $this->get_client()->get_shared_link_for_output();
                }
                echo json_encode($link);
                die();

                break;
            case 'outofthebox-get-gallery':
                if (is_wp_error($authorized)) {
                    // No valid token is set
                    echo json_encode(['lastpath' => $this->_lastPath, 'folder' => '', 'html' => '']);
                    die();
                }

                $gallery = new Gallery($this);

                if (isset($_REQUEST['query']) && !empty($_REQUEST['query']) && '1' === $this->options['search']) { // Search files
                    $imagelist = $gallery->search_image_files();
                } else {
                    $imagelist = $gallery->get_images_list(); // Read folder
                }

                die();

                break;
            case 'outofthebox-upload-file':
                $user_can_upload = $this->get_user()->can_upload();

                if (is_wp_error($authorized) || false === $user_can_upload) {
                    die();
                }

                $upload_processor = new Upload($this);
                switch ($_REQUEST['type']) {
                    case 'do-upload':
                        $upload = $upload_processor->do_upload();

                        break;
                    case 'get-status':
                        $status = $upload_processor->get_upload_status();

                        break;
                    case 'get-direct-url':
                        $status = $upload_processor->do_upload_direct();

                        break;
                    case 'upload-convert':
                        $status = $upload_processor->upload_convert();

                        break;
                    case 'upload-postprocess':
                        $status = $upload_processor->upload_post_process();

                        break;
                }

                die();

                break;
            case 'outofthebox-delete-entries':
//Check if user is allowed to delete entry
                $user_can_delete = $this->get_user()->can_delete_files() || $this->get_user()->can_delete_folders();

                if (is_wp_error($authorized) || false === $user_can_delete || !isset($_REQUEST['entries'])) {
                    echo json_encode(['result' => '-1', 'msg' => __('Failed to delete entry', 'outofthebox')]);
                    die();
                }

                $entries_to_delete = [];
                foreach ($_REQUEST['entries'] as $requested_path) {
                    $entry_path = str_replace('//', '/', $this->get_requested_complete_path().'/'.rawurldecode($requested_path));
                    $entries_to_delete[] = $entry_path;
                }

                $entries = $this->get_client()->delete_entries($entries_to_delete);

                foreach ($entries as $entry) {
                    if (false === $entry) {
                        echo json_encode(['result' => '-1', 'msg' => __('Not all entries could be deleted', 'outofthebox')]);
                        die();
                    }
                }
                echo json_encode(['result' => '1', 'msg' => __('Entry was deleted', 'outofthebox')]);
                die();

                break;
            case 'outofthebox-rename-entry':
                // Check if user is allowed to rename entry
                $user_can_rename = $this->get_user()->can_rename_files() || $this->get_user()->can_rename_folders();

                if (is_wp_error($authorized) || false === $user_can_rename) {
                    echo json_encode(['result' => '-1', 'msg' => __('Failed to rename entry', 'outofthebox')]);
                    die();
                }

                // Strip unsafe characters
                $newname = rawurldecode($_REQUEST['newname']);
                $new_filename = Helpers::filter_filename($newname, false);

                $file = $this->get_client()->rename_entry($new_filename);

                if (is_wp_error($file)) {
                    echo json_encode(['result' => '-1', 'msg' => $file->get_error_message()]);
                } else {
                    echo json_encode(['result' => '1', 'msg' => __('Entry was renamed', 'outofthebox')]);
                }

                die();

                break;
            case 'outofthebox-move-entries':

                // Check if user is allowed to move entry
                $user_can_move = $this->get_user()->can_move();

                if (false === $user_can_move) {
                    echo json_encode(['result' => '-1', 'msg' => __('Failed to move', 'outofthebox')]);
                    die();
                }

                $entries_to_move = [];
                foreach ($_REQUEST['entries'] as $requested_path) {
                    $entry_path = str_replace('//', '/', $this->get_requested_complete_path().'/'.rawurldecode($requested_path));
                    $entries_to_move[] = $entry_path;
                }

                $target_path = str_replace('//', '/', $this->_rootFolder.'/'.rawurldecode($_REQUEST['target']));

                $entries = $this->get_client()->move_entries($entries_to_move, $target_path);

                foreach ($entries as $entry) {
                    if (is_wp_error($entry) || empty($entry)) {
                        echo json_encode(['result' => '-1', 'msg' => __('Not all entries could be moved', 'outofthebox')]);
                        die();
                    }
                }
                echo json_encode(['result' => '1', 'msg' => __('Successfully moved to new location', 'outofthebox')]);

                die();

                break;
            case 'outofthebox-add-folder':

//Check if user is allowed to add folder
                $user_can_addfolder = $this->get_user()->can_add_folders();

                if (is_wp_error($authorized) || false === $user_can_addfolder) {
                    echo json_encode(['result' => '-1', 'msg' => __('Failed to add folder', 'outofthebox')]);
                    die();
                }

//Strip unsafe characters
                $newfolder = rawurldecode($_REQUEST['newfolder']);
                $new_foldername = Helpers::filter_filename($newfolder, false);

                $file = $this->get_client()->add_folder($new_foldername);

                $this->set_last_path($this->_requestedPath.'/'.$file->get_name());

                if (is_wp_error($file)) {
                    echo json_encode(['result' => '-1', 'msg' => $file->get_error_message()]);
                } else {
                    echo json_encode(['result' => '1', 'msg' => __('Folder', 'outofthebox').' '.$newfolder.' '.__('was added', 'outofthebox'), 'lastpath' => rawurlencode($this->_lastPath)]);
                }
                die();

                break;
            case 'outofthebox-get-playlist':
                if (is_wp_error($authorized)) {
                    die();
                }

                $mediaplayer = new Mediaplayer($this);
                $playlist = $mediaplayer->get_media_list();

                break;
            case 'outofthebox-stream':
                $file = $this->get_client()->stream_entry();

                break;
            default:
                error_log('[Out-of-the-Box message]: '.sprintf('No valid AJAX call: %s', $_REQUEST['action']));
                die();
        }
    }

    public function create_from_shortcode($atts)
    {
        $atts = (is_string($atts)) ? [] : $atts;
        $atts = $this->remove_deprecated_options($atts);

        $defaults = [
            'singleaccount' => '1',
            'account' => false,
            'startaccount' => false,
            'dir' => '/',
            'class' => '',
            'startpath' => false,
            'mode' => 'files',
            'userfolders' => '0',
            'usertemplatedir' => '',
            'viewuserfoldersrole' => 'administrator',
            'userfoldernametemplate' => '',
            'maxuserfoldersize' => '-1',
            'ext' => '*',
            'showfiles' => '1',
            'showfolders' => '1',
            'maxfiles' => '-1',
            'filesize' => '1',
            'filedate' => '1',
            'filelayout' => 'list',
            'showcolumnnames' => '1',
            'showext' => '1',
            'showroot' => '0',
            'sortfield' => 'name',
            'sortorder' => 'asc',
            'showbreadcrumb' => '1',
            'candownloadzip' => '0',
            'canpopout' => '0',
            'lightboxnavigation' => '1',
            'showsharelink' => '0',
            'showrefreshbutton' => '1',
            'roottext' => __('Start', 'outofthebox'),
            'search' => '1',
            'searchfrom' => 'parent',
            'searchcontents' => '0',
            'include' => '*',
            'exclude' => '*',
            'showsystemfiles' => '0',
            'maxwidth' => '100%',
            'maxheight' => '',
            'viewrole' => 'administrator|editor|author|contributor|subscriber|guest',
            'previewrole' => 'all',
            'downloadrole' => 'administrator|editor|author|contributor|subscriber|guest',
            'sharerole' => 'all',
            'previewinline' => '1',
            'forcedownload' => '0',
            'maximages' => '25',
            'crop' => '0',
            'quality' => '90',
            'slideshow' => '0',
            'pausetime' => '5000',
            'showfilenames' => '0',
            'targetheight' => '250',
            'folderthumbs' => '1',
            'mediaskin' => '',
            'mediabuttons' => 'prevtrack|playpause|nexttrack|volume|current|duration|fullscreen',
            'autoplay' => '0',
            'hideplaylist' => '0',
            'showplaylistonstart' => '1',
            'playlistinline' => '0',
            'playlistthumbnails' => '1',
            'linktomedia' => '0',
            'linktoshop' => '',
            'ads' => '0',
            'ads_tag_url' => '',
            'ads_skipable' => '1',
            'ads_skipable_after' => '',
            'notificationupload' => '0',
            'notificationdownload' => '0',
            'notificationdeletion' => '0',
            'notificationemail' => '%admin_email%',
            'notification_skipemailcurrentuser' => '0',
            'upload' => '0',
            'upload_folder' => '1',
            'overwrite' => '0',
            'uploadext' => '.',
            'uploadrole' => 'administrator|editor|author|contributor|subscriber',
            'minfilesize' => '0',
            'maxfilesize' => '0',
            'maxnumberofuploads' => '-1',
            'delete' => '0',
            'deletefilesrole' => 'administrator|editor',
            'deletefoldersrole' => 'administrator|editor',
            'rename' => '0',
            'renamefilesrole' => 'administrator|editor',
            'renamefoldersrole' => 'administrator|editor',
            'move' => '0',
            'moverole' => 'administrator|editor',
            'addfolder' => '0',
            'addfolderrole' => 'administrator|editor',
            'deeplink' => '0',
            'deeplinkrole' => 'all',
            'mcepopup' => '0',
            'debug' => '0',
            'demo' => '0',
        ];

        //Create a unique identifier
        $this->listtoken = md5(serialize($defaults).serialize($atts));

        //Read shortcode
        extract(shortcode_atts($defaults, $atts));

        $cached_shortcode = $this->get_shortcodes()->get_shortcode_by_id($this->listtoken);

        if (false === $cached_shortcode) {
            switch ($mode) {
                case 'gallery':
                    $ext = ('*' == $ext) ? 'gif|jpg|jpeg|png|bmp' : $ext;
                    $uploadext = ('.' == $uploadext) ? 'gif|jpg|jpeg|png|bmp' : $uploadext;
                    // no break
                case 'search':
                    $searchfrom = 'root';
                    // no break
                default:
                    break;
            }

            if (!empty($account)) {
                $singleaccount = '1';
            }

            if ('0' === $singleaccount) {
                $dir = '/';
                $account = false;
            }

            if (empty($account)) {
                $primary_account = $this->get_accounts()->get_primary_account();
                if (null !== $primary_account) {
                    $account = $primary_account->get_id();
                }
            }

            $account_class = $this->get_accounts()->get_account_by_id($account);
            if (null === $account_class) {
                error_log('[Out-of-the-Box message]: shortcode cannot be rendered as the requested account is not linked with the plugin');

                return '<i>>>> '.__('ERROR: Contact the Administrator to see this content', 'outofthebox').' <<<</i>';
            }

            $this->set_current_account($account_class);

            //Force $candownloadzip = 0 if we can't use ZipArchive
            if (!class_exists('ZipArchive')) {
                $candownloadzip = '0';
            }

            $dir = rtrim($dir, '/');
            $dir = ('' == $dir) ? '/' : $dir;
            if ('/' !== substr($dir, 0, 1)) {
                $dir = '/'.$dir;
            }

            // Explode roles
            $viewrole = explode('|', $viewrole);
            $previewrole = explode('|', $previewrole);
            $downloadrole = explode('|', $downloadrole);
            $sharerole = explode('|', $sharerole);
            $uploadrole = explode('|', $uploadrole);
            $deletefilesrole = explode('|', $deletefilesrole);
            $deletefoldersrole = explode('|', $deletefoldersrole);
            $renamefilesrole = explode('|', $renamefilesrole);
            $renamefoldersrole = explode('|', $renamefoldersrole);
            $moverole = explode('|', $moverole);
            $addfolderrole = explode('|', $addfolderrole);
            $viewuserfoldersrole = explode('|', $viewuserfoldersrole);
            $deeplinkrole = explode('|', $deeplinkrole);
            $mediabuttons = explode('|', $mediabuttons);

            $this->options = [
                'single_account' => $singleaccount,
                'account' => $account,
                'startaccount' => $startaccount,
                'class' => $class,
                'root' => htmlspecialchars_decode($dir),
                'startpath' => $startpath,
                'mode' => $mode,
                'user_upload_folders' => $userfolders,
                'user_template_dir' => htmlspecialchars_decode($usertemplatedir),
                'view_user_folders_role' => $viewuserfoldersrole,
                'user_folder_name_template' => $userfoldernametemplate,
                'max_user_folder_size' => $maxuserfoldersize,
                'mediaskin' => $mediaskin,
                'mediabuttons' => $mediabuttons,
                'autoplay' => $autoplay,
                'hideplaylist' => $hideplaylist,
                'showplaylistonstart' => $showplaylistonstart,
                'playlistinline' => $playlistinline,
                'playlistthumbnails' => $playlistthumbnails,
                'linktomedia' => $linktomedia,
                'linktoshop' => $linktoshop,
                'ads' => $ads,
                'ads_tag_url' => $ads_tag_url,
                'ads_skipable' => $ads_skipable,
                'ads_skipable_after' => $ads_skipable_after,
                'ext' => explode('|', strtolower($ext)),
                'show_files' => $showfiles,
                'show_folders' => $showfolders,
                'max_files' => $maxfiles,
                'filelayout' => $filelayout,
                'show_filesize' => $filesize,
                'show_filedate' => $filedate,
                'show_columnnames' => $showcolumnnames,
                'show_ext' => $showext,
                'show_root' => $showroot,
                'sort_field' => $sortfield,
                'sort_order' => $sortorder,
                'show_breadcrumb' => $showbreadcrumb,
                'can_download_zip' => $candownloadzip,
                'canpopout' => $canpopout,
                'lightbox_navigation' => $lightboxnavigation,
                'show_sharelink' => $showsharelink,
                'show_refreshbutton' => $showrefreshbutton,
                'root_text' => $roottext,
                'search' => $search,
                'searchfrom' => $searchfrom,
                'search_contents' => $searchcontents,
                'include' => explode('|', strtolower(htmlspecialchars_decode($include))),
                'exclude' => explode('|', strtolower(htmlspecialchars_decode($exclude))),
                'show_system_files' => $showsystemfiles,
                'maxwidth' => $maxwidth,
                'maxheight' => $maxheight,
                'view_role' => $viewrole,
                'preview_role' => $previewrole,
                'download_role' => $downloadrole,
                'share_role' => $sharerole,
                'previewinline' => $previewinline,
                'forcedownload' => $forcedownload,
                'maximages' => $maximages,
                'notificationupload' => $notificationupload,
                'notificationdownload' => $notificationdownload,
                'notificationdeletion' => $notificationdeletion,
                'notificationemail' => $notificationemail,
                'notification_skip_email_currentuser' => $notification_skipemailcurrentuser,
                'upload' => $upload,
                'upload_folder' => $upload_folder,
                'overwrite' => $overwrite,
                'upload_ext' => strtolower($uploadext),
                'upload_role' => $uploadrole,
                'minfilesize' => $minfilesize,
                'maxfilesize' => $maxfilesize,
                'maxnumberofuploads' => $maxnumberofuploads,
                'delete' => $delete,
                'deletefiles_role' => $deletefilesrole,
                'deletefolders_role' => $deletefoldersrole,
                'rename' => $rename,
                'renamefiles_role' => $renamefilesrole,
                'renamefolders_role' => $renamefoldersrole,
                'move' => $move,
                'move_role' => $moverole,
                'addfolder' => $addfolder,
                'addfolder_role' => $addfolderrole,
                'deeplink' => $deeplink,
                'deeplink_role' => $deeplinkrole,
                'crop' => $crop,
                'quality' => $quality,
                'show_filenames' => $showfilenames,
                'targetheight' => $targetheight,
                'folderthumbs' => $folderthumbs,
                'slideshow' => $slideshow,
                'pausetime' => $pausetime,
                'mcepopup' => $mcepopup,
                'debug' => $debug,
                'demo' => $demo,
                'expire' => strtotime('+1 weeks'),
                'listtoken' => $this->listtoken, ];

            $this->options = apply_filters('outofthebox_shortcode_add_options', $this->options, $this, $atts);

            $this->save_shortcodes();

            $this->options = apply_filters('outofthebox_shortcode_set_options', $this->options, $this, $atts);

            //Create userfolders if needed

            if (('auto' === $this->options['user_upload_folders'])) {
                if ('Yes' === $this->settings['userfolder_onfirstvisit']) {
                    $allusers = [];
                    $roles = array_diff($this->options['view_role'], $this->options['view_user_folders_role']);

                    foreach ($roles as $role) {
                        $users_query = new \WP_User_Query([
                            'fields' => 'all_with_meta',
                            'role' => $role,
                            'orderby' => 'display_name',
                        ]);
                        $results = $users_query->get_results();
                        if ($results) {
                            $allusers = array_merge($allusers, $results);
                        }
                    }
                    $userfolder = $this->get_user_folders()->create_user_folders($allusers);
                }
            }
        } else {
            $this->options = apply_filters('outofthebox_shortcode_set_options', $cached_shortcode, $this, $atts);
        }

        if (null === $this->get_current_account() || false === $this->get_current_account()->get_authorization()->has_access_token()) {
            return '<i>>>> '.__('ERROR: Contact the Administrator to see this content', 'outofthebox').' <<<</i>';
        }

        ob_start();
        $this->render_template();

        return ob_get_clean();
    }

    public function render_template()
    {
        // Reload User Object for this new shortcode
        $user = $this->get_user('reload');

        if (false === $this->get_user()->can_view()) {
            do_action('outofthebox_shortcode_no_view_permission', $this);

            return;
        }

        // Render the  template
        $rootfolder = ''; //(($this->options['user_upload_folders'] !== '0') && !\TheLion\OutoftheBox\Helpers::check_user_role($this->options['view_user_folders_role'])) ? '' : $this->options['root'];

        $colors = $this->get_setting('colors');

        if ('manual' === $this->options['user_upload_folders']) {
            $userfolder = get_user_option('out_of_the_box_linkedto');
            if (is_array($userfolder) && isset($userfolder['foldertext'])) {
                $rootfolder = $userfolder['folderid'];
            } else {
                $defaultuserfolder = get_site_option('out_of_the_box_guestlinkedto');
                if (is_array($defaultuserfolder) && isset($defaultuserfolder['folderid'])) {
                    $rootfolder = $defaultuserfolder['folderid'];
                } else {
                    echo "<div id='OutoftheBox' class='{$colors['style']}'>";
                    $this->load_scripts('general');
                    include sprintf('%s/templates/noaccess.php', OUTOFTHEBOX_ROOTDIR);
                    echo '</div>';

                    return;
                }
            }
        }

        $rootfolder = (false !== $this->options['startpath']) ? $this->options['startpath'] : $rootfolder;
        $dataaccountid = (false !== $this->options['startaccount']) ? $this->options['startaccount'] : $this->options['account'];

        $shortcode_class = ('shortcode' === $this->options['mcepopup']) ? 'initiate' : '';

        do_action('outofthebox_before_shortcode', $this);

        echo "<div id='OutoftheBox' class='{$colors['style']} {$this->options['class']} {$shortcode_class} {$this->options['mode']}' style='display:none'>";
        echo "<noscript><div class='OutoftheBox-nojsmessage'>".__('To view the Dropbox folders, you need to have JavaScript enabled in your browser', 'outofthebox').'.<br/>';
        echo "<a href='http://www.enable-javascript.com/' target='_blank'>".__('To do so, please follow these instructions', 'outofthebox').'</a>.</div></noscript>';

        switch ($this->options['mode']) {
            case 'files':

                $this->load_scripts('files');

                echo "<div id='OutoftheBox-{$this->listtoken}' class='OutoftheBox files oftb-".$this->options['filelayout']." jsdisabled' data-list='files' data-token='{$this->listtoken}'  data-account-id='{$dataaccountid}' data-path='".rawurlencode($rootfolder)."' data-org-path='".rawurlencode($this->_lastPath)."' data-sort='".$this->options['sort_field'].':'.$this->options['sort_order']."' data-deeplink='".((!empty($_REQUEST['file'])) ? $_REQUEST['file'] : '')."' data-layout='".$this->options['filelayout']."' data-popout='".$this->options['canpopout']."' data-lightboxnav='".$this->options['lightbox_navigation']."'>";

                if ('linkto' === $this->get_shortcode_option('mcepopup') || 'linktobackendglobal' === $this->get_shortcode_option('mcepopup')) {
                    $button_text = __('Use the Root Folder of your Account', 'outofthebox');
                    echo '<div data-url="'.urlencode('/').'" data-name="/">';
                    echo '<div class="entry_linkto entry_linkto_root">';
                    echo '<span><input class="button-secondary" type="submit" title="'.$button_text.'" value="'.$button_text.'"></span>';
                    echo '</div>';
                    echo '</div>';
                }

                if ('shortcode' === $this->options['mcepopup']) {
                    echo "<div class='selected-folder'><strong>".__('Selected folder', 'outofthebox').": </strong><span class='current-folder-raw'></span></div>";
                }

                include sprintf('%s/templates/frontend.php', OUTOFTHEBOX_ROOTDIR);
                $this->render_uploadform();

                echo '</div>';

                break;
            case 'upload':

                echo "<div id='OutoftheBox-{$this->listtoken}' class='OutoftheBox upload jsdisabled'  data-token='{$this->listtoken}' data-account-id='{$dataaccountid}' data-path='".rawurlencode($rootfolder)."' data-org-path='".rawurlencode($this->_lastPath)."'>";
                $this->render_uploadform();
                echo '</div>';

                break;
            case 'gallery':

                $this->load_scripts('files');

                $nextimages = '';
                if (('0' !== $this->options['maximages'])) {
                    $nextimages = "data-loadimages='".$this->options['maximages']."'";
                }

                echo "<div id='OutoftheBox-{$this->listtoken}' class='OutoftheBox gridgallery jsdisabled' data-list='gallery' data-token='{$this->listtoken}' data-account-id='{$dataaccountid}' data-path='".rawurlencode($rootfolder)."' data-org-path='".rawurlencode($this->_lastPath)."' data-sort='".$this->options['sort_field'].':'.$this->options['sort_order']."'  data-targetheight='".$this->options['targetheight']."' data-deeplink='".((!empty($_REQUEST['image'])) ? $_REQUEST['image'] : '')."' data-slideshow='".$this->options['slideshow']."' data-pausetime='".$this->options['pausetime']."' {$nextimages} data-lightboxnav='".$this->options['lightbox_navigation']."'>";
                include sprintf('%s/templates/gallery.php', OUTOFTHEBOX_ROOTDIR);
                $this->render_uploadform();
                echo '</div>';

                break;
            case 'search':
                echo "<div id='OutoftheBox-{$this->listtoken}' class='OutoftheBox files oftb-".$this->options['filelayout']." searchlist jsdisabled' data-list='search' data-token='{$this->listtoken}' data-account-id='{$dataaccountid}' data-path='".rawurlencode($rootfolder)."' data-org-path='".rawurlencode($this->_lastPath)."' data-sort='".$this->options['sort_field'].':'.$this->options['sort_order']."' data-deeplink='".((!empty($_REQUEST['file'])) ? $_REQUEST['file'] : '')."' data-layout='".$this->options['filelayout']."' data-lightboxnav='".$this->options['lightbox_navigation']."'>";
                $this->load_scripts('files');
                include sprintf('%s/templates/search.php', OUTOFTHEBOX_ROOTDIR);
                echo '</div>';

                break;
            case 'video':
            case 'audio':
                $mediaplayer = $this->load_mediaplayer($this->options['mediaskin']);

                echo "<div id='OutoftheBox-{$this->listtoken}' class='OutoftheBox media ".$this->options['mode']." jsdisabled' data-list='media' data-token='{$this->listtoken}' data-account-id='{$dataaccountid}' data-path='{$this->_lastPath}' data-sort='".$this->options['sort_field'].':'.$this->options['sort_order']."'>";
                $mediaplayer->load_player();
                echo '</div>';
                $this->load_scripts('mediaplayer');

                break;
        }
        echo "<script type='text/javascript'>if (typeof(jQuery) !== 'undefined' && typeof(jQuery.cp) !== 'undefined' && typeof(jQuery.cp.OutoftheBox) === 'function') { jQuery('#OutoftheBox-{$this->listtoken}').OutoftheBox(OutoftheBox_vars); };</script>";
        echo '</div>';

        do_action('outofthebox_after_shortcode', $this);

        $this->load_scripts('general');
    }

    public function render_uploadform()
    {
        $user_can_upload = $this->get_user()->can_upload();

        if (false === $user_can_upload) {
            return;
        }

        $own_limit = ('0' !== $this->options['maxfilesize']);
        $post_max_size_bytes = min(Helpers::return_bytes(ini_get('post_max_size')), Helpers::return_bytes(ini_get('upload_max_filesize')));
        $_max_file_size = ('0' !== $this->options['maxfilesize']) ? Helpers::return_bytes($this->options['maxfilesize']) : ($post_max_size_bytes);
        $min_file_size = (!empty($this->options['minfilesize'])) ? Helpers::return_bytes($this->options['minfilesize']) : '0';

        // Files larger than 300MB cannot be uploaded directly to Dropbox :(
        $max_file_size = max($_max_file_size, 314572800);
        if ($own_limit) {
            if ($_max_file_size < $max_file_size) {
                $max_file_size = $_max_file_size;
            }
        }

        $post_max_size_str = Helpers::bytes_to_size_1024($max_file_size);
        $min_file_size_str = Helpers::bytes_to_size_1024($min_file_size);

        $acceptfiletypes = '.('.$this->options['upload_ext'].')$';
        $max_number_of_uploads = $this->options['maxnumberofuploads'];

        $this->load_scripts('upload');
        include sprintf('%s/templates/uploadform.php', OUTOFTHEBOX_ROOTDIR);
    }

    public function create_thumbnail()
    {
        $this->get_client()->build_thumbnail();
        die();
    }

    public function get_last_path()
    {
        return $this->_lastPath;
    }

    public function set_last_path($last_path)
    {
        $this->_lastPath = $last_path;
        if ('' === $this->_lastPath) {
            $this->_lastPath = '/';
        }
        $this->_set_requested_path();

        return $this->_lastPath;
    }

    public function get_requested_path()
    {
        return $this->_requestedPath;
    }

    public function get_requested_complete_path()
    {
        return $this->_requestedCompletePath;
    }

    public function get_root_folder()
    {
        return $this->_rootFolder;
    }

    public function get_relative_path($full_path, $from_path = null)
    {
        if (empty($from_path)) {
            if ('' === $this->get_root_folder() || '/' === $this->get_root_folder()) {
                return $full_path;
            }

            $from_path = $this->get_root_folder();
        }

        $from_path_arr = explode('/', $from_path);
        $full_path_arr = explode('/', $full_path);
        $difference = (count($full_path_arr) - count($from_path_arr));

        if ($difference < 1) {
            return '/';
        }

        if (1 === $difference) {
            return '/'.end($full_path_arr);
        }

        return '/'.implode('/', array_slice($full_path_arr, -($difference)));
    }

    public function get_listtoken()
    {
        return $this->listtoken;
    }

    public function load_mediaplayer($mediaplayer)
    {
        if (empty($mediaplayer)) {
            $mediaplayer = $this->get_setting('mediaplayer_skin');
        }

        if (file_exists(OUTOFTHEBOX_ROOTDIR.'/skins/'.$mediaplayer.'/Player.php')) {
            require_once OUTOFTHEBOX_ROOTDIR.'/skins/'.$mediaplayer.'/Player.php';
        } else {
            error_log('[Out-of-the-Box message]: '.sprintf('Media Player Skin %s is missing', $mediaplayer));

            return $this->load_mediaplayer(null);
        }

        try {
            $class = '\TheLion\OutoftheBox\MediaPlayers\\'.$mediaplayer;

            return new $class($this);
        } catch (\Exception $ex) {
            error_log('[Out-of-the-Box message]: '.sprintf('Media Player Skin %s is invalid', $mediaplayer));

            return false;
        }
    }

    public function sort_filelist($foldercontents)
    {
        $sort_field = 'name';
        $sort_order = SORT_ASC;

        if (count($foldercontents) > 0) {
            // Sort Filelist, folders first
            $sort = [];

            if (isset($_REQUEST['sort'])) {
                $sort_options = explode(':', $_REQUEST['sort']);

                if ('shuffle' === $sort_options[0]) {
                    $keys = array_keys($foldercontents);
                    shuffle($keys);
                    $random = [];
                    foreach ($keys as $key) {
                        $random[$key] = $foldercontents[$key];
                    }

                    return $random;
                }

                if (2 === count($sort_options)) {
                    $sort_field = $sort_options[0];

                    switch ($sort_options[0]) {
                        case 'name':
                            $sort_field = 'path_display';

                            break;
                        case 'size':
                            $sort_field = 'size';

                            break;
                        case 'modified':
                            $sort_field = 'last_edited';

                            break;
                    }

                    switch ($sort_options[1]) {
                        case 'asc':
                            $sort_order = SORT_ASC;

                            break;
                        case 'desc':
                            $sort_order = SORT_DESC;

                            break;
                    }
                }
            }

            $multisort_flag = SORT_REGULAR;
            if ('name' === $sort_field) {
                //$multisort_flag = SORT_NATURAL;
            }

            list($sort_field, $sort_order) = apply_filters('outofthebox_sort_filelist_settings', [$sort_field, $sort_order], $foldercontents, $this);

            foreach ($foldercontents as $k => $v) {
                if ($v instanceof EntryAbstract) {
                    $sort['is_dir'][$k] = $v->is_dir();
                    $sort['sort'][$k] = strtolower($v->{'get_'.$sort_field}());
                } else {
                    $sort['is_dir'][$k] = $v['is_dir'];
                    $sort['sort'][$k] = $v[$sort_field];
                }
            }

            // Sort by dir desc and then by name asc
            if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
                array_multisort($sort['is_dir'], SORT_DESC, SORT_REGULAR, $sort['sort'], $sort_order, $multisort_flag, $foldercontents, SORT_ASC, $multisort_flag);
            } else {
                array_multisort($sort['is_dir'], SORT_DESC, $sort['sort'], $sort_order, $foldercontents);
            }
        }

        $foldercontents = apply_filters('outofthebox_sort_filelist', $foldercontents, $sort_field, $sort_order, $this);

        return $foldercontents;
    }

    public function send_notification_email($notification_type, $entries)
    {
        $notification = new Notification($this, $notification_type, $entries);
        $notification->send_notification();
    }

    // Check if $entry is allowed

    public function _is_entry_authorized(Entry $entry)
    {
        // Return in case a direct call is being made, and no shortcode is involved
        if (empty($this->options)) {
            return true;
        }

        // Action for custom filters
        $is_authorized_hook = apply_filters('outofthebox_is_entry_authorized', true, $entry, $this);
        if (false === $is_authorized_hook) {
            return false;
        }

        if (strtolower($entry->get_path()) === strtolower($this->_rootFolder)) {
            return true;
        }

        // skip entry if its a file, and we dont want to show files
        if (($entry->is_file()) && ('0' === $this->get_shortcode_option('show_files'))) {
            return false;
        }

        // Skip entry if its a folder, and we dont want to show folders
        if (($entry->is_dir()) && ('0' === $this->get_shortcode_option('show_folders'))) {
            return false;
        }

        // Only keep files with the right extension
        if (true === $entry->is_file() && (!in_array($entry->get_extension(), $this->get_shortcode_option('ext'))) && '*' != $this->options['ext'][0]) {
            return false;
        }

        $_path = str_ireplace($this->_rootFolder.'/', '', $entry->get_path());
        $_path = strtolower($_path);
        $subs = array_filter(explode('/', $_path));

        if ('*' != $this->options['exclude'][0]) {
            if (1 === count($subs)) {
                $found = false;

                foreach ($subs as $sub) {
                    if (in_array($sub, $this->options['exclude'])) {
                        $found = true;

                        continue;
                    }
                }
                if ($found) {
                    return false;
                }
            } elseif (count($subs) > 1) {
                $found = false;

                foreach ($subs as $sub) {
                    if (in_array($sub, $this->options['exclude'])) {
                        $found = true;

                        continue;
                    }
                }
                if ($found) {
                    return false;
                }
            }
        }

        // only allow included folders and files

        if ('*' != $this->options['include'][0]) {
            if (1 === count($subs)) {
                $found = false;

                foreach ($subs as $sub) {
                    if (in_array($sub, $this->options['include'])) {
                        $found = true;

                        continue;
                    }
                }
                if (!$found) {
                    return false;
                }
            } elseif (count($subs) > 1) {
                $found = false;

                foreach ($subs as $sub) {
                    if (in_array($sub, $this->options['include'])) {
                        $found = true;

                        continue;
                    }
                }
                if (!$found) {
                    return false;
                }
            }
        }
        //if ($this->options['include'][0] != '*') {
        //  foreach ($this->options['include'] as $includedentry) {
        //    if (stripos($entry, '/' . $includedentry) === false) {
        //      return false;
        //    }
        //  }
        //}

        // Check if file is hidden system file
        if ($entry->is_file() && '0' === $this->options['show_system_files']) {
            $regex = '/^\.(.*)/i';
            if (1 === preg_match($regex, $entry->get_name())) {
                return false;
            }
        }

        return true;
    }

    // Check if $extensions array has $entry

    public function _is_extension_authorized($entry, $extensions, $prefix = '.')
    {
        if ('*' != $extensions[0]) {
            $pathinfo = \TheLion\OutoftheBox\Helpers::get_pathinfo($entry);
            if (!isset($pathinfo['extension'])) {
                return true;
            }

            foreach ($extensions as $allowedextensions) {
                if (false !== stripos($entry, $prefix.$allowedextensions)) {
                    return true;
                }
            }
        } else {
            return true;
        }

        return false;
    }

    public function is_mobile()
    {
        return $this->mobile;
    }

    public function get_setting($key, $default = null)
    {
        if (!isset($this->settings[$key])) {
            return $default;
        }

        return $this->settings[$key];
    }

    public function set_setting($key, $value)
    {
        $this->settings[$key] = $value;
        $success = update_option('out_of_the_box_settings', $this->settings);
        $this->settings = get_option('out_of_the_box_settings');

        return $success;
    }

    public function get_network_setting($key, $default = null)
    {
        $network_settings = get_site_option('outofthebox_network_settings', []);

        if (!isset($network_settings[$key])) {
            return $default;
        }

        return $network_settings[$key];
    }

    public function set_network_setting($key, $value)
    {
        $network_settings = get_site_option('outofthebox_network_settings', []);
        $network_settings[$key] = $value;

        return update_site_option('outofthebox_network_settings', $network_settings);
    }

    public function get_shortcode()
    {
        return $this->options;
    }

    public function get_shortcode_option($key)
    {
        if (!isset($this->options[$key])) {
            return null;
        }

        return $this->options[$key];
    }

    public function set_shortcode($listtoken)
    {
        $cached_shortcode = $this->get_shortcodes()->get_shortcode_by_id($listtoken);

        if ($cached_shortcode) {
            $this->options = $cached_shortcode;
            $this->listtoken = $listtoken;
        }

        return $this->options;
    }

    /**
     * Function that enables gzip compression when is needed and when is possible.
     */
    public function _set_gzip_compression()
    {
        // Compress file list if possible
        if ('Yes' === $this->get_setting('gzipcompression')) {
            $zlib = ('' == ini_get('zlib.output_compression') || !ini_get('zlib.output_compression')) && ('ob_gzhandler' != ini_get('output_handler'));
            if (true === $zlib) {
                if (extension_loaded('zlib')) {
                    if (!in_array('ob_gzhandler', ob_list_handlers())) {
                        ob_start('ob_gzhandler');
                    }
                }
            }
        }
    }

    public function is_network_authorized()
    {
        if (!function_exists('is_plugin_active_for_network')) {
            require_once ABSPATH.'/wp-admin/includes/plugin.php';
        }

        $network_settings = get_site_option('outofthebox_network_settings', []);

        return isset($network_settings['network_wide']) && is_plugin_active_for_network(OUTOFTHEBOX_SLUG) && ('Yes' === $network_settings['network_wide']);
    }

    /**
     * @return \TheLion\OutoftheBox\Main
     */
    public function get_main()
    {
        return $this->_main;
    }

    /**
     * @return \TheLion\OutoftheBox\App
     */
    public function get_app()
    {
        if (empty($this->_app)) {
            $this->_app = new \TheLion\OutoftheBox\App($this);
            $this->_app->start_client($this->get_current_account());
        } elseif (null !== $this->get_current_account()) {
            $this->_app->get_client()->setAccessToken($this->get_current_account()->get_authorization()->get_access_token());
        }

        return $this->_app;
    }

    public function get_root_namespace_id($id = '')
    {
        $use_team_folders = $this->get_setting('use_team_folders', 'No');

        if ('No' === $use_team_folders) {
            return $id;
        }

        $current_account = $this->get_current_account();

        if (empty($current_account)) {
            return $id;
        }

        return $current_account->get_root_namespace_id();
    }

    /**
     * @return \TheLion\OutoftheBox\Accounts
     */
    public function get_accounts()
    {
        return $this->get_main()->get_accounts();
    }

    /**
     * @return \TheLion\OutoftheBox\Account
     */
    public function get_current_account()
    {
        if (empty($this->_current_account)) {
            if (null !== $this->get_shortcode('account')) {
                $this->_current_account = $this->get_accounts()->get_account_by_id($this->get_shortcode_option('account'));
            }
        }

        return $this->_current_account;
    }

    /**
     * @return \TheLion\OutoftheBox\Account
     */
    public function set_current_account(Account $account)
    {
        $this->_current_account = $account;
    }

    public function clear_current_account()
    {
        $this->_current_account = null;
    }

    /**
     * @return \TheLion\OutoftheBox\Client
     */
    public function get_client()
    {
        if (empty($this->_client)) {
            $this->_client = new \TheLion\OutoftheBox\Client($this->get_app(), $this);
        } elseif (null !== $this->get_current_account()) {
            $this->_app->get_client()->setAccessToken($this->get_current_account()->get_authorization()->get_access_token());
        }

        return $this->_client;
    }

    /**
     * @return \TheLion\OutoftheBox\Cache
     */
    public function get_cache()
    {
        if (empty($this->_cache)) {
            $this->_cache = new \TheLion\OutoftheBox\Cache($this);
        }

        return $this->_cache;
    }

    /**
     * @return \TheLion\OutoftheBox\Shortcodes
     */
    public function get_shortcodes()
    {
        if (empty($this->_shortcodes)) {
            $this->_shortcodes = new \TheLion\OutoftheBox\Shortcodes($this);
        }

        return $this->_shortcodes;
    }

    /**
     * @param mixed $force_reload
     *
     * @return \TheLion\OutoftheBox\User
     */
    public function get_user($force_reload = false)
    {
        if (empty($this->_user) || $force_reload) {
            $this->_user = new \TheLion\OutoftheBox\User($this);
        }

        return $this->_user;
    }

    /**
     * @return \TheLion\OutoftheBox\UserFolders
     */
    public function get_user_folders()
    {
        if (empty($this->_userfolders)) {
            $this->_userfolders = new \TheLion\OutoftheBox\UserFolders($this);
        }

        return $this->_userfolders;
    }

    public function reset_complete_cache()
    {
        if (!file_exists(OUTOFTHEBOX_CACHEDIR)) {
            return false;
        }

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(OUTOFTHEBOX_CACHEDIR, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path) {
            if ($path->isDir()) {
                continue;
            }
            if ('.htaccess' === $path->getFilename()) {
                continue;
            }

            if ('access_token' === $path->getExtension()) {
                continue;
            }

            if (false !== strpos($path->getPathname(), 'thumbnails')) {
                continue;
            }

            try {
                @unlink($path->getPathname());
            } catch (\Exception $ex) {
                continue;
            }
        }

        return true;
    }

    public function do_shutdown()
    {
        $error = error_get_last();

        if (null === $error) {
            return;
        }

        if (E_ERROR !== $error['type']) {
            return;
        }

        if (isset($error['file']) && false !== strpos($error['file'], OUTOFTHEBOX_ROOTDIR)) {
            error_log('[Out-of-the-Box message]: Script terminated. Reason: '.var_export($error, true));
        }
    }

    protected function load_scripts($template)
    {
        if (true === $this->_loadscripts[$template]) {
            return false;
        }

        switch ($template) {
            case 'general':
                if (false === defined('WPCP_DISABLE_FONTAWESOME')) {
                    wp_enqueue_style('Awesome-Font-5-css');
                    if ('Yes' === $this->get_setting('fontawesomev4_shim')) {
                        wp_enqueue_style('Awesome-Font-4-shim-css');
                    }
                }

                wp_enqueue_style('OutoftheBox');
                wp_enqueue_script('OutoftheBox');

                add_action('wp_footer', [$this->get_main(), 'load_custom_css'], 100);
                add_action('admin_footer', [$this->get_main(), 'load_custom_css'], 100);

                break;
            case 'files':
                wp_enqueue_style('qtip');

                if ($this->get_user()->can_move()) {
                    wp_enqueue_script('jquery-ui-droppable');
                    wp_enqueue_script('jquery-ui-draggable');
                }

                wp_enqueue_script('jquery-effects-core');
                wp_enqueue_script('jquery-effects-fade');
                wp_enqueue_style('ilightbox');
                wp_enqueue_style('ilightbox-skin-outofthebox');
                wp_enqueue_script('google-recaptcha');

                break;
            case 'mediaplayer':

                break;
            case 'upload':
                //wp_enqueue_style('OutoftheBox-fileupload-jquery-ui');
                wp_enqueue_script('jquery-ui-droppable');
                wp_enqueue_script('jquery-ui-button');
                wp_enqueue_script('jquery-ui-progressbar');
                wp_enqueue_script('jQuery.iframe-transport');
                wp_enqueue_script('jQuery.fileupload-oftb');
                wp_enqueue_script('jQuery.fileupload-process');
                wp_enqueue_script('google-recaptcha');

                break;
        }

        $this->_loadscripts[$template] = true;
    }

    protected function remove_deprecated_options($options = [])
    {
        // Deprecated Shuffle
        if (isset($options['shuffle'])) {
            unset($options['shuffle']);
            $options['sortfield'] = 'shuffle';
        }
        // Changed Userfolders
        if (isset($options['userfolders']) && '1' === $options['userfolders']) {
            $options['userfolders'] = 'auto';
        }

        if (isset($options['partiallastrow'])) {
            unset($options['partiallastrow']);
        }

        if (isset($options['maxfiles']) && empty($options['maxfiles'])) {
            unset($options['maxfiles']);
        }

        // Convert bytes in version before 1.8 to MB
        if (isset($options['maxfilesize']) && !empty($options['maxfilesize']) && ctype_digit($options['maxfilesize'])) {
            $options['maxfilesize'] = Helpers::bytes_to_size_1024($options['maxfilesize']);
        }

        if (isset($options['forcedownload']) && 1 === $options['forcedownload'] && !isset($options['previewrole'])) {
            $options['previewrole'] = 'none';
        }

        return $options;
    }

    protected function save_shortcodes()
    {
        $this->get_shortcodes()->set_shortcode($this->listtoken, $this->options);
        $this->get_shortcodes()->update_cache();
    }

    protected function _is_action_authorized($hook = false)
    {
        $nonce_verification = ('Yes' === $this->get_setting('nonce_validation'));
        $allow_nonce_verification = apply_filters('out_of_the_box_allow_nonce_verification', $nonce_verification);

        if ($allow_nonce_verification && isset($_REQUEST['action']) && (false === $hook) && is_user_logged_in()) {
            $is_authorized = false;
            switch ($_REQUEST['action']) {
                case 'outofthebox-get-filelist':
                case 'outofthebox-get-gallery':
                case 'outofthebox-get-playlist':
                case 'outofthebox-rename-entry':
                case 'outofthebox-move-entries':
                case 'outofthebox-upload-file':
                case 'outofthebox-add-folder':
                case 'outofthebox-create-zip':
                case 'outofthebox-delete-entries':
                    $is_authorized = check_ajax_referer($_REQUEST['action'], false, false);

                    break;
                case 'outofthebox-create-link':
                    $is_authorized = check_ajax_referer('outofthebox-create-link', false, false);

                    break;
                case 'outofthebox-embedded':
                case 'outofthebox-download':
                case 'outofthebox-stream':
                case 'outofthebox-getpopup':
                case 'outofthebox-thumbnail':
                case 'outofthebox-preview':
                    $is_authorized = true;

                    break;
                case 'outofthebox-reset-cache':
                case 'outofthebox-reset-statistics':
                    $is_authorized = check_ajax_referer('outofthebox-admin-action', false, false);

                    break;
                case 'outofthebox-revoke':
                    $is_authorized = (false !== check_ajax_referer('outofthebox-admin-action', false, false));

                    break;
                case 'edit': // Required for integration one Page/Post pages
                    $is_authorized = true;

                    break;
                case 'editpost': // Required for Yoast SEO Link Watcher trying to build the shortcode
                case 'elementor':
                case 'elementor_ajax':
                case 'wpseo_filter_shortcodes':
                    return false;
                default:
                    error_log('[Out-of-the-Box message]: '." Function _is_action_authorized() didn't receive a valid action: ".$_REQUEST['action']);
                    die();
            }

            if (false === $is_authorized) {
                error_log('[Out-of-the-Box message]: '." Function _is_action_authorized() didn't receive a valid nonce");
                die();
            }
        }

        return true;
    }

    private function _set_requested_path($path = '')
    {
        if ('' === $path) {
            if ('' !== $this->_lastPath) {
                $path = $this->_lastPath;
            } else {
                $path = '/';
            }
        }

        $regex = '/(id:.*)|(ns:[0-9]+(\/.*)?)/i';
        if (1 === preg_match($regex, $path)) {
            $this->_requestedPath = $path;
            $this->_requestedCompletePath = $path;

            return;
        }

        $path = \TheLion\OutoftheBox\Helpers::clean_folder_path($path);
        $path_parts = \TheLion\OutoftheBox\Helpers::get_pathinfo($path);

        $this->_requestedDir = '';
        $this->_requestedFile = '';

        if (isset($path_parts['extension'])) {
            //it's a file
            $this->_requestedFile = $path_parts['basename'];
            $this->_requestedDir = str_replace('\\', '/', $path_parts['dirname']);
            $requestedDir = ('/' === $this->_requestedDir) ? '/' : $this->_requestedDir.'/';
            $this->_requestedPath = $requestedDir.$this->_requestedFile;
        } else {
            //it's a dir
            $this->_requestedDir = str_replace('\\', '/', $path);
            $this->_requestedFile = '';
            $this->_requestedPath = $this->_requestedDir;
        }

        $requestedCompletePath = $this->_rootFolder;
        if ($this->_rootFolder !== $this->_requestedPath) {
            $requestedCompletePath = html_entity_decode($this->_rootFolder.$this->_requestedPath);
        }

        $this->_requestedCompletePath = str_replace('//', '/', $requestedCompletePath);
    }
}
