<?php
function calcArray($arr) {
    $sum = 0;

    foreach ($arr as $num) {
        $sum += $num;
    }

    $avg = $sum / count($arr);

    return "Sum = $sum , Average = $avg";
}

$numbers = [1,2,3,4,5];
echo calcArray($numbers);
?>
