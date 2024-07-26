<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['namespace'=>'App\Http\Controllers'], function () {

    //Создание работника
    Route::post('/employee', 'Api\EmployeeController@create');
    //Создание транзакции
    Route::post('/transaction', 'Api\TransactionController@create');
    //Просмотр всех невыплаченных транзакций
    Route::get('/unpaid', 'Api\TransactionController@unpaid');
    //Оплата всех невыплаченных транзакций
    Route::patch('/pay', 'Api\TransactionController@pay');



    //Просмотр работников
    Route::get('/employers', 'Api\EmployeeController@show');
    //Просмотр всех транзакций
    Route::get('/transactions', 'Api\TransactionController@show');

});
