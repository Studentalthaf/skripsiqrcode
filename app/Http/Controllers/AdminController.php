<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Participant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;  
use App\Models\Pdf;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

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

    public function placeholder($id)
    {
        $event = Event::findOrFail($id);
        return view('pointakses.admin.page.admin_event_placeholder', compact('event'));
    }
    
    public function savePlaceholder(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $event->placeholders = $request->input('placeholders');
        $event->save();
    
        return redirect()->back()->with('success', 'Placeholder berhasil disimpan.');
    }
    
    

    public function generatePdf($id)
    {
        $event = Event::findOrFail($id);
        $peserta = Participant::first();
    
        if (!$peserta) {
            return back()->with('error', 'Data peserta tidak ditemukan.');
        }
    
        $sourcePdfPath = storage_path('app/public/' . $event->template_pdf);
        $outputPath = storage_path('app/public/generated_' . $event->id . '.pdf');
    
        $pdfWidthPt = 841.92;
        $pdfHeightPt = 595.5;
    
        $fpdi = new Fpdi();
        $fpdi->AddPage('L', [$pdfWidthPt, $pdfHeightPt]);
        $fpdi->setSourceFile($sourcePdfPath);
        $tplIdx = $fpdi->importPage(1);
        $fpdi->useTemplate($tplIdx, 0, 0, $pdfWidthPt, $pdfHeightPt);
    
        $fpdi->SetFont('Arial', 'B', 90);
        $fpdi->SetTextColor(255, 0, 0);
    
        // Menggunakan posisi yang telah disimpan untuk placeholder
        $x = floatval($event->name_x);
        $y = $pdfHeightPt - floatval($event->name_y); // Invert y position to match PDF coordinates
    
        $fpdi->SetXY($x, $y);
        $fpdi->Cell(50, 10, utf8_decode($peserta->nama), 0, 1, 'C');
    
        $fpdi->Output($outputPath, 'F');
    
        return response()->download($outputPath);
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
            'pdf' => 'nullable|mimes:pdf|max:5120', // max 5MB
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
    public function index_participant($event_id)
    {
        $participants = Participant::where('event_id', $event_id)->get();
        $event = Event::findOrFail($event_id);
        $encryptionKey = $this->getEncryptionKey($event->encryption_key);
        $user = auth()->user();

        foreach ($participants as $participant) {
            try {
                $decryptedData = $this->decryptData($participant->encrypted_data, $encryptionKey);
                $participant->decrypted_name = $decryptedData['name'];
                $participant->decrypted_email = $decryptedData['email'];
                $participant->decrypted_phone = $decryptedData['phone'];
                $participant->decrypted_logo = $event->logo;
                $participant->decrypted_signature = $event->signature;
                $participant->decrypted_nama_lengkap = $user->nama_lengkap;
                $participant->decrypted_date = $event->date;
                $participant->decrypted_title = $event->title;
            } catch (\Exception $e) {
                $participant->decrypted_name = "Gagal Dekripsi";
            }
        }

        return view('pointakses.admin.page.admin_index_participant', compact('participants', 'event_id'));
    }
    // public function create_participant($event_id)
    // {
    //     $event = Event::findOrFail($event_id);
    //     return view('pointakses.admin.page.admin_page_create_participant', compact('event_id'));
    // }
    public function create_participant($event_id)
    {
        $event = Event::findOrFail($event_id);

        // Ambil user dengan role 'user' saja
        $users = User::where('role', 'user')->get();

        return view('pointakses.admin.page.admin_page_create_participant', compact('event_id', 'users'));
    }

    // public function store_participant(Request $request, $event_id)
    // {
    //     try {
    //         $request->validate([
    //             'nama_peserta' => 'required|string|max:255',
    //             'email' => 'required|email|max:255',
    //             'telepon' => 'required|string|max:15',
    //         ]);

    //         $event = Event::findOrFail($event_id);
    //         $user = auth()->user(); // Ambil user yang sedang login

    //         $participantData = [
    //             'name' => $request->nama_peserta,
    //             'email' => $request->email,
    //             'phone' => $request->telepon,
    //             'tanda_tangan' => $event->signature,
    //             'logo' => $event->logo,
    //             'nama_lengkap' => $user->nama_lengkap,
    //             'date' => $event->date,
    //             'title' => $event->title,
    //         ];

    //         $encryptionKey = $this->getEncryptionKey($event->encryption_key);
    //         $encryptedData = $this->encryptData($participantData, $encryptionKey);

    //         $participant = new Participant();
    //         $participant->user_id = $user->id; // Pastikan user_id diisi
    //         $participant->event_id = $event_id;
    //         $participant->encrypted_data = $encryptedData;
    //         $participant->save();

    //         return redirect()->route('admin.index.participant', ['event_id' => $event_id])
    //             ->with('success', 'Peserta berhasil ditambahkan!');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    //     }
    // }
    public function store_participant(Request $request, $event_id)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id', // Pastikan user_id ada di tabel users
            ]);

            $event = Event::findOrFail($event_id);
            $user = User::findOrFail($request->user_id); // Ambil user berdasarkan ID

            // Data yang akan dienkripsi
            $participantData = [
                'name' => $user->nama_lengkap,
                'email' => $user->email,
                'phone' => $user->telepon,
                'tanda_tangan' => $event->signature,
                'logo' => $event->logo,
                'nama_lengkap' => $user->nama_lengkap,
                'date' => $event->date,
                'title' => $event->title,
            ];

            $encryptionKey = $this->getEncryptionKey($event->encryption_key);
            $encryptedData = $this->encryptData($participantData, $encryptionKey);

            // Simpan ke database
            $participant = new Participant();
            $participant->user_id = $user->id;
            $participant->event_id = $event_id;
            $participant->encrypted_data = $encryptedData;
            $participant->save();

            return redirect()->route('admin.index.participant', ['event_id' => $event_id])
                ->with('success', 'Peserta berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function getEncryptionKey()
    {
        $encryptionKey = env('CHACHA20_SECRET_KEY');

        if (!$encryptionKey) {
            throw new \Exception("Kunci enkripsi tidak ditemukan dalam .env!");
        }

        $binaryKey = hex2bin($encryptionKey);

        if (strlen($binaryKey) !== SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_KEYBYTES) {
            throw new \Exception("Kunci enkripsi tidak valid! Panjangnya harus 32 byte, ditemukan: " . strlen($binaryKey));
        }

        return $binaryKey;
    }

    private function encryptData($data, $key)
    {
        if (strlen($key) !== SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_KEYBYTES) {
            throw new \Exception('Panjang kunci enkripsi tidak sesuai.');
        }

        $nonce = random_bytes(SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NPUBBYTES);
        $ad = "skripsiku";

        $ciphertext = sodium_crypto_aead_chacha20poly1305_ietf_encrypt(
            json_encode($data),
            $ad,
            $nonce,
            $key
        );

        return base64_encode($nonce . $ciphertext);
    }
    private function decryptData($encryptedData, $key)
    {
        if (strlen($key) !== SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_KEYBYTES) {
            throw new \Exception('Panjang kunci enkripsi tidak sesuai.');
        }

        $decodedData = base64_decode($encryptedData);
        if ($decodedData === false) {
            throw new \Exception("Data terenkripsi tidak valid (bukan format base64)");
        }

        $nonce = substr($decodedData, 0, SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NPUBBYTES);
        $ciphertext = substr($decodedData, SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NPUBBYTES);

        $ad = "skripsiku";

        $decrypted = sodium_crypto_aead_chacha20poly1305_ietf_decrypt(
            $ciphertext,
            $ad,
            $nonce,
            $key
        );

        if ($decrypted === false) {
            throw new \Exception("Dekripsi gagal. Kemungkinan kunci atau nonce tidak cocok.");
        }

        return json_decode($decrypted, true);
    }
    public function edit_participant($event_id, $participant_id)
    {
        $event = Event::findOrFail($event_id);
        $participant = Participant::findOrFail($participant_id);

        // Ambil kunci enkripsi
        $encryptionKey = $this->getEncryptionKey($event->encryption_key);
        $decryptedData = $this->decryptData($participant->encrypted_data, $encryptionKey);

        return view('pointakses.admin.page.admin_page_edit_participant', compact('event_id', 'participant', 'decryptedData'));
    }
    public function update_participant(Request $request, $event_id, $participant_id)
    {
        try {
            // Validasi input
            $request->validate([
                'nama_peserta' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'telepon' => 'required|string|max:15',

            ]);

            // Ambil peserta
            $participant = Participant::findOrFail($participant_id);

            // Data peserta
            $participantData = [
                'name' => $request->nama_peserta,
                'email' => $request->email,
                'phone' => $request->telepon,

            ];

            // Ambil kunci enkripsi dari event
            $event = Event::findOrFail($event_id);
            $encryptionKey = $this->getEncryptionKey($event->encryption_key);

            // Enkripsi data peserta
            $encryptedData = $this->encryptData($participantData, $encryptionKey);

            // Update peserta ke database
            $participant->encrypted_data = $encryptedData;
            $participant->save();

            return redirect()->route('admin.index.participant', ['event_id' => $event_id])
                ->with('success', 'Peserta berhasil diperbarui!');
        } catch (\Exception $e) {
            // Log error dan tampilkan pesan
            Log::error("Peserta gagal diperbarui: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui peserta: ' . $e->getMessage());
        }
    }
    public function show_participant($event_id, $participant_id)
    {
        // Ambil peserta berdasarkan ID
        $participant = Participant::findOrFail($participant_id);

        // Ambil data event terkait dengan peserta
        $event = Event::findOrFail($event_id);
        $user = auth()->user();

        // Ambil kunci enkripsi
        $encryptionKey = $this->getEncryptionKey($event->encryption_key);

        // Dekripsi data peserta
        $decryptedData = $this->decryptData($participant->encrypted_data, $encryptionKey);

        // Menyimpan informasi yang didekripsi ke dalam peserta
        $participant->decrypted_name = $decryptedData['name'];
        $participant->decrypted_email = $decryptedData['email'];
        $participant->decrypted_phone = $decryptedData['phone'];
        $participant->decrypted_logo = $event->logo;
        $participant->decrypted_signature = $event->signature;
        $participant->decrypted_nama_lengkap = $user->nama_lengkap;
        $participant->decrypted_date = $event->date;
        $participant->decrypted_title = $event->title;

        return view('pointakses.admin.page.admin_page_show_participant', compact('participant', 'event'));
    }
    public function destroy_participant($event_id, $participant_id)
    {
        // Hapus peserta berdasarkan ID
        $participant = Participant::findOrFail($participant_id);
        $participant->delete();

        return redirect()->route('admin.index.participant', ['event_id' => $event_id])
            ->with('success', 'Peserta berhasil dihapus!');
    }
}
