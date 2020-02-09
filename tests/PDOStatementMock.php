<?php

declare(strict_types=1);

namespace Tests;

final class PDOStatementMock
{
    /** @var array */
    public $data = [];

    public function fetchAll(): array
    {
        if (count($this->data) === 0) {
            return [];
        }

        return array_shift($this->data);
    }

    public function rowCount(): int
    {
        return count($this->data) === 0 ? 0 : count($this->data[0]);
    }

    public function nextRowset(): bool
    {
        return count($this->data) > 0;
    }

    public function execute(): void
    {
        // Do nothing
    }

    public function closeCursor(): void
    {
        // Do nothing
    }
}
