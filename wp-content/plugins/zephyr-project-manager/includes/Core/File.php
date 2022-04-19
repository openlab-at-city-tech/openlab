<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Core;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use Inc\Zephyr;
use Inc\Core\Members;
use Inc\Core\Utillities;
use Inc\ZephyrProjectManager;

class File {
	public $id;
	private $type;
	private $user_id;
	private $created;
	private $subject;
	private $parent_id;
	private $subject_id;

	public function __construct($args) {
		$args = (object) $args;
		$this->id = property_exists($args, 'message') ? maybe_unserialize( $args->message ) : '-1';
		$this->type = property_exists($args, 'type') ? $args->type : serialize('attachment');
	}

	public function getUrl($id){
		return wp_get_attachment_url();
	}

	public function getType() {
		return maybe_unserialize( $this->type );
	}

	public function isType( $type ) {
		if ($this->getType() == $type) {
			return true;
		} else {
			return false;
		}
	}

	public function html() {

		$attachmentUrl = is_numeric($this->id) ? wp_get_attachment_url( $this->id ) : $this->id;
		$isImage = is_numeric($this->id) ? wp_attachment_is_image( $this->id ) :  zpm_is_image($attachmentUrl);

		
		ob_start();

		?>
		<div class="zpm-file-item">
			<div class="zpm-file zpm-hover__shadow">
				<?php if ($isImage) : ?>
					<!-- Image Preview -->
					<a class="zpm_link" href="<?php echo $attachmentUrl; ?>" download>
						<img class="zpm-image-attachment-preview" src="<?php echo $attachmentUrl; ?>" />
					</a>
				<?php else: ?>
					<!-- Attachment Link -->
					<a class="zpm_link" href="<?php echo $attachmentUrl; ?>" download><?php echo $attachmentUrl; ?></a>
				<?php endif; ?>
			</div>
		</div>
		<?php

		$content = ob_get_clean();
		$html = apply_filters( 'zpm_file_html', $content, $this );
		return $html;
	}
}