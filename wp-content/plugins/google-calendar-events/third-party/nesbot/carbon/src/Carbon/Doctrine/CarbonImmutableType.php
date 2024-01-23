<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SimpleCalendar\plugin_deps\Carbon\Doctrine;

use SimpleCalendar\plugin_deps\Doctrine\DBAL\Platforms\AbstractPlatform;
/** @internal */
class CarbonImmutableType extends DateTimeImmutableType implements CarbonDoctrineType
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'carbon_immutable';
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return \true;
    }
}
