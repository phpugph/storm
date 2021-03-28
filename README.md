# Storm
PHP8 SCM SQL Client

[![Travis CI](https://travis-ci.org/phpugph/Storm.svg?branch=master)](https://travis-ci.org/phpugph/storm)
[![Coverage Status](https://coveralls.io/repos/github/phpugph/Storm/badge.svg?branch=master)](https://coveralls.io/github/phpugph/storm?branch=master)
[![Latest Stable Version](https://poser.pugx.org/phpugph/storm/v/stable)](https://packagist.org/packages/phpugph/storm)
[![Total Downloads](https://poser.pugx.org/phpugph/storm/downloads)](https://packagist.org/packages/phpugph/storm)
[![Latest Unstable Version](https://poser.pugx.org/phpugph/storm/v/unstable)](https://packagist.org/packages/phpugph/storm)
[![License](https://poser.pugx.org/phpugph/storm/license)](https://packagist.org/packages/phpugph/storm)

- [Install](#install)
- [Introduction](#intro)
- [Basic Querying](#basic)
- [Data Manipulation](#manipulation)
- [Searching](#searching)
- [Model](#model)
- [Collection](#collection)
- [Putting it Together](#together)
- [Contributing](#contributing)

----

<a name="install"></a>
## Install

`composer install phpugph/storm`

----

<a name="intro"></a>
## Introduction

Welcome to Storm, a different kind of ORM. Complete functionality from low level queries to Search, Collections and Models. Designed for novices and enthusiasts alike.

**Figure 1. Usage**
```php
$resource = new PDO('mysql:host=127.0.0.1;dbname=test_db', 'root', '');
$database = new Storm\SqlFactory($resource);
```

This pattern allows Storm to work easier with other ORM platforms. `SqlFactory` will make a determination of what database class to use. Currently MySQL, MariaDB, PostGreSQL and SQLite are supported (Create an issue if you would like to see another SQL type database supported).

----

<a name="basic"></a>
## Basic Querying

On a very low level can call raw queries as in Figure 2.

**Figure 2. Raw Query**

```php
$database->query('SELECT * FROM user');  // returns results of raw queries
```

It's recommended you bind incoming variables contributed by the end user. Still on a low level, this can be acheived as in `Figure 3`.

**Figure 3. Raw Binding**

```php
$query  = 'SELECT * FROM user WHERE user_name LIKE :user_name AND user_active = :user_active';
$bind   = array(':user_name' => '%'.$userName.'%', ':user_active' => 1);

$database->query($query, $bind); // returns results of raw queries
```

The above figure sets `$query` to a string with bound place holders `:user_name` and `:user_active`. `$bind` has the actual values these placeholders and should be replaced with during execution of the query. We encourage this method because binding values prevents database injections.

> **Note:** Bound variables must start with a colon(:).

----

<a name="manipulation"></a>
## Data Manipulation

If you prefer the wrapper way to save data Figure 4 provides several method examples on how to achieve this.

Figure 4. Data Manipulation

```php
$settings = [
    'user_name'     => 'Chris'
    'user_email'    => 'myemail@mail.com'
];

$filter[] = ['user_id=%s', 1];     

// inserts row into 'user' table
$database->insertRow('user', $settings);
// updates rows in 'user' table where user_id is
$database->updateRows('user', $settings, $filter);
// delete rows in 'user' table where user_id is 1
$database->deleteRows('user', $filter);      
```

Inserting data is pretty trivial. We included 2 ways to insert data. Like getRow(), there's no need to worry about bound data because Storm will do this for you. Figure 4 shows the 2 kind of inserts mentioned.

**Figure 4. Two ways to insert**

```php
$settings = ['user_name' => 'Chris', 'user_email' => 'myemail@mail.com'];
// insert row into 'user' table
$database->insertRow('user', $settings);

$settings = [];
$settings[] = ['user_name' => 'Chris', 'user_email' => 'myemail@mail.com'];
$settings[] = ['user_name' => 'Dan', 'user_email' => 'myemail2@mail.com'];
$settings[] = ['user_name' => 'Clark', 'user_email' => 'myemail3@mail.com'];
// insert multiple rows into 'user' table
$database->insertRows('user', $settings);
```

So obviously `insertRow()` should be used if you just want to insert one row. Inserting two or more rows at the same time, you should use `insertRows()`. This method expects an array of arrays, or an array table.

> **Note:** A common error is using `insertRows()` instead of `insertRow()`.

> **Note:** Using models and collections, you don't really need to worry about this method because it's covered in the `save()` method in a collection or model object. We'll go over models and collections later in this section.

Updating is about as easy as inserting. There's only one method you need to know.

**Figure 5. Updating**

```php
$settings = ['user_name' => 'Chris', 'user_email' => 'myemail@mail.com'];
$filter[] = ['user_id=%s', 1];
// update row into 'user' table
$database->updateRows('user', $settings, $filter);
```

A common scenario is when you need to insert if a column value is not found and update if it is. We added an extra method called `setRow()` to simply to save you some lines of redundancy.

**Figure 6. Insert or update**

```php
$settings = ['user_name' => 'Chris2', 'user_email' => 'myemail@mail.com'];
$database->setRow('user', 'user_email', 'myemail@mail.com', $settings);
```

`Figure 6` basically says, in user table, if `myemail@mail.com` exists in the `user_email` column, then update that row. If not then insert. Removing data is simple enough as well.

**Figure 7. Remove**

```php
$filter[] = ['user_id=%s', 1];
// delete rows in 'user' table where user_id is 1
$database->deleteRows('user', $filter);
```

----

<a name="searching"></a>
## Searching

A better way to build complex queries is with using the search object. An overview example can be found in `Figure 8`.

**Figure 8. MySQL Search**

```php
$database
    ->search('user')
    ->setColumns('*')
    ->innerJoinOn('group', 'group_owner=user_id')
    ->leftJoinUsing('friends', 'user_id')
    ->filterByUserName('Chris')
    ->addFilter("user_last LIKE '%s%%'", 'Brown')
    ->sortByUserId('ASC')
    ->addSort('user_last', 'DESC')
    ->setRange(25)
    ->setStart(75)
    ->getRows();
```

In the figure above there's a few methods being powered with magic, but we'll just start going down the line. First off, to instantiate the search object you simply need to call `search()` passing the name of the table as the argument. Secondly we call `setColumns()`. This call is optional, but if used, can either accept an array of columns or an argument separated list of columns, ie. `setColumns('user_id', 'user_name')`. Next, `innerJoinOn()` is the new way we accept joins. There are eight methods dedicated to different kinds of joins.

**Kinds of Join methods**

```php
innerJoinOn()
innerJoinUsing()
leftJoinOn()
leftJoinUsing()
rightJoinOn()
rightJoinUsing()
outerJoinOn()
outerJoinUsing()
```

No matter what methods you choose from above there are two arguments you need to add. The first argument is the name of the table you would like to join and the second one is the how they relate to each other.

The first magic powered method is called `filterByUserName()`. There is no actual method called `filterByUserName()` in the library. Instead when this function is called it will parse out the name of the method and recognize that UserName is the name of a column and convert that into `addFilter('user_name=%s', 'Chris')` as in `Figure 8`.

`addFilter()` generally accepts two arguments. The first argument is the filter clause. If you notice in our filter example in `Figure 8` we use %s to delimit a binded value. You can have as many bound values per filter as you like. The following arguments need to include the bound values in order of when they occur in the filter clause.

The second magic powered method is called `sortByUserId('ASC')`.There is no actual method called `sortByUserId('ASC')` in the library. Instead when this function is called it will parse out the name of the method and recognize that UserId is the name of a column and convert that into `addSort('user_id', 'ASC')` as in `Figure 8`.

There are three kinds of pagination methods also available

**Pagination Methods**

```php
$database->setRange(75);
$database->setStart(25);
$database->setPage(1);
```

It's important if you are going to use `setPage(1)` to call `setRange(75)` first because the underlying function simply calculates the start index based on the range. Two other methods that are not covered by `Figure 8` are the ability to group and to set the table to something else.

**Figure 9. Other Useful methods**

```php
$database->->setTable('user');
$database->groupBy('user_active');
```

### Getting Results

When your happy with your query you can retrieve the results in 3 ways as described in Figure 0.

**Figure 10. Retrieving Results**

```php
$database->getTotal();
$database->getRows();
$database->getCollection();
```

`Figure 10` shows three ways to get the results, the first way `getTotal()`, will retrieve the total number and does not consider pagination elements. `getRows()` will simply return a raw array. `getCollection()` will return you an object with the results for further manipulation.

----

<a name="collection"></a>
## Collections

Collections do exactly the same thing as models except it manipulates multiple models instead. Collections can be iterable and access as arrays as well. Collections only hold model objects so if you wanted to use your own extended model, you would need to call `setModel('Your_Model')`.

**Figure 11. MySQL Collections**

```php
//set user name for all rows
$collection->setUserName('Chris');

// set or get any abstract key for all rows
$collection->setAnyThing()

//collections are iterable
foreach($collection as $model) {        
    echo $model->getUserName().' ';
    echo $model['user_email'];
}

//access as array
echo $collection[0]['user_name'];
//set as array
$collection[0]['user_email'] = 'my@email.com';

//save to 'user' table in database
//only relevant columns will be saved
//for all rows
$collection->save('user', $database);
```

Some other utility methods not covered by the above examples are date formatting and copying from one column to another. `Figure 12`, show how we would go about doing these things.

**Figure 12. Utility methods**

```php
//formats a date column
$collection->formatTime('post_created', 'F d, y g:ia');

//for each row, copy the value of post_user to the user_id column
$collection->copy('post_user', 'user_id');

//remove the row with the index of 1, reindexes all the rows
$collection->cut(1);

//returns the number of rows
$collection->count();

//adds a new row
$collection->add(['post_title' => 'Hi']);

//returns a table array (no objects)
$collection->get();                                      
```

----

<a name="model"></a>
## Models

We managed to loosely define models which takes off the restrictiveness of a normal ORM and adds scalability as an end result. First off, what we did was define a generic, yet powerful model class that can be extended, but also can be used as is. Our model class is already powerful enough to solve for a lot of use cases, you might not need to extend it. We played around with the concept of "loosely defined" and here's what we came up with.

**Figure 13. Database Model (Extends Array)**

```php
$model->setUserName('Chris'); //set user name
$model->getUserEmail(); // returns user email

//$model->setAnyThing() // set or get any abstract key

echo $model['user_name']; //access as array
$model['user_email'] = 'my@email.com'; //set as array

echo $model->user_name; //access as object
$model->user_name = 'my@email.com'; //set as object

//save to 'user' table in database
//only relevant columns will be saved
$model->save('user', $database);
```

So model properties can be accessed by method, object or array. The preference we leave up to you. With our model, you can put extra key values in the object, even if it has nothing to do with the intended database table. When you call `save()`, this is when you need to specify the table your saving to. This method is really powerful, in that it will first check to see what columns exist in the table then compares it with your model. It will only save columns that have the matching column name in your object. Lastly it will auto determine whether if we should insert or update that row.

A common example is when you have an array table that comprises of joined data. You can leave that array as is then call `save()` for each table as in `Figure 14`.

**Figure 14. Two tables**

```php
$row = [
    'user_id'       => 1,
    'user_name'     => 'Chris',
    'user_email'    => 'my@email.com',
    'post_user'     => 1,
    'post_title'    => 'My Post',
    'post_detail'   => 'This is my new article'
];

$database->model($row)->save('user')->save('post');
```

> **Note:** You can also save to different databases as in `save('post', $db2)`

----

<a name="together"></a>
## Putting it all together

So a common scenario would be retrieving data, manipulating the results and sending back to the database. Let's see with search, collection and model objects how we can achieve this.

**Figure 15. The Coolest Thing Ever!**

```php
//load database
$database
    //search user table
    ->search('user')
    //WHERE user_gender = $_get['gender']
    ->filterByUserGender($_GET['gender'])
    //ORDER BY user_id
    ->sortByUserId('ASC')
    //LIMIT 75, 25
    ->setStart(75)->setRange(25)
    //get a collection object
    ->getCollection()
    //sets all users to active
    ->setUserActive(1)
    //Set a new column post_title
    ->setPostTitle('A '.$_GET['gender'].'\'s Post')
    //Set a new column post_detail
    ->setPostDetail('Content is King')
    //Copy the contents of user_id to a new column post_user
    ->copy('user_id', 'post_user')
    //Set a new column post_created
    ->setPostCreated(time())
    ->formatTime('post_created', 'Y-m-d H:i:s')
    //save to user table
    ->save('user')
    //save to post table
    ->save('post');
```
