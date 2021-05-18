<?php

/**
* Process
*
* Copyright ( c ) 2020 Zeshan Ahmed
* This software is released under the MIT License.
* https://opensource.org/licenses/MIT
*
* @author Zeshan Ahmed <https://zeshanahmed.com>
* @version 1.0.0
* @create date 18-05-2021
*/

function cleanQuery( $query ) {
    if ( empty( $query ) ) {
        return $query;
    }
    // $clean = iconv( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', $tsts );
    // Fucking letter ñ this was the only
    // way i found to remove it without removing
    // the rest of the special characters in the string
    $clean = $query;
    $clean = urlencode( $clean );
    $clean = str_replace( 'n%CC%83', 'n', $clean );
    $clean = str_replace( 'N%CC%83', 'n', $clean );
    $clean = str_replace( '%C3%B3', 'o', $clean ); // accented o
    $clean = str_replace( '%CC%81', '', $clean ); // accented i

    $clean = urldecode( $clean );
    $clean = preg_replace( '!\s+!', ' ', $clean );
    $clean = mb_strtolower( $clean, 'UTF-8' );

    return $clean;
}

function getVar( $array, $key, $default = null ) {
    if ( is_array( $array ) && isset( $array[$key] ) && !empty( $array[$key] ) ) {
        return trim( $array[$key] );
    }

    if ( ! is_null( $default ) ) {
        return $default;
    }

    return '';
}

function hourlyRate( $val, $default = 90 ) {
    $rate = isset( $val ) ? floatval( $val ) : '';

    if ( empty( $rate ) ) {
        return $default;
    }

    return $rate;
}

function percentVar( $val, $default = 15 ) {
    $rate = isset( $val ) ? floatval( $val ) : '';

    if ( empty( $rate ) ) {
        return $default;
    }

    return $rate;
}

// Main query
$query = cleanQuery( getVar( $argv, 1, 1 ) );
$query_arr = explode( ' ', $query );

// Variables
$hours = floatval( $query_arr[0] );
$hourly_rate = hourlyRate( $query_arr[1] );
$percent = percentVar( $query_arr[2] );

// Calculation
$without_percentage = $hourly_rate * $hours;
$with_percentage = ( $without_percentage * $percent ) / 100;
$result = $without_percentage + $with_percentage;

// Processed array with title/subtitle etc.
$processed_arr = array(
    'items' => array (
        array (
            'title' => $result,
            'subtitle' => "total of ${hours} hours at the rate of ${hourly_rate} after adding ${percent}% fees.",
            'arg' => $result,
            'mods' => array (
                'cmd' => array (
                    'valid' => true,
                    'arg' => $result,
                    'subtitle' => 'Action this item to copy the amount with no format',
                ),
            ),
        )
    )
);

$processed = $processed_arr;

if ( $processed ) {
    echo json_encode( $processed );
    exit( 0 );
}

echo '{"items": []}';
exit( 0 );
