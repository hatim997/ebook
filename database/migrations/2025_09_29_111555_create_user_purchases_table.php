<?php

use App\Models\Billing;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('order_no');
            $table->foreignIdFor(User::class)->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignIdFor(Billing::class)->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignIdFor(Book::class)->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->string('amount');
            $table->enum('payment_status',['pending', 'paid', 'failed'])->default('pending');
            $table->enum('payment_type',['card', 'paypal', 'stripe', 'authorize.net','cod'])->default('card');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_purchases');
    }
};
