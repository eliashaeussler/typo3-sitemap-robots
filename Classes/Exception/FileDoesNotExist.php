<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "sitemap_robots".
 *
 * Copyright (C) 2023-2024 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\Typo3SitemapRobots\Exception;

/**
 * FileDoesNotExist
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class FileDoesNotExist extends Exception
{
    public function __construct(string $file)
    {
        parent::__construct(
            sprintf('The file "%s" does not exist or is not readable.', $file),
            1699954412,
        );
    }
}
