<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add username column (nullable temporarily)
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('id');
        });

        // Migrate existing data: use email as username (before @ symbol)
        DB::table('users')->get()->each(function ($user) {
            $username = explode('@', $user->email)[0];
            DB::table('users')
                ->where('id', $user->id)
                ->update(['username' => $username]);
        });

        // Make username unique and not nullable, then drop email
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable(false)->change();
            $table->dropUnique(['email']);
            $table->dropColumn('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add email column back
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->after('id');
        });

        // Restore email from username (add @example.com)
        DB::table('users')->get()->each(function ($user) {
            $email = $user->username . '@example.com';
            DB::table('users')
                ->where('id', $user->id)
                ->update(['email' => $email]);
        });

        // Make email unique and not nullable, then drop username
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->unique()->nullable(false)->change();
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
