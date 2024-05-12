<?php
    /**
     * @param $num int
     * @return string
     */
    function getRandString($num){

        $letter = range('a', 'z');

        $number = range(0, 9);

        $letter = implode('',$letter);
        $letter = $letter.strtoupper($letter).implode('',$number);

        $randStr = '';
        for ($i = 0; $i < $num; $i++){
            $randStr .= $letter[rand(0, strlen($letter) - 1)];
        }
        return $randStr;
    }

function gen_token()
{
	if (function_exists('com_create_guid') === true) {
		return trim(com_create_guid(), '{}');
	}
 
	return sprintf(
		'%04X%04X-%04X-%04X-%04X-%04X%04X%04X', 
		mt_rand(0, 65535), 
		mt_rand(0, 65535),
		mt_rand(0, 65535),
		mt_rand(16384, 20479), 
		mt_rand(32768, 49151),
		mt_rand(0, 65535),
		mt_rand(0, 65535), 
		mt_rand(0, 65535)
	);
}

$rndsalt = getRandString(10);
$rndpass = md5(md5("Qwerty123!").md5($rndsalt));

echo $rndsalt;
echo "\n";
echo $rndpass;

?>
