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

namespace EliasHaeussler\Typo3SitemapRobots\DependencyInjection;

use EliasHaeussler\Typo3SitemapLocator;
use EliasHaeussler\Typo3SitemapRobots\Exception;
use Symfony\Component\DependencyInjection;
use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * ReducedSitemapProviderPass
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 * @internal
 *
 * @todo Remove once support for TYPO3 v11 is dropped
 */
final class ReducedSitemapProviderPass implements DependencyInjection\Compiler\CompilerPassInterface
{
    private const PROVIDERS_TO_EXCLUDE = [
        Typo3SitemapLocator\Sitemap\Provider\RobotsTxtProvider::class,
    ];

    public function __construct(
        private readonly string $sitemapLocatorServiceId = 'sitemap_robots.sitemap_locator_without_robots_txt_provider',
    ) {}

    /**
     * @throws Exception\UnsupportedServiceRequested
     */
    public function process(DependencyInjection\ContainerBuilder $container): void
    {
        if (!$container->hasDefinition($this->sitemapLocatorServiceId)) {
            return;
        }

        $sitemapLocator = $container->getDefinition($this->sitemapLocatorServiceId);

        // Early return if requested service is unsupported
        if ($sitemapLocator->getClass() !== Typo3SitemapLocator\Sitemap\SitemapLocator::class) {
            throw new Exception\UnsupportedServiceRequested(
                Typo3SitemapLocator\Sitemap\SitemapLocator::class,
                $this->sitemapLocatorServiceId,
            );
        }

        $allProviders = array_keys($container->findTaggedServiceIds('sitemap_locator.sitemap_provider'));
        $providers = [];

        foreach ($allProviders as $providerServiceId) {
            $provider = $container->getDefinition($providerServiceId);
            $providerClass = $provider->getClass();

            // Skip unsupported providers
            if ($provider->isAbstract()
                || $providerClass === null
                || !is_a($providerClass, Typo3SitemapLocator\Sitemap\Provider\Provider::class, true)
            ) {
                continue;
            }

            if (!in_array($providerClass, self::PROVIDERS_TO_EXCLUDE, true)) {
                $priority = $providerClass::getPriority();
                $providers[$priority] ??= [];
                $providers[$priority][] = new DependencyInjection\Reference($providerServiceId);
            }
        }

        // Sort by priority (highest to lowest)
        krsort($providers);

        // Pass providers to sitemap locator
        $sitemapLocator->setArgument('$providers', array_values(ArrayUtility::flatten($providers)));
    }
}
