<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::match(['get', 'post'], 'register', [UserController::class, 'register']);
Route::match(['get', 'post'], 'login', [UserController::class, 'login']);
Route::match(['get', 'post'], 'logout', [UserController::class, 'logout']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('posts')->group(function () {
        Route::match(['get', 'post'], '/post_list', [ApiController::class, 'post_list']);
        Route::match(['get', 'post'], '/post_create', [ApiController::class, 'post_create']);
        Route::match(['get', 'post'], '/post_update', [ApiController::class, 'post_update']);
        Route::match(['get', 'post'], '/post_show', [ApiController::class, 'post_show']);
    });

    Route::match(['get', 'post'], 'comment_create', [ApiController::class, 'comment_create']);
    Route::match(['get', 'post'], 'reply_create', [ApiController::class, 'reply_create']);
    Route::match(['get', 'post'], 'status', [ApiController::class, 'status']);

    Route::get('verify-email', [UserController::class, '__invokenoti'])
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [UserController::class, '__invokeveri'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [UserController::class, 'verinotistore'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});
