Sirel -- A Simple Relational Algebra for PHP
============================================

Sirel is a representation of the SQL Abstract Syntax Tree in PHP. 

Sirel aims to be to PHP what [Arel](https://github.com/rails/arel) is for Ruby.

## Install

Requirements:

* PHP 5.4
* Optional: `doctrine/dbal` for wider Database support

Install with [Composer][]:

    php composer.phar require 'chh/sirel:1.0.*@dev'

Then require `vendor/autoload.php` in your app.

[Composer]: http://getcomposer.org

* * *

Sirel is under __heavy__ Development and so the following shortcomings and bugs
still have to be ironed out:

 * Only tested with __SQLite__, most other DBMS implement a superset of 
   SQLite's SQL, so may also work with MySQL though.
 * __No__ Generation of DBMS-specific SQL (will likely come as 
   Doctrine DBAL-enabled Visitor)
 * Only DML (use [Doctrine DBAL][] if you want to run database
   independent DDL)

Now let's dive into a bird's eye overview of the things that work ;-).

[Doctrine DBAL]: http://github.com/doctrine/dbal

## Frequently Asked Questions

> How do I protect myself from SQL injection when using Sirel?

I recommend using Prepared Statements. Make sure you mark
placeholders as raw SQL with `Sirel::sql()`.

Example using PDO:

```php
<?php

use Sirel\Sirel;
use Sirel\Table;

$users = $u = new Table("users");
$select = $users->take(1)->where($u->id->eq(Sirel::sql(':id')));

$connSpec = "…";
$pdo = new \PDO($connSpec);

$stmt = $pdo->prepare($select->toSql());
$stmt->bindParam(':id', $_GET['id']);
$stmt->execute();

$user = $stmt->fetch();
```

## Relations

The core of Sirel's Query Building API is the __Table__. The Table Object provides convenient
access to attributes and Factory Methods for Queries.

You may call these methods on the Table Instance to start building a new query:

 * `from`, `project`, `join`, `where`, `order`, `group`, `take` or `skip` to start building a __Select Query__
 * `insert` to start building an __Insert Query__
 * `update` to start building an __Update Query__
 * and `delete` to start building a __Delete Query__

The constructor takes one argument: the Table Name.

```php
<?php
use Sirel\Table;

$users = new Table("users");
```

The Table Instance can additionally be accessed like an Array to get an Instance of an Attribute.
If no attribute is defined, then it returns a new instance of `Sirel\Attribute\Attribute`.

```php
<?php
...
echo $users['username'];
// -> users.username

// If you don't like accessing arrays:
assert($users['username'] === $users->username);
```

## Joins

Joins look very similar to their SQL Counterparts. Joins are started by the `join` operator. The Join's
`ON` Expression is then set with the next call to `on`. The call to `on` expects one or more Expressions.

Example:

```php
<?php
use Sirel\Table;

$profiles = new Table("profiles");

echo $profiles->join($users)->on($profiles['user_id']->eq($users['id']));
// -> SELECT * FROM profiles INNER JOIN users ON profiles.user_id = users.id
```

Left Joins can be created with the `leftJoin` operator.

Example:

```php
<?php
use Sirel\Table;

$profiles = new Table("profiles");

echo $profiles->leftJoin($users)->on($profiles['user_id']->eq($users['id']));
// -> SELECT * FROM profiles LEFT JOIN users ON profiles.user_id = users.id
```

## Selections

Selections are done in Sirel with the `where` operator. The `where`
operator takes one or more expressions as argument, which can be
created by an attribute. These expressions are then joined by the "AND" Operator.

Restrictions are created by calling the respective method on an attribute. The following
restrictions are supported (which each correspond to their SQL equivalents):

 * eq
 * notEq
 * gt
 * gte
 * lt
 * lte
 * like
 * notLike
 * in
 * notIn

Examples:

```php
<?php
use Sirel\Table;

$users = new Table("users");

echo $users->where($users['username']->eq("johnny"), $users['password']->eq('superSecretPass'));
// -> SELECT * FROM users WHERE users.username = 'johnny' AND users.password = 'superSecretPass'

echo $users->where($users['username']->like('a%'));
// -> SELECT * FROM users WHERE users.username LIKE 'a%'

echo $users->where($users['id']->in([3, 4, 10]));
// -> SELECT * FROM users WHERE users.id IN (3, 4, 10)
```

## Ordering

Ordering is done with the `order` Operator. It receives either an Order Expression
or a combination of an Attribute Name and a Direction (`\Sirel\Node\Order::ASC` or `\Sirel\Node\Order::DESC`).
Additionally Attribute Instances provide `asc` and `desc` methods for creating Order Expressions.

```php
<?php
...
echo $users->order($users['username']->asc());
// -> SELECT * FROM users ORDER BY users.username ASC

echo $users->order($users['username']->desc());
// -> SELECT * FROM users ORDER BY users.username DESC

echo $users->order($users['username'], \Sirel\Node\Order::DESC);
// -> SELECT * FROM users ORDER BY users.username DESC
```

Use `reorder` to clear all current order operations:

```php
<?php

$select = $users->order($users->username->asc())->order($users->id->asc());

// Not let's reorder:
echo $select->reorder($users->id->desc());
// -> SELECT * FROM users ORDER BY users.id DESC;
```

You can reverse the existing order with `->reverseOrder()`:

```php
<?php

$select = $users->order($users->username->asc());

echo $select->reverseOrder();
// -> SELECT * FROM users ORDER BY users.username DESC;
```

Reverse only the order of some attributes by passing a list of attributes
to `->reverseOrder()`:

```php
<?php

$select = $users->order($users->username->asc())->order($users->id->desc());

echo $select->reverseOrder([$users->id]);
// -> SELECT * FROM users ORDER BY users.username ASC, users.id ASC;
```

## Limit & Offset

Limit and Offset correspond to the `take` and `skip` Operators. These take the amount of rows as their sole argument.

```php
<?php
...
echo $users->take(5);
// -> SELECT * FROM users LIMIT 5

echo $users->skip(4);
// -> SELECT * FROM users OFFSET 4
```

## Chaining

The greatest benefit of using a Query Builder is the composability of queries. Therefore
calls to the Manager's methods are not bound to any order and can be chained infinitely.

For Example:

```php
<?php
...
$query = $users->project($users['id']);

$query->take(1)->where($users['username']->eq("johnny"))->where($users['password']->eq('foo'));

$query->project($users['username']);

echo $query;
// -> SELECT users.id, users.username FROM users WHERE users.username = 'johnny' AND users.password = 'foo' LIMIT 1
```

## Insert

Get an insert query manager by calling `->insert()` on the table, or
constructing a new `Sirel\InsertManager`:

```php
<?php

$insert = $users->insert();
// is equivalent to
$insert = new Sirel\InsertManager;
$insert->into($users);
```

You can set a list of column-value pairs with the `->values()` method:

```php
<?php

$insert->values([
    'username' => 'jon',
    'first_name' => 'John',
    'last_name' => 'Doe'
]);

echo $insert->toSql();
// -> INSERT INTO users (users.username, users.first_name, users.last_name) VALUES ('jon', 'John', 'Doe');
```

## Update

Update queries can be created with the table's `->update()` method.
Values can be set with the `->set()` method.

Update queries feature most of the methods of Select queries, which work the same as their Select counterparts:

* `where`
* `take`
* `skip`
* `order`

__Note:__ If you create an `Sirel\UpdateManager` instance, you need to set the
table with the `->table()` method.

```php
<?php

$update = $users->update();
$update->where($users->id->eq(1))->set('last_name' => 'Foobar');

echo $update->toSql();
// -> UPDATE users SET users.last_name = 'Foobar' WHERE users.id = '1';
```

You can also compile an Update query from an existing Select query with
`->compileUpdate()`:

```php
<?php

$select = $users->where($users->first_name->eq("James"))
    ->where($users->last_name->eq("Kirk"))
    ->take(1);

$update = $select->compileUpdate()->set(['first_name' => 'Jim']);

echo $update->toSql();
// -> UPDATE users SET users.first_name = 'Jim' WHERE users.first_name = 'James' AND users.last_name = 'Kirk' LIMIT 1;
```

## Delete

Delete queries can be created by calling the table's `->delete()`
method.

Delete queries understand these operations, which work exactly the same
as their Select counterparts:

* `from`
* `where` 
* `take`
* `skip`
* `order`

```php
<?php

$delete = $users->delete()
    ->take(1)
    ->where($users->id->eq(1));

echo $delete->toSql();
// -> DELETE FROM users WHERE users.id = '1' LIMIT 1;
```

You can also compile an existing Select query to a Delete query:

```php
<?php

$select = $users->where($users->activated->eq(0))->take(10);

$delete = $select->compileDelete();

echo $delete->toSql();
// -> DELETE FROM users WHERE users.activated = '0' LIMIT 10;
```
