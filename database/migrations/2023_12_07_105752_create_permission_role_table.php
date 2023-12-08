<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/yyyy_mm_dd_create_permission_role_table.php

    public function up()
    {
        Schema::create('permissions_role', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('permissions_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();

            $table->unique(['permissions_id', 'role_id']);

            $table->foreign('permissions_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_role');
    }
};
