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

use EliasHaeussler\RectorConfig\Config\Config;
use EliasHaeussler\RectorConfig\Entity\Version;
use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Symfony\DependencyInjection\Rector\Trait_\TraitGetByTypeToInjectRector;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    $rootPath = dirname(__DIR__, 2);

    require $rootPath . '/.Build/vendor/autoload.php';

    Config::create($rectorConfig, PhpVersion::PHP_81)
        ->in(
            $rootPath . '/Classes',
            $rootPath . '/Configuration',
            $rootPath . '/Tests',
        )
        ->not(
            $rootPath . '/.Build/*',
            $rootPath . '/.github/*',
            $rootPath . '/Tests/CGL/vendor/*',
            $rootPath . '/var/*',
        )
        ->withPHPUnit(Version::createMajor(10))
        ->withSymfony()
        ->withTYPO3()
        ->skip(AnnotationToAttributeRector::class, [
            $rootPath . '/Classes/Extension.php',
        ])
        ->skip(TraitGetByTypeToInjectRector::class, [
            $rootPath . '/Tests/Functional/SiteTrait.php',
        ])
        ->apply()
    ;

    $rectorConfig->importNames(false, false);
};
