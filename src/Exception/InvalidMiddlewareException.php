<?php

namespace League\Tactician\Exception;

use InvalidArgumentException;

/**
 * Thrown when the CommandBus was instantiated with an invalid middleware object
 */
class InvalidMiddlewareException extends InvalidArgumentException implements Exception
{
    /**
     * @param $middleware
     *
     * @return InvalidMiddlewareException
     */
    public static function forMiddleware($middleware): InvalidMiddlewareException
    {
        $name = is_object($middleware) ? get_class($middleware) : gettype($middleware);
        $message = sprintf(
            'Cannot add "%s" to middleware chain as it does not implement the Middleware interface.',
            $name
        );
        return new static($message);
    }
}
