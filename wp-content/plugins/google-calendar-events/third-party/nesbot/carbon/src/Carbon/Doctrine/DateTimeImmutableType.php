<?php

/**
 * Thanks to https://github.com/flaushi for his suggestion:
 * https://github.com/doctrine/dbal/issues/2873#issuecomment-534956358
 */
namespace SimpleCalendar\plugin_deps\Carbon\Doctrine;

use SimpleCalendar\plugin_deps\Carbon\CarbonImmutable;
use SimpleCalendar\plugin_deps\Doctrine\DBAL\Types\VarDateTimeImmutableType;
class DateTimeImmutableType extends VarDateTimeImmutableType implements \SimpleCalendar\plugin_deps\Carbon\Doctrine\CarbonDoctrineType
{
    use CarbonTypeConverter;
    protected function getCarbonClassName() : string
    {
        return CarbonImmutable::class;
    }
}
