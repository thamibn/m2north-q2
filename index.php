<?php
require_once('simple_html_dom.php'); //using simple html dom parser package

$siteUrl = "https://www.worldometers.info/coronavirus/";
$html = file_get_html($siteUrl); // html content of the website we trying to scrap

$finalObject = []; //the final object that will hold all the table data (array of objects);

//standard object to be used to create the each object based on the keys.
$standardObj = (object)[
    "country" => "",
    "total_cases" => "",
    "new_cases" => "",
    "total_deaths" => "",
    "new_deaths" => "",
    "total_discovered" => ""
];
/* keys to be used to access the copy of the standard object. ||
   can use foreach to read the keys dynamically from the html data
   but since they've specified static keys i decided to declared them static.
*/
$keys = ['country','total_cases','new_cases', 'total_deaths', 'new_deaths', 'total_discovered'];

//looping throw the html to fin the tables | table available and access the data we want.
foreach ($html->find('table[id=main_table_countries_today]') as $table) {
    foreach ($table->find('tbody') as $tbody) {
        foreach ($tbody->find('tr') as $key => $row) {
            if ($key >= 7) {
                $clonedObj = clone $standardObj; //cloning the standard object since objects are referenced on memory
                foreach($row->find('td') as $index => $td){
                    if ($index < 1) continue;
                    if($index > 6 ) break;
                    $stringKey = $keys[$index -1];
                    $clonedObj->$stringKey = $td->plaintext;
                }
                array_push($finalObject, $clonedObj);
            }
        }
    }
}

//creating the final object file to be used on the next question;
$fp = fopen('coronavirusStats.json', 'w');
fwrite($fp, json_encode($finalObject));
fclose($fp);

echo "Done Generating the json file, check it on the root of this project";