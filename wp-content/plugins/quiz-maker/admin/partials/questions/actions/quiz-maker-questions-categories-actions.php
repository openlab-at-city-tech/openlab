<?php
$action = (isset($_GET['action'])) ? sanitize_text_field( $_GET['action'] ) : '';
$heading = '';
$id = ( isset( $_GET['question_category'] ) ) ? absint( intval( $_GET['question_category'] ) ) : null;
$question_category = [
    'id'            => '',
    'title'         => '',
    'description'   => '',
    'published'     => ''
];
switch( $action ) {
    case 'add':
        $heading = __('Add new category', $this->plugin_name);
        break;
    case 'edit':
        $heading = __('Edit category', $this->plugin_name);
        $question_category = $this->question_categories_obj->get_question_category( $id );
        break;
}
if( isset( $_POST['ays_submit'] ) ) {
    $_POST['id'] = $id;
    $result = $this->question_categories_obj->add_edit_question_category( $_POST );
}
if(isset($_POST['ays_apply'])){
    $_POST["id"] = $id;
    $_POST['ays_change_type'] = 'apply';
    $this->question_categories_obj->add_edit_question_category($_POST);
}
?>
<div class="wrap">
    <div class="container-fluid">
        <h1><?php echo $heading; ?></h1>
        <hr/>
        <form class="ays-quiz-category-form" id="ays-quiz-category-form" method="post">
            <div class="form-group row">
                <div class="col-sm-2">
                    <label for='ays-title'>
                        <?php echo __('Title', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Title of the question category',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-10">
                    <input class='ays-text-input' id='ays-title' name='ays_title' required type='text' value='<?php echo stripslashes(htmlentities($question_category['title'])); ?>'>
                </div>
            </div>

            <hr/>
            <div class='ays-field-dashboard'>
                <label for='ays-description'>
                    <?php echo __('Description', $this->plugin_name); ?>
                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Provide more information about the question category',$this->plugin_name)?>">
                        <i class="ays_fa ays_fa_info_circle"></i>
                    </a>
                </label>
                <?php
                $content = stripslashes(htmlentities($question_category['description']));
                $editor_id = 'ays-description';
                $settings = array('editor_height'=>'4','textarea_name'=>'ays_description','editor_class'=>'ays-textarea');
                wp_editor($content,$editor_id,$settings);
                ?>
            </div>

            <hr/>
            <div class="form-group row">
                <div class="col-sm-2">
                    <label>
                        <?php echo __('Category status', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose whether the question category is active or not. If you choose Unpublished option, the question category won’t be shown anywhere on your website',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>

                <div class="col-sm-3">
                    <div class="form-check form-check-inline">
                        <input type="radio" id="ays-publish" name="ays_publish" value="1" <?php echo ( $question_category["published"] == '' ) ? "checked" : ""; ?> <?php echo ( $question_category['published'] == '1') ? 'checked' : ''; ?> />
                        <label class="form-check-label" for="ays-publish"> <?php echo __('Published', $this->plugin_name); ?> </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" id="ays-unpublish" name="ays_publish" value="0" <?php echo ( $question_category['published']  == '0' ) ? 'checked' : ''; ?> />
                        <label class="form-check-label" for="ays-unpublish"> <?php echo __('Unpublished', $this->plugin_name); ?> </label>
                    </div>
                </div>
            </div>

            <hr/>
            <?php
            wp_nonce_field('question_category_action', 'question_category_action');
            $other_attributes = array( 'id' => 'ays-button' );
            submit_button( __( 'Save and close', $this->plugin_name ), 'primary', 'ays_submit', true, $other_attributes );
            if($id != null){
                submit_button(__('Save', $this->plugin_name), '', 'ays_apply', false, $other_attributes);
            }
            ?>
        </form>
    </div>
</div>
