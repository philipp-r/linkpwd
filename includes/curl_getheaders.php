<?php
// function to get headers with curl
// http://www.codrate.com/articles/get-headers-by-using-curl-in-php

function curl_getheaders($url) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_HEADER => true,
        CURLOPT_NOBODY => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $url));
    $headers = array();
    foreach (explode("\n", curl_exec($curl)) as $key => $header) {
        if (!$key) {
            $headers[] = $header;
        } else {
            $header = explode(':', $header);
            $headers[trim($header[0])] = isset($header[1]) ? trim($header[1]) : '';
        }
    }
    curl_close($curl);
    return count($headers) < 2 ? false : $headers;
}
