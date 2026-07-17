<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150)->comment('Admin full name');
            $table->string('email', 190)->unique()->comment('Admin login email');
            $table->string('phone', 30)->nullable()->comment('Admin phone');
            $table->string('password', 255)->comment('Hashed password');

            $table->string('status', 30)->default('active')->comment('active, suspended');
            $table->timestamp('last_login_at')->nullable()->comment('Last login time');
            $table->timestamp('email_verified_at')->nullable();

            $table->string('avatar_path')->nullable()->comment('Avatar image path');
            $table->text('notes')->nullable()->comment('Private note');
            $table->rememberToken();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status', 'idx_admins_status');
            $table->comment('Stores platform administrators (ecosystem managers)');
        });

        $this->migrateLegacyAdmins();
    }

    /**
     * Move existing platform super-admins (owners with no tenant) into the
     * dedicated admins table. Guarded so it is safe on fresh installs.
     */
    protected function migrateLegacyAdmins(): void
    {
        if (! Schema::hasTable('owners')) {
            return;
        }

        $legacy = DB::table('owners')
            ->whereNull('tenant_id')
            ->whereNull('deleted_at')
            ->get();

        foreach ($legacy as $owner) {
            $exists = DB::table('admins')->where('email', $owner->email)->exists();

            if (! $exists) {
                DB::table('admins')->insert([
                    'name' => $owner->name,
                    'email' => $owner->email,
                    'phone' => $owner->phone ?? null,
                    'password' => $owner->password,
                    'status' => $owner->status ?? 'active',
                    'last_login_at' => $owner->last_login_at ?? null,
                    'email_verified_at' => $owner->email_verified_at ?? null,
                    'notes' => $owner->notes ?? null,
                    'created_at' => $owner->created_at ?? now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('owners')->where('id', $owner->id)->update(['deleted_at' => now()]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
