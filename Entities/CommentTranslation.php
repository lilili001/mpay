<?php

namespace Modules\Mpay\Entities;

use Illuminate\Database\Eloquent\Model;

class CommentTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'mpay__comment_translations';
}
