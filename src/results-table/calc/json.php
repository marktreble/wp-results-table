<?php

function getTableDataFromJSONFile($json) {
    $data = json_decode($json, true);  

    // Just need to calc and add the normalised percentage result (p)
    if (is_array($data)) {
        $max = 0;
        $score = 1000;
        for ($i = 0; $i < count($data); $i++) {
            $row = $data[$i];
            $s = floatVal($row["s"]);
            if ($i == 0) {                
                $max = $s;
            } else {
                $score = ($s / $max) * 1000;
            }
            $row["p"] = sprintf("%0.2f", floor($score * 100)/100);
            $data[$i] = $row;
        }
    }
    return $data;
}

?>