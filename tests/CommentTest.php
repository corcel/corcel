<?php

use Corcel\Comment;

class CommentTest extends PHPUnit_Framework_TestCase
{
    public function testCommentConstructor()
    {
        $comment = new Comment;
        $this->assertTrue($comment instanceof \Corcel\Comment);
    }
    
    public function testCommentId()
    {
        $comment = Comment::find(1);
        
        if ($comment) {
            $this->assertInternalType('integer', $comment->comment_ID);
            $this->assertEquals($comment->comment_ID, 1);
        } else {
            $this->assertNull($comment);
        }
    }
    
    public function testCommentPost()
    {
        $comment = Comment::find(1);
        
        $this->assertTrue($comment->Post()->first() instanceof \Corcel\Post);
        $this->assertEquals($comment->Post()->first()->ID, 1);
    }

    public function testCommentPostId()
    {
        $comments = Comment::findByPostId(1);
        $this->assertEquals(count($comments), 2);

        foreach ($comments as $comment) {
            $this->assertTrue($comment instanceof \Corcel\Comment);
            $this->assertEquals($comment->comment_post_ID, 1);
        }
    }

    public function testOriginal()
    {        
        $comment = Comment::find(2);

        $this->assertTrue($comment->original()->first() instanceof \Corcel\Comment);
        $this->assertEquals($comment->original()->first()->comment_ID, 1);
    }

    public function testCommentApproved()
    {
        $comment = Comment::find(1);

        $this->assertInternalType('boolean', $comment->isApproved());
        $this->assertTrue($comment->isApproved());
    }

    public function testCommentIsReply()
    {
        $comment = Comment::find(2);

        $this->assertInternalType('boolean', $comment->isReply());
        $this->assertTrue($comment->isReply());
    }

    public function testCommentHasReplies()
    {
        $comment = Comment::find(1);

        $this->assertInternalType('boolean', $comment->hasReplies());
        $this->assertTrue($comment->hasReplies());
    }
}