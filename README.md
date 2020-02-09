# ancarda/pdoplus

_PDO with some extra niceties_

[![Latest Stable Version](https://poser.pugx.org/ancarda/pdoplus/v/stable)](https://packagist.org/packages/ancarda/pdoplus)
[![Total Downloads](https://poser.pugx.org/ancarda/pdoplus/downloads)](https://packagist.org/packages/ancarda/pdoplus)
[![License](https://poser.pugx.org/ancarda/pdoplus/license)](https://choosealicense.com/licenses/mit/)
[![Build Status](https://travis-ci.com/ancarda/pdoplus.svg?branch=master)](https://travis-ci.com/ancarda/pdoplus)
[![Coverage Status](https://coveralls.io/repos/github/ancarda/pdoplus/badge.svg?branch=master)](https://coveralls.io/github/ancarda/pdoplus?branch=master)

To install, simply run

    composer require ancarda/pdo-plus

## Usage

Wrap an existing `PDO` object like so:

```php
$pdo     = new PDO(...);
$pdoPlus = new PDOPlus($pdo);
```

PDO Plus will set two attributes on your connection:

 * Use `FETCH_ASSOC` to return associative arrays.
 * Use `ERRMODE_EXCEPTION` to throw `PDOException` from methods.

From there, you can use the `query` method which takes the SQL query as the
first parameter and the prepared statement params as the second parameter.
Query returns multiple rowsets for use with SQL Server and MySQL.

For everything else, you can use `getPDO`.

### Transactions

For an RAII style of transactions, you can call `createTransaction`.

For a more functional approach, use `tryTransaction` which wraps the
transaction in a closure.
