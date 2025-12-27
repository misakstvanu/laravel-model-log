# Laravel Model Log Package - AI Coding Guidelines

## Project Overview
This Laravel package provides automatic logging of Eloquent model changes for audit trails. It captures create, update, delete, restore, and force delete operations with before/after values, batching all saves to the end of the request.

## Architecture
- **Service Provider** (`src/ModelLogServiceProvider.php`): Registers config, publishes migrations/config, sets up terminating callback for batch saving
- **Loggable Trait** (`src/Traits/Loggable.php`): Added to models to enable logging via Eloquent events
- **ModelLog Model** (`src/Models/ModelLog.php`): Handles log collection and batch insertion
- **Migration** (`database/migrations/create_model_logs_table.php.stub`): Creates `model_logs` table
- **Config** (`config/model-log.php`): Controls which models/attributes to log, user tracking

## Key Patterns
- **Batch Logging**: Uses static collection in `ModelLog::collectLog()` and saves via `app->terminating()` callback
- **Event-Driven**: Boot method in trait registers listeners for `created`, `updated`, `deleted`, `restored`, `forceDeleted`
- **Configurable Filtering**: Checks `config('model-log.models')` for include/exclude lists
- **Attribute Exclusion**: Filters out sensitive fields like passwords via `config('model-log.excluded_attributes')`
- **User Tracking**: Optionally captures `Auth::id()` for change attribution

## Development Workflow
- **Setup**: `composer install`, publish assets with `php artisan vendor:publish --provider="Misakstvanu\ModelLog\ModelLogServiceProvider"`
- **Database**: `php artisan migrate` creates logs table
- **Testing**: Use Orchestra Testbench for package testing
- **Publishing**: Tag releases with semantic versioning

## Code Conventions
- PSR-4 autoloading in `composer.json`
- Laravel service provider registration in `composer.json` extra
- Use `static::bootLoggable()` for trait initialization
- Handle soft deletes with `trashed()` check and `restored`/`forceDeleted` events
- JSON cast for old_values/new_values in ModelLog model

## Integration Points
- Integrates with Laravel authentication for user tracking
- Compatible with soft deletes (`Illuminate\Database\Eloquent\SoftDeletes`)
- Uses Laravel's terminating callbacks for efficient batch inserts
- Supports custom user models via config

## Common Tasks
- Adding logging to models: Add `use Loggable;` trait
- Customizing logged fields: Modify `excluded_attributes` in config
- Filtering models: Set `models.include` or `models.exclude` in config
- Querying logs: Use `ModelLog::where('model_class', User::class)->get()`