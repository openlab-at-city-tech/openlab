<?php
/**
 * The template for displaying Quiz
 */

$hdq_id = intval($quiz); // quiz ID
$hdq_quiz_options = hdq_get_quiz_options($hdq_id);

$hdq_results_position = sanitize_text_field($hdq_quiz_options["resultPos"]);
$hdq_timer = intval($hdq_quiz_options["quizTimerS"]);
$hdq_show_what_answers_were_right_wrong = sanitize_text_field($hdq_quiz_options["showResults"]); // highlights which selected answers were right or wrong
$hdq_show_correct = sanitize_text_field($hdq_quiz_options["showResultsCorrect"]); // shows what the right answer was if user got it wrong
$hdq_show_answer_text = sanitize_text_field($hdq_quiz_options["showIncorrectAnswerText"]); // show even if answe was correct

$hdq_immediate_mark = @sanitize_text_field($hdq_quiz_options["immediateMark"]);
$hdq_stop_answer_reselect = @sanitize_text_field($hdq_quiz_options["stopAnswerReselect"]);
if ($hdq_immediate_mark == "") {
    $hdq_immediate_mark = "no";
    $hdq_stop_answer_reselect = "no";
}

$hdq_question_order = sanitize_text_field($hdq_quiz_options["randomizeQuestions"]); // returns order for loop (menu_order, or random)
if ($hdq_question_order === "no" || $hdq_question_order === "" || $hdq_question_order === null) {
    $hdq_question_order = "menu_order";
}
$hdq_random_answer_order = sanitize_text_field($hdq_quiz_options["randomizeAnswers"]);
$hdq_use_pool = intval($hdq_quiz_options["pool"]);
$hdq_paginate = intval($hdq_quiz_options["paginate"]);
$hdq_pass_percent = intval($hdq_quiz_options["passPercent"]);
$hdq_share_results = sanitize_text_field($hdq_quiz_options["shareResults"]);
$hdq_twitter_handle = sanitize_text_field(get_option("hd_qu_tw"));
if ($hdq_twitter_handle == "" || $hdq_twitter_handle == null) {
    $hdq_twitter_handle = "harmonic_design";
}

// disable paginate if pool or timer is in use
if ($hdq_use_pool > 0 || $hdq_timer > 0) {
    $hdq_paginate = 0;
}

$use_adcode = false;
$hdq_adcode = get_option("hd_qu_adcode");
if ($hdq_adcode != "" && $hdq_adcode != null) {
    $hdq_adcode = stripcslashes(urldecode($hdq_adcode));
    $use_adcode = true;
}

wp_enqueue_style(
    'hdq_admin_style',
    plugin_dir_url(__FILE__) . './css/hdq_style.css',
    array(),
    "1.7.0"
);
wp_enqueue_script(
    'hdq_admin_script',
    plugins_url('./js/hdq_script.js', __FILE__),
    array('jquery'),
    '1.7.0',
    true
);

$hdq_featured_image = "";
if (has_post_thumbnail()) {
    $hdq_featured_image = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()), 'full');
}

// create object for localized script
$hdq_local_vars = new \stdClass();
$hdq_local_vars->hdq_quiz_id = $hdq_id;
$hdq_local_vars->hdq_timer = $hdq_timer;
$hdq_local_vars->hdq_show_what_answers_were_right_wrong = $hdq_show_what_answers_were_right_wrong;
$hdq_local_vars->hdq_show_correct = $hdq_show_correct;
$hdq_local_vars->hdq_show_answer_text = $hdq_show_answer_text;
$hdq_local_vars->hdq_immediate_mark = $hdq_immediate_mark;
$hdq_local_vars->hdq_stop_answer_reselect = $hdq_stop_answer_reselect;
$hdq_local_vars->hdq_pass_percent = $hdq_pass_percent;
$hdq_local_vars->hdq_share_results = $hdq_share_results;
$hdq_local_vars->hdq_quiz_permalink = get_the_permalink();
$hdq_local_vars->hdq_twitter_handle = $hdq_twitter_handle;
$hdq_local_vars->hdq_quiz_name = get_the_title();
$hdq_local_vars->hdq_ajax = admin_url('admin-ajax.php');
$hdq_local_vars->hdq_featured_image = $hdq_featured_image;
$hdq_local_vars->hdq_submit = array();
do_action("hdq_submit", $hdq_local_vars);

$hdq_local_vars = json_encode($hdq_local_vars);
wp_localize_script('hdq_admin_script', 'hdq_local_vars', $hdq_local_vars);

?>

<div class = "hdq_quiz_wrapper" id = "hdq_<?php echo $hdq_id; ?>">

    <div class = "hdq_before">
		<?php do_action("hdq_before", $hdq_id);?>
	</div>


	<div class = "hdq_quiz">

<?php
if ($hdq_results_position === "yes") {
    hdq_get_results($hdq_quiz_options);
}

// start the query
wp_reset_postdata();
wp_reset_query();
global $post;
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

if ($hdq_question_order == "rand" || $hdq_paginate == 0) {
    $hdq_paginate = "-1"; // set to infinite since we cannot paginate with rand
}
$quizPagination = "true";
if ($hdq_use_pool > 0) {
    $hdq_paginate = $hdq_use_pool; // set the posts-per-page to the quiz pool amount
    $hdq_question_order = "rand"; // force the quiz to randomize the questions
    $quizPagination = "false"; // disable WP pagination
}

// WP_Query arguments
$args = array(
    'post_type' => array('post_type_questionna'),
    'tax_query' => array(
        array(
            'taxonomy' => 'quiz',
            'terms' => $hdq_id,
        ),
    ),
    'pagination' => $quizPagination, // true or false
    'posts_per_page' => $hdq_paginate, // also used for the pool of questions
    'paged' => $paged,
    'orderby' => $hdq_question_order, // defaults to menu_order
    'order' => 'ASC',
);

$query = new WP_Query($args);
$i = 0;
// figure out the starting question number
$questionNumber = 0;
if ($hdq_paginate >= 1 && $paged > 1) {
    $questionNumber = ($paged * $hdq_paginate) - $hdq_paginate + 1;
}

// The Loop
if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $i++;
        $hdq_q_id = get_the_ID();
        $hdq_selected = intval(get_post_meta($hdq_q_id, 'hdQue_post_class2', true));
        $hdq_image_as_answer = sanitize_text_field(get_post_meta($hdq_q_id, 'hdQue_post_class23', true));
        $hdq_question_as_title = sanitize_text_field(get_post_meta($hdq_q_id, 'hdQue_post_class24', true));
        $hdq_jpaginate = sanitize_text_field(get_post_meta($hdq_q_id, 'hdQue_post_class25', true));
        $hdq_tooltip = sanitize_text_field(get_post_meta($hdq_q_id, 'hdQue_post_class12', true));
        $hdq_after_answer = wp_kses_post(get_post_meta($hdq_q_id, 'hdQue_post_class26', true));
        if ($hdq_jpaginate === "yes") {
            hdq_print_jPaginate($hdq_id);
        }
        if ($hdq_question_as_title === "yes") {
            $i = $i - 1; // reduce the question number
            hdq_print_question_as_title($i, $hdq_q_id, $hdq_tooltip);
        } elseif ($hdq_image_as_answer != "yes") {
            hdq_print_question_normal($i, $hdq_q_id, $hdq_tooltip, $hdq_after_answer, $hdq_selected, $hdq_random_answer_order);
        } elseif ($hdq_image_as_answer === "yes") {
            hdq_print_question_image($i, $hdq_q_id, $hdq_tooltip, $hdq_after_answer, $hdq_selected, $hdq_random_answer_order);
        }

        if ($use_adcode) {
            if ($i % 4 == 0 && $i != 0) {
                echo $hdq_adcode;
            }
        }
    }
}

wp_reset_postdata();

if ($query->max_num_pages > 1 || $hdq_paginate != "-1") {
    if (isset($_GET['currentScore'])) {
        echo '<input type = "hidden" id = "hdq_current_score" value = "' . $_GET['currentScore'] . '"/>';
    }
    if (isset($_GET['totalQuestions'])) {
        echo '<input type = "hidden" id = "hdq_total_questions" value = "' . $_GET['totalQuestions'] . '"/>';
    }

    if ($hdq_use_pool === 0) {
        if ($query->max_num_pages != $paged) {
            hdq_print_next($hdq_id, $paged);
        }
    } else {
        hdq_print_finish($hdq_id);
    }
} else {
    hdq_print_finish($hdq_id);
}

if ($query->max_num_pages == $paged) {
    hdq_print_finish($hdq_id);
}

if ($hdq_results_position != "yes") {
    echo hdq_get_results($hdq_quiz_options);
}

if ($hdq_timer > 3) {
    echo '<div class = "hdq_timer"></div>';
}
?>

        <div class = "hdq_before">
            <?php do_action("hdq_after", $hdq_id);?>
        </div>

        <div class = "hdq_loading_bar"></div>
	</div>
</div>
