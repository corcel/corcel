<?php

namespace Corcel\Tests\Unit\Concerns;

use Corcel\Model\Post;
use Corcel\Tests\TestCase;
use Illuminate\Container\Container;
use Thunder\Shortcode\Parser\WordpressParser;
use Thunder\Shortcode\ShortcodeFacade;

/**
 * Class ShortcodesTest
 *
 * @package Corcel\Tests\Unit\Concerns
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class ShortcodesTest extends TestCase
{
    /** @test */
    public function it_can_change_in_the_config_file_if_laravel()
    {
        config(['corcel.shortcode_parser' => WordpressParser::class]);

        $post = factory(Post::class)->create();
        $method = new \ReflectionMethod($post, 'getShortcodeHandlerInstance');
        $method->setAccessible(true);
        /** @var ShortcodeFacade $handler */
        $handler = $method->invoke($post);

        $property = new \ReflectionProperty($handler, 'parser');
        $property->setAccessible(true);
        $this->assertInstanceOf(WordpressParser::class, $property->getValue($handler));
    }

    /** @test */
    public function it_can_change_the_parser_in_runtime()
    {
        /** @var Post $post */
        $post = factory(Post::class)->create();
        $post->setShortcodeParser(new WordpressParser());

        $method = new \ReflectionMethod($post, 'getShortcodeHandlerInstance');
        $method->setAccessible(true);
        // Forcing Corcel::isLaravel() to return false
        $mock = \Mockery::mock(Container::class);
        $mock->shouldReceive('version')->andReturn('foo');
        Container::setInstance($mock);

        /** @var ShortcodeFacade $handler */
        $handler = $method->invoke($post);

        $property = new \ReflectionProperty($handler, 'parser');
        $property->setAccessible(true);
        $this->assertInstanceOf(WordpressParser::class, $property->getValue($handler));
    }
}
