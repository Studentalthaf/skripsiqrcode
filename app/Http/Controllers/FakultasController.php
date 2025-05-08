<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FakultasController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'fakultas')->get();
        $eventCount = Event::where('user_id', Auth::id())->count();
        $upcomingEvents = Event::where('user_id', Auth::id())
            ->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->get(['title', 'date', 'type_event']);
        return view('pointakses.fakultas.index', compact('users', 'eventCount', 'upcomingEvents'));
    }
    public function event()
    {
        $events = Event::where('user_id', Auth::id())->get();
        return view('pointakses.fakultas.page.fakultas_page_event', compact('events'));
    }
    public function create_event()
    {
        // Menampilkan form tambah acara
        return view('pointakses.fakultas.page.fakultas_page_create_event');
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'type_event' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'template_pdf' => 'nullable|mimes:pdf|max:5120', // max 5MB
        ]);

        if (Auth::check()) {
            $event = new Event();
            $event->user_id = Auth::id();
            $event->title = $request->title;
            $event->description = $request->description;
            $event->date = $request->date;
            $event->type_event = $request->type_event;

            if ($request->hasFile('logo')) {
                $event->logo = $request->file('logo')->store('logos', 'public');
            }

            if ($request->hasFile('signature')) {
                $event->signature = $request->file('signature')->store('signatures', 'public');
            }

            if ($request->hasFile('template_pdf')) {
                $event->template_pdf = $request->file('template_pdf')->store('pdfs', 'public');
            }

            $event->save();

            return redirect()->route('fakultas.event')->with('success', 'Acara berhasil ditambahkan!');
        }

        return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
    }
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'type_event' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'template_pdf' => 'nullable|mimes:pdf|max:5120', // max 5MB
        ]);
    
        // Cek apakah user sudah login
        if (Auth::check()) {
            // Cari acara berdasarka    n ID
            $event = Event::findOrFail($id);
    
            // Pastikan user yang login adalah pemilik acara
            if ($event->user_id !== Auth::id()) {
                return redirect()->route('fakultas.event')->with('error', 'Anda tidak memiliki izin untuk mengedit acara ini.');
            }
    
            // Update data acara
            $event->title = $request->title;
            $event->description = $request->description;
            $event->date = $request->date;
            $event->type_event = $request->type_event;
    
            // Proses penyimpanan logo
            if ($request->hasFile('logo')) {
                // Hapus logo lama jika ada
                if ($event->logo && Storage::disk('public')->exists($event->logo)) {
                    Storage::disk('public')->delete($event->logo);
                }
                $event->logo = $request->file('logo')->store('logos', 'public');
            }
    
            // Proses penyimpanan signature
            if ($request->hasFile('signature')) {
                // Hapus signature lama jika ada
                if ($event->signature && Storage::disk('public')->exists($event->signature)) {
                    Storage::disk('public')->delete($event->signature);
                }
                $event->signature = $request->file('signature')->store('signatures', 'public');
            }
    
            // Proses penyimpanan template PDF
            if ($request->hasFile('template_pdf')) {
                // Hapus template PDF lama jika ada
                if ($event->template_pdf && Storage::disk('public')->exists($event->template_pdf)) {
                    Storage::disk('public')->delete($event->template_pdf);
                }
                $event->template_pdf = $request->file('template_pdf')->store('pdfs', 'public');
            }
    
            // Simpan perubahan
            $event->save();
    
            return redirect()->route('fakultas.event')->with('success', 'Acara berhasil diupdate!');
        }
    
        return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
    }
    
    public function edit_event($id)
    {
        // Cek apakah user sudah login
        if (Auth::check()) {
            // Cari acara berdasarkan ID
            $event = Event::findOrFail($id);
    
            // Pastikan user yang login adalah pemilik acara
            if ($event->user_id !== Auth::id()) {
                return redirect()->route('fakultas.event')->with('error', 'Anda tidak memiliki izin untuk mengedit acara ini.');
            }
    
            // Kirim data acara ke view form edit
            return view('pointakses.fakultas.page.fakultas_page_update_event', compact('event'));
        }
    
        return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
    }
    public function delete_event($id)
    {
        // Cek apakah user sudah login
        if (Auth::check()) {
            // Mencari acara berdasarkan ID
            $event = Event::findOrFail($id);
    
            // Pastikan user yang login adalah pemilik acara
            if ($event->user_id !== Auth::id()) {
                return redirect()->route('fakultas.event')->with('error', 'Anda tidak memiliki izin untuk menghapus acara ini.');
            }
    
            // Hapus logo jika ada
            if ($event->logo && Storage::disk('public')->exists($event->logo)) {
                Storage::disk('public')->delete($event->logo);
            }
    
            // Hapus signature jika ada
            if ($event->signature && Storage::disk('public')->exists($event->signature)) {
                Storage::disk('public')->delete($event->signature);
            }
    
            // Hapus template PDF jika ada
            if ($event->template_pdf && Storage::disk('public')->exists($event->template_pdf)) {
                Storage::disk('public')->delete($event->template_pdf);
            }
    
            // Hapus acara
            $event->delete();
    
            // Redirect kembali ke halaman acara dengan pesan sukses
            return redirect()->route('fakultas.event')->with('success', 'Acara berhasil dihapus!');
        }
    
        return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
    }
}
