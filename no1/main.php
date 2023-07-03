<?php

require_once ('BageConcat.php');

//$fptr = fopen(getenv("OUTPUT_PATH"), "w");
$n = intval(trim(fgets(STDIN)));
$datas = [];
$result = new BageConcat();

for ($n_itr = 0; $n_itr < $n; $n_itr++) {
    

    $input = intval(trim(fgets(STDIN)));
    array_push($datas,$input);

    
}
$result->setDatas($datas);
$result->getResult();

