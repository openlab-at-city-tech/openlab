<?php 
namespace ElementsKit_Lite;

defined( 'ABSPATH' ) || exit;

class ElementsKit_HeaderFooterBuilder_Api extends Core\Handler_Api {

	public function config() {
		$this->prefix = 'my-template';
		$this->param  = '/(?P<id>\w+)/';
	}

	public function get_update() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$id          = $this->request['id'];
		$open_editor = $this->request['open_editor'];

		$title                 = ( $this->request['title'] == '' ) ? ( 'ElementsKit_Lite Template #' . time() ) : $this->request['title'];
		$activation            = $this->request['activation'];
		$type                  = $this->request['type'];
		$condition_a           = ( $type == 'section' ) ? '' : $this->request['condition_a'];
		$condition_singular    = ( $type == 'section' ) ? '' : $this->request['condition_singular'];
		$condition_singular_id = ( $type == 'section' ) ? '' : ( is_array( $this->request['condition_singular_id'] ) ? implode( ',', $this->request['condition_singular_id'] ) : $this->request['condition_singular_id'] );
		
		$post_data = array(
			'post_title'  => $title,
			'post_status' => 'publish',
			'post_type'   => 'elementskit_template',
		);

		$post = get_post( $id );
		
		if ( $post == null ) {
			// $post_data['post_author'] = $this->request['post_author'];
			$id = wp_insert_post( $post_data );
		} else {
			$post_data['ID'] = $id;
			wp_update_post( $post_data );
		}
		
		update_post_meta( $id, '_wp_page_template', 'elementor_canvas' );
		update_post_meta( $id, 'elementskit_template_activation', $activation );
		update_post_meta( $id, 'elementskit_template_type', $type );
		update_post_meta( $id, 'elementskit_template_condition_a', $condition_a );
		update_post_meta( $id, 'elementskit_template_condition_singular', $condition_singular );
		update_post_meta( $id, 'elementskit_template_condition_singular_id', $condition_singular_id );

		// if wpml is active and wpml not set for this post
		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			global $sitepress;
			$wpml_element_type = apply_filters( 'wpml_element_type', 'elementskit_template' );
			$sitepress->set_element_language_details( $id, $wpml_element_type, false, $sitepress->get_current_language(), null, false );
		}

		if ( $open_editor == 'true' ) {
			$url = get_admin_url() . '/post.php?post=' . $id . '&action=elementor';
			wp_safe_redirect( $url );
			exit;
		} else {
			$cond = ucwords(
				str_replace(
					'_',
					' ',
					$condition_a  
					. ( ( $condition_a == 'singular' )
					? ( ( $condition_singular != '' )
						? ( ' > ' . $condition_singular 
						. ( ( $condition_singular_id != '' )
							? ' > ' . $condition_singular_id
							: '' ) )
						: '' )
					: '' )
				)
			);

			return array(
				'saved' => true,
				'data'  => array(
					'id'         => $id,
					'title'      => $title,
					'type'       => $type,
					'activation' => $activation,
					'cond_text'  => $cond,
					'type_html'  => ( ucfirst( $type ) . ( ( $activation == 'yes' ) 
						? ( '<span class="ekit-headerfooter-status ekit-headerfooter-status-active">' . esc_html__( 'Active', 'elementskit-lite' ) . '</span>' ) 
						: ( '<span class="ekit-headerfooter-status ekit-headerfooter-status-inactive">' . esc_html__( 'Inactive', 'elementskit-lite' ) . '</span>' ) ) ),
				),
			);
		}
	}

	public function get_get() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$id   = $this->request['id'];
		$post = get_post( $id );
		if ( $post != null ) {
			return array(
				'title'                 => $post->post_title,
				'status'                => $post->post_status,
				'activation'            => get_post_meta( $post->ID, 'elementskit_template_activation', true ),
				'type'                  => get_post_meta( $post->ID, 'elementskit_template_type', true ),
				'condition_a'           => get_post_meta( $post->ID, 'elementskit_template_condition_a', true ),
				'condition_singular'    => get_post_meta( $post->ID, 'elementskit_template_condition_singular', true ),
				'condition_singular_id' => get_post_meta( $post->ID, 'elementskit_template_condition_singular_id', true ),
			);
		}
		return true;
	}

}
new ElementsKit_HeaderFooterBuilder_Api();
