<?php


namespace ColibriWP\Theme\Components;

use ColibriWP\Theme\Core\Hooks;
use ColibriWP\Theme\View;

class WooContent extends MainContent {

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

								$self->printSidebarColumn( 'left' );

								/** COLUMN START */
								View::printIn(
									View::COLUMN_ELEMENT,
									function () use ( $self ) {

										if ( function_exists( 'woocommerce_content' ) ) {
											woocommerce_content();
										}
									}
								);

								// $self->printSidebarColumn("right");
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

	public function printSidebarColumn( $side = 'right' ) {
		$self = $this;

		$sidebar_id      = 'ecommerce-' . $side;
		$is_active       = is_active_sidebar( "colibri-{$sidebar_id}" );
		$in_customizer   = isset( $GLOBALS['wp_customize'] );
		$is_active       = $is_active || $in_customizer;
		$display_sidebar = Hooks::prefixed_apply_filters( 'colibri_sidebar_enabled', $is_active, $sidebar_id );

		if ( $display_sidebar ) {
			View::printIn(
				View::COLUMN_ELEMENT,
				function () use ( $self, $sidebar_id ) {
					get_sidebar( $sidebar_id );
				},
				array(
					'data-colibri-main-sidebar-col' => 1,
					'class'                         => $self->getSidebarColumnClass( $side ),
				)
			);
		}

	}

	private function getSidebarColumnClass( $side ) {

		$classes = (array) Hooks::prefixed_apply_filters(
			'woocommerce_sidebar_column_class',
			array( 'h-col-12', 'h-col-lg-3', 'h-col-md-4' ),
			$side
		);

		$classes = array_merge( $classes, array( 'colibri-sidebar', "woo-sidebar-{$side}" ) );

		return array_unique( $classes );
	}

	private function getMainRowClass() {
		$classes = Hooks::prefixed_apply_filters(
			'woocommerce_main_row_class',
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
			'woocommerce_main_section_class',
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
		$class = Hooks::prefixed_apply_filters( 'woocommerce_main_content_class', array() );

		if ( ! is_array( $class ) ) {
			$class = (array) $class;
		}

		array_push( $class, 'colibri-woo-main-content-archive' );

		return $class;
	}
}
