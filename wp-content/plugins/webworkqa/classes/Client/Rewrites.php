<?php

namespace WeBWorK\Client;

class Rewrites {
	// @todo get out of constructor
	public function __construct() {
		$this->add_rewrite_rule();
		add_filter( 'query_vars', array( $this, 'register_query_var' ) );
	}

	protected function add_rewrite_rule() {
		add_rewrite_rule(
			'webwork/problems/([^/]+)/?$',
			'index.php?pagename=webwork&ww_problem=$matches[1]',
			'top'
		);
	}

	public function register_query_var( $vars ) {
		$vars[] = 'ww_problem';
		return $vars;
	}
}
