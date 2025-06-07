<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->group(function () {
    // Route để lấy lịch sử chat
    Route::get('/chat/conversation', [ChatController::class, 'getConversation']);

    // Route để gửi tin nhắn mới
    Route::post('/chat/message', [ChatController::class, 'sendMessage']);
});