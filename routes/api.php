<?php

use App\Http\Controllers\API\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\API\Admin\UserController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\NewsController;
use App\Http\Controllers\API\TagController;
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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// ---------------------- CLIENT ----------------------
// News
Route::post('news/list_news_cate', [NewsController::class, 'list_news_cate']);
Route::post('news/list_news_tag', [NewsController::class, 'list_news_tag']);
Route::get('news/latest_news_from_all_categories', [NewsController::class, 'latest_news_from_all_categories']);
Route::get('news/hot_news_in_week', [NewsController::class, 'hot_news_in_week']);
Route::get('news/feature_news', [NewsController::class, 'feature_news']);
Route::post('search/news_base_keyword', [NewsController::class, 'news_base_keyword']);
//---//
Route::post('news/init_cate_news', [CategoryController::class, 'init_cate_news']);
Route::post('news/change_cate_news', [CategoryController::class, 'change_cate_news']);
Route::post('news/hover_change_header_cate_news', [CategoryController::class, 'hover_change_header_cate_news']);

//Categories
Route::get('categories/news_basein_cate/{category}', [CategoryController::class, 'news_basein_cate']);
Route::get('categories/get_all_categories', [CategoryController::class, 'get_all_categories']);

//Tags
Route::get('tags/random_tags', [TagController::class, 'random_tags']);
Route::get('tags/news_base_tag/{tag}', [TagController::class, 'news_base_tag']);

//Resouces
Route::apiResource('/categories', CategoryController::class);
Route::apiResource('/news', NewsController::class);

// ---------------------- CLIENT ----------------------


// ---------------------- ADMIN ----------------------

//Session
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth',

], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('user-profile', [AuthController::class, 'userProfile']);
    Route::post('update_avatar', [AuthController::class, 'update_avatar']);
});

Route::group(['prefix' => 'admin', 'middleware' => 'api'], function () {
    Route::apiResource('categories', AdminCategoryController::class);
    Route::get('get_parent_category', [AdminCategoryController::class, 'get_parent_category']);
    Route::post('update_category', [AdminCategoryController::class, 'update_category']);
    Route::post('destroy_category', [AdminCategoryController::class, 'destroy_category']);
    Route::post('update_avatar', [UserController::class, 'update_avatar']);
    Route::post('update_info', [UserController::class, 'update_info']);
});

// ---------------------- ADMIN ----------------------
