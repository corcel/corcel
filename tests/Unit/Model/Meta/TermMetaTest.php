<?php

namespace Corcel\Tests\Unit\Model\Meta;

use Corcel\Model\Meta\PostMeta;
use Corcel\Model\Meta\TermMeta;
use Corcel\Model\Post;
use Corcel\Model\Term;

/**
 * Class TermMetaTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class TermMetaTest extends \Corcel\Tests\TestCase
{
    public function test_term_relation()
    {
        $term_meta = factory(TermMeta::class)->create();

        $this->assertInstanceOf(Term::class, $term_meta->term);
    }
}
