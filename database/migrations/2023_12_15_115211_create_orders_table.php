<?php

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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string("payment_method");
            $table->timestamps();
        });
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->string("order_notes");
            $table->timestamps();
        });

        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id'); // Assuming you have a 'users' table
            $table->decimal('total', 8, 2)->nullable();
            $table->string('address')->nullable();
            $table->string('status')->nullable();
            $table->string('code')->nullable()->unique();
            $table->integer("note_id")->nullable();
            $table->string("refusal_reason")->nullable();
            $table->integer("coupon_used")->default(0);
            $table->integer("payment_id")->default(1);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
            $table->foreign('note_id')->references('id')->on('notes')->onDelete('cascade');

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('notes');

    }
};
