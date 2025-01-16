<?php

namespace LottaFramework\Facades;

/**
 * @method static users(): array
 * @method static customPostTypes( $query, $exclude_defaults = true ): array
 * @method static termsByTaxonomy( $slug, $per_page = - 1, $hide_empty = false ): array
 * @method static postsByPostType( $slug, $per_page = - 1 ): array
 * @method static postList( $post_type = 'any', $limit = - 1, $search = '' ): array
 */
class Query extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return \LottaFramework\Query::class;
	}
}