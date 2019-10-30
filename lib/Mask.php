<?php

function mask ( $str, $start = 0, $length = null ) {
    $mask = preg_replace ( "/\S/", "*", $str );
    if( is_null ( $length )) {
        $mask = substr ( $mask, $start );
        $str = substr_replace ( $str, $mask, $start );
    }else{
        $mask = substr ( $mask, $start, $length );
        $str = substr_replace ( $str, $mask, $start, $length );
    }
    return $str;
}