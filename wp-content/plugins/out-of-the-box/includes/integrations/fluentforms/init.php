<?php

namespace TheLion\OutoftheBox\Integrations;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

use FluentForm\App\Helpers\Helper;
use FluentForm\Framework\Helpers\ArrayHelper;

class FluentForms_Field extends \FluentForm\App\Services\FormBuilder\BaseFieldManager
{
    public $default_value = '[outofthebox class="fluentforms_upload_box" mode="upload" upload="1" uploadrole="all" upload_auto_start="0" userfolders="auto" viewuserfoldersrole="none"]';
    public $field_type = 'wpcp-outofthebox';

    public function __construct()
    {
        parent::__construct(
            $this->field_type,
            'Dropbox',
            ['cloud', 'dropbox', 'drive', 'documents', 'files', 'upload', 'video', 'audio', 'media', 'embed'],
            'general' // where to push general/advanced
        );

        //Load Scripts and CSS in Editor

        add_action('admin_enqueue_scripts', [$this, 'enqueueEditorAssets']);

        // Data render
        add_filter('fluentform_response_render_'.$this->key, [$this, 'renderResponse'], 10, 3);

        // Validation
        add_filter('fluentform_validate_input_item_'.$this->key, [$this, 'validateInput'], 10, 5);

        // Custom Private Folder names
        add_filter('outofthebox_private_folder_name', [$this, 'new_private_folder_name'], 10, 2);
        add_filter('outofthebox_private_folder_name_guests', [$this, 'rename_private_folder_names_for_guests'], 10, 2);
    }

    public function getComponent()
    {
        return [
            'index' => 99,
            'element' => $this->key,
            'attributes' => [
                'name' => $this->key,
                'class' => '',
                'value' => '',
                'type' => 'hidden',
            ],
            'settings' => [
                'container_class' => '',
                'placeholder' => '',
                'html_codes' => $this->getPlaceholder(),
                'label' => esc_html__('Attach your documents', 'wpcloudplugins'),
                'label_placement' => '',
                'wpcp_shortcode' => $this->default_value,
                'admin_field_label' => '',
                'validation_rules' => [
                    'required' => [
                        'value' => false,
                        'message' => esc_html__('This field is required', 'fluentformpro'),
                    ],
                ],
                'conditional_logics' => [],
            ],
            'editor_options' => [
                'title' => $this->title,
                'icon_class' => 'ff-edit-files',
                'template' => 'customHTML',
            ],
        ];
    }

    public function getGeneralEditorElements()
    {
        return [
            'label',
            'admin_field_label',
            'value',
            'wpcp_shortcode',
            'label_placement',
            'validation_rules',
        ];
    }

    public function generalEditorElement()
    {
        return [
            'wpcp_shortcode' => [
                'template' => 'inputTextarea',
                'label' => 'Shortcode',
                'help_text' => esc_html__('Grab the shortcode via the Shortcode Builder and copy+paste in this field.', 'wpcloudplugins'),
                'css_class' => 'wpcp-shortcode',
                'inline_help_text' => '<br/><div>'.esc_html__('Create the module configuration via the Shortcode Builder and copy+paste the raw shortcode in this field.', 'wpcloudplugins').'</div><br/><button type="button" class="el-button el-button--primary el-button--medium outofthebox open-shortcode-builder">'.esc_html__('Build your shortcode', 'wpcloudplugins').'</button>',
                'rows' => 8,
            ],
        ];
    }

    public function getAdvancedEditorElements()
    {
        return [
            'name',
            'help_message',
            'container_class',
            'class',
            'conditional_logics',
        ];
    }

    public function getPlaceholder()
    {
        ob_start(); ?>
            <div id="OutoftheBox" class="light upload">
                <div class="OutoftheBox upload"style="width: 100%;">
                    <div class="fileupload-box -is-formfield -is-required -has-files" style="width:100%;max-width:100%;"">
                    <!-- FORM ELEMENTS -->
                    <div class="fileupload-form" >
                    <!-- END FORM ELEMENTS -->

                    <!-- UPLOAD BOX HEADER -->
                    <div class="fileupload-header">
                        <div class="fileupload-header-title">
                            <div class="fileupload-empty">
                                <div class="fileupload-header-text-title upload-add-file"><?php esc_html_e('Add your file', 'wpcloudplugins'); ?></div>
                                    <div class="fileupload-header-text-subtitle upload-add-folder"><a><?php esc_html_e('Or select a folder', 'wpcloudplugins'); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END UPLOAD BOX HEADER -->

                    <!-- UPLOAD BOX FOOTER -->
                    <div class="fileupload-footer">
                        <div class="fileupload-footer-content">
                        <button class="fileupload-start-button button"><span> Start upload</span></button>
                        </div>
                    </div>
                    <!-- END UPLOAD BOX FOOTER -->

                    </div>
                </div>
            </div>
            <?php
        return ob_get_clean();
    }

    public function render($data, $form)
    {
        $elementName = $data['element'];
        $data = apply_filters('fluenform_rendering_field_data_'.$elementName, $data, $form);

        $shortcode_render = do_shortcode($data['settings']['wpcp_shortcode']);

        $field_id = $this->makeElementId($data, $form).'_'.Helper::$formInstance;
        $prefill = (isset($_REQUEST[$field_id]) ? stripslashes($_REQUEST[$field_id]) : '');

        $data['attributes']['id'] = $field_id;
        $data['attributes']['class'] = @trim('fileupload-filelist fileupload-input-filelist');
        $data['attributes']['value'] = $prefill;

        $elMarkup = "<label for='".$data['attributes']['id']."' class='ff_file_upload_holder'>%s<input %s></label>";
        $elMarkup = sprintf($elMarkup, $shortcode_render, $this->buildAttributes($data['attributes'], $form));
        $html = $this->buildElementMarkup($elMarkup, $data, $form);

        echo apply_filters('fluenform_rendering_field_html_'.$elementName, $html, $data, $form);
    }

    /**
     * @param $response string|array|number|null - Original input from form submission
     * @param $field array - the form field component array
     * @param $form_id - form id
     *
     * @return string
     */
    public function renderResponse($response, $field, $form_id)
    {
        // $response is the original input from your user
        // you can now alter the $response and return
        $ashtml = true;

        return apply_filters('outofthebox_render_formfield_data', $response, $ashtml, $this);
    }

    public function validateInput($errorMessage, $field, $formData, $fields, $form)
    {
        $fieldName = $field['name'];
        if (empty($formData[$fieldName])) {
            return $errorMessage;
        }
        $value = $formData[$fieldName]; // This is the user input value

        $uploaded_files = json_decode($value);

        if (empty($uploaded_files) || (0 === count((array) $uploaded_files))) {
            return [ArrayHelper::get($field, 'raw.settings.validation_rules.required.message')];
        }

        return $errorMessage;
    }

    public function enqueueEditorAssets()
    {
        if (false === Helper::isFluentAdminPage() || (isset($_GET['route']) && 'editor' != $_GET['route'])) {
            return;
        }

        global $OutoftheBox;

        $OutoftheBox->load_scripts();
        $OutoftheBox->load_styles();

        wp_enqueue_style('OutoftheBox.CustomStyle');
        wp_enqueue_script('WPCP-'.$this->field_type.'-FluentForms', plugins_url('FluentForms.js', __FILE__), ['WPCloudplugin.Libraries'], OUTOFTHEBOX_VERSION, true);
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

        if ('fluentforms_upload_box' !== $processor->get_shortcode_option('class')) {
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
        if ('fluentforms_upload_box' !== $processor->get_shortcode_option('class')) {
            return $private_folder_name_guest;
        }

        return str_replace(esc_html__('Guests', 'wpcloudplugins').' - ', '', $private_folder_name_guest);
    }
}

new FluentForms_Field();
