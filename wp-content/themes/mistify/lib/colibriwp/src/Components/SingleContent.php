<?php

namespace ColibriWP\Theme\Components;

use ColibriWP\Theme\Core\Hooks;
use ColibriWP\Theme\Theme;
use ColibriWP\Theme\View;

class SingleContent extends MainContent {

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

										Theme::getInstance()->get( 'single-template' )->render();

									}
								);

								/** COLUMN END */
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

		array_push( $class, 'colibri-main-content-single' );

		return $class;
	}

}
