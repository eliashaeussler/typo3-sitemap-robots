<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "sitemap_robots".
 *
 * Copyright (C) 2023-2025 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\Typo3SitemapRobots\Tests\Functional;

use EliasHaeussler\Typo3SitemapRobots\Enum;
use TYPO3\CMS\Core;

/**
 * SiteTrait
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
trait SiteTrait
{
    /**
     * @param bool|''|value-of<Enum\EnhancementStrategy> $injectSitemaps
     */
    private function createSite(
        bool|string $injectSitemaps = 'default',
        string $baseUrl = 'https://typo3-testing.local/',
        string $siteIdentifier = 'test-site',
    ): Core\Site\Entity\Site {
        $configPath = $this->instancePath . '/typo3conf/sites';
        $eventDispatcher = new Core\EventDispatcher\NoopEventDispatcher();
        $yamlFileLoader = $this->get(Core\Configuration\Loader\YamlFileLoader::class);

        $siteConfiguration = $this->getSiteConfiguration();
        $siteWriter = new Core\Configuration\SiteWriter($configPath, $eventDispatcher, $yamlFileLoader);
        $siteWriter->createNewBasicSite($siteIdentifier, 1, $baseUrl);

        /** @var array{languages: array<int, mixed>} $rawConfig */
        $rawConfig = $siteConfiguration->load($siteIdentifier);
        $rawConfig['sitemap_robots_inject'] = $injectSitemaps;
        $rawConfig['languages'][1] = [
            'title' => 'German',
            'enabled' => true,
            'locale' => 'de_DE',
            'base' => '/de/',
            'websiteTitle' => '',
            'navigationTitle' => 'Deutsch',
            'fallbackType' => 'strict',
            'fallbacks' => '',
            'flag' => 'de',
            'languageId' => 1,
        ];
        $rawConfig['languages'][2] = [
            'title' => 'French',
            'enabled' => true,
            'locale' => 'fr_FR',
            'base' => '/fr/',
            'websiteTitle' => '',
            'navigationTitle' => 'Français',
            'fallbackType' => 'strict',
            'fallbacks' => '',
            'flag' => 'fr',
            'languageId' => 2,
        ];

        $siteWriter->write($siteIdentifier, $rawConfig);

        $site = $siteConfiguration->getAllExistingSites()[$siteIdentifier] ?? null;

        self::assertInstanceOf(Core\Site\Entity\Site::class, $site);

        return $site;
    }

    private function getSiteConfiguration(): Core\Configuration\SiteConfiguration
    {
        return new Core\Configuration\SiteConfiguration(
            $this->instancePath . '/typo3conf/sites',
            $this->get(Core\Site\SiteSettingsFactory::class),
            $this->get(Core\Site\Set\SetRegistry::class),
            new Core\EventDispatcher\NoopEventDispatcher(),
            new Core\Cache\Frontend\NullFrontend('core'),
            $this->get(Core\Configuration\Loader\YamlFileLoader::class),
            new Core\Cache\Frontend\NullFrontend('runtime'),
        );
    }
}
