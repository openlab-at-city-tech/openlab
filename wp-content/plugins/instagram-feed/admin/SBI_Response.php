<?php

/**
 * Class SBI_Response
 *
 * Sends back ajax response to client end
 *
 * @since 6.0
 */

namespace InstagramFeed;

use Exception;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

class SBI_Response
{
	/**
	 * @var boolean
	 */
	private $is_success;

	/**
	 * @var array
	 */
	private $data;

	/**
	 * Response constructor.
	 *
	 * @param $is_success
	 * @param $data
	 *
	 * @throws Exception
	 */
	public function __construct($is_success, $data)
	{
		$this->is_success = $is_success;
		$this->data = $data;
	}

	/**
	 * Send JSON response
	 */
	public function send()
	{
		if ($this->is_success) {
			wp_send_json_success($this->data);
		}
		wp_send_json_error($this->data);
	}
}
