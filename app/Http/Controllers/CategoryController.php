<?php

namespace App\Http\Controllers;

use App\Http\Repositories\CategoryRepository;
use App\Http\Requests;
use XblogConfig;

class CategoryController extends Controller
{
    protected $categoryRepository;

    /**
     * CategoryController constructor.
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index()
    {
        return view('category.index');
    }

    /**
     * Display the specified resource.
     *
     * @param $name
     * @return \Illuminate\Http\Response
     * @internal param Category $category
     * @internal param int $id
     */
    public function show($name)
    {
        $category = $this->categoryRepository->get($name);
        $page_size = $page_size = get_config('page_size', 7);
        $posts = $this->categoryRepository->pagedPostsByCategory($category, $page_size);
        return view('category.show', compact('posts', 'name'));
    }
}
