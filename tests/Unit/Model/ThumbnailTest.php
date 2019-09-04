<?php

namespace Corcel\Tests\Unit\Model;

use Corcel\Model\Attachment;
use Corcel\Model\Meta\ThumbnailMeta;
use Corcel\Model\Post;

/**
 * Class ThumbnailTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class ThumbnailTest extends \Corcel\Tests\TestCase
{
    public function test_its_meta_belongs_to_post()
    {
        $meta = factory(ThumbnailMeta::class)->create();

        $this->assertInstanceOf(Post::class, $meta->post);
    }
    
    public function test_its_post_has_thumbnail_relation()
    {
        $meta = factory(ThumbnailMeta::class)->create();

        $post = $meta->post;

        $this->assertInstanceOf(ThumbnailMeta::class, $post->thumbnail);
    }

    public function test_it_has_an_attachment()
    {
        $meta = $this->createThumbnailMetaWithAttachment();

        $this->assertInstanceOf(Attachment::class, $meta->attachment);
        $this->assertIsString($meta->post->image);
    }

    public function test_its_post_thumbnail_attachment_url_is_valid()
    {
        $post = $this->createPostWithThumbnail();

        $this->assertEquals('http://google.com', $post->thumbnail->attachment->url);
    }

    public function test_it_has_different_sizes()
    {
        $meta = $this->createThumbnailMetaWithAttachment();

        $thumbnail = $meta->size(ThumbnailMeta::SIZE_THUMBNAIL);

        $this->assertEquals('foobar.jpg', $thumbnail['file']);
        $this->assertEquals('http://example.com/foobar.jpg', $thumbnail['url']);
        $this->assertEquals(150, $thumbnail['width']);
        $this->assertEquals(150, $thumbnail['height']);
        $this->assertEquals('image/jpeg', $thumbnail['mime-type']);
    }

    public function test_it_returns_full_size_for_unknown_size()
    {
        $meta = $this->createThumbnailMetaWithAttachment();

        $fullSize = $meta->size(ThumbnailMeta::SIZE_FULL);
        $unknownSize = $meta->size('unknown');

        $this->assertEquals($fullSize, $unknownSize);
    }

    private function createThumbnailMetaWithAttachment(): ThumbnailMeta
    {
        $attachment = factory(Attachment::class)->create();
        $this->saveThumbnailSizes($attachment);

        $meta = factory(ThumbnailMeta::class)->create([
            'meta_value' => $attachment->ID,
        ]);

        $meta->attachment()->associate($attachment);

        return $meta;
    }

    private function createPostWithThumbnail(): Post
    {
        $thumbnail = factory(ThumbnailMeta::class)->create([
            'meta_value' => function () {
                return factory(Attachment::class)->create([
                    'guid' => 'http://google.com',
                ])->ID;
            },
        ]);

        return $thumbnail->post;
    }

    private function saveThumbnailSizes(Attachment $attachment): Attachment
    {
        $attachment->saveMeta('_wp_attachment_metadata', serialize([
            'sizes' => [
                'thumbnail' => [
                    'file' => 'foobar.jpg',
                    'width' => 150,
                    'height' => 150,
                    'mime-type' => 'image/jpeg',
                ],
            ],
        ]));

        return $attachment;
    }
}
