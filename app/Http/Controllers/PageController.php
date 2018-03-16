<?php

namespace App\Http\Controllers;

use App\Http\Repositories\CommentRepository;
use App\Http\Repositories\PageRepository;
use App\Http\Requests;
use Lufficc\Page\PageHelper;

class PageController extends Controller
{
    use PageHelper;
    protected $pageRepository;
    protected $commentRepository;

    /**
     * PageController constructor.
     * @param PageRepository $pageRepository
     * @param CommentRepository $commentRepository
     */
    public function __construct(PageRepository $pageRepository, CommentRepository $commentRepository)
    {
        $this->pageRepository = $pageRepository;
        $this->commentRepository = $commentRepository;
    }

    public function show($name)
    {
        $page = $this->pageRepository->get($name);
        if (!$this->shouldShow($page)) {
            abort(404);
        }
        $this->onPageShowing($page);
        $comments = $this->commentRepository->getByCommentable('App\Page', $page->id);
        return view('page.show', compact('page', 'comments'));
    }

    private function onPageShowing($page)
    {
        if (auth()->check()) {
            $user = auth()->user();
            $unreadNotifications = $user->unreadNotifications;
            foreach ($unreadNotifications as $notifications) {
                $comment = $notifications->data;
                if ($comment['commentable_type'] == 'App\Page' && $comment['commentable_id'] == $page->id) {
                    $notifications->markAsRead();
                }
            }
        }
    }
}
