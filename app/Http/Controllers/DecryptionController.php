<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DecryptionController extends Controller
{
    public function decryptQr(Request $request)
    {
        $validated = $request->validate([
            'encrypted_data' => 'required|string|regex:/^[A-Za-z0-9+\/=]+$/',
            'ad' => 'nullable|string',
        ]);

        $encryptedData = $validated['encrypted_data'];
        $ad = $validated['ad'] ?? 'skripsiku';
        $key = hex2bin(env('CHACHA20_SECRET_KEY'));

        if (strlen($key) !== 32) {
            return response()->json(['success' => false, 'message' => 'Kunci enkripsi tidak valid.'], 400);
        }

        try {
            $decryptedData = $this->decryptData($encryptedData, $key, $ad);
            return response()->json(['success' => true, 'data' => $decryptedData], 200);
        } catch (\Exception $e) {
            Log::error('Error dekripsi QR: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Dekripsi gagal.'], 400);
        }
    }

    private function decryptData($encryptedData, $key, $ad)
    {
        $decodedData = base64_decode($encryptedData, true);

        if ($decodedData === false) {
            throw new \Exception("Data terenkripsi tidak valid (Base64 decode gagal).");
        }

        if (strlen($decodedData) < SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_NPUBBYTES) {
            throw new \Exception("Data terenkripsi tidak memiliki panjang yang cukup.");
        }

        $nonce = substr($decodedData, 0, SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_NPUBBYTES);
        $ciphertext = substr($decodedData, SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_NPUBBYTES);

        $decrypted = sodium_crypto_aead_chacha20poly1305_decrypt($ciphertext, $ad, $nonce, $key);

        if ($decrypted === false) {
            throw new \Exception("Dekripsi gagal. Nonce atau AD tidak cocok.");
        }

        $jsonDecoded = json_decode($decrypted, true);

        return $jsonDecoded ?? $decrypted;
    }
}
