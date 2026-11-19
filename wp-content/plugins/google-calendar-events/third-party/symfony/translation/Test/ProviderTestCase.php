<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SimpleCalendar\plugin_deps\Symfony\Component\Translation\Test;

use SimpleCalendar\plugin_deps\PHPUnit\Framework\MockObject\MockObject;
use SimpleCalendar\plugin_deps\PHPUnit\Framework\TestCase;
use SimpleCalendar\plugin_deps\Psr\Log\LoggerInterface;
use SimpleCalendar\plugin_deps\Psr\Log\NullLogger;
use SimpleCalendar\plugin_deps\Symfony\Component\HttpClient\MockHttpClient;
use SimpleCalendar\plugin_deps\Symfony\Component\Translation\Dumper\XliffFileDumper;
use SimpleCalendar\plugin_deps\Symfony\Component\Translation\Loader\ArrayLoader;
use SimpleCalendar\plugin_deps\Symfony\Component\Translation\Loader\LoaderInterface;
use SimpleCalendar\plugin_deps\Symfony\Component\Translation\Provider\ProviderInterface;
use SimpleCalendar\plugin_deps\Symfony\Component\Translation\TranslatorBag;
use SimpleCalendar\plugin_deps\Symfony\Component\Translation\TranslatorBagInterface;
use SimpleCalendar\plugin_deps\Symfony\Contracts\HttpClient\HttpClientInterface;
/**
 * A test case to ease testing a translation provider.
 *
 * @author Mathieu Santostefano <msantostefano@protonmail.com>
 */
abstract class ProviderTestCase extends TestCase
{
    protected HttpClientInterface $client;
    protected LoggerInterface|MockObject $logger;
    protected string $defaultLocale;
    protected LoaderInterface|MockObject $loader;
    protected XliffFileDumper|MockObject $xliffFileDumper;
    protected TranslatorBagInterface|MockObject $translatorBag;
    abstract public static function createProvider(HttpClientInterface $client, LoaderInterface $loader, LoggerInterface $logger, string $defaultLocale, string $endpoint): ProviderInterface;
    /**
     * @return iterable<array{0: ProviderInterface, 1: string}>
     */
    abstract public static function toStringProvider(): iterable;
    /**
     * @dataProvider toStringProvider
     */
    public function testToString(ProviderInterface $provider, string $expected)
    {
        $this->assertSame($expected, (string) $provider);
    }
    protected function getClient(): MockHttpClient
    {
        return $this->client ??= new MockHttpClient();
    }
    protected function getLoader(): LoaderInterface
    {
        return $this->loader ??= new ArrayLoader();
    }
    protected function getLogger(): LoggerInterface
    {
        return $this->logger ??= new NullLogger();
    }
    protected function getDefaultLocale(): string
    {
        return $this->defaultLocale ??= 'en';
    }
    protected function getXliffFileDumper(): XliffFileDumper
    {
        return $this->xliffFileDumper ??= new XliffFileDumper();
    }
    protected function getTranslatorBag(): TranslatorBagInterface
    {
        return $this->translatorBag ??= new TranslatorBag();
    }
}
