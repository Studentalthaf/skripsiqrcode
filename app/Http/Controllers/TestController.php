<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Models\Participant;

class TestController extends Controller
{
    /**
     * Menampilkan halaman pemindai QR code
     */
    public function index()
    {
        return view('pointakses.admin.page.admin_page_test');
    }

    /**
     * Memproses dan mendekripsi data QR code
     */
    public function scan(Request $request)
    {
        try {
            // Ambil data dari QR code
            $data = $request->input('qr_data');
            // $data = json_encode([
            //     'id' => 4,
            //     'data' => 'xRkwcdKFIiizb8sZ3sreWsp9lQVFahEMxo6FH/l4BD8hjy90WUxasQaPwgRdHT+fCOFNfaG/55EvvkCa+faOsEkBNy+2LxupFbfHSyHQei2jdvo2Og/onA8fRT6Ta1EfgktY+s2mQrT21GuqUSQWPPjCr00LGYxg01e/6mZtCb8iK0C4vYiD381qytnGrM83NRzdCP4hJyFGTD60r7vBps9v8XaiUl4D1QqVvQL8BA7IlXBna2SdkNzNOU5E6BLhofpS6B+0',
            //     'timestamp' => 1746512568
            // ]);
            // Decode dari JSON
            $decoded = json_decode($data, true);
            
            if (!$decoded || !isset($decoded['data']) || !isset($decoded['id'])) {
                return response()->json(['error' => 'Format QR code tidak dikenali.'], 400);
            }
            
            // Ambil data terenkripsi dan ID participant
            $encryptedData = $decoded['data'];
            $participantId = $decoded['id'];
            
            // Validasi participant (opsional)
            // $participant = Participant::find($participantId);
            // if (!$participant) {
            //     return response()->json(['error' => 'Peserta tidak ditemukan.'], 404);
            // }
            
            // Base64 decode data terenkripsi
            $encrypted = base64_decode($encryptedData);
            
            if ($encrypted === false) {
                return response()->json(['error' => 'Data enkripsi tidak valid (base64).'], 400);
            }
            
            // Ambil kunci dari .env (pastikan panjang kunci 32 byte / 64 karakter hex)
            $key = hex2bin(env('CHACHA20_SECRET_KEY'));
            if (strlen($key) !== SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_KEYBYTES) {
                return response()->json(['error' => 'Kunci enkripsi tidak valid.'], 500);
            }
            
            // Ekstrak komponen dari data terenkripsi
            if (strlen($encrypted) < SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NPUBBYTES) {
                return response()->json(['error' => 'Data terenkripsi terlalu pendek.'], 400);
            }
            
            // Ekstrak nonce (12 byte) dan ciphertext (termasuk tag autentikasi)
            $nonce = substr($encrypted, 0, SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NPUBBYTES);
            $ciphertext = substr($encrypted, SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NPUBBYTES);
            
            // Associated Data harus sama dengan yang digunakan saat enkripsi
            $ad = "skripsiku";
            
            // Dekripsi dengan sodium
            $decrypted = sodium_crypto_aead_chacha20poly1305_ietf_decrypt(
                $ciphertext,
                $ad,
                $nonce,
                $key
            );
            
            if ($decrypted === false) {
                return response()->json(['error' => 'Gagal mendekripsi data. Tag autentikasi tidak valid.'], 400);
            }
            
            // Decode JSON hasil dekripsi
            $decodedData = json_decode($decrypted, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['error' => 'Data yang didekripsi bukan JSON valid.'], 400);
            }
            
            // Berhasil mendekripsi, kembalikan data
            // Konversi objek menjadi string yang terformat untuk ditampilkan
            $formattedData = '';
            foreach ($decodedData as $key => $value) {
                $formattedData .= "<strong>{$key}:</strong> {$value}<br>";
            }       
            
            return response()->json([
                'hasil' => $formattedData,
                'raw_data' => $decodedData, // Tetap sertakan data mentah jika diperlukan
                'id' => $participantId,
                'status' => 'success'
            ]);
            
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('QR Scan Error: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memproses QR code: ' . $e->getMessage()], 500);
        }
    }
}