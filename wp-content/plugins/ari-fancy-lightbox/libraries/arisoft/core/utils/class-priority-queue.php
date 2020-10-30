<?php
namespace Ari\Utils;

use SplPriorityQueue;
use Countable;
use IteratorAggregate;

class Priority_Queue implements Countable, IteratorAggregate {
    protected $queue;

    public function __construct() {
        $this->queue = new SplPriorityQueue;
    }

    public function count() {
        return count( $this->queue );
    }

    public function insert( $data, $priority ) {
        $this->queue->insert( $data, $priority );
    }

    public function getIterator() {
        return clone $this->queue;
    }
}
