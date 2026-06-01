<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Alter users table: change role to string and add new columns
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 50)->change();
            
            $table->string('profesi', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('domisili', 150)->nullable();
            $table->string('is_active_reason')->nullable();
            $table->timestamp('last_login_at')->nullable();
        });

        // 2. Migrate existing Mitra data to Users table if mitra table exists and has data
        if (Schema::hasTable('mitra')) {
            $mitras = DB::table('mitra')->get();
            $idMapping = []; // maps old mitra_id -> new user_id

            foreach ($mitras as $mitra) {
                // Check if user already exists with this nik to prevent duplicate key errors
                $existingUser = DB::table('users')->where('nik', $mitra->nik)->first();
                
                if ($existingUser) {
                    $newUserId = $existingUser->id;
                    // Update user's role and details
                    DB::table('users')->where('id', $newUserId)->update([
                        'role' => 'mitra',
                        'profesi' => $mitra->profesi,
                        'tanggal_lahir' => $mitra->tanggal_lahir,
                        'domisili' => $mitra->domisili,
                        'supervisor_id' => $mitra->upline_id,
                        'is_active' => $mitra->is_active,
                        'is_active_reason' => $mitra->is_active_reason,
                        'last_login_at' => $mitra->last_login_at,
                    ]);
                } else {
                    $newUserId = DB::table('users')->insertGetId([
                        'nama' => $mitra->nama,
                        'nik' => $mitra->nik,
                        'telepon' => $mitra->telepon,
                        'email' => $mitra->email,
                        'password' => $mitra->password,
                        'role' => 'mitra',
                        'profesi' => $mitra->profesi,
                        'tanggal_lahir' => $mitra->tanggal_lahir,
                        'domisili' => $mitra->domisili,
                        'supervisor_id' => $mitra->upline_id,
                        'is_active' => $mitra->is_active,
                        'is_active_reason' => $mitra->is_active_reason,
                        'last_login_at' => $mitra->last_login_at,
                        'created_at' => $mitra->created_at,
                        'updated_at' => $mitra->updated_at,
                    ]);
                }
                
                $idMapping[$mitra->id] = $newUserId;
            }

            // 3. Update Leads references
            if (Schema::hasTable('leads')) {
                // Update owner_type and owner_id for Mitra owners
                $mitraLeads = DB::table('leads')->where('owner_type', 'App\Models\Mitra')->get();
                foreach ($mitraLeads as $lead) {
                    if (isset($idMapping[$lead->owner_id])) {
                        DB::table('leads')->where('id', $lead->id)->update([
                            'owner_type' => 'App\Models\User',
                            'owner_id' => $idMapping[$lead->owner_id],
                        ]);
                    }
                }

                // Update source_mitra_id references
                $sourceLeads = DB::table('leads')->whereNotNull('source_mitra_id')->get();
                foreach ($sourceLeads as $lead) {
                    if (isset($idMapping[$lead->source_mitra_id])) {
                        DB::table('leads')->where('id', $lead->id)->update([
                            'source_mitra_id' => $idMapping[$lead->source_mitra_id],
                        ]);
                    }
                }

                // Drop old foreign key constraint in SQLite/MySQL
                try {
                    Schema::table('leads', function (Blueprint $table) {
                        $table->dropForeign(['source_mitra_id']);
                    });
                } catch (\Exception $e) {
                    // Ignore for SQLite
                }

                // Re-add foreign key referencing users table
                try {
                    Schema::table('leads', function (Blueprint $table) {
                        $table->foreign('source_mitra_id')->references('id')->on('users')->nullOnDelete();
                    });
                } catch (\Exception $e) {
                    // Ignore for SQLite
                }
            }

            // 4. Update Upline Change Requests references
            if (Schema::hasTable('upline_change_requests')) {
                $requests = DB::table('upline_change_requests')->get();
                foreach ($requests as $req) {
                    if (isset($idMapping[$req->mitra_id])) {
                        DB::table('upline_change_requests')->where('id', $req->id)->update([
                            'mitra_id' => $idMapping[$req->mitra_id],
                        ]);
                    }
                }

                try {
                    Schema::table('upline_change_requests', function (Blueprint $table) {
                        $table->dropForeign(['mitra_id']);
                    });
                } catch (\Exception $e) {
                    // Ignore for SQLite
                }

                try {
                    Schema::table('upline_change_requests', function (Blueprint $table) {
                        $table->foreign('mitra_id')->references('id')->on('users')->cascadeOnDelete();
                    });
                } catch (\Exception $e) {
                    // Ignore for SQLite
                }
            }

            // 5. Drop the old mitra table
            Schema::dropIfExists('mitra');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profesi', 'tanggal_lahir', 'domisili', 'is_active_reason', 'last_login_at']);
        });
    }
};
