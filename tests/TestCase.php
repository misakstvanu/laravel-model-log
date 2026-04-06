<?php

namespace Misakstvanu\ModelLog\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Misakstvanu\ModelLog\ModelLogServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabaseSchema();
    }

    protected function getPackageProviders($app): array
    {
        return [
            ModelLogServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        $app['config']->set('model-log.track_users', false);
    }

    protected function setUpDatabaseSchema(): void
    {
        Schema::dropIfExists('model_logs');
        Schema::create('model_logs', function (Blueprint $table) {
            $table->id();
            $table->string('model_class');
            $table->unsignedBigInteger('model_id');
            $table->string('operation');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->index(['model_class', 'model_id']);
            $table->index('operation');
            $table->index('user_id');
        });

        Schema::dropIfExists('articles');
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->dateTime('published_at')->nullable();
            $table->timestamps();
        });
    }
}
