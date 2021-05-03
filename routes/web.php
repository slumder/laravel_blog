<?php

use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'user'], function () {
  Route::group(['prefix' => 'auth'], function () {
    Route::get('/sign-up', 'UserAuthController@signUpPage');
    Route::post('/sign-up', 'UserAuthController@signUpProcess');
    Route::get('/sign-in', 'UserAuthController@signInPage');
    Route::post('/sign-in', 'UserAuthController@signInProcess');
    Route::get('/sign-out', 'UserAuthController@signOut');
  });
});

Route::group(['middleware' => ['auth.admin']], function () {
  Route::group(['prefix' => 'admin'], function () {
    //自我介紹相關
    Route::group(['prefix' => 'user'], function () {
      //自我介紹頁面
      Route::get('/', 'AdminController@editUserPage');
      //處理自我介紹資料
      Route::post('/', 'AdminController@editUserProcess');
    });

    //心情隨筆相關
    Route::group(['prefix' => 'mind'], function () {
      //心情隨筆列表頁面
      Route::get('/', 'AdminController@mindListPage');
      //新增心情隨筆資料
      Route::get('/add', 'AdminController@addMindPage');
      //處理心情隨筆資料
      Route::post('/edit', 'AdminController@editMindProcess');
      //單一資料
      Route::group(['prefix' => '{mind_id}'], function () {
        //編輯心情隨筆資料
        Route::get('/edit', 'AdminController@editMindPage');
        //刪除心情隨筆資料
        Route::get('/delete', 'AdminController@deleteMindProcess');
      });
    });
  });
});

Route::group(['prefix' => '/'], function () {
  Route::get('/', 'HomeController@indexPage');
  Route::group(['prefix' => '{user_id}'], function () {
    Route::get('/user', 'HomeController@userPage');
    Route::get('/mind', 'HomeController@mindPage');
    Route::get('/board', 'HomeController@boardPage');
  });
});
