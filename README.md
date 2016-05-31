Corcel
======

> This package allows you to use WordPress as backend (admin panel) and retrieve its data using Eloquent, with any PHP project or even framework.

[![Travis](https://travis-ci.org/jgrossi/corcel.svg?branch=dev)](https://travis-ci.org/jgrossi/corcel?branch=dev)
[![Packagist](https://img.shields.io/packagist/v/jgrossi/corcel.svg)](https://github.com/jgrossi/corcel/releases)
[![Packagist](https://img.shields.io/packagist/dt/jgrossi/corcel.svg)](https://packagist.org/packages/jgrossi/corcel)

Corcel is a class collection created to retrieve WordPress database data using a better syntax. It uses the [Eloquent ORM](https://github.com/illuminate/database) developed for the Laravel Framework, but you can use Corcel in any type of PHP project, with any framework, including Laravel.

This way, you can use WordPress as the backend (admin panel), to insert posts, custom types, etc, and you can use whatever you want in the frontend, like Silex, Slim Framework, Laravel, Zend, or even pure PHP (why not?). So, just use Corcel to retrieve data from WordPress.

## Installation

To install Corcel, just run the following command:

```
composer require jgrossi/corcel
```

## Usage

### I'm using Laravel

If you are using Laravel you **do not need** to configure database again. It's all already set by Laravel. So you have only to change the `config/database.php` config file and set yours connections. You can use just one connection or two (one for your Laravel app and another to Corcel). Your file will look like this:

```php
<?php // File: /config/database.php

'connections' => [

    'mysql' => [ // this is your Laravel database connection
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'database'  => 'app',
        'username'  => 'admin'
        'password'  => 'secret',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
        'strict'    => false,
        'engine'    => null,
    ],

    'wordpress' => [ // this is your Corcel database connection, where WordPress tables are
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'database'  => 'corcel',
        'username'  => 'admin',
        'password'  => 'secret',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => 'wp_',
        'strict'    => false,
        'engine'    => null,
    ],
    
],
```

Now you should create your own `Post` model class. Laravel stores model classes in `app` directory, inside `App` namespace (or the name you gave it). Your `Post` class should extends `Corcel\Post` and set the connection name you're using, in this case `wordpress`:

```php
<?php // File: app/Post.php

namespace App;

use Corcel\Post as Corcel;

class Post extends Corcel
{
    protected $connection = 'wordpress';
}
```

So, now you can fetch database data:

```php
$posts = App\Post::all(); // using the 'wordpress' connection
$posts = Corcel\Post::all(); // using the 'default' Laravel connection
```

### I'm using another PHP Framework

Here you have to configure the database to fit the Corcel requirements. First, you should include the Composer `autoload` file if not already loaded:

```php
require __DIR__ . '/vendor/autoload.php';
```

Now you must set your WordPress database params:

```php
$params = array(
    'database'  => 'database_name',
    'username'  => 'username',
    'password'  => 'pa$$word',
    'prefix'    => 'wp_' // default prefix is 'wp_', you can change to your own prefix
);
Corcel\Database::connect($params);
```

You can specify all Eloquent params, but some are default (but you can override them).

```php
'driver'    => 'mysql',
'host'      => 'localhost',
'charset'   => 'utf8',
'collation' => 'utf8_unicode_ci',
'prefix'    => 'wp_', // Specify the prefix for WordPress tables, default prefix is 'wp_'
```

### Posts

> Every time you see `Post::method()`, if you're using your own Post class (where you set the connection name), like `App\Post` you should use `App\Post::method()` and not `Post::method()`. All the examples are assuming you already know this difference.

```php
// All published posts
$posts = Post::published()->get();
$posts = Post::status('publish')->get();

// A specific post
$post = Post::find(31);
echo $post->post_title;

// Filter by meta/custom field
$posts = Post::published()->hasMeta('field')->get();
$posts = Post::hasMeta('acf')->get();
```

You can retrieve meta data from posts too.

```php
// Get a custom meta value (like 'link' or whatever) from a post (any type)
$post = Post::find(31);
echo $post->meta->link; // OR
echo $post->fields->link;
echo $post->link; // OR
```

Updating post custom fields:

```php
$post = Post::find(1);
$post->meta->username = 'juniorgrossi';
$post->meta->url = 'http://grossi.io';
$post->save();
```

Inserting custom fields:

```php
$post = new Post;
$post->save();

$post->meta->username = 'juniorgrossi';
$post->meta->url = 'http://grossi.io';
$post->save();
```

### Custom Post Type

You can work with custom post types too. You can use the `type(string)` method or create your own class.

```php
// using type() method
$videos = Post::type('video')->status('publish')->get();

// using your own class
class Video extends Corcel\Post
{
    protected $postType = 'video';
}
$videos = Video::status('publish')->get();
```

Using `type()` method will make Corcel to return all objects as `Corcel\Post`. Using your custom class you have the advantage to customize classes, including custom methods and properties, return all objects as `Video`, for example.

Custom post types and meta data:

```php
// Get 3 posts with custom post type (store) and show its address
$stores = Post::type('store')->status('publish')->take(3)->get();
foreach ($stores as $store) {
    $storeAddress = $store->address; // option 1
    $storeAddress = $store->meta->address; // option 2
    $storeAddress = $store->fields->address; // option 3
}
```

## Single Table Inheritance

If you choose to create a new class for your custom post type, you can have this class be returned for all instances of that post type.

```php
//all objects in the $videos Collection will be instances of Post
$videos = Post::type('video')->status('publish')->get();

// register the video custom post type and its particular class
Post::registerPostType('video', '\App\Video')


//now all objects in the $videos Collection will be instances of Video
$videos = Post::type('video')->status('publish')->get();
```

You can also do this for inbuilt classes, such as Page or Post. Simply register the Page or Post class with the associated post type string, and that object will be returned instead of the default one.

This is particular useful when you are intending to get a Collection of Posts of different types (e.g. when fetching the posts defined in a menu). 

### Shortcodes

You can add [shortcodes](https://codex.wordpress.org/Shortcode_API) by calling the `addShortcode` method on the `Post` model :

```php
Post::addShortcode('gallery', function ($shortcode) {
    return $shortcode->getName() . '.' . $s->getParameter('id');
});
$postWithShortcodes = Post::find(1);
echo $postWithShortcodes->content;
```

The library used to parse the shortcodes is [thunderer/shortcode](https://github.com/thunderer/Shortcode).

If you are using Laravel, we suggest adding your shortcodes handlers in `App\\Providers\\AppServiceProvider`, in the `boot` method.

### Taxonomies

You can get taxonomies for a specific post like:

```php
$post = Post::find(1);
$taxonomy = $post->taxonomies()->first();
echo $taxonomy->taxonomy;
```

Or you can search for posts using its taxonomies:

```php
$post = Post::taxonomy('category', 'php')->first();
```

### Pages

Pages are like custom post types. You can use `Post::type('page')` or the `Page` class.

```php
// Find a page by slug
$page = Page::slug('about')->first(); // OR
$page = Post::type('page')->slug('about')->first();
echo $page->post_title;
```

### Categories & Taxonomies

Get a category or taxonomy or load posts from a certain category. There are multiple ways
to achieve it.

```php
// all categories
$cat = Taxonomy::category()->slug('uncategorized')->posts()->first();
echo "<pre>"; print_r($cat->name); echo "</pre>";

// only all categories and posts connected with it
$cat = Taxonomy::where('taxonomy', 'category')->with('posts')->get();
$cat->each(function($category) {
    echo $category->name;
});

// clean and simple all posts from a category
$cat = Category::slug('uncategorized')->posts()->first();
$cat->posts->each(function($post) {
    echo $post->post_title;
});
```

### Attachment and Revision

Getting the attachment and/or revision from a `Post` or `Page`.

```php
$page = Page::slug('about')->with('attachment')->first();
// get feature image from page or post
print_r($page->attachment);

$post = Post::slug('test')->with('revision')->first();
// get all revisions from a post or page
print_r($post->revision);
```

### Menu

To get a menu by its slug, use the syntax below.
The menu items will be loaded in the `nav_items` variable. The currently supported menu items are: Pages, Posts, Links, Categories, Tags.

```php
$menu = Menu::slug('primary')->first();

foreach ($menu->nav_items as $item) {
    // ....
    'post_title'    => '....', // Nav item name
    'post_name'     => '....', // Nav item slug
    'guid'          => '....', // Nav full url, influent by permalinks
    // ....
}
```

To handle multi-levels menus, loop through all the menu items to put them on the right levels in an array.
Then, you can walk through the items recursively.

Here's just a basic example:

```php
// first, set all menu items on their level
$menuArray = array();
foreach ($menu->nav_items as $item) {
    $parent_id = $item->meta->_menu_item_menu_item_parent;
    $menuArray[$parent_id][] = $item;
}

// now build the menu
foreach ($menuArray[0] as $item) {
    echo '.. menu item main ..';
    if (isset($menuArray[$item->ID])) {
        foreach($menuArray[$item->ID] as $subItem) {
            echo '.. show sub menu item ..';
        }
    }
}
```

### Users

You can manipulate users in the same manner you work with posts:

```php
// All users
$users = User::get();

// A specific user
$user = User::find(1);
echo $user->user_login;
```

### Authentication

#### Using laravel

You will have to register Corcel's authentication service provider in `config/app.php` :

```php
'providers' => [
    // Other Service Providers

    Corcel\Providers\Laravel\AuthServiceProvider::class,
],
```

And then, define the user provider in `config/auth.php` :

```php
'providers' => [
    'users' => [
        'driver' => 'corcel',
        'model'  => Corcel\User::class,
    ],
],
```

To make Laravel's Password Reset work with Corcel, we have to override how passwords are stored in the database. To do this, you must change `Auth/PasswordController.php` from :

```php
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class PasswordController extends Controller
{
    use ResetsPasswords;
```

to

```php
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Corcel\Auth\ResetsPasswords as CorcelResetsPasswords;

class PasswordController extends Controller
{
    use ResetsPasswords, CorcelResetsPasswords {
        CorcelResetsPasswords::resetPassword insteadof ResetsPasswords;
    }
```

#### Using something else

You can use the `AuthUserProvider` class to authenticate an user :

```php
$userProvider = new Corcel\Providers\AuthUserProvider;
$user = $userProvider->retrieveByCredentials(['username' => 'admin']);
if(!is_null($user) && $userProvider->validateCredentials($user, ['password' => 'admin'])) {
    // successfully login
}
```

## Running tests

To run the phpunit tests, execute the following command :

```
./vendor/bin/phpunit
```

If you have the global `phpunit` command installed you can just type:

```
phpunit
```

## Contributing

All contributions are welcome to help improve Corcel.

Before you submit your pull request consider the following guidelines:

- Make your changes in a new git branch, based on the dev branch:

`git checkout -b my-fix-branch dev`

- Create your patch/feature, including appropriate test cases.

- Run the unit tests, and ensure that all tests pass.

- In GitHub, send a pull request to `corcel:dev`.

## Licence

[MIT License](http://jgrossi.mit-license.org/) © Junior Grossi
