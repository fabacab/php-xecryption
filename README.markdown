# PHP-XECryption

XECryption is a simplistic (broken) symmetric key cipher used in several popular amateur cryptography training courses. I thought it might be fun to write a few toy scripts that used it so I whipped up this PHP library to help with that. Then I went down the rabbit hole: ANSI escape sequences for video text terminals, `readline` interactivity, various experiments at ASCII text recognition, whatever I felt like.

So, that's what that repository is.

In case it needs to be stated more obviously: **XECryption is NOT secure. DO NOT USE THIS library for anything important. This algorithm is HORRIBLY BROKEN.** Kthxbai.

# Using

Include the `XECryption.class.php` file in your project, then call its `encrypt()` or `decrypt()` methods as follows:

```php
requre_once 'XECryption.class.php';

// Encrypting.
$plaintext  = "Hello world.";
$passphrase = "fascists deserve painful deaths"; // What? It's true.
$ciphertext = XECryption::encrypt($plaintext, $passphrase);

// Decrypting.
$plaintext = XECryption::decrypt($ciphertext, $passphrase);
```

# Command-line interface

There is also `xecrypt.php`, a command-line tool that interacts with the library. It expects to find `XECryption.class.php` in the current directory, but you can set your shell's `XECRYPT_LIBRARY_PATH` variable to another directory. For instance, in Bash:

```sh
export XECRYPT_LIBRARY_PATH=/usr/local/XECryption
```

The above will tell `xecrypt.php` to expect the library in `/usr/local/XECryption/XECryption.class.php`.

In any event, using the script is simple.

To encrypt the contents of a file:

```sh
php xecrypt.php --mode encrypt --file my_secret_message.txt --pass "secret passphrase" > my_encrypted_message
```

To decrypt the contents of a file:

```sh
php xecrypt.php --mode decrypt --file my_encrypted_message --pass "secret passphrase"
```

## Interactive attack modes

You can try "cracking" XECryption-enciphered strings interactively.

A pseudo-automated dictionary (wordlist) attack:

```sh
$ php xecrypt.php --mode dict --file my_encrypted_message --dict /path/to/wordlist.dict
XECryption::dictionaryAttack() progress...
...250
...500
...750
Possible plaintext: 
%"RJR/ZGEt{rvkqp

                ZGEt{rvkq
Password candidate: aaal
Continue guessing? [y]: y
...1000
...1250
...1500
...1750
...2000
...2250
Possible plaintext:
# PHP-XECryption

XECryption is a simplistic (broken) symmetric key cipher
Password candidate: aabm
Continue guessing? [y]: n
```

# See also

* [XECryption on GitHub](https://github.com/search?q=xecryption)
