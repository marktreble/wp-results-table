<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
require_once(plugin_dir_path( __DIR__ ) . 'results-table/calc/json.php');
require_once(plugin_dir_path( __DIR__ ) . 'results-table/calc/csv.php');
require_once(plugin_dir_path( __DIR__ ) . 'results-table/calc/league.php');
require_once(plugin_dir_path( __DIR__ ) . 'results-table/calc/algorithms/winter_league.php');
require_once(plugin_dir_path( __DIR__ ) . 'results-table/calc/algorithms/BMFA2018.php');


$block_content  = '<div ' . get_block_wrapper_attributes() . '>';

$block_content .= '<div class="wp-block-gbsra-results-table is-nowrap is-layout-flex wp-container-core-group-is-layout-6c531013 wp-block-group-is-layout-flex">';
$block_content .= '<h2 class="league-title">' . esc_html( $attributes['heading'] ) . '</h2>';
$block_content .= '</div>';
//$block_content .= '<h2 class="wp-block-heading">' . esc_html( $attributes['heading'] ) . '</h2>';

$url_path = sprintf('results/%s/', $attributes['folder']);
$full_path = ABSPATH . $url_path;
$list = [];

/**
 * Genarate an array of filenames (will be in alpabetical order)
 */
if (is_dir($full_path)) {
    foreach (new DirectoryIterator($full_path) as $file) {
        if ($file->isDot()) continue; // ignore . and ..
        if (substr($file->getFilename(), 0, 1) == '.') continue; // ignore hidden files
        if (is_file($full_path . $file->getFilename())) {
            $list[] = $file->getFilename();
        }
    }
}

if (count($list) == 0) {
    /**
     * No files to show
     */
    $block_content .= '<p>n/a</p>';
} else {
    /**
     * Sort order
     */
    sort($list);

    $league_result = new LeagueResults();

    /**
     * Map each filename to it's html representation
     */
    $list = array_map(function ($filename) use ($full_path, $league_result) {
        // Read the file
        $data = file_get_contents($full_path.$filename); 
        
        // Parse the heading out of the filename
        // Remove the ordering segment
        $parts = explode(" ", $filename);
        $order = array_shift($parts);
        $name = implode(" ", $parts);
        // Remove the file extension
        $parts = explode(".", $name);
        $extension = array_pop($parts);
        $display_name = htmlspecialchars(implode(".", $parts));

        switch ($extension) {
            case "json":
                $table_data = getTableDataFromJSONFile($data);
                break;
            case "csv":
            case "txt":
            case "f3f":
                $table_data = getTableDataFromCSVFile($data);

        }
        $type = (substr($order, -1) == "E")
            ? "EUROTOUR"
            : ((substr($order, -1) == "M")
                ? "MULTI-DAY"
                : "LEAGUE");
    
        $league_result = accumulateLeagueResults($league_result, $table_data, $type);

        $table = <<<HTML
        <h3 class="wp-block-heading has-text-align-left is-style-default">$display_name</h3>
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
    }, $list);

    $list[] = getLeagueTable($league_result, $attributes['algo']);

    /**
     * Add to the content
     */
    $block_content .= implode("\n", $list);

}

$block_content .= '</div>';
$block_content .= '<hr class="wp-block-separator has-alpha-channel-opacity">';

echo $block_content;

?>

