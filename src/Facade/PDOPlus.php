<?php

declare(strict_types=1);

namespace Ancarda\PDOPlus\Facade;

use Ancarda\PDOPlus\PDOPlus as RealClass;

/**
 * A global instance of PDOPlus
 *
 * Use setInstance to store a PDOPlus in global scope.
 * After that, all subsequent interactions with this class can be used as if it were a PDOPlus.
 *
 * For documentation, refer to the PDOPlus class itself.
 *
 * @see RealClass
 */
class PDOPlus
{
    /** @var RealClass */
    protected static $instance;

    /**
     * Store a PDOPlus in global scope
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
