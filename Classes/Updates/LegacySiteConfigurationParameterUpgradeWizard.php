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

namespace EliasHaeussler\Typo3SitemapRobots\Updates;

use EliasHaeussler\Typo3SitemapRobots\Enum;
use TYPO3\CMS\Core;
use TYPO3\CMS\Install;

/**
 * LegacySiteConfigurationParameterUpgradeWizard
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Install\Attribute\UpgradeWizard('sitemapRobotsLegacySiteConfigurationParameterUpgradeWizard')]
final class LegacySiteConfigurationParameterUpgradeWizard implements Install\Updates\UpgradeWizardInterface
{
    private readonly Core\Information\Typo3Version $typo3Version;

    public function __construct(
        private readonly Core\Configuration\SiteConfiguration $siteConfiguration,
    ) {
        $this->typo3Version = new Core\Information\Typo3Version();
    }

    public function getTitle(): string
    {
        return '[EXT:sitemap_robots] Migrate legacy site configuration parameter';
    }

    public function getDescription(): string
    {
        return 'Migrates legacy configuration values of the "sitemap_robots_inject" parameter within site configuration.';
    }

    public function executeUpdate(): bool
    {
        $outdatedSites = $this->fetchOutdatedSites();
        $successful = true;

        foreach ($outdatedSites as $identifier => $configuration) {
            /** @var bool $outdatedValue */
            $outdatedValue = $configuration['sitemap_robots_inject'];
            $migratedValue = Enum\EnhancementStrategy::fromConfiguration($outdatedValue);

            if ($migratedValue !== null) {
                $configuration['sitemap_robots_inject'] = $migratedValue->value;
            } else {
                $configuration['sitemap_robots_inject'] = '';
            }

            try {
                $this->writeMigratedSiteConfiguration($identifier, $configuration);
            } catch (Core\Configuration\Exception\SiteConfigurationWriteException) {
                $successful = false;
            }
        }

        return $successful;
    }

    public function updateNecessary(): bool
    {
        return $this->fetchOutdatedSites() !== [];
    }

    public function getPrerequisites(): array
    {
        return [];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function fetchOutdatedSites(): array
    {
        $allSites = $this->siteConfiguration->getAllExistingSites(false);
        $outdatedSites = [];

        foreach ($allSites as $site) {
            $siteIdentifier = $site->getIdentifier();
            /** @var array<string, mixed> $configuration */
            $configuration = $this->siteConfiguration->load($siteIdentifier);
            $configurationValue = $configuration['sitemap_robots_inject'] ?? null;

            if (is_bool($configurationValue)) {
                $outdatedSites[$siteIdentifier] = $configuration;
            }
        }

        return $outdatedSites;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function writeMigratedSiteConfiguration(string $identifier, array $configuration): void
    {
        if ($this->typo3Version->getMajorVersion() >= 13) {
            $siteWriter = Core\Utility\GeneralUtility::makeInstance(Core\Configuration\SiteWriter::class);
        } else {
            // @todo Remove once support for TYPO3 v12 is dropped
            $siteWriter = $this->siteConfiguration;
        }

        $siteWriter->write($identifier, $configuration);
    }
}
