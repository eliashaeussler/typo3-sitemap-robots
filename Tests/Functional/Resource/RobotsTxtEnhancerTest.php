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

namespace EliasHaeussler\Typo3SitemapRobots\Tests\Functional\Resource;

use EliasHaeussler\Typo3SitemapLocator;
use EliasHaeussler\Typo3SitemapRobots as Src;
use EliasHaeussler\Typo3SitemapRobots\Tests;
use TYPO3\CMS\Core;
use TYPO3\TestingFramework;

/**
 * RobotsTxtEnhancerTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 * @covers \EliasHaeussler\Typo3SitemapRobots\Resource\RobotsTxtEnhancer
 */
final class RobotsTxtEnhancerTest extends TestingFramework\Core\Functional\FunctionalTestCase
{
    use Tests\Functional\SiteTrait;

    protected array $testExtensionsToLoad = [
        'sitemap_locator',
    ];

    protected bool $initializeDatabase = false;

    private Tests\Functional\Fixtures\DummyRequestFactory $requestFactory;
    private Src\Resource\RobotsTxtEnhancer $subject;
    private Core\Site\Entity\Site $site;
    private Core\Http\Stream $robotsTxt;

    protected function setUp(): void
    {
        parent::setUp();

        $cache = $this->get(Typo3SitemapLocator\Cache\SitemapsCache::class);

        $this->requestFactory = new Tests\Functional\Fixtures\DummyRequestFactory();
        $this->subject = new Src\Resource\RobotsTxtEnhancer(
            new Typo3SitemapLocator\Sitemap\SitemapLocator(
                $this->requestFactory,
                $cache,
                new Core\EventDispatcher\NoopEventDispatcher(),
                [
                    new Typo3SitemapLocator\Sitemap\Provider\DefaultProvider(),
                ],
            ),
        );
        $this->site = $this->createSite();
        $this->robotsTxt = new Core\Http\Stream('php://temp', 'r+');
        $this->robotsTxt->write(<<<TXT
User-Agent: *
Allow: /
TXT);

        // Flush sitemaps cache
        foreach ($this->site->getLanguages() as $siteLanguage) {
            $cache->remove($this->site, $siteLanguage);
        }
    }

    /**
     * @test
     */
    public function enhanceWithSitemapsThrowsExceptionIfSitemapCannotBeResolved(): void
    {
        $site = $this->createSite(true, '/');

        $this->expectException(Typo3SitemapLocator\Exception\BaseUrlIsNotSupported::class);

        $this->subject->enhanceWithSitemaps($this->robotsTxt, $site);
    }

    /**
     * @test
     */
    public function enhanceWithSitemapsInjectsValidLocatedSitemaps(): void
    {
        $this->requestFactory->handler->append(new Core\Http\Response());

        $this->subject->enhanceWithSitemaps($this->robotsTxt, $this->site);

        self::assertSame(
            <<<TXT
User-Agent: *
Allow: /
Sitemap: https://typo3-testing.local/sitemap.xml
TXT,
            trim((string)$this->robotsTxt),
        );
    }
}
