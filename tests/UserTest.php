<?php

use Corcel\User;

class UserTest extends PHPUnit_Framework_TestCase
{
    public function testUserConstructor()
    {
        $user = new User;
        $this->assertTrue($user instanceof \Corcel\User);
    }

    public function testUserId()
    {
        $user = User::find(1);

        if ($user) {
            $this->assertEquals($user->ID, 1);
            $this->assertEquals($user->getAuthIdentifier(), 1);
        } else {
            $this->assertEquals($user, null);
        }
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
        $user = new User;
        $user->save();

        $user->meta->custom_meta1 = 'Hallo';
        $user->meta->custom_meta2 = 'Wereld';
        $user->save();

        $user = User::find($user->ID);
        $this->assertEquals($user->meta->custom_meta1, 'Hallo');
        $this->assertEquals($user->meta->custom_meta2, 'Wereld');
    }
}