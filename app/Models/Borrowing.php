<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'member_id',
        'borrowed_date',
        'due_date',
        'returned_date',
        'status',
    ];

    protected $casts = [
        'borrowed_date' => 'date',
        'due_date' => 'date',
        'returned_date' => 'date',
    ];

    // relation
    public function book():BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function member():BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    // check if borrowing is overdue
    public function isOverdue(): bool
    {
        return $this->due_date < Carbon::today() && $this->status === 'borrowed';
    }
}
