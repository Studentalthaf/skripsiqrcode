<?php


namespace App\Http\Controllers;

use App\Models\Event;  // Mengimpor model Event
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function acara()
    {
        // Mengambil semua acara dari database
        $events = Event::all();
        
        // Mengirim data acara ke tampilan
        return view('pointakses.user.page.page_acara', compact('events'));
    }

    public function tambah()
    {
        // Menampilkan form tambah acara
        return view('pointakses.user.page.page_tambah_acara');
    }
}
