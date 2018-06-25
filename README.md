# ![Igni logo](https://github.com/igniphp/common/blob/master/logo/full.svg)
[![Build Status](https://travis-ci.org/igniphp/storage.svg?branch=master)](https://travis-ci.org/igniphp/storage)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/igniphp/storage/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/igniphp/storage/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/igniphp/storage/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/igniphp/storage/?branch=master)

## Igni Storage
Licensed under MIT License.

## Introduction

Igni/storage is minimalistic mapping/hydration library with support for PDO and MongoDB databases with cross database
access. 

### Features
###### Works with native queries
Just pass your query to the driver, you are no longer limited to custom query builders api, or complex setup and hacks
to force library to work with your input.

###### Small learning curve
There is one page documentation which you can grasp in an hour and many examples that are working straight away
without complex configuration.

###### Support for multiple types of databases 
Mongo, pgsql, mysql, sqlite - you can use all of them together. If this is not sufficient you can write custom driver to support database of your choice. 

###### Embed entities
Allows you to store complex data in your database 

###### Cross database references
It does not matter if you use mongo with sqlite or mysql or any other database, you can keep references to entities stored
in different types of databases with ease. 

### Requirements

 - >= PHP 7.1
 - PDO for mysql, sqlite and/or pgsql support
 - MongoDB extension for mongo support
 
 
### Installation

```
composer install igniphp/storage
```

### Basic Concepts

Igni strongly bases on repository and unit of work patterns. This two patterns are intended to create an abstraction 
layer between the data access layer and the business logic layer of your application. 

The facilitation that is created by UoW makes track of changes and automated unit testing to be achieved in much simpler manner.

###### Unit of Work
Shortly saying UoW _maintains a list of objects affected by a business transaction and coordinates the writing out of changes and the resolution of concurrency problems ([source](https://martinfowler.com/eaaCatalog/unitOfWork.html))._

[Entity Storage](src/EntityStorage.php) is responsible for providing UoW implementation.

###### Repositories
Repository is a central place where data is stored and maintained. Igni provides basic implementation per each of the supported drivers.
 

## Connecting

### Mysql/PgSQL

```php
<?php
use Igni\Storage\Driver\Pdo\Connection;
use Igni\Storage\Driver\Pdo\ConnectionOptions;

$connection = new Connection('localhost', new ConnectionOptions(
    $type = 'mysql',
    $database = 'test',
    $username = 'root',
    $password = 'password'
));
```

### Sqlite

```php
<?php
use Igni\Storage\Driver\Pdo\Connection;
use Igni\Storage\Driver\Pdo\ConnectionOptions;

$connection = new Connection('path/to/database', new ConnectionOptions(
    $type = 'sqlite'
));
```

### MongoDB

```php
<?php
use Igni\Storage\Driver\MongoDB\Connection;
use Igni\Storage\Driver\MongoDb\ConnectionOptions;

$connection = new Connection('localhost', new ConnectionOptions(
    $database = 'test',
    $username = 'test',
    $password = 'password'
));
```

## Mapping

Mapping tells how data that is stored in database should be reflected in your code.

Library provides following tools to map data:
- Repositories (manages entities and provides access to your entities)
- Cursors (used to process and execute queries in the database)
- Collections (abstraction layer around cursors)
- Hydrators (used to map data from and to database)
- Entities (unit of data, can be single person, place or thing)

### Repositories
Repository is a central place where data is stored and maintained.

#### Defining Repository

#### Registering Repository

#### Working with cursor

### Entity
An entity is an object that exists. It can perform various actions and has its own identity. 
An entity can be a single thing, person, place, or object. Entity defines attributes, which keeps information about
what entity needs in order to live. 

#### Defining Entities
Entity must implement `\Storage\Entity` interface. The interface requires you to define `getId` method.

The simplest entity may look like this:

```php
<?php
use Igni\Storage\Entity;
use Igni\Storage\Id;
use Igni\Storage\Id\Uuid;

class SongEntity implements Entity
{
    private $id;
    
    public function __construct()
    {
        $this->id = new Uuid();
    }
    
    public function getId(): Id
    {
        return $this->id;
    }
}
```

This entity cannot be stored yet. What is missing here is:
- the information where entity should be persisted
- which property keeps entity's identity

This and other meta information can be injected to the entity with annotations. Annotation is a note by way of explanation/comment
added to a code. In php world it is kept in doc block comment prefixed by `@`.

Following example stores song in table/collection named `songs` with identity set on `id` property.

```php
<?php
use Igni\Storage\Entity;
use Igni\Storage\Id;
use Igni\Storage\Id\Uuid;
use Igni\Storage\Mapping\Annotations as Storage;

/**
 * @Storage\Entity(source="albums")
 */
class SongEntity implements Entity
{
    /**
     * @var Uuid
     * @Storage\Types\Id()
     */
    private $id;
    
    public function __construct()
    {
        $this->id = new Uuid();
    }
    
    public function getId(): Id
    {
        return $this->id;
    }
}
```
The above entity can be stored, retrieved and deleted but it contains no viable data like: title, artist, album, etc.
Altering more data in the entity can be achieved by creating more properties and annotating them with desired type
annotation. 

#### Types
Types are used to tell library how properties should be treated when data is retrieved and/or stored. 
Igni contains 9 built-in types that you can use straight away and can be found in `Igni\Storage\Mapping\Strategy` namespace.
Each of the built-in type also have corresponding annotation that can be found in `Igni\Storage\Mapping\Annotations\Types` namespace.

##### Date
Used to map datetime and date data types.

###### _Accepted attributes:_

`format` string representation of a [valid format](http://php.net/manual/pl/function.date.php) that is being used to store the value

`timezone` string representation of any [valid timezone](http://php.net/manual/pl/timezones.php) that is being used to store the value

`immutable` tells whether the value should be instantiated as `\DateTimeImmutable` or `\DateTime`

```php
<?php declare(strict_types=1);

class Example implements Igni\Storage\Entity
{
    /**
     * @Igni\Storage\Mapping\Annotations\Types\Date(format="Ymd", immutable=true, timezone="UTC")
     */
    private $value;
    
    public function getId(): Igni\Storage\Id 
    {
        //...
    }
}
```

##### Decimal
Decimals are safe way to deal with fragile numerical data like money. [bcmath](http://php.net/manual/pl/book.bc.php) 
extension is required in order to use decimal values.

###### _Accepted attributes:_

`scale` is the number of digits to the right of the decimal point in a number

`precision` is the number of digits in a number

```php
<?php declare(strict_types=1);

class Example implements Igni\Storage\Entity
{
    /**
     * For example we can store the number 12.45 that has a precision of 4 and a scale of 2.
     * @Igni\Storage\Mapping\Annotations\Types\DecimalNumber(scale=2, precision=4)
     */
    private $value;
    
    public function getId(): Igni\Storage\Id 
    {
        //...
    }
}
```

##### Embed
Embed is an object that is not entity itself.

###### _Accepted attributes:_

`scale` is the number of digits to the right of the decimal point in a number

`precision` is the number of digits in a number

```php
<?php declare(strict_types=1);

class Example implements Igni\Storage\Entity
{
    /**
     * For example we can store the number 12.45 that has a precision of 4 and a scale of 2.
     * @Igni\Storage\Mapping\Annotations\Types\DecimalNumber(scale=2, precision=4)
     */
    private $value;
    
    public function getId(): Igni\Storage\Id 
    {
        //...
    }
}
```

##### Enum

##### Float

##### Id

##### Integer

##### Reference

##### Text

#### Working with custom types

##### Defining custom type

##### Registering custom type

### Working with References

### Working with Collections

## Database Drivers

### Available drivers

### Adding custom driver
