<?php
  
// Load main options panel file  
if ( !function_exists( 'optionsframework_init' ) ) {
	define('OPTIONS_FRAMEWORK_URL', TEMPLATEPATH . '/admin/');
	define('OPTIONS_FRAMEWORK_DIRECTORY', get_bloginfo('template_directory') . '/admin/');
	require_once (OPTIONS_FRAMEWORK_URL . 'options-framework.php');
}

// Enable translation
// Translations can be put in the /languages/ directory
load_theme_textdomain( 'themetrust', TEMPLATEPATH . '/languages' );

// Widgets
require_once (TEMPLATEPATH . '/admin/widgets.php');


// Mobile device detection
if( !function_exists('mobile_user_agent_switch') ){
	function is_mobile(){
		$device = '';
 
		if( stristr($_SERVER['HTTP_USER_AGENT'],'ipad') ) {
			$device = "ipad";
		} else if( stristr($_SERVER['HTTP_USER_AGENT'],'iphone') || strstr($_SERVER['HTTP_USER_AGENT'],'iphone') ) {
			$device = "iphone";
		} else if( stristr($_SERVER['HTTP_USER_AGENT'],'blackberry') ) {
			$device = "blackberry";
		} else if( stristr($_SERVER['HTTP_USER_AGENT'],'android') ) {
			$device = "android";
		}
 
		if( $device ) {
			return $device; 
		} return false; {
			return false;
		}
	}
}

// Disable Updates
function ttrust_hidden_theme( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/themes/update-check/1.0/' ) )
		return $r; // Not a theme update request. Bail immediately.
	$themes = unserialize( $r['body']['themes'] );
	unset( $themes[ get_option( 'template' ) ] );
	unset( $themes[ get_option( 'stylesheet' ) ] );
	$r['body']['themes'] = serialize( $themes );
	return $r;
}

add_filter( 'http_request_args', 'ttrust_hidden_theme', 5, 2 );


//////////////////////////////////////////////////////////////
// Theme Header
/////////////////////////////////////////////////////////////
	
add_action('wp_enqueue_scripts', 'ttrust_scripts');

function ttrust_scripts() {	

	wp_enqueue_script('jquery');
	
	wp_enqueue_script('superfish', get_bloginfo('template_url').'/js/superfish.js', array('jquery'), '1.4.8', true);
	
	wp_enqueue_style('superfish', get_bloginfo('template_url').'/css/superfish.css', false, '1.4.8', 'all' );	
	
	if(is_active_widget(false,'','ttrust_flickr')) :	
    	wp_enqueue_script('flickrfeed', get_bloginfo('template_url').'/js/jflickrfeed.js', array('jquery'), '0.8', true);
	endif;
	
	if(is_active_widget(false,'','ttrust_twitter')) :	
    	wp_enqueue_script('jquery_twitter', get_bloginfo('template_url').'/js/jquery.twitter.js', array('jquery'), '1.5', true);
	endif;	
	
	wp_enqueue_script('fitvids', get_bloginfo('template_url').'/js/jquery.fitvids.js', array('jquery'), '1.0', true);
	
	wp_enqueue_script('fittext', get_bloginfo('template_url').'/js/jquery.fittext.js', array('jquery'), '1.0', true);	
	
	wp_enqueue_style('slideshow', get_bloginfo('template_url').'/css/flexslider.css', false, '1.8', 'all' );
	wp_enqueue_script('slideshow', get_bloginfo('template_url').'/js/jquery.flexslider-min.js', array('jquery'), '1.8', true);	
	
	wp_enqueue_script('theme_trust_js', get_bloginfo('template_url').'/js/theme_trust.js', array('jquery'), '1.0', true);	
	
}

add_action('wp_head','ttrust_theme_head');

function ttrust_theme_head() { ?>
<meta name="generator" content="<?php global $ttrust_theme, $ttrust_version; echo $ttrust_theme.' '.$ttrust_version; ?>" />

<style type="text/css" media="screen">

<?php $heading_font = of_get_option('ttrust_heading_font'); ?>
<?php $body_font = of_get_option('ttrust_body_font'); ?>
<?php $call_to_action_font = of_get_option('ttrust_call_to_action_font'); ?>
<?php $banner_main_font = of_get_option('ttrust_banner_main_font'); ?>
<?php $banner_secondary_font = of_get_option('ttrust_banner_secondary_font'); ?>
<?php if ($heading_font) : ?>
	h1, h2, h3, h4, h5, h6 { font-family: '<?php echo $heading_font; ?>'; }
<?php endif; ?>
<?php if ($body_font) : ?>
	body { font-family: '<?php echo $body_font; ?>'; }
<?php endif; ?>
<?php if ($banner_main_font) : ?>
	#homeBanner h2 { font-family: '<?php echo $banner_main_font; ?>'; }
<?php endif; ?>
<?php if ($banner_secondary_font) : ?>
	#homeBanner p { font-family: '<?php echo $banner_secondary_font; ?>'; }
<?php endif; ?>

<?php if(of_get_option('ttrust_banner_text_position')) : ?>
	#bannerText { top: <?php echo(of_get_option('ttrust_banner_text_position')); ?>px; }
<?php endif; ?>

<?php if(of_get_option('ttrust_color_accent')) : ?>
	blockquote, address {
		border-left: 5px solid <?php echo(of_get_option('ttrust_color_accent')); ?>;
	}	
	#filterNav .selected, #filterNav a.selected:hover, #content .project.small .inside {
		background-color: <?php echo(of_get_option('ttrust_color_accent')); ?>;
	}	
<?php endif; ?>

<?php if(of_get_option('ttrust_color_menu')) : ?>#mainNav ul a, #mainNav ul li.sfHover ul a { color: <?php echo(of_get_option('ttrust_color_menu')); ?> !important;	}<?php endif; ?>

<?php if(of_get_option('ttrust_color_menu_hover')) : ?>
	#mainNav ul li.current a,
	#mainNav ul li.current-cat a,
	#mainNav ul li.current_page_item a,
	#mainNav ul li.current-menu-item a,
	#mainNav ul li.current-post-ancestor a,	
	.single-post #mainNav ul li.current_page_parent a,
	#mainNav ul li.current-category-parent a,
	#mainNav ul li.current-category-ancestor a,
	#mainNav ul li.current-portfolio-ancestor a,
	#mainNav ul li.current-projects-ancestor a {
		color: <?php echo(of_get_option('ttrust_color_menu_hover')); ?> !important;		
	}
	#mainNav ul li.sfHover a,
	#mainNav ul li a:hover,
	#mainNav ul li:hover {
		color: <?php echo(of_get_option('ttrust_color_menu_hover')); ?> !important;	
	}
	#mainNav ul li.sfHover ul a:hover { color: <?php echo(of_get_option('ttrust_color_menu_hover')); ?> !important;}	
<?php endif; ?>

<?php if(of_get_option('ttrust_color_link')) : ?>a { color: <?php echo(of_get_option('ttrust_color_link')); ?>;}<?php endif; ?>

<?php if(of_get_option('ttrust_color_link_hover')) : ?>a:hover {color: <?php echo(of_get_option('ttrust_color_link_hover')); ?>;}<?php endif; ?>

<?php if(of_get_option('ttrust_color_btn')) : ?>.button, #searchsubmit, input[type="submit"] {background-color: <?php echo(of_get_option('ttrust_color_btn')); ?> !important;}<?php endif; ?>

<?php if(of_get_option('ttrust_color_btn_hover')) : ?>.button:hover, #searchsubmit:hover, input[type="submit"]:hover {background-color: <?php echo(of_get_option('ttrust_color_btn_hover')); ?> !important;}<?php endif; ?>

<?php if ( is_archive() ): ?> html {height: 101%;} <?php endif; ?>

<?php if(of_get_option('ttrust_color_banner_bkg')) : ?>
	#homeBanner {
		background-color: <?php echo(of_get_option('ttrust_color_banner_bkg')); ?>;
	}	
<?php endif; ?>

<?php $home_banner_img = of_get_option('ttrust_home_banner_img'); ?>
<?php if($home_banner_img) : ?>
	#homeBanner {
		background-image: url(<?php echo $home_banner_img; ?>);		
		background-repeat:no-repeat;
		background-attachment:fixed;
		background-position:center top;			
	}
<?php endif; ?>

<?php echo(of_get_option('ttrust_custom_css')); ?>

</style>

<!--[if IE 7]>
<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/ie7.css" type="text/css" media="screen" />
<![endif]-->
<!--[if IE 8]>
<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/ie8.css" type="text/css" media="screen" />
<![endif]-->
<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

<?php echo "\n".of_get_option('ttrust_analytics')."\n"; ?>

<?php }

add_action('init', 'remheadlink');
function remheadlink() {
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wlwmanifest_link');
}


//////////////////////////////////////////////////////////////
// Custom Background Support
/////////////////////////////////////////////////////////////

add_theme_support( 'custom-background' );


//////////////////////////////////////////////////////////////
// Body Class
/////////////////////////////////////////////////////////////

function ttrust_body_classes($classes) {	
	
	$classes[] = of_get_option('ttrust_background');	
	return $classes;
}
add_filter('body_class','ttrust_body_classes');


//////////////////////////////////////////////////////////////
// Theme Footer
/////////////////////////////////////////////////////////////

add_action('wp_footer','ttrust_footer');

function ttrust_footer() {		
	wp_reset_query(); 	
	global $wp_query;
	global $post;
		
	if ( false !== strpos($post->post_content, '[slideshow') ) {	
		include(TEMPLATEPATH . '/js/slideshow.php');			
	}		
}


//////////////////////////////////////////////////////////////
// Remove
/////////////////////////////////////////////////////////////

// #more from more-link
function ttrust_remove($content) {
	global $id;
	return str_replace('#more-'.$id.'"', '"', $content);
}
add_filter('the_content', 'ttrust_remove');


//////////////////////////////////////////////////////////////
// Custom Excerpt
/////////////////////////////////////////////////////////////

function excerpt_ellipsis($text) {
	return str_replace('[...]', '...', $text);
}
add_filter('the_excerpt', 'excerpt_ellipsis');


//////////////////////////////////////////////////////////////
// Add Excerpt Support for Pages
/////////////////////////////////////////////////////////////

add_post_type_support( 'page', 'excerpt' );


//////////////////////////////////////////////////////////////
// Get Meta Box Value
/////////////////////////////////////////////////////////////

function get_meta_box_vlaue($m) {
	global $wp_query;
	global $post;
	$meta_box_value = get_post_meta($post->ID, $m, true);
	return $meta_box_value;
}

//////////////////////////////////////////////////////////////
// Pagination Styles
/////////////////////////////////////////////////////////////

add_action( 'wp_print_styles', 'ttrust_deregister_styles', 100 );
function ttrust_deregister_styles() {
	wp_deregister_style( 'wp-pagenavi' );
}
remove_action('wp_head', 'pagenavi_css');
remove_action('wp_print_styles', 'pagenavi_stylesheets');


//////////////////////////////////////////////////////////////
// Navigation Menus
/////////////////////////////////////////////////////////////

add_theme_support('menus');
register_nav_menu('main', 'Main Navigation Menu');

function default_nav() {
	echo '<ul class="sf-menu clearfix" >';					
		wp_list_pages('sort_column=menu_order&title_li='); 
	echo '</ul>';
}

//////////////////////////////////////////////////////////////
// Feature Images (Post Thumbnails)
/////////////////////////////////////////////////////////////

add_theme_support('post-thumbnails');

set_post_thumbnail_size(100, 100, true);
add_image_size('ttrust_post_thumb_big', 720, 220, true);
add_image_size('ttrust_post_thumb_small', 150, 150, true);
add_image_size('ttrust_post_thumb_tiny', 50, 50, true);
add_image_size('ttrust_one_third_cropped', 300, 175, true);


//////////////////////////////////////////////////////////////
// Button Shortcode
/////////////////////////////////////////////////////////////

function ttrust_button($a) {
	extract(shortcode_atts(array(
		'label' 	=> 'Button Text',
		'id' 	=> '1',
		'url'	=> '',
		'target' => '_parent',		
		'size'	=> '',
		'ptag'	=> false
	), $a));
	
	$link = $url ? $url : get_permalink($id);	
	
	if($ptag) :
		return  wpautop('<a href="'.$link.'" target="'.$target.'" class="button '.$size.'">'.$label.'</a>');
	else :
		return '<a href="'.$link.'" target="'.$target.'" class="button '.$size.'">'.$label.'</a>';
	endif;
	
}

add_shortcode('button', 'ttrust_button');

//////////////////////////////////////////////////////////////
// Column Shortcodes
/////////////////////////////////////////////////////////////

function ttrust_one_third( $atts, $content = null ) {
   return '<div class="one_third">' . do_shortcode(wpautop($content)) . '</div>';
}
add_shortcode('one_third', 'ttrust_one_third');

function ttrust_one_third_last( $atts, $content = null ) {
   return '<div class="one_third last">' . do_shortcode(wpautop($content)) . '</div><div class="clearboth"></div>';
}
add_shortcode('one_third_last', 'ttrust_one_third_last');

function ttrust_two_third( $atts, $content = null ) {
   return '<div class="two_third">' . do_shortcode(wpautop($content)) . '</div>';
}
add_shortcode('two_third', 'ttrust_two_third');

function ttrust_two_third_last( $atts, $content = null ) {
   return '<div class="two_third last">' . do_shortcode(wpautop($content)) . '</div><div class="clearboth"></div>';
}
add_shortcode('two_third_last', 'ttrust_two_third_last');

function ttrust_one_half( $atts, $content = null ) {
   return '<div class="one_half">' . do_shortcode(wpautop($content)) . '</div>';
}
add_shortcode('one_half', 'ttrust_one_half');

function ttrust_one_half_last( $atts, $content = null ) {
   return '<div class="one_half last">' . do_shortcode(wpautop($content)) . '</div><div class="clearboth"></div>';
}
add_shortcode('one_half_last', 'ttrust_one_half_last');


//////////////////////////////////////////////////////////////
// Slideshow Shortcode
/////////////////////////////////////////////////////////////

function ttrust_slideshow( $atts, $content = null ) {
    $content = str_replace('<br />', '', $content);
	$content = str_replace('<img', '<li><img', $content);
	$content = str_replace('/>', '/></li>', $content);
	return '<div class="flexslider clearfix"><ul class="slides">' . $content . '</ul></div>';
}
add_shortcode('slideshow', 'ttrust_slideshow');

//////////////////////////////////////////////////////////////
// Elastic Video
/////////////////////////////////////////////////////////////

function ttrust_elasticVideo( $atts, $content = null ) {    
	return '<div class="videoContainer">' . $content . '</div>';
}
add_shortcode('elastic-video', 'ttrust_elasticVideo');

//////////////////////////////////////////////////////////////
// Add conainers to all videos
/////////////////////////////////////////////////////////////

function add_video_containers($content) { 
	$content = str_replace('<object', '<div class="videoContainer"><object', $content);
	$content = str_replace('</object>', '</object></div>', $content);
	
	$content = str_replace('<embed', '<div class="videoContainer"><embed', $content);
	$content = str_replace('</embed>', '</embed></div>', $content);
	
	$content = str_replace('<iframe', '<div class="videoContainer"><iframe', $content);
	$content = str_replace('</iframe>', '</iframe></div>', $content);
	
	return $content;
}

add_action('the_content', 'add_video_containers');  

//////////////////////////////////////////////////////////////
// Home Custom Excerpt
/////////////////////////////////////////////////////////////

// Variable & intelligent excerpt length.
function print_excerpt($title) { // Max excerpt length. Length is set in characters
	global $post;

	$rem_len = ""; //clear variable
	$title_len = strlen($title); //get length of title
	$excerpt_line=20;
	if($title_len <= 30){
    	$rem_len=$excerpt_line*8; //calc space remaining for excerpt
	}elseif($title_len <= 34){
    	$rem_len=$excerpt_line*7;
	}elseif($title_len <= 51){
    	$rem_len=$excerpt_line*6;
	}elseif($title_len <= 68){
    	$rem_len=$excerpt_line*5;
	}elseif($title_len <= 85){
    	$rem_len=$excerpt_line*4;
	}

	$text = $post->post_excerpt;
	if ( '' == $text ) {
    	$text = get_the_content('');
    	$text = apply_filters('the_content', $text);
    	$text = str_replace(']]>', ']]>', $text);
	}
	$text = strip_shortcodes($text); // optional, recommended
	$text = strip_tags($text,'<p>'); // use ' $text = strip_tags($text,'<p><a>'); ' if you want to keep some tags

	$text = substr($text,0,$rem_len);
	$excerpt = reverse_strrchr($text, ' ', 1) . "&#0133;";
	if( $excerpt ) {
    	echo apply_filters('the_excerpt',$excerpt);
	} else {
    	echo apply_filters('the_excerpt',$text);
	}
}

// Returns the portion of haystack which goes until the last occurrence of needle
function reverse_strrchr($haystack, $needle, $trail) {
    return strrpos($haystack, $needle) ? substr($haystack, 0, strrpos($haystack, $needle) + $trail) : false;
}


//////////////////////////////////////////////////////////////
// Custom More Link
/////////////////////////////////////////////////////////////

function more_link() {
	global $post;	
	$more_link = '<p class="moreLink"><a href="'.get_permalink().'" title="'.get_the_title().'">';
	$more_link .= '<span>'.__('Read More', 'themetrust').'</span>';
	$more_link .= '</a></p>';
	echo $more_link;	
}

//////////////////////////////////////////////////////////////
// Custom Sanitize for Theme Options
/////////////////////////////////////////////////////////////

add_action('admin_init','optionscheck_change_santiziation', 100);
 

function optionscheck_change_santiziation() {
    remove_filter( 'of_sanitize_textarea', 'of_sanitize_textarea' );
    add_filter( 'of_sanitize_textarea', 'custom_sanitize_textarea' );
}
 
function custom_sanitize_textarea($input) {
    global $allowedposttags;
    
      $custom_allowedtags["script"] = array();
 
      $custom_allowedtags = array_merge($custom_allowedtags, $allowedposttags);
      $output = wp_kses( $input, $custom_allowedtags);
    return $output;
}


//////////////////////////////////////////////////////////////
// Custom Post Types and Custom Taxonamies
/////////////////////////////////////////////////////////////

add_action( 'init', 'create_post_types' );

function create_post_types() {
	
	$labels = array(
		'name' => __( 'Projects' ),
		'singular_name' => __( 'Project' ),
		'add_new' => __( 'Add New' ),
		'add_new_item' => __( 'Add New Project' ),
		'edit' => __( 'Edit' ),
		'edit_item' => __( 'Edit Project' ),
		'new_item' => __( 'New Project' ),
		'view' => __( 'View Project' ),
		'view_item' => __( 'View Project' ),
		'search_items' => __( 'Search Projects' ),
		'not_found' => __( 'No projects found' ),
		'not_found_in_trash' => __( 'No projects found in Trash' ),
		'parent' => __( 'Parent Project' ),
	);
	
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,		
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title', 'editor', 'thumbnail', 'comments', 'revisions', 'excerpt')
	); 	
	
	register_post_type( 'project' , $args );
	flush_rewrite_rules( false );
}

add_action( 'init', 'create_taxonomies' );
function create_taxonomies() {
	$labels = array(
    	'name' => __( 'Skills' ),
    	'singular_name' => __( 'Skill' ),
    	'search_items' =>  __( 'Search Skills' ),
    	'all_items' => __( 'All Skills' ),
    	'parent_item' => __( 'Parent Skill' ),
    	'parent_item_colon' => __( 'Parent Skill:' ),
    	'edit_item' => __( 'Edit Skill' ),
    	'update_item' => __( 'Update Skill' ),
    	'add_new_item' => __( 'Add New Skill' ),
    	'new_item_name' => __( 'New Skill Name' )
  	); 	

  	register_taxonomy('skill','project',array(
    	'hierarchical' => false,
    	'labels' => $labels
  	));
	flush_rewrite_rules( false );
}

// List custom post type taxonomies

function ttrust_get_terms( $id = '' ) {
  global $post;

  if ( empty( $id ) )
    $id = $post->ID;

  if ( !empty( $id ) ) {
    $post_taxonomies = array();
    $post_type = get_post_type( $id );
    $taxonomies = get_object_taxonomies( $post_type , 'names' );

    foreach ( $taxonomies as $taxonomy ) {
      $term_links = array();
      $terms = get_the_terms( $id, $taxonomy );

      if ( is_wp_error( $terms ) )
        return $terms;

      if ( $terms ) {
        foreach ( $terms as $term ) {
          $link = get_term_link( $term, $taxonomy );
          if ( is_wp_error( $link ) )
            return $link;
          $term_links[] = '<li><span><a href="'.$link.'">' . $term->name . '</a></span></li>';
        }
      }

      $term_links = apply_filters( "term_links-$taxonomy" , $term_links );
      $post_terms[$taxonomy] = $term_links;
    }
    return $post_terms;
  } else {
    return false;
  }
}

function ttrust_get_terms_list( $id = '' , $echo = true ) {
  global $post;

  if ( empty( $id ) )
    $id = $post->ID;

  if ( !empty( $id ) ) {
    $my_terms = ttrust_get_terms( $id );
    if ( $my_terms ) {
      $my_taxonomies = array();
      foreach ( $my_terms as $taxonomy => $terms ) {
        $my_taxonomy = get_taxonomy( $taxonomy );
        if ( !empty( $terms ) ) $my_taxonomies[] = implode( $terms);
      }

      if ( !empty( $my_taxonomies ) ) {
	    $output = "";
        foreach ( $my_taxonomies as $my_taxonomy ) {
          $output .= $my_taxonomy . "\n";
        }        
      }

      if ( $echo )
        if(isset($output)) echo $output;
      else
        if(isset($output)) return $output;
    } else {
      return;
    }
  } else {
    return false;
  }
}





//////////////////////////////////////////////////////////////
// Meta Box
/////////////////////////////////////////////////////////////

$prefix = "_ttrust_";

$project_details = array(		

		"url" => array(
    	"type" => "textfield",
		"name" => $prefix."url",
    	"std" => "",
    	"title" => __('URL','themetrust'),
    	"description" => __('Enter the URL of your project.','themetrust')),

		"url_label" => array(
		"type" => "textfield",
		"name" => $prefix."url_label",
		"std" => "",
		"title" => __('URL Label','themetrust'),
		"description" => __('Enter a label for the URL.','themetrust'))		
);

$page_options = array(	
		"description" => array(
    	"type" => "textarea",
		"name" => $prefix."page_description",
    	"std" => "",
    	"title" => __('Description','themetrust'),
    	"description" => __('Enter a description about this page.','themetrust'))		
);

$portfolio_options = array(	
		"notes" => array(
    	"type" => "textarea",
		"name" => $prefix."page_skills",
    	"std" => "",
    	"title" => __('Skills','themetrust'),
    	"description" => __('For use with the Portfolio page template. <br/><br/>Enter the names of the skills (separated by commas) you want shown on this page. If left blank, all skills will be used.','themetrust'))
);

$home_feature_options = array(
		"featured" => array(
    	"type" => "checkbox",
		"name" => $prefix."featured",
    	"std" => "",
    	"title" => __('Feature on Home','themetrust'),
    	"description" => __('Check this box to feature this on the home page.','themetrust'))
);


$meta_box_groups = array($project_details, $page_options, $portfolio_options, $home_feature_options);

function new_meta_box($post, $metabox) {	
	
	$meta_boxes_inputs = $metabox['args']['inputs'];

	foreach($meta_boxes_inputs as $meta_box) {
	
		$meta_box_value = get_post_meta($post->ID, $meta_box['name'].'_value', true);
		if($meta_box_value == "") $meta_box_value = $meta_box['std'];
		
		echo'<div class="meta-field">';
		
		echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
		
		echo'<p><strong>'.$meta_box['title'].'</strong></p>';
		
		if(isset($meta_box['type']) && $meta_box['type'] == 'checkbox') {
		
			if($meta_box_value == 'true') {
				$checked = "checked=\"checked\"";
			} elseif($meta_box['std'] == "true") {	
					$checked = "checked=\"checked\"";	
			} else {
					$checked = "";
			}
		
			echo'<p class="clearfix"><input type="checkbox" class="meta-radio" name="'.$meta_box['name'].'_value" id="'.$meta_box['name'].'_value" value="true" '.$checked.' /> ';		
			echo'<label for="'.$meta_box['name'].'_value">'.$meta_box['description'].'</label></p><br />';		
		
		} elseif(isset($meta_box['type']) && $meta_box['type'] == 'textarea')  {			
			
			echo'<textarea rows="4" style="width:98%" name="'.$meta_box['name'].'_value" id="'.$meta_box['name'].'_value">'.$meta_box_value.'</textarea><br />';			
			echo'<p><label for="'.$meta_box['name'].'_value">'.$meta_box['description'].'</label></p><br />';			
		
		} else {
			
			echo'<input style="width:70%"type="text" name="'.$meta_box['name'].'_value" id="'.$meta_box['name'].'_value" value="'.$meta_box_value.'" /><br />';		
			echo'<p><label for="'.$meta_box['name'].'_value">'.$meta_box['description'].'</label></p><br />';			
		
		}
		
		echo'</div>';
		
	} // end foreach
		
	echo'<br style="clear:both" />';
	
} // end meta boxes

function create_meta_box() {	
	global $project_details, $page_options, $portfolio_options, $home_feature_options;	
	
	if ( function_exists('add_meta_box') ) {
		add_meta_box( 'new-meta-boxes-details', __('Project Options','themetrust'), 'new_meta_box', 'project', 'normal', 'high', array('inputs'=>$project_details) );				
		add_meta_box( 'new-meta-boxes-page-options', __('Page Options','themetrust'), 'new_meta_box', 'page', 'side', 'low', array('inputs'=>$page_options) );	
		add_meta_box( 'new-meta-boxes-portfolio-options', __('Portfolio Options','themetrust'), 'new_meta_box', 'page', 'side', 'low', array('inputs'=>$portfolio_options) );		
		add_meta_box( 'new-meta-boxes-home-feature', __('Home Feature Options','themetrust'), 'new_meta_box', 'project', 'normal', 'high', array('inputs'=>$home_feature_options) );
		add_meta_box( 'new-meta-boxes-home-feature', __('Home Feature Options','themetrust'), 'new_meta_box', 'page', 'normal', 'high', array('inputs'=>$home_feature_options) );
	}
}



function save_postdata( $post_id ) {
global $post, $new_meta_boxes, $meta_box_groups;

if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
	return $post_id;
}

if( defined('DOING_AJAX') && DOING_AJAX ) { //Prevents the metaboxes from being overwritten while quick editing.
	return $post_id;
}

if( ereg('/\edit\.php', $_SERVER['REQUEST_URI']) ) { //Detects if the save action is coming from a quick edit/batch edit.
	return $post_id;
}

foreach($meta_box_groups as $group) {
	foreach($group as $meta_box) {

		// Verify
		if(isset($_POST[$meta_box['name'].'_noncename'])){
			if ( !wp_verify_nonce( $_POST[$meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
				return $post_id;
			}
		}

		if ( isset($_POST['post_type']) && 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ))
				return $post_id;
		} else {
			if ( !current_user_can( 'edit_post', $post_id ))
				return $post_id;
		}

		$data = "";
		if(isset($_POST[$meta_box['name'].'_value'])){
			$data = $_POST[$meta_box['name'].'_value'];
		}


		if(get_post_meta($post_id, $meta_box['name'].'_value') == "") 
			add_post_meta($post_id, $meta_box['name'].'_value', $data, true);
		elseif($data != get_post_meta($post_id, $meta_box['name'].'_value', true))
			update_post_meta($post_id, $meta_box['name'].'_value', $data);
		elseif($data == "" || $data == $meta_box['std'] )
			delete_post_meta($post_id, $meta_box['name'].'_value', get_post_meta($post_id, $meta_box['name'].'_value', true));
	
		} // end foreach
	} // end foreach
} // end save_postdata

add_action('admin_menu', 'create_meta_box');
add_action('save_post', 'save_postdata');



//////////////////////////////////////////////////////////////
// Comments
/////////////////////////////////////////////////////////////

function ttrust_comments($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>		
	<li id="li-comment-<?php comment_ID() ?>">		
		
		<div class="comment <?php echo get_comment_type(); ?>" id="comment-<?php comment_ID() ?>">						
			
			<?php echo get_avatar($comment,'60',get_bloginfo('template_url').'/images/default_avatar.png'); ?>			
   	   			
   	   		<h5><?php comment_author_link(); ?></h5>
			<span class="date"><?php comment_date(); ?></span>
				
			<?php if ($comment->comment_approved == '0') : ?>
				<p><span class="message"><?php _e('Your comment is awaiting moderation.', 'themetrust'); ?></span></p>
			<?php endif; ?>
				
			<?php comment_text() ?>				
				
			<?php
			if(get_comment_type() != "trackback")
				comment_reply_link(array_merge( $args, array('add_below' => 'comment','reply_text' => '<span>'. __('Reply', 'themetrust') .'</span>', 'login_text' => '<span>'. __('Log in to reply', 'themetrust') .'</span>', 'depth' => $depth, 'max_depth' => $args['max_depth'])))
			
			?>
				
		</div><!-- end comment -->
			
<?php
}

function ttrust_pings($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
		<li class="comment" id="comment-<?php comment_ID() ?>"><?php comment_author_link(); ?> - <?php comment_excerpt(); ?>
<?php
}
?>