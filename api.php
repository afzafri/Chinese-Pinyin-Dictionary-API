<?php

/*
    (Unofficial) Chinese English Pinyin Dictionary PHP API created by Afif Zafri.
    Data are scraped from Yabla website (https://chinese.yabla.com/chinese-english-pinyin-dictionary.php),
    parse the content, and return JSON formatted string.
    Please note that this is not the official API, this is actually just a "hack"
    Usage: http://site.com/api.php?define=TEXT , where 'TEXT' is your text to search (English, Pinyin or Chinese characters)
*/

header("Access-Control-Allow-Origin: *"); # enable CORS

if(isset($_GET['define']))
{
	$define = $_GET['define']; // text to search
	$showall = (isset($_GET['records'])) ? "&limit=".$_GET['records'] : ""; // if set, fetch all result
    $url = "https://chinese.yabla.com/chinese-english-pinyin-dictionary.php?define=".$define.$showall; # yabla website url

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

    $jsondict['http_code'] = $httpstatus; # set http response code into the array
    $jsondict['error_msg'] = $err;

    # check if search result found or not
    if(strpos($parsed[0][0], 'No matches found for') !== false)
    {
        $jsondict['message'] = "No matches found for '".$define."'";
    }
    else
    {
        # data found, start parsing data
        $jsondict['message'] = "Matches found for '".$define."'";

        for($i=0;$i<count($parsed[0]);$i++)
        {

            # --------- PARSE TO GET CHINESE CHARACTERS (SIMPLIFIED) ----------
            $paternword = '#<span class="word">([\w\W]*?)</span>#';
            preg_match_all($paternword, $parsed[0][$i], $word);

            $paternword2 = '#<a([\w\W]*?)</a>#';
            preg_match_all($paternword2, implode('',$word[0]), $cnword);

            $combword = '';

            for($j=0;$j<count($cnword[0]);$j++)
            {
                $combword .= strip_tags($cnword[0][$j]);
            }

            $jsondict['data'][$i]['simplified_char'] = $combword;

						# --------- PARSE TO GET CHINESE CHARACTERS (TRADITIONAL) ----------
						$paternwordTrad = '#<span class="lbl">Trad.</span>([\w\W]*?)</span>#';
		        preg_match_all($paternwordTrad, $parsed[0][$i], $wordTrad);

		        $paternwordTrad2 = '#<a([\w\W]*?)</a>#';
		        preg_match_all($paternwordTrad2, implode('',$wordTrad[0]), $cnwordTrad);

		        $combwordTrad = '';

		        for($j=0;$j<count($cnwordTrad[0]);$j++)
		        {
		            $combwordTrad .= strip_tags($cnwordTrad[0][$j]);
		        }

						$jsondict['data'][$i]['traditional_char'] = $combwordTrad;

						# --------- PARSE TO GET AUDIO MP3 ----------

						$paternaudio = '#data-audio_url="([\w\W]*?)"#';
		        preg_match_all($paternaudio, implode('',$word[0]), $audio);

						$jsondict['data'][$i]['audio'] = strip_tags($audio[1][0]);

            # --------- PARSE TO GET PINYIN ----------

            $paternpinyin = '#<span class="pinyin">([\w\W]*?)</span>#';
            preg_match_all($paternpinyin, $parsed[0][$i], $pinyin);

            $jsondict['data'][$i]['pinyin'] = strip_tags(implode('',$pinyin[0]));

            # --------- PARSE TO GET MEANINGS ----------

            $paternmeaning = '#<div class="meaning">([\w\W]*?)</div>#';
            preg_match_all($paternmeaning, $parsed[0][$i], $meanings);

            for($j=0;$j<count($meanings[0]);$j++)
            {
                $jsondict['data'][$i]['meanings'] = preg_split("/\\r\\n|\\r|\\n/", strip_tags($meanings[0][$j]));
            }

        }
    }

    # project info
    $jsondict['info']['creator'] = "Afif Zafri (afzafri)";
    $jsondict['info']['project_page'] = "https://github.com/afzafri/Chinese-Pinyin-Dictionary-API";
    $jsondict['info']['date_updated'] = "15/01/2020";

    # encode to json
    echo json_encode($jsondict);

}
else
{
		echo "
		<ul>
			<li>Usage: http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."?define=TEXT , where 'TEXT' is your text to search (English, Pinyin or Chinese characters)</li>
			<li>By default, the API will fetch the first 50 records. To fetch all records, append '&records=NUMBER' to the endpoint , where 'NUMBER' is your number of records to display <br> Ex: http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."?define=TEXT&records=100</li>
		</ul>";
}

?>
