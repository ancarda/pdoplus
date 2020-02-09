<?php

declare(strict_types=1);

namespace Tests;

use Ancarda\PDOPlus\ConnectionManager;
use Ancarda\PDOPlus\PDOPlus;
use PHPUnit\Framework\TestCase;

final class ConnectionManagerTest extends TestCase
{
    public function testHappyPath(): void
    {
        $roMock = $this->createMock(PDOPlus::class);
        $roMock->method('quote')->willReturn('"read only connection"');

        $rwMock = $this->createMock(PDOPlus::class);
        $rwMock->method('quote')->willReturn('"read write connection"');

        $connPool = new ConnectionManager();

        $connPool->addConnection('read-only', $roMock);
        $connPool->addConnection('read-write', $rwMock);

        static::assertSame('"read only connection"', $connPool->quote('read-only', ''));
        static::assertSame('"read write connection"', $connPool->quote('read-write', ''));

        static::assertSame(
            [
                'read-only' => '"read only connection"',
                'read-write' => '"read write connection"',
            ],
            $connPool->quote(ConnectionManager::APPLY_TO_ALL, '')
        );
    }
}
