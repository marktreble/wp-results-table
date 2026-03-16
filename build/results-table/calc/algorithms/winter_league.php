<?php
class WL {
    static function getCountingContests($contest_count) {
        // 1 Discard after 2 rounds
        return ($contest_count > 2)
            ? $contest_count -1
            : $contest_count;

    }

    static function getLeagueResults($league_result) {
        // Initialise a result holder
        $result = new stdClass();

        // Set the number of count contests
        $result->counting_contests = WL::getCountingContests($league_result->contest_count);

        // Initialise a results holder array 
        $all_pilots = [];
        
        // Build a list of pilot names and thier summed counting competition scores
        foreach(array_keys($league_result->pilots) as $pilot_name) {
            $scores = $league_result->pilots[$pilot_name]["LEAGUE"];
            rsort($scores);
            $score = array_sum(array_slice($scores, 0, $result->counting_contests));

            $all_pilots[] = [
                "n" => $pilot_name,
                "s" => $score
            ];
        }

        // Sort based on score
        usort($all_pilots, function ($item1, $item2) {
            return $item2["s"] <=> $item1["s"];
        });

        // Set the position indicator (r) and the percentage result (p)
        $max = 0;
        $score = 100;
        for ($i = 0; $i < count($all_pilots); $i++) {
            $row = $all_pilots[$i];
            $row["r"] = $i + 1;
            if ($i == 0) {                
                $max = $row["s"];
            } else {
                $score = ($row["s"] / $max) * 100;
            }
            $row["p"] = sprintf("%0.2f", floor($score * 100)/100);
            $all_pilots[$i] = $row;
        }

        // populate the response object
        $result->pilots = $all_pilots;

        // return the result
        return $result;
    }
}