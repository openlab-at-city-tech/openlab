<?php

namespace Imagely\NGG\Widget;

use Imagely\NGG\Display\View;
use Imagely\NGG\Settings\Settings;

class MediaRSS extends \WP_Widget {

	public $options;

	public function __construct() {
		$widget_ops = [
			'classname'   => 'ngg_mrssw',
			'description' => \__( 'Widget that displays Media RSS links for NextGEN Gallery.', 'nggallery' ),
		];
		parent::__construct( 'ngg-mrssw', \__( 'NextGEN Media RSS', 'nggallery' ), $widget_ops );
	}

	/**
	 * @param array $instance
	 */
	public function form( $instance ) {
		// Default settings.
		$instance = \wp_parse_args(
			(array) $instance,
			[
				'mrss_text'        => \__( 'Media RSS', 'nggallery' ),
				'mrss_title'       => \__( 'Link to the main image feed', 'nggallery' ),
				'show_global_mrss' => true,
				'show_icon'        => true,
				'title'            => 'Media RSS',
			]
		);

		$view = new View(
			'Widget/Form/MediaRSS',
			[
				'self'       => $this,
				'instance'   => $instance,
				'title'      => \esc_attr( $instance['title'] ),
				'mrss_text'  => \esc_attr( $instance['mrss_text'] ),
				'mrss_title' => \esc_attr( $instance['mrss_title'] ),
			],
			'photocrati-widget#form_mediarss'
		);

		return $view->render();
	}

	/**
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                     = $old_instance;
		$instance['title']            = \strip_tags( $new_instance['title'] );
		$instance['show_global_mrss'] = $new_instance['show_global_mrss'];
		$instance['show_icon']        = $new_instance['show_icon'];
		$instance['mrss_text']        = $new_instance['mrss_text'];
		$instance['mrss_title']       = $new_instance['mrss_title'];

		return $instance;
	}

	/**
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// these are handled by extract() but I want to silence my IDE warnings that these vars don't exist.
		$before_widget = null;
		$before_title  = null;
		$after_widget  = null;
		$after_title   = null;
		$widget_id     = null;

		\extract( $args );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '&nbsp;' : $instance['title'], $instance, $this->id_base );

		$view = new View(
			'Widget/Display/MediaRSS',
			[
				'self'          => $this,
				'instance'      => $instance,
				'title'         => $title,
				'settings'      => Settings::get_instance(),
				'before_widget' => $before_widget,
				'before_title'  => $before_title,
				'after_widget'  => $after_widget,
				'after_title'   => $after_title,
				'widget_id'     => $widget_id,
			],
			'photocrati-widget#display_mediarss'
		);

		$view->render();
	}

	/**
	 * @param $mrss_url
	 * @param bool     $show_icon
	 * @param string   $title
	 * @param string   $text
	 * @return string
	 */
	public function get_mrss_link( $mrss_url, $show_icon = true, $title = '', $text = '' ) {
		$out = '';

		if ( $show_icon ) {
			$icon_url = NGGALLERY_URLPATH . 'images/mrss-icon.gif';
			$out     .= "<a href='{$mrss_url}' title='{$title}' class='ngg-media-rss-link'>";
			$out     .= "<img src='{$icon_url}' alt='MediaRSS Icon' title='" . $title . "' class='ngg-media-rss-icon' />";
			$out     .= '</a> ';
		}

		if ( '' !== $text ) {
			$out .= "<a href='{$mrss_url}' title='{$title}' class='ngg-media-rss-link'>";
			$out .= $text;
			$out .= '</a>';
		}

		return $out;
	}
}
