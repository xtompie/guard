<?php

namespace Xtompie\Guard;

class Guard
{
    protected $value;

    public static function of($value)
    {
        return new static($value);
    }

    protected function __construct($value)
    {
        $this->value = $value;
    }

    public function is($callback = null)
    {
        if (func_num_args() == 0) {
            return $this->value !== null;
        }
        if ($this->value !== null) {
            $callback($this->get());
        }
        return $this;
    }

    public function not($callback = null)
    {
        if (func_num_args() == 0) {
            return $this->value === null;
        }
        if (!$this->is()) {
            $callback();
        }
        return $this;
    }    

    public function get($fallback = null)
    {
        return $this->is() ? $this->value : $fallback;
    }

    public function getFn($fallback)
    {
        return $this->is() ? $this->get() : $fallback();
    }

    public function filter($predicate)
    {
        if (!$this->is()) {
            return $this;
        }
        if ($predicate($this->get())) {
            return $this;
        }
        return static::of(null);
    }

    public function reject($predicate)
    {
        if (!$this->is()) {
            return $this;
        }
        if (!$predicate($this->get())) {
            return $this;
        }
        return static::of(null);
    }

    public function map($mapper)
    {
        if (!$this->is()) {
            return $this;
        }
        return static::of($mapper($this->get()));
    }

    public function except($exception = null)
    {
        if ($this->is()) {
            return $this;
        }
        if ($exception === null) {
            throw new NoValueException("Guard no value");
        }
        if (!is_object($exception)) {
            $args = array_slice(func_get_args(), 1);
            $exception = (new \ReflectionClass($exception))->newInstanceArgs($args);
        }
        throw $exception;
    }

    public function blank($value)
    {
        if ($this->is()) {
            return $this;
        }
        return static::of($value);
    }    

    public function blankFn($valueProvider)
    {
        if ($this->is()) {
            return $this;
        }
        return static::of($valueProvider());
    }


}
