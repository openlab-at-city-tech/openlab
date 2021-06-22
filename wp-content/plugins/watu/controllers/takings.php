<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// select taking records for an exam
function watu_takings($in_shortcode = false, $atts = null) {
	global $wpdb, $post;
	
	if($in_shortcode) {
		$_GET['exam_id'] = intval(@$atts['quiz_id']);
	}
	
	// if Namaste! LMS is installed we'll also select courses
	if(class_exists('NamasteLMSCourseModel')) {
		$_course = new NamasteLMSCourseModel();
		$namaste_courses = $_course->select();
	}
	
	// select exam
	$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATU_EXAMS." WHERE ID=%d", intval($_GET['exam_id'])));
	$grades = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATU_GRADES." WHERE  exam_id=%d order by gtitle ", $exam->ID) );
	
	// delete a taking
	if(!empty($_GET['del_taking']) and check_admin_referer('watu_del_taking')) {
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATU_TAKINGS." WHERE ID=%d", intval($_GET['id'])));
		watu_redirect("admin.php?page=watu_takings&exam_id=".$exam->ID);
	}
	
	// mass cleanup
	if(!empty($_POST['delete_all_takings']) and check_admin_referer('watu_delete_all')) {
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATU_TAKINGS." WHERE exam_id=%d", $exam->ID));
	}
	
	// mass delete selected takings
	if(!empty($_POST['del_takings']) and check_admin_referer('watu_del_takings') and !empty($_POST['ids'])) {		
		$ids = array_map('intval', $_POST['ids']);
		
		$wpdb->query("DELETE FROM ".WATU_TAKINGS." WHERE ID IN (". implode(', ', $ids) .")");
	}
	
	// select taking records
	$ob = empty($_GET['ob'])? "tT.id" : sanitize_text_field($_GET['ob']);
	$dir = !empty($_GET['dir'])? $_GET['dir'] : "DESC";


	$offset = empty($_GET['offset']) ? 0 : intval($_GET['offset']);
	$limit_sql = empty($_GET['watu_export']) ? "LIMIT $offset, 10" : "";
	
	// ob, dir and limit can be overwritten by shortcode atts. When limit is passed we will not show pagination (leaderboard)	
	if(!empty($atts['ob']) and in_array($atts['ob'], ['points', 'percent_correct', 'date'] ) ) $ob = 'tT.'.esc_attr($atts['ob']);
	if(!empty($atts['dir'])) $dir = esc_attr($atts['dir']);
	if(!empty($atts['num'])) $limit_sql = $wpdb->prepare(" LIMIT %d ", intval($atts['num']));
	
	if(strtoupper($dir) != 'ASC' and strtoupper($dir) != 'DESC') $dir = 'DESC';
	$odir = ($dir=='ASC')?'DESC':'ASC';	
	
	// filter / search?
	$filters = $joins = array();	
	$filter_sql = $left_join_sql = $role_join_sql = $group_join_sql = $left_join = "";
	$join_sql="LEFT JOIN {$wpdb->users} tU ON tU.ID=tT.user_id";
	
	// display name
	if(!empty($_GET['dn'])) {
	   $_GET['dn'] = sanitize_text_field($_GET['dn']);
		switch($_GET['dnf']) {
			case 'contains': $like="%$_GET[dn]%"; break;
			case 'starts': $like="$_GET[dn]%"; break;
			case 'ends': $like="%$_GET[dn]"; break;
			case 'equals':
			default: $like=$_GET['dn']; break;			
		}
		
		$joins[]= " display_name LIKE '$like' ";
	}
	
	// email
	if(!empty($_GET['email'])) {
	   $_GET['email'] = sanitize_email($_GET['email']); 
		switch($_GET['emailf']) {
			case 'contains': $like="%$_GET[email]%"; break;
			case 'starts': $like="$_GET[email]%"; break;
			case 'ends': $like="%$_GET[email]"; break;
			case 'equals':
			default: $like=$_GET['email']; break;			
		}
		
		$joins[]=$wpdb->prepare(" user_email LIKE %s ", $like);
		$filters[]=$wpdb->prepare(" ((user_id=0 AND email LIKE %s) OR (user_id!=0 AND user_email LIKE %s)) ", $like, $like);
		$left_join = 'LEFT'; // when email is selected, do left join because it might be without logged user
	}
	
	// IP
	if(!empty($_GET['ip'])) {
	   $_GET['ip'] = filter_var($_GET['ip'], FILTER_VALIDATE_IP);
		switch($_GET['ipf']) {
			case 'contains': $like="%$_GET[ip]%"; break;
			case 'starts': $like="$_GET[ip]%"; break;
			case 'ends': $like="%$_GET[ip]"; break;
			case 'equals':
			default: $like=$_GET['ip']; break;			
		}
		
		$filters[]=$wpdb->prepare(" ip LIKE %s ", $like);
	}
	
	// Date
	if(!empty($_GET['date'])) {
	   $_GET['date'] = sanitize_text_field($_GET['date']);
		switch($_GET['datef']) {
			case 'after': $filters[]=$wpdb->prepare(" date>%s ", $_GET['date']); break;
			case 'before': $filters[]=$wpdb->prepare(" date<%s ", $_GET['date']); break;
			case 'equals':
			default: $filters[]=$wpdb->prepare(" date=%s ", $_GET['date']); break;
		}
	}
	
	// Points
	if(!empty($_GET['points'])) {
	   $_GET['points'] = floatval($_GET['points']);
		switch($_GET['pointsf']) {
			case 'less': $filters[]=$wpdb->prepare(" points < %f ", $_GET['points']); break;
			case 'more': $filters[]=$wpdb->prepare(" points > %f ", $_GET['points']); break;
			case 'equals':
			default: $filters[]=$wpdb->prepare(" points=%d ", $_GET['points']); break;
		}
	}
	
	// grade
	if(!empty($_GET['grade_id'])) {
		$filters[] = $wpdb->prepare(" grade_id=%d ", intval($_GET['grade_id']));
	}
	
	// source URL
	if(!empty($_GET['source_url'])) {
		$filters[] = $wpdb->prepare(" source_url=%s ", esc_url_raw($_GET['source_url']));
	}
	
	// Namaste! LMS Course
	if(!empty($_GET['namaste_course_id']) and !empty($namaste_courses)) {
		// let's select here as a subquery might be slower (is it?)
		$namaste_uids = array(-1);
		$namaste_uids1 = $wpdb->get_results($wpdb->prepare("SELECT user_id FROM ".NAMASTE_STUDENT_COURSES." 
			WHERE course_id=%d AND (status='enrolled' OR status='completed')", intval($_GET['namaste_course_id'])));
		foreach($namaste_uids1 as $nu) $namaste_uids[] = $nu->user_id;
		$namaste_uids_sql = implode(",", $namaste_uids);
		$filters[] = " tT.user_id IN ($namaste_uids_sql) ";
	}
		
	// construct filter & join SQLs
	if(count($filters)) {
		$filter_sql=" AND ".implode(" AND ", $filters);
	}
	
	if(count($joins)) {
		$join_sql=" $left_join JOIN {$wpdb->users} tU ON tU.ID=tT.user_id AND "
			.implode(" AND ", $joins);
	}
	
	// select unique source URLs in this quiz
	$source_urls = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT(source_url) FROM ".WATU_TAKINGS." 
		WHERE exam_id=%d AND source_url != ''", intval($_GET['exam_id'])));
	
	$takings = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS tT.*, tU.user_login as user_login, tU.user_email as user_email,
		tG.gtitle as grade_title 
		FROM ".WATU_TAKINGS." tT
		LEFT JOIN ".WATU_GRADES." tG ON tG.ID = tT.grade_id 
		$join_sql
		WHERE tT.exam_id={$exam->ID} $filter_sql 
		ORDER BY $ob $dir $limit_sql");
			
	$count = $wpdb->get_var("SELECT FOUND_ROWS()");	
	
	$delim = get_option('watu_csv_delim');
	if(empty($delim)) $delim = ',';
		
	// export CSV
	if(!empty($_GET['watu_export'])) {
		$newline=watu_define_newline();		
		
		$rows=array();		
		
		if($delim == 'tab') $delim = "\t";
		
		$quotes = get_option('watu_csv_quotes');
		$quote = ($quotes === '0') ? '' : '"';
		
		$rows[]=__('User or IP Address', 'watu').$delim.__('Email address', 'watu').$delim.__('Date', 'watu').$delim.__('Points', 'watu').$delim.__('% Correct Answers', 'watu').
			$delim.__('Num Correct Answers', 'watu').$delim.__('Num Wrong Answers', 'watu').$delim.__('Num Unanswered Questions', 'watu').
			$delim.__('Result/Grade', 'watu');
		foreach($takings as $taking) {
			if(empty($taking->email) and !empty($taking->user_email)) $taking->email = $taking->user_email;
			$row = ($taking->user_id ? $taking->user_login : $taking->ip).$delim.$taking->email.$delim.$quote.date(get_option('date_format'), strtotime($taking->date)).$quote.$delim.
				$taking->points.$delim.$taking->percent_correct.$delim.$taking->num_correct.$delim
				.$taking->num_wrong.$delim.$taking->num_empty.$delim.$quote.$taking->result.$quote;
			$rows[] = $row;		
		} // end foreach taking
		$csv=implode($newline,$rows);		
		
		$now = gmdate('D, d M Y H:i:s') . ' GMT';	
		$filename = 'exam-'.$exam->ID.'-results.csv';	
		header('Content-Type: ' . watu_get_mime_type());
		header('Expires: ' . $now);
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Pragma: no-cache');
		echo $csv;
		exit;
	}	
	
	// this var will be added to links at the view
	$filters_url="dn=".@$_GET['dn']."&dnf=".@$_GET['dnf']."&email=".@$_GET['email']."&emailf=".
		@$_GET['emailf']."&ip=".@$_GET['ip']."&ipf=".@$_GET['ipf']."&date=".@$_GET['date'].
		"&datef=".@$_GET['datef']."&points=".@$_GET['points']."&pointsf=".@$_GET['pointsf'].
		"&grade_id=".@$_GET['grade_id']."&source_url=".@$_GET['source_url'];		
		
	if(!empty($namaste_courses) and !empty($_GET['namaste_course_id'])) {
		$filters_url .= "&namaste_course_id=".intval($_GET['namaste_course_id']);
	}			
		
	// if in shortcode prepare the target URL
	if($in_shortcode) {
		$permalink = get_permalink($post->ID);
		$params = array('exam_id' => $exam->ID);
		$target_url = add_query_arg( $params, $permalink );
	}
	else $target_url = "?page=watu_takings&exam_id=" . $exam->ID;	
		
	$display_filters=(!sizeof($filters) and !sizeof($joins)) ? false : true;	
	
	// shortcode params
	$show_email = isset($atts['show_email']) ? intval($atts['show_email']) : 1;
	$show_points = isset($atts['show_points']) ? intval($atts['show_points']) : 1;
	$show_percent = isset($atts['show_percent']) ? intval($atts['show_percent']) : 1;
	
	wp_enqueue_script('thickbox',null,array('jquery'));
	wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
	if(@file_exists(get_stylesheet_directory().'/watu/takings.php')) include get_stylesheet_directory().'/watu/takings.php';
	else include(WATU_PATH . '/views/takings.php');
}

// display taking details by ajax
function watu_taking_details() {
	global $wpdb, $user_ID;
	
	$view_level = current_user_can('watu_manage') ? 'watu_manage' : 'manage_options';
	
	// select taking
	$taking=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATU_TAKINGS."
			WHERE id=%d", intval($_REQUEST['id'])));
			
	// select user
	$student=$wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->users} 
		WHERE id=%d", $taking->user_id));
		
	if(empty($taking->email) and !empty($student->ID)) $taking->email = $student->user_email;	
	
	// make sure I'm admin or that's me
	if(!current_user_can($view_level) and $student->ID != $user_ID) {
		wp_die( __('You do not have sufficient permissions to access this page', 'watu') );
	}
			
	// select exam
	$exam=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATU_EXAMS." WHERE id=%d", $taking->exam_id));
				
	if(@file_exists(get_stylesheet_directory().'/watu/taking_details.html.php')) include get_stylesheet_directory().'/watu/taking_details.html.php';
	else include(WATU_PATH . '/views/taking_details.html.php');  			
	exit;			
}

// shortcode for showing the basic barchart included in the core WatuPRO
// call this ONLY in the Final Screen of the quiz
function watu_basic_chart($atts) {
	$taking_id = intval($GLOBALS['watu_taking_id']);
	$content = watu_barchart($taking_id, $atts);
	return $content;
}

// basic barchart your points vs avg points, your % vs avg %
// this chart will be loaded by variable or shortcode in the Final screen 
// this function uses globals so it will work properly only when called on controllers/show_exam.php or a shortcode on the Final screen
function watu_barchart($taking_id, $atts) {
	global $wpdb, $achieved;
	
	// normalize params
	$show = empty($atts['show']) ? 'both' : $atts['show'];
	if(!in_array($show, array('both', 'points', 'percent'))) $show = 'both';
	$your_color = empty($atts['your_color']) ? "blue" : $atts['your_color'];
	$avg_color = empty($atts['avg_color']) ? "gray" : $atts['avg_color'];
	$your_percent_text = empty($atts['your_percent_text']) ? __('You: %d%% correct', 'watu') : $atts['your_percent_text'];
	$avg_percent_text = empty($atts['avg_percent_text']) ? __('Avg. %d%% correct', 'watu') : $atts['your_percent_text'];
	$your_points_text = empty($atts['your_points_text']) ? __('Your points: %s', 'watu') : $atts['your_points_text'];
	$avg_points_text = empty($atts['avg_points_text']) ? __('Avg. points: %s', 'watu') : $atts['avg_points_text'];
	$step = 2;
	
	// select taking
	$taking = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATU_TAKINGS." WHERE ID=%d", $taking_id));
	
	// get average points
	$all_point_rows = $wpdb->get_results($wpdb->prepare("SELECT points FROM ".WATU_TAKINGS." WHERE exam_id=%d", $taking->exam_id));
	$all_points = 0;
	foreach($all_point_rows as $r) $all_points += $r->points;	
	$all_points += $achieved;			
	$avg_points = round($all_points / ($wpdb->num_rows + 1), 1);
		
	// the points step should rougly make the higher points bar 200px high
	$more_points = ($avg_points > $taking->points) ? $avg_points : $taking->points;
	if(!$more_points) $more_points = 1; // set to non-zero for division
	$points_step = round(200 / $more_points, 2);
	
	// create & return the chart HTML
	$content = '<table class="watu-basic-chart"><tr>';
	
	if($show == 'points' or $show == 'both') {
		$your_points_text = sprintf($your_points_text, $taking->points);
		$avg_points_text = sprintf($avg_points_text, $avg_points);				
		
		// normalize points here, shouldn't be less than zero when calculating the bar height
		if($taking->points < 0) $taking->points = 0;		
		
		$content .= '<td style="vertical-align:bottom;"><table class="watu-basic-chart-points"><tr><td align="center" style="vertical-align:bottom;">';
		$content .= '<div style="background-color:'.$your_color.';width:100px;height:'.round($points_step * $taking->points). 'px;">&nbsp;</div>'; 
		$content .='</td><td align="center" style="vertical-align:bottom;">';
		$content .= '<div style="background-color:'.$avg_color.';width:100px;height:'.round($points_step * $avg_points). 'px;">&nbsp;</div>';
		$content .='</td></tr><tr><td align="center">' . $your_points_text . '</td><td align="center">'. $avg_points_text .'</td></tr>';
		$content .= '</table></td>';			
	}
	$content .= '</tr></table>';
	
	return $content;
}

function watu_shortcode_takings($atts) {
	ob_start();
	watu_takings(true, $atts);
	$content = ob_get_clean();
	return $content;
}

// handle MoolaMojo integration if enabled
function watu_taking_transfer_moola($taking_id, $exam, $user_id, $points, $grade_id) {
		global $wpdb;
		
		if(empty($user_id)) return false;
		if(empty($points) and empty($grade_id)) return false;
		if(get_option('watu_integrate_moolamojo') != 1) return false;
	
		$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
		if(empty($advanced_settings['transfer_moola'])) return false;
		
		
		if($advanced_settings['transfer_moola_mode'] == 'equal') $credits = $points;
		else {
			// select grade points
			if($grade_id == 0) return false;
			$credits = $wpdb->get_var($wpdb->prepare("SELECT moola FROM ".WATU_GRADES." WHERE ID=%d", $grade_id));			
		}
		
		if($credits == 0) return false;
		
		// actually transfer the moola
		if($credits > 0 ) $reward = true;
		else {
			$reward = false;
			$credits = abs($credits);
		}
		do_action("moolamojo_transaction", $reward, $credits, __('submitted test', 'watu'), $user_id, WATU_TAKINGS, $taking_id);
		
	}  // end transfer_moola
	
// register personal data eraser
function watu_register_eraser($erasers) {
	 $erasers['watu'] = array(
	    'eraser_friendly_name' => __( 'Watu Quiz', 'watu' ),
	    'callback'             => 'watu_erase_data'
	    );
	    
	  return $erasers;
}	

// again deleting user data but this time when called from the WP erase data hook
function watu_erase_data($email_address, $page = 1) {
		 global $wpdb;

		 $number = 200; // Limit us to avoid timing out
  		 $page = (int) $page;
  		 $email_address = sanitize_email($email_address);
  		 
  		 // find student
  		 $user = get_user_by('email', $email_address);
  		 
  		 if(empty($user->ID)) {
  		 	// delete exam results			
			$wpdb->query($wpdb->prepare("DELETE FROM ".WATU_TAKINGS." WHERE email=%s", $email_address));
  		 }
  		 else {
  		 	$wpdb->query($wpdb->prepare("DELETE FROM ".WATU_TAKINGS." WHERE user_id=%d", $user->ID));
  		 }
  		 
  		 return array( 'items_removed' => true,
		    'items_retained' => false, 
		    'messages' => array(), // no messages
		    'done' => true,
		  );
  	} // end data eraser
	