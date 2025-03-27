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

namespace EliasHaeussler\Typo3SitemapRobots\Middleware;

use EliasHaeussler\Typo3SitemapLocator;
use EliasHaeussler\Typo3SitemapRobots\Enum;
use EliasHaeussler\Typo3SitemapRobots\Exception;
use EliasHaeussler\Typo3SitemapRobots\Resource;
use Psr\Http\Message;
use Psr\Http\Server;
use Psr\Log;
use TYPO3\CMS\Core;

/**
 * RobotsTxtSitemapHandler
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class RobotsTxtSitemapHandler implements Server\MiddlewareInterface
{
    public function __construct(
        private readonly Resource\RobotsTxtEnhancer $enhancer,
        private readonly Resource\RobotsTxtFactory $factory,
        private readonly Log\LoggerInterface $logger,
    ) {}

    public function process(
        Message\ServerRequestInterface $request,
        Server\RequestHandlerInterface $handler,
    ): Message\ResponseInterface {
        $site = $request->getAttribute('site');
        $path = ltrim($request->getUri()->getPath(), '/');

        // Early return if site is not available
        if (!($site instanceof Core\Site\Entity\Site)) {
            return $handler->handle($request);
        }

        // Parse site configuration value to enhancement strategy
        /** @var string|bool $configurationValue */
        $configurationValue = $site->getConfiguration()['sitemap_robots_inject'] ?? '';
        $enhancementStrategy = Enum\EnhancementStrategy::fromConfiguration($configurationValue);

        // Early return if sitemap injection is disabled
        if ($enhancementStrategy === null) {
            return $handler->handle($request);
        }

        // Early return if robots.txt is not requested
        if ($path !== 'robots.txt') {
            return $handler->handle($request);
        }

        // Resolve path to local robots.txt file
        $localPath = $this->buildRobotsTxtFilePath();

        // Stream existing file or pass request to next middleware to resolve robots.txt
        try {
            $response = $this->factory->fromFile($localPath);
        } catch (Exception\FileDoesNotExist) {
            $response = $handler->handle($request);
        }

        // Early return if response is not okay
        if ($response->getStatusCode() !== 200) {
            return $response;
        }

        try {
            $this->enhancer->enhanceWithSitemaps($response->getBody(), $site, $enhancementStrategy);
        } catch (Typo3SitemapLocator\Exception\Exception $exception) {
            $this->logger->warning(
                'Unable to inject XML sitemaps into robots.txt at {url}.',
                [
                    'url' => (string)$request->getUri(),
                    'exception' => $exception,
                ],
            );
        }

        return $response;
    }

    private function buildRobotsTxtFilePath(): string
    {
        return Core\Core\Environment::getPublicPath() . DIRECTORY_SEPARATOR . 'robots.txt';
    }
}
