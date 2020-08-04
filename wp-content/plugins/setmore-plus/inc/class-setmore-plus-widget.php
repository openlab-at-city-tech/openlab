<?php
if ( !defined( 'ABSPATH' ) ) die;

class Setmore_Plus_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'setmore_plus_widget',  // base ID
			__( 'Setmore Plus', 'setmore-plus' ),  // name
			array( 'description' => __( 'Add a "Book Appointment" button.', 'setmore-plus' ) )  // args
		);
	}

	/**
	 * Output
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$options = get_option( 'setmoreplus' );

		// Build the widget
		$defaults = array( 'link-text' => $options['link_text'] );
		$data     = array_merge( $args, $instance );
		if ( empty( $data['link-text'] ) ) {
			$data['link-text'] = $defaults['link-text'];
		}

		echo $data['before_widget'];

		// widget title
		if ( !empty( $data['title'] ) ) {
			echo $data['before_title'] . $data['title'] . $data['after_title'];
		}

		// widget text
		if ( !empty( $data['text'] ) ) {
			echo wpautop( $data['text'] );
		}

		// widget link
		$url = apply_filters( 'setmoreplus_url', $options['url'], $data['staff'], $data['lang'] );

		if ( 'button' == $data['style'] ) {
			?>
            <a class="setmore-iframe" href="<?php echo $url; ?>">
                <img border="none" src="<?php echo SETMOREPLUS_URL . 'images/SetMore-book-button.png'; ?>"
                     alt="<?php _e( 'Book an appointment', 'setmore-plus' ); ?>">
            </a>
			<?php
		} elseif ( 'link' == $data['style'] ) {
			wp_enqueue_style( 'setmoreplus-widget-style', SETMOREPLUS_URL . 'css/widget.css' );
			?>
            <a class="setmore setmore-iframe" href="<?php echo $url; ?>">
                <?php _e( $data['link-text'], 'setmore-plus' ); ?>
            </a>
			<?php
		} else {
			?>
            <a class="setmore setmore-iframe" href="<?php echo $url; ?>">
                <?php _e( $data['link-text'], 'setmore-plus' ); ?>
            </a>
			<?php
		}

		echo $data['after_widget'];
	}

	/**
	 * Options form
	 *
	 * @param array $instance
	 * @return void
	 */
	public function form( $instance ) {
		$options   = get_option( 'setmoreplus' );
		$defaults  = array(
			'title'     => '',
			'text'      => '',
			'link-text' => $options['link_text'],
			'style'     => 'button',
			'staff'     => '',
            'lang'      => '',
		);
		$instance  = wp_parse_args( (array)$instance, $defaults );
		$link_text = empty( $instance['link-text'] ) ? $defaults['link-text'] : $instance['link-text'];
		?>
        <script>
          // clicking demo buttons (1) selects radio button and (2) prevents link action
          jQuery(document).ready(function ($) {
            $("a.setmore-admin").click(function (e) {
              $(this).prev("input").attr("checked", "checked").focus();
              e.preventDefault();
            });
          });
        </script>

        <!-- Widget Title -->
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">
				<?php _e( 'Title', 'setmore-plus' ); ?>: <em><?php _e( '(optional)', 'setmore-plus' ); ?></em>
            </label>
            <input id="<?php echo $this->get_field_id( 'title' ); ?>" type="text" class="text widefat"
                   name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>">
        </p>

        <!-- Widget Text -->
        <p>
            <label for="<?php echo $this->get_field_id( 'text' ); ?>">
				<?php _e( 'Text', 'setmore-plus' ); ?>: <em><?php _e( '(optional)', 'setmore-plus' ); ?></em>
            </label>
            <textarea id="<?php echo $this->get_field_id( 'text' ); ?>" class="text widefat"
                      name="<?php echo $this->get_field_name( 'text' ); ?>"
                      rows="3"><?php echo $instance['text']; ?></textarea>
        </p>

        <!-- Link Text -->
        <p>
            <label for="<?php echo $this->get_field_id( 'link-text' ); ?>">
				<?php _e( 'Link Text', 'setmore-plus' ); ?>:
            </label>
            <input id="<?php echo $this->get_field_id( 'link-text' ); ?>" type="text" class="text widefat"
                   name="<?php echo $this->get_field_name( 'link-text' ); ?>"
                   value="<?php echo $instance['link-text']; ?>" placeholder="<?php echo $defaults['link-text']; ?>">
        </p>

        <!-- Style -->
		<?php _e( 'Style', 'setmore-plus' ); ?>:
        <ul class="setmore-style">
            <li>
                <label for="<?php echo $this->get_field_id( 'style-button' ); ?>">
                    <input id="<?php echo $this->get_field_id( 'style-button' ); ?>" type="radio"
                           name="<?php echo $this->get_field_name( 'style' ); ?>"
                           value="button" <?php checked( $instance['style'], 'button' ); ?>>
                    <a class="setmore-admin" href="#">
                        <img style="vertical-align: middle;" border="none"
                             src="<?php echo SETMOREPLUS_URL . 'images/SetMore-book-button.png'; ?>"
                             alt="Book an appointment"></a> <em><?php _e( '(default image)', 'setmore-plus' ); ?></em>
                </label>
            </li>
            <li>
                <label for="<?php echo $this->get_field_id( 'style-link' ); ?>">
                    <input id="<?php echo $this->get_field_id( 'style-link' ); ?>" type="radio"
                           name="<?php echo $this->get_field_name( 'style' ); ?>"
                           value="link" <?php checked( $instance['style'], 'link' ); ?>>
                    <a class="setmore setmore-admin" href="#"><?php echo $link_text; ?></a>
                </label>
            </li>
            <li>
                <label for="<?php echo $this->get_field_id( 'style-none' ); ?>">
                    <input id="<?php echo $this->get_field_id( 'style-none' ); ?>" type="radio"
                           name="<?php echo $this->get_field_name( 'style' ); ?>"
                           value="none" <?php checked( $instance['style'], 'none' ); ?>>
                    <a class="setmore-admin" href="#"><?php echo $link_text; ?></a>
                </label>

                <p><?php _e( "Unstyled.<br>Use CSS class: <code>.widget a.setmore</code>", 'setmore-plus' ); ?></p>
            </li>
        </ul>

        <!-- Booking Page -->
        <p>
			<?php if ( $options['staff_urls'] ) : ?>
                <label for="<?php echo $this->get_field_id( 'staff' ); ?>"><?php _e( 'Booking Page', 'setmore-plus' ); ?>:</label>
                <select class="widefat" id="<?php echo $this->get_field_id( 'staff' ); ?>"
                        name="<?php echo $this->get_field_name( 'staff' ); ?>" >
                    <option value=""><?php _e( 'Main (default)', 'setmore-plus' ); ?></option>
					<?php
					foreach ( $options['staff_urls'] as $staff_id => $staff_info ) {
						printf( '<option value="%s" %s>%s</option>', $staff_id, selected( $staff_id, $instance['staff'] ), $staff_info['name'] );
					}
					?>
                </select>
			<?php else : ?>
                <input type="hidden" name="<?php echo $this->get_field_name( 'staff' ); ?>" value="">
			<?php endif; ?>
        </p>

        <!-- Language -->
        <p>
            <label for="<?php echo $this->get_field_id( 'lang' ); ?>">
                <?php _e( 'Language', 'setmore-plus' ); ?>:
            </label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'lang' ); ?>"
                    name="<?php echo $this->get_field_name( 'lang' ); ?>">
                <option value=""><?php _e( 'preferred language (in Settings)', 'setmore-plus' ); ?></option>
                <?php
                $languages = Setmore_Plus::get_lang();
                foreach ( $languages as $lang => $language ) {
                    printf( '<option value="%s" %s>%s</option>', $lang, selected( $lang, $instance['lang'] ), $language );
                }
                ?>
            </select>
        </p>
		<?php
	}

	/**
	 * Save settings.
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance              = $old_instance;
		$instance['title']     = strip_tags( $new_instance['title'] );
		$instance['text']      = strip_tags( $new_instance['text'] );
		$instance['link-text'] = strip_tags( $new_instance['link-text'] );
		$instance['style']     = $new_instance['style'];
		$instance['staff']     = $new_instance['staff'];
		$instance['lang']      = $new_instance['lang'];

		return $instance;
	}

}
