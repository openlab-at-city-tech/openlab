<?php


namespace ColibriWP\Theme\Customizer\Controls;


class VideoControl extends ImageControl {

	// use ColibriWPControlsAdapter;

	public $mime_type = 'video';

	public function to_json() {
		parent::to_json();
		$attachment = isset( $this->json['attachment'] ) ? $this->json['attachment'] : null;

		/*
		 * When external video is used an attachment is returned with type document. So if no attachment is returned or if
		 * the type is document we make the assumption that an external url is used
		 */
		if ( ! $attachment || ( $attachment && $attachment['type'] === 'document' ) ) {
			$this->updateAttachmentToAllowExternal();
		}

	}
}
