<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(isset($_REQUEST['do']) and $_REQUEST['do']=='show_exam_result' ) $exam_id = intval($_REQUEST['quiz_id']);

if(!is_singular() and isset($GLOBALS['watu_client_includes_loaded'])) { #If this is in the listing page - and a quiz is already shown, don't show another.
	printf(__("Please go to <a href='%s'>%s</a> to view the test", 'watu'), get_permalink(), get_the_title());
	return false;
} 

global $wpdb, $user_ID, $post, $achieved;
$taker_email = '';
$do_redirect = false;

$appid = get_option('watuproshare_facebook_appid');	

if(!empty($appid)) {
		echo  "<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId            : '".$appid."',
      autoLogAppEvents : true,
      xfbml            : true,
      version          : 'v3.1'
    });
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = \"https://connect.facebook.net/en_US/sdk.js\";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>";
}

// select exam
$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATU_EXAMS." WHERE ID=%d", $exam_id));
if(empty($exam->ID)) return sprintf(__('%s not found.', 'watu'), ucfirst(WATU_QUIZ_WORD));
$advanced_settings = unserialize(stripslashes($exam->advanced_settings));

// requires login?
if(!empty($exam->require_login) and !is_user_logged_in()) {
	 echo "<p><b>".sprintf(__('You need to be registered and logged in to take this %s.', 'watu'), __('quiz', 'watu')). 
		      	" <a href='".wp_login_url(get_permalink( $post->ID ))."'>".__('Log in', 'watu')."</a>";
		      if(get_option("users_can_register")) {
						echo " ".__('or', 'watu')." <a href='".add_query_arg(array("redirect_to"=> get_permalink( $post->ID ), 'watu_register'=>1), wp_registration_url())."'>".__('Register', 'watu')."</a></b>";        
					}
					echo "</p>";
	return false;
}

//  Namaste  no access due to lesson restriction
// If option namaste_access_exam_started_lesson is true, exam cannot be accessed if it is associated to a lesson and the lesson is not started 
 $namaste_lesson_no_access = false;
 if(class_exists('NamasteLMS') and get_option('namaste_use_exams') == 'watu' and get_option('namaste_access_exam_started_lesson') == 1) {
 	 $has_lesson = $wpdb->get_var($wpdb->prepare("SELECT tP.ID FROM {$wpdb->posts} tP
 	 	JOIN {$wpdb->postmeta} tM ON tM.post_id=tP.ID
 	 	WHERE tP.post_type = 'namaste_lesson' AND tM.meta_value=%d AND tM.meta_key = 'namaste_required_exam' ", $exam->ID));

	 // was lesson started by the student?
	 $started_lesson = false;
	 if($has_lesson) {
	 	$started_lesson = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".NAMASTE_STUDENT_LESSONS." 
	 		WHERE student_id=%d AND lesson_id=%d", $user_ID, $has_lesson));
	 }		 	 
 	 	
 	 if($has_lesson and !$started_lesson) {
 	 	$lesson = get_post($has_lesson);
 	 	$lesson_link = get_permalink($lesson->ID);
 	 	echo '<p>';
 	 	printf(__('To access this %s you need first to read lesson <a href="%s">%s</a>.', 'watu'), WATU_QUIZ_WORD, $lesson_link, stripslashes($lesson->post_title));
 	 	echo '</p>';
 	 	return false;
 	 }	
 }
// End Namaste check


// can re-take?
if(!empty($exam->require_login) and (empty($exam->take_again) or !empty($exam->times_to_take))) {
	$cnt_takings=$wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM ".WATU_TAKINGS."
				WHERE exam_id=%d AND user_id=%d", $exam->ID, $user_ID)); 
				
	if(empty($exam->take_again) and $cnt_takings > 0) {
		printf(__("Sorry, you can take this %s only once!", 'watu'), __('quiz', 'watu'));
		return false;
	}
	
	// multiple times allowed, but number is specified	
	if($exam->times_to_take and $cnt_takings >= $exam->times_to_take) {
		echo "<p><b>";
		printf(__("Sorry, you can take this quiz only %d times.", 'watu'), $exam->times_to_take);
		echo "</b></p>";
		return false;
	}			
}

$answer_display = get_option('watu_show_answers');
if(!isset($exam->show_answers) or $exam->show_answers == 100) $answer_display = $answer_display; // assign the default
else $answer_display = $exam->show_answers;

$order_sql = ($exam->randomize or $exam->pull_random) ? "ORDER BY RAND()" : "ORDER BY sort_order, ID";
$limit_sql = $exam->pull_random ? $wpdb->prepare("LIMIT %d", $exam->pull_random) : "";

$questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATU_QUESTIONS." 
		WHERE exam_id=%d AND is_inactive=0 $order_sql $limit_sql", $exam_id));
$num_questions = 0;
foreach($questions as $question) {
	if(!$question->is_survey) $num_questions++;
}

$all_questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATU_QUESTIONS." WHERE exam_id = %d AND is_inactive = 0 ", $exam_id));
		
if($questions) {
	if(!isset($GLOBALS['watu_client_includes_loaded']) and !isset($_REQUEST['do']) ) {
		$GLOBALS['watu_client_includes_loaded'] = true; // Make sure that this code is not loaded more than once.
   }
   
// honeypot validation?
if(!empty($exam->use_honeypot) and !empty($_POST['do'])) {
	if(@$_POST['h_app_id'] != '__' . md5('honeyforme' . $_SERVER['REMOTE_ADDR'])) die('WATU_CAPTCHA:::'.__('No answer to the verification question.', 'watu'));	
}   

// text captcha?
if(!empty($exam->require_text_captcha)) {	
	$text_captcha_html = WatuTextCaptcha :: generate();
	$textcaptca_style = $exam->single_page==1?"":"style='display:none;'";
	$text_captcha_html = "<div id='WatuTextCaptcha' $textcaptca_style>".$text_captcha_html."</div>";	
	// verify captcha
	if(!empty($_POST['do'])) {
		if(!WatuTextCaptcha :: verify($_POST['watu_text_captcha_question'], $_POST['watu_text_captcha_answer'])) die('WATU_CAPTCHA:::'.__('Wrong answer to the verification question.', 'watu'));	
	}
}

if(isset($_REQUEST['do']) and $_REQUEST['do']) { // Quiz Reuslts.
	$achieved = $max_points = $num_correct = $num_wrong = $num_empty = 0;
	$result = '';
	
	// we should reorder the questions in the same way they came from POST because exam might be randomized	
	$_exam = new WatuExam();
	$questions = $_exam->reorder_questions($all_questions, $_POST['question_id']);

	foreach ($questions as $qct => $ques) {
		$is_empty = false;
		$qnum = $qct+1;
		$question_number = empty($exam->dont_display_question_numbers) ? "<span class='watu_num'>$qnum. </span>"  : '';
		
		$result .= "<div class='show-question'>";
		$result .= "<div class='show-question-content'>". wpautop($question_number . stripslashes($ques->question), true) . "</div>";
		$all_answers = $ques->answers;
		$correct = false;
		$class = $textarea_class = 'answer';
		$result .= "<ul>";
		$ansArr = is_array( @$_REQUEST["answer-" . $ques->ID] )? $_POST["answer-" . $ques->ID] : array();
		foreach ($all_answers as $ans) {
			$class = 'answer';
			
			list($points, $correct, $class) = WatuQuestion :: calculate($ques, $ans, $ansArr, $correct, $class);		
			if(strstr($class, 'correct-answer')) $textarea_class = $class;	
			
			$achieved += $points;
			if($ques->answer_type != 'textarea') $result .= wpautop("<li class='$class'><span class='answer'><!--WATUEMAIL".$class."WATUEMAIL-->" . stripslashes($ans->answer) . "</span></li>");
			
			// for textareas the answer can be correct only once. If the user has wrongly entered same word multiple times (or with case sensitivity in mind) we have to ensure the question won't add
			// points and num correct answers multiple times.
			if($ques->answer_type == 'textarea' and $correct) break;
		}

		// textareas
		if($ques->answer_type=='textarea' and !empty($_POST["answer-" . $ques->ID][0])) {
			if(!count($all_answers) and !$question->is_survey) $textarea_class = 'correct-answer';
			$result .= wpautop("<li class='user-answer $textarea_class'><span class='answer'><!--WATUEMAIL".$class."WATUEMAIL-->".esc_html(stripslashes($_POST["answer-" . $ques->ID][0]))."</span></li>");
		}		
		
		$result .= "</ul>";
		if(($ques->answer_type == 'textarea' and empty($_POST["answer-" . $ques->ID][0])) 
			or ($ques->answer_type != 'textarea' and empty($_POST["answer-" . $ques->ID])) ) {
			$num_empty++;	 
			$is_empty = true;
			$result .= "<p class='unanswered'>" . __('Question was not answered', 'watu') . "</p>";
		}
			
		// answer explanation?
		if(!empty($ques->feedback)) {
			// has split tag?
			if(strstr($ques->feedback, '{{{split}}}')) {
				$parts = explode('{{{split}}}', $ques->feedback);
				if($correct) $ques->feedback = $parts[0];
				else $ques->feedback = $parts[1];
			}			
			
			$result .= "<div class='show-question-feedback'>".wpautop(stripslashes($ques->feedback))."</div>";
		}	

		$result .= "</div>";
	
		if($correct) $num_correct++;
		if(!$correct and !$is_empty and !$question->is_survey) $num_wrong++;
		$max_points += WatuQuestion :: max_points($ques, $all_answers);
	}
	
	// percent correct answers
	$percent_correct = $num_questions ? round(100 * $num_correct / $num_questions) : 0; 
	
	// Find scoring details
	if($max_points == 0) $percent = 0;
	else $percent = number_format($achieved / $max_points * 100, 2);
						//0-9			10-19%,	 	20-29%, 	30-39%			40-49%
	$all_rating = array(__('Failed', 'watu'), __('Failed', 'watu'), __('Failed', 'watu'), __('Failed', 'watu'), __('Just Passed', 'watu'),
						//																			100%			More than 100%?!
					__('Satisfactory', 'watu'), __('Competent', 'watu'), __('Good', 'watu'), __('Very Good', 'watu'), __('Excellent', 'watu'), __('Unbeatable', 'watu'), __('Cheater', 'watu'));
	$rate = intval($percent / 10);
	if($percent == 100) $rate = 9;
	if($achieved == $max_points) $rate = 10;
	if($percent>100) $rate = 11;
	$rating = @$all_rating[$rate];
	
	$grade = __('None', 'watu');
	$gtitle = $gdescription="";
	$g_id = 0;
	$allGrades = $wpdb->get_results(" SELECT * FROM `".WATU_GRADES."` WHERE exam_id=$exam_id ");
	if( count($allGrades) ){
		foreach($allGrades as $grow ) {

			if( $grow->gfrom <= $achieved and $achieved <= $grow->gto ) {
				$grade = $gtitle = $grow->gtitle;
				$gdescription = wpautop(stripslashes($grow->gdescription));
				$g_id = $grow->ID;
				if(!empty($grow->gdescription)) $grade .= wpautop(stripslashes($grow->gdescription));
				if(!empty($grow->redirect_url)) $do_redirect = $grow->redirect_url;
				break;
			}
		}
	}
	
	####################### VARIOUS AVERAGE CALCULATIONS (think about placing them in function / method #######################
	// calculate averages
	$avg_points = $avg_percent = '';
	if(strstr($exam->final_screen, '%%AVG-POINTS%%')) {
		$all_point_rows = $wpdb->get_results($wpdb->prepare("SELECT points FROM ".WATU_TAKINGS." 
			WHERE exam_id=%d", $exam->ID));
		$all_points = 0;
		foreach($all_point_rows as $r) $all_points += $r->points;	
		$all_points += $achieved;			
		$avg_points = round($all_points / ($wpdb->num_rows + 1), 1);
	}
	
	// better than what %?
	$better_than = '';
	if(strstr($exam->final_screen, '%%BETTER-THAN%%')) {
		// select total completed quizzes
		$total_takings = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM ".WATU_TAKINGS."
			WHERE exam_id=%d", $exam->ID));	
		
		$num_lower = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM ".WATU_TAKINGS."
				WHERE exam_id=%d AND points < %f", $exam->ID, $achieved));
		
		$better_than = $total_takings ? round($num_lower * 100 / $total_takings) : 0;
	}
	####################### END VARIOUS AVERAGE CALCULATIONS #######################
	
	
	$quiz_details = $wpdb->get_row($wpdb->prepare("SELECT name,final_screen, description FROM {$wpdb->prefix}watu_master WHERE ID=%d", $exam_id));

	$quiz_details->final_screen = str_replace('%%TOTAL%%', '%%MAX-POINTS%%', $quiz_details->final_screen);
	$replace_these	= array('%%SCORE%%', '%%MAX-POINTS%%', '%%PERCENTAGE%%', '%%GRADE%%', '%%RATING%%', '%%CORRECT%%', '%%WRONG_ANSWERS%%', '%%QUIZ_NAME%%',	'%%DESCRIPTION%%', '%%GRADE-TITLE%%', '%%GRADE-DESCRIPTION%%', '%%POINTS%%', '%%AVG-POINTS%%', '%%BETTER-THAN%%', '%%EMPTY%%', '%%WRONG%%');
	$with_these		= array($achieved,		 $max_points,	 $percent_correct,			$grade,		 $rating,		$num_correct,	$num_wrong,	   stripslashes($quiz_details->name), wpautop(stripslashes($quiz_details->description)), $gtitle, $gdescription, $achieved, $avg_points, $better_than, $num_empty, $num_wrong);
	
	// insert taking
	$uid = $user_ID ? $user_ID : 0;
	$taker_email = empty($_POST['watu_taker_email']) ? '' : sanitize_email($_POST['watu_taker_email']);
	if(empty($exam->dont_store_data)) {
		if($exam->no_ajax) {
			$taking_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".WATU_TAKINGS."
				WHERE ip=%s AND user_id=%d AND exam_id=%d AND points=%d AND grade_id=%d AND start_time=%s",
				'', $user_ID, $exam->ID, $achieved, $g_id, $_POST['start_time']));				
		}		
		if(empty($taking_id)) {			
			$source_url = empty($exam->save_source_url) ? '' : $_SERVER['HTTP_REFERER'];
			$wpdb->query($wpdb->prepare("INSERT INTO ".WATU_TAKINGS." SET exam_id=%d, user_id=%d, ip=%s, date=CURDATE(), 
				points=%d, grade_id=%d, result=%s, snapshot='', start_time=%s, email=%s, percent_correct=%d, source_url=%s,
				num_correct=%d, num_wrong=%d, num_empty=%d", 
				$exam_id, $uid, '', $achieved, $g_id, $grade, @$_POST['start_time'], 
				$taker_email, $percent, $source_url, $num_correct, $num_wrong, $num_empty));
			$taking_id = $wpdb->insert_id;
		}
	}
	else $taking_id = 0;	
	$GLOBALS['watu_taking_id'] = $taking_id;

	// Show the results
	$output = str_replace($replace_these, $with_these, wpautop(stripslashes($quiz_details->final_screen)));
	if(strstr($output, '%%ANSWERS%%')) {		
		$output = str_replace('%%ANSWERS%%', $result, $output);
	}
	$final_output = apply_filters(WATU_CONTENT_FILTER, $output);
		
	// replace email if entered
	$final_output = watu_replace_email($taker_email, $final_output);
	
	// this filter is for third party integrations. Not used in Watu itself.
	$final_output = apply_filters('watu_final_screen',$final_output, $taking_id, $exam, $questions, $result);
	
	if(!empty($do_redirect)) {
		if(empty($exam->no_ajax)) echo "WATU_REDIRECT:::".$do_redirect;
	}
	else echo $final_output;
		
	// update snapshot
	$wpdb->query($wpdb->prepare("UPDATE ".WATU_TAKINGS." SET snapshot=%s WHERE ID=%d", $final_output, $taking_id)); 
	
	$user_name = __('Guest', 'watu');
	if(is_user_logged_in()) {
		$current_user = wp_get_current_user();
		$user_name = $current_user->display_name;
	}
	$exam->user_name = $user_name; // to use for email subject in email_results
	
	// notify admin	
	if(!empty($exam->email_output)) {
		$email_output = wpautop(stripslashes($exam->email_output));
		$email_output = str_replace($replace_these, $with_these, $email_output);
		if(strstr($email_output, '%%ANSWERS%%')) {		
			$email_output = str_replace('%%ANSWERS%%', $result, $email_output);
		}
		$email_output = watu_replace_email($taker_email, $email_output);
		$email_output = apply_filters(WATU_CONTENT_FILTER, $email_output);
	} 
	else $email_output = $final_output;
	if(!empty($exam->notify_admin)) watu_notify($exam, $uid, $email_output);
	if(!empty($exam->notify_user)) watu_notify($exam, $uid, $email_output, 'user');
	
	do_action('watu_exam_submitted', $taking_id);
	do_action('watu_exam_submitted_detailed', $taking_id, $exam, $user_ID, $achieved, $g_id);
	if(empty($exam->no_ajax)) exit;// Exit due to ajax call
	if(!empty($exam->no_ajax) and !empty($do_redirect)) watu_redirect($do_redirect);

} else { // Show The Test
	$single_page = $exam->single_page;
	if(@file_exists(get_stylesheet_directory().'/watu/show_exam.html.php')) include get_stylesheet_directory().'/watu/show_exam.html.php';
	else include(WATU_PATH . '/views/show_exam.html.php');
 }
} // end if $questions