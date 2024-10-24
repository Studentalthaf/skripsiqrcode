<?php

namespace App\Http\Controllers;

use App\Models\Event;  // Mengimpor model Event
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;  // Import Auth dengan benar
use Illuminate\Support\Facades\Storage; // Import Storage untuk penggunaan

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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Ganti 'logo_acara' menjadi 'logo'
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Cek apakah pengguna sudah login
        if (Auth::check()) {
            $event = new Event();
            $event->user_id = Auth::id();
            $event->title = $request->title;
            $event->description = $request->description;
            $event->date = $request->date;
            $event->type_event = $request->type_event;

            // Proses penyimpanan logo
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('logos', 'public');
                $event->logo = $logoPath;
            }

            // Proses penyimpanan signature
            if ($request->hasFile('signature')) {
                $signaturePath = $request->file('signature')->store('signatures', 'public');
                $event->signature = $signaturePath;
            }

            $event->save();

            return redirect()->route('user.acara')->with('success', 'Acara berhasil ditambahkan!');
        }

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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Cari acara berdasarkan ID
        $event = Event::findOrFail($id);
        $event->title = $request->title;
        $event->date = $request->date;
        $event->type_event = $request->type_event;
        $event->description = $request->description;

        // Proses penyimpanan logo
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($event->logo && Storage::exists($event->logo)) {
                Storage::delete($event->logo);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
            $event->logo = $logoPath;
        }

        // Proses penyimpanan signature
        if ($request->hasFile('signature')) {
            
            if ($event->signature && Storage::exists($event->signature)) {
                Storage::delete($event->signature);
            }
            $signaturePath = $request->file('signature')->store('signatures', 'public');
            $event->signature = $signaturePath;
        }

        $event->save();

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

        // Hapus logo dan signature jika ada
        if ($event->logo && Storage::exists($event->logo)) {
            Storage::delete($event->logo);
        }
        if ($event->signature && Storage::exists($event->signature)) {
            Storage::delete($event->signature);
        }

        $event->delete();

        // Redirect kembali ke halaman acara dengan pesan sukses
        return redirect()->route('user.acara')->with('success', 'Acara berhasil dihapus!');
    }
}
