<?php

namespace Misakstvanu\ModelLog\Models;

use DateTimeInterface;
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
        $normalizeDatetimeToDbFormat = Config::get('model-log.normalize_datetime_to_db_format', false);

        // Filter out excluded attributes
        $oldValues = $oldValues ? array_diff_key($oldValues, array_flip($excludedAttributes)) : null;
        $newValues = $newValues ? array_diff_key($newValues, array_flip($excludedAttributes)) : null;

        if ($normalizeDatetimeToDbFormat) {
            $oldValues = static::normalizeDateTimeValues($oldValues, $modelClass);
            $newValues = static::normalizeDateTimeValues($newValues, $modelClass);
        }

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

    /**
     * Normalize date and datetime values to the model database format.
     */
    protected static function normalizeDateTimeValues(?array $values, string $modelClass): ?array
    {
        if (!$values || !is_subclass_of($modelClass, Model::class)) {
            return $values;
        }

        $model = new $modelClass;
        $dateFormat = $model->getDateFormat();
        $dateAttributes = method_exists($model, 'getDates')
            ? $model->getDates()
            : [$model->getCreatedAtColumn(), $model->getUpdatedAtColumn()];

        foreach ($model->getCasts() as $attribute => $castType) {
            if (!in_array($castType, ['date', 'datetime', 'immutable_date', 'immutable_datetime'], true)) {
                continue;
            }

            $dateAttributes[] = $attribute;
        }

        $dateAttributes = array_unique(array_filter($dateAttributes));

        foreach ($dateAttributes as $attribute) {
            if (!array_key_exists($attribute, $values) || $values[$attribute] === null) {
                continue;
            }

            try {
                $dateTime = $values[$attribute] instanceof DateTimeInterface
                    ? $values[$attribute]
                    : $model->asDateTime($values[$attribute]);

                $values[$attribute] = $model->fromDateTime($dateTime);
            } catch (\Throwable $e) {
                // Keep original value if it cannot be parsed as date/time.
            }
        }

        return $values;
    }
}
