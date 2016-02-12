<?php
/**
 * Command-line interface to the PHP-XECryption library.
 *
 * @todo Decrypt files line-by-line instead of all at once.
 */

if (!class_exists('XECryption')) {
    $xecrypt_libpath = (empty($_ENV['XECRYPT_LIBRARY_PATH']))
        ? '.' // current directory
        : realpath($_ENV['XECRYPT_LIBRARY_PATH']);
    require_once "$xecrypt_libpath/XECryption.class.php";
}

$options = getopt('', array(
    'mode:', // what to do
    'file:', // where to find ciphertext
    'pass:', // what password to try (with encrypt/decrypt modes)
    'dict:', // what dictionary (wordlist) to use on `dict` mode
    'max:',  // maximum value to try on brute forcing
    'start:',// minimum numeric value to try on brute forcing

    'no-colors' // whether or not to colorize terminal output
));

$text = file_get_contents($options['file']);

switch ($options['mode']) {
    case 'brute':
        $start = (is_numeric($options['start'])) ? intval($options['start']) : 0;
        $max   = (is_numeric($options['max'])) ? intval($options['max']) : 1000;
        print XECryption::bruteForce($text, $start, $max);
        break;
    case 'dict':
        $password = XECryption::dictionaryAttack(
            $text,
            $options['dict'],
            array('progress' => 250) // TODO: Variablize
        );
        break;
    case 'decrypt':
        print XECryption::decrypt($text, $options['pass']);
        break;
    case 'encrypt':
        print XECryption::encrypt($text, $options['pass']);
        break;
    default:
        print 'No mode chosen.'.PHP_EOL;
        exit(1);
}
exit(0);
