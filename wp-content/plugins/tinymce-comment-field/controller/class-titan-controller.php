<?php

class TMCECF_TitanController {

    private function __construct() {
        //add_action('tf_admin_page_start', array(&$this, "buy_me_a_coffee"));
        add_action('tf_create_options', array(&$this, 'create_options'));
        add_action('tf_admin_options_saved_tinymce-comment-field', array(&$this, 'save_editor_content_css'));
    }

    public static function &init() {
        static $instance = false;
        if (!$instance) {
            $instance = new self();
        }
        return $instance;
    }

    public function buy_me_a_coffee() {
        ?>
        <div id="setting-error-tgmpa" class="notice-success notice is-dismissible">
            <p><strong></strong></p>

            <p><strong>Thanks for using TinyMCE Comment Field: <em><a
                            href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=B2WSC5FR2L8MU"
                            title="Buy me a coffee">Buy me a coffee</a></em>.</strong></p>

            <p></p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span>
            </button>
        </div>
        <?php
    }

    /**
     *
     */
    public function create_options() {

        global $pagenow;
        $admin_page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRIPPED);

        //workarround for titan framework bug
        $is_settings_page = $admin_page === 'tinymce-comment-field' && $pagenow === 'admin.php';

        $titan = TitanFramework::getInstance('tinymce-comment-field');

        $panel = $titan->createAdminPage(array('name' => 'TinyMCE Comment Field', 'icon' => 'dashicons-edit'));

        $general_tab = $panel->createTab(array('name' => __('General', 'tinymce-comment-field')));

        $general_tab->createOption(array('name' => 'Enabled', 'id' => 'enabled', 'type' => 'checkbox',
            'desc' => __('Enable or Disable TinyMCE Comment Field', 'tinymce-comment-field'),
            'default' => true,));

        $general_tab->createOption(array('name' => 'Mobile Browser Support', 'id' => 'mobile-browser-support',
            'type' => 'checkbox',
            'desc' => __('Enable or Disable TinyMCE Comment Field on Mobile Devices', 'tinymce-comment-field'),
            'default' => true,));

        $post_types = get_post_types(array('public' => true));
        $titan_post_types = array();

        foreach ($post_types as $post_type):
            $titan_post_type = get_post_type_object($post_type);
            $titan_post_types[$post_type] = $titan_post_type->labels->name;
        endforeach;

        $general_tab->createOption(array('name' => __('Editor Font', 'tinymce-comment-field'), 'id' => 'editor-font',
            'type' => 'font',
            'desc' => 'Enable or Disable TinyMCE Comment Field on certain Post Types ',
            'show_font_family' => true, 'enqueue' => true, 'show_text_shadow' => false,
            'default' => array('font-family' => 'Georgia, serif')));

        $general_tab->createOption(array('name' => __('Background Color', 'tinymce-comment-field'),
            'id' => 'background-color', 'type' => 'color',
            'desc' => __('Pick a color', 'tinymce-comment-field'),
            'default' => '#ffffff',));

        if($is_settings_page) {
            $general_tab->createOption(array('name' => _('Height'), 'id' => 'height', 'type' => 'number', 'desc' => '',
                'default' => '200', 'min' => '100', 'max' => '1000', 'unit' => 'px'));
        } else {
            $general_tab->createOption(array('name' => _('Height'), 'id' => 'height', 'type' => 'text', 'desc' => '',
                'default' => '200'));
        }



        $general_tab->createOption(array('name' => __('Post Types', 'tinymce-comment-field'), 'id' => 'post-types',
            'type' => 'multicheck',
            'desc' => __('Enable or Disable TinyMCE Comment Field on certain Post Types', 'tinymce-comment-field'),
            'options' => $titan_post_types, 'default' => array('post', 'page'),));

        $general_tab->createOption(array('name' => __('Text Direction', 'tinymce-comment-field'),
            'id' => 'text-direction',
            'options' => array('ltr' => __('Left to Right', 'tinymce-comment-field'),
                'rtl' => __('Right to Left', 'tinymce-comment-field'),),
            'type' => 'radio',
            'desc' => __('Set the Text Direction', 'tinymce-comment-field'),
            'default' => 'ltr',));

        $general_tab->createOption(array('name' => 'Auto-Embed', 'id' => 'autoembed', 'type' => 'checkbox',
            'desc' => __('Auto embedding of videos and other stuff', 'tinymce-comment-field'),
            'default' => true,));

        $general_tab->createOption(array('name' => __('Text below Comment Field', 'tinymce-comment-field'),
            'id' => 'text-below-commentfield', 'type' => 'editor',
            'desc' => __('Put your text or html here', 'tinymce-comment-field'),));

        $general_tab->createOption(array('type' => 'save',));

        $buttons_tab = $panel->createTab(array('name' => 'Buttons'));

        $buttons_tab->createOption(array('name' => __('Buttons', 'tinymce-comment-field'), 'id' => 'buttons',
            'type' => 'multicheck',
            'desc' => __('Enable or Disable Buttons on the Toolbar', 'tinymce-comment-field'),
            'options' => TMCECF_Buttons::getTeeny(),
            'default' => array('bold', 'italic', 'underline', 'strikethrough', 'cut',
                'copy', 'paste', 'blockquote', 'link', 'unlink'),));

        $buttons_tab->createOption(array('type' => 'save',));

        $shortcode_tab = $panel->createTab(array('name' => 'Shortcodes'));

        global $shortcode_tags;
        $options_allowed_shortcodes = array();
        foreach ($shortcode_tags as $shortcode_tag => $value):

            if (preg_match('/(wp_caption|caption|gallery)/i', $shortcode_tag) === 1) {
                continue;
            }

            $options_allowed_shortcodes[$shortcode_tag] = $shortcode_tag;
        endforeach;

        $shortcode_tab->createOption(array('name' => __('Allowed Shortcodes', 'tinymce-comment-field'),
            'id' => 'allowed-shortcodes', 'type' => 'multicheck',
            'desc' => __('Enable or Disable Shortcodes for the Comment Field', 'tinymce-comment-field'),
            'options' => $options_allowed_shortcodes,
            'default' => array('caption', 'wp_caption'),));

        $shortcode_tab->createOption(array('type' => 'save',));

        $image_tab = $panel->createTab(array('name' => 'Images'));

        $image_tab->createOption(array('name' => __('Allow vistors or users posting images', 'tinymce-comment-field'),
            'id' => 'allow_images_as_tag', 'type' => 'checkbox', 'desc' => '&nbsp;',
            'default' => false));
        global $wp_roles;

        $all_roles = $wp_roles->roles;
        $filteredhtml_roles = array();
        $default_roles = array('unregistered');
        $filteredhtml_roles['unregistered'] = __('Unregistered / Visitor', 'tinymce-comment-field');

        while ($role = current($all_roles)) {
            $wp_role = get_role(key($all_roles));

            if (!$wp_role->has_cap('unfiltered_html')) {
                $filteredhtml_roles[$wp_role->name] = $role['name'];
                $default_roles[] = $wp_role->name;
            } else {
                $unfilteredhtml_roles[$wp_role->name] = $role['name'];
            }

            next($all_roles);
        }

        $image_tab->createOption(array('name' => __('Allow posting images only when visitors or users are in certain roles', 'tinymce-comment-field'),
            'id' => 'allow_images_as_tag_roles', 'type' => 'multicheck', '&nbsp;',
            'options' => $filteredhtml_roles, 'default' => $default_roles));

        $image_tab->createOption(array('name' => __('Always moderate comments with images', 'tinymce-comment-field'),
            'id' => 'moderate_comments_with_images', 'type' => 'checkbox',
            'desc' => '&nbsp;', 'default' => true,));

        $image_tab->createOption(array('name' => __('Hyperlink images', 'tinymce-comment-field'),
            'id' => 'hyperlink_images_as_tag', 'type' => 'checkbox',
            'desc' => '&nbsp;', 'default' => true,));


        if($is_settings_page) {
            $image_tab->createOption(array('name' => __('Max Width', 'tinymce-comment-field'), 'id' => 'max_image_width',
                'type' => 'number',
                'desc' => __('Larger images will be resized proportionally', 'tinymce-comment-field'),
                'default' => '200', 'min' => '100', 'max' => '1000', 'unit' => 'px'));

            $image_tab->createOption(array('name' => __('Max Height', 'tinymce-comment-field'), 'id' => 'max_image_height',
                'type' => 'number',
                'desc' => __('Larger images will be resized proportionally', 'tinymce-comment-field'),
                'default' => '200', 'min' => '100', 'max' => '1000', 'unit' => 'px'));
        } else {
            $image_tab->createOption(array('name' => __('Max Width', 'tinymce-comment-field'), 'id' => 'max_image_width',
                'type' => 'text',
                'desc' => __('Larger images will be resized proportionally', 'tinymce-comment-field'),
                'default' => '200', 'min' => '100', 'max' => '1000', 'unit' => 'px'));

            $image_tab->createOption(array('name' => __('Max Height', 'tinymce-comment-field'), 'id' => 'max_image_height',
                'type' => 'text',
                'desc' => __('Larger images will be resized proportionally', 'tinymce-comment-field'),
                'default' => '200', 'min' => '100', 'max' => '1000', 'unit' => 'px'));
        }




        $image_tab->createOption(array('type' => 'save',));

        $advanced_tab = $panel->createTab(array('name' => __('Advanced', 'tinymce-comment-field')));

        $advanced_tab->createOption(array(
            'name' => __('Custom CSS', 'tinymce-comment-field'),
            'id' => 'custom-css',
            'type' => 'textarea',
            'desc' => 'Custom CSS'
        ));

        $advanced_tab->createOption(array('type' => 'save',));
    }

    public static function save_editor_content_css() {

        $css_url_dynamic = site_url() . '/?mcec_action=comment_editor_content_css&t=' . time();

        $query = http_build_query(array('mcec_action'=> 'comment_editor_content_css', 't'=>time()));

        $css_url_dynamic = site_url() . '?' . $query;

        try {
            $wp_upload_dir = wp_upload_dir();
            $css_filename = 'tinymce-comment-field-editor.css';
            $css_base_path = $wp_upload_dir['basedir'];
            $css_base_url = $wp_upload_dir['baseurl'];
            $css_url = $css_base_url . '/' . $css_filename;
            $css_path = $css_base_path . DIRECTORY_SEPARATOR . $css_filename;
            $css_request = wp_remote_get($css_url_dynamic);

            if(is_a($css_request, 'WP_Error')) {
                throw new ErrorException($css_request->get_error_message());
            }

            $css_content = $css_request['body'];
            $result = file_put_contents($css_path, $css_content);
            $css_url_dynamic = set_url_scheme($css_url_dynamic);
            $css_url = set_url_scheme($css_url);

            if ($result === false):
                update_option('tinymce-comment-field_css-url', $css_url_dynamic);
            else:
                update_option('tinymce-comment-field_css-url', $css_url . '?t=' . time());
            endif;
            update_option('tinymce-comment-field_css-path', $css_path);
        } catch (Exception $ex) {
            update_option('tinymce-comment-field_css-url', $css_url_dynamic);
        }
    }

}
