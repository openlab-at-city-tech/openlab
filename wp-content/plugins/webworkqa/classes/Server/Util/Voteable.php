<?php

namespace WeBWorK\Server\Util;

/**
 * Voteable interface.
 *
 * @since 1.0.0
 */
interface Voteable {
	public function get_id();
	public function get_vote_count( $force_query = false );
}
