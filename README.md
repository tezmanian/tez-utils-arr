# tez-utils-arr

A collection of focused, stateless array utility classes for PHP 8.3+. Each class does one thing and exposes a clean static API.

## Installation

```bash
composer require tez/utils-arr
```

## Components

| Class | Purpose |
|-------|---------|
| `ArrayDiff` | Recursively compares two arrays and returns all differences with dot-notation paths |
| `CartesianProduct` | Computes the Cartesian product of any number of sets lazily via a Generator |
| `DeepMerge` | Recursively merges override arrays into a base array |
| `Find` | Returns the first or last item matching a predicate |
| `Flatten` | Flattens a nested array into a single list |
| `GroupBy` | Groups items by a key derived from a callback |
| `KeyBy` | Builds an associative lookup map from a list |
| `MapWithKeys` | Maps over an array where the callback returns a `[key => value]` pair |
| `Paginator` | Paginates an array and returns an immutable `Page` with slice and metadata |
| `Partition` | Splits an array into two lists in a single pass |
| `Pluck` | Extracts values from an array of records using dot-notation keys |
| `SafeGet` | Safely reads typed values from deeply nested arrays using dot-notation paths |
| `Sliding` | Produces consecutive overlapping windows of a fixed size |
| `SortBy` | Sorts a copy of the array by a derived key |
| `Transpose` | Swaps rows and columns of a 2D array |
| `UniqueBy` | Removes duplicates where uniqueness is determined by a callback |
| `Wrap` | Ensures any value is an array |
| `Zip` | Combines multiple arrays element-by-element into a list of tuples |

---

## ArrayDiff

Recursively compares two arrays and returns all differences with dot-notation paths.

```php
use Tez\Utils\Arr\ArrayDiff;

$result = ArrayDiff::compare(
    ['user' => ['name' => 'Alice', 'age' => 30]],
    ['user' => ['name' => 'Bob',   'age' => 30, 'city' => 'Berlin']],
);

$result->hasChanges(); // true

foreach ($result->changes() as $change) {
    // $change->path  → 'user.name'
    // $change->type  → ChangeType::Changed
    // $change->old   → 'Alice'
    // $change->new   → 'Bob'
}

$result->paths(); // ['user.name', 'user.city']
```

Comparison is strict (`1 !== "1"`). Keys are reported as `ChangeType::Added`, `ChangeType::Removed`, or `ChangeType::Changed`.

---

## CartesianProduct

Computes the Cartesian product of any number of sets lazily via a Generator.

```php
use Tez\Utils\Arr\CartesianProduct;

foreach (CartesianProduct::create(['S', 'M', 'L'], ['red', 'blue']) as $tuple) {
    // ['S', 'red'], ['S', 'blue'], ['M', 'red'], ...
}
```

No intermediate array is built — suitable for large input sets.

---

## DeepMerge

Recursively merges override arrays into a base array. Multiple overrides are applied left to right.

```php
use Tez\Utils\Arr\DeepMerge;

$result = DeepMerge::merge(
    ['db' => ['host' => 'localhost', 'port' => 3306]],
    ['db' => ['host' => 'prod.example.com']],
);
// ['db' => ['host' => 'prod.example.com', 'port' => 3306]]
```

String keys are merged recursively when both sides are arrays. Integer keys are appended (like `array_merge`). Scalar values on the right side always win.

---

## Find

Returns the first or last item matching a predicate.

```php
use Tez\Utils\Arr\Find;

$first = Find::first($users, fn($u) => $u['active']);
$last  = Find::last($users,  fn($u) => $u['active']);
// Returns null when nothing matches
```

---

## Flatten

Flattens a nested array into a single list. Accepts an optional depth limit.

```php
use Tez\Utils\Arr\Flatten;

Flatten::flatten([1, [2, [3, 4]]]);           // [1, 2, 3, 4]
Flatten::flatten([1, [2, [3, 4]]], depth: 1); // [1, 2, [3, 4]]
```

---

## GroupBy

Groups items by a key derived from a callback.

```php
use Tez\Utils\Arr\GroupBy;

$grouped = GroupBy::group($orders, fn($o) => $o['status']);
// ['pending' => [...], 'shipped' => [...]]
```

---

## KeyBy

Builds an associative lookup map from a list. When two items produce the same key, the last wins.

```php
use Tez\Utils\Arr\KeyBy;

$byId = KeyBy::index($users, fn($u) => $u['id']);
// [42 => $user, 99 => $user, ...]
```

---

## MapWithKeys

Maps over an array where the callback returns a `[key => value]` pair.

```php
use Tez\Utils\Arr\MapWithKeys;

$result = MapWithKeys::map($products, fn($p) => [$p['sku'] => $p['price']]);
// ['ABC-1' => 9.99, 'XYZ-2' => 4.49]
```

---

## Paginator

Paginates an array and returns an immutable `Page` with slice and metadata.

```php
use Tez\Utils\Arr\Paginator;

$page = Paginator::paginate($items, page: 2, perPage: 15);

$page->items();       // items on this page
$page->total();       // total item count
$page->currentPage(); // 2
$page->lastPage();    // e.g. 7
$page->hasNext();     // true / false
$page->hasPrev();     // true
```

Throws `InvalidArgumentException` when `$page < 1` or `$perPage < 1`.

---

## Partition

Splits an array into two lists in a single pass — items passing the predicate first, failing items second.

```php
use Tez\Utils\Arr\Partition;

[$active, $inactive] = Partition::by($users, fn($u) => $u['active']);
```

---

## Pluck

Extracts values from an array of records using dot-notation keys.

```php
use Tez\Utils\Arr\Pluck;

// Extract a single column
Pluck::values($users, 'name'); // ['Alice', 'Bob', ...]

// Build key → value map
Pluck::keyedBy($users, valueKey: 'name', indexKey: 'id');
// [42 => 'Alice', 99 => 'Bob']

// Dot-notation for nested values
Pluck::values($orders, 'address.city');
```

Items where the key is absent are silently skipped.

---

## SafeGet

Safely reads typed values from deeply nested arrays using dot-notation paths. Returns `null` (or a default) when any segment is missing.

```php
use Tez\Utils\Arr\SafeGet;

$data = ['chart' => ['result' => [['meta' => ['currency' => 'EUR']]]]];

SafeGet::string($data, 'chart.result.0.meta.currency'); // 'EUR'
SafeGet::int($data,    'chart.result.0.meta.missing');  // null
SafeGet::float($data,  'price', default: 0.0);
SafeGet::bool($data,   'flags.active');
SafeGet::array($data,  'chart.result');
```

Integer-looking segments (e.g. `"0"`) address list indices automatically.

---

## Sliding

Produces consecutive overlapping windows of a fixed size.

```php
use Tez\Utils\Arr\Sliding;

Sliding::window([1, 2, 3, 4, 5], size: 3);
// [[1,2,3], [2,3,4], [3,4,5]]

Sliding::window([1, 2, 3, 4, 5], size: 3, step: 2);
// [[1,2,3], [3,4,5]]
```

---

## SortBy

Sorts a copy of the array by a derived key. Never mutates the original. Result is always re-indexed.

```php
use Tez\Utils\Arr\SortBy;

$sorted = SortBy::sort($products, fn($p) => $p['price']);
$desc   = SortBy::sort($products, fn($p) => $p['price'], descending: true);
```

---

## Transpose

Swaps rows and columns of a 2D array. All rows must have the same column count.

```php
use Tez\Utils\Arr\Transpose;

Transpose::matrix([[1, 2, 3], [4, 5, 6]]);
// [[1, 4], [2, 5], [3, 6]]
```

---

## UniqueBy

Removes duplicates where uniqueness is determined by a callback.

```php
use Tez\Utils\Arr\UniqueBy;

// Keep first occurrence (default)
UniqueBy::filter($items, fn($i) => $i['email']);

// Keep last occurrence
UniqueBy::filter($items, fn($i) => $i['email'], lastWins: true);

// Preserve original keys
UniqueBy::filter($items, fn($i) => $i['email'], preserveKeys: true);
```

---

## Wrap

Ensures any value is an array. `null` becomes `[]`, arrays pass through, everything else gets wrapped.

```php
use Tez\Utils\Arr\Wrap;

Wrap::ensure(null);    // []
Wrap::ensure('hello'); // ['hello']
Wrap::ensure([1, 2]);  // [1, 2]
```

---

## Zip

Combines multiple arrays element-by-element into a list of tuples.

```php
use Tez\Utils\Arr\Zip;

// Pad shorter arrays with null
Zip::zip([1, 2, 3], ['a', 'b']);
// [[1, 'a'], [2, 'b'], [3, null]]

// Stop at shortest input
Zip::shortest([1, 2, 3], ['a', 'b']);
// [[1, 'a'], [2, 'b']]
```

---

## Requirements

- PHP 8.3+
- `tez/utils-enum` ^1.0

## License

MIT
