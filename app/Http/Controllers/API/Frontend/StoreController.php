<?php

namespace App\Http\Controllers\API\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\Book;
use App\Models\UserPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class StoreController extends Controller
{
    public function getStore(Request $request)
    {
        try {
            $user = $request->user();
            $book = Book::firstOrFail();
            $books = Book::with('bookType')->where('id', '!=', $book->id)->get();

            // Check if purchased
            $isPurchased = UserPurchase::where('user_id', $user->id)
                ->where('book_id', $book->id)
                ->where('payment_status', 'paid')
                ->exists();

            $data = $books->map(function ($book) use ($user) {
                return [
                    'title' => $book->title,
                    'slug' => $book->slug,
                    'price' => $book->price,
                    'book_type' => $book->bookType->name ?? null,
                    'description' => $book->description ?? null,
                    'is_purchased' => UserPurchase::where('user_id', $user->id)
                        ->where('book_id', $book->id)
                        ->where('payment_status', 'paid')
                        ->exists(),
                ];
            });

            return response()->json([
                'complete_books' => $data,
                'is_premium' => $isPurchased
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('API get store failed', ['error' => $th->getMessage()]);
            return response()->json([
                'message' => 'Something went wrong!'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function checkout(Request $request)
    {
        try {
            $user = $request->user();
            $billing = Billing::where('user_id', $user->id)->first();
            if (!$billing) {
                $billing = [];
            }else{
                $billing = [
                    'firstname' => $billing->firstname,
                    'lastname' => $billing->lastname,
                    'email' => $billing->email,
                    'phone' => $billing->phone,
                    'address' => $billing->address,
                    'city' => $billing->city,
                    'state' => $billing->state,
                    'zip' => $billing->zip,
                    'country' => $billing->country,
                ];
            }

            return response()->json([
                'billing' => $billing,
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('API get checkout details failed', ['error' => $th->getMessage()]);
            return response()->json([
                'message' => 'Something went wrong!'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
