<?php

namespace Imagely\NGG\IGW;

use Imagely\NGG\Display\StaticAssets;
use Imagely\NGG\Display\View;

class Marketing {

	public function new_pro_display_type_upsell( $id, $name, $title = '', $preview_mvc_path = null ) {
		return [
			'ID'                => $id,
			'default_source'    => 'galleries',
			'entity_types'      => [ 'image' ],
			'hidden_from_igw'   => false,
			'hidden_from_ui'    => false,
			'name'              => $name,
			'title'             => $title,
			'preview_image_url' => $preview_mvc_path ? StaticAssets::get_url( $preview_mvc_path ) : '',
		];
	}

	public function get_pro_display_types() {
		return [
			$this->new_pro_display_type_upsell(
				-1,
				'pro-tile',
				__( 'Pro Tile', 'nggallery' ),
				'IGW/Marketing/pro-tile-preview.jpg'
			),
			$this->new_pro_display_type_upsell(
				-2,
				'pro-mosaic',
				__( 'Pro Mosaic', 'nggallery' ),
				'IGW/Marketing/pro-mosaic-preview.jpg'
			),
			$this->new_pro_display_type_upsell(
				-3,
				'pro-masonry',
				__( 'Pro Masonry', 'nggallery' ),
				'IGW/Marketing/pro-masonry-preview.jpg'
			),
			$this->new_pro_display_type_upsell(
				-4,
				'igw-promo'
			),
		];
	}

	public function get_marketing_cards() {
		$pro_tile = new \C_Marketing_Block_Popup(
			__( 'Pro Tile Gallery', 'nggallery' ),
			\M_Marketing::get_i18n_fragment( 'feature_not_available', __( 'the Pro Tile Gallery', 'nggallery' ) ),
			\M_Marketing::get_i18n_fragment( 'lite_coupon' ),
			StaticAssets::get_url( 'IGW/Marketing/pro-tile-preview.jpg' ),
			'igw',
			'tiledgallery'
		);

		$pro_masonry = new \C_Marketing_Block_Popup(
			__( 'Pro Masonry Gallery', 'nggallery' ),
			\M_Marketing::get_i18n_fragment( 'feature_not_available', __( 'the Pro Masonry Gallery', 'nggallery' ) ),
			\M_Marketing::get_i18n_fragment( 'lite_coupon' ),
			StaticAssets::get_url( 'IGW/Marketing/pro-masonry-preview.jpg' ),
			'igw',
			'masonrygallery'
		);

		$pro_mosaic = new \C_Marketing_Block_Popup(
			__( 'Pro Mosaic Gallery', 'nggallery' ),
			\M_Marketing::get_i18n_fragment( 'feature_not_available', __( 'the Pro Mosaic Gallery', 'nggallery' ) ),
			\M_Marketing::get_i18n_fragment( 'lite_coupon' ),
			StaticAssets::get_url( 'IGW/Marketing/pro-mosaic-preview.jpg' ),
			'igw',
			'mosaicgallery'
		);

		return [
			'pro-tile'    => '<div>' . $pro_tile->render() . '</div>',
			'pro-masonry' => '<div>' . $pro_masonry->render() . '</div>',
			'pro-mosaic'  => '<div>' . $pro_mosaic->render() . '</div>',
		];
	}

	public function enqueue_display_tab_js() {
		$view = new View( 'IGW/marketing' );

		$data = [
			'display_types' => $this->get_pro_display_types(),
			'i18n'          => [
				'get_pro' => __( 'Requires NextGEN Pro', 'nggallery' ),
			],
			'templates'     => $this->get_marketing_cards(),
			'igw_promo'     => $view->render( true ),
		];

		\wp_enqueue_style( 'jquery-modal' );

		\wp_enqueue_script(
			'igw_display_type_upsells',
			StaticAssets::get_url( 'IGW/Marketing/igw_display_type_upsells.js' ),
			[ 'ngg_display_tab', 'jquery-modal' ],
			NGG_SCRIPT_VERSION
		);

		\wp_localize_script(
			'igw_display_type_upsells',
			'igw_display_type_upsells',
			$data
		);

		\M_Marketing::enqueue_blocks_style();

		\wp_add_inline_style(
			'ngg_attach_to_post',
			'.display_type_preview:nth-child(5) {clear: both;} .ngg-marketing-block-display-type-settings label {color: darkgray !important;}'
		);
	}
}
