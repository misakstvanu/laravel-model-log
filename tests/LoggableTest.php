<?php

namespace Misakstvanu\ModelLog\Tests;

use Illuminate\Support\Carbon;
use Misakstvanu\ModelLog\Models\ModelLog;
use Misakstvanu\ModelLog\Tests\Fixtures\Models\Article;

class LoggableTest extends TestCase
{
    public function test_it_logs_create_and_update_operations(): void
    {
        $article = Article::create([
            'title' => 'First',
            'published_at' => '2026-01-10 12:30:00',
        ]);

        $article->update([
            'title' => 'Second',
        ]);

        ModelLog::saveCollectedLogs();

        $logs = ModelLog::query()->orderBy('id')->get();

        $this->assertCount(2, $logs);
        $this->assertSame('create', $logs[0]->operation);
        $this->assertSame('update', $logs[1]->operation);
    }

    public function test_datetime_normalization_can_be_disabled(): void
    {
        config()->set('model-log.normalize_datetime_to_db_format', false);

        ModelLog::saveCollectedLogs();

        $oldValue = Carbon::parse('2026-01-10T12:30:00+00:00')->toISOString();
        $newValue = Carbon::parse('2026-01-10 12:30:00', 'UTC')->format('Y-m-d H:i:s');

        ModelLog::collectLog(
            Article::class,
            1,
            'update',
            ['published_at' => $oldValue],
            ['published_at' => $newValue]
        );

        ModelLog::saveCollectedLogs();

        $log = ModelLog::query()->latest('id')->firstOrFail();

        $oldValues = json_decode($log->getRawOriginal('old_values'), true);
        $newValues = json_decode($log->getRawOriginal('new_values'), true);

        $this->assertSame($oldValue, $oldValues['published_at']);
        $this->assertSame($newValue, $newValues['published_at']);
    }

    public function test_datetime_normalization_can_be_enabled(): void
    {
        config()->set('model-log.normalize_datetime_to_db_format', true);

        ModelLog::saveCollectedLogs();

        ModelLog::collectLog(
            Article::class,
            1,
            'update',
            ['published_at' => '2026-01-10T12:30:00+00:00'],
            ['published_at' => '2026-01-10 12:30:00']
        );

        ModelLog::saveCollectedLogs();

        $log = ModelLog::query()->latest('id')->firstOrFail();

        $oldValues = json_decode($log->getRawOriginal('old_values'), true);
        $newValues = json_decode($log->getRawOriginal('new_values'), true);

        $this->assertSame('2026-01-10 12:30:00', $oldValues['published_at']);
        $this->assertSame('2026-01-10 12:30:00', $newValues['published_at']);
    }
}
