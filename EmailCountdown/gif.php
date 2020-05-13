<?php
date_default_timezone_set('Europe/Moscow');
include 'GIFEncoder.class.php';
$time = $_GET['time'];
$future_date = new DateTime(date('r',strtotime($time)));
$time_now = time();
$now = new DateTime(date('r', $time_now));
$frames = array();
$delays = array();
$image = imagecreatefrompng('countdown.png');
$delay = 100; // milliseconds
$font = array(
    'size' => 95, // Font size, in pts usually.
    'angle' => 0, // Angle of the text
    'x-offset' => 0, // The larger the number the further the distance from the left hand side, 0 to align to the left.
    'y-offset' => 100, // The vertical alignment, trial and error between 20 and 60.
    'file'=>'HelveticaBold.ttf',
    'color'=>imagecolorallocate($image, 0, 0, 0),
);
for($i = 0; $i <= 60; $i++){
    $interval = date_diff($future_date, $now);
    if($future_date < $now){
        // Open the first source image and add the text.
        $image = imagecreatefrompng('https://yadi.sk/d/_Vh7kHrvuUmI-Q/countdown.png');;
        $text = $interval->format('');
        imagettftext ($image , $font['size'] , $font['angle'] , $font['x-offset'] , $font['y-offset'] , $font['color'] , $font['file'], $text );
        ob_start();
        imagegif($image);
        $frames[]=ob_get_contents();
        $delays[]=$delay;
        $loops = 1;
        ob_end_clean();
        break;
    } else {
        // Open the first source image and add the text.
        $image = imagecreatefrompng('countdown.png');;
        $text = $interval->format('%H:%I:%S');
        // %a is weird in that it doesn’t give you a two digit number
        // check if it starts with a single digit 0-9
        // and prepend a 0 if it does
        if(preg_match('/^[0-9]\:/', $text)){
            $text = '0'.$text;
        }
        imagettftext ($image , $font['size'] , $font['angle'] , $font['x-offset'] , $font['y-offset'] , $font['color'] , $font['file'], $text );
        ob_start();
        imagegif($image);
        $frames[]=ob_get_contents();
        $delays[]=$delay;
        $loops = 0;
        ob_end_clean();
    }
    $now->modify('+1 second');
}
//expire this image instantly
header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );
$gif = new AnimatedGif($frames,$delays,$loops);
$gif->display();

