<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mpesa_callbacks', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);
            $table->integer('result_code')->nullable();
            $table->string('result_desc', 200)->nullable();
            $table->string('originator_conversation_id', 100)->nullable();
            $table->string('conversation_id', 100)->nullable();
            $table->string('transaction_id', 100)->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['type', 'result_code']);
            $table->index('originator_conversation_id');
            $table->index('conversation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpesa_callbacks');
    }
};
