# ![Igni logo](https://github.com/igniphp/common/blob/master/logo/full.svg)
[![Build Status](https://travis-ci.org/igniphp/storage.svg?branch=master)](https://travis-ci.org/igniphp/storage)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/igniphp/storage/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/igniphp/storage/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/igniphp/storage/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/igniphp/storage/?branch=master)

## Igni Storage

Igni storage is minimalistic mapping/hydration framework with support for PDO and MongoDB databases with cross database access.

## Table of contents
- [Introduction](#introduction)
  * [Features](#features)
  * [Requirements](#requirements)
  * [Installation](#installation)
  * [Basic Concepts](#basic-concepts)
- [Connecting](#connecting)
  + [Mysql/PgSQL](#mysql-pgsql)
  + [Sqlite](#sqlite)
  + [MongoDB](#mongodb)
  + [Connection Manager](#connection-manager)
- [Mapping](#mapping)
  * [Repositories](#repositories-1)
      - [Defining Repository](#defining-repository)
      - [Registering Repository](#registering-repository)
  * [Entity](#entity)
      - [Defining Entities](#defining-entities)
      - [Types](#types)
        - [Date](#date)
        - [Decimal](#decimal)
        - [Embed](#embed)
        - [Enum](#enum)
        - [Float](#float)
        - [Id](#id)
        - [Integer](#integer)
        - [Text](#text)
        - [Reference](#reference)
  * [Working with custom hydrators](#working-with-custom-hydrators)
  * [Working with custom types](#working-with-custom-types)
  * [Working with Collections](#working-with-collections)
  * [Working with Lazy Collections](#working-with-lazy-collections)

# Introduction

```php
<?php declare(strict_types=1);

use Igni\Storage\Driver\Pdo\Connection;
use Igni\Storage\Storage;
use Igni\Storage\Driver\ConnectionManager;

// Define connection:
ConnectionManager::registerDefault(new Connection('sqlite:/' . __DIR__ . '/db.db'));

// Initialize storage:
$storage = new Storage();
$artist = $storage->get(Artist::class, 1);

// Update artist's name
$artist->name = 'John Lennon';

// Save changes in memory
$storage->persist($artist);

// Commit changes to database
$storage->commit();
```

## Features
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

###### Support for declarative programming
Collection and LazyCollection classes provides interface that supports declarative programming.


## Requirements

 - >= PHP 7.1
 - PDO for mysql, sqlite and/or pgsql support
 - MongoDB extension for mongo support
 
 
## Installation

```
composer install igniphp/storage
```

## Basic Concepts

Igni strongly bases on repository and unit of work patterns. This two patterns are intended to create an abstraction 
layer between the data access layer and the business logic layer of your application. 

The facilitation that is created by UoW makes track of changes and automated unit testing to be achieved in much simpler manner.

#### Unit of Work
Shortly saying UoW maintains a list of objects affected by a business transaction and coordinates the writing out of changes and the resolution of concurrency problems ([source](https://martinfowler.com/eaaCatalog/unitOfWork.html))._

[Entity Storage](src/Storage.php) is responsible for providing UoW implementation.

#### Repositories
Repository is a central place where data is stored and maintained. Igni provides basic implementation per each of the supported drivers:

 - [Pdo Repository](src/Driver/Pdo/Repository.php) 
 - [Mongo Repository](src/Driver/MongoDB/Repository.php)

# Connecting

## Mysql/PgSQL

```php
<?php
use Igni\Storage\Driver\Pdo\Connection;
use Igni\Storage\Driver\Pdo\ConnectionOptions;

new Connection('mysql:host=localhost', new ConnectionOptions(
    $database = 'test',
    $username = 'root',
    $password = 'password'
));
```

## Sqlite

```php
<?php
use Igni\Storage\Driver\Pdo\Connection;

$connection = new Connection('sqlite:/path/to/database');
```

## MongoDB

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

## Connection manager
Connection manager is static class used for registering and obtaining active connections by repositories.
If you would like your repositories to automatically retrieve a connection you have to register one in the
connection manager.

### Registering connection
```php
<?php 
use Igni\Storage\Driver\ConnectionManager;

ConnectionManager::register($name = 'my_connection', $connection);
```

### Checking if connection exists
```php
<?php 
use Igni\Storage\Driver\ConnectionManager;

ConnectionManager::has($name = 'my_connection');// return true
```

### Retrieving connection
```php
<?php 
use Igni\Storage\Driver\ConnectionManager;

ConnectionManager::get($name = 'my_connection');// return true
```

### Releasing connections
```php
<?php 
use Igni\Storage\Driver\ConnectionManager;

// Releases and closes all registered connections
ConnectionManager::release();
```

# Mapping

Mapping tells how data that is stored in database should be reflected in your code.

Library provides following tools to map data:
- Repositories (manages entities and provides access to your entities)
- Cursors (used to process and execute queries in the database)
- Collections (abstraction layer around cursors)
- Hydrators (used to map data from and to database)
- Entities (unit of data, can be single person, place or thing)

## Repositories
Repository is a central place where data is stored and maintained. Repository must implement [Repository interface](src/Repository.php) or
extend one of the provided repository classes, depending which database you are using:

 - [Pdo Repository](src/Driver/Pdo/Repository.php) 
 - [Mongo Repository](src/Driver/MongoDB/Repository.php)

Repositories have to be defined and registered in order to be recognized by unit of work. 

#### Defining Repository
```php
<?php declare(strict_types=1);

use Igni\Storage\Driver\Pdo;
use Igni\Storage\Driver\MongoDB;

// Use pdo repository
class TrackRepository extends Pdo\Repository
{
    public static function getEntityClass(): string 
    {
        return Track::class;
    }
}

// Use mongodb repository
class PlaylistRepository extends MongoDB\Repository
{
    public static function getEntityClass(): string 
    {
        return Playlist::class;
    }
}
```

#### Registering Repository
```php
<?php declare(strict_types=1);
use Igni\Storage\Storage;

// Initialize storage:
$storage = new Storage();

// Add repository
$storage->addRepository(new TrackRepository($storage->getEntityManager()));
```

## Entity
An entity is an object that exists. It can perform various actions and has its own identity. 
An entity can be a single thing, person, place, or object. Entity defines attributes, which keeps information about
what entity needs in order to live. 

#### Defining Entities
Entity must implement `\Igni\Storage\Storable` interface in order to be stored, updated or deleted. 
The interface requires you to define `getId` method.

The simplest entity may look like this:

```php
<?php
use Igni\Storage\Storable;
use Igni\Storage\Id;
use Igni\Storage\Id\Uuid;

class SongEntity implements Storable
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
use Igni\Storage\Storable;
use Igni\Storage\Id;
use Igni\Storage\Id\Uuid;
use Igni\Storage\Mapping\Annotation as Storage;

/**
 * @Storage\Entity(source="albums", connection="default")
 */
class SongEntity implements Storable
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

##### Entity Annotation
Used to register an entity within storage framework

##### _Accepted attributes:_

`source` _(required)_ generally speaking this is name of place where entity is kept in your database (collection, table, etc.)

`hydrator` class name of custom hydrator that should be used during retrieve and persist process

`connection` specify the connection name that should be used by entity's repository 

#### Types
Types are used to tell library how properties should be treated when data is retrieved and/or stored. 
Igni contains 9 built-in types that you can use straight away and can be found in `Igni\Storage\Mapping\Strategy` namespace.
Each of the built-in type also have corresponding annotation that can be found in `Igni\Storage\Mapping\Annotations\Types` namespace.

#### Date
Used to map datetime and date data types.

##### _Accepted attributes:_

`name` keeps equivalent key name stored in database

`format` string representation of a [valid format](http://php.net/manual/pl/function.date.php) that is being used to store the value

`timezone` string representation of any [valid timezone](http://php.net/manual/pl/timezones.php) that is being used to store the value

`immutable` tells whether the value should be instantiated as `\DateTimeImmutable` or `\DateTime`

`readonly` property marked as readonly is ignored during persistence operations

```php
<?php declare(strict_types=1);

class Example implements Igni\Storage\Storable
{
    /**
     * @Igni\Storage\Mapping\Annotation\Property\Date(format="Ymd", immutable=true, timezone="UTC")
     */
    private $value;
    
    public function getId(): Igni\Storage\Id 
    {
        //...
    }
}
```

#### Decimal
Decimals are safe way to deal with fragile numerical data like money. [bcmath](http://php.net/manual/pl/book.bc.php) 
extension is required in order to use decimal values.

##### _Accepted attributes:_

`name` keeps equivalent key name stored in database

`scale` is the number of digits to the right of the decimal point in a number

`precision` is the number of digits in a number

`readonly` property marked as readonly is ignored during persistence operations

```php
<?php declare(strict_types=1);

/** @Igni\Storage\Mapping\Annotation\Entity(source="examples") */
class Example implements Igni\Storage\Storable
{
    /**
     * For example we can store the number 12.45 that has a precision of 4 and a scale of 2.
     * @Igni\Storage\Mapping\Annotation\Property\DecimalNumber(scale=2, precision=4)
     */
    private $value;
    
    public function getId(): Igni\Storage\Id 
    {
        //...
    }
}
```

#### Embed
Embed is an object that is not entity itself but it is composed into the entity. Embeds can be stored in the database as
json or serialized php array.

##### _Accepted attributes:_

`class` _(required)_ contains information about the type of embed object

`name` keeps equivalent key name stored in database

`storeAs` keeps information how data should be stored in the column/property. Can be one of the following values: 
- _plain_
- _json_
- _serialized_

`readonly` property marked as readonly is ignored during persistence operations

```php
<?php declare(strict_types=1);

/** @Igni\Storage\Mapping\Annotation\EmbeddedEntity() */
class Address
{
    /** @var Igni\Storage\Mapping\Annotation\Property\Text() */
    private $street;
    /** @var Igni\Storage\Mapping\Annotation\Property\Text() */
    private $postalCode;
    /** @var Igni\Storage\Mapping\Annotation\Property\Text() */
    private $city;
}

/** @Igni\Storage\Mapping\Annotation\Entity(source="users") */
class User implements Igni\Storage\Storable
{
    /** @var Igni\Storage\Mapping\Annotation\Property\Embed(Address::class, storeAs="json") */
    private $address;
    
    public function getId(): Igni\Storage\Id 
    {
        //...
    }
}
```

    Note: Storing embeds as json in SQL databases can be really usefull, databases like MySQL or PgSQL have good support
    for JSON datatypes.

#### Enum
Enums should be always used when variable can be one out of small set of possible values. It can be used to save storage
space, add additional checks in your code, etc. 


##### _Accepted attributes:_

`values` _(required)_ can be either class that implements `Igni\Storage\Enum` interface or array of values

`name` keeps equivalent key name stored in database
  
`readonly` property marked as readonly is ignored during persistence operations
  
```php
<?php declare(strict_types=1);

class AudioType implements \Igni\Storage\Enum
{
    const MPEG = 0;
    const AAC = 1;
    const MPEG_4 = 2;
    
    private $value;
    
    public function __construct($value)
    {
        $this->value = (int) $value;    
        if (!in_array($this->value, [0, 1, 2])) {
            throw new \InvalidArgumentException('Invalid audio type');
        }
    }
    
    public function getValue(): int
    {
        return $this->value;
    }
}

/** @Igni\Storage\Mapping\Annotation\Entity(source="tracks") */
class Track implements Igni\Storage\Storable
{
    /** @var Igni\Storage\Mapping\Annotation\Property\Enum(AudioType::class) */
    private $audioTypeEnumClass; // This will be instance of AudioType class    
    
    /** @var Igni\Storage\Mapping\Annotation\Property\Enum({"MPEG", "AAC", "MPEG-4"}) */
    private $audioTypeList; // This can be one of the following strings: "MPEG", "AAC", "MPEG-4", but persisted as integer.
    
    public function getId(): Igni\Storage\Id 
    {
        //...
    }
}
```

#### Float
Maps float numbers.

##### _Accepted attributes:_

`name` keeps equivalent key name stored in database

`readonly` property marked as readonly is ignored during persistence operations
  
```php
<?php declare(strict_types=1);

/** @Igni\Storage\Mapping\Annotation\Entity(source="tracks") */
class Track implements Igni\Storage\Storable
{
    /** @var Igni\Storage\Mapping\Annotation\Property\Float(name="audio_length") */
    private $length;  

    public function getId(): Igni\Storage\Id 
    {
        //...
    }
}
```

#### Id
Id is value object that is used to update, remove and retrieve documents by default repository classes.
Once id is set on an object it should not be changed during the runtime. 

If no `class` attribute is specified id by default becomes instance of `Igni\Storage\Id\GenericId`. 
You can map id to your custom class that implements `Igni\Storage\Id` interface.

Igni provides two default implementations for id value object:

- `Igni\Storage\Id\GenericId` 
- `Igni\Storage\Id\Uuid`

`Igni\Storage\Id\GenericId` can be any value, it accepts everything by default and it is not recommended to use it unless
you have no other option.

`Igni\Storage\Id\Uuid` any value passed to the constructor of this class must be valid uuid number. Uuid is kept as 21-22
long varchar value to save the storage space.

##### _Accepted attributes:_

`name` keeps equivalent key name stored in database

`class` once this is set id becomes instance of the given class
  
```php
<?php declare(strict_types=1);

/** @Igni\Storage\Mapping\Annotation\Entity(source="tracks") */
class Track implements Igni\Storage\Storable
{
    /** @var Igni\Storage\Mapping\Annotation\Property\Id(class=Igni\Storage|Id\Uuid::class) */
    private $id;  

    public function getId(): Igni\Storage\Id 
    {
        return $this->id;
    }
}
```

##### Autogenerated ids
The following example shows how to auto-generate ids for your entity.

```php
<?php declare(strict_types=1);

/** @Igni\Storage\Mapping\Annotation\Entity(source="tracks") */
class Track implements Igni\Storage\Storable
{
    use Igni\Storage\Id\AutoGenerateId;
}
```

#### Integer
Maps integer numbers.

##### _Accepted attributes:_

`name` keeps equivalent key name stored in database

`readonly` property marked as readonly is ignored during persistence operations
  
```php
<?php declare(strict_types=1);

/** @Igni\Storage\Mapping\Annotation\Entity(source="tracks") */
class Track implements Igni\Storage\Storable
{
    /** @var Igni\Storage\Mapping\Annotation\Property\IntegerNumber() */
    private $length;  

    public function getId(): Igni\Storage\Id 
    {
        //...
    }
}
```

#### Text

##### _Accepted attributes:_

`name` keeps equivalent key name stored in database

`readonly` property marked as readonly is ignored during persistence operations
  
```php
<?php declare(strict_types=1);

/** @Igni\Storage\Mapping\Annotation\Entity(source="tracks") */
class Track implements Igni\Storage\Storable
{
    /** @var Igni\Storage\Mapping\Annotation\Property\Text() */
    private $lyrics;  

    public function getId(): Igni\Storage\Id 
    {
        //...
    }
}
```

#### Reference
References are properties that stores ids to other entities in your data layer. Storage framework resolves them
automatically on hydration phase.

##### _Accepted attributes:_

`target` _(required)_ FQCN of the entity that is stored as reference in the property 

`name` keeps equivalent key name stored in database

`readonly` property marked as readonly is ignored during persistence operations
 
```php
<?php declare(strict_types=1);

/** @Igni\Storage\Mapping\Annotation\Entity(source="tracks") */
class Track implements Igni\Storage\Storable
{
    /** @var Igni\Storage\Mapping\Annotation\Property\Reference(target=Album::class) */
    private $album;  

    public function getId(): Igni\Storage\Id 
    {
        //...
    }
    
    public function getAlbum(): Album
    {
        return $this->album;
    }
}
```

If entity has to store collection of references it is recommended to create custom hydrator.


## Working with custom hydrators 

Auto-resolving complex schema is memory and cpu consuming and in most cases not sufficient enough. 
At the time like this it is good to have set of tools that will support you in building application layer
where you have total control what is happening on your database layer.
Storage framework was build to provide this kind set of tools, one of them is possibility to define and use 
custom hydrators to help you out with reflecting database schema in your application code.

Custom hydrator is a decorator for hydrator generated by [`Igni\Storage\Hydration\HydratorFactory`](src/Hydration/HydratorFactory.php) 
and must implement [`\Igni\Storage\Hydration\ObjectHydrator`](src/Hydration/ObjectHydrator.php) interface.

The following code is the simplest implementation of custom hydrator:

```php
<?php
class CustomTrackHydrator implements Igni\Storage\Hydration\ObjectHydrator
{
    private $baseHydrator;
    
    public function __construct(Igni\Storage\Hydration\GenericHydrator $baseHydrator) 
    {
        $this->baseHydrator = $baseHydrator;    
    }
    
    public function hydrate(array $data) 
    {
        $entity = $this->baseHydrator->hydrate($data);
        // Modify entity to your needs
        return $entity;
    }
    public function extract($entity): array 
    {
        $extracted = $this->baseHydrator->extract($entity);
        // Modify the data before storing it in database.
        return $extracted;
    }
}
```

Storage framework can recognize custom-defined hydrator once it is set in the [`@Entity`](src/Mapping/Annotation/Entity.php) annotation.

With custom hydrators you can define your own many-to-many and one-to-many relation handling and more.  

```php
<?php

/**
 * @Igni\Storage\Mapping\Annotation\Entity(source="tracks", hydrator=CustomTrackHydrator::class)
 */
class TrackEntity implements Igni\Storage\Storable
{
    public function getId(): Igni\Storage\Id 
    {
        // ...
    }
}
```

For full example please visit [examples directory](examples).

## Working with custom types

Storage provides useful set of daily-basis types like: int, decimal float or reference. If you find you lack some
type that will fulfill your needs there is easy way to define your own custom data-type.

There are two steps required to create custom data-type:
- Create class responsible for data mapping (from and to database)
- Registering defined type

Mapping strategy class must implement [`Igni\Storage\Mapping\MappingStrategy`](src/Mapping/MappingStrategy.php) interface:

```php
<?php
final class MyType implements Igni\Storage\Mapping\MappingStrategy
{
    public static function hydrate(&$value) 
    {
        // Here format data that will be used in the code-land
    }
    
    public static function extract(&$value) 
    {
        // Here format data that will be persisted to database
    }
}

```

For full example please visit [examples directory](examples).

## Working with Collections

Collection is a container that groups multiple elements into a single unit. Storage framework contains collection implementation
that allows to easier represent or manipulate data that are provided by cursor or any other Iterable instance.
 
Storage framework's collection supports declarative programming, it has support for map/reduce operations.
 
### Instantiating new collection
Collection can be instantiated with any iterable including cursor.
```php
<?php
use Igni\Storage\Driver\ConnectionManager;
use Igni\Storage\Mapping\Collection\Collection;

// From iterable
$connection = ConnectionManager::getDefault();
$collection = new Collection($connection->execute('SELECT *FROM artists'));

// From list of items
$numbers = Collection::fromList(1, 2, 3);
```

### Adding new item to a collection
```php
<?php
use Igni\Storage\Mapping\Collection\Collection;

$collection = new Collection();
$withNewItem = $collection->add(1);
$withMultipleItems = $collection->addMany(1, 2, 3);
```

### Removing item from collection
```php
<?php
use Igni\Storage\Mapping\Collection\Collection;

$collection = new Collection([1, 2, 3, 4]);

$collectionWithoutItem = $collection->remove(2);

$collectionWithoutManyItems = $collection->removeMany(1, 4);
```

### Iterating through collection
```php
<?php
use Igni\Storage\Driver\ConnectionManager;
use Igni\Storage\Mapping\Collection\Collection;

$connection = ConnectionManager::getDefault();
$collection = new Collection($connection->execute('SELECT *FROM artists'));

// Imperative approach.
$mappedData = new Collection();
foreach ($collection as $item) {
    $item['age'] = 20;
    $mappedData = $mappedData->add($item);
}

// Declarative approach
$mappedData = $collection->map(function ($item) {
    $item['age'] = 20;
    return $item;
});
```

### Sorting/Reversing items in collection
```php
<?php
use Igni\Storage\Driver\ConnectionManager;
use Igni\Storage\Mapping\Collection\Collection;

$connection = ConnectionManager::getDefault();
$collection = new Collection($connection->execute('SELECT *FROM artists'));

// Sort by age
$sorted = $collection->sort(function(array $current, array $next) {
    return $current['age'] <=> $next['age'];
});

// Reverse
$reversed = $sorted->reverse(); 
```

### Checking if collection contains an element
```php
<?php
use Igni\Storage\Driver\ConnectionManager;
use Igni\Storage\Mapping\Collection\Collection;

$connection = ConnectionManager::getDefault();
$collection = new Collection($connection->execute('SELECT name, age FROM artists'));


if ($collection->contains(['name' => 'Bob', 'age' => 20])) {
    // There is Bob in the collection
}
```

### Searching items in collection
```php
<?php
use Igni\Storage\Driver\ConnectionManager;
use Igni\Storage\Mapping\Collection\Collection;

$connection = ConnectionManager::getDefault();
$collection = new Collection($connection->execute('SELECT *FROM artists'));

// Age greater than 50
$elders = $collection->where(function(array $artist) {
    return $artist['age'] > 50;
});
```

### Checking if any item in a collection fulfils given requirement 
```php
<?php
use Igni\Storage\Driver\ConnectionManager;
use Igni\Storage\Mapping\Collection\Collection;

$connection = ConnectionManager::getDefault();
$collection = new Collection($connection->execute('SELECT *FROM artists'));

if ($collection->any(function($artist) { return $artist['age'] > 70; })) {
    // There is at least one artist who is over 70 yo
}
```

### Checking if every item in a collection fulfils given requirement 
```php
<?php
use Igni\Storage\Driver\ConnectionManager;
use Igni\Storage\Mapping\Collection\Collection;

$connection = ConnectionManager::getDefault();
$collection = new Collection($connection->execute('SELECT *FROM artists'));

if ($collection->every(function($artist) { return $artist['age'] > 2; })) {
    // All artists are above 2 yo
}
```


### Reducing collection to a single value
```php
<?php
use Igni\Storage\Driver\ConnectionManager;
use Igni\Storage\Mapping\Collection\Collection;

$connection = ConnectionManager::getDefault();
$collection = new Collection($connection->execute('SELECT *FROM artists'));

$totalAge = $collection->reduce(
    function(int $total, array $artist) {
        return $total + $artist['age'];
    }, 
    $initialValue = 0
);
```

## Working with Lazy collections

Lazy collection are immutable lazy bastards, they do nothing all the day but iterate through cursor
in the way where item is not fetched from database until it is really needed (such lazy, wow!).
(If you reached this point of documentation take my congratulations :D)

Lazy collection was specially made to work with cursors so it accepts only cursors:
```php
<?php
use Igni\Storage\Driver\ConnectionManager;
use Igni\Storage\Mapping\Collection\LazyCollection;

$connection = ConnectionManager::getDefault();

$lazyBastard = new LazyCollection($connection->execute('SELECT *FROM artists'));

// Iterating
foreach ($lazyBastard as $item) {
    // Do something here
}

// You have changed your mind and get fed with laziness- no probs:
$nonLazy = $lazyBastard->toCollection();
```

##### That's all folks!
