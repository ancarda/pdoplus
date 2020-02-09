<?php

declare(strict_types=1);

namespace Ancarda\PDOPlus;

use DomainException;

/**
 * A method call failed because the database connection isn't in a transaction right now
 */
final class NotInTransactionException extends DomainException
{
    /** @var string $method */
    private $method;

    /**
     * @param string $method
     */
    public function __construct(string $method)
    {
        $this->method = $method;

        parent::__construct(
            'Cannot execute `' . $method . '\' because this database connection isn\'t in a transaction right now'
        );
    }

    /**
     * Get the method that was attempted that caused this exception
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }
}
