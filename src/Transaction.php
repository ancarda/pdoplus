<?php

declare(strict_types=1);

namespace Ancarda\PDOPlus;

/**
 * High level transaction
 *
 * Call finish to commit. Destroy the object (unset) to rollback.
 */
class Transaction
{
    /** @var PDOPlus|null */
    protected $pdoPlus;

    /**
     * @param PDOPlus $pdoPlus
     */
    public function __construct(PDOPlus $pdoPlus)
    {
        $pdoPlus->getPDO()->beginTransaction();

        $this->pdoPlus = $pdoPlus;
    }

    /**
     * Add a query to run in this transaction
     *
     * Throws NotInTransactionException if this transaction has been committed
     *
     * @see PDOPlus::query for more information
     * @param string $query
     * @param array $args
     * @throws NotInTransactionException
     * @return array
     */
    public function query(string $query, array $args = []): array
    {
        if ($this->pdoPlus === null) {
            throw new NotInTransactionException(__FUNCTION__);
        }

        return $this->pdoPlus->query($query, $args);
    }

    /**
     * Commit the transaction.
     *
     * This will cause future methods to begin throwing NotInTransactionException
     * After finish, you should unset this object as it will become useless
     */
    public function finish(): void
    {
        if ($this->pdoPlus !== null) {
            $this->pdoPlus->getPDO()->commit();
            $this->pdoPlus = null;
        }
    }

    /**
     * Rollback if you haven't committed.
     *
     * If you unset or otherwise destroy this object before calling finish(),
     * the transaction will be canceled (rolled back).
     */
    public function __destruct()
    {
        if ($this->pdoPlus !== null && $this->pdoPlus->getPDO()->inTransaction()) {
            $this->pdoPlus->getPDO()->rollBack();
        }
    }
}
