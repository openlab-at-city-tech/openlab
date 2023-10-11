<?php
if(isset($_GET['tab'])){
    $ays_question_tab = esc_attr( $_GET['tab'] );
}else{
    $ays_question_tab = 'tab1';
}
$action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';
$heading = '';
$loader_iamge = '';
$image_text = __('Add Image', $this->plugin_name);
$bg_image_text = __('Add Image', $this->plugin_name);

$id = (isset($_GET['question'])) ? absint(intval($_GET['question'])) : null;
$user_id = get_current_user_id();
$user = get_userdata($user_id);
$author = array(
    'id' => $user->ID,
    'name' => $user->data->display_name
);


$gen_options = ($this->settings_obj->ays_get_setting('options') === false) ? array() : json_decode($this->settings_obj->ays_get_setting('options'), true);

$question_default_type = isset($gen_options['question_default_type']) && $gen_options['question_default_type'] != '' ? $gen_options['question_default_type'] : null;
$question_default_cat = isset($gen_options['question_default_cat']) && $gen_options['question_default_cat'] != '' ? $gen_options['question_default_cat'] : null;
$ays_answer_default_count = isset($gen_options['ays_answer_default_count']) ? $gen_options['ays_answer_default_count'] : null;
$keyword_default_max_value = isset($gen_options['keyword_default_max_value']) ? $gen_options['keyword_default_max_value'] : null;

// Enable question allow HTML
$gen_options['quiz_enable_question_allow_html'] = isset($gen_options['quiz_enable_question_allow_html']) ? sanitize_text_field( $gen_options['quiz_enable_question_allow_html'] ) : 'off';
$quiz_enable_question_allow_html = (isset($gen_options['quiz_enable_question_allow_html']) && sanitize_text_field( $gen_options['quiz_enable_question_allow_html'] ) == "on") ? true : false;

// WP Editor height
$quiz_wp_editor_height = (isset($gen_options['quiz_wp_editor_height']) && $gen_options['quiz_wp_editor_height'] != '') ? absint( sanitize_text_field($gen_options['quiz_wp_editor_height']) ) : 100;

if($question_default_type === null){
    $question_default_type = 'radio';
}
if($ays_answer_default_count === null){
    $ays_answer_default_count = '3';
}
if($question_default_cat === null){
    $question_default_cat = 1;
}

if($keyword_default_max_value === null){
    $keyword_default_max_value = 6;
}

$ays_answer_default_count = intval($ays_answer_default_count);

$options = array(
	'bg_image' => "",
    'use_html' => 'off',
    'enable_question_text_max_length' => 'off',
    'question_text_max_length' => '',
    'question_limit_text_type' => 'characters',
    'question_enable_text_message' => 'off',
    'enable_question_number_max_length' => 'off',
    'question_number_max_length' => '',
    'quiz_hide_question_text' => 'off',
    'enable_question_number_min_length' => 'off',
    'question_number_min_length' => '',
    'enable_max_selection_number' => 'off',
    'max_selection_number' => '',
    'quiz_question_note_message' => '',
    'enable_case_sensitive_text' => 'off',
    'enable_question_number_error_message' => 'off',
    'question_number_error_message' => '',
    'enable_min_selection_number' => 'off',
    'min_selection_number' => '',
    'quiz_disable_answer_stripslashes' => 'off',
);
$question = array(
    'category_id' => $question_default_cat,
    'author_id' => $user_id,
    'question' => '',
    'question_image' => '',
    'type' => $question_default_type,
    'published' => '',
    'user_explanation' => 'off',
    'wrong_answer_text' => '',
    'right_answer_text' => '',
    'explanation' => '',
    'create_date' => current_time( 'mysql' ),
    'not_influence_to_score' => 'off',
    'weight' => floatval(1),
    'options' => json_encode($options),
);

$answers = array();
$question_categories = array();
switch ($action) {
    case 'add':
        $heading = __('Add new question', $this->plugin_name);
        $question_categories = $this->questions_obj->get_published_question_categories();
        $get_question_tags = Quiz_Maker_Data::get_question_tags( true );
        break;
    case 'edit':
        $heading = __('Edit question', $this->plugin_name);
        $question = $this->questions_obj->get_question($id);
        $answers = $this->questions_obj->get_question_answers($id);
        $question_categories = $this->questions_obj->get_question_categories();
        $get_question_tags = Quiz_Maker_Data::get_question_tags( false );
        break;
}

$question_trash_status = (isset( $question['published'] ) && absint( $question['published'] ) == 2 ) ? true : false;

if( $question_trash_status ){
    $url = esc_url_raw( remove_query_arg( array( 'question', 'action', 'paged', 'page' ) ) ) . "?page=quiz-maker-questions";
    wp_redirect( $url );
}

$author_id = (isset( $question['author_id'] ) && $question['author_id'] != "") ? absint( $question['author_id'] ) : $user_id;
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

$loader_iamge = "<span class='display_none ays_quiz_loader_box'><img src='". AYS_QUIZ_ADMIN_URL ."/images/loaders/loading.gif'></span>";
$question['type'] = (isset($question['type']) && $question['type'] != '') ? $question['type'] : $question_default_type;
$question['category_id'] = (isset($question['category_id']) && $question['category_id'] != '') ? $question['category_id'] : $question_default_cat;

if (isset($_POST['ays_submit']) || isset($_POST['ays_submit_top'])) {
    $_POST["id"] = $id;
    $this->questions_obj->add_edit_questions($_POST);
}
if(isset($_POST['ays_apply_top']) || isset($_POST['ays_apply'])){
    $_POST["id"] = $id;
    $_POST['ays_change_type'] = 'apply';
    $this->questions_obj->add_edit_questions($_POST);
}
if(isset($_POST['ays_save_new_top']) || isset($_POST['ays_save_new'])){
    $_POST["id"] = $id;
    $_POST['ays_change_type'] = 'save_new';
    $this->questions_obj->add_edit_questions($_POST);
}

$nex_question_id = "";
$prev_question_id = "";
if ( isset( $id ) && !is_null( $id ) ) {
    $nex_question = $this->get_next_or_prev_row_by_id( $id, "next", "aysquiz_questions" );
    $nex_question_id = (isset( $nex_question['id'] ) && $nex_question['id'] != "") ? absint( $nex_question['id'] ) : null;

    $prev_question = $this->get_next_or_prev_row_by_id( $id, "prev", "aysquiz_questions" );
    $prev_question_id = (isset( $prev_question['id'] ) && $prev_question['id'] != "") ? absint( $prev_question['id'] ) : null;
}

$q_question_title = (isset( $question["question"] ) && $question["question"] != "") ? stripslashes( htmlspecialchars_decode( $question["question"] ) ) : "";

$question_published = (isset( $question["published"] ) && $question["published"] != "") ? absint($question["published"]) : 1;

$question_image = (isset( $question['question_image'] ) && $question['question_image']) ? $question['question_image'] : "";

$style = null;
$bg_style = null;
if ($question_image != '') {
    $style = "display: block;";
    $image_text = __('Edit Image', $this->plugin_name);
}
$question_create_date = (isset($question['create_date']) && $question['create_date'] != '') ? $question['create_date'] : "0000-00-00 00:00:00";
if ( isset( $question['options'] ) && $question['options'] != "" ) {
    $options = json_decode($question['options'], true);
}

if (isset($options['bg_image']) && $options['bg_image'] != '') {
	$bg_style = "display: block;";
	$bg_image_text = __('Edit Image', $this->plugin_name);
}

$question_types = array(
    "radio"         => __("Radio", $this->plugin_name),
    "checkbox"      => __("Checkbox( Multiple )", $this->plugin_name),
    "select"        => __("Dropdown", $this->plugin_name),
    "text"          => __("Text", $this->plugin_name),
    "short_text"    => __("Short Text", $this->plugin_name),
    "number"        => __("Number", $this->plugin_name),
    "date"          => __("Date", $this->plugin_name),
    "custom"        => __("Custom (Banner)", $this->plugin_name),
    "true_or_false" => __("True/False", $this->plugin_name),
    "fill_in_blank" => __("Fill in the blanks", $this->plugin_name),
    "matching"      => __("Matching", $this->plugin_name),
);

$question_types_icon_url = array(
    "radio"             => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-radio-type.svg",
    "checkbox"          => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-checkbox-type.svg",
    "select"            => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-dropdown-type.svg",
    "text"              => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-text-type.svg",
    "short_text"        => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-short-text-type.svg",
    "number"            => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-number-type.svg",
    "date"              => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-date-type.svg",
    "true_or_false"     => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-true-or-false-type.svg",
    "custom"            => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-custom-type.svg",
    "fill_in_blank"     => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-fill-in-blank-type.svg",
    "matching"          => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-matching-type.svg",
);

$true_or_false_arr = array(
    __("True", $this->plugin_name),
    __("False", $this->plugin_name),
);
$text_types = array( "text", "number", "short_text", "date", "fill_in_blank" );
$only_text_types = array( "text","short_text", "fill_in_blank" );
$only_number_types = array( "number" );
$not_multiple_text_types = array( "number", "date" );
switch ($question["type"]) {
    case "number":
        $question_type = 'number';
        break;
    case "text":
        $question_type = 'radio';
        break;
    case "checkbox":
        $question_type = 'checkbox';
        break;
    case "true_or_false":
        $question_type = 'radio';
        $ays_answer_default_count = 2;
        break;
    default:
        $question_type = 'radio';
        break;
}
$is_text_type = in_array($question["type"], $text_types);
$is_only_text_type = in_array($question["type"], $only_text_types);
$is_only_number_type = in_array($question["type"], $only_number_types);
$text_type = ($is_text_type && !in_array($question["type"], $not_multiple_text_types) ) ? true : false;
$only_checkbox_type = array( "checkbox" );
$is_only_checkbox_type = in_array($question["type"], $only_checkbox_type);


$question["user_explanation"] = (isset($question["user_explanation"]) && $question["user_explanation"] != "") ? $question["user_explanation"] : "off";

$question['not_influence_to_score'] = ! isset($question['not_influence_to_score']) ? 'off' : $question['not_influence_to_score'];
$not_influence_to_score = (isset($question['not_influence_to_score']) && $question['not_influence_to_score'] == 'on') ? true : false;

$question['weight'] = ! isset($question['weight']) ? floatval(1) : $question['weight'];
$question_weight = isset($question['weight']) && $question['weight'] != '' ? $question['weight'] : floatval(1);
$answers_weight = isset($question['answers_weight']) && $question['answers_weight'] != '' ? $question['answers_weight'] : floatval(0);

// Use HTML for answers
$options['use_html'] = ! isset($options['use_html']) ? 'off' : $options['use_html'];
$use_html = (isset($options['use_html']) && $options['use_html'] == 'on') ? true : false;

if ($quiz_enable_question_allow_html) {
    if ($action == 'add') {
        $use_html = true;
    }
}

$show_for_video_type = ($question["type"] == 'video') ? true : false;

$is_custom_type = false;
$custom_types = array( "custom" );
if(in_array($question["type"], $custom_types)){
    $is_custom_type = true;
}

$is_mixed_type = false;
$mixed_types = array( "matching" );
if(in_array($question["type"], $mixed_types)){
    $is_mixed_type = true;
}

$question_title = (isset($question['question_title']) && $question['question_title']) ? esc_attr( $question['question_title'] ) : '';
$question_title = stripslashes( $question_title );

$keyword_arr = $this->ays_quiz_generate_keyword_array( $keyword_default_max_value );

// Maximum length of a text field
$options['enable_question_text_max_length'] = isset($options['enable_question_text_max_length']) ? sanitize_text_field( $options['enable_question_text_max_length'] ) : 'off';
$enable_question_text_max_length = (isset($options['enable_question_text_max_length']) && $options['enable_question_text_max_length'] == 'on') ? true : false;

// Length
$question_text_max_length = ( isset($options['question_text_max_length']) && $options['question_text_max_length'] != '' ) ? absint( intval( $options['question_text_max_length'] ) ) : '';

// Limit by
$question_limit_text_type = ( isset($options['question_limit_text_type']) && $options['question_limit_text_type'] != '' ) ? sanitize_text_field( $options['question_limit_text_type'] ) : 'characters';

// Show the counter-message
$options['question_enable_text_message'] = isset($options['question_enable_text_message']) ? sanitize_text_field( $options['question_enable_text_message'] ) : 'off';
$question_enable_text_message = (isset($options['question_enable_text_message']) && $options['question_enable_text_message'] == 'on') ? true : false;

// Hide question text on the front-end
$options['quiz_hide_question_text'] = isset($options['quiz_hide_question_text']) ? sanitize_text_field( $options['quiz_hide_question_text'] ) : 'off';
$quiz_hide_question_text = (isset($options['quiz_hide_question_text']) && $options['quiz_hide_question_text'] == 'on') ? true : false;

// Maximum length of a number field
$options['enable_question_number_max_length'] = isset($options['enable_question_number_max_length']) ? sanitize_text_field( $options['enable_question_number_max_length'] ) : 'off';
$enable_question_number_max_length = (isset($options['enable_question_number_max_length']) && sanitize_text_field( $options['enable_question_number_max_length'] ) == 'on') ? true : false;

// Length
$question_number_max_length = ( isset($options['question_number_max_length']) && sanitize_text_field( $options['question_number_max_length'] ) != '' ) ? intval( sanitize_text_field( $options['question_number_max_length'] ) ) : '';

// Enable maximum selection number
$options['enable_max_selection_number'] = isset($options['enable_max_selection_number']) ? sanitize_text_field( $options['enable_max_selection_number'] ) : 'off';
$enable_max_selection_number = (isset($options['enable_max_selection_number']) && sanitize_text_field( $options['enable_max_selection_number'] ) == 'on') ? true : false;

// Max value
$max_selection_number = ( isset($options['max_selection_number']) && $options['max_selection_number'] != '' ) ? intval( sanitize_text_field( $options['max_selection_number'] ) ) : '';

//Tag IDs
$question_tag_ids = (isset($question['tag_id']) && $question['tag_id'] != '') ? explode(',',$question['tag_id']) : '';

// Note text
$quiz_question_note_message = ( isset( $options['quiz_question_note_message']) && $options['quiz_question_note_message'] != '' ) ? stripslashes( $options['quiz_question_note_message'] ) : '';

// Enable case sensitive text
$options['enable_case_sensitive_text'] = isset($options['enable_case_sensitive_text']) ? sanitize_text_field( $options['enable_case_sensitive_text'] ) : 'off';
$enable_case_sensitive_text = (isset($options['enable_case_sensitive_text']) && sanitize_text_field( $options['enable_case_sensitive_text'] ) == 'on') ? true : false;

// Enable minimum selection number
$options['enable_min_selection_number'] = isset($options['enable_min_selection_number']) ? sanitize_text_field( $options['enable_min_selection_number'] ) : 'off';
$enable_min_selection_number = (isset($options['enable_min_selection_number']) && sanitize_text_field( $options['enable_min_selection_number'] ) == 'on') ? true : false;

// Min value
$min_selection_number = ( isset($options['min_selection_number']) && $options['min_selection_number'] != '' ) ? intval( sanitize_text_field( $options['min_selection_number'] ) ) : '';

// Minimum length of a number field
$options['enable_question_number_min_length'] = isset($options['enable_question_number_min_length']) ? sanitize_text_field( $options['enable_question_number_min_length'] ) : 'off';
$enable_question_number_min_length = (isset($options['enable_question_number_min_length']) && sanitize_text_field( $options['enable_question_number_min_length'] ) == 'on') ? true : false;

// Length
$question_number_min_length = ( isset($options['question_number_min_length']) && sanitize_text_field( $options['question_number_min_length'] ) != '' ) ? intval( sanitize_text_field( $options['question_number_min_length'] ) ) : '';

// Show error message
$options['enable_question_number_error_message'] = isset($options['enable_question_number_error_message']) ? sanitize_text_field( $options['enable_question_number_error_message'] ) : 'off';
$enable_question_number_error_message = (isset($options['enable_question_number_error_message']) && sanitize_text_field( $options['enable_question_number_error_message'] ) == 'on') ? true : false;

// Message
$question_number_error_message = ( isset($options['question_number_error_message']) && sanitize_text_field( $options['question_number_error_message'] ) != '' ) ? stripslashes( esc_attr( sanitize_text_field( $options['question_number_error_message'] ) ) ) : '';

// Enable strip slashes for questions
$options['quiz_enable_question_stripslashes'] = isset($options['quiz_enable_question_stripslashes']) ? sanitize_text_field( $options['quiz_enable_question_stripslashes'] ) : 'off';
$quiz_enable_question_stripslashes = (isset($options['quiz_enable_question_stripslashes']) && $options['quiz_enable_question_stripslashes'] == 'on') ? true : false;

// Disable strip slashes for answers
$options['quiz_disable_answer_stripslashes'] = isset($options['quiz_disable_answer_stripslashes']) ? sanitize_text_field( $options['quiz_disable_answer_stripslashes'] ) : 'off';
$quiz_disable_answer_stripslashes = (isset($options['quiz_disable_answer_stripslashes']) && $options['quiz_disable_answer_stripslashes'] == 'on') ? true : false;

// Answer slug max ID
$answer_slug_max_id = (isset( $options['answer_slug_max_id'] ) && $options['answer_slug_max_id']) ? absint( esc_attr( $options['answer_slug_max_id'] ) ) : 1;

if( $action == "add" ){
    $answer_slug_max_id = $ays_answer_default_count;
}

// Incorrect matches for answers
$options['answer_incorrect_matches'] = isset($options['answer_incorrect_matches']) ? $options['answer_incorrect_matches'] : array();
$answer_incorrect_matches = isset($options['answer_incorrect_matches']) && !empty( $options['answer_incorrect_matches'] ) ? $options['answer_incorrect_matches'] : array();

?>

<div class="wrap">
    <div class="container-fluid">
        <hr/>
        <form method="post" id="ays-question-form">
            <input type="hidden" name="ays_question_tab" value="<?php echo esc_attr($ays_question_tab); ?>">
            <input type="hidden" name="ays_question_ctrate_date" value="<?php echo $question_create_date; ?>">
            <input type="hidden" name="ays_question_author" value="<?php echo $author_id; ?>">
            <input type="hidden" class="quiz_wp_editor_height" value="<?php echo $quiz_wp_editor_height; ?>">
            <div class="ays-quiz-heading-box">
                <div class="ays-quiz-wordpress-user-manual-box">
                    <a href="https://ays-pro.com/wordpress-quiz-maker-user-manual" target="_blank"><?php echo __("View Documentation", $this->plugin_name); ?></a>
                </div>
            </div>
            <h1 class="wp-heading-inline">
                <?php
                if( $owner ){
                    echo $heading;
                    $other_attributes = array('id' => 'ays-button-save-top');
                    submit_button(__('Save and close', $this->plugin_name), 'primary ays-button ays-quiz-loader-banner', 'ays_submit_top', false, $other_attributes);
                    $other_attributes = array('id' => 'ays-button-save-new-top');
                    submit_button(__('Save and new', $this->plugin_name), 'primary ays-button ays-quiz-loader-banner', 'ays_save_new_top', false, $other_attributes);
                    $other_attributes = array(
                        'id' => 'ays-button-apply-top',
                        'title' => __('Ctrl + s', $this->plugin_name),
                        'data-toggle' => 'tooltip',
                        'data-delay'=> '{"show":"1000"}'
                    );
                    submit_button(__('Save', $this->plugin_name), 'ays-button ays-quiz-loader-banner', 'ays_apply_top', false, $other_attributes);
                    echo $loader_iamge;
                }
                ?>
            </h1>
            <div class="nav-tab-wrapper">
                <a href="#tab1" data-tab="tab1" class="nav-tab <?php echo ($ays_question_tab == 'tab1') ? 'nav-tab-active' : ''; ?>">
                    <?php echo __("General", $this->plugin_name);?>
                </a>
                <a href="#tab2" data-tab="tab2" class="nav-tab <?php echo ($ays_question_tab == 'tab2') ? 'nav-tab-active' : ''; ?>">
                    <?php echo __("Settings", $this->plugin_name);?>
                </a>
            </div>
            
            <div id="tab1" class="ays-quiz-tab-content <?php echo ($ays_question_tab == 'tab1') ? 'ays-quiz-tab-content-active' : ''; ?>">
                <p class="ays-subtitle"><?php echo __('General Settings',$this->plugin_name)?></p>
                <hr/>
                <div class="ays-field-dashboard">
                    <label for='ays-question'><?php echo __('Question', $this->plugin_name); ?>
                        <a href="javascript:void(0)" class="add-question-image"><?php echo $image_text; ?></a>
                    </label>
                    <div class="ays-question-image-container" style="<?php echo $style; ?>">
                        <span class="ays-remove-question-img"></span>
                        <img src="<?php echo $question_image; ?>" id="ays-question-img"/>
                        <input type="hidden" name="ays_question_image" id="ays-question-image" value="<?php echo $question_image; ?>"/>
                    </div>
                    <?php
                    $content = $q_question_title;
                    $editor_id = 'ays-question';
                    $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'ays_question', 'editor_class' => 'ays-textarea');
                    wp_editor($content, $editor_id, $settings);
                    ?>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays-type">
                            <?php echo __('Question type', $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip" data-html="true"
                                title="<?php
                                    echo __('Choose the type of question.',$this->plugin_name) .
                                    "<ul style='list-style-type: circle;padding-left: 20px;'>".
                                        "<li>Radio - ". __('Multiple choice question with one Correct answer.',$this->plugin_name) ."</li>".
                                        "<li>Checkbox - ". __('Multiple choice question with multiple Correct answers.',$this->plugin_name) ."</li>".
                                        "<li>Dropdown - ". __('Multiple choice questions with one Correct answer, which will be displayed as Dropdown.',$this->plugin_name) ."</li>".
                                        "<li>Text</li>".
                                        "<li>Number</li>".
                                        "<li>Fill in the blanks</li>".
                                        "<li>Matching</li>".
                                    "</ul>";
                                ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <select id="ays-type" name="ays_question_type" <?php echo $disabled_option; ?>>
                            <option></option>
                            <?php
                                foreach($question_types as $type => $label):
                                $selected = $question["type"] == $type ? "selected" : "";
                            ?>
                            <option value="<?php echo $type; ?>" data-nkar="<?php echo $question_types_icon_url[ $type ]; ?>" <?php echo $selected; ?> ><?php echo $label; ?></option>
                            <?php
                                endforeach;
                            ?>
                        </select>
                    </div>
                </div>
                <hr class="hide_for_custom_type <?php echo ($is_custom_type) ? 'display_none' : ''; ?>" />
                <div class="hide_for_custom_type <?php echo ($is_custom_type) ? 'display_none' : ''; ?>">
                    <label class='ays-label' for="ays-answers-table">
                        <?php
                            if($is_text_type && $question["type"] != 'fill_in_blank'):
                        ?>
                        <?php echo __('Answer', $this->plugin_name); ?>
                        <?php elseif($question["type"] == "fill_in_blank"): ?>
                        <?php 
                            echo __('Answer', $this->plugin_name);
                            if( $owner ): ?>
                            <a href="javascript:void(0)" class="ays-add-fill-in-blank-answer">
                                <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>
                            </a>
                            <?php
                            endif; 
                        ?>
                        <?php
                            else:
                        ?>
                        <?php 
                            if( $question["type"] === 'matching' ) {
                                echo __( 'Choices', $this->plugin_name );
                            } else {
                                echo __( 'Answers', $this->plugin_name );
                            }
                        
                        if( $owner ){ ?>      
                            <a href="javascript:void(0)" class="ays-add-answer">
                                <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>
                            </a>
                        <?php }
                            endif;
                        ?>
                    </label>
                </div>

                <div class="ays-field-dashboard ays-table-wrap hide_for_custom_type <?php echo ($is_custom_type) ? 'display_none' : ''; ?>">
                    <table class="ays-answers-table" id="ays-answers-table" ays_default_count="<?php echo $ays_answer_default_count; ?>">
                        <thead>
                            <tr class="ui-state-default">
                                <?php if(! $is_text_type): ?>
                                <th class="th-150 removable ays-quiz-question-answer-ordering-row"><?php echo __('Ordering', $this->plugin_name); ?></th>
                                <?php if( $question["type"] !== 'matching' ): ?>
                                <th class="th-150 removable ays-quiz-question-answer-correct-row">
                                    <?php echo __('Correct', $this->plugin_name); ?>
                                    <a class="ays_help" data-toggle="tooltip"
                                        title="<?php echo __('If your question does not have any correct answer, please tick a random answer as the correct one for the system to be closed.',$this->plugin_name); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </th>
                                <?php endif; ?>
                                <?php endif; ?>
                                <th class="th-150 ays-weight-row ays-quiz-question-answer-weight-point-row <?php echo $question["type"] !== 'matching' ? '' : 'display_none' ?>"><?php echo __('Weight/Point', $this->plugin_name); ?></th>
                                <?php if( $question["type"] !== 'matching' ): ?>
                                <?php if( $question["type"] == 'checkbox' || $question["type"] == 'radio' || $question["type"] == 'select' || $question["type"] == 'true_or_false' ): ?>
                                <th class="th-150 removable ays-quiz-question-answer-keyword-row">
                                    <?php echo __('Keyword', $this->plugin_name); ?>
                                    <a class="ays_help" data-toggle="tooltip"
                                        title="<?php echo __('Please go to the General Settings > General > Default parameters for Quiz > Keyword default count for changing the default keyword count.',$this->plugin_name); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </th>
                                <?php endif; ?>
                                <?php endif; ?>
                                <th <?php echo ($is_text_type) ? 'class="th-650"' : 'class="ays-quiz-question-answer-answer-row" style="width:500px;"'; ?>><?php
                                    echo $question["type"] === 'matching' ? __('Choice', $this->plugin_name) : __('Answer', $this->plugin_name);
                                ?></th>
                                <?php if( $question["type"] === 'matching' ): ?>
                                <th class="th-350 removable"><?php echo __('Match', $this->plugin_name); ?></th>
                                <?php endif; ?>
                                <?php if(! $is_text_type): ?>
                                <?php if( $question["type"] !== 'matching' ): ?>
                                <th class="th-150 removable ays-quiz-question-answer-image-row"><?php echo __('Image', $this->plugin_name); ?></th>
                                <?php endif; ?>
                                <th class="th-150 removable ays-quiz-question-answer-delete-row"><?php echo __('Delete', $this->plugin_name); ?></th>
                                <?php endif; ?>
                                <?php if($is_text_type && $question["type"] != 'date'): ?>
                                <th class="th-350 reremoveable ays-quiz-question-answer-placeholder-row"><?php echo __('Placeholder', $this->plugin_name); ?></th>
                                <?php endif; ?>
                                <?php if($question["type"] == 'fill_in_blank'): ?>
                                <th class="th-150 reremoveable ays-quiz-question-answer-slug-row">
                                    <span><?php echo __('Slug', $this->plugin_name); ?></span>
                                    <input type="hidden" name="ays_answer_slug_max_id" class="ays-answer-slug-max-id" value="<?php echo $answer_slug_max_id; ?>"/>
                                </th>
                                <th class="th-150 removable ays-quiz-question-answer-delete-row"><?php echo __('Delete', $this->plugin_name); ?></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="<?php echo ($is_text_type) ? 'text_answer' : '';?> <?php echo ($question["type"] === 'matching') ? 'matching_answer' : ''; ?>">
                        <?php if (empty($answers)) : ?>
                        <?php
                            if($question["type"] == 'number'):
                            ?>
                            <tr class="ays-answer-row ui-state-default">
                                <td>
                                    <input type="text" name="ays-answer-weight[]" class="ays-answer-weight w-100" value="0"/>
                                </td>
                                <td>
                                    <input style="display:none;" class="ays-correct-answer" type="checkbox" name="ays-correct-answer[]" value="1" checked/>
                                    <input type="number" step="any" name="ays-correct-answer-value[]" class="ays-correct-answer-value" value=""/>
                                </td>
                                <td>
                                    <input type="text" name="ays-answer-placeholder[]" class="ays-correct-answer-value" value=""/>
                                </td>
                            </tr>
                            <?php
                            elseif($question["type"] == 'short_text'):
                            ?>
                            <tr class="ays-answer-row ui-state-default">
                                <td>
                                    <input type="text" name="ays-answer-weight[]" class="ays-answer-weight w-100" value="0"/>
                                </td>
                                <td>
                                    <input style="display:none;" class="ays-correct-answer" type="checkbox" name="ays-correct-answer[]" value="1" checked/>
                                    <input type="text" name="ays-correct-answer-value[]" class="ays-correct-answer-value" value=""/>
                                </td>
                                <td>
                                    <input type="text" name="ays-answer-placeholder[]" class="ays-correct-answer-value" value=""/>
                                </td>
                            </tr>
                            <?php
                            elseif($question["type"] == 'fill_in_blank'):
                                for ($ays_i=0; $ays_i < $ays_answer_default_count; $ays_i++) :
                                    $ays_even_or_not = ($ays_i%2 !=0) ? 'even' : '';

                                    $slug_id = $ays_i + 1;

                                    $ays_answer_slag_value = '{blank_'. $slug_id .'}';
                                ?>
                                <tr class="ays-answer-row ui-state-default <?php echo $ays_even_or_not; ?>">
                                    <td>
                                        <input type="text" name="ays-answer-weight[]" class="ays-answer-weight w-100" value="0"/>
                                    </td>
                                    <td>
                                        <input style="display:none;" class="ays-correct-answer" type="checkbox" name="ays-correct-answer[]" value="1" checked/>
                                        <input type="text" name="ays-correct-answer-value[]" class="ays-correct-answer-value" value=""/>
                                    </td>
                                    <td>
                                        <input type="text" name="ays-answer-placeholder[]" class="ays-correct-answer-value" value=""/>
                                    </td>
                                    <td class="ays-quiz-question-answer-slug-row">
                                        <input type="text" name="ays_answer_slug[]" class="ays-answer-slug-value" value="<?php echo $ays_answer_slag_value; ?>" readonly title="<?php echo __("Click for copy", $this->plugin_name); ?>" />
                                    </td>
                                    <td class="ays-quiz-question-answer-delete-row">
                                        <a href="javascript:void(0)" class="ays-delete-fill-in-blank-answer">
                                            <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php
                                endfor;
                            elseif($question["type"] == 'text'):
                            ?>
                            <tr class="ays-answer-row ui-state-default">
                                <td>
                                    <input type="text" name="ays-answer-weight[]" class="ays-answer-weight w-100" value="0"/>
                                </td>
                                <td>
                                    <input style="display:none;" class="ays-correct-answer" type="checkbox" name="ays-correct-answer[]" value="1" checked/>
                                    <textarea type="text" name="ays-correct-answer-value[]" class="ays-correct-answer-value"></textarea>
                                </td>
                                <td>
                                    <input type="text" name="ays-answer-placeholder[]" class="ays-correct-answer-value" value=""/>
                                </td>
                            </tr>
                            <?php
                            elseif($question["type"] == 'date'):
                            ?>
                            <tr class="ays-answer-row ui-state-default">
                                <td>
                                    <input type="text" name="ays-answer-weight[]" class="ays-answer-weight w-100" value="0"/>
                                </td>
                                <td>
                                    <input style="display:none;" class="ays-correct-answer" type="checkbox" name="ays-correct-answer[]" value="1" checked/>
                                    <input type="date" name="ays-correct-answer-value[]" class="ays-date-input ays-correct-answer-value" placeholder="<?php echo "e. g. " . current_time( 'Y-m-d' ); ?>">
                                </td>
                            </tr>
                            <?php
                            elseif($question["type"] == 'matching'):
                                for ($ays_i = 0; $ays_i < 3; $ays_i++) :
                                $ays_even_or_not = ( $ays_i % 2 !== 0) ? 'even' : '';
                                ?>
                                <tr class="ays-answer-row ui-state-default <?php echo $ays_even_or_not; ?>">
                                    <td class="ays-quiz-question-answer-ordering-row"><i class="ays_fa ays_fa_arrows" aria-hidden="true"></i></td>
                                    <td class="ays-quiz-question-answer-answer-row">
                                        <input type="text" name="ays-correct-answer-value[]" class="ays-correct-answer-value" value="Choice <?php echo $ays_i + 1; ?>"/>
                                    </td>
                                    <td class="ays-quiz-question-answer-answer-row">
                                        <input type="text" name="ays-correct-answer-value-match[]" class="ays-correct-answer-value" value="Match <?php echo $ays_i + 1; ?>"/>
                                    </td>
                                    <td class="ays-quiz-question-answer-delete-row">
                                        <a href="javascript:void(0)" class="ays-delete-answer">
                                            <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                endfor;
                            else:
                                for ($ays_i=0; $ays_i < $ays_answer_default_count; $ays_i++) :
                                    $ays_even_or_not = ($ays_i%2 !=0) ? 'even' : '';
                                    $true_or_false_val = '';
                                    if ( $question["type"] == 'true_or_false' ) {
                                        if ( isset( $true_or_false_arr[ $ays_i ] ) ) {
                                            $true_or_false_val = $true_or_false_arr[ $ays_i ];
                                        }
                                    }
                                ?>
                                <tr class="ays-answer-row ui-state-default <?php echo $ays_even_or_not; ?>">
                                    <td class="ays-quiz-question-answer-ordering-row"><i class="ays_fa ays_fa_arrows" aria-hidden="true"></i></td>
                                    <td class="ays-quiz-question-answer-correct-row">
                                        <span>
                                            <input type="<?php echo $question_type; ?>" id="ays-correct-answer-<?php echo $ays_i+1; ?>" class="ays-correct-answer" name="ays-correct-answer[]" value="<?php echo $ays_i+1; ?>"/>
                                            <label for="ays-correct-answer-<?php echo $ays_i+1; ?>"></label>
                                        </span>
                                    </td>
                                    <td class="ays-quiz-question-answer-weight-point-row">
                                        <input type="text" name="ays-answer-weight[]" class="ays-answer-weight" value="0"/>
                                    </td>
                                    <td class="ays-quiz-question-answer-keyword-row">
                                        <select name="ays_quiz_keywords[]" class="ays_quiz_keywords">
                                            <?php
                                            $keyword_content = '';
                                            foreach ($keyword_arr as $key => $answer_keyword) {
                                                $keyword_content .= '<option value="'.$answer_keyword.'">'.$answer_keyword.'</option>';
                                            }

                                            echo $keyword_content;
                                            ?>
                                        </select>
                                    </td>
                                    <td class="ays-quiz-question-answer-answer-row">
                                        <input type="text" name="ays-correct-answer-value[]" class="ays-correct-answer-value" value="<?php echo $true_or_false_val; ?>"/>
                                    </td>
                                    <td class="ays-quiz-question-answer-image-row">
                                        <label class='ays-label' for='ays-answer'><a href="javascript:void(0)" class="add-answer-image" style="display:block;"><?php echo __('Add',$this->plugin_name)?></a></label>
                                        <div class="ays-answer-image-container ays-answer-image-container-div" style="display:none;">
                                            <span class="ays-remove-answer-img"></span>
                                            <img src="" class="ays-answer-img"/>
                                            <input type="hidden" name="ays_answer_image[]" class="ays-answer-image-path" value=""/>
                                        </div>
                                    </td>
                                    <td class="ays-quiz-question-answer-delete-row">
                                        <a href="javascript:void(0)" class="ays-delete-answer">
                                            <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                endfor;
                            endif;
                        ?>
                        <?php
                        else:
                            foreach ($answers as $index => $answer) {
                                $class = (($index + 1) % 2 == 0) ? "even" : "";
                                $answer_text =  __('Add',$this->plugin_name);
                                $answer_weight =  $answer['weight'];
                                $answer_slag =  (isset( $answer['slug'] ) && $answer['slug'] != "") ? $answer['slug'] : "";

                                $answer_options = isset( $answer['options'] ) && ! empty( $answer['options'] ) ? $answer['options'] : '';
                                $answer_options = json_decode( $answer_options, true );
                                if ( ! $answer_options ) {
                                    $answer_options = array();
                                }

                                switch ($question["type"]) {
                                    case "number":
                                        $question_type = 'number';
                                        break;
                                    case "text":
                                        $question_type = 'radio';
                                        break;
                                    case "short_text":
                                    case "fill_in_blank":
                                        $question_type = 'text';
                                        break;
                                    case "checkbox":
                                        $question_type = 'checkbox';
                                        break;
                                    default:
                                        $question_type = 'radio';
                                        break;
                                }
                                ?>
                                <tr class="ays-answer-row ui-state-default <?php echo $class; ?>">
                                    <?php
                                        if($question["type"] == 'number'):
                                    ?>
                                    <td>
                                        <input type="text" <?php echo $readonly_option; ?> name="ays-answer-weight[]" class="ays-answer-weight w-100" value="<?php echo $answer_weight; ?>"/>
                                    </td>
                                    <td>
                                        <input style="display:none;" class="ays-correct-answer" type="checkbox" name="ays-correct-answer[]" value="1" checked/>
                                        <input type="<?php echo $question_type; ?>" <?php echo $readonly_option; ?> step="any" name="ays-correct-answer-value[]" class="ays-correct-answer-value" value="<?php echo esc_attr( stripslashes( $answer["answer"] ) ); ?>"/>
                                    </td>
                                    <td>
                                        <input type="text" name="ays-answer-placeholder[]" <?php echo $readonly_option; ?> class="ays-correct-answer-value" value="<?php echo esc_attr( stripslashes( $answer["placeholder"] ) ); ?>"/>
                                    </td>
                                    <?php
                                        elseif($question["type"] == 'short_text'):
                                    ?>
                                    <td>
                                        <input type="text" name="ays-answer-weight[]" <?php echo $readonly_option; ?> class="ays-answer-weight w-100" value="<?php echo $answer_weight; ?>"/>
                                    </td>
                                    <td>
                                        <input style="display:none;" class="ays-correct-answer" type="checkbox" name="ays-correct-answer[]" value="1" checked/>
                                        <input type="<?php echo $question_type; ?>" name="ays-correct-answer-value[]" <?php echo $readonly_option; ?> class="ays-correct-answer-value" value="<?php echo esc_attr( stripslashes( $answer["answer"] ) ); ?>"/>
                                    </td>
                                    <td>
                                        <input type="text" name="ays-answer-placeholder[]" <?php echo $readonly_option; ?> class="ays-correct-answer-value" value="<?php echo esc_attr( stripslashes( $answer["placeholder"] ) ); ?>"/>
                                    </td>
                                    <?php
                                        elseif($question["type"] == 'fill_in_blank'):
                                    ?>
                                    <td>
                                        <input type="text" name="ays-answer-weight[]" <?php echo $readonly_option; ?> class="ays-answer-weight w-100" value="<?php echo $answer_weight; ?>"/>
                                    </td>
                                    <td>
                                        <input style="display:none;" class="ays-correct-answer" type="checkbox" name="ays-correct-answer[]" value="1" checked/>
                                        <input type="<?php echo $question_type; ?>" name="ays-correct-answer-value[]" <?php echo $readonly_option; ?> class="ays-correct-answer-value" value="<?php echo esc_attr( stripslashes( $answer["answer"] ) ); ?>"/>
                                    </td>
                                    <td>
                                        <input type="text" name="ays-answer-placeholder[]" <?php echo $readonly_option; ?> class="ays-correct-answer-value" value="<?php echo esc_attr( stripslashes( $answer["placeholder"] ) ); ?>"/>
                                    </td>
                                    <td class="ays-quiz-question-answer-slug-row">
                                        <input type="text" name="ays_answer_slug[]" class="ays-answer-slug-value" value="<?php echo esc_attr( stripslashes( $answer_slag ) ); ?>" readonly title="<?php echo __("Click for copy", $this->plugin_name); ?>" />
                                    </td>
                                    <td class="ays-quiz-question-answer-delete-row">
                                        <a href="javascript:void(0)" class="ays-delete-fill-in-blank-answer">
                                            <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                    <?php
                                        elseif($question["type"] == 'text'):
                                    ?>
                                    <td>
                                        <input type="text" name="ays-answer-weight[]" <?php echo $readonly_option; ?> class="ays-answer-weight w-100" value="<?php echo $answer_weight; ?>"/>
                                    </td>
                                    <td>
                                        <input style="display:none;" class="ays-correct-answer" type="checkbox" name="ays-correct-answer[]" value="1" checked/>
                                        <textarea type="text" name="ays-correct-answer-value[]" <?php echo $readonly_option; ?> class="ays-correct-answer-value"><?php echo esc_attr( stripslashes( $answer["answer"] ) ); ?></textarea>
                                    </td>
                                    <td>
                                        <input type="text" name="ays-answer-placeholder[]" <?php echo $readonly_option; ?> class="ays-correct-answer-value" value="<?php echo esc_attr( stripslashes( $answer["placeholder"] ) ); ?>"/>
                                    </td>
                                    <?php
                                    elseif($question["type"] == 'date'):
                                    ?>
                                    <td>
                                        <input type="text" name="ays-answer-weight[]" <?php echo $readonly_option; ?> class="ays-answer-weight w-100" value="<?php echo $answer_weight; ?>"/>
                                    </td>
                                    <td>
                                        <input style="display:none;" class="ays-correct-answer" type="checkbox" name="ays-correct-answer[]" value="1" checked/>
                                        <input type="date" name="ays-correct-answer-value[]" <?php echo $readonly_option; ?> class="ays-date-input ays-correct-answer-value" placeholder="<?php echo "e. g. " . current_time( 'Y-m-d' ); ?>" value="<?php echo esc_attr( stripslashes( $answer["answer"] ) ); ?>">
                                    </td>
                                    <?php
                                        elseif($question["type"] == 'matching'):
                                            $match = isset( $answer_options['correct_match'] ) && $answer_options['correct_match'] ? $answer_options['correct_match'] : '';
                                    ?>
                                    <td class="ays-quiz-question-answer-ordering-row"><i class="ays_fa ays_fa_arrows" aria-hidden="true"></i></td>
                                    <td class="ays-quiz-question-answer-answer-row">
                                        <input type="text" name="ays-correct-answer-value[]" class="ays-correct-answer-value" value="<?php echo esc_attr( stripslashes( $answer["answer"] ) ); ?>"/>
                                    </td>
                                    <td class="ays-quiz-question-answer-answer-row">
                                        <input type="text" name="ays-correct-answer-value-match[]" class="ays-correct-answer-value" value="<?php echo esc_attr( stripslashes( $match ) ); ?>"/>
                                    </td>
                                    <td class="ays-quiz-question-answer-delete-row">
                                        <a href="javascript:void(0)" class="ays-delete-answer">
                                            <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                    <?php
                                        else:
                                    ?>
                                    <td class="ays-quiz-question-answer-ordering-row"><i class="ays_fa ays_fa_arrows" aria-hidden="true"></i></td>
                                    <td class="ays-quiz-question-answer-correct-row">
                                        <span>
                                            <input type="<?php echo $question_type; ?>" <?php echo $readonly_option; ?> id="ays-correct-answer-<?php echo($index + 1); ?>" class="ays-correct-answer" name="ays-correct-answer[]" value="<?php echo($index + 1); ?>" <?php echo ($answer["correct"] == 1) ? "checked" : ""; ?>/>
                                            <label for="ays-correct-answer-<?php echo($index + 1); ?>"></label>
                                        </span>
                                    </td>
                                    <td class="ays-quiz-question-answer-weight-point-row">
                                        <input type="text" name="ays-answer-weight[]" class="ays-answer-weight" <?php echo $readonly_option; ?> value="<?php echo $answer_weight; ?>"/>
                                    </td>
                                    <td class="ays-quiz-question-answer-keyword-row">
                                        <select name="ays_quiz_keywords[]" class="ays_quiz_keywords">
                                            <?php
                                            $keyword_content = '';
                                            foreach ($keyword_arr as $key => $answer_keyword) {
                                                $selected = '';

                                                if (isset($answer['keyword']) && $answer['keyword'] == $answer_keyword) {
                                                    $selected = 'selected';
                                                }

                                                $keyword_content .= '<option value="'.$answer_keyword.'" '. $selected .'>'.$answer_keyword.'</option>';
                                            }

                                            echo $keyword_content;
                                            ?>
                                        </select>
                                    </td>
                                    <td class="ays-quiz-question-answer-answer-row"><input type="text" name="ays-correct-answer-value[]" class="ays-correct-answer-value" <?php echo $readonly_option; ?> value="<?php echo esc_attr( stripslashes( $answer["answer"] ) ); ?>"/></td>
                                    <td class="ays-quiz-question-answer-image-row">
                                        <label class='ays-label' for='ays-answer'><a href="javascript:void(0)" class="add-answer-image" <?php echo (is_null($answer['image'])||$answer['image']=='') ? "style=display:block;":"style=display:none"?>><?php echo $answer_text; ?></a></label>
                                        <div class="ays-answer-image-container ays-answer-image-container-div" <?php echo (is_null($answer['image'])||$answer['image']=='') ? "style=display:none; ":"style=display:block"?>>
                                            <span class="ays-remove-answer-img"></span>
                                            <img src="<?php echo $answer['image']; ?>" class="ays-answer-img"/>
                                            <input type="hidden" name="ays_answer_image[]" class="ays-answer-image-path" <?php echo $readonly_option; ?> value="<?php echo $answer['image']; ?>"/>
                                        </div>
                                    </td>
                                    <td class="ays-quiz-question-answer-delete-row">
                                        <a href="javascript:void(0)" class="ays-delete-answer">
                                            <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                    <?php
                                        endif;
                                    ?>
                                </tr>
                                <?php
                            }
                        endif;
                        ?>
                        </tbody>
                    </table>
                    <div class="ays-answers-toolbar-bottom <?php echo ($is_text_type) ? 'display_none' : ''; ?>" style="padding:5px;padding-top:10px;">
                        <label class='ays-label ays-add-answer-first-label' style="margin:0;" for="ays-answers-table">
                        <?php if($is_text_type && $question["type"] != 'fill_in_blank'): ?>
                        <?php echo __('Answer', $this->plugin_name); ?>
                        <?php elseif($question["type"] == "fill_in_blank"): ?>
                        <?php 
                            echo __('Answer', $this->plugin_name);
                            if( $owner ): ?>
                            <a href="javascript:void(0)" class="ays-add-fill-in-blank-answer">
                                <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>
                            </a>
                            <?php
                            endif; 
                        ?>
                        <?php else: ?>
                        <?php

                            if( $question["type"] === 'matching' ) {
                                echo __( 'Choices', $this->plugin_name );
                            } else {
                                echo __( 'Answers', $this->plugin_name );
                            }

                            if( $owner ){?>
                            <a href="javascript:void(0)" class="ays-add-answer">
                                <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>
                            </a>
                            <?php }
                            endif; 
                        ?>
                        </label>
                        <?php if( $question["type"] !== 'matching' ): ?>
                        <span class="ays_divider_left" style="padding-bottom: 10px;padding-top: 5px;margin: 0 15px;"></span>
                        <label class='ays-label use_html' style="margin:0;<?php echo ($question["type"] == 'select') ? 'display:none;' : ''; ?>">
                            <?php echo __( "Use HTML for answers", $this->plugin_name ); ?>
                            <a class="ays_help" style="margin-right:15px;" data-toggle="tooltip" title="<?php echo __('Allowed tags list',$this->plugin_name) . ": <br>, <b>, <em>, <span>, <mark>, <del>, <ins>, <sup>, <sub>, <strong>, <code>, <samp>, <kbd>, <var>, <q>"; ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                            <input type="checkbox" name="ays-use-html" value="on" <?php echo $use_html ? "checked" : ""; ?> <?php echo $readonly_option; ?>>
                        </label>
                        <?php endif; ?>
                    </div>
                    <div class="ays-text-answers-desc <?php echo ($text_type) ? '' : 'display_none'; ?>" style="padding:5px;padding-top:15px;">
                        <blockquote>
                            <p style="margin:0px;"><?php echo __( "For inserting multiple possible correct answers, please use delimeter %%%.", $this->plugin_name ); ?></p>
                            <p style="margin:0px;"><?php echo __( "Example:", $this->plugin_name ); ?> <strong>US%%%USA%%%United States</strong></p>
                        </blockquote>
                    </div>
                    <hr class="show_for_text_type <?php echo ($is_only_text_type && $question["type"] != "fill_in_blank") ? '' : 'display_none'; ?>"/>
                    <div class="form-group row ays_toggle_parent show_for_text_type <?php echo ($is_only_text_type && $question["type"] != "fill_in_blank") ? '' : 'display_none'; ?>" style="margin: 0;">
                        <div class="col-sm-4" style="padding-left: 0;">
                            <label for="ays_enable_question_text_max_length">
                                <?php echo __('Maximum length of a text field', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __( 'Restrict the number of characters/words to be inserted in the text field by the user.' , $this->plugin_name ); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-1">
                            <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_question_text_max_length" name="ays_enable_question_text_max_length" value="on" <?php echo ($enable_question_text_max_length) ? 'checked' : ''; ?>>
                        </div>
                        <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo ($enable_question_text_max_length) ? '' : 'display_none'; ?>">
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays_question_limit_text_type">
                                        <?php echo __('Limit by', $this->plugin_name); ?>
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose your preferred type of limitation.',$this->plugin_name); ?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <select class="ays-text-input ays-text-input-select" id="ays_question_limit_text_type" name="ays_question_limit_text_type" <?php echo $disabled_option; ?>>
                                        <option value='characters' <?php echo ($question_limit_text_type == 'characters') ? 'selected' : '' ?> ><?php echo __( 'Characters' , $this->plugin_name ); ?></option>
                                        <option value='words' <?php echo ($question_limit_text_type == 'words') ? 'selected' : '' ?> ><?php echo __( 'Words' , $this->plugin_name ); ?></option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays_question_text_max_length">
                                        <?php echo __('Length', $this->plugin_name); ?>
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Indicate the length of the characters/words.',$this->plugin_name); ?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="number" id="ays_question_text_max_length" class="ays-text-input" name="ays_question_text_max_length" value="<?php echo $question_text_max_length; ?>" <?php echo $readonly_option; ?>>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays_question_enable_text_message">
                                        <?php echo __('Show word/character counter', $this->plugin_name); ?>
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Tick the checkbox and the live box will appear under the text field. It will indicate the current state of word/character usage.',$this->plugin_name); ?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" id="ays_question_enable_text_message" name="ays_question_enable_text_message" value="on" <?php echo ($question_enable_text_message) ? "checked" : ""; ?> />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="show_for_text_type <?php echo ($is_only_text_type && $question["type"] != "fill_in_blank") ? '' : 'display_none'; ?>"/>
                    <div class="form-group row show_for_text_type <?php echo ($is_only_text_type && $question["type"] != "fill_in_blank") ? '' : 'display_none'; ?>" style="margin: 0;">
                        <div class="col-sm-4" style="padding-left: 0;">
                            <label for="ays_enable_case_sensitive_text">
                                <?php echo __('Enable case sensitive text', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __( 'When this option is enabled, the user’s answer should be written in the particular form. For example, if the right answer is “true”, the “TRUE” will be counted as a wrong one.', $this->plugin_name ) ); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="checkbox" class="ays-enable-timer1" id="ays_enable_case_sensitive_text" name="ays_enable_case_sensitive_text" value="on" <?php echo ($enable_case_sensitive_text) ? 'checked' : ''; ?>>
                        </div>
                    </div>
                    <hr class="show_for_number_type <?php echo ($is_only_number_type) ? '' : 'display_none'; ?>"/>
                    <div class="form-group row ays_toggle_parent show_for_number_type <?php echo ($is_only_number_type) ? '' : 'display_none'; ?>" style="margin: 0;">
                        <div class="col-sm-4" style="padding-left: 0;">
                            <label for="ays_enable_question_number_max_length">
                                <?php echo __('Maximum value of a number field', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __( 'Give a maximum legal value to your number field. For example, if you give a 20 value to the field, then the user will be able to answer the question by writing a value less than or equal to 20.' , $this->plugin_name ); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-1">
                            <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_question_number_max_length" name="ays_enable_question_number_max_length" value="on" <?php echo ($enable_question_number_max_length) ? 'checked' : ''; ?>>
                        </div>
                        <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo ($enable_question_number_max_length) ? '' : 'display_none'; ?>">
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays_question_number_max_length">
                                        <?php echo __('Max value', $this->plugin_name); ?>
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify the maximum value allowed. ',$this->plugin_name); ?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="number" id="ays_question_number_max_length" class="ays-text-input" name="ays_question_number_max_length" value="<?php echo $question_number_max_length; ?>" <?php echo $disabled_option; ?>>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="show_for_number_type <?php echo ($is_only_number_type) ? '' : 'display_none'; ?>"/>
                    <div class="form-group row ays_toggle_parent show_for_number_type <?php echo ($is_only_number_type) ? '' : 'display_none'; ?>" style="margin: 0;">
                        <div class="col-sm-4" style="padding-left: 0;">
                            <label for="ays_enable_question_number_min_length">
                                <?php echo __('Minimum value of a number field', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __( 'Give a minimum legal value to your number field. For example, if you give a 20 value to the field, then the user will be able to answer the question by writing a value more than or equal to 20.' , $this->plugin_name ); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-1">
                            <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_question_number_min_length" name="ays_enable_question_number_min_length" value="on" <?php echo ($enable_question_number_min_length) ? 'checked' : ''; ?>>
                        </div>
                        <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo ($enable_question_number_min_length) ? '' : 'display_none'; ?>">
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays_question_number_min_length">
                                        <?php echo __('Min value', $this->plugin_name); ?>
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify the minimum value allowed. ',$this->plugin_name); ?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="number" id="ays_question_number_min_length" class="ays-text-input" name="ays_question_number_min_length" value="<?php echo $question_number_min_length; ?>" <?php echo $disabled_option; ?>>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="show_for_number_type <?php echo ($is_only_number_type) ? '' : 'display_none'; ?>"/>
                    <div class="form-group row ays_toggle_parent show_for_number_type <?php echo ($is_only_number_type) ? '' : 'display_none'; ?>" style="margin: 0;">
                        <div class="col-sm-4" style="padding-left: 0;">
                            <label for="ays_enable_question_number_error_message">
                                <?php echo __('Show error message', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __( 'When the option is enabled; on the Number Question Type, if in the answer box is typed something else besides the numbers. The "Error text" message will appear.' , $this->plugin_name ) ); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-1">
                            <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_question_number_error_message" name="ays_enable_question_number_error_message" value="on" <?php echo ($enable_question_number_error_message) ? 'checked' : ''; ?>>
                        </div>
                        <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo ($enable_question_number_error_message) ? '' : 'display_none'; ?>">
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays_question_number_error_message">
                                        <?php echo __('Message', $this->plugin_name); ?>
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Write the message, which you want to be shown on the front.',$this->plugin_name); ?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="text" id="ays_question_number_error_message" class="ays-text-input" name="ays_question_number_error_message" value="<?php echo $question_number_error_message; ?>" <?php echo $disabled_option; ?>>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="show_for_checkbox_type <?php echo ($is_only_checkbox_type) ? '' : 'display_none'; ?>"/>
                    <div class="form-group row ays_toggle_parent show_for_checkbox_type <?php echo ($is_only_checkbox_type) ? '' : 'display_none'; ?>" style="margin: 0;">
                        <div class="col-sm-4" style="padding-left: 0;">
                            <label for="ays_enable_min_selection_number">
                                <?php echo __('Enable minimum selection number', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __( 'Require users to choose answers not under the min value you provided. It will work with the Checkbox type.' , $this->plugin_name ); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-1">
                            <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_min_selection_number" name="ays_enable_min_selection_number" value="on" <?php echo ($enable_min_selection_number) ? 'checked' : ''; ?>>
                        </div>
                        <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo ($enable_min_selection_number) ? '' : 'display_none'; ?>">
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays_min_selection_number">
                                        <?php echo __('Min value', $this->plugin_name); ?>
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify the minimum value allowed.',$this->plugin_name); ?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="number" id="ays_min_selection_number" class="ays-text-input" name="ays_min_selection_number" value="<?php echo $min_selection_number; ?>" <?php echo $disabled_option; ?>>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="show_for_checkbox_type <?php echo ($is_only_checkbox_type) ? '' : 'display_none'; ?>"/>
                    <div class="form-group row ays_toggle_parent show_for_checkbox_type <?php echo ($is_only_checkbox_type) ? '' : 'display_none'; ?>" style="margin: 0;">
                        <div class="col-sm-4" style="padding-left: 0;">
                            <label for="ays_enable_max_selection_number">
                                <?php echo __('Enable maximum selection number', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __( 'Allow users to choose more than one answer but not over the max value you provided. It will work with the Checkbox type.' , $this->plugin_name ); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-1">
                            <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_max_selection_number" name="ays_enable_max_selection_number" value="on" <?php echo ($enable_max_selection_number) ? 'checked' : ''; ?>>
                        </div>
                        <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo ($enable_max_selection_number) ? '' : 'display_none'; ?>">
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays_max_selection_number">
                                        <?php echo __('Max value', $this->plugin_name); ?>
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify the maximum value allowed.',$this->plugin_name); ?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="number" id="ays_max_selection_number" class="ays-text-input" name="ays_max_selection_number" value="<?php echo $max_selection_number; ?>" <?php echo $disabled_option; ?>>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="show_only_for_matching_type <?php echo ($question["type"] === 'matching') ? '' : 'display_none'; ?>"/>
                    <div class="form-group row ays_toggle_parent show_only_for_matching_type <?php echo ( $question["type"] === 'matching' ) ? '' : 'display_none'; ?>" style="margin: 0;">
                        <div class="col-sm-12 col-md-6" style="padding-left: 0;">
                            <label class='ays-label ays-add-answer-first-label' style="margin:0;" for="ays-answers-matches-table">
                                <?php echo __( 'Add incorrect matches (distractors)', $this->plugin_name ); ?>
                                <a href="javascript:void(0)" class="ays-add-answer-incorrect-match">
                                    <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>
                                </a>
                            </label>
                            <table class="ays-answers-table w-100" id="ays-answers-matches-table">
                                <thead>
                                    <tr class="ui-state-default">
                                        <th class="th-350"><?php echo __('Match', $this->plugin_name); ?></th>
                                        <th class="th-150 ays-quiz-question-answer-incorrect-match-delete-row"><?php echo __('Delete', $this->plugin_name); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if ( ! empty( $answer_incorrect_matches ) && is_array( $answer_incorrect_matches ) ): ?>
                                    <?php foreach ( $answer_incorrect_matches as $match ): ?>
                                    <tr class="ays-incorrect-match-row">
                                        <td class="ays-quiz-question-answer-incorrect-match-row">
                                            <input type="text" name="ays-answer-incorrect-matches[]" class="ays-incorrect-answer-match-value" value="<?php echo esc_attr( $match ); ?>"/>
                                        </td>
                                        <td class="ays-quiz-question-answer-incorrect-match-delete-row">
                                            <a href="javascript:void(0)" class="ays-delete-answer-incorrect-match">
                                                <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr class="ays-incorrect-match-row ays-incorrect-match-row-empty">
                                        <td class="ays-quiz-question-answer-incorrect-match-row" colspan="2">
                                            <?php echo __('No incorrect matches yet.', $this->plugin_name); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>

                            <?php
                            if( $owner ){
                                ?>
                                <label for=""><?php echo __( 'Add', $this->plugin_name ); ?></label>
                                <a href="javascript:void(0)" class="ays-add-answer-incorrect-match">
                                    <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <hr class="hide_for_custom_type <?php echo ($is_custom_type) ? 'display_none' : ''; ?>"/>
                <div class="form-group row hide_for_custom_type <?php echo ($is_custom_type) ? 'display_none' : ''; ?>">
                    <div class="col-sm-4">
                        <label for="ays_question_weight">
                            <?php echo __('Question weight', $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify the weight of the question. It’s not connected with answers points. It will be multiplied with chosen answer weight (if you choose quiz calculation by points). The default value is 1.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" id="ays_question_weight" class="ays-text-input ays-text-input-short" name="ays_question_weight" value="<?php echo $question_weight; ?>" <?php echo $readonly_option; ?>>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays-category">
                            <?php echo __('Question category', $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can choose your desired category prepared in advance.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <select id="ays-category" name="ays_question_category" <?php echo $disabled_option; ?>>
                            <option></option>
                            <?php
                            $cat = 0;
                            foreach ($question_categories as $question_category) {
                                $checked = (intval($question_category['id']) == intval($question['category_id'])) ? "selected" : "";
                                if ($cat == 0 && intval($question['category_id']) == 0) {
                                    $checked = 'selected';
                                }
                                echo "<option value='" . $question_category['id'] . "' " . $checked . ">" . stripslashes($question_category['title']) . "</option>";
                                $cat++;
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <hr>
                <!-- Question Tags Start -->
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_quiz_question_tags">
                            <?php echo __('Question tags', $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Add tags to your questions. Tags are used for filtering questions to add to your quiz. You can choose as many tags as you want. To create tags, please go Question categories > Tags > Add New.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <select id="ays_quiz_question_tags" multiple name="ays_quiz_question_tags[]" <?php echo $disabled_option; ?>>
                            <?php
                                $selected = '';
                                foreach ($get_question_tags as $key => $get_question_tag) :
                                    if(is_array($question_tag_ids)):
                                        if(in_array($get_question_tag['id'],$question_tag_ids)):
                                            $selected = 'selected';
                                        else:
                                            $selected = '';
                                        endif;
                                    else:
                                        if($get_question_tag['id'] == $question_tag_ids):
                                            $selected = 'selected';
                                        else:
                                            $selected = '';
                                        endif;
                                    endif;
                            ?>
                                    <option value="<?php echo $get_question_tag['id'] ;?>" <?php echo $selected ;?>><?php echo $get_question_tag['title'] ;?></option>
                            <?php
                                endforeach;
                            ?>
                        </select>
                    </div>
                </div>
                <hr>
                <!-- Question Tags End -->
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label>
                            <?php echo __('Question status', $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose whether the question is active or not. If you choose Unpublished option, the question won’t be shown anywhere on your website.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>

                    <div class="col-sm-8">
                        <div class="form-check form-check-inline">
                            <input type="radio" id="ays-publish" name="ays_publish"  <?php echo $readonly_option; ?> value="1" <?php echo ($question_published  == '') ? "checked" : ""; ?>  <?php echo ($question_published == '1') ? 'checked' : ''; ?>/>
                            <label class="form-check-label" for="ays-publish"> <?php echo __('Published', $this->plugin_name); ?> </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" id="ays-unpublish" name="ays_publish"  <?php echo $readonly_option; ?> value="0" <?php echo ($question_published == '0') ? 'checked' : ''; ?>/>
                            <label class="form-check-label" for="ays-unpublish"> <?php echo __('Unpublished', $this->plugin_name); ?> </label>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_quiz_hide_question_text">
                            <?php echo __('Hide question text on the front-end', $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Make the question appear without text. The option is designed to use when the question includes an image as well.',$this->plugin_name); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>

                    <div class="col-sm-8">
                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="ays_quiz_hide_question_text" name="ays_quiz_hide_question_text" value="on" <?php echo ($quiz_hide_question_text) ? "checked" : ""; ?> />
                        </div>
                    </div>
                </div>
                <hr>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_quiz_disable_answer_stripslashes">
                            <?php echo __('Disable strip slashes for answers', $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("By enabling this option, the backslashes will not be removed from your answers' content. It is recommended to have this option enabled, if you are using the MathJax-Latex plugin.",$this->plugin_name)  ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>

                    <div class="col-sm-8">
                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="ays_quiz_disable_answer_stripslashes" name="ays_quiz_disable_answer_stripslashes" value="on" <?php echo ($quiz_disable_answer_stripslashes) ? "checked" : ""; ?> />
                        </div>
                    </div>
                </div>
                <hr>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_quiz_enable_question_stripslashes">
                            <?php echo __('Enable strip slashes for question', $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("If this option is enabled the backslashes will be removed from your questions' content. If you are using the MathJax-Latex plugin it is recommended to have this option disabled.",$this->plugin_name)  ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>

                    <div class="col-sm-8">
                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="ays_quiz_enable_question_stripslashes" name="ays_quiz_enable_question_stripslashes" value="on" <?php echo ($quiz_enable_question_stripslashes) ? "checked" : ""; ?> />
                        </div>
                    </div>
                </div>
                <hr class="hide_for_custom_type <?php echo ($is_custom_type) ? 'display_none' : ''; ?>" />
                <div class="form-group row hide_for_custom_type <?php echo ($is_custom_type) ? 'display_none' : ''; ?>">
                    <div class="col-sm-4">
                        <label for="ays_not_influence_to_score">
                            <?php echo __('No influence to score', $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php
                               echo __( "If you enable this option, this question will not be counted in the final score.", $this->plugin_name ) . " " .
                                    __( "So this question will be just a Survey question.", $this->plugin_name ) . " " .
                                    __( "There will not be correct/incorrect answers.", $this->plugin_name ) . " " .
                                    __( "This is for just collecting data from users.", $this->plugin_name );
                            ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>

                    <div class="col-sm-8">
                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="ays_not_influence_to_score" name="ays_not_influence_to_score" value="on" <?php echo ($not_influence_to_score) ? "checked" : ""; ?>  <?php echo $readonly_option; ?> />
                        </div>
                    </div>
                </div>
                <hr>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_question_title">
                            <?php echo __('Question title',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Title of the question',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <div>
                            <input type="text" class="ays-text-input" id="ays_question_title" name="ays_question_title" value="<?php echo $question_title; ?>" <?php echo $disabled_option; ?>>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="form-group row">
                    <div class="col-sm-12">
                        <a href="javascript:void(0);" id="ays_next_tab" class="ays_next_tab"><?php echo __( 'Advanced settings' , $this->plugin_name ); ?></a>
                    </div>
                </div>
            </div>
            <div id="tab2" class="ays-quiz-tab-content <?php echo ($ays_question_tab == 'tab2') ? 'ays-quiz-tab-content-active' : ''; ?>">
                <p class="ays-subtitle"><?php echo __('Question Settings',$this->plugin_name)?></p>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label>
                            <?php echo __('Question background image', $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Background image of the container. You can choose different images for different questions.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-3">
                        <a href="javascript:void(0)" class="add-question-bg-image m-0"><?= $bg_image_text; ?></a>
                    </div>
                    <div class="col-sm-5">
                        <div class="ays-question-bg-image-container" style="<?= $bg_style; ?>">
                            <span class="ays-remove-question-bg-img"></span>
                            <img src="<?= isset($options['bg_image']) ? $options['bg_image'] : ""; ?>"
                                 id="ays-question-bg-img"/>
                            <input type="hidden" name="ays_question_bg_image" id="ays-question-bg-image"
                                   value="<?= isset($options['bg_image']) ? $options['bg_image'] : ""; ?>"/>
                        </div>
                    </div>
                </div>
                <hr class="hide_for_custom_type <?php echo ($is_custom_type) ? 'display_none' : ''; ?>">
                <div class="form-group row hide_for_custom_type <?php echo ($is_custom_type) ? 'display_none' : ''; ?>">
                    <div class="col-sm-4">
                        <label>
                            <?php echo __('User answer explanation', $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The users can write an explanation for their answers.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-check form-check-inline">
                            <input type="radio" id="ays-user-ex-on" name="ays_user_explanation" value="on"  <?php echo $readonly_option; ?> <?php echo ($question["user_explanation"] == 'on') ? 'checked' : ''; ?>/>
                            <label class="form-check-label" for="ays-user-ex-on"> <?php echo __('Enabled', $this->plugin_name); ?> </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" id="ays-user-ex-off" name="ays_user_explanation" value="off"  <?php echo $readonly_option; ?> <?php echo ($question["user_explanation"] == 'off') ? 'checked' : ''; ?>/>
                            <label class="form-check-label" for="ays-user-ex-off"> <?php echo __('Disabled', $this->plugin_name); ?> </label>
                        </div>
                    </div>
                </div>
                <hr class="hide_for_custom_type <?php echo ($is_custom_type) ? 'display_none' : ''; ?>">
                <div class="form-group row hide_for_custom_type <?php echo ($is_custom_type) ? 'display_none' : ''; ?>">
                    <div class="col-sm-4">
                        <label for="right_answer_text">
                            <?php echo __('Question hint',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Add extra information that can help users about the question.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <?php
                        $content = wpautop(stripslashes((isset($question['question_hint']))?$question['question_hint']:''));
                        $editor_id = 'ays_question_hint';
                        $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'ays_question_hint', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                        wp_editor($content, $editor_id, $settings);
                        ?>
                    </div>
                </div>
                <hr class="hide_for_custom_type <?php echo ($is_custom_type) ? 'display_none' : ''; ?>">
                <div class="form-group row hide_for_custom_type <?php echo ($is_custom_type) ? 'display_none' : ''; ?>">
                    <div class="col-sm-4">
                        <label for="explanation">
                            <?php echo __('Question explanation',$this->plugin_name)?> <sup>(<?php echo __('except for checkbox type',$this->plugin_name);?>)</sup>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Provide descriptive or informative text about the question.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <?php
                        $content = wpautop(stripslashes((isset($question['explanation']))?$question['explanation']:''));
                        $editor_id = 'explanation';
                        $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'explanation', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                        wp_editor($content, $editor_id, $settings);
                        ?>
                    </div>
                </div>
                <hr class="hide_for_custom_type <?php echo ($is_custom_type) ? 'display_none' : ''; ?>">
                <div class="form-group row hide_for_custom_type <?php echo ($is_custom_type) ? 'display_none' : ''; ?>">
                    <div class="col-sm-4">
                        <label for="wrong_answer_text">
                            <?php echo __('Message for the wrong answer ',$this->plugin_name)?><sup>(<?php echo __('except for checkbox type',$this->plugin_name); ?>)</sup>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can write text which will be shown in case of the wrong answer. It doesn’t work when you chose Quiz calculation option By Weight/points from Quiz Settings.',$this->plugin_name); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <?php
                        $content = wpautop(stripslashes((isset($question['wrong_answer_text']))?$question['wrong_answer_text']:''));
                        $editor_id = 'wrong_answer_text';
                        $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'wrong_answer_text', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                        wp_editor($content, $editor_id, $settings);
                        ?>
                    </div>
                </div>
                <hr class="hide_for_custom_type <?php echo ($is_custom_type) ? 'display_none' : ''; ?>">
                <div class="form-group row hide_for_custom_type <?php echo ($is_custom_type) ? 'display_none' : ''; ?>">
                    <div class="col-sm-4">
                        <label for="right_answer_text">
                            <?php echo __('Message for the right answer ',$this->plugin_name)?><sup>(<?php echo __('except for checkbox type',$this->plugin_name); ?>)</sup>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can write text which will be shown in case of right answer.  It doesn’t work when you chose Quiz calculation option By Weight/points from Quiz Settings.',$this->plugin_name); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <?php
                        $content = wpautop(stripslashes((isset($question['right_answer_text']))?$question['right_answer_text']:''));
                        $editor_id = 'right_answer_text';
                        $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'right_answer_text', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                        wp_editor($content, $editor_id, $settings);
                        ?>
                    </div>
                </div>
                <hr>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_quiz_question_note_message">
                            <?php echo __('Note text',$this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Write down additional information about the question that will appear under the answers.',$this->plugin_name);?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <?php
                        $content = $quiz_question_note_message;
                        $editor_id = 'ays_quiz_question_note_message';
                        $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'ays_quiz_question_note_message', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                        wp_editor($content, $editor_id, $settings);
                        ?>
                    </div>
                </div>
            </div>
            <hr>
            <?php
            if( $owner ){
            ?>
            <div class="form-group row ays-question-button-box">
                <div class="col-sm-8 ays-question-button-first-row" style="padding: 0;">
                <?php
                    wp_nonce_field('question_action', 'question_action');
                    $other_attributes = array('id' => 'ays-button-save');
                    submit_button(__('Save and close', $this->plugin_name), 'primary ays-button ays-quiz-loader-banner', 'ays_submit', false, $other_attributes);
                    $other_attributes = array('id' => 'ays-button-save-new');
                    submit_button(__('Save and new', $this->plugin_name), 'primary ays-button ays-quiz-loader-banner', 'ays_save_new', false, $other_attributes);
                    $other_attributes = array(
                        'id' => 'ays-button-apply',
                        'title' => __('Ctrl + s', $this->plugin_name),
                        'data-toggle' => 'tooltip',
                        'data-delay'=> '{"show":"1000"}'
                    );
                    submit_button(__('Save', $this->plugin_name), 'ays-button ays-quiz-loader-banner', 'ays_apply', false, $other_attributes);
                    echo $loader_iamge; 
                ?>
                </div>
                <div class="col-sm-4 ays-question-button-second-row ays-question-button-second-row-padding-right">
                <?php
                    if ( $prev_question_id != "" && !is_null( $prev_question_id ) ) {

                        $other_attributes = array(
                            'id' => 'ays-question-prev-button',
                            'data-message' => __( 'Are you sure you want to go to the previous question page?', $this->plugin_name),
                            'href' => sprintf( '?page=%s&action=%s&question=%d', esc_attr( $_REQUEST['page'] ), 'edit', absint( $prev_question_id ) )
                        );
                        submit_button(__('Prev Question', $this->plugin_name), 'button button-primary ays_default_btn ays-button', 'ays_question_prev_button', false, $other_attributes);
                    }

                    if ( $nex_question_id != "" && !is_null( $nex_question_id ) ) {

                        $other_attributes = array(
                            'id' => 'ays-question-next-button',
                            'data-message' => __( 'Are you sure you want to go to the next question page?', $this->plugin_name),
                            'href' => sprintf( '?page=%s&action=%s&question=%d', esc_attr( $_REQUEST['page'] ), 'edit', absint( $nex_question_id ) )
                        );
                        submit_button(__('Next Question', $this->plugin_name), 'button button-primary ays_default_btn ays-button', 'ays_question_next_button', false, $other_attributes);
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
