<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\Api\V2\BookController as BookControllerV2;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\MemberController;
use App\Models\Author;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function() {

    // api versioning
    // version 1
    Route::prefix('v1')->group(function() {
      
        // user
        Route::get('user', [AuthController::class, 'user']);
        Route::get('logout', [AuthController::class, 'logout']);
    
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
    
        // statistics
        Route::get('statistics', function() {
            return response()->json([
                'total_books' => Book::count(),
                'total_authors' => Author::count(),
                'total_members' => Member::count(),
                'books_borrowed' => Borrowing::where('status', 'borrowed')->count(),
                'overdue_borrowings' => Borrowing::where('status', 'overdue')->count(),
            ]);
        });
    });

    // version 2
    Route::prefix('v2')->group(function () {
        Route::get('latest/five/books', [BookControllerV2::class, 'firstFiveBooks']);
    });
});