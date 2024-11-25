<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


//check role
//->middleware(['role:admin'])
Route::get('/getAll', [UserController::class, 'getAll'])->middleware(['auth:api', 'refresh.token', 'role:admin']);

//Auth Verify Email
Route::prefix('auth')->group(function () {
    #route start
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::get('/email/verify/{id}/{hash}', [UserController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/resend-verification', [UserController::class, 'resendVerificationEmail']);

    Route::put('/update',[UserController::class,'update'])->middleware(['auth:api', 'refresh.token']);

    Route::get('/getDetail/{id}',[UserController::class,'detail'])->middleware(['auth:api', 'refresh.token']);

    //Logout
    Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:api');
});


//Forgot pass
#route start
Route::post('forgot-password', [UserController::class, 'forgotPassword']);

Route::get('show-form', function () {
    return view('resetPass');
})->name('password.reset');

Route::post('reset-password', [UserController::class, 'resetPassword'])->name('password.update');


//CRUD category
Route::post('/createCategory',[CategoryController::class,'createCategory'])->middleware(['auth:api', 'refresh.token']);
Route::put('/updateCategory/{id}',[CategoryController::class,'updateCategory'])->middleware(['auth:api', 'refresh.token']);
Route::get('/getListCategory',[CategoryController::class,'listCategory'])->middleware(['auth:api', 'refresh.token']);
Route::delete('/deleteCategory/{id}',[CategoryController::class,'destroyCategory'])->middleware(['auth:api', 'refresh.token']);


//CRUD blog
Route::post('/createBlog',[BlogController::class,'createBlog'])->middleware(['auth:api', 'refresh.token']);
Route::put('/updateBlog/{id}',[BlogController::class,'updateBlog'])->middleware(['auth:api', 'refresh.token']);
Route::delete('/deleteBlog/{id}',[BlogController::class,'destroyBlog'])->middleware(['auth:api', 'refresh.token']);
#get all blog
Route::get('/getListBlog',[BlogController::class,'listBlog'])->middleware(['auth:api', 'refresh.token']);
#get blog by ID category
Route::get('/getBlogCategory/{id}',[BlogController::class,'listBlogCategory'])->middleware(['auth:api', 'refresh.token']);
#get blog by ID user
Route::get('/getBlogUser/{id}',[BlogController::class,'listBlogUser'])->middleware(['auth:api', 'refresh.token']);
#get deleted blog
Route::get('/getBlogUserDeleted',[BlogController::class,'listBlogUserDeleted'])->middleware(['auth:api', 'refresh.token']);


// CRUD comment
Route::post('/createComment',[CommentController::class,'createComment'])->middleware(['auth:api', 'refresh.token']);
Route::put('/updateComment/{id}',[CommentController::class,'updateComment'])->middleware(['auth:api', 'refresh.token']);
Route::delete('/deleteComment/{id}',[CommentController::class,'destroyComment'])->middleware(['auth:api', 'refresh.token']);
#get comment by ID Blog include reply :) 
Route::get('/getCommentBlog/{id}',[CommentController::class,'listCommentBlog'])->middleware(['auth:api', 'refresh.token']);


//CRUD reply
Route::post('/createReply',[ReplyController::class,'createReply'])->middleware(['auth:api', 'refresh.token']);
Route::put('/updateReply/{id}',[ReplyController::class,'updateReply'])->middleware(['auth:api', 'refresh.token']);
Route::delete('/deleteReply/{id}',[ReplyController::class,'destroyReply'])->middleware(['auth:api', 'refresh.token']);

//CRUD react
Route::post('/likeReaction',[ReactionController::class,'likeReaction'])->middleware(['auth:api', 'refresh.token']);
Route::post('/dislikeReaction',[ReactionController::class,'dislikeReaction'])->middleware(['auth:api', 'refresh.token']);


//ADMIN
Route::put('/activeUser',[AdminController::class,'activeUser'])->middleware(['auth:api', 'refresh.token', 'role:admin']);

#change status Blog// k cần thiết, chưa viết
Route::put('/updateBlogStatus',[BlogController::class,'updateBlog'])->middleware(['auth:api', 'refresh.token', 'role:admin']);
