<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ezTOC_Widget' ) ) {

	/**
	 * Class ezTOC_Widget
	 */
	class ezTOC_Widget extends WP_Widget {

		/**
		 * Setup and register the table of contents widget.
		 *
		 * @access public
		 * @since  1.0
		 */
		public function __construct() {

			$options = array(
				'classname'   => 'ez-toc',
				'description' => __( 'Display the table of contents.', 'easy-table-of-contents' )
			);

			parent::__construct(
				'ezw_tco',
				__( 'Table of Contents', 'easy-table-of-contents' ),
				$options
			);

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueueScripts' ) );
			add_action( 'admin_footer-widgets.php', array( $this, 'printScripts' ), 9999 );
		}

		/**
		 * Callback which registers the widget with the Widget API.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @return void
		 */
		public static function register() {

			register_widget( __CLASS__ );
		}

		/**
		 * Callback to enqueue scripts on the Widgets admin page.
		 *
		 * @access private
		 * @since  1.0
		 *
		 * @param string $hook_suffix
		 */
		public function enqueueScripts( $hook_suffix ) {

			if ( 'widgets.php' !== $hook_suffix ) {
				return;
			}

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'underscore' );
		}

		/**
		 * Callback to print the scripts to the Widgets admin page footer.
		 *
		 * @access private
		 * @since  1.0
		 */
		public function printScripts() {
			?>
			<script>
				( function( $ ){
					function initColorPicker( widget ) {
						widget.find( '.color-picker' ).wpColorPicker( {
							change: _.throttle( function() { // For Customizer
								$(this).trigger( 'change' );
							}, 3000 )
						});
					}

					function onFormUpdate( event, widget ) {
						initColorPicker( widget );
					}

					$( document ).on( 'widget-added widget-updated', onFormUpdate );

					$( document ).ready( function() {
						$( '#widgets-right .widget:has(.color-picker)' ).each( function () {
							initColorPicker( $( this ) );
						} );
					} );
				}( jQuery ) );
			</script>
			<?php
		}

		/**
		 * Display the post content. Optionally allows post ID to be passed
		 *
		 * @link http://stephenharris.info/get-post-content-by-id/
		 * @link http://wordpress.stackexchange.com/a/143316
		 *
		 * @access public
		 * @since  1.0
		 *
		 * @param int $post_id Optional. Post ID.
		 *
		 * @return string
		 */
		public function the_content( $post_id = 0 ) {

			global $post;
			$post = get_post( $post_id );
			setup_postdata( $post );
			ob_start();
			the_content();
			$content = ob_get_clean();
			wp_reset_postdata();

			return $content;
		}

		/**
		 * Renders the widgets.
		 *
		 * @access private
		 * @since  1.0
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) {

			if ( is_404() || is_archive() || is_search() || ( ! is_front_page() && is_home() )  ) return;

			$post = ezTOC::get( get_the_ID() );

                        if( function_exists( 'post_password_required' ) ) {
                           if( post_password_required() ) return;
                        }
                        
			/**
			 * @link https://wordpress.org/support/topic/fatal-error-when-trying-to-access-widget-area/
			 */
			if ( ! $post instanceof ezTOC_Post ) return;

			if ( $post->hasTOCItems() ) {

				/**
				 * @var string $before_widget
				 * @var string $after_widget
				 * @var string $before_title
				 * @var string $after_title
				 */
				extract( $args );

				$class = array(
					'ez-toc-v' . str_replace( '.', '_', ezTOC::VERSION ),
					'ez-toc-widget',
				);

				$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

				if ( false !== strpos( $title, '%PAGE_TITLE%' ) || false !== strpos( $title, '%PAGE_NAME%' ) ) {

					$title = str_replace( '%PAGE_TITLE%', get_the_title(), $title );
				}

				if ( ezTOC_Option::get( 'show_hierarchy' ) ) {

					$class[] = 'counter-hierarchy';

				} else {

					$class[] = 'counter-flat';
				}

				if( ezTOC_Option::get( 'heading-text-direction', 'ltr' ) == 'ltr' ) {
                    $class[] = 'ez-toc-widget-container';
                }
                if( ezTOC_Option::get( 'heading-text-direction', 'ltr' ) == 'rtl' ) {
                    $class[] = 'ez-toc-widget-container-rtl';
                }

				if ( $instance['affix'] ) {

					$class[] = 'ez-toc-affix';
				}


                $class[] = 'ez-toc-widget-direction';

				$custom_classes = ezTOC_Option::get( 'css_container_class', '' );

				if ( 0 < strlen( $custom_classes ) ) {

					$custom_classes = explode( ' ', $custom_classes );
					$custom_classes = apply_filters( 'ez_toc_container_class', $custom_classes, $this );

					if ( is_array( $custom_classes ) ) {

						$class = array_merge( $class, $custom_classes );
					}
				}

				$class = array_filter( $class );
				$class = array_map( 'trim', $class );
				$class = array_map( 'sanitize_html_class', $class );

				echo $before_widget;				
				do_action( 'ez_toc_before_widget_container');

				echo '<div id="ez-toc-widget-container" class="ez-toc-widget-container ' . implode( ' ', $class ) . '">' . PHP_EOL;

				do_action( 'ez_toc_before_widget' );

				/**
				 * @todo Instead of inline style, use the shadow DOM.
				 * @link https://css-tricks.com/playing-shadow-dom/
				 *
				 * @todo Consider not outputting the style if CSS is disabled.
				 * @link https://wordpress.org/support/topic/inline-styling-triggers-html-validation-error/
				 */

				if ( 0 < strlen( $title ) ) {

					?>

					<?php echo $before_title; ?>
                                        <span class="ez-toc-title-container">

                                        <style type="text/css">
                                                #<?php echo $this->id ?> .ez-toc-widget-container ul.ez-toc-list li.active{
                                                        background-color: <?php echo esc_attr( $instance['highlight_color'] ); ?>;
                                                }
                                        </style>

										<?php
										$headerTextToggleClass = '';
										$headerTextToggleStyle = '';
										
										if ( ezTOC_Option::get( 'visibility_on_header_text' ) ) {
											$headerTextToggleClass = 'ez-toc-toggle';
											$headerTextToggleStyle = 'style="cursor: pointer"';
										}
                                        $header_label = '<span class="ez-toc-title ' . $headerTextToggleClass . '" ' .$headerTextToggleStyle . '>' . $title . '</span>';
										?>
										<span class="ez-toc-title-toggle">
                                            <?php if ( 'css' != ezTOC_Option::get( 'toc_loading' ) ): ?>

												<?php
													echo $header_label;
                                                    if ( ezTOC_Option::get( 'visibility' ) ) {

														echo '<a href="#" class="ez-toc-pull-right ez-toc-btn ez-toc-btn-xs ez-toc-btn-default ez-toc-toggle" aria-label="Widget Easy TOC toggle icon"><span style="border: 0;padding: 0;margin: 0;position: absolute !important;height: 1px;width: 1px;overflow: hidden;clip: rect(1px 1px 1px 1px);clip: rect(1px, 1px, 1px, 1px);clip-path: inset(50%);white-space: nowrap;">Toggle Table of Content</span>' . ezTOC::getTOCToggleIcon() . '</a>';
                                                    }
                                                    ?>




                                            <?php else: ?>
                                                <?php 
                                                $toggle_view='';
						if(ezTOC_Option::get('visibility_hide_by_default')==true){
							$toggle_view= "checked";
						}
                                                if( true == get_post_meta( get_the_ID(), '_ez-toc-visibility_hide_by_default', true ) ) {
                                                    $toggle_view = "checked";
                                                }
                                                $cssIconID = uniqid();
												if ( ezTOC_Option::get( 'visibility_on_header_text' ) ) {
                                                	$htmlCSSIcon = '<label for="ez-toc-cssicon-toggle-item-' . $cssIconID . '" style="cursor:pointer">' . $header_label . '<span class="ez-toc-pull-right ez-toc-btn ez-toc-btn-xs ez-toc-btn-default ez-toc-toggle">' . ezTOC::getTOCToggleIcon( 'widget-with-visibility_on_header_text' ) . '</span></label>';
												} else {
													echo $header_label;
													$htmlCSSIcon = '<label for="ez-toc-cssicon-toggle-item-' . $cssIconID . '" class="ez-toc-pull-right ez-toc-btn ez-toc-btn-xs ez-toc-btn-default ez-toc-toggle">' . ezTOC::getTOCToggleIcon( 'widget-with-visibility_on_header_text' ) . '</span></label>';
												}
                                                echo $htmlCSSIcon;

                                                ?>
                                            <?php endif; ?>
                                            </span>
                                        </span>

					<?php echo $after_title; ?>
                                        <?php if ( 'css' == ezTOC_Option::get( 'toc_loading' ) ): ?>
                                            <label for="ez-toc-cssicon-toggle-item-count-<?= $cssIconID ?>" class="cssiconcheckbox">1</label><input type="checkbox" id="ez-toc-cssicon-toggle-item-<?= $cssIconID ?>" <?= $toggle_view?> style="display:none" />
                                        <?php endif; ?>
					<?php
                                        
				}
				do_action( 'ez_toc_before' );
				echo '<nav>'. PHP_EOL . $post->getTOCList() . '</nav>' . PHP_EOL;
				do_action( 'ez_toc_after' );
				do_action( 'ez_toc_after_widget' );

				echo '</div>' . PHP_EOL;
				do_action( 'ez_toc_after_widget_container' );

				echo $after_widget;
								
			}
		}

		/**
		 * Update the widget settings.
		 *
		 * @access private
		 * @since  1.0
		 *
		 * @param array $new_instance
		 * @param array $old_instance
		 *
		 * @return array
		 */
		public function update( $new_instance, $old_instance ) {

			$instance = $old_instance;

			$instance['title'] = strip_tags( $new_instance['title'] );

			$instance['affix'] = array_key_exists( 'affix', $new_instance ) ? $new_instance['affix'] : '0';

			$instance['highlight_color'] = strip_tags( $new_instance['highlight_color'] );

			$instance['hide_inline'] = array_key_exists( 'hide_inline', $new_instance ) ? $new_instance['hide_inline'] : '0';

			return $instance;
		}

		/**
		 * Displays the widget settings on the Widgets admin page.
		 *
		 * @access private
		 * @since  1.0
		 *
		 * @param array $instance
		 *
		 * @return string|void
		 */
		public function form( $instance ) {

			$defaults = array(
				'affix' => '0',
				'highlight_color' => '#ededed',
				'title' => __('Table of Contents', 'easy-table-of-contents' ),
			);

			$instance = wp_parse_args( (array) $instance, $defaults );

			$highlight_color = esc_attr( $instance[ 'highlight_color' ] );

			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'easy-table-of-contents' ); ?>:</label>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>"
				       name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>"
				       style="width:100%;"/>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'highlight_color' ); ?>"><?php _e( 'Active Section Highlight Color:', 'easy-table-of-contents' ); ?></label><br>
				<input type="text" name="<?php echo $this->get_field_name( 'highlight_color' ); ?>" class="color-picker" id="<?php echo $this->get_field_id( 'highlight_color' ); ?>" value="<?php echo $highlight_color; ?>" data-default-color="<?php echo $defaults['highlight_color']; ?>" />
			</p>

			<p style="display: <?php echo ezTOC_Option::get( 'widget_affix_selector' ) ? 'block' : 'none'; ?>;">
				<input class="checkbox" type="checkbox" <?php checked( $instance['affix'], 1 ); ?>
				       id="<?php echo $this->get_field_id( 'affix' ); ?>"
				       name="<?php echo $this->get_field_name( 'affix' ); ?>" value="1"/>
				<label for="<?php echo $this->get_field_id( 'affix' ); ?>"> <?php _e( 'Affix or pin the widget.', 'easy-table-of-contents' ); ?></label>
			</p>

			<p class="description" style="display: <?php echo ezTOC_Option::get( 'widget_affix_selector' ) ? 'block' : 'none'; ?>;">
				<?php _e( 'If you choose to affix the widget, do not add any other widgets on the sidebar. Also, make sure you have only one instance Table of Contents widget on the page.', 'easy-table-of-contents' ); ?>
			</p>
			<?php
		}

	} // end class

	add_action( 'widgets_init', array( 'ezTOC_Widget', 'register' ) );
}
