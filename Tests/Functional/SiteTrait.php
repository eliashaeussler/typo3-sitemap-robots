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

namespace EliasHaeussler\Typo3SitemapRobots\Tests\Functional;

use TYPO3\CMS\Core;

/**
 * SiteTrait
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
trait SiteTrait
{
    private static string $testSiteIdentifier = 'test-site';

    private function createSite(
        bool $injectSitemaps = true,
        string $baseUrl = 'https://typo3-testing.local/',
    ): Core\Site\Entity\Site {
        $configPath = $this->instancePath . '/typo3conf/sites';

        // @todo Remove once support for TYPO3 v11 is dropped
        if ((new Core\Information\Typo3Version())->getMajorVersion() < 12) {
            $siteConfiguration = new Core\Configuration\SiteConfiguration($configPath);
        } else {
            $siteConfiguration = new Core\Configuration\SiteConfiguration(
                $configPath,
                new Core\EventDispatcher\NoopEventDispatcher(),
            );
        }

        $siteConfiguration->createNewBasicSite(static::$testSiteIdentifier, 1, $baseUrl);

        $rawConfig = $siteConfiguration->load(static::$testSiteIdentifier);
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

        $siteConfiguration->write(static::$testSiteIdentifier, $rawConfig);

        $site = $siteConfiguration->getAllExistingSites()[static::$testSiteIdentifier] ?? null;

        self::assertInstanceOf(Core\Site\Entity\Site::class, $site);

        return $site;
    }
}
