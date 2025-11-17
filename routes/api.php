<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\MemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// authors
Route::apiResource('authors', AuthorController::class);

// books
Route::apiResource('books', BookController::class);

// members
Route::apiResource('members', MemberController::class);

// borrowing
Route::apiResource('borrowings', BorrowingController::class)->only(['index', 'store', 'show']);

// return & overdue
Route::post('borrowings/{borrowing}/return', [BorrowingController::class, 'returnBook']);
Route::get('borrowings/overdue/list', [BorrowingController::class, 'overdue']);