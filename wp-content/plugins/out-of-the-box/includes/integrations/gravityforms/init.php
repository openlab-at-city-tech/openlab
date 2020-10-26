<?php
GFForms::include_addon_framework();

class GFOutoftheBoxAddOn extends GFAddOn
{
    protected $_version = '1.0';
    protected $_min_gravityforms_version = '1.9';
    protected $_slug = 'outoftheboxaddon';
    protected $_path = 'out-of-the-box/includes/integrations/init.php';
    protected $_full_path = __FILE__;
    protected $_title = 'Gravity Forms Out-of-the-Box Add-On';
    protected $_short_title = 'Out-of-the-Box Add-On';

    public function init()
    {
        parent::init();

        if (isset($this->_min_gravityforms_version) && !$this->is_gravityforms_supported($this->_min_gravityforms_version)) {
            return;
        }

        // Add a Out-of-the-Box button to the advanced to the field editor
        add_filter('gform_add_field_buttons', [$this, 'outofthebox_field']);
        add_filter('admin_enqueue_scripts', [$this, 'outofthebox_extra_scripts']);

        // Now we execute some javascript technicalitites for the field to load correctly
        add_action('gform_editor_js', [$this, 'gform_editor_js']);
        add_filter('gform_field_input', [$this, 'outofthebox_input'], 10, 5);

        // Add a custom setting to the field
        add_action('gform_field_standard_settings', [$this, 'outofthebox_settings'], 10, 2);

        // Adds title to the custom field
        add_filter('gform_field_type_title', [$this, 'outofthebox_title'], 10, 2);

        // Filter to add the tooltip for the field
        add_filter('gform_tooltips', [$this, 'add_outofthebox_tooltips']);

        // Save some data for this field
        add_filter('gform_field_validation', [$this, 'outofthebox_validation'], 10, 4);

        // Display values in a proper way
        add_filter('gform_entry_field_value', [$this, 'outofthebox_entry_field_value'], 10, 4);
        add_filter('gform_entries_field_value', [$this, 'outofthebox_entries_field_value'], 10, 4);
        add_filter('gform_merge_tag_filter', [$this, 'outofthebox_merge_tag_filter'], 10, 5);

        // Add support for wpDataTables <> Gravity Form integration
        if (class_exists('WPDataTable')) {
            add_action('wpdatatables_before_get_table_metadata', [$this, 'render_wpdatatables_field'], 10, 1);
        }
        // Custom Private Folder names
        add_filter('outofthebox_private_folder_name', [&$this, 'new_private_folder_name'], 10, 2);
        add_filter('outofthebox_private_folder_name_guests', [&$this, 'rename_private_folder_names_for_guests'], 10, 2);
    }

    public function outofthebox_extra_scripts()
    {
        if (GFForms::is_gravity_page()) {
            add_thickbox();
        }

        wp_enqueue_style('WPCP-GravityForms', plugins_url('style.css', __FILE__));
    }

    public function outofthebox_field($field_groups)
    {
        foreach ($field_groups as &$group) {
            if ('advanced_fields' == $group['name']) {
                $group['fields'][] = [
                    'class' => 'button',
                    'value' => 'Out-of-the-Box',
                    'date-type' => 'outofthebox',
                    'onclick' => "StartAddField('outofthebox');",
                ];

                break;
            }
        }

        return $field_groups;
    }

    public function gform_editor_js()
    {
        ?>
            <script type='text/javascript'>
                jQuery(document).ready(function ($) {
                  /* Which settings field should be visible for our custom field*/
                  fieldSettings["outofthebox"] = ".label_setting, .description_setting, .admin_label_setting, .error_message_setting, .css_class_setting, .visibility_setting, .rules_setting, .label_placement_setting, .outofthebox_setting, .conditional_logic_field_setting, .conditional_logic_page_setting, .conditional_logic_nextbutton_setting"; //this will show all the fields of the Paragraph Text field minus a couple that I didn't want to appear.

                  /* binding to the load field settings event to initialize */
                  $(document).bind("gform_load_field_settings", function (event, field, form) {
                    if (field["OutoftheBoxShortcode"] !== undefined && field["OutoftheBoxShortcode"] !== '') {
                      jQuery("#field_outofthebox").val(field["OutoftheBoxShortcode"]);
                    } else {
                      /* Default value */
                      var defaultvalue = '[outofthebox class="gf_upload_box" mode="upload" upload="1" uploadrole="all" upload_auto_start="0" userfolders="auto" viewuserfoldersrole="none"]';
                      jQuery("#field_outofthebox").val(defaultvalue);
                    }
                  });

                  /* Shortcode Generator Popup */
                  $('.OutoftheBox-GF-shortcodegenerator').click(function () {
                    var shortcode = jQuery("#field_outofthebox").val();
                    shortcode = shortcode.replace('[outofthebox ', '').replace('"]', '');
                    var query = encodeURIComponent(shortcode).split('%3D%22').join('=').split('%22%20').join('&');
                    tb_show("Build Shortcode for Form", ajaxurl + '?action=outofthebox-getpopup&' + query + '&type=shortcodebuilder&asuploadbox=1&callback=wpcp_oftb_gf_add_content&TB_iframe=true&height=600&width=800');
                  });

                    /* Callback function to add shortcode to GF field */
                    if (typeof window.wpcp_oftb_gf_add_content === 'undefined') {
                        window.wpcp_oftb_gf_add_content = function (data) {
                            $('#field_outofthebox').val(data);
                            SetFieldProperty('OutoftheBoxShortcode', data);

                            tb_remove();
                        }
                    }
                });

                function SetDefaultValues_outofthebox(field) {
                  field.label = '<?php _e('Upload your Files', 'wpcloudplugins'); ?>';
                }
            </script>
            <?php
    }

    public function outofthebox_input($input, $field, $value, $lead_id, $form_id)
    {
        if ('outofthebox' == $field->type) {
            if (!$this->is_form_editor()) {
                $return = do_shortcode($field->OutoftheBoxShortcode);
                $return .= "<input type='hidden' name='input_".$field->id."' id='input_".$form_id.'_'.$field->id."'  class='fileupload-filelist fileupload-input-filelist' value='".(isset($_REQUEST['input_'.$field->id]) ? stripslashes($_REQUEST['input_'.$field->id]) : '')."'>";

                return $return;
            }

            return '<div class="wpcp-wpforms-placeholder"></div>';
        }

        return $input;
    }

    public function outofthebox_settings($position, $form_id)
    {
        if (1430 == $position) {
            ?>
                <li class="outofthebox_setting field_setting">
                  <label for="field_outofthebox">Out-of-the-Box Shortcode <?php echo gform_tooltip('form_field_outofthebox'); ?></label>
                  <a href="#" class='button-primary OutoftheBox-GF-shortcodegenerator '><?php _e('Build your shortcode', 'wpcloudplugins'); ?></a>
                  <textarea id="field_outofthebox" class="fieldwidth-3 fieldheight-2" onchange="SetFieldProperty('OutoftheBoxShortcode', this.value)"></textarea>
                  <br/><small>Missing a Out-of-the-Box Gravity Form feature? Please let me <a href="https://florisdeleeuwnl.zendesk.com/hc/en-us/requests/new" target="_blank">know</a>!</small>
                </li>
                <?php
        }
    }

    public function outofthebox_title($title, $field_type)
    {
        if ('outofthebox' === $field_type) {
            return 'Out-of-the-Box '.__('Upload', 'wpcloudplugins');
        }

        return $title;
    }

    public function add_outofthebox_tooltips($tooltips)
    {
        $tooltips['form_field_outofthebox'] = '<h6>Out-of-the-Box Shortcode</h6>'.__('Build your shortcode here', 'wpcloudplugins');

        return $tooltips;
    }

    public function outofthebox_validation($result, $value, $form, $field)
    {
        if ('outofthebox' !== $field->type) {
            return $result;
        }

        if (false === $field->isRequired) {
            return $result;
        }

        // Get information uploaded files from hidden input
        $filesinput = rgpost('input_'.$field->id);
        $uploadedfiles = json_decode($filesinput);

        if (empty($uploadedfiles)) {
            $result['is_valid'] = false;
            $result['message'] = __('This field is required. Please upload your files.', 'gravityforms');
        } else {
            $result['is_valid'] = true;
            $result['message'] = '';
        }

        return $result;
    }

    public function outofthebox_entry_field_value($value, $field, $lead, $form)
    {
        if ('outofthebox' !== $field->type) {
            return $value;
        }

        return $this->renderUploadedFiles(html_entity_decode($value));
    }

    public function render_wpdatatables_field($tableId)
    {
        add_filter('gform_get_input_value', [$this, 'outofthebox_get_input_value'], 10, 4);
    }

    public function outofthebox_get_input_value($value, $entry, $field, $input_id)
    {
        if ('outofthebox' !== $field->type) {
            return $value;
        }

        return $this->renderUploadedFiles(html_entity_decode($value));
    }

    public function outofthebox_entries_field_value($value, $form_id, $field_id, $entry)
    {
        $form = GFFormsModel::get_form_meta($form_id);

        if (is_array($form['fields'])) {
            foreach ($form['fields'] as $field) {
                if ('outofthebox' === $field->type && $field_id == $field->id) {
                    if (!empty($value)) {
                        return $this->renderUploadedFiles(html_entity_decode($value));
                    }
                }
            }
        }

        return $value;
    }

    public function outofthebox_set_export_values($value, $form_id, $field_id, $lead)
    {
        $form = GFFormsModel::get_form_meta($form_id);

        if (is_array($form['fields'])) {
            foreach ($form['fields'] as $field) {
                if ('outofthebox' === $field->type && $field_id == $field->id) {
                    return $this->renderUploadedFiles(html_entity_decode($value), false);
                }
            }
        }

        return $value;
    }

    public function outofthebox_merge_tag_filter($value, $merge_tag, $modifier, $field, $rawvalue)
    {
        if ('outofthebox' == $field->type) {
            return $this->renderUploadedFiles(html_entity_decode($value));
        }

        return $value;
    }

    public function renderUploadedFiles($data, $ashtml = true)
    {
        $uploadedfiles = json_decode($data);

        if ((null !== $uploadedfiles) && (count((array) $uploadedfiles) > 0)) {
            $first_entry = current($uploadedfiles);
            $folder_location = ($ashtml && isset($first_entry->folderurl)) ? '<a href="'.urldecode($first_entry->folderurl).'">Dropbox</a>' : 'Dropbox';

            // Fill our custom field with the details of our upload session
            $html = sprintf(__('%d file(s) uploaded to %s:', 'wpcloudplugins'), count((array) $uploadedfiles), $folder_location);
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
     * @param \TheLion\OutoftheBox\Processor $processor
     *
     * @return string
     */
    public function new_private_folder_name($private_folder_name, $processor)
    {
        if (!isset($_COOKIE['WPCP-FORM-NAME-'.$processor->get_listtoken()])) {
            return $private_folder_name;
        }

        if ('gf_upload_box' !== $processor->get_shortcode_option('class')) {
            return $private_folder_name;
        }

        return trim(str_replace(['|', '/'], ' ', sanitize_text_field($_COOKIE['WPCP-FORM-NAME-'.$processor->get_listtoken()])));
    }

    /**
     * Function to change the Private Folder Name for Guest users.
     *
     * @param string                         $private_folder_name_guest
     * @param \TheLion\OutoftheBox\Processor $processor
     *
     * @return string
     */
    public function rename_private_folder_names_for_guests($private_folder_name_guest, $processor)
    {
        if ('gf_upload_box' !== $processor->get_shortcode_option('class')) {
            return $private_folder_name_guest;
        }

        return str_replace(__('Guests', 'wpcloudplugins').' - ', '', $private_folder_name_guest);
    }
}

$GFOutoftheBoxAddOn = new GFOutoftheBoxAddOn();
// This filter isn't fired if inside class
add_filter('gform_export_field_value', [$GFOutoftheBoxAddOn, 'outofthebox_set_export_values'], 10, 4);
