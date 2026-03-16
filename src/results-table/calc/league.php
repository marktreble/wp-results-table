<?php
require(plugin_dir_path( __DIR__ ) . 'calc/algorithms/winter_league.php');
require(plugin_dir_path( __DIR__ ) . 'calc/algorithms/BMFA2018.php');
require(plugin_dir_path( __DIR__ ) . 'calc/algorithms/BMFA2015.php');
require(plugin_dir_path( __DIR__ ) . 'calc/algorithms/BMFA2012.php');
require(plugin_dir_path( __DIR__ ) . 'calc/algorithms/BMFA2000.php');

/**
 * LeagueResults holder class
 */
class LeagueResults { 
    public $contest_count;
    public $pilots;

    public function __construct()
    {
        $this->contest_count = 0;
        $this->pilots = [];
    }
}

/**
 * Build a LeagueResults object by accumulating pilot results
 * for each cometition
 */
function accumulateLeagueResults($league_result, $table_data, $type) {
    $league_result->contest_count++;
    $all_pilots = $league_result->pilots;

    foreach($table_data as $row) {
        if (!in_array($row["n"], array_keys($all_pilots))) {
            $all_pilots[$row["n"]] = [
                "LEAGUE" => [],
                "EUROTOUR" => [],
                "MULTI-DAY" => []
            ];
        }
        $all_pilots[$row["n"]][$type][] = $row["p"];
    }
    $league_result->pilots = $all_pilots;

    return $league_result;
}

/**
 * Calculate a League Results JSON object based on the
 * accumulated results
 */
function getLeagueResults($league_result, $algo) {
    return $algo::getLeagueResults($league_result);
}

/**
 * Get HTML rendering code for a league results table
 */
function getLeagueTable($league_result, $algo) {
    if ($league_result->contest_count <= 1) {
        return "";
    }
    
    $league_results = getLeagueResults($league_result, $algo);

    $table_data = $league_results->pilots;

    $table = <<<HTML
        <h3 class="wp-block-heading has-text-align-left is-style-default">Overall Result {$league_results->counting_contests} Contests Counting</h3>
        <div class="wp-block-group has-medium-font-size has-global-padding is-content-justification-left is-layout-constrained wp-container-core-group-is-layout-e30f7fe5 wp-block-group-is-layout-constrained" style="padding-right:0;padding-left:0">
        <figure class="wp-block-table has-small-font-size" style="margin-left: 0px !important;">
            <table>
                <thead>
                    <tr>
                        <th class="has-text-align-left" data-align="left">Rank</th>
                        <th class="has-text-align-left" data-align="left">Pilot</th>
                        <th class="has-text-align-right" data-align="right">Score</th>
                        <th class="has-text-align-right" data-align="right">Normalized</th>
                    </tr>
                </thead>
HTML;

    foreach($table_data as $row) {
        $table .= <<<HTML
                <tbody>
                    <tr>
                        <td class="has-text-align-left" data-align="left">{$row["r"]}</td>
                        <td class="has-text-align-left" data-align="left">{$row["n"]}</td>
                        <td class="has-text-align-right" data-align="right">{$row["s"]}</td>
                        <td class="has-text-align-right" data-align="right">{$row["p"]}</td>
                    </tr>
                </tbody>
HTML;
        }

    $table .= <<<HTML
            </table>
        </figure>
        </div>
HTML;

    return $table;
}
