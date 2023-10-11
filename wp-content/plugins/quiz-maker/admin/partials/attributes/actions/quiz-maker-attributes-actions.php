<?php
global $wpdb;
$action = (isset($_GET['action'])) ? sanitize_text_field( $_GET['action'] ) : '';
$heading = '';
$id = ( isset( $_GET['quiz_attribute'] ) ) ? absint( intval( $_GET['quiz_attribute'] ) ) : null;

$user_id = get_current_user_id();
$user = get_userdata($user_id);
$author = array(
    'id' => $user->ID,
    'name' => $user->data->display_name
);

$maxId = $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . 'aysquiz_attributes');
$quiz_attribute = array(
    'id'        => '',
    'author_id' => $user_id,
    'name'      => '',
    'slug'      => "quiz_attr_" . ($maxId + 1) ,
    'type'      => '',
    'published' => '',
    'attr_options'   => ''
);


$attr_options = array(
    'show_custom_fields'              => 'off',
    'show_custom_fields_user_page'    => 'off',
    'show_custom_fields_user_results' => 'off',
    'show_custom_fields_quiz_results' => 'off',
);

switch( $action ) {
    case 'add':
        $heading = __('Add new field', $this->plugin_name);
        break;
    case 'edit':
        $heading = __('Edit field', $this->plugin_name);
        $quiz_attribute = $this->attributes_obj->get_attribute_by_id( $id );
        if (isset( $quiz_attribute['attr_options'] ) && $quiz_attribute['attr_options'] != "") {
            $attr_options = json_decode($quiz_attribute['attr_options'], true);
        }
        break;
}

$author_id = (isset( $quiz_attribute['author_id'] ) && $quiz_attribute['author_id'] != "") ? absint( $quiz_attribute['author_id'] ) : $user_id;
$owner = false;
if( $user_id == $author_id ){
    $owner = true;
}

if( $this->current_user_can_edit ){
    $owner = true;
}

$disabled_option = '';
$readonly_option = '';
if( !$owner ){
    $disabled_option = ' disabled ';
    $readonly_option = ' readonly ';
}

if( isset( $_POST['ays_submit'] ) ) {
    $_POST['id'] = $id;
    $result = $this->attributes_obj->add_edit_quiz_attribute( $_POST );
}
if(isset($_POST['ays_apply'])){
    $_POST["id"] = $id;
    $_POST['ays_change_type'] = 'apply';
    $this->attributes_obj->add_edit_quiz_attribute($_POST);
}

//Custom fields for shortcode
$shortcodes = array(
    'user_results'              => __('All Results', $this->plugin_name),
    'user_page'                 => __('User Page', $this->plugin_name),
    'quiz_results'              => __('Single quiz Results', $this->plugin_name),
    'individual_leaderboard'    => __('Individual Leaderboard', $this->plugin_name),
    'leaderboard_by_quiz_cat'   => __('Leaderboard By Quiz Category', $this->plugin_name),
);

$quiz_attribute_published = (isset( $quiz_attribute["published"] ) && $quiz_attribute["published"] != "") ? absint($quiz_attribute["published"]) : 1;

//Show Custom Fields
$attr_options['show_custom_fields'] = isset($attr_options['show_custom_fields']) ? sanitize_text_field($attr_options['show_custom_fields']) : 'off';
$show_custom_fields = (isset($attr_options['show_custom_fields']) && $attr_options['show_custom_fields'] == 'on') ? true : false;

//Show Custom Fields User Page
$attr_options['show_custom_fields_user_page'] = isset($attr_options['show_custom_fields_user_page']) ? sanitize_text_field($attr_options['show_custom_fields_user_page']) : 'off';
$user_page = (isset($attr_options['show_custom_fields_user_page']) && $attr_options['show_custom_fields_user_page'] == 'on') ? true : false;

//Show Custom Fields User Results
$attr_options['show_custom_fields_user_results'] = isset($attr_options['show_custom_fields_user_results']) ? sanitize_text_field($attr_options['show_custom_fields_user_results']) : 'off';
$user_results = (isset($attr_options['show_custom_fields_user_results']) && $attr_options['show_custom_fields_user_results'] == 'on') ? true : false;

//Show Custom Fields User Results
$attr_options['show_custom_fields_quiz_results'] = isset($attr_options['show_custom_fields_quiz_results']) ? sanitize_text_field($attr_options['show_custom_fields_quiz_results']) : 'off';
$quiz_results = (isset($attr_options['show_custom_fields_quiz_results']) && $attr_options['show_custom_fields_quiz_results'] == 'on') ? true : false;

//Show Custom Fields Individual Leaderboard
$attr_options['show_custom_fields_individual_leaderboard'] = isset($attr_options['show_custom_fields_individual_leaderboard']) ? sanitize_text_field($attr_options['show_custom_fields_individual_leaderboard']) : 'off';
$individual_leaderboard = (isset($attr_options['show_custom_fields_individual_leaderboard']) && $attr_options['show_custom_fields_individual_leaderboard'] == 'on') ? true : false;

// Show Custom Fields Leaderboard By Quiz Category
$attr_options['show_custom_fields_leaderboard_by_quiz_cat'] = isset($attr_options['show_custom_fields_leaderboard_by_quiz_cat']) ? sanitize_text_field($attr_options['show_custom_fields_leaderboard_by_quiz_cat']) : 'off';
$leaderboard_by_quiz_cat = (isset($attr_options['show_custom_fields_leaderboard_by_quiz_cat']) && $attr_options['show_custom_fields_leaderboard_by_quiz_cat'] == 'on') ? true : false;


?>

<div class="wrap">
    <div class="container-fluid">
        <div class="ays-quiz-heading-box">
            <div class="ays-quiz-wordpress-user-manual-box">
                <a href="https://ays-pro.com/wordpress-quiz-maker-user-manual" target="_blank"><?php echo __("View Documentation", $this->plugin_name); ?></a>
            </div>
        </div>
        <h1><?php echo $heading; ?></h1>
        <hr/>
        <form class="ays-quiz-attribute-form" id="ays-quiz-attribute-form" method="post">
            <input type="hidden" name="ays_quiz_attributes_author" value="<?php echo $author_id; ?>">
            <div class="form-group row">
                <div class="col-sm-2">
                    <label for='ays-attribute-name'>
                        <?php echo __('Name', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The name of field. It will show up as a placeholder for text input.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-10">
                    <input class='ays-text-input' id='ays-attribute-name' name='ays_name' required type='text' value='<?php echo isset($quiz_attribute['name']) ? esc_attr( $quiz_attribute['name'] ) : ''; ?>' <?php echo $readonly_option; ?>>
                </div>
            </div>

            <hr/>
            <div class="form-group row">
                <div class="col-sm-2">
                    <label>
                        <?php echo __('Slug', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Unique identifier for the field. You can use it in the integrations to show the user’s answer for this field.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-10">
                    <input class='ays-text-input' id='ays-attribute-slug' name='ays_slug' required readonly type='text' value='<?php echo (isset($quiz_attribute['slug'])) ? stripslashes(htmlentities($quiz_attribute['slug'])) : ''; ?>'>
                </div>
            </div>

            <hr/>
            <div class="form-group row">
                <div class="col-sm-2">
                    <label>
                        <?php echo __('Type', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose what kind of field to add from the mentioned list.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-3">
                    <select class='ays-text-input ays-text-input-short' id='ays-attribute-type' name='ays_quiz_attr_type' <?php echo $disabled_option; ?>>
                        <option value="text" <?php echo (isset($quiz_attribute['type']) && $quiz_attribute['type'] == 'text' ) ? 'selected' : '' ; ?>><?php echo __('Text', $this->plugin_name); ?></option>
                        <option value="textarea" <?php echo (isset($quiz_attribute['type']) && $quiz_attribute['type'] == 'textarea' ) ? 'selected' : '' ; ?>><?php echo __('Textarea', $this->plugin_name); ?></option>
                        <option value="email" <?php echo (isset($quiz_attribute['type']) && $quiz_attribute['type'] == 'email' ) ? 'selected' : '' ; ?>><?php echo __('E-Mail', $this->plugin_name); ?></option>
                        <option value="number" <?php echo (isset($quiz_attribute['type']) && $quiz_attribute['type'] == 'number' ) ? 'selected' : '' ; ?>><?php echo __('Number', $this->plugin_name); ?></option>
                        <option value="tel" <?php echo (isset($quiz_attribute['type']) && $quiz_attribute['type'] == 'tel' ) ? 'selected' : '' ; ?>><?php echo __('Telephone', $this->plugin_name); ?></option>
                        <option value="url" <?php echo (isset($quiz_attribute['type']) && $quiz_attribute['type'] == 'url' ) ? 'selected' : '' ; ?>><?php echo __('URL', $this->plugin_name); ?></option>
                        <option value="select" <?php echo (isset($quiz_attribute['type']) && $quiz_attribute['type'] == 'select' ) ? 'selected' : '' ; ?>><?php echo __('Select', $this->plugin_name); ?></option>
                        <option value="checkbox" <?php echo (isset($quiz_attribute['type']) && $quiz_attribute['type'] == 'checkbox' ) ? 'selected' : '' ; ?>><?php echo __('Checkbox', $this->plugin_name); ?></option>
                        <option value="date" <?php echo (isset($quiz_attribute['type']) && $quiz_attribute['type'] == 'date' ) ? 'selected' : '' ; ?>><?php echo __('Date', $this->plugin_name); ?></option>
                    </select>
                </div>
                <div class="col-sm-7 ays_attr_options" <?php echo (!isset($quiz_attribute['type']) || $quiz_attribute['type'] != "select") ? 'style="display:none"' : ''; ?>>    <div class="form-group row">
                        <div class="col-sm-2">
                            <label for="ays_quiz_attr_option"><?php echo __('Options', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Please write your options separated by example: Red; Blue; Green',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                            </label>
                        </div>
                        <div class="col-sm-10">
                            <input type="text" name="ays_quiz_attr_options" id="ays_quiz_attr_option" class="ays-text-input" value="<?php echo (isset($quiz_attribute['options']) && $quiz_attribute['type'] == "select") ? stripslashes(htmlentities($quiz_attribute['options'])) : ''; ?>" placeholder="Red; Blue; Green" <?php echo $readonly_option; ?>>
                        </div>
                    </div>
                </div>
                <div class="col-sm-7 ays_attr_description" <?php echo (!isset($quiz_attribute['type']) || $quiz_attribute['type'] != "checkbox") ? 'style="display:none"' : ''; ?>>
                    <div class="form-group row">
                        <div class="col-sm-2">
                            <label for="ays_quiz_attr_description"><?php echo __('Description', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Please write any text, email or link',$this->plugin_name)?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-10">
                            <input type="text" name="ays_quiz_attr_description" id="ays_quiz_attr_description" class="ays-text-input" value="<?php echo (isset($quiz_attribute['options']) && $quiz_attribute['type'] == "checkbox") ? stripslashes($quiz_attribute['options']) : ''; ?>" placeholder="<?php echo __('Type your description here', $this->plugin_name); ?>" <?php echo $readonly_option; ?>>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="form-group row ays_toggle_parent">
                <div class="col-sm-2">
                    <label for="ays_quiz_show_custom_fields">
                        <?php echo __('Show custom field on your shortcodes', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose in which shortcode to enable the custom field.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-3">
                    <input type="checkbox" id="ays_quiz_show_custom_fields" class="ays_toggle_checkbox" name="ays_quiz_show_custom_fields" <?php echo $show_custom_fields ? 'checked' : '';?>>
                </div>
                <div class="col-sm-7 ays_toggle_target <?php echo $show_custom_fields ? "" : "display_none" ?>">
                    <?php
                        foreach ($shortcodes as $shortcode_key => $shortcode):
                        ?>
                        <div class="form-group row">
                            <div class="col-sm-2">
                                <label for="ays_quiz_<?php echo $shortcode_key; ?>"><?php echo $shortcode;?></label>
                            </div>
                            <div class="col-sm-10">
                                <input type="checkbox" id="ays_quiz_<?php echo $shortcode_key; ?>" name="ays_quiz_show_custom_fields_<?php echo $shortcode_key; ?>" <?php echo $$shortcode_key ? 'checked' : '';?>>
                            </div>
                        </div>
                        <?php
                        endforeach;
                    ?>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-2">
                    <label>
                        <?php echo __('Attribute status', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose whether the attribute is active or not. If you choose Unpublished option, the attribute won’t be shown anywhere.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>

                <div class="col-sm-10">
                    <div class="form-check form-check-inline">
                        <input type="radio" id="ays-publish" name="ays_publish" <?php echo $readonly_option; ?> value="1" <?php echo ( $quiz_attribute_published == '' ) ? "checked" : ""; ?> <?php echo ( $quiz_attribute_published == '1') ? 'checked' : ''; ?> />
                        <label class="form-check-label" for="ays-publish"> <?php echo __('Published', $this->plugin_name); ?> </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" id="ays-unpublish" name="ays_publish"<?php echo $readonly_option; ?>  value="0" <?php echo ( $quiz_attribute_published  == '0' ) ? 'checked' : ''; ?> />
                        <label class="form-check-label" for="ays-unpublish"> <?php echo __('Unpublished', $this->plugin_name); ?> </label>
                    </div>
                </div>
            </div>

            <hr/>
            <?php
            if( $owner ){
                wp_nonce_field('quiz_attribute_action', 'quiz_attribute_action');
                $other_attributes = array( 'id' => 'ays-button' );
                submit_button( __( 'Save and close', $this->plugin_name ), 'primary', 'ays_submit', true, $other_attributes );
                if($id != null){
                    submit_button(__('Save', $this->plugin_name), '', 'ays_apply', true, $other_attributes);
                }
            }
            ?>
        </form>
    </div>
</div>
