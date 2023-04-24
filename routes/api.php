<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\FamilyMemberController;







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

    //Family Member Route//
    Route::post('/register-family-member', [FamilyMemberController::class, 'register']);
    Route::post('/family-member-login', [FamilyMemberController::class, 'login']);
    


});


