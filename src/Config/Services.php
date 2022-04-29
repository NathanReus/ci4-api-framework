<?php

namespace NathanReus\CI4APIFramework\Config;

use Config\Services as BaseService;

class Services extends BaseService
{
	public static function getPrivateKey() {
        $privateKeyFile = APPPATH . 'Security' . DIRECTORY_SEPARATOR . getenv('JWT_PRIVATE_KEY_FILENAME');
        $privateKeyPassphrase = getenv('JWT_PRIVATE_KEY_PASSPHRASE');
        $privateKey = openssl_pkey_get_private(file_get_contents($privateKeyFile), $privateKeyPassphrase);
        return $privateKey;
    }

    public static function getPublicKey() {
        $publicKey = openssl_pkey_get_details(Services::getPrivateKey())['key'];
        return $publicKey;
    }
}
