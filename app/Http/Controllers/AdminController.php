<?php


namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // Mengambil data pengguna yang hanya memiliki role 'admin'
        $users = User::where('role', 'admin')->get();
        
        return view('pointakses.Admin.index', compact('users'));
    }
}
