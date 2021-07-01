<?php

/**
 * Thanks to https://github.com/flaushi for his suggestion:
 * https://github.com/doctrine/dbal/issues/2873#issuecomment-534956358
 */
namespace SimpleCalendar\plugin_deps\Carbon\Doctrine;

use SimpleCalendar\plugin_deps\Doctrine\DBAL\Platforms\AbstractPlatform;
class CarbonType extends \SimpleCalendar\plugin_deps\Carbon\Doctrine\DateTimeType implements \SimpleCalendar\plugin_deps\Carbon\Doctrine\CarbonDoctrineType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'carbon';
    }
    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return \true;
    }
}
