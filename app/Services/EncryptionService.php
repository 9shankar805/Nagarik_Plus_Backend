<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class EncryptionService
{
    private string $key;
    private string $cipher = 'AES-256-CBC';

    public function __construct()
    {
        $key = config('app.document_encryption_key', env('DOCUMENT_ENCRYPTION_KEY'));
        // Ensure 32 bytes for AES-256
        $this->key = substr(hash('sha256', $key, true), 0, 32);
    }

    /**
     * Encrypt sensitive data (AES-256-CBC)
     */
    public function encrypt(array|string $data): string
    {
        $payload = is_array($data) ? json_encode($data) : $data;
        $iv = random_bytes(openssl_cipher_iv_length($this->cipher));
        $encrypted = openssl_encrypt($payload, $this->cipher, $this->key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt previously encrypted data
     */
    public function decrypt(string $encrypted): array|string|null
    {
        try {
            $decoded = base64_decode($encrypted);
            $ivLen = openssl_cipher_iv_length($this->cipher);
            $iv = substr($decoded, 0, $ivLen);
            $cipherText = substr($decoded, $ivLen);
            $decrypted = openssl_decrypt($cipherText, $this->cipher, $this->key, 0, $iv);

            if ($decrypted === false) return null;

            $json = json_decode($decrypted, true);
            return $json ?? $decrypted;
        } catch (\Throwable $e) {
            Log::error('Decryption failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Encrypt a file's binary content
     */
    public function encryptFile(string $filePath): string
    {
        $contents = file_get_contents($filePath);
        return $this->encrypt($contents);
    }

    /**
     * Decrypt and write file to temp path
     */
    public function decryptFileToTemp(string $encryptedContent): ?string
    {
        $decrypted = $this->decrypt($encryptedContent);
        if (!$decrypted) return null;

        $tmpPath = tempnam(sys_get_temp_dir(), 'nagarik_doc_');
        file_put_contents($tmpPath, $decrypted);
        return $tmpPath;
    }
}
