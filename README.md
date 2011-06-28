Sirel -- A Simple Relational Algebra for PHP
============================================

Sirel is a representation of the Relational Algebra (think SQL) in PHP. 
Sirel aims to be to PHP what [Arel](https://github.com/rails/arel) is for Ruby.
Sirel uses __Namespaces__ and follows the 
[PSR-0 Standard](http://groups.google.com/group/php-standards/web/psr-0-final-proposal?pli=1), therefore at least **PHP 5.3** is **required**.

* * *

Sirel is under __heavy__ Development and so the following shortcomings and bugs
still have to be ironed out:

 * Only tested with __SQLite__
 * __No__ Generation of DBMS-specific SQL (will likely come as 
   `Doctrine\DBAL`-enabled Visitor)
 * Only __Inner__ Joins for now
 * Quoting isn't that smart

Now let's dive into a bird's eye overview of the things that work ;-).

## Relations

The core of Sirel's Query Building API is the __Table__. The Table Object provides convenient
access to attributes and Factory Methods for Queries.

You may call this methods on the Table Instance to start building a new query:

 * `from`, `project`, `join`, `where`, `order`, `group`, `take` or `skip` to start building a __Select Query__
 * `insert` to start building an __Insert Query__
 * `update` to start building an __Update Query__
 * and `delete` to start building a __Delete Query__

The constructor takes one argument: the Table Name.

```php
<?php
$users = new \Sirel\Table("users");
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

$profiles = new Table("profiles");

echo $profiles->join($users)->on($profiles['user_id']->eq($users['id']));
// -> SELECT * FROM profiles INNER JOIN users ON profiles.user_id = users.id
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
...
echo $users->where($users['username']->eq("johnny"), $users['password']->eq('superSecretPass'));
// -> SELECT * FROM users WHERE users.username = 'johnny' AND users.password = 'superSecretPass'

echo $users->where($users['username']->like('a%'));
// -> SELECT * FROM users WHERE users.username LIKE 'a%'

echo $users->where($users['id']->in(array(3, 4, 10)));
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

## Limit & Offset

Limit and Offset correspond to the `take` and `skip` Operators. These take the amount of rows
as their sole argument.

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

## Advanced Features

### Strong Typed Attributes

The Table Object can also be initialized with a set of stronger typed attributes to 
define the Table's Scheme.
This is done by calling `addAttribute` with an Instance of the desired Attribute. 

Sirel provides these Attribute Types:

 * BooleanAttribute
 * DecimalAttribute
 * FloatAttribute
 * IntegerAttribute
 * StringAttribute
 * TimeAttribute
 * UndefinedAttribute

```php
<?php
...
$users
    ->addAttribute(new \Sirel\Attribute\IntegerAttribute("id"))
    ->addAttribute(new \Sirel\Attribute\StringAttribute("username"))
    ->addAttribute(new \Sirel\Attribute\StringAttribute("password"));
```

In Addition to that, you will want to turn the "Strict Scheme" Mode of
the Table on, to throw an Exception if an Attribute was not defined prior
to accessing it.

```php
<?php

$users->setStrictScheme(true);

$users['birth_date'];
// This will throw an UnexpectedValueException, because 'birth_date' was
// not defined beforehand
```

