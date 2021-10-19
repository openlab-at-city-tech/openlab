<?php
// model that provides some specific functions regarding a taken exam record
class WatuPROTaking {
	// displays the latest result for a given user. Used from WatuPRO->can_retake when an advanced setting of the quiz
	// says that along with the no-retake message we have to display the latest snapshot
	static function display_latest_result($exam) {
		global $wpdb, $user_ID;
		
		$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
		if(empty($advanced_settings['no_retake_display_result'])) return '';
		
		$result = $wpdb->get_row($wpdb->prepare("SELECT result, ID, grade_id, details FROM ".WATUPRO_TAKEN_EXAMS." 
			WHERE exam_id=%d AND user_id=%d AND in_progress=0 ORDER BY ID DESC LIMIT 1", $exam->ID, $user_ID));
		
		// used in some shortcodes	
		$GLOBALS['watupro_taking_id'] = $result->ID;	
		
		$display = '';
		if(empty($advanced_settings['no_retake_display_result_what'])) return stripslashes($result->result);	
		if($advanced_settings['no_retake_display_result_what'] == 'gtitle') {
			$gtitle = $wpdb->get_var($wpdb->prepare("SELECT gtitle FROM ".WATUPRO_GRADES." WHERE ID=%d", $result->grade_id));
			if(!empty($gtitle)) return stripslashes($gtitle);	
		} 
		
		if($advanced_settings['no_retake_display_result_what'] == 'all') {
			if(!empty($result->details)) return stripslashes($result->details);	
		} 
		
		// in case grade title or final screen is empty, stick to default
		return stripslashes($result->result);	

	} // end display_latest_result
	
	// basic barchart your points vs avg points, your % vs avg %
	// this chart will be loaded by variable or shortcode in the Final screen 
	// this function uses functions that use globals so it will work properly only when called on controllers/submit_exam.php or a shortcode on the Final screen
	static function barchart($taking_id, $atts) {
		global $wpdb;
		
		// normalize params
		$show = @$atts['show'];
		if(!in_array($show, array('both', 'points', 'percent', 'max_points'))) $show = 'both';
		$your_color = empty($atts['your_color']) ? "blue" : $atts['your_color'];
		$avg_color = empty($atts['avg_color']) ? "gray" : $atts['avg_color'];
		$your_percent_text = empty($atts['your_percent_text']) ? __('You: %d%% correct', 'watupro') : $atts['your_percent_text'];
		$avg_percent_text = empty($atts['avg_percent_text']) ? __('Avg. %d%% correct', 'watupro') : $atts['avg_percent_text'];
		$your_points_text = empty($atts['your_points_text']) ? __('Your points: %s', 'watupro') : $atts['your_points_text'];
		$default_avg_points_text = ($show == 'max_points') ? __('Max. points: %s', 'watupro') : __('Avg. points: %s', 'watupro');
		$avg_points_text = empty($atts['avg_points_text']) ? $default_avg_points_text : $atts['avg_points_text'];
		$round_points = empty($atts['round_points']) ? false : true;
		
		// when showing your points vs max points, overview makes no sense and averages should not be hidden
		if($show == 'max_points') {
			$atts['overview'] = 1;
			$atts['average'] = 'show';
		}
		
		$your_overview_percent_text = empty($atts['your_overview_percent_text']) ? __('%s: %d%% correct', 'watupro') : $atts['your_overview_percent_text'];
		$your_overview_points_text = empty($atts['your_overview_points_text']) ? __('%s: %s', 'watupro') : $atts['your_overview_points_text'];
		$step = 2;
		$width = empty($atts['bar_width']) ? 100 : intval($atts['bar_width']); 
		
		// select taking
		$taking = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $taking_id));
		if($round_points) $taking->points = round($taking->points);
		$max_taking_points = $taking->points; // used to define the points step
		
		// select previous takings?
		if(!empty($atts['overview']) and $atts['overview'] > 1 and !empty($taking->user_id)) {
			$atts['overview']--;
			if($atts['overview']>9) $atts['overview'] = 9; // max 10 attempts total
			$takings = $wpdb->get_results($wpdb->prepare("SELECT ID, points, date, percent_correct
				FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID!=%d AND exam_id=%d AND user_id=%d ORDER BY ID DESC LIMIT %d",
				$taking->ID, $taking->exam_id, $taking->user_id, $atts['overview']));
			krsort($takings);	
			if(count($takings)) $show_overview = true;	
			foreach($takings as $t) {
				if($round_points) $t->points = round($t->points);
				if($t->points > $max_taking_points) $max_taking_points = $t->points;
			}	
			$dateformat = get_option('date_format');
			
		}
		
		// get averages
		if($show != 'max_points') {
			$avg_points = self :: avg_points($taking_id, $taking->exam_id);
			$avg_percent = self :: avg_percent($taking_id, $taking->exam_id);
		}
		else {
			// $avg points is actually the maximum possible points
			$avg_points = $taking->max_points;
		}
		
		// the points step should roughly make the higher points bar 200px high
		$more_points = ($avg_points > $max_taking_points) ? $avg_points : $max_taking_points;
		if($more_points <= 0) $more_points = 1; // set to non-zero for division
		$points_step = round(200 / $more_points, 2);
		
		// create & return the chart HTML
		$content = '<table class="watupro-basic-chart"><tr>';
		
		if($show == 'points' or $show == 'both' or $show == 'max_points') {
			$your_points_text = sprintf($your_points_text, $taking->points);
			$avg_points_text = sprintf($avg_points_text, $avg_points);		
			
			// normalize points here, because when they are less than zero, we'll show them zero
			if($taking->points < 0) $taking->points = 0;
			if($avg_points < 0) $avg_points = 0;				
			
			$content .= '<td style="vertical-align:bottom;text-align:center;"><table class="watupro-basic-chart-points"><tr>';
			
			// previous attempts
			if(!empty($show_overview)) {
				foreach($takings as $t) {
					$content .='<td align="center" style="vertical-align:bottom;text-align:center;">';
					$content .= '<div style="background-color:'.$your_color.';width:'.$width.'px;height:'.round($points_step * $t->points). 'px;">&nbsp;</div>'; 
					$content .='</td>';
				}
			}
			
			// latest attempt
			$content .='<td align="center" style="vertical-align:bottom;text-align:center;">';
			$content .= '<table style="width:'.$width.'px;margin:auto;"><tr><td style="background-color:'.$your_color.';height:'.round($points_step * $taking->points). 'px;">&nbsp;</td></tr></table>'; 
			$content .='</td>';
			if(empty($atts['average']) or $atts['average'] == 'show') {
				$content .= '<td align="center" style="vertical-align:bottom;text-align:center;">';
				$content .= '<table style="width:'.$width.'px;margin:auto;"><tr><td style="background-color:'.$avg_color.';height:'.round($points_step * $avg_points). 'px;">&nbsp;</td></tr></table>';
				$content .='</td></tr>';
			}
			
			$content .= '<tr>';
			
			// previous attempts
			if(!empty($show_overview)) {
				foreach($takings as $t) {
					$overview_points_text = sprintf($your_overview_points_text, date_i18n($dateformat, strtotime($t->date)), $t->points);
					$content .='<td style="text-align:center;">' . $overview_points_text . '</td>';
				}
			}		
			
			// latest attempt
			$content .='<td style="text-align:center;">' . $your_points_text . '</td>';
			
			// average
			if(empty($atts['average']) or $atts['average'] == 'show') $content .='<td style="text-align:center;">'. $avg_points_text .'</td>';
			$content .= '</tr>';
			$content .= '</table></td>';			
		}
		
		if($show == 'both') $content .= '<td>&nbsp;&nbsp;&nbsp;</td>';
		
		if($show == 'percent' or $show == 'both') {
			$your_percent_text = sprintf($your_percent_text, $taking->percent_correct);
			$avg_percent_text = sprintf($avg_percent_text, $avg_percent);						
			
			$content .= '<td style="vertical-align:bottom;"><table class="watupro-basic-chart-percent"><tr>';
			
			// previous attempts
			if(!empty($show_overview)) {
				foreach($takings as $t) {
					$content .='<td align="center" style="vertical-align:bottom;">';
					$content .= '<table style="width:'.$width.'px;margin:auto;"><tr><td style="background-color:'.$your_color.';height:'.round($points_step * $t->percent_correct). 'px;">&nbsp;</td></tr></table>'; 
					$content .='</td>';
				}
			}
			
			// latest attempt
			$content .= '<td align="center" style="vertical-align:bottom;">';
			$content .= '<table style="width:'.$width.'px;margin:auto;"><tr><td style="background-color:'.$your_color.';height:'.round($step * $taking->percent_correct). 'px;">&nbsp;</td></tr></table>'; 
			$content .='</td>';
			if(empty($atts['average']) or $atts['average'] == 'show') {			 
				$content .= '<td align="center" style="vertical-align:bottom;">';
				$content .= '<table style="width:'.$width.'px;margin:auto;"><tr><td style="background-color:'.$avg_color.';height:'.round($step * $avg_percent). 'px;">&nbsp;</td></tr></table>';
				$content .='</td></tr>';
			}
			
			$content .='<tr>';
			
			// previous attempts
			if(!empty($show_overview)) {
				foreach($takings as $t) {
					$overview_percent_text = sprintf($your_overview_percent_text, date_i18n($dateformat, strtotime($t->date)), $t->percent_correct);
					$content .='<td style="text-align:center;">' . $overview_percent_text . '</td>';
				}
			}
			
			// latest attempt
			$content .='<td style="text-align:center;">' . $your_percent_text . '</td>';
			
			if(empty($atts['average']) or $atts['average'] == 'show')  $content .= '<td style="text-align:center;">'. $avg_percent_text .'</td>';
			
			$content .= '</tr>';
			$content .= '</table></td>';			
		}
		
		$content .= '</tr></table>';
		
		return $content;
	}
	
	// calculate avg points
	// this function uses globals so it will work properly only when called on controllers/submit_exam.php or a shortcode on the Final screen
	static function avg_points($taking_id, $exam_id=0) {
		global $wpdb, $achieved;
		
		if(empty($exam_id)) $exam_id = $wpdb->get_var($wpdb->prepare("SELECT exam_id FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $taking_id));
		
		$all_point_rows = $wpdb->get_results($wpdb->prepare("SELECT points FROM ".WATUPRO_TAKEN_EXAMS." 
		WHERE exam_id=%d AND in_progress=0 AND ID!=%d", $exam_id, $taking_id));
		$all_points = 0;
		foreach($all_point_rows as $r) $all_points += $r->points;	
		$all_points += $achieved;			
		$avg_points = round($all_points / ($wpdb->num_rows + 1), 1);
		
		return $avg_points;
	} 
	
	// calculate avg percent
	// this function uses globals so it will work properly only when called on controllers/submit_exam.php or a shortcode on the Final screen
	static function avg_percent($taking_id, $exam_id=0) {
		global $wpdb, $achieved, $percent;
		
		if(empty($exam_id)) $exam_id = $wpdb->get_var($wpdb->prepare("SELECT exam_id FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $taking_id));
		
		$all_percent_rows = $wpdb->get_results($wpdb->prepare("SELECT percent_correct FROM ".WATUPRO_TAKEN_EXAMS." 
		WHERE exam_id=%d AND in_progress=0 AND ID!=%d", $exam_id, $taking_id));
		$all_percent = 0;	
		foreach($all_percent_rows as $r) $all_percent += $r->percent_correct;	
		$all_percent += $percent; 	
		$avg_percent = round($all_percent  / ($wpdb->num_rows + 1));
		
		return $avg_percent;
	}
	
	// calculate avg percent from maximum points
	// this function uses globals so it will work properly only when called on controllers/submit_exam.php or a shortcode on the Final screen
	static function avg_percent_of_max($taking_id, $exam_id=0) {
		global $wpdb, $achieved, $pointspercent;
		
		if(empty($exam_id)) $exam_id = $wpdb->get_var($wpdb->prepare("SELECT exam_id FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $taking_id));
		
		$all_percent_rows = $wpdb->get_results($wpdb->prepare("SELECT percent_points FROM ".WATUPRO_TAKEN_EXAMS." 
		WHERE exam_id=%d AND in_progress=0 AND ID!=%d", $exam_id, $taking_id));
		//print_r($all_percent_rows);		
		
		$all_percent = 0;	
		foreach($all_percent_rows as $r) $all_percent += $r->percent_points;	
		$all_percent += $pointspercent; 	
		
		//echo "$pointspercent: ALL $all_percent, ".$wpdb->num_rows;
		
		$avg_percent = round($all_percent  / ($wpdb->num_rows + 1));
		
		return $avg_percent;
	}	// end avg_percent_of_max()
	
	// handle MoolaMojo integration if enabled
	static function transfer_moola($taking_id, $exam, $user_id, $points, $grade_id) {
		global $wpdb;
		
		if(empty($user_id)) return false;
		if(empty($points) and empty($grade_id)) return false;
		if(get_option('watupro_integrate_moolamojo') != 1) return false;
	
		$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
		if(empty($advanced_settings['transfer_moola'])) return false;
		
		
		if($advanced_settings['transfer_moola_mode'] == 'equal') $credits = $points;
		else {
			// select grade points
			if($grade_id == 0) return false;
			$credits = $wpdb->get_var($wpdb->prepare("SELECT moola FROM ".WATUPRO_GRADES." WHERE ID=%d", $grade_id));			
		}
		
		if($credits == 0) return false;
		
		// actually transfer the moola
		if($credits > 0 ) $reward = true;
		else {
			$reward = false;
			$credits = abs($credits);
		}
		do_action("moolamojo_transaction", $reward, $credits, __('submitted test', 'watupro'), $user_id, WATUPRO_TAKEN_EXAMS, $taking_id);
		
	}  // end transfer_moola
	
	// The new answers table for the final screen %%ANSWERS-TABLE%%
	public static function answers_table($taking_id, $media = 'screen') {
		global $wpdb;
		
		$answers = $wpdb->get_results($wpdb->prepare("SELECT tA.ID as ID, tA.is_correct as is_correct, tA.snapshot as snapshot, tQ.title as title, tQ.question as question
			FROM ".WATUPRO_STUDENT_ANSWERS." tA JOIN ".WATUPRO_QUESTIONS." tQ ON tQ.ID = tA.question_id 
			WHERE tA.taking_id=%d ORDER BY tA.ID ASC", $taking_id));
	
		$num_answers = count($answers);
		
		// pagination vars			
		$per_decade = 10;
		$num_decades = ceil($num_answers / $per_decade);	
		
		ob_start();	
		include(WATUPRO_PATH . "/views/answers-table.html.php");
		$content = ob_get_clean();
		return $content;	
	}	// end answers_table
}