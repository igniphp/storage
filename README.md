# ![Igni logo](https://github.com/igniphp/common/blob/master/logo/full.svg)![Build Status](https://travis-ci.org/igniphp/storage.svg?branch=master)

## Igni Storage
Licensed under MIT License.

## Installation

```
composer install igniphp/storage
```

## Introduction

Igni/storage is minimalistic mapping/hydration library with support for PDO and MongoDB databases, and cross database
access. 
Why considering this libraries among others, here are couple reasons:

- Use native query language
- No learning curve for custom/business languages
- Support for multiple different databases
- Support for crossing data nosql (supports only mongodb for now) with sql databases

## Requirements

 - PHP 7.1
 - PDO for mysql, sqlite and/or pgsql support
 - MongoDB extenstion for mongo support

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
Mapping tells how data taken from database should be interpreted in your code.

### Schema

### Repositories
