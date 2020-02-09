<?php

declare(strict_types=1);

namespace Ancarda\PDOPlus;

use PDO;
use PDOException;
use PDOStatement;

class PDOPlus
{
    /** @var PDO */
    protected $pdo;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->pdo = $pdo;
    }

    /**
     * @return PDO
     */
    public function getPDO(): PDO
    {
        return $this->pdo;
    }

    /**
     * @param string $query
     * @param array $args
     * @throws PDOException
     * @return array
     */
    public function query(string $query, array $args = []): array
    {
        $stmt = null;

        if (count($args) === 0) {
            /** @var PDOStatement $stmt */
            $stmt = $this->pdo->query($query);
        } else {
            /** @var PDOStatement $stmt */
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($args);
        }

        $out = [];
        do {
            $out[] = ($stmt->rowCount() === 0) ? [] : $stmt->fetchAll();
        } while ($stmt->nextRowset());

        $stmt->closeCursor();

        return $out;
    }

    /**
     * @param string $text
     * @return string
     */
    public function quote(string $text): string
    {
        return $this->pdo->quote($text);
    }

    /**
     * @return Transaction
     */
    public function createTransaction(): Transaction
    {
        return new Transaction($this);
    }

    /**
     * @param callable $fn
     * @return mixed
     */
    public function tryTransaction(callable $fn)
    {
        $transaction = $this->createTransaction();
        try {
            $out = $fn($transaction);
            $transaction->finish();
            return $out;
        } catch (PDOException $ex) {
            unset($transaction);
        }

        return null;
    }
}
