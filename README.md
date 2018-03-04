# ![Igni logo](https://github.com/igniphp/common/blob/master/logo/full.svg)![Build Status](https://travis-ci.org/igniphp/validation.svg?branch=master)

## Igni Validation
Licensed under MIT License.

**Igni** validation is simple, lightweight and extensible validation library.

## Installation

```
composer install igniphp/validation
```

## Introduction

### Basic example

```php
<?php

use Igni\Validation\Constraint;

$numberValidator = Constraint::number($min = 0);

$numberValidator(1);// true
$numberValidator(-1);// false
$numberValidator->validate(1);// true, same as above
$numberValidator(1.0);// true
$numberValidator->validate('a'); // false
```

### Getting error information

Allows to validate complex arrays 

```php
<?php

use Igni\Validation\Constraint;
use Igni\Validation\Failures;
use Igni\Validation\Exception\ValidationException;

$userValidator = Constraint::group([
    'name' => Constraint::alnum(),
    'age' => Constraint::number(1, 200),
    'email' => Constraint::email(),
    'address' => Constraint::text(),
]);

$userValidator([
    'name' => 'John',
    'age' => 233,
    'email' => 'johnmail',
]);// false


$validationFailures = $userValidator->getFailures();

$validationFailures[0] instanceof Failures\OutOfRangeFailure;// true
$validationFailures[0]->getContext()->getName();//age

$validationFailures[1] instanceof Failures\EmptyValueFailure;// true
$validationFailures[1]->getContext()->getName();//address

// Exception can also be factored out of failure instance
throw ValidationException::forValidationFailure($validationFailures[0]);
```

## API

### `Constraint::alnum(int $min = null, int $max = null)`

Creates validator that checks if passed value contains only digits and letters. 

#### Parameters
- `$min` defines minimum length 
- `$max` defines maximum length

#### Example
```php
<?php
use Igni\Validation\Constraint;

$validator = Constraint::alnum($minLength = 2);
var_dump($validator('a1')); // true
```

### `Constraint::alpha(int $min = null, int $max = null)`

Creates validator that checks if passed value contains only letters.

#### Parameters
- `$min` defines minimum length 
- `$max` defines maximum length

#### Example
```php
<?php
use Igni\Validation\Constraint;

$validator = Constraint::alpha($minLength = 2);
var_dump($validator('aaa')); // true
```

### `Constraint::boolean()`

Creates validator that checks if passed value is valid boolean expression.

#### Example
```php
<?php
use Igni\Validation\Constraint;

$validator = Constraint::boolean();
var_dump($validator(false)); // true
```

### `Constraint::chain(Rule ...$rules)`

Creates validator that uses other validators to perform multiple validations on passed value.

#### Example
```php
<?php
use Igni\Validation\Constraint;

$validator = Constraint::chain(Constraint::text(), Constraint::date());
var_dump($validator('2018-09-10')); // true
```

### `Constraint::date(string $format = null, $min = null, $max = null)`

Creates validator that checks if passed value is valid date. 

#### Parameters
 - `$format` restricts format of passed value
 - `$min` defines minimum date range 
 - `$max` defines maximum date range
 
#### Example
 ```php
<?php
use Igni\Validation\Constraint;
 
$validator = Constraint::date('Y-m-d');
var_dump($validator('2018-09-10')); // true
 ```
     
### `Constraint::email()`

Creates validator that checks if passed value is valid email address.

#### Example
 ```php
<?php
use Igni\Validation\Constraint;
 
$validator = Constraint::email();
var_dump($validator('test@test.com')); // true
 ```

### `Constraint::falsy()`

Creates validator that checks if passed value is valid falsy expression;
- `off`
- `no`
- `false`
- 0

#### Example
 ```php
<?php
use Igni\Validation\Constraint;
 
$validator = Constraint::falsy();
var_dump($validator('no')); // true
 ```

### `Constraint::truthy()`

Creates validator that checks if passed value is valid truthy expression;
- `on`
- `true`
- 1
- `yes`

#### Example
 ```php
<?php
use Igni\Validation\Constraint;
 
$validator = Constraint::truthy();
var_dump($validator('yes')); // true
 ```

### `Constraint::in(...$values)`

Creates validator that checks if passed value exists in defined list of values.

#### Example
 ```php
<?php
use Igni\Validation\Constraint;
 
$validator = Constraint::in('no', 'yes', 'test');
var_dump($validator('no')); // true
 ```

### `Constraint::integer(int $min = null, int $max = null)`

Creates validator that checks if passed value is valid integer expression.

#### Parameters
 - `$min` defines minimum value
 - `$max` defines maximum value

#### Example
 ```php
<?php
use Igni\Validation\Constraint;
 
$validator = Constraint::integer(10, 100);
var_dump($validator(11)); // true
 ```

### `Constraint::number(int $min = null, int $max = null)`

Creates validator that checks if passed value is valid number expression.

#### Parameters
 - `$min` defines minimum value
 - `$max` defines maximum value

#### Example
 ```php
<?php
use Igni\Validation\Constraint;
 
$validator = Constraint::number(10, 100);
var_dump($validator('11.2')); // true
 ```
 
### `Constraint::uuid()`

Creates validator that checks if passed value is valid uuid.

#### Example
 ```php
<?php
use Igni\Validation\Constraint;
 
$validator = Constraint::uuid();
var_dump($validator('1ff60619-81cc-4d8e-88ac-a3ae36a97dce')); // true
 ```

### `Constraint::text()`

Creates validator that accepts every non empty string.

### `Constraint::group(array $validators)`

Creates validator that validates passed value by group of defined validators.

#### Example
 ```php
<?php
use Igni\Validation\Constraint;
 
$validator = Constraint::group([
    'name' => Constraint::text(),
    'age' => Constraint::integer(1, 200),
    'email' => Constraint::email(),
]);
var_dump($validator(['name' => 'John Doe', 'age' => 29, 'email' => 'john@gmail.com'])); // true
 ```

## Creating custom validator

To create custom validator we have to simply extend `\Igni\Validation\Rule` class, please consider following example:

```php
<?php declare(strict_types=1);

use Igni\Validation\Rule;

class ValidateIn extends Rule
{
    public function __construct(...$values)
    {
        $this->attributes['valid_values'] = $values;
    }

    protected function assert($input): bool
    {
        return in_array($input, $this->attributes['valid_values'], $strict = true);
    }
}

```

That's all folks!
