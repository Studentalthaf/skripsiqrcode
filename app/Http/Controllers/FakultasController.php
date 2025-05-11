<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;
use App\Models\Participant;

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
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date' => 'required|date',
                'type_event' => 'required|string|max:255',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'template_pdf' => 'nullable|mimes:pdf|max:5120',
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
        } catch (\Exception $e) {
            Log::error('Error saving event: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan acara: ' . $e->getMessage());
        }
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
    /**
     * Menampilkan halaman untuk mengatur placeholder
     */
    public function edit_placeholder($event_id)
    {
        try {
            // Pastikan acara milik fakultas yang login
            $event = Event::where('id', $event_id)->where('user_id', Auth::id())->firstOrFail();
            Log::info('Mengakses edit_placeholder untuk event_id: ' . $event_id);

            // Periksa template_pdf
            if (!$event->template_pdf || !Storage::disk('public')->exists($event->template_pdf)) {
                Log::info('Template PDF kosong atau tidak ditemukan untuk event_id: ' . $event_id);
                return redirect()->route('fakultas.event')->with('error', 'Acara ini belum memiliki template PDF atau file tidak ditemukan.');
            }

            // Validasi placeholders
            if ($event->placeholders) {
                json_decode($event->placeholders, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::warning('Data placeholders tidak valid untuk event_id: ' . $event_id);
                    $event->placeholders = null;
                }
            }

            // Jika tidak ada placeholders, gunakan name_x dan name_y sebagai fallback
            if (!$event->placeholders && $event->name_x !== null && $event->name_y !== null) {
                $event->placeholders = json_encode([['x' => $event->name_x, 'y' => $event->name_y]]);
            }

            return view('pointakses.fakultas.page.fakultas_page_edit_placeholders', compact('event'));
        } catch (\Exception $e) {
            Log::error('Error loading placeholder edit page: ' . $e->getMessage());
            return redirect()->route('fakultas.event')->with('error', 'Gagal memuat halaman pengaturan placeholder: ' . $e->getMessage());
        }
    }

    /**
     * Menyimpan koordinat placeholder
     */
    public function save_placeholder(Request $request, $event_id)
    {
        try {
            // Pastikan acara milik fakultas yang login
            $event = Event::where('id', $event_id)->where('user_id', Auth::id())->firstOrFail();

            $request->validate([
                'placeholders' => 'required|json',
            ]);

            $placeholders = json_decode($request->placeholders, true);
            if (!is_array($placeholders) || empty($placeholders)) {
                return redirect()->route('fakultas.edit.placeholder', ['event_id' => $event_id])
                    ->with('error', 'Tidak ada placeholder yang disimpan.');
            }

            // Simpan placeholders
            $event->update(['placeholders' => json_encode($placeholders)]);

            // Opsional: Jika masih ingin menyimpan name_x dan name_y untuk kompatibilitas
            if (!empty($placeholders[0])) {
                $event->update([
                    'name_x' => $placeholders[0]['x'],
                    'name_y' => $placeholders[0]['y'],
                ]);
            }

            return redirect()->route('fakultas.index.participant', ['event_id' => $event_id])
                ->with('success', 'Koordinat placeholder berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Error saving placeholders: ' . $e->getMessage());
            return redirect()->route('fakultas.edit.placeholder', ['event_id' => $event_id])
                ->with('error', 'Gagal menyimpan koordinat placeholder: ' . $e->getMessage());
        }
    }
    /**
     * Menghasilkan sertifikat PDF untuk peserta
     */
    /**
     * Menampilkan atau mengunduh sertifikat peserta
     */
    public function viewCertificate($event_id, $participant_id)
    {
        try {
            // Pastikan acara milik fakultas yang login
            $event = Event::where('id', $event_id)->where('user_id', Auth::id())->firstOrFail();

            // Ambil data peserta
            $participant = Participant::where('id', $participant_id)->where('event_id', $event_id)->firstOrFail();

            // Dekripsi data peserta
            $encryptionKey = $this->getEncryptionKey($event->encryption_key);
            $decryptedData = $this->decryptData($participant->encrypted_data, $encryptionKey);

            // Dapatkan path sertifikat
            $certificatePath = storage_path('app/public/' . $decryptedData['certificate_path']);

            // Periksa apakah file sertifikat ada
            if (!file_exists($certificatePath)) {
                Log::error('Certificate file not found: ' . $certificatePath);
                return redirect()->route('fakultas.index.participant', ['event_id' => $event_id])
                    ->with('error', 'File sertifikat tidak ditemukan.');
            }

            // Kembalikan file PDF sebagai respons
            return response()->file($certificatePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="certificate_' . $participant_id . '.pdf"',
            ]);
        } catch (\Exception $e) {
            Log::error('Error viewing certificate: ' . $e->getMessage());
            return redirect()->route('fakultas.index.participant', ['event_id' => $event_id])
                ->with('error', 'Gagal menampilkan sertifikat: ' . $e->getMessage());
        }
    }
    private function generateCertificate(Participant $participant)
    {
        try {
            Log::info('Generating certificate for participant_id: ' . $participant->id);
            $event = Event::findOrFail($participant->event_id);
            Log::info('Event found: ' . $event->id . ', template_pdf: ' . $event->template_pdf);

            $encryptionKey = $this->getEncryptionKey($event->encryption_key);
            Log::info('Encryption key: ' . bin2hex($encryptionKey));

            $decryptedData = $this->decryptData($participant->encrypted_data, $encryptionKey);
            Log::info('Decrypted data: ' . json_encode($decryptedData));

            $sourcePdfPath = storage_path('app/public/' . $event->template_pdf);
            if (!file_exists($sourcePdfPath)) {
                Log::error('Source PDF not found: ' . $sourcePdfPath);
                throw new \Exception('File template PDF tidak ditemukan.');
            }

            $outputPath = storage_path('app/public/' . $decryptedData['certificate_path']);
            Log::info('Output path: ' . $outputPath);

            $pdfWidthPt = 841.92;
            $pdfHeightPt = 595.5;

            $fpdi = new Fpdi();
            $fpdi->AddPage('L', [$pdfWidthPt, $pdfHeightPt]);
            $fpdi->setSourceFile($sourcePdfPath);
            $tplIdx = $fpdi->importPage(1);
            $fpdi->useTemplate($tplIdx, 0, 0, $pdfWidthPt, $pdfHeightPt);

            $fpdi->SetFont('Arial', 'B', 90);
            $fpdi->SetTextColor(0, 0, 0);

            $placeholders = $event->placeholders ? json_decode($event->placeholders, true) : [];
            if (empty($placeholders) && $event->name_x !== null && $event->name_y !== null) {
                $placeholders = [['x' => $event->name_x, 'y' => $pdfHeightPt - $event->name_y]];
            }
            if (empty($placeholders)) {
                $placeholders = [['x' => 100, 'y' => $pdfHeightPt - 100]];
            }

            foreach ($placeholders as $p) {
                $x = floatval($p['x']);
                $y = $pdfHeightPt - floatval($p['y']);
                $fpdi->SetXY($x, $y);
                $fpdi->Cell(50, 10, utf8_decode($decryptedData['name']), 0, 1, 'C');
            }

            $fpdi->Output($outputPath, 'F');
        } catch (\Exception $e) {
            Log::error('Error generating certificate: ' . $e->getMessage());
            throw $e;
        }
    }












    public function index_participant($event_id)
    {
        try {
            // Pastikan acara milik fakultas yang login
            $event = Event::where('id', $event_id)->where('user_id', Auth::id())->firstOrFail();
            $participants = Participant::where('event_id', $event_id)->get();
            $user = Auth::user();

            $encryptionKey = $this->getEncryptionKey($event->encryption_key);

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
                    Log::error('Error decrypting participant data: ' . $e->getMessage());
                    $participant->decrypted_name = "Gagal Dekripsi";
                }
            }

            return view('pointakses.fakultas.page.fakultas_index_participant', compact('participants', 'event_id'));
        } catch (\Exception $e) {
            Log::error('Error fetching participants: ' . $e->getMessage());
            return redirect()->route('fakultas.event')->with('error', 'Gagal memuat daftar peserta: ' . $e->getMessage());
        }
    }

    public function create_participant($event_id)
    {
        try {
            // Pastikan acara milik fakultas yang login
            $event = Event::where('id', $event_id)->where('user_id', Auth::id())->firstOrFail();

            // Ambil user dengan role 'user' saja
            $users = User::where('role', 'user')->get();

            return view('pointakses.fakultas.page.fakultas_page_create_participant', compact('event_id', 'users'));
        } catch (\Exception $e) {
            Log::error('Error loading create participant form: ' . $e->getMessage());
            return redirect()->route('fakultas.event')->with('error', 'Gagal memuat form tambah peserta: ' . $e->getMessage());
        }
    }

    /**
     * Menyimpan peserta baru untuk acara
     */
    public function store_participant(Request $request, $event_id)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $event = Event::where('id', $event_id)->where('user_id', Auth::id())->firstOrFail();
            $user = User::findOrFail($request->user_id);

            $folderPath = 'participants';
            $certificateFilename = uniqid() . '_certificate.pdf';
            $certificatePath = $folderPath . '/' . $certificateFilename;

            $data = [
                'name' => $user->nama_lengkap,
                'email' => $user->email,
                'phone' => $user->no_tlp,
                'event_id' => $event->id,
                'event_title' => $event->title,
                'certificate_path' => $certificatePath,
            ];

            Log::info('Data to encrypt: ' . json_encode($data));
            $encryptionKey = $this->getEncryptionKey($event->encryption_key);
            Log::info('Encryption key: ' . bin2hex($encryptionKey));
            $encryptedData = $this->encryptData($data, $encryptionKey);
            Log::info('Encrypted data: ' . $encryptedData);

            $participant = new Participant();
            $participant->event_id = $event->id;
            $participant->user_id = $user->id;
            $participant->encrypted_data = $encryptedData;
            $participant->save();

            Log::info('Participant saved: ' . $participant->id);
            $this->generateCertificate($participant);
            Log::info('Certificate generated for participant: ' . $participant->id);

            return redirect()->route('fakultas.index.participant', ['event_id' => $event_id])
                ->with('success', 'Peserta berhasil didaftarkan dan sertifikat berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error('Error storing participant: ' . $e->getMessage());
            return redirect()->route('fakultas.index.participant', ['event_id' => $event_id])
                ->with('error', 'Gagal menambahkan peserta: ' . $e->getMessage());
        }
    }



    /**
     * Mendapatkan kunci enkripsi
     */
    private function getEncryptionKey($eventKey)
    {
        $encryptionKey = $eventKey ?: env('CHACHA20_SECRET_KEY');
        Log::info('Encryption key used: ' . $encryptionKey);
        if (!$encryptionKey) {
            throw new \Exception("Kunci enkripsi tidak ditemukan!");
        }

        $binaryKey = hex2bin($encryptionKey);
        if (strlen($binaryKey) !== SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_KEYBYTES) {
            throw new \Exception("Kunci enkripsi tidak valid! Panjangnya harus 32 byte.");
        }

        return $binaryKey;
    }

    /**
     * Mengenkripsi data peserta
     */
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

    /**
     * Mendekripsi data peserta
     */
    private function decryptData($encryptedData, $key)
    {
        if (strlen($key) !== SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_KEYBYTES) {
            throw new \Exception('Panjang kunci enkripsi tidak sesuai.');
        }

        $decodedData = base64_decode($encryptedData, true);
        if ($decodedData === false) {
            Log::error('Invalid base64 data: ' . $encryptedData);
            throw new \Exception("Data terenkripsi tidak valid (bukan format base64)");
        }

        $nonceLength = SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NPUBBYTES;
        if (strlen($decodedData) < $nonceLength) {
            Log::error('Data too short for nonce: ' . $encryptedData);
            throw new \Exception("Data terenkripsi terlalu pendek untuk memuat nonce.");
        }

        $nonce = substr($decodedData, 0, $nonceLength);
        $ciphertext = substr($decodedData, $nonceLength);

        $ad = "skripsiku";

        $decrypted = sodium_crypto_aead_chacha20poly1305_ietf_decrypt(
            $ciphertext,
            $ad,
            $nonce,
            $key
        );

        if ($decrypted === false) {
            Log::error('Decryption failed. Key: ' . bin2hex($key) . ', Nonce: ' . bin2hex($nonce));
            throw new \Exception("Dekripsi gagal. Kemungkinan kunci atau nonce tidak cocok.");
        }

        $decoded = json_decode($decrypted, true);
        if ($decoded === null) {
            Log::error('Invalid JSON after decryption: ' . $decrypted);
            throw new \Exception("Data terdekripsi bukan JSON yang valid.");
        }

        return $decoded;
    }
}
