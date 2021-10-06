<?php
/**
 * This will scan all the content pages that wordpress outputs for our special code. If the code is found, it will replace the requested quiz.
 */
function watupro_shortcode( $attr ) {
	global $wpdb, $post, $user_ID;
	$exam_id = intval($attr[0]);
	$in_progress = null;

	$contents = '';
	if(!is_numeric($exam_id)) return $contents;
	
	// select exam
	$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE id=%d", $exam_id));		
	if(empty($exam)) return sprintf(__('%s not found', 'watupro'), WATUPRO_QUIZ_WORD);
	$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
	
	// override design theme per quiz?
	$design_theme = empty($advanced_settings['design_theme']) ? null : $advanced_settings['design_theme'];
	
	watupro_vc_scripts($design_theme);
	if(class_exists('WTPReports') and (strstr($exam->final_screen, '[watupror-pie-chart') or strstr($exam->final_screen, '[watupror-performance-chart'))) {
		WTPReports::$add_scripts = true;
		WTPReports::print_scripts();
	}
	
	// enqueue dynamic CSS
	 wp_enqueue_style(
			'watupro-dynamic-style',
			site_url("?watupro_dynamic_css=1"),
			array(),
			'1.0');
	
	ob_start();
	
	if(watupro_intel()) WatuPROIntelligence :: conditional_scripts($exam_id);
	watupro_conditional_scripts($exam);	
	
	// passed question ids?	
	if(!empty($attr['question_ids'])) $passed_question_ids = $attr['question_ids'];
		
	if(is_user_logged_in() and empty($advanced_settings['dont_load_inprogress']) and empty($attr['dont_load_inprogress'])) {
		$in_progress = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS." 
			WHERE user_id=%d AND exam_id=%d AND in_progress=1 ORDER BY ID DESC LIMIT 1", $user_ID, $exam_id));
	}
	
	// allow passing user choice via shortcode atts
	if(!empty($attr['user_choice'])) {
		// override tags from shortcode?
		if(!empty($attr['tags'])) $advanced_settings['tags'] = sanitize_text_field(trim($attr['tags']));		
		
		$advanced_settings['user_choice'] = 1;
		$advanced_settings['user_choice_modes'] = explode(",", str_replace(' ', '', sanitize_text_field($attr['user_choice'])));
		$exam->advanced_settings = serialize($advanced_settings);
	}
	
	// user chooses questions?
	if(!empty($advanced_settings['user_choice']) and empty($passed_question_ids) and watupro_intel() 
			and empty($in_progress->ID) and $exam->time_limit <= 0) {		
	   $content = WatuPROIUserChoice :: load($exam);	  
	   return $content;
	}
	
	// submitting without ajax?	
	if(!empty($_POST['no_ajax']) and !empty($exam->no_ajax)) {		
		require(WATUPRO_PATH."/show_exam.php");
		$contents = ob_get_clean();
		$contents = apply_filters('watupro_content', $contents);
		return $contents;
	}
	
	// other cases, show here
	if(empty($_GET['waturl']) or !$exam->shareable_final_screen) {
		// showing the exam
		if(@$exam->mode == 'practice' and watupro_intel()) WatuPracticeController::show($exam);
		else include(WATUPRO_PATH . '/show_exam.php');
		$contents = ob_get_contents();
	}
	else {
		// showing taking results
		$url = @base64_decode($_GET['waturl']); 
		
		list($exam_id, $tid) = explode("|", $url); 
		if(!is_numeric($exam_id) or !is_numeric($tid)) return $contents;
		
		// must check if public URL is allowed 
		$taking = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $tid));
		$contents = WatuPRO::cleanup($taking->details, 'web');
		
		$post->ID = 0;
		$post->comment_status = 'closed';
	}
	
	ob_end_clean();			
	$contents = apply_filters('watupro_content', $contents);
	
	return $contents;
}

// shortcodes to list exams 
function watupro_listcode($attr, $content = '') {
	global $wpdb;
	$cat_id = isset($attr[0]) ? esc_attr($attr[0]) : '';
	if(empty($cat_id)) $cat_id = isset($attr['cat_id']) ? esc_attr($attr['cat_id']) : "";
	
	// cat_id coming from post droppdown?
	if(!empty($_POST['watupro_cat_id'])) $cat_id = sanitize_text_field($_POST['watupro_cat_id']);
		
	// define orderby
	$ob = @$attr['orderby'];
	if(empty($ob)) $ob = @$attr[1];
		
	switch($ob) {		
		case 'title': $orderby = "tE.name"; break;
		case 'latest': $orderby = "tE.ID DESC"; break;
		case 'created': default: $orderby = "tE.ID"; break;
	}
	
	watupro_vc_scripts();
	
	// if include subcats, we have to add subs to cats
	if(!empty($attr['include_subcats']) and !empty($cat_id)) {
		$cat_ids = explode(",", $cat_id);
		$cat_ids = watupro_int_array($cat_ids);
		$cat_ids = array_filter($cat_ids);
		$final_cat_ids = array();
		
		$sub_ids = $wpdb->get_results("SELECT ID FROM ".WATUPRO_CATS." WHERE parent_id IN (".implode(",", $cat_ids).")");
		foreach($cat_ids as $cid) $final_cat_ids[] = $cid;
		foreach($sub_ids as $sub_id) $final_cat_ids[] = $sub_id->ID;
		
		$cat_id = implode(',', $final_cat_ids);
 	}
	
	$show_status = empty($attr['show_status']) ? false : true;
	$content = WTPExam::show_list($cat_id, $orderby, $show_status, $content, $attr);
	
	return $content;	
}

// outputs my exams page in any post or page
function watupro_myexams_code($attr) {
	global $post;
	$cat_id = (!isset($attr[0])) ? '' : esc_attr($attr[0]);
	$status = empty($attr['status']) ? '' : esc_attr($attr['status']);

	if(!empty($_GET['view_details'])) {
      ob_start();
		watupro_taking_details(true);
		$content = ob_get_contents();
      ob_end_clean();
      return $content;			
	}
	
	$content = '';
	if(!is_user_logged_in()) return __('This content is only for logged in users', 'watupro');
	watupro_vc_scripts();
	
	// define orderby
	$ob = @$attr[1];	
	
	switch($ob) {		
		case 'title': $orderby = "tE.name"; break;
		case 'latest': $orderby = "tE.ID DESC"; break;
		case 'created': default: $orderby = "tE.ID"; break;
	}

	ob_start();
	$reorder_by_latest_taking = empty($attr['reorder_by_latest_taking']) ? false : true;
	
	watupro_my_exams($cat_id, $orderby, $status, true, $reorder_by_latest_taking, $attr);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

// outputs my certificates in any post or page
function watupro_mycertificates_code($attr) {
	$content = '';
	if(!is_user_logged_in()) return __('This content is only for logged in users', 'watupro');
	watupro_vc_scripts();
	
	ob_start();	
	watupro_my_certificates(true);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

// outputs generic leaderboard from all tests
function watupro_leaderboard($attr) {
	global $wpdb;
	watupro_vc_scripts();
	
	$num = empty($attr[0]) ? 0 : $attr[0]; // number of users to show
	if(empty($num) or !is_numeric($num)) $num = 10;
	
	// now select them ordered by total points
	$users = $wpdb -> get_results("SELECT SUM(tT.points) as points, tU.user_login as user_login 
		FROM {$wpdb->users} tU JOIN ".WATUPRO_TAKEN_EXAMS." tT ON tT.user_id = tU.ID
		WHERE tT.in_progress = 0 GROUP BY tU.ID ORDER BY points DESC LIMIT $num");
	
	$table = "<table class='watupro-leaderboard'><tr><th>".__('User', 'watupro')."</th><th>".__("Points", 'watupro')."</th></tr>";
	
	foreach($users as $user) {
		$table .= "<tr><td>".$user->user_login."</td><td>".$user->points."</td></tr>";
	}
	
	$table .= "</table>";
	
	return $table;
} // end watupro_leaderboard

// defines the position of the logged in user (or passed user ID) in the leaderboard
function watupro_leaderboard_position($attr) {
	global $wpdb;
	$user_id = (empty($attr['user_id']) or !is_numeric($attr['user_id'])) ? get_current_user_id() : intval($attr['user_id']);
	if(empty($user_id)) return '';
	
	// select top 1000  
	$users = $wpdb -> get_results("SELECT SUM(tT.points) as points, tU.ID as user_id 
		FROM {$wpdb->users} tU JOIN ".WATUPRO_TAKEN_EXAMS." tT ON tT.user_id = tU.ID
		WHERE tT.in_progress = 0 GROUP BY tU.ID ORDER BY points DESC");
		
	$pos = -1;
	foreach($users as $cnt => $user) {
		$cnt++;
		if($user->user_id == $user_id) {
			$pos = $cnt;
			break;
		}
	}	// end foreach
	
	if($pos == -1) return __('Not in the leaderboard.', 'watupro');
	else return $pos;
}

// displays data from user profile of the currently logged user
function watupro_userinfo($atts) {
	global $user_ID;
	
	// let's allow user ID to be passed or taken from certificate
	if(!empty($atts['user_id'])) {
		if($atts['user_id'] == 'certificate') {
			$user_id = $_POST['watupro_certificate_user_id'];
		}
		if(is_numeric($atts['user_id'])) $user_id = $atts['user_id'];
	}		
	
	if(empty($user_id) and !is_user_logged_in()) return @$atts[1];
	if(empty($user_id)) $user_id = $user_ID;	
		
	$field = $atts[0];
		
	$user = get_userdata($user_id);
	
	if(isset($user->data->$field) and !empty($user->data->$field)) return $user->data->$field;
	if(isset($user->data->$field) and empty($user->data->$field)) return @$atts[1];
	
	// not set? must be in meta then
	$metas = get_user_meta($user_id);
	if(count($metas) and is_array($metas)) {
		foreach($metas as $key => $meta) {
			if($key == $field and !empty($meta[0])) return $meta[0];
			if($key == $field and empty($meta[0])) return @$atts[1];
		}
	}
	
	// nothing found, return the default if any
	return @$atts[1];
}

// quiz info showing the points, percent or grade on a given quiz and user
function watupro_result($atts) {
	global $wpdb, $user_ID;
	$quiz_id = intval(@$atts['quiz_id']);
	$user_id = empty($atts['user_id']) ? $user_ID : intval($atts['user_id']);
	if(empty($user_id) and empty($_COOKIE['watupro_taking_id'])) return __('N/a', 'watupro');
	
	watupro_vc_scripts();
	if(class_exists('WTPReports')) {
		WTPReports::$add_scripts = true;
		WTPReports::print_scripts();
	}
	
	$quiz_sql = $taking_id_sql = '';
	if(!empty($quiz_id)) $quiz_sql = $wpdb->prepare(" tT.exam_id=%d AND ", $quiz_id);
	if(!empty($_COOKIE['watupro_taking_id']) and empty($quiz_id)) $taking_id_sql = $wpdb->prepare(" tT.ID = %d AND ", intval($_COOKIE['watupro_taking_id']));	
	
	$result = $wpdb->get_row($wpdb->prepare("SELECT tT.ID as ID, tT.points as points, tT.percent_correct as percent_correct, 
		tG.gtitle as grade_title, tT.details as details, tT.catgrades_serialized as catgrades_serialized 
		FROM ".WATUPRO_TAKEN_EXAMS." tT LEFT JOIN ".WATUPRO_GRADES." tG ON tT.grade_id = tG.ID
		WHERE $quiz_sql $taking_id_sql tT.user_id=%d AND tT.in_progress=0 ORDER BY tT.ID DESC LIMIT 1", $user_id));	
	
		
	if(empty($result->ID)) {		
		if(!empty($atts['placeholder'])) return $atts['placeholder'];
		else return "";
	}	
		
	$what = empty($atts['what']) ? 'points' : trim($atts['what']);
	
	// when cat_id is passed and catgrades_serialized is not empty, we'll override $result  with that category details
	if(!empty($atts['cat_id']) and !empty($result->catgrades_serialized)) {
		$catgrades = unserialize(stripslashes($result->catgrades_serialized));
		
		$cat_details = null;
		foreach($catgrades as $catgrade) {
			if($catgrade['cat_id'] == $atts['cat_id']) {
				$result->grade_title = $catgrade['gtitle'];
				$result->percent_correct = $catgrade['percent'];
				$result->points = $catgrade['points'];
			}
		} // end foreach
	} // end if
	
	$content = '';
	switch($what) {
		case 'grade': $content = stripslashes($result->grade_title); break;
		case 'percent': $content = $result->percent_correct; break;
		case 'percent_points': $content = $result->percent_points; break;
		case 'details': $content = stripslashes($result->details); break;
		case 'points':
		default:
			$content = $result->points;
		break;
	}		 
	
	if(empty($content) and !empty($atts['placeholder'])) $content = $atts['placeholder'];	
	
	return $content;
} // end watupro_result

// shortcode for showing the basic barchart included in the core WatuPRO
// call this ONLY in the Final Screen of the quiz
function watupro_basic_chart($atts) {
	$taking_id = @$GLOBALS['watupro_taking_id'];
	if(empty($taking_id)) $taking_id = @$GLOBALS['watupro_view_taking_id'];
	if(empty($taking_id)) return '';
	$content = WatuPROTaking :: barchart($taking_id, $atts);
	return $content;
}

// num allowed quiz attempts total and num left for current user
function watupro_quiz_attempts($atts) {
	global $wpdb, $user_ID;
	$quiz_id = intval($atts['quiz_id']);
	if(empty($quiz_id)) return '';
	
	$show = ($atts['show'] == 'total') ? 'total' : 'left';
	
	// select quiz ID and num attempts allowed
	$quiz = $wpdb->get_row($wpdb->prepare("SELECT require_login, take_again, times_to_take, takings_by_ip 
		FROM "  . WATUPRO_EXAMS. " WHERE ID=%d", $quiz_id));
		
	// no takings by IP and (no login required OR login required but take_again and no times_to_take limit)
	if(!$quiz->takings_by_ip and (!$quiz->require_login or ($quiz->take_again and !$quiz->times_to_take))) return __('Unlimited', 'watupro');	
	
	// takings by IP is checked first in can_retake, so we'll use it here
	if($quiz->takings_by_ip) {
		if($show == 'total') return $quiz->takings_by_ip;
		
		// else see how many this user has left
		$num_attempts = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM ".WATUPRO_TAKEN_EXAMS." 
			WHERE exam_id=%d AND ip=%s AND in_progress=0", $quiz_id, watupro_user_ip()));
		
		$num_left = $quiz->takings_by_ip - $num_attempts;
		if($num_left < 0) $num_left = 0;		
		return $num_left;	
	}
	
	// when quiz requires login:
	if($quiz->require_login) {
		$total = $quiz->take_again ? $quiz->times_to_take : 1;
		
		if($show == 'total') return $total;
		
		// else see how many this user has left
		$num_attempts = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM ".WATUPRO_TAKEN_EXAMS." 
			WHERE exam_id=%d AND user_id=%d AND in_progress=0", $quiz_id, $user_ID));
			
		$num_left = $total - $num_attempts;	
		if($num_left < 0) $num_left = 0;		
		return $num_left;
	}
}

function watupro_shortcode_takings($atts) {
	global $post, $wp, $wpdb;
	
	$current_url = home_url(add_query_arg(array(),$wp->request));		
	$_GET['current_url'] = $current_url;
	watupro_vc_scripts();
	ob_start();
	
	if(!empty($_GET['watupro_taking_id'])) {
		// return details for specific taking
		$_GET['id'] = $_GET['watupro_taking_id'];		
		watupro_taking_details(true);
		$content = ob_get_clean();
		return $content;
	}	
	
	if(!empty($_GET['watupro_edit_taking'])) {
		require_once(WATUPRO_PATH."/i/models/teacher.php");
		$_GET['id'] = $_GET['taking_id'];
		
		WatuPROITeacherController::edit_taking();
		$content = ob_get_clean();
		return $content;	
	}
	
	$atts['current_url'] = $current_url;	
	watupro_takings(true, $atts);
	$content = ob_get_clean();
	return $content;
}

// will display the math result of the answers of passed question IDs
// example 5 + 8. Note that 5 and 8 are question IDs, not numbers! 
// the numbers are user's answers to those questions
// $atts['math'] - the math expression can contain only 2 values and one operator
// $atts['var'] - whether to store the value in a variable for future usage with the shortcode. Defaults to none.
// $atts['output'] - whether to output the result, defaults to 1.
function watupro_calculator($atts) {
	if(empty($atts['math'])) return '';
	global $wpdb;
	
	$taking_id = @$GLOBALS['watupro_taking_id'];
	if(empty($taking_id)) $taking_id = @$GLOBALS['watupro_view_taking_id'];
	if(empty($taking_id)) return '';
	
	$math = $atts['math'];
	
	// calculate basic math NYI
	if(strstr($math, '+')) {
		$parts = explode('+', $math);
		$op = '+';
	}
	if(strstr($math, '-')) {
		$parts = explode('-', $math);
		$op = '-';
	}
	if(strstr($math, '*')) {
		$parts = explode('*', $math);
		$op = '*';
	}
	if(strstr($math, '/')) {
		$parts = explode('/', $math);
		$op = '/';
	}
	$left = $parts[0];
	$right = $parts[1];
	
	// replace the numeric values with the actual answers given by user
	$answers = $wpdb->get_results($wpdb->prepare("SELECT ID, question_id, answer FROM ".WATUPRO_STUDENT_ANSWERS."
		WHERE taking_id=%d", $taking_id));
	
	foreach($answers as $answer) {
		if($answer->question_id == $left) $left = $answer->answer;
		if($answer->question_id == $right) $right = $answer->answer;
	}	
	
	if(!is_numeric($left)) {	
		// maybe it's a variable?
		if(isset($GLOBALS['watupro_calc_'.$left])) $left = $GLOBALS['watupro_calc_'.$left];
		else $left = 0;
	}
	if(!is_numeric($right)) {		
		if(isset($GLOBALS['watupro_calc_'.$right])) $right = $GLOBALS['watupro_calc_'.$right];
		else $right = 0;
	}
	// echo "LEFT $left RIGHT $right";
		
	switch($op) {
		case '+': $result = $left + $right; break;
		case '-': $result = $left - $right; break;
		case '*': $result = $left * $right; break;
		case '/': $result = $right ? $left / $right : __('N/a', 'watupro'); break;
	}
	
	// assign result to variable?
	if(!empty($atts['var'])) $GLOBALS['watupro_calc_'.$atts['var']] = $result;
	
	if(!isset($atts['output']) or $atts['output']==1) return $result;
} // end watupro_calculator

// how many users completed a quiz (with a given grade, points or percent correct)
// $atts['quiz_id'] - required, the ID of the quiz
// $atts['grade_id'] - optional, grade ID
// $atts['points'] - optional, possible formats: = X, > X, <= X
// $atts['percent_correct'] - optional, possible formats: = X, > X, <= X
// $atts['return'] - "number" or "percent" of users, defaults to number
function watupro_users_completed($atts) {
	global $wpdb;
	
	$quiz_id = intval(@$atts['quiz_id']);
	if(empty($quiz_id)) return "";
	
	// required grade?
	$grade_sql = "";
	if(!empty($atts['grade_id']) and is_numeric($atts['grade_id'])) $grade_sql = $wpdb->prepare(" AND grade_id=%d ", intval($atts['grade_id']));
	
	// points condition?
	$points_sql = "";
	if(!empty($atts['points'])) {
		$atts['points'] = html_entity_decode($atts['points']);
		$atts['points'] = preg_replace("/[^\d\.<>=]/", "", $atts['points']);
		$points_sql = " AND points ".$atts['points'];
	}

	// percent correct condition?
	$percent_sql = "";
	if(!empty($atts['percent_correct'])) {
		$atts['percent_correct'] = html_entity_decode($atts['percent_correct']);
		$atts['percent_correct'] = preg_replace("/[^\d\.\>\=\<]/", "", $atts['percent_correct']);
		$percent_sql = " AND percent_correct ".$atts['percent_correct'];
	}

	if(empty($atts['catgrade_id']) or !is_numeric($atts['catgrade_id'])) {		
		$num_users = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM ".WATUPRO_TAKEN_EXAMS."
		WHERE exam_id=%d AND in_progress=0 $grade_sql $points_sql $percent_sql", $quiz_id));
	}
	else {
		// if category grade ID is used, we'll need more complicated query
		$takings = $wpdb->get_results($wpdb->prepare("SELECT catgrades_serialized FROM ".WATUPRO_TAKEN_EXAMS."
			WHERE exam_id=%d AND in_progress=0 $grade_sql $points_sql $percent_sql", $quiz_id));
		$num_users = 0;
		
		foreach($takings as $taking) {
			$catgrades = unserialize(stripslashes($taking->catgrades_serialized));
			foreach($catgrades as $catgrade) {
				if($catgrade['grade_id'] == $atts['catgrade_id']) $num_users++;
			}
		}	// end foreach taking
	} // end if calculating with $atts['catgrade_id']
		
	// return number or percent?
	if(!empty($atts['return'])	and $atts['return'] == 'percent') {
		
		$all_users = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM ".WATUPRO_TAKEN_EXAMS."
			WHERE exam_id=%d AND in_progress=0", $quiz_id));			
		$percent = empty($all_users) ? 0 : round(100 * ($num_users / $all_users));
		return $percent;	
	}
		
	return $num_users;
	
} // end watupro_users_completed

// generates re-take quiz button or link 
// in fact this is just a reload button/link so you could use it everywhere
// window.location.reload(true);
function watupro_retake_button($atts) {
	global $post;
	$post_id = empty($post->ID) ? @$_POST['post_id'] : $post->ID;	
	$url = get_permalink($post_id);	
	$url = add_query_arg('timestamp', time(), $url);
	
	
	$type = empty($atts['type']) ? 'button' : $atts['type'];
	if($type != 'button' and $type != 'link') $type = 'button';
	
	$css_class = empty($atts['class']) ? '' : ' class="'.$atts['class'].'" ';
	
	$text = empty($atts['text']) ? __('Try again', 'watupro') : $atts['text'];
		
	if($type == 'button') {
		$output = '<input type="button" onclick="window.location=\''.$url.'\'" value="'.$text.'" '.$css_class.'>';
	}
	else $output = '<a href="'.$url.'" '.$css_class.'>'.$text.'</a>';
	
	return $output;
}

// show "segment" stats: this will be the average % correct or average points for the quiz from all users
// who answered the same way in a given question
// $atts['quesiton_id'] - the ID of the question asked
// $atts['segment'] - the answer which will be used as segment. For example if answer=ABC we'll get stats for all
// users who answered "ABC" on this question.
// If $atts['segment'] is empty, we assume "current answer" in which case $taking_id must be present
// $atts['criteria'] - 'percent', 'points', 'grade' or 'category_grade'
// when 'grade' or 'category_grade' optional parameter 'grade_id' or 'catgrade_id' can specify the grade.
// if missing, use the current user's grade. If criteria is 'category_grade' either 'catgrade_id' or 'category_id' becomes required 
// because we must know the category at least. 
// If the shortcode is used inside the "common category grade output" area category_id="this"
// when 'grade' or 'category_grade' the shortcode can be used only in the "final screen"
function watupro_segment_stats($atts) {
	global $wpdb;
		
	$question_id = intval(@$atts['question_id']);
	if(empty($question_id)) return __('N/a', 'watupro').'<!-- no question ID-->';
	
	$taking_id = @$GLOBALS['watupro_taking_id'];
	if(empty($taking_id)) $taking_id = @$GLOBALS['watupro_view_taking_id'];
	$taking_id = intval($taking_id);
	$compare = empty($atts['compare']) ? 'same' : $atts['compare'];
	if(!in_array($compare, array('same', 'worse', 'better'))) $compare = 'same';
	
	if(empty($taking_id) and empty($atts['segment'])) return __('N/a', 'watupro').'<!-- no taking ID and no segment-->';
	
	// if segment is empty here, let's find it based on the latest taking
	if(empty($atts['segment'])) {
		$segment = $wpdb->get_var($wpdb->prepare("SELECT answer FROM ".WATUPRO_STUDENT_ANSWERS."
			WHERE taking_id=%d AND question_id=%d", $taking_id, $question_id));
		$atts['segment'] = $segment;	
	}
	
	$atts['segment'] = sanitize_text_field($atts['segment']);
	
	// now find the desired average
	$criteria = empty($atts['criteria']) ? 'percent_correct' : $atts['criteria'];
	if(!in_array($criteria, array('percent_correct', 'points', 'grade', 'category_grade'))) $criteria = 'percent_correct';
	
	// if there is no taking ID we'll take stats from all quizzes on this question.
	// if there is one, we'll restrict to the quiz
	$exam_sql = '';
	if(!empty($taking_id)) {
		$exam_id = $wpdb->get_var($wpdb->prepare("SELECT exam_id FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $taking_id));
		$exam_sql = $wpdb->prepare(" tT.exam_id=%d AND ", $exam_id);
	}
	
	// points or percent
	if($criteria == 'points' or $criteria == 'percent_correct') {
		$stats_value = $wpdb->get_var($wpdb->prepare("SELECT AVG(tT.$criteria) 
		FROM ".WATUPRO_TAKEN_EXAMS." tT WHERE $exam_sql  tT.in_progress=0
		AND tT.ID IN (SELECT taking_id FROM ".WATUPRO_STUDENT_ANSWERS." WHERE question_id=%d AND answer LIKE '%s')",
		$question_id, $atts['segment']));
	
		$stats_value = round($stats_value);
	}
	
	// % who achieved this grade
	if($criteria == 'grade') {
		// what is the grade ID
		if(empty($taking_id)) return __('N/a', 'watupro').'<!-- no taking ID-->';
		if(empty($atts['grade_id'])) $grade_id = $wpdb->get_var($wpdb->prepare("SELECT grade_id FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $taking_id));
		else $grade_id = intval($atts['grade_id']);
		
		$quiz_id = $wpdb->get_var($wpdb->prepare("SELECT exam_id FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $taking_id));
		
		// grade ID SQL depending on $compare
		$grade_id_sql = '';
		if($compare == 'same') $grade_id_sql = $wpdb->prepare('AND tT.grade_id=%d', $grade_id);
		if($compare == 'worse' or $compare == 'better') {
			// get all worse or better grades
			$grades = WTPGrade :: get_grades($quiz_id);
			$compare_grade = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_GRADES." WHERE ID=%d", $grade_id));			
			$grade_ids = array(-1);
			
			foreach($grades as $grade) {				
				if($grade->ID == $grade_id) continue; // we don't need the same grade
				if($compare == 'worse' and $compare_grade->gto <= $grade->gto) continue;
				if($compare == 'better' and $compare_grade->gto >= $grade->gto) continue;
				$grade_ids[] = $grade->ID;
			}
			
			$grade_id_sql = " AND tT.grade_id IN (".implode(',', $grade_ids).") ";
			
		} // end worse or better grade SQL
		//echo $grade_id_sql;
		$num_grade = $wpdb->get_var($wpdb->prepare("SELECT COUNT(tT.ID) FROM ".WATUPRO_TAKEN_EXAMS." tT 
			WHERE tT.exam_id=%d $grade_id_sql
			AND tT.ID IN (SELECT taking_id FROM ".WATUPRO_STUDENT_ANSWERS." WHERE question_id=%d AND answer LIKE '%s')", 
			$quiz_id, $question_id, $atts['segment']));
		
		$num_all = $wpdb->get_var($wpdb->prepare("SELECT COUNT(tT.ID) FROM ".WATUPRO_TAKEN_EXAMS." tT
			WHERE exam_id=%d AND in_progress=0 AND tT.ID IN (SELECT taking_id FROM ".WATUPRO_STUDENT_ANSWERS." WHERE question_id=%d AND answer LIKE '%s')", 
			$quiz_id, $question_id, $atts['segment']));
		
		// calculate the percentage
		$stats_value = empty($num_all) ? 0 : ((100 * $num_grade) / $num_all);	
		$stats_value = round($stats_value);
	}
	
	// % who achieved this category grade
	if($criteria == 'category_grade') {
		if(empty($taking_id)) return __('N/a', 'watupro').'<!-- no taking ID-->';

		// the shortcode is used in the "catgrades" loop?
		if(empty($atts['catgrade_id']) and empty($atts['category_id'])) return __('N/a', 'watupro').'<!-- no catgarde ID and category ID-->';
		
		if(empty($atts['catgrade_id'])) {
			// in this case $atts['category_id'] will be present
			$catgrades = $wpdb->get_var($wpdb->prepare("SELECT catgrades_serialized FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $taking_id));
			
			$catgrades = unserialize(stripslashes($catgrades));
			
			foreach($catgrades as $catgrade) {
				if($catgrade['cat_id'] == $atts['category_id']) $catgrade_id = $catgrade['grade_id'];
			}
		}
		else $catgrade_id = intval($atts['catgrade_id']);
		
		if(empty($catgrade_id)) return __('N/a', 'watupro').'<!-- catgrade ID not found -->';
		
		// grade ID SQL depending on $compare	
		if($compare == 'worse' or $compare == 'better') {
			// get all worse or better cat grades			
			$compare_grade = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_GRADES." WHERE ID=%d", $catgrade_id));
			$quiz_id = $wpdb->get_var($wpdb->prepare("SELECT exam_id FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $taking_id));
			$grades = WTPGrade :: get_grades($quiz_id, $compare_grade->cat_id);
			$grade_ids = array();
			
			foreach($grades as $grade) {
				if($grade->ID == $catgrade_id) continue; // we don't need the same grade
				if($compare == 'worse' and $grade->gto >= $compare_grade->gto) continue;
				if($compare == 'better' and $grade->gto < $compare_grade->gto) continue;
				$grade_ids[] = $grade->ID;
			}			
		} // end worse or better grade SQL
		
		$quiz_id = $wpdb->get_var($wpdb->prepare("SELECT exam_id FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $taking_id));
		
		// catgrade ID found. Let's see the number of takings on the test and the number of users who got this grade
		$takings = $wpdb->get_results($wpdb->prepare("SELECT catgrades_serialized FROM ".WATUPRO_TAKEN_EXAMS." tT
			WHERE tT.exam_id=%d AND tT.in_progress=0  AND tT.ID IN (SELECT taking_id FROM ".WATUPRO_STUDENT_ANSWERS." WHERE question_id=%d AND answer LIKE '%s')", 
			$quiz_id, $question_id, $atts['segment']));
			
		$num_grade = 0;
		$num_all = count($takings);		
		
		foreach($takings as $taking) {
			$catgrades = unserialize(stripslashes($taking->catgrades_serialized));
			foreach($catgrades as $catgrade) {
				if($catgrade['grade_id'] == $catgrade_id and $compare == 'same') $num_grade++;
				if($compare != 'same' and in_array($catgrade['grade_id'], $grade_ids)) $num_grade++; 
			}
		}	// end foreach taking
		
		// calculate the percentage
		$stats_value = empty($num_all) ? 0 : ((100 * $num_grade) / $num_all);	
		$stats_value = round($stats_value);
	}
		
	return $stats_value;	
} // end watupro segment stats

// output link to print final screen PDF
function watupro_pdf_link($atts, $content = null) {
	// taking ID comes from the current ID just like in certificates	
	$taking_id = @$GLOBALS['watupro_taking_id'];
	if(empty($taking_id)) $taking_id = @$GLOBALS['watupro_view_taking_id'];
	$taking_id = intval($taking_id);
	
	if(!function_exists('pdf_bridge_init')) return __('PDF Bridge not installed.', 'watupro');
		
   $url = site_url("?watupro_view_pdf=1&tid=".$taking_id);
   
   $target = empty($atts['target']) ? '_self' : sanitize_text_field($atts['target']);
   
   if(!empty($content)) return '<a href="'.$url.'" target="'.$target.'">'.$content.'</a>';
   
   $link_text = empty($atts['link_text']) ? __('Download PDF', 'watupro') : sanitize_text_field($atts['link_text']);
   
   return '<a class="watupro-pdf" href="'.$url.'" target="'.$target.'">'.$link_text.'</a>';
}

// output paginator outside of the quiz
function watupro_shortcode_paginator($atts) {
	global $wpdb, $user_ID, $post;
	
	if(!is_singular()) return "";
	
	if(!empty($atts['quiz_id'])) $exam_id = intval($atts['quiz_id']);
	else {		
		// figure it out from the current post
		if(!strstr($post->post_content, '[watupro ') and !strstr($post->post_content, '[WATUPRO ')) return "";
		if(strstr($post->post_content, '[watupro ')) $parts = explode('[watupro ', $post->post_content);
		else $parts = explode('[WATUPRO ', $post->post_content);

		if(preg_match('/^%d%s/', $parts[1])) $sparts = explode(' ', $parts[1]); // maybe space after the exam ID and other arguments
		else $sparts = explode(']', $parts[1]); // in the other case just shortcode like [watupro 7]
		$exam_id = $sparts[0];
	}
	
	if(empty($exam_id)) return '';

	$_question = new WTPQuestion();
	$_exam = new WTPExam();
	
	$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE id=%d", $exam_id));
		
	if(empty($exam) or !$exam->is_active) return "";

	$is_vertical = empty($atts['vertical']) ? false : true;
	
	$_question->exam = $exam; 
	$advanced_settings = unserialize( stripslashes($exam->advanced_settings));
	WTPQuestion :: $advanced_settings = $advanced_settings;
	if(watupro_intel()) WatuPROIQuestion :: $advanced_settings = $advanced_settings;
	$in_progress = null;
	
	if(is_user_logged_in()) {	
		$in_progress = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS." 
			WHERE user_id=%d AND exam_id=%d AND in_progress=1 ORDER BY ID DESC LIMIT 1", $user_ID, $exam_id));
	}	
	else {
		if(!empty($_COOKIE['watupro_taking_id_' . $exam_id])) {
			$in_progress = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS." 
				WHERE ID=%d AND in_progress=1", intval($_COOKIE['watupro_taking_id_' . $exam_id])));
		}
	} // end getting in_progress
	
	if(!empty($advanced_settings['dont_load_inprogress'])) $in_progress = null;
	
	// loading serialized questions or questions coming by POST
	if(!empty($_POST['action']) or !empty($in_progress->serialized_questions)) {	
		$serialized_questions = empty($_REQUEST['watupro_questions']) ? @$in_progress->serialized_questions : $_REQUEST['watupro_questions'];
		$all_question = watupro_unserialize_questions($serialized_questions);
	}
	if(empty($all_question)) $all_question = WTPQuestion :: select_all($exam);
	$cnt_questions	= count($all_question);
	
	$paginator = empty($atts['paginator']) ? 'questions' : sanitize_text_field($atts['paginator']);
	
	// maybe this will handle also category paginator 
	switch($paginator) {
		case 'categories':
         return  WTPExam :: category_paginator($all_question, $exam, $in_progress, $is_vertical);
		break;
		default:
		case 'questions':
			return WTPExam::paginator($cnt_questions, $in_progress, $is_vertical);
		break;
	} // end switch
} // end paginator shortcode

// multiple quiz result shortcode
// $atts['quiz_ids'] = comma separated list of quiz IDs
// $atts['condition_ponts'] = X-Y values for points range
// $atts['condition_percent_correct'] = X-Y values for percent correct range
// $atts['condition_percent_max'] = X-Y values for percent of maximum points range
// vars in content: %%MULTI_POINTS%%, %%MULTI_PERCENTAGE%%, %%MULTI_PERCENTAGEOFMAX%%, %%MULTI_CORRECT%%, %%MULTI_WRONG%%, %%MULTI_EMPTY%%, 
// %%MULTI_MAX-POINTS%%
function watupro_multiquiz($atts, $content = '') {
	global $wpdb;
	
	if(!is_user_logged_in()) return "";
	$user_id = get_current_user_id();
	
	if(empty($atts['quiz_ids'])) return __('Missing IDs', 'watupro');
	$quiz_ids = explode(',', $atts['quiz_ids']);	
	$quiz_ids = array_map('trim', $quiz_ids);
	$quiz_ids = watupro_int_array($quiz_ids);
	
	// figure out if any conditions are satisfied
	
	// points
	if(!empty($atts['condition_points'])) {
		$points = $wpdb->get_var($wpdb->prepare("SELECT SUM(points) FROM ".WATUPRO_TAKEN_EXAMS." 
			WHERE user_id=%d AND in_progress=0 AND exam_id IN (" .implode(',', $quiz_ids). ")", $user_id));
		
		$points_cond = explode('-', $atts['condition_points']);
		$points_cond = array_map('trim', $points_cond);
		
		if(!isset($points_cond[0]) or !isset($points_cond[1])) return __('Wrong points condition', 'watupro'); 
		if($points_cond[0] > $points or $points > $points_cond[1]) return '<!-- unsatisfied points condition -->';
	}
	
	// percent correct
	if(!empty($atts['condition_percent_correct'])) {
		$percent = $wpdb->get_var($wpdb->prepare("SELECT AVG(percent_correct) FROM ".WATUPRO_TAKEN_EXAMS." 
			WHERE user_id=%d AND in_progress=0 AND exam_id IN (" .implode(',', $quiz_ids). ")", $user_id));
		
		$percent_cond = explode('-', $atts['condition_percent_correct']);
		$percent_cond = array_map('trim', $percent_cond);
		if(!isset($percent_cond[0]) or !isset($percent_cond[1])) return __('Wrong percent correct condition', 'watupro'); 
		if($percent_cond[0] > $percent or $percent > $percent_cond[1]) return '<!-- unsatisfied percent condition -->';
	}
	
	// percent of max
	if(!empty($atts['condition_percent_max'])) {
		$percent_max = $wpdb->get_var($wpdb->prepare("SELECT AVG(percent_points) FROM ".WATUPRO_TAKEN_EXAMS." 
			WHERE user_id=%d AND in_progress=0 AND exam_id IN (" .implode(',', $quiz_ids). ")", $user_id));
		
		$percentmax_cond = explode('-', $atts['condition_percent_max']);
		$percentmax_cond = array_map('trim', $percentmax_cond);
		if(!isset($percentmax_cond[0]) or !isset($percentmax_cond[1])) return __('Wrong percent of max points condition', 'watupro'); 
		if($percentmax_cond[0] > $percent_max or $percent_max > $percentmax_cond[1]) return '<!-- unsatisfied percent of max condition -->';
	}
	
	// and calculate only these averages / totals that are required and not already calculated
	if(strstr($content, '%%MULTI_POINTS%%')) {
		if(!isset($points)) {
			// same query as in the condition
			$points = $wpdb->get_var($wpdb->prepare("SELECT SUM(points) FROM ".WATUPRO_TAKEN_EXAMS." 
			WHERE user_id=%d AND in_progress=0 AND exam_id IN (" .implode(',', $quiz_ids). ")", $user_id));
		}
		$content = str_replace('%%MULTI_POINTS%%', $points + 0, $content);
	}	
	
	if(strstr($content, '%%MULTI_PERCENTAGE%%')) {
		if(!isset($percent)) {
			// same query as in the condition
			$percent = $wpdb->get_var($wpdb->prepare("SELECT AVG(percent_correct) FROM ".WATUPRO_TAKEN_EXAMS." 
			WHERE user_id=%d AND in_progress=0 AND exam_id IN (" .implode(',', $quiz_ids). ")", $user_id));
		}
		
		$percent = round($percent,2);
		$content = str_replace('%%MULTI_PERCENTAGE%%', $percent, $content);
	}
	
	if(strstr($content, '%%MULTI_PERCENTAGEOFMAX%%')) {
		if(!isset($percent_max)) {
			// same query as in the condition
			$percent_max = $wpdb->get_var($wpdb->prepare("SELECT AVG(percent_points) FROM ".WATUPRO_TAKEN_EXAMS." 
			WHERE user_id=%d AND in_progress=0 AND exam_id IN (" .implode(',', $quiz_ids). ")", $user_id));
		}
		$percent_max = round($percent_max,2);
		$content = str_replace('%%MULTI_PERCENTAGEOFMAX%%', $percent_max, $content);
	}
	
		
	if(strstr($content, '%%MULTI_CORRECT%%')) {
		// same query as in the condition
		$correct = $wpdb->get_var($wpdb->prepare("SELECT SUM(num_correct) FROM ".WATUPRO_TAKEN_EXAMS." 
		WHERE user_id=%d AND in_progress=0 AND exam_id IN (" .implode(',', $quiz_ids). ")", $user_id));
		
		$content = str_replace('%%MULTI_CORRECT%%', $correct, $content);
	}
	
	if(strstr($content, '%%MULTI_WRONG%%')) {
		// same query as in the condition
		$wrong = $wpdb->get_var($wpdb->prepare("SELECT SUM(num_wrong) FROM ".WATUPRO_TAKEN_EXAMS." 
		WHERE user_id=%d AND in_progress=0 AND exam_id IN (" .implode(',', $quiz_ids). ")", $user_id));
		
		$content = str_replace('%%MULTI_WRONG%%', $wrong, $content);
	}
	
	if(strstr($content, '%%MULTI_EMPTY%%')) {
		// same query as in the condition
		$empty = $wpdb->get_var($wpdb->prepare("SELECT SUM(num_empty) FROM ".WATUPRO_TAKEN_EXAMS." 
		WHERE user_id=%d AND in_progress=0 AND exam_id IN (" .implode(',', $quiz_ids). ")", $user_id));
		
		$content = str_replace('%%MULTI_EMPTY%%', $empty, $content);
	}
	
	if(strstr($content, '%%MULTI_MAX-POINTS%%')) {
		// same query as in the condition
		$max = $wpdb->get_var($wpdb->prepare("SELECT SUM(max_points) FROM ".WATUPRO_TAKEN_EXAMS." 
		WHERE user_id=%d AND in_progress=0 AND exam_id IN (" .implode(',', $quiz_ids). ")", $user_id));
				
		$content = str_replace('%%MULTI_MAX-POINTS%%', $max + 0, $content);
	}
	
	return $content;
} // end watupro_multiquiz

// answer to a single question
// $atts[question_id] - the question ID
// $atts[user_id] - optional user ID otherwise logged in user
// $atts[taking_id] - optional taking ID otherwise latest taking 
function watupro_answer($atts) {
	$question_id = empty($atts['question_id']) ? 0 : intval($atts['question_id']);
	$user_id = empty($atts['user_id']) ? 0 : intval($atts['user_id']);
	$taking_id = empty($atts['taking_id']) ? 0 : intval($atts['taking_id']);
	
	return watupro_user_answer($question_id, $user_id, $taking_id);
}