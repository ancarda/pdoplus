<?php

declare(strict_types=1);

namespace Tests\Facade;

use Ancarda\PDOPlus\Facade\PDOPlus as Facade;
use Ancarda\PDOPlus\PDOPlus;
use Ancarda\PDOPlus\Transaction;
use PDO;
use PHPUnit\Framework\TestCase;

final class PDOPlusTest extends TestCase
{
    public function testFacade(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $transactionMock = $this->createMock(Transaction::class);

        $pdoPlusMock = $this->createMock(PDOPlus::class);
        $pdoPlusMock->method('getPDO')->willReturn($pdoMock);
        $pdoPlusMock->method('quote')->willReturn('"value"');
        $pdoPlusMock->method('query')->willReturn(['mock' => 1]);
        $pdoPlusMock->method('createTransaction')->willReturn($transactionMock);
        $pdoPlusMock->method('tryTransaction')->willReturn(['mock' => 2]);

        $transactionCallback = function (Transaction $transaction): array {
            return [];
        };

        Facade::setInstance($pdoPlusMock);
        Facade::setInstance($pdoPlusMock);

        static::assertEquals($pdoMock, Facade::getPDO());
        static::assertEquals('"value"', Facade::quote('value'));
        static::assertEquals(['mock' => 1], Facade::query('SELECT 1'));
        static::assertEquals($transactionMock, Facade::createTransaction());
        static::assertEquals(['mock' => 2], Facade::tryTransaction($transactionCallback));
    }
}
