<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Quiz_Maker_mailpoet
 * @subpackage Quiz_Maker_mailpoet/includes
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Quiz_Maker_mailpoet
 * @subpackage Quiz_Maker_mailpoet/includes
 * @author     AYS Pro LLC <info@ays-pro.com>
 */
class Quiz_Maker_Integrations
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    private $settings_obj;

    private $capability;

    private $blockquote_content;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version){

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->settings_obj = new Quiz_Maker_Settings_Actions($this->plugin_name);

        $settings_url = sprintf(
            __( "For enabling this option, please go to %s page and fill all options.", $this->plugin_name ),
            "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=".$this->plugin_name."-settings&ays_quiz_tab=tab2' target='_blank'>". __( "this", $this->plugin_name ) ."</a>"
        );
        $blockquote_content = '<blockquote class="error_message">'. $settings_url .'</blockquote>';
        $this->blockquote_content = $blockquote_content;
    }

    // ===== INTEGRATIONS HOOKS =====

    // Integrations settings page action hook
    public function ays_quiz_settings_page_integrations_content( $args ){

        $integrations_contents = apply_filters( 'ays_qm_settings_page_integrations_contents', array(), $args );

        $integrations = array();

        foreach ($integrations_contents as $key => $integrations_content) {
            $content = '<fieldset>';
            if(isset($integrations_content['title'])){
                $content .= '<legend>';
                if(isset($integrations_content['icon'])){
                    $content .= '<img class="ays_integration_logo" src="'. $integrations_content['icon'] .'" alt="" style="margin-right: 10px;">';
                }
                $content .= '<h5>'. $integrations_content['title'] .'</h5></legend>';
            }
            if(isset($integrations_content['content'])){
                $content .= $integrations_content['content'];
            }

            $content .= '</fieldset>';

            $integrations[] = $content;
        }

        echo implode('<hr/>', $integrations);
    }

    //Integrations quiz page action hook
    public function ays_quiz_page_integrations_content($args){

        $integrations_contents = apply_filters( 'ays_qm_quiz_page_integrations_contents', array(), $args );
        $integrations = array();

        foreach ($integrations_contents as $key => $integrations_content) {
            $content = '<fieldset>';
            if(isset($integrations_content['title'])){
                $content .= '<legend>';
                if(isset($integrations_content['icon'])){
                    $content .= '<img class="ays_integration_logo" src="'. $integrations_content['icon'] .'" alt="" style="margin-right: 10px;">';
                }
                $content .= '<h5>'. $integrations_content['title'] .'</h5></legend>';
            }
            $content .= $integrations_content['content'];

            $content .= '</fieldset>';

            $integrations[] = $content;
        }

        echo implode('<hr/>', $integrations);
    }

    ////////////////////////////////////////////////////////////////////////////////////////
    //====================================================================================//
    ////////////////////////////////////////////////////////////////////////////////////////

    // ===== Mad mimi start =====

        // Mad mimi integration

        // Mad mimi integration in quiz page content
        public function ays_quiz_page_mad_mimi_content( $integrations, $args ){

            $quiz_settings = $this->settings_obj;
            // Mad Mimi
            $mad_mimi_res  = ($quiz_settings->ays_get_setting('mad_mimi') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('mad_mimi');
            $mad_mimi      = json_decode($mad_mimi_res, true);
            $mad_mimi_user_name = isset($mad_mimi['user_name']) ? $mad_mimi['user_name'] : '';
            $mad_mimi_api_key   = isset($mad_mimi['api_key']) ? $mad_mimi['api_key'] : '';
            $mad_mimi_lists = $this->ays_quiz_mad_mimi_lists($mad_mimi_user_name , $mad_mimi_api_key);

            $enable_mad_mimi = $args['enable_mad_mimi'];
            $mad_mimi_list = $args['mad_mimi_list'];

            $icon = AYS_QUIZ_ADMIN_URL .'/images/integrations/mad-mimi-logo.png';
            $title = __('Mad Mimi Settings',$this->plugin_name);

            $content = '';
            if(count($mad_mimi) > 0){
                if($mad_mimi_user_name == "" || $mad_mimi_api_key == ""){
                    $content .= $this->blockquote_content;
                }else{
                    $disabled = ($mad_mimi_user_name == "" || $mad_mimi_api_key == "") ? "disabled" : '';
                    $checked = ($enable_mad_mimi == true) ? "checked" : '';

                    $content .= '<div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_quiz_enable_mad_mimi">'. __('Enable Mad Mimi', $this->plugin_name) .'</label>
                        </div>
                        <div class="col-sm-1">
                            <input type="checkbox" class="ays-enable-timer1" id="ays_quiz_enable_mad_mimi" name="ays_quiz_enable_mad_mimi" value="on" '.$checked.' '.$disabled.'/>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_quiz_mad_mimi_list">'. __('Select List', $this->plugin_name) .'</label>
                        </div>
                        <div class="col-sm-8">';
                            if(!empty($mad_mimi_lists)){
                                $mad_mimi_select  = "<select name='ays_quiz_mad_mimi_list' id='ays_quiz_mad_mimi_list'>";
                                $mad_mimi_select .= "<option value='' disabled>Select list</option>";
                                foreach($mad_mimi_lists as $key => $mad_mimi_list){
                                    $list_name = isset($mad_mimi_list['name']) && $mad_mimi_list['name'] != "" ? esc_attr($mad_mimi_list['name']) : "";
                                    $selected = isset($mad_mimi_db_list) && $mad_mimi_db_list == $list_name ? "selected" : "";
                                    $mad_mimi_select .= "<option value='".$list_name."' ".$selected.">".$list_name."</option>";
                                }
                                $mad_mimi_select .= "</select>";
                                $content .= $mad_mimi_select;
                            }else{
                                $content .= __("There are no lists" , $this->plugin_name);
                            }
                    $content .= '</div>
                    </div>';
                }
            }else{
                $content .= $this->blockquote_content;
            }

            $integrations['mad_mimi'] = array(
                'content' => $content,
                'icon' => $icon,
                'title' => $title,
            );

            return $integrations;
        }

        // Mad mimi integration in quiz page options
        public function ays_quiz_page_mad_mimi_options( $args, $options ){

            // Mad Mimi
            $args['enable_mad_mimi']  = (isset($options['enable_mad_mimi']) && $options['enable_mad_mimi'] == 'on') ? true : false;
            $args['mad_mimi_list'] = (isset($options['mad_mimi_list'])) ? $options['mad_mimi_list'] : '';

            return $args;
        }

        // Mad mimi integration in quiz page data saver
        public function ays_quiz_page_mad_mimi_save( $options, $data ){

            $options['enable_mad_mimi'] = ( isset( $_POST['ays_quiz_enable_mad_mimi'] ) && $_POST['ays_quiz_enable_mad_mimi'] == 'on' ) ? 'on' : 'off';
            $options['mad_mimi_list'] = !isset( $_POST['ays_quiz_mad_mimi_list'] ) ? "" : sanitize_text_field( $_POST['ays_quiz_mad_mimi_list'] );

            return $options;
        }

        // Mad mimi integration / settings page

        // Mad mimi integration in General settings page content
        public function ays_settings_page_mad_mimi_content( $integrations, $args ){

            $actions = $this->settings_obj;

            // Mad mimi
            $mad_mimi_options = ($actions->ays_get_setting('mad_mimi') === false) ? json_encode(array()) : $actions->ays_get_setting('mad_mimi');
            $mad_mimi_options = json_decode($mad_mimi_options, true);
            $mad_mimi_user_name = isset($mad_mimi_options['user_name']) && $mad_mimi_options['user_name'] != "" ? esc_attr($mad_mimi_options['user_name']) : '';
            $mad_mimi_api_key   = isset($mad_mimi_options['api_key']) && $mad_mimi_options['api_key'] != "" ? esc_attr($mad_mimi_options['api_key']) : '';

            $icon  = AYS_QUIZ_ADMIN_URL . '/images/integrations/mad-mimi-logo.png';
            $title = __( 'Mad Mimi', $this->plugin_name );

            $content = '';
            $content .= '
                <div class="form-group row">
                    <div class="col-sm-12">
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays_quiz_mad_mimi_user_name">'. __('Username', $this->plugin_name) .'</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="ays-text-input" id="ays_quiz_mad_mimi_user_name" name="ays_quiz_mad_mimi_user_name" value="'. $mad_mimi_user_name .'" >
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays_quiz_mad_mimi_api_key">'. __('API Key', $this->plugin_name) .'</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="ays-text-input" id="ays_quiz_mad_mimi_api_key" name="ays_quiz_mad_mimi_api_key" value="'. $mad_mimi_api_key .'" >
                            </div>
                        </div>';
            $content .= '<blockquote>';
            $content .= sprintf( __( "You can get your API key from your ", $this->plugin_name ) . "<a href='%s' target='_blank'> %s.</a>", "https://madmimi.com/user/edit?account_info_tabs=account_info_personal", "Account" );
            $content .= '</blockquote>';
            $content .= '
                </div>
            </div>';

            $integrations['mad_mimi'] = array(
                'content' => $content,
                'icon' => $icon,
                'title' => $title,
            );

            return $integrations;
        }

        // Mad mimi integration in General settings page data saver
        public function ays_settings_page_mad_mimi_save( $fields, $data ){

            $mad_mimi_user_name = isset($data['ays_quiz_mad_mimi_user_name']) && $data['ays_quiz_mad_mimi_user_name'] != "" ? sanitize_text_field($data['ays_quiz_mad_mimi_user_name']) : '';
            $mad_mimi_api_key   = isset($data['ays_quiz_mad_mimi_api_key']) && $data['ays_quiz_mad_mimi_api_key'] != "" ? sanitize_text_field($data['ays_quiz_mad_mimi_api_key']) : '';

            $mad_mimi_options   = array(
                "user_name" => $mad_mimi_user_name,
                "api_key"   => $mad_mimi_api_key
            );

            $fields['mad_mimi'] = json_encode( $mad_mimi_options );

            return $fields;
        }


        // Mad mimi integration / front-end

        // Mad mimi integration in front-end functional
        public function ays_front_end_mad_mimi_functional( $arguments, $options, $data ){

            if( $arguments['enable_mad_mimi'] ){
                if( !empty( $data['user_email'] ) ){

                    $quiz_settings = $this->settings_obj;
                    $mad_mimi_res = ($quiz_settings->ays_get_setting('mad_mimi') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('mad_mimi');
                    $mad_mimi = json_decode($mad_mimi_res, true);
                    $mad_mimi_user_name = isset($mad_mimi['user_name']) ? $mad_mimi['user_name'] : '' ;
                    $mad_mimi_api_key   = isset($mad_mimi['api_key']) ? $mad_mimi['api_key'] : '' ;

                    $mad_mimi_email  = (isset($_REQUEST['ays_user_email']) && $_REQUEST['ays_user_email'] != "") ? sanitize_email( $_REQUEST['ays_user_email'] ) : "";
                    $user_name       = isset($_REQUEST['ays_user_name']) && $_REQUEST['ays_user_name'] != "" ? explode(" ", stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ) ) : array();
                    $mad_mimi_fname  = (isset($user_name[0]) && $user_name[0] != "") ? $user_name[0] : "";
                    $mad_mimi_lname  = (isset($user_name[1]) && $user_name[1] != "") ? $user_name[1] : "";

                    $mad_mimi_data   = array(
                        "mad_mimi_user_name" => $mad_mimi_user_name,
                        "api_key"            => $mad_mimi_api_key,
                        "list"               => $arguments['mad_mimi_list'],
                        "user_email"         => $mad_mimi_email,
                        "user_first_name"    => $mad_mimi_fname,
                        "user_last_name"     => $mad_mimi_lname
                    );

                    $mresult = $this->ays_quiz_add_mad_mimi_email( $mad_mimi_data );
                }
            }
        }

        // Mad mimi integration in front-end options
        public function ays_front_end_mad_mimi_options( $args, $options ){

            // Mad mimi
            $args['enable_mad_mimi'] = ( isset($options['enable_mad_mimi'] ) && $options['enable_mad_mimi'] == 'on') ? true : false;
            $args['mad_mimi_list'] = (isset($options['mad_mimi_list'])) ? $options['mad_mimi_list'] : '';

            return $args;
        }

    // ===== Mad mimi end =====

    ////////////////////////////////////////////////////////////////////////////////////////
    //====================================================================================//
    ////////////////////////////////////////////////////////////////////////////////////////

    // ===== ConvertKit Settings start =====

        // ConvertKit Settings integration

        // ConvertKit Settings integration in quiz page content
        public function ays_quiz_page_convert_kit_content( $integrations, $args ){

            $quiz_settings = $this->settings_obj;

            // ConvertKit Settings
            $convertKit_res      = ($quiz_settings->ays_get_setting('convertKit') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('convertKit');
            $convertKit          = json_decode($convertKit_res, true);
            $convertKit_api_key  = isset($convertKit['api_key']) && $convertKit['api_key'] != "" ? esc_attr($convertKit['api_key']) : '';
            $convertKit_forms    = $this->ays_get_convertKit_forms($convertKit_api_key);
            $convertKit_forms_list = isset($convertKit_forms['forms']) && !empty($convertKit_forms['forms']) ? $convertKit_forms['forms'] : array();
            $convertKit_response_status = isset($convertKit_forms['status']) && $convertKit_forms['status'] ? true : false;

            $enable_convertKit = $args['enable_convertKit'];
            $convertKit_form_id = $args['convertKit_form_id'];

            $icon = AYS_QUIZ_ADMIN_URL .'/images/integrations/convertkit_logo.png';
            $title = __('ConvertKit Settings',$this->plugin_name);

            $content = '';
            if(count($convertKit) > 0){
                if( $convertKit_api_key == "" ){
                    $content .= $this->blockquote_content;
                }else{
                    $disabled = !$convertKit_response_status ? "disabled" : '';
                    $checked = ($enable_convertKit == true) ? "checked" : '';

                    $content .= '<div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_quiz_enable_convertkit">'. __('Enable ConvertKit', $this->plugin_name) .'</label>
                        </div>
                        <div class="col-sm-1">
                            <input type="checkbox" class="ays-enable-timer1" id="ays_quiz_enable_convertkit" name="ays_quiz_enable_convertkit" value="on" '.$checked.' '.$disabled.'/>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_quiz_convertKit_list">'. __('ConvertKit List', $this->plugin_name) .'</label>
                        </div>
                        <div class="col-sm-8">';
                            if(isset($convertKit_forms) && !empty($convertKit_forms)){
                                if( $convertKit_response_status ){
                                    $convertKit_select  = "<select name='ays_quiz_convertKit_list' id='ays_quiz_convertKit_list'>";
                                    $convertKit_select .= "<option value='' disabled>Select list</option>";
                                    foreach($convertKit_forms_list as $key => $convertKit_form){
                                        $response_form_id = isset($convertKit_form['id']) && $convertKit_form['id'] != "" ? $convertKit_form['id'] : "";
                                        $response_form_name = isset($convertKit_form['name']) && $convertKit_form['name'] != "" ? $convertKit_form['name'] : "";

                                        $selected = ($convertKit_form_id == $response_form_id) ? 'selected' : '';
                                        $convertKit_select .= "<option value='".$response_form_id."' ".$selected.">".$response_form_name."</option>";
                                    }
                                    $convertKit_select .= "</select>";
                                    $content .= $convertKit_select;
                                }else{
                                    $content .= __("There are no forms" , $this->plugin_name);
                                }
                            }else{
                                $content .= __("There are no forms" , $this->plugin_name);
                            }
                    $content .= '</div>
                    </div>';
                }
            }else{
                $content .= $this->blockquote_content;
            }

            $integrations['convertKit'] = array(
                'content' => $content,
                'icon' => $icon,
                'title' => $title,
            );

            return $integrations;
        }

        // ConvertKit Settings integration in quiz page options
        public function ays_quiz_page_convert_kit_options( $args, $options ){

            // ConvertKit Settings
            $args['enable_convertKit']  = (isset($options['enable_convertKit']) && $options['enable_convertKit'] == 'on') ? true : false;
            $args['convertKit_form_id'] = (isset($options['convertKit_form_id'])) ? esc_attr( $options['convertKit_form_id'] ) : '';

            return $args;
        }

        // ConvertKit Settings integration in quiz page data saver
        public function ays_quiz_page_convert_kit_save( $options, $data ){

            $options['enable_convertKit'] = ( isset( $_POST['ays_quiz_enable_convertkit'] ) && $_POST['ays_quiz_enable_convertkit'] == 'on' ) ? 'on' : 'off';
            $options['convertKit_form_id'] = !isset( $_POST['ays_quiz_convertKit_list'] ) ? "" : sanitize_text_field( $_POST['ays_quiz_convertKit_list'] );

            return $options;
        }

        // ConvertKit Settings integration / settings page

        // ConvertKit Settings integration in General settings page content
        public function ays_settings_page_convert_kit_content( $integrations, $args ){

            $actions = $this->settings_obj;

            // ConvertKit Settings
            $convertKit_res         = ($actions->ays_get_setting('convertKit') === false) ? json_encode(array()) : $actions->ays_get_setting('convertKit');
            $convertKit             = json_decode($convertKit_res, true);
            $convertKit_account_id  = isset($convertKit['api_key']) ? esc_attr($convertKit['api_key']) : '';


            $icon  = AYS_QUIZ_ADMIN_URL . '/images/integrations/convertkit_logo.png';
            $title = __( 'ConvertKit', $this->plugin_name );

            $content = '';
            $content .= '
                <div class="form-group row">
                    <div class="col-sm-12">
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays_quiz_convert_kit">'. __('API Key', $this->plugin_name) .'</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="ays-text-input" id="ays_quiz_convert_kit" name="ays_quiz_convert_kit" value="'. $convertKit_account_id .'" >
                            </div>
                        </div>';
            $content .= '<blockquote>';
            $content .= sprintf( __( "You can get your API key from your ", $this->plugin_name ) . "<a href='%s' target='_blank'> %s.</a>", "https://app.convertkit.com/account/edit", "Account" );
            $content .= '</blockquote>';
            $content .= '
                </div>
            </div>';

            $integrations['convertKit'] = array(
                'content' => $content,
                'icon' => $icon,
                'title' => $title,
            );

            return $integrations;
        }

        // ConvertKit Settings integration in General settings page data saver
        public function ays_settings_page_convert_kit_save( $fields, $data ){

            $convertKit_account_id = isset($data['ays_quiz_convert_kit']) && $data['ays_quiz_convert_kit'] != "" ? sanitize_text_field($data['ays_quiz_convert_kit']) : '';

            $convertKit_options = array(
                "api_key" => $convertKit_account_id,
            );

            $fields['convertKit'] = json_encode( $convertKit_options );

            return $fields;
        }


        // ConvertKit Settings integration / front-end

        // ConvertKit Settings integration in front-end functional
        public function ays_front_end_convert_kit_functional( $arguments, $options, $data ){

            if( $arguments['enable_convertKit'] ){
                if( !empty( $data['user_email'] ) ){

                    $quiz_settings = $this->settings_obj;

                    $covertKit_res       = ($quiz_settings->ays_get_setting('convertKit') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('convertKit');
                    $covertKit           = json_decode($covertKit_res, true);
                    $convertKit_api_key  = isset($covertKit['api_key']) && $covertKit['api_key'] != "" ? $covertKit['api_key'] : '';
                    $convertKit_data     = array();

                    $covertKit_email = (isset($_REQUEST['ays_user_email']) && $_REQUEST['ays_user_email'] != "") ? sanitize_email( $_REQUEST['ays_user_email'] ) : "";
                    $covertKit_name  = isset($_REQUEST['ays_user_name']) && $_REQUEST['ays_user_name'] != "" ? explode(" ", stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ) ) : array();
                    $covertKit_fname = (isset($covertKit_name[0]) && $covertKit_name[0] != "") ? $covertKit_name[0] : "";
                    $covertKit_lname = (isset($covertKit_name[1]) && $covertKit_name[1] != "") ? $covertKit_name[1] : "";

                    $convertKit_data = array(
                        "api_key" => $convertKit_api_key,
                        "form_id" => $arguments['convertKit_form_id'],
                        "email"   => $covertKit_email,
                        "fname"   => $covertKit_fname,
                        "lname"   => $covertKit_lname
                    );

                    $mresult = $this->ays_quiz_convertKit_add_user( $convertKit_data );
                }
            }
        }

        // ConvertKit Settings integration in front-end options
        public function ays_front_end_convert_kit_options( $args, $options ){

            // ConvertKit Settings
            $args['enable_convertKit'] = ( isset($options['enable_convertKit'] ) && $options['enable_convertKit'] == 'on') ? true : false;
            $args['convertKit_form_id'] = (isset($options['convertKit_form_id'])) ? $options['convertKit_form_id'] : '';

            return $args;
        }

    // ===== ConvertKit Settings end =====

    ////////////////////////////////////////////////////////////////////////////////////////
    //====================================================================================//
    ////////////////////////////////////////////////////////////////////////////////////////

    // ===== GetResponse start =====

        // GetResponse integration

        // GetResponse integration in quiz page content
        public function ays_quiz_page_get_response_content( $integrations, $args ){

            $quiz_settings = $this->settings_obj;

            // GetResponse
            $getResponse_res = ($quiz_settings->ays_get_setting('get_response') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('get_response');
            $getResponse = json_decode($getResponse_res, true);
            $getResponse_api_key = isset($getResponse['api_key']) ? $getResponse['api_key'] : '';
            $getResponse_lists = $this->ays_quiz_getResposne_lists($getResponse_api_key);
            $getResponse_status  = isset($getResponse_lists['status']) && $getResponse_lists['status'] ? true : false;
            $getResponse_message = isset($getResponse_lists['message']) && $getResponse_lists['message'] ? esc_attr($getResponse_lists['message']) : __("Something went wrong", $this->plugin_name);

            $enable_getResponse = $args['enable_getResponse'];
            $getResponse_db_list = $args['getResponse_list'];

            $icon = AYS_QUIZ_ADMIN_URL .'/images/integrations/get_response.png';
            $title = __('GetResponse Settings',$this->plugin_name);

            $content = '';
            if(count($getResponse) > 0){
                if( $getResponse_api_key == "" ){
                    $content .= $this->blockquote_content;
                }else{
                    $disabled = !$getResponse_status ? "disabled" : '';
                    $checked = ($enable_getResponse == true) ? "checked" : '';

                    $content .= '<div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_quiz_enable_getResponse">'. __('Enable GetResponse', $this->plugin_name) .'</label>
                        </div>
                        <div class="col-sm-1">
                            <input type="checkbox" class="ays-enable-timer1" id="ays_quiz_enable_getResponse" name="ays_quiz_enable_getResponse" value="on" '.$checked.' '.$disabled.'/>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_quiz_getResponse_list">'. __('GetResponse List', $this->plugin_name) .'</label>
                        </div>
                        <div class="col-sm-8">';
                            if( isset( $getResponse_lists ) && !empty( $getResponse_lists ) ){
                                if( $getResponse_status ){
                                    $getResponse_select  = "<select name='ays_quiz_getResponse_list' id='ays_quiz_getResponse_list'>";
                                    $getResponse_select .= "<option value='' disabled>Select list</option>";
                                    foreach($getResponse_lists as $key => $getResponse_list){
                                        if(isset($getResponse_list) && is_array($getResponse_list)){
                                            $list_id   = isset($getResponse_list['campaignId']) && $getResponse_list['campaignId'] != "" ? esc_attr($getResponse_list['campaignId']) : "";
                                            $list_name = isset($getResponse_list['name']) && $getResponse_list['name'] != "" ? esc_attr($getResponse_list['name']) : "";
                                            $selected_list = ($list_id == $getResponse_db_list) ? "selected" : "";
                                            $getResponse_select .= "<option value='".$list_id."' ".$selected_list.">".$list_name."</option>";
                                        }
                                    }
                                    $getResponse_select .= "</select>";
                                    $content .= $getResponse_select;
                                }else{
                                    $content .= "<blockquote style='border-left:2px solid red;font-size: 16px;'>" . $getResponse_message . "</blockquote>";
                                }
                            }else{
                                $content .= __("There are no forms" , $this->plugin_name);
                            }
                    $content .= '</div>
                    </div>';
                }
            }else{
                $content .= $this->blockquote_content;
            }

            $integrations['get_response'] = array(
                'content' => $content,
                'icon' => $icon,
                'title' => $title,
            );

            return $integrations;
        }

        // GetResponse integration in quiz page options
        public function ays_quiz_page_get_response_options( $args, $options ){

            // GetResponse options
            $args['enable_getResponse']  = (isset($options['enable_getResponse']) && $options['enable_getResponse'] == 'on') ? true : false;
            $args['getResponse_list'] = (isset($options['getResponse_list'])) ? esc_attr( $options['getResponse_list'] ) : '';

            return $args;
        }

        // GetResponse integration in quiz page data saver
        public function ays_quiz_page_get_response_save( $options, $data ){

            $options['enable_getResponse'] = ( isset( $_POST['ays_quiz_enable_getResponse'] ) && $_POST['ays_quiz_enable_getResponse'] == 'on' ) ? 'on' : 'off';
            $options['getResponse_list'] = !isset( $_POST['ays_quiz_getResponse_list'] ) ? "" : sanitize_text_field( $_POST['ays_quiz_getResponse_list'] );

            return $options;
        }

        // GetResponse integration / settings page

        // GetResponse integration in General settings page content
        public function ays_settings_page_get_response_content( $integrations, $args ){

            $actions = $this->settings_obj;

            // GetResponse
            $getResponse_res  = ($actions->ays_get_setting('get_response') === false) ? json_encode(array()) : $actions->ays_get_setting('get_response');
            $getResponse      = json_decode($getResponse_res, true);
            $getResponse_api_key = isset($getResponse['api_key']) ? $getResponse['api_key'] : '';



            $icon  = AYS_QUIZ_ADMIN_URL . '/images/integrations/get_response.png';
            $title = __( 'GetResponse', $this->plugin_name );

            $content = '';
            $content .= '
                <div class="form-group row">
                    <div class="col-sm-12">
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="ays_quiz_getresponse_api_key">'. __('GetResponse API Key', $this->plugin_name) .'</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="ays-text-input" id="ays_quiz_getresponse_api_key" name="ays_quiz_getresponse_api_key" value="'. $getResponse_api_key .'" >
                            </div>
                        </div>';
            $content .= '<blockquote>';
            $content .= sprintf( __( "You can get your API key from your ", $this->plugin_name ) . "<a href='%s' target='_blank'> %s.</a>", "https://app.getresponse.com/api", "account" );
            $content .= '</blockquote>';
            $content .= '<blockquote>';
            $content .= __( "For security reasons, unused API keys expire after 90 days. When that happens, you’ll need to generate a new key.", $this->plugin_name );
            $content .= '</blockquote>';
            $content .= '
                </div>
            </div>';

            $integrations['get_response'] = array(
                'content' => $content,
                'icon' => $icon,
                'title' => $title,
            );

            return $integrations;
        }

        // GetResponse integration in General settings page data saver
        public function ays_settings_page_get_response_save( $fields, $data ){

            $getResponse_api_key = isset($data['ays_quiz_getresponse_api_key']) && $data['ays_quiz_getresponse_api_key'] != "" ? sanitize_text_field($data['ays_quiz_getresponse_api_key']) : '';
            $getResponse_options = array(
                "api_key" => $getResponse_api_key
            );

            $fields['get_response'] = json_encode( $getResponse_options );

            return $fields;
        }


        // GetResponse integration / front-end

        // GetResponse integration in front-end functional
        public function ays_front_end_get_response_functional( $arguments, $options, $data ){

            if( $arguments['enable_getResponse'] ){
                if( !empty( $data['user_email'] ) ){

                    $quiz_settings = $this->settings_obj;

                    $getResponse_res = ($quiz_settings->ays_get_setting('get_response') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('get_response');
                    $getResponse     = json_decode($getResponse_res, true);

                    $getResponse_api_key    = isset($getResponse['api_key']) ? $getResponse['api_key'] : '';
                    $getResponse_new_email  = (isset($_REQUEST['ays_user_email']) && $_REQUEST['ays_user_email'] != "") ? sanitize_email($_REQUEST['ays_user_email']) : "";
                    $getResponse_user_name  = isset($_REQUEST['ays_user_name']) && $_REQUEST['ays_user_name'] != "" ? explode(" ", stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ) ) : array();
                    $getResponse_fname      = (isset($getResponse_user_name[0]) && $getResponse_user_name[0] != "") ? $getResponse_user_name[0] : "";
                    $getResponse_lname      = (isset($getResponse_user_name[1]) && $getResponse_user_name[1] != "") ? $getResponse_user_name[1] : "";
                    $getResponse_data = array(
                        "api_key" => $getResponse_api_key,
                        "list_id" => $arguments['getResponse_list'],
                        "email"   => $getResponse_new_email,
                        "fname"   => $getResponse_fname,
                        "lname"   => $getResponse_lname,
                    );

                    $mresult = $this->ays_quiz_add_getResponse_contact( $getResponse_data );
                }
            }
        }

        // GetResponse integration in front-end options
        public function ays_front_end_get_response_options( $args, $options ){

            // ConvertKit Settings
            $args['enable_getResponse'] = ( isset($options['enable_getResponse'] ) && $options['enable_getResponse'] == 'on') ? true : false;
            $args['getResponse_list'] = (isset($options['getResponse_list'])) ? $options['getResponse_list'] : '';

            return $args;
        }

    // ===== GetResponse end =====

    ////////////////////////////////////////////////////////////////////////////////////////
    //====================================================================================//
    ////////////////////////////////////////////////////////////////////////////////////////

    // ===== Integration calls for admin dashboard start =====

        // Mad Mimi - Get lists
        public function ays_quiz_mad_mimi_lists($user_name, $api_key){
            $bad_request = array();

            if ($user_name == "" || $api_key == "") {
                return $bad_request;
            }

            $url = "https://madmimi.com/api/v3/subscriberLists?";
            $data = array(
                "username" => $user_name,
                "api_key"  => $api_key
            );

            $url .= http_build_query($data);

            $headers = array(
                "headers" => array(
                    "Accept"  => "application/json",
                ),
            );

            $api_call = wp_remote_get( $url , $headers);
            if(wp_remote_retrieve_response_code( $api_call ) == 200){
                $subscriber_lists = wp_remote_retrieve_body($api_call);
                if($subscriber_lists == ""){
                    return $bad_request;
                }
                else{
                    $response = json_decode($subscriber_lists , true);
                    $lists = isset($response['subscriberLists']) ? $response['subscriberLists'] : array();
                    return $lists;
                }
            }
            else{
                return $bad_request;
            }
        }

        // ConvertKit Lists
        public function ays_get_convertKit_forms( $api_key ) {
            // error_reporting(0);
            if ($api_key == "") {
                return array();
            }

            $url = "https://api.convertkit.com/v3/forms?api_key=".$api_key;
            $api_call = wp_remote_get($url);
            $body = array();
            $response = array();
            if ( wp_remote_retrieve_response_code( $api_call ) === 200 ){
                $body = wp_remote_retrieve_body( $api_call );
                if($body != ""){
                    $body = json_decode($body , true);
                    $response['forms'] = isset($body['forms']) && !empty($body['forms']) ? $body['forms'] : array();
                    $response['status'] = true;
                }else{
                    $response['forms'] = array();
                    $response['status'] = false;
                }
            }else{
                $response['forms'] = array();
                $response['status'] = false;
            }
            return $response;

        }

        // GetResponse
        public function ays_quiz_getResposne_lists($api_key){
            $bad_request = array();
            if($api_key == ""){
                return $bad_request;
            }

            $url = "https://api.getresponse.com/v3/campaigns";
            $headers = array(
                "headers" => array(
                    "X-Auth-Token" => "api-key ".$api_key,
                )
            );
            $api_call = wp_remote_get($url , $headers);
            $response = wp_remote_retrieve_body( $api_call );
            $new_response = array();
            if($response != ""){
                $new_response = json_decode($response , true);
            }
            if(wp_remote_retrieve_response_code( $api_call ) == 200){
                $new_response['status'] = true;
            }
            else{
                $new_response['status'] = false;
            }
            return $new_response;
        }

    // ===== Integration calls for admin dashboard end =====

    // ===== Front end calls start =====

        // Mad mimi
        public function ays_quiz_add_mad_mimi_email($data){
            if(empty($data)){
                return false;
            }

            $mad_mimi_user_name = isset($data['mad_mimi_user_name']) && $data['mad_mimi_user_name'] != "" ? $data['mad_mimi_user_name'] : "";
            $api_key            = isset($data['api_key']) && $data['api_key'] != "" ? $data['api_key'] : "";
            $list               = isset($data['list']) && $data['list'] != "" ? $data['list'] : "";
            $user_email         = isset($data['user_email']) && $data['user_email'] != "" ? $data['user_email'] : "";
            $user_first_name    = isset($data['user_first_name']) && $data['user_first_name'] != "" ? $data['user_first_name'] : "";
            $user_last_name     = isset($data['user_last_name']) && $data['user_last_name'] != "" ? $data['user_last_name'] : "";

            if($mad_mimi_user_name == "" || $api_key == "" || $list == ""){
                return false;
            }

            $url = "https://api.madmimi.com/audience_lists/".$list."/add?";

            $data = array(
                "username"   => $mad_mimi_user_name,
                "api_key"    => $api_key,
                "email"      => $user_email,
                "first_name" => $user_first_name,
                "last_name"  => $user_last_name
            );

            $url .= http_build_query($data);

            $headers = array(
                "headers" => array(
                    "Accept"  => "application/json",
                )
            );

            $api_call = wp_remote_post( $url , $headers);
            if(wp_remote_retrieve_response_code( $api_call ) == 200){
                $result = wp_remote_retrieve_body($api_call);
                return $result;
            }else{
                return false;
            }
        }

        // ConvertKit
        public function ays_quiz_convertKit_add_user($data) {
            if (empty($data)) {
                return false;
            }

            $api_key = isset($data['api_key']) && $data['api_key'] != '' ? $data['api_key'] : '';
            $convertKit_fname   = (isset($data['fname']) && $data['fname'] != "") ? $data['fname'] : "";
            $convertKit_lname   = (isset($data['lname']) && $data['lname'] != "") ? $data['lname'] : "";
            $convertKit_email   = (isset($data['email']) && $data['email'] != "") ? $data['email'] : "";
            $convertKit_form_id = (isset($data['form_id']) && $data['form_id'] != "") ? $data['form_id'] : "";

            if($api_key == "" || $convertKit_form_id == "" || $convertKit_email == ""){
                return false;
            }

            $url = "https://api.convertkit.com/v3/forms/".$convertKit_form_id."/subscribe?";
            $url .= http_build_query(array(
                    "email"      => $convertKit_email,
                    "api_key"    => $api_key,
                    "first_name" => $convertKit_fname
                )
            );

            $api_call = wp_remote_post($url);
        }

        // GetResponse
        public function ays_quiz_add_getResponse_contact($data){
            if(empty($data)){
                return false;
            }

            $api_key = isset($data['api_key']) && $data['api_key'] != "" ? $data['api_key'] : "";
            $list_id = isset($data['list_id']) && $data['list_id'] != "" ? $data['list_id'] : "";
            if($api_key == "" || $list_id == ""){
                return false;
            }
            $user_email = isset($data['email']) && $data['email'] != "" ? $data['email'] : "";
            $user_fname = isset($data['fname']) && $data['fname'] != "" ? $data['fname'] : "";
            $user_lname = isset($data['lname']) && $data['lname'] != "" ? $data['lname'] : "";

            $url = "https://api.getresponse.com/v3/contacts";
            $headers = array(
                "headers" => array(
                    "X-Auth-Token" => "api-key ".$api_key
                ),
                "body"    => array(
                    "name" => $user_fname." ".$user_lname,
                    "campaign" => array(
                        "campaignId" => $list_id
                    ),
                    "email" => $user_email
                )
            );
            $api_call = wp_remote_post($url , $headers);

            $response = wp_remote_retrieve_body($api_call);
            if(wp_remote_retrieve_response_code($api_call) != 200){
                return false;
            }
        }


    // ===== Front end calls end =====

    ////////////////////////////////////////////////////////////////////////////////////////
    //====================================================================================//
    ////////////////////////////////////////////////////////////////////////////////////////

    // ===== reCAPTCHA start =====

        // reCAPTCHA integration

        // reCAPTCHA integration in quiz page content
        public function ays_quiz_page_recaptcha_content( $integrations, $args ){

            $quiz_settings = $this->settings_obj;
            // reCAPTCHA
            $recaptcha_res  = ($quiz_settings->ays_get_setting('recaptcha') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('recaptcha');
            $recaptcha      = json_decode($recaptcha_res, true);
            $recaptcha_site_key = isset($recaptcha['site_key']) && $recaptcha['site_key'] != "" ? esc_attr($recaptcha['site_key']) : '';
            $recaptcha_secret_key = isset($recaptcha['secret_key']) && $recaptcha['secret_key'] != "" ? esc_attr($recaptcha['secret_key']) : '';

            $enable_recaptcha = $args['enable_recaptcha'];

            $icon = AYS_QUIZ_ADMIN_URL .'/images/integrations/recaptcha_logo.png';
            $title = __('reCAPTCHA Settings',$this->plugin_name);

            $content = '';
            if(count($recaptcha) > 0){
                if($recaptcha_site_key == "" || $recaptcha_secret_key == ""){
                    $content .= $this->blockquote_content;
                }else{
                    $disabled = ($recaptcha_site_key == "" || $recaptcha_secret_key == "") ? "disabled" : '';
                    $checked = ($enable_recaptcha == true) ? "checked" : '';

                    $content .= '<div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_quiz_enable_recaptcha">'. __('Enable reCAPTCHA', $this->plugin_name) .'</label>
                            </div>
                            <div class="col-sm-1">
                                <input type="checkbox" class="ays-enable-timer1" id="ays_quiz_enable_recaptcha" name="ays_quiz_enable_recaptcha" value="on" '.$checked.' '.$disabled.'/>
                            </div>
                        </div>';
                }
            }else{
                $content .= $this->blockquote_content;
            }

            $integrations['recaptcha'] = array(
                'content' => $content,
                'icon' => $icon,
                'title' => $title,
            );

            return $integrations;
        }

        // reCAPTCHA integration in quiz page options
        public function ays_quiz_page_recaptcha_options( $args, $options ){

            // reCAPTCHA
            $args['enable_recaptcha'] = (isset($options['enable_recaptcha']) && $options['enable_recaptcha'] == 'on') ? true : false;

            return $args;
        }

        // reCAPTCHA integration in quiz page data saver
        public function ays_quiz_page_recaptcha_save( $options, $data ){

            $options['enable_recaptcha'] = ( isset( $data['ays_quiz_enable_recaptcha'] ) && $data['ays_quiz_enable_recaptcha'] == 'on' ) ? 'on' : 'off';

            return $options;
        }

        // reCAPTCHA integration / settings page

        // reCAPTCHA integration in General settings page content
        public function ays_settings_page_recaptcha_content( $integrations, $args ){

            $actions = $this->settings_obj;

            // reCAPTCHA
            $recaptcha_options = ($actions->ays_get_setting('recaptcha') === false) ? json_encode(array()) : $actions->ays_get_setting('recaptcha');
            $recaptcha_options = json_decode($recaptcha_options, true);
            $recaptcha_site_key = isset($recaptcha_options['site_key']) && $recaptcha_options['site_key'] != "" ? esc_attr($recaptcha_options['site_key']) : '';
            $recaptcha_secret_key = isset($recaptcha_options['secret_key']) && $recaptcha_options['secret_key'] != "" ? esc_attr($recaptcha_options['secret_key']) : '';
            $recaptcha_language = isset($recaptcha_options['language']) && $recaptcha_options['language'] != "" ? esc_attr($recaptcha_options['language']) : '';
            $recaptcha_theme = isset($recaptcha_options['theme']) && $recaptcha_options['theme'] != "" ? esc_attr($recaptcha_options['theme']) : 'light';

            $icon  = AYS_QUIZ_ADMIN_URL . '/images/integrations/recaptcha_logo.png';
            $title = __( 'reCAPTCHA', $this->plugin_name );

            $content = '';
            $content .= '
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for="ays_quiz_recaptcha_site_key">'. __('reCAPTCHA v2 Site Key', $this->plugin_name) .'</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="ays-text-input" id="ays_quiz_recaptcha_site_key" name="ays_quiz_recaptcha_site_key" value="'. $recaptcha_site_key .'" >
                                </div>
                            </div>
                            <hr/>
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for="ays_quiz_recaptcha_secret_key">'. __('reCAPTCHA v2 Secret Key', $this->plugin_name) .'</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="ays-text-input" id="ays_quiz_recaptcha_secret_key" name="ays_quiz_recaptcha_secret_key" value="'. $recaptcha_secret_key .'" >
                                </div>
                            </div>
                            <hr/>
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for="ays_quiz_recaptcha_language">'. __('reCAPTCHA Language', $this->plugin_name) .'</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="ays-text-input" id="ays_quiz_recaptcha_language" name="ays_quiz_recaptcha_language" value="'. $recaptcha_language .'" >
                                    <span class="ays_quiz_small_hint_text">
                                        <span>' . sprintf(
                                            __( "e.g. en, de - Language used by reCAPTCHA. To get the code for your language click %s here %s", $this->plugin_name ),
                                            '<a href="https://developers.google.com/recaptcha/docs/language" target="_blank">',
                                            "</a>"
                                        ) . '</span>
                                    </span>
                                </div>
                            </div>
                            <hr/>
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for="ays_quiz_recaptcha_theme">'. __('reCAPTCHA Theme', $this->plugin_name) .'</label>
                                </div>
                                <div class="col-sm-9">
                                    <select class="ays-text-input" id="ays_quiz_recaptcha_theme" name="ays_quiz_recaptcha_theme" >
                                        <option value="light" '. ( $recaptcha_theme == 'light' ? 'selected' : '' ) .'>'. __('Light', $this->plugin_name) .'</option>
                                        <option value="dark" '. ( $recaptcha_theme == 'dark' ? 'selected' : '' ) .'>'. __('Dark', $this->plugin_name) .'</option>
                                    </select>
                                </div>
                            </div>
                            ';
            $content .= '<blockquote>';
            $content .= sprintf( __( "You need to set up reCAPTCHA in your Google account to generate the required keys and get them by %s Google's reCAPTCHA admin console %s.", $this->plugin_name ), "<a href='https://www.google.com/recaptcha/admin/create' target='_blank'>", "</a>");
            $content .= '</blockquote>';
            $content .= '
                    </div>
                </div>';

            $integrations['recaptcha'] = array(
                'content' => $content,
                'icon' => $icon,
                'title' => $title,
            );

            return $integrations;
        }

        // reCAPTCHA integration in General settings page data saver
        public function ays_settings_page_recaptcha_save( $fields, $data ){

            $recaptcha_site_key = isset($data['ays_quiz_recaptcha_site_key']) && $data['ays_quiz_recaptcha_site_key'] != "" ? sanitize_text_field($data['ays_quiz_recaptcha_site_key']) : '';
            $recaptcha_secret_key = isset($data['ays_quiz_recaptcha_secret_key']) && $data['ays_quiz_recaptcha_secret_key'] != "" ? sanitize_text_field($data['ays_quiz_recaptcha_secret_key']) : '';
            $recaptcha_language = isset($data['ays_quiz_recaptcha_language']) && $data['ays_quiz_recaptcha_language'] != "" ? sanitize_text_field($data['ays_quiz_recaptcha_language']) : '';
            $recaptcha_theme = isset($data['ays_quiz_recaptcha_theme']) && $data['ays_quiz_recaptcha_theme'] != "" ? sanitize_text_field($data['ays_quiz_recaptcha_theme']) : '';

            $recaptcha_options = array(
                "site_key" => $recaptcha_site_key,
                "secret_key" => $recaptcha_secret_key,
                "language" => $recaptcha_language,
                "theme" => $recaptcha_theme,
            );

            $fields['recaptcha'] = json_encode( $recaptcha_options );

            return $fields;
        }

        // reCAPTCHA integration / front-end

        // reCAPTCHA integration in front-end functional
        public function ays_front_end_recaptcha_functional( $arguments, $options, $data ){
            if( $options['enable_recaptcha'] ){

                $quiz_settings = $this->settings_obj;

                // reCAPTCHA
                $recaptcha_options = ($quiz_settings->ays_get_setting('recaptcha') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('recaptcha');
                $recaptcha_options = json_decode($recaptcha_options, true);
                $recaptcha_site_key = isset($recaptcha_options['site_key']) && $recaptcha_options['site_key'] != "" ? esc_attr($recaptcha_options['site_key']) : '';
                $recaptcha_secret_key = isset($recaptcha_options['secret_key']) && $recaptcha_options['secret_key'] != "" ? esc_attr($recaptcha_options['secret_key']) : '';
                $recaptcha_language = isset($recaptcha_options['language']) && $recaptcha_options['language'] != "" ? esc_attr($recaptcha_options['language']) : '';
                $recaptcha_theme = isset($recaptcha_options['theme']) && $recaptcha_options['theme'] != "" ? esc_attr($recaptcha_options['theme']) : '';

                if( $recaptcha_language != '' ){
                    $hl = "&hl=".$recaptcha_language;
                }

                wp_enqueue_script(
                    $this->plugin_name . '-grecaptcha',
                    // 'https://www.google.com/recaptcha/api.js?onload=wpformsRecaptchaLoad&render=explicit',
                    'https://www.google.com/recaptcha/api.js?render=explicit' . $hl,
                    array('jquery'),
                    null,
                    true
                );

                wp_enqueue_script(
                    $this->plugin_name . '-grecaptcha-js',
                    AYS_QUIZ_PUBLIC_URL . '/js/partials/grecaptcha.js',
                    array('jquery'),
                    $this->version,
                    true
                );

                $unique_key = uniqid();

                $options = array(
                    'uniqueKey' => $unique_key,
                    'siteKey' => $recaptcha_site_key,
                    'secretKey' => $recaptcha_secret_key,
                    'language' => $recaptcha_language,
                    'theme' => $recaptcha_theme,
                );

                $inline_js = "
                    if(typeof aysQuizRecaptchaObj === 'undefined'){
                        var aysQuizRecaptchaObj = [];
                    }
                    aysQuizRecaptchaObj['" . $unique_key . "']  = '" . base64_encode( json_encode( $options ) ) . "';
                ";
                wp_add_inline_script( $this->plugin_name . '-grecaptcha', $inline_js, 'before' );

                $data_content = '';
                $data_content .= '<div class="ays-quiz-section ays-quiz-recaptcha-section">';
                    $data_content .= '<div class="ays-quiz-section-header">';
                        $data_content .= '<div class="ays-quiz-recaptcha-wrap">';
                            $data_content .= '<div class="ays-quiz-g-recaptcha" data-unique-key="'. $unique_key .'"></div>';
                            $data_content .= '<div class="ays-quiz-g-recaptcha-hidden-error ays-quiz-question-validation-error">'. __( "reCAPTCHA field is required please complete!", $this->plugin_name ) .'</div>';
                        $data_content .= '</div>';
                    $data_content .= '</div>';
                $data_content .= '</div>';
    
                $arguments[] = $data_content;
            }

            return $arguments;
        }

        // reCAPTCHA integration in front-end options
        public function ays_front_end_recaptcha_options( $args, $setting ){
            $options = $setting;
            // reCAPTCHA
            $args['enable_recaptcha'] = ( isset( $options['enable_recaptcha'] ) && $options['enable_recaptcha'] == 'on') ? true : false;

            return $args;
        }

    // ===== reCAPTCHA end =====


    ////////////////////////////////////////////////////////////////////////////////////////
    //====================================================================================//
    ////////////////////////////////////////////////////////////////////////////////////////
}
