<?php

namespace Modules\Mpay\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use Translatable;

    protected $table = 'mpay__comments';
    public $translatedAttributes = [];
    protected $fillable = [];
}
