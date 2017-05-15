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
    
    # use regex to parse the page data.
    $patern = '#<ul id="search_results">([\w\W]*?)</ul>#';
    preg_match_all($patern, $result, $parsed);  
    
    # parse to get only the result section
    $patern2 = '#<li>([\w\W]*?)</li>#';
    preg_match_all($patern2, implode('',$parsed[0]), $parsed);
    
    $jsondict = array();
    
    for($i=0;$i<count($parsed[0]);$i++)
    {
        
        # --------- PARSE TO GET CHINESE CHARACTERS ----------
        $paternword = '#<span class="word">([\w\W]*?)</span>#';
        preg_match_all($paternword, $parsed[0][$i], $word);
        
        $paternword2 = '#<a([\w\W]*?)</a>#';
        preg_match_all($paternword2, implode('',$word[0]), $cnword);
        
        $combword = '';
        
        for($j=0;$j<count($cnword[0]);$j++)
        {
            $combword .= strip_tags($cnword[0][$j]);
        }
        
        $jsondict[$i]['cn_char'] = $combword;
        
        
        # --------- PARSE TO GET PINYIN ----------
        
        $paternpinyin = '#<span class="pinyin">([\w\W]*?)</span>#';
        preg_match_all($paternpinyin, $parsed[0][$i], $pinyin);
        
        $jsondict[$i]['pinyin'] = strip_tags(implode('',$pinyin[0]));
        
        
        # --------- PARSE TO GET MEANINGS ----------
        
        $paternmeaning = '#<div class="meaning">([\w\W]*?)</div>#';
        preg_match_all($paternmeaning, $parsed[0][$i], $meanings);
        
        for($j=0;$j<count($meanings[0]);$j++)
        {
            $jsondict[$i]['meanings'] = strip_tags($meanings[0][$j]);
        }
        
        
        
        
    }
    
    # encode to json
    echo json_encode($jsondict);
    
}
else
{
    echo "Usage: http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."?define=TEXT , where 'TEXT' is your text to search (English, Pinyin or Chinese characters)";
}

?>