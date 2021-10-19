<?php
// Initialize this plugin. Called by 'init' hook.
function watupro_init() {	
	global $user_ID, $wpdb;
	if(get_option('watupro_debug_mode'))  {
		$wpdb->show_errors();
		if(!defined('DIEONDBERROR')) define( 'DIEONDBERROR', true );
	}
	
	require(WATUPRO_PATH."/helpers/htmlhelper.php");
	
	// define table names
	define('WATUPRO_EXAMS', $wpdb->prefix."watupro_master");
	define('WATUPRO_TAKEN_EXAMS', $wpdb->prefix."watupro_taken_exams");
	define('WATUPRO_QUESTIONS', $wpdb->prefix."watupro_question");
	define('WATUPRO_STUDENT_ANSWERS', $wpdb->prefix."watupro_student_answers");
	define('WATUPRO_USER_CERTIFICATES', $wpdb->prefix."watupro_user_certificates");
	define('WATUPRO_CATS', $wpdb->prefix."watupro_cats");
	define('WATUPRO_QCATS', $wpdb->prefix."watupro_qcats");
	define('WATUPRO_GRADES', $wpdb->prefix."watupro_grading");
	define('WATUPRO_CERTIFICATES', $wpdb->prefix."watupro_certificates");
	define('WATUPRO_ANSWERS', $wpdb->prefix."watupro_answer");
	define('WATUPRO_GROUPS', $wpdb->prefix."watupro_groups");
	define('WATUPRO_USERS_GROUPS', $wpdb->prefix."watupro_users_groups");
	define('WATUPRO_DEPENDENCIES', $wpdb->prefix."watupro_dependencies");
	define('WATUPRO_PAYMENTS', $wpdb->prefix."watupro_payments");
	define('WATUPRO_USER_FILES', $wpdb->prefix."watupro_user_files");
	define('WATUPRO_BUNDLES', $wpdb->prefix."watupro_bundles"); // bundles of paid quizzes
	define('WATUPRO_COUPONS', $wpdb->prefix.'watupro_coupons'); // promo codes
	define('WATUPRO_UNLOCK_LEVELS', $wpdb->prefix.'watupro_unlock_levels'); // unlock difficulty level criteria
	define('WATUPRO_UNLOCK_LOGS', $wpdb->prefix.'watupro_unlock_logs'); // unlock difficulty level log
	define('WATUPRO_EMAILLOG', $wpdb->prefix. "watupro_emaillog" );
	define('WATUPRO_THEMES', $wpdb->prefix. "watupro_themes" ); // custom design themes only
	define('WATUPRO_WEBHOOKS', $wpdb->prefix. "watupro_webhooks" ); // Zapier (and any other) webhooks
	
	// pagination definitions - let's use something more understandable than 0,1,2,3
	define('WATUPRO_PAGINATE_ONE_PER_PAGE', 0);
	define('WATUPRO_PAGINATE_ALL_ON_PAGE', 1);
	define('WATUPRO_PAGINATE_PAGE_PER_CATEGORY', 2);
	define('WATUPRO_PAGINATE_CUSTOM_NUMBER', 3);
	
	// unfiltered HTML?
	define('WATUPRO_UNFILTERED_HTML', get_option('watupro_unfiltered_html'));
    
	load_plugin_textdomain('watupro', false, WATUPRO_RELATIVE_PATH . '/languages/' );    
	
	// need to redirect the user?
	if(!empty($user_ID)) {
		$redirect=get_user_meta($user_ID, "watupro_redirect", true);		
		
		update_user_meta($user_ID, "watupro_redirect", "");
		
		if(!empty($redirect)) {
			 echo "<meta http-equiv='refresh' content='0;url=$redirect' />"; 
			 exit;
		}
	}	

    $manage_caps = current_user_can('manage_options')?'manage_options':'watupro_manage_exams';
    define('WATUPRO_MANAGE_CAPS', $manage_caps);
    
    // word used for quiz
    $quiz_word = get_option('watupro_quiz_word');
    $quiz_word = $quiz_word ? $quiz_word : __('quiz', 'watupro');
    $quiz_word_plural = get_option('watupro_quiz_word_plural');
    $quiz_word_plural = $quiz_word_plural ? $quiz_word_plural : __('quizzes', 'watupro');  
    define('WATUPRO_QUIZ_WORD', $quiz_word);
    define('WATUPRO_QUIZ_WORD_PLURAL', $quiz_word_plural);
    
   $version = get_bloginfo('version');
   if($version <= 3.3 or get_option('watupro_always_load_scripts')=="1") add_action('wp_enqueue_scripts', 'watupro_vc_scripts');   
	add_action('admin_enqueue_scripts', 'watupro_vc_scripts'); 
	add_action('wp_enqueue_scripts', 'watupro_vc_jquery');
	add_action('register_form','watupro_group_field'); // select user group
   
   add_shortcode( 'WATUPRO-LEADERBOARD', 'watupro_leaderboard' ); 
   add_shortcode( 'watupro-leaderboard', 'watupro_leaderboard' );
   add_shortcode( 'watupro-leaderboard-position', 'watupro_leaderboard_position' );
   add_shortcode( 'WATUPRO-MYEXAMS', 'watupro_myexams_code' );
   add_shortcode( 'watupro-myexams', 'watupro_myexams_code' );
	add_shortcode( 'WATUPRO-MYCERTIFICATES', 'watupro_mycertificates_code' );
	add_shortcode( 'watupro-mycertificates', 'watupro_mycertificates_code' );
	add_shortcode( 'WATUPROLIST', 'watupro_listcode' );
	add_shortcode( 'watuprolist', 'watupro_listcode' );
	add_shortcode( 'WATUPRO', 'watupro_shortcode' );
	add_shortcode( 'watupro', 'watupro_shortcode' );
	add_shortcode('watupro-userinfo', 'watupro_userinfo');
	add_shortcode('watuproshare-buttons', array('WatuPROSharing', 'display'));	
	add_shortcode('watupro-result', 'watupro_result');
	add_shortcode('watupro-answer', 'watupro_answer');
	add_shortcode('watupro-basic-chart', 'watupro_basic_chart');
	add_shortcode('watupro-quiz-attempts', 'watupro_quiz_attempts');
	add_shortcode('watupro-takings', 'watupro_shortcode_takings');
	add_shortcode('watupro-calculator', 'watupro_calculator');
	add_shortcode('watupro-users-completed', 'watupro_users_completed');
	add_shortcode('watupro-retake', 'watupro_retake_button');
	add_shortcode('watupro-segment-stats', 'watupro_segment_stats');
	add_shortcode('watupro-pdf', 'watupro_pdf_link');
	add_shortcode('watupro-paginator', 'watupro_shortcode_paginator');
	add_shortcode('watupro-multiquiz', 'watupro_multiquiz');
	
	// handle tablepress
	if(class_exists('TablePress') and method_exists('TablePress', 'load_controller') and method_exists('TablePress', 'load_model')) {
		TablePress::$model_options = TablePress::load_model( 'options' );
		TablePress::$model_table = TablePress::load_model( 'table' );
		TablePress::$controller = TablePress::load_controller( 'frontend' );
		TablePress::$controller->init_shortcodes();	
	}
	
	// prepare the custom filter on the content
	watupro_define_filters();
	
	// handle view certificate in new way - since version 3.7
	add_action('template_redirect', 'watupro_certificate_redirect');
	// PDF output of final screen
	add_action('template_redirect', 'watupro_taking_pdf');
	// replace title & meta tags on shared final screen
	add_action('template_redirect', 'watupro_share_redirect');
	add_action('template_redirect', 'watupro_social_share_snippet');
	// handle file downloads
	add_action('template_redirect', array('WatuPROFileHandler', 'download'));
	// handle dynamic CSS and custom quiz themes
	add_action('template_redirect', 'watupro_dynamic_css_redirect');
	add_action('template_redirect', 'watupro_custom_theme_redirect');
	
	// delete user data when deleting the user
	add_action('deleted_user', array('WTPUser', 'auto_delete_data'));
	
	// export data (GDPR) from [watupro-myexams]
	add_action('template_redirect', 'watupro_export_my_exams');	
	
	// handle unlocking difficulty levels 
	add_action('watupro_completed_exam', array('WatuPRODiffLevels', 'completed_exam'));
	
	// handle the MoolaMojo integration
	add_action('watupro_completed_exam_detailed', array('WatuPROTaking', 'transfer_moola'), 10, 5);
	
	// dashboard widget
	add_action('wp_dashboard_setup', array('WatuPROWidgets', 'widget'));
	
	// handle webhooks
	add_action('watupro_completed_exam', array('WatuPROWebhooks', 'dispatch'));
	
	if(watupro_intel() and !empty($_POST['stripe_bundle_pay'])) WatuPROPayment::Stripe(true); // process Stripe payment if any
	
	$design_theme = get_option('watupro_design_theme');
	if(empty($design_theme)) update_option('watupro_design_theme', 'default');
	
	add_action('admin_notices', 'watupro_admin_notices');	
	
	// Namaste! LMS integration filters and actions
	add_action('namaste-show-students-filter', 'watupro_namaste_show_students_filter');
	add_filter('namaste-students-filter', 'watupro_namaste_students_filter');
	add_action('namaste_manage_students_extra_th', 'watupro_namaste_students_extra_th');
	add_action('namaste_manage_students_extra_td', 'watupro_namaste_students_extra_td');
	
	// xAPI triggers
	if(class_exists('WP_Experience_API')) {
		include WATUPRO_PATH."/controllers/xapi.php";
		WatuPROXAPI :: register_triggers();
	} 
	
	// fix base sixty-four encoded stuff
	if(get_option('watupro_fix64_contact_fields') == '') watupro_fix64('contact_fields');
	if(get_option('watupro_fix64_advanced_settings') == '') watupro_fix64('advanced_settings');
	
	add_action('admin_notices', 'watupro_admin_notice');
	
	// cleanup old logs
	$cleanup_raw_log = get_option('watupro_cleanup_raw_log');
	if(empty($cleanup_raw_log)) $cleanup_raw_log = 7;
	if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_EMAILLOG."'")) == strtolower(WATUPRO_EMAILLOG)) {
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_EMAILLOG." WHERE date < CURDATE() - INTERVAL %d DAY", $cleanup_raw_log));
	}		
	
	$db_version = get_option("watupro_db_version");
	
	if(version_compare($db_version, '5.59') == -1) watupro_activate(true);
	
	// fix the horrible Jetpack bug as they don't seem to plan fixing it
	add_filter( 'tmp_grunion_allow_editor_view', '__return_false' );
	
	// check for updates
	$domain = $_SERVER['SERVER_NAME'];
	$license_key = get_option('watupro_license_key');
	$license_email = get_option('watupro_license_email');	
		
	// echo 'https://calendarscripts.info/cloud/update-plugin.php?plugin=watupro&m='.$license_email.'&k='.md5($license_key)."&action=info&domain=".$domain;
	if(!empty($license_key) and !empty($license_email) and get_option('watupro_no_auto_updates') != 1) {
		include WATUPRO_PATH.'/lib/plugin-update-checker/plugin-update-checker.php';	
		$MyUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
			'https://calendarscripts.info/cloud/update-plugin.php?plugin=watupro&m='.$license_email.'&k='.md5($license_key)."&action=info&domain=".$domain,
		    WATUPRO_PATH.'/watupro.php',
		    'watupro'
		);
	}
	
} // end init()

// actual activation & installation
function watupro_activate($update = false) {
	global $wpdb;

	if(!$update) watupro_init();

	// Initial options.
	add_option('watupro_show_answers', 1);
	add_option('watupro_single_page', 0);
	add_option('watupro_answer_type', 'radio');    
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
   $wpdb->show_errors();
   $collation = $wpdb->get_charset_collate();
        
        // exams
        if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_EXAMS."'")) != strtolower(WATUPRO_EXAMS)) {  
            $sql = "CREATE TABLE `".WATUPRO_EXAMS."`(
						`ID` int(11) unsigned NOT NULL auto_increment,
						`name` varchar(255) NOT NULL DEFAULT '',
						`description` TEXT NOT NULL,
						`final_screen` TEXT NOT NULL,
						`added_on` datetime NOT NULL,
			         `is_active` TINYINT UNSIGNED NOT NULL DEFAULT '1',
			         `require_login` TINYINT UNSIGNED NOT NULL DEFAULT '0',
			         `take_again` TINYINT UNSIGNED NOT NULL DEFAULT '0', 
			         `email_taker` TINYINT UNSIGNED NOT NULL DEFAULT '0', 
			         `email_admin` TINYINT UNSIGNED NOT NULL DEFAULT '0', 
			         `randomize_questions` TINYINT UNSIGNED DEFAULT '0', 
			         `login_mode` VARCHAR(100) NOT NULL DEFAULT 'open',
			         `time_limit` INT UNSIGNED NOT NULL DEFAULT '0',
						`pull_random` INT UNSIGNED NOT NULL DEFAULT '0',
						`show_answers` TEXT,
						`random_per_category` TINYINT UNSIGNED NOT NULL default 0,
						`group_by_cat` TINYINT UNSIGNED NOT NULL DEFAULT 0,
						`num_answers` INT UNSIGNED NOT NULL DEFAULT 0,
						`single_page` TINYINT UNSIGNED NOT NULL DEFAULT 0,
						`cat_id` INT UNSIGNED NOT NULL DEFAULT 0,
						PRIMARY KEY  (ID)
					) CHARACTER SET utf8;";
            $wpdb->query($sql);   
        }    
        
        // questions
        if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_QUESTIONS."'")) != strtolower(WATUPRO_QUESTIONS)) {  
            $sql = "CREATE TABLE `".WATUPRO_QUESTIONS."` (
							ID int(11) unsigned NOT NULL auto_increment,
							exam_id int(11) unsigned NOT NULL DEFAULT '0',
							question mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
							answer_type char(15) COLLATE utf8_unicode_ci NOT NULL default '',
							sort_order int(3) NOT NULL default 0,
							cat_id INT UNSIGNED NOT NULL DEFAULT 0,
							random_per_category TINYINT UNSIGNED NOT NULL DEFAULT 0,
							explain_answer TEXT,
							PRIMARY KEY  (ID),
							KEY quiz_id (exam_id)
						) CHARACTER SET utf8;";
            $wpdb->query($sql);    
        }    
        
        // answers
        if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_ANSWERS."'")) != strtolower(WATUPRO_ANSWERS)) {  
            $sql = "CREATE TABLE `".WATUPRO_ANSWERS."` (
						ID int(11) unsigned NOT NULL auto_increment,
						question_id int(11) unsigned NOT NULL default '0',
						answer TEXT NOT NULL,
						correct enum('0','1') NOT NULL default '0',
						point DECIMAL(6,2) DEFAULT '0.00',
						sort_order int(3) NOT NULL default 0,
						PRIMARY KEY  (ID)
					) CHARACTER SET utf8;";
            $wpdb->query($sql);         
        }  
        
		// grades
		if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_GRADES."'") != WATUPRO_GRADES)) {  
            $sql = "CREATE TABLE `".WATUPRO_GRADES."` (
				 `ID` int(11) NOT NULL AUTO_INCREMENT,
				 `exam_id` int(11) NOT NULL default 0,
				 `gtitle` varchar (255) NOT NULL default '',
				 `gdescription` mediumtext COLLATE utf8_unicode_ci NOT NULL,
				 `gfrom` DECIMAL(10,2) NOT NULL default '0.00',
				 `gto` DECIMAL(10,2) NOT NULL default '0.00',
				 `certificate_id` INT UNSIGNED NOT NULL default 0,
				 PRIMARY KEY (`ID`)
				) CHARACTER SET utf8";
            $wpdb->query($sql);             
        }   
        
        // taken exams
        if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_TAKEN_EXAMS."'")) != strtolower(WATUPRO_TAKEN_EXAMS)) {  
            $sql = "CREATE TABLE `".WATUPRO_TAKEN_EXAMS."` (
				  	`ID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	            `user_id` INT UNSIGNED NOT NULL ,
	            `exam_id` INT UNSIGNED NOT NULL ,
	            `date` DATE NOT NULL ,
	            `points` DECIMAL(6,2) NOT NULL DEFAULT '0.00',
	            `details` MEDIUMTEXT NOT NULL ,
	            `result` TEXT NOT NULL ,
	            `start_time` DATETIME NOT NULL DEFAULT '2000-01-01 00:00:00',
				  `ip` VARCHAR(40) NOT NULL,
				  `in_progress` TINYINT UNSIGNED NOT NULL default 0
				) CHARACTER SET utf8";
            $wpdb->query($sql);             
        }   
        
        // links to taken_exams
        if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_STUDENT_ANSWERS."'")) != strtolower(WATUPRO_STUDENT_ANSWERS)) {  
            $sql = "CREATE TABLE `".WATUPRO_STUDENT_ANSWERS."` (
				  `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                  `user_id` INT UNSIGNED NOT NULL default 0, 
                  `exam_id` INT UNSIGNED NOT NULL default 0,
                  `taking_id` INT UNSIGNED NOT NULL default 0,
                  `question_id` INT UNSIGNED NOT NULL default 0,
                  `answer` TEXT NOT NULL,
				  `points` DECIMAL(10,2) NOT NULL default '0.00',
				  `question_text` TEXT  NOT NULL
				) CHARACTER SET utf8";
            $wpdb->query($sql);              
        }

		// certificates
        if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_CERTIFICATES."'")) != strtolower(WATUPRO_CERTIFICATES)) {  
            $sql = "CREATE TABLE `".WATUPRO_CERTIFICATES."` (
				  `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				  `title` VARCHAR(255) NOT NULL default '', 
           		`html` LONGTEXT NOT NULL 
				) CHARACTER SET utf8";
            $wpdb->query($sql);         
        }
       
      // question categories
      if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_QCATS."'")) != strtolower(WATUPRO_QCATS)) {  
            $sql = "CREATE TABLE `".WATUPRO_QCATS."` (
				  `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				  `name` VARCHAR(255) NOT NULL default ''
				) CHARACTER SET utf8";
            $wpdb->query($sql);         
      } 
      
      // exam categories
      if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_CATS."'")) != strtolower(WATUPRO_CATS)) {  
            $sql = "CREATE TABLE `".WATUPRO_CATS."` (
				  `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				  `name` VARCHAR(255) NOT NULL DEFAULT '',
				  `ugroups` VARCHAR(255) NOT NULL DEFAULT ''
				) CHARACTER SET utf8";
            $wpdb->query($sql);         
      } 
		      
      // user groups - optionally user can have a group
      if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_GROUPS."'")) != strtolower(WATUPRO_GROUPS)) {  
            $sql = "CREATE TABLE `".WATUPRO_GROUPS."` (
				  `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				  `name` VARCHAR(255) NOT NULL DEFAULT '',
				  `is_def` TINYINT UNSIGNED NOT NULL DEFAULT 0
				) CHARACTER SET utf8";
            $wpdb->query($sql);         
      }
      
       // user groups - optionally user can have a group
      if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_USERS_GROUPS."'")) != strtolower(WATUPRO_USERS_GROUPS)) {  
            $sql = "CREATE TABLE `".WATUPRO_USERS_GROUPS."` (
				  `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				  `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
				  `group_id` INT UNSIGNED NOT NULL DEFAULT 0
				) CHARACTER SET utf8";
            $wpdb->query($sql);         
      }
      
      // keep track about user's certificates
      if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_USER_CERTIFICATES."'")) != strtolower(WATUPRO_USER_CERTIFICATES)) {  
            $sql = "CREATE TABLE `".WATUPRO_USER_CERTIFICATES."` (
						  `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						  `user_id` INT UNSIGNED NOT NULL default 0,
						  `certificate_id` INT UNSIGNED NOT NULL default 0
						) CHARACTER SET utf8";
            $wpdb->query($sql);              
      }     
      
      // files uploaded as user answers
      // keep track about user's certificates
      if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_USER_FILES."'")) != strtolower(WATUPRO_USER_FILES)) {  
            $sql = "CREATE TABLE `".WATUPRO_USER_FILES."` (
						  `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						  `user_id` INT UNSIGNED NOT NULL default 0,
						  `taking_id` INT UNSIGNED NOT NULL default 0,
						  `user_answer_id` INT UNSIGNED NOT NULL default 0,
						  `filename` VARCHAR(255) NOT NULL default '',
						  `filesize` INT UNSIGNED NOT NULL default 0 /* size in  KB */,
						  `filetype` VARCHAR(50) NOT NULL default '',
						  `filecontents` LONGBLOB
						) CHARACTER SET utf8";
            $wpdb->query($sql);              
      }      
      
       // this is email log of all the messages sent in the system 
		  if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_EMAILLOG."'")) != strtolower(WATUPRO_EMAILLOG)) {	  
				$sql = "CREATE TABLE `" . WATUPRO_EMAILLOG . "` (
					  `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
					  `sender` VARCHAR(255) NOT NULL DEFAULT '',
					  `receiver` VARCHAR(255) NOT NULL DEFAULT '',
					  `subject` VARCHAR(255) NOT NULL DEFAULT '',
					  `date` DATE,
					  `datetime` TIMESTAMP,
					  `status` VARCHAR(255) NOT NULL DEFAULT 'OK'				  
					) DEFAULT CHARSET=utf8;";
				$wpdb->query($sql);
		  }
		  
		   // custom design themes        
		   if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_THEMES."'")) != strtolower(WATUPRO_THEMES)) {  
            $sql = "CREATE TABLE `".WATUPRO_THEMES."`(
						`ID` int(11) unsigned NOT NULL auto_increment PRIMARY KEY,
						`name` VARCHAR(255) NOT NULL DEFAULT '',
						`css` TEXT
					) CHARACTER SET utf8;";
            $wpdb->query($sql);   
        }    
      
       // intelligence tables
      if(watupro_intel()) { 
      	// exam dependencies
	      if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_DEPENDENCIES."'")) != strtolower(WATUPRO_DEPENDENCIES)) {  
	            $sql = "CREATE TABLE `".WATUPRO_DEPENDENCIES."` (
					  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `exam_id` int(10) unsigned NOT NULL default 0,
					  `depend_exam` int(10) unsigned NOT NULL default 0,
					  `depend_points` int(11) NOT NULL default 0,
					  `mode` VARCHAR(100) NOT NULL DEFAULT 'points',
					  PRIMARY KEY (`ID`)
					) CHARACTER SET utf8";
	        $wpdb->query($sql);           
	      }
	       
	      // exam fee payments
	      if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_PAYMENTS."'")) != strtolower(WATUPRO_PAYMENTS)) {  
	            $sql = "CREATE TABLE `".WATUPRO_PAYMENTS."` (
					  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `exam_id` int(10) unsigned NOT NULL default 0,
					  `user_id` int(10) unsigned NOT NULL default 0,
					  `date` DATE NOT NULL,
					  `amount` DECIMAL(8,2) NOT NULL default '0.00',
					  `status` VARCHAR(100) NOT NULL default '',
					  `paycode` VARCHAR(100) NOT NULL default '',
					  PRIMARY KEY (`ID`)
					) CHARACTER SET utf8";
	          $wpdb->query($sql);       
	            
	            // add also the USD option by default
					update_option("watupro_currency", "USD");         
	      } 
	      
	      // bundles of paid quizzes - will be stored in the DB so we can check the price
	      // will generate shortcodes for publishing payment buttons
	      if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_BUNDLES."'")) != strtolower(WATUPRO_BUNDLES)) {  
	            $sql = "CREATE TABLE `".WATUPRO_BUNDLES."` (
					  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `price` DECIMAL(8,2),
					  `bundle_type` VARCHAR(100) NOT NULL DEFAULT 'quizzes', /* quizzes, category, or num_quizzes */
					  `quiz_ids` VARCHAR(255) NOT NULL DEFAULT '',
					  `cat_ids` VARCHAR(255) NOT NULL DEFAULT '',  
					  PRIMARY KEY (`ID`)
					) CHARACTER SET utf8";
	        $wpdb->query($sql);           
	      }
	      
	      // promo codes table
		   if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_COUPONS."'")) != strtolower(WATUPRO_COUPONS)) {  
            $sql = "CREATE TABLE `".WATUPRO_COUPONS."`(
						`ID` int(11) unsigned NOT NULL auto_increment,
						`discount` TINYINT UNSIGNED NOT NULL DEFAULT 0,
						`code` VARCHAR(100) NOT NULL DEFAULT '',
						`num_uses` INT UNSIGNED NOT NULL DEFAULT 0,
						`times_used` INT UNSIGNED NOT NULL DEFAULT 0,
						PRIMARY KEY  (ID)
					) CHARACTER SET utf8;";
            $wpdb->query($sql);   
        }    
        
        // unock difficulty levels criteria
		   if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_UNLOCK_LEVELS."'")) != strtolower(WATUPRO_UNLOCK_LEVELS)) {  
            $sql = "CREATE TABLE `".WATUPRO_UNLOCK_LEVELS."`(
						`ID` int(11) unsigned NOT NULL auto_increment,
						`unlock_level` VARCHAR(255) NOT NULL DEFAULT '',
						`min_points` INT UNSIGNED NOT NULL DEFAULT 0,
						`min_questions` INT UNSIGNED NOT NULL DEFAULT 0,
						`percent_correct` TINYINT UNSIGNED DEFAULT 0,
						`from_level` VARCHAR(255) NOT NULL DEFAULT '',
						PRIMARY KEY  (ID)
					) CHARACTER SET utf8;";
            $wpdb->query($sql);   
        }    
        
        // log for unlocked difficulty levels        
		   if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_UNLOCK_LOGS."'")) != strtolower(WATUPRO_UNLOCK_LOGS)) {  
            $sql = "CREATE TABLE `".WATUPRO_UNLOCK_LOGS."`(
						`ID` int(11) unsigned NOT NULL auto_increment,
						`unlocked_level` VARCHAR(255) NOT NULL DEFAULT '',
						`user_id` INT UNSIGNED NOT NULL DEFAULT 0,
						`taking_id` INT UNSIGNED NOT NULL DEFAULT 0,
						PRIMARY KEY  (ID)
					) CHARACTER SET utf8;";
            $wpdb->query($sql);   
        }    
        
        // Webhooks
        $sql = "CREATE TABLE " . WATUPRO_WEBHOOKS . " (
			  ID int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			  exam_id int(11) UNSIGNED NOT NULL DEFAULT 0,
			  grade_id int(11) UNSIGNED NOT NULL DEFAULT 0,
			  hook_url varchar(255) NOT NULL DEFAULT '',
			  payload_config TEXT,
			  PRIMARY KEY  (ID)			  
			) $collation";
			dbDelta( $sql );	  	
         
	      watupro_add_db_fields(array(
      		array("name"=>"method", "type"=>"VARCHAR(100) NOT NULL DEFAULT 'paypal'"),
      		array("name"=>"bundle_id", "type"=>"INT UNSIGNED NOT NULL DEFAULT 0"),
      		array("name"=>"access_code", "type"=>"VARCHAR(20) NOT NULL DEFAULT ''"), /* if user is not logged in */					
      		array("name"=>"num_quizzes_used", "type"=>"INT UNSIGNED NOT NULL DEFAULT 0"), /* for bundles of "num_quizzes" type */
      		array("name"=>"used_quiz_ids", "type"=>"TEXT"), /* for bundles of "num_quizzes" type */
      		array("name"=>"certificate_id", "type"=>"INT UNSIGNED NOT NULL DEFAULT 0"),
			), WATUPRO_PAYMENTS); 
			
			 watupro_add_db_fields(array(
      		array("name"=>"redirect_url", "type"=>"VARCHAR(255) NOT NULL DEFAULT ''"),      							
      		array("name"=>"is_time_limited", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),
      		array("name"=>"time_limit", "type"=>"SMALLINT UNSIGNED NOT NULL DEFAULT 0"), /* interval in days */  
      		array("name"=>"name", "type"=>"VARCHAR(255) NOT NULL DEFAULT ''"),
      		array("name"=>"editor_id", "type"=>"INT UNSIGNED NOT NULL DEFAULT 0"),      		
      		array("name"=>"num_quizzes", "type"=>"INT UNSIGNED NOT NULL DEFAULT 0"), /* for bundles for 10 tests, 100 tests etc */
			), WATUPRO_BUNDLES); 
			
			watupro_add_db_fields(array(
      		array("name"=>"disc_type", "type"=>"VARCHAR(20) NOT NULL DEFAULT 'percent'"),      				
      		array("name"=>"quiz_id", "type"=>"INT UNSIGNED NOT NULL DEFAULT 0"),
      		array("name"=>"date_condition", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),
      		array("name"=>"start_date", "type"=>"DATE"),
      		array("name"=>"end_date", "type"=>"DATE"),
      		array("name"=>"editor_id", "type"=>"INT UNSIGNED NOT NULL DEFAULT 0"),
			), WATUPRO_COUPONS);			
	   } // end Intelligence related tables
       
		# $wpdb->print_error();				
		update_option( "watupro_delete_db", '' );
        
      // add student role if not exists
      // most probably this is no longer required
      $res = add_role('student', 'Student', array(
            'read' => true, // True allows that capability
            'watupro_exams' => true));   
            
      // database upgrades - version 1.1
      $db_version = get_option("watupro_db_version");
      
      // change bundle cat IDs
		if(!empty($db_version) and $db_version < 5.13) {
		   $wpdb->query("ALTER TABLE ".WATUPRO_BUNDLES." CHANGE `cat_id` `cat_ids` VARCHAR(255) NOT NULL DEFAULT ''");
		}
      
      watupro_add_db_fields(array(      		
					array("name"=>"end_time", "type"=>"DATETIME NOT NULL DEFAULT '2000-01-01 00:00:00'"),
					array("name"=>"grade_id", "type"=>"INT UNSIGNED NOT NULL default 0"),
					array("name"=>"percent_correct", "type"=>"TINYINT UNSIGNED NOT NULL default 0"),
					array("name"=>"email", "type"=>"TEXT"),					
					array("name"=>"catgrades", "type"=>"TEXT"),
					array("name"=>"catgrades_serialized", "type"=>"TEXT"),
					array("name"=>"serialized_questions", "type"=>"TEXT"),
					array("name"=>"num_hints_used", "type"=>"SMALLINT UNSIGNED NOT NULL DEFAULT 0"),
					array("name"=>"name", "type"=>"TEXT"),
					array("name"=>"contact_data", "type"=>"TEXT"),
					array("name"=>"marked_for_review", "type"=>"TEXT"),
					array("name"=>"field_company", "type"=>"TEXT"),
					array("name"=>"field_phone", "type"=>"TEXT"),
					array("name"=>"custom_field1", "type"=>"TEXT"),
					array("name"=>"custom_field2", "type"=>"TEXT"),
					array("name"=>"percent_points", "type"=>"TINYINT UNSIGNED NOT NULL default 0"),
					array("name"=>"source_url", "type"=>"VARCHAR(255) NOT NULL DEFAULT ''"),
					array("name"=>"num_correct", "type"=>"SMALLINT NOT NULL DEFAULT 0"),
					array("name"=>"num_wrong", "type"=>"SMALLINT NOT NULL DEFAULT 0"),
					array("name"=>"num_empty", "type"=>"SMALLINT NOT NULL DEFAULT 0"),
					array("name"=>"max_points", "type"=>"DECIMAL(10,2) NOT NULL DEFAULT '0.00'"),
					array("name"=>"from_watu", "type"=>"TINYINT NOT NULL DEFAULT 0"),
					array("name"=>"auto_submitted", "type"=>"TINYINT NOT NULL DEFAULT 0"),
					array("name"=>"timer_log", "type"=>"TEXT"), /* optional timer log */
					array("name"=>"ignore_attempt", "type"=>"TINYINT NOT NULL DEFAULT 0"), /* to ignore this attempt when the number of attempts is limited */
					array("name"=>"current_page", "type"=>"INT NOT NULL DEFAULT 0"), /* to know the current page for in progress category paginated quizzes */
				), WATUPRO_TAKEN_EXAMS);
	
		 watupro_add_db_fields(array(   		
   		array("name"=>"times_to_take", "type"=>"SMALLINT UNSIGNED NOT NULL DEFAULT 0"),
   		array("name"=>"mode", "type"=>"VARCHAR(100) DEFAULT 'live'"),
			array("name"=>"fee", "type"=>"DECIMAL(8,2) NOT NULL DEFAULT '0.00'"),
			array("name"=>"require_captcha", "type"=>"TINYINT NOT NULL DEFAULT '0'"),
			array("name"=>"grades_by_percent", "type"=>"TINYINT NOT NULL DEFAULT '0'"),
			array("name"=>"admin_email", "type"=>"TEXT"),
			array("name"=>"disallow_previous_button", "type"=>"TINYINT UNSIGNED NOT NULL default 0"),
			array("name"=>"email_output", "type"=>"TEXT"),
			array("name"=>"live_result", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),
			array("name"=>"gradecat_design", "type"=>"TEXT"),
			array("name"=>"is_scheduled", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),
      	array("name"=>"schedule_from", "type"=>"DATETIME"),
      	array("name"=>"schedule_to", "type"=>"DATETIME"),
      	array("name"=>"submit_always_visible", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),
      	array("name"=>"retake_grades", "type"=>"TEXT"),
      	array("name"=>"show_pagination", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),
      	array("name"=>"advanced_settings", "type"=>"TEXT"), /* this will be serialized array */
      	array("name"=>"enable_save_button", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),
      	array("name"=>"shareable_final_screen", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),
      	array("name"=>"redirect_final_screen", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),
      	array("name"=>"editor_id", "type"=>"INT UNSIGNED NOT NULL DEFAULT 0"),
      	array("name"=>"question_hints", "type"=>"TEXT"), /* a string like 1|10|3 */
      	array("name"=>"takings_by_ip", "type"=>"INT UNSIGNED NOT NULL DEFAULT 0"),
      	array("name"=>"reuse_default_grades", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),		
      	array("name"=>"store_progress", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"), /*store progress on the fly (when 1 question per page)*/
      	array("name"=>"custom_per_page", "type"=>"SMALLINT UNSIGNED NOT NULL DEFAULT 0"), /* when paginated custom number per page */
      	array("name"=>"randomize_cats", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),
      	array("name"=>"no_ajax", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),
      	array("name"=>"email_subject", "type"=>"TEXT"),
      	array("name"=>"pay_always", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"), /* whether to pay once for each quiz attempt */
      	array("name" => "reuse_questions_from", "type" => "TEXT"), /* This exists here and in the Intelligence module */
      	array("name"=>"published_odd", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"), /* Published in non-standard way*/
      	array("name"=>"published_odd_url", "type"=>"TEXT"), /* The URL where it's publushed in non-srandard way */
      	array("name"=>"admin_comments", "type"=>"TEXT"), /* Internal comments */
      	array("name"=>"delay_results", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"), /* to delay showing of results */
      	array("name"=>"delay_results_date", "type"=>"DATE"), /* The date after which the results will be available */
      	array("name"=>"delay_results_content", "type"=>"TEXT"), /* The content that will be shown before releasing the real output */
      	array("name"=>"namaste_courses", "type"=>"TEXT"), /* string like |id1|id2| when the quiz is limited to namaste course members */
      	array("name"=>"is_likert_survey", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"), /* to show in likert-table format */
      	array("name"=>"tags", "type"=>"TEXT"),
      	array("name"=>"thumb", "type"=>"TEXT"),
      	array("name"=>"limit_reused_questions", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"), /* to use only specific reused questions */      	
      	array("name"=>"reused_question_ids", "type"=>"TEXT"), /* comma separated list of specifc reused quesiton IDs */
      	array("name"=>"user_schedules", "type"=>"TEXT"), /* individual schedules per user, serialized array of arrays */
		), WATUPRO_EXAMS);	
				
			 watupro_add_db_fields(array(
	    		array("name"=>"is_required", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),
	    		array("name"=>"correct_condition", "type"=>"VARCHAR(255) NOT NULL DEFAULT ''"),
	    		array("name"=>"max_selections", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),
	    		array("name"=>"is_inactive", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),
	    		array("name" => "is_survey", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),
	    		array("name" => "elaborate_explanation", "type"=>"VARCHAR(100) NOT NULL DEFAULT ''"),
	    		array("name" => "open_end_mode", "type" => "VARCHAR(255) NOT NULL DEFAULT ''"),
	    		array("name" => "tags", "type" => "VARCHAR(255) NOT NULL DEFAULT ''"),
				array("name" => "open_end_display", "type" => "VARCHAR(255) NOT NULL DEFAULT 'medium'"),
				array("name" => "exclude_on_final_screen", "type" => "TINYINT UNSIGNED NOT NULL DEFAULT 0"),
				array("name" => "hints", "type" => "TEXT"),
				array("name" => "compact_format", "type" => "TINYINT UNSIGNED NOT NULL DEFAULT 0"),
				array("name" => "round_points", "type" => "TINYINT UNSIGNED NOT NULL DEFAULT 0"),
				array("name" => "importance", "type" => "TINYINT UNSIGNED DEFAULT 0"), /* when 100, always include */ 
				array("name" => "calculate_whole", "type" => "TINYINT UNSIGNED DEFAULT 0"), /* whether to treat (sorting) question is a whole when calculating points */
				array("name" => "unanswered_penalty", "type" => "DECIMAL(8,2) NOT NULL DEFAULT '0.00'"), /*penalize not-answering with negative points*/
				array("name" => "truefalse", "type" => "TINYINT NOT NULL DEFAULT 0"), /* is this True/False subtype? */
				array("name" => "accept_feedback", "type" => "TINYINT NOT NULL DEFAULT 0"), /* accept user feedback */
				array("name" => "feedback_label", "type" => "VARCHAR(100) NOT NULL DEFAULT ''"), /* The text before the feedback box */
				array("name" => "reward_only_correct", "type" => "TINYINT NOT NULL DEFAULT 0"), /* reward positive points only when the answer is correct */
				array("name" => "discard_even_negative", "type" => "TINYINT NOT NULL DEFAULT 0"), /* reward ANY points only when the answer is correct */
				array("name" => "difficulty_level", "type" => "VARCHAR(100) NOT NULL DEFAULT ''"), 
				array("name" => "num_columns", "type" => "TINYINT NOT NULL DEFAULT 0"), // num columns for the choices 1 to 4
				array("name" => "design", "type" => "TEXT"), /* various design adjustments, serialized array */ 
				array("name" => "allow_checkbox_groups", "type" => "TINYINT NOT NULL DEFAULT 0"), /* to allow groups of checkboxes in checkbox questions */
				array("name" => "correct_gap_points", "type" => "DECIMAL(6,2) NOT NULL DEFAULT '0.00'"), /* these are now needed even without Intelligence module */
		 	   array("name" => "incorrect_gap_points", "type" => "DECIMAL(6,2) NOT NULL DEFAULT '0.00'"), /* these are now needed even without Intelligence module */
		 	   array("name" => "is_flashcard", "type" => "TINYINT UNSIGNED NOT NULL DEFAULT 0"), /* these are now needed even without Intelligence module */
		 	   array("name" => "dont_randomize_answers", "type" => "TINYINT UNSIGNED NOT NULL DEFAULT 0"),
		 	   array("name" => "reduce_points_per_hint", "type" => "DECIMAL(6,2) NOT NULL DEFAULT '0.00'"),
		 	   array("name" => "reduce_hint_points_to_zero", "type" => "TINYINT UNSIGNED NOT NULL DEFAULT 0"), /* when reducing points for using hints do't go below zero */
		 	   array("name" => "accept_rating", "type" => "TINYINT UNSIGNED NOT NULL DEFAULT 0"), /* when accept rating per individual question is enabled */
		 	   array("name" => "file_upload_required", "type" => "TINYINT UNSIGNED NOT NULL DEFAULT 0"), /* file upload is required */
		 	   array("name" => "no_negative", "type" => "TINYINT UNSIGNED NOT NULL DEFAULT 0"), /* discard negative points */
		 	   array("name" => "max_allowed_points", "type" => "DECIMAL(6,2) NOT NULL DEFAULT '0.00'"), /* maximum possible/allowed points on this question */		 	   
		 	   array("name" => "limit_words", "type" => "SMALLINT UNSIGNED NOT NULL DEFAULT 0"), /* words limit for open-end questions */
		 	   array("name"=>"title", "type"=>"VARCHAR(255) NOT NULL DEFAULT ''"),  
		 	   array("name"=>"dont_explain_unanswered", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),
		 	   array("name"=>"use_wpautop", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"), /* use wpautop() on display() instead of our watupro_nl2br */
			), WATUPRO_QUESTIONS);
			
			watupro_add_db_fields(array(	    		
	    		array("name"=>"require_approval", "type"=>"TINYINT UNSIGNED NOT NULL default 0"),
	    		array("name"=>"require_approval_notify_admin", "type"=>"TINYINT UNSIGNED NOT NULL default 0"),
	    		array("name"=>"approval_notify_user", "type"=>"TINYINT UNSIGNED NOT NULL default 0"),
	    		array("name"=>"approval_email_subject", "type"=>"VARCHAR(255) NOT NULL default ''"),
	    		array("name"=>"approval_email_message", "type"=>"TEXT"),
	    		array("name"=>"editor_id", "type"=>"INT UNSIGNED NOT NULL DEFAULT 0"),
	    		array("name"=>"is_multi_quiz", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),	   	    		
	    		array("name"=>"quiz_ids", "type"=>"VARCHAR(255) NOT NULL DEFAULT ''"), /* string like |1|2|3| when is_multi_quiz is 1 */
	    		array("name"=>"avg_points", "type"=>"INT UNSIGNED NOT NULL DEFAULT 0"), /* avg. points per taking required when multi_quiz is 1 */
	    		array("name"=>"avg_percent", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"), /* avg. percent per taking required when multi_quiz is 1 */	   	
	    		array("name"=>"has_expiration", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),	      		
	    		array("name"=>"expiration_period", "type"=>"VARCHAR(100) NOT NULL DEFAULT ''"), /* SQL-friendly text like "3 month" or "1 year"*/
	    		array("name"=>"expired_message", "type"=>"TEXT"),	   
	    		array("name"=>"expiration_mode", "type"=>"VARCHAR(255) NOT NULL DEFAULT 'period'"), /* period or date */
	    		array("name"=>"expiration_date", "type"=>"DATE"), /* when expiration_mode='date' */   		
	    		array("name"=>"var_text", "type"=>"TEXT"), /* Text for the %%CERTIFICATE%% variable */
	    		array("name"=>"avg_on_each_quiz", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"), /* The averages for multi quiz certificates are required on each quiz */
	    		array("name"=>"fee", "type"=>"DECIMAL(8,2) NOT NULL DEFAULT '0.00'"),
			), WATUPRO_CERTIFICATES);
			
			watupro_add_db_fields(array(
	    		array("name"=>"exam_id", "type"=>"INT UNSIGNED NOT NULL default 0"),
	    		array("name"=>"taking_id", "type"=>"INT UNSIGNED NOT NULL default 0"),
	    		array("name"=>"pending_approval", "type"=>"TINYINT UNSIGNED NOT NULL default 0"),
	    		array("name"=>"pdf_output", "type"=>"LONGBLOB"), /* currently Docraptor, to avoid multiple requests*/
	    		array("name"=>"public_access", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),
	    		array("name"=>"email", "type"=>"VARCHAR(100) NOT NULL DEFAULT ''"), /* will be filled only for non-users */
	    		array("name"=>"quiz_ids", "type"=>"VARCHAR(255) NOT NULL DEFAULT ''"), /* string like |1|2|3| when is_multi_quiz is 1 */
	    		array("name"=>"avg_points", "type"=>"INT UNSIGNED NOT NULL DEFAULT 0"), /* avg. points per taking achieved when multi_quiz is 1 */
	    		array("name"=>"avg_percent", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"), /* avg. percent per taking achieved when multi_quiz is 1 */	   
			), WATUPRO_USER_CERTIFICATES);
			
			watupro_add_db_fields(array(
    			array("name"=>"description", "type"=>"TEXT NOT NULL"),
    			array("name"=>"editor_id", "type"=>"INT UNSIGNED NOT NULL DEFAULT 0"),
    			array("name"=>"parent_id", "type"=>"INT UNSIGNED NOT NULL DEFAULT 0"),
    			array("name"=>"exclude_from_reports", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"), /* Exclude from reports and exports */
    			array("name"=>"icon", "type"=>"VARCHAR(255) NOT NULL DEFAULT ''"), /* icon/image to be used in category paginator */
			), WATUPRO_QCATS);
			
			watupro_add_db_fields(array(
    			array("name"=>"editor_id", "type"=>"INT UNSIGNED NOT NULL DEFAULT 0"),
    			array("name"=>"parent_id", "type"=>"INT UNSIGNED NOT NULL DEFAULT 0"),
			), WATUPRO_CATS);
			
			watupro_add_db_fields(array(
    			array("name"=>"cat_id", "type"=>"INT UNSIGNED NOT NULL DEFAULT 0"), // question category ID
    			array("name"=>"editor_id", "type"=>"INT UNSIGNED NOT NULL DEFAULT 0"), // used only for default grades
    			array("name"=>"percentage_based", "type"=>"INT UNSIGNED NOT NULL DEFAULT 0"), // used only for default grades
    			array("name"=>"redirect_url", "type"=>"VARCHAR(255) NOT NULL DEFAULT ''"),
    			array("name"=>"is_cumulative_grade", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"), // in personality quizzes only
    			array("name"=>"included_grade_ids", "type"=>"VARCHAR(255) NOT NULL DEFAULT ''"), // in personality quizzes only, string like |1|2|3|    
    			array("name" => 'moola', "type" => "INT NOT NULL DEFAULT 0"), // integration with MoolaMojo
    			array("name" => 'category_requirements', "type" => "TEXT"), /* serialized array when the grades in the test depend on category performance */
			), WATUPRO_GRADES);
			
			watupro_add_db_fields(array(
    			array("name"=>"is_correct", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),
				array("name"=>"snapshot", "type"=>"TEXT NOT NULL"),
				array("name"=>"hints_used", "type"=>"TEXT"),
				array("name"=>"num_hints_used", "type"=>"SMALLINT UNSIGNED NOT NULL DEFAULT 0"),
				array("name"=>"onpage_question_num", "type"=>"SMALLINT UNSIGNED NOT NULL DEFAULT 0"), /* use to return on the last page when returning to complete quiz with $in_progress*/
				array("name"=>"timestamp", "type"=>"TIMESTAMP"),
				array("name"=>"feedback", "type"=>"TEXT"),
				array("name"=>"rating", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"),
				array("name"=>"freetext_answer", "type"=>"VARCHAR(255) NOT NULL DEFAULT ''"), /* string that matches any optional freetext answers of the user to ONE choice per question that allows it*/
				array("name"=>"chk_answers", "type"=>"TEXT"), /* checkbox answers separated by | to make searching & stats easier */
				array("name"=>"percent_points", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"), /* percent from max points on this question */ 
			), WATUPRO_STUDENT_ANSWERS);
			
			watupro_add_db_fields(array(
				array('name' => 'explanation', 'type' => 'TEXT'),
				array("name"=>"grade_id", "type"=>"VARCHAR(255) NOT NULL DEFAULT '0' COMMENT 'Used only in personality quizzes' "),
			   array("name"=>"chk_group", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"), /* group number in questions that support checkbox groups */
			   array("name"=>"is_checked", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"), /* checked by default (for radios and checkbox questions) */
			   array("name"=>"accept_freetext", "type"=>"TINYINT UNSIGNED NOT NULL DEFAULT 0"), /* to accept additional free text answer (for radios and checkbox questions) */
			), WATUPRO_ANSWERS);
			
			
			// db updates 3.0
			if(!empty($db_version) and $db_version<3) {
				$sql = "ALTER TABLE ".WATUPRO_ANSWERS." CHANGE `point` `point` DECIMAL(6,2) DEFAULT '0.00'";
				$wpdb->query($sql);
			}
			
			// db updates 3.4
			if(!empty($db_version) and $db_version<3.41) {
				$sql = "ALTER TABLE ".WATUPRO_EXAMS." CHANGE `name` `name` VARCHAR(255) DEFAULT ''";
				$wpdb->query($sql);
			}
			
			// Intelligence specific fields
			if(watupro_intel()) {
				 require_once(WATUPRO_PATH."/i/models/i.php");
				 WatuPROIntelligence::activate();
			}
			
			// add indexes
			$index_taken_exams = $wpdb->get_var("SHOW INDEX FROM ".WATUPRO_TAKEN_EXAMS." WHERE KEY_NAME = 'user_id'");
			if(empty($index_taken_exams)) {
				$wpdb->query("ALTER TABLE ".WATUPRO_TAKEN_EXAMS." ADD INDEX user_id (user_id),
					ADD INDEX exam_id (exam_id), ADD INDEX points (points), ADD INDEX ip (ip),
					ADD INDEX grade_id (grade_id), ADD INDEX percent_correct (percent_correct),
					ADD INDEX date (date)");
			}
			$index_student_answers = $wpdb->get_var("SHOW INDEX FROM ".WATUPRO_STUDENT_ANSWERS." WHERE KEY_NAME = 'user_id'");
			if(empty($index_student_answers)) {
				$wpdb->query("ALTER TABLE ".WATUPRO_STUDENT_ANSWERS." ADD INDEX user_id (user_id),
					ADD INDEX exam_id (exam_id), ADD INDEX taking_id (taking_id), ADD INDEX question_id (question_id)");
			}
			$index_questions = $wpdb->get_var("SHOW INDEX FROM ".WATUPRO_QUESTIONS." WHERE KEY_NAME = 'exam_id'");
			if(empty($index_questions)) {
				$wpdb->query("ALTER TABLE ".WATUPRO_QUESTIONS." ADD INDEX exam_id (exam_id), ADD INDEX cat_id (cat_id), 
					ADD INDEX is_inactive (is_inactive), ADD INDEX is_survey (is_survey)");
			}
			
			// change pdf_output field
			if($db_version < 4) {
				$wpdb->query("ALTER TABLE ".WATUPRO_USER_CERTIFICATES." CHANGE pdf_output pdf_output LONGBLOB");
			}
			
			if($db_version < 4.121) {
				$wpdb->query("ALTER TABLE ".WATUPRO_TAKEN_EXAMS." CHANGE details details MEDIUMTEXT");
			}
			
			if($db_version < 4.13) {
				$wpdb->query("ALTER TABLE ".WATUPRO_GRADES." CHANGE gfrom gfrom DECIMAL(8,2) NOT NULL DEFAULT '0.00', 
					CHANGE gto gto DECIMAL(8,2) NOT NULL DEFAULT '0.00'");
			}
			
			// once update all old quizzes with "store_progress" => true
			if($db_version < 4.15) {
				$wpdb->query("UPDATE ".WATUPRO_EXAMS." SET store_progress=1");
			}
			
			// multiple personality grades
			// once update all old quizzes with "store_progress" => true
			if($db_version < 4.3) {
				$wpdb->query("ALTER TABLE ".WATUPRO_ANSWERS." CHANGE grade_id grade_id VARCHAR(255) NOT NULL DEFAULT '0'");
			}
			
			// change IP address field
			if($db_version < 4.83) {
			   $wpdb->query("ALTER TABLE ".WATUPRO_TAKEN_EXAMS." CHANGE `ip` `ip` VARCHAR(40) NOT NULL DEFAULT ''");
			}
			
			// change IP address field
			if($db_version < 5.22) {
			   $wpdb->query("ALTER TABLE ".WATUPRO_EXAMS." CHANGE `time_limit` `time_limit` DECIMAL(10,1) NOT NULL DEFAULT '0.0'");
			}
			
			// default CSV separator if not set
			if(get_option('watupro_csv_delim') == '') {
				update_option('watupro_csv_delim', ',');
				update_option('watupro_csv_quotes', '1');
			}
			
			// copy user group data to the new table if not done
			if(get_option('watupro_groups_copied') == '') {
				watupro_copy_user_groups();
				update_option('watupro_groups_copied', '1');
			}
			
			// set some defaults on $ui
			$ui = get_option('watupro_ui');
			if(empty($ui)) {
			   $ui = array('question_spacing' => 15, 'answer_spacing' => 5);	
			   update_option('watupro_ui', $ui);
			   
			   update_option('watupro_email_text_checkmarks', 1); // set text checkmarks in emails as selected by default
			}
      	
		 update_option('watupro_admin_notice', sprintf(__('<b>Thank you for activating WatuPRO!</b> Please check our <a href="%s" target="_blank">Quick getting started guide</a> and the <a href="%s">Help</a> page to get started!', 'watupro'), '//blog.calendarscripts.info/watupro-quick-getting-started-guide/', 'admin.php?page=watupro_help'));	        	
      	
      // set current DB version
      update_option("watupro_db_version", '5.59');
}

function watupro_admin_notice() {
	$notice = get_option('watupro_admin_notice');
	if(!empty($notice)) {
		echo "<div class='updated'><p>".stripslashes($notice)."</p></div>";
	}
	// once shown, cleanup
	update_option('watupro_admin_notice', '');
}
	

// assign the role - is this at all needed anymore? Check.
function watupro_register_role($user_id, $password="", $meta=array()) {
   $userdata = array();
   $userdata['ID'] = $user_id;
   $userdata['role'] = @$_POST['role'];

   // only allow if user role is my_role
   if (@$userdata['role'] == "student" and get_option('watupro_register_role') != 'default') {
      wp_update_user($userdata);
   }
   
   // also update redirection so we can go back to the exam after login
   if(!empty($_POST['watupro_redirect_to'])) {
   	update_user_meta($user_id, "watupro_redirect", $_POST['watupro_redirect_to']);
   }
}

// output role field
function watupro_role_field() {
    // thanks to http://www.jasarwebsolutions.com/2010/06/27/how-to-change-a-users-role-on-the-wordpress-registration-form/
    ?>
    <input id="role" type="hidden" tabindex="20" size="25" value="student"  name="role" />
    <input id="role" type="hidden" tabindex="20" size="25" name="watupro_redirect_to" value="<?php echo $_GET['watupro_redirect_to']?>" />
    <?php
}

// add settings link in the plugins page
function watupro_plugin_action_links($links, $file) {		
	if ( strstr($file, "watupro/" )) {
		$settings_link = '<a href="admin.php?page=watupro_options">' . __( 'Settings', 'watupro' ) . '</a>';
		array_unshift( $links, $settings_link );
	}
	return $links;
}

/**
 * Add jQuery Validation script on posts.
 */
function watupro_vc_scripts($design_theme = null) {    
	 global $wpdb;
    wp_enqueue_script('jquery');
    
    if(is_admin()) wp_enqueue_script('jquery-ui-sortable');
    
    $maincss_path = WATUPRO_URL.'style.css';	
	if(@file_exists(get_stylesheet_directory().'/watupro/style.css')) $maincss_path = get_stylesheet_directory_uri().'/watupro/style.css';
        
    wp_enqueue_style(
			'watupro-style',
			$maincss_path,
			array(),
			'6.3.1');
		
		$mainjs_path = WATUPRO_URL.'lib/main.js';	
		if(@file_exists(get_stylesheet_directory().'/watupro/lib/main.js')) $mainjs_path = 	get_stylesheet_directory_uri().'/watupro/lib/main.js';
		
		wp_enqueue_script(
			'watupro-script',
			$mainjs_path,
			array('jquery'),
			'5.4.9');
			
		// timer format
		$ui = get_option('watupro_ui'); 
		$timer_format = empty($ui['timer_format']) ? 'textual' : $ui['timer_format'];
			
		$translation_array = array('answering_required' => __('Answering this question is required', 'watupro'),
			'did_not_answer' => __('You did not answer the question. Are you sure you want to continue?', 'watupro'),
			'missed_required_question' => __('You have not answered a required question', 'watupro'),
			'missed_required_question_num' => __('You have not answered the required question number %s', 'watupro'),
			'please_wait' => __('Please wait...', 'watupro'),
			'try_again' => __('Try again', 'watupro'),
			'time_over' => __("Sorry, your time is over! I'm submitting your results... Done!", 'watupro'),
			'seconds' => ($timer_format == 'textual' ? ' '.__('seconds', 'watupro').' ' : ''),
			'minutes_and' => ($timer_format == 'textual' ? ' '.__('minutes and', 'watupro').' ' : ':'),
			'hours' => ($timer_format == 'textual' ? ' '.__('hours,', 'watupro').' ' : ':'),
			'time_left' => _wtpt(__('Time left:', 'watupro')),
			'email_required' => __('Please enter your email address', 'watupro'),
			'name_required' => __('Please enter your name', 'watupro'),
			'field_required' => __('This field is required', 'watupro'),
			'not_last_page' => sprintf(__('You are not on the last page. Are you sure you want to submit the %s?', 'watupro'), WATUPRO_QUIZ_WORD),
			'please_answer' => __('Please first answer the question', 'watupro'),
			'selections_saved' => __('Your work has been saved. You can come later to continue.', 'watupro'),
			'confirm_submit' => sprintf(__('Are you sure you want to submit the %s?', 'watupro'), WATUPRO_QUIZ_WORD),
			'taking_details' => sprintf(__('Details of submitted %s', 'watupro'), WATUPRO_QUIZ_WORD),
			'questions_pending_review' => __('The following questions have been flagged for review: %s. Are you sure you want to submit your results? Click OK to submit and Cancel to go back and review these questions.', 'watupro'),
			'ajax_url' => admin_url('admin-ajax.php'), 
			'complete_captcha' => __('You need to enter the image verification code', 'watupro'),	
			'complete_text_captcha' => __('You need to answer the verification question', 'watupro'),
			'size_errors' => __('Question(s) number %s have files uploaded that exceed the allowed size of %dKB', 'watupro'),
			'extension_errors' => __('Question(s) number %s have files uploaded that are not within the allowed file types', 'watupro'),
			'cant_continue_low_correct_percent' => __("You can't continue until you reach the desired % correct answers.", 'watupro'),
			'disable_copy' => (get_option('watupro_disable_copy') == 1 ? 1 : 0),
			'please_wait_feedback' => __('Please wait for the feedback...', 'watupro'));
		wp_localize_script( 'watupro-script', 'watupro_i18n', $translation_array );	
		
		if(watupro_intel()) {
			 wp_enqueue_style(
				'watupro-intelligence-css',
				WATUPRO_URL.'i/css/main.css',
				array(),
				'4.4.5');
				
			wp_enqueue_script(
				'watupro-intelligence',
				WATUPRO_URL.'i/js/main.js',
				array(),
				'4.5.1');
		} // endif intel
		
		wp_enqueue_script('jquery-ui-dialog');
		
		// design theme?

		if(empty($design_theme)) $design_theme = get_option('watupro_design_theme');
		if(empty($design_theme)) $design_theme = 'default';
		if(!empty($design_theme) and $design_theme != '-1') {			
			$theme_url = file_exists(WATUPRO_PATH.'/css/themes/'.$design_theme.'.css') ? WATUPRO_URL.'css/themes/'.$design_theme.'.css' : get_stylesheet_directory_uri().'/watupro/themes/'.$design_theme.'.css';

			// in case of custom theme stored in DB			
			if(preg_match('/^\[custom\]/', $design_theme)) {
				$theme_name = str_replace('[custom] ', '', $design_theme);			
				$theme_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".WATUPRO_THEMES." WHERE name=%s", stripslashes($theme_name)));
				if(!empty($theme_id)) $theme_url = site_url('?watupro_custom_theme='.$theme_id);
			}			
			
			if(!is_admin()) {
					wp_enqueue_style(
					'watupro-theme',
					$theme_url,
					array(),
					'4.6.1');
			}		
		}		
}

// always enqueue jquery
function watupro_vc_jquery() {
	 wp_enqueue_script('jquery');
}

// scripts that are used only in some quizzes
function watupro_conditional_scripts($exam) {
	global $wpdb;
	if(empty($exam)) return false;
	$advanced_settings = unserialize( stripslashes(@$exam->advanced_settings));
	
	$conditional_css_enqueued = false;
	if(!empty($advanced_settings['flag_for_review']) or !empty($advanced_settings['accept_rating']) or $exam->is_likert_survey) {
		wp_enqueue_style(
			'watupro-specific',
			WATUPRO_URL.'css/conditional.css',
			array(),
			'4.5.1');
			$conditional_css_enqueued = true;
	}
		
	// check if there are flashcard questions in this quiz
	$q_exam_id = (watupro_intel() and $exam->reuse_questions_from) ? $exam->reuse_questions_from : $exam->ID;
	if(empty($q_exam_id)) $q_exam_id = 0;
	$has_flashcards = $wpdb->get_var("SELECT ID FROM " . WATUPRO_QUESTIONS. " WHERE
			exam_id IN ($q_exam_id) AND is_flashcard=1 AND answer_type = 'checkbox'");
			
	// check if there are open-end questions with word limit
	$has_word_limits = $wpdb->get_var("SELECT ID FROM " . WATUPRO_QUESTIONS. " WHERE
			exam_id IN ($q_exam_id) AND limit_words > 0 AND answer_type = 'textarea'");			
			
	if($has_flashcards or $has_word_limits) {
		if(!$conditional_css_enqueued) {
			wp_enqueue_style(
			'watupro-specific',
			WATUPRO_URL.'css/conditional.css',
			array(),
			'4.5.1');
			$conditional_css_enqueued = true;
		}

		// enqueue scripts			
		wp_enqueue_script(
			'jquery.flip',
			'https://cdn.rawgit.com/nnattawat/flip/master/dist/jquery.flip.min.js',
			array('jquery'),
			'1.1.2');
			
		wp_enqueue_script(
			'watupro-flashcard',
			WATUPRO_URL.'lib/flashcard.js',
			array(),
			'4.5.1');		
	}
	
	
	if(!empty($advanced_settings['flag_for_review'])) {		
		wp_enqueue_script(
				'watupro-mark-review',
				WATUPRO_URL.'lib/mark-review.js',
				array(),
				'4.5');	
	}
	
	if(!empty($advanced_settings['accept_rating'])) {
		wp_enqueue_script(
				'watupro-star-rating',
				WATUPRO_URL.'lib/star-rating/jsimple-star-rating.min.js',
				array(),
				'4.5');
	}	
}

// admin menu
function watupro_add_menu_links() {
	global $wp_version, $_registered_pages, $user_ID;
	$page = 'tools.php';
	
	$student_caps = current_user_can(WATUPRO_MANAGE_CAPS) ? WATUPRO_MANAGE_CAPS:'read'; // used to be watupro_exams
	
	// multiuser settings - let's first default all to WATUPRO_MANAGE_CAPS in case of no Intelligence module	
	$exam_caps = $certificate_caps = $cat_caps = $ugroup_caps = $qcat_caps = $setting_caps = 
		$help_caps = $alltest_caps = WATUPRO_MANAGE_CAPS;
	$multiuser_exams_access = '';
	if(watupro_intel() and !current_user_can('manage_options')) {
		
		if( !WatuPROIMultiUser :: check_access('exams_access', true)) $exam_caps = 'manage_options';
		if( !WatuPROIMultiUser :: check_access('certificates_access', true)) $certificate_caps = 'manage_options';
		if( !WatuPROIMultiUser :: check_access('cats_access', true)) $cat_caps = 'manage_options';
		if( !WatuPROIMultiUser :: check_access('usergroups_access', true)) $ugroup_caps = 'manage_options';
		if( !WatuPROIMultiUser :: check_access('qcats_access', true)) $qcat_caps = 'manage_options';
		if( !WatuPROIMultiUser :: check_access('settings_access', true)) $setting_caps = 'manage_options';		
		if( !WatuPROIMultiUser :: check_access('help_access', true)) $help_caps = 'manage_options';
		if( !WatuPROIMultiUser :: check_access('alltest_access', true)) $alltest_caps = 'manage_options';		
		$multiuser_exams_access = WatuPROIMultiUser::check_access('exams_access', true);		
		$role_settings = unserialize(get_option('watupro_role_settings'));
		
		$user = new WP_User( $user_ID );		
		$roles = ( array ) $user->roles;
		
    	$role_settings = empty($role_settings[$roles[0]]) ? null : $role_settings[$roles[0]];
    	
	}
	
	// students part
	if(!get_option('watupro_nodisplay_myquizzes') and empty($role_settings['hide_myexams'])) {
		 add_menu_page(sprintf(__('My %s', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD_PLURAL)), sprintf(__('My %s', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD_PLURAL)), $student_caps, "my_watupro_exams", 'watupro_my_exams');
	}
	else add_submenu_page(null, sprintf(__('My %s', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD_PLURAL)), sprintf(__('My %s', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD_PLURAL)), $exam_caps, "my_watupro_exams", 'watupro_my_exams');
	if(!get_option('watupro_nodisplay_mycertificates')) add_submenu_page('my_watupro_exams', __("My Certificates", 'watupro'), __("My Certificates", 'watupro'), $student_caps, 'watupro_my_certificates', 'watupro_my_certificates');
	else add_submenu_page(null, __("My Certificates", 'watupro'), __("My Certificates", 'watupro'), $exam_caps, 'watupro_my_certificates', 'watupro_my_certificates');
	
	do_action('watupro_user_menu');
	
	if(!get_option('watupro_nodisplay_mysettings')) add_submenu_page('my_watupro_exams', sprintf(__("%s Settings", 'watupro'), ucfirst(WATUPRO_QUIZ_WORD)), sprintf(__("%s Settings", 'watupro'), ucfirst(WATUPRO_QUIZ_WORD)), $student_caps, 'watupro_my_options', 'watupro_my_options');
	
	// admin menus
	// "watupro_exams" menu is always accessible to WATUPRO_MANAGE_CAPS because it's the main menu item
    add_menu_page(__('Watu PRO', 'watupro'), __('Watu PRO', 'watupro'), WATUPRO_MANAGE_CAPS, "watupro_exams", 'watupro_exams');  
    add_submenu_page('watupro_exams', ucfirst(WATUPRO_QUIZ_WORD_PLURAL), ucfirst(WATUPRO_QUIZ_WORD_PLURAL), WATUPRO_MANAGE_CAPS, "watupro_exams", 'watupro_exams');
    add_submenu_page('watupro_exams',__('Help', 'watupro'), '<span style="color:red;">'.__('Help', 'watupro').'</span>', $help_caps, "watupro_help", "watupro_help");
	 add_submenu_page('watupro_exams', __("Watu PRO Certificates", 'watupro'), __("Certificates", 'watupro'), $certificate_caps, 'watupro_certificates', 'watupro_certificates');
	 add_submenu_page('watupro_exams',sprintf(__('%s Categories', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD)),sprintf(__('%s Categories', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD)), $cat_caps, "watupro_cats", "watupro_cats"); 
	 add_submenu_page('watupro_exams',__('User Groups', 'watupro'), __('User Groups', 'watupro'), $ugroup_caps, "watupro_groups", "watupro_groups"); 
	 add_submenu_page('watupro_exams',__('Question Categories', 'watupro'), __('Question Categories', 'watupro'), $qcat_caps, "watupro_question_cats", "watupro_question_cats");
	 if(empty($multiuser_exams_access) or ($multiuser_exams_access != 'group_view' and $multiuser_exams_access != 'view')) add_submenu_page( 'watupro_exams' ,__('Difficulty Levels', 'watupro'), __('Difficulty Levels', 'watupro'), $exam_caps, "watupro_diff_levels", array('WatuPRODiffLevels', 'manage')); 
	 
	 if($multiuser_exams_access != 'view' and $multiuser_exams_access != 'group_view' and $multiuser_exams_access != 'own' and $multiuser_exams_access != 'group') {
	    add_submenu_page( 'watupro_exams' ,__('Default Grades', 'watupro'), __('Default Grades', 'watupro'), $exam_caps, "watupro_default_grades", "watupro_default_grades");
	 }
 
	 add_submenu_page( 'watupro_exams' , sprintf(__('All %s Results', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD)), sprintf(__('All %s Results', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD)), $alltest_caps, "watupro_takings", "watupro_takings"); 
		 
	 // accessible only to superadmin
	 add_submenu_page('watupro_exams',__('Modules', 'watupro'), __('Modules', 'watupro'), 'manage_options', "watupro_modules", "watupro_modules"); 
	 add_submenu_page('watupro_exams',__('Settings', 'watupro'), __('Settings', 'watupro'), $setting_caps, "watupro_options", "watupro_options"); 
	 add_submenu_page('watupro_exams',__('Text Settings', 'watupro'), __('Text Settings', 'watupro'), $setting_caps, "watupro_text_options", "watupro_text_options");
	 add_submenu_page('watupro_exams', __("Social Sharing", 'watupro'), __('Social Sharing', 'watupro'), 
		'manage_options', 'watupro_social_share',  array('WatuPROSharing', 'options'));
	 add_submenu_page('watupro_exams', __("Webhooks / Zapier", 'watupro'), __('Webhooks / Zapier', 'watupro'), 
		'manage_options', 'watupro_webhooks',  array('WatuPROWebhooks', 'manage'));	
		
	if(class_exists('WP_Experience_API')) {
			add_submenu_page('watupro_exams', __("xAPI / Tin Can", 'watupro'), __("xAPI / Tin Can", 'watupro'), 'manage_options', 'watupro_xapi', array('WatuPROXAPI', "options"));        
		}		
	 
	 do_action('watupro_admin_menu');	 
	 
	 // always accessible to WATUPRO_MANAGE_CAPS
	 add_submenu_page('watupro_exams',__('Shortcode generator', 'watupro'), __('Shortcode generator', 'watupro'), $help_caps, "watupro_shortcode_generator", array('WatuPROShortcodeGen', 'generator'));
	 	 
 	 // not visible in menu 
 	 add_submenu_page(NULL,__('Add/Edit Exam', 'watupro'), __('Add/Edit Exam', 'watupro'), $exam_caps, "watupro_exam", "watupro_exam"); 
 	 add_submenu_page(NULL,__('Add/Edit Question', 'watupro'), __('Add/Edit Question', 'watupro'), $exam_caps, "watupro_question", "watupro_question");  // add/edit question
 	 add_submenu_page(NULL,__('Manage Questions', 'watupro'), __('Manage Questions', 'watupro'), $exam_caps, "watupro_questions", "watupro_questions");  // manage questions
 	 add_submenu_page(NULL,__('Taken Exam Data', 'watupro'), __('Taken Exam Data', 'watupro'), $exam_caps, "watupro_takings", "watupro_takings");  // view takings
 	 add_submenu_page(NULL,__('Manage Grades', 'watupro'), __('Manage Grades', 'watupro'), $exam_caps, "watupro_grades", "watupro_grades");  // manage grades
 	 add_submenu_page(NULL,__('Copy Exam', 'watupro'), __('Copy Exam', 'watupro'), $exam_caps, "watupro_copy_exam", "watupro_copy_exam");  // copy exam
 	 add_submenu_page(NULL,__('Users Who Earned Certificate', 'watupro'), __('Users Who Earned Certificate', 'watupro'), $certificate_caps, "watupro_user_certificates", "watupro_user_certificates");  // view/approve user certificates
 	  add_submenu_page(NULL,__('Manually Award Certificate', 'watupro'), __('Manually Award Certificate', 'watupro'), $certificate_caps, "watupro_award_certificate", "watupro_manually_award_certificate");  // manually award certificates
 	 add_submenu_page(NULL,__('Editing an answer to question', 'watupro'), __('Editing an answer to question', 'watupro'), $exam_caps, "watupro_edit_choice", "watupro_edit_choice"); 
 	 add_submenu_page(NULL,__('Advanced questions import', 'watupro'), __('Advanced questions import', 'watupro'), $exam_caps, "watupro_advanced_import", "watupro_advanced_import"); 
 	 add_submenu_page(NULL,__('User feedback on questions', 'watupro'), __('User feedback on questions', 'watupro'), $exam_caps, "watupro_questions_feedback", 'watupro_questions_feedback'); 
 	 add_submenu_page(NULL,__('Assign users to a group', 'watupro'), __('Assign users to a group', 'watupro'), $exam_caps, "watupro_group_assign", 'watupro_group_assign'); 
 	 add_submenu_page(NULL,__('Flashcard design settings', 'watupro'), __('Flashcard design settings', 'watupro'), $exam_caps, "watupro_flashcard_design", 
 	 	array('WatuPROFlashcard', 'design_settings'));
 	 add_submenu_page(NULL,__('Custom design themes', 'watupro'), __('Custom design themes', 'watupro'), $exam_caps, "watupro_design_themes", 'watupro_design_themes');	
 	 add_submenu_page(NULL,__('Schedules', 'watupro'), __('Schedules', 'watupro'), $exam_caps, "watupro_schedule", 'watupro_schedule');
}

// include advanced-import.php here because of a super odd problem on a couple of installations
function watupro_advanced_import() {
	include WATUPRO_PATH.'/controllers/advanced-import.php';
	WatuPROImport :: dispatch();
}

// function to conditionally add DB fields
function watupro_add_db_fields($fields, $table) {
		global $wpdb;
		
		// check fields
		$table_fields = $wpdb->get_results("SHOW COLUMNS FROM `$table`");
		$table_field_names = array();
		foreach($table_fields as $f) $table_field_names[] = $f->Field;		
		$fields_to_add=array();
		
		foreach($fields as $field) {
			 if(!in_array($field['name'], $table_field_names)) {
			 	  $fields_to_add[] = $field;
			 } 
		}
		
		// now if there are fields to add, run the query
		if(!empty($fields_to_add)) {
			 $sql = "ALTER TABLE `$table` ";
			 
			 foreach($fields_to_add as $cnt => $field) {
			 	 if($cnt > 0) $sql .= ", ";
			 	 $sql .= "ADD $field[name] $field[type]";
			 } 
			 
			 $wpdb->query($sql);
		}
	}

// manually apply Wordpress filters on the content
// to avoid calling apply_filters('the_content')	
function watupro_define_filters() {
	global $wp_embed, $watupro_keep_chars;
		
	// add_filter( 'watupro_content', 'watupro_autop' );	
	// add_filter( 'watupro_content', 'wptexturize' ); // Questionable use!
	// add_filter( 'watupro_content', 'convert_smilies' );
   add_filter( 'watupro_content', 'convert_chars' );
	add_filter( 'watupro_content', 'shortcode_unautop' );
	add_filter( 'watupro_content', 'do_shortcode' );
	
	// Compatibility with specific plugins
	// qTranslate
	if(function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) {
		add_filter('watupro_content', 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage');
		add_filter('watupro_qtranslate', 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage');
		add_filter( 'watupro_qtranslate', 'wptexturize' );
	}
	
	// WP Quick LaTeX
	if(function_exists('quicklatex_parser')) add_filter( 'watupro_content',  'quicklatex_parser', 7);
}	

function watupro_autop($content) {
	return wpautop($content, false);	
}

// send various admin notices
function watupro_admin_notices() {
	$notices = get_option('watupro_admin_notices');
	
	// make sure the plugin is not network-activated
	// this notice will always appear until the issue is resolved
	if ( function_exists( 'is_plugin_active_for_network' ) and is_plugin_active_for_network( 'watupro/watupro.php' ))	{		
		$notices .= "<br><b><font color='red'>".__('WatuPRO plugin is network activated and will not function properly.', 'watupro').'</font> '.__('Please go to your Network administration and deactivate it. Then activate the plugin as blog admin for each blog which needs it.', 'watupro')."</b>";
	}	
	
	if(!empty($notices)) {
		echo "<div class='updated'><p>".stripslashes($notices)."</p></div>";
	}
	// once shown, cleanup
	update_option('watupro_admin_notices', '');
}