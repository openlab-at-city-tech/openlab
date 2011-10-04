<?php
include('lib/setup.php');
include('lib/themeoptions.php');

/* Get thumbnail image for a post */
function ahs_getimg($id,$dimensions=array(150,150)) {
	$image_url = get_template_directory_uri().'/images/no-image.jpg';
	if (has_post_thumbnail($id)) {
		$image_id = get_post_thumbnail_id($id);
		$image_url = wp_get_attachment_image_src($image_id,$dimensions);
		$image_url = $image_url[0];
	}
	return $image_url;
}

/* shortcode to create a basic contact form */
function contact_form() {
	$str = '<div id="write_to_me" class="contact_page_form">
		<p><?php echo $ahstheme_contactintro ?></p>
		
		<div id="error" class="error"></div>
		
		<form id="contactform" onsubmit="return false">
			<input type="hidden" name="themedir" id="themedir" value="'.get_template_directory_uri().'" />
			<div class="input">
				<label>Name</label>
				<input type="text" name="name" id="name" />
			</div>
			<div class="input">
				<label>Email</label>
				<input type="text" name="email" id="email" />
			</div>
			<div class="input">
				<label>Message</label>
				<textarea name="comment" id="comment"></textarea>
			</div>
			<div class="submit">
				<input type="submit" name="submit" value="&raquo; Send" />
			</div>
		</form>
	</div>';
	return $str;
}
add_shortcode('contact_form','contact_form');

/* get a custom-length excerpt. uses substrws() */
function ahs_excerpt($text,$chars=240) {
	$text = strip_tags($text,'<b><strong><br><br /><a>');
	if (ereg('<a',$text)) $text .= "</a>";
	$text = substrws($text,$chars);
	return $text;
}

/* Thanks, Benny. http://www.php.net/manual/en/function.substr.php#90724 */
/** 
* word-sensitive substring function with html tags awareness 
* @param text The text to cut 
* @param len The maximum length of the cut string 
* @returns string 
**/ 
function substrws( $text, $len=180 ) { 

    if ( (strlen($text) > $len) ) { 

        $whitespaceposition = strpos($text," ",$len)-1; 

        if ($whitespaceposition > 0)
            $text = substr($text, 0, ($whitespaceposition+1)); 

        // close unclosed html tags 
        if (preg_match_all("|<([a-zA-Z]+)>|",$text,$aBuffer) ) { 
            if( !empty($aBuffer[1]) ) { 
                preg_match_all("|</([a-zA-Z]+)>|",$text,$aBuffer2); 
                if( count($aBuffer[1]) != count($aBuffer2[1]) ) { 
                    foreach( $aBuffer[1] as $index => $tag ) { 
                        if( empty($aBuffer2[1][$index]) || $aBuffer2[1][$index] != $tag) 
                            $text .= '</'.$tag.'>'; 
                    } 
                } 
            } 
        } 
    } 

    return $text; 
} 


?>