Sirel -- Simple Relation
========================

Sirel is a representation of the Relational Algebra (think SQL) in PHP. Its largely inspired
by Rails' Arel (https://github.com/rails/arel). Sirel uses Namespaces, therefore at least **PHP 5.3** is **required**.

Sirel is under __heavy__ Development and so the following shortcomings and bugs
still have to be ironed out:

 * Select Manager has __no__ API for Joins
 * Insert Manager is not implemented
 * Delete Manager is not implemented
 * __No__ Specific Attribute Visitors (Boolean, Decimal,...)
 * __No__ DBMS-specific Visitors
 * Quoting is really dumb

Now let's dive into a bird's eye overview of the things that work ;-).

## Relations

The core of Sirel's Query Building API is the __Table__. The Table Object provides convenient
access to attributes and Factory Methods for Query Managers (think SQL Statements 
Select, Insert, Update or Delete).

These are:

 * `from`, `project`, `where`, `order`, `group`, `take` or `skip` to get a __Select Manager__
 * `insert` to get an __Insert Manager__
 * `update` to get an __Update Manager__
 * and `delete` to get a __Delete Manager__

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

```php
<?php
...
echo $users->where($users['username']->eq("johnny"), $users['password']->eq('superSecretPass'));
// -> SELECT * FROM users WHERE users.username = 'johnny' AND users.password = 'superSecretPass'
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

The greatest benefit of using a Query Builder is the composability of the queries themselfs. Therefore
calls to the Manager's methods are not bound to any order and can be changed infinitely.

For Example:

```php
<?php
...
$query = $users->project($users['id']);

$query->take(1)->where($users['username']->eq("johnny"))->where($users['password']->eq('foo'));

$query->project($users['username']);

echo $query;
// -> SELECT users.id, users.username FROM users WHERE users.username='johnny' AND users.password='foo' LIMIT 1
```
