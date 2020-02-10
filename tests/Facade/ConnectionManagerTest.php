<?php

declare(strict_types=1);

namespace Tests\Facade;

use Ancarda\PDOPlus\Facade\ConnectionManager as Facade;
use Ancarda\PDOPlus\ConnectionManager;
use Ancarda\PDOPlus\PDOPlus;
use PHPUnit\Framework\TestCase;

final class ConnectionManagerTest extends TestCase
{
    public function testFacade(): void
    {
        $pdoPlusMock = $this->createMock(PDOPlus::class);

        $connectionManagerMock = $this->createMock(ConnectionManager::class);
        $connectionManagerMock->method('__call')->willReturn('"hello"');

        Facade::setInstance($connectionManagerMock);
        Facade::addConnection('read-write', $pdoPlusMock);

        static::assertEquals('"hello"', Facade::quote('read-write', 'hello'));
    }
}
