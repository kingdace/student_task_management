<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Home;
use Illuminate\Support\Facades\Route;


// route parameter



Route::get('/', function () {
    return redirect('/app/home');
});

Route::get('/app/{any}', function() {
    return view('welcome');
});

Route::get('/home', [Home::class, 'HomePage'])->name('home');
Route::get('/view_user', [Home::class, 'ViewUserPage'])->name('view_user');
Route::get('/view_user_edit', [Home::class, 'ViewUserEditPage'])->name('view_user_edit');




// Route::get('/check_email/{token}',[AuthController::class,'validEmail']);
