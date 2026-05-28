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
        Schema::create('mitra', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 20)->unique();
            $table->string('password');
            $table->string('nama', 150);
            $table->string('telepon', 20)->unique();
            $table->string('profesi', 100);
            $table->date('tanggal_lahir');
            $table->string('domisili', 150);
            $table->foreignId('upline_id')->constrained('users');
            $table->boolean('is_active')->default(true);
            $table->string('is_active_reason')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitra');
    }
};
