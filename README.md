Wordpress Corcel
================

*Corcel is under development, but it's working :D*

--

Corcel is a class collection created to retrieve Wordpress database data using a better syntax. It uses the [Eloquent ORM](https://github.com/illuminate/database) developed for the Laravel Framework, but you can use Corcel in any type of PHP project.

This way, you can use Wordpress as the back-end, to insert posts, custom types, etc, and you can use whatever you want in the front-end, like Silex, Slim Framework, Laravel, Zend, or even pure PHP (why not?).

## Installation

To install Corcel, just run the following command `composer require "jgrossi/corcel":"dev-master"`.

## Usage

First, you must include the Composer `autoload` file.

```php
require __DIR__ . '/vendor/autoload.php';
```

Now you must set your Wordpress database params:

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
'prefix'    => 'wp_', // Specify the prefix for wordpress tables, default prefix is 'wp_'
```

### Posts

```php
// All published posts
$posts = Post::published()->get();
$posts = Post::status('publish')->get();

// A specific post
$post = Post::find(31);
echo $post->post_title;
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

Custom post types and meta data:

```php
// Get 3 posts with custom post type (store) and show its title
$stores = Post::type('store')->status('publish')->take(3)->get();
foreach ($stores as $store) {
    $storeAddress = $store->address; // option 1
    $storeAddress = $store->meta->address; // option 2
    $storeAddress = $store->fields->address; // option 3
}
```

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

You can manipulate users in the same manner you work with posts.

```php
// All users
$users = User::get();

// A specific user
$user = User::find(1);
echo $user->user_login;
```

## Running tests

To run the phpunit tests, execute the following command :

```
./vendor/phpunit/phpunit/phpunit --colors --bootstrap tests/config/autoload.php tests
```

## TODO

I'm already working with Wordpress comments integration.

## Licence

Corcel is licensed under the MIT license.
