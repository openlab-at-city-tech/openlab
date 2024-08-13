<?php

namespace Advanced_Sidebar_Menu\Widget;

use Advanced_Sidebar_Menu\Menus\Menu_Abstract;
use Advanced_Sidebar_Menu\Menus\Page as PageMenu;
use Advanced_Sidebar_Menu\Utils;

/**
 * Advanced_Sidebar_Menu_Widgets_Page
 *
 * Parent child menu based on pages.
 *
 * @author OnPoint Plugins
 *
 * @phpstan-import-type PAGE_SETTINGS from PageMenu
 * @phpstan-import-type WIDGET_ARGS from Widget_Abstract
 *
 * @phpstan-type DEFAULTS \Required<\Pick<PAGE_SETTINGS, 'display_all'|'exclude'|'include_childless_parent'|'include_parent'|'levels'|'order_by'|'title'>>
 *
 * @implements Widget<PAGE_SETTINGS, DEFAULTS>
 * @extends Widget_Abstract<PAGE_SETTINGS>
 */
class Page extends Widget_Abstract implements Widget {
	/**
	 * Shared widget instance logic.
	 *
	 * @phpstan-use Instance<PAGE_SETTINGS, DEFAULTS>
	 */
	use Instance;

	public const NAME = 'advanced_sidebar_menu';

	public const TITLE                    = Menu_Abstract::TITLE;
	public const INCLUDE_PARENT           = Menu_Abstract::INCLUDE_PARENT;
	public const INCLUDE_CHILDLESS_PARENT = Menu_Abstract::INCLUDE_CHILDLESS_PARENT;
	public const ORDER_BY                 = Menu_Abstract::ORDER_BY;
	public const EXCLUDE                  = Menu_Abstract::EXCLUDE;
	public const DISPLAY_ALL              = Menu_Abstract::DISPLAY_ALL;
	public const LEVELS                   = Menu_Abstract::LEVELS;

	/**
	 * Default values for the widget.
	 *
	 * @var DEFAULTS
	 */
	protected static $defaults = [
		self::TITLE                    => '',
		self::INCLUDE_PARENT           => '',
		self::INCLUDE_CHILDLESS_PARENT => '',
		self::ORDER_BY                 => 'menu_order',
		self::EXCLUDE                  => '',
		self::DISPLAY_ALL              => '',
		self::LEVELS                   => '100',
	];


	/**
	 * Register the widget.
	 */
	public function __construct() {
		$widget_ops = [
			'classname'             => 'advanced-sidebar-menu advanced-sidebar-page',
			'description'           => __( 'Creates a menu of all the pages using the parent/child relationship', 'advanced-sidebar-menu' ),
			'show_instance_in_rest' => true,
		];
		$control_ops = [];
		if ( ! wp_is_mobile() ) {
			$control_ops['width'] = 620;
		}

		parent::__construct( self::NAME, __( 'Advanced Sidebar - Pages', 'advanced-sidebar-menu' ), $widget_ops, $control_ops );

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
	 * @since 8.2.0
	 *
	 * @param PAGE_SETTINGS $instance - Widget settings.
	 * @param bool  $single   - Singular label or plural.
	 *
	 * @return string
	 */
	public function get_post_type_label( $instance, $single = true ) {
		$type = (string) apply_filters( 'advanced-sidebar-menu/widget/page/post-type-for-label', 'page', $this->control_options, $instance );
		return Utils::instance()->get_post_type_label( $type, $single );
	}


	/**
	 * Get available options to order the pages by.
	 *
	 * @since 9.0.0
	 *
	 * @return array<string, string>
	 */
	public static function get_order_by_options() {
		return (array) apply_filters(
			'advanced-sidebar-menu/widget/page/order-by-options',
			[
				'menu_order' => __( 'Page Order', 'advanced-sidebar-menu' ),
				'post_title' => __( 'Title', 'advanced-sidebar-menu' ),
				'post_date'  => __( 'Published Date', 'advanced-sidebar-menu' ),
			]
		);
	}


	/**
	 * Display options.
	 *
	 * @phpstan-param PAGE_SETTINGS $instance
	 *
	 * @param array                 $instance - Widget settings.
	 * @param Page                  $widget   - Registered widget arguments.
	 *
	 * @return void
	 */
	public function box_display( array $instance, Page $widget ) {
		$settings = $this->set_instance( $instance, static::$defaults );
		?>
		<div class="advanced-sidebar-menu-column-box">
			<p>
				<?php $widget->checkbox( self::INCLUDE_PARENT ); ?>
				<label>
					<?php
					/* translators: Selected post type single label */
					printf( esc_html__( 'Display highest level parent %s', 'advanced-sidebar-menu' ), esc_html( strtolower( $this->get_post_type_label( $settings ) ) ) );
					?>
				</label>
			</p>

			<p>
				<?php $widget->checkbox( self::INCLUDE_CHILDLESS_PARENT ); ?>
				<label>
					<?php
					/* translators: Selected post type single label */
					printf( esc_html__( 'Display menu when there is only the parent %s', 'advanced-sidebar-menu' ), esc_html( strtolower( $this->get_post_type_label( $settings ) ) ) );
					?>
				</label>
			</p>

			<p>
				<?php $widget->checkbox( self::DISPLAY_ALL, self::LEVELS ); ?>
				<label>
					<?php
					/* translators: Selected post type plural label */
					printf( esc_html__( 'Always display child %s', 'advanced-sidebar-menu' ), esc_html( strtolower( $this->get_post_type_label( $settings, false ) ) ) );
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
								<option value="<?php echo esc_attr( (string) $i ); ?>" <?php selected( $i, (int) $settings[ static::LEVELS ] ); ?>>
									<?php echo \absint( $i ); ?>
								</option>
								<?php
							}
							?>
						</select>
						<?php
						/* translators: {select html input}, {Selected post type plural label} */
						printf( esc_html__( 'Display %1$s levels of child %2$s', 'advanced-sidebar-menu' ), ob_get_clean(), esc_html( strtolower( $this->get_post_type_label( $settings, false ) ) ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</label>
				</p>
			</div>

			<?php do_action( 'advanced-sidebar-menu/widget/page/display-box', $settings, $widget ); ?>

		</div>
		<?php
	}


	/**
	 * Order options.
	 *
	 * @phpstan-param PAGE_SETTINGS          $instance
	 *
	 * @param array                          $instance - Widget settings.
	 * @param Page $widget - Registered widget arguments.
	 *
	 * @return void
	 */
	public function box_order( array $instance, Page $widget ) {
		?>
		<div class="advanced-sidebar-menu-column-box">
			<p>
				<label for="<?php echo esc_attr( $widget->get_field_id( self::ORDER_BY ) ); ?>">
					<?php esc_html_e( 'Order by', 'advanced-sidebar-menu' ); ?>
				</label>
				<select
					id="<?php echo esc_attr( $widget->get_field_id( self::ORDER_BY ) ); ?>"
					name="<?php echo esc_attr( $widget->get_field_name( self::ORDER_BY ) ); ?>"
				>
					<?php
					foreach ( static::get_order_by_options() as $key => $order ) {
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
	 * @phpstan-param PAGE_SETTINGS          $instance
	 *
	 * @param array                          $instance - Widget settings.
	 * @param Page $widget - Registered widget arguments.
	 *
	 * @return void
	 */
	public function box_exclude( array $instance, Page $widget ) {
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
	 * @param PAGE_SETTINGS $instance - Widget settings.
	 *
	 * @return string
	 */
	public function form( $instance ) {
		$settings = $this->set_instance( $instance, static::$defaults );
		do_action( 'advanced-sidebar-menu/widget/page/before-form', $settings, $this );
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
				value="<?php echo esc_attr( $settings[ self::TITLE ] ); ?>" />
		</p>
		<?php do_action( 'advanced-sidebar-menu/widget/page/before-columns', $settings, $this ); ?>
		<div class="advanced-sidebar-menu-column advanced-sidebar-menu-column-left">
			<?php do_action( 'advanced-sidebar-menu/widget/page/left-column', $settings, $this ); ?>
		</div>
		<div class="advanced-sidebar-menu-column advanced-sidebar-menu-column-right">
			<?php do_action( 'advanced-sidebar-menu/widget/page/right-column', $settings, $this ); ?>
		</div>
		<div class="advanced-sidebar-menu-full-width"><!-- clear --></div>
		<?php
		do_action( 'advanced-sidebar-menu/widget/page/after-form', $settings, $this );

		return '';
	}


	/**
	 * Save the widget settings.
	 *
	 * @phpstan-param PAGE_SETTINGS $new_instance
	 * @phpstan-param PAGE_SETTINGS $old_instance
	 *
	 * @param array $new_instance - New widget settings.
	 * @param array $old_instance - Old widget settings.
	 *
	 * @return PAGE_SETTINGS
	 */
	public function update( $new_instance, $old_instance ) {
		$new_instance['exclude'] = wp_strip_all_tags( $new_instance['exclude'] );

		return apply_filters( 'advanced-sidebar-menu/widget/page/update', $new_instance, $old_instance );
	}


	/**
	 * Widget Output.
	 *
	 * @see   PageMenu
	 *
	 * @param WIDGET_ARGS   $args     - Widget registration args.
	 * @param PAGE_SETTINGS $instance - Widget settings.
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$settings = $this->set_instance( $instance, static::$defaults );
		$menu = PageMenu::factory( $settings, $args );

		do_action( 'advanced-sidebar-menu/widget/before-render', $menu, $this );

		$menu->render();

		do_action( 'advanced-sidebar-menu/widget/after-render', $menu, $this );
	}
}
