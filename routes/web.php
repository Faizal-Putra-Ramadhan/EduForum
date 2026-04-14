<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ForumController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\LeaderboardController;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/forum', [ForumController::class, 'index'])->name('forum');
    Route::get('/forum/search', [ForumController::class, 'search'])->name('forum.search');
    Route::post('/forum/conversations', [ConversationController::class, 'store'])->name('conversation.start');
    Route::get('/forum/{id}', [ConversationController::class, 'show'])->name('conversation.show');
    Route::post('/forum/group', [ConversationController::class, 'createGroup'])->name('group.create');
    Route::post('/forum/{id}/message', [MessageController::class, 'store'])->name('message.store');
    Route::patch('/forum/{conversation}/read', [MessageController::class, 'markAsRead'])->name('message.read');
    
    Route::get('/leaderboard', [LeaderboardController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
