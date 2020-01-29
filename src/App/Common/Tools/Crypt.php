<?php

namespace App\Common\Tools;

use Defuse\Crypto\Crypto;
use Exception;

class Crypt extends Crypto {

    /**
     * @static
     * @param  string $value
     * @param  string $password
     * @return string
     */
    public static function generateToken(string $value, string $password): string {
        $data = time() - 1 . '|' . $value;
        return self::encryptWithPassword($data, $password);
    }

    

    /**
     * @static
     * @param  string $token
     * @param  string $password
     * @return string
     */
    public static function decryptToken(string $token, string $password, int $duration = 0) {
        try {
            $r = self::decryptWithPassword($token, $password);
        } catch (Exception $e) {
            return false;
        }
        $decrypted = explode('|', $r);
        if ($duration) {
            $timestamp = $decrypted[0];
            $timeZone  = date_default_timezone_get();
            date_default_timezone_set("UTC");
            //Valid 30 mn => 30 * 60s => 1800s
            if ($timestamp > time() or $timestamp < (time() - ($duration))) {
                return false;
            }
            date_default_timezone_set($timeZone);
        }
        return $decrypted[1];
    }
}
