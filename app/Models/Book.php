<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    public function bookLaws()
    {
        return $this->hasMany(BookLaw::class, 'book_id');
    }

    public function bookType()
    {
        return $this->belongsTo(BookType::class, 'book_type_id');
    }
}
