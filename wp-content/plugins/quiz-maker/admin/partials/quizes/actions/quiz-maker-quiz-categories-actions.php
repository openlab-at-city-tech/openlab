<?php
$action = (isset($_GET['action'])) ? sanitize_text_field( $_GET['action'] ) : '';
$heading = '';
$loader_iamge = '';
$id = ( isset( $_GET['quiz_category'] ) ) ? absint( intval( $_GET['quiz_category'] ) ) : null;
    
$user_id = get_current_user_id();
$user = get_userdata($user_id);
$author = array(
    'id' => $user->ID,
    'name' => $user->data->display_name
);

$options = array(

);
$quiz_category = array(
    'id'            => '',
    'author_id'     => $user_id,
    'title'         => '',
    'description'   => '',
    'published'     => '1',
    'options'       => json_encode($options),
);
switch( $action ) {
    case 'add':
        $heading = __('Add new category', $this->plugin_name);
        break;
    case 'edit':
        $heading = __('Edit category', $this->plugin_name);
        $quiz_category = $this->quiz_categories_obj->get_quiz_category( $id );
        break;
}

$author_id = (isset( $quiz_category['author_id'] ) && $quiz_category['author_id'] != "") ? absint( $quiz_category['author_id'] ) : $user_id;
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

$nex_quiz_cat_id = "";
$prev_quiz_cat_id = "";
if ( isset( $id ) && !is_null( $id ) ) {
    $nex_quiz_cat = $this->get_next_or_prev_row_by_id( $id, "next", "aysquiz_quizcategories" );
    $nex_quiz_cat_id = (isset( $nex_quiz_cat['id'] ) && $nex_quiz_cat['id'] != "") ? absint( $nex_quiz_cat['id'] ) : null;

    $prev_quiz_cat = $this->get_next_or_prev_row_by_id( $id, "prev", "aysquiz_quizcategories" );
    $prev_quiz_cat_id = (isset( $prev_quiz_cat['id'] ) && $prev_quiz_cat['id'] != "") ? absint( $prev_quiz_cat['id'] ) : null;
}

// General Settings | options
$gen_options = ($this->settings_obj->ays_get_setting('options') === false) ? array() : json_decode( stripcslashes($this->settings_obj->ays_get_setting('options') ), true);

// WP Editor height
$quiz_wp_editor_height = (isset($gen_options['quiz_wp_editor_height']) && $gen_options['quiz_wp_editor_height'] != '') ? absint( sanitize_text_field($gen_options['quiz_wp_editor_height']) ) : 100;

$options = (isset($quiz_category['options']) && $quiz_category['options'] != '') ? json_decode($quiz_category['options'], true) : array();

if( isset( $_POST['ays_submit'] ) ) {
    $_POST['id'] = $id;
    $result = $this->quiz_categories_obj->add_edit_quiz_category( $_POST );
}
if(isset($_POST['ays_apply'])){
    $_POST["id"] = $id;
    $_POST['ays_change_type'] = 'apply';
    $this->quiz_categories_obj->add_edit_quiz_category($_POST);
}
$loader_iamge = "<span class='display_none ays_quiz_loader_box'><img src='". AYS_QUIZ_ADMIN_URL ."/images/loaders/loading.gif'></span>";

$quiz_category_title = (isset($quiz_category['title']) && $quiz_category['title'] != '') ? stripslashes( esc_attr($quiz_category['title']) ) : "";
$quiz_category_description = (isset($quiz_category['description']) && $quiz_category['description'] != '') ? stripslashes(wpautop($quiz_category['description'])) : "";
$quiz_category_published = (isset($quiz_category["published"]) && $quiz_category["published"] != '') ? stripslashes(esc_attr($quiz_category["published"])) : 1;

?>
<div class="wrap">
    <div class="container-fluid">
        <div class="ays-quiz-heading-box">
            <div class="ays-quiz-wordpress-user-manual-box">
                <a href="https://ays-pro.com/wordpress-quiz-maker-user-manual" target="_blank"><?php echo __("View Documentation", $this->plugin_name); ?></a>
            </div>
        </div>
        <h1><?php echo $heading; ?></h1>
        <div>
            <?php if($id !== null): ?>
            <div class="row">
                <div class="col-sm-3">
                    <label> <?php echo __( "Shortcode text for editor", $this->plugin_name ); ?> </label>
                </div>
                <div class="col-sm-9">
                    <p style="font-size:14px; font-style:italic;">
                        <?php echo __("To insert the Quiz category into a page, post or text widget, copy shortcode below and paste it at the desired place in the editor.", $this->plugin_name); ?><br>
                        <?php echo __( "The count attribute is required for random view type.", $this->plugin_name); ?>
                        <br>
                        <strong class="ays-quiz-shortcode-box" onClick="selectElementContents(this)" class="ays_help" data-toggle="tooltip" title="<?php echo __('Click for copy',$this->plugin_name);?>" style="font-size:16px; font-style:normal;"><?php echo "[ays_quiz_cat id='".$id."' display='all/random' count='5' layout='list/grid']"; ?></strong>
                    </p>
                </div>
            </div>
            <?php endif;?>
        </div>
        <hr/>
        <form class="ays-quiz-category-form" id="ays-quiz-category-form" method="post">
            <input type="hidden" class="quiz_wp_editor_height" value="<?php echo $quiz_wp_editor_height; ?>">
            <input type="hidden" name="ays_quiz_category_author" value="<?php echo $author_id; ?>">
            <div class="form-group row">
                <div class="col-sm-2">
                    <label for='ays-title'>
                        <?php echo __('Title', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Title of the category',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-10">
                    <input class='ays-text-input' id='ays-title' name='ays_title' type='text' value='<?php echo $quiz_category_title; ?>' <?php echo $readonly_option; ?>>
                </div>
            </div>

            <hr/>
            <div class="form-group row">
                <div class="col-sm-2">
                    <label for='ays-description'>
                        <?php echo __('Description', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Provide more information about the quiz category',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-10">
                <?php
                    $content = $quiz_category_description;
                    $editor_id = 'ays-quiz-description';
                    $settings = array(
                        'editor_height' => $quiz_wp_editor_height,
                        'textarea_name' => 'ays_description',
                        'editor_class' => 'ays-textarea'
                    );
                    wp_editor($content,$editor_id,$settings);
                ?>
                </div>
            </div>

            <hr/>
            <div class="form-group row">
                <div class="col-sm-2">
                    <label>
                        <?php echo __('Category status', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose whether the quiz category is active or not. If you choose Unpublished option, the quiz category won’t be shown anywhere on your website.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>

                <div class="col-sm-3">
                    <div class="form-check form-check-inline">
                        <input type="radio" id="ays-publish" name="ays_publish" <?php echo $readonly_option; ?> value="1" <?php echo ( $quiz_category_published == '1') ? 'checked' : ''; ?> />
                        <label class="form-check-label" for="ays-publish"> <?php echo __('Published', $this->plugin_name); ?> </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" id="ays-unpublish" name="ays_publish" <?php echo $readonly_option; ?> value="0" <?php echo ( $quiz_category_published  == '0' ) ? 'checked' : ''; ?> />
                        <label class="form-check-label" for="ays-unpublish"> <?php echo __('Unpublished', $this->plugin_name); ?> </label>
                    </div>
                </div>
            </div>

            <?php
            if( $owner ){
            ?>
            <hr/>
            <div class="form-group row ays-question-button-box">
                <div class="col-sm-8 ays-question-button-first-row" style="padding: 0;">
                <?php
                    wp_nonce_field('quiz_category_action', 'quiz_category_action');
                    $other_attributes = array( 'id' => 'ays-button' );
                    submit_button( __( 'Save and close', $this->plugin_name ), 'primary ays-quiz-loader-banner', 'ays_submit', true, $other_attributes );
                    $other_attributes = array(
                        'id' => 'ays_apply',
                        'title' => __('Ctrl + s', $this->plugin_name),
                        'data-toggle' => 'tooltip',
                        'data-delay'=> '{"show":"1000"}'
                    );
                    submit_button( __( 'Save', $this->plugin_name), 'ays-quiz-loader-banner', 'ays_apply', true, $other_attributes);
                    echo $loader_iamge;
                ?>
                </div>
                <div class="col-sm-4 ays-question-button-second-row">
                <?php
                    if ( $prev_quiz_cat_id != "" && !is_null( $prev_quiz_cat_id ) ) {

                        $other_attributes = array(
                            'id' => 'ays-question-prev-button',
                            'data-message' => __( 'Are you sure you want to go to the previous quiz category page?', $this->plugin_name),
                            'href' => sprintf( '?page=%s&action=%s&quiz_category=%d', esc_attr( $_REQUEST['page'] ), 'edit', absint( $prev_quiz_cat_id ) )
                        );
                        submit_button(__('Prev Quiz Category', $this->plugin_name), 'button button-primary ays_default_btn ays-button ays-quiz-category-next-button-class', 'ays_quiz_cat_prev_button', false, $other_attributes);
                    }

                    if ( $nex_quiz_cat_id != "" && !is_null( $nex_quiz_cat_id ) ) {

                        $other_attributes = array(
                            'id' => 'ays-quiz-category-next-button',
                            'data-message' => __( 'Are you sure you want to go to the next quiz category page?', $this->plugin_name),
                            'href' => sprintf( '?page=%s&action=%s&quiz_category=%d', esc_attr( $_REQUEST['page'] ), 'edit', absint( $nex_quiz_cat_id ) )
                        );
                        submit_button(__('Next Quiz Category', $this->plugin_name), 'button button-primary ays_default_btn ays-quiz-category-next-button-class ays-button', 'ays_quiz_cat_next_button', false, $other_attributes);
                    }
                ?>
                </div>
            </div>
            <?php
            }
            ?>
        </form>
    </div>
</div>
