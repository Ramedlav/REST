<?php

use Illuminate\Http\Request;

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
Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');//+
    Route::post('registration', 'AuthController@registration');//+
    Route::post('logout', 'AuthController@logout');//+
    Route::post('refresh', 'AuthController@refresh');//+
    Route::post('me', 'AuthController@me');//+
});

Route::get('books/all', 'BookController@GetAllBooks');//+ get all books
Route::get('authors/all', 'AuthorController@GetAllAuthors');// + get all authors
Route::post('books/author', 'AuthorController@GetAuthorsBooks');// +get all books of a author

Route::group(['middleware' => 'auth:api'], function (){
    Route::get('books/my', 'BookController@GetUsersBooks');// + get all Users books
    Route::post('book/create', 'BookController@CreateBook');//create book +
    Route::post('book/update', 'BookController@UpdateBook');//update book +
    Route::delete('book/delete/{id}', 'BookController@DeleteBook');//delete book +
});
