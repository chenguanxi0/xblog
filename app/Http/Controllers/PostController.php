<?php

namespace App\Http\Controllers;

use App\Http\Repositories\CommentRepository;
use App\Http\Repositories\PostRepository;
use App\Http\Requests;
use App\Notifications\UserRegistered;
use Gate;
use XblogConfig;

class PostController extends Controller
{
    protected $postRepository;
    protected $commentRepository;

    /**
     * PostController constructor.
     * @param PostRepository $postRepository
     * @param CommentRepository $commentRepository
     */
    public function __construct(PostRepository $postRepository, CommentRepository $commentRepository)
    {
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
    }


    public function index()
    {
        $page_size = get_config('page_size', 7);
        $posts = $this->postRepository->pagedPosts($page_size);
        return view('post.index', compact('posts'));
    }

    public function show($slug)
    {
        $post = get_post($slug);
        $recommendedPosts = $this->postRepository->recommendedPosts($post);
        $comments = $this->commentRepository->getByCommentable('App\Post', $post->id);
        return view('post.show', compact('post', 'comments', 'recommendedPosts'));
    }
}
