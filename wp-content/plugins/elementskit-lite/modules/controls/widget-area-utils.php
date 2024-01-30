<?php 
namespace ElementsKit_Lite\Modules\Controls;

defined( 'ABSPATH' ) || exit;

class Widget_Area_Utils {

	function init() {
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'modal_content' ) );
	}

	public function modal_content() { 
		ob_start(); ?>
		<div class="widgetarea_iframe_modal">
			<?php include 'widget-area-modal.php'; ?>
		</div>
		<?php
			$output = ob_get_contents();
			ob_end_clean();
	
			echo \ElementsKit_Lite\Utils::render( $output ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --  Already escaped inside of the buffering content
	}

	/**
	 * $index for old version & data support
	 */
	public static function parse( $content, $widget_key, $tab_id = 1, $isAjax = '', $index = null ) {
		$key         = ( $content == '' ) ? $widget_key : $content;
		$extract_key = explode( '***', $key );
		$extract_key = $extract_key[0];
		ob_start(); 
		?>

		<div class="widgetarea_warper widgetarea_warper_editable" data-elementskit-widgetarea-key="<?php echo esc_attr( $extract_key ); ?>"  data-elementskit-widgetarea-index="<?php echo esc_attr( $tab_id ); ?>">
			<div class="widgetarea_warper_edit" data-elementskit-widgetarea-key="<?php echo esc_attr( $extract_key ); ?>" data-elementskit-widgetarea-index="<?php echo esc_attr( $tab_id ); ?>">
				<i class="eicon-edit" aria-hidden="true"></i>
				<span><?php esc_html_e( 'Edit Content', 'elementskit-lite' ); ?></span>
			</div>

			<?php
				$builder_post_title = 'dynamic-content-widget-' . $extract_key . '-' . $tab_id;
				$builder_post       = \ElementsKit_Lite\Utils::get_page_by_title( $builder_post_title, 'elementskit_content' );
				$elementor          = \Elementor\Plugin::instance();

				/**
				 * this checking for already existing content of tab.
				 */
				$post_id = isset( $builder_post->ID ) ? $builder_post->ID : null;
			if ( ! $post_id ) {
				$builder_post_title = 'dynamic-content-widget-' . $extract_key . '-' . $index;
				$builder_post       = \ElementsKit_Lite\Utils::get_page_by_title( $builder_post_title, 'elementskit_content' );
			}

			if ( $isAjax === 'yes' ) {
				$post_id = isset( $builder_post->ID ) ? $builder_post->ID : '';
				echo '<div class="elementor-widget-container" data-ajax-post-id="' . esc_attr($post_id). '"></div>';
			} else {
				?>
					<div class="elementor-widget-container">
					<?php
					if ( isset( $builder_post->ID ) ) {
						$builder_post_id = $builder_post->ID;

						// if wpml is active, get the post id from wpml
						if( defined( 'ICL_SITEPRESS_VERSION' ) ) {
							$language_details = apply_filters( 'wpml_post_language_details', NULL, get_the_ID() );
							if( !is_wp_error($language_details) ) {
								$builder_post_id = apply_filters( 'wpml_object_id', $builder_post_id, 'elementskit_content', true, $language_details['language_code'] );
							}
						}

						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --  Displaying with Elementor content rendering
						echo str_replace( '#elementor', '', \ElementsKit_Lite\Utils::render_tab_content( $elementor->frontend->get_builder_content_for_display( $builder_post_id ), $builder_post_id ) );
					} else {
                        echo esc_html__( 'Click on the Edit Content button to edit/add the content.', 'elementskit-lite' );
					}
					?>
					</div>
				<?php
			}
			?>
		</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}
}
