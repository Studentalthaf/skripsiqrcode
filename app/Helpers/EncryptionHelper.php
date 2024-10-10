<?php

namespace App\Helpers;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

class EncryptionHelper
{
    // Menghasilkan kunci ChaCha20 yang aman
    public static function generateKey()
    {
        return Key::createNewRandomKey();
    }

    // Enkripsi data dengan ChaCha20
    public static function encrypt($data, $key)
    {
        return Crypto::encrypt($data, $key);
    }

    // Dekripsi data dengan ChaCha20
    public static function decrypt($encryptedData, $key)
    {
        return Crypto::decrypt($encryptedData, $key);
    }
}
