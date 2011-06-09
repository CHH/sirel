Sirel -- Simple Relation
========================

Sirel is a representation of the Relational Algebra (think SQL) in PHP. Its largely inspired
by Rails' Arel (https://github.com/rails/arel). Sirel uses new features of PHP 5.3, such as Namespaces,
therefore *PHP 5.3* is *required*.

Now let's dive into a bird's eye overview of Sirel's API.

## Relations

The core of Sirel's Query Building API is the _Table_. The Table Object provides convenient
access to attributes and Factory Methods for Query Managers (think SQL Statements 
Select, Insert, Update, Delete). The constructor takes one argument: the Table Name.

```php
<?php
$users = new \Sirel\Table("users");
```

Additionally the Table Object can be accessed as Array, to retrieve an Object 
representation of an Attribute.

```php
<?php
...
echo $users['username'];
// -> users.username
```

This Attribute object also includes Factory Methods for the most common 
Restrictions, for example `gt`, `eq`, `lt`,...

## Selections

Selections are done in Sirel with the `where()` operator. The `where`
operator takes one or more expressions as argument, which get joined
by an AND.

```php
<?php
use Sirel\Table;

$users = new Table("users");
echo $users->where($users['username']->eq("johnny"), $users['password']->eq('superSecretPass'));
// -> SELECT * FROM users WHERE users.username = 'johnny' AND users.password = 'superSecretPass'
```

## Ordering

Ordering is done with the `order()` Operator. It receives either an Order Expression
or an Attribute Name and Ordering Direction (`\Sirel\Node\Order::ASC` or `\Sirel\Node\Order::DESC`).
The Attribute Objects provide `asc()` and `desc()` Methods for convenience, which return
the right Order Objects.

```php
<?php
$users = new \Sirel\Table("users");
echo $users->order($users['username']->desc());
// -> SELECT * FROM users ORDER BY users.username DESC
```

## Limit & Offset

Limit and Offset correspond to the `take()` and `skip()` Operators. 

```php
<?php
$users = new \Sirel\Table("users");
echo $users->take(5);
// -> SELECT * FROM users LIMIT 5

echo $users->skip(4);
// -> SELECT * FROM users OFFSET 4
```

