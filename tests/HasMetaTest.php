<?php

use Corcel\Post;

class HasMetaTest extends PHPUnit_Framework_TestCase
{

    public function testUserHasMeta()
    {
        $post = Post::published()->hasMeta('username', 'juniorgrossi')
            ->first();

        $this->assertTrue($post instanceof \Corcel\Post);
    }

}
