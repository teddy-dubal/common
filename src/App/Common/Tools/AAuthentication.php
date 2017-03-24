<?php

namespace App\Common\Tools;

use Exception;

/**
 * Aventers
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 *
 * @copyright  Copyright (c) 2009-2010 elements.at New Media Solutions GmbH (http://www.elements.at)
 * @license    http://www.pimcore.org/license     New BSD License
 */
class AAuthentication
{

    /**
     * @static
     * @param  string $plainTextPassword
     * @return string
     */
    public static function getPasswordHash($username, $plainTextPassword)
    {
	return md5($username . ":teddy_dona:" . $plainTextPassword);
    }

    /**
     * @static
     * @param  string $username
     * @param  string $passwordHash
     * @param  string $algorithm
     * @param  string $mode
     * @return string
     */
    public static function generateToken($username, $passwordHash, $algorithm = MCRYPT_TRIPLEDES, $mode = MCRYPT_MODE_ECB)
    {

	$data = time() - 1 . '|' . $username;

	$key = $passwordHash;


	// append pkcs5 padding to the data
	$blocksize	 = mcrypt_get_block_size($algorithm, $mode);
	$pkcs		 = $blocksize - (strlen($data) % $blocksize);
	$data .= str_repeat(chr($pkcs), $pkcs);

	//encrypt
	$td = mcrypt_module_open($algorithm, '', $mode, '');

	$iv	 = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);
	$ks	 = mcrypt_enc_get_key_size($td);
	$key	 = substr($key, 0, $ks);

	mcrypt_generic_init($td, $key, $iv);

	$encrypted	 = mcrypt_generic($td, $data);
	$raw		 = base64_encode($encrypted);

	$token = "";
	for ($i = 0; $i < strlen($raw); $i++) {
	    $token .= bin2hex($raw[$i]);
	}
	return $token;
    }

    /**
     * @static
     * @param  string $hex
     * @return  string
     */
    protected static function hex2str($hex)
    {
	$str = "";
	for ($i = 0; $i < strlen($hex); $i += 2) {
	    $str .= chr(hexdec(substr($hex, $i, 2)));
	}
	return $str;
    }

    /**
     * @static
     * @param  string $token
     * @param  string $algorithm
     * @param  string $mode
     * @return array
     */
    public static function decrypt($key, $token, $algorithm = MCRYPT_TRIPLEDES, $mode = MCRYPT_MODE_ECB)
    {

	$encrypted = base64_decode(self::hex2str($token));


	$td = mcrypt_module_open($algorithm, '', $mode, '');

	$iv = MCRYPT_DEV_URANDOM;

	@mcrypt_generic_deinit($td);
	@mcrypt_generic_init($td, $key, $iv);
	if ($encrypted == '')
	    return false;
	$decrypted = mdecrypt_generic($td, $encrypted);

	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);

	$decrypted = str_replace(chr(8), "", $decrypted);
	return preg_replace("/\p{Cc}*$/u", "", explode("|", $decrypted));
    }

    /**
     * @static
     * @throws Exception
     * @param  string $passwordHash
     * @param  string $token
     * @param bool $adminRequired
     * @return User
     */
    public static function tokenAuthentication($passwordHash, $token, $algorithm = MCRYPT_TRIPLEDES, $mode = MCRYPT_MODE_ECB, $duration = 1800)
    {
	if (strlen($token) > 112) {
	    // throw new Exception("invalid token");
	}
	$decrypted	 = AAuthentication::decrypt($passwordHash, $token, $algorithm, $mode);
	$timestamp	 = $decrypted[0];
	$timeZone	 = date_default_timezone_get();
	date_default_timezone_set("UTC");
	//Valid 30 mn => 30 * 60s => 1800s
	if ($timestamp > time() or $timestamp < (time() - ($duration))) {
	    throw new Exception("invalid timestamp");
	}
	date_default_timezone_set($timeZone);
	return true;
    }

}
