<?php
if ( ! class_exists( 'Flawless_Blog_Social_Widget' ) ) {
	/**
	 * Adds Flawless Blog Social Widget.
	 */
	class Flawless_Blog_Social_Widget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			$flawless_blog_social_widget = array(
				'classname'   => 'widget adore-widget social-widget style-1',
				'description' => __( 'Retrive Social Widget', 'flawless-blog' ),
			);
			parent::__construct(
				'flawless_blog_social_widget',
				__( 'Adore Widget: Social Widget', 'flawless-blog' ),
				$flawless_blog_social_widget
			);
		}

		/**
		 * Front-end display of widget.

		 * @see WP_Widget::widget()

		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {
			if ( ! isset( $args['widget_id'] ) ) {
				$args['widget_id'] = $this->id;
			}
			$section_title = ! empty( $instance['title'] ) ? $instance['title'] : '';
			$section_title = apply_filters( 'widget_title', $section_title, $instance, $this->id_base );

			echo $args['before_widget'];
			if ( ! empty( $section_title ) ) {
				echo $args['before_title'] . esc_html( $section_title ) . $args['after_title'];
			}
			?>

			<div class="adore-widget-body">
				<div class="social-widgets-wrap author-social-contacts">
					<?php
					for ( $i = 1; $i <= 4; $i++ ) {
						$link = ( ! empty( $instance[ 'link-' . $i ] ) ) ? $instance[ 'link-' . $i ] : '';
						if ( ! empty( $link ) ) :
							?>
							<a href="<?php echo esc_url( $link ); ?>"></a>
							<?php
						endif;
					}
					?>
				</div>
			</div>

			<?php
			echo $args['after_widget'];
		}

		/**
		 * Back-end widget form.

		 * @see WP_Widget::form()

		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {
			$section_title = isset( $instance['title'] ) ? $instance['title'] : '';
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Section Title:', 'flawless-blog' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $section_title ); ?>" />
			</p>
			<?php
			for ( $i = 1; $i <= 4; $i++ ) {
				$link = isset( $instance[ 'link-' . $i ] ) ? $instance[ 'link-' . $i ] : '';
				?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'link-' . $i ) ); ?>"><?php echo sprintf( esc_html__( 'Social Link %d :', 'flawless-blog' ), $i ); ?></label>
					<input type="url" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link-' . $i ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link-' . $i ) ); ?>" value="<?php echo esc_url( $link ); ?>"/>
				</p>
			<?php } ?>

			<?php
		}

		/**
		 * Sanitize widget form values as they are saved.

		 * @see WP_Widget::update()

		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.

		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {

			$instance          = $old_instance;
			$instance['title'] = sanitize_text_field( $new_instance['title'] );
			for ( $i = 1; $i <= 4; $i++ ) {
				$instance[ 'link-' . $i ] = esc_url_raw( $new_instance[ 'link-' . $i ] );
			}
			return $instance;
		}

	}
}
