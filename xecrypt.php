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
    'file:',
    'dict:',
    'mode:',
    'pass:',
    'no-colors'
));

$text = trim(@implode('', @file($options['file'])));

switch ($options['mode']) {
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
