<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Mengambil data pengguna yang hanya memiliki role 'user'
        $users = User::where('role', 'user')->get();
        
        return view('pointakses.user.index', compact('users'));
    }
}
