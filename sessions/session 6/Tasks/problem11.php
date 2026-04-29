<?php
$arr1 = array('a','b','c','d');
$arr2 = array('c','d','e','f');

$result = array_intersect($arr1, $arr2);

echo implode(" - ", $result);
?>
