<?php
use Carbon\Carbon;

function boolToString ($value) {
    $message = 'Error: value is not a boolean!';
    if (is_bool($value)) {
        $message = $value ? 'True' : 'False';
    }

    return $message;
}

function stringToBool ($value) {
    return is_null($value) ? false : true;
}

function convertIDArray ($collection) {
    return $collection->pluck('id')->toArray();
}

function toUnicode ($value) {
    $burmeseFontConverter = new \App\Helpers\BurmeseFontConverter;
    return $burmeseFontConverter->toUnicode($value);
}

function cutString ($value, $length = 25) {
    return mb_strimwidth($value, 0, $length, '...');
}

function convert ($str, $lang, $thousandSeperator = true) {
	$enNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
	$mmNumbers = ['၀', '၁', '၂', '၃', '၄', '၅', '၆', '၇', '၈', '၉'];

	if ($lang == 'en') { // mm to en
		$str = str_replace($mmNumbers, $enNumbers, $str);
		return $thousandSeperator ? number_format($str) : $str;
	} else { // en to mm
		$str = $thousandSeperator ? number_format($str) : $str;
		return str_replace($enNumbers, $mmNumbers, $str);
	}
}

function formatTimeDifference ($totalSeconds, $divider, $text) {
    $time = round($totalSeconds / $divider);
    $text = ($time > 1) ? $text .'s' : $text;
    return $time .' '. $text .' ago';
}


function formatTimeDifferenceDate ($totalSeconds, $divider, $text) {
    return false;
    $time = round($totalSeconds / $divider);
    $text = ($time > 1) ? $text .'s' : $text;
    if($time < 7){
        return $time .' '. $text .' ago';
    }else{
        return false;
    }
}

function timeFromNow ($time) {
    // prepare variables
    $inMinute = 60;
    $inHour = 3600;
    $inDay = 86400;
    $current = time();
    $secondDifference = $current - strtotime($time);

    // displaying in different formats
    // $timeFromNow = $secondDifference .' seconds ago';

    $date = Carbon::parse($time, 'UTC');
    $timeFromNow = $date->isoFormat('MMMM D YYYY, h:mm a');

    // if ($secondDifference >= $inMinute && $secondDifference < $inHour) {
    //     $timeFromNow = formatTimeDifference($secondDifference, $inMinute, 'min');
    // } else if ($secondDifference >= $inHour && $secondDifference < $inDay) {
    //     $timeFromNow = formatTimeDifference($secondDifference, $inHour, 'hour');
    // } else if ($secondDifference >= $inDay) {
    //     $date = Carbon::parse($time, 'UTC');
    //     $timeFromNow = formatTimeDifferenceDate($secondDifference, $inDay, 'day') === false ? $date->isoFormat('MMMM D YYYY, h:mm a') : formatTimeDifference($secondDifference, $inDay, 'day');
    // }

    return $timeFromNow;
}

function returnIfExist ($object, $key) {
    return isset($object->$key) ? $object->$key : '';
}