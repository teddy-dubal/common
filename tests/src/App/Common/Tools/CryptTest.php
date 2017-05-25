<?php

namespace Tests\App\Common\Tools;

use App\Common\Tools\Crypt;
use Defuse\Crypto\Exception\CryptoException;
use PHPUnit\Framework\TestCase;

final class CryptTest extends TestCase {

    public function testGenerateToken(): string {
        $token = Crypt::generateToken('testToken', 'test');
        $this->assertTrue(is_string($token));
        return $token;
    }

    /**
     * @depends testGenerateToken
     */
    public function testDecryptTokenWithValidPassword($token) {
        $dToken = Crypt::decryptToken($token, 'test');
        $this->assertTrue(is_string($dToken));
        $this->assertEquals('testToken', $dToken);
    }
//    /**
//     * @depends testGenerateToken
//     */
//    public function testDecryptTokenWithInvalide($token) {
//        $dToken = Crypt::decryptToken($token, 'tests');
//        $this->assertTrue(is_string($dToken));
//        $this->assertNotEquals('testToken', $dToken);
//    }
}
