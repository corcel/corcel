<?php

namespace Corcel\Tests\Unit;

use Corcel\Model;
use Corcel\Model\Post;
use Corcel\Tests\TestCase;

/**
 * Class CorcelHelperTest
 *
 * @package Corcel\Tests\Unit
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class CorcelHelperTest extends TestCase
{
    /**
     * @var array
     */
    protected $baseBuilders = [
        'attachment',
        'comment',
        'custom_link',
        'menu',
        'menu_item',
        'option',
        'page',
        'post',
        'tag',
        'taxonomy',
        'term',
        'term_relationship',
        'user',
    ];

    /** @test */
    public function it_returns_the_correct_model_instance()
    {
        collect($this->baseBuilders)->each(function (string $name) {
            $this->assertInstanceOf(Model::class, corcel($name));
        });
    }

    /** @test */
    public function it_works_with_both_singular_and_plural_forms()
    {
        collect($this->baseBuilders)->each(function (string $name) {
            $this->assertInstanceOf(Model::class, corcel(str_plural($name)));
        });
    }

    /** @test */
    public function it_queries_like_the_default_model_format()
    {
        factory(Post::class, 2)->create();
        $this->assertCount(2, corcel('post')->all());
    }

    /** @test */
    public function it_works_for_different_connection_names()
    {
        $this->app['config']->set('corcel.connection', 'foo');
        factory(Post::class)->create();

        $this->app['config']->set('corcel.connection', 'wp');
        $this->assertNull(corcel('post')->first());

        $post = corcel('post')->on('foo')->first();

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals('foo', $post->getConnectionName());
    }
}
