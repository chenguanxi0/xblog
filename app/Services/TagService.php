<?php
/**
 * Created by PhpStorm.
 * User: lufficc
 * Date: 2017/3/18
 * Time: 10:52
 */

namespace App\Services;


use App\Http\Repositories\TagRepository;

class TagService
{
    public $tagRepository;

    /**
     * TagService constructor.
     * @param TagRepository $tagRepository
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function getPosts($tag_name, $page_size = null)
    {
        $tag = $this->tagRepository->get($tag_name);
        if ($page_size == null)
            $page_size = get_config('page_size', 7);
        return $this->tagRepository->pagedPostsByTag($tag, $page_size);
    }

    public function getAll()
    {
        return $this->tagRepository->getAll();
    }
}