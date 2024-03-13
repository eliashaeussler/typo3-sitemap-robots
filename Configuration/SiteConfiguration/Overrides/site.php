<?php

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

(static function() {
    if ((new \TYPO3\CMS\Core\Information\Typo3Version())->getMajorVersion() >= 12) {
        $labelKey = 'label';
    } else {
        // @todo Remove once support for TYPO3 v11 is dropped
        $labelKey = 0;
    }

    $GLOBALS['SiteConfiguration']['site']['columns']['sitemap_robots_inject'] = [
        'label' => 'LLL:EXT:sitemap_robots/Resources/Private/Language/locallang_db.xlf:site.sitemap_robots_inject.label',
        'description' => 'LLL:EXT:sitemap_robots/Resources/Private/Language/locallang_db.xlf:site.sitemap_robots_inject.description',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxLabeledToggle',
            'items' => [
                [
                    $labelKey => '',
                    'labelChecked' => 'LLL:EXT:sitemap_robots/Resources/Private/Language/locallang_db.xlf:site.sitemap_robots_inject.item.checked',
                    'labelUnchecked' => 'LLL:EXT:sitemap_robots/Resources/Private/Language/locallang_db.xlf:site.sitemap_robots_inject.item.unchecked',
                ],
            ],
        ],
    ];

    $GLOBALS['SiteConfiguration']['site']['palettes']['xml_sitemap']['showitem'] .= ', --linebreak--, sitemap_robots_inject';
})();
