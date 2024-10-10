<?php

namespace App\Http\Controllers;

use App\Models\Event;  // Mengimpor model Event
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;  // Import Auth dengan benar

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

    public function store(Request $request)
    {
        // Validasi data input
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'type_event' => 'required|string|max:255',
        ]);

        // Pastikan user sudah login
        if (Auth::check()) {
            // Menyimpan data acara ke dalam database
            $event = new Event();
            $event->user_id = Auth::id();  // Menggunakan ID pengguna yang sedang login
            $event->title = $request->title;
            $event->description = $request->description;
            $event->date = $request->date;
            $event->type_event = $request->type_event;
            $event->save();

            // Redirect ke halaman daftar acara setelah berhasil menyimpan
            return redirect()->route('user.acara')->with('success', 'Acara berhasil ditambahkan!');
        }

        // Jika user tidak login, redirect ke halaman login
        return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'type_event' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Cari acara berdasarkan ID dan update datanya
        $event = Event::findOrFail($id);
        $event->title = $request->title;
        $event->date = $request->date;
        $event->type_event = $request->type_event;
        $event->description = $request->description;
        $event->save();

        // Redirect ke halaman acara dengan pesan sukses
        return redirect()->route('user.acara')->with('success', 'Acara berhasil diupdate!');
    }


    public function edit($id)
    {
        // Cari acara berdasarkan ID
        $event = Event::findOrFail($id);

        // Kirim data acara ke view form edit
        return view('pointakses.user.page.page_edit_acara', compact('event'));
    }

    public function hapus($id)
    {
        // Mencari acara berdasarkan ID
        $event = Event::findOrFail($id);

        // Menghapus acara
        $event->delete();

        // Redirect kembali ke halaman acara dengan pesan sukses
        return redirect()->route('user.acara')->with('success', 'Acara berhasil dihapus!');
    }
}
