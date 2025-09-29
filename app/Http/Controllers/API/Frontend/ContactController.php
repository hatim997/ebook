<?php
namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class ContactController extends Controller
{
    public function contactSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'nullable|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
    
        try {
            $contact = new Contact();
            $contact->name = $request->name;
            $contact->email = $request->email;
            $contact->message = $request->message;
            $contact->save();
    
            return response()->json([
                'success' => true,
                'message' => 'Your message has been sent successfully!',
            ]);
        } catch (\Throwable $th) {
            Log::error('Contact Submit Failed', ['error' => $th->getMessage()]);
    
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong! Please try again later.',
            ], 500);
        }
    }
}