<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Loggable Models
    |--------------------------------------------------------------------------
    |
    | Specify which models should be logged. If empty, all models using the
    | Loggable trait will be logged. You can also specify models to exclude.
    |
    */

    'models' => [
        // 'include' => [App\Models\User::class],
        // 'exclude' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Excluded Attributes
    |--------------------------------------------------------------------------
    |
    | Attributes that should not be logged in old_values and new_values.
    | Common examples: passwords, tokens, etc.
    |
    */

    'excluded_attributes' => [
        'password',
        'remember_token',
        'api_token',
    ],

    /*
    |--------------------------------------------------------------------------
    | User Tracking
    |--------------------------------------------------------------------------
    |
    | Whether to track the user who made the change.
    |
    */

    'track_users' => true,

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The user model to use for tracking. Defaults to the authenticated user.
    |
    */

    'user_model' => null,
];