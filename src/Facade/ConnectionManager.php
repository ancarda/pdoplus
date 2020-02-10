<?php

declare(strict_types=1);

namespace Ancarda\PDOPlus\Facade;

use Ancarda\PDOPlus\ConnectionManager as RealClass;

/**
 * A global instance of ConnectionManager
 *
 * Use setInstance to store a ConnectionManager in global scope.
 * After that, all subsequent interactions with this class can be used as if it were a ConnectionManager.
 *
 * For documentation, refer to the ConnectionManager class itself.
 *
 * @see RealClass
 */
class ConnectionManager
{
    /** @var RealClass */
    protected static $instance;

    /**
     * Store a ConnectionManager in global scope
     *
     * @param RealClass $instance
     */
    public static function setInstance(RealClass $instance): void
    {
        static::$instance = $instance;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments = [])
    {
        /** @var callable $callable */
        $callable = [static::$instance, $name];

        return call_user_func_array($callable, $arguments);
    }
}
