<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index()
    {
        // Mengambil data pengguna dengan role 'admin'
        $users = User::where('role', 'admin')->get();

        return view('pointakses.admin.index', compact('users')); // Perbaiki huruf kecil
    }

    public function event()
    {
        // Mengambil semua event dari database
        $events = Event::all();

        return view('pointakses.admin.page.admin_page_event', compact('events'));
    }

    public function create_event()
    {
        // Menampilkan form tambah acara
        return view('pointakses.admin.page.admin_page_create_event');
    }

    public function store(Request $request)
    {


        // Validasi data input
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'type_event' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
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

            // Simpan logo jika ada
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('logos', 'public');
                $event->logo = $logoPath;
            }

            // Simpan tanda tangan jika ada
            if ($request->hasFile('signature')) {
                $signaturePath = $request->file('signature')->store('signatures', 'public');
                $event->signature = $signaturePath;
            }

            $event->save();

            return redirect()->route('admin.event')->with('success', 'Acara berhasil ditambahkan!');
        }

        return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
    }
    public function delete_event($id)
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
        return redirect()->route('admin.event')->with('success', 'Acara berhasil dihapus!');
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

        return redirect()->route('admin.event')->with('success', 'Acara berhasil diupdate!');
    }

    public function edit_event($id)
    {
        // Cari acara berdasarkan ID
        $event = Event::findOrFail($id);

        // Kirim data acara ke view form edit
        return view('pointakses.admin.page.admin_page_update_event', compact('event'));
    }
    public function users()
    {
        $users = User::all();
        return view('pointakses.admin.page.admin_page_users', compact('users'));
    }
}
