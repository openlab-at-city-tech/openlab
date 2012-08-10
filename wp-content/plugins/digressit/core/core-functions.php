<?php 
global $commentbrowser, $blog_id, $current_user, $current_user_comments, $development_mode, $testing_mode, $production_mode;
global $digressit_content_function, $digressit_comments_function, $digressit_commentbox_function,$is_commentbrowser;

global $browser, $post_paragraph_count;


$digressit = $options = get_option('digressit');

//add_action('init', 'digressit_load');

add_action('after_setup_theme', 'digressit_setup' );
add_action('admin_head-post.php', 'add_comment_change_notice');

add_action('wp_print_scripts',  'digressit_core_print_scripts', 1);
add_action('wp_print_styles',  'digressit_core_print_styles', 1) ; 		
add_action('wp_head',  'on_wp_head') ; 		

if(esc_url($options['custom_header_image'], array('http', 'https'))){
	add_action('add_header_image', 'custom_digressit_logo');
}
add_filter('the_content', 'digressit_parser', 10000);	



/**
 * Basic theme setup function
 * *
 * @since 3.0.0
 */
function digressit_setup(){
	global $wpdb;
	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu()
	add_theme_support( 'nav-menus' );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	
	$sql = "SHOW COLUMNS FROM $wpdb->comments";	
	$columns = $wpdb->get_results($sql);

	$comment_text_signature_exists = false;
	foreach($columns as $col){
		if($col->Field == 'comment_text_signature'){
			$comment_text_signature_exists = true;
		}
	}
	
	if($comment_text_signature_exists == false){
		$sql = "ALTER TABLE `$wpdb->comments` ADD `comment_text_signature` VARCHAR( 255 ) NULL;";	
		$wpdb->query($sql);
	}

	// This theme allows users to set a custom background
	add_custom_background();	
	
	register_nav_menus(
		array(
		  'Main Page' => __('This menu appears in the first page')  ,
		  'Top Menu' => __('A custom top menu')  ,
		  'Sidebar' => __('A custom sidebar menu')  
		)
	);

	
}
	

/**
 * Hooks into the {@the_content} of each posts and breaks the text into an array.
 * *
 * @since 3.0.0
 *
 * @param string $html Required. Comment amount in post if > 0, else total comments blog wide.
 */
function get_secondary_menu(){
	do_action('secondary_menu');
}


/**
 * Hooks into the {@the_content} of each posts and breaks the text into an array.
 * *
 * @since 3.0.0
 *
 * @param string $html Required. Comment amount in post if > 0, else total comments blog wide.
 */
function get_widgets($widget_name){
	return dynamic_sidebar($widget_name);
}


/**
 * Hooks into the {@the_content} of each posts and breaks the text into an array.
 * *
 * @since 3.0.0
 *
 * @param string $html Required. Comment amount in post if > 0, else total comments blog wide.
 */
function get_dynamic_widgets(){
	do_action('add_dynamic_widget');
}


/**
 * Hooks into the {@the_content} of each posts and breaks the text into an array.
 * *
 * @since 3.0.0
 *
 * @param string $html Required. Comment amount in post if > 0, else total comments blog wide.
 */
function get_single_default_widgets(){
	
	$options = get_option('digressit');
	if ( !is_active_sidebar('single-sidebar') ) : 
		
		?>
		
		<div class="sidebar-widgets default-list-post">
		<div id="dynamic-sidebar" class="sidebar <?php echo $options['auto_hide_sidebar']; ?> <?php echo $options['sidebar_position']; ?>">		
		<?php
	
		ListPostsWithCommentCount::widget($args =array(), array('title' => 'Posts', 
															'auto_hide' => true, 
															'position' => 'left', 
															'order_by' => 'ID', 
															'order_type' => 'ASC', 
															'categorize' => false, 
															'categories' => null, 
															'show_category_titles' => false));
		?>
		</div>
		</div>
		<?php
	else:
		do_action('add_dynamic_widget');
	endif;
}



function get_stylized_content_header(){
	if(has_action('stylized_content_header')){
		do_action('stylized_content_header');
	}
}



function get_stylized_title(){
	
	echo "<div id='the_title'  class='the_title'>";	
	if(has_action('stylized_title')){
		do_action('stylized_title');
	}
	else{			
		if(is_single() || is_page() || is_search()){
			echo "<h2><a href='".get_permalink()."'>".get_the_title()."</a></h2>";
		}			
	}
	echo "</div>";
}



function on_wp_head(){
?>
<meta http-equiv="X-UA-Compatible" content="chrome=1">
<?php

}




function lightbox_custom_login(){
	//header('Location: /#login');
	//die();
}



function regexp_digressit_content_parser($html){
	$matches = array();
	//we need to do this twice in case there are empty tags surrounded by empty p tags
	$html = preg_replace('/<(?!input|br|img|meta|hr|\/)[^>]*>\s*<\/[^>]*>/ ', '', $html);
	$html = preg_replace('/<(?!input|br|img|meta|hr|\/)[^>]*>\s*<\/[^>]*>/ ', '', $html);

	$options = get_option('digressit');
	if($options['parse_list_items'] == 1){
		$html = preg_replace('/<(\/?ul|ol)>/', '', $html);
		$html = preg_replace('/<li>/', '<p>&bull;   ', $html);
	}
	$html = html_entity_decode(force_balance_tags($html));


	@preg_match_all('#<('.$tags.')>(.*?)</('.$tags.')>#si',$html,$matches_array);
	$matches = $matches_array[0];

		
	return  $matches;
}


function discrete_digressit_content_parser($content){
	global $wpdb, $image_path, $post;

	$matches = array();
	$paragraph_blocks = explode('[break]', $content);


	$blocks = null;
	$text_signatures = null;
	$permalink = get_permalink($post->ID);



	$defaults = array('post_id' => $post->ID);
	$total_comments = get_comments($defaults);
	$total_count = count($total_comments);

	foreach($paragraph_blocks as $key=>$paragraph){
		$text_signature = $key+1;
		$text_signatures[] = $text_signature;
		$paranumber = $number = ( $key+1 );

		$comment_count = 0;

		foreach($total_comments as $c){
			if($c->comment_text_signature == $paranumber){
				$comment_count++;
			}
		}
		
				
		$paragraphnumber = '<span class="paragraphnumber">';
	 	$numbertext = ($comment_count == 1) ?  'is one comment' : 'are '.$comment_count.' comments';
	 	$numbertext = ($comment_count == 0) ?  'are no comments' : $numbertext;		
		$digit_count = strlen($comment_count);
		$commenticon =	'<span  title="There '.$numbertext.' for this paragraph" class="commenticonbox"><small class="commentcount fff commentcount'.$digit_count.'">'.$comment_count.'</small></span>'."\n";

		if($number == 1){
			//$morelink = '<span class="morelink"></span>';
		}
		else{
			$morelink = null;
		}

		$block_content = "<div id='textblock-$number' class='textblock'>
			<span class='paragraphnumber'><a href='$permalink#$number'>$number</a></span>
			<span  title='There $numbertext for this paragraph' class='commenticonbox'><small class='commentcount commentcount".$digit_count."'>".$comment_count."</small></span>
			<span class='paragraphtext'>".force_balance_tags($paragraph)."</span>
		</div>" .  $morelink;
		
		$blocks[$paranumber] = $block_content;
    }	
	return $blocks;
}


/**
 * strip_selected_tags ( string str [, string strip_tags[, strip_content flag]] )
 * ---------------------------------------------------------------------
 * Like strip_tags() but inverse; the strip_tags tags will be stripped, not kept.
 * strip_tags: string with tags to strip, ex: "<a><p><quote>" etc.
 * strip_content flag: TRUE will also strip everything between open and closed tag
 */
function strip_selected_tags($str, $tags = "", $stripContent = false)
{
    preg_match_all("/<([^>]+)>/i",$tags,$allTags,PREG_PATTERN_ORDER);
    foreach ($allTags[1] as $tag){
        if ($stripContent) {
            $str = preg_replace("/<".$tag."[^>]*>.*<\/".$tag.">/iU","",$str);
        }
        $str = preg_replace("/<\/?".$tag."[^>]*>/iU","",$str);
    }
    return $str;
}



/**
 * Returns true if $string is valid UTF-8 and false otherwise.
 */
function is_utf8($string) {
  
   // From http://w3.org/International/questions/qa-forms-utf-8.html
   return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]            # ASCII
       | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
       |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
       |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       |  \xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
       |  \xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
   )*$%xs', $string);
}

/**
 * Hooks into the {@the_content} of each posts and breaks the text into an array.
 * *
 * @since 3.0.0
 *
 * @param string $html Required. Comment amount in post if > 0, else total comments blog wide.
 * @param string $tags Optional
 * @return array An array of each text block with the proper html tags for comment count and extra tags for adding javascript hooks
 */
function standard_digressit_content_parser($html, $tags = 'div|table|object|p|ul|ol|blockquote|code|h1|h2|h3|h4|h5|h6|h7|h8'){
	global $post;
	$matches = array();
	$html = strip_selected_tags($html, '<hr>');
	//we need to do this twice in case there are empty tags surrounded by empty p tags
	$html = preg_replace('/<(?!input|br|img|meta|hr|\/)[^>]*>\s*<\/[^>]*>/ ', '', $html);
	$html = preg_replace('/<(?!input|br|img|meta|hr|\/)[^>]*>\s*<\/[^>]*>/ ', '', $html);

	$options = get_option('digressit');
	
	$blocks = array();
	$text_signatures = null;
	$permalink = get_permalink($post->ID);



	$defaults = array('post_id' => $post->ID);
	$total_comments = get_comments($defaults);
	$total_count = count($total_comments);
	
		
	if($options['parse_list_items'] == 1){
		$html = preg_replace('/<(\/?ul|ol)>/', '', $html);
		$html = preg_replace('/<li>/', '<p>*   ', $html);
	}
	
	
	$html = wpautop(force_balance_tags($html));
	$html = str_replace('&nbsp', '', $html);
	$html = str_replace('&copy;', '(c)', $html);

	//escape &entities;
	//$html = preg_replace('/&[^; ]{0,6}.?/e', "((substr('\\0',-1) == ';') ? '\\0' : '&amp;'.substr('\\0',1))", $html);
	$html = preg_replace("/&#?[a-z0-9]{2,8};/i","",$html);



	//$html = str_replace('<a <br />;', '<a ', $html);
	//$html = str_replace('<img <br />;', '<img ', $html);
	
	
	
	libxml_use_internal_errors(true);
	//var_dump($html);
	if($result = @simplexml_load_string(trim('<content>'.$html.'</content>'))){
		$xml = $result->xpath('/content/'. $tags);
		foreach($xml as $match){
			$matches[] = $match->asXML();
		}
	}
	else{
		if(current_user_can('edit_posts')){
			
			$matches[] = "There was a problem parsing your content. Please make sure that every HTML tag is properly nested and closed. 
			To validate your text, and to try and repair it, use the <a href='https://wordpress.org/extend/plugins/tidy-up/'>Tidy Up</a> plugin for WordPress.";


			
			if (!$result) {
			    $errors = libxml_get_errors();

				//var_dump($errors);
			

			    foreach ($errors as $error) {
			        $error_messages .= display_xml_error($error, $xml). "<br>";
			    }

			    libxml_clear_errors();
			}	
			
			$matches[] = $error_messages;		
			
		}
		else{
			$matches[] = "Sorry! There was a problem loading the contents of this post. Please notify the site administrator.";
			
			
		}
	
	}

	//var_dump($result);

	foreach($matches as $key=>$paragraph){
		$text_signature = $key+1;
		$text_signatures[] = $text_signature;
		$paranumber = $number = ( $key+1 );

		$comment_count = 0;
		foreach($total_comments as $c){
			if($c->comment_text_signature == $paranumber){
				$comment_count++;
			}
		}
			
		$paragraphnumber = '<span class="paragraphnumber">';
	 	$numbertext = ($comment_count == 1) ?  'is one comment' : 'are '.$comment_count.' comments';
	 	$numbertext = ($comment_count == 0) ?  'are no comments' : $numbertext;
		
		$digit_count = strlen($comment_count);
		$commenticon =	'<span  title="There '.$numbertext.' for this paragraph" class="commenticonbox"><small class="commentcount fff commentcount'.$digit_count.'">'.$comment_count.'</small></span>'."\n";

		if($number == 1){
			//$morelink = '<span class="morelink"></span>';
		}
		else{
			$morelink = null;
		}

		$matches = null;

		preg_match_all('/class=\"([^"]+)\"/is', $paragraph, $matches);
		//var_dump($matches);
		
		if(count($matches)){
			foreach($matches[1] as $match){
				//var_dump( $match);
				if(strstr($match, 'wp-image')){		
					$paragraph = str_replace($match, 'lightbox lightbox-images '.$match, $paragraph);
				}
				$paragraph = str_replace(" class=\"$matches\" ", " class=\"lightbox lighbox-images $classes\" ", $paragraph);
				//break;
			}
		}
		$block_content = "<div id='textblock-$number' class='textblock'>
			<span class='paragraphnumber'><a href='$permalink#$number'>$number</a></span>
			<span  title='There $numbertext for this paragraph' class='commenticonbox'><small class='commentcount commentcount".$digit_count."'>".$comment_count."</small></span>
			<span class='paragraphtext'>".force_balance_tags($paragraph)."</span>
		</div>" .  $morelink;
		
		$blocks[$paranumber] = $block_content;
    }

	global $post_paragraph_count;
	$post_paragraph_count = count($blocks);
	return $blocks;

}


function display_xml_error($error, $xml)
{
    $return  = $xml[$error->line - 1] . "\n";
  //  $return .= str_repeat('-', $error->column) . "\n";

    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "Warning: ";
            break;
         case LIBXML_ERR_ERROR:
            $return .= "Error: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "Fatal Error: ";
            break;
    }

    $return .= trim($error->message) .
               "\n  Line: $error->line";

    if ($error->file) {
        $return .= "\n  File: $error->file";
    }
	$return  .= "<br>";

    return "$return\n\n\n\n";
}




function digressit_parser($content){
	if(is_single()){
		return implode("\n", (array)digressit_paragraphs($content));
	}
	else{
		return $content;
	}
	
}

function digressit_paragraphs($content){	
	return call_user_func(get_digressit_content_parser_function(), $content); 
}

function the_paragraph($number){
	global $post;
	

	echo get_the_paragraph($number);
	
}

function get_the_paragraph($number){
	global $post;
	
	$paragraphs = digressit_paragraphs(wpautop($post->post_content));
	
	return $paragraphs[$number];
	
	
}




function get_digressit_comments_function(){
	$options = get_option('digressit');
	

	if( isset($options['comments_parser']) || function_exists($options['comments_parser']) ){
		return $options['comments_parser'];
	}
	else{
		return 'standard_digressit_comment_parser';
	}	
}

function get_digressit_content_parser_function(){
	$options = get_option('digressit');


	if( isset($options['content_parser']) || function_exists($options['content_parser']) ){
		return $options['content_parser'];
	}
	else{
		return 'standard_digressit_content_parser';
	}	
}






function json_remote_call($webservice, $parameters = null){

	if(is_array($parameters)){
		foreach($parameters as $key => $param){
			$params [$key] = $param;
		}
	}



	$params ['format'] = 'json';


	$data = http_build_query($params);

	//var_dump($data);


	$context_options = array (
		'http' => array (
			'method' => 'POST',
			'header'=> "Content-type: application/x-www-form-urlencoded\r\n",
			"Content-Length: " . strlen($data) . "\r\n",
			'content' => $data
			)
		);



	$context = stream_context_create($context_options);
	
	
	$response = null;
	if($json = file_get_contents($webservice, false, $context)){
		$response = json_decode($json);
	}
	else{
		

		
		$response = (object)array('responseCode' => false, 'errorMessage' => __('Server failed to respond. Please try again later'));
	}
		
	return $response;
}







function add_comment_change_notice() {	
	
	$comments= get_approved_comments($_GET['post']);
	
	if(count($comments)){
		add_action('admin_notices', 'change_content_warning' );
	}
}


function change_content_warning(){
	?>
	
	<div id="register-form" class="updated error" style="padding: 5px; width: 99% <?php echo $hidethis;?>" >
		
		<?php _e('Warning: There are comments attached to the structure of this page. Changing the structure of this post will break the alignment of comments to their paragraphs'); ?>
		
	</div>
	
	<?php
	
}

function get_header_images(){	
	return do_action('add_header_image');
}












function digressit_core_print_styles(){
	global $current_user, $override_default_theme, $browser, $blog_id;
	
	$options = get_option('digressit');
	
	wp_register_style('digressit.core', get_digressit_media_uri('css/core.css'));

	
	if(strlen($options['custom_style_sheet']) > 10 && esc_url($options['custom_style_sheet'], array('http', 'https'))){
		wp_register_style('digressit.custom', $options['custom_style_sheet']);
	}

	//hacks for IE
	wp_register_style('digressit.ie7', get_digressit_media_uri('css/ie7.css'));
	wp_register_style('digressit.ie8', get_digressit_media_uri('css/ie8.css'));
	wp_enqueue_style('digressit.core');		
	

	if($browser['name'] =='msie' && $browser['version'] == '7.0'){
		wp_enqueue_style('digressit.ie7');				
	}

	if($browser['name'] =='msie' && $browser['version'] == '8.0'){
		wp_enqueue_style('digressit.ie8');				
	}

	wp_enqueue_style('digressit.custom');

	if($blog_id == 1 && file_exists(ABSPATH.'/wp-content/plugins/buddypress/bp-core/bp-core-cssjs.php') && function_exists('bp_core_add_admin_bar_css')){
		if ( defined( 'BP_DISABLE_ADMIN_BAR' ) )
			return false;

		if ( is_multisite()  || is_admin() ) {
			$stylesheet = get_blog_option( BP_ROOT_BLOG, 'stylesheet' );

			if ( file_exists( WP_CONTENT_DIR . '/themes/' . $stylesheet . '/_inc/css/adminbar.css' ) )
				wp_enqueue_style( 'bp-admin-bar', apply_filters( 'bp_core_admin_bar_css', WP_CONTENT_URL . '/themes/' . $stylesheet . '/_inc/css/adminbar.css' ) );
			else
				wp_enqueue_style( 'bp-admin-bar', apply_filters( 'bp_core_admin_bar_css', BP_PLUGIN_URL . '/bp-themes/bp-default/_inc/css/adminbar.css' ) );
		}
	}

}


function custom_digressit_logo(){
	$options = get_option('digressit');

	$css_name = preg_replace("/[^a-zA-Z]/", "", get_bloginfo('name'));
	?>
	<style>

	#<?php echo $css_name; ?>-logo{
		background: url(<?php echo $options['custom_header_image']; ?>) no-repeat;
		height: 100px;
	}
	</style>
	<a href="<?php bloginfo('url'); ?>" ><div id="<?php echo $css_name; ?>-logo"></div></a>	
	<?php
}

function get_root_domain(){
	global $development_mode,$testing_mode, $production_mode;
	$development_mode = false;
	$testing_mode = false;
	$production_mode = false;

	return "http://" . DOMAIN_CURRENT_SITE; //isset($_SERVER['HTTPS']) ? "https://". DOMAIN_CURRENT_SITE : "http://" . DOMAIN_CURRENT_SITE;
}


function digressit_core_print_scripts(){
	global $current_user, $post, $blog_id;
	wp_deregister_script('autosave');
    //wp_deregister_script( 'jquery' );
    //wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js');
    if (!is_admin()) {
        wp_deregister_script( 'jquery' );
        wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js');
    }
    wp_enqueue_script( 'jquery' );


	$options = get_option('digressit');


	$url = parse_url(get_root_domain(). $_SERVER["REQUEST_URI"]);


	if(!is_admin()){
		?>
		<script>	
			var siteurl = '<?php echo get_option("siteurl"); ?>';
			var baseurl = '<?php echo get_root_domain() ?>';
			var user_ID =  <?php echo $current_user->ID; ?>;
			<?php if(is_single()): ?>
			var post_ID = <?php echo $post->ID ?>;
			<?php endif; ?>
			var blog_ID = <?php echo $blog_id; ?>;
			var current_blog_id = <?php echo $blog_id; ?>;
			var request_uri = '<?php echo  esc_url(strip_tags($url['path'])); ?>';
			<?php if(is_single()): ?>
				var is_single = true;
				var post_name = '<?php echo $post->post_name; ?>';
				var allow_general_comments = <?php echo !is_null($options["allow_general_comments"]) ? $options["allow_general_comments"] : 0; ?>;
				var allow_comments_search = <?php echo !is_null($options["allow_comments_search"]) ? $options["allow_comments_search"] : 0; ?>;
				var comment_count = <?php echo count($comment_array); ?>;
				var commment_text_signature = new Array(); 
				var commentbox_function = '<?php echo strlen($options['commentbox_parser']) ? $options['commentbox_parser'] : 'grouping_digressit_commentbox_parser'; ?>';
			<?php else: ?>
				var is_single = false;
			<?php endif; ?>

		</script>	
		<?php
	

		if($options['debug_mode'] == 1){
			wp_enqueue_script('digressit.core',		get_digressit_media_uri('js/digressit.core.js'), 'jquery', false, true );	
			wp_enqueue_script('jquery.easing', 		get_digressit_media_uri('js/jquery.easing.js'), 'jquery', false, true );		
			wp_enqueue_script('jquery.scrollto',	get_digressit_media_uri('js/jquery.scrollTo.js'), 'jquery', false, true );		
			wp_enqueue_script('jquery.cookie',		get_digressit_media_uri('js/jquery.cookie.js'), 'jquery', false, true );		
			wp_enqueue_script('jquery.mousewheel',	get_digressit_media_uri('js/jquery.mousewheel.js'), 'jquery', false, true );		
			wp_enqueue_script('jquery.em',			get_digressit_media_uri('js/jquery.em.js'), 'jquery', false, true );
		}		
		else{
			wp_enqueue_script('digressit.core',		get_digressit_media_uri('js/digressit.core.min.js'), 'jquery', false, true );				
		}
	}
}

?>
