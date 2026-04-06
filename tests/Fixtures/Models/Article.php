<?php

namespace Misakstvanu\ModelLog\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Misakstvanu\ModelLog\Traits\Loggable;

class Article extends Model
{
    use Loggable;

    protected $table = 'articles';

    protected $guarded = [];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
