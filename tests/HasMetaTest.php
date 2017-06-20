<?php

use Corcel\Post;

class HasMetaTest extends PHPUnit_Framework_TestCase
{
    public function testUserHasMeta()
    {
        $post = Post::published()->hasMeta('username', 'juniorgrossi')
            ->first();

        $posts = Post::published()->hasMeta('city', ['amsterdam', 'london'])
            ->get();

        $this->assertTrue($post instanceof \Corcel\Post);

        $this->assertNotNull($posts);
        $this->assertTrue($posts instanceof Illuminate\Support\Collection);
        $this->assertNotEquals(0, $posts->count());

        foreach ($posts as $o_post) {
            $this->assertTrue($post instanceof \Corcel\Post);
        }
    }
}
