<?php
// shortcode generator class which makes easy to generate shortcodes
class WatuPROShortcodeGen {
	// displays the page with all the tabs, includes the appropriate views and calls the function to generate
	static function generator() {
		global $wpdb;		
		
		if(!empty($_POST['generate_shortcode'])) {
			$shortcode = '[';
			
			switch($_POST['shortcode']) {
				// simple quiz shortcode
				case 'quiz':
					$shortcode .= 'watupro '.intval($_POST['quiz_id']);
					
					// difficulty level
					if(!empty($_POST['difficulty_level'])) $shortcode .= ' difficulty_level="'.sanitize_text_field($_POST['difficulty_level']).'"';
					
					// category ID 
					if(!empty($_POST['category_id'])) $shortcode .= ' category_id="'.intval($_POST['category_id']).'"';
					
					// tags
					if(!empty($_POST['tags'])) $shortcode .= ' tags="'.sanitize_text_field($_POST['tags']).'"';
				break;
				
				case 'list':
					$shortcode .= 'watuprolist cat_id="' . sanitize_text_field($_POST['cat_id']).'" orderby="'.sanitize_text_field($_POST['orderby']).'"';	
				break;
				
				case 'myexams':
					$shortcode .= 'watupro-myexams';
					if(!empty($_POST['cats']) and is_array($_POST['cats'])) $shortcode.=' '. implode(",", watupro_int_array($_POST['cats']));
					else $shortcode .= ' ALL';
					if(!empty($_POST['orderby'])) $shortcode .= ' '.sanitize_text_field($_POST['orderby']);
					if(!empty($_POST['reorder_by_latest_taking'])) $shortcode .= ' reorder_by_latest_taking=1';
					if(!empty($_POST['status'])) $shortcode .= ' status="'.sanitize_text_field($_POST['status']).'"'; 
					if(!empty($_POST['details_no_popup'])) $shortcode .= ' details_no_popup=1';
				break;
				
				case 'leaderboard':
					if(intval($_POST['number']) <= 0) $_POST['number'] = 10;
					$shortcode .= 'watupro-leaderboard '.intval($_POST['number']);
				break;
				
				case 'result':
					$shortcode .= 'watupro-result what="'.sanitize_text_field($_POST['what']).'"';
					if(!empty($_POST['quiz_id'])) $shortcode .= ' quiz_id='.intval($_POST['quiz_id']);
					if($_POST['for_user'] == 'specify' and !empty($_POST['user_id']) and is_numeric($_POST['user_id'])) $shortcode .= ' user_id='.intval($_POST['user_id']);
					if(!empty($_POST['qcat_id'])) $shortcode .= ' cat_id='.intval($_POST['qcat_id']); 
					if(!empty($_POST['placeholder'])) $shortcode .= ' placeholder="'.sanitize_text_field($_POST['placeholder']).'"';
				break;
				
				// basic chart 
				case 'chart':
					$shortcode .= 'watupro-basic-chart show="'.sanitize_text_field($_POST['show']).'"';
					if(!empty($_POST['your_color'])) $shortcode .= ' your_color="'.sanitize_text_field($_POST['your_color']).'"';
					if(!empty($_POST['avg_color'])) $shortcode .= ' avg_color="'.sanitize_text_field($_POST['avg_color']).'"';
					if(!empty($_POST['bar_width'])) $shortcode .= ' bar_width="'.intval($_POST['bar_width']).'"';
					if(!empty($_POST['average'])) $shortcode .= ' average="'.sanitize_text_field($_POST['average']).'"';
					if(!empty($_POST['overview'])) $shortcode .= ' overview="'.intval($_POST['overview']).'"';
					if(!empty($_POST['your_points_text'])) $shortcode .= ' your_points_text="'.sanitize_text_field($_POST['your_points_text']).'"';
					if(!empty($_POST['your_overview_points_text'])) $shortcode .= ' your_overview_points_text="'.sanitize_text_field($_POST['your_overview_points_text']).'"';
					if(!empty($_POST['avg_points_text'])) $shortcode .= ' avg_points_text="'.sanitize_text_field($_POST['avg_points_text']).'"';
					if(!empty($_POST['your_percent_text'])) $shortcode .= ' your_percent_text="'.sanitize_text_field($_POST['your_percent_text']).'"';
					if(!empty($_POST['your_overview_percent_text'])) $shortcode .= ' your_overview_percent_text="'.sanitize_text_field($_POST['your_overview_percent_text']).'"';
					if(!empty($_POST['avg_percent_text'])) $shortcode .= ' avg_percent_text="'.sanitize_text_field($_POST['avg_percent_text']).'"';
					if(!empty($_POST['round_points'])) $shortcode .= ' round_points=1';
				break;
				
				// no. quiz attempts total & left
				case 'attempts':
					$shortcode .= 'watupro-quiz-attempts quiz_id='.intval($_POST['quiz_id']).' show="'.sanitize_text_field($_POST['show']).'"';
				break;
				
				case 'users_completed':
				   $shortcode .= 'watupro-users-completed quiz_id='.intval($_POST['quiz_id']).' return="'.sanitize_text_field($_POST['return']).'"';
				   if(!empty($_POST['grade_id'])) $shortcode .= ' grade_id='.intval($_POST['grade_id']);
				   if(!empty($_POST['use_points'])) $shortcode .= ' points="'.sanitize_text_field($_POST['points_op']).' '.sanitize_text_field($_POST['points']).'"';
				   if(!empty($_POST['use_percent'])) $shortcode .= ' percent_correct="'.sanitize_text_field($_POST['percent_op']).' '.sanitize_text_field($_POST['percent']).'"';
				break;
				
				case 'retake':
					$shortcode .= 'watupro-retake';
					if(!empty($_POST['type'])) $shortcode .= ' type="'.sanitize_text_field($_POST['type']).'"';
					if(!empty($_POST['class'])) $shortcode .= ' class="'.sanitize_text_field($_POST['class']).'"';
					if(!empty($_POST['text'])) $shortcode .= ' text="'.sanitize_text_field($_POST['text']).'"';
				break;
				
				case 'segment':
					$shortcode .= 'watupro-segment-stats question_id='.intval($_POST['question_id']).' criteria="'.sanitize_text_field($_POST['criteria']).'"';
					if(!empty($_POST['compare']) and ($_POST['criteria'] == 'grade' or $_POST['criteria'] == 'category_grade')) $shortcode .= ' compare="'.sanitize_text_field($_POST['compare']).'"';
					if(!empty($_POST['segment'])) $shortcode .= ' segment="'.sanitize_text_field($_POST['segment']).'"';
					if(!empty($_POST['grade_id']) and $_POST['criteria'] == 'grade') $shortcode .= ' grade_id="'.intval($_POST['grade_id']).'"';
					if(!empty($_POST['catgrade_id']) and $_POST['criteria'] == 'category_grade') $shortcode .= ' catgrade_id="'.intval($_POST['catgrade_id']).'"';
					if(!empty($_POST['category_id']) and $_POST['criteria'] == 'category_grade') $shortcode .= ' category_id="'.sanitize_text_field($_POST['category_id']).'"';
				break;
				
				case 'paginator':
					$shortcode .= 'watupro-paginator paginator="'.sanitize_text_field($_POST['paginator']).'"';
					if(!empty($_POST['vertical'])) $shortcode .= ' vertical="1"';
				break;
				
				case 'expand_personality':
					$shortcode .= 'watupro-expand-personality-result';
					if(!empty($_POST['sort'])) $shortcode .= ' sort="'.sanitize_text_field($_POST['sort']).'"';
					if(!empty($_POST['limit'])) $shortcode .= ' limit="'.intval($_POST['limit']).'"';
					if(!empty($_POST['empty'])) $shortcode .= ' empty="false"';
					if(empty($_POST['chart']) and !empty($_POST['rank'])) $shortcode .= ' rank="'.intval($_POST['rank']).'"';
					if(empty($_POST['chart']) and !empty($_POST['personality'])) $shortcode .= ' personality="'.sanitize_text_field($_POST['personality']).'"';
					if(!empty($_POST['chart'])) $shortcode .= ' chart=1';
				break;
			}
			
			$shortcode .= ']';
		}	
		
		// select the appropriate variables depending on which tab we are on
		$tab = empty($_GET['tab']) ? 'quiz' : sanitize_text_field($_GET['tab']);
		
		switch($tab) {
			case 'quiz':
			default:
				// select quizzes 
				$quizzes = $wpdb->get_results("SELECT * FROM " . WATUPRO_EXAMS." ORDER BY name");
				
				// select difficulty levels if any
				$diff_levels = stripslashes(get_option('watupro_difficulty_levels'));
				$diff_levels_arr = explode(PHP_EOL, $diff_levels);
				
				// select current qcats if quiz category is selected
				if(!empty($_POST['quiz_id'])) { 
					$exam = $wpdb->get_row($wpdb->prepare("SELECT ID, reuse_questions_from FROM ".WATUPRO_EXAMS." WHERE ID=%d", intval($_POST['quiz_id'])));				
				
					$q_exam_id = (watupro_intel() and $exam->reuse_questions_from) ? $exam->reuse_questions_from : $exam->ID;
					
					$qcats = $wpdb->get_results($wpdb->prepare("SELECT tC.* FROM " . WATUPRO_QCATS. " tC
						WHERE tC.ID IN (SELECT cat_id FROM ".WATUPRO_QUESTIONS." WHERE exam_id=%d) ", $q_exam_id));
				}
			break;
			
			case 'list':
				// select quiz categories
				$cats = $wpdb->get_results("SELECT * FROM ".WATUPRO_CATS." WHERE parent_id=0 ORDER BY name");
				$subs = $wpdb->get_results("SELECT * FROM ".WATUPRO_CATS." WHERE parent_id!=0 ORDER BY name");
				// match cats & subs
				foreach($cats as $cnt => $cat) {
					$cat_subs = array();
					foreach($subs as $sub) {
						if($sub->parent_id == $cat->ID) $cat_subs[] = $sub;
					}
					$cats[$cnt] -> subs = $cat_subs;
				}
			break;
			
			case 'myexams':
				// select quiz categories
				// categories if any
				$cats = $wpdb->get_results("SELECT * FROM ".WATUPRO_CATS." WHERE parent_id=0 ORDER BY name");
				$subs = $wpdb->get_results("SELECT * FROM ".WATUPRO_CATS." WHERE parent_id!=0 ORDER BY name");
				// match cats & subs
				foreach($cats as $cnt => $cat) {
					$cat_subs = array();
					foreach($subs as $sub) {
						if($sub->parent_id == $cat->ID) $cat_subs[] = $sub;
					}
					$cats[$cnt] -> subs = $cat_subs;
				}
			break;
			
			case 'result':
				// select quizzes 
				$quizzes = $wpdb->get_results("SELECT * FROM " . WATUPRO_EXAMS." ORDER BY name");
				
				// select question categories
				$qcats = $wpdb->get_results("SELECT * FROM ".WATUPRO_QCATS." WHERE parent_id=0 ORDER BY name");
				$subcats = $wpdb->get_results("SELECT * FROM ".WATUPRO_QCATS." WHERE parent_id!=0 ORDER BY name");
				foreach($qcats as $cnt => $qcat) {
					$cat_subs = array();
					foreach($subcats as $sub) {
						if($sub->parent_id == $qcat->ID) $cat_subs[] = $sub;
					}
					$qcats[$cnt]->subs = $cat_subs;
				}
			break;
			
			case 'attempts':
				// select quizzes 
				$quizzes = $wpdb->get_results("SELECT * FROM " . WATUPRO_EXAMS." ORDER BY name");
			break;
			
			case 'users_completed':
				// select quizzes 
				$quizzes = $wpdb->get_results("SELECT * FROM " . WATUPRO_EXAMS." ORDER BY name");
				
				if(!empty($_POST['quiz_id'])) {
					// select grades
					$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", intval($_POST['quiz_id'])));
					$grades = WTPGrade :: get_grades($exam);
				}
			break;
		}		
		
		if(@file_exists(get_stylesheet_directory().'/watupro/shortcodegen.html.php')) require get_stylesheet_directory().'/watupro/shortcodegen.html.php';
		else require WATUPRO_PATH."/views/shortcodegen.html.php";
	} // end generator()
}