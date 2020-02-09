<?php

declare(strict_types=1);

namespace Ancarda\PDOPlus;

use PDO;

class ConnectionManager
{
    public const APPLY_TO_ALL = 'all';

    /** @var PDOPlus[] */
    protected $connections = [];

    public function addConnection(string $name, PDOPlus $connection): void
    {
        $this->connections[$name] = $connection;
    }

    /**
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function __call(string $name, array $args)
    {
        $instance = array_shift($args);

        if ($instance === 'all') {
            $out = [];
            foreach ($this->connections as $k => $conn) {
                /** @var callable $callable */
                $callable = [$conn, $name];

                $out[$k] = call_user_func_array($callable, $args);
            }

            return $out;
        }

        /** @var callable $callable */
        $callable = [$this->connections[$instance], $name];

        return call_user_func_array($callable, $args);
    }
}
