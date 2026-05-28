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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('telepon', 20);
            $table->string('nik', 20);
            $table->enum('produk', ['NDF Car', 'NDF Motor', 'NDF Property', 'Machinery', 'Heavy Equipment', 'DF Mobil', 'DF Motor']);
            $table->decimal('ntf', 15, 2)->nullable();
            $table->string('unit', 100)->nullable();
            $table->string('no_unit', 50)->nullable();
            $table->string('owner_type', 50);
            $table->unsignedBigInteger('owner_id');
            $table->foreignId('input_by')->constrained('users');
            $table->foreignId('source_mitra_id')->nullable()->constrained('mitra');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['owner_type', 'owner_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
