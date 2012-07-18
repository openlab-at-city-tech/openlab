<?php
//add_action('wp', 'mainpage');
//add_action('wp_print_styles', 'mainpage_wp_print_styles');
//add_action('wp_print_scripts', 'mainpage_wp_print_scripts' );
add_action('wp', 'mainpage_load');



function mainpage_wp_print_styles(){
?>
<link rel="stylesheet" href="<?php echo get_digressit_media_uri('css/mainpage.css'); ?>" type="text/css" media="screen" />
<?php
}

/*
function mainpage_wp_print_scripts(){
	wp_enqueue_script('digressit.mainpage', get_digressit_media_uri('js/digressit.mainpage.js'), 'jquery', false, true );
}
*/

function mainpage_sidebar_widgets(){
	$options = get_option('digressit');
	if(is_active_sidebar('mainpage-sidebar') && $options['enable_sidebar'] != 0){
		?>
		<div class="sidebar-widgets">
		<div id="dynamic-sidebar" class="sidebar  <?php echo $options['auto_hide_sidebar']; ?> <?php echo $options['sidebar_position']; ?>">		
		<?php
		get_widgets('Mainpage Sidebar');
		?>
		</div>
		</div>
		<?php
	}
	
	
}

function mainpage_load(){
	//var_dump(is_mainpage());
	if(is_mainpage() && !is_single()){
		add_action('add_dynamic_widget', 'mainpage_sidebar_widgets');
	}
}




function mainpage_default_menu(){

	$options = get_option('digressit');
	//$options['front_page_order'] = 'ASC';
	//$options['front_page_order_by'] = 'date';
	
	?>
	<ol class="navigation <?php echo $options['frontpage_list_style']; ?>">

	 <?php
	 global $post;
	 $frontpage_posts = get_posts('numberposts=-1&orderby='.$options['front_page_order_by'].'&order=' . $options['front_page_order']);
	 foreach($frontpage_posts as $pp) :
		$comment_count = get_post_comment_count($pp->ID, null, null, null);
	 ?>
	    <li><a href="<?php echo get_permalink($pp->ID); ?>"><?php echo get_the_title($pp->ID); ?> (<?php echo $comment_count;  ?>)</a></li>
	 <?php endforeach; ?>
	 </ol> 
	<?php mainpage_content_display($frontpage_posts); ?>

<?php
}


global $using_mainpage_nav_walker;

class mainpage_nav_walker extends Walker_Nav_Menu
{
	function start_el(&$output, $item, $depth, $args) {
		global $wp_query, $using_mainpage_nav_walker;		
		$using_mainpage_nav_walker = true;

		//var_dump($item);
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$class_names = $value = '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = '';
		$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
		$item_output = $args->before;
		$item_output .= '<a target="_top"'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '('.get_post_comment_count($item->object_id).')</a>';
		$item_output .= $args->after;
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}

function mainpage_content_display($frontpage_posts){
?>
	<div class="previews">
		<div class="bubblearrow"></div>
		<div class="preview default">
		<?php
		
		$options = get_option('digressit');
		$front_page_content = $options['front_page_content'];
		
		//var_dump($front_page_content);
		if((int)$front_page_content){
			$page = get_post($front_page_content);
			$content = $page->post_content;
			$content = apply_filters('the_content', $content);
			echo (strlen($content)) ? $content : "<p>This introduction can be changed by creating a new page titled \"about\"</p>";
			
		}
		else{
			$pages = get_pages();                                   
			foreach($pages as $key=>$page){ 
				if( $page->post_name == 'about'){
					$content = $page->post_content;
					$content = force_balance_tags(apply_filters('the_content', $content));
					break;
				}
			}
			echo (strlen($content)) ? $content : "<p>This introduction can be changed by creating a new page titled \"about\"</p>";
			
		}
		?>

		</div>

		<?php
			foreach($frontpage_posts as $p) :
			//setup_postdata($post);
		?>
			<div class="preview">
				<?php 
				$p = (array)$p;

			
				if(isset($p['object_id'])){
					$post_id = $p['object_id'];
				}
				else{
					$post_id = $p['ID'];					
				}
				$post_object =  get_post($post_id);
				$content = substr(strip_tags($post_object->post_content), 0 , 500);
				echo "<p>".$content;
				if(strlen($content) > 499){
					echo " [...]";
				}
				echo "</p>";
				
				$comment_count = get_post_comment_count($post_object->ID, null, null, null);
				?>

				<div class="comment-count">
					<?php echo $comment_count ?> Comments
				</div>	
			</div>			
		 <?php endforeach; ?>
		</div>			
	<?php
	
}


?>