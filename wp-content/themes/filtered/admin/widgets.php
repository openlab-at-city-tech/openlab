<?php

$ttrust_theme_name = "Filtered";

/* ///////////////////////////////////////////////////////////////////// 
//  Define Widgetized Areas
/////////////////////////////////////////////////////////////////////*/

register_sidebar(array(
	'name' => 'Sidebar',
	'id' => 'sidebar',
	'description' => __('This is the default widget area for the sidebar. This will be displayed if the other sidebars have not been populated with widgets.', 'themetrust'),
	'before_widget' => '<div id="%1$s" class="%2$s sidebarBox widgetBox">',
	'after_widget' => '</div>',
	'before_title' => '<h3>',
	'after_title' => '</h3>'
));

register_sidebar(array(
	'name' => 'Home Sidebar',
	'id' => 'sidebar_home',
	'description' => __('Widget area for the home page sidebar.', 'themetrust'),	
	'before_widget' => '<div id="%1$s" class="%2$s sidebarBox widgetBox">',
	'after_widget' => '</div>',
	'before_title' => '<h3>',
	'after_title' => '</h3>'
));

register_sidebar(array(
	'name' => 'Page Sidebar',
	'id' => 'sidebar_pages',
	'description' => __('Widget area for the sidebar on pages.', 'themetrust'),
	'before_widget' => '<div id="%1$s" class="%2$s sidebarBox widgetBox">',
	'after_widget' => '</div>',
	'before_title' => '<h3>',
	'after_title' => '</h3>'
));

register_sidebar(array(
	'name' => 'Post Sidebar',
	'id' => 'sidebar_posts',
	'description' => __('Widget area for the sidebar on posts.', 'themetrust'),
	'before_widget' => '<div id="%1$s" class="%2$s sidebarBox widgetBox">',
	'after_widget' => '</div>',
	'before_title' => '<h3>',
	'after_title' => '</h3>'
));

register_sidebar(array(
	'name' => 'Footer',
	'id' => 'footer_default',
	'description' => __('This is the default widget area for the footer. This will be displayed if the other footers have not been populated with widgets.', 'themetrust'),
	'before_widget' => '<div id="%1$s" class="oneThird %2$s footerBox widgetBox">',
	'after_widget' => '</div>',
	'before_title' => '<h3>',
	'after_title' => '</h3>'
));

register_sidebar(array(
	'name' => 'Home Page Footer',
	'id' => 'footer_home',
	'description' => __('Widget area for the footer on the home page.', 'themetrust'),
	'before_widget' => '<div id="%1$s" class="oneThird %2$s footerBox widgetBox">',
	'after_widget' => '</div>',
	'before_title' => '<h3>',
	'after_title' => '</h3>'
));

register_sidebar(array(
	'name' => 'Page Footer',
	'id' => 'footer_pages',	
	'description' => __('Widget area for the footer on pages.', 'themetrust'),
	'before_widget' => '<div id="%1$s" class="oneThird %2$s footerBox widgetBox">',
	'after_widget' => '</div>',
	'before_title' => '<h3>',
	'after_title' => '</h3>'
));

register_sidebar(array(
	'name' => 'Post Footer',
	'id' => 'footer_posts',	
	'description' => __('Widget area for the footer on posts.', 'themetrust'),
	'before_widget' => '<div id="%1$s" class="oneThird %2$s footerBox widgetBox">',
	'after_widget' => '</div>',
	'before_title' => '<h3>',
	'after_title' => '</h3>'
));


/* Allow widgets to use shortcodes */
add_filter('widget_text', 'do_shortcode');



/*///////////////////////////////////////////////////////////////////// 
//  Recent Posts
/////////////////////////////////////////////////////////////////////*/

class TTrust_Recent_Posts extends WP_Widget {

	function TTrust_Recent_Posts() {
		global $ttrust_theme_name, $ttrust_version, $options;
		$widget_ops = array('classname' => 'ttrust_recent_posts', 'description' => __('Display recent posts from any category.', 'themetrust'));
		$this->WP_Widget('ttrust_recent_posts', $ttrust_theme_name.' '.__('Recent Posts', 'themetrust'), $widget_ops);
	}

	function widget($args, $instance) {
	
		global $ttrust_theme_name, $options;
	
		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? 'Recent Posts' : $instance['title']);
		if ( !$number = (int) $instance['number'] )
			$number = 10;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 10 )
			$number = 10;
			
		$rp_cat = $instance['rp_cat'];
		$show_post = $instance['show_post'];		 

		$r = new WP_Query(array('cat' => $rp_cat, 'showposts' => $number, 'nopaging' => 0, 'post_status' => 'publish', 'ignore_sticky_posts' => 1));
		if ($r->have_posts()) :
?>		
	
		<?php if($show_post == "true") :?>
			
			<?php $before_widget = str_replace('class="', 'class="oneHalf ' , $before_widget); ?>
			<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
		
			<?php $ttrust_feed = $rp_cat ? get_category_feed_link($rp_cat, '') : of_get_option('ttrust_rss'); ?>
			
		
				<?php $i=1;  while ($r->have_posts()) : $r->the_post(); ?>
				<?php if($i==1) :?>
				<div class="firstPost">					
					<h2><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?> </a></h2>
					<span class="meta"><?php the_time(get_option('date_format')); ?> </span>
					<?php the_excerpt(); ?>					
				</div>
				<?php else : ?>	
				<div class="secondaryPost">					
					<h2><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?> </a></h2>
					<span class="meta"><?php the_time(get_option('date_format')); ?> </span>
				</div>
				
				<?php endif; ?>
				<?php $i++; endwhile; ?>					
			<?php echo $after_widget; ?>
						
		<?php else : ?>
			
			<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
		
			<?php $ttrust_feed = $rp_cat ? get_category_feed_link($rp_cat, '') : of_get_option('ttrust_rss'); ?>
			
		
			<ul class="widgetList">
				<?php  while ($r->have_posts()) : $r->the_post(); ?>
				<li>					
					<h2><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?> </a></h2>
					<span class="meta"><?php the_time(get_option('date_format')); ?> </span>
				</li>
				<?php endwhile; ?>
			</ul>
				
			<?php echo $after_widget; ?>
		
		<?php endif; ?>
<?php
			wp_reset_query();  
		endif;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['rp_cat'] = $new_instance['rp_cat'];
		$instance['show_post'] = $new_instance['show_post'];

		return $instance;
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : 'Recent Posts';
		if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
			$number = 5;
			
		$rp_cat = $instance['rp_cat'];
		$show_post = $instance['show_post'];

		$pn_categories_obj = get_categories('hide_empty=0');
		$pn_categories = array(); ?>

		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'themetrust'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('rp_cat'); ?>"><?php _e('Category', 'themetrust'); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('rp_cat'); ?>" name="<?php echo $this->get_field_name('rp_cat'); ?>">
			<option value=""><?php _e('All', 'themetrust'); ?></option>
			<?php foreach ($pn_categories_obj as $pn_cat) {				
				echo '<option value="'.$pn_cat->cat_ID.'" '.selected($pn_cat->cat_ID, $rp_cat).'>'.$pn_cat->cat_name.'</option>';
			} ?>
		</select></p>
		
		<p><input id="<?php echo $this->get_field_id('show_post'); ?>" name="<?php echo $this->get_field_name('show_post'); ?>" type="checkbox" value="true" <?php if($show_post=="true") echo "checked"; ?>/>
		<label for="<?php echo $this->get_field_id('show_post'); ?>"><?php _e('Show latest post', 'themetrust'); ?></label></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts:', 'themetrust'); ?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /><br />
		<small><?php _e('10 max', 'themetrust'); ?></small></p>
<?php
	}
}

register_widget('TTrust_Recent_Posts');


/*///////////////////////////////////////////////////////////////////// 
//  Twitter
/////////////////////////////////////////////////////////////////////*/


class TTrust_Twitter extends WP_Widget {
 
	function TTrust_Twitter() {
	
		global $ttrust_theme_name, $ttrust_version, $options;
		
        $widget_ops = array('classname' => 'widget_ttrust_twitter', 'description' => 'Display latest tweets.');
		$this->WP_Widget('ttrust_twitter', $ttrust_theme_name.' '.__('Twitter', 'themetrust'), $widget_ops);
    
    }
 
    function widget($args, $instance) {
    
    	global $ttrust_theme_name, $ttrust_version, $options;
       
        extract( $args );
        
        $title	= empty($instance['title']) ? 'Latest Tweets' : $instance['title'];
        $user	= $instance['user'];        
        $label	= empty($instance['twitter_label']) ? 'Follow' : $instance['twitter_label'];
        if ( !$nr = (int) $instance['twitter_count'] )
			$nr = 5;
		else if ( $nr < 1 )
			$nr = 1;
		else if ( $nr > 15 )
			$nr = 15;
 
        ?>
			<?php echo $before_widget; ?>
				<?php echo $before_title . $title . $after_title; ?>
								
				<div id="twitterBox" class="clearfix"></div>

    			<script type="text/javascript">
 					//<![CDATA[
					jQuery(document).ready(function() {
						jQuery("#twitterBox").getTwitter({
							userName: '<?php echo $user; ?>',
							numTweets: '<?php echo $nr; ?>',
							loaderText: "Loading tweets...",
							slideIn: false,
							showHeading: false,
							headingText: "",
							showProfileLink: false
						});
					});
					//]]>    			
    			</script>				
				
				<?php if($label) : ?>
                <p class="twitterLink"><a class="action" href="http://twitter.com/<?php echo $user; ?>"><span><?php echo $label; ?></span></a></p>
                <?php endif; ?>
 
			<?php echo $after_widget; ?>
        <?php
    }

    function update($new_instance, $old_instance) {  
    
    	$instance['title'] = strip_tags($new_instance['title']);
    	$instance['user'] = strip_tags($new_instance['user']);    
    	$instance['twitter_label'] = strip_tags($new_instance['twitter_label']);
    	$instance['twitter_count'] = (int) $new_instance['twitter_count'];
                  
        return $new_instance;
    }
 
    function form($instance) {
    
    	global $ttrust_theme_name, $ttrust_version, $options;
        
		$instance	= wp_parse_args( (array) $instance, array( 'title' => '', 'user' => '', 'twitter_link' => '', 'twitter_label' => '', 'twitter_count' => '') );
		$title		= empty($instance['title']) ? 'Latest Tweets' : $instance['title'];
		$user		= $instance['user'];		
		$label		= $instance['twitter_label'];
		if (!$count = (int) $instance['twitter_count']) $count = 5;
?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'themetrust'); ?>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
			</label>
		</p>		
		<p>
			<label for="<?php echo $this->get_field_id('user'); ?>"><?php _e('Username:', 'themetrust'); ?>
			<input class="widefat" id="<?php echo $this->get_field_id('user'); ?>" name="<?php echo $this->get_field_name('user'); ?>" type="text" value="<?php echo esc_attr($user); ?>" />
			</label>
		</p>		
		<p>
			<label for="<?php echo $this->get_field_id('twitter_count'); ?>"><?php _e('Number of tweets:', 'themetrust'); ?></label>
			<input id="<?php echo $this->get_field_id('twitter_count'); ?>" name="<?php echo $this->get_field_name('twitter_count'); ?>" type="text" value="<?php echo $count; ?>" size="3" /><br />
		</p>		
		<p>
			<label for="<?php echo $this->get_field_id('twitter_label'); ?>"><?php _e('Follow Link label:', 'themetrust'); ?>
			<input class="widefat" id="<?php echo $this->get_field_id('twitter_label'); ?>" name="<?php echo $this->get_field_name('twitter_label'); ?>" type="text" value="<?php echo esc_attr($label); ?>" />
			</label>
		</p>
		
<?php
	}

}
 
register_widget('TTrust_Twitter');




/*///////////////////////////////////////////////////////////////////// 
//  Flickr
/////////////////////////////////////////////////////////////////////*/

class TTrust_Flickr extends WP_Widget {
 
	function TTrust_Flickr() {
		global $ttrust_theme_name, $ttrust_version, $options;
        $widget_ops = array('classname' => 'widget_ttrust_flickr', 'description' => 'Display flickr photos.');
		$this->WP_Widget('ttrust_flickr', $ttrust_theme_name.' '.__('Flickr', 'themetrust'), $widget_ops);
    
    }
 
    function widget($args, $instance) {
    
    	global $options;
        
        extract( $args );
        
        $title	= empty($instance['title']) ? 'Flickr' : apply_filters('widget_title', $instance['title']);
        $user	= empty($instance['user']) ? of_get_option('ttrust_flickr') : $instance['user'];
        
        if ( !$nr = (int) $instance['flickr_nr'] )
			$nr = 6;
		else if ( $nr < 1 )
			$nr = 3;
		else if ( $nr > 15 )
			$nr = 15;
 
        ?>
			<?php echo $before_widget; ?>
				<?php echo $before_title . $title . $after_title; ?>
				
    			<div id="flickrBox" class="clearfix"></div>

    			<script type="text/javascript">
 					//<![CDATA[
					jQuery(document).ready(function($){    			
    					$('#flickrBox').jflickrfeed({
							limit: <?php echo $nr; ?>,
							qstrings: {
								id: '<?php echo $user; ?>'
							},
							itemTemplate:
							'<div class="flickrImage">' +
								'<a href="{{link}}" title="{{title}}">' +
									'<img src="{{image_s}}" alt="{{title}}" />' +
								'</a>' +
							'</div>'
						});
					});
					//]]>    			
    			</script>
 
			<?php echo $after_widget; ?>
        <?php
    }

    function update($new_instance, $old_instance) {  
    
    	$instance['title'] = strip_tags($new_instance['title']);
    	$instance['user'] = strip_tags($new_instance['user']);
    	$instance['flickr_nr'] = (int) $new_instance['flickr_nr'];
                  
        return $new_instance;
    }
 
    function form($instance) {
    
    	global $options;
        
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'user' => '', 'flickr_nr' => '') );
		$title = strip_tags($instance['title']);
		$user = empty($instance['user']) ? of_get_option('ttrust_flickr') : $instance['user'];
		if (!$nr = (int) $instance['flickr_nr']) $nr = 6;
?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'themetrust'); ?>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('user'); ?>"><?php _e('Flickr ID:', 'themetrust'); ?>
			<input class="widefat" id="<?php echo $this->get_field_id('user'); ?>" name="<?php echo $this->get_field_name('user'); ?>" type="text" value="<?php echo esc_attr($user); ?>" />
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('flickr_nr'); ?>"><?php _e('Number of photos:', 'themetrust'); ?></label>
			<input id="<?php echo $this->get_field_id('flickr_nr'); ?>" name="<?php echo $this->get_field_name('flickr_nr'); ?>" type="text" value="<?php echo $nr; ?>" size="3" /><br />
			<small><?php _e('(15 max)'); ?></small>
		</p>
		
<?php
	}

}
 
register_widget('TTrust_Flickr');