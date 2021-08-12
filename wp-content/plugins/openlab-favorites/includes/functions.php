<?php

namespace OpenLab\Favorites;

use OpenLab\Favorites\Favorite\Query;

function user_has_favorited_group( $user_id, $group_id ) {
	$results = Query::get_results(
		[
			'user_id'  => $user_id,
			'group_id' => $group_id,
		]
	);

	return ! empty( $results );
}

function remove_favorite( $user_id, $group_id ) {
	$results = Query::get_results(
		[
			'user_id'  => $user_id,
			'group_id' => $group_id,
		]
	);

	if ( ! $results ) {
		return false;
	}

	$to_delete = count( $results );
	$deleted   = 0;
	foreach ( $results as $result ) {
		if ( $result->delete() ) {
			$deleted++;
		}
	}

	return $deleted === $to_delete;
}
