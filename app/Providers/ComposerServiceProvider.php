<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('widget.categories', 'App\Http\ViewComposers\CategoriesComposer');

        View::composer('widget.hot_posts', 'App\Http\ViewComposers\HotPostsComposer');

        View::composer('widget.tags', 'App\Http\ViewComposers\TagsComposer');

        View::composer(['index', 'layouts.header'], 'App\Http\ViewComposers\PagesComposer');

        View::composer('*', 'App\Http\ViewComposers\SettingsComposer');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
