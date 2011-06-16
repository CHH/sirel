Sirel -- Simple Relation
========================

Sirel is a representation of the Relational Algebra (think SQL) in PHP. Its largely inspired
by Rails' Arel (https://github.com/rails/arel). Sirel uses Namespaces therefore PHP 5.3 is *required*.

Sirel is under _heavy_ Development and so the following shortcomings and bugs
still have to be ironed out:

 * Select Manager has _no_ API for Joins
 * Insert Manager is not implemented
 * Delete Manager is not implemented
 * _No_ Specific Attribute Visitors (Boolean, Decimal,...)
 * _No_ DBMS-specific Visitors
 * Quoting is really dumb

Now let's dive into a bird's eye overview of the things that work ;-).

## Relations

The core of Sirel's Query Building API is the _Table_. The Table Object provides convenient
access to attributes and Factory Methods for Query Managers (think SQL Statements 
Select, Insert, Update or Delete).

These are:

 * `from`, `project`, `where`, `òrder`, `group`, `take` or `skip` _to get a Select Manager_
 * `insert` _to get an Insert Manager_
 * `update` _to get an Update Manager_
 * and `delete` _to get a Delete Manager_

The constructor takes one argument: the Table Name.

```php
<?php
$users = new \Sirel\Table("users");
```

Additionally the Table Object can be accessed as Array, to retrieve an Object 
representation of an Attribute. If no attribute is defined, then it returns
an instance of `Sirel\Attribute\Attribute`.

```php
<?php
...
echo $users['username'];
// -> users.username

// If you don't like accessing arrays:
assert($users['username'] === $users->username);
```

### Strong Typed Attributes

The Table Object can also be initialized with a set of stronger typed attributes
to define the Table's Scheme.
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
created by an attribute.

Restrictions can be produced by an attribute object. Following
Methods are supported (which correspond their SQL buddies):

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
or an Attribute Name and Direction (`\Sirel\Node\Order::ASC` or `\Sirel\Node\Order::DESC`).
Additionally an Attribute provides `asc()` and `desc()` methods for creating Order Expressions.

```php
<?php
...
echo $users->order($users['username']->asc());
// -> SELECT * FROM users ORDER BY users.username ASC

echo $users->order($users['username']->desc());
// -> SELECT * FROM users ORDER BY users.username DESC
```

## Limit & Offset

Limit and Offset correspond to the `take` and `skip` Operators. 

```php
<?php
...
echo $users->take(5);
// -> SELECT * FROM users LIMIT 5

echo $users->skip(4);
// -> SELECT * FROM users OFFSET 4
```
