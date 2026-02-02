<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mpesa_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('type')->index();
            $table->string('status')->default('pending')->index();
            $table->string('phone')->nullable();
            $table->string('party_a')->nullable()->index();
            $table->string('party_b')->nullable()->index();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('currency', 3)->default('KES');
            $table->string('remarks', 200)->nullable();
            $table->string('command_id')->nullable();
            $table->string('originator_conversation_id')->nullable()->index();
            $table->string('conversation_id')->nullable()->index();
            $table->string('merchant_request_id')->nullable()->index();
            $table->string('checkout_request_id')->nullable()->index();
            $table->string('response_code', 20)->nullable();
            $table->string('response_description')->nullable();
            $table->integer('result_code')->nullable();
            $table->string('result_desc')->nullable();
            $table->string('transaction_id')->nullable()->index();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpesa_requests');
    }
};
