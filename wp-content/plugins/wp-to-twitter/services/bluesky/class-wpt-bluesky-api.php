<?php
/**
 * Bluesky access class.
 *
 * @category OAuth
 * @package  XPoster
 * @author   https://github.com/Eleirbag89, documented and adapted to WP code style.
 * @license  GPLv3
 * @link     https://www.joedolson.com/wp-to-twitter/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * OAuth / Wpt_Bluesky_Api
 *
 * A simple library to send status updates to Bluesky instances.
 *
 * @author eleirbag89
 * @version 0.1
 * @link https://github.com/Eleirbag89/BlueskyBotPHP
 */
class Wpt_Bluesky_Api {
	/**
	 * Username/handle for Bluesky instance.
	 *
	 * @var string
	 */
	private $username;

	/**
	 * App password for Bluesky instance.
	 *
	 * @var string
	 */
	private $app_password;

	/**
	 * Construct.
	 *
	 * @param string $username Access token for Bluesky instance.
	 * @param string $app_password App password for Bluesky.
	 */
	public function __construct( $username, $app_password ) {
		$this->username     = $username;
		$this->app_password = $app_password;
	}

	/**
	 * Parse links in a status string into Bluesky facets.
	 *
	 * @param array  $facets Existing Bluesky post facets.
	 * @param string $text Text string to be parsed.
	 *
	 * @return array $post
	 */
	public function parse_links( $facets, $text ) {
		$regex = '/(https?:\/\/[^\s]+)/';
		preg_match_all( $regex, $text, $matches, PREG_OFFSET_CAPTURE );
		$links = array();

		foreach ( $matches[0] as $match ) {
			$urlstring = $match[0];
			$start     = $match[1];
			$end       = $start + strlen( $urlstring );

			$links[] = array(
				'start' => $start,
				'end'   => $end,
				'url'   => $urlstring,
			);
		}

		if ( ! empty( $links ) ) {
			$new_facets = array();
			foreach ( $links as $link ) {
				$new_facets[] = array(
					'index'    => array(
						'byteStart' => $link['start'],
						'byteEnd'   => $link['end'],
					),
					'features' => array(
						array(
							'$type' => 'app.bsky.richtext.facet#link',
							'uri'   => $link['url'],
						),
					),
				);
			}
			$facets = array_merge( $facets, $new_facets );
		}

		return $facets;
	}

	/**
	 * Parse mentions in a status string into Bluesky facets.
	 *
	 * @param array  $facets Existing Bluesky post facets.
	 * @param string $text Text string to be parsed.
	 *
	 * @return array $post
	 */
	public function parse_mentions( $facets, $text ) {
		$regex = '/(^|\s|\()(@)([a-zA-Z0-9.-]+)(\b)/';
		preg_match_all( $regex, $text, $matches, PREG_OFFSET_CAPTURE );

		$mentions = array();
		foreach ( $matches[0] as $match ) {
			$handle = $match[0];
			$start  = $match[1];
			$end    = $start + strlen( $handle );

			$mentions[] = array(
				'start'  => $start,
				'end'    => $end,
				'handle' => $handle,
			);
		}

		if ( ! empty( $mentions ) ) {
			$new_facets = array();
			foreach ( $mentions as $mention ) {
				$post     = array(
					'handle' => trim( str_replace( '@', '', $handle ) ),
				);
				$did      = add_query_arg( $post, 'https://bsky.social/xrpc/com.atproto.identity.resolveHandle' );
				$response = json_decode( wp_remote_get( $did )['body'] );
				$id       = ( property_exists( $response, 'did' ) ) ? $response->did : false;
				if ( ! $id ) {
					continue;
				}
				$new_facets[] = array(
					'index'    => array(
						'byteStart' => $mention['start'],
						'byteEnd'   => $mention['end'],
					),
					'features' => array(
						array(
							'$type' => 'app.bsky.richtext.facet#mention',
							'did'   => $id,
						),
					),
				);
			}
			$facets = array_merge( $facets, $new_facets );
		}

		return $facets;
	}

	/**
	 * Parse tags in a status string into Bluesky facets.
	 *
	 * @param array  $facets Existing Bluesky post facets.
	 * @param string $text Text string to be parsed.
	 *
	 * @return array $post
	 */
	public function parse_tags( $facets, $text ) {
		$regex = '/(?:^|\s)(#[^\d\s]\S*)(?=\s)?/';
		preg_match_all( $regex, $text, $matches, PREG_OFFSET_CAPTURE );
		$tags = array();

		foreach ( $matches[0] as $match ) {
			$tag    = $match[0];
			$tag    = trim( $tag, '.;:,-_!?' );
			$length = strlen( $tag );
			// Bluesky doesn't allow tags longer than 64 characters, not including #.
			if ( $length > 65 ) {
				continue;
			}
			$start = $match[1];
			$end   = $start + strlen( $tag );

			$tags[] = array(
				'start' => $start,
				'end'   => $end,
				'tag'   => $tag,
			);
		}

		if ( ! empty( $tags ) ) {
			$new_facets = array();
			foreach ( $tags as $tag ) {
				$new_facets[] = array(
					'index'    => array(
						'byteStart' => $tag['start'],
						'byteEnd'   => $tag['end'],
					),
					'features' => array(
						array(
							'$type' => 'app.bsky.richtext.facet#tag',
							'tag'   => str_replace( '#', '', $tag['tag'] ),
						),
					),
				);
			}
			$facets = array_merge( $facets, $new_facets );
		}

		return $facets;
	}

	/**
	 * Post a status to the Bluesky status endpoint.
	 *
	 * @param array $status Array posted to Bluesky. [status,visibility,language,media_ids="[]"].
	 *
	 * @return array Bluesky response.
	 */
	public function post_status( $status ) {
		$facets = array();
		$post   = array(
			'collection' => 'app.bsky.feed.post',
			'repo'       => $this->username,
			'record'     => $status,
		);
		// Parse links into facets.
		$facets = $this->parse_links( $facets, $status['text'] );
		$facets = $this->parse_mentions( $facets, $status['text'] );
		$facets = $this->parse_tags( $facets, $status['text'] );

		if ( ! empty( $facets ) ) {
			$post['record']['facets'] = $facets;
		}
		$response = $this->call_api( 'https://bsky.social/xrpc/com.atproto.repo.createRecord', $post );

		return $response;
	}

	/**
	 * Get a Bluesky token.
	 *
	 * @return array Bluesky response.
	 */
	public function verify() {
		$args     = array(
			'identifier'   => $this->username,
			'password'     => $this->app_password,
			'verification' => true,
		);
		$response = $this->call_api( 'https://bsky.social/xrpc/com.atproto.server.createSession', $args );

		return $response;
	}

	/**
	 * Post to the API endpoint.
	 *
	 * @param string $endpoint REST API path.
	 * @param array  $data Data being posted.
	 *
	 * @return array Bluesky response or error.
	 */
	public function call_api( $endpoint, $data ) {
		// Verification gets the bearer token, and does not require it.
		if ( isset( $data['verification'] ) ) {
			$headers = array(
				'Content-Type' => 'application/json',
			);
			// Remove verification flag and encode data.
			unset( $data['verification'] );
			$data = wp_json_encode( $data );
		} else {
			if ( isset( $data['content-type'] ) ) {
				// Media uploads are passed with a content-type of the object uploaded.
				$headers = array(
					'Content-Type'  => $data['content-type'],
					'Authorization' => 'Bearer ' . $this->verify()['accessJwt'],
				);
				unset( $data['content-type'] );
				$data = $data['data'];
			} else {
				$headers = array(
					'Authorization'  => 'Bearer ' . $this->verify()['accessJwt'],
					'Content-Type'   => 'application/json',
					'Accept'         => 'application/json',
					'Accept-Charset' => 'utf-8',
				);
				$data    = wp_json_encode( $data );
			}
		}

		$reply = wp_remote_post(
			$endpoint,
			array(
				'method'  => 'POST',
				'headers' => $headers,
				'body'    => $data,
			)
		);

		if ( is_wp_error( $reply ) ) {
			$error = array(
				'ok'         => false,
				'error_code' => $reply->get_error_code(),
				'error'      => $reply->get_error_message(),
			);
			return wp_json_encode( $error );
		}

		return json_decode( wp_remote_retrieve_body( $reply ), true );
	}
}
