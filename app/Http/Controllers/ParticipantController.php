<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\Event;
use Illuminate\Support\Facades\Log;
use Sodium;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\ErrorCorrectionLevel;


class ParticipantController extends Controller
{
    public function index($event_id)
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

        return view('pointakses.user.page.participant_index', compact('participants', 'event_id'));
    }

    public function show($event_id, $participant_id)
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

        return view('pointakses.user.page.participant_show', compact('participant', 'event'));
    }

    public function create($event_id)
    {
        $event = Event::findOrFail($event_id);
        return view('pointakses.user.page.participant_create', compact('event_id'));
    }

    public function store(Request $request, $event_id)
    {
        try {
            $request->validate([
                'nama_peserta' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'telepon' => 'required|string|max:15',
            ]);
    
            $event = Event::findOrFail($event_id);
            $user = auth()->user(); // Ambil user yang sedang login
    
            $participantData = [
                'name' => $request->nama_peserta,
                'email' => $request->email,
                'phone' => $request->telepon,
                'tanda_tangan' => $event->signature,
                'logo' => $event->logo,
                'nama_lengkap' => $user->nama_lengkap,
                'date' => $event->date,
                'title' => $event->title,
            ];
    
            $encryptionKey = $this->getEncryptionKey($event->encryption_key);
            $encryptedData = $this->encryptData($participantData, $encryptionKey);
    
            $participant = new Participant();
            $participant->user_id = $user->id; // Pastikan user_id diisi
            $participant->event_id = $event_id;
            $participant->encrypted_data = $encryptedData;
            $participant->save();
    
            return redirect()->route('user.participant.index', ['event_id' => $event_id])
                ->with('success', 'Peserta berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    


    // Fungsi untuk mendapatkan kunci enkripsi
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
    


    // Perbaikan metode encryptData dan decryptData menggunakan IETF standar
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


    public function edit($event_id, $participant_id)
    {
        $event = Event::findOrFail($event_id);
        $participant = Participant::findOrFail($participant_id);

        // Ambil kunci enkripsi
        $encryptionKey = $this->getEncryptionKey($event->encryption_key);
        $decryptedData = $this->decryptData($participant->encrypted_data, $encryptionKey);

        return view('pointakses.user.page.participant_edit', compact('event_id', 'participant', 'decryptedData'));
    }

    public function update(Request $request, $event_id, $participant_id)
    {
        try {
            // Validasi input
            $request->validate([
                'nama_peserta' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'telepon' => 'required|string|max:15',
                'nomer_seri' => 'required|string|max:255',
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

            return redirect()->route('user.participant.index', ['event_id' => $event_id])
                ->with('success', 'Peserta berhasil diperbarui!');
        } catch (\Exception $e) {
            // Log error dan tampilkan pesan
            Log::error("Peserta gagal diperbarui: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui peserta: ' . $e->getMessage());
        }
    }

    public function destroy($event_id, $participant_id)
    {
        // Hapus peserta berdasarkan ID
        $participant = Participant::findOrFail($participant_id);
        $participant->delete();

        return redirect()->route('user.participant.index', ['event_id' => $event_id])
            ->with('success', 'Peserta berhasil dihapus!');
    }

    public function generateQrCode($event_id, $participant_id)
    {
        try {
            $participant = Participant::where('event_id', $event_id)
                ->where('id', $participant_id)
                ->firstOrFail();

            $encryptedData = $participant->encrypted_data;

            if (empty($encryptedData)) {
                return response()->json(['success' => false, 'message' => 'Data terenkripsi tidak ditemukan.'], 404);
            }

            // Generate QR Code
            $qrCode = new QrCode($encryptedData);
            $qrCode->setSize(500);

            // Buat direktori jika belum ada
            $directoryPath = storage_path("app/public/qrcodes");
            if (!file_exists($directoryPath)) {
                mkdir($directoryPath, 0755, true);
            }

            // Simpan QR Code dalam format PNG
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Nama file
            $fileName = "QRCode_Participant_{$participant_id}.png";
            $filePath = storage_path("app/public/qrcodes/{$fileName}");

            // Simpan ke storage
            Storage::disk('public')->put("qrcodes/{$fileName}", $result->getString());

            // Kirim file untuk diunduh
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error("Error saat generate QR Code: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal membuat QR Code: ' . $e->getMessage()], 500);
        }
    }
}
