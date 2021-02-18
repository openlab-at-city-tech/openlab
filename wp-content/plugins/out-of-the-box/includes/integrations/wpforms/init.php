<?php

namespace TheLion\OutoftheBox;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Add Button section
add_filter('wpforms_builder_fields_buttons', function ($fields) {
    $tmp = [
        'wpcloudplugins' => [
            'group_name' => 'WP Cloud Plugins',
            'fields' => [],
        ],
    ];

    return array_slice($fields, 0, 1, true) + $tmp + array_slice($fields, 1, count($fields) - 1, true);
}, 8);

class WPForms_Field_Upload_Box extends \WPForms_Field
{
    public function init()
    {
        // Define field type information.
        $this->name = 'Dropbox';
        $this->type = 'wpcp-outofthebox-upload-box';
        $this->group = 'wpcloudplugins';
        $this->icon = 'fa-cloud-upload fa-lg';
        $this->order = 3;

        add_action('wpforms_builder_enqueues_before', [$this, 'enqueues']);

        // Display values in a proper way
        add_filter('wpforms_html_field_value', [$this, 'html_field_value'], 10, 4);
        add_filter('wpforms_plaintext_field_value', [$this, 'plain_field_value'], 10, 3);
        add_filter('wpforms_pro_admin_entries_export_ajax_get_data', [$this, 'export_value'], 10, 2);

        // Custom Private Folder names
        add_filter('outofthebox_private_folder_name', [&$this, 'new_private_folder_name'], 10, 2);
        add_filter('outofthebox_private_folder_name_guests', [&$this, 'rename_private_folder_names_for_guests'], 10, 2);
    }

    ////////////////////////////////
    // **** **** PUBLIC **** **** //
    ////////////////////////////////

    // Field display on the form front-end.
    public function field_display($field, $deprecated, $form_data)
    {
        echo do_shortcode($field['shortcode']);
        $field_id = sprintf('wpforms-%d-field_%d', $form_data['id'], $field['id']);
        $prefill = (isset($_REQUEST[$field_id]) ? stripslashes($_REQUEST[$field_id]) : '');
        echo sprintf("<input type='hidden' name='wpforms[fields][%d]' id='%s' class='fileupload-filelist fileupload-input-filelist' value=''>", $field['id'], $field_id);
    }

    public function plain_field_value($value, $field, $form_data)
    {
        return $this->html_field_value($value, $field, $form_data, false);
    }

    public function html_field_value($value, $field, $form_data, $type)
    {
        if ($this->type !== $field['type']) {
            return $value;
        }

        // Reset $value as WPForms can truncate the content in e.g. the Entries table
        if (isset($field['value'])) {
            $value = $field['value'];
        }

        $ashtml = (in_array($type, ['entry-single', 'entry-table', 'email-html', 'smart-tag']));

        return apply_filters('outofthebox_render_formfield_data', $value, $ashtml, $this);
    }

    public function export_value($export_data, $request_data)
    {
        foreach ($export_data as $row_id => &$entry) {
            if (0 === $row_id) {
                continue; // Skip Headers
            }

            foreach ($entry as $field_id => &$value) {
                if ($request_data['form_data']['fields'][$field_id]['type'] !== $this->type) {
                    continue; // Skip data that isn't related to this custom field
                }
                $value = $this->plain_field_value($value, $request_data['form_data']['fields'][$field_id], $request_data['form_data']);
            }
        }

        return $export_data;
    }

    ///////////////////////////////
    // **** **** ADMIN **** **** //
    ///////////////////////////////

    /**
     * Format field value which is stored.
     *
     * @param int   $field_id     Field ID.
     * @param mixed $field_submit Field value that was submitted.
     * @param array $form_data    Form data and settings.
     */
    public function format($field_id, $field_submit, $form_data)
    {
        if ($this->type !== $form_data['fields'][$field_id]['type']) {
            return;
        }

        $name = !empty($form_data['fields'][$field_id]['label']) ? sanitize_text_field($form_data['fields'][$field_id]['label']) : '';

        wpforms()->process->fields[$field_id] = [
            'name' => $name,
            'value' => $field_submit,
            'id' => absint($field_id),
            'type' => $this->type,
        ];
    }

    // Enqueue Out-of-the-Box scripts
    public function enqueues()
    {
        global $OutoftheBox;

        $OutoftheBox->load_scripts();
        $OutoftheBox->load_styles();
        $OutoftheBox->load_custom_css();

        add_thickbox();


        wp_enqueue_script('WPCP-'.$this->type.'-WPForms', plugins_url('WPForms.js', __FILE__), ['jquery'], false, true);
        wp_enqueue_style('WPCP-'.$this->type.'-WPForms', plugins_url('WPForms.css', __FILE__));

    }

    // Field options panel inside the builder
    public function field_options($field)
    {
        // Basic field options.

        // Options open markup.
        $this->field_option(
            'basic-options',
            $field,
            [
                'markup' => 'open',
            ]
        );
        // Label.
        $this->field_option('label', $field);

        // Description.
        $this->field_option('description', $field);

        $btn = $this->custom_option_field(
            $field['id'],
            'builder',
            null,
            [
                'html_type' => 'button',
                'type' => 'button',
                'slug' => 'shortcode-builder',
                'class' => 'button outofthebox open-shortcode-builder',
                'name' => 'shortcode-builder',
                'value' => 'Open Shortcode Builder',
            ],
            false
        );

        $lbl = $this->field_element(
            'label',
            $field,
            [
                'slug' => 'shortcode',
                'value' => 'Shortcode',
                'tooltip' => 'Edit the raw shortcode or use the Shortcode Builder',
            ],
            false
        );

        $fld = $this->field_element(
            'textarea',
            $field,
            [
                'class' => '',
                'slug' => 'shortcode',
                'name' => 'shortcode',
                'rows' => 5,
                'value' => isset($field['shortcode']) ? $field['shortcode'] : '[outofthebox class="wpforms_upload_box" mode="upload" upload="1" uploadrole="all" upload_auto_start="0" userfolders="auto" viewuserfoldersrole="none"]',
            ],
            false
        );

        $args = [
            'slug' => 'shortcode',
            'content' => $lbl.$fld.$btn,
        ];

        $this->field_element('row', $field, $args);

        // Required toggle.
        $this->field_option('required', $field);

        // Options close markup.
        $this->field_option(
            'basic-options',
            $field,
            [
                'markup' => 'close',
            ]
        );

        // Advanced field options

        // Options open markup.
        $this->field_option(
            'advanced-options',
            $field,
            [
                'markup' => 'open',
            ]
        );

        // Hide label.
        $this->field_option('label_hide', $field);

        // Custom CSS classes.
        $this->field_option('css', $field);

        // Options close markup.
        $this->field_option(
            'advanced-options',
            $field,
            [
                'markup' => 'close',
            ]
        );
    }

    // Field preview inside the builder.
    public function field_preview($field)
    {
        // Label.
        $this->field_preview_option('label', $field);

        // Description.
        $this->field_preview_option('description', $field);

        // Real-Time preview isn't available for this element
        echo '<p>(Real-Time preview is not available for this element. Please refresh page to see changes to its options rendered.)</p>';

        if (!empty($field['shortcode'])) {
            // Shortcode.
            echo do_shortcode($field['shortcode']);
        } else {
            echo '<div class="wpcp-wpforms-placeholder"></div>';
        }
    }

    //The function that will help us create the buttons in the form builder
    public function custom_option_field($field_id, $field_class_mark, $label, $field_info, $echo = true)
    {
        if ('button' === $field_info['html_type']) {
            $output = sprintf('<button class="%s" id="wpforms-field-option-%d-%s" name="fields[%d][%s]" type="%s">%s</button>', $field_info['class'], $field_id, $field_info['slug'], $field_id, $field_info['slug'], $field_info['type'], $field_info['value']);
        }

        if (!$echo) {
            return $output;
        }

        echo $output;
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

        if ('wpforms_upload_box' !== $processor->get_shortcode_option('class')) {
            return $private_folder_name;
        }

        $raw_name = sanitize_text_field($_COOKIE['WPCP-FORM-NAME-'.$processor->get_listtoken()]);
        $name = str_replace(['|', '/'], ' ', $raw_name);
        $filtered_name = \TheLion\OutoftheBox\Helpers::filter_filename(stripslashes($name), false);

        return trim($filtered_name);
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
        if ('wpforms_upload_box' !== $processor->get_shortcode_option('class')) {
            return $private_folder_name_guest;
        }

        return str_replace(__('Guests', 'wpcloudplugins').' - ', '', $private_folder_name_guest);
    }
}

new \TheLion\OutoftheBox\WPForms_Field_Upload_Box();
