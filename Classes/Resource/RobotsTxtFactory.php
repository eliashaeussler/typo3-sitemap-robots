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

namespace EliasHaeussler\Typo3SitemapRobots\Resource;

use EliasHaeussler\Typo3SitemapRobots\Exception;
use Psr\Http\Message;
use TYPO3\CMS\Core;

/**
 * RobotsTxtFactory
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class RobotsTxtFactory
{
    public function __construct(
        private readonly Message\StreamFactoryInterface $streamFactory,
    ) {}

    /**
     * @throws Exception\FileDoesNotExist
     */
    public function fromFile(string $file): Message\ResponseInterface
    {
        if (!file_exists($file)) {
            throw new Exception\FileDoesNotExist($file);
        }

        try {
            $body = $this->streamFactory->createStreamFromFile($file, 'a+');
        } catch (\RuntimeException) {
            throw new Exception\FileDoesNotExist($file);
        }

        return $this->createResponse($body);
    }

    public function fromContents(string $contents): Message\ResponseInterface
    {
        $body = $this->streamFactory->createStream($contents);

        return $this->createResponse($body);
    }

    private function createResponse(Message\StreamInterface $body): Core\Http\Response
    {
        return new Core\Http\Response($body, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }
}
