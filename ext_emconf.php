<?php

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

/** @noinspection PhpUndefinedVariableInspection */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Sitemap Robots',
    'description' => 'Enhances robots.txt with sitemap configurations to improve site visibility in terms of SEO. Injection of XML sitemaps can be managed on a per-site basis. Supports static routes as well as injection into local files.',
    'category' => 'fe',
    'version' => '0.2.1',
    'state' => 'stable',
    'author' => 'Elias Häußler',
    'author_email' => 'elias@haeussler.dev',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.0.99',
            'php' => '8.2.0-8.4.99',
            'sitemap_locator' => '1.0.0-1.99.99',
        ],
    ],
];
