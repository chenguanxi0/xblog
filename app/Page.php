<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Lufficc\Comment\CommentHelper;
use Lufficc\Config\ConfigureHelper;

class Page extends Model
{
    protected $fillable = ['name', 'display_name', 'content', 'html_content'];

    use CommentHelper, ConfigureHelper;

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function configuration()
    {
        return $this->morphOne(Configuration::class, 'configurable');
    }

    /**
     * @return array
     */
    public function getConfigKeys()
    {
        return ['allow_resource_comment', 'comment_type', 'comment_info', 'display', 'sort_order'];
    }
}
