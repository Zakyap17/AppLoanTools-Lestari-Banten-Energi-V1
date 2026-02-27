<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request, \App\Services\LdapService $ldapService)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // 1. Attempt LDAP Authentication (If Enabled)
        if (env('LDAP_ENABLED', false)) {
            if ($ldapService->authenticate($credentials['username'], $credentials['password'])) {

                // 2. Fetch User data from LDAP to sync profile
                $ldapUser = $ldapService->getUserByUsername($credentials['username']);

                if ($ldapUser) {
                    // 3. Find or Create/Update Local User
                    $user = \App\Models\User::updateOrCreate(
                        ['username' => $ldapUser['username']],
                        [
                            'name' => $ldapUser['name'],
                            'guid' => $ldapUser['guid'],
                            'is_active' => true,
                        ]
                    );

                    // 4. Log the user in
                    Auth::login($user);
                    $request->session()->regenerate();
                    return redirect()->intended('dashboard');
                }
            }
        }

        // 5. Fallback to Local Auth (Directly to this if LDAP is disabled)
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        $errorMsg = env('LDAP_ENABLED', false)
            ? 'Credential LDAP tidak valid atau akun tidak terdaftar di Warehouse.'
            : 'Username atau password salah.';

        return back()->withErrors([
            'username' => $errorMsg,
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function keepAlive(Request $request)
    {
        if (Auth::check()) {
            session(['last_activity_time' => time()]);
            return response()->json(['status' => 'alive']);
        }
        return response()->json(['status' => 'unauthenticated'], 401);
    }
}
