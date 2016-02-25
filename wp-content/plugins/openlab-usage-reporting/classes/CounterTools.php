<?php

namespace OLUR;

trait CounterTools {
	protected $start;
	protected $end;
	protected $label;

	public function set_start( $start ) {
		$this->start = $start;
	}

	public function set_end( $end ) {
		$this->end = $end;
	}

	public function set_label( $label ) {
		$this->label = $label;
	}

	protected function format_results( $results ) {
		$retval = array( $this->label );
		$retval = array_merge( $retval, $results );
		return $retval;
	}
}
