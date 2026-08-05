<?php

namespace Misakstvanu\ModelLog\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Misakstvanu\ModelLog\Traits\Loggable;

class Post extends Model
{
    use Loggable;

    protected $table = 'posts';

    protected $guarded = [];

    public static function getModelLogClass(): string
    {
        return CustomModelLog::class;
    }
}
