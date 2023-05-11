<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\BirthdayWishController;







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
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    
    //User Route//
    Route::post('/login', [UsersController::class, 'login']);

    //Gallery Route//
    Route::get('/gallery', [GalleryController::class, 'index']);


    //Family Head//
    Route::post('/register-family-head', [FamilyController::class, 'register']);
    Route::post('/family-head-login', [FamilyController::class, 'login']);
    Route::post('/send-otp', [FamilyController::class, 'sendOtp']);

    //Family Member Route//
    Route::post('/register-family-member', [FamilyMemberController::class, 'register']);
    Route::post('/family-member-login', [FamilyMemberController::class, 'login']);

    //Category Route//
    Route::get('/all-category', [CategoryController::class, 'index']);
    Route::get('/get-category-by-id/{id}', [CategoryController::class, 'show']);
    Route::post('/create-category', [CategoryController::class, 'store']);
    Route::put('/update-category', [CategoryController::class, 'edit']);
    Route::delete('/delete-category/{id}', [CategoryController::class, 'destroy']);

    //SubCategory Route//
    Route::get('/all-sub-category', [SubCategoryController::class, 'index']);
    Route::get('/get-sub-categories', [SubCategoryController::class, 'getSubCategories']);
    Route::get('/get-sub-category-by-id/{id}', [SubCategoryController::class, 'show']);
    Route::get('/get-sub-category-by-catid/{catid}', [SubCategoryController::class, 'getByCat']);
    Route::post('/create-sub-category', [SubCategoryController::class, 'store']);
    Route::put('/update-sub-category', [SubCategoryController::class, 'edit']);
    Route::delete('/delete-sub-category/{id}', [SubCategoryController::class, 'destroy']);

    //News Route//
    Route::get('/all-news', [NewsController::class, 'index']);
    Route::get('/get-news-by-id/{id}', [NewsController::class, 'show']);

    //News Route//
    Route::get('/all-event', [EventController::class, 'index']);
    Route::get('/get-event-by-id/{id}', [EventController::class, 'show']);
    
    //Quiz Route//
    Route::get('/quiz-details', [QuizController::class, 'getQuizDetails']);

    Route::post('/send-birthday-wish', [BirthdayWishController::class, 'send']);

});


