<?php

namespace Misakstvanu\ModelLog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class ModelLog extends Model
{
    protected $table = 'model_logs';

    protected $fillable = [
        'model_class',
        'model_id',
        'operation',
        'old_values',
        'new_values',
        'user_id',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Collected logs to be saved at the end of the request.
     */
    protected static array $collectedLogs = [];

    /**
     * Add a log entry to the collection.
     */
    public static function collectLog(string $modelClass, $modelId, string $operation, ?array $oldValues = null, ?array $newValues = null): void
    {
        $excludedAttributes = Config::get('model-log.excluded_attributes', []);

        // Filter out excluded attributes
        $oldValues = $oldValues ? array_diff_key($oldValues, array_flip($excludedAttributes)) : null;
        $newValues = $newValues ? array_diff_key($newValues, array_flip($excludedAttributes)) : null;

        $userId = null;
        if (Config::get('model-log.track_users', true)) {
            $userId = Auth::id();
        }

        static::$collectedLogs[] = [
            'model_class' => $modelClass,
            'model_id' => $modelId,
            'operation' => $operation,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Save all collected logs to the database.
     */
    public static function saveCollectedLogs(): void
    {
        if (!empty(static::$collectedLogs)) {
            static::insert(static::$collectedLogs);
            static::$collectedLogs = [];
        }
    }
}