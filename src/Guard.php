<?php

namespace Xtompie\Guard;

class Guard
{
    protected $value;

    /**
     * Returns a Guard with the specified value
     *
     * @param mixed $value
     * @return self
     */
    public static function of($value)
    {
        return new static($value);
    }

    /**
     * Returns a Guard with the specified value
     *
     * @param mixed $value
     * @return self
     */
    public static function ofEmpty()
    {
        return new static(null);
    }

    protected function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * If called without argument, returns true if there is a value present, otherwise false.
     * If called with argument and there is a value present, 
     * invokes callback with value as first argument and returns self
     *
     * @param \Closure $callback
     * @return boolean|self
     */
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

    /**
     * If called without argument, returns false if there is a value present, otherwise true.
     * If called with argument and there is no value present, invokes callback and returns self
     *
     * @param \Closure $callback
     * @return boolean|self
     */
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

    /**
     * Return the value.
     * If fallback is present and the value is null, than fallback is returned.
     *
     * @param \Closure $fallback
     * @return mixed
     */
    public function get($fallback = null)
    {
        return $this->is() ? $this->value : $fallback;
    }

    /**
     * Return the value.
     * If fallback is present and the value is null, than fallback is invoked and result returned.
     *
     * @param \Closure $fallback
     * @return mixed
     */
    public function getFn($fallback)
    {
        return $this->is() ? $this->get() : $fallback();
    }

    /**
     * If a value is present and the value matches the given predicate,
     * return a Guard describing the value, otherwise return an empty Guard
     * 
     * @param \Closure $predicate
     * @return self
     */
    public function filter($predicate)
    {
        if (!$this->is()) {
            return $this;
        }
        if ($predicate($this->get())) {
            return $this;
        }
        return static::ofEmpty();
    }

    /**
     * If a value is present and the value matches the given predicate,
     * return an empty Guard, otherwise return a Guard describing the value
     * 
     * @param \Closure $predicate
     * @return self
     */
    public function reject($predicate)
    {
        if (!$this->is()) {
            return $this;
        }
        if (!$predicate($this->get())) {
            return $this;
        }
        return static::ofEmpty();
    }

    /**
     * If value is present, map it to an object of the class. 
     * The mapping occurs by passing value as the first argument to the class constructor. 
     * If value is not present return empty guard. 
     *
     * @param \Clouser $mapper
     * @return self
     */
    public function map($mapper)
    {
        if (!$this->is()) {
            return $this;
        }
        return static::of($mapper($this->get()));
    }

    /**
     * Throw exception if value is not present
     * 
     * If exception provided as class name, next arguments are passed to exception constructor
     *
     * @param string|object $exception Exception object or class name, default NoValueException
     * @return self
     * @throws NoValueException
     * @throws $exception
     */
    public function except($exception = null, $msg = null)
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

    /**
     * If a current value is not present, return a Guard with value provided as argument
     *
     * @param mixed $value Value when current value is null
     * @return self
     */
    public function blank($value)
    {
        if ($this->is()) {
            return $this;
        }
        return static::of($value);
    }    

    /**
     * If a current value is not present, return a Guard with value provided by valueProvider
     *
     * @param \Closure $valueProvider Value provider when current value is null
     * @return self
     */
    public function blankFn($valueProvider)
    {
        if ($this->is()) {
            return $this;
        }
        return static::of($valueProvider());
    }

    /**
     * Returns the Let capture mechanism for value 
     *
     * @return Let
     */
    public function let()
    {
        return new Let($this->get());
    }

    /**
     * If a value is present, it maps it to an object of that class.
     * The value is passed as the first argument to the constructor.
     * and if the result is non-null, return a Guard describing the result.
     * Otherwise return an empty Guard.
     *
     * @param string $class
     * @return self
     */
    public function to($class)
    {
        if (!$this->is()) {
            return $this;
        }

        return static::of(new $class($this->get()));
    }

    /**
     * Returnsstring representation for debugging. 
     *
     * @return string the string representation of this instance
     */
    public function __toString()
    {
        return $this->value === null 
            ? 'Guard.empty' 
            : sprintf('Guard(%s)', strval($this->value));
    }
}
