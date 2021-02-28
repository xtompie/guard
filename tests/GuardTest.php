<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Xtompie\Guard\Guard;
use Xtompie\Guard\Let;
use Xtompie\Guard\NoValueException;

class GuardTest extends TestCase
{
    public function test_ofEmpty()
    {
        // given

        // when
        $value = Guard::ofEmpty(null);

        // then
        $this->assertFalse($value->is());
    }

    public function test_is_returnsTrueForTrueValue()
    {
        // given

        // when
        $value = Guard::of(true);

        // then
        $this->assertTrue($value->is());
    }

    public function test_is_returnsTrueForNumber1Value()
    {
        // given

        // when
        $value = Guard::of(1);

        // then
        $this->assertTrue($value->is());
    }

    public function test_is_returnsFalseForNullValue()
    {
        // given

        // when
        $value = Guard::of(null);

        // then
        $this->assertFalse($value->is());
    }

    public function test_is_callbackIsCalled()
    {
        // given
        $called = false;
        $callback = function () use (&$called) {
            $called = true;
        };
        $value = Guard::of(true);

        // when
        $value->is($callback);
        
        // than
        $this->assertTrue($called);
    }

    public function test_is_callbackIsNotCalled()
    {
        // given
        $called = false;
        $callback = function () use (&$called) {
            $called = true;
        };
        $value = Guard::of(null);

        // when
        $value->is($callback);
        
        // than
        $this->assertFalse($called);
    }    

    public function test_is_returnsSelfWithCallback()
    {
        // given
        $callback = function () {};
        $value = Guard::of(null);

        // when
        $return = $value->is($callback);
        
        // than
        $this->assertSame($value, $return);
    }

    public function test_is_callbackArgument()
    {
        // given
        $origin = 'foobar';
        $capture = null;
        $callback = function ($value) use (&$capture) {
            $capture = $value;
        };
        $value = Guard::of($origin);

        // when
        $value->is($callback);
        
        // than
        $this->assertEquals($origin, $capture);
    }

    public function test_not_returnsFalseForTrueValue()
    {
        // given

        // when
        $value = Guard::of(true);

        // then
        $this->assertFalse($value->not());
    }

    public function test_not_returnsFalseForNumber1Value()
    {
        // given

        // when
        $value = Guard::of(1);

        // then
        $this->assertFalse($value->not());
    }

    public function test_not_returnsTrueForNullValue()
    {
        // given

        // when
        $value = Guard::of(null);

        // then
        $this->assertTrue($value->not());
    }

    public function test_not_callbackIsNotCalled()
    {
        // given
        $called = false;
        $callback = function () use (&$called) {
            $called = true;
        };
        $value = Guard::of(true);

        // when
        $value->not($callback);
        
        // than
        $this->assertFalse($called);
    }

    public function test_not_callbackIsCalled()
    {
        // given
        $called = false;
        $callback = function () use (&$called) {
            $called = true;
        };
        $value = Guard::of(null);

        // when
        $value->not($callback);
        
        // than
        $this->assertTrue($called);
    }    

    public function test_not_returnsSelfWithCallback()
    {
        // given
        $callback = function () {};
        $value = Guard::of(null);

        // when
        $return = $value->not($callback);
        
        // than
        $this->assertSame($value, $return);
    }

    public function test_get_true()
    {
        // given
        $origin = true;

        // when
        $value = Guard::of($origin);

        // then
        $this->assertSame($value->get(), $origin);
    }

    public function test_get_null()
    {
        // given
        $origin = null;

        // when
        $value = Guard::of($origin);

        // then
        $this->assertSame($value->get(), $origin);
    }

    public function test_get_array()
    {
        // given
        $origin = ['a', 'b'];

        // when
        $value = Guard::of($origin);

        // then
        $this->assertSame($value->get(), $origin);
    }

    public function test_get_fallbackIsUsedForNull()
    {
        // given
        $fallback = 'b';
        $value = Guard::of(null);

        // when
        $result = $value->get($fallback);

        // then
        $this->assertSame($result, $fallback);
    }    
    
    public function test_get_fallbackIsNotUserForNotNull()
    {
        // given
        $origin = 'a';
        $fallback = 'b';
        $value = Guard::of($origin);

        // when
        $result = $value->get($fallback);

        // then
        $this->assertSame($result, $origin);
    } 

    public function test_getFn_fallbackIsCalledForNull()
    {
        // given
        $value = Guard::of(null);

        // when
        $value = $value->getFn(fn() => 3);

        // then
        $this->assertSame($value, 3);
    }

    public function test_getFn_fallbackIsNotUsedForNotNull()
    {
        // given
        $value = Guard::of(2);

        // when
        $value = $value->getFn(fn() => 3);

        // then
        $this->assertSame($value, 2);
    }    
    
    public function test_filter_valueIsfiltered()
    {
        // given
        $origin = 3;
        $predicate = function ($value) {
            return $value == 2;
        };
        $value = Guard::of($origin);

        // when
        $value = $value->filter($predicate);
        
        // than
        $this->assertNull($value->get());        
    }

    public function test_filter_valueIsNotFiltered()
    {
        // given
        $origin = 2;
        $predicate = function ($value) {
            return $value == 2;
        };
        $value = Guard::of($origin);

        // when
        $value = $value->filter($predicate);
        
        // than
        $this->assertSame($value->get(), $origin);        
    }    

    public function test_filter_callbackIsNotCalledForNullValue()
    {
        // given
        $called = false;
        $callback = function () use (&$called) {
            $called = true;
        };
        $value = Guard::of(null);

        // when
        $value->filter($callback);
        
        // than
        $this->assertFalse($called);
    }  
    
    public function test_reject_valueIsRejected()
    {
        // given
        $origin = 3;
        $predicate = function ($value) {
            return $value == 2;
        };
        $value = Guard::of($origin);

        // when
        $value = $value->reject($predicate);
        
        // than
        $this->assertEquals($value->get(), $origin);        
    }

    public function test_reject_valueIsNotRejected()
    {
        // given
        $origin = 2;
        $predicate = function ($value) {
            return $value == 2;
        };
        $value = Guard::of($origin);

        // when
        $value = $value->reject($predicate);
        
        // than
        $this->assertNull($value->get());        
    }    

    public function test_reject_callbackIsNotCalled()
    {
        // given
        $called = false;
        $callback = function () use (&$called) {
            $called = true;
        };
        $value = Guard::of(null);

        // when
        $value->reject($callback);
        
        // than
        $this->assertFalse($called);
    }
    
    public function test_map_valueIsMapped()
    {
        // given
        $mapper = function ($value) {
            return $value + 1;
        };
        $value = Guard::of(2);

        // when
        $value = $value->map($mapper);
        
        // than
        $this->assertEquals($value->get(), 3);        
    }    

    public function test_map_callbackIsNotCalledForNullValue()
    {
        // given
        $called = false;
        $callback = function () use (&$called) {
            $called = true;
        };
        $value = Guard::of(null);

        // when
        $value->map($callback);
        
        // than
        $this->assertFalse($called);
    }    
    
    public function test_except_defaultExceptionIsThrownForNullValue()
    {
        // given
        $this->expectException(NoValueException::class);
        $value = Guard::of(null);

        // when
        $value->except();
        
        // than
    }

    public function test_except_customExceptionIsThrownForNullValue()
    {
        // given
        $this->expectException(\LogicException::class);
        $value = Guard::of(null);

        // when
        $value->except(\LogicException::class);
        
        // than
    }    

    public function test_except_customExceptionWitMsgIsThrownForNullValue()
    {
        // given
        $this->expectExceptionMessage('Foobar');
        $value = Guard::of(null);

        // when
        $value->except(\LogicException::class, 'Foobar');
        
        // than
    }    
    
    public function test_except_exceptionIsNotThrownForTrueValue()
    {
        // given
        $value = Guard::of(true);

        // when
        $value->except();
        
        // than
        $this->assertTrue(true);
    }

    public function test_blank_valueIsAssignedForNullValue()
    {
        // given
        $blank = 42;
        $value = Guard::of(null);

        // when
        $value = $value->blank($blank);
        
        // than
        $this->assertEquals($value->get(), $blank);
    }      
    
    public function test_blank_valueIsNotAssigndForTrueValue()
    {
        // given
        $blank = 42;
        $value = Guard::of(true);

        // when
        $value = $value->blank($blank);
        
        // than
        $this->assertTrue($value->get());
    }
    
    public function test_blankFn_valueIsAssignedForNullValue()
    {
        // given
        $blank = fn() => 42;
        $value = Guard::of(null);

        // when
        $value = $value->blankFn($blank);
        
        // than
        $this->assertEquals($value->get(), $blank());
    }      
    
    public function test_blankFn_valueIsNotAssigndForTrueValue()
    {
        // given
        $blank = fn() => 42;
        $value = Guard::of(true);

        // when
        $value = $value->blankFn($blank);
        
        // than
        $this->assertTrue($value->get());
    } 
    
    public function test_let_returnLetInstance()
    {
        // given
        $guard = Guard::of(null);

        // when
        $let = $guard->let();

        // than
        $this->assertInstanceOf(Let::class, $let);
    }

    public function test_let_returnValueFromLetInstance()
    {
        // given
        $guard = Guard::of(['foobar' => 42]);
        $let = $guard->let();

        // when
        $value = $let['foobar'];

        // than
        $this->assertEquals(42, $value->get());
    }

    public function test_let_deepWalk()
    {
        // given
        $guard = Guard::of(['a' => ['b' => ['c' => ['d' => ['e' => ['f' => 42]]]]]]);

        // when
        $value = $guard->let()['a']->let()['b']->let()['c']->let()['d']->let()['e']->let()['f'];

        // than
        $this->assertEquals(42, $value->get());
    }

    public function test_toString_empty()
    {
        // given
        $guard = Guard::of(null);

        // when
        $description = (string)$guard;

        // than
        $this->assertEquals("Guard.empty", $description);
    }

    public function test_toString_withFoobarString()
    {
        // given
        $guard = Guard::of("foobar");

        // when
        $description = (string)$guard;

        // than
        $this->assertEquals("Guard(foobar)", $description);
    }

}