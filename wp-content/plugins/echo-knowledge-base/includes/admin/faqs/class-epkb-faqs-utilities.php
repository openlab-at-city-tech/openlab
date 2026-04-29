<?php

/**
 * Various utilities for the FAQs
 */
class EPKB_FAQs_Utilities {

	/**
	 * Display the FAQs shortcode and Module
	 *
	 * @param $kb_config
	 * @param $faq_groups
	 * @param $faq_title
	 * @param bool $is_shortcode
	 * @param bool $use_content_filter
	 * @param bool $render_inline_css
	 * @return false|string
	 */
	public static function display_faqs( $kb_config, $faq_groups, $faq_title, $is_shortcode=false, $use_content_filter=false, $render_inline_css=true ) {

		// allow to render FAQs schema only once per page even if multiple FAQs blocks or shortcodes present (to avoid schema checker fail)
		static $is_faqs_schema_already_rendered = false;

		// Set Icon Type from User settings
		switch ( $kb_config['faq_icon_type'] ) {
			case 'none':
				$open_icon      = '';
				$closed_icon    = '';
				break;
			case 'icon_plus_box':
				$open_icon      = 'epkbfa-minus-square';
				$closed_icon    = 'epkbfa-plus-square';
				break;
			case 'icon_plus_circle':
				$open_icon      = 'epkbfa-minus-circle';
				$closed_icon    = 'epkbfa-plus-circle';
				break;
			case 'icon_plus':
				$open_icon      = 'epkbfa-minus';
				$closed_icon    = 'epkbfa-plus';
				break;
			case 'icon_arrow_caret':
				$open_icon      = 'epkbfa-angle-up';
				$closed_icon    = 'epkbfa-angle-down';
				break;
			case 'icon_arrow_angle':
			default:
				$open_icon      = 'ep_font_icon_arrow_carrot_down';
				$closed_icon    = 'ep_font_icon_arrow_carrot_right';
				break;
		}

		$container_classes = self::get_faq_classes( $kb_config );

		ob_start();

		if ( $render_inline_css ) { ?>
			<style>	    <?php
				self::get_faq_styles( $kb_config ); ?>
			</style>	<?php
		} ?>

		<div id="epkb-ml-faqs-<?php echo esc_attr( strtolower( $kb_config['kb_main_page_layout'] ) ); ?>-layout" class="epkb-faqs-container <?php echo esc_html( implode( ' ', $container_classes ) ) ?>"> <?php

			// Display the FAQs Title set in the FAQ Module or shortcode Parameter (could be empty)
			if ( $kb_config['ml_faqs_title_location'] != 'none' && ! empty( $faq_title ) ) { ?>
				<h2 class="epkb-faqs-title">
					<span><?php echo esc_html( $faq_title ); ?></span>
				</h2>		<?php
			}

			// Loop through each Group and display its contents
			$faq_schema_json = [
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => [],
			]; ?>

			<div class="epkb-faqs-cat-content-container">				<?php 
				foreach ( $faq_groups as $faq_group_id => $faq_value ) { ?>

					<div class="epkb-faqs-cat-container" id="epkb-faqs-cat-<?php echo esc_attr( $faq_group_id ); ?>"> <?php

						// Display FAQ Group titles if more than one group
						if ( count( $faq_groups ) > 1 ) {
							$faq_group_title = empty( $faq_value['title'] ) ? '' : $faq_value['title'];
							if ( ! empty( $faq_group_title ) ) { ?>
								<div class="epkb-faqs__cat-header">
									<h3 class="epkb-faqs__cat-header__title"><?php echo esc_html( $faq_group_title ); ?></h3>
								</div> <?php
							}
						}

						// display the articles in the columns
						$column_number = 1;
						$columns = self::get_questions_listed_in_columns( $faq_value['faqs'], $kb_config['faq_nof_columns'] ); ?>

						<div class="epkb-faqs__items-list-container"><?php
						// Display this groups questions.
						foreach ( $columns as $column ) { ?>

							<div class="epkb-faqs__items-list epkb-list-column-<?php echo esc_attr( $column_number ); ?>"> <?php

							foreach ( $column as $one_faq ) {

								// add article title and content to the FAQ schema
								if ( ! $is_faqs_schema_already_rendered ) {

									$text = wp_strip_all_tags( $one_faq->post_content );
									$text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
									$text = preg_replace( '/\s+/', ' ', $text );

									$faq_schema_json['mainEntity'][] = array(
										'@type' => 'Question',
										'name' => $one_faq->post_title,
										'acceptedAnswer' => array(
											'@type' => 'Answer',
											'text' => $text,
										)
									);
								}

								// add article title and content to the FAQ schema	?>
								<div class="epkb-faqs__item-container" id="epkb-faqs-article-<?php echo esc_attr( $one_faq->ID ); ?>" >

									<div class="epkb-faqs__item__question" data-faq-type="<?php echo esc_attr( $is_shortcode ? 'faqs' : 'module' ); ?>" tabindex="0" role="button" aria-expanded="false">     <?php
										if ( $kb_config['faq_icon_location'] != 'no_icons' && ( $kb_config['faq_open_mode'] != 'show_all_mode' ) ) { ?>
											<div class="epkb-faqs__item__question__icon epkb-faqs__item__question__icon-closed epkbfa <?php echo esc_attr( $closed_icon ); ?>"></div>
											<div class="epkb-faqs__item__question__icon epkb-faqs__item__question__icon-open epkbfa <?php echo esc_attr( $open_icon ); ?>"></div>    <?php
										} ?>
										<div class="epkb-faqs__item__question__text"><?php echo esc_html( $one_faq->post_title ); ?></div>
									</div>

									<div class="epkb-faqs__item__answer">
										<div class="epkb-faqs__item__answer__text">    <?php
											$content = $use_content_filter ? apply_filters( 'the_content', $one_faq->post_content ) : $one_faq->post_content;
											$content = str_replace( ']]>', ']]&gt;', $content );
											echo $content; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?>
										</div>
									</div>

								</div>  <?php
							}

							$column_number++;							?>

							</div>  <?php
						} ?>

						</div>  <?php

						if ( empty( $faq_value['faqs'] ) ) { ?>
							<div class="epkb-faqs-article-coming-soon"><?php echo esc_html( $kb_config['faq_empty_msg'] ); ?></div>	<?php
						} ?>


					</div>  <?php
				} ?>
			</div>			<?php

			if ( ! $is_faqs_schema_already_rendered ) {
				$is_faqs_schema_already_rendered = true;	?>
				<!--suppress JSUnresolvedVariable -->
				<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema_json ); ?></script>   <?php
			} ?>

		</div>	<?php

		return ob_get_clean();
	}

	/**
	 * Arrange questions in columns based on the number of columns defined in the KB configuration.
	 * @param $faqs_list
	 * @param $max_columns
	 * @return array
	 */
	private static function get_questions_listed_in_columns( $faqs_list, $max_columns=2 ) {

		$nof_columns_int = 2;
		$nof_columns_int = $nof_columns_int > $max_columns ? $max_columns : $nof_columns_int;
		$articles_per_column = (int) ceil( count( $faqs_list ) / $nof_columns_int );

		// create a nested array of articles for each column
		$column_count = 0;
		$column_articles_count = 0;
		$columns = array_fill( 0, $nof_columns_int, [] );
		foreach ( $faqs_list as $faq ) {

			$columns[ $column_count ][] = $faq;
			$column_articles_count++;

			if ( $column_articles_count >= $articles_per_column ) {
				$column_count++;
				$column_articles_count = 0;
			}
		}

		return $columns;
	}

	private static function get_faq_classes( $kb_config ) {

		$columns = empty( $kb_config['faq_nof_columns'] ) ? '1' : $kb_config['faq_nof_columns'];
		switch ( $columns ) {
			case '1':
			default:
				$col_class = 'epkb-faqs-col-1';
				break;
			case '2':
				$col_class = 'epkb-faqs-col-2';
				break;
		}

		switch ( $kb_config['faq_question_space_between'] ) {
			case 'space_none':
				if ( $kb_config['faq_border_mode'] == 'all_around' ) {
					$space_between_questions = 'epkb-faqs-border-all-space-none';
				} else {
					$space_between_questions = 'epkb-faqs-space-none';
				}
				break;
			case 'space_small':
				$space_between_questions = 'epkb-faqs-space-small';
				break;
			case 'space_medium':
				$space_between_questions = 'epkb-faqs-space-medium';
				break;
			default:
				$space_between_questions = 'epkb-faqs-space-medium';
		}

		switch ( $kb_config['faq_compact_mode'] ) {
			case 'compact_small':
				$compact_mode = 'epkb-faqs-compact-small';
				break;
			case 'compact_medium':
				$compact_mode = 'epkb-faqs-compact-medium';
				break;
			default:
				$compact_mode = 'epkb-faqs-compact-medium';
		}

		switch ( $kb_config['faq_open_mode'] ) {
			case 'accordion_mode':
				$show_mode = 'epkb-faqs-accordion-mode';
				break;
			case 'toggle_mode':
				$show_mode = 'epkb-faqs-toggle-mode';
				break;
			case 'show_all_mode':
				$show_mode = 'epkb-faqs-show-all-mode';
				break;
			default:
				$show_mode = 'epkb-faqs-accordion-mode';
		}

		switch ( $kb_config['faq_border_style'] ) {
			case 'sharp':
				$border_style = 'epkb-faqs-border-sharp';
				break;
			case 'rounded':
				$border_style = 'epkb-faqs-border-rounded';
				break;
			default:
				$border_style = 'epkb-faqs-border-rounded';
		}

		switch ( $kb_config['faq_border_mode'] ) {
			case 'none':
				$border_mode = 'epkb-faqs-border-none';
				break;
			case 'all_around':
				$border_mode = 'epkb-faqs-border-all';
				break;
			case 'separator':
				$border_mode = 'epkb-faqs-border-separator';
				break;
			default:
				$border_mode = 'epkb-faqs-border-all';
		}

		switch ( $kb_config['faq_icon_location'] ) {
			case 'no_icons':
				$icon_location = 'epkb-faqs-icon-none';
				break;
			case 'left':
				$icon_location = 'epkb-faqs-icon-left';
				break;
			case 'right':
				$icon_location = 'epkb-faqs-icon-right';
				break;
			default:
				$icon_location = 'epkb-faqs-icon-left';
		}

		$container_classes = array(
			$show_mode,
			$space_between_questions,
			$border_style,
			$border_mode,
			$icon_location,
			$compact_mode,
			$col_class
		);

		return $container_classes;
	}

	private static function get_faq_styles( $kb_config ) {
		$style = '';

		if ( $kb_config['faq_border_mode'] == 'separator' ) {
			$style .= '
				.epkb-faqs-border-separator .epkb-faqs__item__question {
				    border-color: ' . esc_attr( $kb_config['faq_border_color'] ) . '!important;
				  }
				  .epkb-faqs-border-separator .epkb-faqs__item-container--active .epkb-faqs__item__question {
				    border-color: ' . esc_attr( $kb_config['faq_border_color'] ) . '!important;
				  }
				';
		}

		$faq_question_background_color = empty( $kb_config['faq_question_background_color'] ) ? 'transparent' :$kb_config['faq_question_background_color'];
		$faq_answer_background_color   = empty( $kb_config['faq_answer_background_color'] ) ? 'transparent' : $kb_config['faq_answer_background_color'];
		$faq_icon_color = empty( $kb_config['faq_icon_color'] ) ? 'transparent' : $kb_config['faq_icon_color'];

		$style .= '
			.epkb-faqs__item__question { color: ' . esc_attr( $kb_config['faq_question_text_color'] ) . '!important; }
			.epkb-faqs__item__answer { color: ' . esc_attr( $kb_config['faq_answer_text_color'] ) . '!important; }
			
			.epkb-faqs__item-container { border-color: ' . esc_attr( $kb_config['faq_border_color'] ) . '!important; }
			.epkb-faqs__item-container--active .epkb-faqs__item__question { border-color: ' . esc_attr( $kb_config['faq_border_color'] ) . '!important; }
			
			.epkb-faqs__item__question { background-color: ' .  esc_attr( $faq_question_background_color ) . '; }
			.epkb-faqs__item__answer { background-color: ' .  esc_attr( $faq_answer_background_color ) . '; }
			.epkb-faqs__item__question__icon { color: '.  esc_attr( $faq_icon_color ) . '; }	';

		// Display the FAQs Title set in the FAQ Module or shortcode Parameter (could be empty)
		if ( $kb_config['ml_faqs_title_location'] != 'none' && ! empty( $kb_config['ml_faqs_title_text'] ) ) {
			$style .= '.epkb-faqs-title { text-align: ' . esc_attr( $kb_config['ml_faqs_title_location'] ) . '!important; }';
		}

		echo esc_attr( $style );
	}

	public static function get_design_names() {
		return [
			'10'  => esc_html__( 'Compact Simple', 'echo-knowledge-base' ),
			'11'  => esc_html__( 'Blue Borderless', 'echo-knowledge-base' ),
			'12'  => esc_html__( 'Green Separator', 'echo-knowledge-base' ),
			'13'  => esc_html__( 'Sharp Blue Answers', 'echo-knowledge-base' ),
			'14'  => esc_html__( 'Purple Questions', 'echo-knowledge-base' ),
			'15'  => esc_html__( 'Right Icon', 'echo-knowledge-base' ),
			'16'  => esc_html__( 'Modern', 'echo-knowledge-base' ),
			'17' => esc_html__( 'Dark Mode', 'echo-knowledge-base' ),
			'18' => esc_html__( 'Orange Highlight', 'echo-knowledge-base' ),
			'19' => esc_html__( 'Blue Accent', 'echo-knowledge-base' ),
			'20' => esc_html__( 'Green 2-Column', 'echo-knowledge-base' ),
			'21' => esc_html__( 'Purple Elegance', 'echo-knowledge-base' ),
			'22' => esc_html__( 'Red Highlight', 'echo-knowledge-base' ),
			'23' => esc_html__( 'Minimal Gray', 'echo-knowledge-base' ),
			'24' => esc_html__( 'Yellow Highlight', 'echo-knowledge-base' ),
			'25' => esc_html__( 'Indigo Compact', 'echo-knowledge-base' ),
			'26' => esc_html__( 'Light Blue Headers', 'echo-knowledge-base' ),
			'27' => esc_html__( 'Clean Minimal', 'echo-knowledge-base' ),
		];
	}

	public static function get_design_settings( $design_name ) {

		$defaults = array(
			'faq_border_style'              => 'rounded',
			'faq_border_mode'               => 'all_around',
			'faq_open_mode'                 => 'accordion_mode',
			'faq_question_space_between'    => 'space_medium',
			'faq_compact_mode'              => 'compact_medium',
			'faq_border_color'              => '#e8e8e8',
			'faq_icon_location'             => 'left',
			'faq_icon_type'                 => 'icon_plus',
			'faq_icon_color'                => '#000000',
			'faq_nof_columns'               => '1',
			'faq_question_background_color' => '#FFFFFF',
			'faq_answer_background_color'   => '#FFFFFF',
			'faq_question_text_color'       => '#000000',
			'faq_answer_text_color'			=> '#000000',
		);

		switch ( $design_name ) {
			case '1':
				$design_settings = array(
					'faq_compact_mode'              => 'compact_small',
					'faq_question_space_between'    => 'space_small',
					'faq_icon_type'                 => 'icon_plus_box',
					'faq_border_mode'               => 'none',
				);
				break;
			case '2':
				$design_settings = array(
					'faq_border_style'              => 'sharp',
					'faq_nof_columns'               => '2',
					'faq_icon_type'                 => 'icon_arrow_angle',
				);
				break;
			case '3':
				$design_settings = array(
					'faq_border_mode'               => 'none',
					'faq_icon_type'                 => 'icon_arrow_angle',
					'faq_question_background_color' => '#ececec',
					'faq_answer_background_color'   => '#fbfbfb',
				);
				break;
			case '4':
				$design_settings = array(
					'faq_border_style'              => 'sharp',
					'faq_border_mode'               => 'separator',
					'faq_border_color'              => '#D0E57C',
					'faq_icon_type'                 => 'icon_plus',
					'faq_icon_color'                => '#D0E57C',
					'faq_question_background_color' => '',
					'faq_answer_background_color'   => '',
				);
				break;
			case '5':
				$design_settings = array(
					'faq_border_color'              => '#e8e8e8',
					'faq_icon_location'             => 'right',
					'faq_icon_type'                 => 'icon_plus_circle',
					'faq_border_mode'               => 'separator',
				);
				break;
			case '6':
				$design_settings = array(
					'faq_icon_type'                 => 'icon_arrow_caret',
					'faq_border_style'              => 'sharp',
					'faq_question_space_between'    => 'space_none',
					'faq_answer_background_color'   => '#ececec',
				);
				break;
			case '7':
				$design_settings = array(
					'faq_border_style'              => 'sharp',
					'faq_icon_type'                 => 'icon_plus_circle',
					'faq_question_background_color' => '#e6e6fa',
					'faq_question_space_between'    => 'space_none',
				);
				break;
			case '8':
				$design_settings = array(
					'faq_nof_columns'               => '2',
					'faq_icon_location'             => 'right',
				);
				break;

			// NEW DESIGNS
			case '10':
			default:
				$design_settings = array(
					'faq_compact_mode'              => 'compact_small',
					'faq_question_space_between'    => 'space_small',
				);
				break;
			case '11':
				$design_settings = array(
					'faq_border_mode'               => 'none',
					'faq_icon_type'                 => 'icon_arrow_caret',
					'faq_question_background_color' => '#9fc8db',
					'faq_answer_background_color'   => '#f3faff',
				);
				break;
			case '12':
				$design_settings = array(
					'faq_border_style'              => 'sharp',
					'faq_border_mode'               => 'separator',
					'faq_border_color'              => '#D0E57C',
					'faq_icon_type'                 => 'icon_plus_box',
					'faq_icon_color'                => '#d0E57c',
					'faq_question_background_color'	=> '#f8f8f8',
					'faq_question_space_between'    => 'space_small',
				);
				break;
			case '13':
				$design_settings = array(
					'faq_icon_type'                 => 'icon_arrow_angle',
					'faq_border_style'              => 'sharp',
					'faq_question_space_between'    => 'space_none',
					'faq_answer_background_color'   => '#d8e4f8',
				);
				break;
			case '14':
				$design_settings = array(
					'faq_border_style'              => 'sharp',
					'faq_icon_type'                 => 'icon_plus_circle',
					'faq_question_background_color' => '#c09fe6',
					'faq_question_space_between'    => 'space_none',
				);
				break;
			case '15':
				$design_settings = array(
					'faq_nof_columns'               => '1',
					'faq_icon_location'             => 'right',
				);
				break;
			case '16':
				$design_settings = array(
					'faq_border_style'              => 'rounded',
					'faq_border_mode'               => 'all_around',
					'faq_border_color'              => '#eaeaea',
					'faq_icon_type'                 => 'icon_plus_circle',
					'faq_icon_color'                => '#4285f4',
					'faq_icon_location'             => 'right',
					'faq_question_space_between'    => 'space_medium',
					'faq_question_background_color' => '#f8f9fa',
					'faq_answer_background_color'   => '#ffffff',
					'faq_question_text_color'       => '#202124',
					'faq_answer_text_color'         => '#5f6368',
				);
				break;
			case '17':
				$design_settings = array(
					'faq_border_style'              => 'rounded',
					'faq_border_mode'               => 'all_around',
					'faq_border_color'              => '#2c3e50',
					'faq_icon_type'                 => 'icon_arrow_caret',
					'faq_icon_color'                => '#ffffff',
					'faq_icon_location'             => 'left',
					'faq_question_space_between'    => 'space_small',
					'faq_question_background_color' => '#34495e',
					'faq_answer_background_color'   => '#ecf0f1',
					'faq_question_text_color'       => '#ffffff',
					'faq_answer_text_color'         => '#2c3e50',
				);
				break;
			case '18':
				$design_settings = array(
					'faq_border_style'              => 'sharp',
					'faq_border_mode'               => 'separator',
					'faq_border_color'              => '#e67e22',
					'faq_icon_type'                 => 'icon_plus',
					'faq_icon_color'                => '#e67e22',
					'faq_icon_location'             => 'right',
					'faq_question_space_between'    => 'space_medium',
					'faq_question_background_color' => '#ffffff',
					'faq_answer_background_color'   => '#fef9e7',
					'faq_question_text_color'       => '#d35400',
					'faq_answer_text_color'         => '#34495e',
				);
				break;
			case '19':
				$design_settings = array(
					'faq_border_style'              => 'rounded',
					'faq_border_mode'               => 'none',
					'faq_icon_type'                 => 'icon_plus_box',
					'faq_icon_color'                => '#ffffff',
					'faq_icon_location'             => 'left',
					'faq_question_space_between'    => 'space_medium',
					'faq_question_background_color' => '#3498db',
					'faq_answer_background_color'   => '#eef7fb',
					'faq_question_text_color'       => '#ffffff',
					'faq_answer_text_color'         => '#2980b9',
				);
				break;
			case '20':
				$design_settings = array(
					'faq_border_style'              => 'sharp',
					'faq_border_mode'               => 'all_around',
					'faq_border_color'              => '#27ae60',
					'faq_icon_type'                 => 'icon_arrow_angle',
					'faq_icon_color'                => '#27ae60',
					'faq_nof_columns'               => '2',
					'faq_question_space_between'    => 'space_small',
					'faq_question_background_color' => '#f2f9f4',
					'faq_answer_background_color'   => '#ffffff',
					'faq_question_text_color'       => '#2ecc71',
					'faq_answer_text_color'         => '#34495e',
				);
				break;
			case '21':
				$design_settings = array(
					'faq_border_style'              => 'rounded',
					'faq_border_mode'               => 'all_around',
					'faq_border_color'              => '#9b59b6',
					'faq_icon_type'                 => 'icon_plus_circle',
					'faq_icon_color'                => '#9b59b6',
					'faq_icon_location'             => 'right',
					'faq_question_space_between'    => 'space_medium',
					'faq_question_background_color' => '#f5eef8',
					'faq_answer_background_color'   => '#ffffff',
					'faq_question_text_color'       => '#8e44ad',
					'faq_answer_text_color'         => '#34495e',
				);
				break;
			case '22':
				$design_settings = array(
					'faq_border_style'              => 'sharp',
					'faq_border_mode'               => 'separator',
					'faq_border_color'              => '#e74c3c',
					'faq_icon_type'                 => 'icon_arrow_caret',
					'faq_icon_color'                => '#e74c3c',
					'faq_icon_location'             => 'left',
					'faq_question_space_between'    => 'space_small',
					'faq_question_background_color' => '#ffffff',
					'faq_answer_background_color'   => '#fdecea',
					'faq_question_text_color'       => '#c0392b',
					'faq_answer_text_color'         => '#34495e',
				);
				break;
			case '23':
				$design_settings = array(
					'faq_border_style'              => 'rounded',
					'faq_border_mode'               => 'all_around',
					'faq_border_color'              => '#bdc3c7',
					'faq_icon_type'                 => 'icon_plus',
					'faq_icon_color'                => '#7f8c8d',
					'faq_nof_columns'               => '2',
					'faq_question_space_between'    => 'space_medium',
					'faq_question_background_color' => '#ecf0f1',
					'faq_answer_background_color'   => '#ffffff',
					'faq_question_text_color'       => '#2c3e50',
					'faq_answer_text_color'         => '#7f8c8d',
				);
				break;
			case '24':
				$design_settings = array(
					'faq_border_style'              => 'rounded',
					'faq_border_mode'               => 'all_around',
					'faq_border_color'              => '#f1c40f',
					'faq_icon_type'                 => 'icon_arrow_angle',
					'faq_icon_color'                => '#f39c12',
					'faq_icon_location'             => 'left',
					'faq_question_space_between'    => 'space_medium',
					'faq_question_background_color' => '#fcf3cf',
					'faq_answer_background_color'   => '#ffffff',
					'faq_question_text_color'       => '#f39c12',
					'faq_answer_text_color'         => '#34495e',
				);
				break;
			case '25':
				$design_settings = array(
					'faq_border_style'              => 'rounded',
					'faq_border_mode'               => 'all_around',
					'faq_border_color'              => '#3f51b5',
					'faq_icon_type'                 => 'icon_plus_circle',
					'faq_icon_color'                => '#3f51b5',
					'faq_icon_location'             => 'right',
					'faq_question_space_between'    => 'space_medium',
					'faq_compact_mode'              => 'compact_small',
					'faq_question_background_color' => '#e8eaf6',
					'faq_answer_background_color'   => '#ffffff',
					'faq_question_text_color'       => '#303f9f',
					'faq_answer_text_color'         => '#616161',
				);
				break;
			case '26':
				$design_settings = array(
					'faq_border_style'              => 'sharp',
					'faq_border_mode'               => 'separator',
					'faq_border_color'              => '#e1e1e1',
					'faq_icon_type'                 => 'none',
					'faq_icon_location'             => 'left',
					'faq_question_space_between'    => 'space_none',
					'faq_compact_mode'              => 'compact_medium',
					'faq_question_background_color' => '#c4dce6',
					'faq_answer_background_color'   => '#ffffff',
					'faq_question_text_color'       => '#505050',
					'faq_answer_text_color'         => '#666666',
				);
				break;
			case '27':
				$design_settings = array(
					'faq_border_style'              => 'sharp',
					'faq_border_mode'               => 'separator',
					'faq_border_color'              => '#e1e1e1',
					'faq_icon_type'                 => 'icon_plus_circle',
					'faq_icon_color'                => '#00a0be',
					'faq_icon_location'             => 'right',
					'faq_question_space_between'    => 'space_small',
					'faq_compact_mode'              => 'compact_medium',
					'faq_question_background_color' => '#ffffff',
					'faq_answer_background_color'   => '#ffffff',
					'faq_question_text_color'       => '#00a0be',
					'faq_answer_text_color'         => '#666666',
				);
				break;
		}

		// Overwrite defaults with design settings
		$design_settings = array_merge( $defaults, $design_settings );

		return $design_settings;
	}

	public static function get_faq_groups( $group_ids=[], $order_by='name' ) {

		$groups = get_terms( [
			'taxonomy'      => EPKB_FAQs_CPT_Setup::FAQ_CATEGORY,
			'include'       => $group_ids,
			'fields'        => 'id=>name',
			'hide_empty'    => false,
			'orderby'       => $order_by,
		] );

		return $groups;
	}

	/**
	 * Returns array of FAQs sorted by 'faqs_order_sequence'
	 * @param $faq_group_id
	 * @return array
	 */
	public static function get_sorted_group_faqs( $faq_group_id ) {

		$faqs = get_posts( [
			'post_type'         => EPKB_FAQs_CPT_Setup::FAQS_POST_TYPE,
			'posts_per_page'    => -1,
			'tax_query'         => array(
				array(
					'taxonomy'  => EPKB_FAQs_CPT_Setup::FAQ_CATEGORY,
					'field'     => 'term_id',
					'terms'     => $faq_group_id,
				)
			),
		] );

		$faq_ids = array_column( $faqs, 'ID' );
		$faqs_order_sequence = get_term_meta( $faq_group_id, 'faqs_order_sequence', true );

		// set unsorted sequence if the current sequence is empty but the Group has assigned FAQs (normally there is always user's defined sequence); ensure sequence type
		if ( ( empty( $faqs_order_sequence ) && ! empty( $faq_ids ) ) || ! is_array( $faqs_order_sequence ) ) {
			$faqs_order_sequence = $faq_ids;
		}

		// set FAQ ID as key in FAQs array
		$faqs = array_combine( $faq_ids, $faqs );

		// sort FAQs for the current Group
		$sorted_faqs = [];
		foreach ( $faqs_order_sequence as $faq_id ) {
			if ( ! array_key_exists( $faq_id, $faqs ) ) {
				continue;
			}
			$sorted_faqs[] = $faqs[$faq_id];
			unset( $faqs[$faq_id] );
		}

		return $sorted_faqs;
	}

	// prepare all groups and their questions
	public static function get_faq_groups_questions( $faq_groups ) {
		$faq_groups_questions = [];
		foreach ( $faq_groups as $faq_group_id => $faq_group_name ) {

			/* $faq_group_status = get_term_meta( $faq_group_id, 'faq_group_status', true ) == 'publish' ? 'publish' : 'draft';
			if ( empty( $faq_group_status) || $faq_group_status != 'publish' ) {
				continue;
			} */

			$faqs = self::get_sorted_group_faqs( $faq_group_id );
			/* if ( empty( $faqs ) ) {
				unset( $faq_groups[$faq_group_id] );
				continue;
			} */

			$faq_groups_questions[$faq_group_id] = ['title' => $faq_group_name, 'faqs' => $faqs];
		}

		return $faq_groups_questions;
	}

	public static function display_error( $message ) {
		if ( current_user_can( 'manage_options' ) ) {
			return esc_html__( 'FAQs shortcode: No categories with articles found.', 'echo-knowledge-base' );
		}

		return '';
	}

	/**
	 * Display message for users with access to FAQs Module settings if no FAQs selected
	 *
	 * @param $kb_config
	 * @param $is_faqs_module
	 */
	public static function display_faqs_missing_message( $kb_config, $is_faqs_module ) {

		// only users with at least Editor access can see the message
		if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_faqs_write' ) ) {    ?>
			<div class='epkb-faqs-article-coming-soon'><?php echo esc_html( $kb_config['faq_empty_msg'] ); ?></div> <?php
			return;
		}

		// Ensure that the selected FAQ groups are valid and not deleted
		$all_faq_groups = EPKB_FAQs_Utilities::get_faq_groups();
		if ( is_wp_error( $all_faq_groups ) ) {
			echo EPKB_FAQs_Utilities::display_error( $all_faq_groups->get_error_message() );//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		} ?>

		<div class="epkb-faqs-cat-content-container epkb-faqs-show-all-mode">
			<div class="epkb-faqs-cat-container">
				<div class="epkb-faqs__item-container">
					<div class="epkb-faqs__item__question">
						<section id="eckb-kb-faqs-not-assigned">
							<h2 class="eckb-kb-faqs-not-assigned-title"><?php
								if ( empty( $all_faq_groups ) ) {
									esc_html_e( 'No FAQs Defined', 'echo-knowledge-base' );
								} else {
									esc_html_e( 'Select At Least One FAQ Group', 'echo-knowledge-base' );
								}
							?></h2>
							<div class="eckb-kb-faqs-not-assigned-body">
								<p><?php
									if ( empty( $all_faq_groups ) ) {
										echo esc_html__( 'You do not have any FAQ Groups defined.', 'echo-knowledge-base' ) . ' ' . '<a href="' .
											esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_config['id'] . '&page=epkb-faqs#faqs-groups' ) ) . '" target="_blank">' .
											esc_html__( 'Create FAQ Group', 'echo-knowledge-base' ) .
                                            '<span class="epkbfa epkbfa-external-link"></span>
                                            </a>';
									} else {
										echo ( $is_faqs_module ?
											esc_html__( 'Select an FAQ group in the Frontend Editor to display FAQs.', 'echo-knowledge-base' )
											: esc_html__( 'Select an FAQ group in the FAQs block settings to display FAQs.', 'echo-knowledge-base' ) );
									} ?>
								</p>
								<p>
									<i>
										<span><?php esc_html_e( 'If you need help, please contact us', 'echo-knowledge-base' ); ?></span>
										<a href="https://www.echoknowledgebase.com/technical-support/" target="_blank"> <?php esc_html_e( 'here', 'echo-knowledge-base' ); ?>
											<span class="epkbfa epkbfa-external-link"></span>
										</a>

									</i>
								</p>
							</div>
						</section>
					</div>
				</div>
			</div>
		</div>  <?php
	}
}
