<?php
/**
 * Handles process urls of Origins for editing/unlinking.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.1.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Handlers
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Broken_Links_Actions\Handlers;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WP_Error;
use WP_Post;
use WP_User;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Traits\Execution_Time;
use WPMUDEV_BLC\App\Broken_Links_Actions\Link;
use WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Main as Processor;
use WPMUDEV_BLC\Core\Utils\Utilities;

/**
 * Class Full_Site_Handler
 *
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Handlers
 */
class Origins_Handler extends Base {
	/**
	 * Use the Dashboard_API Trait.
	 *
	 * @since 2.1
	 */
	use Execution_Time;

	/**
	 * The Link object
	 *
	 * @var object|null
	 */
	protected $link_object = null;

	/**
	 * Offset is used to check if process for current table is complete.
	 *
	 * @var int
	 */
	protected $offset = 0;

	/**
	 * Select limit.
	 *
	 * @var int
	 */
	protected $limit = 0;

	protected $origins = array();


	public function __construct( Link $link = null ) {
		$this->link_object = $link;
		$this->offset      = $this->link_object->get_offset();
		$this->limit       = $this->link_object->get_limit();
		$this->origins     = $this->link_object->origins();
	}

	/**
	 * Runs the url process in origins
	 *
	 * @return array
	 */
	public function execute() {
		$notfound_in = array();
		$new_offset  = intval( $this->offset );
		$origins     = array_slice( $this->origins, $this->offset, $this->limit );

		if ( ! empty( $origins ) ) {
			foreach ( $origins as $post_url ) {
				$new_offset ++;

				if ( ! $this->process_instance( null, $post_url ) ) {
					$notfound_in[] = $post_url;
				}
			}
		}

		$remaining_origins = array_slice( $this->origins, $this->offset + $this->limit );

		return array(
			//'completed'   => $status,
			'completed'   => empty( $remaining_origins ),
			'notfound_in' => $notfound_in,
			'offset'      => $new_offset,
			'break'       => true,
		);
	}

	/**
	 * @param int|null $post_id
	 * @param string|null $post_url
	 *
	 * @return bool
	 */
	public function process_instance( int $post_id = null, string $post_url = null ) {
		if ( empty( $post_url ) ) {
			return false;
		}

		$processor = new Processor( $this->link_object );

		if ( empty( $post_id ) ) {
			$author_page = Utilities::is_author_url( $post_url );

			if ( $author_page instanceof WP_User || $author_page instanceof WP_User ) {
				return $processor->execute_in_user_author_meta( $author_page->ID );
			}

			$post_id = url_to_postid( $post_url );
		}

		return $processor->execute_url_in_post( $post_id );
	}
}
