<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{

    /**
     * First five books.
     */
    public function firstFiveBooks()
    {
        $book = Book::latest()->take(5)->get();

        return BookResource::collection($book);
    }
}
