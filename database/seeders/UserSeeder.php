<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create user: Indra Setiawan Suwandi
        User::create([
            'name' => 'Indra Setiawan Suwandi',
            'username' => 'A000278',
            'password' => Hash::make('Live'),
        ]);

        echo "User created successfully!\n";
        echo "Username: A000278\n";
        echo "Password: Live\n";
    }
}
