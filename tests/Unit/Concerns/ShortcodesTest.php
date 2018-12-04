<?php

namespace Corcel\Tests\Unit\Concerns;

use Corcel\Corcel;
use Corcel\Model;
use Corcel\Model\Post;
use Corcel\Tests\TestCase;
use Thunder\Shortcode\Parser\ParserInterface;
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
    public function test_it_can_change_in_the_config_file_if_laravel()
    {
        config(['corcel.shortcode_parser' => WordpressParser::class]);

        $post = factory(Post::class)->create();
        $handler = $this->getHandler($post);
        $value = $this->getParserValue($handler);

        $this->assertInstanceOf(WordpressParser::class, $value);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_it_can_change_the_parser_in_runtime()
    {
        // Force Corcel::isLaravel() returning false
        $mockedCorcel = \Mockery::mock('alias:' . Corcel::class);
        $mockedCorcel->shouldReceive('isLaravel')->andReturn(false);

        /** @var Post $post */
        $post = factory(Post::class)->create();
        $post->setShortcodeParser(new WordpressParser());

        $handler = $this->getHandler($post);
        $value = $this->getParserValue($handler);

        $this->assertInstanceOf(WordpressParser::class, $value);
    }

    private function getHandler(Model $post): ShortcodeFacade
    {
        $method = new \ReflectionMethod($post, 'getShortcodeHandlerInstance');
        $method->setAccessible(true);

        return $method->invoke($post);
    }

    private function getParserValue(ShortcodeFacade $handler): ParserInterface
    {
        $property = new \ReflectionProperty($handler, 'parser');
        $property->setAccessible(true);

        return $property->getValue($handler);
    }
}
