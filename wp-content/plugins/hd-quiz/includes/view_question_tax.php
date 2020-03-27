<?php
// shows a list of all questions attached to this quiz,
// along with the settings tab for quiz options

$quiz_id = intval($_POST['quiz_id']);
$quiz = get_term($quiz_id, "quiz");
?>
<h1 id = "hdq_h1_title">
	<?php echo $quiz->name; ?>
</h1>

<div class = "hdq_button2" data-id = "<?php echo $quiz_id; ?>" id = "hdq_add_question">
	<span class="dashicons dashicons-plus"></span> ADD NEW QUESTION
</div>

<div class = "hdq_button1" data-id = "<?php echo $quiz_id; ?>" id = "hdq_save_quiz">
	<span class="dashicons dashicons-sticky"></span> SAVE QUIZ
</div>

<p>
	Quiz Shortcode: <code>[HDquiz quiz = "<?php echo $quiz_id; ?>"]</code> <br/><small>You can copy / paste that shortcode onto any post or page to display this quiz.</small>
</p>

<p>
	Add a new question to this quiz, or select a question below to edit it. You can also drag-and-drop to re-order the questions.
</p>


<div id="hdq_tabs">
	<ul>
		<li class="hdq_active_tab" data-hdq-content="hdq_tab_content">Questions</li>
		<li data-hdq-content="hdq_tab_settings">Quiz Settings</li>
	</ul>
	<div class="clear"></div>
</div>
<div id = "hdq_tab_content" class = "hdq_tab hdq_tab_active">

<div id = "hdq_quiz_question_list">
<?php
// WP_Query arguments
$args = array(
    'post_type' => array('post_type_questionna'),
    'tax_query' => array(
        array(
            'taxonomy' => 'quiz',
            'terms' => $quiz_id,
        ),
    ),
    'nopaging' => true,
    'posts_per_page' => '-1',
    'order' => 'ASC',
    'orderby' => 'menu_order',
);

// The Query
$query = new WP_Query($args);

$menu_number = 0;

// The Loop
if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $extra_classes = "";
        $hdq_question_as_title = sanitize_text_field(get_post_meta(get_the_ID(), 'hdQue_post_class24', true));
        if ($hdq_question_as_title === "yes") {
            $extra_classes = $extra_classes . 'hdq_quiz_question_title ';
        }
        $hdq_paginate = sanitize_text_field(get_post_meta(get_the_ID(), 'hdQue_post_class25', true));
        if ($hdq_paginate === "yes") {
            echo '<div class = "hdq_question_paginated">&nbsp;</div>';
        }
        echo '<div class = "hdq_quiz_item hdq_quiz_question ' . $extra_classes . '" data-id = "' . get_the_ID() . '" data-quiz-id = "' . $quiz_id . '" data-menu-number = "' . $menu_number . '">' . mb_strimwidth(get_the_title(), 0, 70, "...") . '</div>';
        $menu_number = $menu_number + 1;
    }
} else {
    echo '<p>Newly added questions will appear here</p>';
}

// Restore original Post Data
wp_reset_postdata()

?>
	</div>
</div>
<div id = "hdq_tab_settings" class = "hdq_tab">
<?php hdq_print_quiz_settings($quiz_id);?>
	</div>
</div>