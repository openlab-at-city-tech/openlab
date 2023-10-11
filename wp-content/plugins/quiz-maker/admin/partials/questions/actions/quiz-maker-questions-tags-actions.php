<?php
$action = (isset($_GET['action'])) ? sanitize_text_field( $_GET['action'] ) : '';
$heading = '';
$loader_iamge = '';
$id = ( isset( $_GET['question_tag'] ) ) ? absint( intval( $_GET['question_tag'] ) ) : null;

$user_id = get_current_user_id();
$user = get_userdata($user_id);
$author = array(
    'id' => $user->ID,
    'name' => $user->data->display_name
);

$question_tag = array(
    'id'          => '',
    'author_id'   => $user_id,
    'title'       => '',
    'description' => '',
    'status'      => 'published',
    'options'     => array(),
);

$options = array(

);

switch( $action ) {
    case 'add':
        $heading = __('Add new tag', $this->plugin_name);
        break;
    case 'edit':
        $heading = __('Edit tag', $this->plugin_name);
        $question_tag = $this->question_tags_obj->get_question_tag( $id );
        break;
}

$author_id = (isset( $question_tag['author_id'] ) && $question_tag['author_id'] != "") ? absint( $question_tag['author_id'] ) : $user_id;
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

// General Settings | options
$gen_options = ($this->settings_obj->ays_get_setting('options') === false) ? array() : json_decode( stripcslashes($this->settings_obj->ays_get_setting('options') ), true);

// WP Editor height
$quiz_wp_editor_height = (isset($gen_options['quiz_wp_editor_height']) && $gen_options['quiz_wp_editor_height'] != '') ? absint( sanitize_text_field($gen_options['quiz_wp_editor_height']) ) : 100;

if( isset( $_POST['ays_submit'] ) ) {
    $_POST["id"] = $id;
    $this->question_tags_obj->add_edit_question_tag();
}
if(isset($_POST['ays_apply'])){
    $_POST["id"] = $id;
    $this->question_tags_obj->add_edit_question_tag();
}

//Tag Title
$title = (isset($question_tag['title']) && $question_tag['title'] != '') ? stripslashes( htmlentities( $question_tag['title'], ENT_QUOTES ) ) : '';

//Tag Description
$description = (isset($question_tag['description']) && $question_tag['description'] != '') ? stripslashes( htmlspecialchars_decode( $question_tag['description'] ) ) : '';

//Tag Status
$status = (isset($question_tag['status']) && $question_tag['status'] != '') ? $question_tag['status'] : 'published';

//Tag Loader
$loader_iamge = "<span class='display_none ays_quiz_loader_box'><img src=". AYS_QUIZ_ADMIN_URL ."/images/loaders/loading.gif></span>";

?>
<div class="wrap">
    <div class="container-fluid">
        <h1><?php echo $heading; ?></h1>
        <hr/>
        <form class="ays-quiz-tag-form" id="ays-quiz-tag-form" method="post">
            <input type="hidden" class="quiz_wp_editor_height" value="<?php echo $quiz_wp_editor_height; ?>">
            <input type="hidden" name="ays_quiz_question_tags_author" value="<?php echo $author_id; ?>">
            <!-- Qusetion tag title start -->
            <div class="form-group row">
                <div class="col-sm-2">
                    <label for='ays_quiz_question_tags_title'>
                        <?php echo __('Title', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Title of the question tag',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-10">
                    <input class='ays-text-input' id='ays_quiz_question_tags_title' name='ays_quiz_question_tags_title' type='text' value='<?php echo $title; ?>' <?php echo $readonly_option; ?>>
                </div>
            </div>
            <!-- Qusetion tag title end -->
            <hr/>
            <!-- Qusetion tag description start -->
            <div class="form-group row">
                <div class="col-sm-2">
                    <label for='ays_quiz_question_tags_description'>
                        <?php echo __('Description', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Provide more information about the question tag',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-10">
                    <?php
                    $content = $description;
                    $editor_id = 'ays_quiz_question_tags_description';
                    $settings = array('editor_height'=>$quiz_wp_editor_height,'textarea_name'=>'ays_quiz_question_tags_description','editor_class'=>'ays-textarea');
                    wp_editor($content,$editor_id,$settings);
                    ?>
                </div>
            </div>
            <!-- Qusetion tag description end -->
            <hr/>
            <!-- Qusetion tag status start -->
            <div class="form-group row">
                <div class="col-sm-2">
                    <label>
                        <?php echo __('Tag status', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose whether the question tag is active or not. If you choose Unpublished option, the question tag won’t be shown anywhere on your website',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>

                <div class="col-sm-10">
                    <div class="form-check form-check-inline" >
                        <input type="radio" id="ays_quiz_questio_tag_status_publish" <?php echo $readonly_option; ?> name="ays_quiz_quetion_tag_status" value="published" <?php echo $status == 'published' ? 'checked' : ''; ?>/>
                        <label class="form-check-label" for="ays_quiz_questio_tag_status_publish"> <?php echo __('Published', $this->plugin_name); ?> </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" id="ays_quiz_questio_tag_status_unpublish" <?php echo $readonly_option; ?> name="ays_quiz_quetion_tag_status" value="unpublished" <?php echo $status == 'unpublished' ? 'checked' : ''; ?>/>
                        <label class="form-check-label" for="ays_quiz_questio_tag_status_unpublish"> <?php echo __('Unpublished', $this->plugin_name); ?> </label>
                    </div>
                </div>
            </div>
            <!-- Qusetion tag status end -->
            <hr/>
            <?php
            if( $owner ){
                wp_nonce_field('question_tag_action', 'question_tag_action');
                $other_attributes = array( 'id' => 'ays-button-save' );
                submit_button( __( 'Save and close', $this->plugin_name ), 'primary ays-quiz-loader-banner', 'ays_submit', true, $other_attributes );
                $other_attributes = array(
                    'id' => 'ays-button-apply',
                    'title' => __('Ctrl + s', $this->plugin_name),
                    'data-toggle' => 'tooltip',
                    'data-delay'=> '{"show":"1000"}'
                );
                submit_button( __( 'Save', $this->plugin_name), 'ays-quiz-loader-banner', 'ays_apply', false, $other_attributes);
                echo $loader_iamge;
            }
            ?>
        </form>
    </div>
</div>
