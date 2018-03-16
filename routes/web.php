<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

// Site route
Route::get('/', ['uses' => 'HomeController@index', 'as' => 'index']);
Route::get('/projects', ['uses' => 'HomeController@projects', 'as' => 'projects']);
Route::get('/search', ['uses' => 'HomeController@search', 'as' => 'search']);
Route::get('/achieve', ['uses' => 'HomeController@achieve', 'as' => 'achieve']);

// User Auth
Auth::routes();
Route::get('/auth/github', ['uses' => 'Auth\AuthController@redirectToGithub', 'as' => 'github.login']);
Route::get('/auth/github/callback', ['uses' => 'Auth\AuthController@handleGithubCallback', 'as' => 'github.callback']);
Route::get('/github/register', ['uses' => 'Auth\AuthController@registerFromGithub', 'as' => 'github.register']);
Route::post('/github/store', ['uses' => 'Auth\AuthController@store', 'as' => 'github.store']);

// User
Route::get('/user/{name}', ['uses' => 'UserController@show', 'as' => 'user.show']);
Route::get('/notifications', ['uses' => 'UserController@notifications', 'as' => 'user.notifications']);
Route::get('/readNotification/{id}', ['uses' => 'UserController@readNotification', 'as' => 'user.readNotification']);
Route::patch('/user/upload/avatar', ['uses' => 'UserController@uploadAvatar', 'as' => 'user.upload.avatar']);
Route::patch('/user/upload/profile', ['uses' => 'UserController@uploadProfile', 'as' => 'user.upload.profile']);
Route::patch('/user/upload/info', ['uses' => 'UserController@update', 'as' => 'user.update.info']);


// Post
Route::get('/blog', ['uses' => 'PostController@index', 'as' => 'post.index']);
Route::get('/blog/{slug}', ['uses' => 'PostController@show', 'as' => 'post.show']);

// Category
Route::get('/category/{name}', ['uses' => 'CategoryController@show', 'as' => 'category.show']);
Route::get('/category', ['uses' => 'CategoryController@index', 'as' => 'category.index']);

// Tag
Route::get('/tag/{name}', ['uses' => 'TagController@show', 'as' => 'tag.show']);
Route::get('/tag', ['uses' => 'TagController@index', 'as' => 'tag.index']);

// Comment
Route::get('/commentable/{commentable_id}/comments', ['uses' => 'CommentController@show', 'as' => 'comment.show']);
Route::get('comment/{comment}', ['uses' => 'CommentController@edit', 'as' => 'comment.edit']);

// SiteMap
Route::get('sitemap', 'GeneratedController@index');
Route::get('sitemap.xml', 'GeneratedController@index');

// Feed
Route::get('feed.xml', 'GeneratedController@feed')->name('feed');

// Comment
Route::resource('comment', 'CommentController', ['only' => ['store', 'destroy', 'edit', 'update']]);

/*
 * must last
 */
Route::get('/{name}', ['uses' => 'PageController@show', 'as' => 'page.show']);