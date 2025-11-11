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

namespace EliasHaeussler\Typo3SitemapRobots\Tests\Functional;

use EliasHaeussler\Typo3SitemapLocator;
use GuzzleHttp\Handler;
use Symfony\Component\EventDispatcher;

/**
 * ClientMockTrait
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
trait ClientMockTrait
{
    private EventDispatcher\EventDispatcher $eventDispatcher;
    private Handler\MockHandler $handler;

    private function createMockHandler(): Handler\MockHandler
    {
        return $this->handler ??= new Handler\MockHandler();
    }

    private function registerMockHandler(): void
    {
        $this->eventDispatcher ??= new EventDispatcher\EventDispatcher();
        $this->eventDispatcher->addListener(
            Typo3SitemapLocator\Event\BeforeClientConfiguredEvent::class,
            function (Typo3SitemapLocator\Event\BeforeClientConfiguredEvent $event): void {
                $event->setOption('handler', $this->createMockHandler());
            },
        );
    }
}
