<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // public function login ()
    // {
    //     if(Auth::user())
    //     {
    //         return redirect('/home');
    //     }

    //     $data = array(
    //         'title' => 'Login'
    //     );

    //     return view('Auth.Login', $data);
    // }

    // public function cek_login(Request $request)
    // {
    //     $credentials = [
    //         'email'     => $request->email,
    //         'password'  => $request->password,
    //         'is_active' => 'Aktif',
    //         'tahun'     => $request->tahun,
    //     ];

    //     if (Auth::guard('web')->attempt($credentials)) {

    //         return response()->json([
    //             'status'   => 'success',
    //             'message'  => 'Login berhasil',
    //             'redirect' => url('/home')
    //         ]);
    //     }

    //     return response()->json([
    //         'status'  => 'error',
    //         'message' => 'Upss.. Akun Anda belum aktif atau Email & Password Salah'
    //     ], 401);
    // }

    // public function logout()
    // {
    //     Auth::guard('web')->logout();
    //     return redirect('/login')->with('success', 'Logout Berhasil');
    // }

    // public function register ()
    // {
    //     $data = array(
    //         'title' => 'Halaman Register'
    //     );

    //     return view('Auth.Register', $data);
    // }

    public function login()
    {
        if (Auth::check()) {

            return $this->redirectByRole(Auth::user());
        }

        return view('Auth.Login', [
            'title' => 'Login'
        ]);
    }

    public function cek_login(Request $request)
    {
        $credentials = [
            'email'     => $request->email,
            'password'  => $request->password,
            'is_active' => 'Aktif',
            'tahun'     => $request->tahun,
        ];

        if (Auth::attempt($credentials)) {

            $user = Auth::user();

            return response()->json([
                'status'   => 'success',
                'message'  => 'Login berhasil',
                'redirect' => $this->redirectByRole($user)
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Upss.. Akun Anda belum aktif atau Email & Password Salah'
        ], 401);
    }

    private function redirectByRole($user)
    {
        $role = strtolower($user->role);

        if ($role == 'ppk') {
            return url('/ppk/dashboard');
        }

        if ($role == 'pa' || $role == 'kpa') {
            return url('/pa/dashboard');
        }

        if ($role == 'user') {
            return url('/home');
        }

        if ($role == 'admin') {
            return url('/home');
        }

        return url('/home');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login')->with('success', 'Logout Berhasil');
    }
}
