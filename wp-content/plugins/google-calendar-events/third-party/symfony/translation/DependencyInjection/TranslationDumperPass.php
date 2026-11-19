<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SimpleCalendar\plugin_deps\Symfony\Component\Translation\DependencyInjection;

use SimpleCalendar\plugin_deps\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use SimpleCalendar\plugin_deps\Symfony\Component\DependencyInjection\ContainerBuilder;
use SimpleCalendar\plugin_deps\Symfony\Component\DependencyInjection\Reference;
/**
 * Adds tagged translation.formatter services to translation writer.
 */
class TranslationDumperPass implements CompilerPassInterface
{
    /**
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('translation.writer')) {
            return;
        }
        $definition = $container->getDefinition('translation.writer');
        foreach ($container->findTaggedServiceIds('translation.dumper', \true) as $id => $attributes) {
            $definition->addMethodCall('addDumper', [$attributes[0]['alias'], new Reference($id)]);
        }
    }
}
