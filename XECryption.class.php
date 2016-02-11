<?php
/**
 * A simple PHP encryptor/decryptor for the XECryption scheme.
 *
 * @link https://www.hackthissite.org/playlevel/6/
 */

/**
 * Main class.
 */
class XECryption {

    /**
     * Get the numeric key from an ASCII password.
     *
     * @link http://www.asciitable.com/ ASCII code table reference.
     *
     * @param string $password
     *
     * @return int
     */
    private static function getKey ($password) {
        $chars = array();
        for ($i = 0; $i < strlen($password); $i++) {
            $chars[] = ord($password[$i]);
        }
        return array_sum($chars);
    }

    /**
     * Encrypts to XECryption.
     *
     * @param string $plaintext
     * @param string $password
     *
     * @return string
     */
    public static function encrypt ($plaintext, $password) {
        $key = self::getKey($password);
        $ciphertext = '';

        // XECryption replaces each input character with a set of three
        // dot-separated numbers whose sum is the ASCII code point of
        // the original character plus the ASCII value of the password.
        // So, for each input character:
        for ($i = 0; $i < strlen($plaintext); $i++) {
            // Get the ASCII code number.
            $n = ord($plaintext[$i]);
            
            // Take the ASCII value, divide it by 3, make sure it's
            // still an integer (rounding down), then add a "random"
            // number between negative 10 and positive 10 to it.
            $n1 = floor($n / 3) + rand(-10, 10);
            $n2 = floor($n / 3) + rand(-10, 10); // same for number 2
            // Third number is the difference between the original
            // ASCII value, the sum of the first two, plus the key.
            $n3 = $n - ($n1 + $n2) + $key;

            // Append all those number segments in dot-separated form.
            $ciphertext .= ".$n1.$n2.$n3";
        }

        return $ciphertext;
    }

    /**
     * Decrypts XECryption-enciphered texts.
     *
     * @link http://www.asciitable.com/ ASCII code table reference.
     *
     * @param string $ciphertext
     * @param string $password
     *
     * @return string|false
     */
    public static function decrypt ($ciphertext, $password) {
        $key = self::getKey($password);
        $plaintext = '';

        // XECryption uses dots followed by a number to encode the
        // ASCII table. So, first, make an array of all the chars.
        $pattern = '/(?:\.[0-9-]+){3}/';
        if (preg_match_all($pattern, $ciphertext, $matches)) {
            foreach ($matches[0] as $x) {
                // The dot-separated numbers will always add up to 
                // the decimal ASCII value of the plaintext character
                // when the correct numeric offset ($key) is subtracted
                // from them. So we sum the three ciphertext values
                // and then subtract the key's offset value. 
                $plaintext .= chr(array_sum(explode('.', $x)) - $key);
            }
        }

        return $plaintext;
    }

    /**
     * Performs dictionary attack on an XECrpytion-enciphered message.
     *
     * Use something like Hashcat's maskprocessor to generate brute force
     * dictionaries.
     *
     * Pass options to customize behavior. For example:
     *
     * <pre>
     * XECryption::dictionaryAttack(
     *     $ciphertext,
     *     '/path/to/wordlist.dict',
     *     array('progress' => 100) // print progress each 100 guesses
     * );
     * </pre>
     *
     * @link https://hashcat.net/wiki/doku.php?id=maskprocessor maskprocessor documentation
     *
     * @param string $ciphertext
     * @param string $dict
     * @param array $opts
     *
     * @return string|false
     */
    public static function dictionaryAttack ($ciphertext, $dict, $opts = array()) {
        $attempt = 0;

        $fh = fopen($dict, 'r');
        while (($buf = fgets($fh, 1024)) !== false) {
            $guess = trim($buf);

            if (is_numeric($opts['progress']) && (0 === $attempt % $opts['progress'])) {
                if (0 === $attempt) {
                    print __METHOD__.'() progress...'.PHP_EOL;
                } else {
                    print "...$attempt".PHP_EOL;
                }
            }

            $possible_plain = self::decrypt($ciphertext, $guess);
            if (self::testPlain($possible_plain)) {
                print self::colorize('Possible plaintext:', '[31m').PHP_EOL.$possible_plain.PHP_EOL;
                print self::colorize("Password candidate: $guess".PHP_EOL, '[31m');
                $r = readline('Continue guessing? [y]: ');
                if ('N' === strtoupper(substr($r, 0, 1))) {
                    fclose($fh);
                    return $guess;
                }
            }

            $attempt++;
        }
        fclose($fh);

        return false;
    }

    /**
     * Prints in color if CLI.
     *
     * @link https://wiki.archlinux.org/index.php/Bash/Prompt_customization#Terminfo_escape_sequences
     *
     * @param string $string
     * @param string $color Bash escape code.
     *
     * @return string
     */
    private static function colorize ($string, $color) {
        if (!isset(self::get_opts()['no-colors'])) {
            return "\e$color $string \e[0m ";
        } else {
            return $string;
        }
    }

    /**
     * Whether or not we were invoked from the command line.
     *
     * @return bool
     */
    private static function is_cli () {
        return ('cli' === PHP_SAPI) ? true : false;
    }

    /**
     * Grabs the options parsed from the CLI script.
     *
     * @return array
     */
    private static function get_opts () {
        if (self::is_cli()) {
            global $options;
            return $options;
        } else {
            return array();
        }
    }

    /**
     * Simple check to see if decryption might have worked.
     *
     * The idea here is to test for a relatively high occurrence of
     * printable ASCII characters.
     *
     * @param string $possible_plaintext
     *
     * @return bool
     */
    public static function testPlain ($possible_plaintext) {
        $pattern = '/[\x09-\x7F]+/';
        $x = preg_match_all($pattern, $possible_plaintext);
        return (1 === $x) ? true : false;
    }

}
