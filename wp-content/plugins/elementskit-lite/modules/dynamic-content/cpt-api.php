<?php 
namespace ElementsKit_Lite;

defined( 'ABSPATH' ) || exit;

class ElementsKit_Cpt_Api extends Core\Handler_Api {

	public function config() {
		$this->prefix = 'dynamic-content';
		$this->param  = '/(?P<type>\w+)/(?P<key>\w+(|[-]\w+))/';
	}

	public function get_content_editor() {
		$content_key  = $this->request['key'];
		$content_type = $this->request['type'];
		
		$builder_post_title = 'dynamic-content-' . $content_type . '-' . $content_key;
		$builder_post_id    = get_page_by_title( $builder_post_title, OBJECT, 'elementskit_content' );

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

		$url = get_admin_url() . '/post.php?post=' . $builder_post_id . '&action=elementor';
		wp_safe_redirect( $url );
		exit;
	}

}
new ElementsKit_Cpt_Api();
