<?php 
class BMFA2000 {
    // One discard after 3 rounds
    // 2nd discard after 5 rounds?
    static function getCountingContests($contest_count) {        
        switch ($contest_count) {
            case 1:
                return 1;
                break;
            case 2:
            case 3:
                return 2;
                break;            
            case 4:
                return 3;
                break;            
            case 5:
            case 6:
                return 4;
                break;            
            default:
                // More than 6 were never acheived
                // So we don't know what the rule was
                return 5;
                break;
        }
    }

    static function getLeagueResults($league_result) {
        // Initialise a result holder
        $result = new stdClass();

        // Set the number of count contests
        $result->counting_contests = BMFA2000::getCountingContests($league_result->contest_count);

        // Initialise a results holder array 
        $all_pilots = [];
        
        // Build a list of pilot names and thier summed counting competition scores
        foreach(array_keys($league_result->pilots) as $pilot_name) {
            $scores = array_merge(
                $league_result->pilots[$pilot_name]["LEAGUE"],
                $league_result->pilots[$pilot_name]["EUROTOUR"],
                $league_result->pilots[$pilot_name]["MULTI-DAY"]
            );
            rsort($scores);
            $score = array_sum(array_slice($scores, 0, $result->counting_contests));

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