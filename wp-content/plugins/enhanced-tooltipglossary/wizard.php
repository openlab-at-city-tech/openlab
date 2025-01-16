<?php

class CMTT_SetupWizard{

    //Common functions

    public static $steps = [
        1 => ['title' => 'Glossary Index Page',
            'options' => [
                0 => [
                    'name' => 'cmtt_glossaryID',
                    'title' => 'Create an Index Glossary Page?',
                    'type' => 'bool',
                    'value' => -1,
                    'hint' => 'Automatically generate a Glossary Index Page and select it as the default one.'
                ],
                1 => [
                    'name' => 'cmtt_glossaryListTiles',
                    'title' => 'How should terms be displayed on the index page?',
                    'type' => 'radio',
                    'options' => [
                        0 => [
                            'title' => 'List',
                            'value' => 0
                        ],
                        1 => [
                            'title' => 'Tiles',
                            'value' => 1
                        ],
                    ],
                    'hint' => 'Choose if the glossary base should be displayed as a list or tiles on the Glossary Index Page.'
                ],
                2 => [
                    'name' => 'cmtt_glossaryOnPosttypes',
                    'title' => 'Select the post types where you want to highlight glossary terms.',
                    'type' => 'multicheckbox',
                    'options' => [__CLASS__,'getPostTypes'],
                    'hint' => 'Select the post types where you\'d like the Glossary Terms to be highlighted.'
                ],
            ]
        ],
        2 => ['title' =>'Terms Settings',
            'options' => [
                0 => [
                    'name' => 'cmtt_glossaryTermLink',
                    'title' => 'Add links to highlighted terms?',
                    'type' => 'bool',
                    'value' => 0,
                    'hint' => 'Enable this option if you want to show links from posts or pages to the glossary term pages. This will only apply to Post / Pages and not to the Glossary Index page.'
                ],
                1 => [
                    'name' => 'cmtt_glossaryTooltip',
                    'title' => 'Show tooltips?',
                    'type' => 'bool',
                    'value' => 1,
                    'hint' => 'Enable this option if you want to show tooltips for highlighted terms.'
                ],
                2 => [
                    'name' => 'cmtt_glossaryCaseSensitive',
                    'title' => 'Should terms be case-sensitive?',
                    'type' => 'bool',
                    'value' => 1,
                    'hint' => 'Enable this option if you want glossary terms to be case-sensitive.'
                ],
            ],
        ],
        3 => ['title' =>'Compatibility',
            'options' => [
                0 => [
                    'name' => 'cmtt_glossaryTooltipHashContent',
                    'title' => 'Move tooltip contents to footer?',
                    'type' => 'bool',
                    'value' => 1,
                    'hint' => 'When this option is enabled, tooltip content will not be passed directly to JavaScript via the HTML attribute. This setting can improve compatibility with page builders, such as Elementor.'
                ],
                1 => [
                    'name' => 'cmtt_script_in_footer',
                    'title' => 'Load the scripts in footer?',
                    'type' => 'bool',
                    'value' => 1,
                    'hint' => 'This setting loads JavaScript and CSS at the end of the page, which can improve initial page loading speed but may cause compatibility issues. You can disable this option in the plugin settings if needed.'
                ],
                2 => [
                    'name' => 'cmtt_glossaryTurnOnAmp',
                    'title' => 'Show tooltips on AMP pages?',
                    'type' => 'bool',
                    'value' => 1,
                    'hint' => 'Enable this option to make the plugin work correctly if you use either "AMP" plugin or "AMP for WP – Accelerated Mobile Pages" plugin.'
                ],
            ],
        ],
        4 => ['title' =>'Add First Term',
            'content' => "<p><strong>To add a new term, follow these steps:</strong></p>
            <ul style='list-style:pointer; padding: 0 15px; margin: 0; line-height: 1em;'>
                <li>Go to \"<a href='post-new.php?post_type=glossary' target='_blank'>Add New</a>\" in the plugin menu.</li>
                <li>Enter the term title.</li>
                <li>Enter the term description.</li>
                <li>Optionally, add a featured image.</li>
                <li>Click the \"Publish\" button.</li>
            </ul><br/>
            <img src='" . CMTT_PLUGIN_URL . "assets/img/wizard_step_4.png' width='700px' height='400px'/>"],
        5 => ['title' =>'Glossary Dashboard',
            'content' => "<p><strong>You can manage all your terms on the \"<a href='edit.php?post_type=glossary' target='_blank'>Glossary</a>\" dashboard located in the plugin menu:</strong></p>
            <img src='". CMTT_PLUGIN_URL . "assets/img/wizard_step_5.png' width='850px' height='400px'/>"],
        6 => ['title' =>'Glossary Index Link',
            'content' => "<p><strong>You can always find the current link to the glossary index page in the plugin settings:</strong></p>
            <img src='" . CMTT_PLUGIN_URL . "assets/img/wizard_step_6.png' width='700px' height='450px'/>"],
    ];

    public static $slug = 'cmtt';

    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_submenu_page'),30);
        add_action('wp_ajax_cmtt_save_wizard_options',[__CLASS__,'saveOptions']);
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueueAdminScripts' ] );
    }

    public static function add_submenu_page(){
        if(\CM\CMTT_Settings::get('cmtt_addWizardMenu', 1)){
            add_submenu_page( CMTT_MENU_OPTION, 'Setup Wizard', 'Setup Wizard', 'manage_options', self::$slug . '_setup_wizard',[__CLASS__,'renderWizard'],20 );
        }
    }

    public static function enqueueAdminScripts(){
        $screen = get_current_screen();

        if ($screen && $screen->id === 'cm-tooltip-glossary_page_cmtt_setup_wizard') {
            wp_enqueue_style('wizard-css', CMTT_PLUGIN_URL . 'assets/css/wizard.css');
            wp_enqueue_script('wizard-js', CMTT_PLUGIN_URL . 'assets/js/wizard.js');
            wp_localize_script('wizard-js', 'wizard_data', ['ajaxurl' => admin_url('admin-ajax.php')]);
        }
    }

    public static function renderWizard(){
        require 'views/backend/wizard.php';
    }

    public static function renderSteps(){
        $output = '';
        $steps = self::$steps;
        foreach($steps as $num => $step){
            $output .= "<div class='cm-wizard-step step-{$num}' style='display:none;'>";
            $output .= "<h1>" . CMTT_SetupWizard::getStepTitle($num) . "</h1>";
            $output .= "<div class='step-container'>
                            <div class='cm-wizard-menu-container'>" . self::renderWizardMenu($num)." </div>";
            $output .= "<div class='cm-wizard-content-container'>";
            if(isset($step['options'])){
                $output .= "<form>";
                $output .= wp_nonce_field('wizard-form');
                foreach($step['options'] as $option){
                    $output .=  self::renderOption($option);
                }
                $output .= "</form>";
            }
            elseif (isset($step['content'])){
                $output .= $step['content'];
            }
            $output .= '</div></div>';
            $output .= self::renderStepsNavigation($num);
            $output .= '</div>';
        }
        return $output;
    }

    public static function renderStepsNavigation($num){
        $settings_url = admin_url( 'admin.php?page=cmtt_settings' );
        $output = "<div class='step-navigation-container'>
            <button class='prev-step' data-step='{$num}'>Previous</button>";
        if($num == count(self::$steps)){
            $output .= "<button class='finish' onclick='window.location.href = \"$settings_url\" '>Finish</button>";
        } else {
         $output .= "<button class='next-step' data-step='{$num}'>Next</button>";
        }
        $output .= "<p><a href='$settings_url'>Skip the setup wizard</a></p></div>";
        return $output;
    }

    public static function renderOption($option){
        switch($option['type']) {
            case 'bool':
                return self::renderBool($option);
            case 'int':
                return self::renderInt($option);
            case 'string':
                return self::renderString($option);
            case 'radio':
                return self::renderRadioSelect($option);
            case 'select':
                return self::renderSelect($option);
            case 'multicheckbox':
                return self::renderMulticheckbox($option);
        }
    }

    public static function renderBool($option){
        $checked = checked($option['value'],\CM\CMTT_Settings::get( $option['name'] ),false);
         $output = "<div class='form-group'>
                <label for='{$option['name']}' class='label'>{$option['title']}<div class='cm_field_help' data-title='{$option['hint']}'></div></label>";
        if($option['value'] === 1 || $option['value'] === 0 ){
            $oposite_val = intval(!$option['value']);
            $output .= "<input type='hidden' name='{$option['name']}' value='{$oposite_val}'>";
        }
        $output .= "<input type='checkbox' id='{$option['name']}' name='{$option['name']}' class='toggle-input' value='{$option['value']}' {$checked}>
                <label for='{$option['name']}' class='toggle-switch'></label>
            </div>";
        return $output;
    }

    public static function renderInt($option){
        $min = isset($option['min']) ? "min='{$option['min']}'" : '';
        $max = isset($option['max']) ? "max='{$option['max']}'" : '';
        $step = isset($option['step']) ? "step='{$option['step']}'" : '';
        return "<div class='form-group'>
                <label for='{$option['name']}' class='label'>{$option['title']}<div class='cm_field_help' data-title='{$option['hint']}'></div></label>
                <input type='number' id='{$option['name']}' name='{$option['name']}' value='{$option['value']}' {$min} {$max} {$step}/>
            </div>";
    }

    public static function renderString($option){
        return "<div class='form-group'>
                <label for='{$option['name']}' class='label'>{$option['title']}<div class='cm_field_help' data-title='{$option['hint']}'></div></label>
                <input type='text' id='{$option['name']}' name='{$option['name']}' value='{$option['value']}'/>
            </div>";
    }

    public static function renderRadioSelect($option){
        $options = $option['options'];
        $output = "<div class='form-group'>
                <label for='{$option['name']}' class='label'>{$option['title']}<div class='cm_field_help' data-title='{$option['hint']}'></div></label>
                <div>";
        if(is_callable($option['options'], false, $callable_name)) {
            $options = call_user_func($option['options']);
        }
        foreach($options as $item) {
            $checked = checked($item['value'],\CM\CMTT_Settings::get( $option['name'] ),false);
            $output .= "<input type='radio' name='{$option['name']}' value='{$item['value']}' {$checked}/>
                <label for='{$option['name']}'>{$item['title']}</label><br>";
        }
        $output .= "</div></div>";
        return $output;
    }

    public static function renderSelect($option){
        $options = $option['options'];
    $output = "<div class='form-group'>
                <label for='{$option['name']}' class='label'>{$option['title']}<div class='cm_field_help' data-title='{$option['hint']}'></div></label>
                <select id='{$option['name']}' name='{$option['name']}'>";
        if(is_callable($option['options'], false, $callable_name)) {
            $options = call_user_func($option['options']);
        }
        foreach($options as $item) {
        $selected = selected($item['value'],\CM\CMTT_Settings::get( $option['name'] ),false);
        $output .= "<option value='{$item['value']}' {$selected}>{$item['title']}</option>";
    }
    $output .= "</select></div>";
        return $output;
}
    public static function renderMulticheckbox($option){
        $options = $option['options'];
        $output = "<div class='form-group'>
                <label for='{$option['name']}' class='label'>{$option['title']}<div class='cm_field_help' data-title='{$option['hint']}'></div></label>
                <div>";
        if(is_callable($option['options'], false, $callable_name)) {
            $options = call_user_func($option['options']);
        }
        foreach($options as $item) {
            $checked = in_array($item['value'],\CM\CMTT_Settings::get( $option['name'] )) ? 'checked' : '';
            $output .= "<input type='checkbox' id='{$option['name']}' name='{$option['name']}[]' value='{$item['value']}' {$checked}/>
                <label for='{$option['name']}'>{$item['title']}</label><br>";
        }
        $output .= "</div></div>";
        return $output;
    }

    public static function renderWizardMenu($current_step){
        $steps = self::$steps;
        $output = "<ul class='cm-wizard-menu'>";
        foreach ($steps as $key => $step) {
            $num = $key;
            $selected = $num == $current_step ? 'class="selected"' : '';
            $output .= "<li {$selected} data-step='$num'>Step $num: {$step['title']}</li>";
        }
        $output .= "</ul>";
        return $output;
    }

    public static function getStepTitle($current_step){
        $steps = self::$steps;
        $title = "Step {$current_step}: ";
        $title .= $steps[$current_step]['title'];
        return $title;
    }

    //Custom functions

    public static function getPostTypes(){
        $args    = array(
            'public' => true,
        );
        $output_type = 'objects';
        $operator    = 'and';
        $post_types = get_post_types( $args, $output_type, $operator );
        $selected   = \CM\CMTT_Settings::get( 'cmtt_glossaryOnPosttypes' );
        if ( ! is_array( $selected ) ) {
            $selected = array();
        }
        $options = [];
        foreach ( $post_types as $post_type ) {
            $checked = in_array($post_type->name,$selected)? 'checked' :'';
            $options[] = ['title' => $post_type->labels->singular_name,
                'value' => $post_type->name];
        }
        return $options;
    }

    public static function outputPostTypes(){
        $args    = array(
            'public' => true,
        );
        $output_type = 'objects';
        $operator    = 'and';
        $post_types = get_post_types( $args, $output_type, $operator );
        $selected   = \CM\CMTT_Settings::get( 'cmtt_glossaryOnPosttypes' );
        if ( ! is_array( $selected ) ) {
            $selected = array();
        }
        $output = '';
        foreach ( $post_types as $post_type ) {
            $checked = in_array($post_type->name,$selected)?'checked':'';
            $output .= "<input type='checkbox' id='{$post_type->name}' name='cmtt_glossaryOnPosttypes[]' value='{$post_type->name}' {$checked}>
                <label for='{$post_type->name}'>{$post_type->labels->singular_name}</label><br>";
        }
        return $output;
    }

    public static function saveOptions(){
        if (isset($_POST['data'])) {
            // Parse the serialized data
            parse_str($_POST['data'], $formData);
            if(!wp_verify_nonce($formData['_wpnonce'],'wizard-form')){
                wp_send_json_error();
            }

            foreach($formData as $key => $value){
                if(strpos($key,'cmtt_') === false){
                    continue;
                }
                if($key == 'cmtt_glossaryID' && !empty(get_post(\CM\CMTT_Settings::get('cmtt_glossaryID',0)))){
                    continue;
                }
                if(is_array($value)){
                    $sanitized_value = array_map('sanitize_text_field', $value);
                    \CM\CMTT_Settings::set($key, $sanitized_value);
                    continue;
                }
                $sanitized_value = sanitize_text_field($value);
                \CM\CMTT_Settings::set($key, $sanitized_value);
            }
            \CMTT_Glossary_Index::tryGenerateGlossaryIndexPage();
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    }
}
