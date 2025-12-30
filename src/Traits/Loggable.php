<?php

namespace Misakstvanu\ModelLog\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Misakstvanu\ModelLog\Models\ModelLog;

trait Loggable
{
    public static function bootLoggable(): void
    {
        // Check if this model should be logged
        $config = Config::get('model-log.models', []);
        $include = $config['include'] ?? [];
        $exclude = $config['exclude'] ?? [];

        if (!empty($include) && !in_array(static::class, $include)) {
            return;
        }

        if (!empty($exclude) && in_array(static::class, $exclude)) {
            return;
        }

        static::created(function (Model $model) {
            ModelLog::collectLog(
                get_class($model),
                $model->getKey(),
                'create',
                null,
                $model->getAttributes()
            );
        });

        static::updated(function (Model $model) {
            ModelLog::collectLog(
                get_class($model),
                $model->getKey(),
                'update',
                $model->getOriginal(),
                $model->getAttributes()
            );
        });

        static::deleted(function (Model $model) {
            ModelLog::collectLog(
                get_class($model),
                $model->getKey(),
                'delete',
                $model->getOriginal(),
                null
            );
        });

        if (method_exists(static::class, 'trashed')) {
            static::trashed(function (Model $model) {
                ModelLog::collectLog(
                    get_class($model),
                    $model->getKey(),
                    'soft_delete',
                    $model->getOriginal(),
                    null
                );
            });
        }

        if (method_exists(static::class, 'restored')) {
            static::restored(function (Model $model) {
                ModelLog::collectLog(
                    get_class($model),
                    $model->getKey(),
                    'restore',
                    null,
                    $model->getAttributes()
                );
            });
        }

        if (method_exists(static::class, 'forceDeleted')) {
            static::forceDeleted(function (Model $model) {
                ModelLog::collectLog(
                    get_class($model),
                    $model->getKey(),
                    'force_delete',
                    $model->getOriginal(),
                    null
                );
            });
        }
    }
}
