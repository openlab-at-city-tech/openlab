<?php 
namespace ElementsKit_Lite;

defined( 'ABSPATH' ) || exit;

class ElementsKit_Cpt_Api extends Core\Handler_Api {

	public function config() {
		$this->prefix = 'dynamic-content';
		$this->param  = '/(?P<type>\w+)/(?P<key>\w+(|[-]\w+))/';
	}

	public function get_content_editor() {
		
		if (current_user_can('edit_posts')) {
			$content_key  = $this->request['key'];
			$content_type = $this->request['type'];
			$builder_post_title = 'dynamic-content-' . $content_type . '-' . $content_key;
			$builder_post_id    = Utils::get_page_by_title( $builder_post_title, 'elementskit_content' );

			if ( is_null( $builder_post_id ) ) {
				$defaults        = array(
					'post_content' => '',
					'post_title'   => $builder_post_title,
					'post_status'  => 'publish',
					'post_type'    => 'elementskit_content',
				);
				$builder_post_id = wp_insert_post( $defaults );
				update_post_meta( $builder_post_id, '_wp_page_template', 'elementor_canvas' );
			} else {
				$builder_post_id = $builder_post_id->ID;
			}
		} else {
			wp_die( esc_html__( 'You are not allowed to access this page.', 'elementskit-lite' ) );
		}

		// if wpml is active and wpml not set for this post
		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			$builder_post_id = $this->set_wpml_data($builder_post_id);
		}

		$url = admin_url( 'post.php?post=' . $builder_post_id . '&action=elementor' );
		wp_safe_redirect( $url );
		exit;
	}

	public function set_wpml_data($builder_post_id) {
		global $sitepress;
		$default_language = $sitepress->get_default_language();
		$wpml_element_type = apply_filters( 'wpml_element_type', 'elementskit_content' );
		$trid = $sitepress->get_element_trid( $builder_post_id, $wpml_element_type );
		if( ! $trid ) {
			$sitepress->set_element_language_details( $builder_post_id, $wpml_element_type, false, $default_language, null, false );
		}

		// get wpml post by language code
		$referer = wp_get_referer();
		$referer = wp_parse_url($referer);
		$referer = !empty($referer['query']) ? $referer['query'] : '';
		$referer = parse_str($referer, $referer_args);

		if( !empty($referer_args['post']) ) {
			$language_details = apply_filters( 'wpml_post_language_details', NULL, $referer_args['post'] );
			if( !is_wp_error($language_details) ) {
				$builder_post_id = apply_filters( 'wpml_object_id', $builder_post_id, 'elementskit_content', true, $language_details['language_code'] );
			}
		}

		return $builder_post_id;
	}
}
new ElementsKit_Cpt_Api();
