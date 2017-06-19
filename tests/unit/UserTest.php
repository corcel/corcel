<?php

use Corcel\User;
use Corcel\UserMetaCollection;

/**
 * Class UserTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
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
    public function user_has_multiple_property_aliases()
    {
        $user = factory(User::class)->create();
        $user->saveMeta('nickname', 'foo');
        $user->saveMeta('first_name', 'bar');
        $user->saveMeta('last_name', 'baz');

        $this->assertEquals($user->last_name, 'baz');
        $this->assertEquals($user->user_login, $user->login);
        $this->assertEquals($user->user_email, $user->email);
        $this->assertEquals($user->user_nicename, $user->slug);
        $this->assertEquals($user->user_url, $user->url);
        $this->assertEquals($user->meta->nickname, $user->nickname);
        $this->assertEquals($user->meta->first_name, $user->first_name);
        $this->assertEquals($user->meta->last_name, $user->last_name);
        $this->assertEquals($user->user_registered, $user->created_at);
    }

    /**
     * @test
     */
    public function user_has_the_correct_auth_identifier()
    {
        // TODO $this->assertEquals(21, $user->getAuthIdentifier());
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

    /**
     * @test
     */
    public function user_model_can_have_a_different_connection()
    {
        $user = factory(User::class)->make();
        $user->setConnection('foo');
        $user->save();

        $user->createMeta('fee', 'baz');

        $this->assertEquals('foo', $user->getConnectionName());

        $user->meta->each(function ($meta) {
            $this->assertEquals('foo', $meta->getConnectionName());
        });
    }

    /**
     * @test
     */
    public function user_has_meta_scope_with_empty_meta()
    {
        $id = factory(User::class)->create()->ID;

        $user = (new User())->newQuery()
            ->where('ID', $id)
            ->hasMeta('foo', 'bar')
            ->first();

        $this->assertEmpty($user);
    }

    /**
     * @test
     */
    public function user_has_meta_scope_with_valid_meta()
    {
        $user = factory(User::class)->create();
        $user->saveMeta('foo', 'bar');

        $validUser = (new User())->newQuery()
            ->where('ID', $user->ID)
            ->hasMeta('foo', 'bar')
            ->first();

        $this->assertNotEmpty($validUser);
    }
}
