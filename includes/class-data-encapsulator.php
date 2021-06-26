<?php
/**
 * Created by PhpStorm.
 * User: bobuchacha
 * Date: 7/11/18
 * Time: 1:10 AM
 */
class DataEncapsulation
{
	const KEY_SIZE = 256;
	const ENC_TYPE = 'aes-256-ofb';

	static function encrypt($data)
	{
		// generate key
		$encryption_key = openssl_random_pseudo_bytes(self::KEY_SIZE);                                                      // always must be 30 for 40 byte header file
		// Generate an initialization vector
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::ENC_TYPE));
		// Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
		$encrypted = openssl_encrypt($data, self::ENC_TYPE, $encryption_key, 0, $iv);
		// The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
		return base64_encode($encryption_key . $iv . $encrypted);
	}

	static function decrypt($data)
	{
		$data = base64_decode($data);
		$encryption_key = substr($data, 0, self::KEY_SIZE);
		$ivlen = openssl_cipher_iv_length(self::ENC_TYPE);
		$iv = substr($data, self::KEY_SIZE, $ivlen);
		$data = substr($data, self::KEY_SIZE + $ivlen);
		return openssl_decrypt($data, self::ENC_TYPE, $encryption_key, 0, $iv);
	}

}
