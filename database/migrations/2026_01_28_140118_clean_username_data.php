<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clean username data - remove @ and everything after it
        DB::table('users')->get()->each(function ($user) {
            if (strpos($user->username, '@') !== false) {
                $cleanUsername = explode('@', $user->username)[0];
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['username' => $cleanUsername]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this data cleaning
    }
};
