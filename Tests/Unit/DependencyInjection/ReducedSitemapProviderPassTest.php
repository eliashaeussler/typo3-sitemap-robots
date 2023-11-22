<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "sitemap_robots".
 *
 * Copyright (C) 2023 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace EliasHaeussler\Typo3SitemapRobots\Tests\Unit\DependencyInjection;

use EliasHaeussler\Typo3SitemapLocator;
use EliasHaeussler\Typo3SitemapRobots as Src;
use Symfony\Component\DependencyInjection;
use TYPO3\TestingFramework;

/**
 * ReducedSitemapProviderPassTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 * @covers \EliasHaeussler\Typo3SitemapRobots\DependencyInjection\ReducedSitemapProviderPass
 *
 * @todo Remove once support for TYPO3 v11 is dropped
 */
final class ReducedSitemapProviderPassTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Src\DependencyInjection\ReducedSitemapProviderPass $subject;
    private DependencyInjection\ContainerBuilder $container;
    private DependencyInjection\Definition $definition;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Src\DependencyInjection\ReducedSitemapProviderPass('foo');
        $this->container = new DependencyInjection\ContainerBuilder();
        $this->definition = new DependencyInjection\Definition(Typo3SitemapLocator\Sitemap\SitemapLocator::class);

        $this->container->setDefinition('foo', $this->definition);

        $this->addProvider(Typo3SitemapLocator\Sitemap\Provider\DefaultProvider::class);
        $this->addProvider(Typo3SitemapLocator\Sitemap\Provider\RobotsTxtProvider::class);
        $this->addProvider(Typo3SitemapLocator\Sitemap\Provider\SiteProvider::class);
    }

    /**
     * @test
     */
    public function processDoesNothingIfDefinitionIsMissingInContainer(): void
    {
        $container = new DependencyInjection\ContainerBuilder();
        $expected = new DependencyInjection\ContainerBuilder();

        $this->subject->process($container);

        self::assertEquals($expected, $container);
    }

    /**
     * @test
     */
    public function processThrowsExceptionIfDefinitionHasUnsupportedClassConfigured(): void
    {
        $this->container->setDefinition('foo', new DependencyInjection\Definition());

        $this->expectExceptionObject(
            new Src\Exception\UnsupportedServiceRequested(
                Typo3SitemapLocator\Sitemap\SitemapLocator::class,
                'foo',
            ),
        );

        $this->subject->process($this->container);
    }

    /**
     * @test
     */
    public function processSkipsAbstractProviders(): void
    {
        $definition = $this->addProvider(Typo3SitemapLocator\Sitemap\Provider\DefaultProvider::class, 'dummy');
        $definition->setAbstract(true);

        $this->subject->process($this->container);

        $this->assertProviderIsMissing('dummy');
    }

    /**
     * @test
     */
    public function processSkipsProvidersWithoutClass(): void
    {
        $definition = $this->addProvider(Typo3SitemapLocator\Sitemap\Provider\DefaultProvider::class, 'dummy');
        $definition->setClass(null);

        $this->subject->process($this->container);

        $this->assertProviderIsMissing('dummy');
    }

    /**
     * @test
     */
    public function processSkipsUnsupportedProviders(): void
    {
        $this->addProvider(self::class, 'dummy');

        $this->subject->process($this->container);

        $this->assertProviderIsMissing('dummy');
    }

    /**
     * @test
     */
    public function processSkipsProvidersToExclude(): void
    {
        $this->subject->process($this->container);

        $this->assertProviderIsMissing(Typo3SitemapLocator\Sitemap\Provider\RobotsTxtProvider::class);
    }

    /**
     * @test
     */
    public function processOrdersProvidersByPriority(): void
    {
        $this->subject->process($this->container);

        $providers = $this->definition->getArgument('$providers');

        self::assertIsArray($providers);
        self::assertCount(2, $providers);
        self::assertInstanceOf(DependencyInjection\Reference::class, $providers[0]);
        self::assertSame(Typo3SitemapLocator\Sitemap\Provider\SiteProvider::class, (string)$providers[0]);
        self::assertInstanceOf(DependencyInjection\Reference::class, $providers[1]);
        self::assertSame(Typo3SitemapLocator\Sitemap\Provider\DefaultProvider::class, (string)$providers[1]);
    }

    private function assertProviderIsMissing(string $id): void
    {
        $providers = $this->definition->getArgument('$providers');

        self::assertIsArray($providers);

        foreach ($providers as $provider) {
            self::assertInstanceOf(DependencyInjection\Reference::class, $provider);
            self::assertNotSame($id, (string)$provider);
        }
    }

    private function addProvider(string $providerClass, string $id = null): DependencyInjection\Definition
    {
        $definition = new DependencyInjection\Definition($providerClass);
        $definition->addTag('sitemap_locator.sitemap_provider');

        $this->container->setDefinition($id ?? $providerClass, $definition);

        return $definition;
    }
}
