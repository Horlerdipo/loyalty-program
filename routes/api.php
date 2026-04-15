<?php

use Illuminate\Support\Facades\Route;

Route::middleware('throttle:api')->group(
    base_path('routes/api/routes.php'),
);
