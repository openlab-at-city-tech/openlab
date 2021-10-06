<?php 
class WTPGrade {
	// calculate grade
	// personality grade calculation is passed to the intelligence module
	public static $advanced_settings = null;
	public static $cats_maxpoints;
	public static $current_category_id;
	public static $taking_id;
	
	// calculate normal grade based on whole test performance
	static function calculate($exam_id, $points, $percent, $cat_id = 0, $user_grade_ids = null, $pointspercent = 0) {
		global $wpdb;		
		
		$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $exam_id));
		$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
		self :: $advanced_settings = $advanced_settings;
		$grade = __('None', 'watupro');
		$grade_obj = (object)array("title"=>__('None', 'watupro'), "description"=>"");
		$do_redirect = false;
		$certificate_id = 0;
		
		$grades = self :: get_grades($exam, $cat_id);		
		
		if( count($grades) ) {			
			// calculate by percentage in Intelligence 
			if(watupro_intel()) {				
				if(!empty($exam->is_personality_quiz)) {					
					return WTPIGrade :: calculate($user_grade_ids); 
				}
			}			
			
			foreach($grades as $grow ) { 
				$match_criteria = $points;
				   
				// from Intelligence - calculate by %
				if(!empty($exam->grades_by_percent)) {
					if(!empty($advanced_settings['grades_by_percent_mode']) and $advanced_settings['grades_by_percent_mode'] == 'max_points') $match_criteria = $pointspercent;
					else $match_criteria = $percent;
				}			
				
				if( $grow->gfrom <= $match_criteria and $match_criteria <= $grow->gto ) {
					list($grade, $grade_obj, $certificate_id, $do_redirect) = self :: match_grade($grow); 					               
					break;
				}
			}
		}
		
		$certificate_id = empty($certificate_id) ? array() : array($certificate_id);
		
		// do I have to also assign a multiple quiz certificate? If yes, it adds to the $certificate_id
		$multiquiz_certificate_id = WatuPROCertificate :: multi_quiz($exam_id, $points, $percent);		
		if(!empty($multiquiz_certificate_id)) $certificate_id = array_merge($certificate_id, $multiquiz_certificate_id);
		
		list($grade, $certificate_id, $do_redirect, $grade_obj) = apply_filters('watupro_calculate_grade', 
					array($grade, $certificate_id, $do_redirect, $grade_obj), @$GLOBALS['watupro_taking_id'], $exam_id, $points, $percent, $cat_id, $user_grade_ids, $pointspercent);		
		
		return array($grade, $certificate_id, $do_redirect, $grade_obj);
	}
	
	// calculate final grade that depends on meeting criteria per question category
	static function calculate_dependent($exam_id, $catgrades_array, $achieved, $percent, $user_grade_ids = null, $pointspercent = 0, $certificate_id = 0) {
		global $wpdb;		
				
		$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $exam_id));
		$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
		self :: $advanced_settings = $advanced_settings;
		$grade = __('None', 'watupro');
		$grade_obj = (object)array("title"=>__('None', 'watupro'), "description"=>"");
		$do_redirect = false;
		$certificate_id = empty($certificate_id) ? array() : array($certificate_id);
				
		if(empty($catgrades_array)) return array($grade, $certificate_id, $do_redirect, $grade_obj);
		
		$grades = self :: get_grades($exam);		
		if(!count($grades)) return array($grade, $certificate_id, $do_redirect, $grade_obj);	
		
		// calculate by percentage in Intelligence - unlikely to be selected when quiz is in this mode, but just in case 
		if(watupro_intel()) {				
			if(!empty($exam->is_personality_quiz)) {					
				return WTPIGrade :: calculate($user_grade_ids); 
			}
		}	// end personality
		 
		$num_required_matches = count($catgrades_array);
				  
		foreach($grades as $grow ) { 
			
			$match_criteria = 'points';
			$main_compare = $achieved;
			if(!empty($exam->grades_by_percent)) {
				if(!empty($advanced_settings['grades_by_percent_mode']) and $advanced_settings['grades_by_percent_mode'] == 'max_points') {
					$match_criteria = 'percent_points';
					$main_compare = $pointspercent;
				}
				else {
					$match_criteria = 'percent';
					$main_compare = $percent;
				}
			}			
			
			// first check if we satisfy any global gfrom / gto. Need to pass $achieved, $percent and $pointspercent in the function too
			if( ($grow->gfrom != 0 or $grow->gto != 0) and ($grow->gfrom > $main_compare or $main_compare > $grow->gto) ) continue;			
			
			// now see if we can match the grade in each category			
			$num_achieved_matches = 0;
			$grade_requirements = unserialize($grow->category_requirements);
			if(!is_array($grade_requirements)) $grade_requirements = array();
			
			// before counting matches we must ensure that if a given question category exists as grade requirement, but does NOT exist in $catgrades_array
			// then the grade should be skipped and NOT assigned.
			// do this UNLESS user has selected the option "ignore specific category requirements if questions from that category were not present in the quiz"
			if(empty($advanced_settings['calculate_dependent_ignore_empty_cats'])) {
				foreach($grade_requirements as $key => $reqs) {
					// if both reqs from-to are zeros, we can skip this cat		
					if(intval($reqs['from']) == 0 and intval($reqs['to']) == 0) continue;		
					
					$key_found = false;
					foreach($catgrades_array as $catgrade) {
						if($catgrade['cat_id'] == $key) $key_found = true;
					}
					
					if(!$key_found) continue 2; // skip the upper loop, this grade cannot be satisfied
				}
			} // end skipping grades 			
			
			foreach($catgrades_array as $catgrade) {
			   $grade_from = @$grade_requirements[$catgrade['cat_id']]['from'];
			   $grade_to = @$grade_requirements[$catgrade['cat_id']]['to'];
			   
				// define grade from and grade to for this category. If both are zeros, consider the category fine, increase $num_achieved_matches and continue
				if(empty($grade_from) and empty($grade_to)) {
						$num_achieved_matches++;
						continue;	
				}				
				
				// if all requirements in all cats are true, $num_achieved_matches++;
				// echo $grade_from." : ".$catgrade[$match_criteria]." : $grade_to<br>";
				if( $grade_from <= $catgrade[$match_criteria] and $catgrade[$match_criteria] <= $grade_to ) {
					
					$num_achieved_matches++;
				}
			}
						
			// if matched, assign it
			if($num_achieved_matches >= $num_required_matches) {
				list($grade, $grade_obj, $cert_id, $do_redirect) = self :: match_grade($grow); 
				break;
			}			
		} // end foreach $grades
		//exit;
		
		if(!empty($cert_id)) $certificate_id[] = $cert_id;
		return array($grade, $certificate_id, $do_redirect, $grade_obj);			
	} // end calculate dependent

	// small helper to return the data, used also by the Intelligence module grade object	
	static function match_grade($grow) {
		$do_redirect = false;					
		$grade = $grow->gtitle;		
		// redirect?
		if(preg_match("/^http:\/\//i", $grade) or preg_match("/^https:\/\//i", $grade)) {
			$do_redirect = $grade;
		}	
		
		$grade = '<span class="watupro-gtitle">'.stripslashes($grade).'</span>';		
		
		// new redirect using a field
		if(!empty($grow->redirect_url)) {
			$do_redirect = $grow->redirect_url;
			if(!preg_match("/^http:\/\//i", $do_redirect) and !preg_match("/^https:\/\//i", $do_redirect)) $do_redirect = "http://".$do_redirect;
		}		
		
		if(!empty($grow->gdescription)) $grade.="<div class='watupro-grade-description'><p>".stripslashes($grow->gdescription)."</p></div>";
		
		return array($grade, $grow,  $grow->certificate_id, $do_redirect); 
	}
	
	// if %%CATGRADES%% is used, this calculates and replaces them on the final screen
	static function replace_category_grades($final_screen, $taking_id, $exam_id, $email_output) {
		global $wpdb;
		
		$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $exam_id));
		$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
		
		if(!strstr($final_screen, '%%CATGRADES%%') and !strstr($final_screen, '%%CATEGORY-')
		 	and !strstr($email_output, '%%CATGRADES%%') and !strstr($email_output, '%%CATEGORY-')
		 	and empty($advanced_settings['always_calculate_catgrades'])
		 	and empty($advanced_settings['final_grade_depends_on_cats'])) return false;
		
		if($exam->reuse_default_grades) {
			$exam->gradecat_design = get_option('watupro_gradecat_design');
			$advanced_settings['gradecat_order'] = get_option('watupro_gradecat_order');
		}
		
		if(empty($exam->gradecat_design)) $exam->gradecat_design = get_option('watupro_gradecat_design');
		
		$catgrades = array();
		
		// select the student_answers details of this taking and group by category
		$answers = $wpdb->get_results( $wpdb->prepare("SELECT tA.*, tQ.cat_id as cat_id, tQ.is_survey as is_survey,
			tC.parent_id as parent_cat_id
			FROM ".WATUPRO_STUDENT_ANSWERS." tA JOIN ".WATUPRO_QUESTIONS." tQ ON tA.question_id=tQ.ID 
			LEFT JOIN ".WATUPRO_QCATS." tC ON tC.ID = tQ.cat_id
			WHERE tA.taking_id=%d", $taking_id) ); 
		$cat_ids = array(0);
		foreach($answers as $answer) {
			if(!in_array($answer->cat_id, $cat_ids)) $cat_ids[] = $answer->cat_id;
			
			// if we sum subcategory performance we have to make sure that parent will be included even if only its children have questions
			if(!empty($advanced_settings['sum_subcats_catgrades']) and !in_array($answer->parent_cat_id, $cat_ids)) $cat_ids[] = $answer->parent_cat_id;
		}	
		
		// now select the categories
		$cats = $wpdb -> get_results("SELECT * FROM ".WATUPRO_QCATS." WHERE ID IN (".implode(",", $cat_ids).") ORDER BY name");
		
		// Reorder them accordingly to the Advanced Settings tab
		if( !empty($advanced_settings['sorted_categories']) and 
			 (empty($advanced_settings['gradecat_order']) or $advanced_settings['gradecat_order'] == 'same')
			) {
			$final_cats = $final_cat_ids = array();
			$sorted_cats = $advanced_settings['sorted_categories'];
			
			asort($sorted_cats); // sort by the order number
			// print_r($sorted_cats);
			foreach($sorted_cats as $key => $val) {
				if(!empty($advanced_settings['sorted_categories_encoded'])) $key = rawurldecode($key);
				foreach($cats as $cat) {
					if($cat->name == $key) {
						$final_cats[] = $cat;
						$final_cat_ids[] = $cat->ID;
					}
				}
			}
			
			// OK so far, however we may have main cat not sorted at all in the case when:
			// a) "Sum up subcategories" is selected but
			// b) user has not visited the Edit quiz page to re-save the sorting.
			// In this case we'll still add the category at the end. It won't be sorted but at least will be included
			// and will be available for the %%CATEGORY-X%% variables
			// There's no IF required here perhaps (that's why adding "or true") because the problem can happen even without the setting - version 6.1.0.7
			if(!empty($advanced_settings['sum_subcats_catgrades']) or true) {				
				foreach($cats as $cat) {
					if(!in_array($cat->ID, $final_cat_ids)) {
						$final_cats[] = $cat;
						$final_cat_ids[] = $cat->ID;
					} 
				}
			} // end adding the missing parent cats (missing because they were not sorted)
			
			$cats = $final_cats;
		} // end reorder
		
		// for each category calculate the grade and add to $catgrades
		$cats_maxpoints = self :: $cats_maxpoints;
	
		foreach($cats as $cat) {
			$total = $correct = $percentage = $points = $max_points = $empty = $wrong = 0;
			$catgrade = stripslashes($exam->gradecat_design);
			
			// small trick to allow pasting shortcodes with category_id="this" which will be replaced before do_shortcode()
			// is called on the final screen
			$catgrade = str_replace('category_id="this"', 'category_id="'.$cat->ID.'"', $catgrade);
			
			// don't calculate for subcategories when main categories will be used only
			if(!empty($advanced_settings['sum_subcats_catgrades']) and empty($advanced_settings['subcats_catgrades_include']) and !empty($cat->parent_id)) continue;
			
			foreach($answers as $answer) {
				if($answer->cat_id != $cat->ID
					and (empty($advanced_settings['sum_subcats_catgrades']) or $answer->parent_cat_id != $cat->ID)) continue;
				if(!empty($advanced_settings['exclude_survey_from_catgrades']) and $answer->is_survey) continue;
				$total ++;
				if($answer->is_correct) $correct++;
				if(empty($answer->answer)) $empty++;
				if(!empty($answer->answer) and !$answer->is_correct) $wrong++;
				$points += $answer->points;
			}
			
			// don't at all show categories without questions asked 
			// (such case can happen if $advanced_settings['exclude_survey_from_catgrades'] is true)
			if(!$total) continue;
			
			// percentage and grade
			$percent = $total ? round($correct / $total, 2) * 100 : 0;
			// calculate also percentage of max points
			$percent_of_max = empty($cats_maxpoints[$cat->ID]['max_points']) ? 0 : round(100 * $points / $cats_maxpoints[$cat->ID]['max_points']);
			if($points <= 0) $percent_of_max = 0;
			
			list($grade, $certificate_id, $do_redirect, $grade_obj) = self::calculate($exam_id, $points, $percent, $cat->ID, null, $percent_of_max);
			
			// now replace in the $catgrade text
			$catgrade = str_replace("%%CATEGORY%%", stripslashes($cat->name), $catgrade);
			$catgrade = str_replace("%%CATEGORY-ID%%", $cat->ID, $catgrade);
			$catgrade = str_replace("%%CATDESC%%", stripslashes($cat->description), $catgrade);
			$catgrade = str_replace("%%CORRECT%%", $correct, $catgrade);
			$catgrade = str_replace("%%WRONG%%", $wrong, $catgrade);
			$catgrade = str_replace("%%EMPTY%%", $wrong, $catgrade);
			$catgrade = str_replace("%%TOTAL%%", $total, $catgrade);
			$catgrade = str_replace("%%POINTS%%", $points, $catgrade);
			$catgrade = str_replace("%%PERCENTAGE%%", $percent, $catgrade);
			$catgrade = str_replace("%%PERCENTAGEOFMAX%%", $percent_of_max, $catgrade);
			$catgrade = str_replace("%%GTITLE%%", @$grade_obj->gtitle, $catgrade);
			$catgrade = str_replace("%%GDESC%%", wpautop(stripslashes(@$grade_obj->gdescription)), $catgrade);
			
			// add to $catgrades
			$catgrades[] = array(
				'cat_id'		=>	$cat->ID,
				'name' 			=>	stripslashes($cat->name),
				'description' 	=>	stripslashes($cat->description),
				'correct' 		=>	$correct,
				'wrong' 			=>	$wrong,
				'empty' 			=>	$empty,
				'total' 			=>	$total,
				'points' 		=>	$points,
				'percent' 		=>	$percent,
				'gtitle' 		=>	@$grade_obj->gtitle,
				'gdescription' 	=>	wpautop(stripslashes(@$grade_obj->gdescription)),
				'grade_id' => @$grade_obj->ID,
				'html'			=>	$catgrade
			);
		}
		
		// should we reorder by criteria different by "same as shown on the quiz"?		
		if(!empty($advanced_settings['gradecat_order']) and $advanced_settings['gradecat_order']!= 'same') {
			$catgrades = self :: reorder_catgrades($catgrades, $advanced_settings['gradecat_order']);
		} 
		
		$catgrades_str = '';
		foreach($catgrades as $cnt => $catgrade) {
			$cnt++;
			if(!empty($advanced_settings['gradecat_limit']) and $advanced_settings['gradecat_limit'] < $cnt) break;
			$catgrades_str .= wpautop($catgrade['html']).' ';
		}
		return array($catgrades_str, $catgrades);
	}
	
	// gets the proper grades for a quiz based on whether it uses default or its own grades
	// $exam can be object or ID
	static function get_grades($exam, $cat_id = 0) {
		global $wpdb;
		
		if(is_numeric($exam)) {
			$exam = $wpdb->get_row($wpdb->prepare("SELECT ID, reuse_default_grades, grades_by_percent FROM ".WATUPRO_EXAMS." WHERE ID=%d", $exam));
		}
		
		$grades_quiz_id = $exam->reuse_default_grades ? 0 : $exam->ID;
		if($exam->reuse_default_grades) {
			$grades_quiz_id = 0;
			$grade_type_sql = $wpdb->prepare(" AND percentage_based = %d ", $exam->grades_by_percent); 
		}
		else {
			$grades_quiz_id = $exam->ID;
			$grade_type_sql = '';
		}
		
		$grades = $wpdb->get_results(" SELECT * FROM `".WATUPRO_GRADES."` 
			WHERE exam_id=$grades_quiz_id AND cat_id=$cat_id $grade_type_sql 
			ORDER BY gfrom DESC");
					
		return $grades;	
	} // end get_grades
	
	// reorder %%CATGRADES%% depending to user selection
	static function reorder_catgrades($catgrades, $order) {
		if($order == "best") usort($catgrades, array(__CLASS__, 'sort_catgrades_best'));
		else usort($catgrades, array(__CLASS__, 'sort_catgrades_worst'));
		return $catgrades;
	}
	
	// sort catgrades best on top
	static function sort_catgrades_best($cat2, $cat1) {
		$rank_key_primary = 'percent';
		$rank_key_secondary = 'points';
		
		if(!empty(self :: $advanced_settings['sort_catgrades_by_points'])) {
			$rank_key_primary = 'points';
			$rank_key_secondary = 'percent';
		}		
		
		if($cat1[$rank_key_primary] == $cat2[$rank_key_primary] and $cat1[$rank_key_secondary] == $cat2[$rank_key_secondary]) return 0;
		
		// only in this case we'll check secondary
		if($cat1[$rank_key_primary] == $cat2[$rank_key_primary]) {
			return ($cat1[$rank_key_secondary] < $cat2[$rank_key_secondary]) ? -1 : 1;
		}
		
		// primary not equal, so we'll compare by it
		return ($cat1[$rank_key_primary] < $cat2[$rank_key_primary]) ? -1 : 1;
	} // end sort best
	
		// sort cagrades worst on top
	static function sort_catgrades_worst($cat1, $cat2) {
		return self :: sort_catgrades_best($cat2, $cat1);
	} // end sort worst
}