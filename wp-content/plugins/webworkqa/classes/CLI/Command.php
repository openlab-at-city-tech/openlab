<?php

namespace WeBWorK\CLI;

use \WP_CLI;
use \WP_CLI_Command;

use \WeBWorK\Server\Question;
use \WeBWorK\Server\Response;

class Command {
	/**
	 * Get the URL for a question or response.
	 *
	 * @subcommand get-url
	 *
	 * <item-id>
	 * : ID of the item
	 */
	public function get_url( $args, $assoc_args ) {
		$item_id = (int) $args[0];

		$post = get_post( $item_id );

		if ( ! $post || ! in_array( $post->post_type, array( 'webwork_question', 'webwork_response' ) ) ) {
			WP_CLI::error( 'No WeBWorK question or response found by that ID.' );
		}

		$client_url = home_url();

		if ( 'webwork_question' === $post->post_type ) {
			$question = new Question( $post->ID );
			$url      = $question->get_url( $client_url );
		} else {
			$response = new Response( $post->ID );
			$question = new Question( $response->get_question_id() );
			$url      = $question->get_url( $client_url );
		}

		WP_CLI::success( "The URL is: $url" );
	}
}
