<?php
/**
 * Unit tests for XECryption class.
 *
 * @link https://www.hackthissite.org/playlevel/6/
 */

/**
 * Tests main encryption/decryption cipher.
 */
class XECryptionTest extends PHPUnit_Framework_TestCase {

    /**
     * Tests enciphering method.
     */
    public function testEncrypt () {
        $plaintext  = 'Here is my secret plan to make fascists suffer.';
        $password   = 'kill fascists slowly';

        // Encrypt and then decrypt again to make sure it works, even
        // despite the random number in the encryption algorithm.
        $ciphertext = XECryption::encrypt($plaintext, $password);
        $this->assertSame($plaintext, XECryption::decrypt($ciphertext, $password));
    }

    /**
     * Tests decryption method.
     */
    public function testDecrypt () {
        $plaintext  =<<<END_MSG
This is a test message. It's, like, "hard" to decrypt and stuff!
END_MSG;
        $password   = 'fascists deserve painful deaths';
        $ciphertext = XECryption::encrypt($plaintext, $password);

        $this->assertSame($plaintext, XECryption::decrypt($ciphertext, $password));
    }

    /**
     * Tests dictionary attack method.
     */
    public function testDictionaryAttack () {
        $this->markTestIncomplete();
    }

    /**
     * Tests brute force attack method.
     */
    public function testBruteForceAttack () {
        $this->markTestIncomplete();
    }

}
