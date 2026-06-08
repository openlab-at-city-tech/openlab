<?php

namespace RebelCode\Spotlight\Instagram\Utils;

class Types
{
    public static function getType($var): string
    {
        return is_object($var) ? get_class($var) : gettype($var);
    }
}
