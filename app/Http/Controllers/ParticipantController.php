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
        // Ambil semua peserta berdasarkan event_id
        $participants = Participant::where('event_id', $event_id)->get();
    
        // Ambil kunci enkripsi dari event
        $event = Event::findOrFail($event_id);
        $encryptionKey = $this->getEncryptionKey($event->encryption_key);
        $user = auth()->user();
        
        // Dekripsi data peserta jika diperlukan
        foreach ($participants as $participant) {
            $decryptedData = $this->decryptData($participant->encrypted_data, $encryptionKey);
            $participant->decrypted_name = $decryptedData['name'];
            $participant->decrypted_email = $decryptedData['email'];
            $participant->decrypted_phone = $decryptedData['phone']; 
            $participant->decrypted_logo = $event->logo; 
            $participant->decrypted_signature = $event->signature; 
            $participant->decrypted_nama_lengkap = $user->nama_lengkap;
            $participant->decrypted_date = $event->date;
            $participant->decrypted_title = $event->title;
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
            // Validasi input peserta
            $request->validate([
                'nama_peserta' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'telepon' => 'required|string|max:15',
            ]);
    
            // Ambil data event berdasarkan ID
            $event = Event::findOrFail($event_id);
    
            // Ambil nama lengkap pengguna yang terautentikasi
            $user = auth()->user();
    
            // Data peserta termasuk path tanda tangan dan logo dari event
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
    
            // Enkripsi data peserta menggunakan ChaCha20-Poly1305
            $encryptionKey = $this->getEncryptionKey($event->encryption_key);
            $encryptedData = $this->encryptData($participantData, $encryptionKey);
    
            // Simpan data peserta
            $participant = new Participant();
            $participant->event_id = $event_id;
            $participant->encrypted_data = $encryptedData;
            $participant->save();
    
            return redirect()->route('user.participant.index', ['event_id' => $event_id])
                ->with('success', 'Peserta berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data peserta: ' . $e->getMessage());
        }
    }

    // Fungsi untuk mendapatkan kunci enkripsi
    private function getEncryptionKey($encryptionKeyFromEvent)
    {
        // Ambil kunci enkripsi dari event, jika tidak ada, fallback ke .env
        if (empty($encryptionKeyFromEvent)) {
            $encryptionKeyFromEvent = hex2bin(env('CHACHA20_SECRET_KEY'));

            if (strlen($encryptionKeyFromEvent) !== 32) {
                throw new \Exception('Panjang kunci enkripsi dari .env tidak sesuai.');
            }
        } else {
            $encryptionKeyFromEvent = hex2bin($encryptionKeyFromEvent);
            if (strlen($encryptionKeyFromEvent) !== 32) {
                throw new \Exception('Panjang kunci enkripsi dari event tidak sesuai.');
            }
        }

        return $encryptionKeyFromEvent;
    }

    // Fungsi untuk mengenkripsi data menggunakan ChaCha20-Poly1305
    private function encryptData($data, $key)
    {
        if (strlen($key) !== 32) { 
            throw new \Exception('Panjang kunci enkripsi tidak sesuai.');
        }
    
        // Membuat nonce
        $nonce = random_bytes(SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_NPUBBYTES); // 12 bytes untuk ChaCha20
        
        // Definisikan AD
        $ad = "skripsiku"; 
    
        // Enkripsi data
        $ciphertext = sodium_crypto_aead_chacha20poly1305_encrypt(
            json_encode($data),
            $ad, 
            $nonce,
            $key
        );
    
        // Encode ke base64
        return base64_encode($nonce . $ciphertext);
    }   
    
    private function decryptData($encryptedData, $key)
    {
        if (strlen($key) !== 32) { 
            throw new \Exception('Panjang kunci enkripsi tidak sesuai.');
        }
    
        $decodedData = base64_decode($encryptedData);
    
        // Pastikan data terdecode dengan benar
        if ($decodedData === false) {
            throw new \Exception("Data tidak valid.");
        }
    
        // Ambil nonce dan ciphertext
        $nonce = mb_substr($decodedData, 0, SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_NPUBBYTES, '8bit');
        $ciphertext = mb_substr($decodedData, SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_NPUBBYTES, null, '8bit');
    
        // Definisikan AD
        $ad = "skripsiku";  // Menetapkan nilai AD yang sama dengan saat enkripsi
        // Dekripsi data
        $decrypted = sodium_crypto_aead_chacha20poly1305_decrypt(
            $ciphertext,
            $ad, // Menggunakan AD yang sama
            $nonce,
            $key
        );
    
        if ($decrypted === false) {
            throw new \Exception("Dekripsi gagal. Mungkin nonce atau AD tidak cocok.");
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
        $participant = Participant::where('event_id', $event_id)
            ->where('id', $participant_id)
            ->firstOrFail();
    
        $encryptedData = $participant->encrypted_data;
    
        if (empty($encryptedData)) {
            return response()->json(['success' => false, 'message' => 'Data terenkripsi tidak ditemukan.'], 404);
        }
    
        // Membuat QR Code dengan Endroid
        $qrCode = new QrCode($encryptedData);
        $qrCode->setSize(500);
        // Membuat writer
        $writer = new PngWriter();
        
        // Generate PNG
        $result = $writer->write($qrCode);
    
        // Nama file untuk unduhan
        $fileName = "QRCode_Participant_{$participant_id}.png";
    
        // Menyimpan file QR Code ke storage lokal
        Storage::disk('local')->put("qrcodes/{$fileName}", $result->getString());
    
        // Mengirimkan file QR Code untuk diunduh
        return response()->download(storage_path("app/qrcodes/{$fileName}"), $fileName)->deleteFileAfterSend(true);
    }

}
    

