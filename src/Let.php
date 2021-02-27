<?php

namespace Xtompie\Guard;

use ArrayAccess;
use LogicException;

class Let implements ArrayAccess
{
    protected $___subject;

    public function __construct($subject)
    {
        $this->___subject = $subject;
    }

    public function __invoke()
    {
        if (!is_callable($this->___subject)) {
            return Guard::of(null);
        }
        return Guard::of(call_user_func_array($this->___subject, func_get_args()));
    }

    public function __call($method, $args)
    {
        if (!is_object($this->___subject)) {
            return Guard::of(null);
        }
        if (!is_callable([$this->___subject, $method])) {
            return Guard::of(null);
        }
        return Guard::of(call_user_func_array([$this->___subject, $method], $args));
    }

    public function __get($property)
    {
        if (!is_object($this->___subject)) {
            return Guard::of(null);
        }
        if (
            !property_exists($this->___subject, $property)
            && !isset($this->___subject->$property) // isset will call magic __isset() if present
        ) {
            return Guard::of(null);
        }
        return Guard::of($this->___subject->$property);
    }

    public function offsetExists($offset)
    {
        throw new \LogicException();
    }

    public function offsetGet($offset)
    {
        if (!is_array($this->___subject)) {
            return Guard::of(null);
        }
        if (!array_key_exists($offset, $this->___subject)) {
            return Guard::of(null);
        }
        return Guard::of($this->___subject[$offset]);  
    }

    public function offsetSet($offset, $value)
    {
        throw new LogicException();
    }

    public function offsetUnset($offset)
    {
        throw new \LogicException();
    }
}