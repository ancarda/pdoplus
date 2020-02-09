<?php

declare(strict_types=1);

namespace Tests;

use Ancarda\PDOPlus\PDOPlus;
use Ancarda\PDOPlus\Transaction;
use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\TestCase;

final class PDOPlusTest extends TestCase
{
    public function testConstruct(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects(static::exactly(2))
            ->method('setAttribute')
            ->withConsecutive(
                [static::equalTo(PDO::ATTR_DEFAULT_FETCH_MODE), static::equalTo(PDO::FETCH_ASSOC)],
                [static::equalTo(PDO::ATTR_ERRMODE), static::equalTo(PDO::ERRMODE_EXCEPTION)]
            );

        new PDOPlus($pdoMock);
    }

    public function testGetPdo(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $pdoPlus = new PDOPlus($pdoMock);

        static::assertSame($pdoMock, $pdoPlus->getPDO());
    }

    public function testQuery(): void
    {
        $stmtMock = new PDOStatementMock();
        $stmtMock->data = [
            [ // Result Set 0
                ['rs1' => 'value'],
            ],
            [ // Result Set 1
                ['rs2' => 'value'],
            ],
        ];

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects(static::once())->method('query')->willReturn($stmtMock);

        $pdoPlus = new PDOPlus($pdoMock);
        static::assertSame(
            [ // Container for all results
                [ // Result Set 0
                    ['rs1' => 'value'], // Row 0
                ],
                [ // Result Set 1
                    ['rs2' => 'value'], // Row 0
                ],
            ],
            $pdoPlus->query('SELECT 1')
        );
    }

    public function testQueryWithArgs(): void
    {
        $stmtMock = new PDOStatementMock();
        $stmtMock->data = [
            [ // Result Set 0
                ['col' => 'b'],
            ],
        ];

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects(static::once())->method('prepare')->willReturn($stmtMock);

        $pdoPlus = new PDOPlus($pdoMock);
        static::assertSame(
            [ // Container for all results
                [ // Result Set 0
                    ['col' => 'b'], // Row 0
                ],
            ],
            $pdoPlus->query('SELECT * FROM table WHERE col = :a', ['a' => 'b'])
        );
    }

    public function testQuote(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('quote')->willReturn('"abc"');

        $pdoPlus = new PDOPlus($pdoMock);
        static::assertSame('"abc"', $pdoPlus->quote('abc'));
    }

    public function testTryTransaction(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects(static::once())->method('beginTransaction');
        $pdoMock->expects(static::once())->method('commit');
        $pdoMock->expects(static::never())->method('rollBack');

        $pdoPlus = new PDOPlus($pdoMock);

        $ret = $pdoPlus->tryTransaction(function (Transaction $trx): array {
            return ['mock' => 1];
        });

        static::assertSame(['mock' => 1], $ret);
    }

    public function testTryTransactionWithException(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects(static::once())->method('beginTransaction');
        $pdoMock->expects(static::never())->method('commit');

        $pdoPlus = new PDOPlus($pdoMock);

        $ret = $pdoPlus->tryTransaction(function (Transaction $trx): array {
            // Query failure
            throw new PDOException('');
        });

        static::assertNull($ret);
    }
}
