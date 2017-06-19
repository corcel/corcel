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
    public function thumbnail_meta_belongs_to_post()
    {
        $meta = factory(ThumbnailMeta::class)->create();

        $this->assertInstanceOf(Post::class, $meta->post);
    }
    
    /**
     * @test
     */
    public function post_thumbnail_attachment()
    {
        $meta = factory(ThumbnailMeta::class)->create();

        $post = $meta->post;

        $this->assertInstanceOf(ThumbnailMeta::class, $post->thumbnail);
    }

    /**
     * @test
     */
    public function thumbnail_has_an_attachment()
    {
        $meta = $this->createThumbnailMetaWithAttachment();

        $this->assertInstanceOf(Attachment::class, $meta->attachment);
        $this->assertTrue(is_string($meta->post->image));
    }

    /**
     * @test
     */
    public function thumbnail_attachment_url_is_valid()
    {
        $post = $this->createPostWithThumbnail();

        $this->assertEquals('http://google.com', $post->thumbnail->attachment->url);
    }

    /**
     * @test
     */
    public function thumbnail_has_different_sizes()
    {
        $post = $this->createPostWithThumbnail();

        $sizes = $post->thumbnail->attachment->size(ThumbnailMeta::SIZE_FULL);

        //
    }

    /**
     * @return ThumbnailMeta
     */
    private function createThumbnailMetaWithAttachment()
    {
        $attachment = factory(Attachment::class)->create();
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
                // TODO add meta information for this Attachment
                return factory(Attachment::class)->create([
                    'guid' => 'http://google.com',
                ])->ID;
            },
        ]);

        return $thumbnail->post;
    }
}
