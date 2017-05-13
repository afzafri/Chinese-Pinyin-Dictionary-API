<?php

if(isset($_GET['define']))
{
	$define = $_GET['define']; 
    $url = "https://chinese.yabla.com/chinese-english-pinyin-dictionary.php?define=".$define; # yabla website url
   
    $ch = curl_init(); # initialize curl object
    curl_setopt($ch, CURLOPT_URL, $url); # set url
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); # receive server response
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); # disable SSL verification (THIS IS NOT PROPER/SAFE)
    $result = curl_exec($ch); # execute curl, fetch webpage content
    $httpstatus = curl_getinfo($ch, CURLINFO_HTTP_CODE); # receive http response status
    $err = curl_error($ch);
    curl_close($ch);  # close curl
    
    print_r($result); # test print scraped page
}
else
{
    echo "Usage: http://site.com/api.php?define=TEXT , where 'TEXT' is your text to search (English, Pinyin or Chinese characters)";
}

?>