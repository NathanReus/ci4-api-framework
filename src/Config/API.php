<?php

namespace NathanReus\CI4APIFramework\Config;

use CodeIgniter\Config\BaseConfig;

class API extends BaseConfig
{
	/**
	 * --------------------------------------------------------------------
	 * Password Minimum Length
	 * --------------------------------------------------------------------
	 * 
	 * The minimum number of characters permitted in a login password.
	 * 
	 * @var int
	 */
	public $passwordMinLength = 8;

	/**
	 * --------------------------------------------------------------------
	 * Password Maximum Length
	 * --------------------------------------------------------------------
	 * 
	 * The maxinum number of characters permitted in a login password.
	 * 
	 * @var int
	 */
	public $passwordMaxLength = 64;

	/**
	 * --------------------------------------------------------------------
	 * Permit POST Data
	 * --------------------------------------------------------------------
	 * 
	 * If enabled, the API will accept data via 'POST'. If disabled, 
	 * it will only accept via JSON in the body.
	 * 
	 */
	public $permitPostData = true;
	
	/**
	 * --------------------------------------------------------------------
	 * Access Token expiration period
	 * --------------------------------------------------------------------
	 * 
	 * The amount of time that an Access Token will be valid for, 
	 * in seconds. Keep this short!
	 * 
	 * @var int 
	 */
	public $accessTokenTime = 1 * HOUR;

	/**
	 * --------------------------------------------------------------------
	 * Refresh Token expiration period
	 * --------------------------------------------------------------------
	 * 
	 * The amount of time that a Refresh Token will be valid for, 
	 * in seconds. This can be longer, but not too long.
	 * 
	 * @var int 
	 */
	public $refreshTokenTime = 2 * WEEK;

	/**
	 * --------------------------------------------------------------------
	 * Refresh Token family hash options
	 * --------------------------------------------------------------------
	 * 
	 * The list of characters from which the family has of the Refresh
	 * Tokens will be randomly selected from, and the length of the hash.
	 * 
	 * @var string
	 */
	public $refreshTokenFamilyCharacters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	public $refreshTokenFamilyHashLength = 32;
}
