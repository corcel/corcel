<?php

use Corcel\Comment;
use Corcel\Post;

/**
 * Class CommentTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class CommentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function comment_has_the_correct_instance()
    {
        $comment = factory(Comment::class)->create();

        $this->assertNotNull($comment);
        $this->assertInstanceOf(Comment::class, $comment);
    }

    /**
     * @test
     */
    public function comment_has_integer_id()
    {
        $comment = factory(Comment::class)->create();

        $this->assertInternalType('integer', $comment->comment_ID);
    }

    /**
     * @test
     */
    public function comment_has_post_relation()
    {
        $comment = factory(Comment::class)->create();

        $this->assertNotNull($post = $comment->post);
        $this->assertInstanceOf(Post::class, $post);
        $this->assertInternalType('integer', $post->ID);
    }

    /**
     * @test
     */
    public function query_post_by_id()
    {
        $post = $this->createPostWithComments();
        $comments = Comment::findByPostId($post->ID);

        $this->assertEquals(2, $comments->count());
        $this->assertInstanceOf(Comment::class, $comments->first());
        $this->assertEquals($post->ID, $comments->first()->post->ID);
    }

    /**
     * @test
     */
    public function comment_has_parent()
    {
        $comment = $this->createCommentWithParent();

        $this->assertInstanceOf(Comment::class, $comment->original);
        $this->assertEquals($comment->comment_parent, $comment->original->comment_ID);
    }

    /**
     * @test
     */
    public function comment_is_approved()
    {
        $comment = factory(Comment::class)->create();

        $this->assertInternalType('boolean', $comment->isApproved());
        $this->assertTrue($comment->isApproved());
    }

    /**
     * @test
     */
    public function comment_can_be_a_reply()
    {
        $comment = $this->createCommentWithReplies();

        $this->assertCount(3, $comment->replies);
        $this->assertInstanceOf(Comment::class, $comment->replies->first());
        $this->assertInternalType('boolean', $comment->replies->first()->isReply());
        $this->assertTrue($comment->replies->first()->isReply());
    }

    /**
     * @test
     */
    public function comment_has_replies()
    {
        $comment = $this->createCommentWithReplies();

        $this->assertTrue($comment->hasReplies());
        $this->assertInternalType('boolean', $comment->hasReplies());
    }

    /**
     * @test
     */
    public function comment_can_have_a_different_database_connection_name()
    {
        $comment = factory(Comment::class)->make();
        $comment->setConnection('foo');
        $comment->save();

        $post = factory(Post::class)->create();
        $comment->post()->associate($post);
        $comment->save();

        $this->assertEquals('foo', $comment->getConnectionName());
        $this->assertEquals('foo', $comment->post->getConnectionName());
    }

    /**
     * @test
     */
    public function comment_can_have_meta_fields()
    {
        $comment = factory(Comment::class)->create();

        $comment->saveField('foo', 'bar');

        $this->assertEquals('bar', $comment->meta->foo);
    }

    /**
     * @test
     */
    public function comment_can_update_meta()
    {
        $comment = factory(Comment::class)->create();
        $comment->saveMeta('foo', 'bar');

        $this->assertEquals('bar', $comment->meta->foo);

        $comment->saveField('foo', 'baz');
        unset($comment->meta); // Force reloading meta relation

        $this->assertEquals('baz', $comment->meta->foo);
    }

    /**
     * @test
     */
    public function comment_has_meta()
    {
        factory(Comment::class)->create()
            ->saveMeta('foo', 'bar');

        $comment = Comment::hasMeta('foo', 'bar')->first();

        $this->assertInstanceOf(Comment::class, $comment);
    }

    /**
     * @return Post
     */
    private function createPostWithComments()
    {
        $post = factory(Post::class)->create();

        $post->comments()->saveMany([
            factory(Comment::class)->make(),
            factory(Comment::class)->make(),
        ]);

        return $post;
    }

    /**
     * @return Comment
     */
    private function createCommentWithParent()
    {
        return factory(Comment::class)->create([
            'comment_parent' => function () {
                return factory(Comment::class)->create()->comment_ID;
            }
        ]);
    }

    /**
     * @return Comment
     */
    private function createCommentWithReplies()
    {
        $comment = factory(Comment::class)->create();

        $comment->replies()->saveMany([
            factory(Comment::class)->make(),
            factory(Comment::class)->make(),
            factory(Comment::class)->make(),
        ]);

        return $comment;
    }
}
