<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\UserPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserPurchasesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view user purchases');
        try {
            $userPurchases = UserPurchase::with('billing', 'book')->get();
            return view('dashboard.user-purchases.index',compact('userPurchases'));
        } catch (\Throwable $th) {
            Log::error('User Purchase Index Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('view user purchases');
        try {
            $userPurchase = UserPurchase::with('billing', 'book', 'user')->findOrFail($id);
            return view('dashboard.user-purchases.show', compact('userPurchase'));
        } catch (\Throwable $th) {
            Log::error('User Purchase Show Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
