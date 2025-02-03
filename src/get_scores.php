<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

define('FILENAME',"topScores.json");

if (file_exists(FILENAME))	
{
    $aData = (array)json_decode(file_get_contents(FILENAME));
    foreach($aData AS $i => $a)
    {
        $aData[$i] = array_splice($a,0,10);
    }
}
else 	$aData = array();


echo json_encode($aData);

/*
$conn = new mysqli("DB_HOST", "DB_USER", "DB_PASS", "DB_NAME");
$result = $conn->query("SELECT name, score, email, gender FROM scores ORDER BY score DESC LIMIT 10");
$scores = [];

while($row = $result->fetch_assoc()) {
    $scores[] = $row;
}

echo json_encode($scores);
$conn->close();
*/
?>