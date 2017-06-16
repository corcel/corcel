<?php

use Corcel\User;

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

    public function testUserCustomFields()
    {
        $user = User::find(2);
        $this->assertNotEmpty($user->meta);
        $this->assertNotEmpty($user->fields);
        $this->assertEquals($user->getAuthIdentifier(), 2);

        $this->assertTrue($user->meta instanceof \Corcel\UserMetaCollection);
    }

    public function testUpdateCustomFields()
    {
        $user = User::find(2);
        $user->meta->custom_meta1 = 'Hello';
        $user->meta->custom_meta2 = 'world';
        $user->save();

        $user = User::find(2);
        $this->assertEquals($user->meta->custom_meta1, 'Hello');
        $this->assertEquals($user->meta->custom_meta2, 'world');
    }

    public function testInsertCustomFields()
    {
        $user = new User();
        $user->user_login = 'test';
        $user->save();

        $user->meta->custom_meta1 = 'Hallo';
        $user->meta->custom_meta2 = 'Wereld';
        $user->save();

        $user = User::find($user->ID);
        $this->assertEquals($user->meta->custom_meta1, 'Hallo');
        $this->assertEquals($user->meta->custom_meta2, 'Wereld');
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
