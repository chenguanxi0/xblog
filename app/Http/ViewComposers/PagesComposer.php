<?php
/**
 * Created by PhpStorm.
 * User: lufficc
 * Date: 2016/8/19
 * Time: 14:41
 */
namespace App\Http\ViewComposers;

use App\Http\Repositories\PageRepository;
use App\Services\PageService;
use Illuminate\View\View;

class PagesComposer
{

    protected $pageService;

    /**
     * Create a new profile composer.
     *
     * @internal param UserRepository $users
     * @param PageService $pageService
     */
    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {
        $pages = $this->pageService->getPages();
        $view->with('pages', $pages);
    }
}