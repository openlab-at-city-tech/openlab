<?php

namespace Smashballoon\Stubs\Traits;

trait Singleton
{
    protected static $instance;
    public static function getInstance()
    {
        return isset(static::$instance) ? static::$instance : static::$instance = new static();
    }
    private function __construct()
    {
        $this->init();
    }
    protected function init()
    {
    }
}
