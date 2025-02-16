<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

define ('C_bOnline', (	($_SERVER['HTTP_HOST'] != 'localhost') && ($_SERVER['SERVER_ADDR'] != '127.0.0.1' ) )	) ;

define('C_sLogFile',	'ajax.log');
define('C_bCacheLog',	0);
define('C_bNoLog',		0);
$g_sMess 	= '';
$g_iStartTime	= microtime_float();
x("hello world :-) " . Timestamp(),1);

//foreach($_SERVER AS $sKey => $sValue)	x("_SERVER: $sKey => "._LogVar($sValue)."<br>");
//foreach($_ENV AS $sKey => $sValue)	x("_ENV: $sKey => $sValue<br>");


define('FILENAME',"db.php");

if (file_exists(FILENAME))	
{
    $sData = file_get_contents(FILENAME);
    $sData = substr($sData,8,strlen($sData)-13);
    $aData = (array)json_decode($sData);
}
else 	$aData = array();

//x("db:\n".print_r($aData,true));




$sInput = file_get_contents('php://input');
//$sInput = '{"type":0,"name":"test8","level":5,"email":"","schizo":"0"}';
x("input:\n$sInput");
if ($sInput)
{
	$oAdd = json_decode($sInput);
	x("oAdd:\n".print_r($oAdd,true));

	$iType = $oAdd->type;
	if (!isset($aData[$iType]))
	{
		$aData[$iType] = array();
	}	
	unset($oAdd->type);
	$oAdd->time = date('Y-m-d H:i');
	$aData[$iType][] = $oAdd;

	usort($aData[$iType], "cmp");
	//x("sorted:\n".print_r($aData,true));

	$sSave = json_encode($aData);
	$sSave = str_replace("]," , "],\n\n",str_replace("},{" , "},\n{", $sSave));
	
	file_put_contents(FILENAME,"<?php/*\n$sSave\n*/?>");
	
	//x("saved:\nsSave");

	//x("send email to robo ? '{$oAdd->name}'");
	if (	(stripos($oAdd->name,'robo') !== 0) && (stripos($oAdd->name,'roland') !== 0)	)
	{
		MailTo("roland@robosoft.de","schizobene game $iType: $oAdd->name/$oAdd->schizo = $oAdd->level",json_encode($oAdd));	
	}
}
//x("final:\n".print_r($aData,true));



// return top ten for each game type as json
foreach($aData AS $i => $a)
{
    $a10 = array_splice($a,0,10);
    foreach($a10 AS $o) if ($o->email)  $o->email = "*";
    $aData[$i] = $a10;
}

echo json_encode($aData);

xx();


function cmp($a, $b) 
{
	if ($a->level ==  $b->level)	return 0;
	return ($a->level >  $b->level) ? -1 : 1;	
}

// common functions /////

function Timestamp($iUTime=0)
{
	if ($iUTime)
	{
		return date('Y-m-d H:i:s',$iUTime);	// YmdHis
	}
	return date('Y-m-d H:i:s');	// YmdHis
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function x ($sMess,$bReset=0)
{
  	global $g_sMess,$g_iStartTime;

	//$sMess = (microtime_float() - $g_iStartTime) . "\t$sMess";
	
	if (C_bNoLog)
	{
		return $sMess;
	}

	if (C_bCacheLog)
	{
	    if (strlen($g_sMess) > 1000000)
	    {
	        //return 1;
	    }
		$g_sMess .= $sMess."\n";
		return $sMess;
	}

  	$sMode = 'a';
	if ($bReset)
	{
	  	$sMode = 'w';
	}
    $hfile = fopen(C_sLogFile, $sMode);
	if (!$hfile)
	{
		//mess("log file ".C_sLogFile." could not be opened (access=$sMode) :-(");
		return $sMess;
	}
	fwrite($hfile, $sMess."\n");
	fclose($hfile);
	return $sMess;
}

function xx()
{
  	global $g_sMess;
	if (C_bCacheLog)
	{
        $hfile = fopen(C_sLogFile, 'w');
		if (!$hfile)
		{
			//mess("log file " . C_sLogFile . " could not be opened for write :-(");
			return 0;
		}
		$g_sMess .= 'close log: '.TimeStamp();
		fwrite($hfile, $g_sMess);
		fclose($hfile);
	}
}

function MailTo($to,$subject,$message)
{
	$headers = 'From: roland@schizobene.de' . "\r\n" .
	    'Reply-To: roland@schizobene.de' . "\r\n" .
	    'X-Mailer: PHP/' . phpversion();

		$message .= "\nHTTP_ORIGIN: ".$_SERVER['HTTP_ORIGIN'];

	$aTo = explode(',',$to);
	$iSent = 0;
	foreach($aTo AS $sTo)
	{
		if (C_bOnline)
		{
			if (mail($sTo, $subject, $message, $headers))	
			{
				x("notification sent to $sTo");
				$iSent++;
			}
		}
		else
			x("EmailTo($sTo,$subject); iSent=$iSent\n$message");
	}
	return $iSent;
}
function _LogVar(&$v,$iLen=100)
{
	$s = "";
	if (is_array($v))
	{
		$s = implode(",",
			array_map(
				function($o){return is_array($o) ? "array" : (string)$o;}
				, $v) ) ;
	}
	else $s = strval($v);
		
	if (strlen($s)> $iLen)
		$s = substr($s,0,$iLen) . "..";
	return $s;
}


?>