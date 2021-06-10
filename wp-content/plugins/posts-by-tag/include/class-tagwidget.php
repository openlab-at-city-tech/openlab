<?php
/**
 * TagWidget Class - Wrapper for the widget
 *
 * @author Sudar
 * @package Posts_By_Tag
 */

class TagWidget extends WP_Widget {
	/**
	 * Constructor
	 */
	function __construct() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'TagWidget', 'description' => __( 'Widget that shows posts from a set of tags', 'posts-by-tag' ) );

		/* Widget control settings. */
		$control_ops = array( 'id_base' => 'tag-widget' );

		/* Create the widget. */
		parent::__construct( 'tag-widget', __( 'Posts By Tag', 'posts-by-tag' ), $widget_ops, $control_ops );
	}

	/**
	 * Handle Widget
	 *
	 * @see WP_Widget::widget
	 * @param unknown $args
	 * @param unknown $instance
	 */
	function widget( $args, $instance ) {
		global $post;

		$post_id           = $post->ID;

		extract( $args );
		$instance          = $this->handle_old_keys( $instance );
		$tags              = $instance['tags'];
		$current_tags      = (bool) $instance['current_tags'];
		$current_page_tags = (bool) $instance['current_page_tags'];
		$current_slug_tags = (bool) $instance['current_slug_tags'];
		$exclude           = (bool) $instance['exclude'];

		$tag_links         = (bool) $instance['tag_links'];
		$disable_cache     = (bool) $instance['disable_cache'];
		$link_target       = $instance['link_target'];

		$title             = $instance['title'];

		if ( $current_page_tags ) {
			// get tags and title from page custom fields

			if ( $post_id > 0 ) {
				Posts_By_Tag::update_postmeta_key( $post_id );
				$posts_by_tag_page_fields = get_post_meta( $post_id, Posts_By_Tag::CUSTOM_POST_FIELD, true );

				if ( isset( $posts_by_tag_page_fields ) && is_array( $posts_by_tag_page_fields ) ) {
					if ( $posts_by_tag_page_fields['widget_title'] != '' ) {
						$title = $posts_by_tag_page_fields['widget_title'];
					}
					if ( '' != $posts_by_tag_page_fields['widget_tags'] ) {
						$tags = $posts_by_tag_page_fields['widget_tags'];
					}
				}
			}
		}

		if ( ( $current_tags || $current_page_tags || $current_slug_tags ) && is_singular() && $post_id > 0 ) {
			$key = "posts-by-tag-$widget_id-$post_id";
		} else {
			$key = "posts-by-tag-$widget_id";
		}

		if ( $disable_cache || ( false === ( $widget_content = get_transient( $key ) ) ) ) {

			$widget_content = get_posts_by_tag( $tags, $instance );

			if ( ! $disable_cache ) {
				// store in cache
				set_transient( $key, $widget_content, 86400 ); // 60*60*24 - 1 Day
			}
		}

		if ( $widget_content != '' ) {
			echo $before_widget;
			echo $before_title;
			echo $title;
			echo $after_title;

			echo $widget_content;
			if ( $tag_links && ! $exclude ) {
				echo Posts_By_Tag_Util::get_tag_more_links( $tags );
			}

			echo $after_widget;
		}
	}

	/**
	 * Handle Widget update
	 *
	 *
	 * @see WP_Widget::update
	 * @param unknown $new_instance
	 * @param unknown $old_instance
	 * @return unknown
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// validate data
		$instance['title']                 = strip_tags( $new_instance['title'] );
		$instance['tags']                  = strip_tags( $new_instance['tags'] );
		$instance['current_tags']          = (bool) $new_instance['current_tags'];
		$instance['current_page_tags']     = (bool) $new_instance['current_page_tags'];
		$instance['current_slug_tags']     = (bool) $new_instance['current_slug_tags'];
		$instance['number']                = intval( $new_instance['number'] );
		$instance['exclude']               = (bool) $new_instance['exclude'];
		$instance['exclude_current_post']  = (bool) $new_instance['exclude_current_post'];
		$instance['thumbnail']             = (bool) $new_instance['thumbnail'];
		$instance['thumbnail_size']        = strip_tags( $new_instance['thumbnail_size'] );
		$instance['thumbnail_size_width']  = intval( $new_instance['thumbnail_size_width'] );
		$instance['thumbnail_size_height'] = intval( $new_instance['thumbnail_size_height'] );
		$instance['author']                = (bool) $new_instance['author'];
		$instance['date']                  = (bool) $new_instance['date'];
		$instance['excerpt']               = (bool) $new_instance['excerpt'];
		$instance['content']               = (bool) $new_instance['content'];
		$instance['order']                 = ( $new_instance['order'] === 'asc' ) ? 'asc' : 'desc';
		$instance['order_by']              = strip_tags( $new_instance['order_by'] );

		if ( $instance['order_by'] == '' ) {
			$instance['order_by'] = 'date';
		}

		$instance['campaign']              = strip_tags( $new_instance['campaign'] );
		$instance['event']                 = strip_tags( $new_instance['event'] );

		$instance['tag_links']             = (bool)$new_instance['tag_links'];
		$instance['link_target']           = $new_instance['link_target'];
		$instance['disable_cache']         = (bool)$new_instance['disable_cache'];

		return $instance;
	}

	/**
	 * Handle Widget Form
	 *
	 * @see WP_Widget::form
	 * @param unknown $instance
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
			'title'                 => '',
			'tags'                  => '',
			'number'                => '5',
			'current_tags'          => false,
			'current_page_tags'     => false,
			'current_slug_tags'     => false,
			'exclude'               => false,
			'exclude_current_post'  => false,
			'thumbnail'             => false,
			'thumbnail_size'        => 'thumbnail',
			'thumbnail_size_width'  => '100',
			'thumbnail_size_height' => '100',
			'author'                => false,
			'date'                  => false,
			'excerpt'               => false,
			'content'               => false,
			'order'                 => 'desc',
			'order_by'              => 'date',
			'campaign'              => '',
			'event'                 => '',
			'tag_links'             => false,
			'link_target'           => false,
			'disable_cache'         => false
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		$title                 = esc_attr( $instance['title'] );
		$tags                  = $instance['tags'];
		$number                = intval( $instance['number'] );
		$current_tags          = (bool) $instance['current_tags'];
		$current_page_tags     = (bool) $instance['current_page_tags'];
		$current_slug_tags     = (bool) $instance['current_slug_tags'];
		$exclude               = (bool) $instance['exclude'];
		$exclude_current_post  = (bool) $instance['exclude_current_post'];
		$thumbnail             = (bool) $instance['thumbnail'];
		$thumbnail_size        = esc_attr( $instance['thumbnail_size'] );
		$thumbnail_size_width  = intval( $instance['thumbnail_size_width'] );
		$thumbnail_size_height = intval( $instance['thumbnail_size_height'] );
		$author                = (bool) $instance['author'];
		$date                  = (bool) $instance['date'];
		$excerpt               = (bool) $instance['excerpt'];
		$content               = (bool) $instance['content'];
		$order                 = ( strtolower( $instance['order'] ) === 'asc' ) ? 'asc' : 'desc';
		$order_by              = strtolower( $instance['order_by'] );

		$campaign              = esc_attr( $instance['campaign'] );
		$event                 = esc_attr( $instance['event'] );

		$tag_links             = (bool) $instance['tag_links'];
		$link_target           = $instance['link_target'];
		$disable_cache         = (bool) $instance['disable_cache'];

		// show/hide logic
		if ( $thumbnail ) {
			$thumbnail_size_style = 'block';
			if ( $thumbnail_size == 'custom' ) {
				$thumbnail_size_custom_style = 'block';
			} else {
				$thumbnail_size_custom_style = 'none';
			}
		} else {
			$thumbnail_size_style = 'none';
		}

		$is_analytics = apply_filters( Posts_By_Tag::FILTER_PRO_ANALYTICS, false );

		// TODO: Use JavaScript to disable mutually exclusive fields
?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'posts-by-tag' ); ?>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></label>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'tags' ); ?>">
            <?php _e( 'Tags:' , 'posts-by-tag' ); ?><br />
                    <input class="widefat" id="<?php echo $this->get_field_id( 'tags' ); ?>" name="<?php echo $this->get_field_name( 'tags' ); ?>" type="text" value="<?php echo $tags; ?>" onfocus ="setSuggest('<?php echo $this->get_field_id( 'tags' ); ?>');" />
            </label><br />
            <?php _e( 'Separate multiple tags by comma', 'posts-by-tag' );?>
		</p>

        <p>
            <label for="<?php echo $this->get_field_id( 'exclude' ); ?>">
            <input type ="checkbox" class ="checkbox" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" value ="true" <?php checked( $exclude, true ); ?> /></label>
            <?php _e( 'Exclude these tags' , 'posts-by-tag' ); ?>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'exclude_current_post' ); ?>">
            <input type ="checkbox" class ="checkbox" id="<?php echo $this->get_field_id( 'exclude_current_post' ); ?>" name="<?php echo $this->get_field_name( 'exclude_current_post' ); ?>" value ="true" <?php checked( $exclude_current_post, true ); ?> /></label>
            <?php _e( 'Exclude current post/page' , 'posts-by-tag' ); ?>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'current_tags' ); ?>">
            <input type ="checkbox" class ="checkbox" id="<?php echo $this->get_field_id( 'current_tags' ); ?>" name="<?php echo $this->get_field_name( 'current_tags' ); ?>" value ="true" <?php checked( $current_tags, true ); ?> /></label>
            <?php _e( 'Get tags from current Post' , 'posts-by-tag' ); ?>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'current_page_tags' ); ?>">
            <input type ="checkbox" class ="checkbox" id="<?php echo $this->get_field_id( 'current_page_tags' ); ?>" name="<?php echo $this->get_field_name( 'current_page_tags' ); ?>" value ="true" <?php checked( $current_page_tags, true ); ?> /></label>
            <?php _e( 'Get tags and title from custom fields. You need to set the custom field for each post/page.' , 'posts-by-tag' ); ?>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'current_slug_tags' ); ?>">
            <input type ="checkbox" class ="checkbox" id="<?php echo $this->get_field_id( 'current_slug_tags' ); ?>" name="<?php echo $this->get_field_name( 'current_slug_tags' ); ?>" value ="true" <?php checked( $current_slug_tags, true ); ?> /></label>
            <?php _e( 'Use current post slug as tag.' , 'posts-by-tag' ); ?>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>">
				<?php _e( 'Number of posts to show:', 'posts-by-tag' ); ?>
            <input style="width: 25px; text-align: center;" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" /></label>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'thumbnail' ); ?>">
            <input type ="checkbox" class ="checkbox" id="<?php echo $this->get_field_id( 'thumbnail' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail' ); ?>" value ="true" <?php checked( $thumbnail, true ); ?> onchange = "thumbnailChanged(<?php echo "'", $this->get_field_id( 'thumbnail' ), "','", $this->get_field_id( 'thumbnail_size' ) , "'" ?>);" ></label>
            <?php _e( 'Show post thumbnails' , 'posts-by-tag' ); ?>
        </p>

        <p style = "display: <?php echo $thumbnail_size_style; ?> ;">
            <label for="<?php echo $this->get_field_id( 'thumbnail_size' ); ?>"><?php _e( 'Select thumbnail size' , 'posts-by-tag' ) ?></label>
            <select id = "<?php echo $this->get_field_id( 'thumbnail_size' ); ?>" name = "<?php echo $this->get_field_name( 'thumbnail_size' ); ?>" onchange = "thumbnailSizeChanged(<?php echo "'", $this->get_field_id( 'thumbnail_size' ), "'"; ?>);">
                <option value = "thumbnail" <?php selected( $thumbnail_size, 'thumbnail' ); ?> ><?php _e( 'Thumbnail', 'posts-by-tag' ); ?></option>
                <option value = "medium" <?php selected( $thumbnail_size, 'medium' ); ?> ><?php _e( 'Medium', 'posts-by-tag' ); ?></option>
                <option value = "large" <?php selected( $thumbnail_size, 'large' ); ?> ><?php _e( 'Large', 'posts-by-tag' ); ?></option>
                <option value = "full" <?php selected( $thumbnail_size, 'full' ); ?> ><?php _e( 'Full', 'posts-by-tag' ); ?></option>
                <option value = "custom" <?php selected( $thumbnail_size, 'custom' ); ?> ><?php _e( 'Custom', 'posts-by-tag' ); ?></option>
            </select>

            <span id = "<?php echo $this->get_field_id( 'thumbnail_size' ); ?>-span" style = "display: <?php echo $thumbnail_size_custom_style; ?> ;">
                <?php _e( 'Custom size:', 'posts-by-tag' ); ?>
                <input style=" text-align: center;" id="<?php echo $this->get_field_id( 'thumbnail_size_width' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail_size_width' ); ?>" size = "4" type="text" value="<?php echo $thumbnail_size_width; ?>" /> x
                <input style=" text-align: center;" id="<?php echo $this->get_field_id( 'thumbnail_size_height' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail_size_height' ); ?>" size = "4" type="text" value="<?php echo $thumbnail_size_height; ?>" />
            </span>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'author' ); ?>">
            <input type ="checkbox" class ="checkbox" id="<?php echo $this->get_field_id( 'author' ); ?>" name="<?php echo $this->get_field_name( 'author' ); ?>" value ="true" <?php checked( $author, true ); ?> /></label>
            <?php _e( 'Show author name' , 'posts-by-tag' ); ?>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'date' ); ?>">
            <input type ="checkbox" class ="checkbox" id="<?php echo $this->get_field_id( 'date' ); ?>" name="<?php echo $this->get_field_name( 'date' ); ?>" value ="true" <?php checked( $date, true ); ?> /></label>
            <?php _e( 'Show post date' , 'posts-by-tag' ); ?>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'excerpt' ); ?>">
            <input type ="checkbox" class ="checkbox" id="<?php echo $this->get_field_id( 'excerpt' ); ?>" name="<?php echo $this->get_field_name( 'excerpt' ); ?>" value ="true" <?php checked( $excerpt, true ); ?> /></label>
				<?php _e( 'Show post excerpt' , 'posts-by-tag' ); ?>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'content' ); ?>">
            <input type ="checkbox" class ="checkbox" id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>" value ="true" <?php checked( $content, true ); ?> /></label>
				<?php _e( 'Show post content' , 'posts-by-tag' ); ?>
        </p>

		<p>
            <?php _e( 'Sort by: ', 'posts-by-tag' ); ?>
            <label for="<?php echo $this->get_field_id( 'order_by' ); ?>">
                <input name="<?php echo $this->get_field_name( 'order_by' ); ?>" type="radio" value="date" <?php checked( $order_by, 'date' ); ?> />
				<?php _e( 'Date', 'posts-by-tag' ); ?>
            </label>
            <label for="<?php echo $this->get_field_id( 'order_by' ); ?>">
                <input name="<?php echo $this->get_field_name( 'order_by' ); ?>" type="radio" value="title" <?php checked( $order_by, 'title' ); ?> />
				<?php _e( 'Title', 'posts-by-tag' ); ?>
            </label>
            <label for="<?php echo $this->get_field_id( 'order_by' ); ?>">
                <input name="<?php echo $this->get_field_name( 'order_by' ); ?>" type="radio" value="rand" <?php checked( $order_by, 'rand' ); ?> />
				<?php _e( 'Random', 'posts-by-tag' ); ?>
            </label>
        </p>

		<p>
            <?php _e( 'Order by: ', 'posts-by-tag' ); ?>
            <label for="<?php echo $this->get_field_id( 'order' ); ?>">
                <input name="<?php echo $this->get_field_name( 'order' ); ?>" type="radio" value="asc" <?php checked( $order, 'asc' ); ?> />
				<?php _e( 'Ascending', 'posts-by-tag' ); ?>
            </label>
            <label for="<?php echo $this->get_field_id( 'order' ); ?>">
                <input name="<?php echo $this->get_field_name( 'order' ); ?>" type="radio" value="desc" <?php checked( $order, 'desc' ); ?> />
				<?php _e( 'Descending', 'posts-by-tag' ); ?>
            </label>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'tag_links' ); ?>">
            <input type ="checkbox" class ="checkbox" id="<?php echo $this->get_field_id( 'tag_links' ); ?>" name="<?php echo $this->get_field_name( 'tag_links' ); ?>" value ="true" <?php checked( $tag_links, true ); ?> /></label>
				<?php _e( 'Show Tag links' , 'posts-by-tag' ); ?>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'link_target' ); ?>">
				<?php _e( 'Target attribute for links', 'posts-by-tag' ); ?>
            <input style="width: 75px; text-align: center;" id="<?php echo $this->get_field_id( 'link_target' ); ?>" name="<?php echo $this->get_field_name( 'link_target' ); ?>" type="text" value="<?php echo $link_target; ?>" /></label>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'disable_cache' ); ?>">
            <input type ="checkbox" class ="checkbox" id="<?php echo $this->get_field_id( 'disable_cache' ); ?>" name="<?php echo $this->get_field_name( 'disable_cache' ); ?>" value ="true" <?php checked( $disable_cache, true ); ?> /></label>
				<?php _e( 'Disable Cache' , 'posts-by-tag' ); ?>
        </p>

        <p class = "pbt-analytics">
            <strong><?php _e( 'Google Analytics Tracking', 'posts-by-tag' ); ?></strong><br>
<?php
		if ( ! $is_analytics ) {
			$disable = 'disabled';
?>
            <span class = "pbt-google-analytics-pro" style = "color:red;"><?php _e( 'Only available in Pro addon.' , 'posts-by-tag' ); ?><a href = "http://sudarmuthu.com/out/buy-posts-by-tag-google-analytics-addon" target = '_blank'>Buy now</a></span>
<?php
		}
?>
            <label for="<?php echo $this->get_field_id( 'campaign' ); ?>">
				<?php _e( 'Campaign code', 'posts-by-tag' ); ?>
                <input type ="text" <?php echo $disable; ?> id="<?php echo $this->get_field_id( 'campaign' ); ?>" name="<?php echo $this->get_field_name( 'campaign' ); ?>" value ="<?php echo $campaign; ?>" style="width:100%;">
            </label>

            <br>

            <label for="<?php echo $this->get_field_id( 'event' ); ?>">
				<?php _e( 'Event code', 'posts-by-tag' ); ?><br>
                <input type ="text" <?php echo $disable; ?> id="<?php echo $this->get_field_id( 'event' ); ?>" name="<?php echo $this->get_field_name( 'event' ); ?>" value ="<?php echo $event; ?>" style="width: 100%;">
            </label>

            <p> <?php _e( 'You can use the following placeholders' , 'posts-by-tag' ) ?> </p>
            <p><?php echo implode( ', ', Posts_By_Tag::$TEMPLATES ); ?></p>
<?php
	}

	/**
	 * Handle old keys
	 *
	 * These new keys were added in 3.1 and are not present in older version.
	 *
	 * @since 3.1
	 * @access private
	 * @param unknown $instance
	 * @return unknown
	 */
	private function handle_old_keys( $instance ) {
		$instance['tag_from_post']              = $instance['current_tags'];
		$instance['tag_from_post_custom_field'] = $instance['current_page_tags'];
		$instance['tag_from_post_slug']         = $instance['current_slug_tags'];

		return $instance;
	}
}
