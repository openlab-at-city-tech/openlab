<?php

namespace ColibriWP\Theme\Components;

use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\Core\Hooks;
use ColibriWP\Theme\Translations;
use ColibriWP\Theme\View;

class PageContent extends ComponentBase {

	public static function selectiveRefreshSelector() {
		return '.colibri-page-content';
	}

	/**
	 * @return array();
	 */
	protected static function getOptions() {
		$prefix = 'page_content_';

		return array(
			'sections' => array(
				'page_content_section' => array(
					'title' => Translations::get( 'content_settings' ),
					'panel' => 'content_panel',
					'type'  => 'colibri_section',
				),
			),

			'settings' => array(
				'page_content_pen'        => array(
					'control' => array(
						'type'        => 'pen',
						'section'     => "{$prefix}section",
						'colibri_tab' => 'content',
					),

				),

				"{$prefix}plugin-content" => array(
					'control' => array(
						'type'        => 'plugin-message',
						'section'     => "{$prefix}section",
						'colibri_tab' => 'content',
					),
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

	public function renderContent( $parameters = array() ) {
		$self = $this;

		add_filter( 'the_content', array( $this, 'wrapPostContent' ), 100 );

		View::printIn(
			View::CONTENT_ELEMENT,
			function () use ( $self ) {
				View::printIn(
					View::SECTION_ELEMENT,
					function () {
						View::printIn(
							View::ROW_ELEMENT,
							function () {
								View::printIn(
									View::COLUMN_ELEMENT,
									function () {
										while ( have_posts() ) :
											the_post();
											\ColibriWP\Theme\View::partial( 'content', 'content-page' );
										endwhile;
									}
								);
							}
						);
					},
					$self->getPageSectionClass()
				);
				get_template_part( 'comments-page' );
			},
			array(
				'class' => array( 'page-content', 'colibri-page-content' ),
			)
		);

		remove_filter( 'the_content', array( $this, 'wrapPostContent' ), 100 );
	}

	public function wrapPostContent( $content ) {
		ob_start();

		wp_link_pages(
			array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'mistify' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'mistify' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text">, </span>',
			)
		);

			$link_pages = ob_get_clean();
		return "<div class='kubio-post-content entry-content'>{$content} {$link_pages}</div>";
	}

	private function getPageSectionClass() {

		$classes = Hooks::prefixed_apply_filters(
			'page_section_class',
			array(
				'outer_class' => array(),
				'inner_class' => array( 'h-section-boxed-container' ),
			)
		);

		return $classes;
	}
}
