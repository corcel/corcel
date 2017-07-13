Corcel
======

> This package allows you to use WordPress as backend (admin panel) and retrieve its data using Eloquent, with any PHP project or even framework.

[![Travis](https://travis-ci.org/corcel/corcel.svg?branch=master)](https://travis-ci.org/corcel/corcel?branch=master)
[![Packagist](https://img.shields.io/packagist/v/jgrossi/corcel.svg)](https://packagist.org/packages/jgrossi/corcel)
[![Packagist](https://img.shields.io/packagist/dt/jgrossi/corcel.svg)](https://github.com/jgrossi/corcel/releases)
[![Coverage Status](https://coveralls.io/repos/github/corcel/corcel/badge.svg?branch=master)](https://coveralls.io/github/corcel/corcel?branch=master)

<a href='https://ko-fi.com/A36513JF' target='_blank'><img height='36' style='border:0px;height:36px;' src='https://az743702.vo.msecnd.net/cdn/kofi4.png?v=0' border='0' alt='Buy Me a Coffee at ko-fi.com' /></a>

[![Twitter Follow](https://img.shields.io/twitter/follow/corcelphp.svg?style=social&label=Follow)](http://twitter.com/CorcelPHP)

Corcel is a class collection created to retrieve WordPress database data using a better syntax. It uses the [Eloquent ORM](https://github.com/illuminate/database) developed for the Laravel Framework, but you can use Corcel in any type of PHP project, with any framework, including Laravel.

This way, you can use WordPress as the backend (admin panel), to insert posts, custom types, etc, and you can use whatever you want in the frontend, like Silex, Slim Framework, Laravel, Zend, or even pure PHP (why not?). So, just use Corcel to retrieve data from WordPress.

# Contents

- [Installing Corcel](#install)
- [Database Setup](#database-setup)
- [Usage](#usage)
    - [Posts](#posts)
    - [Advanced Custom Fields (ACF) Integration](#acf)
    - [Custom Post Type](#custom-post)
    - [Single Table Inheritance](#single-tab)
    - [Taxonomies](#taxonomies)
    - [Post Format](#post-format)
    - [Pages](#pages)
    - [Categories & Taxonomies](#cats)
    - [Attachments & Revision](#attachments)
    - [Options](#options)
    - [Menu](#menu)
    - [Users](#users)
    - [Authentication](#auth)
    - [Running Tests](#tests)
- [Contributing](#contrib)
- [License](#license)

# <a id="install"></a> Installing Corcel

You need to use Composer to install Corcel into your project:

```
composer require jgrossi/corcel
```

Now you're almost ready to use Corcel classes, like `Corcel\Post`.

## WordPress Installation

You can install WordPress inside Laravel's `/public` directory, like `/public/wordpress`, for example, or even in a different domain or server. You only have to enable access to its database, because that's what Corcel will use. This is made by creating a database connection.

# <a id="database-setup"></a> Database Setup

## Laravel Setup

Just the the database `connection` you want to be used by Corcel. Let' suppose you have those following database connections in your `config/database.php` file:

```php
<?php // File: /config/database.php

'connections' => [

    'mysql' => [ // for Laravel database
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'database'  => 'mydatabase',
        'username'  => 'admin'
        'password'  => 'secret',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
        'strict'    => false,
        'engine'    => null,
    ],

    'wordpress' => [ // for WordPress database
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'database'  => 'mydatabase',
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

In this case you should want to use the `wordpress` connection for Corcel, so just set it into the Corcel config file `config/corcel.php`:

```php
'connection' => 'wordpress',
```

## Other PHP Framework (not Laravel) Setup

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

# <a id="usage"></a>  Usage

Optionally you can create your own `Post` model which extends `Corcel\Post`. Then set the connection name you're using, in this case `foo-bar`:

```php
<?php // File: app/Post.php

namespace App;

use Corcel\Post as Corcel;

class Post extends Corcel
{
    protected $connection = 'foo-bar';
}
```

So, now you can fetch WP database data using your own class:

```php
$posts = App\Post::all(); // using the 'foo-bar' connection
```

> Remembering that if you have a `wordpress` or `corcel` database connection you don't need to extend `Corcel\Post` class, just use `Corcel\Post` instead.

## <a id="posts"></a> Posts

> Every time you see `Post::method()`, if you're using your own Post class (where you set the connection name), like `App\Post` you should use `App\Post::method()` and not `Post::method()`. All the examples are assuming you already know this difference.

```php
// All published posts
$posts = Post::published()->get();
$posts = Post::status('publish')->get();

// A specific post
$post = Post::find(31);
echo $post->post_title;
```

### Meta Data (Custom Fields)

> NOTE: In Corcel v1 you could save meta data using the `Post::save()` method. That's not allowed anymore. Use `saveMeta()` or `createMeta()` (see below) methods to save post meta.

You can retrieve meta data from posts too.

```php
// Get a custom meta value (like 'link' or whatever) from a post (any type)
$post = Post::find(31);
echo $post->meta->link; // OR
echo $post->fields->link;
echo $post->link; // OR
```

To create or update meta data form a User just use the `saveMeta()` or `saveField()` methods. They return `bool` like the Eloquent `save()` method.

```php
$post = Post::find(1);
$post->saveMeta('username', 'jgrossi');
```

You can save many meta data at the same time too:

```php
$post = Post::find(1);
$post->saveMeta([
    'username' => 'jgrossi',
    'url' => 'http://jgrossi.com',
]);
```

You also have the `createMeta()` and `createField()` methods, that work like the `saveX()` methods, but they are used only for creation and return the `PostMeta` created instance, instead of `bool`.

```php
$post = Post::find(1);
$postMeta = $post->createMeta('foo', 'bar'); // instance of PostMeta class
$trueOrFalse = $post->saveMeta('foo', 'baz'); // boolean
```

### Querying Posts by Custom Fields (Meta)

There are multiples possibilities to query posts by their custom fields (meta). Just use the `hasMeta()` scope under `Post` (actually for all models using the `HasMetaFields` trait) class:

```php
// Using just one custom field
$post = Post::published()->hasMeta('username', 'jgrossi')->first(); // setting key and value
$post = Post::published()->hasMeta('username'); // setting just the key
```

You can also use the `hasMeta()` scope passing an array as parameter:

```php
$post = Post::hasMeta(['username' => 'jgrossi'])->first();
$post = Post::hasMeta(['username' => 'jgrossi', 'url' => 'jgrossi.com'])->first();
// Or just passing the keys
$post = Post::hasMeta(['username', 'url'])->first();
```

### Fields Aliases

The `Post` class has support to "aliases", so if you check the `Post` class you should note some aliases defined in the static `$aliases` array, like `title` for `post_title` and `content` for `post_content`.

```php
$post = Post::find(1);
$post->title === $post->post_title; // true
```

If you're extending the `Post` class to create your own class you can use `$aliases` too. Just add new aliases to that static property inside your own class and it will automatically inherit all aliases from parent `Post` class:

```php
class A extends \Corcel\Post
{
    protected static $aliases = [
        'foo' => 'post_foo',
    ];
}

$a = A::find(1);
echo $a->foo;
echo $a->title; // from Post class
```

### Custom Scopes

To order posts you can use `newest()` and `oldest()` scopes, for both `Post` and `User` classes:

```php
$newest = Post::newest()->first();
$oldest = Post::oldest()->first();
```

### Pagination

To order posts just use Eloquent `paginate()` method:

```php
$posts = Post::published()->paginate(5);
foreach ($posts as $post) {
    // ...
}
```

To display the pagination links just call the `links()` method:
 
 ```php
 {{ $posts->links() }}
 ```

## <a id="acf"></a>  Advanced Custom Fields (ACF)

If you want to retrieve a custom field created by the [Advanced Custom Fields (ACF)](http://advancedcustomfields.com) plugin, you have to install the [`corcel/acf`](http://github.com/corcel/acf) plugin and call the custom field like this:

```php
$post = Post::find(123);
echo $post->acf->some_radio_field;
$repeaterFields = $post->acf->my_repeater_name;
```

To avoid unnecessary SQL queries just set the field type you're requesting. Usually two SQL queries are necessary to get the field type, so if you want to specify it you're skipping those extra queries:

```php
$post = Post::find(123);
echo $post->acf->text('text_field_name');
echo $post->acf->boolean('boolean_field_name');
```

## <a id="custom-post"></a> Custom Post Type

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

## <a id="single-tab"></a>Single Table Inheritance

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

## <a id="shortcodes"></a> Shortcodes

### From config (Laravel)

You can map all shortcodes you want inside the `config/corcel.php` file, under the `'shortocodes'` key. In this case you should create your own class that `implements` the `Corcel\Shortcode` interface, that requires a `render()` method:

```php
'shortcodes' => [
    'foo' => App\Shortcodes\FooShortcode::class,
    'bar' => App\Shortcodes\BarShortcode::class,
],
```

This is a sample shortcode class:

```php
class FakeShortcode implements \Corcel\Shortcode
{
    /**
     * @param ShortcodeInterface $shortcode
     * @return string
     */
    public function render(ShortcodeInterface $shortcode)
    {
        return sprintf(
            'html-for-shortcode-%s-%s',
            $shortcode->getName(),
            $shortcode->getParameter('one')
        );
    }
}
```

### In runtime

You can add [shortcodes](https://codex.wordpress.org/Shortcode_API) by calling the `addShortcode` method on the `Post` model :

```php
// [gallery id="1"]
Post::addShortcode('gallery', function ($shortcode) {
    return $shortcode->getName() . '.' . $shortcode->getParameter('id');
});
$post = Post::find(1);
echo $post->content;
```

If you are using Laravel, we suggest adding your shortcodes handlers in `App\Providers\AppServiceProvider`, in the `boot` method.

The [*thunderer/shortcode*](https://github.com/thunderer/Shortcode) library is used to parse the shortcodes.  For more information, [click here](https://github.com/thunderer/Shortcode).

## <a id="taxonomies"></a>Taxonomies

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

## <a id="post-format"></a>Post Format

You can also get the post format, like the WordPress function `get_post_format()`:

```php
echo $post->getFormat(); // should return something like 'video', etc
```

## <a id="pages"></a>Pages

Pages are like custom post types. You can use `Post::type('page')` or the `Corcel\Page` class.

```php

use Corcel\Page;

// Find a page by slug
$page = Page::slug('about')->first(); // OR
$page = Post::type('page')->slug('about')->first();
echo $page->post_title;
```

## <a id="cats"></a>Categories & Taxonomies

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

## <a id="attachments"></a>Attachment and Revision

Getting the attachment and/or revision from a `Post` or `Page`.

```php
$page = Page::slug('about')->with('attachment')->first();
// get feature image from page or post
print_r($page->attachment);

$post = Post::slug('test')->with('revision')->first();
// get all revisions from a post or page
print_r($post->revision);
```

## <a id="options"></a>Options

> In previous versions of Corcel this classe was called `Options` instead of `Option` (singular). So take care of using always this class in the singular form starting from `v2.0.0`.

> The `Option::getAll()` method was removed in Corcel 2+, in favor of `Option::asArray($keys [])`.

You can use the `Option` class to get data from `wp_options` table:

```php
$siteUrl = Option::get('siteurl');
```

You can also add new options:

```php
Option::add('foo', 'bar'); // stored as string
Option::add('baz', ['one' => 'two']); // this will be serialized and saved
```

You can get all options in a simple array:

```php
$options = Option::asArray();
echo $options['siteurl'];
```

Or you can specify only the keys you want to get:

```php
$options = Option::asArray(['siteurl', 'home', 'blogname']);
echo $options['home'];
```

## <a id="menu"></a> Menu

To get a menu by its slug, use the syntax below. The menu items will be loaded in the `items` variable (it's a collection of `Corcel\MenuItem` objects). 

The currently supported menu items are: Pages, Posts, Custom Links and Categories.

Once you'll have instances of `MenuItem` class, if you want to use the original instance (like the original Page or Term, for example), just call the `MenuItem::instance()` method. The `MenuItem` object is just a post with `post_type` equals `nav_menu_item`:

```php
$menu = Menu::slug('primary')->first();

foreach ($menu->items as $item) {
    echo $item->instance()->title; // if it's a Post
    echo $item->instance()->name; // if it's a Term
    echo $item->instance()->link_text; // if it's a custom link 
}
```

The `instance()` method will return the matching object:

- `Post` instance for `post` menu item;
- `Page` instance for `page` menu item;
- `CustomLink` instance for `custom` menu item;
- `Term` instance for `category` menu item.

### Multi-levels Menus

To handle multi-levels menus, loop through all the menu items to put them on the right levels, for example.

You can use the `MenuItem::parent()` method to retrieve the parent instance of that menu item:

```php
$items = Menu::slug('foo')->first()->items;
$parent = $items->first()->parent(); // Post, Page, CustomLink or Term (category)
```

To group menu items according their parents, you can use the `->groupBy()` method in the `$menu->items` collection, grouping menu items by their `$item->parent()->ID`. 

To read more about the `groupBy()` method [take a look on the Laravel documentation](https://laravel.com/docs/5.4/collections#method-groupby).

## <a id="users"></a> Users

You can manipulate users in the same manner you work with posts:

```php
// All users
$users = User::get();

// A specific user
$user = User::find(1);
echo $user->user_login;
```

## <a id="auth"></a>Authentication

### Using Laravel

Make sure you have `CorcelServiceProvider` provider registered in `config/app.php` :

```php
'providers' => [
    // Other Service Providers

    Corcel\Laravel\CorcelServiceProvider::class,
],
```

And then, define the user provider in `config/auth.php` to allow Laravel to login with WordPress users:

```php
'providers' => [
    'users' => [
        'driver' => 'corcel',
        'model'  => Corcel\User::class,
    ],
],
```

Now you can use the `Auth` facade to authenticate users:

```php
Auth::validate([
    'email' => 'admin@example.com', // or using 'username' too
    'password' => 'secret',
]);
```

To make Laravel's Password Reset work with Corcel, we have to override how passwords are stored in the database. To do this, you must change `Auth/PasswordController.php` from:

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
use Corcel\Laravel\Auth\ResetsPasswords as CorcelResetsPasswords;

class PasswordController extends Controller
{
    use ResetsPasswords, CorcelResetsPasswords {
        CorcelResetsPasswords::resetPassword insteadof ResetsPasswords;
    }
```

### Not using Laravel

You can use the `AuthUserProvider` class to manually authenticate a user :

```php
$userProvider = new Corcel\Laravel\Auth\AuthUserProvider;
$user = $userProvider->retrieveByCredentials(['username' => 'admin']);
if(!is_null($user) && $userProvider->validateCredentials($user, ['password' => 'admin'])) {
    // successfully login
}
```

> Remember you can use both `username` and `email` as credentials for a User.

# <a id="tests"></a> Running Tests

To run the phpunit tests, execute the following command :

```
./vendor/bin/phpunit
```

If you have the global `phpunit` command installed you can just type:

```
phpunit
```

# <a id="contrib"></a> Contributing

All contributions are welcome to help improve Corcel.

Before you submit your pull request consider the following guidelines:

- Make your changes in a new git branch, based on the dev branch:

`git checkout -b my-fix-branch dev`

- Create your patch/feature, including appropriate test cases.

- Run the unit tests, and ensure that all tests pass.

- In GitHub, send a pull request to `corcel:dev`, not `corcel:master`, please.

## <a id="license"></a> Licence

[MIT License](http://jgrossi.mit-license.org/) © Junior Grossi
