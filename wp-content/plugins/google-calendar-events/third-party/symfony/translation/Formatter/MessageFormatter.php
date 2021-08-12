<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SimpleCalendar\plugin_deps\Symfony\Component\Translation\Formatter;

use SimpleCalendar\plugin_deps\Symfony\Component\Translation\IdentityTranslator;
use SimpleCalendar\plugin_deps\Symfony\Contracts\Translation\TranslatorInterface;
// Help opcache.preload discover always-needed symbols
\class_exists(\SimpleCalendar\plugin_deps\Symfony\Component\Translation\Formatter\IntlFormatter::class);
/**
 * @author Abdellatif Ait boudad <a.aitboudad@gmail.com>
 */
class MessageFormatter implements \SimpleCalendar\plugin_deps\Symfony\Component\Translation\Formatter\MessageFormatterInterface, \SimpleCalendar\plugin_deps\Symfony\Component\Translation\Formatter\IntlFormatterInterface
{
    private $translator;
    private $intlFormatter;
    /**
     * @param TranslatorInterface|null $translator An identity translator to use as selector for pluralization
     */
    public function __construct(TranslatorInterface $translator = null, \SimpleCalendar\plugin_deps\Symfony\Component\Translation\Formatter\IntlFormatterInterface $intlFormatter = null)
    {
        $this->translator = $translator ?? new IdentityTranslator();
        $this->intlFormatter = $intlFormatter ?? new \SimpleCalendar\plugin_deps\Symfony\Component\Translation\Formatter\IntlFormatter();
    }
    /**
     * {@inheritdoc}
     */
    public function format(string $message, string $locale, array $parameters = [])
    {
        if ($this->translator instanceof TranslatorInterface) {
            return $this->translator->trans($message, $parameters, null, $locale);
        }
        return \strtr($message, $parameters);
    }
    /**
     * {@inheritdoc}
     */
    public function formatIntl(string $message, string $locale, array $parameters = []) : string
    {
        return $this->intlFormatter->formatIntl($message, $locale, $parameters);
    }
}
