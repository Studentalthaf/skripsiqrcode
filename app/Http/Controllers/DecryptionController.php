<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DecryptionController extends Controller
{
    public function decryptQr(Request $request)
    {
        $validated = $request->validate([
            'encrypted_data' => 'required|string',
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
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    private function decryptData($encryptedData, $key, $ad)
    {
        $decodedData = base64_decode($encryptedData);

        if ($decodedData === false) {
            throw new \Exception("Data terenkripsi tidak valid.");
        }

        $nonce = mb_substr($decodedData, 0, SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_NPUBBYTES, '8bit');
        $ciphertext = mb_substr($decodedData, SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_NPUBBYTES, null, '8bit');

        $decrypted = sodium_crypto_aead_chacha20poly1305_decrypt($ciphertext, $ad, $nonce, $key);

        if ($decrypted === false) {
            throw new \Exception("Dekripsi gagal. Nonce atau AD tidak cocok.");
        }

        return json_decode($decrypted, true);
    }
}
