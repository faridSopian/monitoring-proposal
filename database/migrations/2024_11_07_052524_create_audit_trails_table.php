<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ID user
            $table->string('username'); // Username
            $table->string('menu_accessed'); // Menu yang diakses
            $table->string('method'); // Method yang digunakan (e.g., GET, POST)
            $table->timestamp('activity_time'); // Waktu aktivitas
            $table->text('detail')->nullable(); // Detail perubahan data
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_trails');
    }
};
