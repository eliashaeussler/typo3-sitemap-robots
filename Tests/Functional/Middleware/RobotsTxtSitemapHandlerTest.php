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

namespace EliasHaeussler\Typo3SitemapRobots\Tests\Functional\Middleware;

use EliasHaeussler\Typo3SitemapLocator;
use EliasHaeussler\Typo3SitemapRobots as Src;
use EliasHaeussler\Typo3SitemapRobots\Tests;

use function file_put_contents;

use PHPUnit\Framework;
use Psr\Log;
use TYPO3\CMS\Core;
use TYPO3\TestingFramework;

use function unlink;

/**
 * RobotsTxtSitemapHandlerTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Middleware\RobotsTxtSitemapHandler::class)]
final class RobotsTxtSitemapHandlerTest extends TestingFramework\Core\Functional\FunctionalTestCase
{
    use Src\Tests\Functional\SiteTrait;

    protected array $testExtensionsToLoad = [
        'sitemap_locator',
        'sitemap_robots',
    ];

    protected bool $initializeDatabase = false;

    private Tests\Functional\Fixtures\DummyRequestFactory $requestFactory;
    private Tests\Functional\Fixtures\DummyLogger $logger;
    private Src\Middleware\RobotsTxtSitemapHandler $subject;
    private Core\Site\Entity\Site $site;
    private Core\Http\ServerRequest $request;
    private Tests\Functional\Fixtures\DummyRequestHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $cache = $this->get(Typo3SitemapLocator\Cache\SitemapsCache::class);

        $this->requestFactory = new Tests\Functional\Fixtures\DummyRequestFactory();
        $this->logger = new Tests\Functional\Fixtures\DummyLogger();
        $this->subject = new Src\Middleware\RobotsTxtSitemapHandler(
            new Src\Resource\RobotsTxtEnhancer(
                new Typo3SitemapLocator\Sitemap\SitemapLocator(
                    $this->requestFactory,
                    $cache,
                    new Core\EventDispatcher\NoopEventDispatcher(),
                    [
                        new Typo3SitemapLocator\Sitemap\Provider\DefaultProvider(),
                    ],
                ),
            ),
            $this->get(Src\Resource\RobotsTxtFactory::class),
            $this->logger,
        );
        $this->site = $this->createSite();
        $this->request = new Core\Http\ServerRequest('https://typo3-testing.local/robots.txt');
        $this->request = $this->request->withAttribute('site', $this->site);
        $this->handler = new Tests\Functional\Fixtures\DummyRequestHandler();

        // Flush sitemaps cache
        foreach ($this->site->getLanguages() as $siteLanguage) {
            $cache->remove($this->site, $siteLanguage);
        }
    }

    #[Framework\Attributes\Test]
    public function processDoesNothingIfSiteIsNotAvailable(): void
    {
        $request = $this->request->withoutAttribute('site');

        $actual = $this->subject->process($request, $this->handler);

        self::assertStringNotContainsString(self::getExpectedContent(), (string)$actual->getBody());
    }

    #[Framework\Attributes\Test]
    public function processDoesNothingIfSitemapInjectionIsDisabled(): void
    {
        $site = $this->createSite(false);
        $request = $this->request->withAttribute('site', $site);

        $actual = $this->subject->process($request, $this->handler);

        self::assertStringNotContainsString(self::getExpectedContent(), (string)$actual->getBody());
    }

    #[Framework\Attributes\Test]
    public function processDoesNothingIfRequestedUrlIsNotSupported(): void
    {
        $request = $this->request->withUri(
            new Core\Http\Uri('https://typo3-testing.local/foo'),
        );

        $actual = $this->subject->process($request, $this->handler);

        self::assertStringNotContainsString(self::getExpectedContent(), (string)$actual->getBody());
    }

    #[Framework\Attributes\Test]
    public function processInjectsLocatedSitemapsOfDefaultSiteLanguageIfNoSiteLanguageIsAvailableInRequest(): void
    {
        $this->requestFactory->handler->append(new Core\Http\Response());

        $actual = $this->subject->process($this->request, $this->handler);

        self::assertStringContainsString(self::getExpectedContent(), (string)$actual->getBody());
    }

    #[Framework\Attributes\Test]
    public function processStreamsLocalRobotsTxtFileWithLocatedSitemapsInjected(): void
    {
        $filename = $this->instancePath . '/robots.txt';

        file_put_contents(
            $filename,
            <<<TXT
User-Agent: *
Allow: /
TXT,
        );

        $this->requestFactory->handler->append(new Core\Http\Response());

        $expected = self::getExpectedContent();

        $actual = $this->subject->process($this->request, $this->handler);

        self::assertSame(
            <<<TXT
User-Agent: *
Allow: /
{$expected}
TXT,
            (string)$actual->getBody(),
        );

        unlink($filename);
    }

    #[Framework\Attributes\Test]
    public function processDoesNothingIfHandledResponseIsNotOkay(): void
    {
        $response = new Core\Http\Response();
        $response = $response->withStatus(404);

        $this->requestFactory->handler->append(new Core\Http\Response());
        $this->handler->expectedResponse = $response;

        $actual = $this->subject->process($this->request, $this->handler);

        self::assertSame($response, $actual);
        self::assertStringNotContainsString(self::getExpectedContent(), (string)$actual->getBody());
    }

    #[Framework\Attributes\Test]
    public function processInjectsLocatedSitemapsOfGivenSiteLanguage(): void
    {
        $this->requestFactory->handler->append(new Core\Http\Response());

        $request = $this->request->withAttribute('language', $this->site->getLanguageById(1));

        $actual = $this->subject->process($request, $this->handler);

        self::assertStringNotContainsString(self::getExpectedContent(), (string)$actual->getBody());
        self::assertStringContainsString(self::getExpectedContent('de'), (string)$actual->getBody());
    }

    #[Framework\Attributes\Test]
    public function processLogsWarningIfSitemapCannotBeResolved(): void
    {
        $site = $this->createSite(true, '/');
        $request = $this->request->withAttribute('site', $site);

        $actual = $this->subject->process($request, $this->handler);

        self::assertStringNotContainsString(self::getExpectedContent(), (string)$actual->getBody());
        self::assertEquals(
            [
                Log\LogLevel::WARNING => [
                    [
                        'message' => 'Unable to inject XML sitemaps into robots.txt at {url}.',
                        'context' => [
                            'url' => 'https://typo3-testing.local/robots.txt',
                            'exception' => new Typo3SitemapLocator\Exception\BaseUrlIsNotSupported('/'),
                        ],
                    ],
                ],
            ],
            $this->logger->log,
        );
    }

    private static function getExpectedContent(string $language = ''): string
    {
        if ($language !== '') {
            $language .= '/';
        }

        return sprintf('Sitemap: https://typo3-testing.local/%ssitemap.xml', $language);
    }
}
