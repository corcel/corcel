<?php

namespace Corcel\Tests\Unit\Model\Meta;

use Corcel\Model\Meta\UserMeta;
use Corcel\Model\User;

/**
 * Class UserMetaTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class UserMetaTest extends \Corcel\Tests\TestCase
{
    public function test_user_relation()
    {
        $user_meta = factory(UserMeta::class)->create();

        $this->assertInstanceOf(User::class, $user_meta->user);
    }
}
