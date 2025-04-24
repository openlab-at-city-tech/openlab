<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\Control;
use LottaFramework\Customizer\Sanitizes;

/**
 * @since 2.1.0
 */
class QueryTags extends Control {
	public function getType(): string {
		return 'lotta-query-tags';
	}

	public function getSanitize() {
		return [ Sanitizes::class, 'tags' ];
	}

	public function setQuey( $query ) {
		return $this->setOption( 'query', $query );
	}
}