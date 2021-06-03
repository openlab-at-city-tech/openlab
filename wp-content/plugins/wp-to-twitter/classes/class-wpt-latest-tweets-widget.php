<?php
/**
 * Widget to show latest Tweets.
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
class WPT_Latest_Tweets_Widget extends WP_Widget {

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
			'title'                => '',
			'twitter_id'           => '',
			'twitter_num'          => '',
			'twitter_duration'     => '',
			'twitter_hide_replies' => 0,
			'twitter_include_rts'  => 0,
			'link_links'           => '',
			'link_mentions'        => '',
			'link_hashtags'        => '',
			'intents'              => '',
			'source'               => '',
			'show_images'          => '',
			'hide_header'          => 0,
		);

		$widget_ops = array(
			'classname'                   => 'wpt-latest-tweets',
			'description'                 => __( 'Display a list of your latest tweets.', 'wp-to-twitter' ),
			'customize_selective_refresh' => true,
		);

		$control_ops = array(
			'id_base' => 'wpt-latest-tweets',
			'width'   => 200,
			'height'  => 250,
		);
		parent::__construct( 'wpt-latest-tweets', __( 'WP to Twitter - Latest Tweets', 'wp-to-twitter' ), $widget_ops, $control_ops );
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
		/** Merge with defaults */
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
			<label for="<?php echo $this->get_field_id( 'twitter_id' ); ?>"><?php _e( 'Twitter Username', 'wp-to-twitter' ); ?> :</label>
			<input type="text" id="<?php echo $this->get_field_id( 'twitter_id' ); ?>" name="<?php echo $this->get_field_name( 'twitter_id' ); ?>" value="<?php echo esc_attr( $instance['twitter_id'] ); ?>" class="widefat"/>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'hide_header' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'hide_header' ); ?>" value="1" <?php checked( $instance['hide_header'], 1 ); ?>/>
			<label for="<?php echo $this->get_field_id( 'hide_header' ); ?>"><?php _e( 'Hide Widget Header', 'wp-to-twitter' ); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'twitter_num' ); ?>"><?php _e( 'Number of Tweets to Show', 'wp-to-twitter' ); ?> :</label>
			<input type="text" id="<?php echo $this->get_field_id( 'twitter_num' ); ?>" name="<?php echo $this->get_field_name( 'twitter_num' ); ?>" value="<?php echo esc_attr( $instance['twitter_num'] ); ?>" size="3"/>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'twitter_hide_replies' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'twitter_hide_replies' ); ?>" value="1" <?php checked( $instance['twitter_hide_replies'], 1 ); ?>/>
			<label for="<?php echo $this->get_field_id( 'twitter_hide_replies' ); ?>"><?php _e( 'Hide @ Replies', 'wp-to-twitter' ); ?></label>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'twitter_include_rts' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'twitter_include_rts' ); ?>" value="1" <?php checked( $instance['twitter_include_rts'], 1 ); ?>/>
			<label for="<?php echo $this->get_field_id( 'twitter_include_rts' ); ?>"><?php _e( 'Include Retweets', 'wp-to-twitter' ); ?></label>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'link_links' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'link_links' ); ?>" value="1" <?php checked( $instance['link_links'], 1 ); ?>/>
			<label for="<?php echo $this->get_field_id( 'link_links' ); ?>"><?php _e( 'Parse links', 'wp-to-twitter' ); ?></label>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'link_mentions' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'link_mentions' ); ?>" value="1" <?php checked( $instance['link_mentions'], 1 ); ?>/>
			<label for="<?php echo $this->get_field_id( 'link_mentions' ); ?>"><?php _e( 'Parse @mentions', 'wp-to-twitter' ); ?></label>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'show_images' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_images' ); ?>" value="1" <?php checked( $instance['show_images'], 1 ); ?>/>
			<label for="<?php echo $this->get_field_id( 'show_images' ); ?>"><?php _e( 'Show Images', 'wp-to-twitter' ); ?></label>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'link_hashtags' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'link_hashtags' ); ?>" value="1" <?php checked( $instance['link_hashtags'], 1 ); ?>/>
			<label for="<?php echo $this->get_field_id( 'link_hashtags' ); ?>"><?php _e( 'Parse #hashtags', 'wp-to-twitter' ); ?></label>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'intents' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'intents' ); ?>" value="1" <?php checked( $instance['intents'], 1 ); ?>/>
			<label for="<?php echo $this->get_field_id( 'intents' ); ?>"><?php _e( 'Include Reply/Retweet/Favorite Links', 'wp-to-twitter' ); ?></label>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'source' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'source' ); ?>" value="1" <?php checked( $instance['source'], 1 ); ?>/>
			<label for="<?php echo $this->get_field_id( 'source' ); ?>"><?php _e( 'Include Tweet source', 'wp-to-twitter' ); ?></label>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id( 'cache' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'cache' ); ?>" value="1" />
			<label for="<?php echo $this->get_field_id( 'cache' ); ?>"><?php _e( 'Clear cache', 'wp-to-twitter' ); ?></label>
		</p>
		<?php
	}
}
