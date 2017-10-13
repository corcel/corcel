<?php /** @var \Illuminate\Routing\Router $router */

$router->get('posts', 'PostsController@index')->name('posts.index');
$router->get('posts/{post}', 'PostsController@show')->name('posts.show');