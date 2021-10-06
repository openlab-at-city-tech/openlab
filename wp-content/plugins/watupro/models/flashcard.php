<?php
// display and process flashcard questions 
class WatuPROFlashcard {
	static function display($ques, $in_progress, $q_answers) {
		$design_settings = self :: get_settings();
		$width = $design_settings['width'];
		$height = $design_settings['height'];
		$border_radius = $design_settings['border_radius'];
		$color = $design_settings['color'];
		$text_color = $design_settings['text_color'];
		$text_size = $design_settings['text_size'];
		$back_color = $design_settings['back_color'];
		$back_text_color = $design_settings['back_text_color'];
		$back_text_size = $design_settings['back_text_size'];
		$separator = $design_settings['flashcard_separator'];
		
		$ui = get_option('watupro_ui');
		$ui['flashcard_separator'] = empty($ui['flashcard_separator']) ? '=' : $ui['flashcard_separator'];
		
		$flashcards = '';
		foreach ($q_answers as $ans_cnt => $ans) {
			$onclick = "onclick=\"WatuPROFlipCard('{$ans->ID}');\"";		
			
			// make sure to handle images
			if(strstr($ans->answer, "src=")) $ans->answer = str_replace("src=", "src[WTPEQUALSIGN]", $ans->answer);
			
			// front & back answer
			$parts = explode($separator, $ans->answer);			
			$front_answer = stripslashes(trim($parts[0]));
			$back_answer = stripslashes(trim($parts[1]));	
			
			// replace 	[WTPEQUALSIGN] with = if any
			$front_answer = str_replace('[WTPEQUALSIGN]', '=', $front_answer);
			$back_answer = str_replace('[WTPEQUALSIGN]', '=', $back_answer);
			
			$flashcards .= "<div id='watuproFlashcard-".$ans->ID."' $onclick class='watupro_flashcard' style='width:" . $width . "px;height:" . $height . "px; border-radius:" . $border_radius . "%;'><div class='watupro_front_face front' style='background-color:" . $color . ";border-radius:" . $border_radius . "%;color:".$text_color.";font-size:".$text_size."px;'>" . $front_answer . "</div>
			<div class='watupro_back_face back' style='background-color:" . $back_color . ";border-radius:" . $border_radius. "%;color:".$back_text_color.";font-size:".$back_text_size."px;'>" . $back_answer . "</div></div>";			
		}
		echo $flashcards;
	} // end display
	
	// process single flashcard
	static function process($class, $ans, $compare, $sr_text) {
		$design_settings = self :: get_settings();
		$width = $design_settings['width'];
		$height = $design_settings['height'];
		$border_radius = $design_settings['border_radius'];
		$color = $design_settings['color'];
		$text_color = $design_settings['text_color'];
		$text_size = $design_settings['text_size'];
		$back_color = $design_settings['back_color'];
		$back_text_color = $design_settings['back_text_color'];
		$back_text_size = $design_settings['back_text_size'];
		$flashcard_class = 'watupro_front_face front';	
		$separator = $design_settings['flashcard_separator'];
		
		
		// front or back shown will depend on user's choice
		if(strstr($class, 'user-answer')) {
			$color = $back_color;
			$text_color = $back_text_color;
			$text_size = $back_text_size;
			$flashcard_class = 'watupro_back_face back';		
		}
		
		// make sure to handle images
		if(strstr($ans->answer, "src=")) $ans->answer = str_replace("src=", "src[WTPEQUALSIGN]", $ans->answer);
		$parts = explode($separator, $ans->answer);
		$front_answer = stripslashes(trim($parts[0]));
		$back_answer = stripslashes(trim($parts[1]));	

		$has_image = (strstr($front_answer, '[WTPEQUALSIGN]') or	strstr($back_answer, '[WTPEQUALSIGN]')) ? true : false;
				
		// replace 	[WTPEQUALSIGN] with = if any
		$front_answer = str_replace('[WTPEQUALSIGN]', '=', $front_answer);
		$back_answer = str_replace('[WTPEQUALSIGN]', '=', $back_answer);	
		
		// correct or wrong image
		if(strstr($class, 'correct-answer')) $result_image = '<img src="'.WATUPRO_URL.'correct.png" hspace="10">';
		else $result_image = '<img src="'.WATUPRO_URL.'wrong.png" hspace="10">';
		
		if(strstr($class, 'user-answer-unrevealed') or 
			(!strstr($class, 'user-answer') and !strstr($class, 'correct-answer'))) $result_image = ''; 
		
		$class = ''; // cleanup the original classes to avoid misalignment	
		
		// if there is image in either, display just the back answer because the space probably won't be enough
		$display_answer = $has_image ? $back_answer : sprintf(__('(%1$s)<br>%2$s', 'watupro'), $front_answer, $back_answer);
	
		$flashcard = "<div id='watuproFlashcard-".$ans->ID."-show' class='watupro_flashcard processed' style='width:" . $width . "px;height:" . $height . "px; border-radius:" . $border_radius . "%;color:".$text_color.";font-size:".$text_size."px;'>
		<div class='$flashcard_class' style='background-color:" . $color . ";border-radius:" . $border_radius . "%;color:".$text_color.";font-size:".$text_size."px;'>
		<div style='text-align:right;vertical-align:top;height:20px;'>$result_image</div>" . $display_answer . "</div>$sr_text</div>";			
		return $flashcard; 
	}
	
	static function design_settings() {
		global $wpdb;
		
		if(!empty($_POST['ok']) and check_admin_referer('watupro_flashcards')) {
			$design_settings = array(
				'width' => intval($_POST['width']),
				'height' => intval($_POST['height']),
				'border_radius' => intval($_POST['border_radius']),
				'color' => sanitize_text_field($_POST['color']),
				'text_color' => sanitize_text_field($_POST['text_color']),
				'text_size' => intval($_POST['text_size']),
				'back_color' => sanitize_text_field($_POST['back_color']),
				'back_text_color' => sanitize_text_field($_POST['back_text_color']),
				'back_text_size' => intval($_POST['back_text_size']),
				'flashcard_separator' => sanitize_text_field($_POST['flashcard_separator']),
			);
			update_option('watupro_flashcard_design', $design_settings);	
		}
		
		
		$design_settings = self :: get_settings(); // make sure we don't end up with any invalid settings
		
		include(WATUPRO_PATH . '/views/flashcard-design.html.php');
	} // end design_settings
	
	// fill default design settings
	static function get_settings() {
		$design_settings = get_option('watupro_flashcard_design');
		if(empty($design_settings)) $design_settings = array();
		if(empty($design_settings['width']) or !is_int($design_settings['width'])) $design_settings['width'] = 200;
		if(empty($design_settings['height']) or !is_int($design_settings['height'])) $design_settings['height'] = 200;
		if(empty($design_settings['border_radius']) or !is_int($design_settings['border_radius']) 
			or $design_settings['border_radius'] < 0 or $design_settings['border_radius'] > 90) $design_settings['border_radius'] = 5;
		if(empty($design_settings['color'])) $design_settings['color'] = '#000080';	
		if(empty($design_settings['text_color'])) $design_settings['text_color'] = '#FFFF00';
		if(empty($design_settings['text_size']) or !is_int($design_settings['text_size'])) $design_settings['text_size'] = 20;
		if(empty($design_settings['back_color'])) $design_settings['back_color'] = '#008000';
		if(empty($design_settings['back_text_color'])) $design_settings['back_text_color'] = '#FFFFFF';
		if(empty($design_settings['back_text_size']) or !is_int($design_settings['back_text_size'])) $design_settings['back_text_size'] = 20;
		if(empty($design_settings['flashcard_separator'])) $design_settings['flashcard_separator'] = '=';

		return $design_settings;
	}
}