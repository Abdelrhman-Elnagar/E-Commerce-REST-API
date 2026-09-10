<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', fn() => response()->json(['message' => 'API Only']));

Route::get('/', function () {
    return response()->json([
        'message' => 'E-Commerce API is running...',
        'version' => '1.0'
    ]);
});
