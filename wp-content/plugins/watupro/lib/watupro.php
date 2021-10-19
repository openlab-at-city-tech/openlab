<?php
// store some of the logic here to encapsulate the things a little bit 
class WatuPRO {
	 static $output_sent = false;
	 
    function add_taking($exam_id, $in_progress=0) {
        global $user_ID, $wpdb;   
        // echo "IN PROGRESS: $in_progress<br>";
        // existing incomplete taking with this exam and user ID?
        if(!empty($user_ID)) {        		
        		$exists = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".WATUPRO_TAKEN_EXAMS."
        			WHERE user_id=%d AND exam_id=%d AND in_progress=1",$user_ID,$exam_id));
        		if(!empty($exists))  $taking_id=$exists;  
        } 
        
        $timer_log = empty($_POST['timer_log']) ? '' : esc_sql($_POST['timer_log']);
        
        if(empty($taking_id) and !empty($_COOKIE['watupro_taking_id_'.$exam_id]) and empty($user_ID)) $taking_id = intval($_COOKIE['watupro_taking_id_' . $exam_id]);	

     		// when completing the exam in_progress should become 0
     		if(!$in_progress and !empty($taking_id)) {
     			$wpdb->query("UPDATE ".WATUPRO_TAKEN_EXAMS." SET in_progress=0, timer_log='$timer_log' WHERE ID='$taking_id'");
     			
     			if(empty($_POST['no_ajax'])) setcookie('watupro_taking_id_'.$exam_id, '', time()-3600*48, '/');
     			else {
     				?>
     				<script type="text/javascript" >
     				var d = new Date();
					d.setTime(d.getTime() - (48*3600*1000));
					var expires = "expires="+ d.toUTCString();     				
     				document.cookie = "watupro_taking_id_<?php echo $exam_id?>=;" + expires + ";path=/";
     				</script>
     				<?php
     			}
     		}	
     		
     		// select exam and get advanced settings
     		$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $exam_id));
     		$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
     		
     		// current page			
     		if($exam->single_page == WATUPRO_PAGINATE_ONE_PER_PAGE) $current_page = intval($_POST['current_question']);
     		else $current_page = empty($_POST['current_catpage']) ? 1 : intval($_POST['current_catpage']);
     		
     		// if we are logging timer, make sure to select any existing log
			$log_timer = (empty($advanced_settings['log_timer']) or !$exam->time_limit) ? 0 : 1;
     		if($log_timer and !empty($taking_id)) {
     			$old_log = $wpdb->get_var($wpdb->prepare("SELECT timer_log FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $taking_id));
     			$timer_log = $old_log.' '.$timer_log;
     		}
        
        if(empty($taking_id)) {
					  // select exam
					  $exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $exam_id));					  
					  if(!empty($_POST['start_time'])) {
					  		$start_time = $_POST['start_time'];
					  		if(!strstr($start_time, '-')) $start_time = date("Y-m-d H:i:s", $start_time); // make sure it's in datetime format and not unix timestamp
					  }
					  else $start_time = current_time('mysql');
					  
					  // make sure we are allowed another attempt
					  $ok = $this->can_retake($exam);
					  if(!$ok) return false;
					  
				// avoid re-saving on page refresh (when no ajax)
				if($exam->no_ajax) {
					$taking_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".WATUPRO_TAKEN_EXAMS."
						WHERE ip=%s AND user_id=%d AND start_time=%s AND exam_id=%d",
						watupro_user_ip(), $user_ID, $start_time, $exam->ID));
					
					if(empty($_POST['no_ajax'])) setcookie('watupro_taking_id_' . $exam_id, $taking_id, time()+3600*12, '/');
		  			else {
		  				?>
		  				<script type="text/javascript" >
		  				var d = new Date();
						d.setTime(d.getTime() + (12*3600*1000));
						var expires = "expires="+ d.toUTCString();     				
		  				document.cookie = "watupro_taking_id_<?php echo $exam_id?>=<?php echo $taking_id;?>;" + expires + ";path=/";
		  				</script>
		  				<?php
		  			}
					if(!empty($taking_id)) return $taking_id;	
				}				
					
        		$wpdb->insert(WATUPRO_TAKEN_EXAMS, array(
	            "user_id"=>$user_ID,
	            "exam_id"=>$exam_id, 
	            "date"=>date('Y-m-d', current_time('timestamp')),
	            "start_time"=>$start_time,
	            "ip"=> watupro_user_ip(),
	            "in_progress"=>$in_progress,
	            "details" => "",
	            "result" => "",
	            "end_time" => "2000-01-01 00:00:00",
	            "grade_id" => 0,
	            "percent_correct" => 0,
	            "serialized_questions" => @$_POST['watupro_questions'],
	            "points" => 0,
	            'current_page' => $current_page,
	            'timer_log' => $timer_log
			   ),
		      array('%d','%d','%s','%s','%s','%s','%s','%s','%s','%d','%d', '%s', '%s', '%d', '%s'));
		        
		      // save the ID just in case
		      $taking_id = $wpdb->insert_id;
        }
        else { // taking ID exists
        	 // warning for timed quizzes. If we have started timer we have added taking_id but serialized questions is empty.
        	 // handle such case
        	 $taking = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $taking_id));
        	 if(empty($taking->serialized_questions) and !empty($_POST['watupro_questions'])) {
        	 	$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_TAKEN_EXAMS." 
        	 		SET serialized_questions=%s, timer_log=%s, current_page=%d WHERE ID=%d", $_POST['watupro_questions'], $timer_log, $current_page, $taking_id));
        	 }
        	 else {
        	 	// else update only the current page
        	 	$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_TAKEN_EXAMS." 
        	 		SET current_page=%d WHERE ID=%d", $current_page, $taking_id));
        	 }
        }
        
        update_user_meta( $user_ID, "current_watupro_taking_id", $taking_id);
       
        if(empty($_POST['no_ajax'])) setcookie('watupro_taking_id_' . $exam_id, $taking_id, time()+3600*12, '/');
  			else {
  				?>
  				<script type="text/javascript" >
  				var d = new Date();
				d.setTime(d.getTime() + (12*3600*1000));
				var expires = "expires="+ d.toUTCString();     				
  				document.cookie = "watupro_taking_id_<?php echo $exam_id?>=<?php echo $taking_id;?>;" + expires + ";path=/";
  				</script>
  				<?php
  			}
			//echo "SETTING COOKIE";
        
        return $taking_id;
    } // end add_taking

    // store results in the DB
    // @param $taking_id INT - the ID of the attempt
    // @param $points FLOAT - number of points achieved
    // @param grade TEXT - the grade title & description
    // @param $details TEXT - the final screen
    // @param $percent INT - percent correct answers
    // @param $grade_obj OBJECT - the grade object
    // @param $catgrades TEXT - the category grade output
    // @param $contact_data TEXT - the contact details in text format
    // @param $pointspercent INT - the % of max points
    // @param $catgrades_array ARRAY - the array of category grades to serialize
    // @param $data ARRAY - array of any other data so we can stop adding endless arguments to this function :)
    function update_taking($taking_id, $points, $grade, $details="", $percent = 0, $grade_obj = null, $catgrades = '', $contact_data = '', $pointspercent = 0, $catgrades_array = array(), $data = null) {
        // update existing taking   
         global $user_ID, $wpdb;     
			$advanced_settings = $this->advanced_settings;   
			$exam = $this->this_quiz;      
         
        	if(!empty($_POST['watupro_taker_email'])) $_POST['taker_email'] = $_POST['watupro_taker_email'];
			if(!empty($_POST['watupro_taker_name'])) $_POST['taker_name'] = $_POST['watupro_taker_name'];
         
         $num_hints_used = $wpdb->get_var($wpdb->prepare("SELECT SUM(num_hints_used) FROM ".WATUPRO_STUDENT_ANSWERS."
         	WHERE taking_id=%d", $taking_id));
         	
        // unset textual fields from catgrades array to save DB space
        if(!empty($catgrades_array) and empty($advanced_settings['store_full_catgrades'])) {
	        foreach($catgrades_array as $cnt => $catgrade) {
	        	  unset($catgrades_array[$cnt]['description']);
	        	  unset($catgrades_array[$cnt]['gdescription']);
	        	  unset($catgrades_array[$cnt]['html']);
			  } 	
		  }
		  
		  $source_url = empty($advanced_settings['save_source_url']) ? '' : $_SERVER['HTTP_REFERER'];
		  
		  $_POST['taker_email'] = sanitize_email(@$_POST['taker_email']);
		  $_POST['taker_name'] = sanitize_text_field(@$_POST['taker_name']);
		  $_POST['taker_company'] = sanitize_text_field(@$_POST['taker_company']);
		  $_POST['taker_phone'] = sanitize_text_field(@$_POST['taker_phone']);
		  $_POST['taker_field1'] = sanitize_text_field(@$_POST['taker_field1']);
		  $_POST['taker_field2'] = sanitize_text_field(@$_POST['taker_field2']);
		  $auto_submitted = empty($_POST['auto_submitted']) ? 0 : 1;
		  
		  $log_timer = empty($advanced_settings['log_timer']) ? 0 : 1;
		  $timer_log = '';
		  if($log_timer) $timer_log = esc_sql($_POST['timer_log']);
		  
		  // current page			
     		if($exam->single_page == WATUPRO_PAGINATE_ONE_PER_PAGE) $current_page = intval($_POST['current_question']);
     		else $current_page = empty($_POST['current_catpage']) ? 1 : intval($_POST['current_catpage']);
     		           
        $wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_TAKEN_EXAMS." SET 
            details=%s, points=%s, result=%s, end_time=%s, percent_correct=%d, grade_id=%d, email=%s, catgrades=%s, 
            num_hints_used=%d, name=%s, contact_data=%s, field_company=%s, field_phone=%s, percent_points=%d,
            catgrades_serialized=%s, source_url=%s, custom_field1=%s, custom_field2=%s, num_correct=%d, num_wrong=%d, 
            num_empty=%d, max_points=%f, auto_submitted=%d, timer_log=%s, current_page=%s WHERE ID=%d", 
			      wp_encode_emoji($details), $points, wpautop($grade, false), current_time('mysql'), $percent, @$grade_obj->ID, 
			      @$_POST['taker_email'], $catgrades, $num_hints_used, @$_POST['taker_name'], $contact_data, 
			      @$_POST['taker_company'], @$_POST['taker_phone'], $pointspercent, serialize($catgrades_array), $source_url, 
			      @$_POST['taker_field1'], @$_POST['taker_field2'], intval(@$data['num_correct']),
			      intval(@$data['num_wrong']), intval(@$data['num_empty']), floatval(@$data['max_points']), 
			      $auto_submitted, $timer_log, $current_page, $taking_id));
    }  // end update_taking
    
    // email exam details to where is selected
    // grade_id is passed to check if there is advanced setting that limits sending email to user
    function email_results($exam, $output, $grade_id = null, $certificate_id = null) {
    	   global $user_ID, $user_email, $wpdb, $wp_roles;
    	   $sender = watupro_admin_email();
			//$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers = 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= 'From: '. $sender . "\r\n";
			
			// $exam->user_name is set in controllers/submit_exam.php. In case this function is called elsehwere, avoid php notice:
			if(empty($exam->user_name)) $exam->user_name = '';
					
			$admin_output = $output = WatuPRO::cleanup($output);		
			if(strstr($output, "{{{split}}}")) {
				$parts = explode("{{{split}}}", $output);
				$output = trim($parts[0]);
				$admin_output = trim($parts[1]);
			}	
			
			$admin_subject = __('User results on %%QUIZ_NAME%%', 'watupro');
			$user_subject = __('Your results on %%QUIZ_NAME%%', 'watupro');	
			if(!empty($exam->email_subject)) {
				$email_subject = stripslashes($exam->email_subject);
				if(strstr($email_subject, '{{{split}}}')) {
					list($user_subject, $admin_subject) = explode('{{{split}}}', $email_subject);
				}
				else $user_subject = $admin_subject = $email_subject;
			}	
		
			$output='<html><head><title>'.__('Your results on ', 'watupro').$exam->name.'</title>
			</head>
			<html><body>'.$output.'</body></html>';
			// echo $output;
			
			// attach certificate?
			$attachments = array();			
			$generate_pdf_certificates = get_option('watupro_generate_pdf_certificates');
			$attach_certificates = get_option('watupro_attach_certificates');
			if(!empty($certificate_id) and $generate_pdf_certificates == "1" and $attach_certificates) {
				$attachments = array();				
				
				// from version 6.0.1 certificate_id is array
				foreach($certificate_id as $cert_id) {
					$_GET['certificate_as_attachment'] = true;
					$_GET['id'] = $cert_id;	
					$_GET['taking_id'] = $_POST['watupro_current_taking_id'];
					
					// do not attach it if the certificate requires approval
					$requires_approval = $wpdb->get_var($wpdb->prepare("SELECT require_approval FROM ".WATUPRO_CERTIFICATES." WHERE ID=%d", $cert_id));
					if(!empty($requires_approval)) continue;
					
					$settings = get_option('watupro_certificates_pdf');
					$cert_settings = @$settings[$cert_id];
					$file_name = "certificate-".$cert_id.'-'.$_GET['taking_id'].'.pdf';
					if(!empty($cert_settings['file_name'])) $file_name = $cert_settings['file_name'];
					
					// if file exists we have to change the file name
					if(@file_exists(WP_CONTENT_DIR . "/uploads/".$file_name)) {
						$file_name = preg_replace("/\.pdf/$", '', $file_name);
						$file_name = $file_name .'-'. substr(md5($_SERVER['REMOTE_ADDR']), 0, 6).'.pdf';
					}
					$_GET['download_file_name'] = $file_name;				
					watupro_view_certificate();
					
					$attachments[] = WP_CONTENT_DIR . "/uploads/".$file_name;
				}
			}
				
			if(!is_user_logged_in() or !empty($_POST['taker_email'])) $user_email = @$_POST['taker_email'];
			// user setting may override the var
			if($exam->email_taker and is_user_logged_in() and get_user_meta($user_ID, 'watupro_no_quiz_mails', true)) $exam->email_taker = false;
			
			$advanced_settings = unserialize( stripslashes($exam->advanced_settings));
			
			// now email user			
			if($exam->email_taker and $user_email) {
				// check for grade-related restrictions				
				if(!empty($advanced_settings['email_grades']) and is_array($advanced_settings['email_grades'])
					and !in_array($grade_id, $advanced_settings['email_grades'])) $dont_email_taker = true;			
					
				$user_subject = str_replace('%%QUIZ_NAME%%', stripslashes($exam->name), $user_subject);	
				$user_subject = str_replace('%%USER-NAME%%', $exam->user_name, $user_subject);
				$user_email = apply_filters('watupro-user-email', $user_email, $_POST['watupro_current_taking_id']);
				$output = apply_filters('watupro-email-results-taker', $output, $_POST['watupro_current_taking_id']);
				
				//echo "EMAIL: $output";
				
				if(empty($dont_email_taker)) {
					$result = wp_mail($user_email, $user_subject, $output, $headers, $attachments);
					
					// insert into the raw email log
			   	$status = $result ? 'OK' : "Error: ".$GLOBALS['phpmailer']->ErrorInfo;
			   	// save email log if the table is available
			   	if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_EMAILLOG."'")) == strtolower(WATUPRO_EMAILLOG)) {
			   		$wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_EMAILLOG." SET
			   			sender=%s, receiver=%s, subject=%s, date=CURDATE(), status=%s",
			   			$sender, $user_email, $user_subject, $status));
			   	}
				}				
			}
			
			if($exam->email_admin) {				
				// if user is logged in, let admin know who is taking the test
				$output = $admin_output;

				// check for grade-related restrictions				
				if(!empty($advanced_settings['admin_email_grades']) and is_array($advanced_settings['admin_email_grades'])
					and !in_array($grade_id, $advanced_settings['admin_email_grades'])) $dont_email_admin = true;						
				
				if(empty($dont_email_admin)) {					
					$user_data = $user_email;
					if(!empty($_POST['taker_name'])) $user_data .= " ($_POST[taker_name])";
					// maybe $_POST['taker_name'] is empty but we know the user ID
					if(empty($_POST['taker_name']) and !empty($user_ID)) {
						$userdata = get_userdata($user_ID);
						$user_data .= " (".$userdata->display_name.")"; 
					}			
					
					$ui = get_option('watupro_ui');
					
					if(!empty($user_email) and empty($ui['exclude_details_of_taker'])) $output = sprintf(__("Details of %s:", 'watupro'), $user_data) . "<br><br>".$output;			
					
					$admin_email = empty($exam->admin_email)?	get_option('admin_email') : $exam->admin_email;
					
					// let's allow a filter. Maybe other plugin will need to change the receiver
					$admin_email = apply_filters('watupro-admin-email', $admin_email, $_POST['watupro_current_taking_id']);
					
					$admin_subject = str_replace('%%QUIZ_NAME%%', stripslashes($exam->name), $admin_subject); 
					$admin_subject = str_replace('%%USER-NAME%%', $exam->user_name, $admin_subject);	
					
					// admin email can contain variables from the "Ask for contact details" section
					if(strstr($admin_email, '%%EMAIL%%')) $admin_email = str_replace('%%EMAIL%%', sanitize_email($_POST['taker_email']), $admin_email);
					if(strstr($admin_email, '%%USER-NAME%%')) $admin_email = str_replace('%%USER-NAME%%', sanitize_email($_POST['taker_name']), $admin_email);
					if(strstr($admin_email, '%%FIELD-COMPANY%%')) $admin_email = str_replace('%%FIELD-COMPANY%%', sanitize_email($_POST['taker_company']), $admin_email);
					if(strstr($admin_email, '%%FIELD-PHONE%%')) $admin_email = str_replace('%%FIELD-PHONE%%', sanitize_email($_POST['taker_phone']), $admin_email);
					if(strstr($admin_email, '%%FIELD-1%%')) $admin_email = str_replace('%%FIELD-1%%', sanitize_email($_POST['taker_field1']), $admin_email);
					if(strstr($admin_email, '%%FIELD-2%%')) $admin_email = str_replace('%%FIELD-2%%', sanitize_email($_POST['taker_field2']), $admin_email);
					
					// admin email can also be group manager's email addresses
					if(strstr($admin_email, '%%GROUP-MANAGERS%%') and is_user_logged_in()) {
						// get user groups
						$ugroups = get_user_meta($user_ID, 'watupro_groups', true);
						
						// get all manager roles
						$roles = $wp_roles->roles;
						$enabled_roles = array();
						foreach($roles as $key => $role) {
							$r=get_role($key);
							if(!empty($r->capabilities['watupro_manage_exams'])) $enabled_roles[] = $key;
						}
						
						// get all users with these roles
						$managers = get_users(array('role__in' => $enabled_roles));
						
						// get any managers to this group and collect their emails in comma separated string
						$manager_emails = array();
						foreach($ugroups as $ugroup) {
							foreach($managers as $manager) {
								// if manager has role add their email
								$manager_groups = get_user_meta($manager->ID, 'watupro_groups', true);
								if(in_array($ugroup, $manager_groups) and !in_array($manager->user_email, $manager_emails)) {
									$manager_emails[] = $manager->user_email;
									continue;
								}
							} // end foreach manager
						} // end foreach user group
						
						// replace in $admin_email var
						$admin_email = str_replace('%%GROUP-MANAGERS%%', implode(', ', $manager_emails), $admin_email);
					} 
					// end replacing %%GROUP-MANAGER%%
					
					// admin email can also contain user meta field data or ID of another user
					if(strstr($admin_email, '{{{usermeta-') and is_user_logged_in()) {
						$matches =  array();
						preg_match_all("/{{{([^}}}])*}}}/", $admin_email, $matches);
						
						foreach($matches[0] as $cnt => $match) {
							if(!strstr($match, '{{{usermeta-')) continue;
							
							// cleanup braces and extract meta field name
							$meta_key = str_replace(array('{{{usermeta-', '}}}'), '', $match);
							$meta_value = get_user_meta($user_ID, $meta_key, true);
							
							// is this email address or numeric ID? If numeric, it's the ID of other user and we have to get their email address
							if(is_numeric($meta_value)) {
								$meta_user = get_user_by('ID', $meta_value);
								//print_r($meta_user);
								if(!empty($meta_user->ID)) $meta_value = $meta_user->user_email;
							}
							
							// if meta_value is email, replace in admin_email
							if(strstr($meta_value, '@') and strstr($meta_value, '.')) {
								$admin_email = str_replace($match, $meta_value, $admin_email);
							}
							
						} //  end foreach match
					}
					// end replacing {{{usermeta-fields}}}
					
					$output = apply_filters('watupro-email-results-admin', $output, $_POST['watupro_current_taking_id']);
					
					// set respondend in reply-to?
					if(!empty($advanced_settings['taker_reply_to']) and !empty($user_email)) $headers .= 'Reply-to: '. $user_email . "\r\n";
					
					// echo $admin_email;
					//echo $output;
					$result = wp_mail($admin_email, $admin_subject, $output, $headers, $attachments);
					// insert into the raw email log
			   	$status = $result ? 'OK' : "Error: ".$GLOBALS['phpmailer']->ErrorInfo;
			   	
			   	// save email log if the table is available
			   	if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_EMAILLOG."'")) == strtolower(WATUPRO_EMAILLOG)) {
					   $wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_EMAILLOG." SET
				   		sender=%s, receiver=%s, subject=%s, date=CURDATE(), status=%s",
				   		$sender, $admin_email, $admin_subject, $status));
				   	}
				}	// end email admin	 		
			}
			
			// delete the certificate file
			if(!empty($certificate_id) and $generate_pdf_certificates == "1" and $attach_certificates) {
				@unlink( WP_CONTENT_DIR . "/uploads/".$file_name );
			}
	}
	
	// see if user still can take the exam depending on number of takings allowed
	// returns true if they can take and false if they can't 
	function can_retake($exam) {
		global $wpdb, $user_ID;
		$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
		$descr = '';
		
		// if the test is paid AND allows restarting limits, prepare the payment messages
   	$payment_restart_msg = '';
   	if(is_user_logged_in() and watupro_intel() and $exam->fee > 0 and !empty($advanced_settings['payment_restarts_attempts'])) {
   		ob_start();
   		WatuPROPayment::render($exam, false, true);	
   		$render_payment = ob_get_clean();   		
   		$payment_restart_msg = '<p>'.__('You can re-start your allowed attempts by repurchasing access.', 'watupro').'</p>'.$render_payment;
   	}
      
		// filter for 3rd party code
		$exam->times_to_take = apply_filters('watupro_times_to_take', $exam->times_to_take, $exam->ID);
		$exam = apply_filters('watupro_can_take', $exam);
		if($exam === TRUE || $exam === FALSE) return $exam;
		// end 3rd party integrations
		
		if(!empty($advanced_settings['always_show_description'])) {
			$descr = preg_replace('#({{{button).*?(}}})#', '', $exam->description);		
			$descr = apply_filters('watupro_content', wpautop(stripslashes($descr)));
		}
		
		// no login required but have a restriction by IP
		if(!empty($exam->takings_by_ip)) {
			// select number of takings by this IP address
			$num_taken = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM ".WATUPRO_TAKEN_EXAMS."
				WHERE exam_id=%d AND ip=%s AND in_progress=0", $exam->ID, watupro_user_ip()));
				
			if($exam->takings_by_ip <= $num_taken) {
				echo $descr;
				echo "<p><b><!--IP address-->";
				printf(__("Sorry, you can take this %s only %d times.", 'watupro'), WATUPRO_QUIZ_WORD, $exam->takings_by_ip);
				echo "</b></p>";
				echo $payment_restart_msg;
				echo WatuPROTaking :: display_latest_result($exam);
				return false;
			}	
		} // end IP based check		
		
		// no login required but have a restriction by contact email address
		if(!empty($_POST['watupro_taker_email'])) $_POST['taker_email'] = $_POST['watupro_taker_email'];
		if(!empty($advanced_settings['takings_by_email']) and !empty($_POST['taker_email'])) {
			// select number of takings by this email address
			$num_taken = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM ".WATUPRO_TAKEN_EXAMS."
				WHERE exam_id=%d AND email=%s AND in_progress=0 AND ignore_attempt=0", $exam->ID, $_POST['taker_email']));
				
			if(!empty($advanced_settings['contact_fields']['email']) and $advanced_settings['takings_by_email'] > 0 and $advanced_settings['takings_by_email'] <= $num_taken) {
				echo $descr;
				echo "<p><b><!-- email address-->";
				printf(__("Sorry, you can take this %s only %d times.", 'watupro'), WATUPRO_QUIZ_WORD, $advanced_settings['takings_by_email']);
				echo "</b></p>";
				echo $payment_restart_msg;
				echo WatuPROTaking :: display_latest_result($exam);
				return false;
			}		
		}
		
		// no limits if login is not required
		if(!$exam->require_login) return true;		
		
		if($exam->take_again) {			
			// Intelligence limitations
			if(watupro_intel()) {
				if(!WatuPROIExam::can_retake($exam)) {
					echo $descr;
					echo $payment_restart_msg;
					return false;
				}
			}		
		
			if(empty($exam->times_to_take) 
				and (empty($exam->retake_grades) or strlen($exam->retake_grades) <=2) 
				and empty($advanced_settings['retake_days_limit']) ) {
				return true; // 0 = unlimited
			}
			
         // now select number of takings			
			if(!is_user_logged_in()) {
				echo $descr;
				printf(__("Sorry, you are not allowed to submit this %s.", 'watupro'), WATUPRO_QUIZ_WORD);				
				return false;
			}
			
			// is there a limit for how many days after the first attempt they can retake the quiz?
			if(!empty($advanced_settings['retake_days_limit'])) {
				// select first attempt if any
				$first_date = $wpdb->get_var($wpdb->prepare("SELECT date FROM " . WATUPRO_TAKEN_EXAMS." 
					WHERE user_id=%d AND exam_id=%d AND in_progress=0 AND ignore_attempt=0 ORDER BY ID LIMIT 1", $user_ID, $exam->ID));
				if(!empty($first_date)) {
					// calculate num dayes to today
					$num_days = round( (current_time('timestamp') - strtotime($first_date)) / (24*3600));
					if($num_days > $advanced_settings['retake_days_limit']) {
						echo $descr;
						echo "<p><b>";
						printf(__("Sorry, you can can no longer access this %s.", 'watupro'), WATUPRO_QUIZ_WORD);				
						echo "</b></p>";
						return false;
					}
				}	 
			} // end checking for days after first attempt limitation
			
			// is the num-takings limit in total or per day, week, month?
			$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
			$timed_sql = $timed_msg = '';
			if(!empty($advanced_settings['retakings_per_period'])) {
				$timed_sql = " AND date >= '" . date("Y-m-d", current_time('timestamp'))."' - INTERVAL ".$advanced_settings['retakings_per_period'];
				switch($advanced_settings['retakings_per_period']) {
					case '24 hour': $timed_time = __('24 hours', 'watupro'); break;
					case '1 week': $timed_time = __('a week', 'watupro'); break;
					case '1 month': $timed_time = __('a month', 'watupro'); break;
					case '1 year': $timed_time = __('an year', 'watupro'); break;
				}
				$timed_msg = ' ' . sprintf(__('within interval of %s', 'watupro'), $timed_time);
			}
			
			$cnt_takings=$wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM ".WATUPRO_TAKEN_EXAMS."
				WHERE exam_id=%d AND user_id=%d AND in_progress=0 AND ignore_attempt=0 $timed_sql", $exam->ID, $user_ID));
			if(!$cnt_takings) return true; // if there are no takings no need to check further			
				
			if(!empty($exam->times_to_take) and $cnt_takings >= $exam->times_to_take) {
				echo $descr;
				echo "<p><b>";
				printf(__('Sorry, you can take this %1$s only %2$s times%3$s.', 'watupro'), WATUPRO_QUIZ_WORD, $exam->times_to_take, $timed_msg);				
				echo "</b></p>";
				echo $payment_restart_msg;
				echo WatuPROTaking :: display_latest_result($exam);
				return false;
			}		
			
			// all OK so far? Let's see if we have grade-based limitation and there are previous takings
			if(!empty($exam->retake_grades) and strlen($exam->retake_grades) > 2 and $cnt_takings) {
				$grids = explode("|", $exam->retake_grades);
				$grids = array_filter($grids);
				
				if(count($grids)) {
					// get latest taking
					$latest_taking = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS." 
						WHERE exam_id=%d AND user_id=%d AND in_progress=0 AND ignore_attempt=0 ORDER BY ID DESC LIMIT 1", $exam->ID, $user_ID));

					if($latest_taking->grade_id == 0 ) $latest_taking->grade_id = -1; // because in the setting "None" is stored as -1
					
				   // maybe the grades limit expires after some time?
				   $ignore_retake_grades_limit = false;
				   if(!empty($advanced_settings['retake_grades_expire']) and $advanced_settings['retake_grades_expire'] > 0) {				   	
				   	if(strtotime($latest_taking->end_time) < (current_time('timestamp') - $advanced_settings['retake_grades_expire']*24*3600) ) $ignore_retake_grades_limit = true;
				   }		
						
					if(!in_array($latest_taking->grade_id, $grids) and !$ignore_retake_grades_limit) {
						echo $descr;
						echo "<p><b>";
						_e("You can't take this quiz again because of the latest grade you achieved on it.", 'watupro');						
						echo "</b></p>";
						echo $payment_restart_msg;
						echo WatuPROTaking :: display_latest_result($exam);
						return false;
					}	
				}	
			}	// end grade-related limitation check		
					
		} // end if $exam->take_again
		else {
			// Only 1 taking allowed: see if exam is already taken by this user
			$taking = $this->get_taking($exam);
						
			if(!empty($taking->ID) and !$taking->in_progress) {
				echo $descr;
				echo "<p><b>";
				printf(__("Sorry, you can take this %s only once!", 'watupro'), WATUPRO_QUIZ_WORD);
				echo "</b></p>";
				echo $payment_restart_msg;
				echo WatuPROTaking :: display_latest_result($exam);
				return false;
			}
		}		
		
		// just in case
		return true;
	}
	
	// get existing taking for given exam (only for logged in users)
	function get_taking($exam)	{
		global $wpdb, $user_ID;		
		if(!is_user_logged_in()) return false;
		
		$taking = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}watupro_taken_exams
			WHERE exam_id=%d AND user_id=%d AND ignore_attempt=0 ORDER BY ID DESC LIMIT 1", $exam->ID, $user_ID));
			
		return $taking;	
	}
	
	// verifies if time limit is fine and there is no cheating
	// allow 15 seconds for submitting in case of server overload
	// TO CHANGE, NYI: If there is no $in_progress but there is user meta start_time compare to it instead of POST
	// If there is no in_progress and no user meta use start_time from session (see initialize_timer function)
	// Only if none of these, compare to $_POST['start_time']. ONLY in this case if for some reason $_POST['start_time'] is invalid
	// (time is before year 2000) then just return true;
	function verify_time_limit($exam, $in_progress = null) {
		global $user_ID, $wpdb;

		if(!$exam->full_time_limit) return true;
		
		// Logged in. In progress available.
		if(is_user_logged_in() and $in_progress) {			
			// compare with saved data
			$start = watupro_mktime($in_progress->start_time);			
						
			$_POST['timer_log'] .= 'User is logged in. Attempt "in progress" has been stored. Start time was '.date('Y-m-d H:i:s', $start).' and current time is '.current_time('mysql').'. ';			
		} // end when logged in and in_progress is available
		
		// Logged in. In progress not available.
		if(is_user_logged_in() and !$in_progress) {
			$user_meta_start = get_user_meta($user_ID, "start_exam_".$exam->ID, true);
			if(empty($user_meta_start)) {
				if(!empty($_COOKIE['start_time'.$exam->ID])) {
					$_POST['timer_log'] .= "Getting time from session.";
					$start = $_COOKIE['start_time'.$exam->ID];
				}
				else {
					$_POST['timer_log'] .= "Getting time from POST.";
					$start = $_POST['start_time'];
				}
			} 
			else {
				$start = $user_meta_start;
				$_POST['timer_log'] .= "Getting time from user meta.";
			}
			
			$_POST['timer_log'] .= 'User is logged in. Attempt "in progress" has NOT been stored. Start time was '.date('Y-m-d H:i:s', $start).' and current time is '.current_time('mysql').'. ';
		} // end when logged in and in_progress is NOT available
		
		// Not logged in. In progress available.
		if(!is_user_logged_in() and $in_progress) {			
			// compare with saved data
			$start = watupro_mktime($in_progress->start_time);			
			//echo ($start+$exam->full_time_limit*60+10)."<br>".current_time();
			//exit;
			
			$_POST['timer_log'] .= 'User is NOT logged in. Attempt "in progress" has been stored. Start time was '.date('Y-m-d H:i:s', $start).' and current time is '.current_time('mysql').'. ';			
		} // end when NOT logged in and in_progress is available
		
		// Not logged in. In progress not available.
		if(!is_user_logged_in() and !$in_progress) {			
			// compare with saved data
			if(!empty($_COOKIE['start_time'.$exam->ID])) {
					$_POST['timer_log'] .= "Getting time from session.";
					$start = $_COOKIE['start_time'.$exam->ID];
			}
			else {
					$_POST['timer_log'] .= "Getting time from POST.";
					$start = $_POST['start_time'];
			}
						
			$_POST['timer_log'] .= 'User is NOT logged in. Attempt "in progress" has NOT been stored. Start time was '.date('Y-m-d H:i:s', $start).' and current time is '.current_time('mysql').'. ';			
		} // end when NOT logged in and in_progress is NOT available
		
		// protection against using same user account on more than one computer. The start time of the quiz for this user cannot be before the end time of another attempt.
		/*if(is_user_logged_in() and !empty($start)) {
			$another_attempt = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ". WATUPRO_TAKEN_EXAMS . " 
				WHERE user_id=%d AND exam_id=%d AND in_progress=0 AND end_time > '".date('Y-m-d H:i:s', $start)."'", $user_ID, $exam->ID));
			echo $wpdb->prepare("SELECT ID FROM ". WATUPRO_TAKEN_EXAMS . " 
				WHERE user_id=%d AND exam_id=%d AND in_progress=0 AND end_time > '".date('Y-m-d H:i:s', $start)."'", $user_ID, $exam->ID);	
			if(!empty($another_attempt)) {
				$_POST['timer_log'] .= 'Timer abuse detected.';
				return false;
			}	
		}*/
		
		$timer_allowance = get_option('watupro_timer_allowance');
		if(empty($timer_allowance) or $timer_allowance < 1 or !is_numeric($timer_allowance)) $timer_allowance = 10; 
		if($start and ($start + $exam->full_time_limit*60 + $timer_allowance) < current_time('timestamp')) {
				$_POST['timer_log'] .= 'Check failed, timer verification returns false. ';
				return false;
		}
		
		$_POST['timer_log'] .= 'Timer check successful. ';
		return true;
	}
	
	// small helper to convert answer ID's into texts
	function answer_text($answers, $ansArr) {
		
		$answer_text="";
		foreach($answers as $answer) {
			if(in_array($answer->ID, $ansArr)) {
				if(!empty($answer_text)) $answer_text.=", ";
				$answer_text.=$answer->answer;
			}
		}
		
		return $answer_text;
	}
	
    // INSERT specific details in watupro_student_answers 
    // done either in completing exam or while clicking next/prev
    // $points and question_text are not required for in_progress takings. As there we only need to store
    // what answer is given so student can continue
    // $answer is answer text when we are completing the exam. But it's stored as (ID, text, or array)
    // if we are storing in progress data - because it's easier to save&retrieve this way
    function store_details($exam_id, $taking_id, $question_id, $answer, $points=0, $question_text="", $is_correct=0, $snapshot = '', $ques_max_points = 0) {
        global $wpdb, $user_ID;
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        
        if(empty($points)) $points = "0.00";
        
        // remove hardcoded correct/incorrect images if any
	    	// (for example we may have these in fill the gaps questions)
	    	$answer = str_replace('<img src="'.WATUPRO_URL.'correct.png" hspace="5">', '', $answer);
	    	$answer = str_replace('<img src="'.WATUPRO_URL.'wrong.png" hspace="5">', '', $answer);	    	
	    	$answer = wp_encode_emoji($answer);
                
        // if detail exists update
        $detail = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_STUDENT_ANSWERS."
         WHERE taking_id=%d AND exam_id=%d AND question_id=%d", $taking_id, $exam_id, $question_id));
         
        // question hints if any
        $hints = empty($_POST['question_'.$question_id.'_hints']) ? '' : watupro_strip_tags($_POST['question_'.$question_id.'_hints']);						
        $no_hints = count( explode("watupro-hint", $hints) ) - 1; 
        $question_text = ''; // unset this, we'll no longer store it for performance reasons
        
        $question = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_QUESTIONS." WHERE ID=%d", $question_id));
        
        // evaluate $is_correct?
        if(!empty($this->evaluate_on_the_fly)) {        		
        		// the answer here should not be serialized so we get it from $_POST directly
        	   list($points, $is_correct) = WTPQuestion::calc_answer($question, $_POST['answer-'.$question_id]);		
        }
        
        // freetext answer on one of the choices?
        $freetext_answer = '';
        if($question->answer_type == 'checkbox' or $question->answer_type == 'radio') {
        	   $choice_ids = $wpdb->get_results($wpdb->prepare("SELECT ID FROM ".WATUPRO_ANSWERS." WHERE question_id=%d ORDER BY ID", $question_id));
        	   foreach($choice_ids as $choice_id) {
        	   	if(!empty($_POST['freetext_'.$choice_id->ID])) {
        	   		$freetext_answer = sanitize_text_field($_POST['freetext_'.$choice_id->ID]);
        	   		break; // only one answer can contain it so no need to go further.
        	   	}
        	   }
        	   
        	   $freetext_answer = wp_encode_emoji($freetext_answer);
        } // end filling freetext answer
        
        $snapshot = wp_encode_emoji($snapshot);
        
        // chk_answer for checkbox questions
        $chk_answers = '';
        if($question->answer_type == 'checkbox' and isset($_POST['answer-'.$question_id]) and is_array($_POST['answer-'.$question_id])) {
        	  // we need textual answers however because if the admin changes an option we don't want that counted improperly
        	  $choices = $wpdb->get_results($wpdb->prepare("SELECT ID, answer FROM ".WATUPRO_ANSWERS." WHERE question_id=%d ORDER BY ID", $question_id));        	  
        	  foreach($choices as $choice) {
        	  	  foreach($_POST['answer-'.$question_id] as $selected_choice) {
        	  	  	   if($selected_choice == $choice->ID) $chk_answers .= '|'.stripslashes($choice->answer);
        	  	  }
        	  }     	  
        	  $chk_answers .= '|';
        }
        
        // percent points
        $percent_points = $ques_max_points <= 0 ? 0 : round((100 * $points) / $ques_max_points); 
                 
        if(empty($detail->ID)) {
    		   $wpdb->insert(WATUPRO_STUDENT_ANSWERS,
    			array("user_id"=>$user_id, "exam_id"=>$exam_id, "taking_id"=>$taking_id,
    				"question_id"=>$question_id, "answer"=>$answer,
    				"points"=>$points, "question_text"=>$question_text, 
    				"is_correct" => $is_correct, 'snapshot'=>$snapshot, 'hints_used'=>$hints, 
    				"num_hints_used" => $no_hints, "onpage_question_num" => intval(@$_POST['current_question']),
    				"feedback" => wp_encode_emoji(@$_POST['feedback-'.$question_id]),
    				"rating" => intval(@$_POST['question_rating_'.$question_id]),
    				"freetext_answer" => $freetext_answer,
    				"percent_points" => $percent_points,
    				"chk_answers" => $chk_answers),
    			array("%d","%d","%d","%d","%s","%f","%s", "%d", "%s", "%s", "%d", "%d", "%s", "%d","%s", "%d", "%s"));    
    			$detail_id = $wpdb->insert_id;			
        }
        else {
				// don't remove the snapshot
				if(empty($snapshot) and !empty($detail->snapshot)) $snapshot = stripslashes($detail->snapshot);        	
        	
            $wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_STUDENT_ANSWERS." SET
               answer=%s, points=%s, question_text=%s, is_correct=%d, snapshot=%s, hints_used = %s, 
               num_hints_used=%d, onpage_question_num=%d, feedback=%s, rating=%d, freetext_answer=%s, 
               chk_answers=%s, percent_points=%d
               WHERE id=%d", $answer, $points, $question_text, $is_correct, $snapshot, $hints, $no_hints,
               intval(@$_POST['current_question']), wp_encode_emoji(@$_POST['feedback-'.$question_id]), 
               intval(@$_POST['question_rating_'.$question_id]), $freetext_answer, $chk_answers, $percent_points, $detail->ID ));
            $detail_id = $detail->ID;                 
        } 
        
        // uploaded file?
        WatuPROFileHandler :: upload_file($question_id, $detail_id, $taking_id);
    }
    
    // regroup questions by category or pull random per category
    function group_by_cat($questions, $exam) {
    		$advanced_settings = unserialize( stripslashes($exam->advanced_settings) );
    		   		
			// pull random by category?    	
			if($exam->pull_random and $exam->random_per_category) {				
				$cat_ids = array();
				$cats = array();
				
				// questions may (and probably are) shuffled. Make sure important are on top
				$important_questions = $notimportant_questions = array();
				foreach($questions as $question) {
					if($question->importance > 0) $important_questions[] = $question;
					else $notimportant_questions[] = $question;
				}
				$questions = array_merge($important_questions, $notimportant_questions);
				
				foreach($questions as $cnt=>$question) {
					 if(!in_array($question->cat_id, $cat_ids)) {
					 		$cat_ids[] = $question->cat_id;
							$cats[$question->cat_id] = 0;
					 }
								 
					 // enough questions in the category? then skip this one	
					 $pull_random = isset($advanced_settings['random_per_'.$question->cat_id]) ? 
					 	intval($advanced_settings['random_per_'.$question->cat_id]) : $exam->pull_random;		
					 	
					 // make sure that $pull random is not unintentionally 0
					 if($pull_random == 0) $pull_random = $exam->pull_random;
					 if($pull_random == -1) $pull_random = 0;	
					 					 				 
					 if($cats[$question->cat_id] >= $pull_random) {
					 		unset($questions[$cnt]);
					 		continue;
					 }
					 
					 $cats[$question->cat_id]++;
				}
			}
			
			// when questions are pulled per category and randomized but NOT grouped, we have to shuffle them
			// this shuffle is required because we had to put important on top
			if( ($exam->randomize_questions==1 or $exam->randomize_questions==2) 
				and $exam->pull_random and $exam->random_per_category and !$exam->group_by_cat) shuffle($questions);
    	
    	  // now group by category if selected
    	  if(!$exam->group_by_cat) return $questions;

			// now regroup
			$cats=array();
			foreach($questions as $question) {
				if(empty($question->cat)) $question->cat = __('Uncategorized', 'watupro');
				if(!in_array($question->cat, $cats)) $cats[]=$question->cat;
			}    
			
			$cats = WTPCategory :: sort_cats($cats, $advanced_settings, $exam);	
			
			$regrouped_questions=array();
			
			foreach($cats as $cat) {
				foreach($questions as $question) {
					if($question->cat==$cat) $regrouped_questions[]=$question;
				}
			}			
    	
    	  return $regrouped_questions;
    }
    
    // calculate generic rating
    function calculate_rating($total, $score, $percent) {
    	$all_rating = array(__('Failed', 'watupro'), __('Failed', 'watupro'), __('Failed', 'watupro'), __('Failed', 'watupro'), __('Just Passed', 'watupro'),
    	__('Satisfactory', 'watupro'), __('Competent', 'watupro'), __('Good', 'watupro'), __('Very Good', 'watupro'),__('Excellent', 'watupro'), __('Unbeatable', 'watupro'), __('Cheater', 'watupro'));
    	$rate = intval($percent / 10);
    	if($percent == 100) $rate = 9;
    	if($score == $total) $rate = 10;
    	if($percent>100) $rate = 11;
    	$rating = @$all_rating[$rate];
    	return $rating;
    }
    
    // match answers to questions and if required show only some of the answers
    function match_answers(&$all_question, $exam) {
    		global $wpdb, $ob;
    		
    		$ob = "sort_order,ID";
    		// if answers are limited, correct is selected first, then we'll shuffle the answers    		
    		if($exam->num_answers) $ob = "correct DESC, RAND()";
    		if(!$exam->num_answers and ($exam->randomize_questions==1 or $exam->randomize_questions==3)) $ob = "RAND()";
    		
    	   $qids=array(0);
			foreach($all_question as $question) $qids[]=$question->ID;
			$qids=implode(",",$qids);
			
			// answers array accordingly to randomization settings
			$all_answers = $wpdb->get_results("SELECT *	FROM ".WATUPRO_ANSWERS."
			WHERE question_id IN ($qids) ORDER BY $ob");
			
			// because of survey and true/false, always select ordered by ID
			$all_answers_by_order = $wpdb->get_results("SELECT *	FROM ".WATUPRO_ANSWERS."
			WHERE question_id IN ($qids) ORDER BY sort_order, ID");
			
			foreach($all_question as $cnt=>$question) {
				$all_question[$cnt]->q_answers = array();
				 
				// make sure question is always radio if not recognized. This happens when they import wrongly for example
				if($question->answer_type != 'radio' and $question->answer_type != 'checkbox' and $question->answer_type != 'textarea'
					and $question->answer_type != 'matrix' and $question->answer_type != 'nmatrix' and $question->answer_type != 'slider' 
					and $question->answer_type != 'sort' and $question->answer_type != 'gaps') $question->answer_type = 'radio';
				 
				// see whether we use the pre-ordered or randomized questions 	
				if($question->is_survey or $question->truefalse or $question->answer_type == 'matrix' 
					or $question->answer_type == 'nmatrix' or $question->dont_randomize_answers) {
						$answers_for_use = $all_answers_by_order;						
				} 
				else {
					$answers_for_use = $all_answers ;
				}
				
				foreach($answers_for_use as $answer) {
					 if($answer->question_id==$question->ID) {
					 		$all_question[$cnt]->q_answers[]=$answer;
					 }
				}	
				
				// shall we cut number of answers?
				if($exam->num_answers and !$question->is_survey and !$question->truefalse and !$question->dont_randomize_answers
					and $question->answer_type!='matrix' and $question->answer_type!='nmatrix'  and $question->answer_type!='textarea') {
					$all_question[$cnt]->q_answers = array_slice($all_question[$cnt]->q_answers, 0, $exam->num_answers);
					
					// shuffle again to make sure the correct are not on top BUT ONLY if the question is not sticked to non-randomize
					shuffle($all_question[$cnt]->q_answers);
				}
				
				// let's trick WP engine over-optimizers here with one more shuffle
    			if($ob == 'RAND()' and !$question->is_survey and !$question->truefalse and $question->answer_type!='matrix' 
    				 and $question->answer_type!='nmatrix'  and $question->answer_type!='textarea' and !$question->dont_randomize_answers) shuffle($all_question[$cnt]->q_answers);
			} // end foreach question
    }
    
    // check if user can access exam
    static function can_access($exam) { 
      	global $user_ID, $wpdb;
      	if($exam->fee > 0 and watupro_intel()) require_once(WATUPRO_PATH."/i/models/payment.php");
      	$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
      
        // limited total number of attempts for this test?
        if(!empty($advanced_settings['total_attempts_limit'])) {
        	  $num_attempts = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM ".WATUPRO_TAKEN_EXAMS." 
        	  	WHERE exam_id=%d AND in_progress=0", $exam->ID));
        	  if($num_attempts >= $advanced_settings['total_attempts_limit']) {        	  	
        	  	 if(empty($advanced_settings['total_attempts_limit_message'])) printf(__('This %s is no longer available.', 'watupro'), WATUPRO_QUIZ_WORD);
        	  	 else echo stripslashes(rawurldecode($advanced_settings['total_attempts_limit_message']));     
        	  	 WatuPRO::$output_sent = true;
		 	 	 return false;
        	  }
        } // end checking the total number of attempts	
    	   	
    	 // always access free public exams    	 
    	 if($exam->fee > 0 and class_exists('WatuPROIExam') and method_exists('WatuPROIExam', 'adjust_price')) WatuPROIExam :: adjust_price($exam);
		 if(!$exam->require_login and $exam->fee <= 0) return true;
		  
		 if($exam->require_login and !is_user_logged_in()) return false;
		 
		 // can't access due to Namaste! LMS?
		 $namaste_no_access = false;
		 if(class_exists('NamasteLMS') and get_option('namaste_use_exams') == 'watupro' and $exam->require_login and !empty($exam->namaste_courses)) {
		 	 $is_enrolled = false;
		 	 $namaste_courses_str = '';
		 	 $namaste_num_courses = '';
		 	 $_student = new NamasteLMSStudentModel();
		 	 
		 	 $cids = explode('|', $exam->namaste_courses);
		 	 foreach($cids as $cid) {
		 	 	if(empty($cid)) continue;
		 	 	$namaste_num_courses++;
		 	 	$course = get_post($cid);
		 	 	if(!empty($namaste_courses_str)) $namaste_courses_str .= ', ';
		 	 	$namaste_courses_str .= stripslashes($course->post_title);
		 	 	
		 	 	if($_student->is_enrolled($user_ID, $cid)) {
		 	 		$is_enrolled = true;
		 	 		continue;
		 	 	}
		 	 }
		 	 
		 	 if(!$is_enrolled) $namaste_no_access = true;
		 } // end Namaste check
		 
		 // admin can always access
		 if(current_user_can('manage_options') or current_user_can('watupro_manage_exams')) {
		 	if(empty($_POST['action']) and $exam->fee > 0) echo "<p><b>".sprintf(__('Note: This %s requires payment, but you are administrator and do not need to go through it.','watupro'), WATUPRO_QUIZ_WORD)."</b></p>";
		 	
		 	if($namaste_no_access) echo '<p><b>'.sprintf(__('This %s is accessible to users enrolled in some of the following courses: %s. You are administrator and still can access it.', 'watupro'),
		 		WATUPRO_QUIZ_WORD, $namaste_courses_str).'</b></p>';
		 	return true;
		 }
		 
		 // Namaste no access?
		 if($namaste_no_access) {
		 	 if($namaste_num_courses == 1) printf(__('This %s is accessible only to students enrolled in %s.', 'watupro'), WATUPRO_QUIZ_WORD, $namaste_courses_str); 
		 	 else printf(__('This %s is accessible only to students enrolled in some of the following courses: %s.', 'watupro'), WATUPRO_QUIZ_WORD, $namaste_courses_str);
		 	 return false;
		 }
		 
		// again Namaste but this time no access due to lesson restriction
		 // If option namaste_access_exam_started_lesson is true, exam cannot be accessed if it is associated to a lesson and the lesson is not started 
		 $namaste_lesson_no_access = false;
		 if(class_exists('NamasteLMS') and get_option('namaste_use_exams') == 'watupro' and get_option('namaste_access_exam_started_lesson') == 1) {
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
		 	 	printf(__('To access this %s you need first to read lesson <a href="%s">%s</a>.', 'watupro'), WATUPRO_QUIZ_WORD, $lesson_link, stripslashes($lesson->post_title));
		 	 	echo '</p>';
		 	 	return false;
		 	 }	
		 }
		     	     	
    	 // USER GROUP CHECKS
		 $allowed = WTPCategory::has_access($exam);
		 
		 if(!$allowed) {
		 		printf(__('You are not in allowed user group to take this %s.', 'watupro'), WATUPRO_QUIZ_WORD);
		 		return false;
		 }
		 
		 // restriction by username or email address?		 
		 if(!empty($advanced_settings['restrict_by_user']) and !empty($advanced_settings['allowed_users'])) {
		 	 $allowed_users = explode(PHP_EOL, $advanced_settings['allowed_users']);
		 	 array_walk($allowed_users, 'watupro_trim_value');
		 	 array_map('strtolower', $allowed_users);
		 	 $user_info = get_userdata($user_ID);		 	 

		 	 if(!in_array($user_info->data->user_login, $allowed_users) and !in_array(strtolower($user_info->data->user_email), $allowed_users)) {
		 	 	printf(__('You are not within the allowed user accounts for this %s.', 'watupro'), WATUPRO_QUIZ_WORD);
		 		return false;
		 	 }
		 }
		 
		 // Play plugin restrictions
		 if(class_exists('WatuPROPlayLevels') and method_exists('WatuPROPlayLevels', 'can_access')) {
		 	 if(!WatuPROPlayLevels :: can_access($exam)) return false;
		 }
		 
		 // INTELLIGENCE MODULE RESTRICTIONS
		 if(watupro_intel()) {
			require_once(WATUPRO_PATH."/i/models/dependency.php");
		 	$dependency_message = '';
		 	
		 	if(!WatuPRODependency::check($exam, $dependency_message)) {		 		
		 		echo $dependency_message;
		 		return false;
		 	}		 	
		 			 	
		 	
			if($exam->fee > 0) {			
				
			//echo "FEEE";	
				if(!empty($_POST['stripe_pay'])) WatuPROPayment::Stripe(); // process Stripe payment if any
				if(!empty($_GET['watupro_pdt'])) WatuPROPayment::paypal_ipn(); // process PDT payment if any
				
				if(!WatuPROPayment::valid_payment($exam)) {	
					 if(function_exists('ww_bridge_display_payment_page')) ww_bridge_display_payment_page($exam->ID, false);			
					self::$output_sent = WatuPROPayment::render($exam);				
					return false;
				}
			}
		 }
		 
		 // restrictions from other plugins?		 
		 $success = apply_filters('watupro-access-exam', true, $exam->ID);
		 if(!$success) return false;

    	 return true;
	 }
	 
	 // convert our special correct/wrong classes to 
	 // simple HTML so it can be visible in email and downloaded doc
	 static function cleanup($output, $media = 'email') {
	 	// replace correct/wrong classes for the email
		$correct_style=' style="padding-right:20px;background:url('.plugins_url("watupro").'/correct.png) no-repeat right top;" ';
		$wrong_style=' style="padding-right:20px;background:url('.plugins_url("watupro").'/wrong.png) no-repeat right top;" ';
		$user_answer_style = ' style="font-weight:bold;color:blue;" ';
		
		if($media=='web') $correct_style = $wrong_style="";	
		if($media == 'pdf') {
			$correct_style = '><img src="'.plugins_url("watupro").'/correct.png" hspace="10"';
			$wrong_style = '><img src="'.plugins_url("watupro").'/wrong.png" hspace="10"';
		}	
		
		if(get_option('watupro_email_text_checkmarks') == 1 and $media == 'email') {
		   $correct_style = '><b><font color="green"> &#10004; </font></b';
			$wrong_style = '><b><font color="red"> &#127335; </font></b';
		}
		
		// if media is email remove any column styles
		if($media == 'email') {
			$output = str_replace(array('watupro-2-columns ', 'watupro-3-columns ', 'watupro-4-columns '), '', $output);
		}		
		
		$output = str_replace('><!--WATUEMAILanswerWATUEMAIL--','',$output);
		$output = str_replace('><!--WATUEMAILanswer user-answer correct-answerWATUEMAIL--', $correct_style, $output);
		$output = str_replace('><!--WATUEMAILanswer correct-answerWATUEMAIL--',$correct_style, $output);
		$output = str_replace('><!--WATUEMAILanswer user-answerWATUEMAIL--', $wrong_style, $output);
		
		// in email we have to replace user-answer in <li> tag with hardcoded code
		// the class is replaced even when it contains correct-answer
		if($media == 'email' or $media == 'pdf') {
			$output = str_replace("<li class='answer user-answer'>", "<li ".$user_answer_style.">", $output);
			$output = str_replace("<li class='answer user-answer correct-answer'>", "<li ".$user_answer_style.">", $output);
			
			// fill the gaps have this code
			$output = str_replace('<span class="user-answer">', "<span ".$user_answer_style.">", $output);
			$output = str_replace('<span class="user-answer-unrevealed">', "<span ".$user_answer_style.">", $output);
			$output = str_replace("<li class='answer user-answer-unrevealed'>", "<li ".$user_answer_style.">", $output);
			
			// and some questions have it this way:
			$output = str_replace("<li class='answer user-answer-unrevealed '>", "<li ".$user_answer_style.">", $output);
			
			// replace open end before this because they don't properly receive the checkmark 
			if(get_option('watupro_email_text_checkmarks') == 1) {
			  $output = str_replace('<span class="watupro-screen-reader watupro-open-end">correct</span>', '<b><font color="green"> &#10004; </font></b>', $output);
			  $output = str_replace('<span class="watupro-screen-reader watupro-open-end">wrong</span>', '<b><font color="red"> &#127335; </font></b>', $output);
			}
			
			$output = preg_replace('/<span class="watupro-screen-reader">(.*?)<\/span>/', '', $output);
		}
		
		// shortcodes
		if($media == 'web') {
			$output = $output;
			$output = do_shortcode($output);
		}	
		else 	$output = strip_shortcodes($output);

		return $output;
	 }
}


/******************************** Procedure functions below ************************************/
function watupro_taking_details($noexit = false) {
		global $wpdb, $user_ID;
		
		// select taking
		$taking = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS."
			WHERE id=%d", intval($_GET['id'])));
		$catgrades = unserialize(stripslashes($taking->catgrades_serialized));	
		$GLOBALS['watupro_view_taking_id'] = $taking->ID; 	
		// grade title, if any
		$gtitle = $wpdb->get_var($wpdb->prepare("SELECT gtitle FROM ".WATUPRO_GRADES." WHERE ID=%d", $taking->grade_id));	
		
		// select user
		$student = get_userdata($taking->user_id);

		// make sure I'm admin or that's me
		if(!current_user_can(WATUPRO_MANAGE_CAPS) and $student->ID!=$user_ID) {
			wp_die( __('You do not have sufficient permissions to access this page', 'watupro') );
		}
		
		// select detailed answers
		$answers = $wpdb->get_results($wpdb->prepare("SELECT tA.*, tQ.question as question, tQ.feedback_label as feedback_label,
		tQ.is_survey as is_survey, tQ.calculate_whole as calculate_whole, tC.name as category
		FROM ".WATUPRO_STUDENT_ANSWERS." tA JOIN ".WATUPRO_QUESTIONS." tQ ON tQ.id=tA.question_id 
		LEFT JOIN ".WATUPRO_QCATS." tC ON tC.ID = tQ.cat_id
		WHERE taking_id=%d ORDER BY id", $taking->ID));
		
		// select exam
		$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE id=%d", $taking->exam_id));
		
		if(class_exists('WTPReports') and strstr($exam->final_screen, '[watupror-pie-chart')) {
			WTPReports::$add_scripts = true;
			WTPReports::print_scripts();
		}
		
		if($exam->no_ajax) {
			// any uploaded files?
			$files = $wpdb->get_results($wpdb->prepare("SELECT ID, user_answer_id, filename, filesize FROM ".WATUPRO_USER_FILES."
				WHERE taking_id=%d", $taking->ID));
				
			foreach($answers as $cnt=>$answer) {
				foreach($files as $file) {
					if($file->user_answer_id == $answer->ID) $answers[$cnt]->file = $file;
				}
			}	
		} // end no_ajax
		
		$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
		if(current_user_can(WATUPRO_MANAGE_CAPS)) $advanced_settings['show_only_snapshot'] = null;
		
		// maybe delay results? Don't do it for users with manage caps
		$disallow_results = false;
		$exam->delay_results = WTPUser :: delay_results($exam);
		if(!current_user_can(WATUPRO_MANAGE_CAPS) and $exam->delay_results and current_time('timestamp') < strtotime($exam->delay_results_date)) $disallow_results = true;
		
		// default view of taking details page
		$default_view = get_option('watupro_taking_details_default_view');
		if(empty($default_view)) $default_view = 'table';
		if(!empty($taking->from_watu)) $default_view = 'snapshot';
		
		$ui = get_option('watupro_ui');
		$view_details_hidden_columns = empty($ui['view_details_hidden_columns']) ? array() : (array) $ui['view_details_hidden_columns'];
		
		// export?
		if(!empty($_GET['export'])) {
			if(!empty($advanced_settings['show_only_snapshot']) and !current_user_can(WATUPRO_MANAGE_CAPS)) {
				wp_die( __('You do not have sufficient permissions to access this page', 'watupro') );
			}
			
			$default_view = get_option('watupro_taking_details_default_download');
			if(empty($default_view)) $default_view = 'snapshot';
			
			// doc or PDF? if the PDF bridge is active, generate PDF
			$file_name = (get_option('watupro_taking_details_default_download_file') == 'doc') ? "results.doc" : "results.html";
			
			if(function_exists('pdf_bridge_init') and get_option('watupro_generate_pdf_certificates') == "1") {
				$output = '<html>
				<head><title>'.sprintf(__('%s Results', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD)).'</title>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
				<style type="text/css">
				font-family: sans-serif;
				table, th, td {
				   border: 1px solid black;
				}
				</style>
				<body>';
				ob_start();
				if(@file_exists(get_stylesheet_directory().'/watupro/taking_details.php')) require get_stylesheet_directory().'/watupro/taking_details.php';
				else require WATUPRO_PATH."/views/taking_details.php";
				$content = ob_get_clean();				
				$output .= $content;				
				$output .= '</body></html>';
				$_GET['download_file_name'] = 'results.pdf';
				$output = apply_filters('pdf-bridge-convert', $output);
				echo $output;
				return true;		
			}
			
			$now = gmdate('D, d M Y H:i:s') . ' GMT';
			header('Content-Type: ' . watupro_get_mime_type());
			header('Expires: ' . $now);
			header('Content-Disposition: attachment; filename="'.$file_name.'"');
			header('Pragma: no-cache');			
			echo "<html><head>";
			echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head>";
			echo "<body style='max-width:800px;'>";
			$download_doc = true;
			if(@file_exists(get_stylesheet_directory().'/watupro/taking_details.php')) require get_stylesheet_directory().'/watupro/taking_details.php';
			else require WATUPRO_PATH."/views/taking_details.php";
			
			echo "</body></html>";
			exit;
		}
		   
		if(@file_exists(get_stylesheet_directory().'/watupro/taking_details.php')) require get_stylesheet_directory().'/watupro/taking_details.php';
		else require WATUPRO_PATH."/views/taking_details.php";
		if(!$noexit) exit;
}

function watupro_define_newline() {
	// credit to http://yoast.com/wordpress/users-to-csv/
	$unewline = "\r\n";
	if (strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), 'win')) {
	   $unewline = "\r\n";
	} else if (strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), 'mac')) {
	   $unewline = "\r";
	} else {
	   $unewline = "\n";
	}
	return $unewline;
}

function watupro_get_mime_type()  {
	// credit to http://yoast.com/wordpress/users-to-csv/
	$USER_BROWSER_AGENT="";

			if (preg_match('/OPERA(\/| )([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
				$USER_BROWSER_AGENT='OPERA';
			} else if (preg_match('/MSIE ([0-9].[0-9]{1,2})/',strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
				$USER_BROWSER_AGENT='IE';
			} else if (preg_match('/OMNIWEB\/([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
				$USER_BROWSER_AGENT='OMNIWEB';
			} else if (preg_match('/MOZILLA\/([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
				$USER_BROWSER_AGENT='MOZILLA';
			} else if (preg_match('/KONQUEROR\/([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
		    	$USER_BROWSER_AGENT='KONQUEROR';
			} else {
		    	$USER_BROWSER_AGENT='OTHER';
			}

	$mime_type = ($USER_BROWSER_AGENT == 'IE' || $USER_BROWSER_AGENT == 'OPERA')
				? 'application/octetstream'
				: 'application/octet-stream';
	return $mime_type;
}

// calls $watu->store details
// called by ajax, add_action('wp_loaded','watupro_store_details'); is in main watupro.php
function watupro_store_details() {
   // only for logged in users
   // if(!is_user_logged_in()) exit;   
   $_watu=new WatuPRO();
   $taking_id = $_watu->add_taking($_POST['exam_id'],1);
   $answer = serialize(@$_POST['answer-'.$_POST['question_id']]);
   
	// need to evaluate whether the question is correctly answered?
	// this is set to true when we have defined runtime logic for the quiz
	// the property will then be used in $_watu->store_details() to evaluate the question and return $is_correct
	if(!empty($_POST['evaluate_on_the_fly'])) $_watu->evaluate_on_the_fly = true;	   
   
   $_watu->store_details($_POST['exam_id'], $taking_id, $_POST['question_id'], $answer);
   $_watu->evaluate_on_the_fly = false;
   
   if(watupro_intel() and !empty($_POST['evaluate_on_the_fly'])) watuproi_evaluate_on_the_fly($taking_id);
   
   return $taking_id;
   exit;
}

// calls watpro_store_details for each question in $_POST
// called by ajax when user clicks the optional save buton
function watupro_store_all($question_ids) {
	if(!is_user_logged_in()) exit;
	$_watu=new WatuPRO();
	$taking_id = $_watu->add_taking($_POST['exam_id'],1);
	
	$qids = $_POST['question_ids'];	
	foreach($qids as $qid) {
		$answer = empty($_POST['answer-'.$qid]) ? '' : serialize($_POST['answer-'.$qid]);
		$_watu->store_details($_POST['exam_id'], $taking_id, $qid, $answer);
	}
}

function watupro_submit() {
	require(WATUPRO_PATH."/show_exam.php");
	exit;
}

function watupro_initialize_timer() {
	// set up timer and return time as ajax
	// to avoid cheating this won't happen if current $in_progress taking exists for this exam and user
	global $user_ID, $wpdb;
	$time = current_time('timestamp');
	$_watu = new WatuPRO();
	
	//echo "SETTING COOKIES";
	
	// select exam and get advanced settings
   $exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", intval($_POST['exam_id'])));
	$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
   
	$log_timer = empty($advanced_settings['log_timer']) ? 0 : 1;
	$timer_log = '';
	if($log_timer == 1) $timer_log .= "Timer initialization. ";
	
	if(is_user_logged_in()) {
		$meta_start_time = get_user_meta($user_ID, "start_exam_".intval($_POST['exam_id']), true);
		if(empty($meta_start_time)) {
			if($log_timer == 1) $timer_log .= "There was no unfinished attempt on the test so we set up current time. ";
			update_user_meta( $user_ID, "start_exam_".intval($_POST['exam_id']), $time);
		}
		else {
			if($log_timer == 1) $timer_log .= "There was unfinished attempt on this test started at ".date('Y-m-d H:i:s', $meta_start_time).'. ';
		}
		if(!empty($meta_start_time)) $time = $meta_start_time;
		setcookie('start_time'.$_POST['exam_id'], $time, time()+3600*12, '/');
		$_POST['start_time'] = $time;
		$_POST['timer_log'] = $timer_log;
		$taking_id = $_watu->add_taking($_POST['exam_id'],1);
	}
	else {
		 // not logged in		 		 
		 if(empty($_COOKIE['start_time'.$_POST['exam_id']])) {
		 	setcookie('start_time'.$_POST['exam_id'], $time, time()+3600*24*1000, '/');		 	
		 }
		 else $time = $_COOKIE['start_time'.$_POST['exam_id']];
	} 
	//session_write_close();
	echo "<!--WATUPRO_TIME-->".$time."<!--WATUPRO_TIME-->".current_time('timestamp');
	exit;
}

// check if intelligence module is present
function watupro_intel() {
	if(file_exists(WATUPRO_PATH."/i/controllers/practice.php")) return true;
	else return false;
}

// similar to above but for other modules
function watupro_module($module) {
	if(@file_exists(WATUPRO_PATH."/modules/".$module."/controllers/init.php")) return true;
	else return false;
}