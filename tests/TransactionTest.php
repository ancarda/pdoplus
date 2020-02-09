<?php

declare(strict_types=1);

namespace Tests;

use Ancarda\PDOPlus\NotInTransactionException;
use Ancarda\PDOPlus\PDOPlus;
use Ancarda\PDOPlus\Transaction;
use PDO;
use PHPUnit\Framework\TestCase;

final class TransactionTest extends TestCase
{
    public function testHappyPath(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects(static::once())->method('beginTransaction');
        $pdoMock->expects(static::once())->method('commit')->willReturn(null);

        $pdoPlusMock = $this->createMock(PDOPlus::class);
        $pdoPlusMock->expects(static::exactly(2))->method('getPDO')->willReturn($pdoMock);
        $pdoPlusMock->expects(static::once())->method('query')->willReturn(['mock' => 1]);

        $transaction = new Transaction($pdoPlusMock);
        static::assertSame(['mock' => 1], $transaction->query('SELECT 1'));

        $transaction->finish();
    }

    public function testAutomaticRollback(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects(static::once())->method('beginTransaction');
        $pdoMock->expects(static::once())->method('inTransaction')->willReturn(true);
        $pdoMock->expects(static::once())->method('rollBack');

        $pdoPlusMock = $this->createMock(PDOPlus::class);
        $pdoPlusMock->expects(static::exactly(3))->method('getPDO')->willReturn($pdoMock);

        $transaction = new Transaction($pdoPlusMock);
        unset($transaction);
    }

    public function testLockAfterCommit(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects(static::once())->method('beginTransaction');
        $pdoMock->expects(static::once())->method('commit');

        $pdoPlusMock = $this->createMock(PDOPlus::class);
        $pdoPlusMock->expects(static::exactly(2))->method('getPDO')->willReturn($pdoMock);

        $transaction = new Transaction($pdoPlusMock);
        $transaction->finish();

        $this->expectException(NotInTransactionException::class);
        $transaction->query('SELECT 1');
    }
}
