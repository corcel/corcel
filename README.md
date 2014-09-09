Wordpress Corcel
================

*Corcel is under development.*

--

Corcel is a class collection created to retrieve Wordpress database data using a better syntax. It uses the Eloquent ORM developed for the Laravel Framework, but you can use Corcel in any type of PHP project.

This way you can use Wordpress as back-end, to insert posts, custom types, etc, and you can use what you want in front-end, like Silex, Slim Framework, Laravel, Zend, or even pure PHP (why not?).

## Installation

To install Corcel just create a `composer.json` file and add:

    "require": {
        "jgrossi/corcel": "dev-master"
    },

After that run `composer install` and wait.

## Usage

First you must include the Composer `autoload` file.

    require __DIR__ . '/vendor/autoload.php';

Now you must set your Wordpress database params:

    $params = array(
        'database'  => 'database_name',
        'username'  => 'username',
        'password'  => 'pa$$word',
    );
    Corcel\Database::connect($params);

You can specify all Eloquent params, but some are default (but you can override them).

    'driver'    => 'mysql',
    'host'      => 'localhost',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',

### Posts

    // All published posts
    $posts = Post::published()->get();
    $posts = Post::status('publish')->get();

    // A specific post
    $post = Post::find(31);
    echo $post->post_title;

You can retrieve meta data from posts too.

    // Get a custom meta value (like 'link' or whatever) from a post (any type)
    $post = Post::find(31);
    echo $post->meta->link; // OR
    echo $post->link;

### Custom Post Type

You can work with custom post types too. You can use the `type(string)` method or create your own class.

    // using type() method
    $videos = Post::type('video')->status('publish')->get();

    // using your own class
    class Video extends Corcel\Post
    {
        protected $postType = 'video';
    }
    $videos = Video::status('publish')->get();

Custom post types and meta data.

    // Get 3 posts with custom post type (store) and show its title
    $stores = Post::type('store')->status('publish')->take(3)->get();
    foreach ($stores as $store) {
        $storeAddress = $store->address;
    }

### Pages

Pages are like custom post types. You can use `Post::type('page')` or the `Page` class.

    // Find a page by slug
    $page = Page::slug('about')->first(); // OR
    $page = Post::type('page')->slug('about')->first();
    echo $page->post_title;

### Categories & Taxonomies

Get a category or taxonomy or load posts from a certain category. There are multiple ways
to achief it.

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


### Attachment and Revision

Getting the attachment and/or revision from a `Post` or `Page`.

    $page = Page::slug('about')->with('attachment')->first();
    // check if the page or post has attachment
    var_dump( $page->hasAttachment() );
    // get feature image from page or post
    print_r($page->attachment);

    // quick get the url of the post/page or attachment
    print_r($page->url());

    // stripped version can be used, when having htaccess rules for files. Can be done in the following way:
    print_r($page->url(true));


    $post = Post::slug('test')->with('revision')->first();
    // get all revisions from a post or page
    print_r($post->revision);


## TODO

I'm already working with Wordpress comments integration.

## Licence

Corcel is licensed under the MIT license.
