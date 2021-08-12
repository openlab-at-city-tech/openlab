<?php
/**
 * Widget to show searched Tweets.
 *
 * @category Core
 * @package  WP to Twitter
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/wp-to-twitter/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP to Twitter Latest Tweets widget class.
 */
class WPT_Search_Tweets_Widget extends WP_Widget {

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Constructor. Set the default widget options and create widget.
	 *
	 * @since 0.1.8
	 */
	function __construct() {

		$this->defaults = array(
			'title'         => '',
			'twitter_num'   => '',
			'search'        => '',
			'result_type'   => 'recent', // mixed, recent, popular.
			'geocode'       => '', // 37.777,-127.98,2km.
			'link_links'    => '',
			'link_mentions' => '',
			'show_images'   => '',
			'link_hashtags' => '',
			'intents'       => '',
			'source'        => '',
		);

		$widget_ops = array(
			'classname'                   => 'wpt-search-tweets',
			'description'                 => __( 'Display a list of tweets returned by a search.', 'wp-to-twitter' ),
			'customize_selective_refresh' => true,
		);

		$control_ops = array(
			'id_base' => 'wpt-search-tweets',
			'width'   => 200,
			'height'  => 250,
		);
		parent::__construct( 'wpt-search-tweets', __( 'WP to Twitter - Searched Tweets', 'wp-to-twitter' ), $widget_ops, $control_ops );
	}

	/**
	 * Echo the widget content.
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	function widget( $args, $instance ) {
		$before_widget = $args['before_widget'];
		$after_widget  = $args['after_widget'];
		$before_title  = $args['before_title'];
		$after_title   = $args['after_title'];

		wp_enqueue_script( 'twitter-platform', 'https://platform.twitter.com/widgets.js' );
		// Merge with defaults.
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		echo $before_widget;
		if ( $instance['title'] ) {
			echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;
		}
		echo wpt_twitter_feed( $instance );
		echo $after_widget;
	}

	/**
	 * Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved/updated.
	 *
	 * @since 0.1
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Settings to save or bool false to cancel saving
	 */
	function update( $new_instance, $old_instance ) {
		// Force the cache to refresh.
		update_option( 'wpt_delete_cache', 'true' );
		$new_instance['title'] = strip_tags( $new_instance['title'] );

		return $new_instance;
	}

	/**
	 * Echo the settings update form.
	 *
	 * @param array $instance Current settings.
	 */
	function form( $instance ) {
		// Merge with defaults.
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'wp-to-twitter' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'search' ); ?>"><?php _e( 'Search String', 'wp-to-twitter' ); ?> :</label>
			<input type="text" id="<?php echo $this->get_field_id( 'search' ); ?>" name="<?php echo $this->get_field_name( 'search' ); ?>" value="<?php echo esc_attr( $instance['search'] ); ?>" class="widefat"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'twitter_num' ); ?>"><?php _e( 'Number of Tweets to Show', 'wp-to-twitter' ); ?> :</label>
			<input type="text" id="<?php echo $this->get_field_id( 'twitter_num' ); ?>" name="<?php echo $this->get_field_name( 'twitter_num' ); ?>" value="<?php echo esc_attr( $instance['twitter_num'] ); ?>" size="3"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'result_type' ); ?>"><?php _e( 'Type of Results', 'wp-to-twitter' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'result_type' ); ?>"  id="<?php echo $this->get_field_id( 'result_type' ); ?>"> <option 	value='recent'<?php echo ( 'recent' === $instance['result_type'] ) ? ' selected="selected"' : ''; ?>><?php _e( 'Recent Tweets', 'wp-to-twitter' ); ?></option> <option 	value='popular'<?php echo ( 'popular' === $instance['result_type'] ) ? ' selected="selected"' : ''; ?>><?php _e( 'Popular Tweets', 'wp-to-twitter' ); ?></option> <option 	value='mixed'<?php echo ( 'mixed' === $instance['result_type'] ) ? ' selected="selected"' : ''; ?>><?php _e( 'Mixed', 'wp-to-twitter' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'geocode' ); ?>"><?php _e( 'Geocode (Latitude,Longitude,Radius)', 'wp-to-twitter' ); ?> :</label>
			<input type="text" id="<?php echo $this->get_field_id( 'geocode' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'geocode' ); ?>" value="<?php echo esc_attr( $instance['geocode'] ); ?>" size="32" placeholder="37.781157,-122.398720,2km"/>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'link_links' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'link_links' ); ?>" value="1" <?php checked( $instance['link_links'] ); ?>/>
			<label for="<?php echo $this->get_field_id( 'link_links' ); ?>"><?php _e( 'Parse links', 'wp-to-twitter' ); ?></label>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'link_mentions' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'link_mentions' ); ?>" value="1" <?php checked( $instance['link_mentions'] ); ?>/>
			<label for="<?php echo $this->get_field_id( 'link_mentions' ); ?>"><?php _e( 'Parse @mentions', 'wp-to-twitter' ); ?></label>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'show_images' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_images' ); ?>" value="1" <?php checked( $instance['show_images'], 1 ); ?>/>
			<label for="<?php echo $this->get_field_id( 'show_images' ); ?>"><?php _e( 'Show Images', 'wp-to-twitter' ); ?></label>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'link_hashtags' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'link_hashtags' ); ?>" value="1" <?php checked( $instance['link_hashtags'] ); ?>/>
			<label for="<?php echo $this->get_field_id( 'link_hashtags' ); ?>"><?php _e( 'Parse #hashtags', 'wp-to-twitter' ); ?></label>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'intents' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'intents' ); ?>" value="1" <?php checked( $instance['intents'] ); ?>/>
			<label for="<?php echo $this->get_field_id( 'intents' ); ?>"><?php _e( 'Include Reply/Retweet/Favorite Links', 'wp-to-twitter' ); ?></label>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'source' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'source' ); ?>" value="1" <?php checked( $instance['source'] ); ?>/>
			<label for="<?php echo $this->get_field_id( 'source' ); ?>"><?php _e( 'Include Tweet source', 'wp-to-twitter' ); ?></label>
		</p>
		<?php
	}
}
