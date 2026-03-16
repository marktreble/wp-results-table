<?php 
class BMFA2012 {
    // Best 2 single day
    // + Best Multi Day
    static function getLeagueResults($league_result) {
        // Initialise a result holder
        $result = new stdClass();

        // Set the number of count contests
        $result->counting_contests = 3;

        // Initialise a results holder array 
        $all_pilots = [];
        
        // Build a list of pilot names and thier summed counting competition scores
        foreach(array_keys($league_result->pilots) as $pilot_name) {
            $multiday = array_merge(
                $league_result->pilots[$pilot_name]["EUROTOUR"],
                $league_result->pilots[$pilot_name]["MULTI-DAY"]
            );
            rsort($multiday);
        
            $multiday = (count($multiday) > 0) ? [$multiday[0]] : [];

            $singleday = array_merge(
                $league_result->pilots[$pilot_name]["LEAGUE"]
            );
            rsort($singleday);

            $singleday = array_slice($singleday, 0, 2);

            $score = array_sum(array_merge(
                $singleday,
                $multiday
            ));

            $all_pilots[] = [
                "n" => $pilot_name,
                "s" => sprintf("%0.2f", floor($score * 100)/100)
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
?>