<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'isbn',
        'description',
        'author_id',
        'genre',
        'published_date',
        'total_copies',
        'available_copies',
        'price',
        'cover_image',
        'status',
    ];

    // relation
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    public function isAvailable(): bool
    {
        return $this->available_copies > 0;
    }

    // decrease available copies when borrowed
    public function borrow(): void
    {
        if ($this->available_copies > 0) {
            $this->decrement('available_copies');
        }
    }

    // increment available copies when borrowed
    public function returnBook(): void
    {
        if ($this->available_copies < $this->total_copies) {
            $this->increment('available_copies');
        }
    }
}
