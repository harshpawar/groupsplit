<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bill_splits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained()->cascadeOnDelete();
            $table->foreignId('group_member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('share_amount', 12, 2);
            $table->decimal('approved_amount', 12, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->unique(['bill_id', 'group_member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_splits');
    }
};
