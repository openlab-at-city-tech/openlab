<?php

class ThemeRain_Social extends WP_Widget {

	function __construct() {

		parent::__construct( 'themerain_social', 'ThemeRain Social', array( 'description' => 'Add social media icons.' ) );

	}

	function form( $instance ) {

		$title = empty( $instance['title'] ) ? '' : esc_attr( $instance['title'] );
		$twitter = empty( $instance['twitter'] ) ? '' : esc_attr( $instance['twitter'] );
		$facebook = empty( $instance['facebook'] ) ? '' : esc_attr( $instance['facebook'] );
		$dribbble = empty( $instance['dribbble'] ) ? '' : esc_attr( $instance['dribbble'] );
		$behance = empty( $instance['behance'] ) ? '' : esc_attr( $instance['behance'] );
		$flickr = empty( $instance['flickr'] ) ? '' : esc_attr( $instance['flickr'] );
		$googleplus = empty( $instance['googleplus'] ) ? '' : esc_attr( $instance['googleplus'] );
		$instagram = empty( $instance['instagram'] ) ? '' : esc_attr( $instance['instagram'] );
		$youtube = empty( $instance['youtube'] ) ? '' : esc_attr( $instance['youtube'] );
		$vimeo = empty( $instance['vimeo'] ) ? '' : esc_attr( $instance['vimeo'] );
		$pinterest = empty( $instance['pinterest'] ) ? '' : esc_attr( $instance['pinterest'] );
		$soundcloud = empty( $instance['soundcloud'] ) ? '' : esc_attr( $instance['soundcloud'] );
		$github = empty( $instance['github'] ) ? '' : esc_attr( $instance['github'] );
		$linkedin = empty( $instance['linkedin'] ) ? '' : esc_attr( $instance['linkedin'] );
		$xing = empty( $instance['xing'] ) ? '' : esc_attr( $instance['xing'] );
		$skype = empty( $instance['skype'] ) ? '' : esc_attr( $instance['skype'] );
		$rss = empty( $instance['rss'] ) ? '' : esc_attr( $instance['rss'] );

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Title:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'twitter' ) ); ?>">Twitter:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'twitter' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'twitter' ) ); ?>" type="text" value="<?php echo esc_attr( $twitter ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'facebook' ) ); ?>">Facebook:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'facebook' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'facebook' ) ); ?>" type="text" value="<?php echo esc_attr( $facebook ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'dribbble' ) ); ?>">Dribbble:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'dribbble' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'dribbble' ) ); ?>" type="text" value="<?php echo esc_attr( $dribbble ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'behance' ) ); ?>">Behance:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'behance' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'behance' ) ); ?>" type="text" value="<?php echo esc_attr( $behance ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'flickr' ) ); ?>">Flickr:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'flickr' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'flickr' ) ); ?>" type="text" value="<?php echo esc_attr( $flickr ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'googleplus' ) ); ?>">Google+:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'googleplus' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'googleplus' ) ); ?>" type="text" value="<?php echo esc_attr( $googleplus ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'instagram' ) ); ?>">Instagram:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'instagram' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'instagram' ) ); ?>" type="text" value="<?php echo esc_attr( $instagram ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'youtube' ) ); ?>">YouTube:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'youtube' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'youtube' ) ); ?>" type="text" value="<?php echo esc_attr( $youtube ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'vimeo' ) ); ?>">Vimeo:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'vimeo' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'vimeo' ) ); ?>" type="text" value="<?php echo esc_attr( $vimeo ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'pinterest' ) ); ?>">Pinterest:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'pinterest' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'pinterest' ) ); ?>" type="text" value="<?php echo esc_attr( $pinterest ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'soundcloud' ) ); ?>">SoundCloud:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'soundcloud' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'soundcloud' ) ); ?>" type="text" value="<?php echo esc_attr( $soundcloud ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'github' ) ); ?>">GitHub:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'github' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'github' ) ); ?>" type="text" value="<?php echo esc_attr( $github ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'linkedin' ) ); ?>">LinkedIn:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'linkedin' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'linkedin' ) ); ?>" type="text" value="<?php echo esc_attr( $linkedin ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'xing' ) ); ?>">XING:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'xing' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'xing' ) ); ?>" type="text" value="<?php echo esc_attr( $xing ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'skype' ) ); ?>">Skype:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'skype' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'skype' ) ); ?>" type="text" value="<?php echo esc_attr( $skype ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'rss' ) ); ?>">RSS:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'rss' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'rss' ) ); ?>" type="text" value="<?php echo esc_attr( $rss ); ?>">
		</p>
		<?php

	}

	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['twitter'] = strip_tags( $new_instance['twitter'] );
		$instance['facebook'] = strip_tags( $new_instance['facebook'] );
		$instance['dribbble'] = strip_tags( $new_instance['dribbble'] );
		$instance['behance'] = strip_tags( $new_instance['behance'] );
		$instance['flickr'] = strip_tags( $new_instance['flickr'] );
		$instance['googleplus'] = strip_tags( $new_instance['googleplus'] );
		$instance['instagram'] = strip_tags( $new_instance['instagram'] );
		$instance['youtube'] = strip_tags( $new_instance['youtube'] );
		$instance['vimeo'] = strip_tags( $new_instance['vimeo'] );
		$instance['pinterest'] = strip_tags( $new_instance['pinterest'] );
		$instance['soundcloud'] = strip_tags( $new_instance['soundcloud'] );
		$instance['github'] = strip_tags( $new_instance['github'] );
		$instance['linkedin'] = strip_tags( $new_instance['linkedin'] );
		$instance['xing'] = strip_tags( $new_instance['xing'] );
		$instance['skype'] = strip_tags( $new_instance['skype'] );
		$instance['rss'] = strip_tags( $new_instance['rss'] );
		return $instance;

	}

	function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', $instance['title'] );
		$twitter = $instance['twitter'];
		$facebook = $instance['facebook'];
		$dribbble = $instance['dribbble'];
		$behance = $instance['behance'];
		$flickr = $instance['flickr'];
		$googleplus = $instance['googleplus'];
		$instagram = $instance['instagram'];
		$youtube = $instance['youtube'];
		$vimeo = $instance['vimeo'];
		$pinterest = $instance['pinterest'];
		$soundcloud = $instance['soundcloud'];
		$github = $instance['github'];
		$linkedin = $instance['linkedin'];
		$xing = $instance['xing'];
		$skype = $instance['skype'];
		$rss = $instance['rss'];

		echo $args['before_widget'];
		if ( $title ) echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		if ( $twitter ) echo '<a href="' . esc_url( $twitter ) . '" target="_blank"><i class="fa fa-twitter"></i></a>';
		if ( $facebook ) echo '<a href="' . esc_url( $facebook ) . '" target="_blank"><i class="fa fa-facebook"></i></a>';
		if ( $dribbble ) echo '<a href="' . esc_url( $dribbble ) . '" target="_blank"><i class="fa fa-dribbble"></i></a>';
		if ( $behance ) echo '<a href="' . esc_url( $behance ) . '" target="_blank"><i class="fa fa-behance"></i></a>';
		if ( $flickr ) echo '<a href="' . esc_url( $flickr ) . '" target="_blank"><i class="fa fa-flickr"></i></a>';
		if ( $googleplus ) echo '<a href="' . esc_url( $googleplus ) . '" target="_blank"><i class="fa fa-google-plus"></i></a>';
		if ( $instagram ) echo '<a href="' . esc_url( $instagram ) . '" target="_blank"><i class="fa fa-instagram"></i></a>';
		if ( $youtube ) echo '<a href="' . esc_url( $youtube ) . '" target="_blank"><i class="fa fa-youtube-play"></i></a>';
		if ( $vimeo ) echo '<a href="' . esc_url( $vimeo ) . '" target="_blank"><i class="fa fa-vimeo-square"></i></a>';
		if ( $pinterest ) echo '<a href="' . esc_url( $pinterest ) . '" target="_blank"><i class="fa fa-pinterest-p"></i></a>';
		if ( $soundcloud ) echo '<a href="' . esc_url( $soundcloud ) . '" target="_blank"><i class="fa fa-soundcloud"></i></a>';
		if ( $github ) echo '<a href="' . esc_url( $github ) . '" target="_blank"><i class="fa fa-github"></i></a>';
		if ( $linkedin ) echo '<a href="' . esc_url( $linkedin ) . '" target="_blank"><i class="fa fa-linkedin"></i></a>';
		if ( $xing ) echo '<a href="' . esc_url( $xing ) . '" target="_blank"><i class="fa fa-xing"></i></a>';
		if ( $skype ) echo '<a href="' . esc_url( $skype ) . '" target="_blank"><i class="fa fa-skype"></i></a>';
		if ( $rss ) echo '<a href="' . esc_url( $rss ) . '" target="_blank"><i class="fa fa-rss"></i></a>';
		echo $args['after_widget'];

	}

}

function register_themerain_social() {

    register_widget( 'ThemeRain_Social' );

}
add_action( 'widgets_init', 'register_themerain_social' );