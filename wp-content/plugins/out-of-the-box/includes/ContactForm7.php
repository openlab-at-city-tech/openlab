<?php

namespace TheLion\OutoftheBox;

class ContactFormAddon
{
    /**
     * @var \TheLion\OutoftheBox\Main
     */
    private $_main;

    public function __construct(Main $_main)
    {
        $this->_main = $_main;

        add_action('wpcf7_init', [&$this, 'add_shortcode_handler']);
        add_action('wpcf7_admin_init', [&$this, 'add_tag_generator'], 60);

        add_action('admin_enqueue_scripts', [&$this, 'add_admin_scripts']);
        add_action('wpcf7_admin_footer', [&$this, 'load_admin_scripts']);
        add_action('wpcf7_enqueue_scripts', [&$this, 'add_front_end_scripts']);

        add_filter('wpcf7_mail_tag_replaced_outofthebox*', [&$this, 'set_email_tag'], 999, 4);
        add_filter('wpcf7_mail_tag_replaced_outofthebox', [&$this, 'set_email_tag'], 999, 4);

        add_filter('outofthebox_private_folder_name', [&$this, 'new_private_folder_name'], 10, 2);
        add_filter('outofthebox_private_folder_name_guests', [&$this, 'rename_private_folder_names_for_guests'], 10, 2);
    }

    public function add_admin_scripts($hook_suffix)
    {
        if (false === strpos($hook_suffix, 'wpcf7')) {
            return;
        }

        $this->get_main()->load_scripts();
        $this->get_main()->load_styles();

        wp_enqueue_script('WPCloudplugin.Libraries');
        wp_enqueue_script('OutoftheBox.tinymce');
        wp_enqueue_style('Awesome-Font-5-css');
        wp_enqueue_style('OutoftheBox.tinymce');
    }

    public function add_front_end_scripts()
    {
        wp_register_script('OutoftheBox.ContactForm7', plugins_url('js/ContactForm7.js', __FILE__), ['jquery'], OUTOFTHEBOX_VERSION, true);
    }

    public function load_admin_scripts()
    {
        $this->add_front_end_scripts();
        wp_enqueue_script('OutoftheBox.ContactForm7');
    }

    public function add_tag_generator()
    {
        if (class_exists('WPCF7_TagGenerator')) {
            $tag_generator = \WPCF7_TagGenerator::get_instance();
            $tag_generator->add('outofthebox', 'Out-of-the-Box', [$this, 'tag_generator_body']);
        }
    }

    public function tag_generator_body($contact_form, $args = '')
    {
        $args = wp_parse_args($args, []);
        $type = 'outofthebox';

        $description = __('Generate a form-tag for a Out-of-the-Box Upload field.', 'contact-form-7'); ?>
        <div class="control-box">
          <fieldset>
            <legend><?php echo sprintf(esc_html($description)); ?></legend>
            <table class="form-table">
              <tbody>
                <tr>
                  <th scope="row"><?php echo esc_html(__('Field type', 'contact-form-7')); ?></th>
                  <td>
                    <fieldset>
                      <legend class="screen-reader-text"><?php echo esc_html(__('Field type', 'contact-form-7')); ?></legend>
                      <label><input type="checkbox" name="required" /> <?php echo esc_html(__('Required field', 'contact-form-7')); ?></label>
                    </fieldset>
                  </td>
                </tr>

                <tr>
                  <th scope="row"><label for="<?php echo esc_attr($args['content'].'-name'); ?>"><?php echo esc_html(__('Name', 'contact-form-7')); ?></label></th>
                  <td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr($args['content'].'-name'); ?>" /></td>
                </tr>

                <tr>
                  <th scope="row"><label for="<?php echo esc_attr($args['content'].'-shortcode'); ?>"><?php echo esc_html(__('Out-of-the-Box Shortcode', 'outofthebox')); ?></label></th>
                  <td>
                    <input type="hidden" name="shortcode" class="outofthebox-shortcode-value large-text option" id="<?php echo esc_attr($args['content'].'-shortcode'); ?>" />
                    <code id="outofthebox-shortcode-decoded-value" style="margin-bottom:15px;display:none;width: 400px;word-wrap: break-word;"></code>
                    <input type="button" class="button button-primary OutoftheBox-CF-shortcodegenerator " value="<?php echo esc_attr(__('Build your Out-of-the-Box shortcode', 'outofthebox')); ?>" />
                    <iframe id="outofthebox-shortcode-iframe" src="about:blank" data-src='<?php echo OUTOFTHEBOX_ADMIN_URL; ?>?action=outofthebox-getpopup&type=contactforms7' width='100%' height='500' tabindex='-1' frameborder='0' style="display:none"></iframe>
                    <p class="out-of-the-box-upload-folder description">You can use the available input fields in your form to name the upload folder based on user input. To do so, just add the <code>outofthebox_private_folder_name</code> to the Class attribute of your input field (i.e. <code>[text* your-name class:outofthebox_private_folder_name]</code>).</p>
                  </td>
                </tr>

              </tbody>
            </table>
          </fieldset>
        </div>

        <div class="insert-box">
          <input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

          <div class="submitbox">
            <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr(__('Insert Tag', 'contact-form-7')); ?>" />
          </div>

          <br class="clear" />

          <p class="description mail-tag"><label for="<?php echo esc_attr($args['content'].'-mailtag'); ?>"><?php echo sprintf(esc_html('To list the uploads in your email, insert the mail-tag (%s) in the Mail tab.'), '<strong><span class="mail-tag"></span></strong>'); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr($args['content'].'-mailtag'); ?>" /></label></p>
        </div>
        <?php
    }

    /**
     * Add shortcode handler to CF7.
     */
    public function add_shortcode_handler()
    {
        if (function_exists('wpcf7_add_form_tag')) {
            wpcf7_add_form_tag(
                ['outofthebox', 'outofthebox*'],
                [$this, 'shortcode_handler'],
                true
            );
        }
    }

    public function shortcode_handler($tag)
    {
        $tag = new \WPCF7_FormTag($tag);

        if (empty($tag->name)) {
            return '';
        }

        $required = ('*' == substr($tag->type, -1));
        if ($required) {
            add_filter('outofthebox_shortcode_set_options', [&$this, 'set_required_shortcode'], 10, 3);
        }

        // Shortcode
        $shortcode = base64_decode(urldecode($tag->get_option('shortcode', '', true)));
        $return = apply_filters('outofthebox-wpcf7-render-shortcode', do_shortcode($shortcode));
        $return .= "<input type='hidden' name='".$tag->name."' class='fileupload-filelist fileupload-input-filelist'/>";

        wp_enqueue_script('OutoftheBox.ContactForm7');

        return $return;
    }

    public function set_required_shortcode($options, $processor, $atts)
    {
        $options['class'] .= ' wpcf7-validates-as-required';

        return $options;
    }

    public function set_email_tag($output, $submission, $ashtml, $mail_tag)
    {
        $filelist = stripslashes($submission);

        return $this->render_uploaded_files_list($filelist, $ashtml);
    }

    public function render_uploaded_files_list($data, $ashtml = true)
    {
        $uploadedfiles = json_decode($data);

        if ((null !== $uploadedfiles) && (count((array) $uploadedfiles) > 0)) {
            $first_entry = current($uploadedfiles);
            $folder_location = ($ashtml && isset($first_entry->folderurl)) ? '<a href="'.urldecode($first_entry->folderurl).'">Dropbox</a>' : 'Dropbox';

            // Fill our custom field with the details of our upload session
            $html = sprintf(__('%d file(s) uploaded to %s:', 'outofthebox'), count((array) $uploadedfiles), $folder_location);
            $html .= ($ashtml) ? '<ul>' : "\r\n";

            foreach ($uploadedfiles as $fileid => $file) {
                $html .= ($ashtml) ? '<li><a href="'.$file->link.'">' : '';
                $html .= $file->path;
                $html .= ($ashtml) ? '</a>' : '';
                $html .= ' ('.$file->size.')';
                $html .= ($ashtml) ? '</li>' : "\r\n";
            }

            $html .= ($ashtml) ? '</ul>' : '';
        } else {
            return $data;
        }

        return $html;
    }

    /**
     * Function to change the Private Folder Name.
     *
     * @param string                         $private_folder_name
     * @param \TheLion\OutoftheBox\Processor $outofthebox_processor
     *
     * @return string
     */
    public function new_private_folder_name($private_folder_name, $outofthebox_processor)
    {
        if (!isset($_COOKIE['OftB-CF7-NAME'])) {
            return $private_folder_name;
        }

        if ('cf7_upload_box' !== $outofthebox_processor->get_shortcode_option('class')) {
            return $private_folder_name;
        }

        return trim(str_replace(['|', '/'], ' ', sanitize_text_field($_COOKIE['OftB-CF7-NAME'])));
    }

    /**
     * Function to change the Private Folder Name for Guest users.
     *
     * @param string                         $private_folder_name_guest
     * @param \TheLion\OutoftheBox\Processor $outofthebox_processor
     *
     * @return string
     */
    public function rename_private_folder_names_for_guests($private_folder_name_guest, $outofthebox_processor)
    {
        if ('cf7_upload_box' !== $outofthebox_processor->get_shortcode_option('class')) {
            return $private_folder_name_guest;
        }

        return str_replace(__('Guests', 'outofthebox').' - ', '', $private_folder_name_guest);
    }

    /**
     * @return \TheLion\OutoftheBox\Main
     */
    public function get_main()
    {
        return $this->_main;
    }
}

$CF7OutoftheBoxAddOn = new ContactFormAddon($this);
