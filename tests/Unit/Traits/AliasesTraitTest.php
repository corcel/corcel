<?php

namespace Corcel\Tests\Unit\Traits;

use Corcel\Attachment;

/**
 * Class AliasesTraitTest
 *
 * @package Corcel\Tests\Unit\Traits
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class AliasesTraitTest extends \Corcel\Tests\TestCase
{
    /**
     * @test
     */
    public function it_inherits_aliases_from_parent()
    {
        $attachment = factory(Attachment::class)->create([
            'post_status' => 'foo',
            'post_content' => 'bar',
        ]);

        $this->assertNotNull($attachment->status);
        $this->assertNotNull($attachment->content);
        $this->assertNull($attachment->wrong_property);
    }
}
