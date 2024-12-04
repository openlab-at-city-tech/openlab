<?php
if ( ! class_exists( 'Flawless_Blog_Grid_Posts_Widget' ) ) {
	/**
	 * Adds Flawless Blog Grid Posts Widget.
	 */
	class Flawless_Blog_Grid_Posts_Widget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			$grid_posts_widget = array(
				'classname'   => 'widget adore-widget grid-widget',
				'description' => __( 'Retrive Posts List Widgets', 'flawless-blog' ),
			);
			parent::__construct(
				'flawless_blog_grid_posts_widget',
				__( 'Adore Widget: Grid Posts Widget', 'flawless-blog' ),
				$grid_posts_widget
			);
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {
			if ( ! isset( $args['widget_id'] ) ) {
				$args['widget_id'] = $this->id;
			}
			$grid_posts_title       = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';
			$grid_posts_title       = apply_filters( 'widget_title', $grid_posts_title, $instance, $this->id_base );
			$grid_posts_post_offset = isset( $instance['offset'] ) ? absint( $instance['offset'] ) : '';
			$grid_posts_category    = isset( $instance['category'] ) ? absint( $instance['category'] ) : '';

			echo $args['before_widget'];
			?>
			<?php
			if ( ! empty( $grid_posts_title ) ) {
				echo $args['before_title'] . esc_html( $grid_posts_title ) . $args['after_title'];
			}

			?>
			<div class="adore-widget-body">

				<?php
				$grid_posts_widgets_args = array(
					'post_type'      => 'post',
					'posts_per_page' => absint( 4 ),
					'offset'         => absint( $grid_posts_post_offset ),
					'cat'            => absint( $grid_posts_category ),
				);

				$query = new WP_Query( $grid_posts_widgets_args );
				if ( $query->have_posts() ) :
					while ( $query->have_posts() ) :
						$query->the_post();
						?>
						<div class="post-item post-grid">
							<div class="post-item-image">
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail(); ?>							
								</a>
								<div class="read-time-comment">
									<span class="reading-time">
										<i class="far fa-clock"></i>
										<?php
										echo flawless_blog_time_interval( get_the_content() );
										echo esc_html__( ' min read', 'flawless-blog' );
										?>
									</span>
									<span class="comment">
										<i class="far fa-comment"></i>
										<?php echo absint( get_comments_number( get_the_ID() ) ); ?>
									</span>
								</div>
							</div>
							<div class="post-item-content">
								<h3 class="entry-title">
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</h3>  
							</div>
						</div>
						<?php
					endwhile;
					wp_reset_postdata();
				endif;
				?>

			</div>
			<?php
			echo $args['after_widget'];
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {
			$grid_posts_title       = isset( $instance['title'] ) ? $instance['title'] : '';
			$grid_posts_post_offset = isset( $instance['offset'] ) ? absint( $instance['offset'] ) : '';
			$grid_posts_category    = isset( $instance['category'] ) ? absint( $instance['category'] ) : '';
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Section Title:', 'flawless-blog' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $grid_posts_title ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'offset' ) ); ?>"><?php esc_html_e( 'Number of posts to displace or pass over:', 'flawless-blog' ); ?></label>
				<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'offset' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'offset' ) ); ?>" type="number" step="1" min="0" value="<?php echo absint( $grid_posts_post_offset ); ?>" size="3" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php esc_html_e( 'Select the category to show posts:', 'flawless-blog' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>" class="widefat" style="width:100%;">
					<?php
					$categories = flawless_blog_get_post_cat_choices();
					foreach ( $categories as $category => $value ) {
						?>
						<option value="<?php echo absint( $category ); ?>" <?php selected( $grid_posts_category, $category ); ?>><?php echo esc_html( $value ); ?></option>
					<?php } ?>      
				</select>
			</p>
			<?php
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance             = $old_instance;
			$instance['title']    = sanitize_text_field( $new_instance['title'] );
			$instance['offset']   = (int) $new_instance['offset'];
			$instance['category'] = (int) $new_instance['category'];
			return $instance;
		}

	}
}
