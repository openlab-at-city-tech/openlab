<?php

namespace Advanced_Sidebar_Menu\Widget;

use Advanced_Sidebar_Menu\Menus\Menu_Abstract;

/**
 * Advanced_Sidebar_Menu_Widgets_Page
 *
 * Parent child menu based on pages.
 *
 * @author OnPoint Plugins
 * @since  7.0.0
 */
class Page extends Widget_Abstract {
	const NAME = 'advanced_sidebar_menu';

	const TITLE                    = Menu_Abstract::TITLE;
	const INCLUDE_PARENT           = Menu_Abstract::INCLUDE_PARENT;
	const INCLUDE_CHILDLESS_PARENT = Menu_Abstract::INCLUDE_CHILDLESS_PARENT;
	const ORDER_BY                 = Menu_Abstract::ORDER_BY;
	const EXCLUDE                  = Menu_Abstract::EXCLUDE;
	const DISPLAY_ALL              = Menu_Abstract::DISPLAY_ALL;
	const LEVELS                   = Menu_Abstract::LEVELS;

	/**
	 * Default values for the widget.
	 *
	 * @var array
	 */
	protected static $defaults = [
		self::TITLE                    => false,
		self::INCLUDE_PARENT           => false,
		self::INCLUDE_CHILDLESS_PARENT => false,
		self::ORDER_BY                 => 'menu_order',
		self::EXCLUDE                  => false,
		self::DISPLAY_ALL              => false,
		self::LEVELS                   => 100,
	];


	/**
	 * Register the widget.
	 */
	public function __construct() {
		$widget_ops = [
			'classname'   => 'advanced-sidebar-menu',
			'description' => __( 'Creates a menu of all the pages using the child/parent relationship', 'advanced-sidebar-menu' ),
		];
		$control_ops = [
			'width' => wp_is_mobile() ? false : 620,
		];

		parent::__construct( self::NAME, __( 'Advanced Sidebar Pages Menu', 'advanced-sidebar-menu' ), $widget_ops, $control_ops );

		$this->hook();
	}


	/**
	 * Add the sections to the widget via actions.
	 *
	 * @notice Anything using the column actions must use the $widget class passed
	 *         via do_action instead of $this
	 *
	 * @return void
	 */
	protected function hook() {
		static $hooked;
		if ( null !== $hooked ) {
			return;
		}
		$hooked = true;

		add_action( 'advanced-sidebar-menu/widget/page/left-column', [ $this, 'box_display' ], 5, 2 );
		add_action( 'advanced-sidebar-menu/widget/page/left-column', [ $this, 'box_order' ], 15, 2 );
		add_action( 'advanced-sidebar-menu/widget/page/left-column', [ $this, 'box_exclude' ], 20, 2 );
	}


	/**
	 * Get the label for used post type.
	 *
	 * For adjusting widget option labels.
	 *
	 * @param array $instance - Widget settings.
	 * @param bool  $single   - Singular label or plural.
	 *
	 * @since 8.2.0
	 *
	 * @return mixed
	 */
	public function get_post_type_label( $instance, $single = true ) {
		$post_type = get_post_type_object( apply_filters( 'advanced-sidebar-menu/widget/page/post-type-for-label', 'page', $this->control_options, $instance ) );
		if ( null === $post_type ) {
			$post_type = get_post_type_object( 'page' ); // Sensible fallback.
		}

		return $single ? $post_type->labels->singular_name : $post_type->labels->name;
	}


	/**
	 * Display options.
	 *
	 * @param array           $instance - Widget settings.
	 * @param Widget_Abstract $widget   - Registered widget arguments.
	 *
	 * @return void
	 */
	public function box_display( array $instance, $widget ) {
		?>
		<div class="advanced-sidebar-menu-column-box">
			<p>
				<?php $widget->checkbox( self::INCLUDE_PARENT ); ?>
				<label>
					<?php
					/* translators: Selected post type single label */
					printf( esc_html__( 'Display highest level parent %s', 'advanced-sidebar-menu' ), esc_html( strtolower( $this->get_post_type_label( $instance ) ) ) );
					?>
				</label>
			</p>


			<p>
				<?php $widget->checkbox( self::INCLUDE_CHILDLESS_PARENT ); ?>
				<label>
					<?php
					/* translators: Selected post type single label */
					printf( esc_html__( 'Display menu when there is only the parent %s', 'advanced-sidebar-menu' ), esc_html( strtolower( $this->get_post_type_label( $instance ) ) ) );
					?>
				</label>
			</p>

			<p>
				<?php $widget->checkbox( self::DISPLAY_ALL, self::LEVELS ); ?>
				<label>
					<?php
					/* translators: Selected post type plural label */
					printf( esc_html__( 'Always display child %s', 'advanced-sidebar-menu' ), esc_html( strtolower( $this->get_post_type_label( $instance, false ) ) ) );
					?>
				</label>
			</p>

			<div
				<?php
				if ( apply_filters( 'advanced-sidebar-menu/widget/page/hide-levels-field', true ) ) {
					$widget->hide_element( self::DISPLAY_ALL, self::LEVELS );
				}
				?>
			>
				<p>
					<label for="<?php echo esc_attr( $widget->get_field_id( self::LEVELS ) ); ?>">
						<?php
						ob_start();
						?>
						<select
							id="<?php echo esc_attr( $widget->get_field_id( self::LEVELS ) ); ?>"
							name="<?php echo esc_attr( $widget->get_field_name( self::LEVELS ) ); ?>">
							<option value="100">
								<?php esc_html_e( '- All -', 'advanced-sidebar-menu' ); ?>
							</option>
							<?php
							for ( $i = 1; $i < 10; $i ++ ) {
								?>
								<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $i, (int) $instance[ self::LEVELS ] ); ?>>
									<?php echo (int) $i; ?>
								</option>
								<?php
							}
							?>
						</select>
						<?php
						/* translators: {select html input}, {Selected post type plural label} */
						printf( esc_html__( 'Display %1$s levels of child %2$s', 'advanced-sidebar-menu' ), ob_get_clean(), esc_html( strtolower( $this->get_post_type_label( $instance, false ) ) ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</label>
				</p>
			</div>

			<?php do_action( 'advanced-sidebar-menu/widget/page/display-box', $instance, $widget ); ?>

		</div>
		<?php
	}


	/**
	 * Order options.
	 *
	 * @param array           $instance - Widget settings.
	 * @param Widget_Abstract $widget   - Registered widget arguments.
	 *
	 * @return void
	 */
	public function box_order( array $instance, $widget ) {
		?>
		<div class="advanced-sidebar-menu-column-box">

			<p>
				<label for="<?php echo esc_attr( $widget->get_field_id( self::ORDER_BY ) ); ?>">
					<?php esc_html_e( 'Order by', 'advanced-sidebar-menu' ); ?>
				</label>
				<select
					id="<?php echo esc_attr( $widget->get_field_id( self::ORDER_BY ) ); ?>"
					name="<?php echo esc_attr( $widget->get_field_name( self::ORDER_BY ) ); ?>">
					<?php
					$order_by = (array) apply_filters(
						'advanced-sidebar-menu/widget/page/order-by-options',
						[
							'menu_order' => 'Page Order',
							'post_title' => 'Title',
							'post_date'  => 'Published Date',
						]
					);

					foreach ( $order_by as $key => $order ) {
						printf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $instance[ self::ORDER_BY ], $key, false ), esc_html( $order ) );
					}
					?>
				</select>
			</p>
			<?php do_action( 'advanced-sidebar-menu/widget/page/order-box', $instance, $widget ); ?>

		</div>
		<?php
	}


	/**
	 * Exclude options.
	 *
	 * @param array           $instance - Widget settings.
	 * @param Widget_Abstract $widget   - Registered widget arguments.
	 *
	 * @return void
	 */
	public function box_exclude( array $instance, $widget ) {
		?>
		<div class="advanced-sidebar-menu-column-box">
			<p>
				<label for="<?php echo esc_attr( $widget->get_field_id( self::EXCLUDE ) ); ?>">
					<?php
					/* translators: Selected post type plural label */
					printf( esc_html__( '%s to exclude (ids, comma separated)', 'advanced-sidebar-menu' ), esc_html( $this->get_post_type_label( $instance, false ) ) );
					?>
				</label>
				<input
					id="<?php echo esc_attr( $widget->get_field_id( self::EXCLUDE ) ); ?>"
					name="<?php echo esc_attr( $widget->get_field_name( self::EXCLUDE ) ); ?>"
					class="widefat advanced-sidebar-menu-block-field"
					type="text"
					value="<?php echo esc_attr( $instance[ self::EXCLUDE ] ); ?>" />
				<?php
				do_action( 'advanced-sidebar-menu/widget/page/exclude-box', $instance, $widget );
				?>
			</p>
		</div>
		<?php
	}


	/**
	 * Widget form.
	 *
	 * @param array $instance - Widget settings.
	 *
	 * @since 7.2.1
	 *
	 * @return void
	 */
	public function form( $instance ) {
		$instance = $this->set_instance( $instance, self::$defaults );
		do_action( 'advanced-sidebar-menu/widget/page/before-form', $instance, $this );
		?>
		<p xmlns="http://www.w3.org/1999/html">
			<label for="<?php echo esc_attr( $this->get_field_id( self::TITLE ) ); ?>">
				<?php esc_html_e( 'Title', 'advanced-sidebar-menu' ); ?>:
			</label>
			<input
				id="<?php echo esc_attr( $this->get_field_id( self::TITLE ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( self::TITLE ) ); ?>"
				class="widefat"
				type="text"
				value="<?php echo esc_attr( $instance[ self::TITLE ] ); ?>" />
		</p>
		<?php do_action( 'advanced-sidebar-menu/widget/page/before-columns', $instance, $this ); ?>
		<div class="advanced-sidebar-menu-column advanced-sidebar-menu-column-left">
			<?php do_action( 'advanced-sidebar-menu/widget/page/left-column', $instance, $this ); ?>
		</div>
		<div class="advanced-sidebar-menu-column advanced-sidebar-menu-column-right">
			<?php do_action( 'advanced-sidebar-menu/widget/page/right-column', $instance, $this ); ?>
		</div>
		<div class="advanced-sidebar-menu-full-width"><!-- clear --></div>
		<?php
		do_action( 'advanced-sidebar-menu/widget/page/after-form', $instance, $this );
	}


	/**
	 * Save the widget settings.
	 *
	 * @param array $new_instance - New widget settings.
	 * @param array $old_instance - Old widget settings.
	 *
	 * @return array|mixed
	 */
	public function update( $new_instance, $old_instance ) {
		if ( isset( $new_instance['exclude'] ) ) {
			$new_instance['exclude'] = wp_strip_all_tags( $new_instance['exclude'] );
		}

		return apply_filters( 'advanced-sidebar-menu/widget/page/update', $new_instance, $old_instance );
	}


	/**
	 * Widget Output.
	 *
	 * @param array $args     - Widget registration args.
	 * @param array $instance - Widget settings.
	 *
	 * @see   \Advanced_Sidebar_Menu\Menus\Page
	 *
	 * @since 7.0.0
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$instance = (array) wp_parse_args( $instance, self::$defaults );
		$asm = \Advanced_Sidebar_Menu\Menus\Page::factory( $instance, $args );

		do_action( 'advanced-sidebar-menu/widget/before-render', $asm, $this );

		$asm->render();

		do_action( 'advanced-sidebar-menu/widget/after-render', $asm, $this );
	}
}
