<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Corcel Database Connection Name
    |--------------------------------------------------------------------------
    |
    | By default, Corcel uses your default database connection, set on
    | `config/database.php` (`default` key). Usually you'd like to use a
    | custom database just for WordPress. First you must configure that
    | database connection in `config/database.php`, and then set here its
    | name, like 'corcel', for example. Then you can work with two or more
    | database, but this one is only for your WordPress tables.
    |
    */

    'connection' => 'corcel',

    /*
    |--------------------------------------------------------------------------
    | Registered Custom Post Types
    |--------------------------------------------------------------------------
    |
    | WordPress allows you to create your own custom post types. Corcel
    | makes querying posts using a custom post type easier, but here you can
    | set a list of your custom post types, and Corcel will automatically
    | register all of them, making returning those custom classes, instead
    | of just Post objects.
    |
    */

    'post_types' => [
//        'video' => App\Models\Video::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Registered Shortcodes
    |--------------------------------------------------------------------------
    |
    | With Corcel you can register as many shortcodes you want, but that's
    | usually made in runtime. Here it's the place to set all your custom
    | shortcodes to make Corcel registering all of them automatically. Just
    | create your own shortcode class implementing `Corcel\Shortcode` interface.
    |
    */

    'shortcodes' => [
//        'foo' => App\Shortcodes\FooShortcode::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Registered Shortcode Parser
    |--------------------------------------------------------------------------
    |
    | Corcel uses the thunderer/shortcode library to parse shortcodes. Thunderer
    | provides three different parsers for shortcodes. You can use a
    | different parser if it suits your requirements better, or create your own.
    |
    */

    'shortcode_parser' => Thunder\Shortcode\Parser\RegularParser::class,
    // 'shortcode_parser' => Thunder\Shortcode\Parser\RegexParser::class,
    // 'shortcode_parser' => Thunder\Shortcode\Parser\WordpressParser::class,

];
