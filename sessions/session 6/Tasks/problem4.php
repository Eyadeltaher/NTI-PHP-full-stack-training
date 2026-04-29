<?php
$films = array("Fast","Predestination","Persuit","Prestige");
$keyword = "avatar";

$found = false;

foreach ($films as $film) {
    if (strtolower($film) == strtolower($keyword)) {
        $found = true;
        break;
    }
}

echo $found ? "Yes" : "No";
?>
