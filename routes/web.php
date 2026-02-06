<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pageController;
use App\Http\Controllers\PostController;

Route::controller(pageController::class)->group(function (){
    Route::get('/', 'home')->name('home');
    Route::get('blog', 'blog')->name('blog');
    Route::get('blog/{post:atributo}', 'post')->name('post');
});

Route::controller(ProfileController::class)->group(function (){
      Route::get('profile.edit', 'edit')->name('profile.edit');
});

Route::redirect('dashboard', 'posts')->name('dashboard');

Route::resource('posts', PostController::class)->middleware(['auth', 'verified'])->except(['show']);

require __DIR__.'/auth.php';

