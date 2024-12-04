<?php

namespace ColibriWP\Theme\Components;

use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\Core\Hooks;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Translations;
use ColibriWP\Theme\View;

class MainContent extends ComponentBase {

	public static function selectiveRefreshSelector() {
		return '.colibri-main-content-archive,.colibri-main-content-single';
	}

	protected static function getOptions() {
		$prefix = 'content.';

		return array(
			'settings' => array(
				'blog_posts.pen'                    => array(
					'control' => array(
						'type'        => 'pen',
						'section'     => 'content',
						'colibri_tab' => 'content',
					),
				),

				'blog_posts_per_row'                => array(
					'transport' => 'refresh',
					'default'   => Defaults::get( 'blog_posts_per_row' ),
					'control'   => array(
						'label'       => Translations::get( 'posts_per_row' ),
						'section'     => 'content',
						'colibri_tab' => 'content',
						'type'        => 'button-group',
						'button_size' => 'medium',
						'choices'     => array(
							1 => '1',
							2 => '2',
							3 => '3',
							4 => '4',
						),
						'none_value'  => '',
					),
				),

				"{$prefix}separator1"               => array(
					'transport' => 'refresh',
					'default'   => '',
					'control'   => array(
						'label'       => '',
						'type'        => 'separator',
						'section'     => 'content',
						'colibri_tab' => 'content',
					),
				),

				'blog_enable_masonry'               => array(
					'transport' => 'refresh',
					'default'   => Defaults::get( 'blog_enable_masonry' ),
					'control'   => array(
						'label'       => Translations::get( 'enable_masonry' ),
						'type'        => 'switch',
						'section'     => 'content',
						'colibri_tab' => 'content',
					),
				),

				"{$prefix}separator3"               => array(
					'default' => '',
					'control' => array(
						'label'       => '',
						'type'        => 'separator',
						'section'     => 'content',
						'colibri_tab' => 'content',
					),
				),

				'blog_show_post_thumb_placeholder'  => array(
					'default'    => Defaults::get( 'blog_show_post_thumb_placeholder' ),
					'control'    => array(
						'label'       => Translations::get( 'show_thumbnail_placeholder' ),
						'type'        => 'switch',
						'section'     => 'content',
						'colibri_tab' => 'content',
					),
					'css_output' => array(
						array(
							'selector'    => '.wp-block-kubio-post-featured-image.kubio-post-featured-image--image-missing',
							'media'       => CSSOutput::NO_MEDIA,
							'property'    => 'display',
							'true_value'  => 'block',
							'false_value' => 'none',
						),
					),
				),
				'blog_post_thumb_placeholder_color' => array(
					'default'      => Defaults::get( 'blog_post_thumb_placeholder_color' ),
					'control'      => array(
						'label'       => Translations::get( 'thumbnail_placeholder_color' ),
						'type'        => 'color',
						'section'     => 'content',
						'colibri_tab' => 'content',
					),
					'css_output'   => array(
						array(
							'selector' => '.wp-block-kubio-post-featured-image',
							'media'    => CSSOutput::NO_MEDIA,
							'property' => 'background-color',
						),
					),
					'active_rules' => array(
						array(
							'setting'  => 'blog_show_post_thumb_placeholder',
							'operator' => '=',
							'value'    => true,
						),
					),
				),
			),
			'sections' => array(

				'content' => array(
					'title'    => Translations::get( 'blog_settings' ),
					'priority' => 2,
					'panel'    => 'content_panel',
					'type'     => 'colibri_section',

				),
			),

			'panels'   => array(
				'content_panel' => array(
					'priority'       => 2,
					'title'          => Translations::get( 'content_sections' ),
					'type'           => 'colibri_panel',
					'footer_buttons' => array(
						'change_header' => array(
							'label'   => Translations::get( 'add_section' ),
							'name'    => 'colibriwp_add_section',
							'classes' => array( 'colibri-button-large', 'button-primary' ),
							'icon'    => 'dashicons-plus-alt',
						),
					),
				),
			),
		);
	}


	public function printMasonryFlag() {
		$value = $this->mod( 'blog_enable_masonry', false );
		if ( $value ) {
			wp_enqueue_script( 'jquery-masonry' );
			$value = 'true';
		} else {
			$value = 'false';
		}
		echo $value;
	}

	public function renderContent( $parameters = array() ) {

		$self = $this;
		View::printIn(
			View::CONTENT_ELEMENT,
			function () use ( $self ) {
				/** SECTION START */
				View::printIn(
					View::SECTION_ELEMENT,
					function () use ( $self ) {
						/** ROW START */
						View::printIn(
							View::ROW_ELEMENT,
							function () use ( $self ) {

								/** COLUMN START */
								View::printIn(
									View::COLUMN_ELEMENT,
									function () use ( $self ) {

										View::partial(
											'main',
											'archive',
											array(
												'component' => $self,
											)
										);
									}
								);

								$self->printRightSidebarColumn();

							},
							$self->getMainRowClass()
						);
						/** ROW END */
					},
					$self->getMainSectionClass()
				);
				/** SECTION END */
			},
			array(
				'class' => $self->getContentClass(),
			)
		);
	}

	public function printRightSidebarColumn() {
		$self = $this;

		$display_sidebar = Hooks::prefixed_apply_filters( 'blog_sidebar_enabled', true, 'right' );

		if ( $display_sidebar && is_active_sidebar( 'colibri-sidebar-1' ) ) {
			View::printIn(
				View::COLUMN_ELEMENT,
				function () use ( $self ) {
					get_sidebar();
				},
				array(
					'data-colibri-main-sidebar-col' => 1,
					'class'                         => $self->getSidebarColumnClass( 'right' ),
				)
			);
		}

	}

	private function getSidebarColumnClass( $side ) {

		$classes = (array) Hooks::prefixed_apply_filters(
			'blog_sidebar_column_class',
			array( 'h-col-12', 'h-col-lg-3', 'h-col-md-4' ),
			$side
		);

		$classes = array_merge( $classes, array( 'colibri-sidebar', "blog-sidebar-{$side}" ) );

		return array_unique( $classes );
	}

	private function getMainRowClass() {
		$classes = Hooks::prefixed_apply_filters(
			'main_row_class',
			array(
				'outer_class' => array(),
				'inner_class' => array( 'gutters-col-0' ),
			)
		);

		$classes = array_merge_recursive(
			$classes,
			array(
				'outer_class' => array( 'main-row' ),
				'inner_class' => array( 'main-row-inner' ),
			)
		);

		return $classes;
	}

	private function getMainSectionClass() {

		$classes = Hooks::prefixed_apply_filters(
			'main_section_class',
			array(
				'outer_class' => array(),
				'inner_class' => array( 'h-section-boxed-container' ),
			)
		);

		$classes = array_merge_recursive(
			$classes,
			array(
				'outer_class' => array( 'main-section' ),
				'inner_class' => array( 'main-section-inner' ),
			)
		);

		return $classes;
	}

	private function getContentClass() {
		$class = Hooks::prefixed_apply_filters( 'main_content_class', array() );

		if ( ! is_array( $class ) ) {
			$class = (array) $class;
		}

		array_push( $class, 'colibri-main-content-archive' );

		return $class;
	}

	public function parentRender() {
		parent::render();
	}
}
