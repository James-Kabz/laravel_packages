<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mpesa_requests', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);
            $table->string('phone', 30)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('remarks', 200)->nullable();
            $table->string('originator_conversation_id', 100)->nullable();
            $table->string('conversation_id', 100)->nullable();
            $table->string('response_code', 20)->nullable();
            $table->string('response_description', 200)->nullable();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->timestamps();

            $table->index(['type', 'phone']);
            $table->index('originator_conversation_id');
            $table->index('conversation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpesa_requests');
    }
};
