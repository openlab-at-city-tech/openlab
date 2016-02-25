<?php

namespace OLUR;

trait CounterTools {
	protected $start;
	protected $end;

	public function set_start( $start ) {
		$this->start = $start;
	}

	public function set_end( $end ) {
		$this->end = $end;
	}
}
