<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        // Mengembalikan view untuk halaman test
        return view('pointakses.user.page.page_test'); // Pastikan Anda memiliki file view di resources/views/user/test.blade.php
    }
}
