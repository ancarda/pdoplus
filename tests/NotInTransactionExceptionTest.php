<?php

declare(strict_types=1);

namespace Tests;

use Ancarda\PDOPlus\NotInTransactionException;
use PHPUnit\Framework\TestCase;

final class NotInTransactionExceptionTest extends TestCase
{
    public function testHappyPath(): void
    {
        $exception = new NotInTransactionException(__FUNCTION__);

        static::assertSame(
            sprintf(
                "Cannot execute `%s' because this database connection isn't in a transaction right now",
                __FUNCTION__
            ),
            $exception->getMessage()
        );
        static::assertSame(__FUNCTION__, $exception->getMethod());
    }
}
