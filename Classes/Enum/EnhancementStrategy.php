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

namespace EliasHaeussler\Typo3SitemapRobots\Enum;

/**
 * EnhancementStrategy
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
enum EnhancementStrategy: string
{
    case AllLanguages = 'all';
    case DefaultLanguage = 'default';

    public static function fromConfiguration(string|bool $configuration): ?self
    {
        // BC layer: Handle legacy configuration values
        // @todo Remove with v1 of the extension
        if ($configuration === true) {
            return self::DefaultLanguage;
        }

        if ($configuration === false || $configuration === '') {
            return null;
        }

        return self::from($configuration);
    }
}
