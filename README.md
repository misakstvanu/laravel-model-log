# Laravel Model Log

A Laravel package that automatically logs all changes to Eloquent models for audit trails.

## Installation

Install the package via Composer:

```bash
composer require misakstvanu/laravel-model-log
```

Publish the migration and config files:

```bash
php artisan vendor:publish --provider="Misakstvanu\ModelLog\ModelLogServiceProvider" --tag=model-log-migrations
php artisan vendor:publish --provider="Misakstvanu\ModelLog\ModelLogServiceProvider" --tag=model-log-config
```

Run the migration:

```bash
php artisan migrate
```

## Usage

Add the `Loggable` trait to any model you want to log:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Misakstvanu\ModelLog\Traits\Loggable;

class User extends Model
{
    use Loggable;

    // ...
}
```

That's it! All changes to the model will be automatically logged to the `model_logs` table.

## Logged Operations

The package logs the following operations:
- `create`: When a new record is created
- `update`: When an existing record is updated
- `delete`: When a record is soft deleted (if the model uses soft deletes)
- `soft_delete`: When a record is soft deleted
- `restore`: When a soft deleted record is restored
- `force_delete`: When a record is permanently deleted

## Configuration

You can customize the behavior by editing `config/model-log.php`:

- `models.include`: Array of models to log (if empty, all models with the trait are logged)
- `models.exclude`: Array of models to exclude from logging
- `excluded_attributes`: Attributes to exclude from logging (e.g., passwords)
- `track_users`: Whether to track the user who made the change
- `user_model`: Custom user model class

## Database Schema

The `model_logs` table contains:
- `model_class`: The class name of the model
- `model_id`: The primary key of the model
- `operation`: The type of operation
- `old_values`: JSON of the old attribute values (for updates/deletes)
- `new_values`: JSON of the new attribute values (for creates/updates)
- `user_id`: The ID of the user who made the change (nullable)
- `created_at`: Timestamp of the change

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).