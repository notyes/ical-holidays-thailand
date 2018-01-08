<?php 

// recheck error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// autoload 
require_once 'vendor/autoload.php';

$type = array('thaiholidays', 'buddhistdays');

if (in_array( $_GET['type'], $type )) {
    
    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename="cal.ics"');
    echo  file_get_contents( 'thaiholidays'.date('Y').'.ics' );

}elseif ( $_GET['type'] === 'tencent' ) {
    
    // get env
    $dotenv = new Dotenv\Dotenv(__DIR__);
    $dotenv->load();

    $DEFAULT_URL = getenv('DEFAULT_URL');
    $DEFAULT_PATH = getenv('DEFAULT_PATH');

    if ( empty($default_url) AND empty( $DEFAULT_PATH ) ) {
        echo 'cannot load env'; die();
    }

    $firebase = new \Firebase\FirebaseLib($DEFAULT_URL);
    $nameEvent = $firebase->get($DEFAULT_PATH .'/holidays/');
    $nameEvent = array_filter(json_decode($nameEvent));
    $vCalendar = new \Eluceo\iCal\Component\Calendar('ical.acodify.com');
    $vEvent = new \Eluceo\iCal\Component\Event();

    foreach ($nameEvent as $key => $value) {
        $vEvent = new \Eluceo\iCal\Component\Event();
        $vEvent->setDtStart(new \DateTime( $value->start ));
        $vEvent->setDtEnd(new \DateTime( $value->end ));
        $vEvent->setNoTime(true);
        $vEvent->setSummary( $value->detail );
        $vCalendar->addComponent($vEvent);
    }

    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename="cal.ics"');

    echo $vCalendar->render();


}else{
    echo 'Not Type Holidays';
}
