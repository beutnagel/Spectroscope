<?php

// testing simple save operating time function
// It takes the current time this file was called and 
// encrypts it in a txt file. Call this file everytime 
// the service runs
date_default_timezone_set('Europe/Berlin');

// file is located in root
$file = __DIR__."/last.txt";

// current timestamp
$time = date("Y-m-d H:i:s",time());

// encrypt timestamp
$encrypted = my_simple_crypt( $time, 'e' );

// write to file (overwrite)
$write = fopen($file, "w") or die("Unable to open file to register operations");
fwrite($write, $encrypted);
fclose($write);


/**
 * Encrypt and decrypt
 * 
 * @author Nazmul Ahsan <n.mukto@gmail.com>
 * @link http://nazmulahsan.me/simple-two-way-function-encrypt-decrypt-string/
 *
 * @param string $string string to be encrypted/decrypted
 * @param string $action what to do with this? e for encrypt, d for decrypt
 */
function my_simple_crypt( $string, $action = 'e' ) {
    // you may change these values to your own
    $secret_key = 'there_is_a_white_horse';
    $secret_iv = 'Das83dxs9wD';
 
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash( 'sha256', $secret_key );
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
 
 	$salt = "gandalf";
    if( $action == 'e' ) {
    	$string .= $salt; // salt
        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    }
    else if( $action == 'd' ){
        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
        $output = str_replace($salt, "", $output);
    }
 
    return $output;
}