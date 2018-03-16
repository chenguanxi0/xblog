<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Repositories\CategoryRepository;
use App\Http\Repositories\CommentRepository;
use App\Http\Repositories\PostRepository;
use App\Http\Repositories\TagRepository;
use App\Http\Requests;
use App\Notifications\UserRegistered;
use App\Post;
use Carbon\Carbon;
use Chumper\Zipper\Facades\Zipper;
use File;
use Gate;
use Illuminate\Http\Request;
use League\HTMLToMarkdown\HtmlConverter;
use XblogConfig;

class PostController extends Controller
{
    protected $postRepository;
    protected $tagRepository;
    protected $categoryRepository;
    protected $commentRepository;

    /**
     * PostController constructor.
     * @param PostRepository $postRepository
     * @param CategoryRepository $categoryRepository
     * @param TagRepository $tagRepository
     * @param CommentRepository $commentRepository
     */
    public function __construct(PostRepository $postRepository,
                                CategoryRepository $categoryRepository,
                                TagRepository $tagRepository,
                                CommentRepository $commentRepository)
    {
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;
        $this->commentRepository = $commentRepository;
    }


    public function create()
    {
        return view('admin.post.create',
            [
                'categories' => $this->categoryRepository->getAll(),
                'tags' => $this->tagRepository->getAll(),
            ]
        );
    }

    public function store(Request $request)
    {
        $this->validatePostForm($request);

        if ($this->postRepository->create($request))
            return redirect('admin/posts')->with('success', '文章' . $request['name'] . '创建成功');
        else
            return redirect('admin/posts')->withErrors('文章' . $request['name'] . '创建失败');
    }

    public function preview($slug)
    {
        $post = Post::withoutGlobalScopes()->where('slug', $slug)->with('tags')->first();
        if (!$post)
            abort(404);
        $preview = true;
        return view('post.show', compact('post', 'preview'));
    }

    public function publish($id)
    {
        $post = Post::withoutGlobalScopes()->find($id);
        if ($post->trashed()) {
            return back()->withErrors($post->title . '发布失败，请先恢复删除');
        }
        $this->clearAllCache();
        if ($post->status == 0) {
            $post->status = 1;
            $post->published_at = Carbon::now();
            if ($post->save())
                return back()->with('success', $post->title . '发布成功');
        } else if ($post->status == 1) {
            $post->status = 0;
            if ($post->save())
                return back()->with('success', $post->title . '撤销发布成功');
        }
        return back()->withErrors($post->title . '操作失败');
    }


    public function edit($id)
    {
        $post = Post::withoutGlobalScopes()->find($id);

        $this->checkPolicy('update', $post);

        $post->description = (new HtmlConverter(['header_style' => 'atx']))->convert($post->description);

        return view('admin.post.edit', [
            'post' => $post,
            'categories' => $this->categoryRepository->getAll(),
            'tags' => $this->tagRepository->getAll(),
        ]);
    }


    public function update(Request $request, $id)
    {
        $post = Post::withoutGlobalScopes()->find($id);
        $this->checkPolicy('update', $post);
        $this->validatePostForm($request, true);

        if ($this->postRepository->update($request, $post)) {
            return redirect('admin/posts')->with('success', '文章' . $request['name'] . '修改成功');
        } else
            return redirect('admin/posts')->withErrors('文章' . $request['name'] . '修改失败');
    }

    public function download($id)
    {
        $post = Post::withoutGlobalScopes()->where('id', $id)->with(['tags', 'category'])->first();
        $info = $this->getPostContent($post);
        return response($info, 200,
            [
                "Content-Type" => 'application/force-download',
                'Content-Disposition' => "attachment; filename=\"" . $post->title . ".md\""
            ]
        );
    }

    public function downloadAll()
    {
        $path = storage_path('post' . DIRECTORY_SEPARATOR . 'all.zip');
        if (File::exists($path)) {
            File::delete($path);
        }
        $zipper = Zipper::make($path);
        foreach (Post::withoutGlobalScopes()->get() as $post) {
            $zipper->folder('posts')->addString($post->title . '.md', $this->getPostContent($post));
        }
        $zipper->close();
        return response()->download($path);
    }

    private function getPostContent(Post $post)
    {
        $info = "title: " . $post->title;
        $info = $info . "\ndate: " . $post->created_at->format('Y-m-d H:i');
        $info = $info . "\npermalink: " . $post->slug;
        $info = $info . "\ncategory: " . $post->category->name;
        $info = $info . "\ntags:\n";
        foreach ($post->tags as $tag) {
            $info = $info . "- $tag->name\n";
        }
        $info = $info . "---\n\n" . $post->content;
        return $info;
    }

    public function restore($id)
    {
        $post = Post::withoutGlobalScopes()->findOrFail($id);
        if ($post->trashed()) {
            $post->restore();
            $this->clearAllCache();
            return redirect()->route('admin.posts')->with('success', '恢复成功');
        }
        return redirect()->route('admin.posts')->withErrors('恢复失败');
    }


    public function destroy($id)
    {
        $post = Post::withoutGlobalScopes()->findOrFail($id);
        $redirect = route('admin.posts');
        if (request()->has('redirect'))
            $redirect = request()->input('redirect');

        if ($post->trashed()) {
            $result = $post->forceDelete();
        } else {
            $result = $post->delete();
        }
        if ($result) {
            $this->clearAllCache();
            return redirect($redirect)->with('success', '删除成功');
        } else
            return redirect($redirect)->withErrors('删除失败');
    }

    private function validatePostForm(Request $request, $update = false)
    {
        $v = [
            'title' => 'required',
            'description' => 'required',
            'category_id' => 'required',
            'content' => 'required',
        ];
        if (!$update)
            $v = array_merge($v, ['slug' => 'required|unique:posts']);
        $this->validate($request, $v);
    }

    public function clearAllCache()
    {
        $this->postRepository->clearAllCache();
    }

    public function updateConfig(Request $request, $id)
    {
        $post = Post::withoutGlobalScopes()->findOrFail($id);
        if ($post->saveConfig($request->all()))
            return $this->succeedJsonMessage('Update configure successfully');
        return $this->failedJsonMessage('Update Configure failed');
    }
}
