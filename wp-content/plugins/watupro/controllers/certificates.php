<?php
function watupro_certificates() {
	global $wpdb, $user_ID;
	wp_enqueue_style('style.css', plugins_url('/watupro/style.css'), null, '1.0');
	
	$multiuser_access = 'all';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('certificates_access');
	
	$exams = $wpdb->get_results("SELECT ID, name FROM ".WATUPRO_EXAMS." ORDER BY name");
	
	$expiration_date = empty($_POST['expiration_date']) ? date('Y-m-d') : sanitize_text_field($_POST['expiration_date']);
	
	// prepare vars for add & edit
	if(!empty($_POST['ok'])) {
		$title = sanitize_text_field($_POST['title']);
		$html = watupro_strip_tags($_POST['html']);
		$require_approval = empty($_POST['require_approval']) ? 0 : 1;
		$require_approval_notify_admin = empty($_POST['require_approval_notify_admin']) ? 0 : 1;
		$approval_notify_user = empty($_POST['approval_notify_user']) ? 0 : 1;
		$approval_email_subject = sanitize_text_field($_POST['approval_email_subject']);
		$approval_email_message = watupro_strip_tags($_POST['approval_email_message']);
		$is_multi_quiz = empty($_POST['is_multi_quiz']) ? 0 : 1;
	   $quiz_ids = empty($_POST['quiz_ids']) ? '' : '|'.implode('|', watupro_int_array($_POST['quiz_ids'])).'|';	
	   $avg_points = intval($_POST['avg_points']);		
	   $avg_percent = intval($_POST['avg_percent']);
		$has_expiration = empty($_POST['has_expiration']) ? 0 : 1;
		if(!is_numeric($_POST['expiration_period_num'])) $has_expiration = false;
		$expiration_period = $_POST['expiration_period_num'].' '.$_POST['expiration_period_period'];	
		$expired_message = watupro_strip_tags($_POST['expired_message']);
		$expiration_mode = empty($_POST['expiration_mode']) ? 'period' : sanitize_text_field($_POST['expiration_mode']);
		$var_text = watupro_strip_tags($_POST['var_text']);
		$avg_on_each_quiz = empty($_POST['avg_on_each_quiz']) ? 0 : 1;
		$fee = empty($_POST['fee']) ? 0 : floatval($_POST['fee']);
	}
	
	switch(@$_GET['do']) {
		case 'add':
			if(!empty($_POST['ok']) and check_admin_referer('watupro_certificate')) {
				$wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_CERTIFICATES." (title, html, require_approval, 
					require_approval_notify_admin, approval_notify_user, approval_email_subject, approval_email_message, editor_id,
					is_multi_quiz, quiz_ids, avg_points, avg_percent, has_expiration, expiration_period, expired_message,
					expiration_mode, expiration_date, var_text, avg_on_each_quiz, fee)
					VALUES (%s, %s, %d, %d, %d, %s, %s, %d, %d, %s, %d, %d, %d, %s, %s, %s, %s, %s, %d, %f)", 
					$title, $html, $require_approval, $require_approval_notify_admin, $approval_notify_user, $approval_email_subject, 
					$approval_email_message, $user_ID, $is_multi_quiz, $quiz_ids, $avg_points, $avg_percent, 
					$has_expiration, $expiration_period, $expired_message, $expiration_mode, $expiration_date, $var_text, $avg_on_each_quiz, $fee));
				$cid = $wpdb->insert_id;
				do_action('watupro-certificate-saved', $cid);	
				echo "<meta http-equiv='refresh' content='0;url=admin.php?page=watupro_certificates' />"; 
				exit;
			}
			
			watupro_enqueue_datepicker();
			$expiration_num = 1;		   			
			if(@file_exists(get_stylesheet_directory().'/watupro/certificate.php')) require get_stylesheet_directory().'/watupro/certificate.php';
			else require WATUPRO_PATH."/views/certificate.php";
		break;
	
		case 'edit':
			if($multiuser_access == 'own') {
				$certificate=$wpdb->get_row($wpdb->prepare("SELECT * FROM 
					".WATUPRO_CERTIFICATES." WHERE ID=%d", $_GET['id']));
				if($certificate->editor_id != $user_ID) wp_die(__('You can manage only your own certificates', 'watupro'));	
			}		
		
			if(!empty($_POST['del'])) {
	           $wpdb->query($wpdb->prepare("DELETE FROM 
					".WATUPRO_CERTIFICATES." WHERE ID=%d", intval($_GET['id'])));
	
				echo "<meta http-equiv='refresh' content='0;url=admin.php?page=watupro_certificates' />"; 
				exit;
			}
	
			if(!empty($_POST['ok']) and check_admin_referer('watupro_certificate')) {
				$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_CERTIFICATES." SET
					title=%s, html=%s, require_approval= %d, require_approval_notify_admin=%d, approval_notify_user=%d,
					approval_email_subject=%s, approval_email_message=%s, is_multi_quiz=%d, quiz_ids=%s, avg_points=%d, avg_percent=%d,
					has_expiration=%d, expiration_period=%s, expired_message=%s, expiration_mode=%s, expiration_date=%s, var_text=%s,
					avg_on_each_quiz = %d, fee = %f
					WHERE ID=%d", $title, $html, $require_approval, $require_approval_notify_admin, $approval_notify_user, $approval_email_subject, 
					$approval_email_message, $is_multi_quiz, $quiz_ids, $avg_points, $avg_percent, 
					$has_expiration, $expiration_period, $expired_message, $expiration_mode, $expiration_date, $var_text, $avg_on_each_quiz, $fee,
					intval($_GET['id'])));
				do_action('watupro-certificate-saved', $_GET['id']);	
				echo "<meta http-equiv='refresh' content='0;url=admin.php?page=watupro_certificates' />"; 
				exit;
			}
	
			$certificate = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_CERTIFICATES." WHERE ID=%d", $_GET['id']));
			$expiration_date = $certificate->expiration_date;
	     
	      if(strstr($certificate->expiration_period, ' ')) {
	         $parts = explode(' ', $certificate->expiration_period);
	         $expiration_num = $parts[0];
	         $expiration_period = $parts[1];
	      }
	      else $expiration_num = 1;	
	      
	      watupro_enqueue_datepicker();
			if(@file_exists(get_stylesheet_directory().'/watupro/certificate.php')) require get_stylesheet_directory().'/watupro/certificate.php';
			else require WATUPRO_PATH."/views/certificate.php";
		break;
	
		default:
			if(!empty($_POST['save_pdf_settings'])) {
				$generate_pdf_certificates = empty($_POST['generate_pdf_certificates']) ? 0 : 1;
				$multiple_certificates = empty($_POST['multiple_certificates']) ? 0 : 1;
				$no_rtf = empty($_POST['no_rtf']) ? 0 : 1;
				$attach_certificates = empty($_POST['attach_certificates']) ? 0 : 1;
				$public_certificates = empty($_POST['public_certificates']) ? 0 : 1;
				
				update_option('watupro_generate_pdf_certificates', $generate_pdf_certificates);
				update_option('watupro_docraptor_test_mode', intval($_POST['docraptor_test_mode']));
				update_option('watupro_pdf_engine', sanitize_text_field(@$_POST['pdf_engine']));
				update_option('watupro_multiple_certificates', $multiple_certificates);
				update_option('watupro_certificates_no_rtf', $no_rtf);
				update_option('watupro_attach_certificates', $attach_certificates);
				if(!empty($_POST['docraptor_key'])) update_option('watupro_docraptor_key', sanitize_text_field($_POST['docraptor_key']));
				update_option('watupro_public_certificates', $public_certificates);
			}		
		
			// select my certificates
			$own_sql = ($multiuser_access == 'own') ? $wpdb->prepare(" WHERE editor_id = %d ", $user_ID) : "";
			$certificates=$wpdb->get_results("SELECT * FROM ".WATUPRO_CERTIFICATES." $own_sql ORDER BY title");
				
			$generate_pdf_certificates = get_option('watupro_generate_pdf_certificates');	
			$docraptor_key = get_option('watupro_docraptor_key');
			$docraptor_test_mode = get_option('watupro_docraptor_test_mode');
			$pdf_engine = get_option('watupro_pdf_engine');
	
			if(@file_exists(get_stylesheet_directory().'/watupro/certificates.php')) require get_stylesheet_directory().'/watupro/certificates.php';
			else require WATUPRO_PATH."/views/certificates.php";
		break;
	}
}

// shows the certificates earned by a student
function watupro_my_certificates($in_shortcode = false) {
	global $wpdb, $user_ID;
	
	// admin can see this for every student
	if(!empty($_GET['user_id']) and current_user_can(WATUPRO_MANAGE_CAPS)) $user_id = $_GET['user_id'];
	else $user_id = $user_ID;
	
	$user = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE ID=%d", $user_id));
	
	if(!empty($_GET['set_public_access'])) {
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_USER_CERTIFICATES." SET
			public_access=%d WHERE id=%d AND user_id=%d", $_GET['public_access'], $_GET['id'], $user_id));
		// watupro_redirect("admin.php?page=watupro_my_certificates");	
	}
	
	$certificates = $wpdb->get_results($wpdb->prepare("SELECT tC.*, tE.name as exam_name, tG.gtitle as grade, 
		tT.points as points, tT.end_time as end_time, tT.id as taking_id, tUS.ID as us_id, tUS.public_access as public_access,
		tUS.quiz_ids as quiz_ids, tUS.avg_points as avg_points, tUS.avg_percent as avg_percent
		FROM ".WATUPRO_USER_CERTIFICATES." tUS 
		JOIN ".WATUPRO_CERTIFICATES." tC ON tUS.certificate_id = tC.ID
		JOIN ".WATUPRO_TAKEN_EXAMS." tT ON tT.ID = tUS.taking_id
		LEFT JOIN ".WATUPRO_GRADES." tG ON tT.grade_id=tG.id
		JOIN ".WATUPRO_EXAMS." tE ON tE.ID = tT.exam_id
		WHERE tUS.user_id = %d AND tUS.pending_approval=0 
		GROUP BY tUS.ID ORDER BY tT.ID  DESC", $user_id));		
		
	foreach($certificates as $cnt=>$certificate) {
		if($certificate->is_multi_quiz and !empty($certificate->quiz_ids)) {
			$quizzes = $wpdb->get_results("SELECT name FROM ".WATUPRO_EXAMS." WHERE ID IN (".$certificate->quiz_ids.") ORDER BY name");
			$quiz_str = '';
			foreach($quizzes as $qct=>$quiz) {
				if($qct) $quiz_str .= ', ';
				$quiz_str .= stripslashes($quiz->name);
			}
			
			$certificates[$cnt]->exam_name = $quiz_str;
			$certificates[$cnt]->grade = sprintf(__('Avg. points: %d; Avg. %% correct answers: %d%%', 'watupro'), $certificate->avg_points, $certificate->avg_percent);
		}	
	}	
			
	// cleanup duplicates - we only need certificates shown for the latest taking
	/*$final_certificates = array();	
	$certificate_ids = array();
	
	foreach($certificates as $certificate) {
		if(in_array($certificate->ID, $certificate_ids)) continue;		
		$final_certificates[] = $certificate;
		$certificate_ids[] = $certificate->ID;
	}
	
	$certificates = $final_certificates;*/
	
	$public_certificates = get_option('watupro_public_certificates');
	
	if(@file_exists(get_stylesheet_directory().'/watupro/my_certificates.php')) require get_stylesheet_directory().'/watupro/my_certificates.php';
	else require WATUPRO_PATH."/views/my_certificates.php";
}


function watupro_view_certificate() {
	global $wpdb, $user_ID;
	
	// select certificate
	$certificate = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_CERTIFICATES." WHERE ID=%d", $_GET['id']));
	
	if(empty($certificate->ID)) {
		wp_die(__("no such certificate", "watupro"));
	}
	$output = stripslashes($certificate->html);
	
	// no taking id? only admin can see it then
	if(empty($_GET['taking_id'])) {
		if(!current_user_can(WATUPRO_MANAGE_CAPS)) 
			wp_die( __('You do not have sufficient permissions to access this page', 'watupro').' 1' );
	}
	else {
		// find taking 
		$taking = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS."
			WHERE ID=%d", intval($_GET['taking_id'])));
		$GLOBALS['watupro_view_taking_id'] = $taking->ID;	

		// find user_certificate record and see if the current user is allowed to see the certificate
		$user_certificate = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_USER_CERTIFICATES."
			WHERE taking_id=%d AND certificate_id=%d AND user_id=%d", $taking->ID, $certificate->ID, $taking->user_id));
		if(empty($user_certificate)) wp_die(__('Such certificate was never earned.', 'watupro'));	
		
		// set earner ID in pseudo-POST var so it can be used by shortcodes
		$_POST['watupro_certificate_user_id'] = $user_certificate->user_id;
		
		// public access or a certificate of non user? (those are always public)		
		$public_certificates = get_option('watupro_public_certificates');
		if(!is_user_logged_in() and empty($user_certificate->public_access) and !empty($user_certificate->user_id) and empty($public_certificates)) {
			watupro_redirect( wp_login_url(site_url("?watupro_view_certificate=1&taking_id=".$_GET['taking_id']."&id=".$_GET['id'])) );
		}
			
		if(empty($user_certificate->public_access) and empty($user_certificate->email) and empty($public_certificates)
			and ($taking->user_id!=$user_ID or $user_certificate->pending_approval) and !current_user_can(WATUPRO_MANAGE_CAPS)) {
			wp_die( __('You do not have sufficient permissions to access this page', 'watupro').' 2' );
		}
		
		// handle certificate expiration
		if($certificate->has_expiration) {
			if($certificate->expiration_mode != 'date') {
			   $is_valid = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d
			    AND DATE(end_time) >= '".date('Y-m-d', current_time('timestamp'))."' - INTERVAL ".$certificate->expiration_period, $taking->ID));
		   }
		   else {
		   	if(current_time('timestamp') > strtotime($certificate->expiration_date)) $is_valid = false;
		   	else $is_valid = true;
		   }
		   
		   if(!$is_valid)  {
		      // the certificate has expired
		      $expired_message = $certificate->expired_message;
		      if(empty($expired_message)) $expired_message = '<p>'.__('This certificate has expired.', 'watupro').'</p>';
		      $output = stripslashes($expired_message);
		   } 
		}
		
		// paid certificate?
		if($certificate->fee > 0) {
			if(!empty($_POST['stripe_pay'])) WatuPROPayment::Stripe(false, true); // process Stripe payment if any
			if(!empty($_GET['watupro_pdt'])) WatuPROPayment::paypal_ipn(); // process PDT payment if any
				
			if(!WatuPROPayment::valid_certificate_payment($certificate)) {				
				$certificate->name = $certificate->title;
				$output_sent = WatuPROPayment::render($certificate, true);				
				exit;	
			}
		}
				
		$user_id = $taking->user_id;
	
		// select exam
		$exam = $wpdb->get_row($wpdb->prepare("SELECT tQ.*, tC.name as cat FROM ".WATUPRO_EXAMS." tQ
			LEFT JOIN ".WATUPRO_CATS." tC ON tC.ID = tQ.cat_id 
			WHERE tQ.ID=%d", $taking->exam_id));
			
		// select grade
		$grade = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_GRADES." WHERE ID=%d", $taking->grade_id));
		
		$user_info=get_userdata($user_id);
		
		if(empty($taking->name)) {
			$name=(empty($user_info->first_name) or empty($user_info->last_name)) ? @$user_info->display_name:
				$user_info->first_name." ".$user_info->last_name;
		}
		else $name = $taking->name;		
		
		// apply qranslate filter before replacing the vars
		$output = apply_filters('watupro_qtranslate', $output); 
		
		// replace {{{name-field}}} and {{{email-field}}}
		$name_field = empty($taking->name) ? $name : $taking->name;
		$email_field = empty($taking->email) ? $user_info->user_email : $taking->email;
		$output = str_replace('{{{name-field}}}', $name_field, $output);
		$output = str_replace('{{{email-field}}}', $email_field, $output);		
		$output=str_replace("%%GRADE%%", stripslashes($taking->result), $output);
		$output=str_replace("%%GTITLE%%", stripslashes(@$grade->gtitle), $output);
		$grade_description = preg_replace('/<span class="watupro-gtitle">(.*?)<\/span>/', '', $taking->result);
		$output=str_replace("%%GDESC%%", $grade_description, $output);
		$output=str_replace("%%QUIZ_NAME%%", stripslashes($exam->name), $output);
		$output=str_replace("%%QUIZ_CAT%%", ($exam->cat_id ? stripslashes($exam->cat) : __('Uncategorized', 'watupro')), $output);
		$output=str_replace("%%DESCRIPTION%%", stripslashes($exam->description), $output);
		$output=str_replace("%%USER_NAME%%", stripslashes($name), $output);
		$output=str_replace("%%USER-NAME%%", stripslashes($name), $output);
		$output=str_replace("%%EMAIL%%", $email_field, $output);
		$output=str_replace("%%POINTS%%", $taking->points, $output);
		$output=str_replace("%%POINTS-ROUNDED%%", round($taking->points), $output);				
		$taken_date = date_i18n(get_option('date_format'), strtotime($taking->date));
	   $output = str_replace("%%DATE%%", $taken_date, $output);

	   $replace_fields = array('company' => $taking->field_company, 'phone' => $taking->field_phone, 'field1' => stripslashes($taking->custom_field1), 'field2' => stripslashes($taking->custom_field2));	   
	   $exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . WATUPRO_EXAMS . " WHERE ID=%d ", $taking->exam_id));
		$output = WTPExam :: replace_contact_fields($exam, $replace_fields, $output);
	   
		// expiration date if any
		$expiration_date = '';
		if(strstr($output, '%%EXPIRATION-DATE%%') and $certificate->has_expiration) {
			if($certificate->expiration_mode != 'date') {
				$expiration_date = $wpdb->get_var($wpdb->prepare("SELECT %s + INTERVAL {$certificate->expiration_period}", $taking->date));
				$expiration_date = date_i18n(get_option('date_format'), strtotime($expiration_date));
			}
			else {
				$expiration_date = date_i18n(get_option('date_format'), strtotime($certificate->expiration_date));
			}
		}	   
		$output = str_replace("%%EXPIRATION-DATE%%", $expiration_date, $output);
		
		// replace category grades
		$catgrades_array = unserialize(stripslashes($taking->catgrades_serialized));
		//print_r($catgrades_array);
		if(!empty($catgrades_array) and is_array($catgrades_array)) {
			foreach($catgrades_array as $cnt => $catgrade) {	
				// category percentageofmax
				//$percent_of_max = empty($cats_maxpoints[$catgrade['cat_id']]['max_points']) ? 0 : round(100 * $catgrade['points'] / $cats_maxpoints[$catgrade['cat_id']]['max_points']);
				//if($catgrade['points'] <= 0) $percent_of_max = 0;			
				//$catgrades_array[$cnt]['percent_points'] = $percent_of_max;
			
				$output =  str_replace('%%CATEGORY-NAME-'.$catgrade['cat_id'].'%%', stripslashes($catgrade['name']), $output);
				$output =  str_replace('%%CATEGORY-DESCRIPTION-'.$catgrade['cat_id'].'%%', @$catgrade['description'], $output);
				$output =  str_replace('%%CATEGORY-CORRECT-'.$catgrade['cat_id'].'%%', $catgrade['correct'], $output);
				$output =  str_replace('%%CATEGORY-TOTAL-'.$catgrade['cat_id'].'%%', $catgrade['total'], $output);
				$output =  str_replace('%%CATEGORY-POINTS-'.$catgrade['cat_id'].'%%', $catgrade['points'], $output);
				$output =  str_replace('%%CATEGORY-PERCENTAGE-'.$catgrade['cat_id'].'%%', $catgrade['percent'], $output);
				$output =  str_replace('%%CATEGORY-PERCENTAGEOFMAX-'.$catgrade['cat_id'].'%%', $catgrade['percent_points'], $output);
				
				// the following will work only if the data is stored
				$output =  str_replace('%%CATEGORY-GTITLE-'.$catgrade['cat_id'].'%%', @$catgrade['gtitle'], $output);
				$output =  str_replace('%%CATEGORY-GDESCRIPTION-'.$catgrade['cat_id'].'%%', @$catgrade['gdescription'], $output);	
			}
		}
	   
	   $taken_start_time = date(get_option('date_format').' '.get_option('time_format'), strtotime($taking->start_time));
	   $output=str_replace("%%START-TIME%%", $taken_start_time, $output);
	   $taken_end_time = date(get_option('date_format').' '.get_option('time_format'), strtotime($taking->end_time));
	   $output=str_replace("%%END-TIME%%", $taken_end_time, $output);
	   $output=str_replace("%%ID%%", sprintf('%04d', $user_certificate->ID), $output);
		$output = watupro_parse_answerto($output, $taking->ID, $exam);	  	  	 
		$output = WTPExam :: replace_contact_fields($exam, 
		 	array('company'=>$taking->field_company, 'phone' => $taking->field_phone), $output);
		 	
		$time_spent = '';
		if(strstr($output, '%%TIME-SPENT%%')) {
			$time_spent = WTPRecord :: time_spent_human( WTPRecord :: time_spent($taking));
			$output=str_replace("%%TIME-SPENT%%", $time_spent, $output);
		} 	
		
		// verification seal. If the plugin [qrcode]Example string[/qrcode] is installed, we'll call do_shortcode to generate a QR code
		// if not, output an URL and let the certificate author decide how to use it
		if(strstr($output, '%%VERIFICATION%%')) {
			$hash = md5($user_certificate->ID . $user_certificate->user_id . $user_certificate->certificate_id . $user_certificate->taking_id);
			$url = site_url('?watupro_verify_certificate=1&id='.$user_certificate->ID.'&hash=' . $hash);
			$verification = $url;
			
			if(class_exists('DoQRCode')) {
				$url = str_replace('&', '_____', $url); // QR code does not work very well with & so we are doing some trick here
				$verification = '[qrcode]'.$url.'[/qrcode]';
			}	
			
			$output = str_replace('%%VERIFICATION%%', $verification, $output);
		}
		
	   $output = apply_filters('watupro_content', $output);	   
	   
	   if(get_option('watupro_certificates_no_rtf') != 1) {
	   	$output = wpautop($output);
	   }
	 }	// end found taking
	
	if(get_option('watupro_generate_pdf_certificates') == "1") {
		$output = WatuPRO :: cleanup($output, 'pdf'); 	
		$pdf_engine = get_option('watupro_pdf_engine');
		// $test_mode = 1;
		// generate through docRaptor
		if(empty($pdf_engine) or $pdf_engine == 'docraptor') {
			if(empty($user_certificate->pdf_output)) {			
				$api_key = get_option('watupro_docraptor_key');
				$test_mode = get_option('watupro_docraptor_test_mode');
				include_once(WATUPRO_PATH.'/lib/docraptor/DocRaptor.class.php');
				$docraptor = new DocRaptor($api_key);
				$docraptor->setDocumentContent($output)->setDocumentType('pdf')->setTest($test_mode)->setName('certificate.pdf');
				$content = $docraptor->fetchDocument();		
				
				// store in DB to avoid more queries
				$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_USER_CERTIFICATES." SET pdf_output = %s WHERE ID = %d", $content, $user_certificate->ID));
			}
			else {			
				$content = $wpdb->get_var($wpdb->prepare("SELECT BINARY pdf_output FROM ".WATUPRO_USER_CERTIFICATES." WHERE ID=%d", $user_certificate->ID));			
			}
			
			header("Content-Length: ".strlen($content)); 
			header('Content-type: application/pdf');
			echo $content;
			exit;
		}
		
		if(!empty($pdf_engine) and $pdf_engine = 'pdf-bridge') {
			if(!strstr($output, '<html>')) {
				$output = '<html>
				<head><title>'.$certificate->title.'</title>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
				<body><!--watupro-certificate-id-'.$certificate->ID.'-watupro-certificate-id-->'.$output.'</body>
				</html>';
			}
			else {
				$output .= '<!--watupro-certificate-id-'.$certificate->ID.'-watupro-certificate-id-->';
			}	
			//	die($output);
			$content = apply_filters('pdf-bridge-convert', $output);		
			//echo $content;
			if(empty($_GET['certificate_as_attachment'])) exit;
			else return true;	
		}		
	} // end pdf certificate
	
	// else output HTML
	$output = WatuPRO :: cleanup($output); 	
	
	?>
	<html>
	<head><title><?php echo $certificate->title;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
	<body><?php echo $output;?></body>
	</html>
	<?php 
	exit;
}

// template redirect for viewing certificate
function watupro_certificate_redirect() {
	if(empty($_GET['watupro_view_certificate']) and empty($_GET['watupro_verify_certificate'])) return true;
	if(!empty($_GET['watupro_view_certificate'])) watupro_view_certificate();
	if(!empty($_GET['watupro_verify_certificate'])) watupro_verify_certificate();
}

// view and manage users who earned certificates
function watupro_user_certificates() {
	global $wpdb, $user_ID;
	
	$certificate = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_CERTIFICATES." WHERE ID=%d", $_GET['id']));
	
	// check access	
	$multiuser_access = 'all';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('certificates_access');
	if($multiuser_access == 'own') {
		if($certificate->editor_id != $user_ID) wp_die(__('You can manage only your own certificates', 'watupro'));	
	}	
	
	if(!empty($_GET['approve'])) {
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_USER_CERTIFICATES." SET pending_approval = 0 WHERE ID=%d", $_GET['user_certificate_id']));
		
		// send email to user?
		if($certificate->approval_notify_user) WatuPROCertificate :: approval_notify($certificate, $_GET['user_certificate_id']);
		
		watupro_redirect("admin.php?page=watupro_user_certificates&id=".$_GET['id']);
	}
	
	if(!empty($_GET['delete'])) {
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_USER_CERTIFICATES." WHERE ID=%d", intval($_GET['user_certificate_id'])));
	}
	
	// select users
	$users = $wpdb->get_results($wpdb->prepare("SELECT tUC.ID as user_certificate_id, tU.user_nicename as user_nicename, tU.user_email as user_email, 
	tE.name as exam_name, tUC.pending_approval as pending_approval, tT.ID as taking_id, tT.date as taking_date, tT.result as taking_result,
	tE.ID as exam_id, tT.email as taker_email, tT.name as taker_name, tUC.quiz_ids as quiz_ids, tUC.avg_points as avg_points, tUC.avg_percent as avg_percent
	FROM ".WATUPRO_USER_CERTIFICATES." tUC 
	LEFT JOIN {$wpdb->users} tU ON tUC.user_id = tU.ID  
	JOIN ".WATUPRO_TAKEN_EXAMS." tT ON  tT.ID = tUC.taking_id
	JOIN ".WATUPRO_EXAMS." tE ON tE.ID = tT.exam_id AND tE.ID = tUC.exam_id
	WHERE tUC.certificate_id=%d
	ORDER BY tT.ID DESC", $certificate->ID));
	// print_r($users);
	
	// prepare quiz names
	if($certificate->is_multi_quiz) {
		foreach($users as $cnt=>$user) {
			$quizzes = $wpdb->get_results("SELECT name FROM ".WATUPRO_EXAMS." WHERE ID IN (".$user->quiz_ids.") ORDER BY name");
			$quiz_str = '';
			foreach($quizzes as $qct=>$quiz) {
				if($qct) $quiz_str .= ', ';
				$quiz_str .= stripslashes($quiz->name);
			}
			
			$users[$cnt]->quizzes = $quiz_str;
		} // end foreach user
	} // end if multi quiz
	
	$dateformat = get_option('date_format');
	
	// if this is a multi-quiz certificate we need a string $exam_names from all quizzes required
	
	$is_admin = true;
	wp_enqueue_script('thickbox',null,array('jquery'));
	wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
	
	if(@file_exists(get_stylesheet_directory().'/watupro/users-earned-certificate.html.php')) require get_stylesheet_directory().'/watupro/users-earned-certificate.html.php';
	else require WATUPRO_PATH."/views/users-earned-certificate.html.php";
}

// Manually award certificate
function watupro_manually_award_certificate() {
   global $wpdb;
   
   // select the certificate
   $certificate = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_CERTIFICATES." WHERE id=%d", intval($_GET['id'])));
   $taken_exams = array();
   
   // fetch user and manually award certificate
   if(!empty($_POST['user_name']) and check_admin_referer('watupro_award')) {
      if(strstr($_POST['user_name'], '@')) $user = get_user_by('email', sanitize_email($_POST['user_name']));
      else $user = get_user_by('login', sanitize_text_field($_POST['user_name']));
      
      // select quiz taking. If user ID is 0, the query will be by email
      $user_sql = empty($user->ID) ? $wpdb->prepare(" AND tT.email=%s ", $_POST['user_name']) : $wpdb->prepare(" AND tT.user_id=%d ", $user->ID);
      $takings = $wpdb->get_results("SELECT tE.ID as exam_id, tE.name as exam_name, tT.date as date, tT.points as points, tG.gtitle as grade, 
         tT.percent_correct as percent_correct, tT.ID as ID
         FROM ".WATUPRO_EXAMS." tE JOIN ".WATUPRO_TAKEN_EXAMS." tT ON tT.exam_id=tE.ID
         LEFT JOIN ".WATUPRO_GRADES." tG ON tG.ID = tT.grade_id
         WHERE tT.in_progress=0 $user_sql ORDER BY exam_name ASC, date DESC");
      
      // now fill array of exams with takings to make it JS based dropdowns
      $exam_ids = array();
      $taken_exams = array();
      foreach($takings as $taking) {
         if(!in_array($taking->exam_id, $exam_ids)) {
            $exam_ids[] = $taking->exam_id;
            $taken_exams[] = array("ID"=> $taking->exam_id, "name"=>$taking->exam_name, "takings"=>array());
         }
      }     
      
      // now match takings to exams again to make the drop-downs in the view
      foreach($taken_exams as $cnt=>$taken_exam) {
         foreach($takings as $taking) {
            if($taking->exam_id == $taken_exam['ID']) $taken_exams[$cnt]['takings'][] = $taking;
         }
      } // end filling $taken_exams
      
      if(!empty($_POST['award']) and !empty($_POST['taking_id'])) {
         // award and redirect to user certificates page
         $user_email = empty($user->ID) ? $_POST['user_name'] : $user->user_email;
         
         $multi_certificate_sql = '';
         if($certificate->is_multi_quiz) { 
            $_POST['quiz_ids'] = empty($_POST['quiz_ids']) ? array() : watupro_int_array($_POST['quiz_ids']);
            $quiz_ids = implode(',', $_POST['quiz_ids']);
            $multi_certificate_sql = $wpdb->prepare(", quiz_ids=%s, avg_points=%f, avg_percent=%d", 
               $quiz_ids, floatval($_POST['avg_points']), intval($_POST['avg_percent']));
         }                  
         
         $wpdb->query($wpdb->prepare("INSERT INTO " .WATUPRO_USER_CERTIFICATES . " SET
            user_id=%d, certificate_id=%d, exam_id=%d, taking_id=%d, email=%s $multi_certificate_sql",
            @$user->ID, $certificate->ID, intval($_POST['exam_id']), intval($_POST['taking_id']), $user_email));
      }
   }
   
   // select exams for multiple-quiz certificates
   if($certificate->is_multi_quiz) {
      $quiz_ids = explode('|', $certificate->quiz_ids);
      $quiz_ids = array_filter($quiz_ids);
      if(empty($quiz_ids)) $quiz_ids[] = 0;
      $exams = $wpdb->get_results("SELECT ID, name FROM ".WATUPRO_EXAMS." WHERE ID IN (".implode(',', $quiz_ids).") ORDER BY name");
   }
   
   $dateformat = get_option('date_format');
   
   include(WATUPRO_PATH . '/views/award-certificate.html.php');
} // end manually award

// This is not a certificate function but because we use the same logic as for viewing PDF it will be here
// called on template_redirect
function watupro_taking_pdf() {
	global $wpdb, $user_ID;
	
	if(empty($_GET['watupro_view_pdf']) or !function_exists('pdf_bridge_init')) return false;
	
	// ensure that taking ID is the same in session
	if(empty($_GET['tid'])) return false;
	$taking_id = intval($_GET['tid']);
	
	// select taking
	$taking = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $taking_id));
	
	
	if($taking_id != $_COOKIE['watupro_taking_id']) {
		// this means we are not on the just generated final screen. We should verify one looks at his own taking OR is admin
		// make sure I'm admin or that's me
		if( !current_user_can(WATUPRO_MANAGE_CAPS) and $taking->user_id != $user_ID) {
			wp_die( __('You do not have sufficient permissions to access this page', 'watupro') );
		}
	}
	
	// select test
	$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $taking->exam_id));
	$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
	$pdf_settings = empty($advanced_settings['pdf_settings']) ? array() : $advanced_settings['pdf_settings'];
	
	// maybe delay results? Don't do it for users with manage caps
	$disallow_results = false;
	$exam->delay_results = WTPUser :: delay_results($exam);
	if(!current_user_can(WATUPRO_MANAGE_CAPS) and $exam->delay_results and current_time('timestamp') < strtotime($exam->delay_results_date)) $disallow_results = true;
	
	// select taking final screen
	$output = '';
	if($disallow_results) $output = apply_filters('watupro_content', stripslashes($exam->delay_results_content)); 
	else $output = WatuPRO::cleanup($taking->details, 'pdf');
	
	// replace the link for PDF and social sharing link classes class watupro-paginated-hidden
	$output = str_replace(array('class="watupro-social-sharing"', 'class="watupro-pdf"'), 'style="display:none;"', $output);
		
	// prepare and send CSS URLs
	$stylesheets = array(WATUPRO_URL.'style.css', WATUPRO_URL.'css/conditional.css', get_stylesheet_uri());	
		
	// if there are stylesheets passed from settings, merge
	// NYI
	
	$pdf_settings['stylesheets'] = $stylesheets;	
	
	// output PDF
	if(!strstr($output, '<html>')) {
		$output = '<html>
		<head><title>'.(empty($certificate->title) ? __('Results', 'watupro') : stripslashes($certificate->title)).'</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
		<body>'.$output.'</body>
		</html>';
	}

	$content = apply_filters('pdf-bridge-convert', $output, $pdf_settings);		
	return true;	
} // end watupro_taking_pdf

// verify certificate
function watupro_verify_certificate() {
	global $wpdb;
	// we might be coming from an imperfect QR code where & is replaced with 5 underscores
	if(strstr($_GET['watupro_verify_certificate'], '_____')) {
		$parts = explode('_____', $_GET['watupro_verify_certificate']);
		$idparts = $parts[1];
		$hashprts = $parts[2];
		list($x, $_GET['id']) = explode('=', $idparts);
		list($x, $_GET['hash']) = explode('=', $hashprts);
	}
	if(empty($_GET['hash']) or empty($_GET['id'])) die(__('Invalid request', 'watupro'));
	
	// find user certificate
	$user_certificate = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_USER_CERTIFICATES." WHERE ID=%d", intval($_GET['id'])));
	if(empty($user_certificate->ID)) die(__('Not a valid certificate.', 'watupro'));
	
	// compare the hash with the md5 hash of ID . user_id . certificate_id . taking_id
	// There is no real "security" required here but we want to avoid users randomly checking every ID
	$hash = md5($user_certificate->ID . $user_certificate->user_id . $user_certificate->certificate_id . $user_certificate->taking_id);	
	if($_GET['hash'] != $hash) die(__('Not a valid certificate.', 'watupro'));
	
	// select taking
	$taking = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . WATUPRO_TAKEN_EXAMS. " WHERE ID=%d", $user_certificate->taking_id));
	$user_info=get_userdata($taking->user_id);
	
	if(empty($taking->name)) {
		$name=(empty($user_info->first_name) or empty($user_info->last_name)) ? @$user_info->display_name:
			$user_info->first_name." ".$user_info->last_name;
	}
	else $name = $taking->name;		
	if(empty($name)) $name = __('Guest', 'watupro');
	
	$date = date_i18n(get_option('date_format'), strtotime($taking->date));
	
	// the certificate is valid. For now just display a basic text verifying this.
	echo '<p align="center">'.sprintf(__('Valid certificate issued to %1$s on %2$s', 'watupro'), $name, $date).'</p>';
	exit;
}