<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mpesa_callbacks', function (Blueprint $table): void {
            $table->id();
            $table->string('type')->index();
            $table->integer('result_code')->nullable();
            $table->string('result_desc')->nullable();
            $table->string('originator_conversation_id')->nullable()->index();
            $table->string('conversation_id')->nullable()->index();
            $table->string('transaction_id')->nullable()->index();
            $table->string('merchant_request_id')->nullable()->index();
            $table->string('checkout_request_id')->nullable()->index();
            $table->string('mpesa_receipt_number')->nullable()->index();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('phone')->nullable()->index();
            $table->string('party_a')->nullable()->index();
            $table->string('party_b')->nullable()->index();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpesa_callbacks');
    }
};
