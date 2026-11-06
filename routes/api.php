<?php

use App\Http\Controllers\ActivateRoomController;
use App\Http\Controllers\InactivateRoomController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::apiResource('rooms', RoomController::class)->except('destroy');

Route::post('rooms/{room}/activate', ActivateRoomController::class)->name('rooms.activate');
Route::post('rooms/{room}/inactivate', InactivateRoomController::class)->name('rooms.inactivate');
