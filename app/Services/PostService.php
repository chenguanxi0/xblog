<?php
/**
 * Created by PhpStorm.
 * User: lufficc
 * Date: 2017/3/18
 * Time: 10:52
 */

namespace App\Services;


use App\Http\Repositories\PostRepository;
use App\Post;
use Lufficc\Post\PostHelper;

class PostService
{
    protected $postRepository;
    use PostHelper;

    /**
     * PostService constructor.
     * @param PostRepository $postRepository
     */
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getPosts($page_size = null)
    {
        if ($page_size == null)
            $page_size = get_config('page_size', 7);
        return $this->postRepository->pagedPosts($page_size);
    }

    public function getAllPosts()
    {
        return $this->postRepository->achieve();
    }

    public function getRecommendedPosts(Post $post, $limit = 5)
    {
        return $this->postRepository->recommendedPosts($post, $limit);
    }

    public function getPost($slug)
    {
        $post = $this->postRepository->get($slug);
        $this->onPostShowing($post);
        return $post;
    }

    public function getCount()
    {
        return Post::count();
    }

    public function getPostById($id)
    {
        return $this->postRepository->find($id);
    }
}