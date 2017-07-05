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

        $this->assertEquals($attachment->title, $attachment->post_title);
        $this->assertEquals($attachment->url, $attachment->guid);
        $this->assertEquals($attachment->type, $attachment->post_mime_type);
        $this->assertEquals($attachment->description, $attachment->post_content);
        $this->assertEquals($attachment->caption, $attachment->post_excerpt);
        $this->assertEquals($attachment->alt, $attachment->meta->_wp_attachment_image_alt);
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
