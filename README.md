# Guard - PHP Library

Guard container object for dealing with optional values or null values

```php
$user = Guard::of(request()->input('id'))
    ->map(fn($id) => User::find($id))
    ->not(fn() => abort(404))
    ->get();
```

**Draft**

## Usage

```php


abort_unless($user = User::find(request()->input('id')), 404);

$user = Guard::of(request()->input('id'))
    ->map(fn($id) => User::find($id))
    ->not(fn() => abort(404))
    ->get()

$user = request()->Guard::of('id')
    ->map(fn($id) => User::find($id))
    ->else404()
    ->get()

::of(request()->input('id'))
->is()
->filter(fn($value) => $value > 0)
->reject()
->map(fn($id) => User::find($id))
->let()->id
->else404()
->else403()
->throw(IllegalArgumentException::class)
->get($default)
->getFn(fn() => User::first())
->if(fn($v) => dump($v))
->else(fn($v) => echo "no data :(")



// UC NullPointerException
$notnull = Guard::of(null)->throw()->get(); // NullPointerException will be thrown

// UC default value if null
$notnull = Guard::of(null)->get('default'); // $notnull will be 'default'

// UC IllegalArgumentException
function divide($a, $b) {
    $b = Guard::of($b)
        ->throw(IllegalArgumentException::class, 'Divided by 0')
        ->get();
}


// UC
$user = Guard::of(request()->input('id'))
    ->map(fn($id) => User::find($id))
    ->else404()
    ->get()

// UC 
$avatar = Guard::of(request()->input('id'))
    ->map(fn($id) => User::with('avatar')->find($id))
    ->else404()
    ->Guard::of()->avatar
    ->else403()
    ->get()

// UC 
$option = [
    'a' => 'A',
    'b' => 'B',
];
$key = 'c';
$label = Guard::of($option)
    ->let()[$key]
    ->get('Undefined')




```