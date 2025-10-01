<?php

namespace App\Http\Controllers\API\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\UserFavourite;
use App\Models\UserPurchase;
use App\Models\UserRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        try {
            $user = $request->user();

            // Fetch counts in fewer queries
            $userFavouritesCount = UserFavourite::where('user_id', $user->id)->count();
            $userReadsCount      = UserRead::where('user_id', $user->id)->count();

            // Load book with law count directly (no need to pull all relations)
            $book = Book::withCount('bookLaws')->firstOrFail();

            // Remaining laws
            $userRemainingCount = max(0, $book->book_laws_count - $userReadsCount);

            // Completion percentage
            $completionPercentage = $book->book_laws_count > 0
                ? round(($userReadsCount / $book->book_laws_count) * 100, 2)
                : 0;

            // Check purchase existence (optimized exists query)
            $isPurchased = UserPurchase::where([
                ['user_id', $user->id],
                ['book_id', $book->id],
                ['payment_status', 'paid'],
            ])->exists();



            return response()->json([
                'user_favourites_count' => $userFavouritesCount,
                'user_reads_count'      => $userReadsCount,
                'user_remaining_count'  => $userRemainingCount,
                'completion_percentage' => $completionPercentage,
                'is_purchased'          => $isPurchased,
                'user'     => [
                    'id'      => $user->id,
                    'name'    => $user->name,
                    'email' => $user->email,
                ],
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('API Profile failed', ['error' => $th->getMessage()]);
            return response()->json([
                'message' => 'Something went wrong!'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
