<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * oEmbed handler to respond and render group events.
 */
class BPEO_Group_oEmbed_Extension extends BP_Core_oEmbed_Extension {
	/**
	 * Custom oEmbed slug endpoint.
	 *
	 * @var string
	 */
	public $slug_endpoint = 'eo-events';

	/**
	 * Custom hooks.
	 */
	protected function custom_hooks() {
		add_action( 'oembed_dataparse',        array( $this, 'remove_iframe_attributes' ), 20, 3 );

		add_filter( 'bp_eo-events_embed_html', array( $this, 'modify_oembed_html' ), 10, 2 );
	}

	/**
	 * Check if we're on a group events page.
	 *
	 * @return bool
	 */
	protected function is_page() {
		return bp_is_group() && bp_is_current_action( 'events' ) && ( bp_is_action_variable( 'upcoming' ) || false === bp_action_variables() );
	}

	/**
	 * Validates the URL to determine if the group event page is valid.
	 *
	 * @param  string   $url The URL to check.
	 * @return int|bool Group ID on success; boolean false on failure.
	 */
	protected function validate_url_to_item_id( $url ) {
		// Check the URL to see if we're on a group page.
		if ( 0 !== strpos( $url, bp_get_groups_directory_permalink() ) ) {
			return false;
		}

		// Get URI path.
		$path = untrailingslashit( str_replace( bp_get_groups_directory_permalink(), '', $url ) );
		$path = explode( '/', $path );

		// Not an event page, so bail!
		if ( empty( $path[1] ) || bpeo_get_events_slug() !== $path[1] ) {
			return false;
		}
		if ( ! empty( $path[2] ) && 'upcoming' !== $path[2] ) {
			return false;
		}

		// Check if group exists.
		if ( ! empty( $path[0] ) && $group_id = BP_Groups_Group::group_exists( $path[0] ) ) {
			$group = new BP_Groups_Group( $group_id );

			// Okay, we're good to go!
			if ( ! empty( $group->id ) && 'public' === $group->status ) {
				return $group->id;
			}
		}

		return false;
	}

	/**
	 * Sets the oEmbed response data for our group events calendar.
	 *
	 * @param  int $item_id The group ID.
	 * @return array
	 */
	protected function set_oembed_response_data( $item_id ) {
		$group = new BP_Groups_Group( $item_id );

		$group_name = bp_get_group_name( $group );

		return array(
			'title'        => sprintf( __( '%s - Group Calendar', 'bp-event-organiser' ), $group_name ),
			'author_name'  => $group_name,
			'author_url'   => bpeo_get_group_permalink( $group ),
			'content'      => '',

			// Custom identifier.
			'x_type' => $this->slug_endpoint
		);
	}

	/**
	 * Remove some <iframe> attributes in our oEmbed response.
	 *
	 * WordPress sets the <iframe> sandbox attribute to 'allow-scripts' regardless
	 * of whatever the oEmbed response is in {@link wp_filter_oembed_result()}. We
	 * need to remove some iframe attributes so our embedded calendar will work.
	 *
	 * @param string $result The oEmbed HTML result.
	 * @param object $data   A data object result from an oEmbed provider.
	 * @param string $url    The URL of the content to be embedded.
	 * @return string
	 */
	public function remove_iframe_attributes( $result, $data, $url ) {
		// Make sure we are on our EO oEmbed request.
		if ( false === isset( $data->x_type ) || $this->slug_endpoint !== $data->x_type ) {
			return $result;
		}

		// Remove sandbox attribute.
		$result = str_replace( ' sandbox="allow-scripts"', '', $result );

		// Also remove 'security' attribute; this is only used for IE < 10.
		$result = str_replace( 'security="restricted"', '', $result );

		return $result;
	}

	/**
	 * Modify oEmbed HTML.
	 *
	 * Removes <iframe> sandbox, title and class attributes and switches our
	 * <iframe> src to use our custom embed trigger query argument.  Also
	 * removes WP's embed JS since that isn't applicable for our needs.
	 *
	 * @param  string $retval Current embed HTML.
	 * @return string
	 */
	public function modify_oembed_html( $retval ) {
		// Remove a bunch of stuff from WP's default oEmbed HTML response.
		$find = array(
			' title="-"',
			' class="wp-embedded-content"',
			' sandbox="allow-scripts" security="restricted"',
			' scrolling="no"'
		);
		$retval = str_replace( $find, '', $retval );

		// Switch '?embed' to '?embedded' since we use 'embedded' as our embed trigger.
		$retval = str_replace( '?embed=', '?embedded=', $retval );

		// Use only the <iframe> and none of WP's embed JS.
		$retval = substr( $retval, strpos( $retval, '<iframe' ) );

		return $retval;
	}

	/** NOT APPLICABLE ******************************************************/

	/**
	 * We don't use any fancy template parts here, so plug it and go.
	 */
	protected function content() {}

	/**
	 * We don't use any fallback HTML for our oEmbed content, so return nothing.
	 *
	 * @param  int $item_id The group ID
	 * @return string
	 */
	protected function set_fallback_html( $item_id ) {
		return '';
	}

	/**
	 * We don't use a custom title attribute for our iframe, so return a dash.
	 *
	 * @param  int $item_id The group ID
	 * @return string
	 */
	protected function set_iframe_title( $item_id ) {
		return '-';
	}
}
