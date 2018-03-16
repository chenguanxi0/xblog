<?php

namespace App\Providers;

use App\Comment;
use App\Http\Repositories\MapRepository;
use App\Observers\CommentObserver;
use App\Observers\PageObserver;
use App\Observers\PostObserver;
use App\Page;
use App\Post;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Comment::observe(CommentObserver::class);
        Post::observe(PostObserver::class);
        Page::observe(PageObserver::class);
        view()->share('background_image','/images/bg.jpg');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('XblogConfig', function ($app) {
            return new MapRepository();
        });
    }
}
