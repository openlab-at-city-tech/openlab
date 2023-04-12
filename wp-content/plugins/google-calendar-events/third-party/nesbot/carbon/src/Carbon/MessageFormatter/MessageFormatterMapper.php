<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SimpleCalendar\plugin_deps\Carbon\MessageFormatter;

use ReflectionMethod;
use SimpleCalendar\plugin_deps\Symfony\Component\Translation\Formatter\MessageFormatter;
use SimpleCalendar\plugin_deps\Symfony\Component\Translation\Formatter\MessageFormatterInterface;
$transMethod = new ReflectionMethod(MessageFormatterInterface::class, 'format');
require $transMethod->getParameters()[0]->hasType() ? __DIR__ . '/../../../lazy/Carbon/MessageFormatter/MessageFormatterMapperStrongType.php' : __DIR__ . '/../../../lazy/Carbon/MessageFormatter/MessageFormatterMapperWeakType.php';
final class MessageFormatterMapper extends LazyMessageFormatter
{
    /**
     * Wrapped formatter.
     *
     * @var MessageFormatterInterface
     */
    protected $formatter;
    public function __construct(?MessageFormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new MessageFormatter();
    }
    protected function transformLocale(?string $locale) : ?string
    {
        return $locale ? \preg_replace('/[_@][A-Za-z][a-z]{2,}/', '', $locale) : $locale;
    }
}
