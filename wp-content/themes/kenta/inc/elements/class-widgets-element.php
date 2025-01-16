<?php
/**
 * Widgets element
 *
 * @package Kenta
 */

use LottaFramework\Customizer\GenericBuilder\Element;
use LottaFramework\Facades\CZ;
use LottaFramework\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Widgets_Element' ) ) {

	class Kenta_Widgets_Element extends Element {

		use Kenta_Widgets_Controls;

		/**
		 * {@inheritDoc}
		 */
		public function getControls() {
			return $this->getWidgetsControls( [
				'css-selective-refresh' => 'kenta-footer-selective-css',
				'async-selector'        => '.' . $this->slug,
				'widgets-style'         => 'ghost',
				'scroll-reveal'         => 'no',
				'widgets-padding'       => [
					'top'    => '0px',
					'right'  => '0px',
					'bottom' => '0px',
					'left'   => '0px',
					'linked' => true
				],
			] );
		}

		/**
		 * @param null $id
		 * @param array $data
		 */
		public function after_register( $id = null, $data = [] ) {
			$id = $id ?? $this->slug;

			$options  = $this->getOptions();
			$settings = $data['settings'] ?? [];

			add_action( 'widgets_init', function () use ( $id, $options, $settings ) {

				$widgets_class = 'kenta-widget clearfix %2$s';

				if ( $options->checked( $this->getSlug( 'scroll-reveal' ) ) ) {
					$widgets_class = 'kenta-scroll-reveal ' . $widgets_class;
				}

				$title_class = 'widget-title mb-half-gutter heading-content';
				$tag         = $options->get( $this->getSlug( 'title-tag' ), $settings );

				register_sidebar( [
					'name'          => $this->getLabel(),
					'id'            => $id,
					'before_widget' => '<aside id="%1$s" class="' . $widgets_class . '">',
					'after_widget'  => '</aside>',
					'before_title'  => '<' . $tag . ' class="' . $title_class . '">',
					'after_title'   => '</' . $tag . '>',
				] );
			} );
		}

		protected function getOptions() {
			return CZ::getFacadeRoot();
		}

		protected function getSidebarId( $attrs = [] ) {
			return $this->slug;
		}

		protected function getAttrId( $attrs = [] ) {
			return $this->slug;
		}

		protected function beforeRender( $attrs = [] ) {
			$attrs['class'] = Utils::clsx( [
				'no-underline' => ! CZ::checked( $this->getSlug( 'link-underline' ) ),
				'kenta-heading',
				'kenta-heading-' . CZ::get( $this->getSlug( 'title-style' ) ),
				$this->slug
			], $attrs['class'] ?? [] );

			foreach ( $attrs as $attr => $value ) {
				$this->add_render_attribute( $this->slug, $attr, $value );
			}
		}
	}
}
