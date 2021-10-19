<?php
// integrates WatuPRO with the WP Experience API plugin
// https://wordpress.org/plugins/wp-experience-api/
class WatuPROXAPI {
	static function options() {
		if(!empty($_POST['ok']) and check_admin_referer('watupro_xapi')) {
			$options = array(
				'passed_exam' => empty($_POST['passed_exam']) ? 0 : 1,
				'failed_exam' => empty($_POST['failed_exam']) ? 0 : 1,				
			);			
			update_option('watupro_xapi', $options);
		}
		
		$options = get_option('watupro_xapi');
		
		include(WATUPRO_PATH . '/views/xapi-options.html.php');
	}	
	
	static function register_triggers() {
		if(!class_exists('WP_Experience_API')) return false;
		
		// make all conditional, i.e. hook only if chosen so in WatuPRO xAPI Settings page
		$options = get_option('watupro_xapi');
		
		// completed exam
		if(!empty($options['passed_exam']) or !empty($options['failed_exam'])) {
			WP_Experience_API::register( 'completed_exam', array(
				'hooks' => array( 'watupro_completed_exam' ),
				'num_args' => array( 'watupro_completed_exam' => 1 ),
				'process' => function( $hook, $args ) {
					global $wpdb;
					$options = get_option('watupro_xapi');
					
					// args parameter should return $user_id, $achievement_id, $this_trigger, $site_id, $args
					$taking_id = empty($args[0]) ? 0 : intval($args[0]);
					
					// select taking
					$taking = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $taking_id));
					
					// if no student ID and taking email, return
					if(empty($taking->user_id) and empty($taking->email)) return false;
					
					$exam = $wpdb->get_row($wpdb->prepare("SELECT ID, name, advanced_settings FROM " . WATUPRO_EXAMS . " WHERE ID=%d", $taking->exam_id));
					
					// define if passed or failed
					$passed = false;
					$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
					if(empty($advanced_settings['completion_criteria']) or $advanced_settings['completion_criteria'] == 'taken') $passed = true;
					else {
						// passed only if completion criteria are met
						$grids = explode('|', $advanced_settings['completion_grades']);
						$grids = array_filter($grids);
						if(in_array($taking->grade_id, $grids)) $passed = true;
					}
					
					if($passed and empty($options['passed_exam'])) return false;
					if(!$passed and empty($options['failed_exam'])) return false;
					
					$verb = $passed ? 'passed' : 'failed';
					
					$student_id = $taking->user_id ? $taking->user_id : 0;
					
					// construct statement
					$statement = array(
								'verb' => array(
									'id' => 'http://adlnet.gov/expapi/verbs/'.$verb,
									'display' => array( 'en-US' => $verb ),
								),
								'object' => array(
									'id' => WTPExam :: get_permalink($exam->ID),
									'definition' => array(
										'name' => array(
											'en-US' => stripslashes($exam->name),
										),
										'description' => array(
											'en-US' => stripslashes($exam->name),
										),
										'type' => 'http://adlnet.gov/expapi/activities/assessment',
									)
								),
								'context_raw' => self :: context_raw(),		
								'timestamp_raw' => date( 'c' ),
								'user' => intval($student_id),
							);
					
					$statement = self :: add_author($taking->user_id, $statement, $taking);		
				
					return $statement;
				} // end process
			) ); // end enrolled course
		}
	}
	
	// adds author to statement
	static function add_author($student_id, $statement, $taking = null) {
		if($student_id) {
				$student = get_userdata($student_id);		
				$user = array(
					'objectType' => 'Agent',
					'name' => $student->display_name,
					'mbox' => $student->user_email,
				);		
				
				$statement = array_merge( $statement, array( 'actor' => $user ) );
		}
		else {
			// get data from taking
			$user = array(
					'objectType' => 'Agent',
					'name' => ($taking->name ? $taking->name : __('Guest', 'watupro')),
					'mbox' => $taking->email,
				);	
			$statement = array_merge( $statement, array( 'actor_raw' => $user ) );	
		}		
		
		
		return $statement;
	}
	
	// return raw context
	static function context_raw() {
		$context = array(
			'extensions' => array(
				'http://id.tincanapi.com/extension/browser-info' => array( 'user_agent' => $_SERVER['HTTP_USER_AGENT'] ),
				'http://nextsoftwaresolutions.com/xapi/extensions/referer' => @$_SERVER['HTTP_REFERER'],
			),
			'platform' => defined( 'CTLT_PLATFORM' ) ? constant( 'CTLT_PLATFORM' ) : 'unknown'
		);
		return $context;							
	} // end context_rar
}