<?php

use Corcel\Attachment;
use Corcel\Post;
use Corcel\ThumbnailMeta;

/**
 * Class ThumbnailTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class ThumbnailTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function its_meta_belongs_to_post()
    {
        $meta = factory(ThumbnailMeta::class)->create();

        $this->assertInstanceOf(Post::class, $meta->post);
    }
    
    /**
     * @test
     */
    public function its_post_has_thumbnail_relation()
    {
        $meta = factory(ThumbnailMeta::class)->create();

        $post = $meta->post;

        $this->assertInstanceOf(ThumbnailMeta::class, $post->thumbnail);
    }

    /**
     * @test
     */
    public function it_has_an_attachment()
    {
        $meta = $this->createThumbnailMetaWithAttachment();

        $this->assertInstanceOf(Attachment::class, $meta->attachment);
        $this->assertTrue(is_string($meta->post->image));
    }

    /**
     * @test
     */
    public function its_post_thumbnail_attachment_url_is_valid()
    {
        $post = $this->createPostWithThumbnail();

        $this->assertEquals('http://google.com', $post->thumbnail->attachment->url);
    }

    /**
     * @test
     */
    public function it_has_different_sizes()
    {
        $meta = $this->createThumbnailMetaWithAttachment();

        $thumbnail = $meta->size(ThumbnailMeta::SIZE_THUMBNAIL);

        $this->assertEquals('foobar.jpg', $thumbnail['file']);
        $this->assertEquals('http://example.com/foobar.jpg', $thumbnail['url']);
        $this->assertEquals(150, $thumbnail['width']);
        $this->assertEquals(150, $thumbnail['height']);
        $this->assertEquals('image/jpeg', $thumbnail['mime-type']);
    }

    /**
     * @return ThumbnailMeta
     */
    private function createThumbnailMetaWithAttachment()
    {
        $attachment = factory(Attachment::class)->create();
        $this->saveThumbnailSizes($attachment);

        $meta = factory(ThumbnailMeta::class)->create([
            'meta_value' => $attachment->ID,
        ]);

        $meta->attachment()->associate($attachment);

        return $meta;
    }

    /**
     * @return Post
     */
    private function createPostWithThumbnail()
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

    /**
     * @param Attachment $attachment
     * @return Attachment
     */
    private function saveThumbnailSizes(Attachment $attachment)
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
