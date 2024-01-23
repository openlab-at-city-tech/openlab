<?php
/**
 * BP Classic Core Login Widget class.
 *
 * @package bp-classic\inc\core\classes
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BuddyPress Login Widget.
 *
 * @since 1.0.0
 */
class BP_Classic_Core_Login_Widget extends WP_Widget {

	/**
	 * Constructor method.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct(
			false,
			_x( '(BuddyPress) Log In', 'Title of the login widget', 'bp-classic' ),
			array(
				'description'                 => __( 'Show a Log In form to logged-out visitors, and a Log Out link to those who are logged in.', 'bp-classic' ),
				'classname'                   => 'widget_bp_core_login_widget buddypress widget',
				'customize_selective_refresh' => true,
				'show_instance_in_rest'       => true,
			)
		);

		if ( is_customize_preview() || bp_is_widget_block_active( '', $this->id_base ) ) {
			add_action( 'bp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		}
	}

	/**
	 * Enqueue assets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		wp_enqueue_style( 'bp-classic-widget-styles' );
	}

	/**
	 * Display the login widget.
	 *
	 * @since 1.0.0
	 *
	 * @see WP_Widget::widget() for description of parameters.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget settings, as saved by the user.
	 */
	public function widget( $args, $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';

		/**
		 * Filters the title of the Login widget.
		 *
		 * @since 1.0.0
		 *
		 * @param string $title    The widget title.
		 * @param array  $instance The settings for the particular instance of the widget.
		 * @param string $id_base  Root ID for all widgets of this type.
		 */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo $args['before_widget'] . $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( is_user_logged_in() ) :
			/**
			 * Fires before the display of widget content if logged in.
			 *
			 * @since 1.0.0
			 */
			do_action( 'bp_before_login_widget_loggedin' );
			?>
			<div class="bp-login-widget-user-avatar">
				<a href="<?php bp_loggedin_user_link(); ?>">
					<?php bp_loggedin_user_avatar( 'type=thumb&width=50&height=50' ); ?>
				</a>
			</div>

			<div class="bp-login-widget-user-links">
				<div class="bp-login-widget-user-link"><a href="<?php echo esc_url( bp_loggedin_user_url() ); ?>"><?php echo esc_html( bp_core_get_user_displayname( bp_loggedin_user_id() ) ); ?></a></div>
				<div class="bp-login-widget-user-logout"><a class="logout" href="<?php echo esc_url( wp_logout_url( bp_get_requested_url() ) ); ?>"><?php esc_html_e( 'Log Out', 'bp-classic' ); ?></a></div>
			</div>
			<?php
			/**
			 * Fires after the display of widget content if logged in.
			 *
			 * @since 1.0.0
			 */
			do_action( 'bp_after_login_widget_loggedin' );

		else :
			/**
			 * Fires before the display of widget content if logged out.
			 *
			 * @since 1.0.0
			 */
			do_action( 'bp_before_login_widget_loggedout' );
			?>

			<form name="bp-login-form" id="bp-login-widget-form" class="standard-form" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post">
				<label for="bp-login-widget-user-login"><?php esc_html_e( 'Username', 'bp-classic' ); ?></label>
				<input type="text" name="log" id="bp-login-widget-user-login" class="input" value="" />

				<label for="bp-login-widget-user-pass"><?php esc_html_e( 'Password', 'bp-classic' ); ?></label>
				<input type="password" name="pwd" id="bp-login-widget-user-pass" class="input" value="" <?php bp_form_field_attributes( 'password' ); ?> />

				<div class="forgetmenot"><label for="bp-login-widget-rememberme"><input name="rememberme" type="checkbox" id="bp-login-widget-rememberme" value="forever" /> <?php esc_html_e( 'Remember Me', 'bp-classic' ); ?></label></div>

				<input type="submit" name="wp-submit" id="bp-login-widget-submit" value="<?php esc_attr_e( 'Log In', 'bp-classic' ); ?>" />

				<?php if ( bp_get_signup_allowed() ) : ?>

					<span class="bp-login-widget-register-link"><a href="<?php echo esc_url( bp_get_signup_page() ); ?>"><?php esc_html_e( 'Register', 'bp-classic' ); ?></a></span>

				<?php endif; ?>

				<?php
				/**
				 * Fires inside the display of the login widget form.
				 *
				 * @since 1.0.0
				 */
				do_action( 'bp_login_widget_form' );
				?>

			</form>

			<?php
			/**
			 * Fires after the display of widget content if logged out.
			 *
			 * @since 1.0.0
			 */
			do_action( 'bp_after_login_widget_loggedout' );

		endif;

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Update the login widget options.
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_instance The new instance options.
	 * @param array $old_instance The old instance options.
	 * @return array $instance The parsed options to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';

		return $instance;
	}

	/**
	 * Output the login widget options form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Settings for this widget.
	 * @return void
	 */
	public function form( $instance = array() ) {
		$settings = bp_parse_args(
			$instance,
			array(
				'title' => '',
			)
		);
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'bp-classic' ); ?>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $settings['title'] ); ?>" /></label>
		</p>
		<?php
	}
}
