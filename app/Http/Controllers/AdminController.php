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
use Carbon\Carbon;
use setasign\Fpdi\Fpdi;


class AdminController extends Controller
{
   public function index()
{
    $users = User::where('role', 'admin')->get();
    $userCount = User::where('role', 'user')->count();
    $fakultasCount = User::where('role', 'fakultas')->count();
    $eventCount = Event::count();

    $upcomingEvents = Event::with('user')
        ->where('date', '>=', now())
        ->orderBy('date', 'asc')
        ->get(['id', 'title', 'date', 'type_event', 'user_id']);

    return view('pointakses.admin.index', compact(
        'users',
        'userCount',
        'fakultasCount', // Ensure this is exactly 'fakultasCount'
        'eventCount',
        'upcomingEvents'
    ));
}


    public function event()
    {
        // Mengambil semua event dari database
        $events = Event::all();
        $events = Event::paginate(10);

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

            return view('pointakses.admin.page.admin_page_edit_placeholders', compact('event'));
        } catch (\Exception $e) {
            Log::error('Error loading placeholder edit page: ' . $e->getMessage());
            return redirect()->route('admin.event')->with('error', 'Gagal memuat halaman pengaturan placeholder: ' . $e->getMessage());
        }
    }


    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date' => 'required|date',
                'type_event' => 'required|string|max:255',
                'logo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'signature' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'template_pdf' => 'required|mimes:pdf|max:5120',
                'faculty_id' => 'required|exists:users,id,role,admin',
            ]);

            Log::info('Validated data', $validated);

            if (Auth::check() && Auth::user()->role === 'admin') {
                Log::info('User logged in', ['user_id' => Auth::id(), 'role' => Auth::user()->role]);

                $event = new Event();
                $event->user_id = $request->faculty_id;
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

                Log::info('Event data before save', $event->toArray());
                $event->save();
                Log::info('Event saved', ['event_id' => $event->id]);

                return redirect()->route('admin.event')->with('success', 'Acara berhasil ditambahkan!');
            }

            Log::warning('Unauthorized store attempt', ['user_id' => Auth::id()]);
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        } catch (\Exception $e) {
            Log::error('Error saving event: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan acara: ' . $e->getMessage());
        }
    }

    public function edit_event($id)
    {
        try {
            if (Auth::check() && Auth::user()->role === 'admin') {
                Log::info('Accessing edit form', ['user_id' => Auth::id(), 'event_id' => $id]);
                $event = Event::findOrFail($id);
                return view('pointakses.admin.page.admin_page_update_event', compact('event'));
            }
            Log::warning('Unauthorized access to edit form', ['user_id' => Auth::id()]);
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        } catch (\Exception $e) {
            Log::error('Error accessing edit form: ' . $e->getMessage());
            return redirect()->route('admin.event')->with('error', 'Gagal memuat form edit: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
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
                'faculty_id' => 'required|exists:users,id,role,admin',
            ]);

            Log::info('Validated data for update', $validated);

            if (Auth::check() && Auth::user()->role === 'admin') {
                Log::info('User logged in', ['user_id' => Auth::id(), 'role' => Auth::user()->role]);

                $event = Event::findOrFail($id);

                $event->user_id = $request->faculty_id;
                $event->title = $request->title;
                $event->description = $request->description;
                $event->date = $request->date;
                $event->type_event = $request->type_event;

                if ($request->hasFile('logo')) {
                    if ($event->logo && Storage::disk('public')->exists($event->logo)) {
                        Storage::disk('public')->delete($event->logo);
                    }
                    $event->logo = $request->file('logo')->store('logos', 'public');
                }

                if ($request->hasFile('signature')) {
                    if ($event->signature && Storage::disk('public')->exists($event->signature)) {
                        Storage::disk('public')->delete($event->signature);
                    }
                    $event->signature = $request->file('signature')->store('signatures', 'public');
                }

                if ($request->hasFile('template_pdf')) {
                    if ($event->template_pdf && Storage::disk('public')->exists($event->template_pdf)) {
                        Storage::disk('public')->delete($event->template_pdf);
                    }
                    $event->template_pdf = $request->file('template_pdf')->store('pdfs', 'public');
                }

                Log::info('Event data before update', $event->toArray());
                $event->save();
                Log::info('Event updated', ['event_id' => $event->id]);

                return redirect()->route('admin.event')->with('success', 'Acara berhasil diupdate!');
            }

            Log::warning('Unauthorized update attempt', ['user_id' => Auth::id()]);
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        } catch (\Exception $e) {
            Log::error('Error updating event: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengupdate acara: ' . $e->getMessage());
        }
    }

    public function delete_event($id)
    {
        try {
            if (Auth::check() && Auth::user()->role === 'admin') {
                Log::info('Attempting to delete event', ['user_id' => Auth::id(), 'event_id' => $id]);
                $event = Event::findOrFail($id);

                if ($event->logo && Storage::disk('public')->exists($event->logo)) {
                    Storage::disk('public')->delete($event->logo);
                }
                if ($event->signature && Storage::disk('public')->exists($event->signature)) {
                    Storage::disk('public')->delete($event->signature);
                }
                if ($event->template_pdf && Storage::disk('public')->exists($event->template_pdf)) {
                    Storage::disk('public')->delete($event->template_pdf);
                }

                $event->delete();
                Log::info('Event deleted', ['event_id' => $id]);

                return redirect()->route('admin.event')->with('success', 'Acara berhasil dihapus!');
            }

            Log::warning('Unauthorized delete attempt', ['user_id' => Auth::id()]);
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        } catch (\Exception $e) {
            Log::error('Error deleting event: ' . $e->getMessage());
            return redirect()->route('admin.event')->with('error', 'Gagal menghapus acara: ' . $e->getMessage());
        }
    }

    public function users()
    {
        $users = User::paginate(10); // Ubah dari all() ke paginate()
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
            'role' => 'required|in:admin,user,admin',
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

public function viewCertificate($event_id, $participant_id)
{
    try {
        // Ambil acara tanpa memeriksa user_id untuk admin
        $event = Event::where('id', $event_id)->firstOrFail();

        // Jika bukan admin, batasi akses ke acara milik fakultas yang login
        if (Auth::check() && Auth::user()->role !== 'admin') {
            $event = Event::where('id', $event_id)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();
        }

        // Ambil data peserta
        $participant = Participant::where('id', $participant_id)
                                  ->where('event_id', $event_id)
                                  ->firstOrFail();

        // Dekripsi data peserta
        $encryptionKey = $this->getEncryptionKey($event->encryption_key);
        $decryptedData = $this->decryptData($participant->encrypted_data, $encryptionKey);

        // Dapatkan path sertifikat
        $certificatePath = storage_path('app/public/' . $decryptedData['certificate_path']);

        // Periksa apakah file sertifikat ada
        if (!file_exists($certificatePath)) {
            Log::error('Certificate file not found: ' . $certificatePath);
            return redirect()->route('admin.index.participant', ['event_id' => $event_id])
                ->with('error', 'File sertifikat tidak ditemukan.');
        }

        // Kembalikan file PDF sebagai respons
        return response()->file($certificatePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="certificate_' . $participant_id . '.pdf"',
        ]);
    } catch (\Exception $e) {
        Log::error('Error viewing certificate: ' . $e->getMessage());
        return redirect()->route('admin.index.participant', ['event_id' => $event_id])
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
            Log::error('QR Code generation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghasilkan QR Code'], 500);
        }
    }
    public function admin_event_history()
    {
        $pastEvents = Event::with('user')->where('date', '<', Carbon::today())->orderBy('date', 'desc')->get();
        $upcomingEvents = Event::with('user')->where('date', '>=', Carbon::today())->orderBy('date', 'asc')->get();
        return view('pointakses.admin.page.admin_event_history', compact('pastEvents', 'upcomingEvents'));
    }
    public function show_event($id)
    {
        // Fetch the event with its user and participants (including their user data)
        $event = Event::with(['user', 'participants.user'])->findOrFail($id);

        // Get participants (automatically filtered to users with role 'user' via Participant::user())
        $participants = $event->participants;

        // Count participants
        $participantCount = $participants->count();

        // Return the view with event, participants, and participant count
        return view('pointakses.admin.page.admin_event_show', compact('event', 'participants', 'participantCount'));
    }
}
