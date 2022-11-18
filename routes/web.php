<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {
    Route::prefix('posts')->group(function () {
        Route::match(['get', 'post'], '/', [PostController::class, 'index'])->name('post-list');
        Route::match(['get', 'post'], '/create', [PostController::class, 'create'])->name('post-create');
        Route::match(['get', 'post'], '/update/{id}', [PostController::class, 'update']);
        Route::match(['get', 'post'], '/delete/{id}', [PostController::class, 'delete']);
        Route::match(['get', 'post'], '/{post}', [PostController::class, 'show']);
    });

    Route::match(['get', 'post'], 'like/{post}', [LikeController::class, 'update']);
    Route::match(['get', 'post'], 'comment/{post}', [CommentController::class, 'create']);
    Route::match(['get', 'post'], 'reply/{comment}', [ReplyController::class, 'commentReply']);
    Route::match(['get', 'post'], 'reply/{comment}/{reply}', [ReplyController::class, 'replyReply']);
});
