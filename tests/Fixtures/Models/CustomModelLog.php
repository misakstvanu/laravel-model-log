<?php

namespace Misakstvanu\ModelLog\Tests\Fixtures\Models;

use Misakstvanu\ModelLog\Models\ModelLog;

class CustomModelLog extends ModelLog
{
    protected $table = 'custom_model_logs';
}
