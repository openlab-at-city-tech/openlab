<?php
// handles uploaded files in questions
class WatuPROFileHandler {
	// uploads a file assigned to user's answer
	static function upload_file($question_id, $detail_id, $taking_id) {
		global $wpdb, $user_ID;
						
		// uploaded file?
		if(empty($_FILES['file-answer-'.$question_id]['tmp_name'])) return false;
		
		// get size, type and contents
		$filesize = round($_FILES['file-answer-'.$question_id]['size'] / 1024);
		$filetype = $_FILES['file-answer-'.$question_id]['type'];
		$contents = file_get_contents($_FILES['file-answer-'.$question_id]['tmp_name']);
		
		// meets the requirements for type and size?
		if(!self :: check_requirements($_FILES['file-answer-'.$question_id]['name'], $filesize, $filetype)) return false;
		
		// filter the file contents to allow third party plugins interact
		$contents = apply_filters('watupro-user-file-uploaded', $contents, $question_id, $detail_id, $taking_id);
		
		// now upload - store in BLOB
		$exists = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".WATUPRO_USER_FILES." WHERE user_answer_id=%d", $detail_id));
		
		if($exists) {			
			$result = $wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_USER_FILES." SET 
				filename=%s, filesize=%d, filetype=%s, filecontents=%s, user_id=%d, user_answer_id=%d, taking_id=%d
				WHERE ID=%d", $_FILES['file-answer-'.$question_id]['name'], $filesize, $filetype, 
				$contents, $user_ID, $detail_id, $taking_id, $exists));
		}
		else {		
			$result = $wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_USER_FILES." SET 
				filename=%s, filesize=%d, filetype=%s, filecontents=%s, user_id=%d, user_answer_id=%d, taking_id=%d", 
				$_FILES['file-answer-'.$question_id]['name'], $filesize, $filetype, $contents,
				$user_ID, $detail_id, $taking_id));			
		} 
				
		if($result === false) printf(__('The file %s was not uploaded. Probably too large.', 'watupro'), $_FILES['file-answer-'.$question_id]['name'])."<br>";
	}	// end upload
	
	// will check if the uploaded files meet given size and type requirements
	static function check_requirements($filename, $filesize, $filetype) {
		$max_upload = get_option('watupro_max_upload');
		
		// don't upload files with 0 bytes size
		if($filesize == 0) {
			printf(__('Error uploading %s: The file is empty. Please try again.', 'watupro'), $filename)."<br>";
			return false;
		}
		
		if($max_upload > 0  and $filesize > $max_upload) {
			printf(__('Error uploading %s: The file is %dKB while the max. upload size is %dKB.', 'watupro'), $filename, $filesize, $max_upload)."<br>";
			return false;
		} 
			
		$orig_allowed_types = get_option('watupro_upload_file_types');
		$allowed_types = preg_replace("/\s/", '', strtolower($orig_allowed_types)); // remove spaces
		$allowed_types = explode(',', $allowed_types);
		
		$parts = explode(".", $filename);
		$file_ext = strtolower(array_pop($parts));
		
		if(!in_array($file_ext, $allowed_types)) {
			printf(__('Error uploading %s: only files of type: %s are allowed','watupro'), $filename, $orig_allowed_types);
			return false;
		}
		
		return true;
	}	
	
	// download a file
	static function download() {
		global $wpdb, $user_ID;
		
		// only do this when the URL contains watupro_download_file=$file_id
		if(empty($_GET['watupro_download_file']) or empty($_GET['id']) or !is_numeric($_GET['id'])) return true;
		
		if(!is_user_logged_in()) wp_die(__('Only logged in users can download uploaded files.', 'watupro'));
		
		// select the uploaded file
		$file = $wpdb->get_row($wpdb->prepare("SELECT ID, user_id, user_answer_id, filename, filesize, filetype 
			FROM ".WATUPRO_USER_FILES." WHERE ID=%d", $_GET['id']));
			
		if(empty($file->ID)) wp_die(__('The file has been deleted.', 'watupro'));	
		
		// check access	
		if($file->user_id != $user_ID) {
			if(!current_user_can(WATUPRO_MANAGE_CAPS)) wp_die(__('You can only download your own files.', 'watupro'));
			
			// manager. Let's see if he's allowed to see this file
			$multiuser_access = 'all';
			if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('exams_access');
			if($multiuser_access == 'own') {
				$exam_id = $wpdb->get_var($wpdb->prepare("SELECT exam_id FROM ".WATUPRO_STUDENT_ANSWERS." WHERE
					ID=%d", $file->user_answer_id));
					
				$editor_id = $wpdb->get_var($wpdb->prepare("SELECT editor_id FROM 
					".WATUPRO_EXAMS." WHERE ID=%d", $exam_id));
				if($editor_id != $user_ID) wp_die(__('You can download only files of your own students.', 'watupro'));	
			}		
		}	 
		
		// all good, let's download
		$content = $wpdb->get_var($wpdb->prepare("SELECT BINARY filecontents 
			FROM ".WATUPRO_USER_FILES." WHERE ID=%d", $file->ID));	
			
		header("Content-Length: ".strlen($content)); 
		header("Content-Description: File Transfer");
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".$file->filename."\"");
		header("Content-Transfer-Encoding: binary");
		echo $content;
		exit;
	} // end download
	
	// replaces the uploaded files information in the final screen
	static function final_screen($output, $taking_id) {
		global $wpdb;
		$logged_in = is_user_logged_in();

		$files = $wpdb->get_results($wpdb->prepare("SELECT tF.ID as ID, tF.filename as filename, 
			tF.filesize as filesize, tA.question_id as question_id 
			FROM ".WATUPRO_USER_FILES." tF JOIN ".WATUPRO_STUDENT_ANSWERS." tA ON
			tA.ID = tF.user_answer_id 
			WHERE tF.taking_id=%d", $taking_id));
		
		foreach($files as $file) {
			$file_text = sprintf(__('Uploaded: %s (%d KB)', 'watupro'), $file->filename, $file->filesize);
			if($logged_in) $file_link = "<a href=".site_url("?watupro_download_file=1&id=".$file->ID).">$file_text</a>";
			else $file_link = $file_text;
			$output = str_replace('<!--watupro-uploaded-file-'.$file->question_id.'-->', $file_link, $output);
		}			
		
		return $output;
	}
}