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
use App\Http\Controllers\StripeController;
use App\Http\Controllers\GeminiController;
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




//Auth Verify Email
Route::prefix('auth')->group(function () {
    #route start
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/sendOtp',[UserController::class,'sendOTP']);

    Route::post('/login', [UserController::class, 'login']);
    Route::post('/google-login', [GoogleAuthController::class, 'loginWithGoogleToken']);
    Route::get('/email/verify/{id}/{hash}', [UserController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/resend-verification', [UserController::class, 'resendVerificationEmail']);
    Route::put('/update',[UserController::class,'update'])->middleware(['auth:api']);

    Route::get('/getDetail/{id}',[UserController::class,'detail'])->middleware(['auth:api']);

    //Logout
    Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:api');
});

//Refresh TOKEN
Route::get('/refreshToken',[UserController::class,'refreshToken']);



//Forgot pass
#route start
Route::post('forgot-password', [UserController::class, 'forgotPassword']);
Route::post('reset-password', [UserController::class, 'resetPassword']);


//CRUD category
Route::get('/getListCategory',[CategoryController::class,'listCategory'])->middleware(['auth:api']);


//CRUD blog
Route::post('/createBlog',[BlogController::class,'createBlog'])->middleware(['auth:api']);
Route::put('/updateBlog/{id}',[BlogController::class,'updateBlog'])->middleware(['auth:api']);
Route::delete('/deleteBlog/{id}',[BlogController::class,'destroyBlog'])->middleware(['auth:api']);

#get all blog + search + paginate
Route::get('/getListBlog',[BlogController::class,'listBlog'])->middleware(['auth:api']);
#get blog by ID category + search + paginte
Route::get('/getBlogCategory/{id}',[BlogController::class,'listBlogCategory'])->middleware(['auth:api']);
#get blog by ID user + search + paginate
Route::get('/getBlogUser/{id}',[BlogController::class,'listBlogUser'])->middleware(['auth:api']);
#get deleted blog + search + paginate
Route::get('/getBlogUserDeleted',[BlogController::class,'listBlogUserDeleted'])->middleware(['auth:api']);

#get detail blog by id blog
Route::get('/getDetailBlog/{id}',[BlogController::class,'detailBlog'])->middleware(['auth:api']);


// CRUD comment
Route::post('/createComment',[CommentController::class,'createComment'])->middleware(['auth:api']);
Route::put('/updateComment/{id}',[CommentController::class,'updateComment'])->middleware(['auth:api']);
Route::delete('/deleteComment/{id}',[CommentController::class,'destroyComment'])->middleware(['auth:api']);
#get comment by ID Blog include reply :) 
Route::get('/getCommentBlog/{id}',[CommentController::class,'listCommentBlog'])->middleware(['auth:api']);


//CRUD reply
Route::post('/createReply',[ReplyController::class,'createReply'])->middleware(['auth:api']);
Route::put('/updateReply/{id}',[ReplyController::class,'updateReply'])->middleware(['auth:api']);
Route::delete('/deleteReply/{id}',[ReplyController::class,'destroyReply'])->middleware(['auth:api']);

//CRUD react
Route::post('/likeReaction',[ReactionController::class,'likeReaction'])->middleware(['auth:api']);
Route::post('/dislikeReaction',[ReactionController::class,'dislikeReaction'])->middleware(['auth:api']);
#get Reaction by Blog ID
Route::get('/getReactionBlog/{id}',[ReactionController::class,'listReactionBlog'])->middleware(['auth:api']);


//ADMIN
Route::put('/activeUser',[AdminController::class,'activeUser'])->middleware(['auth:api', 'role:admin']);

#get blog pending + search + paginate
Route::get('/getListPendingBlog',[BlogController::class,'listPendingBlog'])->middleware(['auth:api', 'role:admin']);
// Stripe 
Route::post('/create-checkout-session', [StripeController::class, 'createCheckoutSession']);
Route::get('/checkout-session', [StripeController::class, 'getSessionDetails']);
#get all user
//->middleware(['role:admin'])
Route::get('/getAll', [UserController::class, 'getAll'])->middleware(['auth:api', 'role:admin']);
#category
Route::post('/createCategory',[CategoryController::class,'createCategory'])->middleware(['auth:api', 'role:admin']);
Route::put('/updateCategory/{id}',[CategoryController::class,'updateCategory'])->middleware(['auth:api', 'role:admin']);
Route::delete('/deleteCategory/{id}',[CategoryController::class,'destroyCategory'])->middleware(['auth:api', 'role:admin']);

#change status Blog// 
Route::put('/updateBlogStatus/{id}',[BlogController::class,'updateStatusBlog'])->middleware(['auth:api', 'role:admin']);

// Gemini
Route::post('/chat/{blogId}', [GeminiController::class, 'ChatWithGemini'])->middleware(['auth:api']);
Route::get('/chat/{blogId}', [GeminiController::class, 'GetChatHistory'])->middleware(['auth:api']);