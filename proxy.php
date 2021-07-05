<?php

    $calendarsJson = file_get_contents( "calendars.json" ); 
    $calendars = json_decode($calendarsJson);

    foreach($calendars as $calendar) {
        if($calendar->name ==  $_GET['name']) {
            $found = true;
            echo fetchUrl($calendar->ics);
        }
    }
    if(!$found) {
        header('x-proxy-err-message: Not found.');
        die;
    }

    function fetchUrl($uri) {

        $cachePath = $_SERVER['DOCUMENT_ROOT'] . '/cache/' . urlencode($uri);

        $fileExists = false;
        $age = -1;
        if(file_exists($cachePath)) {
            $fileExists = true;
            $time = filemtime($cachePath);
            $age = time() - $time;
            if($age < 7200) { // two hour cache
                header('x-proxy-cache-age: ' . $age);
                return file_get_contents($cachePath);
            } 
        }

        try {

            $handle = curl_init();
        
            curl_setopt($handle, CURLOPT_URL, $uri);
            curl_setopt($handle, CURLOPT_POST, false);
            curl_setopt($handle, CURLOPT_BINARYTRANSFER, false);
            curl_setopt($handle, CURLOPT_HEADER, true);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);

            curl_setopt($handle, CURLOPT_POSTREDIR, 3);
            curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($handle, CURLOPT_MAXREDIRS, 3);

            //needed for some hosts not to be blocked
            curl_setopt($handle, CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36"');

            // Uncomment this line and change the referrer
            //curl_setopt($handle, CURLOPT_REFERER, 'https://yourdomain.com/');

        
            $response = curl_exec($handle);

            $ch = curl_errno($handle);
            if ($ch) {
                header('x-proxy-err-http-code: ' . $ch);
                header('x-proxy-err-message: ' . $error_msg = curl_error($ch));
            }

            $hlength  = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
            $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);

            $body     = substr($response, $hlength);

            
            //redirects are okay
            if ($httpCode != 200 && $httpCode != 301 && $httpCode != 302 && $httpCode != 308) {
                header('x-proxy-err-http-code: ' . $httpCode);
                if($fileExists) {
                    header('x-proxy-cache-age: ' . $age);
                    return file_get_contents($cachePath);
                } else {
                    throw new Exception($httpCode);
                }
            } else {
                header('x-proxy-cache-age: ' . 0);
            }

            file_put_contents($cachePath, $body);
        
            return $body;
            
        } catch (\Exception $e) {
            header('x-proxy-err-message: ' . $e->getMessage());
            die;
        }    
    }

?>