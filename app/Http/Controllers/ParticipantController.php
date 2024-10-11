<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\Event;

class ParticipantController extends Controller
{
    // Menampilkan semua peserta berdasarkan event_id
    public function index($event_id)
    {
        // Ambil semua peserta berdasarkan event_id
        $participants = Participant::where('event_id', $event_id)->get();

        // Ambil kunci enkripsi dari event
        $event = Event::findOrFail($event_id);
        $encryptionKey = $this->getEncryptionKey($event->encryption_key);

        // Dekripsi data peserta jika diperlukan
        foreach ($participants as $participant) {
            $decryptedData = $this->decryptData($participant->encrypted_data, $encryptionKey);
            $participant->decrypted_name = $decryptedData['name'];
            $participant->decrypted_email = $decryptedData['email'];
            $participant->decrypted_phone = $decryptedData['phone'];
        }

        return view('pointakses.user.page.participant_index', compact('participants', 'event_id'));
    }

    // Menampilkan form untuk menambah peserta pada event tertentu
    public function create($event_id)
    {
        $event = Event::findOrFail($event_id);
        return view('pointakses.user.page.participant_create', compact('event_id'));
    }

    public function store(Request $request, $event_id)
    {
        try {
            // Validasi input
            $request->validate([
                'nama_peserta' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'telepon' => 'required|string|max:15',
            ]);
    
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
    
            // Simpan peserta ke database
            $participant = new Participant();
            $participant->event_id = $event_id;
            $participant->encrypted_data = $encryptedData;
            $participant->save();
    
            return redirect()->route('user.participant.index', ['event_id' => $event_id])
                             ->with('success', 'Peserta berhasil ditambahkan!');
        } catch (\Exception $e) {
            // Log error dan tampilkan pesan
            \Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data peserta.');
        }
    }

    // Fungsi untuk mendapatkan kunci enkripsi
    private function getEncryptionKey($encryptionKeyFromEvent)
    {
        // Ambil kunci enkripsi dari event, jika tidak ada, fallback ke .env
        if (empty($encryptionKeyFromEvent)) {
            $encryptionKeyFromEvent = hex2bin(env('CHACHA20_SECRET_KEY'));

            if (strlen($encryptionKeyFromEvent) !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
                throw new \Exception('Panjang kunci enkripsi dari .env tidak sesuai.');
            }
        } else {
            $encryptionKeyFromEvent = hex2bin($encryptionKeyFromEvent);
            if (strlen($encryptionKeyFromEvent) !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
                throw new \Exception('Panjang kunci enkripsi dari event tidak sesuai.');
            }
        }

        return $encryptionKeyFromEvent;
    }
    
    // Fungsi untuk mengenkripsi data
    private function encryptData($data, $key)
    {
        if (strlen($key) !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            throw new \Exception('Panjang kunci enkripsi tidak sesuai.');
        }
    
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES); // Nonce untuk enkripsi
        $ciphertext = sodium_crypto_secretbox(json_encode($data), $nonce, $key); // Enkripsi data
    
        return base64_encode($nonce . $ciphertext); // Gabungkan nonce dan ciphertext lalu encode ke base64
    }
    
    // Fungsi untuk mendekripsi data
    private function decryptData($encryptedData, $key)
    {
        if (strlen($key) !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            throw new \Exception('Panjang kunci enkripsi tidak sesuai.');
        }
    
        $decodedData = base64_decode($encryptedData);
    
        // Ambil nonce dan ciphertext dari data yang sudah didekode
        $nonce = mb_substr($decodedData, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($decodedData, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');
    
        // Dekripsi data
        $decrypted = sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
    
        if ($decrypted === false) {
            throw new \Exception("Dekripsi gagal.");
        }
    
        return json_decode($decrypted, true);
    }
}
