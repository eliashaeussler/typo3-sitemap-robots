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

namespace EliasHaeussler\Typo3SitemapRobots\Tests\Functional\Updates;

use EliasHaeussler\Typo3SitemapRobots as Src;
use EliasHaeussler\Typo3SitemapRobots\Tests;
use PHPUnit\Framework;
use TYPO3\CMS\Core;
use TYPO3\TestingFramework;

/**
 * LegacySiteConfigurationParameterUpgradeWizardTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Updates\LegacySiteConfigurationParameterUpgradeWizard::class)]
final class LegacySiteConfigurationParameterUpgradeWizardTest extends TestingFramework\Core\Functional\FunctionalTestCase
{
    use Tests\Functional\SiteTrait;

    protected array $testExtensionsToLoad = [
        'sitemap_locator',
        'sitemap_robots',
    ];

    protected bool $initializeDatabase = false;

    private Core\Configuration\SiteConfiguration $siteConfiguration;
    private Src\Updates\LegacySiteConfigurationParameterUpgradeWizard $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->siteConfiguration = $this->getSiteConfiguration();
        $this->subject = new Src\Updates\LegacySiteConfigurationParameterUpgradeWizard($this->siteConfiguration);
    }

    #[Framework\Attributes\Test]
    public function executeUpdateDoesNothingIfAllSitesAreUpToDate(): void
    {
        $expected = [
            'test-site' => $this->createSite(),
            'other-test-site' => $this->createSite(siteIdentifier: 'other-test-site'),
        ];

        self::assertTrue($this->subject->executeUpdate());
        self::assertEquals($expected, $this->siteConfiguration->getAllExistingSites(false));
    }

    #[Framework\Attributes\Test]
    #[Framework\Attributes\DataProvider('executeUpdateMigratesOutdatedSitesDataProvider')]
    public function executeUpdateMigratesOutdatedSites(bool $outdated, string $migrated): void
    {
        $this->createSite($outdated);
        $this->createSite(siteIdentifier: 'other-test-site');

        $expected = $this->siteConfiguration->load('test-site');
        $expected['sitemap_robots_inject'] = $migrated;

        self::assertTrue($this->subject->executeUpdate());
        self::assertEquals($expected, $this->siteConfiguration->load('test-site'));
    }

    #[Framework\Attributes\Test]
    public function updateNecessaryReturnsFalseIfAllSitesAreUpToDate(): void
    {
        $this->createSite();
        $this->createSite(siteIdentifier: 'other-test-site');

        self::assertFalse($this->subject->updateNecessary());
    }

    #[Framework\Attributes\Test]
    public function updateNecessaryReturnsTrueIfAnySiteIsOutdated(): void
    {
        $this->createSite(true);
        $this->createSite(siteIdentifier: 'other-test-site');

        self::assertTrue($this->subject->updateNecessary());
    }

    /**
     * @return \Generator<string, array{bool, 'default'|''}>
     */
    public static function executeUpdateMigratesOutdatedSitesDataProvider(): \Generator
    {
        yield 'true => default' => [true, 'default'];
        yield 'false => empty' => [false, ''];
    }
}
