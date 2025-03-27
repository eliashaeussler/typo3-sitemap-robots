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

namespace EliasHaeussler\Typo3SitemapRobots\Tests\Unit\Enum;

use EliasHaeussler\Typo3SitemapRobots as Src;
use PHPUnit\Framework;
use TYPO3\TestingFramework;

/**
 * EnhancementStrategyTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Enum\EnhancementStrategy::class)]
final class EnhancementStrategyTest extends TestingFramework\Core\Unit\UnitTestCase
{
    #[Framework\Attributes\Test]
    public function fromConfigurationMigratesLegacyConfigurationValues(): void
    {
        self::assertSame(
            Src\Enum\EnhancementStrategy::DefaultLanguage,
            Src\Enum\EnhancementStrategy::fromConfiguration(true),
        );
        self::assertNull(Src\Enum\EnhancementStrategy::fromConfiguration(false));
    }

    #[Framework\Attributes\Test]
    public function fromConfigurationReturnsNullOnEmptyString(): void
    {
        self::assertNull(Src\Enum\EnhancementStrategy::fromConfiguration(''));
    }

    #[Framework\Attributes\Test]
    public function fromConfigurationReturnsResolvedEnumFromSupportedConfiguration(): void
    {
        self::assertSame(
            Src\Enum\EnhancementStrategy::AllLanguages,
            Src\Enum\EnhancementStrategy::fromConfiguration('all'),
        );
        self::assertSame(
            Src\Enum\EnhancementStrategy::DefaultLanguage,
            Src\Enum\EnhancementStrategy::fromConfiguration('default'),
        );
    }
}
