# Guard

Guard container object for dealing with optional/null values.

```php
use Xtompie\Guard\Guard;

$user = Guard::of(request()->input('id'))
    ->filter(fn($id) => ctype_digit($id))
    ->map(fn($id) => User::find($id))
    ->not(fn() => abort(404))
    ->is(fn($user) => info("User found {$user->id}"))
    ->get();
```

## Installation

Using [composer](https://getcomposer.org/)

```
composer require xtompie/guard
```

## Docs

Guard protects for calling methods on nulls values. 
The value might or might not be present.
It has elegant fluent chaining syntax.
No need for if/else statments and additional variables. 
Guard syntax is cleaner and more readable.
Guard object is immutable.

Methods:
- `of` - Creates guard
- `ofEmpty` - Creates empty guard
- `is` - Tells if value present or runs callback when value present
- `not` - Same as `is` but negation
- `get` - Gets raw value
- `getFn` - Gets raw value with fallback callback
- `filter` - Filter the value if present
- `reject` - Same as `filter` but negation
- `map` - Maps value if present 
- `assert` - Throw exception if value is not present
- `blank` - Gets guard with provided value if current is not present
- `blankFn` - Same as `blank` but with callback
- `let` - Returns guard capture mechanism for nested properties, calls, array offsets

More info in source [Guard.php](src/Guard.php)

### Usage

#### NoValueException

```php
use Xtompie\Guard\Guard;

Guard::of(null)->assert()->get(); // NoValueException will be thrown
```

#### Default value 
```php
use Xtompie\Guard\Guard;

echo Guard::of(null)->get('default'); // -> default
```

#### Complex type and value
```php
use Xtompie\Guard\Guard;

function divide($a, $b) {
    $b = Guard::of($b)
        ->map(fn($i) => (int)$i)
        ->reject(fn($i) => $i === 0)
        ->assert(\UnexpectedValueException::class)
        ->get();
    return $a / $b;
}
```

#### Let

`let()` Returns Let object. Offset get, property get, method call can be called on Let. 
After that operation new Guard with operation result will be returned.
When offset, property, method not exist, empty Guard will be returned.

```php
use Xtompie\Guard\Guard;

$options = [
    'a' => 'A',
    'b' => 'B',
];
$key = 'c';
echo Guard::of($options)->let()[$key]->get();
```

```php
use Xtompie\Guard\Guard;

echo Guard::of(new \stdClass())
    ->let()->nonExistingMethod()
    ->let()->nonExistingProperty
    ->let()['nonExistingOffset']
    ->get('Undefined')
;
```

#### Extending

```php
namespace MyApp\Util;

use Xtompie\Guard\Guard as BaseGuard;

class Guard extends BaseGuard
{
    public function or404()
    {
        $this->not(fn() => abort(404));
    }

    public function reject0()
    {
        return $this->reject(fn($i) => $i === 0);
    }
}

echo gettype(Guard::of(0)->reject0()->get());
```
