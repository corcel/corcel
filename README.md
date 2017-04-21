Corcel
======

> This package allows you to use WordPress as backend (admin panel) and retrieve its data using Eloquent, with any PHP project or even framework.

[![Travis](https://travis-ci.org/corcel/corcel.svg?branch=dev)](https://travis-ci.org/corcel/corcel?branch=dev)
[![Packagist](https://img.shields.io/packagist/v/jgrossi/corcel.svg)](https://packagist.org/packages/jgrossi/corcel)
[![Packagist](https://img.shields.io/packagist/dt/jgrossi/corcel.svg)](https://github.com/jgrossi/corcel/releases)

<a href='https://ko-fi.com/A36513JF' target='_blank'><img height='36' style='border:0px;height:36px;' src='https://az743702.vo.msecnd.net/cdn/kofi4.png?v=0' border='0' alt='Buy Me a Coffee at ko-fi.com' /></a>

[![Twitter Follow](https://img.shields.io/twitter/follow/corcelphp.svg?style=social&label=Follow)](http://twitter.com/CorcelPHP)

Corcel is a class collection created to retrieve WordPress database data using a better syntax. It uses the [Eloquent ORM](https://github.com/illuminate/database) developed for the Laravel Framework, but you can use Corcel in any type of PHP project, with any framework, including Laravel.

This way, you can use WordPress as the backend (admin panel), to insert posts, custom types, etc, and you can use whatever you want in the frontend, like Silex, Slim Framework, Laravel, Zend, or even pure PHP (why not?). So, just use Corcel to retrieve data from WordPress.

# Contents

- [Installing Corcell and Wordpress into Laravel](#install)
- [Database Setup](#database-setup)
- [Usage](#usage)    
    - [Posts](#posts)
    - [Advanced Custom Fields Integration](#acf)
    - [Custom Post Type](#custom-post)
    - [Single Table Inheritance](#single-tab)
    - [Taxonomies](#taxonomies)
    - [Post Format](#post-format)
    - [Pages](#pages)
    - [Categories & Taxonomies](#cats)
    - [Attachments & Revision](#attachments)
    - [Menu](#menu)
    - [Users](#users)
    - [Authentication](#auth)
    - [Running Tests](#tests)
- [Contributing](#contrib)
- [License](#license)



# <a id="install"></a> Installing Corcel and Wordpress into Laravel

## Add corcel to your laravel project 
```
composer require jgrossi/corcel
```
## Add Wordpress to your laravel project
So we have now our Laravel folder structure with everything installed. Now let's bring in a fresh installation of Wordpress. 

### **Access Option 1 - Sub Directory**
- Install WordPress as a subdirectory of Laravels public folder (ex. `/public/wordpress`). To access your backend you would go to `http://example.dev/wordpress/wp-admin`.

### **Access Option 2 - Sub Domain**
- Install Wordpress as a sub-dir of the Laravel's root, like `/wordpress`. So you will have `/app` and `/wordpress` in the same position. For this you have to create another VirtualHost to point to Wordpress installation. You can setup a subdomain like `wp.example.dev` and poit it to `/wordpress`. This way you can access the Admin going to `http://wp.example.dev/wp-admin`. 

### **Once you've decided**

Clone wordpress into the **option 1** or the **option 2** directory.
```
git clone https://github.com/WordPres wordpress
```
### **Quick Tip!** If you use github you won't be able to check in your project until you remove the .git folder inside your newly created wordpress directory.
```
cd wordpress && sudo rm -r .git
```
# <a id="database-setup"></a> Database Setup
You have two options you can let Wordpress and Laravel share the same database, or you can have a seperate database for each. This is totally up to you.



## Laravel and Wordpress sharing a database

Since laravel manages its own migrations you can share a database with WordPress, Laravel will only update/rollback/refresh the ones it knows about.

Add this new wordpress connection type to your connections array in `/config/database.php`. 

```php
<?php // File: /config/database.php

'connections' => [

    'mysql' => [ // this is how Laravel connects to the DB
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

    'wordpress' => [ // this is how Corcel connects to the DB
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

Now Laravel and Wordpress are sharing the same database.

## Laravel database and a seperate WordPress database

Add this new `wordpress` connection type to connections array in `/config/database.php`. Asjust the `wordpress` connections details so they are accurate. Corcel will use the `wordpress` connection to get its data and Laravel will use the `mysql` by default for its data.

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
        'database'  => 'wordpress',
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


## I'm using another PHP Framework

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

Optionally you can create your own `Post` model which extends `Corcel\Post`. Then set the connection name you're using, in this case `wordpress`:

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
```

## <a id="posts"></a> Posts

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

## <a id="menu"></a> Menu

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

### Using laravel

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

### Using something else

You can use the `AuthUserProvider` class to authenticate an user :

```php
$userProvider = new Corcel\Providers\AuthUserProvider;
$user = $userProvider->retrieveByCredentials(['username' => 'admin']);
if(!is_null($user) && $userProvider->validateCredentials($user, ['password' => 'admin'])) {
    // successfully login
}
```

# <a id="tests"></a> Running tests

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

- In GitHub, send a pull request to `corcel:dev`.

## <a id="license"></a>Licence

[MIT License](http://jgrossi.mit-license.org/) © Junior Grossi
