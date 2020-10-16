<?php

require_once dirname(__FILE__) . '/vendor/autoload.php';

use Twilio\Rest\Client;

$client = new Goutte\Client();

$url = 'https://www.bestbuy.com/site/nvidia-geforce-rtx-3080-10gb-gddr6x-pci-express-4-0-graphics-card-titanium-and-black/6429440.p?skuId=6429440&intl=nosplash#headerskip';

try {
    $crawler = $client->request('GET', $url);
    $data = $crawler->html();

    $matchesSoldOut = preg_match_all('/sold\sout/im', $data);
    $matchesComingSoon = preg_match_all('/coming\ssoon/im', $data);

    $msg = date('Y-m-d H:i:s') . ' : Matches sould out: ' . $matchesSoldOut . ' Matches coming soon: ' . $matchesComingSoon;

    writeToLog($msg);

    // if ($matchesSoldOut == 0 || $matchesComingSoon > 0) {
        sendNotifications();
    // }
} Catch (Exception $e){
    $msg = date('Y-m-d H:i:s') . ' : Exception: ' . $e->getMessage();
    writeToLog($msg);
}


function sendNotifications() {

    $msg = date('Y-m-d H:i:s') . ' : Sending twilio notification';
    writeToLog($msg);

    // Your Account SID and Auth Token from twilio.com/console
    $account_sid = 'ACad7db494ca14dec5e6b8800ee0faab51';
    $auth_token = '7bbda8b8ce5c5c7e1e19de69a0e25c67';
    // In production, these should be environment variables. E.g.:
    // $auth_token = $_ENV["TWILIO_ACCOUNT_SID"]

    // A Twilio number you own with Voice capabilities
    $twilio_number = "+12319362877";

    // Where to make a voice call (your cell phone?)
    $to_number = "+573502978658";

    $client = new Client($account_sid, $auth_token);
    $client->account->calls->create(  
        $to_number,
        $twilio_number,
        array(
            "url" => "http://demo.twilio.com/docs/voice.xml"
        )
    );

    $msg = date('Y-m-d H:i:s') . ' : Twilio notification sent';
    writeToLog($msg);
}

function writeToLog($msg) {
    
    $file_path = dirname(__FILE__)  . '/log.txt';
    $file_handle = fopen($file_path, 'r'); 
    $contents = fread($file_handle, filesize($file_path));
    fclose($file_handle);

    $contents .= "\n" . $msg;
    $file_handle = fopen($file_path, 'w');
    fwrite($file_handle, $contents);
    fclose($file_handle);
}