# Guard - PHP Library

Guard container object for dealing with optional values or null values

```php
$user = Guard::of(request()->input('id')) // wrap value
    ->filter(fn($id) => ctype_digit($id)) // filter arg called when value is not null
    ->map(fn($id) => User::find($id)) // map arg called when value is not null
    ->not(fn() => abort(404)) // not arg called when value is null
    ->is(fn($user) => info("User found {$user->id}"))  // is arg called when value is not null
    ->get(); // get raw value - instace of User::class
```

## Instalation

`composer require xtompie/guard`

## Doc

Guard object is immutable.

Methods:
- `of` - Creates guard
- `is` - Tells if value present or runs callback when value present
- `not` - Same as `is` but negation
- `get` - Gets raw value
- `getFn` - Gets raw value with fallback callback
- `filter` - Filter the value if present
- `reject` - Same as `filter` but negation
- `map` - Maps value if present 
- `except` - Throw exception if value is not present
- `blank` - Gets guard with provided value if current is not present
- `blankFn` - Same as `blank` but with callback
- `let` - Returns guard capture mechanism for nested properties, calls, array offsets

More info in source [Guard.php](https://github.com/xtompie/guard/blob/master/src/Guard.php)


## Usage

1. NoValueException
```php
Guard::of(null)->throw()->get(); // NoValueException will be thrown
```

2. Default value 
```php
echo Guard::of(null)->get('default'); // -> default
```

3. Complex type and value guarding
```php
function divide($a, $b) {
    $b = Guard::of($b)
        ->map(fn($i) => (int)$i)
        ->reject(fn($i) => $i === 0)
        ->except(\UnexpectedValueException::class)
        ->get();
    return $a / $b;
}
```

4. Let

`let()` Returns Let object. Offset get, property get, method call can be called on Let. 
After that operation new Guard with operation result will be returned.
When offset, property, method not exist, Guard with null will be returned.

```php
$options = [
    'a' => 'A',
    'b' => 'B',
];
$key = 'c';
echo Guard::of($options)->let()[$key]->get();
```

```php
echo Guard::of(new \stdClass())
    ->let()->nonExistingMethod()
    ->let()->nonExistingProperty
    ->let()['nonExistingOffset']
    ->get('Undefined')
;
```

5. Extending

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
