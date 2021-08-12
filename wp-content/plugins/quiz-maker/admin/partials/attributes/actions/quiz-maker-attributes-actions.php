<?php
global $wpdb;
$action = (isset($_GET['action'])) ? sanitize_text_field( $_GET['action'] ) : '';
$heading = '';
$id = ( isset( $_GET['quiz_attribute'] ) ) ? absint( intval( $_GET['quiz_attribute'] ) ) : null;
$maxId = $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . 'aysquiz_attributes');
$quiz_attribute = array(
    'id'        => '',
    'name'      => '',
    'slug'      => "quiz_attr_" . ($maxId + 1) ,
    'type'      => '',
    'published' => '',
    'options'   => ''
);

switch( $action ) {
    case 'add':
        $heading = __('Add new field', $this->plugin_name);
        break;
    case 'edit':
        $heading = __('Edit field', $this->plugin_name);
        $quiz_attribute = $this->attributes_obj->get_attribute_by_id( $id );
        break;
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

?>

<div class="wrap">
    <div class="container-fluid">
        <h1><?php echo $heading; ?></h1>
        <hr/>
        <form class="ays-quiz-attribute-form" id="ays-quiz-attribute-form" method="post">
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
                    <input class='ays-text-input' id='ays-attribute-name' name='ays_name' required type='text' value='<?php echo isset($quiz_attribute['name']) ? esc_attr( $quiz_attribute['name'] ) : ''; ?>'>
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
                    <select class='ays-text-input ays-text-input-short' id='ays-attribute-type' name='ays_quiz_attr_type'>
                        <option value="text" <?php echo (isset($quiz_attribute['type']) && $quiz_attribute['type'] == 'text' ) ? 'selected' : '' ; ?>><?php echo __('Text', $this->plugin_name); ?></option>
                        <option value="textarea" <?php echo (isset($quiz_attribute['type']) && $quiz_attribute['type'] == 'textarea' ) ? 'selected' : '' ; ?>><?php echo __('Textarea', $this->plugin_name); ?></option>
                        <option value="email" <?php echo (isset($quiz_attribute['type']) && $quiz_attribute['type'] == 'email' ) ? 'selected' : '' ; ?>><?php echo __('E-Mail', $this->plugin_name); ?></option>
                        <option value="number" <?php echo (isset($quiz_attribute['type']) && $quiz_attribute['type'] == 'number' ) ? 'selected' : '' ; ?>><?php echo __('Number', $this->plugin_name); ?></option>
                        <option value="tel" <?php echo (isset($quiz_attribute['type']) && $quiz_attribute['type'] == 'tel' ) ? 'selected' : '' ; ?>><?php echo __('Telephone', $this->plugin_name); ?></option>
                        <option value="url" <?php echo (isset($quiz_attribute['type']) && $quiz_attribute['type'] == 'url' ) ? 'selected' : '' ; ?>><?php echo __('URL', $this->plugin_name); ?></option>
                        <option value="select" <?php echo (isset($quiz_attribute['type']) && $quiz_attribute['type'] == 'select' ) ? 'selected' : '' ; ?>><?php echo __('Select', $this->plugin_name); ?></option>
                        <option value="checkbox" <?php echo (isset($quiz_attribute['type']) && $quiz_attribute['type'] == 'checkbox' ) ? 'selected' : '' ; ?>><?php echo __('Checkbox', $this->plugin_name); ?></option>
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
                            <input type="text" name="ays_quiz_attr_options" id="ays_quiz_attr_option" class="ays-text-input" value="<?php echo (isset($quiz_attribute['options']) && $quiz_attribute['type'] == "select") ? stripslashes(htmlentities($quiz_attribute['options'])) : ''; ?>" placeholder="Red; Blue; Green">
                        </div>
                    </div>
                </div>
                <div class="col-sm-7 ays_attr_description" <?php echo (!isset($quiz_attribute['type']) || $quiz_attribute['type'] != "checkbox") ? 'style="display:none"' : ''; ?>>
                    <div class="form-group row">
                        <div class="col-sm-2">
                            <label for="ays_quiz_attr_option"><?php echo __('Description', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Please write any text, email or link',$this->plugin_name)?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-10">
                            <input type="text" name="ays_quiz_attr_description" id="ays_quiz_attr_description" class="ays-text-input" value="<?php echo (isset($quiz_attribute['options']) && $quiz_attribute['type'] == "checkbox") ? stripslashes($quiz_attribute['options']) : ''; ?>" placeholder="<?php echo __('Type your description here', $this->plugin_name); ?>">
                        </div>
                    </div>
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

                <div class="col-sm-3">
                    <div class="form-check form-check-inline">
                        <input type="radio" id="ays-publish" name="ays_publish" value="1" <?php echo ( $quiz_attribute["published"] == '' ) ? "checked" : ""; ?> <?php echo ( $quiz_attribute['published'] == '1') ? 'checked' : ''; ?> />
                        <label class="form-check-label" for="ays-publish"> <?php echo __('Published', $this->plugin_name); ?> </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" id="ays-unpublish" name="ays_publish" value="0" <?php echo ( $quiz_attribute['published']  == '0' ) ? 'checked' : ''; ?> />
                        <label class="form-check-label" for="ays-unpublish"> <?php echo __('Unpublished', $this->plugin_name); ?> </label>
                    </div>
                </div>
            </div>

            <hr/>
            <?php
            wp_nonce_field('quiz_attribute_action', 'quiz_attribute_action');
            $other_attributes = array( 'id' => 'ays-button' );
            submit_button( __( 'Save and close', $this->plugin_name ), 'primary', 'ays_submit', true, $other_attributes );
            if($id != null){
                submit_button(__('Save', $this->plugin_name), '', 'ays_apply', true, $other_attributes);
            }
            ?>
        </form>
    </div>
</div>
