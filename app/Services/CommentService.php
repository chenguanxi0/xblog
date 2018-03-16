<?php
/**
 * Created by PhpStorm.
 * User: lufficc
 * Date: 2017/3/18
 * Time: 11:13
 */

namespace App\Services;


use App\Comment;
use App\Http\Repositories\CommentRepository;

class CommentService
{
    protected $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function getComments($commentable_type, $commentable_id)
    {
        return $this->commentRepository->getByCommentable($commentable_type, $commentable_id);
    }

    public function delete($comment_id, $force = false)
    {
        if ($force) {
            $comment = Comment::withTrashed()->findOrFail($comment_id);
        } else {
            $comment = Comment::findOrFail($comment_id);
        }
        return $this->commentRepository->delete($comment, $force);
    }
}