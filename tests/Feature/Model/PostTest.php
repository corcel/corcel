<?php

namespace Corcel\Tests\Feature\Model;

use Corcel\Model\Post;
use Corcel\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * Class PostTest
 *
 * @package Corcel\Tests\Feature\Model
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class PostTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function it_returns_a_user_resource_instance()
    {
        $post = factory(Post::class)->create();

        $response = $this->json('GET', route('posts.show', $post));

        $response->assertStatus(200);
        $response->assertJson(['data' => ['id' => $post->ID]]);
    }
}
