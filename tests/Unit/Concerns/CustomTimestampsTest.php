<?php

namespace Corcel\Tests\Unit\Concerns;

use Carbon\Carbon;
use Corcel\Concerns\CustomTimestamps;
use Corcel\Model\User;
use Corcel\Tests\TestCase;

class CustomTimestampsTest extends TestCase
{
    public function test_it_overrides_the_default_timestamps_fields()
    {
        $fake = new FakeUser();
        $fake->setCreatedAt($created_at = Carbon::now()->toDateTimeString());
        $fake->setUpdatedAt($updated_at = Carbon::now()->toDateTimeString());

        $this->assertEquals($created_at, $fake->foo_created);
        $this->assertEquals($created_at, $fake->foo_created_gmt);
        $this->assertEquals($updated_at, $fake->foo_updated_gmt);
        $this->assertEquals($updated_at, $fake->foo_updated_gmt);
    }
}

class FakeUser extends User
{
    const CREATED_AT = 'foo_created';
    const UPDATED_AT = 'foo_updated';

    use CustomTimestamps;
}
