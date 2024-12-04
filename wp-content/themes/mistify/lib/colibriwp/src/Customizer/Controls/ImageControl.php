<?php


namespace ColibriWP\Theme\Customizer\Controls;

use WP_Customize_Image_Control;

class ImageControl extends WP_Customize_Image_Control {

	use ColibriWPControlsAdapter;

	public function to_json() {
		parent::to_json();

		$attachment = isset( $this->json['attachment'] ) ? $this->json['attachment'] : null;
		if ( ( ! $attachment ) ) {
			$this->updateAttachmentToAllowExternal();
		}

	}

	public function updateAttachmentToAllowExternal() {
		$url = $this->value();
		if ( ! ! $url ) {

			$external_attachment = array(
				'id'    => 1,
				'url'   => $url,
				'type'  => $this->mime_type,
				'icon'  => wp_mime_type_icon( $this->mime_type ),
				'title' => wp_basename( $url ),
			);
			if ( 'image' === $this->mime_type ) {
				$external_attachment['sizes'] = array(
					'full' => array( 'url' => $url ),
				);
			}
			$this->json['attachment'] = $external_attachment;
		}
	}
}
