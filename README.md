Wordpress Corcel
================

Wrappers to use Wordpress backend with you framework of choice. Under development.

    $posts = Post::published()->type('post_type')->take(4)->orderBy('post_title')->get();
    foreach ($posts as $post) {
        echo $post->post_title . ' - ' . $post->meta->some_meta;
    }


