<?php

use Corcel\Attachment;

/**
 * Class AttachmentTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class AttachmentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_has_aliases()
    {
        $attachment = factory(Attachment::class)->create();

        $this->assertEquals($attachment->post_title, $attachment->title);
        $this->assertEquals($attachment->guid, $attachment->url);
        $this->assertEquals($attachment->post_mime_type, $attachment->type);
        $this->assertEquals($attachment->post_content, $attachment->description);
        $this->assertEquals($attachment->post_excerpt, $attachment->caption);
        $this->assertEquals($attachment->meta->_wp_attachment_image_alt, $attachment->alt);
    }

    /**
     * @test
     */
    public function it_does_not_have_parent_aliases()
    {
        $attachment = factory(Attachment::class)->create();

        $this->assertNull($attachment->status);
    }

    /**
     * @test
     */
    public function its_to_array_method_has_all_appends_property_values()
    {
        $attachment = factory(Attachment::class)->create();

        $array = $attachment->toArray();

        $this->assertArrayHasKey('title', $array);
        $this->assertArrayHasKey('url', $array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('caption', $array);
        $this->assertArrayHasKey('alt', $array);
    }
}
