<?php
class WatuPROCertificate {
	static $user_id;	
	
	// returns certificate link and inserts the certificate in user-certificates table
	static function assign($exam, $taking_id, $certificate_id, $user_id) {
		global $wpdb;		
		
		if(!empty($_POST['watupro_taker_email'])) $_POST['taker_email'] = $_POST['watupro_taker_email'];
		$email = @$_POST['taker_email'];
		
		// select certificate
		$cert = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_CERTIFICATES." WHERE ID=%d", $certificate_id));
		if(empty($cert)) return "";
		
		if(!empty($cert->require_approval)) {
			$pending_approval = 1;
			$certificate_text = "";
			if($cert->require_approval_notify_admin) self :: pending_approval_notify($cert, $user_id, $exam, $taking_id);
		}
		else {
			$certificate_url = site_url("?watupro_view_certificate=1&taking_id=$taking_id&id=".$certificate_id);			
			
		   if(empty($cert->var_text)) {
		   	$certificate_text = "<p>".__('You can now', 'watupro')." <a href='".$certificate_url."' target='_blank'>".__('print your certificate', 'watupro')."</a></p>";
		   }			
			else {
				if(strstr($cert->var_text, '{{url}}')) {
					$certificate_text = str_replace('{{url}}', $certificate_url, stripslashes($cert->var_text));
				}				
				else {
					$certificate_text = '<p><a href="'.$certificate_url.'" target="_blank">'.stripslashes($cert->var_text).'</a></p>';
				}
			} // end replacing certificate text
			
			$pending_approval = 0;
		}
		
		// select quiz ID
		$quiz_id = $wpdb->get_var($wpdb->prepare("SELECT exam_id FROM ".WATUPRO_TAKEN_EXAMS." 
			WHERE ID=%d", $taking_id));
		
		// delete any previous records for this user
		if(!get_option('watupro_multiple_certificates')) {
			if(!empty($user_id)) {
				$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_USER_CERTIFICATES." 
				WHERE user_id=%d AND certificate_id = %d AND exam_id=%d", $user_id, $certificate_id, $quiz_id));
			}
			else {
				// delete certificates by email				
				$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_USER_CERTIFICATES." 
				WHERE email = %s AND certificate_id = %d AND exam_id=%d", $email, $certificate_id, $quiz_id));
			}
		}
	       
		// if(empty($user_id) and empty($email)) return ''; // either logged in user or taking email is required	       
	       
	   // store in user certificates
	   $sql = "INSERT INTO ".WATUPRO_USER_CERTIFICATES." (user_id, certificate_id, exam_id, taking_id, pending_approval, email) 
	    	VALUES (%d, %d, %d, %d, %d, %s) ";
	   $wpdb->query($wpdb->prepare($sql, $user_id, $certificate_id, $exam->ID, $taking_id, $pending_approval, $email));
	   $ucert_id = $wpdb->insert_id;
	   
	   if($cert->is_multi_quiz) {
	   	// update the record with the multi-quiz details
	   	$details = get_user_meta($user_id, 'watupro_multicertificate_details', true);
	   	$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_USER_CERTIFICATES." SET
	   		quiz_ids=%s, avg_points=%d, avg_percent=%d
	   		WHERE ID=%d", $details['quiz_ids'], $details['avg_points'], $details['avg_percent'],  $ucert_id));
		}
    
 	   return $certificate_text;
	}
	
	// send notification email to admin when someone earns a certificate that requires approval
	static function pending_approval_notify($cert, $user_id, $exam, $taking_id) {
		global $wpdb;
		if(empty($user_id)) {
			$user_nicename = empty($_POST['watupro_taker_name']) ? @$_POST['taker_name'] : $_POST['watupro_taker_name'];
		}
		else {
			$user = get_userdata($user_id);
			$user_nicename = $user->user_nicename;
		}
				
		$subject = __('A certificate is earned and is pending approval.', 'watupro');
		$message = __('The user "%%user-name%%" has earned the certificate "%%certificate-name%%".  
		To view users who are pending approvals on this certificate visit %%url%%', 'watupro');
		$message = str_replace('%%user-name%%', $user_nicename, $message);
		$message = str_replace('%%certificate-name%%', $cert->title, $message); 
		$message = str_replace('%%url%%', admin_url('admin.php?page=watupro_user_certificates&id='.$cert->ID), $message);
		
		// send email
		$admin_email = watupro_admin_email();
		// $headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers = 'Content-type: text/html; charset=utf8' . "\r\n";
		$headers .= 'From: '. $admin_email . "\r\n";		
		// echo "$admin_email, $subject, $message<br><br>";
		$result = wp_mail($admin_email, $subject, $message, $headers);
		$status = $result ? 'OK' : "Error: ".$GLOBALS['phpmailer']->ErrorInfo;
		
		// save email log if the table is available
   	if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_EMAILLOG."'")) == strtolower(WATUPRO_EMAILLOG)) {
   		$wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_EMAILLOG." SET
   			sender=%s, receiver=%s, subject=%s, date=CURDATE(), status=%s",
   			$admin_email, $admin_email, $subject, $status));
   	}
	}
	
	// sends approval notification to the user when their assigned certificate is approved
	static function approval_notify($certificate, $user_certificate_id) {
		global $wpdb; 
		
		// select user certificate along with taking date
		$user_certificate = $wpdb->get_row($wpdb->prepare("SELECT tUC.*, tT.date as date, tT.email as taking_email 
			FROM ".WATUPRO_USER_CERTIFICATES." tUC
			JOIN ".WATUPRO_TAKEN_EXAMS." tT ON tT.ID = tUC.taking_id 
			WHERE tUC.ID = %d AND tUC.certificate_id=%d", $user_certificate_id, $certificate->ID));
				
		$admin_email = watupro_admin_email();
		$user = get_userdata($user_certificate->user_id);
		$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $user_certificate->exam_id));		
			
		// replace email subject and contents	
		$date = date( get_option('date_format'), strtotime($user_certificate->date));
		
		$subject = str_replace('{{quiz-name}}', stripslashes($exam->name), stripslashes($certificate->approval_email_subject));
		$subject = str_replace('{{certificate}}', stripslashes($certificate->title), $subject);
		$subject = str_replace('{{date}}', $date, $subject);
		
		$message = str_replace('{{quiz-name}}', stripslashes($exam->name), stripslashes($certificate->approval_email_message));
		$message = str_replace('{{certificate}}', stripslashes($certificate->title), $message);
		$message = str_replace('{{date}}', $date, $message);
		$message = str_replace('{{url}}', site_url("?watupro_view_certificate=1&taking_id=".$user_certificate->taking_id."&id=".$certificate->ID), $message);
		
		$message = apply_filters('watupro_content', $message);
		
		$attachments = array();			
		$generate_pdf_certificates = get_option('watupro_generate_pdf_certificates');
		$attach_certificates = get_option('watupro_attach_certificates');
		if(!empty($certificate->ID) and $generate_pdf_certificates == "1" and $attach_certificates) {
			$_GET['certificate_as_attachment'] = true;
			$_GET['id'] = $certificate->ID;
			$_GET['taking_id'] = $user_certificate->taking_id;
			
			$settings = get_option('watupro_certificates_pdf');
			$cert_settings = @$settings[$certificate_id];
			$file_name = "certificate-".$_GET['taking_id'].'.pdf';
			if(!empty($cert_settings['file_name'])) $file_name = $cert_settings['file_name'];
			
			// if file exists we have to change the file name
			if(@file_exists(WP_CONTENT_DIR . "/uploads/".$file_name)) {
				$file_name = preg_replace("/\.pdf/$", '', $file_name);
				$file_name = $file_name .'-'. substr(md5($_SERVER['REMOTE_ADDR']), 0, 6).'.pdf';
			}
			$_GET['download_file_name'] = $file_name;				
			watupro_view_certificate();
			
			$attachments = array( WP_CONTENT_DIR . "/uploads/".$file_name );			
		}
		
		// send email
		//$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers = 'Content-type: text/html; charset=utf8' . "\r\n";
		$headers .= 'From: '. $admin_email . "\r\n";
		$user_email = empty($user_certificate->taking_email) ? $user->user_email :  $user_certificate->taking_email;
		// echo "$user_email, $subject, $message<br><br>";
		$result = wp_mail($user_email, $subject, $message, $headers, $attachments);

		// insert into the raw email log
   	$status = $result ? 'OK' : "Error: ".$GLOBALS['phpmailer']->ErrorInfo;
   	
   	// save email log if the table is available
   	if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_EMAILLOG."'")) == strtolower(WATUPRO_EMAILLOG)) {
   		$wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_EMAILLOG." SET
   			sender=%s, receiver=%s, subject=%s, date=CURDATE(), status=%s",
   			$admin_email, $user_email, $subject, $status));		
   	}
   	
		// delete the certificate file
		if(!empty($certificate->ID) and $generate_pdf_certificates == "1" and $attach_certificates) {
			@unlink( WP_CONTENT_DIR . "/uploads/".$file_name );
		}
	}
	
	// find multi-quiz certificates
	static function multi_quiz($exam_id, $achieved, $percent) {
		global $wpdb, $user_ID;
		if(empty(self :: $user_id)) self :: $user_id = $user_ID;
		
		if(!is_user_logged_in()) return false; // such certificate can only work with logged in users
		
		// find any multi-quiz certificates that contain this quiz
		$certificates = $wpdb->get_results("SELECT * FROM " . WATUPRO_CERTIFICATES . 
			" WHERE is_multi_quiz=1 AND quiz_ids LIKE '%|".$exam_id."|%' ORDER BY avg_points DESC, avg_percent DESC, ID");	
			
	
		// all takings of this user
		$takings = $wpdb->get_results($wpdb->prepare("SELECT ID, exam_id, points, percent_correct 
			FROM ".WATUPRO_TAKEN_EXAMS." WHERE user_id=%d AND in_progress=0", self :: $user_id));	
		$taken_quiz_ids = array();
		foreach($takings as $cnt => $taking) {
			// VERY IMPORTANT: If $taking->ID is the current taking, the % correct and points are not yet stored to the database 
			// so we have to override it using the $percent variable which is passed to the function
			if(isset($_POST['watupro_current_taking_id']) and $taking->ID == $_POST['watupro_current_taking_id']) {
				$takings[$cnt]->percent_correct = $percent;
				$takings[$cnt]->points = $achieved;
			}
			
			if(!in_array($taking->exam_id, $taken_quiz_ids)) $taken_quiz_ids[] = $taking->exam_id;
		}	
		
		// when the first is found, return it
		$certificate_ids = array();
		foreach($certificates as $certificate) {
			// extract quiz IDs
			$quiz_ids = explode('|', $certificate->quiz_ids);
			$quiz_ids = array_filter($quiz_ids);
			if(empty($quiz_ids)) continue;
			
			// did the user take them all?
			foreach($quiz_ids as $quiz_id) {
				if(!in_array($quiz_id, $taken_quiz_ids)) continue 2; // even one non-taken quiz means this certificate won't be earned
			}
			
			// the new option now allows you to require the average on every single quiz in the sequence
			if($certificate -> avg_on_each_quiz) {				
				foreach($quiz_ids as $quiz_id) {
					$quiz_ok = false;
					// make sure there is at least one completed attempt on this quiz where BOTH requirements for points and percent are satisfied
					foreach($takings as $taking) {
						if($taking->exam_id == $quiz_id and $taking->points >= $certificate->avg_points and $taking->percent_correct >= $certificate->avg_percent) {							
							$quiz_ok = true;
							break; // no need to check further takings
						}
					}					
					
					// even one non-satisfied means this certificate won't be earned
					if(!$quiz_ok) continue 2;
				}
				
				// good. This certificate is earned
				$details = array('quiz_ids'=> implode(',', $quiz_ids), 'avg_points' => $certificate->avg_points, 'avg_percent' => $certificate->avg_percent);
				update_user_meta(self :: $user_id, 'watupro_multicertificate_details', $details);
				$certificate_ids[] = $certificate->ID;
			}
			else {
				// the code below handles the default type of multi quiz certificates: where the averages mean average performance on ALL quizzes			
				// count the number of takings for this certificate quizzes
				$num_quizzes = 0;
				foreach($takings as $taking) {
					if(in_array($taking->exam_id, $quiz_ids)) $num_quizzes++;
				}				
				// echo "NUM QUIZZES $num_quizzes";
				// did the user collect the required averages?
				$total_points = $total_percent = 0;
				foreach($takings as $taking) {
					if(!in_array($taking->exam_id, $quiz_ids)) continue;
					$total_points += $taking->points;
					$total_percent += $taking->percent_correct;
				}
				
				$avg_points = round($total_points / $num_quizzes);
				$avg_percent = round($total_percent / $num_quizzes);
				
				// if all is true, return the certificate ID
				if($avg_points >= $certificate->avg_points and $avg_percent >= $certificate->avg_percent) {
					// update user meta with the certificate criteria because we'll need this on assign()
					$details = array('quiz_ids'=> implode(',', $quiz_ids), 'avg_points'=>$avg_points, 'avg_percent'=>$avg_percent);
					update_user_meta(self :: $user_id, 'watupro_multicertificate_details', $details);
					
					$certificate_ids[] = $certificate->ID;
				}
			}	// end if the averages are required as total		
			
		} // end foreach certificate
		
		// return array of all satisfied certificate IDs
		return $certificate_ids;
	} // end multi_quiz
}