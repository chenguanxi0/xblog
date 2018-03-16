@extends('layouts.app')
@section('description',$post->description)
@section('keywords',$post->category->name)
@section('title',$post->title)
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2 col-sm-12 phone-no-padding">
                <div class="post-detail">
                    <div class="center-block">
                        <div class="post-detail-title">{{ $post->title }}</div>
                        <div class="post-meta">
                            <span class="post-category">
                           <i class="fa fa-folder-o fa-fw"></i>
                           <a href="{{ route('category.show',$post->category->name) }}">
                           {{ $post->category->name }}
                           </a>
                           </span>
                            <span class="post-comments-count">
                           &nbsp;|&nbsp;
                           <i class="fa fa-comments-o fa-fw" aria-hidden="true"></i>
                           <span>{{ $post->comments_count }}</span>
                           </span>
                            <span>
                           &nbsp;|&nbsp;
                           <i class="fa fa-eye"></i>
                           <span>{{ $post->view_count }}</span>
                           </span>
                            @can('update',$post)
                                <span>
                                    &nbsp;|&nbsp;
                                    <a href="{{ route('post.edit',$post->id) }}">
                                        <i class="fa fa-pencil fa-fw"></i>
                                    </a>
                                </span>
                                <span>
                                    &nbsp;|&nbsp;
                                    <a class="swal-dialog-target"
                                       data-url="{{ route('post.destroy',$post->id) }}"
                                       data-dialog-msg="Delete {{ $post->title }} ?">
                                    <i class="fa fa-trash-o fa-fw"></i>
                                    </a>
                                </span>
                            @endcan
                        </div>
                    </div>
                    <div class="post-detail-content">
                        {!! $post->html_content !!}
                        <p>-- END</p>
                        @include('widget.pay')
                    </div>
                    <div class="post-info-panel">

                        <p class="info">
                            <label class="info-title">创建日期:</label>{{ $post->created_at->format('Y年m月d日') }}
                        </p>
                        @if(isset($post->published_at) && $post->published_at)
                            <p class="info">
                                <label class="info-title">修改日期:</label>{{ $post->published_at->format('Y年m月d日') }}
                            </p>
                        @endif
                        <p class="info">
                            <label class="info-title">文章标签:</label>
                            @foreach($post->tags as $tag)
                                <a class="tag" href="{{ route('tag.show',$tag->name) }}">{{ $tag->name }}</a>
                            @endforeach
                        </p>
                    </div>
                </div>
                @if(isset($recommendedPosts))
                    @include('widget.recommended_posts',['recommendedPosts'=>$recommendedPosts])
                @endif
                @if(!(isset($preview) && $preview) && $post->isShownComment())
                    @include('widget.comment',[
                            'comment_key'=>$post->slug,
                            'comment_title'=>$post->title,
                            'comment_url'=>route('post.show',$post->slug),
                            'commentable'=>$post,
                            'comments'=>isset($comments) ? $comments:[],
                            'redirect'=>request()->fullUrl(),
                             'commentable_type'=>'App\Post'])
                @endif
            </div>
        </div>
    </div>
@endsection