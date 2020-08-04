<?php

namespace TheLion\OutoftheBox;

class WooCommerce extends \WC_Integration
{
    /**
     * @var \TheLion\OutoftheBox\WooCommerce_Uploads
     */
    public $uploads;

    /**
     * @var \TheLion\OutoftheBox\WooCommerce_Downloads
     */
    public $downloads;

    /**
     * @var \TheLion\OutoftheBox\Main
     */
    private $_main;

    public function __construct()
    {
        global $OutoftheBox;
        $this->_main = $OutoftheBox;

        // Add Filter to remove the default 'Guest - ' part from the Private Folder name
        add_filter('outofthebox_private_folder_name_guests', [&$this, 'rename_private_folder_for_guests']);

        // Update shortcodes with Product ID/Order ID when available
        add_filter('outofthebox_shortcode_add_options', [&$this, 'update_shortcode'], 10, 3);

        if (defined('DOING_AJAX') && (!isset($_REQUEST['action']) || 'outofthebox-wcpd-direct-download' !== $_REQUEST['action'])) {
            return false;
        }

        $this->uploads = new \TheLion\OutoftheBox\WooCommerce_Uploads($this);
        $this->downloads = new \TheLion\OutoftheBox\WooCommerce_Downloads($this);

        $this->id = 'outofthebox-woocommerce';
        $this->method_title = __('WooCommerce Dropbox', 'outofthebox');
        $this->method_description = __('Easily add downloadable products right from your Dropbox.', 'outofthebox').' '
                .sprintf(__('To be able to use this integration, you only need to link your Dropbox Account to the plugin on the %s.', 'outofthebox'), '<a href="'.admin_url('admin.php?page=OutoftheBox_settings').'">Out-of-the-Box settings page</a>');

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();
    }

    public function rename_private_folder_for_guests($private_folder_name)
    {
        return str_replace(__('Guests', 'outofthebox').' - ', '', $private_folder_name);
    }

    public function update_shortcode($options, $processor, $raw_shortcode)
    {
        if (isset($raw_shortcode['wc_order_id'])) {
            $options['wc_order_id'] = $raw_shortcode['wc_order_id'];
        }

        if (isset($raw_shortcode['wc_product_id'])) {
            $options['wc_product_id'] = $raw_shortcode['wc_product_id'];
        }

        return $options;
    }

    /**
     * @return \TheLion\OutoftheBox\Processor
     */
    public function get_processor()
    {
        return $this->_main->get_processor();
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
            $this->_app = new \TheLion\OutoftheBox\App($this->get_processor());
            $this->_app->start_client();
        }

        return $this->_app;
    }
}

class WooCommerce_Downloads
{
    /**
     * @var \TheLion\OutoftheBox\WooCommerce
     */
    private $_woocommerce;

    public function __construct(WooCommerce $_woocommerce)
    {
        $this->_woocommerce = $_woocommerce;

        // Actions
        add_action('woocommerce_download_file_force', [&$this, 'do_direct_download'], 1, 2);
        add_action('woocommerce_download_file_xsendfile', [&$this, 'do_xsendfile_download'], 1, 2);
        add_action('woocommerce_download_file_redirect', [&$this, 'do_redirect_download'], 1, 2);

        if (class_exists('WC_Product_Documents')) {
            add_action('wp_ajax_nopriv_outofthebox-wcpd-direct-download', [&$this, 'wc_product_documents_download_via_url']);
            add_action('wp_ajax_outofthebox-wcpd-direct-download', [&$this, 'wc_product_documents_download_via_url']);
            add_filter('wc_product_documents_link_target', [&$this, 'wc_product_documents_open_link_in_new_window'], 10, 4);
            add_filter('wc_product_documents_get_sections', [&$this, 'wc_product_documents_update_document_urls'], 10, 3);
        }

        // Load custom scripts in the admin area
        if (is_admin()) {
            add_action('admin_enqueue_scripts', [$this, 'add_scripts']);
            add_action('edit_form_advanced', [&$this, 'render_file_selector'], 1, 1);
        }
    }

    /**
     * Render the File Browser to allow the user to add files to the Product.
     *
     * @param \WP_Post $post
     *
     * @return string
     */
    public function render_file_selector(\WP_Post $post = null)
    {
        if (isset($post) && 'product' !== $post->post_type) {
            return;
        } ?>
        <div id='oftb-embedded' style='clear:both;display:none'>
          <?php
          $atts = [
              'singleaccount' => '0',
              'dir' => '',
              'mode' => 'files',
              'filelayout' => 'grid',
              'filesize' => '0',
              'filedate' => '0',
              'addfolder' => '0',
              'showbreadcrumb' => '1',
              'showcolumnnames' => '0',
              'downloadrole' => 'none',
              'candownloadzip' => '0',
              'showsharelink' => '0',
              'mcepopup' => 'woocommerce', ];

        echo $this->get_woocommerce()->get_processor()->create_from_shortcode(
            $atts
        ); ?>
        </div>
        <?php
    }

    /**
     * Load all the required Script and Styles.
     */
    public function add_scripts()
    {
        $current_screen = get_current_screen();

        if (!in_array($current_screen->id, ['product', 'shop_order'])) {
            return;
        }

        $this->get_woocommerce()->get_main()->load_styles();
        $this->get_woocommerce()->get_main()->load_scripts();

        // register scripts/styles
        add_thickbox();
        wp_register_style('outofthebox-woocommerce', OUTOFTHEBOX_ROOTPATH.'/css/woocommerce.css');
        wp_register_script('outofthebox-woocommerce', OUTOFTHEBOX_ROOTPATH.'/includes/js/Woocommerce.js', ['jquery'], OUTOFTHEBOX_VERSION);

        // enqueue scripts/styles
        wp_enqueue_style('OutoftheBox.tinymce');
        wp_enqueue_style('Awesome-Font-5-css');
        wp_enqueue_style('outofthebox-woocommerce');
        wp_enqueue_script('outofthebox-woocommerce');
        wp_enqueue_script('OutoftheBox');

        // register translations
        $translation_array = [
            'choose_from_dropbox' => __('Choose from Dropbox', 'outofthebox'),
            'download_url' => '?action=outofthebox-wc-direct-download&id=',
            'file_browser_url' => OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-getwoocommercepopup',
            'wcpd_url' => OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-wcpd-direct-download&id=',
        ];

        wp_localize_script('outofthebox-woocommerce', 'outofthebox_woocommerce_translation', $translation_array);
    }

    /**
     * @param string $file_path
     *
     * @return \TheLion\OutoftheBox\CacheNode
     */
    public function get_entry_for_download_by_url($file_path)
    {
        $processor = $this->get_woocommerce()->get_processor();

        $download_url = parse_url($file_path);
        parse_str($download_url['query'], $download_url_query);
        $entry_id = $download_url_query['id'];
        $entry_path = urldecode(base64_decode($entry_id));

        // Fallback for old embed urls without account info
        if (!isset($download_url_query['account_id'])) {
            $primary_account = $processor->get_accounts()->get_primary_account();
            if (null === $primary_account) {
                return false;
            }
            $account_id = $primary_account->get_id();
        } else {
            $account_id = $download_url_query['account_id'];
        }

        $account = $processor->get_accounts()->get_account_by_id($account_id);

        if (null === $account) {
            return false;
        }

        $processor->set_current_account($account);

        $entry = $this->get_woocommerce()->get_processor()->get_client()->get_entry($entry_path, false);

        if (false === $entry) {
            return false;
        }

        return $entry;
    }

    public function get_redirect_url_for_entry(Entry $entry)
    {
        $transient_url = self::get_download_url_transient($entry->get_id());
        if (!empty($transient_url)) {
            return $transient_url;
        }

        $downloadlink = $this->get_woocommerce()->get_processor()->get_client()->get_temporarily_link($entry);

        do_action('outofthebox_log_event', 'outofthebox_download', $entry);

        self::set_download_url_transient($entry->get_id(), $downloadlink);

        return $downloadlink;
    }

    public function wc_product_documents_download_via_url()
    {
        if (!isset($_REQUEST['id'])) {
            return false;
        }

        if (!isset($_REQUEST['pid'])) {
            return false;
        }

        $entry_id = urldecode($_REQUEST['id']);
        $product_id = $_REQUEST['pid'];
        $documents_collection = new \WC_Product_Documents_Collection($product_id);

        foreach ($documents_collection->get_sections() as $section) {
            foreach ($section->get_documents() as $position => $document) {
                $file_location = urldecode($document->get_file_location());

                if (false === strpos($file_location, 'outofthebox-wcpd-direct-download')) {
                    continue;
                }

                if (false !== strpos($file_location, 'id='.$entry_id)) {
                    $entry_path = urldecode(base64_decode($entry_id));
                    $cached_entry = $this->get_woocommerce()->get_processor()->get_client()->get_entry($entry_path, false);
                    $downloadlink = $this->get_redirect_url_for_entry($cached_entry);

                    // Redirect to the file
                    header('Location: '.$downloadlink);
                    exit;
                }
            }
        }

        self::download_error(__('File not found', 'woocommerce'));
    }

    public function wc_product_documents_open_link_in_new_window($target, $product, $section, $document)
    {
        $file_location = $document->get_file_location();

        if (false === strpos($file_location, 'outofthebox-wcpd-direct-download')) {
            return false; // Do nothing
        }

        return '_blank" class="lightbox-group" title="'.$document->get_label();
    }

    public function wc_product_documents_update_document_urls($sections, $collection, $include_empty)
    {
        $product_id = $collection->get_product_id();
        if (empty($product_id)) {
            return $sections;
        }

        foreach ($sections as $section) {
            foreach ($section->get_documents() as $position => $document) {
                $file_location = $document->get_file_location();

                if (false === strpos($file_location, 'outofthebox-wcpd-direct-download')) {
                    continue;
                }

                if (false !== strpos($file_location, 'pid')) {
                    continue;
                }

                $section->add_document(new \WC_Product_Documents_Document($document->get_label(), $file_location.'&pid='.$collection->get_product_id()), $position);
            }
        }

        return $sections;
    }

    public function do_direct_download($file_path, $filename)
    {
        if (false === strpos($file_path, 'outofthebox-wc-direct-download')) {
            return false; // Do nothing
        }

        $entry = $this->get_entry_for_download_by_url($file_path);

        if (empty($entry)) {
            self::download_error(__('File not found', 'woocommerce'));
        }

        $downloadlink = $this->get_redirect_url_for_entry($entry);
        $filename = $entry->get_name();
        // Download the file
        self::download_headers($downloadlink, $filename);

        if (!\WC_Download_Handler::readfile_chunked($downloadlink)) {
            $this->do_redirect_download($file_path, $filename);
        }

        exit;
    }

    public function do_xsendfile_download($file_path, $filename)
    {
        if (false === strpos($file_path, 'outofthebox-wc-direct-download')) {
            return false; // Do nothing
        }

        // Fallback
        $this->do_direct_download($file_path, $filename);
    }

    public function do_redirect_download($file_path, $filename)
    {
        if (false === strpos($file_path, 'outofthebox-wc-direct-download')) {
            return false; // Do nothing
        }

        $entry = $this->get_entry_for_download_by_url($file_path);

        if (empty($entry)) {
            self::download_error(__('File not found', 'woocommerce'));
        }

        $downloadlink = $this->get_redirect_url_for_entry($entry);

        // Redirect to the file
        header('Location: '.$downloadlink);
        exit;
    }

    public static function get_download_url_transient($entry_id)
    {
        return get_transient('outofthebox_wc_download_'.$entry_id);
    }

    public static function set_download_url_transient($entry_id, $url)
    {
        // Update progress
        return set_transient('outofthebox_wc_download_'.$entry_id, $url, HOUR_IN_SECONDS);
    }

    /**
     * @return \TheLion\OutoftheBox\WooCommerce
     */
    public function get_woocommerce()
    {
        return $this->_woocommerce;
    }

    /**
     * Get content type of a download.
     *
     * @param string $file_path
     *
     * @return string
     */
    private static function get_download_content_type($file_path)
    {
        $file_extension = strtolower(substr(strrchr($file_path, '.'), 1));
        $ctype = 'application/force-download';

        foreach (get_allowed_mime_types() as $mime => $type) {
            $mimes = explode('|', $mime);
            if (in_array($file_extension, $mimes)) {
                $ctype = $type;

                break;
            }
        }

        return $ctype;
    }

    /**
     * Set headers for the download.
     *
     * @param string $file_path
     * @param string $filename
     */
    private static function download_headers($file_path, $filename)
    {
        self::check_server_config();
        self::clean_buffers();
        nocache_headers();

        header('X-Robots-Tag: noindex, nofollow', true);
        header('Content-Type: '.self::get_download_content_type($file_path));
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; '.sprintf('filename="%s"; ', rawurlencode($filename)).sprintf("filename*=utf-8''%s", rawurlencode($filename)));
        header('Content-Transfer-Encoding: binary');

        if ($size = @filesize($file_path)) {
            header('Content-Length: '.$size);
        }
    }

    /**
     * Check and set certain server config variables to ensure downloads work as intended.
     */
    private static function check_server_config()
    {
        wc_set_time_limit(0);
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
        }
        @ini_set('zlib.output_compression', 'Off');
        @session_write_close();
    }

    /**
     * Clean all output buffers.
     *
     * Can prevent errors, for example: transfer closed with 3 bytes remaining to read.
     */
    private static function clean_buffers()
    {
        if (ob_get_level()) {
            $levels = ob_get_level();
            for ($i = 0; $i < $levels; ++$i) {
                @ob_end_clean();
            }
        } else {
            @ob_end_clean();
        }
    }

    /**
     * Die with an error message if the download fails.
     *
     * @param string $message
     * @param string $title
     * @param int    $status
     */
    private static function download_error($message, $title = '', $status = 404)
    {
        if (!strstr($message, '<a ')) {
            $message .= ' <a href="'.esc_url(get_site_url()).'">Go to back</a>';
        }
        wp_die($message, $title, ['response' => $status]);
    }
}

class WooCommerce_Uploads
{
    /**
     * @var \TheLion\OutoftheBox\WooCommerce
     */
    private $_woocommerce;

    public function __construct(WooCommerce $_woocommerce)
    {
        $this->_woocommerce = $_woocommerce;

        // Add Tabs & Content to Product Edit Page
        add_action('admin_head', [&$this, 'add_product_data_tab_scripts_and_style']);
        add_filter('product_type_options', [&$this, 'add_uploadable_product_option']);
        add_filter('woocommerce_product_data_tabs', [&$this, 'add_product_data_tab']);
        add_action('woocommerce_product_data_panels', [&$this, 'add_product_data_tab_content']);
        add_action('woocommerce_process_product_meta_simple', [&$this, 'save_product_data_fields']);
        add_action('woocommerce_process_product_meta_variable', [&$this, 'save_product_data_fields']);
        add_action('woocommerce_ajax_save_product_variations', [&$this, 'save_product_data_fields']);
        add_action('woocommerce_process_product_meta_composite', [&$this, 'save_product_data_fields']);

        // Add Upload button to my Order Table
        add_filter('woocommerce_my_account_my_orders_actions', [&$this, 'add_orders_column_actions'], 10, 2);

        // Add Upload Box to Order Page
        //add_action('woocommerce_view_order', array(&$this, 'render_upload_field'), 11);
        add_action('woocommerce_order_details_before_order_table', [&$this, 'render_upload_field'], 11);
        add_action('woocommerce_order_details_after_order_table', [&$this, 'render_upload_field'], 11);

        // Add link to upload box in the Thank You text
        add_filter('woocommerce_thankyou_order_received_text', [&$this, 'change_order_received_text'], 10, 2);

        // Add Upload Box to Admin Order Page
        add_action('add_meta_boxes', [&$this, 'add_meta_box'], 10, 2);

        // Add Order note when uploading files
        add_action('outofthebox_upload_post_process', [&$this, 'add_order_note'], 10, 2);
    }

    public function add_order_note($_uploaded_entries, $processor)
    {
        // Grab the Order/Product data from the shortcode
        $order_id = $processor->get_shortcode_option('wc_order_id');
        $product_id = $processor->get_shortcode_option('wc_product_id');

        if (empty($order_id) || empty($product_id)) {
            return;
        }

        $order = new \WC_Order($order_id);

        if (empty($order)) {
            return;
        }

        $product = wc_get_product($product_id);

        // Make sure that we are working with an array
        $uploaded_entries = [];
        if (!is_array($_uploaded_entries)) {
            $uploaded_entries[] = $_uploaded_entries;
        } else {
            $uploaded_entries = $_uploaded_entries;
        }

        // Build the Order note
        $order_note = sprintf(__('%d file(s) uploaded for product', 'outofthebox'), count((array) $uploaded_entries)).' <strong>'.$product->get_title().'</strong>:';
        $order_note .= '<br/><br/><ul>';

        foreach ($uploaded_entries as $entry) {
            $link = ($processor->get_client()->has_shared_link($entry)) ? $processor->get_client()->get_shared_link($entry).'?dl=0' : 'javascript:void(0)';
            $name = $entry->get_name();
            $size = \TheLion\OutoftheBox\Helpers::bytes_to_size_1024($entry->get_size());

            $order_note .= '<li><a href="'.$link.'">'.$name.'</a> ('.$size.')</li>';
        }

        $order_note .= '</ul>';

        // Add the note
        $order->add_order_note($order_note);

        // Save the data
        $order->save();
    }

    /**
     *  Add a Meta Box to the Order Page where you can find all the uploaded files for the order.
     *
     * @param mixed $post_type
     * @param mixed $post
     */
    public function add_meta_box($post_type, $post)
    {
        if (!in_array($post_type, ['shop_order'])) {
            return;
        }

        $order = new \WC_Order($post->ID);

        if (false === $this->requires_order_uploads($order)) {
            return false;
        }

        add_meta_box('woocommerce-outofthebox-box-order-detail', __('Uploaded Files', 'outofthebox'), [&$this, 'render_meta_box'], 'shop_order', 'advanced', 'high');
    }

    /**
     * Add link to upload box in the Thank You text.
     *
     * @param string    $thank_you_text
     * @param \WC_Order $order
     *
     * @return string
     */
    public function change_order_received_text($thank_you_text, $order)
    {
        if (false === $this->requires_order_uploads($order)) {
            return $thank_you_text;
        }

        $order_url = '#uploads';
        $thank_you_text .= ' '.sprintf(__('You can now %sstart uploading your documents%s', 'outofthebox'), '<a href="'.$order_url.'">', '</a>').'.';

        return $thank_you_text;
    }

    /**
     * Add new Product Type to the Product Data Meta Box.
     *
     * @param array $product_type_options
     *
     * @return array
     */
    public function add_uploadable_product_option($product_type_options)
    {
        $product_type_options['uploadable'] = [
            'id' => '_uploadable',
            'wrapper_class' => 'show_if_simple show_if_variable',
            'label' => __('Uploads', 'outofthebox'),
            'description' => __('Allows your customers to upload files when ordering this product.', 'outofthebox'),
            'default' => 'no',
        ];

        return $product_type_options;
    }

    /**
     * Add new Data Tab to the Product Data Meta Box.
     *
     * @param array $product_data_tabs
     *
     * @return array
     */
    public function add_product_data_tab($product_data_tabs)
    {
        $product_data_tabs['cloud-uploads-dropbox'] = [
            'label' => __('Upload to Dropbox', 'outofthebox'),
            'target' => 'cloud_uploads_data_dropbox',
            'class' => ['show_if_uploadable'],
        ];

        return $product_data_tabs;
    }

    /**
     * Add the content of the new Data Tab.
     */
    public function add_product_data_tab_content()
    {
        global $post;

        $default_shortcode = '[outofthebox mode="files" viewrole="all" userfolders="auto" downloadrole="all" upload="1" uploadrole="all" rename="1" renamefilesrole="all" renamefoldersrole="all" editdescription="1" editdescriptionrole="all" delete="1" deletefilesrole="all" deletefoldersrole="all" viewuserfoldersrole="none" search="0" showbreadcrumb="0"]';
        $shortcode = get_post_meta($post->ID, 'outofthebox_upload_box_shortcode', true); ?> 
        <div id='cloud_uploads_data_dropbox' class='panel woocommerce_options_panel' style="display:none" >
          <div class="cloud_uploads_data_panel options_group">
            <?php
            woocommerce_wp_checkbox(
            [
                'id' => 'outofthebox_upload_box',
                'label' => __('Upload to Dropbox', 'outofthebox'),
            ]
        ); ?>
            <div class="show_if_outofthebox_upload_box">
              <h4><?php echo 'Dropbox '.__('Upload Box Settings', 'outofthebox'); ?></h4>
              <?php
              $default_box_title = 'Uploads';
        $box_title = get_post_meta($post->ID, 'outofthebox_upload_box_title', true);

        woocommerce_wp_text_input(
            [
                'id' => 'outofthebox_upload_box_title',
                'label' => __('Title Upload Box', 'outofthebox'),
                'placeholder' => $default_box_title,
                'desc_tip' => false,
                'description' => '<br><br>'.__('Enter the title for the upload box', 'outofthebox').'. '.__('You can use the placeholders <code>%wc_order_id%</code>, <code>%wc_product_id%</code>, <code>%wc_product_sku%</code>, <code>%wc_product_name%</code>, <code>%jjjj-mm-dd%</code>', 'outofthebox'),
                'value' => empty($box_title) ? $default_box_title : $box_title,
            ]
        );

        $default_box_description = '';
        $box_description = get_post_meta($post->ID, 'outofthebox_upload_box_description', true);

        woocommerce_wp_textarea_input(
            [
                'id' => 'outofthebox_upload_box_description',
                'label' => __('Description Upload Box', 'outofthebox'),
                'placeholder' => $default_box_description,
                'desc_tip' => false,
                'description' => '<br><br>'.__('Enter a short description of what the customer needs to upload', 'outofthebox').'.',
                'value' => empty($box_description) ? $default_box_description : $box_description,
            ]
        ); ?>

              <p class="form-field outofthebox_upload_folder ">
                <label for="outofthebox_upload_folder">Upload Box</label>
                <a href="#TB_inline?height=450&amp;width=800&amp;inlineId=oftb-embedded" class="button insert-dropbox OutoftheBox-shortcodegenerator" style="float:none"><?php echo __('Build your Upload Box', 'outofthebox'); ?></a>
                <a href="#" class="" style="float:none" onclick="jQuery('#outofthebox_upload_box_shortcode').fadeToggle()"><?php echo __('Edit Shortcode Manually', 'outofthebox'); ?></a>
                <br/><br/>
                <textarea class="long" style="display:none" name="outofthebox_upload_box_shortcode" id="outofthebox_upload_box_shortcode" placeholder="<?php echo $default_shortcode; ?>"  rows="3" cols="20"><?php echo (empty($shortcode)) ? $default_shortcode : $shortcode; ?></textarea>
              </p>

              <?php
              $default_folder_template = '%wc_order_id% - %wc_product_name% - %user_email%';
        $folder_template = get_post_meta($post->ID, 'outofthebox_upload_box_folder_template', true);

        woocommerce_wp_text_input(
            [
                'id' => 'outofthebox_upload_box_folder_template',
                'label' => __('Upload Folder Name', 'outofthebox'),
                'description' => '<br><br>'.__('Unique folder name where the uploads should be stored. Make sure that Private Folder feature is enabled in the shortcode', 'outofthebox').'. '.__('You can use the placeholders <code>%wc_order_id%</code>, <code>%wc_order_quantity%</code>, <code>%wc_product_id%</code>, <code>%wc_product_sku%</code>, <code>%wc_product_quantity%</code>, <code>%wc_product_name%</code>, <code>%user_login%</code>, <code>%user_email%</code>, <code>%display_name%</code>, <code>%ID%</code>, <code>%user_role%</code>, <code>%jjjj-mm-dd%</code>', 'outofthebox'),
                'desc_tip' => false,
                'placeholder' => $default_folder_template,
                'value' => empty($folder_template) ? $default_folder_template : $folder_template,
            ]
        );

        $outofthebox_upload_box_active_on_status = get_post_meta($post->ID, 'outofthebox_upload_box_active_on_status', true);
        if (empty($outofthebox_upload_box_active_on_status)) {
            $outofthebox_upload_box_active_on_status = ['wc-pending', 'wc-processing'];
        }

        $this->woocommerce_wp_multi_checkbox([
            'id' => 'outofthebox_upload_box_active_on_status',
            'name' => 'outofthebox_upload_box_active_on_status[]',
            'label' => __(''
                    .'Show when Order is', 'woocommerce'),
            'options' => wc_get_order_statuses(),
            'value' => $outofthebox_upload_box_active_on_status,
        ]); ?>

            </div>
          </div>
        </div><?php
    }

    /**
     * New Multi Checkbox field for woocommerce backend.
     *
     * @param mixed $field
     */
    public function woocommerce_wp_multi_checkbox($field)
    {
        global $thepostid, $post;

        $thepostid = empty($thepostid) ? $post->ID : $thepostid;
        $field['class'] = isset($field['class']) ? $field['class'] : 'select short';
        $field['style'] = isset($field['style']) ? $field['style'] : '';
        $field['wrapper_class'] = isset($field['wrapper_class']) ? $field['wrapper_class'] : '';
        $field['value'] = isset($field['value']) ? $field['value'] : get_post_meta($thepostid, $field['id'], true);
        $field['cbvalue'] = isset($field['cbvalue']) ? $field['cbvalue'] : 'yes';
        $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];
        $field['desc_tip'] = isset($field['desc_tip']) ? $field['desc_tip'] : false;

        echo '<fieldset class="form-field '.esc_attr($field['id']).'_field '.esc_attr($field['wrapper_class']).'">
    <legend>'.wp_kses_post($field['label']).'</legend>';

        if (!empty($field['description']) && false !== $field['desc_tip']) {
            echo wc_help_tip($field['description']);
        }

        echo '<ul class="wc-radios">';

        foreach ($field['options'] as $key => $value) {
            echo '<li><label><input type="checkbox" class="'.esc_attr($field['class']).'" style="'.esc_attr($field['style']).'" name="'.esc_attr($field['name']).'" id="'.esc_attr($field['id']).'" value="'.esc_attr($key).'" '.(in_array($key, $field['value']) ? 'checked="checked"' : '').' /> '.esc_html($value).'</label></li>';
        }
        echo '</ul>';

        if (!empty($field['description']) && false === $field['desc_tip']) {
            echo '<span class="description">'.wp_kses_post($field['description']).'</span>';
        }

        echo '</fieldset>';
    }

    /**
     * Add the scripts and styles required for the new Data Tab.
     */
    public function add_product_data_tab_scripts_and_style()
    {
        ?>
        <style>
          #woocommerce-product-data ul.wc-tabs li.cloud-uploads-dropbox_options a:before { font-family: Dashicons; content: '\f176'; }
          .show_if_outofthebox_upload_box{
            background: #fff;
            border: 1px solid #e5e5e5;
            margin: 5px 15px 10px;
            padding: 1px 12px;
            position: relative;
            overflow: hidden;
          }
        </style>
        <script>
            jQuery(document).ready(function ($) {
              $('input#_uploadable').change(function () {
                var is_uploadable = $('input#_uploadable:checked').size();
                $('.show_if_uploadable').hide();
                $('.hide_if_uploadable').hide();
                if (is_uploadable) {
                  $('.hide_if_uploadable').hide();
                }
                if (is_uploadable) {
                  $('.show_if_uploadable').show();
                }
              });
              $('input#_uploadable').trigger('change');

              $('input#outofthebox_upload_box').change(function () {
                var outofthebox_upload_box = $('input#outofthebox_upload_box:checked').size();
                $('.show_if_outofthebox_upload_box').hide();
                if (outofthebox_upload_box) {
                  $('.show_if_outofthebox_upload_box').show();
                }
              });
              $('input#outofthebox_upload_box').trigger('change');

              /* Shortcode Generator Popup */
              $('.OutoftheBox-shortcodegenerator').click(function () {
                var shortcode = $("#outofthebox_upload_box_shortcode").val();
                shortcode = shortcode.replace('[outofthebox ', '').replace('"]', '');
                var query = encodeURIComponent(shortcode).split('%3D%22').join('=').split('%22%20').join('&');
                tb_show("Build Shortcode for Form", ajaxurl + '?action=outofthebox-getpopup&' + query + '&type=woocommerce&TB_iframe=true&height=600&width=800');
              });
            });
        </script>
        <?php
    }

    /**
     * Save the new added input fields properly.
     *
     * @param int $post_id
     */
    public function save_product_data_fields($post_id)
    {
        $is_uploadable = isset($_POST['_uploadable']) ? 'yes' : 'no';
        update_post_meta($post_id, '_uploadable', $is_uploadable);

        $outofthebox_upload_box = isset($_POST['outofthebox_upload_box']) ? 'yes' : 'no';
        update_post_meta($post_id, 'outofthebox_upload_box', $outofthebox_upload_box);

        if (isset($_POST['outofthebox_upload_box_title'])) {
            update_post_meta($post_id, 'outofthebox_upload_box_title', $_POST['outofthebox_upload_box_title']);
        }

        if (isset($_POST['outofthebox_upload_box_description'])) {
            update_post_meta($post_id, 'outofthebox_upload_box_description', $_POST['outofthebox_upload_box_description']);
        }

        if (isset($_POST['outofthebox_upload_box_shortcode'])) {
            update_post_meta($post_id, 'outofthebox_upload_box_shortcode', $_POST['outofthebox_upload_box_shortcode']);
        }

        if (isset($_POST['outofthebox_upload_box_folder_template'])) {
            update_post_meta($post_id, 'outofthebox_upload_box_folder_template', $_POST['outofthebox_upload_box_folder_template']);
        }

        if (isset($_POST['outofthebox_upload_box_active_on_status'])) {
            $post_data = $_POST['outofthebox_upload_box_active_on_status'];
            // Data sanitization
            $sanitize_data = [];
            if (is_array($post_data) && sizeof($post_data) > 0) {
                foreach ($post_data as $value) {
                    $sanitize_data[] = esc_attr($value);
                }
            }
            update_post_meta($post_id, 'outofthebox_upload_box_active_on_status', $sanitize_data);
        } else {
            update_post_meta($post_id, 'outofthebox_upload_box_active_on_status', ['wc-pending', 'wc-processing']);
        }
    }

    /**
     * Add an 'Upload' Action to the Order Table.
     *
     * @param array $actions
     *
     * @return array
     */
    public function add_orders_column_actions($actions, \WC_Order $order)
    {
        if ($this->requires_order_uploads($order)) {
            $actions['upload'] = [
                'url' => $order->get_view_order_url().'#uploads',
                'name' => __('Upload files', 'outofthebox'),
            ];
        }

        return $actions;
    }

    /**
     * Render the Upload Box on the Order View.
     *
     * @param int $order_id
     */
    public function render_upload_field($order_id)
    {
        /* Only render the upload form once
         * Preferably before the order table, but not all templates have this hook available */
        if (doing_action('woocommerce_order_details_before_order_table')) {
            remove_action('woocommerce_order_details_after_order_table', [&$this, 'render_upload_field'], 11);
        }

        if (doing_action('woocommerce_order_details_after_order_table')) {
            remove_action('woocommerce_order_details_before_order_table', [&$this, 'render_upload_field'], 11);
        }

        $order = new \WC_Order($order_id);

        foreach ($order->get_items() as $order_item) {
            $originial_product = $this->get_product($order_item);

            if (false === $this->requires_product_uploads($originial_product)) {
                continue;
            }

            /** Select the product that contains the information * */
            $meta_product = $originial_product;
            if ($this->is_product_variation($originial_product)) {
                $meta_product = wc_get_product($originial_product->get_parent_id());
            }

            $box_title = apply_filters('outofthebox_woocommerce_upload_box_title', get_post_meta($meta_product->get_id(), 'outofthebox_upload_box_title', true), $order, $originial_product, $this);
            $box_description = get_post_meta($meta_product->get_id(), 'outofthebox_upload_box_description', true);
            $shortcode = get_post_meta($meta_product->get_id(), 'outofthebox_upload_box_shortcode', true);
            $folder_template = get_post_meta($meta_product->get_id(), 'outofthebox_upload_box_folder_template', true);
            $upload_active_on = get_post_meta($meta_product->get_id(), 'outofthebox_upload_box_active_on_status', true);
            if (empty($upload_active_on)) {
                $upload_active_on = ['wc-pending', 'wc-processing'];
            }
            $upload_active = in_array('wc-'.$order->get_status(), $upload_active_on);

            $shortcode_params = shortcode_parse_atts($shortcode);
            $shortcode_params['userfoldernametemplate'] = $this->set_placeholders($folder_template, $order, $originial_product);
            $shortcode_params['wc_order_id'] = $order->get_id();
            $shortcode_params['wc_product_id'] = $originial_product->get_id();

            // When Upload box isn't active, change it to a view only file browser
            if (false === $upload_active) {
                $shortcode_params['mode'] = 'files';
                $shortcode_params['upload'] = '0';
                $shortcode_params['delete'] = '0';
                $shortcode_params['rename'] = '0';
                $shortcode_params['candownloadzip'] = '1';
                $shortcode_params['editdescription'] = '0';
            }

            $show_box = apply_filters('outofthebox_woocommerce_show_upload_field', true, $order, $originial_product, $this);

            if ($show_box) {
                echo '<div class="woocommerce-order-upload-box">';

                do_action('outofthebox_woocommerce_before_render_upload_field', [$order, $originial_product, $this]);

                echo '<h2 id="uploads">'.$this->set_placeholders($box_title, $order, $originial_product).'</h2>';

                if (!empty($box_description)) {
                    echo '<p>'.$box_description.'</p>';
                }
                // Don't show the upload box when there isn't select a root folder
                // IN THE DROPBOX VERSION DIR CAN BE EMPTY IN CASE THE ROOT FOLDER IS BEING USED
                //if (empty($shortcode_params['dir']) && $shortcode_params['userfolder'] !== 'manual') {
                //    echo sprintf(__('Please %sconfigure%s the upload location for this product', 'outofthebox'), '', '') . '.';
                //    continue;
                //}

                echo $this->get_woocommerce()->get_processor()->create_from_shortcode($shortcode_params);

                do_action('outofthebox_woocommerce_before_render_upload_field', [$order, $originial_product, $this]);
                echo '</div>';
            }
        }
    }

    /**
     * Render the Meta Box.
     */
    public function render_meta_box(\WP_Post $post)
    {
        $order = new \WC_Order($post->ID);

        foreach ($order->get_items() as $order_item) {
            $originial_product = $this->get_product($order_item);

            if (false === $this->requires_product_uploads($originial_product)) {
                continue;
            }

            /** Select the product that contains the information * */
            $meta_product = $originial_product;
            if ($this->is_product_variation($originial_product)) {
                $meta_product = wc_get_product($originial_product->get_parent_id());
            }

            $shortcode = get_post_meta($meta_product->get_id(), 'outofthebox_upload_box_shortcode', true);
            $folder_template = get_post_meta($meta_product->get_id(), 'outofthebox_upload_box_folder_template', true);

            $shortcode_params = shortcode_parse_atts($shortcode);
            $shortcode_params['userfoldernametemplate'] = $this->set_placeholders($folder_template, $order, $originial_product);
            $shortcode_params['wc_order_id'] = $order->get_id();
            $shortcode_params['wc_product_id'] = $originial_product->get_id();

            // Always show the File Browser mode in the Dashboard
            $shortcode_params['mode'] = 'files';
            $shortcode_params['candownloadzip'] = '1';

            // Meta Box is located inside Form tag, so force the plugin to start the update
            $shortcode_params['class'] = (isset($shortcode_params['class']) ? $shortcode_params['class'].' auto_upload' : 'auto_upload');

            // Don't show the upload box when there isn't select a root folder
            if (empty($shortcode_params['dir']) && 'manual' !== $shortcode_params['userfolder']) {
                $product_url = admin_url('post.php?post='.$originial_product->get_id().'&action=edit');
                echo sprintf(__('Please %sconfigure%s the upload location for this product', 'outofthebox'), '<a href="'.$product_url.'">', '</a>').'.';

                continue;
            }

            echo $this->get_woocommerce()->get_processor()->create_from_shortcode($shortcode_params);
        }
    }

    /**
     * Checks if the order uses this upload functionality.
     *
     * @param \WC_Order $order
     *
     * @return boolean
     */
    public function requires_order_uploads($order)
    {
        if (false === ($order instanceof \WC_Order)) {
            return false;
        }

        foreach ($order->get_items() as $order_item) {
            $product = $this->get_product($order_item);
            $requires_upload = $this->requires_product_uploads($product);

            if ($requires_upload) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the product uses this upload functionality.
     *
     * @param \WC_Product $product
     *
     * @return boolean
     */
    public function requires_product_uploads($product = null)
    {
        if (empty($product) || !($product instanceof \WC_Product)) {
            return false;
        }

        if ($this->is_product_variation($product)) {
            $product = wc_get_product($product->get_parent_id());
        }

        $_uploadable = get_post_meta($product->get_id(), '_uploadable', true);
        $_outofthebox_upload_box = get_post_meta($product->get_id(), 'outofthebox_upload_box', true);

        if ('yes' === $_uploadable && 'yes' === $_outofthebox_upload_box) {
            return true;
        }

        return false;
    }

    /**
     * Loads the product or its parent product in case of a variation.
     *
     * @param type $order_item
     *
     * @return \WC_Product
     */
    public function get_product($order_item)
    {
        $product = $order_item->get_product();

        if (empty($product) || !($product instanceof \WC_Product)) {
            return false;
        }

        return $product;
    }

    /**
     * Check if product is a variation
     * Upload meta data is currently only stored on the parent product.
     *
     * @param $product
     *
     * @return boolean
     */
    public function is_product_variation($product)
    {
        $product_type = $product->get_type();

        return 'variation' === $product_type;
    }

    /**
     * Fill the placeholders with the User/Product/Order information.
     *
     * @param string $template
     *
     * @return string
     */
    public function set_placeholders($template, \WC_Order $order, \WC_Product $product)
    {
        $user = $order->get_user();

        // Guest User
        if (false === $user) {
            $user_id = $order->get_order_key();
            $user = new \stdClass();
            $user->user_login = $order->get_billing_first_name().' '.$order->get_billing_last_name();
            $user->display_name = $order->get_billing_first_name().' '.$order->get_billing_last_name();
            $user->user_firstname = $order->get_billing_first_name();
            $user->user_lastname = $order->get_billing_last_name();
            $user->user_email = $order->get_billing_email();
            $user->ID = $user_id;
            $user->user_role = __('Anonymous user', 'outofthebox');
        }

        $product_quantity = 0;
        foreach ($order->get_items() as $item_id => $item_product) {
            if ($item_product->get_product_id() == $product->get_id()) {
                $product_quantity = $order->get_item_count($item_product->get_type());
            }
        }

        $output = strtr($template, [
            '%wc_order_id%' => $order->get_order_number(),
            '%wc_order_quantity%' => $order->get_item_count(),
            '%wc_product_id%' => $product->get_id(),
            '%wc_product_sku%' => $product->get_sku(),
            '%wc_product_name%' => $product->get_name(),
            '%wc_product_quantity%' => $product_quantity,
            '%user_login%' => isset($user->user_login) ? $user->user_login : '',
            '%user_email%' => isset($user->user_email) ? $user->user_email : '',
            '%user_firstname%' => isset($user->user_firstname) ? $user->user_firstname : '',
            '%user_lastname%' => isset($user->user_lastname) ? $user->user_lastname : '',
            '%display_name%' => isset($user->display_name) ? $user->display_name : '',
            '%ID%' => isset($user->ID) ? $user->ID : '',
            '%user_role%' => isset($user->roles) ? implode(',', $user->roles) : '',
            '%jjjj-mm-dd%' => date('Y-m-d'),
        ]);

        return apply_filters('outofthebox_woocommerce_set_placeholders', $output, $template, $order, $product);
    }

    /**
     * @return \TheLion\OutoftheBox\WooCommerce
     */
    public function get_woocommerce()
    {
        return $this->_woocommerce;
    }
}
