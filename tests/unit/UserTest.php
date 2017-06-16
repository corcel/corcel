<?php

use Corcel\User;
use Corcel\UserMetaCollection;

class UserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function user_is_instance_of_corcel_user()
    {
        $user = factory(User::class)->create();

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @test
     */
    public function user_has_the_correct_id()
    {
        $user = factory(User::class)->create(['ID' => 20]);

        $this->assertNotNull($user);
        $this->assertEquals(20, $user->ID);
    }

    /**
     * @test
     */
    public function user_has_the_correct_auth_identifier()
    {
//        $this->assertEquals(21, $user->getAuthIdentifier());
    }

    /**
     * @test
     */
    public function user_can_add_meta()
    {
        $user = factory(User::class)->create();

        $user->saveMeta('foo', 'bar');

        $this->assertNotEmpty($user->meta);
        $this->assertNotEmpty($user->fields);
        $this->assertInstanceOf(UserMetaCollection::class, $user->meta);
    }

    /**
     * @test
     */
    public function user_can_update_meta()
    {
        $user = factory(User::class)->create();

        $user->saveMeta('foo', 'bar');
        $user->saveField('foo', 'baz');

        $this->assertEquals($user->meta->foo, 'baz');
    }

    public function testUserConnection()
    {
        $user = new User();
        $user->setConnection('no_prefix');
        $user->user_login = 'test';
        $user->save();

        $user->meta->active = 1;
        $user->save();

        $this->assertEquals('no_prefix', $user->getConnection()->getName());
        $user->meta->each(function ($meta) {
            $this->assertEquals('no_prefix', $meta->getConnection()->getName());
        });
    }

    public function testUserHasMeta()
    {
        $adm = (new User())->newQuery()
            ->where('id', 1)
            ->hasMeta('nickname', 'adm')
            ->first()
        ;

        $this->assertEmpty($adm);

        $admin = (new User())->newQuery()
            ->where('id', 1)
            ->hasMeta('nickname', 'admin')
            ->first()
        ;

        $this->assertNotEmpty($admin);
    }
}
