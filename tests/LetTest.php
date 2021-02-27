<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Xtompie\Guard\Guard;
use Xtompie\Guard\Let;


class LetTest extends TestCase
{
    public function testInvokeObject()
    {
        // given
        $subject = new class 
        {
            public function __invoke()
            {
            }
        };
        $let = new Let($subject);

        // when
        $guard = $let(); 

        // then
        $this->assertInstanceOf(Guard::class, $guard);
    }

    public function testInvokeObjectReturnValue()
    {
        // given
        $subject = new class 
        {
            public function __invoke()
            {
                return 42;
            }
        };
        $let = new Let($subject);
        $guard = $let();

        // when
        $value = $guard->get(); 

        // then
        $this->assertEquals(42, $value);
    }

    public function testInvokeClosure()
    {
        // given
        $subject = function () {};
        $let = new Let($subject);

        // when
        $guard = $let(); 

        // then
        $this->assertInstanceOf(Guard::class, $guard);
    }

    public function testInvokeClosureReturnValue()
    {
        // given
        $subject = function () {
            return 42;
        };
        $let = new Let($subject);
        $guard = $let();

        // when
        $value = $guard->get(); 

        // then
        $this->assertEquals(42, $value);
    }

    public function testCallMethod()
    {
        // given
        $subject = new class 
        {
            public function foobar()
            {
            }
        };
        $let = new Let($subject);

        // when
        $guard = $let->foobar(); 

        // then
        $this->assertInstanceOf(Guard::class, $guard);
    }

    public function testCallMethodReturnValue()
    {
        // given
        $subject = new class 
        {
            public function foobar()
            {
                return 42;
            }
        };
        $let = new Let($subject);
        $guard = $let->foobar();

        // when
        $value = $guard->get(); 

        // then
        $this->assertEquals(42, $value);
    }

    public function testProperty()
    {
        // given
        $subject = new class 
        {
            public $foobar;
        };
        $let = new Let($subject);

        // when
        $guard = $let->foobar; 

        // then
        $this->assertInstanceOf(Guard::class, $guard);
    }

    public function testPropertyWithValue()
    {
        // given
        $subject = new class 
        {
            public $foobar = 42;
        };
        $let = new Let($subject);
        $guard = $let->foobar;

        // when
        $value = $guard->get(); 

        // then
        $this->assertEquals(42, $value);
    }
        
    public function testMagicPropertyWithValue()
    {
        // given
        $subject = new class 
        {
            public function __isset($property)
            {
                return true;
            }
            public function __get($property)
            {
                return 42;
            }
        };
        $let = new Let($subject);
        $guard = $let->foobar;

        // when
        $value = $guard->get(); 

        // then
        $this->assertEquals(42, $value);
    }

    public function testArrayOffset()
    {
        // given
        $let = new Let([]);

        // when
        $guard = $let['foobar']; 

        // then
        $this->assertInstanceOf(Guard::class, $guard);
    }    

    public function testArrayOffsetWithValue()
    {
        // given
        $let = new Let(['foobar' => 42]);
        $guard = $let['foobar'];

        // when
        $value = $guard->get(); 

        // then
        $this->assertEquals(42, $value);
    }
}