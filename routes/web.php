<?php

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
// Home
Route::get('/', 'Auth\LoginController@home');

// User specific
Route::get('feed', 'FeedController@home');
Route::get('profile', 'ProfileController@home');
Route::get('settings', 'ProfileSettingsController@show');

// Questions
Route::get('question/create', 'QuestionController@create_view')->name('question_create');
Route::get('question/{id}', 'QuestionController@home')->name('question');
Route::post('api/question', 'QuestionController@create')->name('question_create_api');

// Answers
Route::put('api/answer/{id}', 'AnswerController@create');
Route::delete('api/answer/delete/{id}', 'AnswerController@delete');

// Authentication
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');
