<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "sitemap_robots".
 *
 * Copyright (C) 2023 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\Typo3SitemapRobots\Tests\Unit\Resource;

use EliasHaeussler\Typo3SitemapRobots as Src;
use PHPUnit\Framework;
use Psr\Http\Message;
use RuntimeException;
use TYPO3\CMS\Core;
use TYPO3\TestingFramework;

/**
 * RobotsTxtFactoryTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 * @covers \EliasHaeussler\Typo3SitemapRobots\Resource\RobotsTxtFactory
 */
final class RobotsTxtFactoryTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Message\StreamFactoryInterface&Framework\MockObject\MockObject $streamFactory;
    private Src\Resource\RobotsTxtFactory $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->streamFactory = $this->createMock(Message\StreamFactoryInterface::class);
        $this->subject = new Src\Resource\RobotsTxtFactory($this->streamFactory);
    }

    /**
     * @test
     */
    public function fromFileThrowsExceptionIfFileDoesNotExist(): void
    {
        $this->expectExceptionObject(
            new Src\Exception\FileDoesNotExist('foo'),
        );

        $this->subject->fromFile('foo');
    }

    /**
     * @test
     */
    public function fromFileThrowsExceptionIfFileCannotBeOpened(): void
    {
        $file = __FILE__;

        $this->streamFactory->method('createStreamFromFile')->willThrowException(
            new RuntimeException('The file ' . $file . ' cannot be opened.'),
        );

        $this->expectExceptionObject(
            new Src\Exception\FileDoesNotExist($file),
        );

        $this->subject->fromFile($file);
    }

    /**
     * @test
     */
    public function fromFileReturnsResponseForGivenFile(): void
    {
        $file = __FILE__;
        $subject = new Src\Resource\RobotsTxtFactory(new Core\Http\StreamFactory());

        $actual = $subject->fromFile($file);

        self::assertStringEqualsFile($file, (string)$actual->getBody());
        self::assertSame(200, $actual->getStatusCode());
        self::assertSame(['text/plain; charset=utf-8'], $actual->getHeader('Content-Type'));
    }

    /**
     * @test
     */
    public function fromContentsReturnsResponseForGivenContents(): void
    {
        $subject = new Src\Resource\RobotsTxtFactory(new Core\Http\StreamFactory());

        $actual = $subject->fromContents('foo');

        self::assertSame('foo', (string)$actual->getBody());
        self::assertSame(200, $actual->getStatusCode());
        self::assertSame(['text/plain; charset=utf-8'], $actual->getHeader('Content-Type'));
    }
}
