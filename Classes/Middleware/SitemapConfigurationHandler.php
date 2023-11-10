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

namespace EliasHaeussler\Typo3SitemapRobots\Middleware;

use EliasHaeussler\Typo3SitemapLocator;
use Psr\Http\Message;
use Psr\Http\Server;
use Psr\Log;
use TYPO3\CMS\Core;

/**
 * SitemapConfigurationHandler
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class SitemapConfigurationHandler implements Server\MiddlewareInterface
{
    public function __construct(
        private readonly Typo3SitemapLocator\Sitemap\SitemapLocator $sitemapLocator,
        private readonly Log\LoggerInterface $logger,
    ) {}

    public function process(
        Message\ServerRequestInterface $request,
        Server\RequestHandlerInterface $handler,
    ): Message\ResponseInterface {
        $site = $request->getAttribute('site');
        $siteLanguage = $request->getAttribute('language');
        $path = ltrim($request->getUri()->getPath(), '/');

        // Early return if site is not available
        if (!($site instanceof Core\Site\Entity\Site)) {
            return $handler->handle($request);
        }

        // Early return if sitemap injection is disabled
        if (!($site->getConfiguration()['sitemap_robots_inject'] ?? false)) {
            return $handler->handle($request);
        }

        // Early return if robots.txt is not requested
        if ($path !== 'robots.txt') {
            return $handler->handle($request);
        }

        // Use default site language if language was not resolved
        if (!($siteLanguage instanceof Core\Site\Entity\SiteLanguage)) {
            $siteLanguage = $site->getDefaultLanguage();
        }

        $response = $handler->handle($request);

        try {
            return $this->injectSitemapsIntoRobotsTxt($site, $siteLanguage, $response);
        } catch (Typo3SitemapLocator\Exception\Exception $exception) {
            $this->logger->warning(
                'Unable to inject XML sitemaps into robots.txt at {url}.',
                [
                    'url' => (string)$request->getUri(),
                    'exception' => $exception,
                ],
            );

            // Early return if sitemaps cannot be located
            return $response;
        }
    }

    /**
     * @throws Typo3SitemapLocator\Exception\BaseUrlIsNotSupported
     * @throws Typo3SitemapLocator\Exception\SitemapIsMissing
     */
    private function injectSitemapsIntoRobotsTxt(
        Core\Site\Entity\Site $site,
        Core\Site\Entity\SiteLanguage $siteLanguage,
        Message\ResponseInterface $response,
    ): Message\ResponseInterface {
        $sitemaps = $this->sitemapLocator->locateBySite($site, $siteLanguage);

        $body = $response->getBody();
        $body->seek(0, SEEK_END);

        foreach ($sitemaps as $sitemap) {
            if ($this->sitemapLocator->isValidSitemap($sitemap)) {
                $body->write(PHP_EOL);
                $body->write($this->decorateSitemapForRobotsTxt($sitemap));
            }
        }

        return $response;
    }

    private function decorateSitemapForRobotsTxt(Typo3SitemapLocator\Domain\Model\Sitemap $sitemap): string
    {
        return sprintf('Sitemap: %s', $sitemap->getUri());
    }
}
