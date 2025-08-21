<?php

namespace TEC\Common\StellarWP\Models\ValueObjects;

/**
 * Model Relationships
 *
 * @since 2.19.6
 *
 * @method static HAS_ONE();
 * @method static HAS_MANY();
 * @method static MANY_TO_MANY();
 * @method static BELONGS_TO();
 * @method static BELONGS_TO_MANY();
 */
class Relationship {
	const HAS_ONE         = 'has-one';
	const HAS_MANY        = 'has-many';
	const MANY_TO_MANY    = 'many-to-many';
	const BELONGS_TO      = 'belongs-to';
	const BELONGS_TO_MANY = 'belongs-to-many';
}
