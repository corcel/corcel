<?php

namespace Corcel\Tests\Unit\Model;

use Corcel\Model\Attachment;

/**
 * Class AttachmentTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class AttachmentTest extends \Corcel\Tests\TestCase
{
    public function test_it_has_aliases()
    {
        $attachment = $this->createAttachmentWithMeta();

        $this->assertEquals($attachment->post_title, $attachment->title);
        $this->assertEquals($attachment->guid, $attachment->url);
        $this->assertEquals($attachment->post_mime_type, $attachment->type);
        $this->assertEquals($attachment->post_content, $attachment->description);
        $this->assertEquals($attachment->post_excerpt, $attachment->caption);
        $this->assertEquals($attachment->meta->_wp_attachment_image_alt, $attachment->alt);
    }

    public function test_its_to_array_method_has_all_appends_property_values()
    {
        $attachment = $this->createAttachmentWithMeta();

        $array = $attachment->toArray();

        $this->assertArrayHasKey('title', $array);
        $this->assertArrayHasKey('url', $array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('caption', $array);
        $this->assertArrayHasKey('alt', $array);
    }

    private function createAttachmentWithMeta()
    {
        $attachment = factory(Attachment::class)->create();

        $attachment->saveMeta('_wp_attachment_image_alt', 'foobar');

        return $attachment;
    }
}
