<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Category;
use App\Http\Repositories\CategoryRepository;
use App\Http\Requests;
use Illuminate\Http\Request;
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:categories',
        ]);

        if ($this->categoryRepository->create($request))
            return back()->with('success', '分类' . $request['name'] . '创建成功');
        else
            return back()->with('error', '分类' . $request['name'] . '创建失败');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Category $category
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function edit(Category $category)
    {
        return view('admin.category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Category $category
     * @return mixed
     * @internal param int $id
     */
    public function update(Request $request, Category $category)
    {
        $this->validate($request, [
            'name' => 'required|unique:categories',
        ]);

        if ($this->categoryRepository->update($request, $category)) {
            return redirect()->route('admin.categories')->with('success', '分类' . $request['name'] . '修改成功');
        }

        return back()->withInput()->withErrors('分类' . $request['name'] . '修改失败');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return mixed
     * @internal param int $id
     */
    public function destroy(Category $category)
    {
        if ($category->posts()->withoutGlobalScopes()->count() > 0) {
            return redirect()->route('admin.categories')->withErrors($category->name . '下面有文章，不能刪除');
        }
        $this->categoryRepository->clearCache();
        if ($category->delete())
            return back()->with('success', $category->name . '刪除成功');
        return back()->withErrors($category->name . '刪除失败');
    }
}
