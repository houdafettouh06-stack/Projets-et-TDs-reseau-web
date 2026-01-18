<?php
// core/Security.php

class Security {
    // Clé de chiffrement (Devrait être dans .env en prod, ici en dur pour la démo)
    private const ENCRYPTION_KEY = 'UEMF_SECURE_KEY_2026_AES_256_GCM';
    private const CIPHER_ALGO = 'aes-256-gcm';

    /**
     * Chiffre une donnée sensible (Email, Tel)
     */
    public static function encrypt($data) {
        if (empty($data)) return $data;
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::CIPHER_ALGO));
        $tag = ""; // Sera rempli par openssl_encrypt
        $encrypted = openssl_encrypt($data, self::CIPHER_ALGO, self::ENCRYPTION_KEY, 0, $iv, $tag);
        // On stocke le tout en base64 : IV . TAG . EncryptedData
        return base64_encode($iv . $tag . $encrypted);
    }

    /**
     * Déchiffre une donnée
     */
    public static function decrypt($data) {
        if (empty($data)) return $data;
        $raw = base64_decode($data);
        $ivLen = openssl_cipher_iv_length(self::CIPHER_ALGO);
        $tagLen = 16; // GCM tag length standard

        if (strlen($raw) < $ivLen + $tagLen) return $data; // Donnée non chiffrée ou corrompue

        $iv = substr($raw, 0, $ivLen);
        $tag = substr($raw, $ivLen, $tagLen);
        $ciphertext = substr($raw, $ivLen + $tagLen);

        return openssl_decrypt($ciphertext, self::CIPHER_ALGO, self::ENCRYPTION_KEY, 0, $iv, $tag);
    }

    /**
     * Nettoyage anti-XSS et Injection
     */
    public static function sanitize($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
}
