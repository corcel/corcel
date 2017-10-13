<?php

namespace Corcel\Laravel\Controllers;

use Corcel\Laravel\Resource\Post as PostResource;
use Corcel\Model\Post;

/**
 * Class PostsController
 *
 * @package Corcel\Laravel\Controllers
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class PostsController extends Controller
{
    /**
     * @var Post
     */
    protected $post;

    /**
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }


    /**
     * @param int $id
     * @return PostResource
     */
    public function show($id)
    {
        $post = $this->post->findOrFail($id);

        return new PostResource($post);
    }
}
