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

namespace EliasHaeussler\Typo3SitemapRobots\Tests\Functional\Fixtures;

use GuzzleHttp\Client;
use GuzzleHttp\Handler;
use Psr\Http\Message;
use TYPO3\CMS\Core;

/**
 * DummyRequestFactory
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 * @internal
 */
final class DummyRequestFactory extends Core\Http\RequestFactory
{
    private readonly Client $client;

    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct(
        public readonly Handler\MockHandler $handler = new Handler\MockHandler(),
    ) {
        $this->client = new Client(['handler' => $this->handler]);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function request(string $uri, string $method = 'GET', array $options = [], ?string $context = null): Message\ResponseInterface
    {
        return $this->client->request($method, $uri, $options);
    }
}
