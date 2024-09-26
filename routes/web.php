<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowController;


Route::get('/admin-only',function(){
    // implemeting gate on controller level
//     if(Gate::allows('visitAdminPages')){
//         return 'welcome admin';
//     }
//    return 'unauthorized';

return 'welcome admin';
})->middleware('can:visitAdminPages');


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/',[UserController::class,"showCorrectHomePage"])->name('login');
Route::post('/register',[UserController::class,'registerUser'])->middleware('guest');
Route::post('/login',[UserController::class,'login'])->middleware('guest');
Route::post('/logout',[UserController::class,'logout'])->middleware('mustBeLoggedIn');
Route::get('/manage-avatar',[UserController::class,'showAvatarForm'])->middleware('mustBeLoggedIn');
Route::post('/manage-avatar',[UserController::class,'saveAvatar'])->middleware('mustBeLoggedIn');


//PROFILE RELATED ROUTES
Route::get('/profile/{user:username}',[UserController::class,'profile']);
Route::get('/profile/{user:username}/followers',[UserController::class,'followers']);
Route::get('/profile/{user:username}/following',[UserController::class,'following']);



//post related routes
Route::get('/create-post',[PostController::class,'showCreateForm'])->middleware('mustBeLoggedIn');
Route::post('/create-post',[PostController::class,'savePost'])->middleware('mustBeLoggedIn');
Route::get('/post/{post}',[PostController::class,'getPost'])->middleware('mustBeLoggedIn');
Route::delete('/post/{post}',[PostController::class,'delete'])->middleware('can:delete,post');// adding policy as a middleware
Route::get('/post/{post}/edit',[PostController::class,'showEditForm'])->middleware('can:update,post');
Route::put('/post/{post}',[PostController::class,'updatePost'])->middleware('can:update,post');
Route::get('/search/{term}',[PostController::class,'search']);

//FOLLOW ROUTES

Route::post('/create-follow/{user:username}',[FollowController::class,'createFollow'])->middleware('mustBeLoggedIn');       
Route::post('/remove-follow/{user:username}',[FollowController::class,'removeFollow'])->middleware('mustBeLoggedIn');
