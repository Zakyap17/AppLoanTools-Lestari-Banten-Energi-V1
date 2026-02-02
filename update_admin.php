<?php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Find the old user or create new
$user = User::where('email', 'test@example.com')->first();

if ($user) {
    echo "Updating existing user...\n";
    $user->name = 'Zaky Aprilian';
    $user->email = 'zakyaprilian17@gmail.com';
    $user->password = Hash::make('Z0212');
    $user->save();
} else {
    echo "Creating new user...\n";
    User::updateOrCreate(
        ['email' => 'zakyaprilian17@gmail.com'],
        [
            'name' => 'Zaky Aprilian',
            'password' => Hash::make('Z0212')
        ]
    );
}

echo "User updated: Zaky Aprilian (zakyaprilian17@gmail.com)\n";
