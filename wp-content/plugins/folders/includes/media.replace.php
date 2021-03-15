<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class folders_replace_media {

    public $button_color;

    public $is_enabled =  false;

    public $upgradeLink;

    function __construct() {

        $customize_folders = get_option('customize_folders');

        $this->button_color = isset($customize_folders['media_replace_button'])?$customize_folders['media_replace_button']:"#FA166B";

        $this->is_enabled = isset($customize_folders['folders_enable_replace_media'])?$customize_folders['folders_enable_replace_media']:"yes";

        $this->is_enabled = ($this->is_enabled == "yes")?true:false;

        if($this->is_enabled) {

            add_action('admin_menu', array($this, 'admin_menu'));

            add_filter('media_row_actions', array($this, 'add_media_action'), 10, 2);

            add_action('add_meta_boxes', function () {
                add_meta_box('folders-replace-box', esc_html__('Replace Media', 'folders'), array($this, 'replace_meta_box'), 'attachment', 'side', 'low');
            });
            add_filter('attachment_fields_to_edit', array($this, 'attachment_editor'), 10, 2);

            add_action('admin_enqueue_scripts', array($this, 'folders_admin_css_and_js'));

            add_action('admin_init', array($this, 'handle_folders_file_upload'));
        }

	    $customize_folders = get_option("customize_folders");
	    if(isset($customize_folders['show_folder_in_settings']) && $customize_folders['show_folder_in_settings'] == "yes") {
		    $this->upgradeLink = admin_url("options-general.php?page=wcp_folders_settings&setting_page=upgrade-to-pro");
	    } else {
		    $this->upgradeLink = admin_url("admin.php?page=folders-upgrade-to-pro");
	    }
    }

    public function folders_admin_css_and_js($page) {
	    if($page == "media_page_folders-replace-media" || $page == "admin_page_folders-replace-media") {
            wp_enqueue_style('folders-media', plugin_dir_url(dirname(__FILE__)) . 'assets/css/replace-media.css', array(), WCP_FOLDER_VERSION);
            wp_enqueue_script('folders-media', plugin_dir_url(dirname(__FILE__)) . 'assets/js/replace-media.js', array(), WCP_FOLDER_VERSION);
        }
    }

    public function admin_menu() {
        add_submenu_page(null,
            esc_html__("Replace media", "folders"),
            esc_html__("Replace media", "folders"),
            'upload_files',
            'folders-replace-media',
            array($this, 'folders_replace_media')
        );
    }

    public function folders_replace_media() {
        global $plugin_page;
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
        $attachment_id = isset($_GET['attachment_id']) ? sanitize_text_field($_GET['attachment_id']) : '';
        $nonce = isset($_GET['nonce']) ? sanitize_text_field($_GET['nonce']) : '';
        if (!wp_verify_nonce($nonce, "folders-replace-media-".$attachment_id)) {
            echo 'Invalid Nonce';
            exit;
        }
        $attachment = get_post($attachment_id);
        if(empty($attachment) || !isset($attachment->guid)) {
            echo 'Invalid URL';
            exit;
        }
        $guid = $attachment->guid;
        $guid = explode(".", $guid);
        if($guid == $attachment->guid) {
            echo 'Invalid URL';
            exit;
        }
        $form_action = $this->getMediaReplaceURL($attachment_id);
        include_once dirname(dirname(__FILE__)) . WCP_DS . "/templates" . WCP_DS . "admin" . WCP_DS . "media-replace.php";
    }

    public function add_media_action($actions, $post) {
        if(!$this->is_enabled) {
	        return array_merge($actions);
        }
	    if (wp_attachment_is('image', $post->ID)) {
		    $link = $this->getMediaReplaceURL( $post->ID );

		    $newaction['replace_media'] = '<a style="color: ' . $this->button_color . '" href="' . $link . '" rel="permalink">' . esc_html__( "Replace media", "folders" ) . '</a>';

		    return array_merge( $actions, $newaction );
	    } else {
		    $link = $this->getMediaReplaceURL( $post->ID );

		    $newaction['replace_media'] = '<a style="color: ' . $this->button_color . '" target="_blank" href="' . $this->upgradeLink . '" rel="permalink">' . esc_html__( "Replace Media 🔑", "folders" ) . '</a>';

		    return array_merge( $actions, $newaction );
	    }
	    return $actions;
    }

    public function getMediaReplaceURL($attach_id) {
        $url = admin_url( "upload.php");
        $url = add_query_arg(array(
            'page' => 'folders-replace-media',
            'action' => 'folders_replace_media',
            'attachment_id' => $attach_id,
            'nonce' => wp_create_nonce("folders-replace-media-".$attach_id)
        ), $url);

        return $url;
    }

    public function replace_meta_box($post) {
	    if (wp_attachment_is('image', $post->ID)) {
		    $link = $this->getMediaReplaceURL($post->ID);
		    echo "<p><a style='background: {$this->button_color}; border-color: {$this->button_color}; color:#ffffff' href='" . $link . "' class='button-secondary'>" . esc_html__( "Upload a new file", "folders" ) . "</a></p><p>" . esc_html__( "Click on the button to replace the file with another file", "folders" ) . "</p>";
	    } else {
		    echo "<p><a style='color: {$this->button_color}; font-weight: 500' target='_blank' href='" . $this->upgradeLink . "' >" . esc_html__( "Upgrade to Pro", "folders" ) . "</a> ".esc_html__( "to replace any kind of files while uploading including pdf/svg/docx/etc & more.", "folders" ) . "</p>";
	    }
    }

    public function attachment_editor($form_fields, $post)
    {
        $screen = null;
        if (function_exists('get_current_screen'))
        {
            $screen = get_current_screen();

            if(! is_null($screen) && $screen->id == 'attachment') // hide on edit attachment screen.
                return $form_fields;
        }
	    if (wp_attachment_is('image', $post->ID)) {
		    $link                   = $this->getMediaReplaceURL( $post->ID );
		    $form_fields["folders"] = array(
			    "label" => esc_html__( "Replace media", "folders" ),
			    "input" => "html",
			    "html"  => "<a style='background: {$this->button_color}; border-color: {$this->button_color}; color:#ffffff' href='" . $link . "' class='button-secondary'>" . esc_html__( "Upload a new file", "folders" ) . "</a>",
			    "helps" => esc_html__( "Click on the button to replace the file with another file", "folders" )
		    );
	    } else {
		    $form_fields["folders"] = array(
			    "label" => esc_html__( "Replace media", "folders" ),
			    "input" => "html",
			    "html"  => "<div style='border: solid 1px #c0c0c0; padding: 10px; border-radius: 2px; background: #ececec;'><a style='color: {$this->button_color}; font-weight: 500' target='_blank' href='" . $this->upgradeLink . "' >" . esc_html__( "Upgrade to Pro", "folders" ) . "</a> ".esc_html__( "to replace media files other than images", "folders" ) . "</div>",
			    "helps" => esc_html__( "Click on the button to replace the file with another file", "folders" )
		    );
	    }

        return $form_fields;
    }

    public function getFileSize($attachment_id) {
        $size = filesize( get_attached_file( $attachment_id ));
        if($size > 1000000) {
            $size = ($size/1000000);
            return number_format((float)$size, 2, ".", ",")." MB";
        } else if($size > 1000) {
            $size = ($size/1000);
            return number_format((float)$size, 2, ".", ",")." KB";
        }
        return $size." B";
    }

    public $old_file_ext;
    public $old_file_path;
    public $old_file_url;
    public $new_file_path;
    public $new_file_url;

    public $new_file_name;

    public $current_upload_data;

    public $mode = "rename-file";

    public $old_image_meta;
    public $new_image_meta;
    public $upload_dir;

    public $is_old_image = 0;
    public $is_new_image = 0;
    public $attachment_id;

    public function handle_folders_file_upload() {
        global $wpdb;
        if(isset($_FILES['new_media_file'])) {
            if($_FILES['new_media_file']['error'] == 0) {
                $attachment_id = isset($_GET['attachment_id']) ? sanitize_text_field($_GET['attachment_id']) : '';
                $nonce = isset($_GET['nonce']) ? sanitize_text_field($_GET['nonce']) : '';
                if (!wp_verify_nonce($nonce, "folders-replace-media-" . $attachment_id)) {
                    return;
                }
                $attachment = get_post($attachment_id);
                if (empty($attachment) || !isset($attachment->guid)) {
                    return;
                }
                $attachment_url = $attachment->guid;
                $url = wp_get_attachment_url($attachment_id);
                if(!empty($url)) {
                    $attachment_url = $url;
                }
                $guid = explode(".", $attachment_url);
                $guid = array_pop($guid);
                if ($guid == $attachment->guid) {
                    return;
                }

                $this->attachment_id = $attachment_id;

                $file = $_FILES['new_media_file'];
                $file_name = $file['name'];
                $file_ext = explode(".", $file_name);
                $file_ext = array_pop($file_ext);

                if ($guid == $file_ext) {
                    $this->mode = "replace-file";
                }

                if (wp_attachment_is('image', $attachment_id)) {
                    $this->is_old_image = 1;
                }
                $this->old_file_url = $attachment_url;

                $new_file = $file['tmp_name'];

                $file_parts = pathinfo($attachment_url);

                $upload_dir = wp_upload_dir($attachment->post_date_gmt);
                $this->current_upload_data = $upload_dir;
                $upload_path = $upload_dir['path'];

                $this->upload_dir = $upload_dir;

                $this->old_file_path = $upload_dir['path'] . "/" . $file_parts['basename'];

                $this->old_image_meta = wp_get_attachment_metadata($attachment_id);
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 755, true);
                }
                if (is_dir($upload_path)) {
                    $file_name = $this->checkForFileName($file['name'], $upload_path.DIRECTORY_SEPARATOR);
                    $upload_file = $upload_path . DIRECTORY_SEPARATOR . $file_name;
                    $status = move_uploaded_file($new_file, $upload_file);

                    $this->new_file_path = $upload_dir['path'] . "/" . $file_name;

                    $this->new_file_url = $upload_dir['url']."/".$file_name;

                    if ($status) {

                        if(file_exists($this->upload_dir['path'].DIRECTORY_SEPARATOR.$file_parts['basename'])) {
                            @unlink($this->upload_dir['path'].DIRECTORY_SEPARATOR.$file_parts['basename']);
                        }

                        update_attached_file($attachment->ID, $this->new_file_path);

                        $update_array = array();
                        $update_array['ID'] = $attachment->ID;
                        $update_array['post_title'] = $file_parts['filename'];
                        $update_array['post_name'] = sanitize_title($file_parts['filename']);
                        $update_array['guid'] = $this->new_file_path; //wp_get_attachment_url($this->post_id);
                        $update_array['post_mime_type'] = $file['type'];
                        $post_id = \wp_update_post($update_array, true);

                        // update post doesn't update GUID on updates.
                        $wpdb->update($wpdb->posts, array('guid' => $this->new_file_path), array('ID' => $attachment->ID));

                        $this->removeThumbImages();

                        $metadata = wp_generate_attachment_metadata($attachment->ID, $this->new_file_path);
                        wp_update_attachment_metadata($attachment->ID, $metadata);

                        $this->new_image_meta = wp_get_attachment_metadata($attachment_id);

                        $this->searchAndReplace();

                        wp_redirect(admin_url("post.php?post=" . $attachment_id . "&action=edit"));
                        exit;
                    } else {
                        wp_die("Error during uploading file");
                    }

                } else {
                    wp_die("Permission issue, Unable to create directory");
                }
            }
        }
    }

    public function checkForFileName($fileName, $filePath, $postFix = 0) {
        $new_file_name = $fileName;
        if(!empty($postFix)) {
            $file_array = explode(".", $fileName);
            $file_ext = array_pop($file_array);
            $new_file_name = implode(".", $file_array)."-".$postFix.".".$file_ext;
        }
        if(!file_exists($filePath.$new_file_name)) {
            return $new_file_name;
        }
        return $this->checkForFileName($fileName, $filePath, ($postFix+1));
    }

    public $replace_items = array();

    public function removeThumbImages() {
        if(!empty($this->old_image_meta) && isset($this->old_image_meta['sizes']) && !empty($this->upload_dir) && isset($this->upload_dir['path'])) {
            $path = $this->upload_dir['path'].DIRECTORY_SEPARATOR;
            foreach ($this->old_image_meta['sizes'] as $image) {
                if(file_exists($path.$image['file'])) {
                    @unlink($path . $image['file']);
                }
            }
        }
    }

    public function searchAndReplace() {
        if (wp_attachment_is('image', $this->attachment_id)) {
            $this->is_new_image = 1;
        }
        if($this->old_file_url != $this->new_file_url) {
            $replace = array(
                'search' => $this->old_file_url,
                'replace' => $this->new_file_url,
            );
            $this->replace_items[] = $replace;
        }

        $base_url = $this->upload_dir['url'];
        $base_url = trim($base_url, "/")."/";
        $new_url = $this->new_file_url;

        if(isset($this->old_image_meta['sizes']) && !empty($this->old_image_meta['sizes'])) {
            if(!isset($this->new_image_meta['sizes']) || empty($this->new_image_meta['sizes'])) {
                foreach ($this->old_image_meta['sizes'] as $key=>$image) {
                    $replace = array(
                        'search' => $base_url.$image['file'],
                        'replace' => $new_url,
                    );
                    $this->replace_items[] = $replace;
                }
            } else if(isset($this->new_image_meta['sizes']) && !empty($this->new_image_meta['sizes'])) {
                $new_size = $this->new_image_meta['sizes'];
                foreach ($this->old_image_meta['sizes'] as $key=>$image) {
                    $new_replace_url = $new_url;
                    if(isset($new_size[$key])) {
                        $new_replace_url = $base_url.$new_size[$key]['file'];
                    }
                    $replace = array(
                        'search' => $base_url.$image['file'],
                        'replace' => $new_replace_url,
                    );
                    $this->replace_items[] = $replace;
                }
            }
        }

        if(!empty($this->replace_items)) {
            $replace_items = array();
            foreach($this->replace_items as $args) {
                if($args['search'] != $args['replace']) {
                    $replace_items[] = $args;
                }
            }
            $this->replace_items = $replace_items;
            $this->replaceURL();
        }
    }

    function replaceURL() {
        /* check in post content */
        $this->checkInPostContent();

        /* check in options */
        $this->checkInOptions();

        /* check in meta */
        $this->checkInMetaData();

        if(function_exists('folders_clear_all_caches')) {
            folders_clear_all_caches();
        }
    }

    function checkInPostContent() {
        global $wpdb;
        $post_table = $wpdb->prefix."posts";
        if(!empty($this->replace_items)) {
            $query = "SELECT ID, post_content FROM {$post_table} WHERE post_content LIKE %s";
            $update_query = "UPDATE {$post_table} SET post_content = %s WHERE ID = %d";
            foreach ($this->replace_items as $args) {
                if($args['search'] != $args['replace']) {
                    $sql_query = $wpdb->prepare($query, "%".$args['search']."%");
                    $results = $wpdb->get_results($sql_query, ARRAY_A );
                    if(!empty($results)) {
                        foreach ($results AS $row) {
                            $content = $this->findAndReplaceContent($row['post_content'], $args['search'], $args['replace']);
                            $update_post_query = $wpdb->prepare($update_query, $content, $row['ID']);
                            $result = $wpdb->query($update_post_query);
                        }
                    }
                }
            }
        }
    }

    function checkInOptions() {
        global $wpdb;
        $post_table = $wpdb->prefix."options";
        if(!empty($this->replace_items)) {
            $query = "SELECT option_id, option_value FROM {$post_table} WHERE option_value LIKE %s";
            $update_query = "UPDATE {$post_table} SET option_value = %s WHERE option_id = %d";
            foreach ($this->replace_items as $args) {
                if($args['search'] != $args['replace']) {
                    $sql_query = $wpdb->prepare($query, "%".$args['search']."%");
                    $results = $wpdb->get_results($sql_query, ARRAY_A );
                    if(!empty($results)) {
                        foreach ($results AS $row) {
                            $content = $this->findAndReplaceContent($row['post_content'], $args['search'], $args['replace']);
                            $update_post_query = $wpdb->prepare($update_query, $content, $row['ID']);
                            $result = $wpdb->query($update_post_query);
                        }
                    }
                }
            }
        }
    }

    function checkInMetaData() {
        $tables = array(
            array(
                'table_name' => 'usermeta',
                'primary_key' => 'umeta_id',
                'search_key' => 'meta_value'
            ),
            array(
                'table_name' => 'termmeta',
                'primary_key' => 'meta_id',
                'search_key' => 'meta_value'
            ),
            array(
                'table_name' => 'postmeta',
                'primary_key' => 'meta_id',
                'search_key' => 'meta_value'
            ),
            array(
                'table_name' => 'commentmeta',
                'primary_key' => 'meta_id',
                'search_key' => 'meta_value'
            )
        );
        global $wpdb;
        foreach ($tables as $table) {
            $post_table = $wpdb->prefix . $table['table_name'];
            if (!empty($this->replace_items)) {
                $query = "SELECT {$table['primary_key']}, {$table['search_key']} FROM {$post_table} WHERE {$table['search_key']} LIKE %s";
                $update_query = "UPDATE {$post_table} SET {$table['search_key']} = %s WHERE {$table['primary_key']} = %d";
                foreach ($this->replace_items as $args) {
                    if ($args['search'] != $args['replace']) {
                        $sql_query = $wpdb->prepare($query, "%" . $args['search'] . "%");
                        $results = $wpdb->get_results($sql_query, ARRAY_A);
                        if (!empty($results)) {
                            foreach ($results as $row) {
                                $content = $this->findAndReplaceContent($row[$table['search_key']], $args['search'], $args['replace']);
                                $update_post_query = $wpdb->prepare($update_query, $content, $row[$table['primary_key']]);
                                $result = $wpdb->query($update_post_query);
                            }
                        }
                    }
                }
            }
        }
    }

    function findAndReplaceContent($content, $search, $replace, $in_deep = false) {
        $content = maybe_unserialize($content);
        $isJson = $this->isJSON($content);

        if ($isJson) {
            $content = json_decode($content);
        }

        if (is_string($content)) {
            $content = str_replace($search, $replace, $content);
        }
        else if(is_wp_error($content)) {

        }
        else if(is_array($content)) {
            foreach($content as $index => $value) {
                $content[$index] = $this->findAndReplaceContent($value, $search, $replace, true);
                if (is_string($index))  {
                    $index_replaced = $this->findAndReplaceContent($index, $search, $replace, true);
                    if ($index_replaced !== $index)
                        $content = $this->changeArrayKey($content, array($index => $index_replaced));
                }
            }
        }
        else if(is_object($content)) {
            foreach($content as $key => $value) {
                $content->{$key} = $this->findAndReplaceContent($value, $search, $replace, true);
            }
        }

        if ($isJson && $in_deep === false) {
            $content = json_encode($content, JSON_UNESCAPED_SLASHES);
        }
        else if($in_deep === false && (is_array($content) || is_object($content))) {
            $content = maybe_serialize($content);
        }

        return $content;
    }

    function changeArrayKey($array, $set) {
        if (is_array($array) && is_array($set)) {
            $newArr = array();
            foreach ($array as $k => $v) {
                $key = array_key_exists( $k, $set) ? $set[$k] : $k;
                $newArr[$key] = is_array($v) ? $this->changeArrayKey($v, $set) : $v;
            }
            return $newArr;
        }
        return $array;
    }

    function isJSON($content)
    {
        if (is_array($content) || is_object($content))
            return false;

        $json = json_decode($content);
        return $json && $json != $content;
    }
}
$folders_replace_media = new folders_replace_media();