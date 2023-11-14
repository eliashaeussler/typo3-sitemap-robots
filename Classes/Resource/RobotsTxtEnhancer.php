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

namespace EliasHaeussler\Typo3SitemapRobots\Resource;

use EliasHaeussler\Typo3SitemapLocator;
use Psr\Http\Message;
use TYPO3\CMS\Core;

/**
 * RobotsTxtEnhancer
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class RobotsTxtEnhancer
{
    public function __construct(
        private readonly Typo3SitemapLocator\Sitemap\SitemapLocator $sitemapLocator,
    ) {}

    /**
     * @throws Typo3SitemapLocator\Exception\BaseUrlIsNotSupported
     * @throws Typo3SitemapLocator\Exception\SitemapIsMissing
     */
    public function enhanceWithSitemaps(
        Message\StreamInterface $robotsTxt,
        Core\Site\Entity\Site $site,
        Core\Site\Entity\SiteLanguage $siteLanguage,
    ): void {
        $sitemaps = $this->sitemapLocator->locateBySite($site, $siteLanguage);

        // Go to end of file stream
        $robotsTxt->seek(0, SEEK_END);

        // Inject all valid sitemaps into robots.txt file stream
        foreach ($sitemaps as $sitemap) {
            if ($this->sitemapLocator->isValidSitemap($sitemap)) {
                $robotsTxt->write(PHP_EOL);
                $robotsTxt->write($this->decorateSitemapForRobotsTxt($sitemap));
            }
        }
    }

    private function decorateSitemapForRobotsTxt(Typo3SitemapLocator\Domain\Model\Sitemap $sitemap): string
    {
        return sprintf('Sitemap: %s', $sitemap->getUri());
    }
}
