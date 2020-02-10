<?php

declare(strict_types=1);

namespace Ancarda\PDOPlus;

class ConnectionManager
{
    /**
     * Execute function on all connections
     *
     * This connection name causes ConnectionManager to apply the operation
     * to all connections in this pool
     *
     * The specific value of this constant should not be stored anywhere.
     * It may change at any time. Always use `ConnectionManager::APPLY_TO_ALL`
     * in your code.
     *
     * @var string
     **/
    public const APPLY_TO_ALL = 'all';

    /** @var PDOPlus[] */
    protected $connections = [];

    public function addConnection(string $name, PDOPlus $connection): void
    {
        if ($name === static::APPLY_TO_ALL) {
            throw new IllegalConnectionNameException(sprintf(
                "Cannot name a connection APPLY_TO_ALL (`%s')",
                static::APPLY_TO_ALL
            ));
        }

        $this->connections[$name] = $connection;
    }

    public function hasConnection(string $name): bool
    {
        return isset($this->connections[$name]);
    }

    /**
     * @param string $name
     * @param array $args
     * @throws NoSuchConnectionException
     * @return mixed
     */
    public function __call(string $name, array $args)
    {
        $instance = array_shift($args);

        if ($instance === static::APPLY_TO_ALL) {
            $out = [];
            foreach ($this->connections as $k => $conn) {
                /** @var callable $callable */
                $callable = [$conn, $name];

                $out[$k] = call_user_func_array($callable, $args);
            }

            return $out;
        }

        if (! $this->hasConnection($instance)) {
            throw new NoSuchConnectionException();
        }

        /** @var callable $callable */
        $callable = [$this->connections[$instance], $name];

        return call_user_func_array($callable, $args);
    }
}
