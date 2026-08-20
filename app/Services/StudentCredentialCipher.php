<?php
namespace App\Services;

use Illuminate\Encryption\Encrypter;
use RuntimeException;

class StudentCredentialCipher
{
    private function encrypter(): Encrypter
    {
        $secret = (string) config('app.student_password_key');
        if ($secret === '') {
            $secret = (string) config('app.key');
        }
        if ($secret === '') {
            throw new RuntimeException('STUDENT_PASSWORD_KEY or APP_KEY must be configured.');
        }

        $key = hash('sha256', $secret, true);
        return new Encrypter($key, 'AES-256-CBC');
    }

    public function encrypt(string $plain): string
    {
        return $this->encrypter()->encryptString($plain);
    }

    public function decrypt(?string $cipherText): ?string
    {
        if (!$cipherText) return null;
        return $this->encrypter()->decryptString($cipherText);
    }
}
