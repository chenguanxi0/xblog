<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Comment;
use App\Http\Repositories\CommentRepository;
use App\Http\Repositories\PostRepository;
use App\Http\Requests;
use App\Scopes\VerifiedCommentScope;
use Gate;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected $commentRepository;
    protected $postRepository;

    public function __construct(CommentRepository $commentRepository, PostRepository $postRepository)
    {
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
    }

    public function restore($comment_id)
    {
        $comment = $this->findComment($comment_id);

        $this->checkPolicy('restore', $comment);

        if ($comment->trashed()) {
            $comment->restore();
            $this->commentRepository->clearAllCache();
            return redirect()->route('admin.comments')->with('success', '恢复成功');
        }
        return redirect()->route('admin.comments')->withErrors('恢复失败');
    }

    public function verify($comment_id)
    {
        $comment = $this->findComment($comment_id);
        $msg = 'Verified successfully.';
        if ($comment->isVerified()) {
            $msg = 'UNVerified successfully.';
            $comment->status = 0;
        } else {
            $comment->status = 1;
        }
        if ($comment->save()) {
            $this->commentRepository->clearAllCache();
            return back()->with('success', $msg);
        }
        return back()->withErrors('Action failed.');
    }

    protected function findComment($id)
    {
        return Comment::withoutGlobalScopes()->findOrFail($id);
    }

    protected function deleteUnVerifiedComments()
    {
        $result = Comment::withoutGlobalScope(VerifiedCommentScope::class)->where('status', 0)->forceDelete();
        $this->commentRepository->clearAllCache();
        return back()->with('success', "Delete $result comments.");
    }
}
