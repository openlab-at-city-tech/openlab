<?php
// general HDQ Addon Save Results Light functions

// Tell HD Quiz to send an AJAX request to `hdq_a_light_submit_action()`
// once quiz has been submitted
function hdq_a_light_submit($quizOptions)
{
    array_push($quizOptions->hdq_submit, "hdq_a_light_submit_action");
    return $quizOptions;
}
add_action('hdq_submit', 'hdq_a_light_submit');

// the functon that runs once quiz submitted
function hdq_a_light_submit_action($data)
{
    function hdq_a_i_validate_score($score)
    {
        return intval($score);
    }

    // check if logged-in users only should be saved
    $membersOnly = sanitize_text_field(get_option("hdq_a_l_members_only"));
    if ($membersOnly === "yes" && !is_user_logged_in()) {
        die();
    }

    $result = new stdClass();
    $quizID = intval($_POST['data']["quizID"]);
    $result->quizID = $quizID;
    $score = array_map('hdq_a_i_validate_score', $_POST['data']["score"]);
    $result->score = $score;

    // get quiz meta
    $hdq_quiz_options = hdq_get_quiz_options($quizID);
    $passPercent = intval($hdq_quiz_options["passPercent"]);
    $result->passPercent = $passPercent;

    // get quiz term info
    $term = get_term($quizID, "quiz");
    $quizName = $term->name;
    $result->quizName = $quizName;

    // create the user info
    $quizTaker = array();
    $current_user = wp_get_current_user();
    if ($current_user->ID === 0) {
        $quizTaker[0] = "0";
        $quizTaker[1] = "--";
    } else {
        $quizTaker[0] = $current_user->ID;
        $quizTaker[1] = $current_user->data->display_name;
    }
    $result->quizTaker = $quizTaker;

    // save the date and time
    $result->datetime = date('m-d-Y h:i:s a', time());

    // read in existing results
    $data = get_option("hdq_quiz_results_l");

    if ($data == "" || $data == null) {
        $data = array();
        update_option("hdq_quiz_results_l", "");
    } else {
        $data = json_decode(html_entity_decode($data), true);
    }

    // append new result to data
    array_push($data, $result);

    // re-encode and update record
    $result = json_encode($data);
    update_option("hdq_quiz_results_l", sanitize_text_field($result));

    echo "Quiz result has been logged";

    die();
}
add_action('wp_ajax_hdq_a_light_submit_action', 'hdq_a_light_submit_action');
add_action('wp_ajax_nopriv_hdq_a_light_submit_action', 'hdq_a_light_submit_action');
