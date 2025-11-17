<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBorrowingRequest;
use App\Http\Resources\BorrowingResource;
use App\Models\Book;
use App\Models\Borrowing;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Borrowing::with(['book', 'member']);

        // filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // filter by member
        if ($request->has('member_id')) {
            $query->where('member_id', $request->member_id);
        }
        
        $borrowing = $query->latest()->paginate(10);

        return BorrowingResource::collection($borrowing);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBorrowingRequest $request)
    {
        $book = Book::findOrFail($request->book_id);

        // check if book is available
        if (!$book->isAvailable()) {
            return response()->json([
                'message' => 'Book is not available for borrowing'
            ]);
        }

        // create borrowing record
        $borrowing = Borrowing::create($request->validated());

        // update book availability
        $book->borrow();

        $borrowing->load(['book', 'member']);

        return new BorrowingResource($borrowing);
    }

    /**
     * Display the specified resource.
     */
    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['book', 'member']);

        return new BorrowingResource($borrowing);
    }

    /**
     * Return a specific book.
     */
    public function returnBook(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'borrowed') {
            return response()->json([
                'message' => 'Book has already been returned'
            ]);
        }

        // update borrowing record
        $borrowing->update([
            'returned_date' => now(),
            'status' => 'returned',
        ]);

        // update book availability
        $borrowing->book->returnBook();

        $borrowing->load(['book', 'member']);

        return new BorrowingResource($borrowing);
    }

    /**
     * Overdue list.
     */
    public function overdue()
    {
        // fetch overdues
        $overdueBorrowings = Borrowing::with(['book', 'member'])
            ->where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->get();

        // update status to overdue
        Borrowing::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        return BorrowingResource::collection($overdueBorrowings);
    }
}
