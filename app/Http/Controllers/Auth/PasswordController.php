<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Show the change password form.
     */
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    /**
     * Update the user's password.
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed'],
        ]);


        try {
            $user = Auth::user();
            // Use Hash::make explicitly to be absolutely sure
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
            $user->save();

            \Illuminate\Support\Facades\Log::info('Password update success for: ' . $user->username);
            return back()->with('success', 'Password updated successfully!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Password update failed: ' . $e->getMessage());
            return back()->withErrors(['password' => 'Failed to update password. Please try again.']);
        }
    }



}
