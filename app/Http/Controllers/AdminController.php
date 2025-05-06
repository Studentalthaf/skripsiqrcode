<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

use App\Models\Event;
use App\Models\Participant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Hash;

use Endroid\QrCode\QrCode;

use setasign\Fpdi\Fpdi;
use FPDF;
use App\Models\Pdf;
use Illuminate\Support\Str;


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

        $placeholders = $request->input('placeholders');

        try {
            $coords = json_decode($placeholders, true);

            if (is_array($coords) && count($coords) > 0) {
                // Validasi setiap koordinat
                foreach ($coords as $coord) {
                    if (!isset($coord['x']) || !isset($coord['y'])) {
                        return redirect()->route('admin.event')->with('error', 'Format koordinat tidak valid.');
                    }

                    if ($coord['x'] < 0 || $coord['y'] < 0) {
                        return redirect()->route('admin.event')->with('error', 'Koordinat tidak boleh negatif.');
                    }
                }

                // Simpan seluruh koordinat placeholder
                $event->name_x = (float)$coords[0]['x']; // Jika hanya ingin menyimpan koordinat pertama
                $event->name_y = (float)$coords[0]['y']; // Jika hanya ingin menyimpan koordinat pertama

                // Jika ingin menyimpan semua placeholder
                $event->placeholders = json_encode($coords);  // Pastikan ada field placeholders di tabel event

                $event->save();

                return redirect()->route('admin.event')->with('success', 'Koordinat placeholder berhasil disimpan.');
            } else {
                return redirect()->route('admin.event')->with('warning', 'Tidak ada koordinat ditemukan.');
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.event')->with('error', 'Gagal memproses data placeholder: ' . $e->getMessage());
        }
    }





    public function viewCertificate($event_id, $participant_id)
    {
        // Cari data peserta berdasarkan participant_id
        $participant = Participant::findOrFail($participant_id);
        $event = Event::findOrFail($event_id);

        // Dekripsi data peserta
        $encryptionKey = $this->getEncryptionKey($event->encryption_key);
        $data = $this->decryptData($participant->encrypted_data, $encryptionKey);

        // Tentukan path file sertifikat berdasarkan data yang didekripsi
        $pdfPath = storage_path('app/public/' . $data['certificate_path']);

        // Cek apakah file sertifikat ada
        if (!file_exists($pdfPath)) {
            return redirect()->back()->with('error', 'Sertifikat peserta tidak ditemukan.');
        }

        // Kembalikan file sertifikat untuk ditampilkan
        return response()->file($pdfPath);
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
    public function admin_user_store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'NIM' => 'required|string|max:20|unique:users,NIM',
            'email' => 'required|email|unique:users,email',
            'no_tlp' => 'nullable|string|max:15',
            'unit_kerja' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'role' => 'required|in:admin,user,fakultas',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            // Membuat user baru
            $user = new User();
            $user->nama_lengkap = $validated['nama_lengkap'];
            $user->NIM = $validated['NIM'];
            $user->email = $validated['email'];
            $user->no_tlp = $validated['no_tlp'];
            $user->unit_kerja = $validated['unit_kerja'];
            $user->alamat = $validated['alamat'];
            $user->role = $validated['role'];
            $user->password = bcrypt($validated['password']);
            $user->save();

            return redirect()->route('admin.users')->with('success', 'User berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error saving user: ' . $e->getMessage());
            return redirect()->route('admin.users')->with('error', 'Gagal menambah user: ' . $e->getMessage());
        }
    }
    public function admin_edit_user($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json($user);
        } catch (\Exception $e) {
            Log::error('Error fetching user: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengambil data user'], 500);
        }
    }

    public function admin_update_user(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'NIM' => 'required|string|max:20|unique:users,NIM,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'no_tlp' => 'nullable|string|max:15',
            'unit_kerja' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'role' => 'required|in:admin,user,fakultas',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            $user->nama_lengkap = $validated['nama_lengkap'];
            $user->NIM = $validated['NIM'];
            $user->email = $validated['email'];
            $user->no_tlp = $validated['no_tlp'];
            $user->unit_kerja = $validated['unit_kerja'];
            $user->alamat = $validated['alamat'];
            $user->role = $validated['role'];
            if ($request->filled('password')) {
                $user->password = bcrypt($validated['password']);
            }
            $user->save();

            return redirect()->route('admin.users')->with('success', 'User berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->route('admin.users')->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }
    public function admin_destroy_user($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return redirect()->route('admin.users')->with('success', 'User berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return redirect()->route('admin.users')->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }



    public function edit_user($id)
    {
        $user = User::findOrFail($id);
        return view('pointakses.admin.page.admin_page_edit_user', compact('user'));
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



    public function store_participant(Request $request, $event_id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $event = Event::findOrFail($event_id);
        $user = User::findOrFail($request->user_id);

        // Generate path file sertifikat
        $folderPath = 'participants';
        $certificateFilename = uniqid() . '_certificate.pdf'; // Kasih nama unik untuk setiap sertifikat
        $certificatePath = $folderPath . '/' . $certificateFilename;

        // Data untuk enkripsi
        $data = [
            'name' => $user->nama_lengkap,
            'email' => $user->email,
            'phone' => $user->no_tlp,
            'event_id' => $event->id,
            'event_title' => $event->title,
            'certificate_path' => $certificatePath, // Menyimpan path sertifikat
        ];

        // Enkripsi data peserta
        $encryptionKey = $this->getEncryptionKey($event->encryption_key);
        $encryptedData = $this->encryptData($data, $encryptionKey);

        // Simpan data peserta
        $participant = new Participant();
        $participant->event_id = $event->id;
        $participant->user_id = $user->id;
        $participant->encrypted_data = $encryptedData;
        $participant->save();

        // ✨ Generate sertifikat otomatis setelah peserta disimpan
        try {
            $this->generateCertificate($participant);
        } catch (\Exception $e) {
            return redirect()->route('admin.index.participant', ['event_id' => $event_id])
                ->with('error', 'Gagal membuat sertifikat: ' . $e->getMessage());
        }

        return redirect()->route('admin.index.participant', ['event_id' => $event_id])
            ->with('success', 'Peserta berhasil didaftarkan dan sertifikat berhasil dibuat.');
    }

    private function generateCertificate(Participant $participant)
    {
        $event = $participant->event;
        $user = $participant->user;

        if (empty($event->template_pdf)) {
            throw new \Exception('Template PDF belum diatur untuk event ini.');
        }

        $templatePath = storage_path('app/public/' . $event->template_pdf);
        if (!file_exists($templatePath)) {
            throw new \Exception('File template sertifikat tidak ditemukan.');
        }

        // Mengatur default koordinat jika kosong
        if (is_null($event->name_x) || is_null($event->name_y)) {
            $event->name_x = 50; // default value for X
            $event->name_y = 100; // default value for Y
        }

        // Ambil data terenkripsi peserta
        $encryptionKey = $this->getEncryptionKey($event->encryption_key);
        $data = $this->decryptData($participant->encrypted_data, $encryptionKey);

        // Lokasi output file sertifikat
        $pdfOutputPath = storage_path('app/public/' . $data['certificate_path']);

        // Buat objek FPDI
        $pdf = new \setasign\Fpdi\Fpdi();

        // Tentukan sumber file template
        $pageCount = $pdf->setSourceFile($templatePath);

        // Import halaman pertama
        $templateId = $pdf->importPage(1);

        // Dapatkan ukuran asli halaman pertama
        $templateSize = $pdf->getTemplateSize($templateId);

        // Tambahkan halaman (walaupun tidak membuat halaman baru, halaman template tetap harus ditambahkan terlebih dahulu)
        $pdf->AddPage($templateSize['orientation'], [$templateSize['width'], $templateSize['height']]);

        // Gunakan halaman template
        $pdf->useTemplate($templateId);

        // Tulis nama peserta di atas template
        $pdf->SetFont('Helvetica', 'B', 48); // Ukuran font lebih besar
        $pdf->SetTextColor(0, 0, 0); // Hitam
        $pdf->SetXY($event->name_x, $event->name_y); // Koordinat untuk nama peserta
        $pdf->Cell(0, 10, $data['name'], 0, 1, 'C'); // Tulis nama peserta di posisi yang telah ditentukan

        // Pastikan folder penyimpanan ada
        $folderPath = storage_path('app/public/participants');
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Simpan file sertifikat
        $pdf->Output('F', $pdfOutputPath);
    }

    public function create_participant($event_id)
    {
        $event = Event::findOrFail($event_id);

        // Ambil user dengan role 'user' saja
        $users = User::where('role', 'user')->get();

        return view('pointakses.admin.page.admin_page_create_participant', compact('event_id', 'users'));
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

    public function downloadQRCode($id)
    {
        try {
            $participant = Participant::findOrFail($id);
            $user = auth()->user();

            if (!$user->is_admin && $participant->user_id !== $user->id) {
                abort(403, 'Anda tidak berhak mengunduh QR code ini.');
            }

            $payload = json_encode([
                'id' => $participant->id,
                'data' => $participant->encrypted_data,
                'timestamp' => time() // Tambahkan timestamp untuk validasi
            ]);

            // Generate QR code dengan Endroid
            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($payload)
                ->size(600)
                ->margin(30) // Tambahkan margin untuk pembacaan yang lebih baik
                ->build();

            // Gunakan direktori sementara yang tidak dapat diakses publik
            $filename = 'qrcode_' . $participant->id . '_' . uniqid() . '.png';
            $filePath = storage_path('app/temp/' . $filename);

            // Buat direktori jika belum ada
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            file_put_contents($filePath, $result->getString());

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            // Log error
            \Log::error('QR Code generation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghasilkan QR Code'], 500);
        }
    }
}
