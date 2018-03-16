<?php

namespace App;

use App\Scopes\VerifiedCommentScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed commentable_type
 * @property mixed commentable_id
 */
class Comment extends Model
{
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new VerifiedCommentScope());
    }

    use SoftDeletes;

    protected $fillable = ['content'];
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ip()
    {
        return $this->belongsTo(Ip::class);
    }

    public function commentable()
    {
        return $this->morphTo();
    }

    public function isVerified()
    {
        return $this->status == 1;
    }


    protected $commentableData = [];

    public function getCommentableData()
    {
        if (empty($this->commentableData)) {
            $this->commentableData['deleted'] = false;
            switch ($this->commentable_type) {
                case 'App\Post':
                    $post = app('App\Post')->where('id', $this->commentable_id)->select('title', 'slug')->first();
                    $this->commentableData['type'] = '文章';
                    if ($post == null) {
                        $this->commentableData['deleted'] = true;
                        return $this->commentableData;
                    }
                    $this->commentableData['title'] = $post->title;
                    $this->commentableData['url'] = route('post.show', $post->slug);
                    break;
                case 'App\Page':
                    $page = app('App\Page')->where('id', $this->commentable_id)->select('name', 'display_name')->first();
                    $this->commentableData['type'] = '页面';
                    if ($page == null) {
                        $this->commentableData['deleted'] = true;
                        return $this->commentableData;
                    }
                    $this->commentableData['title'] = $page->display_name;
                    $this->commentableData['url'] = route('page.show', $page->name);
                    break;
            }
        }
        return $this->commentableData;
    }
}
