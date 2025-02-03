<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

define('C_bCacheLog',	0);
define('C_bNoLog',		0);
define('C_sLogFile',	'log.txt');


$g_sMess 	= '';
$g_iStartTime	= microtime_float();
x("hello world :-) " . Timestamp(),1);


define('FILENAME',"topScores.json");


$sData = file_get_contents(FILENAME);
if (!$sData)
	$aData = array();
else
	$aData = (array)json_decode($sData);

x("db:\n".print_r($aData,true));

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

}
x("final:\n".print_r($aData,true));


//x("sorted:\n".print_r($aData,true));

$sSave = json_encode($aData);
$sSave = str_replace("]," , "],\n\n",str_replace("},{" , "},\n{", $sSave));

file_put_contents(FILENAME,$sSave);

x("saved:\nsSave");
xx();

function cmp($a, $b) 
{
	if ($a->level ==  $b->level)	return 0;
	return ($a->level >  $b->level) ? -1 : 1;	
}

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

?>