<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    function index()
    {
        return view('halaman_auth/login');
    }

    /**
     * Proses login pengguna.
     */
    function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
        ]);

        // Data login
        $infologin = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        // Cek autentikasi
        if (Auth::attempt($infologin)) {
            // Redirect berdasarkan role
            if (Auth::user()->role === 'user') {
                return redirect()->route('user.index')->with('success', 'Anda berhasil login');
            } elseif (Auth::user()->role === 'admin') {
                return redirect()->route('admin.index')->with('success', 'Halo Admin, Anda berhasil login');
            } else {
                Auth::logout();
                return redirect()->route('login')->withErrors('Role tidak valid');
            }
        } else {
            return redirect()->route('login')->withErrors('Email atau Password salah');
        }
    }

    /**
     * Menampilkan halaman registrasi.
     */
    function create()
    {
        return view('halaman_auth/reg');
    }

    /**
     * Proses registrasi pengguna.
     */
    function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_lengkap' => 'required|min:5',
            'NIM' => 'required|min:11|unique:users,NIM', // Tambahkan validasi NIM
            'email' => 'required|unique:users|email',
            'password' => 'required|min:6',
            'no_tlp' => 'required|min:11',
            'unit_kerja' => 'required|min:2',
            'alamat' => 'required|min:2',
        ], [
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi',
            'nama_lengkap.min' => 'Nama Lengkap minimal harus 5 karakter',
            'NIM.required' => 'NIM wajib diisi', // Pesan error untuk NIM
            'NIM.min' => 'NIM minimal harus 11 karakter',
            'NIM.unique' => 'NIM sudah terdaftar',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal harus 6 karakter',
            'no_tlp.required' => 'Nomor Telepon wajib diisi',
            'no_tlp.min' => 'Nomor Telepon minimal harus 11 digit',
            'unit_kerja.required' => 'Unit Kerja wajib diisi',
            'unit_kerja.min' => 'Unit Kerja minimal harus 2 karakter',
            'alamat.required' => 'Alamat wajib diisi',
            'alamat.min' => 'Alamat minimal harus 2 karakter',
        ]);

        // Data registrasi
        $inforegister = [
            'nama_lengkap' => $request->nama_lengkap,
            'NIM' => $request->NIM, // Tambahkan NIM ke data registrasi
            'email' => $request->email,
            'password' => bcrypt($request->password), // Enkripsi password
            'no_tlp' => $request->no_tlp,
            'unit_kerja' => $request->unit_kerja,
            'alamat' => $request->alamat,
            'role' => 'user', // Default role adalah user
        ];

        try {
            // Simpan data pengguna ke database
            $user = User::create($inforegister);
        } catch (\Throwable $th) {
            return back()->withInput()->with('error_create', 'Gagal membuat akun');
        }

        // Redirect ke halaman login dengan pesan sukses
        return redirect()->route('login')->with('success', 'Akun berhasil dibuat. Silakan login.');
    }

    /**
     * Logout pengguna.
     */
    function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}