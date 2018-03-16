<?php
/**
 * Created by PhpStorm.
 * User: lufficc
 * Date: 2017/3/18
 * Time: 10:52
 */

namespace App\Services;


use App\Http\Repositories\CategoryRepository;

class CategoryService
{
    protected $categoryRepository;

    /**
     * CategoryService constructor.
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getPosts($category_name, $page_size = null)
    {
        $category = $this->categoryRepository->get($category_name);
        if ($page_size == null)
            $page_size = get_config('page_size', 7);
        return $this->categoryRepository->pagedPostsByCategory($category, $page_size);
    }

    public function getAll()
    {
        return $this->categoryRepository->getAll();
    }
}