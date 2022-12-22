<?php
/*
This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program. If not, see <https://www.gnu.org/licenses/>.
*/

/*
Save the html of your collection pages on the realfevr website
e.g. https://www.realfevr.com/collection?page=1&per_page=96
Inspect the html element then "Copy outer html"
Save it to the collection-html folder
Repeat for each page
*/

set_time_limit(0);

libxml_use_internal_errors(true);
$dom = new DOMDocument();

$options = array(
    "ssl" => array(
        "verify_peer" => false,
        "verify_peer_name" => false,
    ),
);

$path = dirname(__FILE__) . '/collection-html/';
$files = array_values(array_filter(scandir($path), function($file) use ($path) {
    return !is_dir($path . '/' . $file);
}));

$collection = array();

$collection[] = array(
    'id' => 'id',
    'number' => 'number',
    'max' => 'max',
    'player' => 'player',
    'sport' => 'sport',
    'drop' => 'drop',
    'pack' => 'pack',
    'event' => 'event',
    'status' => 'status',
);

foreach ($files as $file) {
    $file = 'collection-html/'.$file;
    $html = file_get_contents($file, false, stream_context_create($options));
    if (empty($html)) {
        die;
    }

    if (strpos($file, 'bs') !== false) {
        $sport = 'beach soccer';
    } else {
        $sport = 'football';
    }

    $dom->loadHTML($html);
    $content_node = $dom->getElementById('main-content');
    if (empty($content_node)) {
        continue;
    }

    //$collection_nodes = getElementsByClass($content_node, 'ul', 'collectible--details');
    $collection_nodes = getElementsByClass($content_node, 'div', 'col col-md-3');
    if (empty($collection_nodes)) {
        continue;
    }

    foreach ($collection_nodes as $key => $node) {

        foreach ($node->childNodes[0]->childNodes as $item) {

            $css = $item->getAttribute('class');
            if ($css === 'collectible--anchor') {

                $cid = $item->getAttribute('href');
                $cid = trim(str_replace('/collectibles/', ' ',$cid));

            } elseif ($css === 'collectible--details') {
                $text = str_replace('<br>',' ',$dom->saveHTML($item->childNodes[1]));
                $player = strip_tags($text);

                $event = $item->childNodes[2]->nodeValue;

                $i = $item->childNodes[3];
                $serial = $i->firstChild->nodeValue;
                $pieces = explode('/', $serial);
                $num = $pieces[0];
                $max = $pieces[1];
                $drop = $i->childNodes[1]->nodeValue;
                //$drop = $item->childNodes[2]->childNodes[1]->childNodes[0]->nodeValue;
                if ($drop == 'FE') {
                    $drop = '1';
                } else {
                    $drop = str_replace('#', '', $drop);
                }
                $pack = $i->childNodes[2]->childNodes[1]->childNodes[1]->childNodes[0]->nodeValue;
                $pack = str_replace('Pack #', '', $pack);

                $status = $item->childNodes[5]->firstChild->nodeValue;
            }

        }
        $nft = array(
            'id' => $cid,
            'num' => $num,
            'max' => $max,
            'player' => $player,
            'sport' => $sport,
            'drop' => $drop,
            'pack' => $pack,
            'event' => $event,
            'status' => $status,
        );
        $collection[] = $nft;
    }

    saveListing($collection, 'collection-csv/collection.csv');
}


function getElementsByClass(&$parentNode, $tagName, $className) {
    $nodes = array();

    $childNodeList = $parentNode->getElementsByTagName($tagName);
    for ($i = 0; $i < $childNodeList->length; $i++) {
        $temp = $childNodeList->item($i);
        if (stripos($temp->getAttribute('class'), $className) !== false) {
            $nodes[] = $temp;
        }
    }

    return $nodes;
}

function saveListing($listing, $filename) {

	$fp = fopen($filename, 'w');

	foreach ($listing as $value) {
		fputcsv($fp, $value);
	}

	fclose($fp);
}

die('finished');

?>
