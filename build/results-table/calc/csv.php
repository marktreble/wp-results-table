<?php

function getTableDataFromCSVFile($csv) {
    // Split rows
    $rows = explode("\n", $csv);
    if (count($rows) === 0) {
        return [];
    }

    // Split first line (meta data)
    $meta = explode(",", $rows[0]);
    if (count($meta) === 0) {
        return [];
    }

    // Extract the number of rounds comleted
    // and the number of discards to apply
    $rounds_completed = intval($meta[0]) - 1;
    $num_discards = ($rounds_completed > 14)
        ? 2
        : (($rounds_completed > 3)
            ? 1
            : 0
    );

    if ($rounds_completed <= 0) {
        return [];
    }

    // Inititialise the data array
    $data = [];

    // Initialise an array of winning times at 600s for each round
    // No one could possibly ever fly that slowly!
    $round_winners = [];
    for ($i = 0; $i < $rounds_completed; $i++) {
        $round_winners[] = 600;
    }

    // Loop through rows and add the pilot names and
    // array of their times to the data array.
    // Also calculate the winning time for each round
    // and populate into $round_winners
    for ($i = 1; $i < count($rows); $i++) {
        if (strlen(trim($rows[$i])) == 0) continue; 
        $row = explode(",", $rows[$i]);
        $pilot_name = $row[0];

        $times = array_slice($row, 2, $rounds_completed);

        $data[] = [
            "n" => $pilot_name,
            "t" => $times     
        ];

        for ($round = 0; $round < count($times); $round++) {
            $time = floatVal($times[$round]);
            if (($time > 0)
                && ($time < $round_winners[$round])) {
                $round_winners[$round] = $time;
            }
        }
    }

    // Loop through each pilot - now that we know the
    // winning time in each round, it is easy to calculate
    // each pilot's score in each round
    for ($i = 0; $i < count($data); $i++) {
        $pilot = $data[$i];

        $scores = array_map(
            function($time, $idx) use ($round_winners) {
                if ($time == 0) return 0;
                return floor($round_winners[$idx] / $time * 100000) / 100;    
            }, 
            $pilot["t"],
            array_keys($pilot["t"])
        );

        rsort($scores);
        $score = array_sum(array_slice($scores, 0, $rounds_completed - $num_discards));

        $pilot["s"] = $score;
        $pilot["t"] = null;

        $data[$i] = $pilot;
    }

    // Sort the pilot order by the score descending
    usort($data, function ($item1, $item2) {
         return $item2["s"] <=> $item1["s"];
    });

    // Calculate the normalised percentage score (p)
    // and apply the position indicate (r) to the pilot object
    $max = 0;
    $score = 1000;
    for ($i = 0; $i < count($data); $i++) {
        $row = $data[$i];
        $row["r"] = $i + 1;
        if ($i == 0) {                
            $max = $row["s"];
        } else {
            $score = ($row["s"] / $max) * 1000;
        }
        $row["p"] = sprintf("%0.2f", floor($score * 100)/100);
        $data[$i] = $row;
    }
    

    return $data;
}

?>