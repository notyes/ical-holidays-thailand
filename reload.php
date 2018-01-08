<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// phpcs:disable Generic.Arrays.DisallowLongArraySyntax.Found

require_once 'vendor/autoload.php';

// get env
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$DEFAULT_URL = getenv('DEFAULT_URL');
$DEFAULT_PATH = getenv('DEFAULT_PATH');

if ( empty($default_url) AND empty( $DEFAULT_PATH ) ) {
    echo 'cannot load env'; die();
}

use ICal\ICal;

try {

    $buddhistdays = new ICal('buddhistdays.ics');
    $thaiholidays = new ICal('thaiholidays.ics');

    // $ical->initFile('ICal.ics');
    // $ical->initUrl('https://raw.githubusercontent.com/u01jmg3/ics-parser/master/examples/ICal.ics');
    
} catch (\Exception $e) {
    die($e);
}


if (! empty( $buddhistdays->cal['VEVENT'] )) {
    
    $firebase = new \Firebase\FirebaseLib($DEFAULT_URL);

    $allEvent = array();

    $firebase->delete($DEFAULT_PATH . '/buddhistdays'); 

    foreach ($buddhistdays->cal['VEVENT'] as $key => $value) {
        
        $isData = array();
        $isData['DTSTART'] = $value['DTSTART'];
        $isData['DTEND'] = $value['DTEND'];
        $isData['SUMMARY'] = $value['SUMMARY'];

        $firebase->set($DEFAULT_PATH . '/buddhistdays/' . ++$key, $isData);
        
    }
}

if (! empty( $thaiholidays->cal['VEVENT'] )) {
    
    $firebase = new \Firebase\FirebaseLib($DEFAULT_URL);

    $allEvent = array();

    $firebase->delete($DEFAULT_PATH . '/thaiholidays'); 

    foreach ($thaiholidays->cal['VEVENT'] as $key => $value) {
        
        $isData = array();
        $isData['DTSTART'] = $value['DTSTART'];
        $isData['DTEND'] = $value['DTEND'];
        $isData['SUMMARY'] = $value['SUMMARY'];

        $firebase->set($DEFAULT_PATH . '/thaiholidays/' . ++$key, $isData);
        
    }
}

echo 'Keep Data Success';