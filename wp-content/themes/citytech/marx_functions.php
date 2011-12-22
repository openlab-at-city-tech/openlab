<?php
/* Copy paste everything in this file into functions.php before lunch */

/* Members Slider */
function my_init_method() {
    if (!is_admin()) {
        wp_deregister_script( 'jquery' );
        wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js');
        wp_enqueue_script( 'jquery' );
        wp_register_script( 'jcarousellite', get_bloginfo('stylesheet_directory') . '/js/jcarousellite.js');
        wp_enqueue_script( 'jcarousellite' );
        wp_register_script( 'easyaccordion', get_bloginfo('stylesheet_directory') . '/js/easyaccordion.js');
        wp_enqueue_script( 'easyaccordion' );
        wp_register_script( 'utility', get_bloginfo('stylesheet_directory') . '/js/utility.js');
        wp_enqueue_script( 'utility' );
    }
}    
 
add_action('init', 'my_init_method');

function wds_featured_slider_script() { ?>
	<script>
		
		jQuery(document).ready(function() {
			jQuery("#home-new-member-wrap").jCarouselLite({
				btnNext: ".next",
				btnPrev: ".prev",
				vertical: false,
				visible: 1,
				auto:4000,
				speed:200
			});
			
			jQuery("#header #menu-item-40 ul li ul li a").prepend("+ ");
		});
	</script>
<?php }
add_action('wp_head','wds_featured_slider_script');


/* accordion slider */
function easy_accordion_slider_script() { ?>
	
<?php }
//add_action('wp_head','easy_accordion_slider_script');

function easy_accordion_slider() {
	if ( is_home() ) { ?>
		<div id="wds-accordion-slider">
			<dl>
	
				<?php
				/* Query */
				$args = array( 'post_type' => 'slider', 'posts_per_page' => 5 );
				$loop = new WP_Query( $args );
				
				
				/* Start Loop */
				while ( $loop->have_posts() ) : $loop->the_post();
				$tabTitle = get_post_meta(get_the_id(), 'tab_title', true);
				$slideURL = get_post_meta(get_the_id(), 'slide_url', true);
				
					echo '<dt>' . $tabTitle . '</dt>';
					echo '<dd>';
						echo '<a href="' . $slideURL . '" class="image-link">';
						the_post_thumbnail();
						echo '</a>';
						echo '<div class="post-info">';
							echo '<h4 class="slide-title">' . get_the_title() . '</h4>';
							$excerpt = get_the_excerpt();
							echo apply_filters('the_content', $excerpt);
						echo '</div>';
					echo '</dd>';
				endwhile; 
				/* End Loop */
				?>
		
			</dl>
		</div>

		<div class="slider-shadow"></div>
		
	<?php } /* end is_home */
}
add_action('genesis_before_loop', 'easy_accordion_slider' );

/* Accordion Slider Custom Post Type */
add_action('init', 'slider_register');
 
function slider_register() {
 
	$labels = array(
		'name' => _x('Slider', 'post type general name'),
		'singular_name' => _x('Slide', 'post type singular name'),
		'add_new' => _x('Add New', 'slider item'),
		'add_new_item' => __('Add New Slide'),
		'edit_item' => __('Edit Slide'),
		'new_item' => __('New Slide'),
		'view_item' => __('View Slide'),
		'search_items' => __('Search Slider'),
		'not_found' =>  __('Nothing found'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => ''
	);
 
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' => get_stylesheet_directory_uri() . '/article16.png',
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title','editor','thumbnail')
	  ); 
 
	register_post_type( 'slider' , $args );
}

/* Add Meta Box */
add_action("admin_init", "admin_init");
 
function admin_init(){
  add_meta_box("slider-meta", "Slider Custom Meta", "slider_meta", "slider", "side", "low");
}
 
function slider_meta(){
  global $post;
  $custom = get_post_custom($post->ID);
  $tab_title = $custom["tab_title"][0];
  $slide_url = $custom["slide_url"][0];
  ?>
  <p><label for="tab_title"><b>Tab Title:</b></label></p>
  <p><input class="large-text" name="tab_title" type="text" value="<?php echo $tab_title; ?>" /></p>
  <p><label for="slide_url"><b>Slide URL:</b></label></p>
  <p><input class="large-text" name="slide_url" type="text" value="<?php echo $slide_url; ?>" /></p>
  <?php
}

/* Save Post */
add_action('save_post', 'save_details');
function save_details(){
	global $post;
	
	if ( isset( $_POST['tab_title'] ) ) {
		update_post_meta($post->ID, "tab_title", $_POST["tab_title"]);
	}

	if ( isset( $_POST['slide_url'] ) ) {	
		update_post_meta($post->ID, "slide_url", $_POST["slide_url"]);
	}		
}

/* Slider Columns */
add_action("manage_posts_custom_column",  "slider_custom_columns");
add_filter("manage_edit-slider_columns", "slider_edit_columns");
 
function slider_edit_columns($columns){
  $columns = array(
    "cb" => "<input type=\"checkbox\" />",
    "title" => "Slider Title",
    "description" => "Slider Description",
    "tab_title" => "Slide Tab Title",
    "slide_url" => "Slide URL",
  );
 
  return $columns;
}
function slider_custom_columns($column){
  global $post;
 
  switch ($column) {
    case "description":
      the_excerpt();
      break;
    case "tab_title":
      $custom = get_post_custom();
      echo $custom["tab_title"][0];
      break;
    case "slide_url":
      $custom = get_post_custom();
      echo $custom["slide_url"][0];
      break;
  }
}

add_theme_support( 'post-thumbnails' );

// Custom Login
function my_custom_logo() { ?>
	<style type="text/css">
		#login { margin: 50px auto 0 auto; width: 350px; }
		#login h1 a { background: url(<?php bloginfo('stylesheet_directory') ?>/images/logo.png) center no-repeat; height:125px; width: 370px; }
		body { background: #fff }
	</style>
<?php }
add_action('login_head', 'my_custom_logo');
?>