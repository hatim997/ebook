<?php

namespace App\Http\Controllers\API\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    public function allBooks()
    {
        try {
            $books = Book::with('bookLaws')->get();

            return response()->json([
                'books' => $books,
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            Log::error('API All Books failed', ['error' => $th->getMessage()]);
            return response()->json(['message' => 'Something went wrong!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
