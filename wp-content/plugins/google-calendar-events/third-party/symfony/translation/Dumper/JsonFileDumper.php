<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SimpleCalendar\plugin_deps\Symfony\Component\Translation\Dumper;

use SimpleCalendar\plugin_deps\Symfony\Component\Translation\MessageCatalogue;
/**
 * JsonFileDumper generates an json formatted string representation of a message catalogue.
 *
 * @author singles
 */
class JsonFileDumper extends \SimpleCalendar\plugin_deps\Symfony\Component\Translation\Dumper\FileDumper
{
    /**
     * {@inheritdoc}
     */
    public function formatCatalogue(MessageCatalogue $messages, string $domain, array $options = [])
    {
        $flags = $options['json_encoding'] ?? \JSON_PRETTY_PRINT;
        return \json_encode($messages->all($domain), $flags);
    }
    /**
     * {@inheritdoc}
     */
    protected function getExtension()
    {
        return 'json';
    }
}
