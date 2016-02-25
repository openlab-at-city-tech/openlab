<?php

namespace OLUR;

trait CounterTools {
	protected $start;
	protected $end;
	protected $label;
	protected $counts;

	public function set_start( $start ) {
		$this->start = $start;
	}

	public function set_end( $end ) {
		$this->end = $end;
	}

	public function set_label( $label ) {
		$this->label = $label;
	}

	public function format_results_for_csv() {
		$retval = array_merge( array( $this->label ), $this->counts );
		return $retval;
	}
}
